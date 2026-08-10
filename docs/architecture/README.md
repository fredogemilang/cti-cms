# Architecture Reference

> Deep dives into each subsystem of CTI CMS. Read the relevant file before modifying that area.

| System | File | Key Concepts |
|--------|------|-------------|
| Models & Relationships | [models-relationships.md](models-relationships.md) | 36 models, DB schema, polymorphic SEO |
| Page Builder | [page-builder.md](page-builder.md) | PageBlock system, locale switching, repeaters |
| CPT System | [cpt-system.md](cpt-system.md) | CustomPostType, CptEntry, MetaField, meta_boxes |
| SEO System | [seo-system.md](seo-system.md) | SeoMeta, InjectSeoTags, SchemaBuilder, breadcrumbs |
| Sitemap & Feed | [sitemap-feed.md](sitemap-feed.md) | Multi-locale sitemap, RSS/Atom, LLMs.txt |
| Redirect & 404 | [redirect-404.md](redirect-404.md) | Redirect Manager, 404 Logger |
| i18n & Localization | [i18n-system.md](i18n-system.md) | Three translation systems, HasTranslations trait |
| Form System | [form-system.md](form-system.md) | Form builder, field types, submissions |
| Theme System | [theme-system.md](theme-system.md) | ThemeLoader, theme.json, resolve_block_asset |
| Middleware Stack | [middleware-stack.md](middleware-stack.md) | 13 middleware layers in order |
| Filter & Hook System | [filter-hook-system.md](filter-hook-system.md) | WordPress-style filters, URL overrides |
| API & Auth | [api-auth-system.md](api-auth-system.md) | REST API v1, 2FA, tokens, webhooks |
| Settings System | [settings-system.md](settings-system.md) | CmsSettingsServiceProvider, setting() helper |
| Admin Panel | [admin-panel.md](admin-panel.md) | Admin menu, activity log, backup, queue, trash |
| Plugin System | [plugin-system.md](plugin-system.md) | Plugin architecture, auto-discovery |

## Quick Navigation by Task

| Task | Read |
|------|------|
| Add new page template | page-builder.md → theme-system.md |
| Add new CPT type | cpt-system.md → seo-system.md |
| Add new language | i18n-system.md |
| Debug form submission | form-system.md |
| Debug 404 | redirect-404.md → middleware-stack.md |
| Add admin page | admin-panel.md → api-auth-system.md |
| Create plugin | plugin-system.md |
