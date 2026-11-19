# CTI CMS — Architecture Overview (AI Skill)

> This document is an AI skill — a machine-readable guide that teaches AI agents
> how CTI CMS works. Read this BEFORE attempting any CMS operation.

## Core Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Framework | Laravel | 13.x |
| Frontend | Livewire + Alpine.js | 4.x + 3.x |
| CSS | Tailwind CSS | 4.x |
| Admin Panel | Custom Livewire (route prefix `/ctrlpanel/`) | — |
| Database | MySQL 8+ | — |
| Queue | Database driver (cron-based, shared hosting) | — |

## Directory Structure

```
app/
├── Http/Controllers/        ← Web + API controllers
├── Livewire/                ← Admin panel components
├── Models/                  ← Eloquent models (core only)
├── Services/                ← Business logic services
│   ├── Ai/                  ← AI resource registry
│   ├── Seo/                 ← SEO services (breadcrumbs, schema, sitemap)
│   └── Media/               ← Image processing pipeline
├── Traits/                  ← Reusable traits (HasTranslations, HasSeo, etc.)
├── Events/                  ← Event system (RenderAdminMenu, etc.)
├── Providers/               ← Service providers + CmsPluginServiceProvider
├── Mcp/                     ← MCP server, tools, resources, prompts
│   ├── Servers/
│   ├── Tools/
│   ├── Resources/
│   ├── Prompts/
│   └── Guards/
├── View/Components/         ← Blade components (<x-image>, <x-icon>, etc.)
│
themes/
├── {theme-slug}/
│   ├── theme.json           ← Theme manifest
│   ├── views/               ← Blade templates
│   │   ├── layouts/         ← Base layouts
│   │   ├── pages/           ← Page templates (home.blade.php, about.blade.php)
│   │   ├── partials/        ← Reusable sections
│   │   ├── single-{cpt}.blade.php  ← CPT single templates
│   │   └── archive-{cpt}.blade.php ← CPT archive templates
│   └── assets/              ← Static assets (CSS, JS, fonts, icons)
│
plugins/
├── {plugin-slug}/
│   ├── plugin.json          ← Plugin manifest
│   ├── Providers/           ← ServiceProvider extending CmsPluginServiceProvider
│   ├── Models/              ← Plugin models
│   ├── Livewire/            ← Admin components
│   └── resources/views/     ← Plugin views
│
docs/
├── theme-development.md     ← Theme dev guidelines
├── plugin-development.md    ← Plugin dev guidelines
└── ai-skills/               ← AI instruction files (MCP Resources)
```

## Key Models & Relationships

### Page
- Fields: `title`, `slug`, `template`, `status`, `menu_order`, `parent_id`
- Has many `PageBlock` (via `page_blocks` table)
- Has one `SeoMeta` (polymorphic)
- Uses `HasTranslations` trait for `title`, `meta_description`
- Templates defined in `theme.json` → `page_templates`

### PageBlock
- Fields: `page_id`, `key`, `type`, `value`, `sort_order`
- `key` matches block definition in `theme.json`
- `value` is JSON — stores content for each block
- `type`: `text`, `textarea`, `image`, `gallery`, `wysiwyg`, `repeater`, etc.

### CustomPostType (CPT)
- Fields: `name`, `slug`, `description`, `is_active`, `has_archive`
- Has many `CptMetaField` — dynamic field definitions
- Has many `CptEntry` — actual content entries
- Has many `Taxonomy` — category/tag systems

### CptEntry
- Fields: `cpt_id`, `title`, `slug`, `status`, `meta` (JSON), `translations` (JSON)
- `meta` column: `{"field_key": "value", ...}` — dynamic data
- `translations` column: `{"en": {"title": "...", "field_key": "..."}, "id": {...}}`
- Uses `HasTranslations` trait

### CptMetaField
- Fields: `cpt_id`, `key`, `label`, `type`, `options`, `is_required`, `sort_order`
- Types: `text`, `textarea`, `wysiwyg`, `image`, `gallery`, `select`, `checkbox`, `number`, `date`, `url`, `email`, `color`, `icon`, `repeater`

### Media
- Fields: `filename`, `path`, `alt_text`, `mime_type`, `size`, `width`, `height`, `disk`, `variants` (JSON)
- Variants: `{"sm": "uploads/sm/file.webp", "md": "...", "lg": "...", "xl": "...", "thumb": "...", "lqip": "data:image/..."}`

### Taxonomy & TaxonomyTerm
- Taxonomy: `name`, `slug`, `cpt_id`, `is_hierarchical`
- TaxonomyTerm: `taxonomy_id`, `name`, `slug`, `parent_id`, `sort_order`

### ApiToken
- Fields: `tokenable_type`, `tokenable_id`, `name`, `token_hash`, `prefix`, `abilities` (JSON), `allowed_ips` (JSON), `rate_limit_per_minute`, `expires_at`
- Polymorphic owner (usually `User`)
- MCP abilities: `mcp.connect`, `mcp.read`, `mcp.write`, `mcp.delete`, `mcp.admin`, `mcp.theme.read`, `mcp.theme.write`, `mcp.media.upload`, `mcp.content.publish`

## Translation System (Dual)

### 1. Model-Level Translations (`HasTranslations` trait)
For translatable model fields (titles, descriptions, meta values).
```php
$entry->getTranslation('title', 'id');  // Get Indonesian title
$entry->setTranslation('title', 'id', 'Judul Baru');
```
Stored in `translations` JSON column on the model.

### 2. String-Level Translations (`t()` helper)
For UI strings in Blade templates — buttons, labels, headings.
```php
t('common.read_more', 'Read More');  // Key + English fallback
t('cdt.hero_title', 'Welcome to CDT');
```
Stored in `string_translation_keys` + `string_translations` tables.
Admin manages at `/ctrlpanel/settings/string-translations`.

## Asset System

### Theme Assets
```blade
{{ theme_asset('assets/css/style.css') }}  {{-- Static theme files --}}
```

### Content Images (from Media Library)
```blade
{{ resolve_block_asset($path, 'lg') }}     {{-- Resolves variant WebP --}}
<x-image :src="$path" sizes="100vw" />     {{-- MANDATORY for user uploads --}}
```

### Rules
- ❌ NEVER use `asset('storage/')` — use `resolve_block_asset()`
- ❌ NEVER use raw `<img>` for user-uploaded images — use `<x-image>`
- ❌ NEVER use external CDN URLs — download and serve locally
- ✅ Use `<x-image>` for ALL user-uploaded content images
- ✅ Use `theme_asset()` for static theme files

## Routing Structure

| Route Pattern | Purpose |
|---------------|---------|
| `GET /` | Homepage |
| `GET /{page-slug}` | Static pages |
| `GET /{cpt-slug}` | CPT archive (if `has_archive`) |
| `GET /{entry-slug}` | CPT single entry |
| `GET /ctrlpanel/*` | Admin panel (Livewire) |
| `GET /api/v1/*` | REST API |
| `POST /mcp/cms` | MCP Server endpoint |

## Core vs Theme vs Plugin Boundary

| Layer | What Goes Here | Examples |
|-------|---------------|----------|
| **Core** (`app/`) | Generic CMS functionality | Page CRUD, CPT engine, Media, SEO, Auth |
| **Theme** (`themes/`) | Client-specific templates + assets | CDT theme views, OFIS theme views |
| **Plugin** (`plugins/`) | Domain-specific features | Blog/Posts, YouTube integration |

**NEVER** add client-specific code to core. **NEVER** add plugin logic to themes.
