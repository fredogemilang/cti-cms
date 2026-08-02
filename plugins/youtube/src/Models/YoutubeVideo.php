<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class YoutubeVideo extends Model
{
    protected $table = 'youtube_videos';

    protected $fillable = [
        'youtube_id',
        'title',
        'description',
        'thumbnail_default',
        'thumbnail_medium',
        'thumbnail_high',
        'channel_title',
        'published_at',
        'duration',
        'duration_seconds',
        'view_count',
        'like_count',
        'tags',
        'is_featured',
        'is_visible',
        'sort_order',
        'synced_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'synced_at' => 'datetime',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(YoutubePlaylist::class, 'youtube_playlist_videos', 'video_id', 'playlist_id')
            ->withPivot('position');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $mins, $secs);
        }

        return sprintf('%d:%02d', $mins, $secs);
    }

    public function getFormattedViewsAttribute(): string
    {
        $views = $this->view_count;
        if ($views >= 1000000) {
            return round($views / 1000000, 1).'M';
        }
        if ($views >= 1000) {
            return round($views / 1000, 1).'K';
        }

        return (string) $views;
    }

    public function getYoutubeUrlAttribute(): string
    {
        return "https://www.youtube.com/watch?v={$this->youtube_id}";
    }
}
