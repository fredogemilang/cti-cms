# CLAUDE.md — CTI CMS

> **CTI CMS** is a reusable, modular Laravel CMS core. Deployed as **CDT** at `cdt.devs`.
> Core = generic (`app/`, `database/`). Theme = client-specific (`themes/cdt/`). Plugin = domain extensions.

## Quick Reference

| Task | Command |
|------|---------|
| Dev server (all services) | `composer run dev` |
| Run all tests | `composer run test` |
| Run single test | `php artisan test --filter=ClassName` |
| Build frontend | `npm run build` |
| Clear all caches | `php artisan optimize:clear` |
| Lint | `./vendor/bin/pint` |
| Queue worker | `php artisan queue:work` |
| Tinker | `php artisan tinker` |
| Publish theme assets | `php artisan theme:publish cdt` |

## Tech Stack

- **PHP 8.2+** / **Laravel 13.x**
- **Livewire 4.0** — all admin views are full-page Livewire components
- **Alpine.js** — lightweight reactivity in Blade views
- **Tailwind CSS 4** — via Vite plugin, dark mode via `.dark` class
- **TipTap 3.x** — rich text editor
- **Vite 7** — asset bundling, auto-discovers theme assets
- **SQLite** (dev) / **MySQL** (production)
- **spatie/laravel-permission** — RBAC

## ⚠️ Core vs Theme Boundary (CRITICAL)

```
app/              ← Generic CMS features ANY site would need
themes/cdt/       ← CDT-specific views, partials, URL overrides
plugins/          ← Domain extensions (posts, google-site-kit, etc.)
```

**When unsure whether something belongs in core or theme → discuss before implementing.**

## 5 Rules for AI Agents

1. **ALWAYS use `resolve_block_asset($path)`** — never `asset('storage/' . $v)`. See docs/gotchas.md#media
2. **NEVER hardcode content in templates** — all from database via `$entry->meta`, `setting()`, `t()`
3. **Scan ALL pages/vendors** — don't assume a bug is isolated to one item
4. **EN and ID must stay in sync** — verify after every data edit
5. **Core vs Theme boundary** — generic in `app/`, project-specific in `themes/{project}/`

## Key Conventions

1. **Translations**: JSON `translations` column on Page, PageBlock, CptEntry, FormField. Trait: `HasTranslations`. Default locale in main columns, others in `translations.{locale}.{field}`.
2. **Assets**: `resolve_block_asset($path, 'sm')` resolves to correct public path + variant WebP. Falls back to original if variant missing.
3. **Settings**: `setting('key', default)` helper — reads from `settings` table via in-memory cache.
4. **Forms**: Created via admin UI, assigned to theme placeholders via `theme_{slug}_form_assignments`. Rendered with `tailwind-form.blade.php`.
5. **SEO**: `SeoMeta` polymorphic per-locale. `InjectSeoTags` middleware auto-injects. Always use `<x-seo-breadcrumbs :entity="$entry" />`.
6. **Localization**: URL prefix `/id/path`. `SetLocale` middleware. `t('group.key', 'Default')` for string translations.
7. **Page Builder**: `Page` → `PageBlock` with `translations` JSON. Repeaters store rows in `value` (JSON).
8. **CPT**: `CustomPostType` → `CptEntry` + `MetaField`. Entry meta in `meta` JSON. Translations in `meta._translations.{locale}`.
9. **Debug**: Use `scratch/` dir for temp PHP scripts. Bootstrap: `require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php';`
10. **SEO Headings**: Strict `h1 > h2 > h3`. No heading tags in nav/footer — use `<span>`. Exactly one `<h1>` per page.
11. **Admin Menu**: Registered via `AdminMenuBuilder` + `RenderAdminMenu` event. Never hardcode sidebar.
12. **Themes**: `theme.json` defines templates, blocks, form_placeholders, menu_locations. Published with `theme:publish`.
13. **Redirects**: `Redirect` model + `HandleRedirects` middleware (prepended, pre-route).
14. **404 Logging**: `NotFoundLog` + `Log404` middleware (terminable). Throttled 5min, static assets skipped, auto-prune 90 days.
15. **Sitemap**: On-the-fly with per-type cache. Multi-locale with `<xhtml:link>` alternates. Respects filter hooks.
16. **Plugins**: Check `is_plugin_active('posts')` before using plugin models. Blog ALWAYS uses Posts plugin, never CPT.

## Project Structure

```
app/
├── Livewire/Admin/         ← Core admin Livewire components
├── Models/                 ← 36 Eloquent models
├── Services/               ← PluginLoader, ThemeLoader, SettingsRegistry, etc.
├── Traits/                 ← HasTranslations, HasSeoMeta, HasRoles
├── Providers/              ← App, Auth, Plugin, Theme service providers
├── Http/Middleware/        ← 13 middleware layers
└── Support/                ← Filter, helpers
plugins/
├── posts/                  ← Blog with categories/tags
└── google-site-kit/        ← Analytics integration
themes/cdt/                 ← Active theme
├── theme.json              ← Templates, blocks, form_placeholders
├── views/                  ← Blade templates + partials
└── assets/                 ← Published to public/themes/cdt/
```

## Middleware Stack (order matters)

| # | Middleware | Role |
|---|-----------|------|
| 0 | `HandleRedirects` | 301/302 before route matching |
| 1 | `SetLocale` | Locale from URL/query/session/cookie |
| 2 | `InjectSeoTags` | Meta tags, OG, JSON-LD in `<head>` |
| 3 | `OptimizeHtml` | Minify HTML |
| 4 | `CompressResponse` | Gzip/Brotli |
| 5 | `SecurityHeaders` | CSP, X-Frame, HSTS |
| 6 | `PageCache` | Full-page cache for static pages |
| 7 | `Log404` | Passive 404 logging (terminable) |

## Key Models (36 total)

| Model | Table | Purpose |
|-------|-------|---------|
| `Page` | `pages` | Static pages with PageBuilder |
| `PageBlock` | `page_blocks` | Block-based builder, translations JSON |
| `CustomPostType` | `custom_post_types` | CPT schema definition |
| `CptEntry` | `cpt_entries` | CPT content, meta JSON |
| `MetaField` | `meta_fields` | CPT field schema |
| `Form` | `forms` | Dynamic forms (SoftDeletes) |
| `FormField` | `form_fields` | Form field definitions |
| `FormEntry` | `form_entries` | Form submissions |
| `Media` | `media` | Uploads, WebP variants |
| `SeoMeta` | `seo_metas` | Per-locale SEO (polymorphic) |
| `Redirect` | `redirects` | 301/302 rules |
| `NotFoundLog` | `not_found_logs` | 404 tracking |
| `Setting` | `settings` | Key-value config |
| `User` | `users` | spatie RBAC |
| `MenuItem` | `menu_items` | Navigation hierarchy |

## Translation Systems

Three independent systems that work together:

1. **Model-Level** (`HasTranslations` trait): Default locale in main columns, others in `translations` JSON
2. **String Registry** (`t()` helper): `t('group.key', 'Default')` — scanned and managed in admin
3. **CPT Meta** (`meta._translations.{locale}`): Custom field translations within entry meta JSON

## API

REST API v1 at `/api/v1/`. 98 endpoints total — see `docs/api-reference.md`.
- Admin endpoints require Bearer token
- Public endpoints: pages, CPT entries, menus, forms, posts, settings
- Plugin auto-discovery: `plugins/{slug}/routes/api.php`

## Critical Gotchas (see `docs/gotchas.md` for full list)

| # | Issue | Fix |
|---|-------|-----|
| G12 | Controller param binding in localized routes | Signature must accept `$localeOrSlug, $slug` |
| G6 | PageBlocks unique constraint too restrictive | Dropped DB constraint, app-level validation |
| G24 | Image variants need queue worker | `QUEUE_CONNECTION=database` requires active worker |
| G25 | `resolve_block_asset($path, 'sm')` fallback | Falls back to original (2-3MB) if variant missing |
| G34 | Polylang import slug conflicts | Use `findExistingPolylangEntry()` + merge translations |
| G45 | `.gitignore` blocking theme files on project branches | Remove `/themes/{name}` from `.gitignore` on project branches |

## Scheduled Tasks

```
* * * * * php artisan schedule:run
```
- `events:complete-expired` (daily 00:01)
- `activity:prune` (daily 03:00, 90d)
- `content:purge-trash` (daily 02:30, 30d)
- `content:publish-scheduled` (every minute)
- `media:optimize` (backfill WebP)

## Documentation

| File | Content |
|------|---------|
| `docs/gotchas.md` | All 45 gotchas & lessons learned |
| `docs/architecture/` | Per-subsystem architecture deep dives |
| `docs/api-reference.md` | 98 REST API endpoints |
| `docs/theme-development.md` | Theme creation guide |
| `docs/plugin-development.md` | Plugin creation guide |
| `docs/sidebar-menu-system.md` | Admin menu system |
