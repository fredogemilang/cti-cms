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

### B. Responsive Image Component (`<x-image>`) — ⚠️ MANDATORY

> **RULE**: All user-uploaded content images (from Media Library / block fields / CPT meta) **MUST** use `<x-image>`. Raw `<img>` tags for uploaded content are **strictly forbidden** — they bypass srcset, serve oversized images to mobile, and trigger PageSpeed "Improve image delivery" warnings.

`<x-image>` automatically renders a modern `<picture>` element with WebP `<source>`, responsive `srcset`, `sizes`, `loading="lazy"`, `decoding="async"`, LQIP blur placeholder, and focal-point object-position.

```blade
<!-- Basic usage -->
<x-image :src="$page->hero_image" alt="Hero Image" class="w-full h-auto rounded-2xl" />

<!-- With explicit sizes for layout context -->
<x-image :src="$entry->featured_image" alt="{{ $entry->title }}"
    class="w-full h-full object-cover"
    sizes="(max-width: 768px) 100vw, 50vw" />

<!-- With Media model directly (preferred when available) -->
<x-image :media="$mediaModel" alt="{{ $mediaModel->alt_text }}" size="lg" />

<!-- Disable placeholder for transparent images -->
<x-image :src="$logo" alt="Logo" :placeholder="false" class="h-12 w-auto" />
```

#### Props Reference

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | `null` | Image path (resolved via `resolve_block_asset`) |
| `media` | `Media` | `null` | Media model instance (preferred, skips DB lookup) |
| `size` | `string` | `'lg'` | Preferred variant for `src` (`thumb`, `sm`, `md`, `lg`, `xl`) |
| `sizes` | `string` | `'100vw'` | HTML `sizes` attribute — **critical for performance** |
| `alt` | `string` | `null` | Alt text (falls back to Media record) |
| `loading` | `string` | `'lazy'` | Set `'eager'` for above-the-fold hero images |
| `decoding` | `string` | `'async'` | Image decoding strategy |
| `class` | `string` | `''` | CSS classes for `<img>` element |
| `pictureClass` | `string` | `''` | CSS classes for `<picture>` wrapper |
| `placeholder` | `bool` | `true` | Show LQIP blur placeholder (auto-disabled for PNG/WebP/SVG) |

#### `sizes` Attribute Cheat Sheet

The `sizes` attribute tells the browser which variant to download **before CSS loads**. Without it, the browser always downloads the largest image.

| Layout Context | `sizes` Value |
|----------------|---------------|
| Full-width hero | `100vw` (default) |
| 2-column grid | `(max-width: 768px) 100vw, 50vw` |
| 3-column grid | `(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw` |
| Sidebar card | `(max-width: 768px) 100vw, 400px` |
| Thumbnail / avatar | `80px` or `150px` |
| Logo | `200px` |

#### Generated Variants (automatic on upload)

| Variant | Max Width | Use Case |
|---------|-----------|----------|
| `thumb` | 150px | Admin thumbnails |
| `sm` | 480px | Mobile viewport |
| `md` | 768px | Tablet viewport |
| `lg` | 1280px | Desktop viewport |
| `xl` | 1920px | Large/retina screens |
| WebP | per variant | Auto-generated alongside each variant |
| LQIP | 16px blur | Base64 inline placeholder |

### C. When Raw `<img>` is Acceptable

Raw `<img>` tags are ONLY allowed for:
- **Static theme assets** (icons, logos, decorative SVGs from `themes/{slug}/assets/`)
- **Tiny icons** under 5KB
- **SVG files** (vector, no variants needed)

```blade
<!-- ✅ OK: Static theme asset -->
<img src="{{ theme_asset('assets/icons/arrow.svg') }}" alt="Arrow" class="w-4 h-4">

<!-- ❌ FORBIDDEN: User-uploaded image without srcset -->
<img src="{{ resolve_block_asset($block->image) }}" alt="...">

<!-- ✅ CORRECT: User-uploaded image with <x-image> -->
<x-image :src="resolve_block_asset($block->image)" alt="..." sizes="(max-width: 768px) 100vw, 50vw" />
```

> **Icon systems (dua sistem, jangan dicampur):** content icons (CPT meta, Page
> Builder blocks, tombol UI konten) memakai prefix `lucide:<name>` — contoh
> `<x-icon name="lucide:shield-check" class="w-5 h-5" />` atau
> `render_icon('lucide:shield-check', 'w-5 h-5')`. Icon **admin sidebar menu**
> memakai Material Symbols bare name (`build`, `dashboard`, `dynamic_form`) —
> lihat `sidebar-menu-system.md`. Jangan hardcode inline `<svg>` untuk icon
> konten.

### D. Image Quality Settings (Admin Panel)

These settings in Settings → Media control compression quality:

| Setting | Default | Description |
|---------|---------|-------------|
| `img_jpg_quality` | 85 | JPEG compression (1-100) |
| `img_webp_quality` | 80 | WebP compression (1-100) |
| `img_max_dimension` | 2560px | Max width/height for uploaded originals |
| `img_auto_webp` | true | Auto-generate WebP companions |
| `img_optimize_original` | true | Compress original on upload |

### E. Static-to-CMS Migration — Image Pipeline ⚠️ CRITICAL

> **RULE**: When integrating a static HTML site (Vite/Handlebars/Next.js/etc.) into a CMS theme, **content images** (hero banners, stock photos, section backgrounds) **MUST be uploaded through `MediaService`** — either via Admin Panel or via `php artisan media:import`. They MUST NOT be placed directly in `themes/{slug}/assets/`.

**Why this matters:**
- Images placed in `themes/{slug}/assets/` are treated as static build artifacts
- `GenerateImageVariants` job never runs on them → no sm/md/lg/xl variants
- `ResponsiveImageService` cannot find them in the `media` table → no srcset
- `<x-image>` renders them as a single-size `<img>` with no responsive delivery
- Mobile users download desktop-sized images (e.g., 143KB instead of 25KB)

**What goes WHERE:**

| Asset Type | Location | Rationale |
|------------|----------|-----------|
| Brand logos, icons, SVGs | `themes/{slug}/assets/` | Small, vector, no variants needed |
| CSS, JS, fonts | `themes/{slug}/assets/` | Build artifacts |
| Hero banners, stock photos | **Media Library** (`media:import`) | Need responsive variants |
| Section background images | **Media Library** (`media:import`) | Need responsive variants |
| Blog/content thumbnails | **Media Library** (`media:import`) | Need responsive variants |

**Standard import command (`media:import`):**

```bash
# Import a single image + assign to homepage block
php artisan media:import public/themes/cdt/assets/banner_hero.jpg \
    --page=home --block=hero_image

# Import entire directory of content images
php artisan media:import public/themes/cdt/assets/ --skip-existing

# Dry run to preview what will be imported
php artisan media:import public/themes/cdt/assets/ --dry-run
```

The command automatically:
1. Uploads through `MediaService` → compression + WebP conversion
2. Dispatches `GenerateImageVariants` → sm/md/lg/xl + WebP companions + LQIP
3. Creates `media` DB record with proper alt text (derived from filename)
4. Optionally assigns to a page block value (`--page` + `--block`)

**Telltale signs of violation** (audit checklist):
- ❌ Block fallback value contains `themes/{slug}/assets/photo-*.jpg`
- ❌ `resolve_block_asset()` returns a path starting with `themes/`
- ❌ `<x-image>` renders without `srcset` attribute in HTML output
- ✅ Block value contains `uploads/` or `media/` path → from Media Library


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

## 5. Theme Manifest (`theme.json`) & Form Assignments

Every theme **MUST** contain a `theme.json` manifest at its root directory. This manifest defines theme metadata, supported capabilities, page templates, block schemas, form placeholders, and archive settings.

### A. Manifest Schema (`theme.json`)

```json
{
    "name": "CDT Theme",
    "slug": "cdt",
    "version": "1.0.0",
    "description": "Official Central Data Technology (CDT) Enterprise Theme.",
    "author": "CTI Group",
    "screenshot": "screenshot.png",
    "supports": [
        "pages",
        "posts",
        "menus",
        "cpt",
        "forms"
    ],
    "form_placeholders": [
        {
            "key": "contact_form",
            "label": "Contact Form",
            "description": "Main contact form displayed on the Contact Us page and global Contact Section."
        },
        {
            "key": "consultation_form",
            "label": "Consultation Form",
            "description": "Consultation request form displayed on Technology Alliance and Tech Product single pages."
        },
        {
            "key": "newsletter_form",
            "label": "Newsletter Subscription Form",
            "description": "Newsletter subscription form displayed in the footer modal."
        }
    ],
    "page_templates": {
        "default": {
            "label": "Default",
            "description": "Standard page layout",
            "blocks": []
        },
        "home": {
            "label": "Homepage",
            "description": "CDT Main Homepage layout",
            "blocks": []
        }
    },
    "archive_settings": {
        "per_page": 12,
        "layout": "grid",
        "show_sidebar": false
    }
}
```

### B. Form Placeholders & Admin Assignments

1. **Placeholder Registration**: Form slots defined under `"form_placeholders"` in `theme.json` are automatically parsed by the Form Builder engine.
2. **Admin Assignments UI**: Admins manage form-to-placeholder mappings at `/ctrlpanel/forms/assignments` (or via Form Studio). Mappings are saved into site settings as `theme_{$theme->slug}_form_assignments`.
3. **Rendering Assigned Forms in Theme Blade Views**:
```blade
@php
    $theme = active_theme();
    $assignments = setting("theme_{$theme->slug}_form_assignments", []);
    $formId = $assignments['contact_form'] ?? null;
    $form = $formId ? \App\Models\Form::with('fields')->find($formId) : null;
@endphp

@if($form)
    @include('cdt::partials.tailwind-form', ['form' => $form, 'variant' => 'dark'])
@endif
```

### C. Publishing Theme Assets
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
