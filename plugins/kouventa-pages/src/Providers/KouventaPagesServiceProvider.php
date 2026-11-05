<?php

namespace Plugins\KouventaPages\Providers;

use App\Events\BuildSitemap;
use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use App\Services\Ai\AiResourceRegistry;
use App\Services\Sitemap\SitemapBuilder;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Plugins\KouventaPages\Http\Controllers\KouventaPageController;
use Plugins\KouventaPages\Livewire\ClientManager;
use Plugins\KouventaPages\Livewire\PageEditor;
use Plugins\KouventaPages\Livewire\PageIndex;
use Plugins\KouventaPages\Livewire\PartnerManager;
use Plugins\KouventaPages\Livewire\VideoManager;
use Plugins\KouventaPages\Models\KouventaPage;
use Plugins\KouventaPages\Services\KouventaSitemapBuilder;

class KouventaPagesServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'kouventa-pages';

    protected array $livewireComponents = [
        'plugins.kouventa-pages.page-index' => PageIndex::class,
        'plugins.kouventa-pages.page-editor' => PageEditor::class,
        'plugins.kouventa-pages.client-manager' => ClientManager::class,
        'plugins.kouventa-pages.partner-manager' => PartnerManager::class,
        'plugins.kouventa-pages.video-manager' => VideoManager::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(SitemapBuilder::class, KouventaSitemapBuilder::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->app->bind(SitemapBuilder::class, KouventaSitemapBuilder::class);

        // Place Kouventa main menu items under CONTENT section in Admin Sidebar
        Filter::add('admin_menu.default_sections', function (array $sections) {
            $sections['plugin:kouventa-solutions'] = 'CONTENT';
            $sections['plugin:kouventa-products'] = 'CONTENT';
            $sections['plugin:kouventa-pricing'] = 'CONTENT';
            $sections['plugin:kouventa-clients'] = 'CONTENT';
            $sections['plugin:kouventa-partners'] = 'CONTENT';
            $sections['plugin:kouventa-videos'] = 'CONTENT';
            $sections['plugin:posts'] = 'CONTENT';

            return $sections;
        });

        Filter::add('admin_menu.default_order', function (array $order) {
            $kouventaKeys = [
                'plugin:kouventa-solutions',
                'plugin:kouventa-products',
                'plugin:kouventa-pricing',
                'plugin:kouventa-clients',
                'plugin:kouventa-partners',
                'plugin:kouventa-videos',
                'cpt:testimonials',
            ];

            $newOrder = [];
            foreach ($order as $key) {
                if (in_array($key, $kouventaKeys, true)) {
                    continue;
                }
                $newOrder[] = $key;
                if ($key === 'core:2') {
                    foreach ($kouventaKeys as $kKey) {
                        $newOrder[] = $kKey;
                    }
                }
            }

            return $newOrder;
        });

        $this->app->resolving(AiResourceRegistry::class, function (AiResourceRegistry $registry) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('kouventa_pages')) {
                    foreach (KouventaPage::published()->get(['id', 'title', 'slug']) as $page) {
                        $registry->registerResource($page->title, $page->getUrl('en'), 'Interactive Product / Solution Page');
                    }
                }
            } catch (\Throwable $e) {
                // Ignore if table not ready
            }
        });

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
                        $event->add(
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
        // 1. Solutions (AI Solutions & Customer Engagement Solutions)
        $event->addMenuItem([
            'title' => 'Solutions',
            'route' => 'admin.kouventa-pages.solutions|admin.kouventa-pages.solutions.*',
            'url' => route('admin.kouventa-pages.solutions'),
            'icon' => 'psychology',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-solutions',
            'children' => [],
        ]);

        // 2. Products (Artificial Intelligence & Customer Engagement)
        $event->addMenuItem([
            'title' => 'Products',
            'route' => 'admin.kouventa-pages.products|admin.kouventa-pages.products.*',
            'url' => route('admin.kouventa-pages.products'),
            'icon' => 'inventory_2',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-products',
            'children' => [],
        ]);

        // 3. Pricing
        $event->addMenuItem([
            'title' => 'Pricing',
            'route' => 'admin.kouventa-pages.pricing|admin.kouventa-pages.pricing.*',
            'url' => route('admin.kouventa-pages.pricing'),
            'icon' => 'payments',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-pricing',
            'children' => [],
        ]);

        // 4. Clients (They trusted us)
        $event->addMenuItem([
            'title' => 'Clients',
            'route' => 'admin.kouventa-pages.clients|admin.kouventa-pages.clients.*',
            'url' => route('admin.kouventa-pages.clients'),
            'icon' => 'handshake',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-clients',
            'children' => [],
        ]);

        // 5. Logo Partners (Technology Partners)
        $event->addMenuItem([
            'title' => 'Logo Partners',
            'route' => 'admin.kouventa-pages.partners|admin.kouventa-pages.partners.*',
            'url' => route('admin.kouventa-pages.partners'),
            'icon' => 'hub',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-partners',
            'children' => [],
        ]);

        // 6. YouTube Videos (See Kouventa in Action)
        $event->addMenuItem([
            'title' => 'YouTube Videos',
            'route' => 'admin.kouventa-pages.videos|admin.kouventa-pages.videos.*',
            'url' => route('admin.kouventa-pages.videos'),
            'icon' => 'smart_display',
            'permission' => null,
            'is_active' => true,
            'source' => 'plugin:kouventa-videos',
            'children' => [],
        ]);
    }
}
