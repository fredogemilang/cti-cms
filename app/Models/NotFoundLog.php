<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NotFoundLog extends Model
{
    protected $fillable = [
        'path', 'full_url', 'referrer', 'user_agent',
        'hit_count', 'first_seen_at', 'last_seen_at',
        'is_resolved', 'redirect_id',
    ];

    protected $casts = [
        'hit_count' => 'integer',
        'is_resolved' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Throttle window in seconds — avoid DB writes on rapid-fire 404 hits
     * to the same path. Only updates once per window.
     */
    protected const THROTTLE_SECONDS = 300; // 5 minutes

    /**
     * Static asset extensions to ignore — no point logging missing .css/.js/images.
     */
    protected const IGNORED_EXTENSIONS = [
        'css', 'js', 'map', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'avif',
        'ico', 'woff', 'woff2', 'ttf', 'eot', 'otf', 'mp4', 'webm', 'pdf',
    ];

    /**
     * The redirect rule that was created to resolve this 404 (if any).
     */
    public function redirect(): BelongsTo
    {
        return $this->belongsTo(Redirect::class);
    }

    /**
     * Record or increment a 404 hit for the given path.
     *
     * Uses a cache-based throttle so the same path doesn't cause
     * a DB write on every single request — only once per THROTTLE window.
     */
    public static function recordHit(string $path, ?string $fullUrl = null, ?string $referrer = null, ?string $userAgent = null): void
    {
        // Skip static assets
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, static::IGNORED_EXTENSIONS, true)) {
            return;
        }

        // Normalize path
        $path = '/'.ltrim($path, '/');

        // Throttle: skip if we already logged this path recently
        $cacheKey = '404_throttle:'.md5($path);
        if (Cache::has($cacheKey)) {
            return;
        }

        // Upsert: create or increment
        try {
            $affected = DB::table('not_found_logs')
                ->where('path', $path)
                ->update([
                    'hit_count' => DB::raw('hit_count + 1'),
                    'last_seen_at' => now(),
                    'referrer' => $referrer ? mb_substr($referrer, 0, 1000) : null,
                    'user_agent' => $userAgent ? mb_substr($userAgent, 0, 500) : null,
                    'updated_at' => now(),
                ]);

            if ($affected === 0) {
                DB::table('not_found_logs')->insert([
                    'path' => mb_substr($path, 0, 500),
                    'full_url' => $fullUrl ? mb_substr($fullUrl, 0, 1000) : null,
                    'referrer' => $referrer ? mb_substr($referrer, 0, 1000) : null,
                    'user_agent' => $userAgent ? mb_substr($userAgent, 0, 500) : null,
                    'hit_count' => 1,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                    'is_resolved' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Swallow duplicate-key race conditions silently; report anything else.
            if (! str_contains($e->getMessage(), 'Duplicate entry')) {
                report($e);
            }
        }

        // Set throttle so we don't write again for this path within the window
        Cache::put($cacheKey, true, static::THROTTLE_SECONDS);
    }

    /**
     * Mark this 404 as resolved (e.g. after creating a redirect).
     */
    public function markResolved(?int $redirectId = null): void
    {
        $this->update([
            'is_resolved' => true,
            'redirect_id' => $redirectId,
        ]);
    }

    /**
     * Scope: only unresolved 404s.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope: order by most frequent hits.
     */
    public function scopeMostFrequent($query)
    {
        return $query->orderByDesc('hit_count');
    }

    /**
     * Scope: order by most recent.
     */
    public function scopeMostRecent($query)
    {
        return $query->orderByDesc('last_seen_at');
    }

    /**
     * Prune old resolved entries or entries that haven't been hit for a while.
     *
     * @param  int  $days  Prune entries older than this many days
     */
    public static function prune(int $days = 90): int
    {
        return static::where('is_resolved', true)
            ->where('last_seen_at', '<', now()->subDays($days))
            ->delete();
    }
}
