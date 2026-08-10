# Page Builder

## How It Works

- `Page` model has many `PageBlock` (ordered, hierarchical via `parent_block_id`)
- Blocks defined in `themes/cdt/theme.json` under `templates.{name}.blocks[]`
- Edited via `PageForm` Livewire component (`app/Livewire/Admin/Pages/PageForm.php`)
- Locale switching: `switchLocale()` snapshots current form → loads target locale data

## Block Types

**Translatable:** `text`, `textarea`, `wysiwyg`, `code`, `markdown`

**Atomic (not translatable):** `number`, `date`, `media`, `switcher`, `select`, `radio`, `checkbox`, `gallery`, `posts`, `color`, `icon`, `url`, `email`

**Special:** `repeater` — has children blocks + value is JSON array of rows

## Translation Flow

1. Default locale values stored in `value` column
2. Non-default locales stored in `translations` JSON: `{locale: {value: "..."}}`
3. `switchLocale()` in PageForm: snapshot → swap → restore on save
4. Repeaters: entire rows array stored per locale

## Template Schema (theme.json)

```json
{
  "templates": {
    "home": {
      "label": "Home Page",
      "blocks": [
        {"name": "hero_prefix", "type": "text", "label": "Hero Prefix", "default": "Speed Up Your"},
        {"name": "expertise_list", "type": "repeater", "label": "Cards", "children": [
          {"name": "image", "type": "media"},
          {"name": "title", "type": "text"},
          {"name": "description", "type": "textarea"}
        ]}
      ]
    }
  }
}
```

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/Page.php` | Page model with HasTranslations, HasSeoMeta |
| `app/Models/PageBlock.php` | Block model with translations JSON |
| `app/Livewire/Admin/Pages/PageForm.php` | Main page editor Livewire component |
| `app/Livewire/Admin/Pages/PageList.php` | Page index listing |

## Rendering Blocks in Theme

```blade
@foreach($page->blocks as $block)
    <x-page-block :block="$block" />
@endforeach
```

Use `resolve_block_asset($block->value, 'sm')` for media blocks — never `asset('storage/')`.
