<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SettingsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'settings.view',
                'module' => 'settings',
                'action' => 'view',
                'description' => 'View CMS settings',
                'source' => 'core',
            ],
            [
                'name' => 'settings.edit',
                'module' => 'settings',
                'action' => 'edit',
                'description' => 'Edit CMS settings',
                'source' => 'core',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        $adminRole = Role::where('name', 'Administrator')->first();
        if ($adminRole) {
            $perms = Permission::where('module', 'settings')->get();
            $adminRole->permissions()->syncWithoutDetaching($perms->pluck('id'));
        }

        $this->command->info('Settings permissions created.');
    }
}
