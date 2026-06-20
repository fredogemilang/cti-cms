<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'module' => 'dashboard', 'action' => 'view', 'description' => 'View dashboard', 'source' => 'core', 'icon' => 'dashboard', 'sort_order' => 1],

            // Users
            ['name' => 'users.view', 'module' => 'users', 'action' => 'view', 'description' => 'View users list', 'source' => 'core', 'icon' => 'group', 'sort_order' => 10],
            ['name' => 'users.create', 'module' => 'users', 'action' => 'create', 'description' => 'Create new user', 'source' => 'core', 'icon' => 'group', 'sort_order' => 10],
            ['name' => 'users.edit', 'module' => 'users', 'action' => 'edit', 'description' => 'Edit user', 'source' => 'core', 'icon' => 'group', 'sort_order' => 10],
            ['name' => 'users.delete', 'module' => 'users', 'action' => 'delete', 'description' => 'Delete user', 'source' => 'core', 'icon' => 'group', 'sort_order' => 10],

            // Roles
            ['name' => 'roles.view', 'module' => 'roles', 'action' => 'view', 'description' => 'View roles list', 'source' => 'core', 'icon' => 'shield', 'sort_order' => 20],
            ['name' => 'roles.create', 'module' => 'roles', 'action' => 'create', 'description' => 'Create new role', 'source' => 'core', 'icon' => 'shield', 'sort_order' => 20],
            ['name' => 'roles.edit', 'module' => 'roles', 'action' => 'edit', 'description' => 'Edit role', 'source' => 'core', 'icon' => 'shield', 'sort_order' => 20],
            ['name' => 'roles.delete', 'module' => 'roles', 'action' => 'delete', 'description' => 'Delete role', 'source' => 'core', 'icon' => 'shield', 'sort_order' => 20],
            ['name' => 'roles.assign-permissions', 'module' => 'roles', 'action' => 'assign-permissions', 'description' => 'Assign permissions to role', 'source' => 'core', 'icon' => 'shield', 'sort_order' => 20],

            // Menus
            ['name' => 'menus.view', 'module' => 'menus', 'action' => 'view', 'description' => 'View menu items', 'source' => 'core', 'icon' => 'menu', 'sort_order' => 30],
            ['name' => 'menus.create', 'module' => 'menus', 'action' => 'create', 'description' => 'Create new menu item', 'source' => 'core', 'icon' => 'menu', 'sort_order' => 30],
            ['name' => 'menus.edit', 'module' => 'menus', 'action' => 'edit', 'description' => 'Edit menu item', 'source' => 'core', 'icon' => 'menu', 'sort_order' => 30],
            ['name' => 'menus.delete', 'module' => 'menus', 'action' => 'delete', 'description' => 'Delete menu item', 'source' => 'core', 'icon' => 'menu', 'sort_order' => 30],

            // Plugins
            ['name' => 'plugins.view', 'module' => 'plugins', 'action' => 'view', 'description' => 'View plugins list', 'source' => 'core', 'icon' => 'extension', 'sort_order' => 40],
            ['name' => 'plugins.install', 'module' => 'plugins', 'action' => 'install', 'description' => 'Install new plugin', 'source' => 'core', 'icon' => 'extension', 'sort_order' => 40],
            ['name' => 'plugins.activate', 'module' => 'plugins', 'action' => 'activate', 'description' => 'Activate plugin', 'source' => 'core', 'icon' => 'extension', 'sort_order' => 40],
            ['name' => 'plugins.deactivate', 'module' => 'plugins', 'action' => 'deactivate', 'description' => 'Deactivate plugin', 'source' => 'core', 'icon' => 'extension', 'sort_order' => 40],
            ['name' => 'plugins.delete', 'module' => 'plugins', 'action' => 'delete', 'description' => 'Delete plugin', 'source' => 'core', 'icon' => 'extension', 'sort_order' => 40],

            // Trash
            ['name' => 'content.trash.view',   'module' => 'trash', 'action' => 'view',   'description' => 'View trashed content',        'source' => 'core', 'icon' => 'delete', 'sort_order' => 55],
            ['name' => 'content.trash.manage', 'module' => 'trash', 'action' => 'manage', 'description' => 'Restore / permanently delete', 'source' => 'core', 'icon' => 'delete', 'sort_order' => 55],

            // Webhooks
            ['name' => 'webhooks.view',   'module' => 'webhooks', 'action' => 'view',   'description' => 'View webhooks',  'source' => 'core', 'icon' => 'sync', 'sort_order' => 85],
            ['name' => 'webhooks.create', 'module' => 'webhooks', 'action' => 'create', 'description' => 'Create webhook', 'source' => 'core', 'icon' => 'sync', 'sort_order' => 85],
            ['name' => 'webhooks.edit',   'module' => 'webhooks', 'action' => 'edit',   'description' => 'Edit webhook',   'source' => 'core', 'icon' => 'sync', 'sort_order' => 85],
            ['name' => 'webhooks.delete', 'module' => 'webhooks', 'action' => 'delete', 'description' => 'Delete webhook', 'source' => 'core', 'icon' => 'sync', 'sort_order' => 85],

            // API Tokens
            ['name' => 'api-tokens.view',   'module' => 'api-tokens', 'action' => 'view',   'description' => 'View API tokens',  'source' => 'core', 'icon' => 'key', 'sort_order' => 90],
            ['name' => 'api-tokens.create', 'module' => 'api-tokens', 'action' => 'create', 'description' => 'Create API token', 'source' => 'core', 'icon' => 'key', 'sort_order' => 90],
            ['name' => 'api-tokens.revoke', 'module' => 'api-tokens', 'action' => 'revoke', 'description' => 'Revoke API token', 'source' => 'core', 'icon' => 'key', 'sort_order' => 90],

            // Email Templates
            ['name' => 'email-templates.view', 'module' => 'email-templates', 'action' => 'view', 'description' => 'View email templates', 'source' => 'core', 'icon' => 'mail', 'sort_order' => 95],
            ['name' => 'email-templates.edit', 'module' => 'email-templates', 'action' => 'edit', 'description' => 'Edit email templates', 'source' => 'core', 'icon' => 'mail', 'sort_order' => 95],
            ['name' => 'email-templates.test', 'module' => 'email-templates', 'action' => 'test', 'description' => 'Test send email',      'source' => 'core', 'icon' => 'mail', 'sort_order' => 95],

            // Queue
            ['name' => 'queue.view',  'module' => 'queue', 'action' => 'view',  'description' => 'View job queue',   'source' => 'core', 'icon' => 'queue', 'sort_order' => 96],
            ['name' => 'queue.retry', 'module' => 'queue', 'action' => 'retry', 'description' => 'Retry failed jobs', 'source' => 'core', 'icon' => 'queue', 'sort_order' => 96],

            // SEO
            ['name' => 'seo.view', 'module' => 'seo', 'action' => 'view', 'description' => 'View SEO data', 'source' => 'core', 'icon' => 'travel_explore', 'sort_order' => 97],
            ['name' => 'seo.edit', 'module' => 'seo', 'action' => 'edit', 'description' => 'Edit SEO data', 'source' => 'core', 'icon' => 'travel_explore', 'sort_order' => 97],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
