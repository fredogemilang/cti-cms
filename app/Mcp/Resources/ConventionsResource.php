<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('cms://conventions')]
#[MimeType('text/markdown')]
#[Description('CMS coding conventions and strict rules — translation system, asset pipeline, architecture boundaries, plugin safety, and naming conventions. READ THIS to avoid violating CMS rules.')]
class ConventionsResource extends Resource
{
    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.connect');

        $output = <<<'MD'
# CTI CMS — Coding Conventions & Strict Rules

## Translation System

### Model-Level (HasTranslations)
- Content translations stored in `translations` JSON column
- Use `$model->getTranslation('field', 'locale')`
- Always provide both EN and ID

### String-Level (t() helper)
- UI strings: `t('namespace.key', 'Default English')`
- Keys: `common.*` (shared), `{theme}.*` (theme-specific), `plugin-{slug}.*` (plugin)
- **NEVER** hardcode text in Blade templates

## Asset Rules

| Rule | Correct | Wrong |
|------|---------|-------|
| Content images | `<x-image :src="$path" />` | `<img src="{{ $path }}">` |
| Block assets | `resolve_block_asset($path, 'lg')` | `asset('storage/' . $path)` |
| Theme assets | `theme_asset('assets/css/style.css')` | `asset('themes/...')` |
| External libs | Download → `theme_asset()` | CDN URLs |

## Architecture Boundary

| Layer | Contains | Never Contains |
|-------|----------|----------------|
| `app/` (Core) | Generic CMS: Pages, CPT, Media, Auth, SEO | Client code, theme logic |
| `themes/` | Blade views, CSS, JS, static assets | Business logic, models |
| `plugins/` | Domain features, models, providers | Theme templates, core overrides |

## Naming Conventions

| Entity | Convention | Example |
|--------|-----------|---------|
| CPT slug | `kebab-case` | `technology-alliance` |
| Meta field key | `snake_case` | `hero_image` |
| Template name | `kebab-case` | `home`, `about-us` |
| Translation key | `dot.separated` | `cdt.hero_title` |
| Blade partial | `kebab-case` | `hero-section.blade.php` |

## Plugin Safety

```php
// Always check plugin is active before using its models
if (is_plugin_active('posts')) {
    $posts = \Plugins\Posts\Models\Post::published()->get();
}
```

## Image Processing

```php
// CORRECT: Upload through MediaService
$media = app(MediaService::class)->upload($file);
$path = $media->path; // Store this in database

// WRONG: Direct file storage
Storage::put('uploads/image.jpg', $file); // NO! No variants generated
```

## SEO

- One `<h1>` per page
- Use `<x-seo-breadcrumbs>` component
- Set `meta_title` (50-60 chars) and `meta_description` (150-160 chars)
- Never skip heading levels (h1 > h2 > h3)
MD;

        return Response::text($output);
    }
}
