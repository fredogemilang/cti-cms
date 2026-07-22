<?php

namespace App\Services;

use App\Models\IndexingLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IndexNowService
{
    public const API_ENDPOINT = 'https://api.indexnow.org/indexnow';

    /**
     * Get or generate IndexNow API key.
     */
    public function getKey(): string
    {
        /** @var string|null $key */
        $key = setting('seo_indexnow_key');
        if (empty($key)) {
            return $this->generateKey();
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
     * @param  mixed|null  $model
     */
    public function submitUrls(array $urls, $model = null): bool
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

            $status = $response->status();
            $responseBody = $response->body();
            $success = in_array($status, [200, 202], true);

            if ($success) {
                Setting::set('seo_indexnow_last_ping_at', now()->toIso8601String(), 'seo', 'text');
            }

            // Record activity log for each submitted URL
            foreach ($urls as $url) {
                IndexingLog::create([
                    'protocol' => 'indexnow',
                    'url' => $url,
                    'status_code' => $status,
                    'response' => $responseBody,
                    'request_time' => now(),
                    'entity_type' => $model ? get_class($model) : null,
                    'entity_id' => $model && isset($model->id) ? (int) $model->id : null,
                ]);
            }

            return $success;
        } catch (\Throwable $e) {
            foreach ($urls as $url) {
                IndexingLog::create([
                    'protocol' => 'indexnow',
                    'url' => $url,
                    'status_code' => 0,
                    'response' => $e->getMessage(),
                    'request_time' => now(),
                    'entity_type' => $model ? get_class($model) : null,
                    'entity_id' => $model && isset($model->id) ? (int) $model->id : null,
                ]);
            }

            return false;
        }
    }

    /**
     * Auto ping IndexNow for an entity (Page, Post, CPT entry).
     *
     * @param  mixed  $entity
     */
    public function pingEntity($entity): bool
    {
        $enabled = (bool) setting('seo_indexnow_enabled', true);
        $autoPing = (bool) setting('seo_indexnow_auto_ping', true);

        if (! $enabled || ! $autoPing) {
            return false;
        }

        $url = null;
        if (method_exists($entity, 'getPublicUrl')) {
            $url = $entity->getPublicUrl();
        } elseif (isset($entity->slug)) {
            $url = url('/'.$entity->slug);
        }

        if (! $url) {
            return false;
        }

        $cacheKey = 'indexnow_ping_'.md5($url);
        if (Cache::has($cacheKey)) {
            return true;
        }

        $success = $this->submitUrls([$url], $entity);
        if ($success) {
            Cache::put($cacheKey, true, 60);
        }

        return $success;
    }
}
