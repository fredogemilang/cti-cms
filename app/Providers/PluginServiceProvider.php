<?php

namespace App\Providers;

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\PageController;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Services\PermissionRegistry;
use App\Services\PluginLoader;
use App\Services\PluginManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PluginLoader::class, function ($app) {
            return new PluginLoader;
        });

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager($app->make(PermissionRegistry::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(PluginLoader $loader): void
    {
        // Load active plugins
        $loader->boot();

        // Register catch-all route for pages AFTER ALL service providers have booted
        // This ensures plugin routes like /events or /posts take precedence
        $this->app->booted(function () {
            Route::middleware('web')->group(function () {
                $adminPath = config('admin.path', 'admin');

                $this->registerCptRoutes($adminPath);

                $localePattern = $this->nonDefaultLocalesPattern();

                // Register CDT theme short CPT routes
                if (active_theme()?->slug === 'cdt') {
                    if ($localePattern !== 'nothing-to-match') {
                        Route::get('/{locale}/{vendorSlug}/{productSlug}', [ArchiveController::class, 'localeShortProductSingle'])
                            ->where('locale', $localePattern)
                            ->where('vendorSlug', '[a-zA-Z0-9\-]+')
                            ->where('productSlug', '[a-zA-Z0-9\-]+')
                            ->name('locale.cdt.product.single');

                        Route::get('/{locale}/{vendorSlug}', [ArchiveController::class, 'localeShortVendorSingle'])
                            ->where('locale', $localePattern)
                            ->where('vendorSlug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\-]+')
                            ->name('locale.cdt.vendor.single');
                    }

                    Route::get('/{vendorSlug}/{productSlug}', [ArchiveController::class, 'shortProductSingle'])
                        ->where('vendorSlug', '[a-zA-Z0-9\-]+')
                        ->where('productSlug', '[a-zA-Z0-9\-]+')
                        ->name('cdt.product.single');

                    Route::get('/{vendorSlug}', [ArchiveController::class, 'shortVendorSingle'])
                        ->where('vendorSlug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\-]+')
                        ->name('cdt.vendor.single');
                }

                if ($localePattern !== 'nothing-to-match') {
                    // Localized Catch-all: Pages (e.g. /id/about-us)
                    Route::get('/{locale}/{slug}', [PageController::class, 'show'])
                        ->where('locale', $localePattern)
                        ->where('slug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\\-]+')
                        ->name('locale.pages.show');
                }

                // Catch-all: Pages (must be LAST)
                Route::get('/{slug}', [PageController::class, 'show'])
                    ->where('slug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\\-]+')
                    ->name('pages.show');
            });
        });
    }

    /**
     * Build a regex pattern of non-default locales (e.g. 'id') for route constraints.
     */
    protected function nonDefaultLocalesPattern(): string
    {
        try {
            if (! Schema::hasTable('settings')) {
                return 'nothing-to-match';
            }

            $all = available_locales();
            $default = setting('default_locale', config('app.locale', 'en'));
            $nonDefault = array_values(array_filter($all, fn ($l) => $l !== $default));

            if (empty($nonDefault)) {
                return 'nothing-to-match';
            }

            return implode('|', array_map('preg_quote', $nonDefault));
        } catch (\Throwable $e) {
            return 'nothing-to-match';
        }
    }

    /**
     * Register CPT archive, single entry, and taxonomy term archive routes.
     *
     * Route order:
     *   1. Taxonomy term archives: /{taxonomy-slug}/{term-slug}
     *   2. CPT single entries:     /{cpt-slug}/{entry-slug}
     *   3. CPT archives:           /{cpt-slug}
     *
     * These must be registered BEFORE the catch-all page route.
     */
    protected function registerCptRoutes(string $adminPath): void
    {
        try {
            if (! Schema::hasTable('custom_post_types') || ! Schema::hasTable('custom_taxonomies')) {
                return;
            }

            // Collect all localized slugs dynamically across all active CPTs
            $archiveSlugs = CustomPostType::withArchive()->get()
                ->flatMap(fn ($cpt) => $cpt->getAllLocalizedSlugs())
                ->unique()->values()->toArray();

            $singleSlugs = CustomPostType::publiclyQueryable()->get()
                ->flatMap(fn ($cpt) => $cpt->getAllLocalizedSlugs())
                ->unique()->values()->toArray();

            $taxonomySlugs = CustomTaxonomy::active()->pluck('slug')->toArray();

            if (empty($archiveSlugs) && empty($singleSlugs) && empty($taxonomySlugs)) {
                return;
            }

            $localePattern = $this->nonDefaultLocalesPattern();

            // Taxonomy term archives: /{taxonomy-slug}/{term-slug}
            if (! empty($taxonomySlugs)) {
                $taxPattern = implode('|', array_map('preg_quote', $taxonomySlugs));

                if ($localePattern !== 'nothing-to-match') {
                    Route::get('/{locale}/{taxonomySlug}/{termSlug}', [ArchiveController::class, 'localeTermArchive'])
                        ->where('locale', $localePattern)
                        ->where('taxonomySlug', $taxPattern)
                        ->where('termSlug', '[a-zA-Z0-9\\-]+')
                        ->name('locale.taxonomy.term.archive');
                }

                Route::get('/{taxonomySlug}/{termSlug}', [ArchiveController::class, 'termArchive'])
                    ->where('taxonomySlug', $taxPattern)
                    ->where('termSlug', '[a-zA-Z0-9\\-]+')
                    ->name('taxonomy.term.archive');
            }

            // CPT single entries: /{cpt-slug}/{entry-slug} (requires publicly_queryable)
            if (! empty($singleSlugs)) {
                $singlePattern = implode('|', array_map('preg_quote', $singleSlugs));

                if ($localePattern !== 'nothing-to-match') {
                    Route::get('/{locale}/{cptSlug}/{parentSlug}/{entrySlug}', [ArchiveController::class, 'localeNestedSingle'])
                        ->where('locale', $localePattern)
                        ->where('cptSlug', $singlePattern)
                        ->where('parentSlug', '[a-zA-Z0-9\\-]+')
                        ->where('entrySlug', '[a-zA-Z0-9\\-]+')
                        ->name('locale.cpt.entry.nested.show');

                    Route::get('/{locale}/{cptSlug}/{entrySlug}', [ArchiveController::class, 'localeSingle'])
                        ->where('locale', $localePattern)
                        ->where('cptSlug', $singlePattern)
                        ->where('entrySlug', '[a-zA-Z0-9\\-]+')
                        ->name('locale.cpt.entry.show');
                }

                Route::get('/{cptSlug}/{parentSlug}/{entrySlug}', [ArchiveController::class, 'nestedSingle'])
                    ->where('cptSlug', $singlePattern)
                    ->where('parentSlug', '[a-zA-Z0-9\\-]+')
                    ->where('entrySlug', '[a-zA-Z0-9\\-]+')
                    ->name('cpt.entry.nested.show');

                Route::get('/{cptSlug}/{entrySlug}', [ArchiveController::class, 'single'])
                    ->where('cptSlug', $singlePattern)
                    ->where('entrySlug', '[a-zA-Z0-9\\-]+')
                    ->name('cpt.entry.show');
            }

            // CPT archive listings: /{cpt-slug} (requires has_archive)
            if (! empty($archiveSlugs)) {
                $archivePattern = implode('|', array_map('preg_quote', $archiveSlugs));

                if ($localePattern !== 'nothing-to-match') {
                    Route::get('/{locale}/{cptSlug}', [ArchiveController::class, 'localeArchive'])
                        ->where('locale', $localePattern)
                        ->where('cptSlug', $archivePattern)
                        ->name('locale.cpt.archive');
                }

                Route::get('/{cptSlug}', [ArchiveController::class, 'archive'])
                    ->where('cptSlug', $archivePattern)
                    ->name('cpt.archive');
            }
        } catch (\Exception $e) {
            Log::debug('Failed to register CPT routes: '.$e->getMessage());
        }
    }
}
