<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Interactive CMS installation wizard.
 *
 * Handles first-time setup:
 *  1. Environment validation
 *  2. Database migration
 *  3. Core data seeding (roles, permissions, menu, email templates)
 *  4. Admin user creation
 *  5. Storage link creation
 *
 * Safe to re-run — uses updateOrCreate patterns.
 *
 * Usage:
 *   php artisan cms:install                    # Interactive wizard
 *   php artisan cms:install --no-interaction   # CI/Docker (uses defaults + env vars)
 */
class CmsInstall extends Command
{
    protected $signature = 'cms:install
        {--site-name=  : The site name}
        {--admin-name= : The admin user display name}
        {--admin-email= : The admin user email}
        {--admin-password= : The admin user password}
        {--timezone= : Application timezone (e.g. UTC, Asia/Jakarta)}
        {--locale= : Default locale (e.g. en, id)}
        {--skip-migrate : Skip running migrations}
        {--skip-seed : Skip running seeders}
        {--force : Force install even in production}';

    protected $description = 'Run the CMS installation wizard.';

    public function handle(): int
    {
        $this->printBanner();

        // ── Step 1: Environment Check ────────────────────────────
        $this->info('');
        $this->components->info('Step 1/5 — Environment Check');

        if (! $this->checkEnvironment()) {
            return self::FAILURE;
        }

        // ── Step 2: Gather Configuration ─────────────────────────
        $this->components->info('Step 2/5 — Site Configuration');
        $config = $this->gatherConfig();

        // ── Step 3: Database Migration ───────────────────────────
        $this->components->info('Step 3/5 — Database');

        if (! $this->option('skip-migrate')) {
            $this->runMigrations();
        } else {
            $this->components->warn('Migrations skipped (--skip-migrate).');
        }

        // ── Step 4: Seed Core Data ───────────────────────────────
        $this->components->info('Step 4/5 — Seed Core Data');

        if (! $this->option('skip-seed')) {
            $this->seedCoreData();
        } else {
            $this->components->warn('Seeding skipped (--skip-seed).');
        }

        // ── Step 5: Create Admin User ────────────────────────────
        $this->components->info('Step 5/5 — Admin User');
        $this->createAdminUser($config);

        // ── Post-install ─────────────────────────────────────────
        $this->postInstall($config);

        $this->printSummary($config);

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    //  Environment check
    // ──────────────────────────────────────────────

    protected function checkEnvironment(): bool
    {
        $checks = [
            ['PHP >= 8.2', version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['APP_KEY set', ! empty(config('app.key'))],
            ['Database connection', $this->checkDatabase()],
            ['Storage directory writable', is_writable(storage_path())],
        ];

        $allPassed = true;
        foreach ($checks as [$label, $passed]) {
            if ($passed) {
                $this->components->twoColumnDetail($label, '<fg=green>✓ OK</>');
            } else {
                $this->components->twoColumnDetail($label, '<fg=red>✗ FAILED</>');
                $allPassed = false;
            }
        }

        if (! $allPassed) {
            $this->newLine();
            $this->components->error('Environment checks failed. Fix the issues above and re-run.');

            // Special hint for APP_KEY
            if (empty(config('app.key'))) {
                $this->line('  Run: <comment>php artisan key:generate</comment>');
            }

            return false;
        }

        return true;
    }

    protected function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ──────────────────────────────────────────────
    //  Configuration gathering
    // ──────────────────────────────────────────────

    protected function gatherConfig(): array
    {
        $interactive = ! $this->option('no-interaction');

        $siteName = $this->option('site-name')
            ?? ($interactive ? $this->ask('Site name', config('app.name', 'My CMS')) : config('app.name', 'My CMS'));

        $timezone = $this->option('timezone')
            ?? ($interactive ? $this->anticipate('Timezone', ['UTC', 'Asia/Jakarta', 'America/New_York', 'Europe/London'], config('app.timezone', 'UTC')) : config('app.timezone', 'UTC'));

        $locale = $this->option('locale')
            ?? ($interactive ? $this->anticipate('Default locale', ['en', 'id', 'es', 'fr', 'de', 'ja', 'zh'], config('app.locale', 'en')) : config('app.locale', 'en'));

        $adminName = $this->option('admin-name')
            ?? ($interactive ? $this->ask('Admin name', 'Administrator') : 'Administrator');

        $adminEmail = $this->option('admin-email')
            ?? ($interactive ? $this->ask('Admin email', 'admin@example.com') : 'admin@example.com');

        $adminPassword = $this->option('admin-password')
            ?? ($interactive ? $this->secret('Admin password (min 8 chars)') : 'password');

        // Validate password length
        if (strlen($adminPassword) < 8) {
            $this->components->warn('Password too short — using "password" as default.');
            $adminPassword = 'password';
        }

        return [
            'site_name' => $siteName,
            'timezone' => $timezone,
            'locale' => $locale,
            'admin_name' => $adminName,
            'admin_email' => $adminEmail,
            'admin_password' => $adminPassword,
        ];
    }

    // ──────────────────────────────────────────────
    //  Migration
    // ──────────────────────────────────────────────

    protected function runMigrations(): void
    {
        $this->components->task('Running migrations', function () {
            Artisan::call('migrate', [
                '--force' => true,
            ]);

            return true;
        });
    }

    // ──────────────────────────────────────────────
    //  Seeding
    // ──────────────────────────────────────────────

    protected function seedCoreData(): void
    {
        $seeders = [
            'Roles' => \Database\Seeders\RoleSeeder::class,
            'Core Permissions' => \Database\Seeders\PermissionSeeder::class,
            'Media Permissions' => \Database\Seeders\MediaPermissionsSeeder::class,
            'Theme Permissions' => \Database\Seeders\ThemePermissionsSeeder::class,
            'Pages Permissions' => \Database\Seeders\PagesPermissionsSeeder::class,
            'Settings Permissions' => \Database\Seeders\SettingsPermissionsSeeder::class,
            'Activity Permissions' => \Database\Seeders\ActivityPermissionsSeeder::class,
            'Forms Permissions' => \Database\Seeders\FormsPermissionsSeeder::class,
            'Admin Menu' => \Database\Seeders\MenuItemSeeder::class,
            'Email Templates' => \Database\Seeders\EmailTemplateSeeder::class,
        ];

        foreach ($seeders as $label => $class) {
            $this->components->task($label, function () use ($class) {
                Artisan::call('db:seed', [
                    '--class' => $class,
                    '--force' => true,
                ]);

                return true;
            });
        }
    }

    // ──────────────────────────────────────────────
    //  Admin user
    // ──────────────────────────────────────────────

    protected function createAdminUser(array $config): void
    {
        $this->components->task('Creating admin user', function () use ($config) {
            $user = User::updateOrCreate(
                ['email' => $config['admin_email']],
                [
                    'name' => $config['admin_name'],
                    'password' => Hash::make($config['admin_password']),
                ]
            );

            $adminRole = Role::where('slug', 'administrator')->first();

            if ($adminRole && ! $user->roles()->where('role_id', $adminRole->id)->exists()) {
                $user->roles()->attach($adminRole->id);
            }

            return true;
        });
    }

    // ──────────────────────────────────────────────
    //  Post-install
    // ──────────────────────────────────────────────

    protected function postInstall(array $config): void
    {
        // Create storage symlink
        if (! file_exists(public_path('storage'))) {
            $this->components->task('Creating storage symlink', function () {
                Artisan::call('storage:link');

                return true;
            });
        }

        // Activate default theme
        $this->components->task('Activating default theme', function () {
            if (! Schema::hasTable('themes')) {
                return false;
            }

            DB::table('themes')->updateOrInsert(
                ['slug' => 'default'],
                [
                    'name' => 'Default',
                    'version' => '1.0.0',
                    'description' => 'A clean, modern default theme for the Web CMS.',
                    'author' => 'Web CMS',
                    'is_active' => true,
                    'supports' => json_encode(['pages', 'posts', 'menus']),
                    'installed_at' => now(),
                    'activated_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return true;
        });

        // Publish theme assets
        $this->components->task('Publishing theme assets', function () {
            Artisan::call('theme:publish', ['--all' => true]);

            return true;
        });

        // Save site settings to database
        $this->components->task('Saving site settings', function () use ($config) {
            if (! Schema::hasTable('settings')) {
                return false;
            }

            $settings = [
                'site_name' => $config['site_name'],
                'site_timezone' => $config['timezone'],
            ];

            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => json_encode($value),
                        'group' => 'general',
                        'type' => 'string',
                        'updated_at' => now(),
                    ]
                );
            }

            return true;
        });

        // Write an install marker
        $this->components->task('Marking installation complete', function () {
            $markerPath = storage_path('cms_installed');
            File::put($markerPath, json_encode([
                'version' => config('cms.version', '1.0.0'),
                'installed_at' => now()->toIso8601String(),
                'php_version' => PHP_VERSION,
            ]));

            return true;
        });
    }

    // ──────────────────────────────────────────────
    //  UI
    // ──────────────────────────────────────────────

    protected function printBanner(): void
    {
        $this->newLine();
        $this->line('<fg=cyan>  ╔══════════════════════════════════════════╗</>');
        $this->line('<fg=cyan>  ║</>      <fg=white;options=bold>Web CMS — Installation Wizard</>       <fg=cyan>║</>');
        $this->line('<fg=cyan>  ║</>      <fg=gray>v' . config('cms.version', '1.0.0') . '</>' . str_repeat(' ', 30 - strlen(config('cms.version', '1.0.0'))) . '<fg=cyan>║</>');
        $this->line('<fg=cyan>  ╚══════════════════════════════════════════╝</>');
    }

    protected function printSummary(array $config): void
    {
        $adminPath = config('admin.path', config('cms.path', 'admin'));
        $appUrl = config('app.url', 'http://localhost');

        $this->newLine();
        $this->line('<fg=green>  ╔══════════════════════════════════════════╗</>');
        $this->line('<fg=green>  ║</>   <fg=white;options=bold>✓ Installation Complete!</>               <fg=green>║</>');
        $this->line('<fg=green>  ╚══════════════════════════════════════════╝</>');
        $this->newLine();

        $this->components->twoColumnDetail('<fg=gray>Site Name</>', $config['site_name']);
        $this->components->twoColumnDetail('<fg=gray>Admin URL</>', "<fg=cyan>{$appUrl}/{$adminPath}</>");
        $this->components->twoColumnDetail('<fg=gray>Admin Email</>', $config['admin_email']);
        $this->components->twoColumnDetail('<fg=gray>Timezone</>', $config['timezone']);
        $this->components->twoColumnDetail('<fg=gray>Locale</>', $config['locale']);
        $this->components->twoColumnDetail('<fg=gray>CMS Version</>', config('cms.version', '1.0.0'));

        $this->newLine();
        $this->components->info('Run `php artisan serve` to start the development server.');
    }
}
