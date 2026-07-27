# Project Agent Guidelines

## Theme & Content Architecture: CPT vs. Repeater Field Selection

When scaffolding custom content, building themes, or designing API integration scripts for pages:

### Decision Matrix
1. **Use Custom Post Type (CPT)** if:
   - Content items are global entities reused across multiple pages or archives (e.g., *Products, Case Studies, Job Openings, Testimonials*).
   - Each item requires its own dedicated detail URL (e.g., `/products/cloud-security`).
   - Items require taxonomy filtering, global search, or sitewide relationship bindings.

2. **Use Repeater Block Field (`theme.json`)** if:
   - Content items are **repeatable UI elements bound to a single page layout** (e.g., *Area of Expertise cards on Homepage*, *FAQ accordions on About page*, *Pricing cards on Landing page*, *Feature highlights*).
   - Cards or columns might expand or change order in the future, but items do **NOT** need standalone detail URLs or sitewide archives.
   - Non-technical admins need a simple add/remove/reorder interface in the Page Block Builder to manage columns without creating separate CPT entries.

For full implementation patterns, see [docs/theme-development.md](file:///c:/laragon/www/cdt/backend/docs/theme-development.md#cpt-vs-repeater-field-selection-architecture).
