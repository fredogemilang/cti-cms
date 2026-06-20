<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Webhook extends Model
{
    protected $fillable = [
        'name', 'url', 'events', 'signing_secret', 'headers', 'is_active', 'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class)->latest();
    }

    public static function generateSecret(): string
    {
        return Str::random(40);
    }

    public function listensTo(string $event): bool
    {
        return in_array($event, (array) $this->events, true);
    }
}
