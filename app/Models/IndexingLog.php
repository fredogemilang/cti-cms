<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol',
        'url',
        'status_code',
        'response',
        'request_time',
        'entity_type',
        'entity_id',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'request_time' => 'datetime',
        ];
    }
}
