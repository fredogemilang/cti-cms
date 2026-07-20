<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Scaffold a new CMS plugin with standard directory structure.
 *
 * Usage:
 *   php artisan make:plugin contact-form
 *   php artisan make:plugin article-submission --with-model=ArticleSubmission
 */
class MakePlugin extends Command
{
    protected $signature = 'make:plugin
        {slug : The plugin slug (kebab-case, e.g. contact-form)}
        {--with-model= : Generate an Eloquent model with this name}';

    protected $description = 'Scaffold a new CMS plugin with standard directory structure.';

    public function handle(): int
    {
        $slug = Str::slug($this->argument('slug'));
        $pluginPath = base_path("plugins/{$slug}");

        if (File::exists($pluginPath)) {
            $this->error("Plugin directory already exists: plugins/{$slug}");

            return self::FAILURE;
        }

        $pascalName = Str::studly($slug);
        $namespace = "Plugins\\{$pascalName}";
        $provider = "{$namespace}\\Providers\\{$pascalName}ServiceProvider";

        $this->info("Creating plugin: {$pascalName} ({$slug})");

        // Create directory structure
        $directories = [
            'src/Providers',
            'src/Livewire',
            'routes',
            'resources/views',
            'database/migrations',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$pluginPath}/{$dir}", 0755, true);
        }

        // Generate plugin.json
        File::put("{$pluginPath}/plugin.json", $this->stubPluginJson($pascalName, $slug, $provider, $namespace));

        // Generate ServiceProvider
        File::put("{$pluginPath}/src/Providers/{$pascalName}ServiceProvider.php", $this->stubServiceProvider($pascalName, $namespace));

        // Generate routes/web.php
        File::put("{$pluginPath}/routes/web.php", $this->stubRoutes($namespace, $slug));

        // Generate README.md
        File::put("{$pluginPath}/README.md", $this->stubReadme($pascalName, $slug));

        // Optional: generate model
        $modelName = $this->option('with-model');
        if ($modelName) {
            File::makeDirectory("{$pluginPath}/src/Models", 0755, true);
            File::put("{$pluginPath}/src/Models/{$modelName}.php", $this->stubModel($namespace, $modelName));
            $this->line("  ✓ Model: src/Models/{$modelName}.php");
        }

        $this->newLine();
        $this->info('Plugin scaffolded successfully!');
        $this->newLine();

        $this->table(['File', 'Purpose'], [
            ['plugin.json', 'Plugin manifest (name, version, permissions)'],
            ["src/Providers/{$pascalName}ServiceProvider.php", 'Service provider (extends CmsPluginServiceProvider)'],
            ['routes/web.php', 'Web routes (auto-loaded)'],
            ['resources/views/', 'Blade views (auto-loaded as \''.$slug.'::*\')'],
            ['database/migrations/', 'Database migrations (auto-loaded)'],
        ]);

        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Upload & install via Admin → Plugins, or register manually:');
        $this->line("     INSERT INTO plugins (name, slug, provider, is_active) VALUES ('{$pascalName}', '{$slug}', '{$provider}', 1);");
        $this->line('  2. Run: php artisan migrate');
        $this->line('  3. Clear cache: php artisan optimize:clear');

        return self::SUCCESS;
    }

    protected function stubPluginJson(string $name, string $slug, string $provider, string $namespace): string
    {
        $data = [
            'name' => $name,
            'slug' => $slug,
            'version' => '1.0.0',
            'description' => "A {$name} plugin for the CMS.",
            'author' => 'CMS Team',
            'namespace' => $namespace,
            'provider' => $provider,
            'permissions' => [
                'resources' => [
                    [
                        'module' => $slug,
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'icon' => 'extension',
                        'description' => "Manage {$name}",
                    ],
                ],
            ],
            'requires' => [
                'php' => '>=8.2',
                'cms' => '>=1.0',
            ],
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n";
    }

    protected function stubServiceProvider(string $name, string $namespace): string
    {
        $adminPath = config('admin.path', 'admin');

        return <<<PHP
<?php

namespace {$namespace}\\Providers;

use App\\Events\\RenderAdminMenu;
use App\\Providers\\CmsPluginServiceProvider;
use App\\Services\\SettingsRegistry;

class {$name}ServiceProvider extends CmsPluginServiceProvider
{
    protected string \$pluginSlug = '{$this->toSlug($name)}';

    /**
     * Register admin menu items.
     */
    protected function registerMenuItems(RenderAdminMenu \$event): void
    {
        // \$event->addMenuItem([
        //     'title'      => '{$name}',
        //     'route'      => 'admin.{$this->toSlug($name)}',
        //     'url'        => route('admin.{$this->toSlug($name)}.index'),
        //     'icon'       => 'extension',
        //     'permission' => '{$this->toSlug($name)}.view',
        //     'is_active'  => true,
        //     'source'     => 'plugin:{$this->toSlug($name)}',
        //     'children'   => [],
        // ]);
    }

    /**
     * Register settings fields (appears under Admin → Settings).
     */
    protected function registerSettings(SettingsRegistry \$registry): void
    {
        // \$registry->registerGroup('plugin_{$this->toSlug($name)}', [
        //     'label'       => '{$name}',
        //     'icon'        => 'extension',
        //     'order'       => 200,
        //     'description' => '{$name} plugin settings.',
        //     'fields'      => [
        //         ['key' => '{$this->toSnake($name)}_enabled', 'label' => 'Enable {$name}', 'type' => 'boolean', 'default' => true, 'rules' => ['boolean']],
        //     ],
        // ]);
    }
}

PHP;
    }

    protected function stubRoutes(string $namespace, string $slug): string
    {
        $adminPath = config('admin.path', 'admin');

        return <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;

/*
|--------------------------------------------------------------------------
| {$slug} Plugin Routes
|--------------------------------------------------------------------------
|
| Routes are auto-loaded by CmsPluginServiceProvider.
| Admin routes should use the 'web' middleware group (applied automatically).
|
*/

// Public routes
// Route::get('/{$slug}', function () {
//     return view('{$slug}::index');
// })->name('{$slug}.index');

// Admin routes
// Route::middleware(['web', 'auth'])->prefix('{$adminPath}/{$slug}')->name('admin.{$slug}.')->group(function () {
//     Route::get('/', function () {
//         return view('{$slug}::admin.index');
//     })->name('index');
// });

PHP;
    }

    protected function stubReadme(string $name, string $slug): string
    {
        return <<<MD
# {$name} Plugin

> A plugin for the Web CMS.

## Installation

1. Copy this directory to `plugins/{$slug}/`
2. Go to **Admin → Plugins** and activate **{$name}**
3. Run `php artisan migrate` to create database tables

## Directory Structure

```
{$slug}/
├── plugin.json              # Plugin manifest
├── src/
│   ├── Providers/           # Service provider
│   ├── Livewire/            # Livewire components (auto-discovered)
│   └── Models/              # Eloquent models
├── routes/
│   └── web.php              # Web routes (auto-loaded)
├── resources/
│   └── views/               # Blade views (namespace: '{$slug}::')
├── database/
│   └── migrations/          # Migrations (auto-loaded)
└── README.md
```

## Development

This plugin extends `CmsPluginServiceProvider` which auto-loads:
- **Routes** from `routes/web.php` and `routes/api.php`
- **Views** from `resources/views/` (accessible as `{$slug}::view.name`)
- **Migrations** from `database/migrations/`
- **Livewire** components from `src/Livewire/` (registered as `plugins.{$slug}.*`)

Override hook methods in the ServiceProvider for:
- `registerMenuItems()` — admin sidebar entries
- `registerSettings()` — settings page fields
- `registerScheduledTasks()` — cron jobs

MD;
    }

    protected function stubModel(string $namespace, string $modelName): string
    {
        return <<<PHP
<?php

namespace {$namespace}\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class {$modelName} extends Model
{
    protected \$fillable = [
        //
    ];
}

PHP;
    }

    private function toSlug(string $name): string
    {
        return Str::slug(Str::snake($name));
    }

    private function toSnake(string $name): string
    {
        return Str::snake($name);
    }
}
