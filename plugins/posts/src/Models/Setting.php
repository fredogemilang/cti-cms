<?php

namespace Plugins\Posts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'posts_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (! Schema::hasTable('posts_settings')) {
            return $default;
        }

        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public static function set($key, $value)
    {
        if (! Schema::hasTable('posts_settings')) {
            return null;
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get localized archive slug, fallback to primary archive slug if empty.
     */
    public static function getArchiveSlug(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = function_exists('setting') ? setting('default_locale', config('app.locale', 'en')) : config('app.locale', 'en');
        $primarySlug = static::get('archive_slug', 'blog-news');

        if ($locale === $defaultLocale) {
            return $primarySlug;
        }

        $locSlug = static::get('archive_slug_'.$locale);
        if (! empty($locSlug)) {
            return $locSlug;
        }

        return $primarySlug;
    }

    /**
     * Get localized archive title, fallback to primary archive title if empty.
     */
    public static function getArchiveTitle(?string $locale = null, string $default = 'Blog & News'): string
    {
        $locale ??= app()->getLocale();
        $defaultLocale = function_exists('setting') ? setting('default_locale', config('app.locale', 'en')) : config('app.locale', 'en');
        $primaryTitle = static::get('archive_title', $default);

        if ($locale === $defaultLocale) {
            return ! empty($primaryTitle) ? $primaryTitle : $default;
        }

        $locTitle = static::get('archive_title_'.$locale);
        if (! empty($locTitle)) {
            return $locTitle;
        }

        return ! empty($primaryTitle) ? $primaryTitle : $default;
    }
}
