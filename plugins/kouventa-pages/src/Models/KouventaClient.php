<?php

namespace Plugins\KouventaPages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KouventaClient extends Model
{
    protected $table = 'kouventa_clients';

    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'alt_text',
        'max_height',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'max_height' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active clients ordered by sort_order.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Resolve the public URL of the logo.
     */
    public function getLogoUrl(): string
    {
        return resolve_block_asset($this->logo);
    }
}

