# Plugin Development Guide

Build plugins to extend the CMS with custom functionality. Plugins are self-contained packages with their own routes, views, models, and Livewire components.

## Table of Contents
- [Quick Start](#quick-start)
- [Plugin Structure](#plugin-structure)
- [Service Provider](#service-provider)
- [Route Configuration](#route-configuration)
- [Permission System](#permission-system)
- [Menu Registration](#menu-registration)
- [Settings Registration](#settings-registration)
- [View Development](#view-development)
- [Permalink Slug Pattern](#permalink-slug-pattern)
- [Testing](#testing)
- [Checklist](#checklist)

---

## Quick Start

```bash
# Scaffold a plugin
php artisan make:plugin contact-form

# Scaffold with a model
php artisan make:plugin gallery --with-model=GalleryItem
```

This generates a complete plugin in `plugins/contact-form/` with a service provider, routes, views, and migrations — all pre-configured for auto-discovery.

---

## Plugin Structure

```
plugins/
└── contact-form/
    ├── plugin.json                         # Manifest (required)
    ├── src/
    │   ├── Providers/
    │   │   └── ContactFormServiceProvider.php  # Extends CmsPluginServiceProvider
    │   ├── Http/Controllers/
    │   ├── Models/
    │   └── Livewire/                       # Auto-discovered components
    ├── routes/
    │   ├── web.php                         # Auto-loaded web routes
    │   └── api.php                         # Auto-loaded API routes (optional)
    ├── resources/views/                    # Auto-loaded as '{slug}::' namespace
    ├── database/migrations/                # Auto-loaded
    ├── config/contact-form.php             # Auto-loaded (optional)
    └── README.md
```

### plugin.json (Manifest)

```json
{
    "name": "Contact Form",
    "slug": "contact-form",
    "version": "1.0.0",
    "description": "Simple contact form with admin notifications.",
    "author": "Your Name",
    "provider": "Plugins\\ContactForm\\Providers\\ContactFormServiceProvider",
    "requires": {
        "php": "^8.2",
        "cms": "^1.0"
    },
    "permissions": [
        {"name": "contact-form.view", "description": "View submissions"},
        {"name": "contact-form.delete", "description": "Delete submissions"}
    ],
    "settings": {
        "label": "Contact Form",
        "icon": "mail",
        "fields": [
            {"key": "cf_recipient", "label": "Recipient Email", "type": "email", "default": ""},
            {"key": "cf_enabled", "label": "Enable Form", "type": "boolean", "default": true}
        ]
    },
    "schedule": [
        {"command": "contact-form:cleanup", "cron": "daily"}
    ]
}
```

---

## Service Provider

All plugins should extend `CmsPluginServiceProvider`:

```php
<?php

namespace Plugins\ContactForm\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use App\Services\SettingsRegistry;

class ContactFormServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'contact-form';

    // ✅ Routes, views, migrations, Livewire — all auto-discovered!
    // No need for loadRoutesFrom(), loadViewsFrom(), loadMigrationsFrom()

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title'      => 'Contact Form',
            'route'      => 'admin.contact-form',
            'url'        => route('admin.contact-form.index'),
            'icon'       => 'mail',
            'permission' => 'contact-form.view',
            'is_active'  => true,
            'source'     => 'plugin:contact-form',
            'children'   => [],
        ]);
    }

    protected function registerSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('plugin_contact-form', [
            'label'       => 'Contact Form',
            'icon'        => 'mail',
            'order'       => 200,
            'description' => 'Contact form plugin settings.',
            'fields'      => [
                ['key' => 'cf_recipient', 'label' => 'Recipient Email', 'type' => 'email', 'default' => '', 'rules' => ['nullable', 'email']],
                ['key' => 'cf_enabled', 'label' => 'Enable Form', 'type' => 'boolean', 'default' => true, 'rules' => ['boolean']],
            ],
        ]);
    }

    protected function registerContentTypes(\App\Services\ContentTypeRegistry $registry): void
    {
        $registry->register('products', [
            'slug'                  => 'products',
            'label'                 => 'Products',
            'singular'              => 'Product',
            'icon'                  => 'shopping_bag',
            'default_title_pattern' => '{title} {sep} {site}',
            'default_schema_type'   => 'Product',
        ]);
    }

    protected function registerTaxonomies(\App\Services\TaxonomyRegistry $registry): void
    {
        $registry->register('product_cat', [
            'slug'                  => 'product_cat',
            'label'                 => 'Product Categories',
            'singular'              => 'Product Category',
            'icon'                  => 'folder_open',
            'default_title_pattern' => '{term} Archives {sep} {site}',
            'default_schema_type'   => 'CollectionPage',
        ]);
    }
}
```

### What's Auto-Discovered

| Convention | What happens |
|-----------|-------------|
| `routes/web.php` | Loaded with `web` + `auth` middleware |
| `routes/api.php` | Loaded with `api` middleware |
| `resources/views/` | Registered as `{slug}::` namespace |
| `database/migrations/` | Included in `php artisan migrate` |
| `config/{slug}.php` | Merged into `config('{slug}')` |
| `src/Livewire/*.php` | Registered as `plugins.{slug}.component-name` |

### Livewire Auto-Discovery

Components in `src/Livewire/` are auto-discovered recursively:

```
src/Livewire/
├── SubmissionTable.php      → plugins.contact-form.submission-table
└── Admin/
    └── FormBuilder.php      → plugins.contact-form.admin.form-builder
```

To override component names, set `$livewireComponents` in your provider:

```php
protected array $livewireComponents = [
    'plugins.contact-form.submissions' => \Plugins\ContactForm\Livewire\SubmissionTable::class,
];
```

---

## Route Configuration

### ⚠️ CRITICAL: Always Include 'web' Middleware

All plugin routes **MUST** include the `'web'` middleware to prevent conflicts with the frontend catch-all route.

```php
<?php

use Illuminate\Support\Facades\Route;
use Plugins\ContactForm\Http\Controllers\ContactFormController;

// Admin Routes
Route::prefix(config('admin.path', 'admin'))
    ->name('admin.')
    ->middleware(['web', 'auth'])  // ✅ Always include 'web'!
    ->group(function () {

        Route::prefix('contact-form')
            ->name('contact-form.')
            ->middleware('permission:contact-form.view')
            ->group(function () {
                Route::get('/', [ContactFormController::class, 'index'])->name('index');
                Route::delete('/{id}', [ContactFormController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:contact-form.delete');
            });
    });
```

> **Why?** The frontend catch-all route `/{slug}` is in the `web` middleware group. Routes without `'web'` have lower priority, causing 302 redirects.

---

## Permission System

### Naming Convention: `{resource}.{action}`

```php
// ✅ Correct
'contact-form.view'
'contact-form.create'
'contact-form.delete'

// ❌ Wrong
'contact-form.contact-form.view'  // duplicated resource
'contactform.view'                // doesn't match slug
```

### Registering Permissions

Define in `plugin.json` (recommended) or create in a seeder:

```php
Permission::firstOrCreate(
    ['name' => 'contact-form.view'],
    [
        'module'      => 'contact-form',
        'action'      => 'view',
        'description' => 'View contact form submissions',
        'source'      => 'plugin:contact-form',
    ]
);
```

---

## Menu Registration

Always use the `registerMenuItems()` hook — never seed menus to the database.

```php
protected function registerMenuItems(RenderAdminMenu $event): void
{
    $event->addMenuItem([
        'title'      => 'Contact Form',
        'route'      => 'admin.contact-form.index',
        'url'        => route('admin.contact-form.index'),
        'icon'       => 'mail',           // Material Symbols icon
        'permission' => 'contact-form.view',
        'is_active'  => true,
        'source'     => 'plugin:contact-form',
        'children'   => [
            [
                'title'      => 'All Submissions',
                'route'      => 'admin.contact-form.index',
                'url'        => route('admin.contact-form.index'),
                'icon'       => 'list',
                'permission' => 'contact-form.view',
                'is_active'  => true,
                'source'     => 'plugin:contact-form',
                'children'   => [],
            ],
        ],
    ]);
}
```

**Icon system:** [Material Symbols](https://fonts.google.com/icons) — common: `dashboard`, `article`, `event`, `people`, `settings`, `mail`, `extension`.

---

## Settings Registration

Two options:

### Option A: In `plugin.json` (simple, no PHP)

```json
{
    "settings": {
        "label": "Contact Form",
        "icon": "mail",
        "fields": [
            {"key": "cf_email", "label": "Email", "type": "email", "default": ""}
        ]
    }
}
```

### Option B: In `registerSettings()` (full control)

```php
protected function registerSettings(SettingsRegistry $registry): void
{
    $registry->registerGroup('plugin_contact-form', [
        'label'  => 'Contact Form',
        'icon'   => 'mail',
        'order'  => 200,
        'fields' => [
            [
                'key'     => 'cf_email',
                'label'   => 'Recipient Email',
                'type'    => 'email',
                'default' => '',
                'rules'   => ['nullable', 'email'],
            ],
        ],
    ]);
}
```

---

## View Development

### Admin Views

All admin views **must** extend `layouts.admin`:

```blade
@extends('layouts.admin')

@section('title', 'Contact Form')
@section('page-title', 'Contact Form Submissions')

@section('content')
    {{-- Your content --}}
@endsection
```

| Section | Purpose |
|---------|---------|
| `title` | Browser tab title |
| `page-title` | Header text |
| `content` | Main content area |
| `hide-title` | Set `true` for full-width editor create/edit pages |

### Admin Index Page & UI Standards

All admin list/index pages (both core and plugin views) **must** follow these 5 UI standards for visual consistency:

1. **Header Action Button (`@section('page-actions')`)**:
   Primary page action buttons (e.g. `+ Add ...`, `+ Create ...`) **must** be placed in the header action slot using `@section('page-actions')` with `<x-admin.ui.button variant="primary">`.

2. **Filter Status Tabs with Counts (`getStatusCountsProperty()`)**:
   Top row filter tabs (`All`, `Active`, `Inactive`, `Published`, `Draft`, etc.) **must** include numeric count badges (`px-2 py-0.5 rounded-lg text-[10px] font-bold`) computed dynamically via Livewire `getStatusCountsProperty()`.

3. **Search Input (`<x-admin.ui.input>`) & Controls Placement**:
   - **Row 1**: Filter Status Tabs.
   - **Row 2 (Left)**: Search input using `<x-admin.ui.input>` (`dark:bg-[#1A1A1A]`), focus-within icon transition (`group-focus-within:text-[#2563EB]`), plus secondary filter dropdowns and a `Clear Filters` button when active.
   - **Row 2 (Right)**: Display rows dropdown (`Display: 10 Rows`).

4. **Shared Table Components (`<x-admin.ui.table>`)**:
   Tables **must** use shared UI components: `<x-admin.ui.table>`, `<x-slot:thead>`, `<x-admin.ui.table-header>`, `<x-admin.ui.table-row>`, and `<x-admin.ui.table-cell>`.

5. **Action Buttons & Tooltips (`data-tooltip`)**:
   All table column action buttons **must** specify `data-tooltip="..."` (e.g. `data-tooltip="Edit"`, `data-tooltip="Delete"`) and use consistent hover styling (`emerald` for view/manage, `blue` for edit, `red` for delete).

### Admin Create/Edit Views (Editor Standard Layout)

All admin create and edit pages (Pages, Forms, CPT, Taxonomies, Posts) **must** follow this standard 2-panel layout structure:

1. **Blade Sections & Layout Header**:
   - Use `@section('hide-title', true)` (do NOT use `hide-header`) so the Top Navbar Admin Global (Search, theme toggle, notifications, avatar) remains intact.

2. **Root Container & Context Bar**:
   - The root element **must** use `<div class="flex flex-col h-full bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC]">`.
   - Top element is a fixed Context Bar (`px-6 py-4 md:px-10 border-b border-gray-200 dark:border-[#272B30] bg-white/50 dark:bg-[#0B0B0B]/50 shrink-0`) containing a back button (`<-`), page title, and status badges.

3. **2-Panel Flex Layout Structure**:
   - Parent: `<div class="flex-1 flex overflow-hidden">`
   - **Left Panel (Main Editor)**: `<div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">` containing `<div class="max-w-4xl mx-auto space-y-8">`.
   - **Right Panel (Sidebar)**: `<aside class="w-[360px] bg-[#F4F5F6] dark:bg-[#0B0B0B] border-l border-gray-200 dark:border-[#272B30] overflow-y-auto no-scrollbar hidden lg:block shrink-0">` with inner wrapper `<div class="p-6 space-y-6">`.
   - *Crucial*: Ensure the Left Panel `<div>` is properly closed before starting `<aside>`.

4. **Sidebar Card `ACTIONS` (Top Card)**:
   - First card in the right sidebar **must** be an **Actions Card**: `<div class="rounded-2xl bg-white dark:bg-[#1A1A1A] p-5 border border-gray-200 dark:border-[#272B30]">` with a `rocket_launch` header icon.
   - Primary action button uses *Gradient Primary* styling (`bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20`).

5. **Toggle Switches**:
   - Toggle switch controls **must** be wrapped in `<label class="relative inline-flex items-center cursor-pointer">` to ensure the `after:absolute` toggle circle positions correctly within the track.

### Frontend Views

Reference views using your plugin's namespace:

```blade
@extends($activeTheme->slug . '::layouts.app')

@section('content')
    @include('contact-form::partials.form')
@endsection
```

---

## Permalink Slug Pattern

For content with URL slugs (posts, events, etc.), implement these Livewire methods:

```php
// Auto-generate slug from title
public function updatedTitle($value)
{
    if (!$this->manualSlug && !$this->isEdit) {
        $this->slug = $this->makeUniqueSlug(Str::slug($value));
    }
}

// Sanitize manual edits
public function updatedSlug($value)
{
    $this->slug = Str::slug($value);
    $this->manualSlug = true;
}

// Ensure uniqueness
protected function makeUniqueSlug(string $slug): string
{
    $original = $slug;
    $counter = 2;
    while (YourModel::where('slug', $slug)->where('id', '!=', $this->itemId ?? 0)->exists()) {
        $slug = $original . '-' . $counter++;
    }
    return $slug;
}
```

---

## Testing

```bash
# Check routes are registered
php artisan route:list --path=ctrlpanel/contact-form

# Clear caches after changes
php artisan optimize:clear

# Run full test suite
php artisan test
```

---

## Checklist

Before shipping your plugin:

- [ ] `plugin.json` has correct `slug`, `version`, and `provider`
- [ ] Service provider extends `CmsPluginServiceProvider`
- [ ] Routes include `'web'` middleware
- [ ] Permissions follow `{slug}.{action}` format
- [ ] Menu registered via `registerMenuItems()`
- [ ] Admin views extend `layouts.admin`
- [ ] All routes use `config('admin.path')` for prefix
- [ ] Migrations use `updateOrCreate` for idempotency
- [ ] README.md documents features and configuration

---

**Reference implementation:** See `plugins/posts/` for a working example.

**Last Updated:** 2026-07-20
**Version:** 2.0
