<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Services\Ai\AiResourceRegistry;
use App\Services\BreadcrumbService;
use App\Services\ContentTypeRegistry;
use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use App\Services\MediaUsageService;
use App\Services\PageTemplateService;
use App\Services\Schema\Providers\OrganizationSchemaProvider;
use App\Services\Schema\Providers\PageSchemaProvider;
use App\Services\Schema\SchemaRegistry;
use App\Services\SettingsRegistry;
use App\Services\TaxonomyRegistry;
use App\Support\Filter;
use Illuminate\Queue\Events\JobProcessing;
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

        // AI & Extensible Schema Registry 2.0
        $this->app->singleton(AiResourceRegistry::class);
        $this->app->singleton(SchemaRegistry::class, function ($app) {
            $registry = new SchemaRegistry;
            $registry->registerProvider($app->make(OrganizationSchemaProvider::class));
            $registry->registerProvider($app->make(PageSchemaProvider::class));

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /** @var AiResourceRegistry $aiRegistry */
        $aiRegistry = $this->app->make(AiResourceRegistry::class);
        $aiRegistry->registerResource('Schema Manifest (AI Index)', url('/schema-manifest.json'), 'Machine-readable Data Catalog Index');
        $aiRegistry->registerResource('Schema Knowledge Graph', url('/schema.json'), 'Aggregated site-wide JSON-LD graph');

        Page::saved(function (Page $page) {
            app(IndexNowService::class)->pingEntity($page);
            app(GoogleIndexingService::class)->pingEntity($page, 'URL_UPDATED');
            app(SchemaRegistry::class)->clearCache();
        });

        Page::deleted(function (Page $page) {
            app(IndexNowService::class)->pingEntity($page);
            app(GoogleIndexingService::class)->pingEntity($page, 'URL_DELETED');
            app(SchemaRegistry::class)->clearCache();
        });

        // Flush setting memo between queued jobs
        $this->app['events']->listen(JobProcessing::class, function () {
            Setting::flushMemo();
        });

        // CDT Theme Short URLs Filter Hooks
        Filter::add('cpt_entry.url', function ($url, $entry, $locale = null) {
            if (active_theme()?->slug !== 'cdt') {
                return $url;
            }

            if (! $entry || ! isset($entry->postType)) {
                return $url;
            }

            $cptSlug = $entry->postType->slug;
            $locale ??= app()->getLocale();
            $defaultLocale = setting('default_locale', config('app.locale', 'en'));

            $localePrefix = ($locale !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
                ? '/'.$locale
                : '';

            $entrySlug = $entry->getTranslation('slug', $locale, fallback: true) ?? $entry->slug;

            if ($cptSlug === 'technology-alliance') {
                return url($localePrefix.'/'.ltrim($entrySlug, '/'));
            }

            if ($cptSlug === 'tech-products' || $cptSlug === 'products') {
                $parentVendor = $entry->parentRelatedEntries()->first();
                $parentSlug = $parentVendor
                    ? ($parentVendor->getTranslation('slug', $locale, fallback: true) ?? $parentVendor->slug)
                    : ($entry->getMeta('parent_vendor') ?: null);

                if ($parentSlug) {
                    return url($localePrefix.'/'.ltrim($parentSlug, '/').'/'.ltrim($entrySlug, '/'));
                }
            }

            return $url;
        }, 10);

        Filter::add('cpt_entry.url_redirect', function ($redirectUrl, $entry) {
            if (active_theme()?->slug !== 'cdt') {
                return $redirectUrl;
            }

            if (! $entry || ! isset($entry->postType)) {
                return $redirectUrl;
            }

            $cptSlug = $entry->postType->slug;
            if ($cptSlug === 'technology-alliance' || $cptSlug === 'tech-products' || $cptSlug === 'products') {
                $shortUrl = $entry->getUrl();
                $currentUrl = request()->url();
                if ($shortUrl !== $currentUrl && ! str_ends_with($currentUrl, '/preview')) {
                    return $shortUrl;
                }
            }

            return $redirectUrl;
        }, 10);
    }
}
