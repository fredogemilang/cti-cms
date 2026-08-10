# Models & Relationships

## Core Models Map (36 models)

```
Page (pages)
├── PageBlock (page_blocks) — blocks with translations JSON, ordered, hierarchical
├── PageRevision (page_revisions) — block snapshot history
└── SeoMeta (seo_metas) — polymorphic, per locale

CustomPostType (custom_post_types)
├── MetaField (meta_fields) — schema definition, supports 20+ field types
├── CptEntry (cpt_entries) — content items with meta JSON, translations JSON, SoftDeletes
│   ├── CptEntryRelationship — entry-to-entry pivot (parent ↔ child)
│   ├── CptEntryRevision — meta snapshot history
│   ├── EditorialNote — per-entry comment thread
│   └── SeoMeta — polymorphic SEO per locale
├── CustomTaxonomy — taxonomy schema (categories, tags, etc.)
└── TaxonomyTerm — taxonomy terms with hierarchy

Form (forms) — SoftDeletes
├── FormField (form_fields) — with translations JSON, 15+ field types
└── FormEntry (form_entries) — submission data JSON

User (users)
├── roles (spatie/laravel-permission)
├── permissions (spatie/laravel-permission)
└── ApiToken (api_tokens) — personal access tokens for REST API

Media (media) — uploads, WebP conversion, SoftDeletes
Setting (settings) — key-value with group/type
MenuItem (menu_items) — parent/child hierarchy, dynamic routing

Redirect (redirects) — 301/302 rules, regex support, SoftDeletes
NotFoundLog (not_found_logs) — passive 404 logging, aggregated, auto-pruned

Activity (activities) — audit trail, auto-pruned 90 days
Plugin (plugins) — active/inactive state, auto-discovered
Theme (themes) — installed themes, active flag

Webhook (webhooks) — URL + events, delivery logs
EmailTemplate (email_templates) — versioned email templates
StringTranslationKey → StringTranslation → StringTranslationSource — string registry
Backup (spatie/laravel-backup) — database/filesystem backups
```

## Key Relationships

| Parent | Child | Type |
|--------|-------|------|
| Page | PageBlock | Has Many (ordered) |
| Page | PageRevision | Has Many |
| Page | SeoMeta | Morph Many |
| CustomPostType | CptEntry | Has Many |
| CustomPostType | MetaField | Has Many |
| CptEntry | CptEntry | Belongs To Many (pivot: parent_id ↔ child_id) |
| CptEntry | SeoMeta | Morph Many |
| CptEntry | CptEntryRevision | Has Many |
| CptEntry | EditorialNote | Has Many |
| Form | FormField | Has Many (ordered) |
| Form | FormEntry | Has Many |
| User | ApiToken | Has Many |
| CustomTaxonomy | TaxonomyTerm | Has Many |
| MenuItem | MenuItem | Has Many (parent_id, ordered) |

## Traits Used Across Models

| Trait | Models | Purpose |
|-------|--------|---------|
| `HasTranslations` | Page, PageBlock, CptEntry, FormField | Multi-locale JSON translations |
| `HasSeoMeta` | Page, CptEntry | Polymorphic SEO data |
| `SoftDeletes` | Form, CptEntry, Media, Redirect | Trash/recycle bin |
