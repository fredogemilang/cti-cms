<?php

namespace Plugins\GoogleSiteKit\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Plugins\GoogleSiteKit\Services\GoogleApiService;

class GoogleSiteKitServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'google-site-kit';

    /**
     * Register bindings in the container.
     */
    protected function registerBindings(): void
    {
        $this->app->singleton(GoogleApiService::class, function ($app) {
            return new GoogleApiService;
        });
    }

    /**
     * Register admin menu items.
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'Site Kit',
            'route' => 'admin.google-site-kit.index',
            'url' => route('admin.google-site-kit.index'),
            'icon' => 'query_stats', // Google Analytics / stats style icon
            'permission' => 'google-site-kit.view',
            'is_active' => true,
            'source' => 'plugin:google-site-kit',
            'children' => [
                [
                    'title' => 'Dashboard',
                    'route' => 'admin.google-site-kit.index',
                    'url' => route('admin.google-site-kit.index'),
                    'icon' => 'dashboard',
                    'permission' => 'google-site-kit.view',
                    'is_active' => true,
                    'source' => 'plugin:google-site-kit',
                    'children' => [],
                ],
                [
                    'title' => 'Settings',
                    'route' => 'admin.google-site-kit.settings',
                    'url' => route('admin.google-site-kit.settings'),
                    'icon' => 'settings',
                    'permission' => 'google-site-kit.edit',
                    'is_active' => true,
                    'source' => 'plugin:google-site-kit',
                    'children' => [],
                ],
            ],
        ]);
    }
}
