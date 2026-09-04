<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EditorialPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'content.approve'],
            [
                'name' => 'content.approve',
                'module' => 'editorial',
                'action' => 'approve',
                'description' => 'Approve and publish editorial content',
                'source' => 'core',
            ]
        );

        // Assign to Administrator / Super Admin roles
        $roles = Role::whereIn('slug', ['super-admin', 'admin'])
            ->orWhereIn('name', ['Administrator', 'Super Administrator', 'Super Admin'])
            ->get();

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        if ($this->command) {
            $this->command->info('Editorial permissions created and assigned successfully.');
        }
    }
}
