<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('plugins')->updateOrInsert(
            ['slug' => 'google-site-kit'],
            [
                'name' => 'GoogleSiteKit',
                'version' => '1.0.0',
                'description' => 'Google Site Kit integration for statistics, search console, tracking codes, and speed.',
                'author' => 'CMS Team',
                'provider' => 'Plugins\\GoogleSiteKit\\Providers\\GoogleSiteKitServiceProvider',
                'is_active' => true,
                'installed_at' => now(),
                'activated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed permissions
        $permissions = [
            [
                'name' => 'google-site-kit.view',
                'module' => 'google-site-kit',
                'resource' => 'google-site-kit',
                'action' => 'view',
                'description' => 'View Google Site Kit dashboard',
                'source' => 'plugin:google-site-kit',
                'plugin_slug' => 'google-site-kit',
                'is_active' => true,
                'icon' => 'query_stats',
                'sort_order' => 100,
            ],
            [
                'name' => 'google-site-kit.edit',
                'module' => 'google-site-kit',
                'resource' => 'google-site-kit',
                'action' => 'edit',
                'description' => 'Configure Google Site Kit credentials and tags',
                'source' => 'plugin:google-site-kit',
                'plugin_slug' => 'google-site-kit',
                'is_active' => true,
                'icon' => 'settings',
                'sort_order' => 101,
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Attach new permissions to Administrator role
        $adminRole = DB::table('roles')->where('slug', 'administrator')->first();
        if ($adminRole) {
            $perms = DB::table('permissions')
                ->whereIn('name', ['google-site-kit.view', 'google-site-kit.edit'])
                ->pluck('id');

            foreach ($perms as $permId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plugins')->where('slug', 'google-site-kit')->delete();
        DB::table('permissions')->where('plugin_slug', 'google-site-kit')->delete();
    }
};
