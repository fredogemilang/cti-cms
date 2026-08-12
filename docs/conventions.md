# Conventions & Patterns

## Media Assets

### Golden Rule
**ALWAYS** use `resolve_block_asset($path, $variant)` — **NEVER** `asset('storage/' . $val)`.

### Why
If `$val` is a theme asset (`themes/cdt/assets/logo.webp`), hardcoding `storage/` creates a broken URL: `/storage/themes/cdt/assets/logo.webp` → 403 Forbidden.

### Storage Paths

| What | Stored As | Resolves To |
|------|-----------|-------------|
| Theme asset | `themes/cdt/assets/photo.webp` | `/themes/cdt/assets/photo.webp` |
| Upload | `media/photo-timestamp-hash.webp` | `/storage/media/photo-timestamp-hash.webp` |
| Full URL | `https://example.com/img.png` | returned as-is |

### DB Storage Rules
- **Never** store full domain URLs in DB (`http://cdt.devs/storage/...`)
- **Always** store relative paths: `media/photo.webp` or `themes/cdt/assets/logo.webp`

### Image Variants
Available sizes: `thumb`, `sm`, `md`, `lg`, `xl`
```blade
<img src="{{ resolve_block_asset($block->image, 'sm') }}" alt="{{ $block->title }}">
```

### Responsive Image Component
```blade
<x-image :src="$page->hero_image" alt="Hero" class="w-full h-auto rounded-2xl" />
```
Auto-renders `<picture>` with WebP sources, `srcset`, `sizes`, `loading="lazy"`, `decoding="async"`.

### Marquee Gallery Performance (G22, G23)
- Remove `overflow-hidden` + use `translate3d(0,0,0)` + `will-change: transform` + `backface-visibility: hidden`
- Use `loading="eager" decoding="async"` (NOT `loading="lazy"`) for marquee images
- Always pass variant `'sm'` to `resolve_block_asset()` for gallery
- Queue worker MUST be running or variants won't exist (G24)

---

## Scratch Scripts (Debug/Test)

Use `scratch/` directory for temporary PHP scripts:
```php
<?php // scratch/test-feature.php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Your test code here
$page = App\Models\Page::with('blocks')->find(1);
dd($page->toArray());
```

---

## Livewire Admin Forms

### Structure
```php
// app/Livewire/Admin/Pages/PageForm.php
#[Layout('layouts.admin')]
class PageForm extends Component
{
    use WithFileUploads;

    public ?Page $page = null;
    public array $blocks = [];
    public string $activeLocale;

    public function mount(?int $id = null) { /* load or create */ }
    public function switchLocale(string $locale) { /* snapshot → swap */ }
    public function save() { /* validate + persist */ }
    public function render() { return view('livewire.admin.pages.page-form'); }
}
```

### Media Picker Integration
- `MediaPicker` component for image selection
- Emits `media-selected` (single) or `media-selected-multiple` (gallery) events
- Parent form listens and updates field value
- Gallery auto-detected when field name starts with `gallery_`

### Repeater Pattern
- Repeater children defined in `options.repeater_fields` via API
- Children stored in `value` JSON column as array of row objects
- Each row contains key-value pairs matching child field names

---

## SEO Breadcrumbs

### Always use the component:
```blade
<x-seo-breadcrumbs :entity="$page" class="text-zinc-500 mb-8" />
<x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-10" />
```

### Rules:
1. **No manual `<nav>` tags** — use the component
2. Auto-generates JSON-LD `BreadcrumbList` in `<head>`
3. Respects `has_archive`, parent/child hierarchy
4. Localized home labels from SEO settings

---

## Form Engine & Multi-Language Rules

### 1. Form Multi-Language & Anti-Redundancy Rule
- Edit form translations directly on the `Form` model (`forms.translations` JSON) and `FormField` model (`form_fields.translations` JSON) via Form Studio (`/ctrlpanel/forms/{id}/studio`).
- **NEVER create duplicate `string_translations` (`t()`)** for form titles, descriptions, submit buttons, or field labels.

### 2. Mandatory Form Title & Description Confirmation Rule
- When generating or adding a form to a page, **DO NOT ASSUME** where the title and description come from.
- **ALWAYS confirm with the user first**:
  - Option A: Form Model (`$form->name` / `$form->description`)
  - Option B: Page Blocks / CPT Meta Fields
  - Option C: String Translations (`t()`)

### 3. Strict Form Assignment Compliance Rule
- All forms MUST strictly comply with Form Assignments (`/ctrlpanel/forms/assignments`).
- If a form is NOT assigned to a slot in `Setting::get("theme_{$theme->slug}_form_assignments")`, it **MUST NOT** render on the frontend.

---

## Branching Strategy

```
main                         ← Clean core (no client-specific code)
├── project/cdt              ← CDT deployment
├── project/ofis             ← OFIS deployment
└── project/{name}           ← New client deployments
```

- Core improvements → `main`
- Client-specific → `project/{name}`
- On project branches: **remove `/themes/{name}` from `.gitignore`** (G45)

---

## Deployment (cPanel)

1. Push to `project/{name}` branch
2. Pull on server
3. `chown -R {user}:{user} /home/{user}/{domain}/` (G45)
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan migrate --force`
6. `php artisan theme:publish {name}`
7. `php artisan storage:link`
8. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. Cron: `* * * * * php /home/{user}/{domain}/artisan schedule:run`
10. Supervisor for queue worker
