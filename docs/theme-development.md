# Theme Development Guidelines — String Translation Standards

All themes developed for CDT CMS must adhere to the **Centralized String Translation Registry System**.

## 1. Using the Universal `t()` Helper

In Blade views, never hardcode text or write locale conditional checks (`if (app()->getLocale() === 'id')`). Always use the universal `t()` helper:

```blade
<!-- Good: Semantic Key with Default Fallback Value -->
<h2 class="title">{{ t('akamai.benefits_title', 'Benefits of') }} {{ $entry->title }}</h2>

<!-- Good: Buttons & CTAs -->
<a href="#contact" class="btn">{{ t('common.talk_to_experts', 'Talk to Our Experts') }}</a>

<!-- Good: Placeholders & Variables -->
<p>{{ t('auth.welcome_message', 'Welcome back, :name', ['name' => $user->name]) }}</p>
```

## 2. Key Naming Conventions

Keys must use **semantic dot-notation**:
- `common.save`
- `header.search_placeholder`
- `footer.copyright`
- `product.benefits_heading`

The first segment is automatically parsed as the `group` (e.g. `common`, `header`, `footer`, `product`), and the rest is stored as `key`.

## 3. Auto-Discovery & Admin Management

When developing themes, simply write `t('group.key', 'Default')` calls in your Blade files. 
Admin users can click **"Scan Website Strings"** in `/ctrlpanel/settings/string-translations` to automatically discover all theme strings and manage translations from the dashboard.
