# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Quick Reference

| Task | Command |
|------|---------|
| Dev server (all services) | `composer run dev` |
| Run all tests | `composer run test` |
| Run a single test | `php artisan test --filter=ClassName` |
| Run a specific test method | `php artisan test --filter=testMethodName` |
| Build frontend | `npm run build` |
| Laravel commands | `php artisan` (standard) |
| List routes | `php artisan route:list` |
| Clear all caches | `php artisan optimize:clear` |
| Lint (Pint) | `./vendor/bin/pint` |
| Run queue worker | `php artisan queue:work` |
| Tinker REPL | `php artisan tinker` |

## Tech Stack

- **PHP 8.2+** / **Laravel 13.x** (Laravel 12 framework)
- **Livewire 4.0** — all admin views are Livewire full-page components
- **Tailwind CSS 4** (via Vite plugin) — dark mode via `.dark` class selector
- **TipTap 3.x** — rich text editor (see `resources/js/app.js`)
- **Vite 7** — asset bundling; includes Tailwind, auto-discovers theme assets
- **SQLite** (local dev, `database/database.sqlite`) or **MySQL** (production)

## Project Structure & Architecture

```
plugins/                  # ⚠️ THE CORE EXTENSIBILITY LAYER — most domain logic lives here
  events/                 # Events management, registration, doorprize
  membership/             # Member registration & approval
  posts/                  # Blog/news with categories/tags
  article-submission/     # Public article uploads
app/
  Livewire/Admin/         # Core admin Livewire components
  Models/                 # Core Eloquent models (User, Page, Media, etc.)
  Services/               # Singletons: PluginLoader, ThemeLoader, SettingsRegistry, etc.
  Traits/                 # HasTranslations, HasSeoMeta, HasRoles, FindsByLocalizedSlug
  Providers/              # App, Auth, Plugin, Theme, BrevoMail service providers
themes/                   # Blade-based themes (iccom/ active)
```

### Plugin System

Plugins are the primary organizational pattern. Each plugin is a self-contained package at `plugins/{slug}/`:

```
plugins/{slug}/
  plugin.json             # {name, slug, version, provider} — provider class is the entry point
  routes/web.php          # MUST include 'web' middleware (see docs/plugin-development.md)
  src/
    Providers/            # ServiceProvider — boot loads routes/views/migrations, registers menu via event
    Http/Controllers/     # Plugin controllers
    Livewire/             # Plugin Livewire components
    Models/               # Plugin Eloquent models
  database/migrations/    # Migrations auto-loaded by PluginLoader
  resources/views/        # Views namespace = plugin slug
```

- **PluginLoader** (`app/Services/PluginLoader.php`): Boots active plugins from DB, auto-registers PSR-4 namespace `Plugins\{PascalCaseSlug}\`, validates routes include `web` middleware.
- **Activation**: Plugins are stored in the `plugins` table. Activation/deactivation managed via admin UI.
- **Menu registration**: Use the `RenderAdminMenu` event — never seed menus to DB.
- **Permission naming**: `{resource}.{action}` format (e.g. `events.view`, `posts.create`).

### Theme System

Themes live in `themes/{slug}/` with a `theme.json` manifest. On activation, assets are published to `public/themes/{slug}/`. Views are referenced by namespace: `themename::view.path`. The `theme_view()`, `theme_asset()`, and `active_theme()` helpers resolve the active theme. See `docs/theme-development.md`.

### Admin Panel

- **Path**: Configurable via `ADMIN_PATH` env var (default `admin`), accessible at `config('admin.path')`.
- **UI**: Livewire full-page components with dark-mode-ready design (`dark:bg-[#1A1A1A]`). All admin index views MUST adhere to the 5 standard UI rules (Header `@section('page-actions')`, filter status tabs with `getStatusCountsProperty()` count badges, search via `<x-admin.ui.input>`, shared `<x-admin.ui.table>`, and `data-tooltip` action buttons — see `docs/plugin-development.md#admin-index-page--ui-standards`).
- **Auth**: Custom `AuthController` with 2FA support (`EnforceTwoFactor` middleware). Optional enforced 2FA per role.
- **RBAC**: Users → Roles → Permissions. `CheckPermission` middleware gates all admin routes.

### Content Architecture

- **Pages**: Row-per-page with `PageBlock` children (block-based builder). Slug is the URL path.
- **Custom Post Types (CPT)**: Dynamic content types with entries, taxonomies, and meta fields.
- **Forms**: Builder-based forms with conditional logic, entries, CSV export.
- **Catch-all route**: `/{slug}` is the *last* registered route (in `PluginServiceProvider::booted`) — plugin routes take precedence.

### Translation Pattern (`HasTranslations` trait)

The default locale (e.g. `id`) is stored in the model's primary columns (`title`, `slug`, etc.). Translations for other locales go into a `translations` JSON column: `{"en": {"title": "About"}, "ja": {...}}`. This keeps default-locale queries unchanged. Use `$model->getTranslation('field', 'en')` or the `translate()` helper.

### Settings

`SettingsRegistry` defines groups of key-value pairs with field types, validation rules, and sections. The `setting()` helper reads from the `settings` table with in-memory cache. Admin UI renders settings at `/admin/settings/{group}` via `SettingsPage` Livewire component.

### Scheduled Tasks

- `events:complete-expired` — marks past events as completed (daily 00:01)
- `activity:prune` — prunes old audit log entries
- `content:purge-trash` — removes expired trashed content
- `content:publish-scheduled` — publishes scheduled content
- `media:optimize` — backfill WebP conversions

Setup cron: `* * * * * php artisan schedule:run`

## Key Patterns

- **Livewire components** are full-page components in admin. Use `#[Layout]` attribute or extend `layouts.admin`.
- **Slug UI**: Always use the click-to-edit inline badge pattern (see `docs/plugin-development.md#permalink-slug-pattern`).
- **Permission middleware**: `permission:{resource}.{action}` — checks the `permissions` table.
- **Media picker**: `TiptapMediaPicker` Livewire component bridges TipTap editor and media library.
- **SEO**: Per-model `HasSeoMeta` trait stores SEO metadata. `SeoRenderer` service outputs meta tags.
- **Page cache**: `PageCache` middleware caches anonymous GET responses; auto-purged on Page/CPT save.
- **Audit log**: `ActivityLogger` service with `activity()` helper. Model observers track CRUD.

## API

REST API v1 at `/api/v1/`. Auth via token (create/revoke in admin → API Tokens). Public endpoints: pages, CPT entries, media, form submissions. See `routes/api.php`.

## Queue

Uses `database` queue driver by default. Run with:
```bash
php artisan queue:work       # for production (under Supervisor)
php artisan queue:listen     # for development (included in `composer run dev`)
```

## Known Bugs & Watch-Outs (Fixed & Standardized)

### Repeater Fields: Canonical `repeater_fields` Key
- **Official Standard**: The official key for repeater sub-fields in the `options` JSON column is strictly **`repeater_fields`**.
- **Strict Validation & Clean Rejection**: All legacy keys (such as `sub_fields`) or unsupported keys (such as `anak_fields`, `items`) are **strictly rejected with HTTP 422 Unprocessable Entity**.

### Repeater Key Binding: `name` vs `id`
API data uses `name` as the key for repeater sub-fields. Always use: `$subField['name'] ?? $subField['id'] ?? Str::snake(...)`.

### CPT MetaBoxes & Field Groups
All CPT MetaFields default to `field_group = 'general'`. `CptForm` automatically creates and ensures a `'general'` MetaBox exists when loading or saving CPT schemas.

### Media Upload: `uploaded_by` Column
`MediaService::upload()` hardcodes `auth()->id()` which can be null for API token auth. Fixed to fallback: `$metadata['uploaded_by'] ?? auth()->id() ?? 1`.

### Icon Library System
- **Lucide Icons**: 2,007 official Lucide SVG icons loaded via `resources/icons/lucide.json`.
- **Field Type**: `'icon'` registered in `MetaField::$fieldTypes` and `PageBlock::$blockTypes`.
- **Rendering**: `<x-icon name="lucide:shield" class="w-5 h-5 text-blue-500" />` or `render_icon($name, $class)` helper.
- **Admin Settings**: Icon libraries managed at `/ctrlpanel/settings/icons` (`IconLibrariesSettings.php`).

## Plugins: Reference Implementations

- **Posts** (`plugins/posts`) — complete example with Livewire CRUD, categories, tags, slug generation, permissions
- **Events** (`plugins/events`) — complex plugin with wizard, registration flow, doorprize console, custom questions
- **Membership** (`plugins/membership`) — simpler plugin with approval workflow
