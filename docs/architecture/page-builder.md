# Page Builder

## How It Works

- `Page` model has many `PageBlock` (ordered, hierarchical via `parent_block_id`)
- Blocks defined in `themes/cdt/theme.json` under `templates.{name}.blocks[]`
- Edited via `PageForm` Livewire component (`app/Livewire/Admin/Pages/PageForm.php`)
- Locale switching: `switchLocale()` snapshots current form → loads target locale data

## Block Types

**Translatable:** `text`, `textarea`, `wysiwyg`, `code`, `markdown`, `button`, `title`, `card`

**Atomic (not translatable):** `number`, `date`, `media`, `switcher`, `select`, `radio`, `checkbox`, `gallery`, `posts`, `color`, `icon`, `url`, `email`

**Special:**
- `button`: Compound block combining text and URL in one editor card (value: `{"text":"...","url":"...","target":"_self"}`). Access via `$page->buttonBlock('name')`.
- `title`: Compound block combining optional prefix and main title in one editor card (value: `{"prefix":"...","main":"..."}`). Access via `$page->titleBlock('name')`.
- `card`: Compound block combining title, visual asset (Image/Icon), description (Text/Listing/WYSIWYG), and button (text/url/target) in one editor card. Access via `$page->cardBlock('name')`.

## Translation Flow

1. Default locale values stored in `value` column
2. Non-default locales stored in `translations` JSON: `{locale: {value: "..."}}`
3. `switchLocale()` in PageForm: snapshot → swap → restore on save
4. Repeaters & Compound blocks (`button`, `title`, `card`): entire payload array stored per locale

## Template Schema (theme.json)

```json
{
  "templates": {
    "home": {
      "label": "Home Page",
      "blocks": [
        {"name": "hero_title", "type": "title", "label": "Hero Title", "default": {"prefix": "Speed Up Your", "main": "Transformation Journey"}},
        {"name": "hero_cta", "type": "button", "label": "Hero Button", "default": {"text": "Learn More", "url": "#areas-of-expertise"}},
        {"name": "blog_callout", "type": "card", "label": "Blog Callout", "default": {"title": "Blog, News & Video", "asset_type": "image", "image": "...", "icon": "lucide:sparkles", "description_type": "text", "description": "...", "button_text": "Explore", "button_url": "/insights"}},
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

### Compound Block Helpers

```php
// Title Block (prefix + main)
$heroTitle = $page->titleBlock('hero_title', ['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey']);
// Returns: ['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey']

// Button Block (text + url + target)
$heroCta = $page->buttonBlock('hero_cta', ['text' => 'Learn More', 'url' => '#areas-of-expertise']);
// Returns: ['text' => 'Learn More', 'url' => '#areas-of-expertise', 'target' => '_self']

// Card Block (title + visual asset + description format + button)
$blogCard = $page->cardBlock('blog_callout', ['title' => 'Blog, News & Video', 'button_text' => 'Explore', 'button_url' => '/insights']);
// Returns: [
//   'title' => 'Blog, News & Video',
//   'asset_type' => 'image', // 'image' or 'icon'
//   'image' => '...',
//   'icon' => 'lucide:sparkles',
//   'description_type' => 'text', // 'text', 'listing', or 'wysiwyg'
//   'description' => '...',
//   'list_icon' => 'lucide:check-circle',
//   'list_items' => "...",
//   'wysiwyg_content' => "...",
//   'button_text' => 'Explore',
//   'button_url' => '/insights',
//   'button_target' => '_self'
// ]
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
