# Project Rules & Guidelines for CTI CMS

## CPT MetaBox & Field Group Strict Alignment Rule
Whenever adding or modifying a `MetaField` for any `CustomPostType` (`cpt_entries`):
1. **Register MetaBox Tab**: Always inspect `$cpt->settings['meta_boxes']`. If adding a new field or field group, register `{"id": "box_id", "title": "Box Title", "context": "normal"}` into `$cpt->settings['meta_boxes']`.
2. **Match Field Group**: Always set `MetaField.field_group = "box_id"` matching the exact `id` in `$cpt->settings['meta_boxes']`.
3. **Avoid Unmapped Fields**: Never leave `field_group` as `null` or unmapped, otherwise the field falls out of the main tab container into an unorganized fallback box (`CUSTOM FIELDS`).

## Mandatory SEO Breadcrumbs Rule
**NEVER** write custom, manual, or hardcoded `<nav>` breadcrumb HTML in theme views!
1. **ALWAYS Use Component**: Always render breadcrumbs using `<x-seo-breadcrumbs :entity="$entry" />` (or `:entity="$page"` / `:entity="$postType"`).
2. **Never Reinvent Breadcrumb HTML**: Do not write custom `<nav>` tags, manual `<a>` links, or inline chevron SVG loops in template files.
3. **Integrates with BreadcrumbService**: The component automatically resolves `$postType->has_archive`, parent/child entry hierarchies, current locale translations, and syncs with JSON-LD schema structured data in `<head>`.
4. **Pass Custom Styling via Attributes**: Customize text colors and margins directly via Blade attributes (e.g. `<x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-10" />`).

## Mandatory MediaService Image Import Rule
1. **Always Use `MediaService`**: All images during import MUST be processed via `app(App\Services\MediaService::class)->upload($file, $metadata)`.
2. **Automatic Optimization**: Calling `MediaService` ensures images are compressed, converted to WebP, provided with responsive variants (`sm`, `md`, `lg`, `xl`, `thumb`), and registered in the `media` table.
3. **Store Relative Paths**: Store `$media->path` in database fields, NEVER hardcode external URLs or direct un-tracked file copies.

## Mandatory Local Asset Rule (No External CDN)
1. **Zero External CDN Dependencies**: ALL frontend assets (CSS, JS libraries like Swiper/Alpine/jQuery, fonts, icons, etc.) MUST be downloaded and stored locally.
2. **Theme Asset Helper**: Place theme scripts and styles inside `themes/{theme}/assets/` and load them via `{{ theme_asset('filename.js') }}`.
3. **No External URLs**: NEVER write external CDN `<script src="https://cdn...">` or `<link href="https://cdn...">` tags in Blade views or layouts. All asset dependencies must work 100% offline.

## Mandatory Reference Inspection & User Confirmation Rule for Pages
1. **Inspect HTML Reference First**: ALWAYS inspect the original HTML export (`dist/index.html`) to verify whether sections (e.g. About, Contact) are designed as *One-Page Scroll Anchors* (`/#about`, `/#contact`) or *Dedicated Standalone Pages*.
2. **Never Assume Dedicated Pages**: DO NOT create separate dedicated `Page` records in the database without checking reference files first.
3. **Ask in Implementation Plan**: If there is ambiguity or options on page creation vs single-page scroll layout, list the question explicitly under `## Open Questions` in `implementation_plan.md` for user confirmation BEFORE executing.

## Mandatory Plugin Status Fail-Safe Rule
1. **Always Check Plugin Status**: Theme views MUST check `is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Post::class)` before querying model classes from optional plugins.
2. **Graceful Fallback**: If a plugin is deactivated in the admin panel, the view MUST fallback gracefully (e.g. returning an empty collection or hiding the section) without throwing `ClassNotFoundException` or crashing the app.




