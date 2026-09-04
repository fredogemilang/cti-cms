<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SearchIndex extends Model
{
    public $timestamps = false;

    protected $table = 'search_index';

    protected $fillable = [
        'searchable_type',
        'searchable_id',
        'locale',
        'title',
        'excerpt',
        'body',
        'url',
        'indexed_at',
    ];

    protected $casts = [
        'indexed_at' => 'datetime',
    ];

    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }
}
