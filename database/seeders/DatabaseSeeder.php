<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Master database seeder.
 *
 * For fresh installs, prefer `php artisan cms:install` which runs this
 * seeder AND creates the admin user interactively.
 *
 * This seeder only seeds core CMS data (roles, permissions, menu, templates).
 * Client-specific or plugin seeders should be run separately.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // ── Core RBAC ──────────────────────────
            RoleSeeder::class,
            PermissionSeeder::class,

            // ── Module Permissions ─────────────────
            MediaPermissionsSeeder::class,
            ThemePermissionsSeeder::class,
            PagesPermissionsSeeder::class,
            SettingsPermissionsSeeder::class,
            ActivityPermissionsSeeder::class,
            FormsPermissionsSeeder::class,

            // ── UI & Content ───────────────────────
            MenuItemSeeder::class,
            EmailTemplateSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('Core CMS data seeded successfully.');
        $this->command->info('Run `php artisan cms:install` for the full setup wizard.');
    }
}
