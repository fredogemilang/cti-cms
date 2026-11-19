<?php

namespace App\Services;

use App\Http\Middleware\PageCache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Litespeed\LSCache\LSCache;

class CacheManager
{
    protected static bool $purgeRequested = false;

    /**
     * Check if running on a LiteSpeed web server.
     */
    public static function isLiteSpeed(): bool
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';

        return stripos($serverSoftware, 'LiteSpeed') !== false || isset($_SERVER['X-LSCACHE']);
    }

    /**
     * Check if a purge was requested during this lifecycle.
     */
    public static function isPurgeRequested(): bool
    {
        return static::$purgeRequested;
    }

    /**
     * Purge all cached pages across both LSCache (if available) and PageCache.
     */
    public static function purgeAll(): void
    {
        static::$purgeRequested = true;

        // 1. LiteSpeed Web Server Native Response Header (works for all LiteSpeed hosts)
        if (! headers_sent()) {
            @header('X-LiteSpeed-Purge: *');
        }

        // 2. Third-party package if installed
        if (class_exists(LSCache::class)) {
            try {
                LSCache::purge('*');
            } catch (\Throwable $e) {
                Log::warning('LSCache::purgeAll failed: '.$e->getMessage());
            }
        }

        // 3. Clear direct filesystem lscache if running locally or via CLI on cPanel
        static::purgeDiskLSCache();

        // 4. Always purge Laravel-level PageCache fallback
        PageCache::purgeAll();
    }

    /**
     * Purge specific cache tag (LSCache tag-based purging, falls back to purgeAll on PageCache).
     */
    public static function purgeTag(string $tag): void
    {
        if (! headers_sent()) {
            @header('X-LiteSpeed-Purge: '.$tag);
        }

        if (static::isLiteSpeed() && class_exists(LSCache::class)) {
            try {
                LSCache::purge($tag);

                return;
            } catch (\Throwable $e) {
                Log::warning("LSCache::purge tag '{$tag}' failed: ".$e->getMessage());
            }
        }

        // Fallback for non-LiteSpeed: purge all
        static::purgeAll();
    }

    /**
     * Clean filesystem LiteSpeed cache directory if accessible (e.g. /home/user/lscache).
     */
    protected static function purgeDiskLSCache(): void
    {
        $homeDir = getenv('HOME') ?: ($_SERVER['HOME'] ?? null);
        $candidates = array_filter([
            env('LSCACHE_DIR'),
            $homeDir ? rtrim($homeDir, '/').'/lscache' : null,
            '/tmp/lshttpd/swap',
        ]);

        foreach ($candidates as $dir) {
            if ($dir && is_dir($dir) && is_writable($dir)) {
                try {
                    $items = File::glob($dir.'/*');
                    foreach ($items as $item) {
                        if (is_dir($item)) {
                            File::cleanDirectory($item);
                        } elseif (is_file($item) && ! str_starts_with(basename($item), '.')) {
                            @unlink($item);
                        }
                    }
                } catch (\Throwable $e) {
                    // Suppress permission errors silently
                }
            }
        }
    }
}
