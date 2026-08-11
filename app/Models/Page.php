<?php

namespace App\Models;

use App\Services\SeoRenderer;
use App\Services\ThemeLoader;
use App\Traits\HasSanitizedContent;
use App\Traits\HasSeoMeta;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasSanitizedContent, HasSeoMeta, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'menu_order',
        'status',
        'published_at',
        'author_id',
        'updated_by',
        'template',
        'featured_image',
        'seo',
        'settings',
        'translations',
        'is_system',
        'locked_by',
        'locked_at',
    ];

    /** Fields that can carry per-locale values via the translations JSON column. */
    protected array $translatable = ['title', 'slug', 'seo'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'seo' => 'array',
            'settings' => 'array',
            'translations' => 'array',
            'menu_order' => 'integer',
            'is_system' => 'boolean',
            'locked_at' => 'datetime',
        ];
    }

    // Available templates (static fallback when theme defines no page_templates)
    public static array $templates = [
        'default' => 'Default',
        'full-width' => 'Full Width',
        'landing' => 'Landing Page',
        'sidebar-left' => 'Sidebar Left',
        'sidebar-right' => 'Sidebar Right',
    ];

    /**
     * Get available page templates from the active theme, fallback to legacy list.
     */
    public static function getTemplates(): array
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        if ($theme) {
            $templates = $theme->getPageTemplates();
            if (! empty($templates)) {
                return collect($templates)
                    ->mapWithKeys(fn ($t, $key) => [$key => $t['label'] ?? ucfirst($key)])
                    ->toArray();
            }
        }

        return static::$templates;
    }

    // === RELATIONSHIPS ===

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('menu_order');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->whereNull('parent_block_id')->orderBy('order');
    }

    public function allBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('order');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class)->orderBy('created_at', 'desc');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    // === SCOPES ===

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopePrivate($query)
    {
        return $query->where('status', 'private');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeUserCreated($query)
    {
        return $query->where(function ($q) {
            $q->where('is_system', false)->orWhereNull('is_system');
        });
    }

    // === HELPERS ===

    public function getBlock(string $name): ?PageBlock
    {
        return $this->allBlocks()->where('name', $name)->first();
    }

    public function getBlockValue(string $name, $default = null)
    {
        $block = $this->getBlock($name);

        return $block ? $block->localizedValue() : $default;
    }

    /**
     * Shortcut alias for getBlockValue().
     */
    public function block(string $name, $default = null)
    {
        return $this->getBlockValue($name, $default);
    }

    /**
     * Get array payload of a repeater block.
     */
    public function repeaterBlock(string $name, array $default = []): array
    {
        $value = $this->block($name);
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $default;
    }

    /**
     * Get array payload of a button block ['text' => '...', 'url' => '...', 'target' => '...'].
     */
    public function buttonBlock(string $name, array $default = []): array
    {
        $value = $this->block($name);
        if (is_array($value)) {
            return array_merge(['text' => '', 'url' => '#', 'target' => '_self'], $value);
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_merge(['text' => '', 'url' => '#', 'target' => '_self'], $decoded);
            }
        }

        return $default;
    }

    /**
     * Get array payload of a title block ['prefix' => '...', 'main' => '...'].
     */
    public function titleBlock(string $name, array $default = []): array
    {
        $value = $this->block($name);
        if (is_array($value)) {
            return array_merge(['prefix' => '', 'main' => ''], $value);
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_merge(['prefix' => '', 'main' => ''], $decoded);
            }
            return ['prefix' => '', 'main' => $value];
        }

        return $default;
    }

    /**
     * Get array payload of a card block ['title' => '...', 'description' => '...', 'image' => '...', 'button_text' => '...', 'button_url' => '...'].
     */
    public function cardBlock(string $name, array $default = []): array
    {
        $value = $this->block($name);
        $defaults = ['title' => '', 'description' => '', 'image' => '', 'button_text' => '', 'button_url' => '#'];
        if (is_array($value)) {
            return array_merge($defaults, $value);
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_merge($defaults, $decoded);
            }
            return array_merge($defaults, ['title' => $value]);
        }

        return array_merge($defaults, $default);
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' &&
            (! $this->published_at || $this->published_at->isPast());
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isPrivate(): bool
    {
        return $this->status === 'private';
    }

    public function isLocked(): bool
    {
        return $this->locked_by !== null && $this->locked_at !== null && Carbon::parse($this->locked_at)->diffInMinutes(now()) < 2;
    }

    public function isLockedByOther(?int $userId): bool
    {
        return $this->isLocked() && $this->locked_by !== $userId;
    }

    public function lock(int $userId): void
    {
        $this->update([
            'locked_by' => $userId,
            'locked_at' => now(),
        ]);
    }

    public function unlock(): void
    {
        $this->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    public function ancestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;
        $seen = [$this->id];
        $maxDepth = 20;

        while ($current && $maxDepth-- > 0) {
            if (in_array($current->id, $seen, true)) {
                break; // Circular reference detected
            }
            $seen[] = $current->id;
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    public function descendants(): Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    public function getFullPath(): string
    {
        $path = $this->slug;
        $ancestors = $this->ancestors();

        foreach ($ancestors->reverse() as $ancestor) {
            $path = $ancestor->slug.'/'.$path;
        }

        return $path;
    }

    public function hasTranslationForLocale(string $locale): bool
    {
        if ($locale === static::defaultLocale()) {
            return true;
        }

        if (! empty($this->translations[$locale]) && is_array($this->translations[$locale]) && count(array_filter($this->translations[$locale])) > 0) {
            return true;
        }

        $transSlug = $this->getTranslation('slug', $locale, false);
        if (! empty($transSlug) && trim((string) $transSlug) !== '') {
            return true;
        }

        $transTitle = $this->getTranslation('title', $locale, false);

        return ! empty($transTitle) && trim((string) $transTitle) !== '';
    }

    public function getUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = static::defaultLocale();

        if ($locale !== $defaultLocale && ! $this->hasTranslationForLocale($locale)) {
            $locale = $defaultLocale;
        }

        $prefix = ($locale !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
            ? '/'.$locale
            : '';

        $slug = $this->getTranslation('slug', $locale, false) ?: $this->slug;

        // Homepage resolves to root URL, not /home
        if ($slug === 'home' || $this->slug === 'home') {
            return url($prefix ?: '/');
        }

        return url($prefix.'/'.$slug);
    }

    /**
     * Resolve a Page by slug, scanning the default `slug` column first and then
     * each locale's translated slug. When the match is on a non-default locale,
     * also sets app()->setLocale() so the request renders in that language.
     */
    public static function findByLocalizedSlug(string $slug): ?self
    {
        // Default column match (default locale)
        $page = static::published()->where('slug', $slug)->first();
        if ($page) {
            return $page;
        }

        // Scan translated slugs across all configured locales
        $defaultLocale = static::defaultLocale();
        $locales = array_filter(available_locales(), fn ($l) => $l !== $defaultLocale);

        foreach ($locales as $locale) {
            // JSON_EXTRACT path: $.{locale}.slug
            $page = static::published()
                ->whereRaw('JSON_EXTRACT(translations, ?) = ?', ["$.\"{$locale}\".slug", $slug])
                ->first();
            if ($page) {
                app()->setLocale($locale);

                return $page;
            }
        }

        return null;
    }



    /**
     * Return a URL for this page in the given locale (uses that locale's slug if defined).
     */
    public function localizedUrl(string $locale): string
    {
        $defaultLocale = static::defaultLocale();
        $prefix = ($locale !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
            ? '/'.$locale
            : '';

        $slug = $this->getTranslation('slug', $locale) ?? $this->slug;

        return url($prefix.'/'.ltrim($slug, '/'));
    }

    public function getMetaTitle(): string
    {
        $resolved = app(SeoRenderer::class)->resolve($this);

        return $resolved['title'];
    }

    public function getMetaDescription(): ?string
    {
        return $this->seo['meta_description'] ?? null;
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if (auth()->check()) {
                $page->updated_by = auth()->id();
            }
        });

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }

            // Ensure unique slug
            $originalSlug = $page->slug;
            $counter = 1;
            while (static::where('slug', $page->slug)->exists()) {
                $page->slug = $originalSlug.'-'.$counter;
                $counter++;
            }
        });

        static::updating(function ($page) {
            // Protect system page slug and template from being changed
            if ($page->is_system) {
                if ($page->isDirty('slug')) {
                    $page->slug = $page->getOriginal('slug');
                }
                if ($page->isDirty('template')) {
                    $page->template = $page->getOriginal('template');
                }
            }

            // Ensure unique slug on update
            if ($page->isDirty('slug')) {
                $originalSlug = $page->slug;
                $counter = 1;
                while (static::where('slug', $page->slug)->where('id', '!=', $page->id)->exists()) {
                    $page->slug = $originalSlug.'-'.$counter;
                    $counter++;
                }
            }
        });

        static::deleting(function ($page) {
            // Prevent soft-deleting system pages
            if ($page->is_system) {
                return false;
            }
        });
    }
}
