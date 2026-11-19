<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'locale',
        'title',
        'description',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image_id',
        'twitter_card',
        'schema_type',
        'schema_data',
        'focus_keyword',
        'seo_score',
        'readability_score',
        'ai_summary',
        'is_cornerstone',
    ];

    protected $casts = [
        'schema_data' => 'array',
        'seo_score' => 'integer',
        'readability_score' => 'integer',
        'is_cornerstone' => 'boolean',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_id');
    }

    public function isIndexable(): bool
    {
        return ! str_contains((string) $this->robots, 'noindex');
    }
}
