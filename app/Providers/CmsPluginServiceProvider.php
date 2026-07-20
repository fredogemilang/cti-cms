<?php

namespace App\Providers;

use App\Events\RenderAdminMenu;
use App\Services\SettingsRegistry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Base service provider for CMS plugins.
 *
 * Plugin developers extend this class and set $pluginSlug.
 * Everything else is auto-detected from the plugin's directory structure:
 *
 *   routes/web.php          → auto-loaded with 'web' middleware
 *   routes/api.php          → auto-loaded with 'api' middleware
 *   resources/views/        → registered as '{slug}::' namespace
 *   database/migrations/    → auto-loaded
 *   config/{slug}.php       → auto-merged into config('{slug}')
 *   src/Livewire/           → auto-discovered and registered
 *
 * Override hook methods for custom behavior:
 *   registerMenuItems()     → admin sidebar menu entries
 *   registerSettings()      → settings page fields
 *   registerScheduledTasks()→ cron jobs
 *   registerBindings()      → service container bindings
 */
abstract class CmsPluginServiceProvider extends ServiceProvider
{
    /**
     * The plugin slug (must match the directory name under plugins/).
     * This is the only required property.
     */
    protected string $pluginSlug = '';

    /**
     * Manually registered Livewire components.
     * Use this when you need custom component names.
     * Format: ['component-name' => ComponentClass::class]
     */
    protected array $livewireComponents = [];

    /**
     * Whether to auto-discover Livewire components from src/Livewire/.
     * Set to false to disable and register manually via $livewireComponents.
     */
    protected bool $autoDiscoverLivewire = true;

    /**
     * The resolved base path for this plugin.
     */
    private ?string $basePath = null;

    /**
     * Register bindings in the container.
     * Override in your plugin to register custom bindings.
     */
    public function register(): void
    {
        $this->registerBindings();

        // Auto-merge config if config/{slug}.php exists
        $configFile = $this->basePath()."/config/{$this->pluginSlug}.php";
        if (file_exists($configFile)) {
            $this->mergeConfigFrom($configFile, $this->pluginSlug);
        }
    }

    /**
     * Bootstrap the plugin services.
     */
    public function boot(): void
    {
        $this->autoLoadRoutes();
        $this->autoLoadViews();
        $this->autoLoadMigrations();
        $this->autoRegisterLivewire();
        $this->bootMenuItems();
        $this->bootSettings();
        $this->bootScheduledTasks();
    }

    // ──────────────────────────────────────────────
    //  Auto-loaders (convention-based)
    // ──────────────────────────────────────────────

    /**
     * Auto-load routes from routes/web.php and routes/api.php.
     */
    protected function autoLoadRoutes(): void
    {
        $routesPath = $this->basePath().'/routes';

        if (file_exists($routesPath.'/web.php')) {
            $this->loadRoutesFrom($routesPath.'/web.php');
        }

        if (file_exists($routesPath.'/api.php')) {
            $this->loadRoutesFrom($routesPath.'/api.php');
        }
    }

    /**
     * Auto-load views from resources/views/ with plugin slug as namespace.
     */
    protected function autoLoadViews(): void
    {
        $viewsPath = $this->basePath().'/resources/views';

        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->pluginSlug);
        }
    }

    /**
     * Auto-load migrations from database/migrations/.
     */
    protected function autoLoadMigrations(): void
    {
        $migrationsPath = $this->basePath().'/database/migrations';

        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Auto-discover and register Livewire components from src/Livewire/.
     *
     * Component naming convention:
     *   src/Livewire/PostsTable.php → 'plugins.{slug}.posts-table'
     *   src/Livewire/Settings.php   → 'plugins.{slug}.settings'
     */
    protected function autoRegisterLivewire(): void
    {
        if (! class_exists(Livewire::class)) {
            return;
        }

        // Register manually declared components first (takes precedence)
        foreach ($this->livewireComponents as $name => $class) {
            Livewire::component($name, $class);
        }

        // Auto-discover from src/Livewire/ directory
        if ($this->autoDiscoverLivewire) {
            $livewirePath = $this->basePath().'/src/Livewire';

            if (! is_dir($livewirePath)) {
                return;
            }

            $this->discoverLivewireComponents($livewirePath);
        }
    }

    /**
     * Recursively discover Livewire components in a directory.
     */
    protected function discoverLivewireComponents(string $directory, string $subNamespace = ''): void
    {
        $namespace = $this->pluginNamespace().'\\Livewire'.$subNamespace;
        $prefix = 'plugins.'.$this->pluginSlug;

        if ($subNamespace) {
            // Convert SubDir to sub-dir for component naming
            $subPrefix = collect(explode('\\', ltrim($subNamespace, '\\')))
                ->map(fn ($part) => $this->toKebabCase($part))
                ->implode('.');
            $prefix .= '.'.$subPrefix;
        }

        $files = File::files($directory);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $file->getFilenameWithoutExtension();
            $fqcn = $namespace.'\\'.$className;

            if (! class_exists($fqcn)) {
                continue;
            }

            $componentName = $prefix.'.'.$this->toKebabCase($className);

            // Don't override manually registered components
            if (! isset($this->livewireComponents[$componentName])) {
                Livewire::component($componentName, $fqcn);
            }
        }

        // Recurse into subdirectories
        $directories = File::directories($directory);
        foreach ($directories as $subDir) {
            $dirName = basename($subDir);
            $this->discoverLivewireComponents($subDir, $subNamespace.'\\'.$dirName);
        }
    }

    // ──────────────────────────────────────────────
    //  Hook methods (override in your plugin)
    // ──────────────────────────────────────────────

    /**
     * Register container bindings.
     * Override in your plugin to bind interfaces to implementations.
     */
    protected function registerBindings(): void
    {
        // Override in plugin
    }

    /**
     * Register admin menu items.
     * Override to add entries to the admin sidebar.
     *
     * @param  RenderAdminMenu  $event
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        // Override in plugin
    }

    /**
     * Register settings fields.
     * Override to add a settings tab under Admin → Settings.
     *
     * @param  SettingsRegistry  $registry
     */
    protected function registerSettings(SettingsRegistry $registry): void
    {
        // Override in plugin
    }

    /**
     * Register scheduled tasks.
     * Override to schedule artisan commands or closures.
     *
     * @param  Schedule  $schedule
     */
    protected function registerScheduledTasks(Schedule $schedule): void
    {
        // Override in plugin
    }

    // ──────────────────────────────────────────────
    //  Boot wrappers for hook methods
    // ──────────────────────────────────────────────

    /**
     * Wire up the menu hook if the plugin overrides registerMenuItems().
     */
    protected function bootMenuItems(): void
    {
        Event::listen(RenderAdminMenu::class, function (RenderAdminMenu $event) {
            $this->registerMenuItems($event);
        });
    }

    /**
     * Wire up settings registration.
     */
    protected function bootSettings(): void
    {
        $registry = $this->app->make(SettingsRegistry::class);
        $this->registerSettings($registry);

        // Also auto-register settings from plugin.json manifest
        $this->registerManifestSettings($registry);
    }

    /**
     * Wire up scheduled task registration.
     */
    protected function bootScheduledTasks(): void
    {
        $this->app->booted(function () {
            if ($this->app->runningInConsole()) {
                $schedule = $this->app->make(Schedule::class);
                $this->registerScheduledTasks($schedule);

                // Also auto-register schedule from plugin.json manifest
                $this->registerManifestSchedule($schedule);
            }
        });
    }

    // ──────────────────────────────────────────────
    //  Manifest auto-registration (from plugin.json)
    // ──────────────────────────────────────────────

    /**
     * Read settings definition from plugin.json and auto-register.
     */
    protected function registerManifestSettings(SettingsRegistry $registry): void
    {
        $manifest = $this->readManifest();
        if (! $manifest || ! isset($manifest['settings'])) {
            return;
        }

        $settings = $manifest['settings'];
        $slug = 'plugin_'.$this->pluginSlug;

        $registry->registerGroup($slug, [
            'label' => $settings['label'] ?? $manifest['name'] ?? ucfirst($this->pluginSlug),
            'icon' => $settings['icon'] ?? 'extension',
            'order' => $settings['order'] ?? 200,
            'description' => $settings['description'] ?? null,
            'fields' => $settings['fields'] ?? [],
        ]);
    }

    /**
     * Read schedule definition from plugin.json and auto-register.
     */
    protected function registerManifestSchedule(Schedule $schedule): void
    {
        $manifest = $this->readManifest();
        if (! $manifest || ! isset($manifest['schedule'])) {
            return;
        }

        foreach ($manifest['schedule'] as $task) {
            if (! isset($task['command'])) {
                continue;
            }

            $event = $schedule->command($task['command']);

            // Map common frequency strings
            match ($task['cron'] ?? 'daily') {
                'everyMinute' => $event->everyMinute(),
                'everyFiveMinutes' => $event->everyFiveMinutes(),
                'everyTenMinutes' => $event->everyTenMinutes(),
                'everyThirtyMinutes' => $event->everyThirtyMinutes(),
                'hourly' => $event->hourly(),
                'daily' => $event->daily(),
                'weekly' => $event->weekly(),
                'monthly' => $event->monthly(),
                default => $event->cron($task['cron']),
            };
        }
    }

    // ──────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────

    /**
     * Get the base path for this plugin.
     */
    protected function basePath(): string
    {
        if ($this->basePath === null) {
            $this->basePath = base_path('plugins/'.$this->pluginSlug);
        }

        return $this->basePath;
    }

    /**
     * Get the PSR-4 namespace for this plugin.
     * Converts slug (e.g., 'article-submission') to PascalCase namespace.
     */
    protected function pluginNamespace(): string
    {
        $pascalSlug = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->pluginSlug)));

        return 'Plugins\\'.$pascalSlug;
    }

    /**
     * Read and cache the plugin.json manifest.
     */
    protected function readManifest(): ?array
    {
        static $cache = [];

        if (isset($cache[$this->pluginSlug])) {
            return $cache[$this->pluginSlug];
        }

        $path = $this->basePath().'/plugin.json';
        if (! file_exists($path)) {
            return $cache[$this->pluginSlug] = null;
        }

        return $cache[$this->pluginSlug] = json_decode(file_get_contents($path), true);
    }

    /**
     * Convert PascalCase or camelCase to kebab-case.
     */
    protected function toKebabCase(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
    }
}
