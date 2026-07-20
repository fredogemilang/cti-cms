# Theme Development Guide

Build themes to customize the frontend look and feel. Themes are self-contained Blade template packages with their own CSS, JS, and assets.

## Table of Contents
- [Quick Start](#quick-start)
- [Theme Structure](#theme-structure)
- [Theme Manifest](#theme-manifest)
- [Layout Template](#layout-template)
- [Page Templates](#page-templates)
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
    }
}
```

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

### Helpers

| Helper | Returns | Example |
|--------|---------|---------|
| `setting('key', 'default')` | Mixed | `setting('site_name', 'My Site')` |
| `$page->block('name')` | String/null | `$page->block('hero_title')` |
| `$block->localizedValue` | String | Locale-aware block value |
| `$block->getDecodedValue()` | Array/null | JSON-decoded value |
| `$block->getOption('key')` | String/null | Block metadata option |

---

## Tips & Best Practices

1. **Use CSS custom properties** for theming — makes it easy to customize colors
2. **Avoid external CSS frameworks** (Bootstrap, Tailwind) to keep themes lightweight
3. **Use `loading="lazy"`** on images for performance
4. **Always test responsive** — mobile traffic is 60%+ in most markets
5. **Include a `screenshot.png`** (800×600) for the admin theme picker
6. **Use `@stack` for page-specific assets** — don't load everything globally
7. **Reference `$activeTheme->slug`** instead of hardcoding your theme slug in `@extends` and `@include`

---

**Reference:** See `themes/default/` for a complete working theme.

**Last Updated:** 2026-07-20
**Version:** 2.0
