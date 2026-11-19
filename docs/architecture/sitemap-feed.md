# Sitemap & Feed System

## Components

| Output | URL | Controller |
|--------|-----|------------|
| Sitemap Index | `/sitemap.xml` | `SitemapController` |
| Page Sitemap | `/sitemap-pages.xml` | `SitemapController` |
| CPT Sitemap | `/sitemap-{type}.xml` | `SitemapController` |
| RSS Feed | `/feed` | `FeedController` |
| Atom Feed | `/feed/atom` | `FeedController` |
| LLMs.txt | `/llms.txt` | `LlmsTxtController` |
| Robots.txt | `/robots.txt` | `RobotsController` (dynamic) |

## Sitemap Features

- **On-the-fly generation** with per-type, per-locale caching
- **Multi-locale**: `<xhtml:link>` alternates for each URL (`/en/page` ↔ `/id/page`)
- **Respects `has_archive`**: CPT types with `has_archive = false` excluded
- **Respects `publicly_queryable`**: non-public CPTs excluded
- **Filter hooks**: `cpt_entry.url` and `cpt.archive_url` modify URLs
- **Auto-ping**: IndexNow + Google Indexing API on content publish

## Sitemap Structure

```xml
<sitemapindex>
  <sitemap><loc>/sitemap-pages.xml</loc></sitemap>
  <sitemap><loc>/sitemap-solutions.xml</loc></sitemap>
  <sitemap><loc>/sitemap-products.xml</loc></sitemap>
  ...
</sitemapindex>
```

Each sub-sitemap includes `<xhtml:link rel="alternate" hreflang="..." href="..."/>` for every locale.

## RSS/Atom Feeds

- Latest published content from all public CPTs
- Configurable items per feed
- Full content or excerpt mode

## LLMs.txt

- Auto-generated from published pages + CPT entries
- Provides structured content listing for AI crawlers
- Respects `noindex` SEO settings

## Robots.txt

- **Dynamic** — served by `RobotsController`, NOT static `public/robots.txt`
- Configurable via admin SEO settings
- Includes sitemap pointer
- **Gotcha:** Delete `public/robots.txt` if it exists, or web server serves it instead of Laravel (G29)

## Key Files

| File | Purpose |
|------|---------|
| `app/Http/Controllers/SitemapController.php` | Sitemap generation |
| `app/Http/Controllers/FeedController.php` | RSS/Atom feeds |
| `app/Http/Controllers/LlmsTxtController.php` | LLMs.txt generation |
| `app/Http/Controllers/RobotsController.php` | Dynamic robots.txt |
| `app/Services/IndexNowService.php` | IndexNow protocol |
| `app/Services/GoogleIndexingService.php` | Google Indexing API |
