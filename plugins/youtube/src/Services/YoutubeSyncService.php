<?php

namespace Plugins\Youtube\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plugins\Youtube\Models\YoutubeVideo;

class YoutubeSyncService
{
    /**
     * Sync videos from YouTube Channel (supports API Key and RSS Fallback).
     *
     * @return array Sync result summary
     */
    public function sync(): array
    {
        $apiKey = Setting::get('youtube_api_key', 'AIzaSyBg1ngOtubANX-JCB2eJxGM-gqRIENXOPQ');
        $channelId = Setting::get('youtube_channel_id', 'UCG0E2Kc-QvMRLJ70Q-XeemA');

        if (is_array($apiKey)) {
            $apiKey = $apiKey['v'] ?? '';
        }
        if (is_array($channelId)) {
            $channelId = $channelId['v'] ?? '';
        }

        $syncedCount = 0;

        // Try YouTube Data API v3 first if API key exists
        if (! empty($apiKey) && ! empty($channelId)) {
            try {
                $syncedCount = $this->syncViaApi($apiKey, $channelId);
            } catch (\Throwable $e) {
                Log::warning("YouTube API sync failed: " . $e->getMessage() . ". Falling back to RSS feed.");
                $syncedCount = $this->syncViaRss($channelId);
            }
        } elseif (! empty($channelId)) {
            $syncedCount = $this->syncViaRss($channelId);
        }

        return [
            'status' => 'success',
            'count' => $syncedCount,
            'message' => "Successfully synced {$syncedCount} videos from YouTube Channel.",
        ];
    }

    /**
     * Sync via YouTube Data API v3 (fetches multiple pages if available).
     */
    protected function syncViaApi(string $apiKey, string $channelId): int
    {
        $pageToken = null;
        $totalSynced = 0;
        $maxPages = 5; // Fetch up to 250 videos

        for ($page = 0; $page < $maxPages; $page++) {
            $queryParams = [
                'key' => $apiKey,
                'channelId' => $channelId,
                'part' => 'snippet',
                'order' => 'date',
                'maxResults' => 50,
                'type' => 'video',
            ];

            if ($pageToken) {
                $queryParams['pageToken'] = $pageToken;
            }

            $response = Http::get('https://www.googleapis.com/youtube/v3/search', $queryParams);

            if ($response->failed()) {
                if ($page === 0) {
                    throw new \Exception("YouTube API request failed with status " . $response->status());
                }
                break;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                $videoId = $item['id']['videoId'] ?? null;
                if (! $videoId) {
                    continue;
                }

                $snippet = $item['snippet'] ?? [];
                $title = $snippet['title'] ?? 'Untitled Video';
                $description = $snippet['description'] ?? '';
                $channelTitle = $snippet['channelTitle'] ?? 'Central Data Technology';
                $publishedAt = isset($snippet['publishedAt']) ? date('Y-m-d H:i:s', strtotime($snippet['publishedAt'])) : now();
                $thumbs = $snippet['thumbnails'] ?? [];

                YoutubeVideo::updateOrCreate(
                    ['youtube_id' => $videoId],
                    [
                        'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
                        'description' => $description,
                        'thumbnail_default' => $thumbs['default']['url'] ?? "https://img.youtube.com/vi/{$videoId}/default.jpg",
                        'thumbnail_medium' => $thumbs['medium']['url'] ?? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
                        'thumbnail_high' => $thumbs['high']['url'] ?? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                        'channel_title' => $channelTitle,
                        'published_at' => $publishedAt,
                        'is_visible' => true,
                        'synced_at' => now(),
                    ]
                );

                $totalSynced++;
            }

            $pageToken = $data['nextPageToken'] ?? null;
            if (! $pageToken) {
                break;
            }
        }

        return $totalSynced;
    }

    /**
     * Fallback: Sync via YouTube Channel Public RSS Feed.
     */
    protected function syncViaRss(string $channelId): int
    {
        $rssUrl = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";
        $response = Http::get($rssUrl);

        if ($response->failed()) {
            return 0;
        }

        $xmlString = $response->body();
        $xml = simplexml_load_string($xmlString);

        if (! $xml || ! isset($xml->entry)) {
            return 0;
        }

        $count = 0;
        foreach ($xml->entry as $entry) {
            $ytNs = $entry->children('http://www.youtube.com/xml/schemas/2015');
            $mediaNs = $entry->children('http://search.yahoo.com/mrss/');

            $videoId = (string) ($ytNs->videoId ?? '');
            if (! $videoId) {
                $videoId = str_replace('yt:video:', '', (string) $entry->id);
            }

            if (! $videoId) {
                continue;
            }

            $title = (string) $entry->title;
            $description = (string) ($mediaNs->group->description ?? '');
            $publishedAt = (string) $entry->published;
            $thumbUrl = (string) ($mediaNs->group->thumbnail->attributes()->url ?? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg");

            YoutubeVideo::updateOrCreate(
                ['youtube_id' => $videoId],
                [
                    'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
                    'description' => $description,
                    'thumbnail_default' => "https://img.youtube.com/vi/{$videoId}/default.jpg",
                    'thumbnail_medium' => "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
                    'thumbnail_high' => $thumbUrl,
                    'channel_title' => 'Central Data Technology',
                    'published_at' => ! empty($publishedAt) ? date('Y-m-d H:i:s', strtotime($publishedAt)) : now(),
                    'is_visible' => true,
                    'synced_at' => now(),
                ]
            );

            $count++;
        }

        return $count;
    }
}
