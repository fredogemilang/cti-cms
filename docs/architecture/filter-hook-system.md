# Filter & Hook System

## Overview

WordPress-style filter/hook system via `App\Support\Filter`. Allows themes and plugins to modify core behavior without touching core files.

## API

```php
// Register a filter
Filter::add('hook_name', function($value, ...$args) {
    return $modifiedValue;
}, priority: 10);

// Apply a filter (core code calls this)
$result = Filter::apply('hook_name', $defaultValue, ...$args);
```

## Active Hooks

| Hook | Purpose | Registered By |
|------|---------|---------------|
| `cpt_entry.url` | Override entry URLs (shorten paths) | Theme ServiceProvider |
| `cpt_entry.url_redirect` | 301 redirects for old URLs | Theme ServiceProvider |
| `cpt.archive_url` | Override archive page URLs | Theme ServiceProvider |
| `sitemap.entries` | Filter entries in sitemap | Theme / Plugin |
| `menu.items` | Modify menu items before render | Theme ServiceProvider |

## Example: URL Shortening

```php
// In ThemeServiceProvider::boot()
Filter::add('cpt_entry.url', function($url, $entry, $locale) {
    // Shorten /solutions to /
    if ($entry->post_type->slug === 'solutions') {
        return url($locale ? "{$locale}/{$entry->slug}" : $entry->slug);
    }
    return $url;
}, priority: 10);
```

## Example: Adding Data to Sitemap

```php
Filter::add('sitemap.entries', function($entries, $type) {
    if ($type === 'pages') {
        // Add custom entries or remove specific pages
    }
    return $entries;
});
```

## Priority System

Lower number = earlier execution (same as WordPress).
Default priority: 10.

## Key File

| File | Purpose |
|------|---------|
| `app/Support/Filter.php` | Filter/hook registry |

## When to Use

- ✅ Theme needs to modify core URL generation
- ✅ Plugin needs to add items to admin menu
- ✅ Theme needs to filter sitemap entries
- ❌ Core features (belong in `app/`)
- ❌ Simple config changes (use `setting()`)
