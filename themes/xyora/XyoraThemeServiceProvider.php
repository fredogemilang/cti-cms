<?php

namespace Themes\Xyora;

use Illuminate\Support\Facades\Route;
use Themes\Xyora\Http\Controllers\SearchController;

/**
 * Xyora theme service provider.
 *
 * Convention: themes/{slug}/{StudlySlug}ThemeServiceProvider.php —
 * auto-discovered and booted by ThemeLoader when this theme is active.
 */
class XyoraThemeServiceProvider
{
    public function boot(): void
    {
        $this->registerSearchRoutes();
    }

    /**
     * Public site search (registered here instead of core routes so the
     * core stays generic — see docs/branching-strategy.md and G11).
     */
    protected function registerSearchRoutes(): void
    {
        Route::get('/search', [SearchController::class, 'index'])->name('search');

        try {
            $allLocales = array_filter(array_map('trim', explode(',', (string) setting('available_locales', 'id,en'))));
            $defaultLocale = setting('default_locale', config('app.locale', 'en'));
            $localePattern = implode('|', array_map(fn ($l) => preg_quote($l, '#'), $allLocales ?: [$defaultLocale]));

            Route::get('/{locale}/search', [SearchController::class, 'index'])
                ->where('locale', $localePattern)
                ->name('locale.search');
        } catch (\Throwable $e) {
            // Settings may be unavailable during early boot (fresh install) —
            // skip the localized search route, the default /search still works.
        }
    }
}
