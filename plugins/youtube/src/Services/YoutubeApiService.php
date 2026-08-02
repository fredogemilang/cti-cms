<?php

namespace Plugins\Youtube\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Plugins\Youtube\Exceptions\YoutubeApiException;
use Plugins\Youtube\Models\YoutubePlaylist;
use Plugins\Youtube\Models\YoutubePlaylistVideo;
use Plugins\Youtube\Models\YoutubeVideo;

class YoutubeApiService
{
    private string $apiKey;

    private string $baseUrl = 'https://www.googleapis.com/youtube/v3';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('youtube.api_key') ?? (class_exists(Setting::class) ? Setting::get('youtube_api_key', '') : '');
    }

    public function testConnection(string $channelId): array
    {
        try {
            $data = $this->request('channels', [
                'part' => 'snippet,statistics,contentDetails',
                'id' => $channelId,
            ]);

            if (empty($data['items'])) {
                return ['success' => false, 'error' => 'Channel not found.'];
            }

            $channel = $data['items'][0];

            return [
                'success' => true,
                'channel' => [
                    'title' => $channel['snippet']['title'] ?? null,
                    'description' => $channel['snippet']['description'] ?? null,
                    'thumbnail' => $channel['snippet']['thumbnails']['default']['url'] ?? null,
                    'subscriberCount' => $channel['statistics']['subscriberCount'] ?? 0,
                    'videoCount' => $channel['statistics']['videoCount'] ?? 0,
                    'uploadsPlaylistId' => $channel['contentDetails']['relatedPlaylists']['uploads'] ?? null,
                ],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getChannel(string $channelId): array
    {
        return $this->request('channels', [
            'part' => 'snippet,contentDetails,statistics',
            'id' => $channelId,
        ]);
    }

    public function getUploadsPlaylistId(string $channelId): ?string
    {
        $data = $this->getChannel($channelId);

        return $data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;
    }

    public function getPlaylistItems(string $playlistId, ?string $pageToken = null, int $maxResults = 50): array
    {
        $params = [
            'part' => 'snippet,contentDetails',
            'playlistId' => $playlistId,
            'maxResults' => $maxResults,
        ];

        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        $response = $this->request('playlistItems', $params);

        return [
            'items' => $response['items'] ?? [],
            'nextPageToken' => $response['nextPageToken'] ?? null,
            'totalResults' => $response['pageInfo']['totalResults'] ?? 0,
        ];
    }

    public function getVideoDetails(array $videoIds): array
    {
        if (empty($videoIds)) {
            return [];
        }

        $chunks = array_chunk($videoIds, 50);
        $results = [];

        foreach ($chunks as $chunk) {
            $response = $this->request('videos', [
                'part' => 'snippet,contentDetails,statistics',
                'id' => implode(',', $chunk),
            ]);

            if (! empty($response['items'])) {
                $results = array_merge($results, $response['items']);
            }
        }

        return $results;
    }

    public function getPlaylists(string $channelId, ?string $pageToken = null, int $maxResults = 50): array
    {
        $params = [
            'part' => 'snippet,contentDetails',
            'channelId' => $channelId,
            'maxResults' => $maxResults,
        ];

        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        $response = $this->request('playlists', $params);

        return [
            'items' => $response['items'] ?? [],
            'nextPageToken' => $response['nextPageToken'] ?? null,
        ];
    }

    public function syncAllVideos(string $channelId): array
    {
        $uploadsPlaylistId = $this->getUploadsPlaylistId($channelId);
        if (! $uploadsPlaylistId) {
            return ['synced' => 0, 'errors' => ['Uploads playlist not found for channel.']];
        }

        $pageToken = null;
        $videoIds = [];
        $syncedCount = 0;
        $errors = [];

        do {
            $data = $this->getPlaylistItems($uploadsPlaylistId, $pageToken);

            $chunkIds = [];
            foreach ($data['items'] as $item) {
                if (isset($item['contentDetails']['videoId'])) {
                    $chunkIds[] = $item['contentDetails']['videoId'];
                }
            }

            if (! empty($chunkIds)) {
                try {
                    $videosData = $this->getVideoDetails($chunkIds);
                    foreach ($videosData as $video) {
                        $durationSeconds = self::parseDuration($video['contentDetails']['duration'] ?? '');

                        YoutubeVideo::updateOrCreate(
                            ['youtube_id' => $video['id']],
                            [
                                'title' => $video['snippet']['title'] ?? '',
                                'description' => $video['snippet']['description'] ?? '',
                                'thumbnail_default' => $video['snippet']['thumbnails']['default']['url'] ?? null,
                                'thumbnail_medium' => $video['snippet']['thumbnails']['medium']['url'] ?? null,
                                'thumbnail_high' => $video['snippet']['thumbnails']['high']['url'] ?? null,
                                'channel_title' => $video['snippet']['channelTitle'] ?? '',
                                'published_at' => isset($video['snippet']['publishedAt']) ? Carbon::parse($video['snippet']['publishedAt']) : null,
                                'duration' => $video['contentDetails']['duration'] ?? null,
                                'duration_seconds' => $durationSeconds,
                                'view_count' => $video['statistics']['viewCount'] ?? 0,
                                'like_count' => $video['statistics']['likeCount'] ?? 0,
                                'tags' => $video['snippet']['tags'] ?? null,
                                'synced_at' => now(),
                            ]
                        );
                        $syncedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            $pageToken = $data['nextPageToken'];
        } while ($pageToken);

        return ['synced' => $syncedCount, 'errors' => $errors];
    }

    public function syncAllPlaylists(string $channelId): array
    {
        $pageToken = null;
        $syncedCount = 0;

        do {
            $data = $this->getPlaylists($channelId, $pageToken);

            foreach ($data['items'] as $item) {
                $playlist = YoutubePlaylist::updateOrCreate(
                    ['youtube_id' => $item['id']],
                    [
                        'title' => $item['snippet']['title'] ?? '',
                        'description' => $item['snippet']['description'] ?? '',
                        'thumbnail_url' => $item['snippet']['thumbnails']['high']['url'] ?? ($item['snippet']['thumbnails']['default']['url'] ?? null),
                        'video_count' => $item['contentDetails']['itemCount'] ?? 0,
                        'synced_at' => now(),
                    ]
                );

                $syncedCount++;

                // Sync playlist items (pivot)
                $itemPageToken = null;
                $position = 0;

                // Clear existing pivot relation for this playlist before re-syncing
                YoutubePlaylistVideo::where('playlist_id', $playlist->id)->delete();

                do {
                    $itemsData = $this->getPlaylistItems($item['id'], $itemPageToken);
                    foreach ($itemsData['items'] as $playlistItem) {
                        $videoId = $playlistItem['contentDetails']['videoId'] ?? null;
                        if ($videoId) {
                            $videoModel = YoutubeVideo::where('youtube_id', $videoId)->first();
                            if ($videoModel) {
                                YoutubePlaylistVideo::create([
                                    'playlist_id' => $playlist->id,
                                    'video_id' => $videoModel->id,
                                    'position' => $position,
                                ]);
                                $position++;
                            }
                        }
                    }
                    $itemPageToken = $itemsData['nextPageToken'];
                } while ($itemPageToken);
            }

            $pageToken = $data['nextPageToken'];
        } while ($pageToken);

        return ['synced' => $syncedCount];
    }

    public function syncAll(string $channelId): array
    {
        $videosResult = $this->syncAllVideos($channelId);
        $playlistsResult = $this->syncAllPlaylists($channelId);

        return [
            'videos_synced' => $videosResult['synced'],
            'playlists_synced' => $playlistsResult['synced'],
            'errors' => $videosResult['errors'] ?? [],
        ];
    }

    public static function parseDuration(string $iso8601): int
    {
        if (empty($iso8601)) {
            return 0;
        }

        try {
            $interval = new \DateInterval($iso8601);

            return ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function request(string $endpoint, array $params): array
    {
        if (empty($this->apiKey)) {
            throw new YoutubeApiException('YouTube API key is not configured.');
        }

        $params['key'] = $this->apiKey;

        $response = Http::get($this->baseUrl.'/'.$endpoint, $params);

        if (! $response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new YoutubeApiException("YouTube API Error: {$error}");
        }

        return $response->json();
    }
}
