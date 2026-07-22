<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IndexNowService
{
    public const API_ENDPOINT = 'https://api.indexnow.org/indexnow';

    public function isEnabled(): bool
    {
        return (bool) setting('seo_indexnow_enabled', true);
    }

    public function isAutoPingEnabled(): bool
    {
        return $this->isEnabled() && (bool) setting('seo_indexnow_auto_ping', true);
    }

    public function getKey(): string
    {
        $key = (string) setting('seo_indexnow_key', '');
        if ($key === '') {
            $key = $this->generateKey();
        }

        return $key;
    }

    public function generateKey(): string
    {
        $key = Str::random(32);
        Setting::set('seo_indexnow_key', $key, 'seo', 'text');

        return $key;
    }

    public function getKeyUrl(): string
    {
        return url('/indexnow-'.$this->getKey().'.txt');
    }

    /**
     * Submit an array of full URLs to the IndexNow API.
     *
     * @param  array<string>  $urls
     */
    public function submitUrls(array $urls): bool
    {
        $urls = array_values(array_filter(array_unique(array_map('trim', $urls))));
        if (empty($urls)) {
            return false;
        }

        $host = parse_url(config('app.url'), PHP_URL_HOST) ?? parse_url($urls[0], PHP_URL_HOST);

        $payload = [
            'host' => $host,
            'key' => $this->getKey(),
            'keyLocation' => $this->getKeyUrl(),
            'urlList' => $urls,
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'User-Agent' => 'CTI-CMS/1.0 IndexNow',
                ])
                ->post(self::API_ENDPOINT, $payload);

            $success = in_array($response->status(), [200, 202], true);

            if ($success) {
                Setting::set('seo_indexnow_last_ping_at', now()->toIso8601String(), 'seo', 'text');
            }

            return $success;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * Ping IndexNow for a single entity (Page, Post, CptEntry).
     */
    public function pingEntity(mixed $entity): bool
    {
        if (! $this->isAutoPingEnabled()) {
            return false;
        }

        $slug = $entity->slug ?? null;
        if (! $slug) {
            return false;
        }

        // Only ping for published items if status attribute exists
        if (isset($entity->status) && $entity->status !== 'published') {
            return false;
        }

        $url = url('/'.$slug);

        // Rate limiting cache check (avoid pinging same URL within 60 seconds)
        $cacheKey = 'indexnow_ping_'.md5($url);
        if (Cache::has($cacheKey)) {
            return false;
        }

        Cache::put($cacheKey, true, 60);

        return $this->submitUrls([$url]);
    }
}
