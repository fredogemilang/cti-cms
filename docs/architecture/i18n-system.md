# i18n & Localization

## Architecture Overview

Three independent but complementary translation systems:

### 1. Model-Level Translations (JSON column)

**Trait:** `HasTranslations` on `Page`, `CptEntry`, `PageBlock`, `FormField`

```php
protected array $translatable = ['title', 'slug', 'content', 'excerpt'];
```

- Default locale → main DB columns (`title`, `slug`)
- Other locales → `translations` JSON: `{"id": {"title": "Beranda", "slug": "beranda"}}`
- Access: `$model->getTranslation('title', 'id', fallback: true)`
- Mutate: `$model->setTranslation('title', 'id', 'Beranda')`

### 2. String Translation Registry (DB tables)

**Tables:** `string_translation_keys`, `string_translations`, `string_translation_sources`

**Helper:** `t('group.key', 'Default Value', ['param' => $val])`

- Centralized for themes & plugins — no isolated translation files
- Scanner: "Scan Website Strings" action in admin auto-discovers `t()` calls
- Fallback chain: Requested Locale → Fallback Locale → default_value → key

**Theme usage:**
```blade
<h2>{{ t('akamai.benefits_title', 'Benefits of') }} {{ $entry->title }}</h2>
<a href="#contact">{{ t('common.talk_to_experts', 'Talk to Our Experts') }}</a>
```

### 3. CptEntry Meta Translations

- Stored in `meta._translations.{locale}` within the `meta` JSON column
- Used for CPT custom field values (hero_title, banner_description, etc.)
- Access via `$entry->getMeta('hero_title')` — auto-locale

## Locale Routing

```
/en/page-slug     ← English
/id/page-slug     ← Indonesian
/page-slug        ← Default locale (no prefix)
```

- `SetLocale` middleware detects locale from URL prefix / query / session / cookie
- Admin locale switcher stores preference in session

## Adding a New Language

1. Add locale to `config/app.php` `available_locales`
2. Add locale to `SeoGeneralSettings` locale list
3. Translate strings via admin `/ctrlpanel/settings/string-translations`
4. Translate model content via admin forms (Page Builder / CPT editor)

## Key Files

| File | Purpose |
|------|---------|
| `app/Traits/HasTranslations.php` | Model translation trait |
| `app/Http/Middleware/SetLocale.php` | Locale detection middleware |
| `app/helpers.php` | `t()` helper function |
| `app/Models/StringTranslationKey.php` | String registry model |
