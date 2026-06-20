# PRD Core CMS Roadmap

Planning konsolidasi untuk 7 fitur core CMS yang belum ada (di luar theme & plugin system). Setiap section bisa di-spin-off menjadi PRD terpisah saat eksekusi.

**Stack**: Laravel 13, Livewire 4, PHP 8.2+.
**Out of scope di roadmap ini**: Backup (sudah ditangani via cPanel daily).

---

## Prioritas & Urutan Eksekusi

| # | Fitur | Effort | Risiko | Dependency |
|---|-------|--------|--------|------------|
| 1 | SEO per-konten + sitemap submission | M | Low | — |
| 2 | REST API (headless-ready) | L | Med | Sanctum |
| 3 | Cache layer + image variants | M | Med | Redis/file cache |
| 4 | Login security (2FA optional + reset + rate limit) | M | Low | Notification mail |
| 5 | Email template manager + queue dashboard | M | Low | Horizon (opsional) |
| 6 | Scheduled publish + Trash | S | Low | — |
| 7 | Webhooks + API key | M | Low | Queue |

`S = ≤1 minggu, M = 1–2 minggu, L = 2–4 minggu` (1 dev fulltime).

Urutan rekomendasi: **1 → 4 → 6 → 3 → 5 → 2 → 7** (value × ketergantungan).

---

## 1. SEO per-Konten + Sitemap Submission

### Tujuan
Editor bisa atur meta title, description, OG/Twitter card, canonical, robots, dan schema.org per Page / CPT Entry / Post. Sitemap submit otomatis ke Google/Bing saat content published.

### Current State
- [routes/web.php:14](routes/web.php#L14) sudah expose `/sitemap.xml` via `SitemapController`.
- Tidak ada kolom SEO di [database/migrations/2026_02_01_000002_create_pages_table.php](database/migrations/2026_02_01_000002_create_pages_table.php) maupun `cpt_entries`.
- Tidak ada metabox SEO di Livewire page editor.

### Scope
- Tabel polymorphic `seo_meta` agar reusable untuk Page, CptEntry, dan Posts plugin.
- Komponen Livewire `SeoMetaBox` (collapsible card di editor).
- Service `SeoRenderer` untuk render `<meta>` + JSON-LD di Blade layout.
- Generator schema.org: Article, Event, Organization, BreadcrumbList, FAQPage.
- Sitemap regenerator job + IndexNow (Bing) + Google Indexing API (opsional via plugin).

### Database Schema
```sql
CREATE TABLE seo_meta (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    seoable_type VARCHAR(255) NOT NULL,
    seoable_id   BIGINT UNSIGNED NOT NULL,
    title           VARCHAR(70)  NULL,
    description     VARCHAR(160) NULL,
    canonical_url   VARCHAR(500) NULL,
    robots          VARCHAR(50)  DEFAULT 'index,follow',
    og_title        VARCHAR(95)  NULL,
    og_description  VARCHAR(200) NULL,
    og_image_id     BIGINT UNSIGNED NULL,
    twitter_card    VARCHAR(20)  DEFAULT 'summary_large_image',
    schema_type     VARCHAR(50)  NULL,    -- 'Article','Event',...
    schema_data     JSON NULL,             -- override field schema
    focus_keyword   VARCHAR(100) NULL,
    seo_score       TINYINT UNSIGNED NULL,
    readability_score TINYINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_seoable (seoable_type, seoable_id),
    FOREIGN KEY (og_image_id) REFERENCES media(id) ON DELETE SET NULL
);

CREATE TABLE sitemap_pings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    target  ENUM('google','bing','indexnow') NOT NULL,
    url     VARCHAR(500) NOT NULL,
    status  ENUM('queued','sent','failed') DEFAULT 'queued',
    response_code SMALLINT NULL,
    response_body TEXT NULL,
    pinged_at TIMESTAMP NULL,
    INDEX (status, pinged_at)
);
```

### Files to Add / Touch
- `app/Models/SeoMeta.php` (new)
- `app/Traits/HasSeoMeta.php` (new) — `morphOne` trait dipakai Page, CptEntry, Post.
- `app/Livewire/Admin/Seo/SeoMetaBox.php` (new)
- `app/Services/SeoRenderer.php` (new)
- `app/Services/SchemaBuilder.php` (new)
- `app/Services/SitemapService.php` (refactor [app/Http/Controllers/SitemapController.php](app/Http/Controllers/SitemapController.php))
- `app/Jobs/PingSitemap.php` (new)
- `resources/views/components/seo/head.blade.php` (new) — dipanggil di layout theme.
- Migration: `seo_meta`, `sitemap_pings`.
- Setting baru di [app/Services/SettingsRegistry.php](app/Services/SettingsRegistry.php): `seo.default_title_template`, `seo.indexnow_key`, `seo.google_search_console_verification`.

### Acceptance Criteria
- [ ] Editor bisa edit meta title/description dengan live char counter (warning >60/>160).
- [ ] Preview SERP snippet di metabox.
- [ ] OG image picker dari Media Library.
- [ ] Schema.org JSON-LD ter-render di view-source untuk page/post.
- [ ] Saat publish → job ping sitemap ke Google + IndexNow.
- [ ] `/sitemap.xml` include lastmod, changefreq, priority dari setting per content type.
- [ ] Robots meta `noindex` honored saat dipilih.
- [ ] Sitemap split per 50k URL jika perlu.

---

## 2. REST API (Headless-Ready)

### Tujuan
Expose content untuk konsumen eksternal (mobile app, Next.js frontend, integrasi). Read-only public + authenticated write via API token.

### Current State
- Tidak ada `routes/api.php` di [routes/](routes/).
- Tidak ada Sanctum/Passport.
- Content access hanya via web controller.

### Scope
- Install **Laravel Sanctum** untuk personal access tokens.
- Versioned API: `/api/v1/...`.
- Resources: Pages, Posts, Events, CptEntries, Taxonomies, Media (read-only public), Forms (submit), Users (auth-only).
- Pagination, filter, include, fields sparse fieldsets (JSON:API-lite).
- Rate limit per token + per IP.
- OpenAPI 3.1 spec (Scribe atau manual) + Swagger UI di `/admin/api-docs`.
- CORS config per setting.

### Database Schema
Sanctum default tables (`personal_access_tokens`) + extension untuk scope:
```sql
ALTER TABLE personal_access_tokens
    ADD COLUMN scopes JSON NULL AFTER abilities,
    ADD COLUMN rate_limit_per_minute INT UNSIGNED DEFAULT 60,
    ADD COLUMN allowed_ips JSON NULL,
    ADD COLUMN expires_at TIMESTAMP NULL;
```

### Files to Add
- `routes/api.php` (new) — di-load via [bootstrap/app.php](bootstrap/app.php).
- `app/Http/Controllers/Api/V1/{Page,Post,Event,Cpt,Media,Form,Auth}Controller.php`
- `app/Http/Resources/V1/...Resource.php` — transformer.
- `app/Http/Middleware/ApiAuth.php`, `ApiRateLimit.php`, `ApiScope.php`.
- `app/Livewire/Admin/ApiTokens/{Index,Create}.php` — UI manage token.
- `config/api.php` (new) — versioning, default per_page, CORS allowed origins.
- Migration: extend `personal_access_tokens`.
- `resources/views/admin/api-docs/index.blade.php` — embed Swagger UI.

### Endpoints (v1 skeleton)
```
GET    /api/v1/pages
GET    /api/v1/pages/{slug}
GET    /api/v1/posts?category=&tag=&page=
GET    /api/v1/events?upcoming=true
GET    /api/v1/cpt/{type}
GET    /api/v1/taxonomies/{taxonomy}/terms
POST   /api/v1/forms/{slug}/submit
POST   /api/v1/auth/login           (returns token)
POST   /api/v1/auth/logout
GET    /api/v1/me                   (auth)
```

### Acceptance Criteria
- [ ] Admin bisa generate/revoke API token via UI dengan scope + IP allowlist.
- [ ] Endpoint public read terbatas hanya konten `published`.
- [ ] Endpoint dengan auth honored permission table.
- [ ] Rate limit return 429 + `Retry-After` header.
- [ ] OpenAPI spec accessible di `/api/v1/openapi.json`.
- [ ] CORS preflight working untuk allowed origins dari Settings.
- [ ] Test integration: 1 happy-path + 1 auth failure + 1 rate-limit per resource.

---

## 3. Cache Layer + Image Variants

### Tujuan
(a) Full-page cache untuk frontend page render; (b) responsive image variants + WebP otomatis.

### Current State
- [app/Services/MediaService.php](app/Services/MediaService.php) hanya store original.
- Tidak ada full-page cache. Plugin Loader berat di tiap request.
- `bootstrap/app.php` belum ada middleware cache.

### Scope A: Page Cache
- Middleware `CacheResponse` untuk public GET requests.
- Tag-based cache invalidation via Observer (page/post/cpt updated → flush tag).
- Bypass cache jika user logged-in, query has `?preview=`, atau cookie tertentu.
- Stale-while-revalidate header.

### Scope B: Image Variants
- Saat upload media (image), generate variants async: `thumb (150)`, `sm (480)`, `md (768)`, `lg (1280)`, `xl (1920)` + WebP untuk masing-masing.
- Helper Blade `@responsiveImage($media, 'lg')` → render `<picture>` dengan srcset.
- Focal point picker di MediaDetails.
- Lazy-load + LQIP (base64 16x16 blur) auto-generated.

### Database Schema
```sql
ALTER TABLE media
    ADD COLUMN variants JSON NULL AFTER metadata,
    ADD COLUMN focal_x DECIMAL(5,4) DEFAULT 0.5,
    ADD COLUMN focal_y DECIMAL(5,4) DEFAULT 0.5,
    ADD COLUMN placeholder_data_uri TEXT NULL,
    ADD COLUMN webp_available BOOLEAN DEFAULT FALSE;

CREATE TABLE page_cache_entries (
    cache_key VARCHAR(255) PRIMARY KEY,
    url       VARCHAR(500) NOT NULL,
    tags      JSON NOT NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    INDEX (expires_at)
);
```

### Files to Add / Touch
- `app/Http/Middleware/CacheResponse.php`
- `app/Services/PageCacheService.php`
- `app/Jobs/GenerateImageVariants.php`
- `app/Observers/CacheInvalidationObserver.php` (registered untuk Page, PageBlock, Post, Event, MenuItem, Setting)
- `app/Services/ResponsiveImageService.php`
- Refactor [app/Services/MediaService.php](app/Services/MediaService.php) — trigger job after store.
- `resources/views/components/image.blade.php` (new) — `<x-image :media="$m" size="lg" />`.
- Composer: `intervention/image` (atau pakai Imagick langsung).

### Acceptance Criteria
- [ ] First request `/about` MISS, second request HIT (<50ms).
- [ ] Edit page → tag invalidate → next request MISS lagi.
- [ ] Logged-in user selalu bypass cache.
- [ ] Upload image 5MB → 5 variants + 5 WebP ter-generate dalam <30s (queued).
- [ ] `<picture>` element render dengan srcset 5 ukuran + WebP source.
- [ ] LQIP placeholder ada di `loading="lazy"` images.
- [ ] Focal point disimpan dan dipakai saat crop.

---

## 4. Login Security (2FA Optional + Password Reset + Rate Limit)

### Tujuan
Produksi-grade login: brute-force protection, password reset via email, dan 2FA TOTP yang **opsional** (per-user atau enforced per-role).

### Current State
- [app/Http/Controllers/Auth/AuthController.php](app/Http/Controllers/Auth/AuthController.php) login basic tanpa throttle visible.
- `users.password_changed_at` & `users.last_login_at` ada di migrations.
- Tidak ada password reset, tidak ada 2FA.

### Scope
**4A. Rate limit login**
- Throttle 5 attempts / 15 menit per IP+email.
- Lockout 30 menit setelah 10 attempts.
- Log ke `activities` table.

**4B. Password reset**
- Route `/admin/forgot-password` + `/admin/reset-password/{token}`.
- Token expire 60 menit.
- Email template di Email Template Manager (lihat #5).
- Setelah reset, invalidate semua session lain + force re-login.

**4C. 2FA Optional (TOTP)**
- User bisa enable/disable di Profile.
- QR code via `bacon/bacon-qr-code` + `pragmarx/google2fa`.
- 8 recovery codes one-time.
- Admin bisa **enforce 2FA per role** (setting `auth.force_2fa_roles = ['super-admin']`).
- Saat enforce, user dipaksa setup 2FA di login berikutnya.

### Database Schema
```sql
ALTER TABLE users
    ADD COLUMN two_factor_secret VARCHAR(255) NULL,
    ADD COLUMN two_factor_recovery_codes TEXT NULL,
    ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL,
    ADD COLUMN failed_login_attempts TINYINT UNSIGNED DEFAULT 0,
    ADD COLUMN locked_until TIMESTAMP NULL;

CREATE TABLE password_resets (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    INDEX (token)
);
```
(`password_resets` mungkin sudah ada by default Laravel — verify.)

### Files to Add / Touch
- `app/Http/Controllers/Auth/{ForgotPassword,ResetPassword,TwoFactor}Controller.php`
- `app/Http/Middleware/EnforceTwoFactor.php`
- `app/Livewire/Admin/Profile/TwoFactorSettings.php`
- `app/Notifications/PasswordResetNotification.php`
- `app/Services/TwoFactorService.php`
- Refactor [app/Http/Controllers/Auth/AuthController.php](app/Http/Controllers/Auth/AuthController.php) → tambah throttle + 2FA challenge step.
- Setting baru: `auth.force_2fa_roles`, `auth.password_reset_enabled`, `auth.lockout_minutes`.
- Composer: `pragmarx/google2fa-laravel`, `bacon/bacon-qr-code`.

### Acceptance Criteria
- [ ] Login attempt ke-6 dalam 15 menit return error rate-limited.
- [ ] User klik "Lupa password" → email kirim link → reset berhasil.
- [ ] Reset link expire setelah 60 menit / single-use.
- [ ] User enable 2FA → scan QR → masukkan kode → confirmed.
- [ ] Recovery code valid sekali pakai.
- [ ] Admin enable enforce 2FA untuk role X → user role X login → dipaksa setup.
- [ ] Disable 2FA hanya bisa setelah masukkan password current.
- [ ] Audit log mencatat semua peristiwa: failed login, lockout, reset, 2FA enable/disable.

---

## 5. Email Template Manager + Queue Dashboard

### Tujuan
Admin bisa edit subject & body email transactional dari UI tanpa edit kode. Bisa monitor queue + retry failed.

### Current State
- `app/Mail/` ada (perlu cek isinya), email body hardcode di Blade.
- Job table sudah ada ([database/migrations/0001_01_01_000002_create_jobs_table.php](database/migrations/0001_01_01_000002_create_jobs_table.php)).
- Tidak ada `failed_jobs` UI.
- Events plugin (PRD 07) butuh per-event template — sudah ditangani di plugin sendiri. Ini untuk **core CMS** templates (password reset, form notification, user invitation).

### Scope
**5A. Email Template Manager**
- CRUD template dengan: key (unique slug), subject, body (HTML editor), description, available variables list.
- Blade-like variable substitution: `{{ user.name }}`, `{{ site.name }}`.
- Test send to email tertentu.
- Versioning ringan (last 5 versions).

**5B. Queue Dashboard**
- Table view: jobs queued, processing, failed.
- Retry / forget / batch retry failed.
- Filter by queue name + date.
- (Opsional) install Laravel Horizon jika pakai Redis driver.

### Database Schema
```sql
CREATE TABLE email_templates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) UNIQUE NOT NULL,   -- 'password_reset','user_invitation'
    name     VARCHAR(255) NOT NULL,
    subject  VARCHAR(255) NOT NULL,
    body_html LONGTEXT NOT NULL,
    body_text TEXT NULL,
    variables JSON NULL,                      -- list variabel + description
    description TEXT NULL,
    is_system BOOLEAN DEFAULT FALSE,          -- system templates can't be deleted
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE email_template_versions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    template_id BIGINT UNSIGNED NOT NULL,
    subject  VARCHAR(255) NOT NULL,
    body_html LONGTEXT NOT NULL,
    edited_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (template_id) REFERENCES email_templates(id) ON DELETE CASCADE
);

-- failed_jobs default Laravel — pastikan migration jalan.
```

### Files to Add
- `app/Models/EmailTemplate.php`
- `app/Services/EmailTemplateRenderer.php` — render variables, return Mailable.
- `app/Livewire/Admin/EmailTemplates/{Index,Edit,Preview,TestSend}.php`
- `app/Livewire/Admin/Queue/{JobsTable,FailedJobsTable}.php`
- Seeder system templates (password_reset, user_invited, form_notification, etc.)
- Route: `/admin/email-templates`, `/admin/queue`.

### Acceptance Criteria
- [ ] Admin lihat list template, edit body via WYSIWYG.
- [ ] Variable picker insert `{{ user.name }}` ke editor.
- [ ] "Send test email" kirim ke email yang diinput dengan dummy variables.
- [ ] Versioning: rollback ke versi sebelumnya.
- [ ] Password reset flow (#4) pakai template `password_reset` dari DB.
- [ ] Failed job di-retry → masuk lagi ke queue.
- [ ] Queue dashboard refresh polling tiap 5 detik (Livewire).

---

## 6. Scheduled Publish + Trash

### Tujuan
Editor bisa jadwalkan publish content di tanggal future. Content yang di-delete masuk Trash (recycle bin) 30 hari sebelum dihapus permanen.

### Current State
- [database/migrations/2026_02_01_000002_create_pages_table.php](database/migrations/2026_02_01_000002_create_pages_table.php) — perlu cek apakah ada `published_at` & soft deletes.
- [app/Models/Page.php](app/Models/Page.php) belum lihat `SoftDeletes`.
- Tidak ada UI Trash.

### Scope
**6A. Scheduled Publish**
- Field `status` extend: `draft | scheduled | published | private`.
- Field `published_at` (datetime).
- Cron job `php artisan content:publish-scheduled` (per menit) — flip status `scheduled → published` saat `published_at <= now()`.
- Editor UI: tombol "Publish" jadi dropdown ("Publish now", "Schedule…").

**6B. Trash**
- SoftDeletes untuk Page, Post, CptEntry, Form, Media.
- UI `/admin/trash` dengan tab per resource.
- Bulk restore + permanent delete.
- Auto-purge content yang sudah trashed >30 hari (configurable).

### Database Schema
```sql
ALTER TABLE pages
    ADD COLUMN published_at TIMESTAMP NULL AFTER status,
    ADD COLUMN deleted_at TIMESTAMP NULL,
    ADD INDEX idx_published (status, published_at),
    MODIFY COLUMN status ENUM('draft','scheduled','published','private') DEFAULT 'draft';

-- Same pattern untuk cpt_entries, forms.
-- posts/events plugin migrate sendiri.

ALTER TABLE media
    ADD COLUMN deleted_at TIMESTAMP NULL;
```

### Files to Add / Touch
- `app/Console/Commands/PublishScheduledContent.php`
- `app/Console/Commands/PurgeOldTrash.php`
- Register di `routes/console.php`: every minute & daily.
- `app/Livewire/Admin/Trash/{TrashIndex,RestoreAction}.php`
- Route: `/admin/trash`.
- Update [app/Models/Page.php](app/Models/Page.php), Post, CptEntry, Form, Media — add `SoftDeletes`.
- Update query scopes: public hanya tampil `published` AND `published_at <= now()`.
- Setting: `content.trash_retention_days` (default 30).

### Acceptance Criteria
- [ ] Editor pilih "Schedule" + future datetime → status jadi `scheduled`.
- [ ] Frontend tidak menampilkan scheduled content sampai waktunya tiba.
- [ ] Cron flip status saat waktunya tiba (test dengan job:run manual).
- [ ] Delete page → masuk Trash, tidak tampil di list utama.
- [ ] Restore dari Trash → kembali ke state sebelumnya.
- [ ] Permanent delete: konfirmasi 2x, hilang dari DB.
- [ ] Cron purge content yang trashed >30 hari.
- [ ] Permission `content.trash.restore` & `content.trash.purge` ditambahkan.

---

## 7. Webhooks + API Key

### Tujuan
Trigger HTTP POST ke URL eksternal saat event tertentu (form submitted, post published, user registered). Memungkinkan integrasi Zapier, n8n, Make.com.

### Current State
- Tidak ada webhook table.
- API key sebagian sudah ter-handle di #2 (Sanctum), tapi webhook butuh **signing secret** terpisah.

### Scope
- Webhook subscription: URL, event triggers (multi), signing secret, active flag.
- Event registry: `form.submitted`, `post.published`, `user.registered`, `event.registration.created`, `page.published`, `media.uploaded`.
- Async delivery via queue dengan retry exponential backoff (1m, 5m, 30m, 2h, 12h).
- Signing: HMAC-SHA256 header `X-Webhook-Signature` agar receiver bisa verify.
- Delivery log: payload, response code, response body, attempts.
- UI test webhook (kirim sample payload).

### Database Schema
```sql
CREATE TABLE webhooks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    url  VARCHAR(500) NOT NULL,
    events JSON NOT NULL,                    -- ['form.submitted','post.published']
    signing_secret VARCHAR(64) NOT NULL,
    headers JSON NULL,                       -- custom headers
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (is_active)
);

CREATE TABLE webhook_deliveries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    webhook_id BIGINT UNSIGNED NOT NULL,
    event VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    response_code SMALLINT NULL,
    response_body TEXT NULL,
    attempts TINYINT UNSIGNED DEFAULT 0,
    status ENUM('pending','success','failed','retrying') DEFAULT 'pending',
    next_retry_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (webhook_id) REFERENCES webhooks(id) ON DELETE CASCADE,
    INDEX (status, next_retry_at)
);
```

### Files to Add
- `app/Models/{Webhook,WebhookDelivery}.php`
- `app/Services/WebhookDispatcher.php` — dispatch event → match subscribers → queue jobs.
- `app/Jobs/DeliverWebhook.php`
- `app/Events/{FormSubmitted,PostPublished,UserRegistered,...}.php` — domain events.
- `app/Listeners/DispatchWebhooks.php` — single listener untuk semua event registry.
- `app/Livewire/Admin/Webhooks/{Index,Edit,DeliveriesLog,Test}.php`
- Route: `/admin/webhooks`.
- Permission: `webhooks.view`, `webhooks.create`, `webhooks.edit`, `webhooks.delete`.

### Acceptance Criteria
- [ ] Admin buat webhook, pilih events, dapat signing secret auto-generated.
- [ ] Submit form → POST hit webhook URL dalam <5s (queued).
- [ ] Receiver verify HMAC: `hash_hmac('sha256', payload, secret) === signature`.
- [ ] Non-2xx response → retry sesuai schedule, max 5 attempts.
- [ ] Setelah max attempts → status `failed`, kirim notification ke admin.
- [ ] UI "Send test" kirim sample payload + tampilkan response.
- [ ] Delivery log retensi 30 hari (configurable), purge via cron.
- [ ] Disable webhook → tidak ada delivery baru tapi log lama tetap.

---

## Cross-Cutting Concerns

### Permissions Baru
Tambahkan ke [database/seeders/PermissionSeeder.php] (verify ada):
```
seo.view, seo.edit
api-tokens.view, api-tokens.create, api-tokens.revoke
email-templates.view, email-templates.edit, email-templates.test
queue.view, queue.retry
content.trash.view, content.trash.restore, content.trash.purge
content.schedule
webhooks.view, webhooks.create, webhooks.edit, webhooks.delete
auth.force-2fa
```

### Settings Group Baru
Di [app/Services/SettingsRegistry.php](app/Services/SettingsRegistry.php):
- `seo` — default templates, IndexNow key, GSC verification, OG default image.
- `cache` — page cache TTL, image variant sizes, CDN URL.
- `auth` — lockout, 2FA enforce roles, password policy.
- `api` — CORS origins, default rate limit, version.
- `webhooks` — delivery retention days.

### Migration Strategy
- Setiap feature: satu migration baru (jangan modify migration lama).
- Backfill data: opsional seeder untuk system templates (#5) & default SEO meta (#1) untuk content existing.
- Plugin-aware: pastikan extension `pages`, `cpt_entries` juga di-apply ke posts/events tables via plugin migrations.

### Testing Strategy per Fitur
| Fitur | Unit | Feature | E2E manual |
|-------|------|---------|------------|
| 1 SEO | Schema builder, meta validator | Save metabox, sitemap render | View source check |
| 2 API | Resources transform | Auth, rate-limit, CORS | Postman collection |
| 3 Cache | Tag invalidation logic | Middleware HIT/MISS | Image variant gen |
| 4 Auth | TwoFactorService verify | Login throttle, reset flow | 2FA full cycle |
| 5 Email | Variable substitution | Template CRUD, test send | Queue retry |
| 6 Schedule | Scope queries | Cron command | Trash restore |
| 7 Webhooks | HMAC sign verify | Dispatch on event | Real Zapier hook |

---

## Tracking & Reporting

- Buat sub-folder `docs/prd/core-{nn}-{slug}/` saat fitur dipromote dari roadmap ini ke PRD lengkap.
- Update [README.md](README.md) section "Features" tiap fitur ship.
- Tag git per release: `core/seo-v1`, `core/api-v1`, dst.
