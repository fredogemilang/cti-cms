<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class YoutubePlaylist extends Model
{
    protected $table = 'youtube_playlists';

    protected $fillable = [
        'youtube_id',
        'title',
        'description',
        'thumbnail_url',
        'video_count',
        'is_visible',
        'sort_order',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
        'is_visible' => 'boolean',
        'video_count' => 'integer',
    ];

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(YoutubeVideo::class, 'youtube_playlist_videos', 'playlist_id', 'video_id')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function getYoutubeUrlAttribute(): string
    {
        return "https://www.youtube.com/playlist?list={$this->youtube_id}";
    }
}
