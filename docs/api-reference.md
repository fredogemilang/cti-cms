# CMS Headless REST API Reference v1

This document provides a comprehensive reference for the **98 REST API Endpoints** built into the CMS. The API suite allows complete Headless & Hybrid integration for frontends (e.g., Next.js, Nuxt, Mobile Apps) as well as headless administrative management.

---

## Table of Contents
1. [Authentication](#authentication)
2. [Public Content APIs](#public-content-apis)
3. [Admin Management APIs](#admin-management-apis)
   - [Custom Post Types & MetaFields](#1-custom-post-types--metafields-schema)
   - [CPT Entries & Relationships](#2-cpt-entries--relationships)
   - [Pages & Page Blocks](#3-pages--page-blocks)
   - [Page Revisions](#4-page-revisions--rollback)
   - [Media Manager](#5-media-manager)
   - [SEO & GEO Metadata](#6-seo--geo-metadata)
   - [Navigation Menus](#7-navigation-menus)
   - [Form Builder & Submissions](#8-form-builder--submissions)
   - [Taxonomies & Terms](#9-taxonomies--terms)
   - [Site Settings](#10-site-settings)
   - [Redirect Rules](#11-redirect-rules)
   - [Webhooks Engine](#12-webhooks-engine)
   - [Users & Roles](#13-users--roles)
   - [Audit Activity Logs](#14-audit-activity-logs)
   - [Plugin Management](#15-plugin-management)
   - [Appearance & Themes](#16-appearance--themes)
   - [Email Templates](#17-email-templates)
   - [SEO Indexing & Sitemap Logs](#18-seo-indexing--sitemap-logs)
4. [Plugin API Auto-Discovery](#plugin-api-auto-discovery)

---

## Authentication

All Admin endpoints require an API Token passed in the HTTP Authorization header:

```http
Authorization: Bearer <your_api_token>
Accept: application/json
```

To exchange user credentials for a token:
- **`POST /api/v1/auth/login`**: Body `{"email": "...", "password": "..."}`
- **`POST /api/v1/auth/logout`**: Revokes current token.
- **`GET /api/v1/me`**: Returns authenticated user profile.

---

## Public Content APIs

Public endpoints are accessible without authentication (unless restricted by server firewall or CORS).

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/pages` | `GET` | List published pages (supports `?q=search` and `?per_page=N`) |
| `/api/v1/pages/{slug}` | `GET` | Get single published page (includes blocks, URL, and resolved `seo` payload) |
| `/api/v1/cpt/{type}` | `GET` | List published entries for a CPT (e.g. `products`, `solutions`, `jobs`) |
| `/api/v1/cpt/{type}/{slug}` | `GET` | Get single entry with meta values, relationships, and resolved `seo` payload |
| `/api/v1/menus` | `GET` | Get public navigation menu tree for header/footer rendering |
| `/api/v1/taxonomies` | `GET` | List public taxonomies |
| `/api/v1/taxonomies/{slug}/terms` | `GET` | List terms for a taxonomy (e.g. `categories`, `tags`) |
| `/api/v1/settings/public` | `GET` | Get site identity (name, tagline, logo, favicon, available_locales, active_theme) |
| `/api/v1/redirects` | `GET` | List active 301/302 redirect rules (for Edge CDN or frontend router) |
| `/api/v1/forms/{slug}/submit` | `POST` | Submit form response (throttled 30/min) |
| `/api/v1/posts` | `GET` | List blog posts (Plugin `posts`) |
| `/api/v1/posts/{slug}` | `GET` | Get single blog post detail |
| `/api/v1/posts/categories` | `GET` | List blog categories |
| `/api/v1/openapi.json` | `GET` | OpenAPI 3.1.0 JSON Specification |

---

## Admin Management APIs

All admin routes are prefixed with `/api/v1/admin/`.

### 1. Custom Post Types & MetaFields Schema
- `GET /api/v1/admin/cpt` — List all CPT schemas
- `POST /api/v1/admin/cpt` — Create CPT schema (`name`, `slug`, `singular_label`, `is_hierarchical`)
- `GET|PUT|DELETE /api/v1/admin/cpt/{id}` — Manage CPT schema
- `GET /api/v1/admin/cpt/{id}/fields` — List MetaFields for a CPT
- `POST /api/v1/admin/cpt/{id}/fields` — Create MetaField (`name`, `label`, `type`, `is_required`, `options`)
  > **Repeater Fields Payload Note**: Repeater sub-fields MUST strictly be provided under `"options": {"repeater_fields": [...]}`. Legacy `"sub_fields"` or any non-standard key (e.g. `"anak_fields"`) are strictly rejected with HTTP 422 Unprocessable Entity.
- `PUT|DELETE /api/v1/admin/cpt/{id}/fields/{fieldId}` — Manage MetaField

### 2. CPT Entries & Relationships
- `GET /api/v1/admin/cpt/{type}/entries` — List CPT entries with filters (`?status=published`, `?q=search`)
- `POST /api/v1/admin/cpt/{type}/entries` — Create entry (`title`, `slug`, `content`, `featured_image`, `meta`, `relationships`, `translations`)
- `GET|PUT|DELETE /api/v1/admin/cpt/{type}/entries/{id}` — Manage entry

### 3. Pages & Page Blocks
- `GET /api/v1/admin/pages` — List all pages
- `POST /api/v1/admin/pages` — Create page (`title`, `slug`, `template`, `status`)
- `GET|PUT|DELETE /api/v1/admin/pages/{id}` — Manage page
- `GET /api/v1/admin/pages/{id}/blocks` — List page blocks
- `POST /api/v1/admin/pages/{id}/blocks` — Add block (`name`, `type`, `value`, `order`)
- `PUT|DELETE /api/v1/admin/pages/{id}/blocks/{blockId}` — Manage block

### 4. Page Revisions & Rollback
- `GET /api/v1/admin/pages/{id}/revisions` — List page revision history
- `POST /api/v1/admin/pages/{id}/revisions/{revisionId}/restore` — Restore page to revision

### 5. Media Manager
- `POST /api/v1/admin/media/upload` — Upload file (multipart form `file`, auto WebP conversion)
- `PUT /api/v1/admin/media/{id}` — Update alt text, title, caption
- `DELETE /api/v1/admin/media/{id}` — Delete file
- `POST /api/v1/admin/media/bulk-delete` — Bulk delete media items (`{"ids": [1, 2, 3]}`)

### 6. SEO & GEO Metadata
- `GET /api/v1/admin/pages/{id}/seo` & `PUT /api/v1/admin/pages/{id}/seo` — Page SEO & GEO metadata
- `GET /api/v1/admin/cpt/{type}/entries/{id}/seo` & `PUT /api/v1/admin/cpt/{type}/entries/{id}/seo` — CPT Entry SEO & GEO
- `PUT /api/v1/admin/settings/seo` — Global SEO & GEO defaults

### 7. Navigation Menus
- `GET /api/v1/admin/menus` — List menu items
- `POST /api/v1/admin/menus` — Create menu item (`title`, `route`, `icon`, `parent_id`, `order`)
- `GET|PUT|DELETE /api/v1/admin/menus/{id}` — Manage menu item
- `POST /api/v1/admin/menus/reorder` — Reorder menu items tree

### 8. Form Builder & Submissions
- `GET|POST|PUT|DELETE /api/v1/admin/forms` — Manage form schema
- `GET /api/v1/admin/forms/{id}/entries` — List submitted responses
- `DELETE /api/v1/admin/forms/{id}/entries/{entryId}` — Delete form submission

### 9. Taxonomies & Terms
- `GET|POST|PUT|DELETE /api/v1/admin/taxonomies` — Manage CustomTaxonomies
- `GET|POST|PUT|DELETE /api/v1/admin/taxonomies/{id}/terms` — Manage TaxonomyTerms

### 10. Site Settings
- `GET /api/v1/admin/settings` — List all registered setting groups, field schemas, and current values
- `GET /api/v1/admin/settings/{group}` — Get settings for group (`general`, `content`, `auth`, `brevo`, `languages`, `string-translations`, `cache`, `cdn`, `imgopt`, `pageopt`, `api`, `permalinks`, `icons`)
- `PUT /api/v1/admin/settings/{group}` — Update settings for group

### 11. Redirect Rules
- `GET|POST|PUT|DELETE /api/v1/admin/redirects` — Manage 301/302 redirect rules

### 12. Webhooks Engine
- `GET|POST|PUT|DELETE /api/v1/admin/webhooks` — Manage webhooks
- `GET /api/v1/admin/webhooks/{id}/deliveries` — List delivery logs

### 13. Users & Roles
- `GET|POST|PUT|DELETE /api/v1/admin/users` — Manage users & assign roles
- `GET /api/v1/admin/roles` — List roles & permissions

### 14. Audit Activity Logs
- `GET /api/v1/admin/activity-logs` — Search & filter audit logs

### 15. Plugin Management
- `GET /api/v1/admin/plugins` — List plugins
- `POST /api/v1/admin/plugins/{slug}/toggle` — Enable / disable plugin (`{"is_active": true}`)

### 16. Appearance & Themes
- `GET /api/v1/admin/themes` — List installed themes & current active theme
- `POST /api/v1/admin/themes/{slug}/activate` — Activate theme

### 17. Email Templates
- `GET /api/v1/admin/email-templates` — List email templates
- `GET|PUT /api/v1/admin/email-templates/{id}` — Update email template & record version

### 18. SEO Indexing & Sitemap Logs
- `GET /api/v1/admin/seo/indexing-logs` — View IndexNow & sitemap ping history

---

## Plugin API Auto-Discovery

Every plugin can register custom API endpoints by adding `plugins/{slug}/routes/api.php`. Routes are auto-discovered when the plugin is active and disabled when deactivated. See `docs/plugin-development.md` for full instructions.
