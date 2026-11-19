# CTI CMS — Translation System (AI Skill)

> This skill explains the dual translation system in CTI CMS.
> AI agents MUST understand both systems before creating/editing content.

## System 1: Model-Level Translations (`HasTranslations` trait)

For **content fields** that need to be translated (titles, descriptions, meta values).

### How It Works
Models using `HasTranslations` trait store translations in a `translations` JSON column:

```json
{
  "en": { "title": "About Us", "description": "We are..." },
  "id": { "title": "Tentang Kami", "description": "Kami adalah..." }
}
```

### API
```php
// Get translation
$entry->getTranslation('title', 'en');  // "About Us"
$entry->getTranslation('title', 'id');  // "Tentang Kami"
$entry->getTranslation('title');        // Uses current locale

// Set translation
$entry->setTranslation('title', 'id', 'Judul Baru');

// Bulk set
$entry->setTranslations('title', ['en' => 'New Title', 'id' => 'Judul Baru']);
```

### Which Models Use It
- `Page` (title)
- `CptEntry` (title + translatable meta fields)
- `TaxonomyTerm` (name)

## System 2: String Translations (`t()` helper)

For **UI strings** in Blade templates — buttons, labels, headings, navigation text.

### How It Works
Strings are stored in two tables:
- `string_translation_keys`: `{ id, key, default_value, group, ... }`
- `string_translations`: `{ id, key_id, locale, value }`

### API
```php
// In PHP
t('common.read_more', 'Read More');
t('cdt.hero_subtitle', 'Digital Transformation', ['year' => 2025]);

// In Blade
{{ t('common.contact_us', 'Contact Us') }}
{{ t('auth.welcome', 'Welcome, :name', ['name' => $user->name]) }}
```

### Key Naming Convention
- `common.*` — shared across all themes (buttons, labels)
- `{theme-slug}.*` — theme-specific strings (e.g., `cdt.hero_title`)
- `plugin-{slug}.*` — plugin strings (e.g., `plugin-posts.save_button`)

### Auto-Discovery
Admin can click **"Scan Website Strings"** in `/ctrlpanel/settings/string-translations` to automatically find all `t()` calls in Blade views and register them.

### REST API
All translations available at `GET /api/v1/translations/{locale}` for headless consumers.

## Rules (MANDATORY)

1. **NEVER** hardcode text in Blade templates — always use `t()`
2. **NEVER** create custom translation tables in plugins — use the core system
3. **ALWAYS** provide both EN and ID translations when creating content
4. **ALWAYS** use dot-notation keys with appropriate namespace prefix
5. **ALWAYS** provide a sensible English default as the second `t()` argument
6. **NEVER** use `if (app()->getLocale() === 'id')` conditionals — use `t()` instead
