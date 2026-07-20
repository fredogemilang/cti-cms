<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full access to all CMS features.',
                'is_super_admin' => true,
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can manage content and users.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'description' => 'Can create and manage own content.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'Subscriber',
                'slug' => 'subscriber',
                'description' => 'Read-only access to the dashboard.',
                'is_super_admin' => false,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
