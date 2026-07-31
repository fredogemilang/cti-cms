# Project Agent Guidelines

## Theme & Content Architecture: CPT vs. Repeater Field Selection

When scaffolding custom content, building themes, or designing API integration scripts for pages:

### Decision Matrix
1. **Use Custom Post Type (CPT)** if:
   - Content items are global entities reused across multiple pages or archives (e.g., *Products, Case Studies, Job Openings, Testimonials*).
   - Each item requires its own dedicated detail URL (e.g., `/products/cloud-security`).
   - Items require taxonomy filtering, global search, or sitewide relationship bindings.

2. **Use Repeater Block Field (`theme.json`)** if:
   - Content items are **repeatable UI elements bound to a single page layout** (e.g., *Area of Expertise cards on Homepage*, *FAQ accordions on About page*, *Pricing cards on Landing page*, *Feature highlights*).
   - Cards or columns might expand or change order in the future, but items do **NOT** need standalone detail URLs or sitewide archives.
   - Non-technical admins need a simple add/remove/reorder interface in the Page Block Builder to manage columns without creating separate CPT entries.

For full implementation patterns, see [docs/theme-development.md](file:///c:/laragon/www/cdt/backend/docs/theme-development.md#cpt-vs-repeater-field-selection-architecture).

---

## Media Assets & Image URL Handling Rules (DOs and DONTs)

When populating media/image fields via API, seeders, theme defaults, or rendering image blocks in Blade templates and Admin Livewire components:

### 🚫 DONTs (Common Pitfalls & Anti-Patterns)
1. **NEVER hardcode `asset('storage/' . $value)` in Blade views or Admin components**:
   - ❌ Incorrect: `<img src="{{ asset('storage/' . $image) }}">`
   - **Why**: If `$image` is a theme asset (e.g. `themes/cdt/assets/security.webp`), hardcoding `'storage/'` prepends storage and creates broken URLs like `/storage/themes/cdt/assets/security.webp` which fail with **403 Forbidden** errors.
2. **NEVER store full domain URLs in database blocks via API/seeders**:
   - ❌ Incorrect: `"image": "http://cdt.devs/storage/uploads/photo.jpg"`
   - **Why**: Hardcoded domain names break across environments (Local `cdt.devs` vs Staging vs Production).
3. **NEVER manually manipulate theme config cache in production**:
   - ❌ Incorrect: Assuming `theme.json` edits take effect immediately without clearing cache.
   - **Why**: Theme config is cached for 1 hour (`3600s`). Use `php artisan cache:clear` or ensure `app.debug = true` during development.

---

### ✅ DOs (Mandatory Standard Practices)
1. **ALWAYS use `resolve_block_asset($path)` helper**:
   - ✅ Correct (Frontend): `<img src="{{ resolve_block_asset($item['image'] ?? '') }}">`
   - ✅ Correct (Admin Editor): `<img src="{{ resolve_block_asset($fieldValue) }}">`
   - **How `resolve_block_asset()` handles all asset types**:
     - Full URL (`http://...` / `https://...`) $\rightarrow$ Returns URL unchanged.
     - Starts with `themes/` $\rightarrow$ `asset($cleanPath)`
     - Starts with `storage/` $\rightarrow$ `asset($cleanPath)`
     - Filename in active theme assets (e.g., `security.webp`) $\rightarrow$ `asset("themes/{theme}/assets/{path}")`
     - Storage uploads (e.g., `uploads/2026/07/photo.jpg`) $\rightarrow$ `asset("storage/{path}")`
2. **ALWAYS store clean, relative paths in `theme.json` and API payloads**:
   - For Theme Default Assets: `"image": "themes/cdt/assets/photo.jpg"` or `"photo.jpg"`
   - For User Uploaded Media: `"image": "uploads/2026/07/photo.jpg"` or `"media/1/photo.jpg"`
3. **ALWAYS run `php artisan theme:publish --all` when adding theme assets**:
   - Theme assets in `themes/{slug}/assets/` must be published to `public/themes/{slug}/assets/` so web servers can serve them statically.

---

## CPT & Repeater Fields Standardization Rules

1. **Canonical Key `repeater_fields` Only**:
   - Official key in `options` JSON column for repeater subfields MUST strictly be **`repeater_fields`**.
2. **Strict Repeater Options Key Validation**:
   - API endpoints and model observers will **reject any unrecognized or legacy repeater option keys (e.g., `sub_fields`, `anak_fields`, `items`) with HTTP 422**.
3. **CPT MetaBoxes & Field Grouping**:
   - All CPT MetaFields default to `field_group = 'general'`.
   - `CptForm` automatically creates and ensures a `'general'` MetaBox exists when loading or saving CPT schemas.
4. **Icon Field Type & Lucide Icons**:
   - Field type `'icon'` is registered across CPT MetaFields and Page Blocks.
   - Use `<x-icon name="lucide:shield" class="w-5 h-5 text-blue-500" />` or `render_icon($name, $class)` to render icons.
   5. **Universal Strict CPT MetaField Naming Compliance**:
   - Applies to **ALL CPTs** (`tech-products`, `technology-alliance`, `posts`, `pages`, etc.).
   - Applies to **ALL Languages / Locales** (`en`, `id`, `es`, etc.).
   - Applies to **ALL Import/Input Methods** (CLI Artisan Importers, REST API HTTP Requests, Seeders, Admin Forms).
   - Data stored in `meta` or `meta['_translations'][<locale>]` **MUST STRICTLY MATCH FLAT CPT METAFIEILD KEYS** in database `meta_fields` table (e.g. `hero_title`, `about_content`, `banner_headline`). Raw nested JSON keys (e.g. `hero.title`, `about.paragraphs`) must be converted before storing.

---

## Scratch Script Debugging & Testing Pattern (Mandatory Practice)

When performing complex database updates, schema inspections, data migrations, or API route testing:

1. **ALWAYS use temporary scratch scripts in `scratch/` directory**:
   - Create single-file PHP scripts under `backend/scratch/` (e.g. `scratch/inspect_metafield.php`, `scratch/test_route.php`).
   - Bootstrap Laravel cleanly at the top of the scratch script:
     ```php
     <?php
     require 'vendor/autoload.php';
     $app = require_once 'bootstrap/app.php';
     $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
     $kernel->bootstrap();
     ```
2. **Why Scratch Scripts are Superior**:
   - Avoids shell escaping errors (PowerShell / CMD quote escaping issues).
   - Allows executing multi-line Eloquent queries, inspecting relationships, and verifying DB state safely.
   - Provides instant, clear, un-truncated diagnostic output.
3. **Clean Up Requirement**:
   - Always delete temporary scripts from `scratch/` directory after testing is completed.

---

## Crawl4AI Integration & Crawled Data Management

1. **Server Endpoint & Authorization**:
   - Crawl4AI REST API Server: `https://crawl.altia.dev/`
   - Authorization Header: `Authorization: Bearer crawl4ai-9a7f3b2c1d5e8f4a6b0c3d7e2f1a5b8c`
2. **Crawled Content Storage Directory**:
   - Target directory for storing extracted web content/Markdown: `backend/crawled_data/`
3. **Git Ignore Compliance**:
   - Directory `/crawled_data/` is strictly added to `.gitignore` to prevent committing raw or external crawled artifacts to repository version control.

---

## Centralized String Translation Registry Standards (Themes & Plugins)

1. **Mandatory Universal Helper (`t()`)**:
   - All Themes and Plugins **MUST** use the universal `t('group.key', 'Default Value', ['param' => $value])` helper function for UI labels, buttons, headers, and static phrases.
   - Do **NOT** write hardcoded `if (app()->getLocale() === 'id')` conditionals in Blade views.
2. **Semantic Dot-Notation Keys**:
   - Canonical keys MUST follow semantic dot-notation: `t('common.save')`, `t('header.contact_us')`, `t('akamai.hero.title')`.
   - The first segment is assigned as `group` (e.g. `common`, `header`, `akamai`), and remaining segments are assigned as `key` (e.g. `save`, `contact_us`, `hero.title`).
3. **No Isolated Translation Tables**:
   - Plugins and Themes are strictly prohibited from creating isolated translation database tables or custom translation files. All string translations MUST use the central `string_translation_keys`, `string_translations`, and `string_translation_sources` schema.
4. **Non-Destructive Auto-Discovery**:
   - The Admin String Translation Manager provides a `Scan Website Strings` action that automatically discovers `t()` calls across `themes/`, `plugins/`, and `resources/views/`. Scanner runs are non-destructive and preserve existing manual translations.
5. **Fallback Chain & Resolution**:
   - Translation resolution automatically executes: `Requested Locale` $\rightarrow$ `Fallback Locale` $\rightarrow$ `default_value` $\rightarrow$ `key`.
