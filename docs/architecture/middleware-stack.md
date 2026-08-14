# Middleware & Performance Stack

## Web Middleware Stack (in order)

Registered in `bootstrap/app.php`:

| # | Middleware | File | Purpose |
|---|-----------|------|---------|
| 0 | `HandleRedirects` | `app/Http/Middleware/HandleRedirects.php` | **Prepended** — 301/302 redirect rules before route matching |
| 1 | `SetLocale` | `app/Http/Middleware/SetLocale.php` | Set app locale from URL prefix / query / session / cookie |
| 2 | `InjectSeoTags` | `app/Http/Middleware/InjectSeoTags.php` | Auto-inject meta tags, OG, Twitter Cards, JSON-LD into `<head>` |
| 3 | `OptimizeHtml` | `app/Http/Middleware/OptimizeHtml.php` | Minify HTML output (collapse whitespace, remove comments) |
| 4 | `CompressResponse` | `app/Http/Middleware/CompressResponse.php` | Gzip/Brotli compression based on Accept-Encoding |
| 5 | `SecurityHeaders` | `app/Http/Middleware/SecurityHeaders.php` | CSP, X-Frame-Options, X-Content-Type-Options, HSTS |
| 6 | `PageCache` | `app/Http/Middleware/PageCache.php` | Full-page cache for anonymous GET requests. Auto-purged on Page/CPT save |
| 7 | `Log404` | `app/Http/Middleware/Log404.php` | Passive 404 logging via terminable callback. Throttled 5min, static assets skipped |

## Named Middleware (Aliases)

| Alias | Class | Usage |
|-------|-------|-------|
| `permission` | `CheckPermission` | Route-level permission gate: `permission:posts.create` |
| `role` | `CheckRole` | Route-level role gate |
| `enforce-2fa` | `EnforceTwoFactor` | Force 2FA setup before accessing admin |
| `api.auth` | `ApiAuth` | API token authentication (Bearer) |
| `api.cors` | `ApiCors` | CORS headers for API routes |

## Cache Layers

| Cache | Driver | Scope |
|-------|--------|-------|
| Settings | In-memory array | Per-request, cleared on settings update |
| Page Cache | File/Redis | Full HTML for anonymous GET, purged on content change |
| Sitemap Cache | File | Per-post-type, per-locale, regenerated on content change |
| Route Cache | File | `php artisan route:cache` (production) |
| Config Cache | File | `php artisan config:cache` (production) |
| View Cache | File | `php artisan view:cache` (production) |

## Security Headers

```php
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Strict-Transport-Security: max-age=31536000; includeSubDomains (HTTPS only)
Content-Security-Policy: (configurable via admin settings)
```

## Queue

Uses `database` queue driver by default. Jobs:
- `GenerateImageVariants` — WebP conversion after upload
- `SendFormNotificationJob` — email notification on form submit
- `IndexNowService` — notify search engines

Run with: `php artisan queue:work` (production under Supervisor) or `php artisan queue:listen` (dev).
