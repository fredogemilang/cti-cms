<?php

namespace App\Providers;

use App\Models\Page;
use App\Services\ActivityLogger;
use App\Services\BreadcrumbService;
use App\Services\ContentTypeRegistry;
use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use App\Services\MediaUsageService;
use App\Services\PageTemplateService;
use App\Services\SettingsRegistry;
use App\Services\TaxonomyRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Core application service provider.
 *
 * Registers global singletons only. Events, settings, plugins,
 * and themes each have their own dedicated service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsRegistry::class);
        $this->app->singleton(ContentTypeRegistry::class);
        $this->app->singleton(TaxonomyRegistry::class);
        $this->app->singleton(BreadcrumbService::class);
        $this->app->singleton(IndexNowService::class);
        $this->app->singleton(GoogleIndexingService::class);
        $this->app->singleton(ActivityLogger::class);
        $this->app->singleton(MediaUsageService::class);
        $this->app->singleton(PageTemplateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Page::saved(function (Page $page) {
            app(IndexNowService::class)->pingEntity($page);
            app(GoogleIndexingService::class)->pingEntity($page, 'URL_UPDATED');
        });

        Page::deleted(function (Page $page) {
            app(IndexNowService::class)->pingEntity($page);
            app(GoogleIndexingService::class)->pingEntity($page, 'URL_DELETED');
        });
    }
}
