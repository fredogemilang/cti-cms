# Custom Post Type (CPT) System

## Models

| Model | Table | Purpose |
|-------|-------|---------|
| `CustomPostType` | `custom_post_types` | Schema: slug, labels, settings (meta_boxes), has_archive |
| `MetaField` | `meta_fields` | Field definition: name (snake_case), type, label, field_group, options (JSON) |
| `CptEntry` | `cpt_entries` | Content: title, slug, meta (JSON), translations (JSON), status, SoftDeletes |
| `CptEntryRelationship` | `cpt_entry_relationships` | Entry-to-entry pivot |
| `CptEntryRevision` | `cpt_entry_revisions` | Meta snapshot history |
| `CustomTaxonomy` | `custom_taxonomies` | Taxonomy schema |
| `TaxonomyTerm` | `taxonomy_terms` | Taxonomy terms |

## MetaField Types

`text`, `textarea`, `wysiwyg`, `number`, `email`, `url`, `tel`, `date`, `datetime`, `time`, `select`, `radio`, `checkbox`, `switcher`, `media`, `gallery`, `color`, `icon`, `code`, `range`, `repeater`

## MetaBox & field_group

`$cpt->settings['meta_boxes']` defines tabs:
```json
[{"id": "hero", "title": "Hero", "context": "normal"}, ...]
```

Each `MetaField.field_group` → MUST match an active tab ID in `$cpt->settings['meta_boxes']`.

**Strict Rule:** When adding a new field group:
1. Register `{"id": "box_id", "title": "Box Title", "context": "normal"}` in `$cpt->settings['meta_boxes']`
2. Set `MetaField.field_group = "box_id"`

**Gotcha:** Unmapped fields (`field_group = null` or empty) fall into unorganized "CUSTOM FIELDS" fallback box.

## Entry Meta Storage

```json
{
  "hero_title": "Cloud Security",
  "hero_image": "uploads/2026/07/banner.jpg",
  "solutions_featured": [{"name": "...", "icon": "lucide:shield"}],
  "_translations": {
    "id": {
      "hero_title": "Keamanan Cloud",
      "hero_image": "uploads/2026/07/banner-id.jpg"
    }
  }
}
```

## Accessing Meta

```php
$entry->getMeta('hero_title');                    // auto-locale
$entry->getMeta('hero_title', locale: 'id');      // specific locale
$entry->meta['hero_title'];                       // raw (no translation lookup)
```

## Entry URLs

```
/{locale}/{post_type_slug}/{entry_slug}    ← default
/{entry_slug}                              ← via cpt_entry.url filter hook
```

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/CustomPostType.php` | CPT schema model |
| `app/Models/CptEntry.php` | Entry model with HasTranslations |
| `app/Models/MetaField.php` | Field definition model |
| `app/Livewire/Admin/Cpt/CptForm.php` | Main CPT editor |
| `app/Livewire/Admin/Cpt/EntryForm.php` | Entry editor with meta fields |
