# CTI CMS — SEO Best Practices (AI Skill)

> This skill covers SEO implementation rules in CTI CMS themes and content.

## Per-Entity SEO Meta

Every page and CPT entry has polymorphic `SeoMeta` with:
- `meta_title` — browser tab title (50-60 chars)
- `meta_description` — search snippet (150-160 chars)
- `og_title`, `og_description`, `og_image` — social sharing
- `canonical_url` — canonical link
- `no_index`, `no_follow` — robots directives

### Setting SEO via API
```
PUT /api/v1/admin/pages/{id}/seo
PUT /api/v1/admin/cpt/{type}/entries/{id}/seo
```

## Heading Hierarchy Rules

1. **Exactly ONE `<h1>` per page** — usually in the hero section
2. Sub-headings follow `h1 > h2 > h3` — never skip levels
3. **NO headings in nav, footer, or tabs** — use `<span>` or `<button>`
4. Hero category subtitles: use `<span class="block">` NOT `<h2>`

## Breadcrumbs

### MANDATORY: `<x-seo-breadcrumbs>` Component
```blade
<x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-10" />
<x-seo-breadcrumbs :entity="$page" class="text-zinc-500 mb-8" />
```

### Rules
- ❌ NEVER write manual `<nav>` breadcrumb HTML
- ✅ ALWAYS use `<x-seo-breadcrumbs>` component
- Auto-generates JSON-LD `BreadcrumbList` schema in `<head>`
- Respects CPT `has_archive` and parent/child relations

## Structured Data (JSON-LD)

Injected automatically by `InjectSeoTags` middleware:
- `Organization` schema (from settings)
- `WebSite` schema with search action
- `BreadcrumbList` (from breadcrumbs)
- `Article` (for blog posts via plugin)

## Sitemap

Auto-generated at `/sitemap.xml`:
- All published pages
- All active CPT entries
- Posts (if plugin active)
- Priority and changefreq based on content type

## IndexNow

Auto-pings IndexNow on content publish/update for fast indexing.
Logs viewable at `/ctrlpanel/seo/indexing-logs`.

## llms.txt

Available at `/llms.txt` — structured text file for AI crawlers.
Controlled by `seo_llms_enabled` setting.
