<?php

namespace App\Providers;

use App\Services\ActivityLogger;
use App\Services\MediaUsageService;
use App\Services\PageTemplateService;
use App\Services\SettingsRegistry;
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
        $this->app->singleton(ActivityLogger::class);
        $this->app->singleton(MediaUsageService::class);
        $this->app->singleton(PageTemplateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
