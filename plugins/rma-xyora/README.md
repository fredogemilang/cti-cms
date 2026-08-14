# RmaXyora Plugin

> A plugin for the Web CMS.

## Installation

1. Copy this directory to `plugins/rma-xyora/`
2. Go to **Admin → Plugins** and activate **RmaXyora**
3. Run `php artisan migrate` to create database tables

## Directory Structure

```
rma-xyora/
├── plugin.json              # Plugin manifest
├── src/
│   ├── Providers/           # Service provider
│   ├── Livewire/            # Livewire components (auto-discovered)
│   └── Models/              # Eloquent models
├── routes/
│   └── web.php              # Web routes (auto-loaded)
├── resources/
│   └── views/               # Blade views (namespace: 'rma-xyora::')
├── database/
│   └── migrations/          # Migrations (auto-loaded)
└── README.md
```

## Development

This plugin extends `CmsPluginServiceProvider` which auto-loads:
- **Routes** from `routes/web.php` and `routes/api.php`
- **Views** from `resources/views/` (accessible as `rma-xyora::view.name`)
- **Migrations** from `database/migrations/`
- **Livewire** components from `src/Livewire/` (registered as `plugins.rma-xyora.*`)

Override hook methods in the ServiceProvider for:
- `registerMenuItems()` — admin sidebar entries
- `registerSettings()` — settings page fields
- `registerScheduledTasks()` — cron jobs
