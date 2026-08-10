# Gotchas & Lessons Learned

> Append-only. Format: `G{id}. Title (Date)`. Latest at bottom.

---

## G1. SoftDelete ≠ Slug Available (2026-07-28)
Forms use SoftDeletes. Deleted form's slug stays occupied. Use unique slug or force-delete.

## G2. render_theme_form() = Bootstrap (2026-07-28)
Default `Form::renderForm()` outputs Bootstrap classes. CDT theme uses Tailwind → must use `tailwind-form.blade.php` partial.

## G3. HTML in Alpine x-data = SyntaxError (2026-07-31)
Never embed HTML tags inside JavaScript strings in `x-data` attributes. Livewire's `safeAsyncFunction()` parser crashes on `<`, `>`, `"`. Use plain text defaults.

## G4. MetaField field_group Must Match MetaBox (2026-07-31)
New MetaField must have `field_group` matching an active MetaBox tab ID in `$cpt->settings['meta_boxes']`, or it won't render in admin form.

## G5. Repeater Subfield Key Casing (2026-07-31)
Static site uses camelCase (`videoId`), CPT uses snake_case (`video_id`). `EntryForm::loadEntry()` normalizes automatically.

## G6. page_blocks Unique Constraint Too Restrictive (2026-08-01)
Old: `unique(page_id, name)`. Broke when multiple repeaters had children named `title`. **Fix:** Dropped the constraint. App-level validation handles top-level uniqueness.

## G7. Settings Deduplication: Logo & Favicon (2026-08-01)
`site_logo` and `site_favicon` were in BOTH General Settings and SEO Settings. Both wrote to the same key — last save wins. **Fix:** Removed from SEO Settings, kept in General Settings only. SEO page now shows info note with link to General Settings.

## G8. Footer/Nav Heading Tags Pollute SEO Outline (2026-08-01)
Footer `<h4>` and nav `<h3>` tags appear in document outline. **Fix:** Replace with `<span>` — visual unchanged, SEO clean.

## G9. Form Consent Text Translation (2026-08-01)
`consent_text` field in FormField supports HTML links. Translation stored in `form_fields.translations` JSON: `{"id": {"consent_text": "..."}}`. Studio UI has dedicated textarea for each locale.

## G10. CSRF Required for /ctrlpanel/* (2026-07-28)
All web endpoints need CSRF token from `<meta name="csrf-token">` + session cookie.

## G11. CTI CMS ≠ CDT Website (2026-08-01)
**This is CTI CMS** — a reusable core. CDT is just one client using it. Core changes (`app/`, `database/`) must be generic. CDT-specific behavior → `themes/cdt/` or plugins via hooks. **When unsure, discuss before implementing.**

## G12. Controller Parameter Binding in Localized Routes (2026-08-01)
`Route::get('/{locale}/{slug}', [PageController::class, 'show'])` passes `$locale` as 1st positional argument. Single-param signature `show(string $slug)` gets `$slug = 'id'` instead of `'home'`, causing 404. **Fix:** Signature must be `show(?string $localeOrSlug = null, ?string $slug = null)` with `$targetSlug = $slug ?? $localeOrSlug`.

## G13. Hero Category Heading Order Violation (2026-08-01)
Putting category subtitle `<h2 class="...">Company</h2>` above `<h1>About Us</h1>` violates Google SEO heading order requirements. **Fix:** Use `<span class="block ...">` for hero category labels so `<h1>` is the first heading in the document.

## G14. Tab Buttons Must Not Be Headings (2026-08-01)
Wrapping tab switcher labels in `<h3>` breaks ARIA tab accessibility and creates empty headings in SEO outline. **Fix:** Use `<button>` for tab controls, put `<h3>` on actual card titles inside tab contents.

## G15. Shared Theme Sections Belong in Partials (2026-08-01)
Common sections across pages must be placed in `themes/cdt/views/partials/` and included with `@include('cdt::partials.contact-section')` to prevent duplicating form logic.

## G16. SEO Breadcrumbs Component & Schema (2026-08-01)
Instead of static Blade HTML breadcrumbs, use `<x-seo-breadcrumbs :entity="$page" />`. It automatically uses admin SEO settings, localized home titles, and injects JSON-LD `BreadcrumbList` schema into `<head>`.

## G17. CPT Form Blade @foreach Array Validation on options_list (2026-08-01)
When `options_list` for select/radio/checkbox MetaField is stored as a string, PHP 8.3 throws `foreach() argument must be of type array|object, string given`. **Fix:** Auto-normalize `options_list` string to `[['label' => ..., 'value' => ...]]` in `CptForm::loadCpt()`.

## G18. CPT Entry Meta Translation Storage (_translations) (2026-08-01)
`CptEntry::getMeta($key)` looks up translated custom field values under `$meta['_translations'][$locale][$key]`. Pass `_translations` array inside `meta` JSON payload when seeding or importing multi-language CPT entries.

## G19. PageForm Gallery Block Handler Missing (removeGalleryImage) (2026-08-01)
When configuring a Page block of type `gallery`, clicking delete calls `removeGalleryImage()`. If missing in `PageForm.php`, Livewire throws `MethodNotFoundException`. **Fix:** Add `removeGalleryImage()` method and `gallery_` media picker handler.

## G20. MediaPicker Multiple Upload & Selection Protocol (2026-08-01)
`MediaPicker` auto-detects multiple mode when `$field` starts with `gallery_` or `gallery_add.`. Emits `media-selected-multiple` event. Parent forms listen to bulk-insert images without duplications.

## G21. Blade Directive Typo @canend (2026-08-01)
Laravel Blade uses `@endcan` to close `@can(...)`. Using `@canend` causes `ParseError: syntax error, unexpected token "else"` on subsequent `@else` directives.

## G22. CSS Marquee Animation + overflow-hidden = Lag Over Time (2026-08-01)
`overflow-hidden` + `border-radius` on animated elements causes browser re-clip every frame. **Fix:** Remove `overflow-hidden`, use `translate3d(0,0,0)` + `will-change: transform` + `backface-visibility: hidden` for GPU promotion. Add `contain: layout style` per row.

## G23. loading="lazy" pada Marquee Gallery = Jank (2026-08-01)
`loading="lazy"` on marquee images causes browser to decode images as cards enter viewport → repeated jank. **Fix:** Use `loading="eager" decoding="async"` for all marquee images — prefetch at load, decode in background.

## G24. Image Variants Auto-Generate Butuh Queue Worker (2026-08-01)
`MediaService::upload()` dispatches `GenerateImageVariants` Job automatically. But `QUEUE_CONNECTION=database` → Job queued in DB, not processed without worker. Local: `php artisan queue:work --stop-when-empty`. cPanel: cron job every minute. Without this, gallery loads full-size images.

## G25. resolve_block_asset($path, 'sm') Fallback ke Original (2026-08-01)
`resolve_block_asset($path, 'sm')` looks for `{filename}-sm.webp`. If not found, falls back to original (could be 2-3 MB). Ensure queue worker is running after upload. For marquee gallery, always pass variant `'sm'`.

## G26. CPT MetaBox Tab & Field Group Strict Alignment (2026-08-01)
When adding MetaField, ensure `field_group` matches `id` in `$cpt->settings['meta_boxes']`. If `field_group` is null/empty, field falls into unorganized "CUSTOM FIELDS" fallback box.

## G27. Parent Menu Active State Mismatch & Child Fallback (2026-08-02)
If parent `sidebar-item.blade.php` only checks its own `activeRoutePattern`, active submenu won't auto-expand parent. **Fix:** Check if any child is active when parent `$isActive` is false.

## G28. Wildcard Route Pattern Collision in Submenu Matching (2026-08-02)
Pattern `'admin.cpt.*'` on parent menu makes ALL CPT types expand together. **Fix:** Set parent `activeRoutePattern => null`, use specific patterns on submenu items with `postTypeSlug` parameter matching.

## G29. Static public/robots.txt Overriding Dynamic SEO Route (2026-08-02)
Static file `public/robots.txt` served directly by web server, bypassing `RobotsController`. **Fix:** Delete `public/robots.txt` so Laravel routing handles `/robots.txt` dynamically from DB settings.

## G30. Scheduled Publishing Command Registration Requirement (2026-08-02)
Setting `status = 'scheduled'` and `published_at` won't auto-publish unless `Schedule::command('content:publish-scheduled')->everyMinute()` is registered in `routes/console.php`.

## G31. @livewire Directive Collision in Alpine Event Listeners (2026-08-02)
Using `@livewire:navigated.window="..."` causes Blade compiler to treat it as `@livewire(...)` directive without arguments → `ArgumentCountError`. **Fix:** Use `x-on:livewire:navigated.window="..."`.

## G32. Livewire SPA Navigation Scroll Reset on Persisted Scroll Containers (2026-08-02)
Livewire `wire:navigate` resets scroll to top. Internal scroll containers like sidebar `<nav>` lose scroll position. **Fix:** Add `wire:persist="sidebar-nav"` + save/restore `scrollTop` via `sessionStorage`.

## G33. Dynamic Breadcrumb Resolution via AdminMenuBuilder (2026-08-02)
Views without `@section('breadcrumb')` can auto-generate hierarchical breadcrumbs by matching active route/URL against `AdminMenuBuilder::getUnifiedMenuList()`.

## G34. WordPress Polylang Multi-Post Import & Unique Slug Conflicts (2026-08-02)
Polylang WP API returns separate posts for EN and ID with similar/identical slugs. Batch migration without variant merging creates duplicate rows → `Slug already exists`. **Fix:** Use `findExistingPolylangEntry()` to find existing rows via `meta['wp_original_ids']`, merge translations with `setTranslation()`, increment `translated` counter.

## G35. Order-Independent Polylang Import & Swapped Locale Columns (2026-08-02)
If Indonesian post is processed before English, `CptEntry::create()` puts Indonesian text in main columns, causing reversed locale data when English version arrives second. **Fix:** Always use `$entry->setTranslation($field, $wpLang, $value)` for BOTH initial creation AND merging. `HasTranslations` trait auto-routes default locale to main columns and secondary locales to JSON `translations`.

## G37. CPT Meta Field Polylang Multi-Locale Storage (2026-08-02)
`setTranslation()` default only handles standard columns (`title`, `slug`, `content`, `excerpt`). CPT custom fields (`meta.banner_description`) not updated when merging secondary post, causing Indonesian `meta` data to overwrite English primary. **Fix:** Store default locale meta at root `$meta[$key]`, store secondary locale at `$meta['_translations'][$lang][$key]`.

## G38. Admin API vs Web Endpoints (2026-07-28)
`POST /api/v1/admin/forms` (API token) only for form metadata. Create form + fields in one request must via `POST /ctrlpanel/forms` (session cookie + CSRF).

## G39. Form Assignments di Settings Table (2026-07-28)
Form-to-placeholder mapping stored in `settings` table: `theme_{slug}_form_assignments` = `{"alliance_form": "4", ...}`. Read via `setting("theme_{$theme->slug}_form_assignments", [])`.

## G40. Form Validation Flow (2026-07-28)
Submit → honeypot → captcha → per-field validation → `FormEntry` created → `SendFormNotificationJob` queued → redirect. Failure = redirect back with `$errors`.

## G41. MetaField `icon` Type (2026-07-31)
Valid type for icon field: `icon` (Icon Picker with Lucide Icons), not `text`. `MetaField::FIELD_TYPES['icon']`.

## G42. Repeater Children via API (2026-07-31)
Repeater children cannot be created via `storeField()` standalone. Must be saved as `options.repeater_fields` in parent repeater field via `updateField()`.

## G43. Global Validation Errors pada CPT Entry Form (2026-07-31)
Add red alert box at top of CPT form + per-subfield error messages in repeater.

## G44. Breadcrumb Auto-Generated (2026-07-31)
CMS has `BreadcrumbService` + `SeoBreadcrumbs` component. Breadcrumbs auto-generated from hierarchy. No need for `hero_breadcrumb` field in CPT — redundant. See G16.

## G45. `.gitignore` Theme Blocking on Project Branches & Server Permissions (2026-08-04)
Rule `/themes/{name}` in `.gitignore` carried from `main` branch makes new partials on server (like `contact-section.blade.php`) ignored by Git. **Fix:** (1) On `project/{name}` branch, REMOVE `/themes/{name}` from `.gitignore` so all partials are tracked. (2) After every `git pull`/`git reset` as `root` on cPanel, run `chown -R <cpanel_user>:<cpanel_user> /home/<cpanel_user>/<domain>/` to prevent Permission Denied.
