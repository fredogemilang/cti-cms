<?php

namespace Themes\Kouventa;

use App\Events\BuildSitemap;
use App\Http\Controllers\PageController;
use App\Services\Sitemap\SitemapBuilder;
use App\Support\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Themes\Kouventa\Handlers\SolutionRouteHandler;

class KouventaThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any theme services.
     */
    public function register(): void
    {
        // Extend SitemapBuilder to include clean solution URLs in page-sitemap.xml
        $this->app->extend(SitemapBuilder::class, function (SitemapBuilder $builder) {
            return new class extends SitemapBuilder {
                public function getPageUrls(): array
                {
                    $urls = parent::getPageUrls();
                    if (class_exists(SolutionRouteHandler::class)) {
                        foreach (SolutionRouteHandler::getAllSolutionSitemapUrls() as $item) {
                            $urls[] = $item;
                        }
                    }

                    return $urls;
                }
            };
        });
    }

    /**
     * Bootstrap any theme services.
     */
    public function boot(): void
    {
        // Ensure handler file is loaded if autoloader has not picked it up yet
        if (! class_exists(SolutionRouteHandler::class)) {
            require_once __DIR__.'/src/Handlers/SolutionRouteHandler.php';
        }

        // Register subpage routes before the catch-all single-segment page route
        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            // Product subpages (/product/ai, /product/customer-engagement)
            Route::get('/product/{sub}', function (string $sub) {
                return app(PageController::class)->show("product/{$sub}");
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.product.sub');

            // Solution department subpages (/solutions/ai/{dept})
            Route::get('/solutions/ai/{dept}', function (Request $request, string $dept) {
                return app(SolutionRouteHandler::class)->showDept($request, null, $dept);
            })->where('dept', '[a-zA-Z0-9\-]+')->name('kouventa.solutions.ai.dept');

            // Solution customer engagement use cases (/solutions/customer-engagement/{case})
            Route::get('/solutions/customer-engagement/{case}', function (Request $request, string $case) {
                return app(SolutionRouteHandler::class)->showCase($request, null, $case);
            })->where('case', '[a-zA-Z0-9\-]+')->name('kouventa.solutions.ce.case');

            // Solution subpages (/solutions/ai, /solutions/customer-engagement)
            Route::get('/solutions/{sub}', function (Request $request, string $sub) {
                return app(SolutionRouteHandler::class)->showSub($request, null, $sub);
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.solutions.sub');

            if ($localePattern !== 'nothing-to-match') {
                Route::get('/{locale}/product/{sub}', function (string $locale, string $sub) {
                    return app(PageController::class)->show($locale, "product/{$sub}");
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.locale.product.sub');

                Route::get('/{locale}/solutions/ai/{dept}', function (Request $request, string $locale, string $dept) {
                    return app(SolutionRouteHandler::class)->showDept($request, $locale, $dept);
                })->where('locale', $localePattern)->where('dept', '[a-zA-Z0-9\-]+')->name('kouventa.locale.solutions.ai.dept');

                Route::get('/{locale}/solutions/customer-engagement/{case}', function (Request $request, string $locale, string $case) {
                    return app(SolutionRouteHandler::class)->showCase($request, $locale, $case);
                })->where('locale', $localePattern)->where('case', '[a-zA-Z0-9\-]+')->name('kouventa.locale.solutions.ce.case');

                Route::get('/{locale}/solutions/{sub}', function (Request $request, string $locale, string $sub) {
                    return app(SolutionRouteHandler::class)->showSub($request, $locale, $sub);
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.locale.solutions.sub');
            }
        });

        // Also listen to BuildSitemap event for full / aggregate sitemaps
        Event::listen(BuildSitemap::class, function (BuildSitemap $event) {
            if (class_exists(SolutionRouteHandler::class)) {
                foreach (SolutionRouteHandler::getAllSolutionSitemapUrls() as $item) {
                    $event->add($item['loc'], $item['lastmod'], $item['changefreq'], $item['priority']);
                }
            }
        });
    }
}
