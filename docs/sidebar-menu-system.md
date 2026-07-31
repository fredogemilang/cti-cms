# Sidebar Menu System

The admin sidebar uses a **unified, auto-discoverable menu registration system**. All menu items — core features, Custom Post Types (CPTs), plugins, and built-in modules — are registered through a single service and rendered by a generic Blade component.

## Architecture Overview

```
┌─────────────────────────────────────────────┐
│          AdminMenuBuilder Service           │
│                                              │
│  ┌──────────┐  ┌──────────┐  ┌───────────┐  │
│  │ Core DB  │  │   CPTs   │  │  Plugins  │  │
│  │ Items    │  │          │  │  (Event)  │  │
│  └────┬─────┘  └────┬─────┘  └─────┬─────┘  │
│       │              │              │        │
│  ┌────┴─────┐        │              │        │
│  │ Meta     │        │              │        │
│  │ Override │        │              │        │
│  └────┬─────┘        │              │        │
│       │              │              │        │
│       └──────────┬───┘──────────────┘        │
│                  │                           │
│  ┌───────────────┴──────────────┐            │
│  │  Built-in Items              │            │
│  │  (Form Builder, CPT Mgr,    │            │
│  │   Settings)                  │            │
│  └───────────────┬──────────────┘            │
│                  │                           │
│                  ▼                           │
│         getUnifiedMenuList()                 │
│         (standardized contract)              │
└──────────────────┬──────────────────────────┘
                   │
          ┌────────┴────────┐
          │                 │
          ▼                 ▼
   sidebar-new         Menu Customizer
   (generic render)    (drag & drop)
```

## Data Contract

Every menu item follows this standardized structure:

```php
[
    'key'                => 'builtin:form-builder',   // Unique identifier
    'title'              => 'Form Builder',           // Display name
    'icon'               => 'dynamic_form',           // Material Symbols icon name
    'permission'         => 'forms.view',             // Required permission (null = all users)
    'source'             => 'core',                   // 'core'|'cpt'|'plugin' (badge type)
    'source_label'       => 'Core System',            // Badge text in Menu Customizer
    'section'            => 'SYSTEM',                 // 'MAIN'|'CONTENT'|'PLUGINS'|'SYSTEM'
    'is_active'          => true,                     // Visibility toggle
    'activeRoutePattern' => 'admin.forms.*',          // Route pattern(s) for active state
    'url'                => null,                     // Direct URL (plugins use this)
    'children'           => [                         // Sub-menu items
        [
            'title'              => 'All Forms',
            'route'              => 'admin.forms.index',       // Named route
            'routeParams'        => [],                        // Route parameters
            'permission'         => null,                      // Per-child permission
            'activeRoutePattern' => 'admin.forms.index',       // Active state pattern
        ],
    ],
]
```

### Key Format

| Prefix | Source | Example |
|--------|--------|---------|
| `core:` | DB `menu_items` table | `core:1` (Dashboard), `core:5` (Users) |
| `cpt:` | `custom_post_types` table | `cpt:solutions`, `cpt:tech-products` |
| `plugin:` | Plugin via `RenderAdminMenu` event | `plugin:posts`, `plugin:google-site-kit` |
| `builtin:` | Hardcoded in `getBuiltinItems()` | `builtin:form-builder`, `builtin:settings` |

### Active State Pattern

`activeRoutePattern` supports pipe-separated patterns:

```php
'activeRoutePattern' => 'admin.users.*|admin.profile.*|admin.roles.*',
```

The sidebar component uses `request()->routeIs()` to check each pattern.

## How to Add a New Menu Item

### Adding a Core Built-in Feature

Edit `AdminMenuBuilder.php`:

1. **Add the item** in `getBuiltinItems()`:

```php
$items[] = [
    'key' => 'builtin:my-feature',
    'title' => 'My Feature',
    'icon' => 'star',
    'permission' => 'my-feature.view',
    'source' => 'core',
    'source_label' => 'Core System',
    'section' => $activeSectionMap['builtin:my-feature'] ?? 'SYSTEM',
    'is_active' => true,
    'activeRoutePattern' => 'admin.my-feature.*',
    'children' => [
        [
            'title' => 'Dashboard',
            'route' => 'admin.my-feature.index',
            'routeParams' => [],
            'permission' => null,
            'activeRoutePattern' => 'admin.my-feature.index',
        ],
    ],
];
```

2. **Add to section map** in `getUnifiedMenuList()`:

```php
$defaultSectionMap['builtin:my-feature'] = 'SYSTEM';
```

3. **Add to default order** in `sortItemsByCustomOrder()`:

```php
$defaultOrderKeys[] = 'builtin:my-feature';
```

That's it — the sidebar and Menu Customizer automatically pick it up.

### Adding a Plugin Menu Item

In your plugin's service provider:

```php
protected function registerMenuItems(RenderAdminMenu $event): void
{
    $event->addMenuItem([
        'title'      => 'My Plugin',
        'route'      => 'admin.myplugin',
        'url'        => route('admin.myplugin.index'),
        'icon'       => 'extension',
        'permission' => 'myplugin.view',
        'is_active'  => true,
        'source'     => 'plugin:my-plugin',  // MUST use 'plugin:{slug}' prefix
        'children'   => [
            [
                'title'      => 'All Items',
                'route'      => 'admin.myplugin.index',
                'url'        => route('admin.myplugin.index'),
                'permission' => 'myplugin.view',
            ],
        ],
    ]);
}
```

Plugin items are auto-discovered via the `RenderAdminMenu` event and appear in both the sidebar and Menu Customizer.

### Adding a Core DB Menu Item

1. Insert a row in `menu_items` table
2. Add rendering metadata in `getCoreItemsMeta()`:

```php
99 => [
    'icon' => 'my_icon',
    'permission' => 'my.permission',
    'activeRoutePattern' => 'admin.my-route.*',
    'children' => [
        [
            'title' => 'Sub-item',
            'route' => 'admin.my-route.index',
            'routeParams' => [],
            'permission' => null,
            'activeRoutePattern' => 'admin.my-route.index',
        ],
    ],
],
```

## Menu Customizer (Drag & Drop)

The Menu Customizer at `/ctrlpanel/menus` reads from `getUnifiedMenuList()` and allows reordering. The custom order is stored in `settings` as `admin_sidebar_custom_order`.

When a custom order exists, the section assignments are derived from the order array (items between `SECTION:MAIN` and `SECTION:CONTENT` belong to MAIN, etc.).

## Sidebar Rendering

The sidebar uses three Blade partials:

| File | Purpose |
|------|---------|
| `sidebar-new.blade.php` | Main loop — iterates items, renders section headers |
| `sidebar-item.blade.php` | Dispatcher — checks permissions, delegates to link or expandable |
| `sidebar-item-link.blade.php` | Simple link items (no children) |
| `sidebar-item-expandable.blade.php` | Expandable items with children + flyout support |

### Special Cases

- **CPT items**: Active state checks both route pattern AND `postTypeSlug` parameter
- **Settings children**: Use `_settingsSlug` to match `request()->route('group')` parameter
- **Plugin items**: May use `url` instead of `route` for href resolution

## File Reference

| File | Role |
|------|------|
| `app/Services/AdminMenuBuilder.php` | Central registry — all items defined here |
| `app/Events/RenderAdminMenu.php` | Event hook for plugins |
| `app/Providers/CmsPluginServiceProvider.php` | Base class with `registerMenuItems()` |
| `resources/views/components/admin/sidebar-new.blade.php` | Main sidebar template |
| `resources/views/components/admin/sidebar-item.blade.php` | Generic item component |
| `resources/views/components/admin/sidebar-item-link.blade.php` | Link partial |
| `resources/views/components/admin/sidebar-item-expandable.blade.php` | Expandable partial |
| `resources/views/admin/menus/index.blade.php` | Menu Customizer UI |
