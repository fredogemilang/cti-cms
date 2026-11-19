# Filter & Hook System

## Overview

WordPress-style hook system that lets themes and plugins change core behavior without touching core files:

- `App\Support\Filter` — pass a value through callbacks and return the modified value (`apply_filters` / `add_filter`).
- `App\Support\Action` — run callbacks at a defined point in the lifecycle, no return value (`do_action` / `add_action`).

> Core (`app/`, `resources/`, `plugins/`) must never contain client names, CPT slugs or URL schemes of a specific site.
> Put that logic in the theme service provider and hook it in through the extension points below.

## API

```php
// Filters
Filter::add('hook_name', function ($value, ...$args) {
    return $modifiedValue;
}, priority: 10);

$result = Filter::apply('hook_name', $defaultValue, ...$args); // core calls this

// Actions
Action::add('hook_name', function (...$args) { /* side effects */ }, priority: 10);
Action::run('hook_name', ...$args); // core calls this

// Helper aliases
add_filter(...); apply_filters(...); add_action(...); do_action(...);
```

## Theme Service Provider

Convention (booted by `App\Services\ThemeLoader` for the **active** theme only):

```
themes/{slug}/
├── {Studly}ThemeServiceProvider.php   ← class Themes\{Studly}\{Studly}ThemeServiceProvider
└── src/                               ← Themes\{Studly}\* autoloaded at runtime (no composer.json entry)
    └── Http/Controllers/ShortUrlController.php
```

The provider's `register()` and `boot()` run during app boot, after plugin providers and **before** the catch-all
routes are built, so `Action::add('routes.before_catch_all', ...)` in `boot()` is registered in time.

## Core Filters

| Hook | Default | Args | Purpose |
|------|---------|------|---------|
| `cpt_entry.url` | core URL | `$entry, $locale` | Override entry URLs (short URLs) |
| `cpt_entry.url_redirect` | `null` | `$entry` | 301 to canonical URL |
| `cpt.archive_url` | core URL | `$postType, $locale` | Override archive URLs |
| `cpt_archive.per_page` | archive setting | `$postType` | Items per archive page |
| `cpt.hide_untranslated_entries` | `false` | `$postType` | Hide entries without content in current locale |
| `cpt_entry.form_meta` | `$meta` | `$entry, $postType` | Pre-fill admin entry form meta |
| `cpt_entry.sync_relationship_field` | `false` | `$field, $entry, $meta` | Return `true` to take over syncing a relationship field |
| `home.view_data` | `['page' => $page]` | `$page` | Extra data for `{theme}::pages.home` |
| `deferred_section.allowed` | `[]` | — | Sections served by `/_deferred/{section}` |
| `deferred_section.data` | `['page' => $page]` | `$section, $page` | Data for `{theme}::partials.deferred.{section}` |
| `breadcrumb.cpt_label` | translated plural label | `$cpt, $locale` | Breadcrumb label for a CPT |
| `search.highlight_class` | neutral yellow | — | CSS class of `<mark>` in search results |
| `admin_menu.default_sections` | core sections | — | Default sidebar section map |
| `admin_menu.default_order` | core order | — | Default sidebar order |
| `forms.attribution_cookie` | `cms_attribution` | — | Cookie read for form attribution data |

## Core Actions

| Hook | Args | Purpose |
|------|------|---------|
| `routes.before_catch_all` | `string $adminPath, string $localePattern` | Register theme URL schemes (e.g. `/{vendor}/{product}`) that must win over the page catch-all. `$localePattern` is a regex of non-default locales (or `nothing-to-match`). |

## Example: Theme Short URLs

```php
namespace Themes\Acme;

use App\Support\Action;
use App\Support\Filter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Themes\Acme\Http\Controllers\ShortUrlController;

class AcmeThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filter::add('cpt_entry.url', function ($url, $entry, $locale) {
            if ($entry->postType?->slug === 'solutions') {
                return localized_url('/'.$entry->slug, $locale);
            }

            return $url;
        });

        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            Route::get('/{vendor}/{product}', [ShortUrlController::class, 'product'])
                ->where('vendor', '^(?!'.$adminPath.'|api)[a-z0-9-]+$')
                ->name('acme.product.single');
        });
    }
}
```

## Priority System

Lower number = earlier execution (same as WordPress). Default priority: 10.

## Key Files

| File | Purpose |
|------|---------|
| `app/Support/Filter.php` | Filter registry |
| `app/Support/Action.php` | Action registry |
| `app/Services/ThemeLoader.php` | Boots theme provider + `src/` autoloader |
| `tests/Feature/CoreExtensionHooksTest.php` | Coverage for route/home/deferred hooks |

## When to Use

- ✅ Theme needs to modify core URL generation or routes
- ✅ Theme needs extra view data for home/deferred sections
- ✅ Plugin needs to add items to admin menu
- ❌ Core features (belong in `app/`)
- ❌ Simple config changes (use `setting()`)
