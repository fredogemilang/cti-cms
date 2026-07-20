# Web CMS

A modular, extensible CMS built with Laravel 12. Designed for distribution — create plugins and themes to customize for any project.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)

---

## Quick Start

```bash
# 1. Clone
git clone https://github.com/fredogemilang/web-cms.git my-site
cd my-site

# 2. Install dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Create your MySQL database, then:
php artisan cms:install
```

The installer wizard will guide you through: database migration, core data seeding, admin user creation, default theme activation, and asset publishing.

After install, run `php artisan serve` and visit `http://localhost:8000`.

---

## Features

### Core
- **Page Builder** — create pages with 15+ block types (text, wysiwyg, media, gallery, repeater, etc.)
- **Media Library** — centralized file/image management with variants
- **Form Builder** — drag-and-drop forms with entries, notifications, honeypot, CAPTCHA
- **Dynamic Menus** — manage navigation via admin
- **Role-Based Access** — granular permissions per module
- **Email Templates** — editable system emails with variable substitution
- **SEO Meta** — per-page title, description, OG tags
- **Activity Log** — audit trail for all admin actions
- **Redirects** — regex-capable redirect manager
- **Webhooks** — event-driven HTTP callbacks
- **API Tokens** — scoped API authentication
- **i18n** — translatable pages and content blocks

### Plugin System
- Convention-based auto-discovery (routes, views, migrations, Livewire)
- Manifest-driven settings and scheduled tasks
- Dependency validation between plugins
- Upload & activate via admin panel

### Theme System
- Blade-based with view namespace isolation
- Page template definitions in `theme.json`
- Asset publishing via `theme:publish` command
- Multiple themes, one active at a time

---

## Architecture

```
web-cms/
├── app/
│   ├── Console/Commands/       # Artisan commands (cms:install, make:plugin, etc.)
│   ├── Http/Controllers/       # Core controllers
│   ├── Models/                 # Eloquent models
│   ├── Providers/              # Service providers
│   │   ├── CmsPluginServiceProvider.php  ← base class for plugins
│   │   ├── CmsEventServiceProvider.php
│   │   └── CmsSettingsServiceProvider.php
│   └── Services/               # PluginLoader, ThemeLoader, SettingsRegistry
├── config/
│   ├── cms.php                 # CMS version, admin path, feature toggles
│   └── admin.php               # Admin panel config
├── plugins/                    # Plugin directory
│   └── posts/                  # Example: Blog plugin
├── themes/                     # Theme directory
│   ├── default/                # Ships with CMS
│   └── iccom/                  # Example: client theme
├── database/
│   ├── migrations/             # Core migrations (42 files)
│   └── seeders/                # Core seeders (roles, permissions, menus)
└── docs/                       # Developer guides
```

---

## Artisan Commands

| Command | Description |
|---------|-------------|
| `php artisan cms:install` | Interactive installation wizard |
| `php artisan make:plugin {slug}` | Scaffold a new plugin |
| `php artisan make:theme {slug}` | Scaffold a new theme |
| `php artisan theme:publish {slug}` | Publish theme assets to `public/` |
| `php artisan theme:publish --all` | Publish all theme assets |

---

## Creating a Plugin

### Scaffold

```bash
php artisan make:plugin contact-form
```

This generates:

```
plugins/contact-form/
├── plugin.json
├── src/Providers/ContactFormServiceProvider.php
├── routes/web.php
├── resources/views/
├── database/migrations/
└── README.md
```

### Service Provider

Plugins extend `CmsPluginServiceProvider` — just set `$pluginSlug` and everything auto-loads:

```php
class ContactFormServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'contact-form';

    // Routes, views, migrations, Livewire components — all auto-discovered ✨

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title'      => 'Contact Form',
            'route'      => 'admin.contact-form.index',
            'url'        => route('admin.contact-form.index'),
            'icon'       => 'mail',
            'permission' => 'contact-form.view',
            'is_active'  => true,
            'source'     => 'plugin:contact-form',
            'children'   => [],
        ]);
    }
}
```

### Auto-Discovery Conventions

| Directory | Auto-loaded as |
|-----------|---------------|
| `routes/web.php` | Web routes with `web` + `auth` middleware |
| `routes/api.php` | API routes with `api` middleware |
| `resources/views/` | Blade views as `{slug}::` namespace |
| `database/migrations/` | Database migrations |
| `config/{slug}.php` | Config file |
| `src/Livewire/` | Livewire components as `plugins.{slug}.*` |

### Manifest Settings (plugin.json)

Declare settings and schedules without PHP code:

```json
{
    "name": "Contact Form",
    "slug": "contact-form",
    "version": "1.0.0",
    "settings": {
        "label": "Contact Form",
        "icon": "mail",
        "fields": [
            {"key": "cf_email", "label": "Recipient Email", "type": "email", "default": ""}
        ]
    },
    "schedule": [
        {"command": "contact-form:cleanup", "cron": "daily"}
    ]
}
```

> For the full plugin development guide, see [docs/plugin-development.md](docs/plugin-development.md).

---

## Creating a Theme

### Scaffold

```bash
php artisan make:theme starter --author="Your Name"
```

### Theme Structure

```
themes/starter/
├── theme.json                  # Manifest
├── assets/css/theme.css        # Stylesheets
├── views/
│   ├── layouts/app.blade.php   # Base HTML
│   ├── pages/
│   │   ├── home.blade.php      # Homepage
│   │   └── single.blade.php    # Default page
│   └── partials/
│       ├── header.blade.php
│       └── footer.blade.php
```

### Available Template Variables

| Variable | Type | Description |
|----------|------|-------------|
| `$activeTheme` | `Theme` | Active theme model (always available) |
| `$page` | `Page` | Current page with blocks |
| `$blocks` | `Collection` | Page blocks (on single pages) |
| `$testimonials` | `Collection` | CPT entries (homepage) |

### Referencing Assets

```blade
<link rel="stylesheet" href="{{ asset('themes/starter/assets/css/theme.css') }}">
```

After editing theme CSS/JS, publish to public:

```bash
php artisan theme:publish starter
```

> For the full theme development guide, see [docs/theme-development.md](docs/theme-development.md).

---

## Configuration

### config/cms.php

```php
return [
    'version'   => '1.0.0',
    'path'      => env('ADMIN_PATH', 'ctrlpanel'),
    'installed' => file_exists(storage_path('cms_installed')),
    'features'  => [
        'pages'           => true,
        'forms'           => true,
        'media'           => true,
        'menus'           => true,
        'api'             => true,
        'webhooks'        => true,
        'email_templates' => true,
        'activity_log'    => true,
    ],
];
```

### Environment Variables

Key variables in `.env`:

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | `My CMS` | Site name |
| `ADMIN_PATH` | `ctrlpanel` | Admin panel URL prefix |
| `DB_DATABASE` | `web_cms` | Database name |
| `MAIL_MAILER` | `log` | Mail driver (`log`, `smtp`, `brevo`) |

---

## Production Deployment

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false

# 2. Install (no dev dependencies)
composer install --optimize-autoloader --no-dev

# 3. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Setup cron (required for scheduled tasks)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# 5. Start queue worker (required for async mail/jobs)
php artisan queue:work --daemon
```

### Production Checklist

- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] `APP_URL` set to your domain with HTTPS
- [ ] Strong `DB_PASSWORD`
- [ ] `SESSION_DRIVER=redis` or `database`
- [ ] `CACHE_STORE=redis` (recommended)
- [ ] Supervisor running `queue:work`
- [ ] Cron running `schedule:run` every minute
- [ ] `php artisan storage:link` executed
- [ ] HTTPS enforced at web server level
- [ ] `.env` file not readable by web (chmod 600)

---

## Testing

```bash
php artisan test
```

Current: **53 tests, 107 assertions** covering:
- Plugin dependency validation
- Form submission flow
- Page translations
- Redirect middleware
- Settings encryption
- Activity logging

---

## Requirements

- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2.x
- Node.js 18+ (for Vite, optional)

---

## License

MIT License. See [LICENSE](LICENSE) for details.
