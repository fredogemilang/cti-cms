# API & Authentication System

## REST API v1

Base: `/api/v1/`

**98 endpoints total.** See `docs/api-reference.md` for full list.

### Authentication

- Admin endpoints: `Authorization: Bearer <api_token>`
- Public endpoints: no auth required
- `POST /api/v1/auth/login` — exchange credentials for token
- `POST /api/v1/auth/logout` — revoke current token
- `GET /api/v1/me` — authenticated user profile

### Endpoint Categories

| Category | Prefix | Count |
|----------|--------|-------|
| Public Content | `/api/v1/` | 14 endpoints |
| Pages & Blocks | `/api/v1/admin/pages` | 10 |
| CPT Schema | `/api/v1/admin/cpt` | 10 |
| CPT Entries | `/api/v1/admin/cpt/{type}/entries` | 6 |
| Media | `/api/v1/admin/media` | 5 |
| Forms | `/api/v1/admin/forms` | 6 |
| SEO | `/api/v1/admin/*/seo` | 6 |
| Menus | `/api/v1/admin/menus` | 5 |
| Taxonomies | `/api/v1/admin/taxonomies` | 8 |
| Settings | `/api/v1/admin/settings` | 3 |
| Redirects | `/api/v1/admin/redirects` | 4 |
| Webhooks | `/api/v1/admin/webhooks` | 6 |
| Users & Roles | `/api/v1/admin/users` | 6 |
| Activity Logs | `/api/v1/admin/activity-logs` | 1 |
| Plugins | `/api/v1/admin/plugins` | 2 |
| Themes | `/api/v1/admin/themes` | 2 |
| Email Templates | `/api/v1/admin/email-templates` | 2 |
| Indexing | `/api/v1/admin/seo/indexing-logs` | 1 |
| OpenAPI Spec | `/api/v1/openapi.json` | 1 |

### API Tokens

- Created/revoked in Admin → API Tokens
- Scoped per-user with expiration
- Used for headless frontends (Next.js, Nuxt, Mobile Apps)

### Two-Factor Authentication (2FA)

- TOTP-based (Time-based One-Time Password)
- `EnforceTwoFactor` middleware gates admin access
- Configurable per role — can require 2FA for specific roles only
- QR code setup via admin profile page

### RBAC

- spatie/laravel-permission: Users → Roles → Permissions
- `CheckPermission` middleware: `Route::middleware('permission:posts.create')`
- Permission naming: `{resource}.{action}` (e.g. `pages.edit`, `cpt.delete`)

### Webhooks

- Register URL + events in admin
- Delivered on: `page.created`, `page.updated`, `cpt_entry.created`, `form.submitted`, etc.
- Delivery logs with retry status

## Key Files

| File | Purpose |
|------|---------|
| `routes/api.php` | API route definitions |
| `app/Http/Middleware/ApiAuth.php` | API token authentication |
| `app/Http/Controllers/Api/V1/AuthController.php` | Login/logout/token management |
| `app/Http/Middleware/EnforceTwoFactor.php` | 2FA enforcement |
| `app/Models/ApiToken.php` | Token model |
