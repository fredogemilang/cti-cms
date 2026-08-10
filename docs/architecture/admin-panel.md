# Admin Panel & Tools

## Access

- Path: Configurable via `ADMIN_PATH` env var (default `ctrlpanel`)
- URL: `/{ADMIN_PATH}` → `/ctrlpanel`
- Auth: Custom `AuthController` with optional 2FA

## Menu System

- **Registration:** Via `AdminMenuBuilder` + `RenderAdminMenu` event — never seed menus to DB
- **Hierarchy:** `MenuItem` model with parent/child, order, icons (Lucide), route patterns
- **Active detection:** Pattern-based with child fallback
- **Gotcha G27/G28:** Parent menu must check children's active state; wildcard patterns cause collision

```php
// Menu structure
MAIN → CONTENT → PLUGINS → SYSTEM
```

## Built-in Admin Pages

| Section | Path | Purpose |
|---------|------|---------|
| Dashboard | `/ctrlpanel` | Quick stats, recent activity |
| Pages | `/ctrlpanel/pages` | Page list + Page Builder |
| CPT | `/ctrlpanel/cpt/{type}` | CPT entry management |
| Media | `/ctrlpanel/media` | Media library, upload, WebP |
| Forms | `/ctrlpanel/forms` | Form builder + submissions |
| Menus | `/ctrlpanel/menus` | Navigation menu manager |
| SEO | `/ctrlpanel/seo` | SEO overview, settings, redirects, 404 log |
| Settings | `/ctrlpanel/settings` | All settings groups |
| Users | `/ctrlpanel/users` | User management + RBAC |
| Plugins | `/ctrlpanel/plugins` | Plugin activation/deactivation |
| Themes | `/ctrlpanel/themes` | Theme management |
| API Tokens | `/ctrlpanel/api-tokens` | Token management |
| Activity Log | `/ctrlpanel/activity-logs` | Audit trail |
| Backup | `/ctrlpanel/backup` | Database/filesystem backups |
| Queue | `/ctrlpanel/queue` | Queue monitor |
| Trash | `/ctrlpanel/trash` | Soft-deleted content recovery |

## UI Standards

All admin index views MUST follow 5 rules:
1. Header with `@section('page-actions')` for primary buttons
2. Filter status tabs with `getStatusCountsProperty()` count badges
3. Search via `<x-admin.ui.input>`
4. Shared `<x-admin.ui.table>` component
5. `data-tooltip` action buttons

## Additional Tools

### Activity Log
- `Activity` model with polymorphic subject
- Auto-logged: CRUD on Pages, CPT Entries, Forms, Media, Users
- Auto-pruned daily (90 days default)

### Backup System
- spatie/laravel-backup integration
- Configurable: database + filesystem
- Scheduled daily at 02:00

### Queue Monitor
- Admin UI shows pending/failed jobs
- Failed jobs pruned after 14 days

### Content Locking
- Prevents concurrent editing
- Auto-released on save or timeout

### Scheduled Publishing
- Set `published_at` + status `scheduled`
- `content:publish-scheduled` runs every minute

### Trash / Soft Deletes
- Pages, CPT Entries, Forms, Media, Redirects
- `content:purge-trash` runs daily at 02:30 (30 days)

### Revisions
- Pages: `PageRevision` — block state snapshots
- CPT Entries: `CptEntryRevision` — meta snapshots
- Restore any revision with one click

## Scheduled Tasks Summary

| Frequency | Task | Time |
|-----------|------|------|
| Every minute | `content:publish-scheduled` | — |
| Every minute | `queue:work` (via cron) | — |
| Daily | `events:complete-expired` | 00:01 |
| Daily | `backup:run` | 02:00 |
| Daily | `content:purge-trash` | 02:30 |
| Daily | `activity:prune` (90d) | 03:00 |
| Daily | `not-found-logs:prune` (90d) | 03:15 |
| Daily | `queue:prune-failed` (14d) | 03:30 |

## Key Files

| File | Purpose |
|------|---------|
| `app/Services/AdminMenuBuilder.php` | Menu registration |
| `app/Livewire/Admin/` | All admin Livewire components |
| `app/Http/Controllers/Auth/` | Auth controllers + 2FA |
| `app/Models/Activity.php` | Audit log model |
