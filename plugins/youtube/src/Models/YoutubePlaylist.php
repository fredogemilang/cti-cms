<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_visible' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function videos()
    {
        return $this->belongsToMany(
            YoutubeVideo::class,
            'youtube_playlist_videos',
            'playlist_id',
            'video_id',
            'id',
            'id'
        )->withPivot('position');
    }
}
