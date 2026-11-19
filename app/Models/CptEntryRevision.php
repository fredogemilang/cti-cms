<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CptEntryRevision extends Model
{
    protected $fillable = [
        'cpt_entry_id',
        'user_id',
        'title',
        'slug',
        'status',
        'meta',
        'translations',
        'is_autosave',
    ];

    protected $casts = [
        'meta' => 'array',
        'translations' => 'array',
        'is_autosave' => 'boolean',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(CptEntry::class, 'cpt_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
