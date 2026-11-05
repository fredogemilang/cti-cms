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
use Themes\Kouventa\Handlers\PricingRouteHandler;
use Themes\Kouventa\Handlers\SolutionRouteHandler;

class KouventaThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any theme services.
     */
    public function register(): void
    {
        // Extend SitemapBuilder to include clean solution & pricing URLs in page-sitemap.xml
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
                    if (class_exists(PricingRouteHandler::class)) {
                        foreach (PricingRouteHandler::getAllPricingSitemapUrls() as $item) {
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
        // Ensure handler files are loaded if autoloader has not picked them up yet
        if (! class_exists(SolutionRouteHandler::class)) {
            require_once __DIR__.'/src/Handlers/SolutionRouteHandler.php';
        }
        if (! class_exists(PricingRouteHandler::class)) {
            require_once __DIR__.'/src/Handlers/PricingRouteHandler.php';
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

            // Pricing tab clean subpages (/pricing/{tab})
            Route::get('/pricing/{tab}', function (Request $request, string $tab) {
                return app(PricingRouteHandler::class)->showTab($request, null, $tab);
            })->where('tab', '[a-zA-Z0-9\-]+')->name('kouventa.pricing.tab');

            // Pricing base route (/pricing)
            Route::get('/pricing', function (Request $request) {
                return app(PricingRouteHandler::class)->showBase($request, null);
            })->name('kouventa.pricing.base');

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

                Route::get('/{locale}/pricing/{tab}', function (Request $request, string $locale, string $tab) {
                    return app(PricingRouteHandler::class)->showTab($request, $locale, $tab);
                })->where('locale', $localePattern)->where('tab', '[a-zA-Z0-9\-]+')->name('kouventa.locale.pricing.tab');

                Route::get('/{locale}/pricing', function (Request $request, string $locale) {
                    return app(PricingRouteHandler::class)->showBase($request, $locale);
                })->where('locale', $localePattern)->name('kouventa.locale.pricing.base');
            }
        });

        // Also listen to BuildSitemap event for full / aggregate sitemaps
        Event::listen(BuildSitemap::class, function (BuildSitemap $event) {
            if (class_exists(SolutionRouteHandler::class)) {
                foreach (SolutionRouteHandler::getAllSolutionSitemapUrls() as $item) {
                    $event->add($item['loc'], $item['lastmod'], $item['changefreq'], $item['priority']);
                }
            }
            if (class_exists(PricingRouteHandler::class)) {
                foreach (PricingRouteHandler::getAllPricingSitemapUrls() as $item) {
                    $event->add($item['loc'], $item['lastmod'], $item['changefreq'], $item['priority']);
                }
            }
        });
    }
}
