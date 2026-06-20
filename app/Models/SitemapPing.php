<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitemapPing extends Model
{
    protected $fillable = [
        'target',
        'url',
        'status',
        'response_code',
        'response_body',
        'pinged_at',
    ];

    protected $casts = [
        'pinged_at' => 'datetime',
    ];
}
