# GoogleSiteKit Plugin

> A plugin for the Web CMS.

## Installation

1. Copy this directory to `plugins/google-site-kit/`
2. Go to **Admin → Plugins** and activate **GoogleSiteKit**
3. Run `php artisan migrate` to create database tables

## Directory Structure

```
google-site-kit/
├── plugin.json              # Plugin manifest
├── src/
│   ├── Providers/           # Service provider
│   ├── Livewire/            # Livewire components (auto-discovered)
│   └── Models/              # Eloquent models
├── routes/
│   └── web.php              # Web routes (auto-loaded)
├── resources/
│   └── views/               # Blade views (namespace: 'google-site-kit::')
├── database/
│   └── migrations/          # Migrations (auto-loaded)
└── README.md
```

## Development

This plugin extends `CmsPluginServiceProvider` which auto-loads:
- **Routes** from `routes/web.php` and `routes/api.php`
- **Views** from `resources/views/` (accessible as `google-site-kit::view.name`)
- **Migrations** from `database/migrations/`
- **Livewire** components from `src/Livewire/` (registered as `plugins.google-site-kit.*`)

Override hook methods in the ServiceProvider for:
- `registerMenuItems()` — admin sidebar entries
- `registerSettings()` — settings page fields
- `registerScheduledTasks()` — cron jobs
