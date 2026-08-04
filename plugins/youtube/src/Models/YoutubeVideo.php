<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'tags' => 'array',
    ];

    public function getUrl(): string
    {
        $id = $this->youtube_id ?: ($this->video_id ?? '');
        return "https://www.youtube.com/watch?v={$id}";
    }

    public function getEmbedUrl(): string
    {
        $id = $this->youtube_id ?: ($this->video_id ?? '');
        return "https://www.youtube-nocookie.com/embed/{$id}";
    }

    public function getBestThumbnail(): string
    {
        if (! empty($this->thumbnail_high)) {
            return $this->thumbnail_high;
        }
        if (! empty($this->thumbnail_medium)) {
            return $this->thumbnail_medium;
        }
        if (! empty($this->thumbnail_default)) {
            return $this->thumbnail_default;
        }
        $id = $this->youtube_id ?: ($this->video_id ?? '');
        return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
    }
}
