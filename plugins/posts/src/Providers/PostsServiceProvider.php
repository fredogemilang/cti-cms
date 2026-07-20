<?php

namespace Plugins\Posts\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Plugins\Posts\Livewire\BlogList;
use Plugins\Posts\Livewire\CategoriesManager;
use Plugins\Posts\Livewire\PostForm;
use Plugins\Posts\Livewire\PostsTable;
use Plugins\Posts\Livewire\Settings;
use Plugins\Posts\Livewire\WordPressMigration;

class PostsServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'posts';

    /**
     * Manual Livewire component mappings.
     *
     * These override auto-discovery for backward compatibility
     * with existing view references like @livewire('plugins.posts-table').
     */
    protected array $livewireComponents = [
        'plugins.posts-table' => PostsTable::class,
        'plugins.post-form' => PostForm::class,
        'plugins.categories-manager' => CategoriesManager::class,
        'plugins.posts-settings' => Settings::class,
        'plugins.wordpress-migration' => WordPressMigration::class,
        'posts.blog-list' => BlogList::class,
    ];

    /**
     * Register admin menu items.
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'Posts',
            'route' => 'admin.posts',
            'url' => route('admin.posts.index'),
            'icon' => 'rss_feed',
            'permission' => 'posts.view',
            'is_active' => true,
            'source' => 'plugin:posts',
            'children' => [
                [
                    'title' => 'All Posts',
                    'route' => 'admin.posts.index',
                    'url' => route('admin.posts.index'),
                    'icon' => 'list',
                    'permission' => 'posts.view',
                    'is_active' => true,
                    'source' => 'plugin:posts',
                    'children' => [],
                ],
                [
                    'title' => 'Create Post',
                    'route' => 'admin.posts.create',
                    'url' => route('admin.posts.create'),
                    'icon' => 'add_circle',
                    'permission' => 'posts.create',
                    'is_active' => true,
                    'source' => 'plugin:posts',
                    'children' => [],
                ],
                [
                    'title' => 'Categories',
                    'route' => 'admin.posts.categories',
                    'url' => route('admin.posts.categories'),
                    'icon' => 'category',
                    'permission' => 'categories.view',
                    'is_active' => true,
                    'source' => 'plugin:posts',
                    'children' => [],
                ],
                [
                    'title' => 'Tags',
                    'route' => 'admin.posts.tags',
                    'url' => route('admin.posts.tags'),
                    'icon' => 'label',
                    'permission' => 'tags.view',
                    'is_active' => true,
                    'source' => 'plugin:posts',
                    'children' => [],
                ],
                [
                    'title' => 'Settings',
                    'route' => 'admin.posts.settings',
                    'url' => route('admin.posts.settings'),
                    'icon' => 'settings',
                    'permission' => 'posts.view',
                    'is_active' => true,
                    'source' => 'plugin:posts',
                    'children' => [],
                ],
            ],
        ]);
    }
}
