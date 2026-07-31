<?php

namespace App\Models;

use App\Traits\HasSeoMeta;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CptEntry extends Model
{
    use HasSeoMeta, HasTranslations, SoftDeletes;

    protected $table = 'cpt_entries';

    protected $fillable = [
        'post_type_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'parent_id',
        'status',
        'published_at',
        'meta',
        'seo',
        'translations',
        'menu_order',
    ];

    protected $casts = [
        'meta' => 'array',
        'seo' => 'array',
        'translations' => 'array',
        'published_at' => 'datetime',
        'menu_order' => 'integer',
    ];

    /** Fields that can carry per-locale values via the translations JSON column. */
    protected array $translatable = ['title', 'slug', 'content', 'excerpt'];

    /**
     * Resolve a CptEntry by slug within a given post type, scanning the default
     * `slug` column first then each locale's translated slug. On a non-default
     * locale match, sets app()->setLocale() so the request renders accordingly.
     */
    public static function findByLocalizedSlug(CustomPostType|string $postType, string $slug): ?self
    {
        $postTypeId = $postType instanceof CustomPostType
            ? $postType->id
            : CustomPostType::where('slug', $postType)->value('id');

        if (! $postTypeId) {
            return null;
        }

        $base = static::query()
            ->where('post_type_id', $postTypeId)
            ->where('status', 'published');

        $entry = (clone $base)->where('slug', $slug)->first();
        if ($entry) {
            return $entry;
        }

        $defaultLocale = static::defaultLocale();
        $locales = array_filter(available_locales(), fn ($l) => $l !== $defaultLocale);

        foreach ($locales as $locale) {
            $entry = (clone $base)
                ->whereRaw('JSON_EXTRACT(translations, ?) = ?', ["$.\"{$locale}\".slug", $slug])
                ->first();
            if ($entry) {
                app()->setLocale($locale);

                return $entry;
            }
        }

        return null;
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->slug)) {
                $entry->slug = Str::slug($entry->title);
            }
            if (empty($entry->author_id)) {
                $entry->author_id = auth()->id();
            }
        });
    }

    /**
     * Get the post type this entry belongs to
     */
    public function postType(): BelongsTo
    {
        return $this->belongsTo(CustomPostType::class, 'post_type_id');
    }

    /**
     * Get the author of this entry
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the parent entry (for hierarchical CPTs)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CptEntry::class, 'parent_id');
    }

    /**
     * Get the children entries (for hierarchical CPTs)
     */
    public function children(): HasMany
    {
        return $this->hasMany(CptEntry::class, 'parent_id')->orderBy('menu_order');
    }

    /**
     * Get the taxonomy terms attached to this entry
     */
    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'cpt_entry_term', 'entry_id', 'term_id');
    }

    /**
     * Get terms for a specific taxonomy
     */
    public function termsForTaxonomy(int $taxonomyId)
    {
        return $this->terms()->where('taxonomy_id', $taxonomyId)->get();
    }

    /**
     * Get related child entries for a given meta field (Relationship)
     */
    public function relatedEntries($metaField = null): BelongsToMany
    {
        $query = $this->belongsToMany(
            CptEntry::class,
            'cpt_entry_relationships',
            'parent_entry_id',
            'child_entry_id'
        )
            ->withPivot('order')
            ->orderBy('cpt_entry_relationships.order');

        if ($metaField !== null) {
            $fieldId = is_numeric($metaField)
                ? $metaField
                : MetaField::where('name', $metaField)
                    ->where('fieldable_type', CustomPostType::class)
                    ->where('fieldable_id', $this->post_type_id)
                    ->value('id');
            $query->wherePivot('meta_field_id', $fieldId);
        }

        return $query;
    }

    /**
     * Get parent entries that relate to this entry via a given meta field
     */
    public function parentRelatedEntries($metaField = null): BelongsToMany
    {
        $query = $this->belongsToMany(
            CptEntry::class,
            'cpt_entry_relationships',
            'child_entry_id',
            'parent_entry_id'
        )
            ->withPivot('order')
            ->orderBy('cpt_entry_relationships.order');

        if ($metaField !== null) {
            $fieldId = is_numeric($metaField)
                ? $metaField
                : MetaField::where('name', $metaField)->value('id');
            $query->wherePivot('meta_field_id', $fieldId);
        }

        return $query;
    }

    /**
     * Scope for published entries
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for entries of a specific post type
     */
    public function scopeOfType($query, $postTypeId)
    {
        return $query->where('post_type_id', $postTypeId);
    }

    /**
     * Scope for entries by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get a meta value (with locale translation support and automatic English fallback)
     */
    public function getMeta(string $key, $default = null)
    {
        $locale = app()->getLocale();
        $defaultLocale = static::defaultLocale();
        $meta = parent::getAttribute('meta') ?? [];

        if ($locale !== $defaultLocale && isset($meta['_translations'][$locale][$key]) && $meta['_translations'][$locale][$key] !== '') {
            $translatedVal = $meta['_translations'][$locale][$key];
            $defaultVal = $meta[$key] ?? null;

            // If both default and translation are arrays (e.g., benefits_cards, features, solutions_other)
            if (is_array($translatedVal) && is_array($defaultVal)) {
                foreach ($translatedVal as $idx => &$item) {
                    if (is_array($item) && isset($defaultVal[$idx]) && is_array($defaultVal[$idx])) {
                        // Always inherit fresh icon/image/logo/media from default locale (EN)
                        foreach (['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'] as $mediaKey) {
                            if (isset($defaultVal[$idx][$mediaKey])) {
                                $item[$mediaKey] = $defaultVal[$idx][$mediaKey];
                            }
                        }
                    }
                }
                unset($item);
            }

            return $translatedVal;
        }

        return $meta[$key] ?? $default;
    }

    /**
     * Set a meta value
     */
    public function setMeta(string $key, $value): void
    {
        $meta = $this->meta ?? [];
        $meta[$key] = $value;
        $this->meta = $meta;
    }

    /**
     * Get status badge info for display
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'published' => ['color' => 'green', 'label' => 'Published'],
            'draft' => ['color' => 'gray', 'label' => 'Draft'],
            'scheduled' => ['color' => 'blue', 'label' => 'Scheduled'],
            'archived' => ['color' => 'amber', 'label' => 'Archived'],
            default => ['color' => 'gray', 'label' => ucfirst($this->status)],
        };
    }

    /**
     * Get the public frontend URL for this entry, respecting the current locale.
     * Uses translated slugs so /products/what-is-cdt and /products/apa-itu-cdt
     * both resolve to the same entry while keeping the locale in the URL.
     */
    public function getUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = static::defaultLocale();
        $localePrefix = ($locale !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
            ? '/'.$locale
            : '';

        $cptSlug = $this->relationLoaded('postType') && $this->postType instanceof CustomPostType
            ? $this->postType->slug
            : (string) CustomPostType::where('id', $this->post_type_id)->value('slug');

        $entrySlug = $this->getTranslation('slug', $locale, fallback: true) ?? $this->slug;

        /** @var CptEntry|null $parentRelated */
        $parentRelated = $this->parentRelatedEntries()->first();
        if ($parentRelated && $parentRelated->post_type_id !== $this->post_type_id) {
            $parentSlug = $parentRelated->getTranslation('slug', $locale, fallback: true) ?? $parentRelated->slug;
            // Use parent's CPT slug so sub-products appear under the parent's URL namespace
            $parentCptSlug = CustomPostType::where('id', $parentRelated->post_type_id)->value('slug') ?? $cptSlug;

            $url = url($localePrefix.'/'.$parentCptSlug.'/'.$parentSlug.'/'.$entrySlug);

            return apply_filters('cpt_entry.url', $url, $this, $locale);
        }

        /** @var CptEntry|null $hierarchicalParent */
        $hierarchicalParent = $this->parent;
        if ($hierarchicalParent) {
            $parentSlug = $hierarchicalParent->getTranslation('slug', $locale, fallback: true) ?? $hierarchicalParent->slug;

            $url = url($localePrefix.'/'.$cptSlug.'/'.$parentSlug.'/'.$entrySlug);

            return apply_filters('cpt_entry.url', $url, $this, $locale);
        }

        $url = url($localePrefix.'/'.$cptSlug.'/'.$entrySlug);

        return apply_filters('cpt_entry.url', $url, $this, $locale);
    }

    /**
     * Get the previous published entry (by published_at) within the same CPT.
     */
    public function getPreviousEntry(): ?self
    {
        return static::where('post_type_id', $this->post_type_id)
            ->where('status', 'published')
            ->where('published_at', '<', $this->published_at)
            ->orderByDesc('published_at')
            ->first();
    }

    /**
     * Get the next published entry (by published_at) within the same CPT.
     */
    public function getNextEntry(): ?self
    {
        return static::where('post_type_id', $this->post_type_id)
            ->where('status', 'published')
            ->where('published_at', '>', $this->published_at)
            ->orderBy('published_at')
            ->first();
    }

    /**
     * Generate Schema.org JSON-LD structured data including relationship variants/related items
     */
    public function getSchemaJsonLd(): array
    {
        $url = $this->getUrl();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Thing',
            'name' => $this->title,
            'description' => $this->getResolvedSeoDescription() ?? $this->excerpt,
            'url' => $url,
        ];

        if ($this->featured_image) {
            $schema['image'] = asset('storage/'.$this->featured_image);
        }

        $relationshipFields = MetaField::where('fieldable_type', CustomPostType::class)
            ->where('fieldable_id', $this->post_type_id)
            ->where('type', 'relationship')
            ->get();

        $relatedItems = [];
        foreach ($relationshipFields as $field) {
            $children = $this->relatedEntries($field->id)->where('status', 'published')->get();
            foreach ($children as $child) {
                /** @var CptEntry $child */
                $relatedItems[] = [
                    '@type' => 'Thing',
                    'name' => $child->title,
                    'url' => $child->getUrl(),
                ];
            }
        }

        if (! empty($relatedItems)) {
            $schema['isRelatedTo'] = $relatedItems;
        }

        return $schema;
    }
}
