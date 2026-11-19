# Redirect & 404 System

## Redirect Manager

### Model
`Redirect` model (`redirects` table):
- `from_url` — source path (supports regex)
- `to_url` — destination path
- `status_code` — 301 (permanent) or 302 (temporary)
- `is_regex` — treat `from_url` as regex pattern
- SoftDeletes

### Middleware
`HandleRedirects` — **prepended** middleware (runs BEFORE route matching). This ensures redirects are processed before Laravel tries to match the URL to a route/controller.

### Admin UI
- List all redirects at `/ctrlpanel/seo/redirects`
- Create, edit, delete with live preview
- Regex toggle for pattern-based redirects

### How It Works
1. Every request hits `HandleRedirects` first
2. Middleware queries active redirects (cached)
3. If URL matches `from_url` → 301/302 redirect to `to_url`
4. Regex patterns evaluated with `preg_match`

## 404 Logger

### Model
`NotFoundLog` model (`not_found_logs` table):
- `url` — the 404'd path
- `referer` — HTTP referer
- `count` — hit count (aggregated)
- `first_seen_at` / `last_seen_at` — timestamps

### Middleware
`Log404` — last middleware in stack, uses **terminable callback** (runs AFTER response sent to browser). This means logging never slows down the 404 response.

### Throttling & Cleanup
- Same URL logged max once per 5 minutes (count incremented)
- Static assets (`.css`, `.js`, `.jpg`, etc.) skipped entirely
- Auto-pruned after 90 days via scheduled task

### Admin Dashboard
- 404 audit at `/ctrlpanel/seo/not-found-logs`
- Grouped by URL with hit counts
- Easy creation of redirect rules from 404 entries

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/Redirect.php` | Redirect rule model |
| `app/Models/NotFoundLog.php` | 404 log model |
| `app/Http/Middleware/HandleRedirects.php` | Prepended redirect middleware |
| `app/Http/Middleware/Log404.php` | Terminable 404 logger |
