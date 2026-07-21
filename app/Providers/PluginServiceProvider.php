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

                // Catch-all: Pages (must be LAST)
                Route::get('/{slug}', [PageController::class, 'show'])
                    ->where('slug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\\-]+')
                    ->name('pages.show');
            });
        });
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

            // Collect slugs to build regex constraints
            $cptSlugs = CustomPostType::withArchive()->pluck('slug')->toArray();
            $taxonomySlugs = CustomTaxonomy::active()->pluck('slug')->toArray();

            if (empty($cptSlugs) && empty($taxonomySlugs)) {
                return;
            }

            // Taxonomy term archives: /{taxonomy-slug}/{term-slug}
            if (! empty($taxonomySlugs)) {
                $taxPattern = implode('|', array_map('preg_quote', $taxonomySlugs));
                Route::get('/{taxonomySlug}/{termSlug}', [ArchiveController::class, 'termArchive'])
                    ->where('taxonomySlug', $taxPattern)
                    ->where('termSlug', '[a-zA-Z0-9\\-]+')
                    ->name('taxonomy.term.archive');
            }

            // CPT single entries: /{cpt-slug}/{entry-slug}
            if (! empty($cptSlugs)) {
                $cptPattern = implode('|', array_map('preg_quote', $cptSlugs));
                Route::get('/{cptSlug}/{entrySlug}', [ArchiveController::class, 'single'])
                    ->where('cptSlug', $cptPattern)
                    ->where('entrySlug', '[a-zA-Z0-9\\-]+')
                    ->name('cpt.entry.show');

                // CPT archive listings: /{cpt-slug}
                Route::get('/{cptSlug}', [ArchiveController::class, 'archive'])
                    ->where('cptSlug', $cptPattern)
                    ->name('cpt.archive');
            }
        } catch (\Exception $e) {
            Log::debug('Failed to register CPT routes: '.$e->getMessage());
        }
    }
}
