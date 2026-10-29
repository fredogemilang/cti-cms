<?php

namespace Plugins\KouventaPages\Models;

use App\Models\User;
use App\Traits\FindsByLocalizedSlug;
use App\Traits\HasSeoMeta;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KouventaPage extends Model
{
    use FindsByLocalizedSlug, HasSeoMeta, HasTranslations, SoftDeletes;

    protected $table = 'kouventa_pages';

    protected $fillable = [
        'slug',
        'title',
        'template',
        'status',
        'published_at',
        'sections',
        'translations',
        'settings',
        'seo',
        'author_id',
        'updated_by',
    ];

    /** Fields that can carry per-locale values via the translations JSON column. */
    protected array $translatable = ['title', 'slug', 'sections', 'seo'];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'translations' => 'array',
            'settings' => 'array',
            'seo' => 'array',
            'published_at' => 'datetime',
        ];
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

    public function revisions(): HasMany
    {
        return $this->hasMany(KouventaPageRevision::class, 'page_id')->latest();
    }

    // === SCOPES ===

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // === SECTION & BLOCK DATA ACCESSORS ===

    /**
     * Get a specific section's data for the current locale (with automatic fallback to default).
     */
    public function section(string $name, array $default = []): array
    {
        $sections = $this->sections ?? [];

        return $sections[$name] ?? $default;
    }

    /**
     * Backward-compatible block reader matching Core Page::block().
     */
    public function block(string $name, $default = null): mixed
    {
        $sections = $this->sections ?? [];

        if (array_key_exists($name, $sections)) {
            return $sections[$name];
        }

        // Check common section mappings (hero, challenges, journey, products, cta)
        foreach ($sections as $sec) {
            if (is_array($sec) && array_key_exists($name, $sec)) {
                return $sec[$name];
            }
        }

        return $default;
    }

    /**
     * Backward-compatible titleBlock reader matching Core Page::titleBlock().
     */
    public function titleBlock(string $name, array $default = []): array
    {
        $val = $this->block($name);
        if (is_array($val)) {
            return array_merge($default, array_filter($val, fn ($v) => $v !== null && $v !== ''));
        }

        return $default;
    }

    /**
     * Backward-compatible buttonBlock reader matching Core Page::buttonBlock().
     */
    public function buttonBlock(string $name, array $default = []): array
    {
        $val = $this->block($name);
        if (is_array($val)) {
            return array_merge(['target' => '_self'], $default, array_filter($val, fn ($v) => $v !== null && $v !== ''));
        }

        return $default;
    }

    /**
     * Flattened text content accessor for search indexing (IndexSearchable / ContentTextExtractor).
     */
    public function getContentAttribute(): string
    {
        $sections = $this->sections ?? [];
        $parts = [];

        array_walk_recursive($sections, function ($item) use (&$parts) {
            if (is_string($item) && ! empty($item) && ! str_starts_with($item, '<svg') && ! str_starts_with($item, 'data:image')) {
                $parts[] = strip_tags($item);
            }
        });

        return implode(' ', $parts);
    }

    /**
     * Get full URL for this page, taking into account current/requested locale.
     */
    public function getUrl(?string $locale = null): string
    {
        $slug = ltrim($this->slug, '/');

        if (function_exists('localized_url')) {
            return localized_url('/'.$slug, $locale);
        }

        return url('/'.$slug);
    }

    public function getBreadcrumbTitle(): string
    {
        return $this->title ?? 'Page';
    }

    /**
     * Save a revision snapshot and prune older revisions exceeding the limit (20).
     */
    public function saveRevision(?string $summary = null, ?int $userId = null): KouventaPageRevision
    {
        $revision = $this->revisions()->create([
            'user_id' => $userId ?? auth()->id(),
            'title' => $this->getRawAttribute('title'),
            'sections' => $this->getRawAttribute('sections') ?? [],
            'translations' => $this->getRawAttribute('translations') ?? [],
            'summary' => $summary ?? 'Snapshot saved',
        ]);

        // Keep maximum 20 revisions
        $excessIds = $this->revisions()->skip(20)->take(50)->pluck('id');
        if ($excessIds->isNotEmpty()) {
            KouventaPageRevision::whereIn('id', $excessIds)->delete();
        }

        return $revision;
    }
}

