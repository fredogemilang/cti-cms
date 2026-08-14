# Plugin System

## Architecture

Plugins are self-contained packages at `plugins/{slug}/`. Each has its own namespace, routes, views, migrations, and admin pages.

```
plugins/{slug}/
├── plugin.json             ← {name, slug, version, provider}
├── routes/
│   ├── web.php             ← MUST include 'web' middleware
│   └── api.php             ← Auto-discovered REST endpoints
├── src/
│   ├── Providers/
│   │   └── {Plugin}ServiceProvider.php  ← Entry point
│   ├── Http/Controllers/   ← Plugin controllers
│   ├── Livewire/            ← Plugin Livewire components
│   └── Models/              ← Plugin Eloquent models
├── database/migrations/    ← Auto-loaded by PluginLoader
└── resources/views/        ← Views namespace = plugin slug
```

## PluginLoader

`app/Services/PluginLoader.php`:
1. Queries active plugins from `plugins` table
2. Auto-registers PSR-4 namespace `Plugins\{PascalCaseSlug}\`
3. Calls each plugin's ServiceProvider `boot()`
4. Validates routes include `web` middleware
5. Discovers API routes from `plugins/{slug}/routes/api.php`

## Activation

- Plugins stored in `plugins` DB table
- Activate/deactivate via admin UI at `/ctrlpanel/plugins`
- Toggle also available via REST API: `POST /api/v1/admin/plugins/{slug}/toggle`
- On activation: migrations run, routes registered, menu items added
- On deactivation: routes removed, menu items hidden

## Menu Registration

Use the `RenderAdminMenu` event — never seed menus to DB:
```php
Event::listen(RenderAdminMenu::class, function ($event) {
    $event->addMenuItem([
        'title' => 'Posts',
        'route' => 'admin.posts.index',
        'url' => route('admin.posts.index'),
        'icon' => 'rss_feed', // Material Symbols name — NOT the 'lucide:' prefix
        'permission' => 'posts.view',
        'is_active' => true,
    ]);
});
```

## Permission Naming

`{resource}.{action}` format:
- `posts.view`, `posts.create`, `posts.edit`, `posts.delete`
- `events.view`, `events.manage`, `events.export`

## Checking Plugin Status

Always check before using plugin models/views:
```php
if (is_plugin_active('posts')) {
    $posts = \Plugins\Posts\Models\Post::published()->get();
}
```

## Built-in Plugins

| Plugin | Slug | Purpose |
|--------|------|---------|
| Posts | `posts` | Blog with categories, tags, featured images |
| Google Site Kit | `google-site-kit` | Analytics + Search Console integration |

## Key Rules

- Blog content ALWAYS uses Posts plugin, never CPT
- Plugin views are namespaced by slug: `posts::index`, `posts::show`
- Plugin assets go in `plugins/{slug}/resources/assets/`
- See `docs/plugin-development.md` for full development guide
