# Settings System

## Overview

Centralized key-value configuration stored in `settings` table with in-memory per-request cache. Settings are organized into groups with field schemas, validation, and admin UI.

## Model

`Setting` model:
- `key` — unique setting key (e.g. `site_name`, `site_logo`)
- `value` — stored as JSON or string
- `group` — settings group (e.g. `general`, `seo`, `auth`)
- `type` — value type: `string`, `integer`, `boolean`, `json`, `image`, `color`
- `label` — human-readable label for admin UI
- `rules` — validation rules

## Helper

```php
$name = setting('site_name', 'My CMS');        // with default
$logo = setting('site_logo');                    // without default
$items = setting('homepage_hero_items', []);     // JSON array

// Theme-specific
$assignments = setting("theme_{$theme->slug}_form_assignments", []);
```

## Cache Behavior

1. First call → loaded from DB, cached in memory
2. Subsequent calls → from memory (no DB query)
3. Settings updated → cache cleared for that group
4. Next request → fresh load from DB

## Admin UI

- Groups rendered as tabbed settings pages at `/ctrlpanel/settings/{group}`
- Each group defined by a Livewire component (e.g. `SeoGeneralSettings`, `AuthSettings`)
- Field types auto-render appropriate inputs (text, toggle, image upload, color picker, etc.)
- Save triggers cache clear for the group

## Settings Groups

| Group | Component | Key Settings |
|-------|-----------|-------------|
| `general` | `GeneralSettings` | site_name, site_tagline, site_logo, site_favicon, available_locales |
| `seo` | `SeoGeneralSettings` | default_meta_title, default_meta_description, og_image, breadcrumb_settings |
| `auth` | `AuthSettings` | enforce_2fa, session_lifetime, password_policy |
| `cache` | `CacheSettings` | page_cache_ttl, enable_page_cache |
| `forms` | `FormSettings` | recaptcha_enabled, turnstile_enabled, spam_protection |
| `icons` | `IconLibrariesSettings` | active icon libraries |

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/Setting.php` | Setting model |
| `app/Services/SettingsRegistry.php` | Group & field registration |
| `app/helpers.php` | `setting()` helper |
