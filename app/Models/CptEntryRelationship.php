<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CptEntryRelationship extends Model
{
    protected $table = 'cpt_entry_relationships';

    protected $fillable = [
        'parent_entry_id',
        'child_entry_id',
        'meta_field_id',
        'order',
    ];

    public function parentEntry(): BelongsTo
    {
        return $this->belongsTo(CptEntry::class, 'parent_entry_id');
    }

    public function childEntry(): BelongsTo
    {
        return $this->belongsTo(CptEntry::class, 'child_entry_id');
    }

    public function metaField(): BelongsTo
    {
        return $this->belongsTo(MetaField::class, 'meta_field_id');
    }
}
