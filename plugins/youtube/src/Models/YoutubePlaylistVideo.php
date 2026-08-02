<?php

namespace Plugins\Youtube\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YoutubePlaylistVideo extends Model
{
    protected $table = 'youtube_playlist_videos';

    protected $fillable = [
        'playlist_id',
        'video_id',
        'position',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(YoutubePlaylist::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(YoutubeVideo::class);
    }
}
