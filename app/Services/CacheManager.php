<?php

namespace App\Services;

use App\Http\Middleware\PageCache;
use Illuminate\Support\Facades\Log;

class CacheManager
{
    /**
     * Check if running on a LiteSpeed web server.
     */
    public static function isLiteSpeed(): bool
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';

        return stripos($serverSoftware, 'LiteSpeed') !== false;
    }

    /**
     * Purge all cached pages across both LSCache (if available) and PageCache.
     */
    public static function purgeAll(): void
    {
        // 1. Purge LiteSpeed Cache if package class exists
        if (class_exists(\Litespeed\LSCache\LSCache::class)) {
            try {
                \Litespeed\LSCache\LSCache::purge('*');
            } catch (\Throwable $e) {
                Log::warning('LSCache::purgeAll failed: ' . $e->getMessage());
            }
        }

        // 2. Always purge Laravel-level PageCache fallback
        PageCache::purgeAll();
    }

    /**
     * Purge specific cache tag (LSCache tag-based purging, falls back to purgeAll on PageCache).
     */
    public static function purgeTag(string $tag): void
    {
        if (static::isLiteSpeed() && class_exists(\Litespeed\LSCache\LSCache::class)) {
            try {
                \Litespeed\LSCache\LSCache::purge($tag);
                return;
            } catch (\Throwable $e) {
                Log::warning("LSCache::purge tag '{$tag}' failed: " . $e->getMessage());
            }
        }

        // Fallback for non-LiteSpeed: purge all
        PageCache::purgeAll();
    }
}
