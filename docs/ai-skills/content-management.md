# CTI CMS — Content Management (AI Skill)

> This skill teaches AI how to create, read, update, and delete content in CTI CMS.

## Pages

### Data Structure
```json
{
  "title": "About Us",
  "slug": "about-us",
  "template": "default",
  "status": "published",
  "menu_order": 2,
  "parent_id": null,
  "translations": {
    "en": { "title": "About Us" },
    "id": { "title": "Tentang Kami" }
  },
  "blocks": [
    {
      "key": "hero_title",
      "type": "text",
      "value": "Welcome to Our Company",
      "sort_order": 1
    },
    {
      "key": "hero_image",
      "type": "image",
      "value": "uploads/hero-banner.webp",
      "sort_order": 2
    }
  ]
}
```

### Status Lifecycle
```
draft → published → trashed → (purged after 30 days)
draft → scheduled (published_at set) → published (auto by cron)
```

### Template System
Each page has a `template` field (default: `"default"`). Templates are defined in the active theme's `theme.json` under `page_templates`. Each template defines which blocks are available.

### Block Types

| Type | Value Format | Description |
|------|-------------|-------------|
| `text` | `"string"` | Single-line text |
| `textarea` | `"multi\nline"` | Multi-line plain text |
| `wysiwyg` | `"<p>HTML</p>"` | Rich text (HTML) |
| `image` | `"uploads/file.webp"` | Image path (from Media Library) |
| `gallery` | `["path1.webp", "path2.webp"]` | Array of image paths |
| `repeater` | `[{"title": "...", "desc": "..."}]` | Array of objects |
| `select` | `"option_value"` | Single select from predefined options |
| `checkbox` | `true` / `false` | Boolean toggle |
| `number` | `42` | Numeric value |
| `url` | `"https://..."` | URL string |
| `icon` | `"lucide:shield-check"` | Icon identifier |

### Translation Rules for Content
1. **Page title** → uses `HasTranslations` trait, stored in `translations` JSON column
2. **Block values** → stored per-locale in block `translations` JSON
3. **UI strings in templates** → use `t('key', 'Default')` helper
4. **NEVER** hardcode text in templates
5. **EN and ID MUST be in sync** — always provide both locales

## Custom Post Type (CPT) Entries

### Data Structure
```json
{
  "title": "Akamai Technologies",
  "slug": "akamai",
  "cpt_slug": "technology-alliance",
  "status": "published",
  "meta": {
    "subtitle": "Cloud Security & CDN",
    "logo": "uploads/akamai-logo.webp",
    "website_url": "https://akamai.com",
    "description": "<p>Akamai provides...</p>",
    "features": [
      { "title": "Web Security", "icon": "lucide:shield" },
      { "title": "CDN", "icon": "lucide:globe" }
    ]
  },
  "translations": {
    "en": {
      "title": "Akamai Technologies",
      "subtitle": "Cloud Security & CDN",
      "description": "<p>Akamai provides...</p>"
    },
    "id": {
      "title": "Akamai Technologies",
      "subtitle": "Keamanan Cloud & CDN",
      "description": "<p>Akamai menyediakan...</p>"
    }
  },
  "taxonomy_terms": [12, 15, 23]
}
```

### Meta Field Types
Same as block types above. Meta fields are defined per-CPT in `CptMetaField` model.

### Creating Content — Checklist
1. ✅ Check which CPT you're targeting and its available meta fields
2. ✅ Provide translations for ALL configured locales (en, id)
3. ✅ Use `MediaService` for image uploads — NEVER put raw file paths
4. ✅ Set status to `draft` first, then publish separately
5. ✅ Assign taxonomy terms if the CPT has taxonomies
6. ✅ SEO meta is optional but recommended (title, description, og_image)

### Publishing Rules
- Only users/tokens with `mcp.content.publish` ability can set status to `published`
- Without publish ability, content is created as `draft`
- Scheduled publishing: set `published_at` to a future datetime
