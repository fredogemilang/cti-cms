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
| `hide-header` | Set `true` for create/edit pages |

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
