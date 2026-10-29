<?php

namespace Plugins\KouventaPages\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KouventaPageRevision extends Model
{
    protected $table = 'kouventa_page_revisions';

    protected $fillable = [
        'page_id',
        'user_id',
        'title',
        'sections',
        'translations',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'translations' => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(KouventaPage::class, 'page_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

