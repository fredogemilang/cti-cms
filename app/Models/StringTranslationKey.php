<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $group
 * @property string $key
 * @property string|null $default_value
 */
class StringTranslationKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'default_value',
    ];

    /**
     * Get translations associated with this key.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(StringTranslation::class, 'translation_key_id');
    }

    /**
     * Get source locations associated with this key.
     */
    public function sources(): HasMany
    {
        return $this->hasMany(StringTranslationSource::class, 'translation_key_id');
    }

    /**
     * Scope query to a specific group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
