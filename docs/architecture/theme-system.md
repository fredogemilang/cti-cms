# Theme System

## Structure

```
themes/cdt/
├── theme.json              ← Config: templates, blocks, form_placeholders, menu_locations
├── views/
│   ├── layouts/app.blade.php        ← Main frontend layout
│   ├── pages/home.blade.php         ← Homepage template
│   ├── partials/
│   │   ├── header.blade.php         ← Navigation
│   │   ├── footer.blade.php         ← Footer
│   │   └── tailwind-form.blade.php  ← Reusable form renderer
│   ├── single-{cpt-slug}.blade.php  ← CPT single templates
│   ├── archive-{cpt-slug}.blade.php ← CPT archive templates
│   └── ...
└── assets/                 ← Published to public/themes/cdt/assets/
```

## Key Services

| Service | Purpose |
|---------|---------|
| `ThemeLoader` | Discovers & activates themes from `themes/` directory |
| `active_theme()` | Helper — returns active Theme model |
| `theme_view($path)` | Resolves view path with theme namespace override |
| `theme_asset($path)` | Resolves asset URL from published theme assets |
| `resolve_block_asset($path, $variant)` | Resolves media path with variant support |
| `php artisan theme:publish {slug}` | Publishes theme assets to `public/themes/{slug}/` |

## theme.json Manifest

```json
{
    "name": "CDT Theme",
    "slug": "cdt",
    "version": "1.0.0",
    "supports": ["pages", "posts", "menus", "cpt", "forms"],
    "form_placeholders": [
        {"key": "contact_form", "label": "Contact Form"},
        {"key": "newsletter_form", "label": "Newsletter Subscription"}
    ],
    "page_templates": {
        "default": {"label": "Default", "blocks": []},
        "home": {"label": "Homepage", "blocks": [...]}
    },
    "menu_locations": {
        "header": "Header Navigation",
        "footer": "Footer Navigation"
    },
    "archive_settings": {
        "per_page": 12,
        "layout": "grid"
    }
}
```

## Asset Resolution

**Golden Rule:** ALWAYS use `resolve_block_asset($path)` — NEVER `asset('storage/' . $val)`

| Stored As | Resolves To |
|-----------|-------------|
| `themes/cdt/assets/photo.webp` | `/themes/cdt/assets/photo.webp` |
| `media/photo-timestamp-hash.webp` | `/storage/media/photo-timestamp-hash.webp` |
| `https://example.com/img.png` | returned as-is |

**DB Storage:** Always store relative paths, never full domain URLs.

## Publishing

```bash
php artisan theme:publish cdt          # Single theme
php artisan theme:publish --all         # All themes
```

`themes/{slug}/assets/` → `public/themes/{slug}/assets/`

## View Namespaces

- Theme views accessed via namespace: `cdt::pages.home`, `cdt::partials.header`
- Include: `@include('cdt::partials.contact-section')`
- `theme_view('pages.home')` helper resolves active theme automatically
