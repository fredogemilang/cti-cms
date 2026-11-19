# SEO System

## Components

| Component | File | Role |
|-----------|------|------|
| `SeoMeta` model | `app/Models/SeoMeta.php` | Per-locale SEO data storage (polymorphic) |
| `SeoMetaBox` | `app/Livewire/Admin/Seo/SeoMetaBox.php` | Admin editor widget |
| `SeoGeneralSettings` | `app/Livewire/Admin/Seo/SeoGeneralSettings.php` | Global SEO config & breadcrumbs settings |
| `SeoOverview` | `app/Livewire/Admin/Seo/SeoOverview.php` | SEO dashboard with stats |
| `SeoBulkEditor` | `app/Livewire/Admin/Seo/SeoBulkEditor.php` | Bulk edit SEO fields |
| `SeoIndexNow` | `app/Livewire/Admin/Seo/SeoIndexNow.php` | Instant indexing UI |
| `InjectSeoTags` | `app/Http/Middleware/InjectSeoTags.php` | Auto-inject meta tags & JSON-LD in `<head>` |
| `SeoRenderer` | `app/Services/SeoRenderer.php` | Resolves title/desc/OG per entity |
| `SchemaBuilder` | `app/Services/SchemaBuilder.php` | JSON-LD structured data |
| `BreadcrumbService` | `app/Services/BreadcrumbService.php` | Hierarchical breadcrumb items |
| `SeoBreadcrumbs` | `app/View/Components/SeoBreadcrumbs.php` | Blade component `<x-seo-breadcrumbs />` |
| `IndexNowService` | `app/Services/IndexNowService.php` | IndexNow protocol ping |
| `GoogleIndexingService` | `app/Services/GoogleIndexingService.php` | Google Indexing API |

## SEO Data Model

`SeoMeta` is polymorphic — each Page or CptEntry can have per-locale SEO rows:
```
seoable_type = "App\Models\Page" | "App\Models\CptEntry"
seoable_id   = page_id | entry_id
locale       = "en" | "id"
```

Fields: `meta_title`, `meta_description`, `og_title`, `og_description`, `og_image`, `twitter_card`, `canonical_url`, `noindex`, `nofollow`, `schema_type`, `schema_data` (JSON), `geo_region`, `geo_placename`, `geo_position`, `geo_latitude`, `geo_longitude`

## How It Works

1. Admin sets SEO data via `SeoMetaBox` component on Page/CPT editor
2. On frontend request, `InjectSeoTags` middleware fires after `SetLocale`
3. Middleware calls `SeoRenderer::render()` with the current entity
4. `SeoRenderer` resolves title/desc with fallback chain: SEO table → entity title → site defaults
5. Output injected before `</head>` via response manipulation

## Breadcrumbs

**Always use the component:**
```blade
<x-seo-breadcrumbs :entity="$page" class="text-zinc-500 mb-8" />
```

The component auto-generates:
- Visual breadcrumb trail (respects `has_archive`, parent/child relations)
- JSON-LD `BreadcrumbList` schema in `<head>`
- Localized labels (Home / Beranda from SEO settings)
- Proper `aria-label` and semantic HTML

**Never hand-code breadcrumb `<nav>` tags in Blade.**

## Heading Hierarchy (CRITICAL)

- Exactly **one `<h1>` per page** (usually in Hero section)
- No heading tags (`<h1>`–`<h6>`) in nav, footer, or tab buttons — use `<span>` or `<button>`
- Hero category labels must use `<span>`, not `<h2>`, so `<h1>` stays first
- Tab labels must use `<button>`, not `<h3>`

## Related Systems

- [Sitemap & Feed](sitemap-feed.md) — Multi-locale sitemap, RSS/Atom, LLMs.txt, Robots.txt
- [Redirect & 404](redirect-404.md) — Redirect Manager & 404 Logger
- [Middleware Stack](middleware-stack.md) — InjectSeoTags middleware details
