<?php

namespace Plugins\Posts\Models;

use App\Models\CptEntry;
use App\Traits\FindsByLocalizedSlug;
use App\Traits\HasSeoMeta;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $content
 * @property string|null $featured_image
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 */
class Post extends Model
{
    use FindsByLocalizedSlug, HasSeoMeta, HasTranslations, SoftDeletes;

    protected static function baseLocalizedSlugQuery(): Builder
    {
        return static::query()
            ->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'status',
        'visibility',
        'password',
        'published_at',
        'is_featured',
        'views_count',
        'meta',
        'translations',
    ];

    /** Per-locale fields stored in the translations JSON column. */
    protected array $translatable = ['title', 'slug', 'excerpt', 'content'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'meta' => 'array',
        'translations' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->author_id) && auth()->check()) {
                $currentUser = auth()->user();
                $author = PostAuthor::firstOrCreate(
                    ['name' => $currentUser->name],
                    ['slug' => Str::slug($currentUser->name), 'email' => $currentUser->email]
                );
                $post->author_id = $author->id;
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(PostAuthor::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function hasTranslationForLocale(string $locale): bool
    {
        $transSlug = $this->getTranslation('slug', $locale, false);

        return ! empty($transSlug) && trim((string) $transSlug) !== '';
    }

    public function getUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = function_exists('setting') ? setting('default_locale', config('app.locale', 'en')) : config('app.locale', 'en');

        // If requested locale is non-default and post DOES NOT have a translation for it, fallback to default locale URL
        if ($locale !== $defaultLocale && ! $this->hasTranslationForLocale($locale)) {
            $locale = $defaultLocale;
        }

        $prefix = ($locale !== $defaultLocale) ? '/'.$locale : '';
        $slug = $this->getTranslation('slug', $locale, fallback: false) ?: $this->slug;
        $archiveSlug = 'blog-news';
        if (class_exists(Setting::class)) {
            $archiveSlug = Setting::getArchiveSlug($locale);
        }

        return url($prefix.'/'.$archiveSlug.'/'.$slug);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

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

    public function cptEntries(): BelongsToMany
    {
        return $this->belongsToMany(
            CptEntry::class,
            'post_cpt_relations',
            'post_id',
            'cpt_entry_id'
        )->withPivot('cpt_slug')->withTimestamps();
    }

    public function getReadingTime(?string $locale = null, int $wpm = 200): int
    {
        $locale ??= app()->getLocale();
        $rawContent = $this->getTranslation('content', $locale) ?: ($this->content ?? '');
        $plainText = trim(strip_tags($rawContent));
        if (empty($plainText)) {
            return 1;
        }
        $wordCount = str_word_count($plainText);

        return max(1, (int) ceil($wordCount / $wpm));
    }
}
