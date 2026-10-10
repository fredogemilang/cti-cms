<?php

namespace Themes\Kouventa;

use App\Http\Controllers\PageController;
use App\Support\Action;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class KouventaThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any theme services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any theme services.
     */
    public function boot(): void
    {
        // Register subpage routes before the catch-all single-segment page route
        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            // Product subpages (/product/ai, /product/customer-engagement)
            Route::get('/product/{sub}', function (string $sub) {
                return app(PageController::class)->show("product/{$sub}");
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.product.sub');

            // Solution subpages (/solutions/ai, /solutions/customer-engagement)
            Route::get('/solutions/{sub}', function (string $sub) {
                return app(PageController::class)->show("solutions/{$sub}");
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.solutions.sub');

            if ($localePattern !== 'nothing-to-match') {
                Route::get('/{locale}/product/{sub}', function (string $locale, string $sub) {
                    return app(PageController::class)->show($locale, "product/{$sub}");
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.locale.product.sub');

                Route::get('/{locale}/solutions/{sub}', function (string $locale, string $sub) {
                    return app(PageController::class)->show($locale, "solutions/{$sub}");
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.locale.solutions.sub');
            }
        });
    }
}
