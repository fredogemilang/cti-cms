# Plugin Development Guidelines — String Translation Standards

Plugins extending CDT CMS must follow the **Centralized String Translation Registry System**. Custom plugins are prohibited from creating isolated translation tables or independent language files.

## 1. Using `t()` with Plugin Namespaces

Plugins should namespace their translation keys using dot-notation:

```php
// In Plugin Controllers or Views
$buttonText = t('plugin-sitekit.badge_label', 'Active Badge');
```

```blade
<!-- In Plugin Blade Templates -->
<div class="plugin-widget">
    <h3>{{ t('plugin-sitekit.widget_title', 'Analytics Widget') }}</h3>
    <button>{{ t('common.submit', 'Submit') }}</button>
</div>
```

## 2. Centralized Registry Architecture

- **No Custom Translation Tables**: Plugins MUST use the core `string_translation_keys`, `string_translations`, and `string_translation_sources` tables.
- **REST API Dictionary**: All plugin translations are automatically included in the public REST API endpoint `GET /api/v1/translations/{locale}`.
- **Cache Invalidation**: Saving a translation invalidates `translations:{locale}` cache automatically.
