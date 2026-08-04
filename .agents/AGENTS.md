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

