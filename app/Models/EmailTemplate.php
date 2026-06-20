<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key_name', 'name', 'subject', 'body_html', 'body_text',
        'variables', 'description', 'is_system',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_system' => 'boolean',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(EmailTemplateVersion::class, 'template_id')->orderByDesc('created_at');
    }

    public static function findByKey(string $key): ?self
    {
        return static::where('key_name', $key)->first();
    }
}
