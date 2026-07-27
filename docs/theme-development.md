# Theme Development Guide

Build themes to customize the frontend look and feel. Themes are self-contained Blade template packages with their own CSS, JS, and assets.

## Table of Contents
- [Quick Start](#quick-start)
- [Theme Structure](#theme-structure)
- [Theme Manifest](#theme-manifest)
- [Seed Pages](#seed-pages)
- [Layout Template](#layout-template)
- [Page Templates](#page-templates)
- [Archive Views](#archive-views)
- [Block Rendering](#block-rendering)
- [Asset Management](#asset-management)
- [Available Variables](#available-variables)
- [Tips & Best Practices](#tips--best-practices)

---

## Quick Start

```bash
# Scaffold a theme
php artisan make:theme starter --author="Your Name"

# Publish assets to public/
php artisan theme:publish starter

# Activate via Admin → Appearance → Themes
```

---

## Theme Structure

```
themes/starter/
├── theme.json                      # Manifest (required)
├── assets/
│   ├── css/theme.css               # Main stylesheet
│   ├── js/                         # JavaScript (optional)
│   └── images/                     # Static images (optional)
├── views/
│   ├── layouts/
│   │   └── app.blade.php           # Base HTML layout (required)
│   ├── pages/
│   │   ├── home.blade.php          # Homepage template (required)
│   │   └── single.blade.php        # Default page template (required)
│   ├── archive.blade.php           # CPT archive listing (optional)
│   ├── archive-{cpt}.blade.php     # CPT-specific archive (optional)
│   ├── single-entry.blade.php      # CPT single entry (optional)
│   ├── single-{cpt}.blade.php      # CPT-specific single (optional)
│   ├── taxonomy-{tax}.blade.php    # Taxonomy term archive (optional)
│   └── partials/
│       ├── header.blade.php        # Site header/navigation
│       ├── footer.blade.php        # Site footer
│       └── block.blade.php         # Block renderer (optional)
└── screenshot.png                  # Preview (800×600 recommended)
```

---

## Theme Manifest

Every theme needs a `theme.json`:

```json
{
    "name": "Starter",
    "slug": "starter",
    "version": "1.0.0",
    "description": "A clean starter theme.",
    "author": "Your Name",
    "author_url": "https://example.com",
    "screenshot": "screenshot.png",
    "requires": {
        "php": "^8.2",
        "cms": "^1.0"
    },
    "supports": ["pages", "posts", "menus"],
    "page_templates": {
        "default": {
            "label": "Default",
            "description": "Standard page layout"
        },
        "home": {
            "label": "Homepage",
            "description": "Main landing page",
            "blocks": [
                {"name": "hero_title", "type": "text", "label": "Hero Title"},
                {"name": "hero_subtitle", "type": "textarea", "label": "Hero Subtitle"},
                {"name": "hero_image", "type": "media", "label": "Hero Image"}
            ]
        }
    },
    "seed_pages": [
        {"title": "Home", "slug": "home", "template": "home", "status": "published", "menu_order": 0},
        {"title": "About", "slug": "about", "template": "default", "status": "draft", "menu_order": 1},
        {"title": "Contact", "slug": "contact", "template": "default", "status": "draft", "menu_order": 2}
    ],
    "archive_settings": {
        "per_page": 12,
        "layout": "grid",
        "show_sidebar": true,
        "show_excerpt": true,
        "show_author": true,
        "show_date": true,
        "excerpt_length": 150
    }
}
```

---

## Seed Pages

Themes can define **core pages** that are auto-created when the theme is activated. These "system pages" cannot be deleted by admins, and their slug & template are locked.

### Configuration

Add `seed_pages` to `theme.json`:

```json
"seed_pages": [
    {
        "title": "Home",
        "slug": "home",
        "template": "home",
        "status": "published",
        "menu_order": 0
    },
    {
        "title": "About",
        "slug": "about",
        "template": "default",
        "status": "draft",
        "menu_order": 1
    }
]
```

### How It Works

1. On theme activation, the CMS creates pages defined in `seed_pages`
2. Each page is marked as `is_system = true` (protected)
3. If a page with the same slug already exists, it's marked as system instead of duplicated
4. Admins can still edit **content** (title, blocks, SEO) but cannot change slug, template, or delete
5. Admins can still create custom pages via "Add New Page" — these are user-created and fully editable

### System Page Protection

| Action | System Page | User-Created Page |
|--------|-------------|-------------------|
| Edit title & content | ✅ | ✅ |
| Change slug | 🔒 Locked | ✅ |
| Change template | 🔒 Locked | ✅ |
| Delete | 🔒 Blocked | ✅ |
| Duplicate | ✅ (creates user copy) | ✅ |

---

## Layout Template

The base layout at `views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', setting('site_name', config('app.name')))</title>

    {{-- Favicon --}}
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @endif

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/starter/assets/css/theme.css') }}">

    @livewireStyles
    @stack('styles')
</head>
<body>
    @include($activeTheme->slug . '::partials.header')

    <main>
        @yield('content')
    </main>

    @include($activeTheme->slug . '::partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
```

### Key Points
- Use `$activeTheme->slug` for namespace in `@include` and `@extends`
- Always include `@livewireStyles` / `@livewireScripts` (admin components use Livewire)
- Use `@stack('styles')` and `@stack('scripts')` for page-specific assets
- Use `setting()` helper to pull site settings from the database

---

## Page Templates

### Homepage (`pages/home.blade.php`)

Controller passes: `$page`, `$testimonials`, `$partners`

```blade
@extends($activeTheme->slug . '::layouts.app')

@section('title', setting('site_name'))

@section('content')
    <h1>{{ $page?->block('hero_title') ?? 'Welcome' }}</h1>
    <p>{{ $page?->block('hero_subtitle') ?? 'Build something amazing.' }}</p>

    @if($page && $page->blocks->count())
        @foreach($page->blocks as $block)
            @if($block->is_active)
                {{-- Render each block --}}
            @endif
        @endforeach
    @endif
@endsection
```

### Single Page (`pages/single.blade.php`)

Controller passes: `$page`, `$blocks`

```blade
@extends($activeTheme->slug . '::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
    <h1>{{ $page->title }}</h1>

    @foreach($blocks as $block)
        @if($block->is_active)
            @include($activeTheme->slug . '::partials.block', ['block' => $block])
        @endif
    @endforeach
@endsection
```

### Template Resolution

The CMS looks for templates in this order:
1. `{theme}::pages.{slug}` — slug-specific template
2. `{theme}::pages.template-{template}` — template name
3. `{theme}::pages.single` — default page
4. `pages.single` — fallback

---

## Archive Views

Themes can provide archive and single entry views for Custom Post Types (CPTs) and taxonomy term archives.

### URL Structure

```
/{cpt-slug}/                    → Archive listing (paginated)
/{cpt-slug}/{entry-slug}        → Single entry view
/{taxonomy-slug}/{term-slug}/   → Taxonomy term archive
```

**Example:**
```
/blog/                          → All blog posts
/blog/hello-world               → Single blog post
/category/teknologi/            → Posts in "Teknologi" category
/tag/laravel/                   → Posts tagged "Laravel"
```

> **Note:** These routes are only active for CPTs with `has_archive = true` and active taxonomies.

### Archive Template (`archive.blade.php`)

Controller passes: `$postType`, `$entries` (paginated), `$taxonomies`

```blade
@extends($activeTheme->slug . '::layouts.app')

@section('title', $postType->plural_label)

@section('content')
    <h1>{{ $postType->plural_label }}</h1>

    @foreach($entries as $entry)
        <article>
            <h2><a href="{{ $entry->getUrl() }}">{{ $entry->title }}</a></h2>
            <p>{{ Str::limit(strip_tags($entry->excerpt), 150) }}</p>
            <time>{{ $entry->published_at->format('M d, Y') }}</time>
        </article>
    @endforeach

    {{ $entries->links() }}
@endsection
```

### Single Entry Template (`single-entry.blade.php`)

Controller passes: `$postType`, `$entry`, `$taxonomies`, `$previousEntry`, `$nextEntry`

```blade
@extends($activeTheme->slug . '::layouts.app')

@section('title', $entry->title)

@section('content')
    <h1>{{ $entry->title }}</h1>
    <div>{!! $entry->content !!}</div>

    {{-- Taxonomy terms --}}
    @foreach($entry->terms as $term)
        <a href="{{ $term->getUrl() }}">{{ $term->name }}</a>
    @endforeach

    {{-- Prev/Next --}}
    @if($previousEntry)
        <a href="{{ $previousEntry->getUrl() }}">&larr; {{ $previousEntry->title }}</a>
    @endif
    @if($nextEntry)
        <a href="{{ $nextEntry->getUrl() }}">{{ $nextEntry->title }} &rarr;</a>
    @endif
@endsection
```

### Taxonomy Term Template

For taxonomy term archives, the controller passes: `$taxonomy`, `$term`, `$terms`, `$entries` (paginated)

You can create a taxonomy-specific view at `taxonomy-{taxonomy-slug}.blade.php`.

### Archive View Resolution

| View Type | Resolution Priority |
|-----------|-------------------|
| **CPT Archive** | `{theme}::archive-{cpt}` → `{theme}::archive` → `archive-{cpt}` → `archive` |
| **Single Entry** | `{theme}::single-{cpt}` → `{theme}::single-entry` → `single-{cpt}` → `single-entry` |
| **Taxonomy Term** | `{theme}::taxonomy-{tax}` → `{theme}::archive` → `taxonomy-{tax}` → `archive` |

---

## CPT Relationships & Sub-CPTs (Product & Sub-Product / Variants)

When working with related Custom Post Types (e.g. `Product` → `Sub Product` / Variants, `Course` → `Lessons`, `Project` → `Client`), themes can easily retrieve and render related entries, breadcrumbs, and Schema.org structured data.

### 1. Retrieving Related Entries in Blade

To display related child entries (e.g. Sub-Products under a Main Product):

```blade
{{-- In single-product.blade.php --}}
@php
    // Fetch related sub-products using MetaField name or ID
    $subProducts = $entry->relatedEntries('sub_products')->published()->get();
@endphp

@if($subProducts->isNotEmpty())
    <div class="sub-products-grid">
        <h3>Product Variants / Sub-Products</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($subProducts as $sub)
                <div class="product-card border rounded-xl p-4">
                    @if($sub->featured_image)
                        <img src="{{ asset('storage/' . $sub->featured_image) }}" alt="{{ $sub->title }}">
                    @endif
                    <h4><a href="{{ $sub->getUrl() }}">{{ $sub->title }}</a></h4>
                    <p>{{ Str::limit(strip_tags($sub->excerpt), 100) }}</p>
                    <a href="{{ $sub->getUrl() }}" class="btn-detail">View Variant Specs &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>
@endif
```

### 2. Retrieving Parent Entry from a Sub-Product

To display the parent link or breadcrumb from a child Sub-Product page:

```blade
{{-- In single-sub_product.blade.php --}}
@php
    // Get primary parent product entry
    $parentProduct = $entry->parentRelatedEntries('sub_products')->first();
@endphp

@if($parentProduct)
    <nav class="breadcrumb text-sm mb-4">
        <a href="{{ url('/') }}">Home</a> &gt; 
        <a href="{{ url('/' . $postType->slug) }}">{{ $postType->plural_label }}</a> &gt; 
        <a href="{{ $parentProduct->getUrl() }}">{{ $parentProduct->title }}</a> &gt; 
        <span>{{ $entry->title }}</span>
    </nav>
@endif
```

### 3. SEO Friendly Nested URLs

When a Sub-Product has a related parent entry, `$entry->getUrl()` automatically resolves to a nested URL structure for optimal SEO:

```
/{cpt-slug}/{parent-entry-slug}/{sub-product-slug}
```
**Example:** `/sub-products/iphone-15-pro/iphone-15-pro-256gb-natural-titanium`

### 4. Schema.org Structured Data (JSON-LD)

To render Google-ready Schema.org JSON-LD including related items (`isRelatedTo`):

```blade
@push('head')
<script type="application/ld+json">
{!! json_encode($entry->getSchemaJsonLd(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush
```

### Archive Settings (`theme.json`)

Configure archive behavior in `theme.json`:

```json
"archive_settings": {
    "per_page": 12,
    "layout": "grid",
    "show_sidebar": true,
    "show_excerpt": true,
    "show_author": true,
    "show_date": true,
    "excerpt_length": 150
}
```

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `per_page` | int | 12 | Entries per page |
| `layout` | string | `grid` | Layout style (`grid`, `list`) |
| `show_sidebar` | bool | true | Show taxonomy sidebar |
| `show_excerpt` | bool | true | Show entry excerpts |
| `show_author` | bool | true | Show author name |
| `show_date` | bool | true | Show published date |
| `excerpt_length` | int | 150 | Max excerpt characters |

### Entry URL Helpers

Use these in your templates:

```blade
{{-- Entry URL --}}
<a href="{{ $entry->getUrl() }}">{{ $entry->title }}</a>

{{-- CPT archive URL --}}
<a href="{{ $postType->getArchiveUrl() }}">All {{ $postType->plural_label }}</a>

{{-- Taxonomy term URL --}}
<a href="{{ $term->getUrl() }}">{{ $term->name }}</a>
```

---

## Block Rendering

The CMS page builder supports these block types:

| Type | Value | Rendering |
|------|-------|-----------| 
| `text` | `string` | `$block->localizedValue` |
| `textarea` | `string` | `nl2br(e($block->localizedValue))` |
| `wysiwyg` | `HTML` | `{!! $block->localizedValue !!}` |
| `number` | `int` | `$block->value` with prefix/suffix options |
| `media` | `path` | `asset('storage/' . $block->value)` |
| `gallery` | `JSON array` | `$block->getDecodedValue()` — array of image paths |
| `date` | `Y-m-d` | `Carbon::parse($block->value)` |
| `datetime` | `ISO` | `Carbon::parse($block->value)` |
| `select` / `radio` | `string` | `$block->value` |
| `checkbox` | `JSON array` | `$block->getDecodedValue()` — array of selected values |
| `switcher` | `bool` | `$block->getDecodedValue()` — true/false |
| `color` | `#hex` | `$block->value` |
| `repeater` | `JSON` | `$block->localizedValue()` with `$block->childBlocks` |

### Example Block Partial

See `themes/default/views/partials/block.blade.php` for a complete reference implementation.

---

## Asset Management

### Publishing Assets

Theme assets live in `themes/{slug}/assets/` but are served from `public/themes/{slug}/assets/`:

```bash
# Publish one theme
php artisan theme:publish starter

# Publish all themes
php artisan theme:publish --all

# Force overwrite
php artisan theme:publish starter --force
```

### Referencing in Blade

```blade
<link rel="stylesheet" href="{{ asset('themes/starter/assets/css/theme.css') }}">
<script src="{{ asset('themes/starter/assets/js/app.js') }}" defer></script>
<img src="{{ asset('themes/starter/assets/images/logo.png') }}" alt="Logo">
```

### In CSS (relative paths)

```css
.hero {
    background-image: url('../images/hero-bg.jpg');
}
```

---

## Available Variables

These are shared with all theme views via `View::share()`:

| Variable | Type | Description |
|----------|------|-------------|
| `$activeTheme` | `Theme` model | Name, slug, version of active theme |
| `$themeConfig` | `array` | Merged config from `themes/{slug}/config/*.php` |

### Page Variables

| Variable | Available In | Type | Description |
|----------|-------------|------|-------------|
| `$page` | Page views | `Page` | Page model with blocks |
| `$blocks` | Page views | `Collection` | Page blocks |

### Archive Variables

| Variable | Available In | Type | Description |
|----------|-------------|------|-------------|
| `$postType` | Archive / Single | `CustomPostType` | CPT model |
| `$entries` | Archive | `LengthAwarePaginator` | Paginated entries |
| `$entry` | Single Entry | `CptEntry` | Single entry model |
| `$taxonomies` | Archive / Single | `Collection` | Available taxonomies |
| `$previousEntry` | Single Entry | `CptEntry\|null` | Previous entry |
| `$nextEntry` | Single Entry | `CptEntry\|null` | Next entry |
| `$taxonomy` | Term Archive | `CustomTaxonomy` | Taxonomy model |
| `$term` | Term Archive | `TaxonomyTerm` | Current term |
| `$terms` | Term Archive | `Collection` | All terms with count |

### Helpers

| Helper | Returns | Example |
|--------|---------|---------| 
| `setting('key', 'default')` | Mixed | `setting('site_name', 'My Site')` |
| `$page->block('name')` | String/null | `$page->block('hero_title')` |
| `$block->localizedValue` | String | Locale-aware block value |
| `$block->getDecodedValue()` | Array/null | JSON-decoded value |
| `$block->getOption('key')` | String/null | Block metadata option |
| `$entry->getUrl()` | String | Full URL to single entry |
| `$postType->getArchiveUrl()` | String | Full URL to CPT archive |
| `$term->getUrl()` | String | Full URL to term archive |

---

## Tips & Best Practices

1. **Use CSS custom properties** for theming — makes it easy to customize colors
2. **Avoid external CSS frameworks** (Bootstrap, Tailwind) to keep themes lightweight
3. **Use `loading="lazy"`** on images for performance
4. **Always test responsive** — mobile traffic is 60%+ in most markets
5. **Include a `screenshot.png`** (800×600) for the admin theme picker
6. **Use `@stack` for page-specific assets** — don't load everything globally
7. **Reference `$activeTheme->slug`** instead of hardcoding your theme slug in `@extends` and `@include`
8. **Define `seed_pages`** for core pages your theme needs — admin can focus on content instead of setup
9. **Provide archive views** if your theme is meant for content-heavy sites (blogs, news, portfolios)

---

## SEO & GEO (Generative Engine Optimization)

### Automatic SEO Injection

The CMS **automatically injects SEO tags** into every public HTML response via the `InjectSeoTags` middleware. This means:

> **Theme developers do NOT need to manually add SEO meta tags.** The CMS handles it.

The middleware inserts the following just before `</head>`:
- `<meta name="description">` — from per-page or global default
- `<meta name="robots">` — from per-page setting
- `<link rel="canonical">` — from per-page setting or current URL
- Open Graph tags (`og:title`, `og:description`, `og:image`, etc.)
- Twitter Card tags
- Google/Bing verification meta tags
- JSON-LD schema (per-page Article/WebPage/Event + site-wide Organization)
- Publishing Principles schema (E-E-A-T signals for AI engines)

### What Themes Get for Free

| Feature | How It Works | Theme Action Required |
|---------|-------------|----------------------|
| Meta description | Injected from SEO meta box | ❌ None |
| Open Graph + Twitter | Auto-generated from per-page or global | ❌ None |
| JSON-LD per page | Built from entity + schema_type | ❌ None |
| Organization schema | Built from Admin → Settings → SEO | ❌ None |
| BreadcrumbList schema | Built from BreadcrumbService | ❌ None (Auto-injected in `<head>`) |
| Publishing Principles | Injected in Organization schema | ❌ None |
| Google/Bing verification | Auto-injected | ❌ None |
| Speakable markup | Auto-added to Article types | ✅ Optional enhancement |
| AI Summary (`abstract`) | From per-page "GEO / AI" section | ❌ None |
| `/llms.txt` | Auto-generated controller | ❌ None |
| `/sitemap.xml` | Auto-generated controller | ❌ None |
| `/robots.txt` | Auto-generated controller | ❌ None |

### Rendering Breadcrumbs in Themes

Themes can render responsive, SEO-optimized breadcrumbs configured in **SEO Settings → Breadcrumbs** using the `<x-seo-breadcrumbs />` Blade component:

```blade
<div class="container mx-auto px-4 py-3">
    {{-- Default breadcrumbs (auto-detects entity from page/post view data) --}}
    <x-seo-breadcrumbs />

    {{-- Pass specific entity model explicitly --}}
    <x-seo-breadcrumbs :entity="$post" />
</div>
```

The component automatically inherits all separator choices, homepage anchor text, path prefixes, and bold styles configured by admins in **Admin → SEO Settings → Breadcrumbs**. In addition, the `<head>` of the page automatically receives the JSON-LD `BreadcrumbList` schema for Google Rich Snippets without any extra code.

### Optional: Enhancing SEO from Theme Views

While automatic SEO covers everything, theme developers can optionally:

#### 1. Add Speakable Markup

Mark key content sections that AI assistants should quote:

```blade
<article>
    <h1>{{ $entry->title }}</h1>
    <div class="entry-content" data-speakable>
        {!! $entry->content !!}
    </div>
</article>
```

The `data-speakable` attribute and `.entry-content` class are pre-configured in the schema's `SpeakableSpecification`. Theme developers can add the attribute to any important content block.

#### 2. Remove Duplicate Meta Tags

If your theme was previously adding manual meta tags via `@push('meta')`, you should **remove them** to avoid duplicates:

```blade
{{-- ❌ OLD WAY: Remove this from your theme views --}}
@push('meta')
    <meta name="description" content="...">
    <meta property="og:title" content="...">
@endpush

{{-- ✅ NEW WAY: Nothing needed. The CMS handles it automatically. --}}
```

#### 3. Custom Page Title

The `<title>` tag is NOT managed by the middleware (it's set via `@section('title')`). Themes should continue to set page titles:

```blade
@section('title', $page->getMetaTitle())
```

### Layout Template (Minimal SEO-Compatible)

A minimal `layouts/app.blade.php` that works perfectly with auto-injected SEO:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', setting('site_name', config('app.name')))</title>

    {{-- Favicon --}}
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @endif

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/starter/assets/css/theme.css') }}">

    @livewireStyles
    @stack('styles')

    {{-- SEO/GEO tags are auto-injected here by InjectSeoTags middleware --}}
</head>
<body>
    @include($activeTheme->slug . '::partials.header')

    <main>
        @yield('content')
    </main>

    @include($activeTheme->slug . '::partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
```

### GEO Features Available via Admin

Theme developers should know these GEO features exist so they can guide content creators:

| Feature | Location | Purpose |
|---------|----------|---------|
| AI Summary | SEO meta box → GEO / AI | Key takeaway for AI citation |
| Cornerstone Content | SEO meta box → GEO / AI | Flag important pages |
| Publishing Principles | Settings → SEO → Publishing Principles | E-E-A-T trust signals |
| Organization Enrichment | Settings → SEO → Schema.org | Entity recognition by AI |
| Site AI Summary | Settings → SEO → GEO / AI | Homepage/llms.txt description |
| LLMS.txt | Settings → SEO → GEO / AI | AI crawler site map |

---

## CPT vs. Repeater Field Selection Architecture

When designing a theme, populating pages via API, or writing prompts for AI agents, you must evaluate whether repeatable content should be stored as a **Custom Post Type (CPT)** or as a **Repeater Block Field** in `theme.json`.

### 🎯 Decision Rule: When to Use CPT vs. Repeater

```
                         Does content need standalone URLs,
                       individual detail pages, or sitewide queries?
                                      /          \
                                    YES          NO
                                    /              \
                        [Use Custom Post Type]    Is it repeatable content
                                                  specific to ONE page layout
                                                  (e.g., Cards, Features, FAQs)?
                                                               |
                                                              YES
                                                               |
                                                    [Use Repeater Field]
```

#### 1. Custom Post Type (CPT) — Sitewide / Entity Driven
Use a CPT when:
- Content items are global entities reused across multiple pages or archives (e.g., *Products, Case Studies, Team Members, Job Vacancies, Testimonials*).
- Each item requires its own dedicated detail URL (e.g., `/products/cloud-security`, `/careers/devops-engineer`).
- Content requires multi-taxonomy filtering, searching, or pagination.

#### 2. Repeater Field (`theme.json`) — Page-Specific / Section Driven
Use a Repeater Field when:
- Content items are **repeatable UI elements bound to a single page template** (e.g., *Area of Expertise cards on Homepage*, *FAQ accordions on About page*, *Pricing Tiers on Landing page*, *Feature highlights*).
- Columns or cards might expand or change order in the future, but items do **NOT** need standalone detail URLs or sitewide archive pages.
- Non-technical admins need a simple drag-and-drop / add-remove interface in the Page Block Builder to manage columns without creating a full CPT entry.

---

### 🛠️ How to Implement Repeater Fields

#### 1. Define Repeater Schema in `theme.json`
Inside your page template blocks in `theme.json`, define the repeater with its child fields (`text`, `textarea`, `media`, `wysiwyg`, `switcher`, `color`, `number`):

```json
"page_templates": {
    "home": {
        "label": "Homepage",
        "blocks": [
            {
                "name": "expertise_list",
                "type": "repeater",
                "label": "Area of Expertise Cards",
                "children": [
                    {"name": "image", "type": "media", "label": "Card Image"},
                    {"name": "title", "type": "text", "label": "Card Title"},
                    {"name": "description", "type": "textarea", "label": "Card Description"}
                ],
                "default": [
                    {
                        "image": "themes/cdt/assets/security.webp",
                        "title": "Security",
                        "description": "Preventative approach against cyber threats..."
                    },
                    {
                        "image": "themes/cdt/assets/clouds.webp",
                        "title": "Cloud",
                        "description": "Reap the benefits of cloud-native development..."
                    }
                ]
            }
        ]
    }
}
```

#### 2. Render Repeater Data in Blade View (`pages/home.blade.php`)
Repeater data is stored as a JSON array string or array. Always safely parse and provide fallback default data:

```blade
@php
    $expertiseItems = $page?->block('expertise_list');
    if (is_string($expertiseItems)) {
        $expertiseItems = json_decode($expertiseItems, true);
    }
    if (empty($expertiseItems) || !is_array($expertiseItems)) {
        $expertiseItems = [
            [
                'image' => 'themes/cdt/assets/security.webp',
                'title' => 'Security',
                'description' => 'Default security description...'
            ]
        ];
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($expertiseItems as $item)
        @php
            $imgUrl = resolve_block_asset($item['image'] ?? '');
        @endphp
        <div class="card p-6 bg-white rounded-2xl">
            @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="{{ $item['title'] ?? '' }}" class="w-full h-40 object-cover rounded-xl mb-4">
            @endif
            <h3 class="font-bold text-xl mb-2">{{ $item['title'] ?? '' }}</h3>
            <p class="text-zinc-500 text-sm leading-relaxed">{{ $item['description'] ?? '' }}</p>
        </div>
    @endforeach
</div>
```

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

**Reference:** See `themes/default/` and `themes/cdt/` for complete working themes.

**Last Updated:** 2026-07-27
**Version:** 4.2


