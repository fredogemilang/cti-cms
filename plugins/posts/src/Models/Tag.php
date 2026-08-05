<?php

namespace Plugins\Posts\Models;

use App\Traits\FindsByLocalizedSlug;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use FindsByLocalizedSlug, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'translations',
    ];

    protected array $translatable = ['name', 'slug'];

    protected $casts = [
        'translations' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
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
        $tagBase = 'tag';
        if (class_exists(\App\Models\Setting::class)) {
            $tagBase = \App\Models\Setting::get('permalink_tag_base', 'tag');
        }

        return url($prefix.'/'.$archiveSlug.'/'.$tagBase.'/'.$slug);
    }
}
