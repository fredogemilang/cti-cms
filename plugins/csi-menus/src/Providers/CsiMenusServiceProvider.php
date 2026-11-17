<?php

namespace Plugins\CsiMenus\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Plugins\CsiMenus\Livewire\MenuManager;

class CsiMenusServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'csi-menus';

    protected array $livewireComponents = [
        'csi-menus.manager' => MenuManager::class,
    ];

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'Navigation Menus',
            'route' => 'admin.csi-menus.index',
            'activeRoutePattern' => 'admin.csi-menus.*',
            'url' => route('admin.csi-menus.index'),
            'icon' => 'menu',
            'permission' => 'menus.view',
            'is_active' => true,
            'source' => 'plugin:csi-menus',
            'children' => [],
        ]);
    }
}
