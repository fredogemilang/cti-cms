# CTI CMS — CPT System (AI Skill)

> This skill teaches AI how the Custom Post Type system works in CTI CMS.

## What is a CPT?

A Custom Post Type (CPT) is a user-defined content type with its own fields,
URL pattern, archive page, and taxonomy. Examples: `technology-alliance`,
`industry`, `service`, `package`.

## CPT Definition Schema

```json
{
  "name": "Technology Alliance",
  "slug": "technology-alliance",
  "description": "Technology partner and alliance listing",
  "is_active": true,
  "has_archive": true,
  "icon": "handshake",
  "meta_fields": [
    {
      "key": "subtitle",
      "label": "Subtitle",
      "type": "text",
      "is_required": false,
      "is_translatable": true,
      "sort_order": 1
    },
    {
      "key": "logo",
      "label": "Partner Logo",
      "type": "image",
      "is_required": true,
      "is_translatable": false,
      "sort_order": 2
    },
    {
      "key": "features",
      "label": "Features List",
      "type": "repeater",
      "is_required": false,
      "is_translatable": true,
      "options": {
        "fields": [
          { "key": "title", "type": "text", "label": "Feature Title" },
          { "key": "icon", "type": "icon", "label": "Feature Icon" },
          { "key": "description", "type": "textarea", "label": "Description" }
        ]
      },
      "sort_order": 3
    }
  ],
  "taxonomies": [
    {
      "name": "Category",
      "slug": "alliance-category",
      "is_hierarchical": true
    }
  ]
}
```

## Template Resolution

For a CPT with slug `technology-alliance`:

1. **Single**: `themes/{theme}/views/single-technology-alliance.blade.php`
   - Fallback: `themes/{theme}/views/single.blade.php`
2. **Archive**: `themes/{theme}/views/archive-technology-alliance.blade.php`
   - Fallback: `themes/{theme}/views/archive.blade.php`
3. **URL pattern**:
   - Archive: `/{cpt-slug}` → `/technology-alliance`
   - Single: `/{entry-slug}` → `/akamai`

## CptEntry Meta Storage

The `meta` JSON column stores key-value pairs matching the CPT's meta fields:

```php
// Reading
$entry->meta['subtitle'];           // Direct access
$entry->getMetaValue('subtitle');    // Via helper (with null safety)

// Writing
$entry->meta = array_merge($entry->meta, ['subtitle' => 'New Value']);
$entry->save();
```

## Relationships

```
CustomPostType
  ├── hasMany CptMetaField     (field definitions)
  ├── hasMany CptEntry         (content entries)
  └── hasMany Taxonomy
        └── hasMany TaxonomyTerm
              └── belongsToMany CptEntry (pivot: cpt_entry_taxonomy_term)
```

## Creating a CPT — Step by Step

1. **Define the CPT** — name, slug, description, has_archive, icon
2. **Add Meta Fields** — define each field with type, validation, sort order
3. **Add Taxonomies** (optional) — categories, tags for the CPT
4. **Create Theme Templates** — single and archive Blade views
5. **Create Entries** — populate with content and assign taxonomy terms

## Important Rules

1. **Slug immutability**: Once a CPT slug is set and entries exist, changing it breaks URLs. Warn before modification.
2. **Meta field key convention**: Use `snake_case` (e.g., `hero_image`, `website_url`)
3. **Translatable fields**: Only fields marked `is_translatable: true` have translations in the `translations` JSON column
4. **Repeater fields**: Stored as JSON arrays in `meta`. Each item has the sub-field keys defined in `options.fields`
5. **Image fields**: Store relative paths from Media Library (e.g., `uploads/image.webp`), NEVER absolute URLs
