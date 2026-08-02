# Plugin Development Guidelines — CTI CMS

Plugins extend CTI CMS functionality without modifying core files. Plugins must be modular, self-contained, and follow the standard registration contracts outlined below.

---

## 1. Plugin Manifest (`plugin.json`)

Every plugin MUST have a `plugin.json` manifest in its root directory:

```json
{
    "name": "Posts",
    "slug": "posts",
    "version": "1.0.0",
    "description": "A blog posts management plugin.",
    "author": "CMS Team",
    "namespace": "Plugins\\Posts",
    "provider": "Plugins\\Posts\\Providers\\PostsServiceProvider",
    "permissions": {
        "resources": [
            {
                "module": "posts",
                "actions": ["view", "create", "edit", "delete", "publish"],
                "icon": "article",
                "description": "Manage blog posts"
            }
        ]
    },
    "requires": {
        "php": ">=8.2",
        "cms": ">=1.0"
    }
}
```

- **Permissions**: Defined in `permissions.resources` are automatically registered into Spatie RBAC and appear in `/ctrlpanel/users` role management.

---

## 2. Centralized String Translation System (`t()`)

Plugins extending CTI CMS must follow the **Centralized String Translation Registry System**. Custom plugins are prohibited from creating isolated translation tables or independent language files.

### A. Using `t()` with Plugin Namespaces
Plugins should namespace their translation keys using dot-notation:

```php
// In Plugin Controllers or Views
$buttonText = t('plugin-posts.save_button', 'Save Post');
```

```blade
<!-- In Plugin Blade Templates -->
<div class="plugin-widget">
    <h3>{{ t('plugin-posts.widget_title', 'Recent Posts') }}</h3>
    <button>{{ t('common.submit', 'Submit') }}</button>
</div>
```

### B. Architecture Rules
- **No Custom Translation Tables**: Plugins MUST use the core `string_translation_keys` and `string_translations` tables.
- **REST API Dictionary**: All plugin translations are automatically included in `GET /api/v1/translations/{locale}`.

---

## 3. Admin Menu Registration Standards

Plugins registering sidebar menu items via `RenderAdminMenu` event MUST follow these rules:

1. **Source Prefix**: Always set `'source' => 'plugin:{plugin-slug}'`.
2. **Explicit `activeRoutePattern`**: Submenu items MUST define an explicit `activeRoutePattern` using pipe-separated routes so that submenus remain active/highlighted when editing or viewing items (e.g. `admin.myplugin.index|admin.myplugin.edit|admin.myplugin.show`).
3. **Parent Wildcard Pattern**: Parent menu items should specify `'activeRoutePattern' => 'admin.myplugin.*'` to ensure the parent container stays expanded across all plugin routes.

```php
protected function registerMenuItems(RenderAdminMenu $event): void
{
    $event->addMenuItem([
        'title'              => 'Posts',
        'route'              => 'admin.posts',
        'activeRoutePattern' => 'admin.posts.*',
        'url'                => route('admin.posts.index'),
        'icon'               => 'rss_feed',
        'permission'         => 'posts.view',
        'is_active'          => true,
        'source'             => 'plugin:posts',
        'children'           => [
            [
                'title'              => 'All Posts',
                'route'              => 'admin.posts.index',
                'activeRoutePattern' => 'admin.posts.index|admin.posts.edit|admin.posts.show',
                'url'                => route('admin.posts.index'),
                'permission'         => 'posts.view',
            ],
            [
                'title'              => 'Create Post',
                'route'              => 'admin.posts.create',
                'activeRoutePattern' => 'admin.posts.create',
                'url'                => route('admin.posts.create'),
                'permission'         => 'posts.create',
            ],
        ],
    ]);
}
```

---

## 4. ServiceProvider & Livewire Registration

Plugin ServiceProviders must extend `App\Providers\CmsPluginServiceProvider`:

```php
namespace Plugins\Posts\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Plugins\Posts\Livewire\PostsTable;
use Plugins\Posts\Livewire\PostForm;

class PostsServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'posts';

    /**
     * Livewire component aliases registered automatically.
     */
    protected array $livewireComponents = [
        'plugins.posts-table' => PostsTable::class,
        'plugins.post-form' => PostForm::class,
    ];

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        // Menu registration logic...
    }
}
```

---

## 5. Core vs Plugin Boundary (Critical Rule)

- **Core (`app/`, `database/`)**: Contains ONLY generic CMS functionality usable by any client.
- **Plugins (`plugins/`)**: Contains domain-specific business logic (Blog/Posts, YouTube integration, Google Site Kit, etc.).
- **Themes (`themes/`)**: Contains client-specific templates and views (e.g. CDT theme).

Never add client-specific or plugin-specific code directly into core controllers or models.
