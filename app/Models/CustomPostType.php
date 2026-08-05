<?php

namespace App\Models;

use App\Services\Sitemap\SitemapBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class CustomPostType extends Model
{
    protected $fillable = [
        'name',
        'singular_label',
        'plural_label',
        'slug',
        'icon',
        'description',
        'is_hierarchical',
        'show_in_menu',
        'show_in_rest',
        'has_archive',
        'publicly_queryable',
        'supports',
        'settings',
        'is_active',
        'translations',
    ];

    protected $casts = [
        'is_hierarchical' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_rest' => 'boolean',
        'has_archive' => 'boolean',
        'publicly_queryable' => 'boolean',
        'supports' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'translations' => 'array',
    ];

    /**
     * Default supports values
     */
    public static array $defaultSupports = [
        'title',
        'editor',
        'thumbnail',
        'excerpt',
        'author',
    ];

    /**
     * Available support options
     */
    public static array $availableSupports = [
        'title' => 'Title',
        'editor' => 'Content Editor',
        'thumbnail' => 'Featured Image',
        'excerpt' => 'Excerpt',
        'author' => 'Author',
        'comments' => 'Comments',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cpt) {
            if (empty($cpt->slug)) {
                $cpt->slug = Str::slug($cpt->name, '_');
            }
            if (empty($cpt->supports)) {
                $cpt->supports = self::$defaultSupports;
            }
        });

        static::saved(function ($cpt) {
            SitemapBuilder::clearCache($cpt->slug);
        });

        static::deleted(function ($cpt) {
            SitemapBuilder::clearCache($cpt->slug);
        });
    }

    /**
     * Get meta fields for this CPT
     */
    public function metaFields(): MorphMany
    {
        return $this->morphMany(MetaField::class, 'fieldable')->orderBy('order');
    }

    /**
     * Get taxonomies attached to this CPT
     */
    public function taxonomies()
    {
        return CustomTaxonomy::where('is_active', true)
            ->whereJsonContains('post_types', $this->slug)
            ->get();
    }

    /**
     * Get the table name for this CPT's content
     */
    public function getContentTableName(): string
    {
        return 'cpt_'.$this->slug;
    }

    /**
     * Scope for active CPTs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for CPTs shown in menu
     */
    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    /**
     * Get the route name for this CPT
     */
    public function getRouteNameAttribute(): string
    {
        return 'admin.cpt.'.$this->slug;
    }

    /**
     * Get the admin URL for listing entries
     */
    public function getAdminUrlAttribute(): string
    {
        return route('admin.cpt.entries.index', $this->slug);
    }

    /**
     * Check if this CPT supports a feature
     */
    public function supports(string $feature): bool
    {
        return in_array($feature, $this->supports ?? []);
    }

    /**
     * Scope for CPTs that have a public archive enabled.
     */
    public function scopeWithArchive($query)
    {
        return $query->where('has_archive', true)->where('is_active', true);
    }

    /**
     * Scope for CPTs where each entry has its own URL page.
     */
    public function scopePubliclyQueryable($query)
    {
        return $query->where('publicly_queryable', true)->where('is_active', true);
    }

    /**
     * Get a translated field value for the CPT.
     */
    public function getTranslation(string $field, ?string $locale = null, bool $fallback = true): ?string
    {
        $locale ??= app()->getLocale();
        $translations = $this->translations ?? [];

        if (isset($translations[$locale][$field]) && filled($translations[$locale][$field])) {
            return $translations[$locale][$field];
        }

        if ($fallback) {
            $defaultLocale = setting('default_locale', config('app.locale', 'en'));
            if ($locale !== $defaultLocale && isset($translations[$defaultLocale][$field]) && filled($translations[$defaultLocale][$field])) {
                return $translations[$defaultLocale][$field];
            }

            return match ($field) {
                'singular_label' => $this->singular_label,
                'plural_label' => $this->plural_label,
                'slug' => $this->slug,
                'description' => $this->description,
                default => null,
            };
        }

        return null;
    }

    /**
     * Get the localized slug for this CPT based on locale.
     */
    public function getLocalizedSlug(?string $locale = null): string
    {
        return $this->getTranslation('slug', $locale) ?: $this->slug;
    }

    /**
     * Get all registered localized slugs for this CPT.
     */
    public function getAllLocalizedSlugs(): array
    {
        $slugs = [$this->slug];
        if (is_array($this->translations)) {
            foreach ($this->translations as $lang => $data) {
                if (! empty($data['slug'])) {
                    $slugs[] = $data['slug'];
                }
            }
        }

        return array_values(array_unique(array_filter($slugs)));
    }

    /**
     * Get the public archive URL for this CPT.
     */
    public function getArchiveUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        $targetSlug = $this->getLocalizedSlug($locale);

        if ($locale !== $defaultLocale) {
            $url = url('/'.$locale.'/'.$targetSlug);
        } else {
            $url = url('/'.$targetSlug);
        }

        return apply_filters('cpt.archive_url', $url, $this, $locale);
    }
}
