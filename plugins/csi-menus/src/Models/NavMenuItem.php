<?php

namespace Plugins\CsiMenus\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavMenuItem extends Model
{
    use HasTranslations;

    protected $table = 'csi_nav_menu_items';

    protected array $translatable = ['title', 'url'];

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'item_type',
        'css_class',
        'translations',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavMenu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavMenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Localized title with fallback to primary value.
     */
    public function localizedTitle(?string $locale = null): string
    {
        return (string) ($this->getTranslation('title', $locale) ?? $this->getRawOriginal('title') ?? '');
    }

    /**
     * Resolve localized URL, keeping anchors and external URLs intact and localizing internal paths.
     */
    public function resolvedUrl(?string $locale = null): string
    {
        $raw = (string) ($this->getTranslation('url', $locale) ?? $this->getRawOriginal('url') ?? '#');
        $raw = trim($raw);

        if ($raw === '' || $raw === '#' || str_starts_with($raw, '#') || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'mailto:') || str_starts_with($raw, 'tel:')) {
            return $raw ?: '#';
        }

        return function_exists('localized_url') ? localized_url($raw, $locale) : url($raw);
    }
}
