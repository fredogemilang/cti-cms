# Project Rules & Guidelines for CTI CMS

## CPT MetaBox & Field Group Strict Alignment Rule
Whenever adding or modifying a `MetaField` for any `CustomPostType` (`cpt_entries`):
1. **Register MetaBox Tab**: Always inspect `$cpt->settings['meta_boxes']`. If adding a new field or field group, register `{"id": "box_id", "title": "Box Title", "context": "normal"}` into `$cpt->settings['meta_boxes']`.
2. **Match Field Group**: Always set `MetaField.field_group = "box_id"` matching the exact `id` in `$cpt->settings['meta_boxes']`.
3. **Avoid Unmapped Fields**: Never leave `field_group` as `null` or unmapped, otherwise the field falls out of the main tab container into an unorganized fallback box (`CUSTOM FIELDS`).
