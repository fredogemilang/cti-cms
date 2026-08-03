# Theme Development Guidelines — CTI CMS

All themes developed for CTI CMS must adhere to the core design system, performance guidelines, and translation standards outlined below.

---

## 1. Using the Universal `t()` Helper for Translations

In Blade views, never hardcode text or write locale conditional checks (`if (app()->getLocale() === 'id')`). Always use the universal `t()` helper:

```blade
<!-- Good: Semantic Key with Default Fallback Value -->
<h2 class="title">{{ t('akamai.benefits_title', 'Benefits of') }} {{ $entry->title }}</h2>

<!-- Good: Buttons & CTAs -->
<a href="#contact" class="btn">{{ t('common.talk_to_experts', 'Talk to Our Experts') }}</a>

<!-- Good: Placeholders & Variables -->
<p>{{ t('auth.welcome_message', 'Welcome back, :name', ['name' => $user->name]) }}</p>
```

### Auto-Discovery & Admin Management
 Admin users can click **"Scan Website Strings"** in `/ctrlpanel/settings/string-translations` to automatically discover all theme strings and manage translations from the dashboard.

---

## 2. Asset Resolving & Responsive Images

### A. Asset Helper (`resolve_block_asset`)
Never use raw `asset('storage/'.$path)` for user-uploaded block media. Always use `resolve_block_asset($path, $variant)`:

```blade
<!-- Resolves variant WebP file (sm, md, lg, xl, thumb) if available -->
<img src="{{ resolve_block_asset($block->image, 'sm') }}" alt="{{ $block->title }}">
```

### B. Responsive Image Component (`<x-image>`)
For primary image elements, prefer using the `<x-image>` Blade component. It automatically renders a modern `<picture>` element with WebP sources, `srcset`, `sizes`, `loading="lazy"`, and `decoding="async"`:

```blade
<x-image :src="$page->hero_image" alt="Hero Image" class="w-full h-auto rounded-2xl" />
```

---

## 3. SEO Heading Hierarchy & HTML Standards

To maintain clean document outline and optimal search engine indexing:

1. **Strict H1 Hierarchy**: Exactly **one `<h1>` per page** (usually in the Hero section). Sub-headings must follow `h1 > h2 > h3`.
2. **No Headings in Nav, Footer, or Tabs**: Footer titles, navbar item labels, and tab switcher buttons **must NOT use `<h1..h6>` tags**. Use `<span>` or `<button>` elements instead.
3. **Hero Category Subtitles**: Do NOT place `<h2>` category labels above `<h1>`. Use `<span class="block ...">` for category subtitles so `<h1>` remains the first heading element in the DOM tree.

---

## 4. Shared Sections & Partials

Common sections reused across multiple templates (such as contact forms, CTA banners, or newsletter signups) **MUST be placed in partials**:

```
themes/cdt/views/partials/
├── contact-section.blade.php
├── hero.blade.php
└── footer.blade.php
```

Include partials in page templates using dot notation:
```blade
@include('cdt::partials.contact-section')
```

---

## 5. Theme Manifest (`theme.json`) & Publishing

Every theme requires a `theme.json` manifest defining available templates, block schemas, form placeholders, and menu locations:

```json
{
  "name": "CDT Theme",
  "slug": "cdt",
  "version": "1.0.0",
  "templates": {
    "home": {
      "label": "Home Page",
      "blocks": []
    }
  },
  "menu_locations": {
    "primary": "Primary Navigation",
    "footer": "Footer Navigation"
  }
}
```

Publish theme assets to the public directory using artisan:
```bash
php artisan theme:publish cdt
```

---

## 6. Mandatory SEO Breadcrumb Component (`<x-seo-breadcrumbs>`)

All theme templates (single CPTs, single posts, static pages, archives) **MUST** use the official `<x-seo-breadcrumbs>` component for breadcrumbs.

```blade
<!-- Mandatory: Always use <x-seo-breadcrumbs> -->
<x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-10" />

<!-- For static pages -->
<x-seo-breadcrumbs :entity="$page" class="text-zinc-500 mb-8" />
```

### Strict Rules:
1. **No Manual `<nav>` Tags**: Writing manual/hardcoded `<nav>` breadcrumb HTML, `<a>` tag chains, or inline SVG loops in Blade views is **strictly forbidden**.
2. **Automatic Schema Sync**: `<x-seo-breadcrumbs>` integrates directly with `BreadcrumbService`, automatically respecting `$postType->has_archive`, parent/child entry relations, and generating matching JSON-LD `BreadcrumbList` schema in `<head>`.
3. **Attribute Customization**: Pass custom colors, font sizes, or margins via standard Blade `$attributes` (`class="..."`).
