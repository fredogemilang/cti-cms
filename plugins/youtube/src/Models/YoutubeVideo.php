<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    protected $table = 'youtube_videos';

    protected $fillable = [
        'video_id',
        'title',
        'description',
        'category',
        'thumbnail_url',
        'duration',
        'author',
        'published_at',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getUrl(): string
    {
        return "https://www.youtube.com/watch?v={$this->video_id}";
    }

    public function getEmbedUrl(): string
    {
        return "https://www.youtube-nocookie.com/embed/{$this->video_id}";
    }
}
