<?php

namespace Plugins\KouventaPages\Providers;

use App\Events\BuildSitemap;
use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use App\Support\Action;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Plugins\KouventaPages\Http\Controllers\KouventaPageController;
use Plugins\KouventaPages\Livewire\PageEditor;
use Plugins\KouventaPages\Livewire\PageIndex;
use Plugins\KouventaPages\Models\KouventaPage;

class KouventaPagesServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'kouventa-pages';

    protected array $livewireComponents = [
        'plugins.kouventa-pages.page-index' => PageIndex::class,
        'plugins.kouventa-pages.page-editor' => PageEditor::class,
    ];

    public function boot(): void
    {
        parent::boot();

        // 1. Hook frontend routes before Core catch-all page route
        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            // Product subpages (/product/ai, /product/customer-engagement)
            Route::get('/product/{sub}', function (string $sub) {
                return app(KouventaPageController::class)->show(request(), null, "product/{$sub}");
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.pages.product.sub');

            // Solution subpages (/solutions/ai, /solutions/customer-engagement)
            Route::get('/solutions/{sub}', function (string $sub) {
                return app(KouventaPageController::class)->show(request(), null, "solutions/{$sub}");
            })->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.pages.solutions.sub');

            // Pricing page (/pricing)
            Route::get('/pricing', function () {
                return app(KouventaPageController::class)->show(request(), null, 'pricing');
            })->name('kouventa.pages.pricing');

            if ($localePattern !== 'nothing-to-match') {
                Route::get('/{locale}/product/{sub}', function (string $locale, string $sub) {
                    return app(KouventaPageController::class)->show(request(), $locale, "product/{$sub}");
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.pages.locale.product.sub');

                Route::get('/{locale}/solutions/{sub}', function (string $locale, string $sub) {
                    return app(KouventaPageController::class)->show(request(), $locale, "solutions/{$sub}");
                })->where('locale', $localePattern)->where('sub', '[a-zA-Z0-9\-]+')->name('kouventa.pages.locale.solutions.sub');

                Route::get('/{locale}/pricing', function (string $locale) {
                    return app(KouventaPageController::class)->show(request(), $locale, 'pricing');
                })->where('locale', $localePattern)->name('kouventa.pages.locale.pricing');
            }
        });

        // 2. Register pages into sitemap
        Event::listen(BuildSitemap::class, function (BuildSitemap $event) {
            try {
                $pages = KouventaPage::published()->get();
                $locales = available_locales();

                foreach ($pages as $p) {
                    foreach ($locales as $loc) {
                        $event->addUrl(
                            $p->getUrl($loc),
                            $p->updated_at ?? now(),
                            'weekly',
                            0.8
                        );
                    }
                }
            } catch (\Throwable $e) {
                // Ignore sitemap failures gracefully
            }
        });
    }

    /**
     * Register admin sidebar menu items.
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $children = [
            [
                'title' => 'All Interactive Pages',
                'route' => 'admin.kouventa-pages.index',
                'activeRoutePattern' => 'admin.kouventa-pages.index|admin.kouventa-pages.*',
                'url' => route('admin.kouventa-pages.index'),
                'icon' => 'list',
                'permission' => null,
                'is_active' => true,
                'source' => 'plugin:kouventa-pages',
                'children' => [],
            ],
        ];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kouventa_pages')) {
                $pages = KouventaPage::select('id', 'title', 'slug')->get();
                foreach ($pages as $page) {
                    $children[] = [
                        'title' => $page->title,
                        'route' => 'admin.kouventa-pages.edit',
                        'activeRoutePattern' => 'admin.kouventa-pages.edit',
                        'url' => route('admin.kouventa-pages.edit', $page->id),
                        'icon' => str_starts_with($page->slug, 'product/') ? 'inventory_2' : (str_starts_with($page->slug, 'solutions/') ? 'psychology' : 'payments'),
                        'permission' => null,
                        'is_active' => true,
                        'source' => 'plugin:kouventa-pages',
                        'children' => [],
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Fallback to basic menu if table not ready
        }

        $event->addMenuItem([
            'title' => 'Kouventa Pages',
            'route' => 'admin.kouventa-pages.index',
            'url' => route('admin.kouventa-pages.index'),
            'icon' => 'auto_stories',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-pages',
            'children' => $children,
        ]);
    }
}

