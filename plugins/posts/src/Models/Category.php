<?php

namespace Plugins\Posts\Models;

use App\Traits\FindsByLocalizedSlug;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use FindsByLocalizedSlug, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'translations',
    ];

    protected array $translatable = ['name', 'slug', 'description'];

    protected $casts = [
        'translations' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::deleting(function ($category) {
            if ($category->slug === 'uncategorized') {
                return false;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = function_exists('default_locale') ? default_locale() : (function_exists('setting') ? setting('default_locale', config('app.locale', 'en')) : config('app.locale', 'en'));

        $prefix = ($locale !== $defaultLocale) ? '/'.$locale : '';
        $slug = $this->getTranslation('slug', $locale, fallback: false) ?: $this->slug;
        $archiveSlug = 'blog-news';
        if (class_exists(Setting::class)) {
            $archiveSlug = Setting::getArchiveSlug($locale);
        }
        $categoryBase = 'category';
        if (class_exists(\App\Models\Setting::class)) {
            $categoryBase = \App\Models\Setting::get('permalink_category_base', 'category');
        }

        return url($prefix.'/'.$archiveSlug.'/'.$categoryBase.'/'.$slug);
    }
}
