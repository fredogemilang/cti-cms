<?php

namespace Plugins\KouventaPages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KouventaVideo extends Model
{
    protected $table = 'kouventa_videos';

    protected $fillable = [
        'title',
        'youtube_id',
        'youtube_url',
        'category',
        'thumbnail',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active videos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order videos by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Extract 11-char YouTube ID from any YouTube URL or direct ID.
     */
    public static function extractYoutubeId(?string $urlOrId): ?string
    {
        if (empty($urlOrId)) {
            return null;
        }

        $urlOrId = trim($urlOrId);

        // Direct 11-char alphanumeric ID
        if (preg_match('/^[a-zA-Z0-9_\-]{11}$/', $urlOrId)) {
            return $urlOrId;
        }

        // Match youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID, youtube.com/shorts/ID
        if (preg_match('#(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})#i', $urlOrId, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Resolve the public URL of the video thumbnail.
     */
    public function getThumbnailUrl(): string
    {
        if (! empty($this->thumbnail)) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }

            if (function_exists('resolve_block_asset')) {
                return resolve_block_asset($this->thumbnail);
            }

            return asset($this->thumbnail);
        }

        // Check local theme image asset
        if (! empty($this->youtube_id)) {
            $localPath = public_path('themes/kouventa/assets/images/youtube/' . $this->youtube_id . '.jpg');
            if (file_exists($localPath) && function_exists('theme_asset')) {
                return theme_asset('images/youtube/' . $this->youtube_id . '.jpg');
            }

            return "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }

        return '';
    }

    /**
     * Get the clean watch URL on YouTube.
     */
    public function getWatchUrl(): string
    {
        if (! empty($this->youtube_url)) {
            return $this->youtube_url;
        }

        return "https://www.youtube.com/watch?v={$this->youtube_id}";
    }

    /**
     * Get the privacy-enhanced embed URL for the modal player.
     */
    public function getEmbedUrl(): string
    {
        return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?autoplay=1&rel=0";
    }
}
