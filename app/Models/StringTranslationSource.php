<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StringTranslationSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_key_id',
        'source_type',
        'source_name',
        'source_file',
    ];

    /**
     * Get the parent translation key.
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(StringTranslationKey::class, 'translation_key_id');
    }
}
