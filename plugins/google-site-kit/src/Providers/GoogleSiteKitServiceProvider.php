<?php

namespace Plugins\GoogleSiteKit\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use App\Services\SettingsRegistry;
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

    /**
     * Register settings fields (appears under Admin → Settings).
     */
    protected function registerSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('plugin_google_site_kit', [
            'label' => 'Google Site Kit',
            'icon' => 'query_stats',
            'order' => 210,
            'description' => 'Google Site Kit tracking tags and credentials.',
            'fields' => [
                ['key' => 'gsk_enabled', 'label' => 'Enable HTML Code Injection', 'type' => 'boolean', 'default' => true, 'rules' => ['boolean']],
                ['key' => 'gsk_ga4_tag_id', 'label' => 'Google Analytics 4 (GA4) Tag ID', 'type' => 'string', 'default' => '', 'placeholder' => 'G-XXXXXXXXXX', 'rules' => ['nullable', 'string', 'max:20']],
                ['key' => 'gsk_gtm_id', 'label' => 'Google Tag Manager ID', 'type' => 'string', 'default' => '', 'placeholder' => 'GTM-XXXXXXX', 'rules' => ['nullable', 'string', 'max:20']],
                ['key' => 'gsk_ads_id', 'label' => 'Google Ads ID', 'type' => 'string', 'default' => '', 'placeholder' => 'AW-XXXXXXXXX', 'rules' => ['nullable', 'string', 'max:20']],
                ['key' => 'gsk_pagespeed_api_key', 'label' => 'PageSpeed Insights API Key', 'type' => 'string', 'default' => '', 'placeholder' => 'Enter API Key for PageSpeed Insight metrics', 'rules' => ['nullable', 'string']],
            ],
        ]);
    }
}
