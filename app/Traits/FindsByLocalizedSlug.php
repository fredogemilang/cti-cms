<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Adds a static `findByLocalizedSlug` to any model that uses [[HasTranslations]]
 * with `slug` in `$translatable`. Looks up by the default `slug` column first,
 * then scans each available locale's translated slug in the `translations` JSON.
 * On a non-default match, it sets app()->setLocale() so the request renders
 * in the matched language.
 *
 * Models can override `baseLocalizedSlugQuery()` to add filters like
 * `where('status', 'published')`.
 */
trait FindsByLocalizedSlug
{
    public static function findByLocalizedSlug(string $slug): ?self
    {
        $base = static::baseLocalizedSlugQuery();
        $currentLocale = app()->getLocale();
        $defaultLocale = static::defaultLocale();

        // 1. If currently in a non-default locale, check that locale's translated slug first
        if ($currentLocale !== $defaultLocale) {
            $row = (clone $base)
                ->whereRaw('JSON_EXTRACT(translations, ?) = ?', ["$.\"{$currentLocale}\".slug", $slug])
                ->first();
            if ($row) {
                return $row;
            }
        }

        // 2. Check primary slug column (default locale)
        $row = (clone $base)->where('slug', $slug)->first();
        if ($row) {
            return $row;
        }

        // 3. Scan remaining locales
        $locales = array_filter(available_locales(), fn ($l) => $l !== $defaultLocale && $l !== $currentLocale);

        foreach ($locales as $locale) {
            $row = (clone $base)
                ->whereRaw('JSON_EXTRACT(translations, ?) = ?', ["$.\"{$locale}\".slug", $slug])
                ->first();
            if ($row) {
                app()->setLocale($locale);

                return $row;
            }
        }

        return null;
    }

    /**
     * Override in concrete models to add status / soft-delete filters.
     * Default: no extra constraints.
     */
    protected static function baseLocalizedSlugQuery(): Builder
    {
        return static::query();
    }
}
