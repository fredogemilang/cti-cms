# CTI CMS — Media Pipeline (AI Skill)

> This skill explains how the media/image system works in CTI CMS.

## Upload Flow

```
User Upload → MediaService::upload()
    → Validate (type, size)
    → Compress original (if img_optimize_original enabled)
    → Store to disk (default: public/uploads/)
    → Create Media DB record
    → Dispatch GenerateImageVariants job
        → Generate sm (480px), md (768px), lg (1280px), xl (1920px), thumb (150px)
        → Generate WebP companions for each variant
        → Generate LQIP (16px blur, base64)
        → Update Media.variants JSON column
```

## Media Model

```json
{
  "id": 42,
  "filename": "hero-banner.jpg",
  "path": "uploads/hero-banner.jpg",
  "alt_text": "Hero Banner",
  "mime_type": "image/jpeg",
  "size": 245000,
  "width": 1920,
  "height": 1080,
  "disk": "public",
  "variants": {
    "thumb": "uploads/thumb/hero-banner.webp",
    "sm": "uploads/sm/hero-banner.webp",
    "md": "uploads/md/hero-banner.webp",
    "lg": "uploads/lg/hero-banner.webp",
    "xl": "uploads/xl/hero-banner.webp",
    "lqip": "data:image/webp;base64,UklGR..."
  }
}
```

## Rendering in Templates

### MANDATORY: `<x-image>` Component
```blade
{{-- Basic usage --}}
<x-image :src="$page->hero_image" alt="Hero" class="w-full h-auto" />

{{-- With sizes for performance --}}
<x-image :src="$entry->featured_image" alt="{{ $entry->title }}"
    sizes="(max-width: 768px) 100vw, 50vw" />

{{-- With Media model directly --}}
<x-image :media="$mediaModel" alt="{{ $mediaModel->alt_text }}" />
```

### Asset Resolution
```blade
{{-- For block/content images --}}
{{ resolve_block_asset($block->image, 'lg') }}

{{-- For static theme assets --}}
{{ theme_asset('assets/icons/logo.svg') }}
```

## Settings (Admin → Settings → Media)

| Setting | Default | Description |
|---------|---------|-------------|
| `img_jpg_quality` | 85 | JPEG compression (1-100) |
| `img_webp_quality` | 80 | WebP compression (1-100) |
| `img_max_dimension` | 2560 | Max width/height on upload |
| `img_auto_webp` | true | Auto-generate WebP |
| `img_optimize_original` | true | Compress original on upload |

## Import Command
```bash
# Import single image
php artisan media:import path/to/image.jpg --page=home --block=hero_image

# Import directory
php artisan media:import path/to/images/ --skip-existing

# Dry run
php artisan media:import path/to/images/ --dry-run
```

## Rules (MANDATORY)

1. ❌ NEVER put content images in `themes/{slug}/assets/` — use Media Library
2. ❌ NEVER use raw `<img>` for user-uploaded images — use `<x-image>`
3. ❌ NEVER use `asset('storage/')` — use `resolve_block_asset()`
4. ❌ NEVER store external URLs in database — download and upload through MediaService
5. ✅ ALWAYS use `<x-image>` with `sizes` attribute for responsive delivery
6. ✅ ALWAYS upload through `MediaService` for variant generation
7. ✅ Static theme assets (icons, logos, SVGs) go in `themes/{slug}/assets/`
