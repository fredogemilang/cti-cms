<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ActivityPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'activity.view',   'module' => 'activity', 'action' => 'view',   'description' => 'View audit log', 'source' => 'core'],
            ['name' => 'activity.delete', 'module' => 'activity', 'action' => 'delete', 'description' => 'Delete (prune) audit log entries', 'source' => 'core'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        if ($admin = Role::where('name', 'Administrator')->first()) {
            $admin->permissions()->syncWithoutDetaching(
                Permission::where('module', 'activity')->pluck('id')
            );
        }

        $this->command->info('Activity permissions created.');
    }
}
