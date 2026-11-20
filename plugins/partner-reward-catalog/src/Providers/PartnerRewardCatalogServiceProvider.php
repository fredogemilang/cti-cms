<?php

namespace Plugins\PartnerRewardCatalog\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Illuminate\Support\Facades\View;
use Plugins\PartnerRewardCatalog\Livewire\CatalogManager;

class PartnerRewardCatalogServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'partner-reward-catalog';

    protected array $livewireComponents = [
        'partner-reward-catalog.manager' => CatalogManager::class,
    ];

    public function boot(): void
    {
        parent::boot();

        // Register plugin namespace explicitly so routes/views work immediately upon activation
        View::addNamespace('partner-reward-catalog', dirname(__DIR__, 2).'/resources/views');
    }

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'Partner Reward Catalog',
            'route' => 'admin.partner-reward-catalog.index',
            'activeRoutePattern' => 'admin.partner-reward-catalog.*',
            'url' => route('admin.partner-reward-catalog.index'),
            'icon' => 'menu_book',
            'permission' => 'partner-reward-catalog.view',
            'is_active' => true,
            'source' => 'plugin:partner-reward-catalog',
            'children' => [],
        ]);
    }
}
