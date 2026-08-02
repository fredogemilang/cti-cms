<?php

namespace App\Livewire\Admin\Cpt\Entries;

use App\Models\CptEntry;
use App\Models\CptEntryRelationship;
use App\Models\CptEntryRevision;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\MetaField;
use App\Models\TaxonomyTerm;
use App\Services\ContentLockService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class EntryForm extends Component
{
    use WithFileUploads;

    public CustomPostType $postType;

    public ?int $entryId = null;

    public bool $isEdit = false;

    public ?array $activeLock = null;

    public bool $showRevisionsModal = false;

    // Core Fields
    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public string $excerpt = '';

    public ?string $featuredImage = null;

    public string $status = 'draft';

    public ?string $publishedAt = null;

    public ?int $parentId = null;

    public int $menuOrder = 0;

    // Meta Fields
    public array $meta = [];

    // Taxonomy Terms
    public array $selectedTerms = [];

    public array $newTermInput = [];

    // UI State
    public bool $showMediaPicker = false;

    // Relationship Modal Picker UI State
    public bool $showRelationshipModal = false;

    public ?int $activeRelationshipFieldId = null;

    public string $relationshipSearch = '';

    public array $tempSelectedRelationshipIds = [];

    public function openRelationshipModal(int $fieldId)
    {
        /** @var MetaField|null $field */
        $field = $this->postType->metaFields->where('id', $fieldId)->first();
        if (! $field) {
            return;
        }

        $this->activeRelationshipFieldId = $fieldId;
        $this->relationshipSearch = '';
        $val = $this->meta[$field->name] ?? [];
        $this->tempSelectedRelationshipIds = is_array($val)
            ? array_values(array_map('intval', array_filter($val, fn ($v) => $v !== '')))
            : array_filter([(int) $val]);
        $this->showRelationshipModal = true;
    }

    public function closeRelationshipModal()
    {
        $this->showRelationshipModal = false;
        $this->activeRelationshipFieldId = null;
        $this->relationshipSearch = '';
        $this->tempSelectedRelationshipIds = [];
    }

    public function toggleRelationshipTempSelection(int $entryId)
    {
        /** @var MetaField|null $field */
        $field = $this->postType->metaFields->where('id', $this->activeRelationshipFieldId)->first();
        if (! $field) {
            return;
        }

        $cardinality = $field->options['cardinality'] ?? 'many_to_many';
        if ($cardinality === 'one_to_many') {
            $this->tempSelectedRelationshipIds = [$entryId];
        } else {
            if (in_array($entryId, $this->tempSelectedRelationshipIds, true)) {
                $this->tempSelectedRelationshipIds = array_values(
                    array_filter($this->tempSelectedRelationshipIds, fn ($id) => $id !== $entryId)
                );
            } else {
                $this->tempSelectedRelationshipIds[] = $entryId;
            }
        }
    }

    public function confirmRelationshipSelection()
    {
        /** @var MetaField|null $field */
        $field = $this->postType->metaFields->where('id', $this->activeRelationshipFieldId)->first();
        if ($field) {
            $cardinality = $field->options['cardinality'] ?? 'many_to_many';
            if ($cardinality === 'one_to_many') {
                $this->meta[$field->name] = ! empty($this->tempSelectedRelationshipIds) ? (string) $this->tempSelectedRelationshipIds[0] : '';
            } else {
                $this->meta[$field->name] = array_map('strval', $this->tempSelectedRelationshipIds);
            }
        }

        $this->closeRelationshipModal();
    }

    public function removeRelationshipItem(string $fieldName, int $entryId)
    {
        if (isset($this->meta[$fieldName])) {
            if (is_array($this->meta[$fieldName])) {
                $this->meta[$fieldName] = array_values(
                    array_filter($this->meta[$fieldName], fn ($id) => (int) $id !== $entryId)
                );
            } else {
                if ((int) $this->meta[$fieldName] === $entryId) {
                    $this->meta[$fieldName] = '';
                }
            }
        }
    }

    // === Translations state ===
    #[Url(as: 'lang', keep: true)]
    public string $editingLocale = '';

    #[Url(as: 'tab', keep: true)]
    public string $activeTab = '';

    /** Per-locale snapshots of translatable form fields: {locale: {title, slug, content, excerpt}} */
    public array $localizedSnapshots = [];

    public array $availableLocales = [];

    protected function rules(): array
    {
        $isDefaultLocale = $this->editingLocale === CptEntry::defaultLocale();

        $rules = [
            'title' => $isDefaultLocale ? 'required|string|max:255' : 'nullable|string|max:255',
            'slug' => $isDefaultLocale ? 'required|string|max:255' : 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,pending,published,scheduled,archived',
            'publishedAt' => 'nullable|date',
            'parentId' => 'nullable|integer|exists:cpt_entries,id',
            'menuOrder' => 'integer|min:0',
        ];

        // Add validation for meta fields
        foreach ($this->postType->metaFields as $field) {
            $fieldRules = [];
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific validation
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'repeater':
                    $subFields = $field->options['repeater_fields'] ?? [];
                    if (! empty($subFields)) {
                        foreach ($subFields as $subField) {
                            $subFieldId = $subField['name'] ?? $subField['id'] ?? Str::snake($subField['label'] ?? '');
                            if (! empty($subFieldId) && ! empty($subField['is_required'])) {
                                $rules['meta.'.$field->name.'.*.'.$subFieldId] = 'required';
                            }
                        }
                    }
                    $fieldRules[] = 'array';
                    break;
            }

            $rules['meta.'.$field->name] = $fieldRules;
        }

        return $rules;
    }

    public function mount(CustomPostType $postType, ?int $id = null)
    {
        $this->postType = $postType;
        $this->availableLocales = available_locales();
        $this->editingLocale = CptEntry::defaultLocale();

        // Initialize meta fields with defaults
        foreach ($postType->metaFields as $field) {
            $defaultValue = $field->default_value;

            // Handle options-based defaults (Select, Radio, Checkbox)
            if (in_array($field->type, ['select', 'radio', 'checkbox']) && isset($field->options['options_list'])) {
                $optionsList = $field->options['options_list'];

                if ($field->type === 'checkbox') {
                    // For checkbox, default is an array of selected values
                    $defaultValues = [];
                    foreach ($optionsList as $option) {
                        if (! empty($option['is_default'])) {
                            $defaultValues[] = $option['value'];
                        }
                    }
                    $defaultValue = $defaultValues;
                } else {
                    // For select/radio, find the first default
                    foreach ($optionsList as $option) {
                        if (! empty($option['is_default'])) {
                            $defaultValue = $option['value'];
                            break;
                        }
                    }
                }
            }

            $this->meta[$field->name] = $defaultValue ?? '';

            // Ensure array for checkbox/gallery/repeater if empty logic
            if (($field->type === 'checkbox' || $field->type === 'gallery' || $field->type === 'repeater') && ! is_array($this->meta[$field->name])) {
                $this->meta[$field->name] = [];
            }
        }

        $requestedLocale = request()->query('lang');
        if ($id) {
            $this->entryId = $id;
            $this->isEdit = true;
            $this->loadEntry();

            if ($requestedLocale && in_array($requestedLocale, $this->availableLocales, true) && $requestedLocale !== CptEntry::defaultLocale()) {
                $this->switchLocale($requestedLocale);
            }
            $this->refreshLock();
        }
    }

    public function addRepeaterRow($fieldName)
    {
        // Find the field definition
        $field = $this->postType->metaFields->where('name', $fieldName)->first();
        $options = is_array($field?->getAttribute('options')) ? $field->getAttribute('options') : [];
        $subFields = $options['repeater_fields'] ?? [];

        if ($field && $field->type === 'repeater' && ! empty($subFields)) {
            $newRow = [];
            foreach ($subFields as $subField) {
                // Initialize based on sub-field type
                $rowKey = $subField['name'] ?? $subField['id'] ?? Str::snake($subField['label'] ?? 'field_'.$loop->index);

                // Determine default value
                $defaultValue = '';
                if (isset($subField['options']['options_list']) && is_array($subField['options']['options_list'])) {
                    foreach ($subField['options']['options_list'] as $option) {
                        if (isset($option['is_default']) && $option['is_default']) {
                            $defaultValue = $option['value'];
                            break;
                        }
                    }
                }

                $newRow[$rowKey] = $defaultValue;
            }

            // Ensure the meta field is an array
            if (! isset($this->meta[$fieldName]) || ! is_array($this->meta[$fieldName])) {
                $this->meta[$fieldName] = [];
            }

            $this->meta[$fieldName][] = $newRow;
        }
    }

    public function removeRepeaterRow($fieldName, $index)
    {
        if (isset($this->meta[$fieldName][$index])) {
            unset($this->meta[$fieldName][$index]);
            $this->meta[$fieldName] = array_values($this->meta[$fieldName]);
        }
    }

    #[On('media-selected')]
    public function onMediaSelected($field, $mediaId = null, $mediaPath = null, $mediaUrl = null)
    {
        if ($field === 'featured_image') {
            $this->featuredImage = $mediaPath;
        } elseif (str_starts_with($field, 'meta.')) {
            $cleanPath = substr($field, 5);
            $fieldName = str_replace('meta.', '', $field);
            $this->meta[$fieldName] = $mediaPath;
            data_set($this->meta, $cleanPath, $mediaPath);
        } elseif (str_starts_with($field, 'gallery_add.')) {
            $fieldName = str_replace('gallery_add.', '', $field);
            if (! isset($this->meta[$fieldName])) {
                $this->meta[$fieldName] = [];
            }
            $this->meta[$fieldName][] = $mediaPath;
        }
    }

    #[On('media-selected-multiple')]
    public function onMediaSelectedMultiple($field, array $mediaPaths)
    {
        if (str_starts_with($field, 'gallery_add.')) {
            $fieldName = str_replace('gallery_add.', '', $field);
            if (! isset($this->meta[$fieldName]) || ! is_array($this->meta[$fieldName])) {
                $this->meta[$fieldName] = [];
            }
            foreach ($mediaPaths as $path) {
                if (! in_array($path, $this->meta[$fieldName], true)) {
                    $this->meta[$fieldName][] = $path;
                }
            }
            $this->meta[$fieldName] = array_values($this->meta[$fieldName]);
        }
    }

    #[On('media-removed')]
    public function onMediaRemoved($field)
    {
        if ($field === 'featured_image') {
            $this->featuredImage = null;
        }
        // Handle Meta Fields
        elseif (str_starts_with($field, 'meta.')) {
            $fieldName = str_replace('meta.', '', $field);
            $this->meta[$fieldName] = null;
        }
    }

    public function removeGalleryImage($fieldName, $index)
    {
        if (isset($this->meta[$fieldName][$index])) {
            unset($this->meta[$fieldName][$index]);
            $this->meta[$fieldName] = array_values($this->meta[$fieldName]);
        }
    }

    protected function loadEntry()
    {
        $entry = CptEntry::with('terms')->findOrFail($this->entryId);

        $this->title = $entry->title;
        $this->slug = $entry->slug;
        $this->content = $entry->content ?? '';
        $this->excerpt = $entry->excerpt ?? '';
        $this->featuredImage = $entry->featured_image;
        $this->status = $entry->status;
        $this->publishedAt = $entry->published_at?->format('Y-m-d\TH:i');
        $this->parentId = $entry->parent_id;
        $this->menuOrder = $entry->menu_order;

        // Load meta values and normalize repeater subfield keys
        if ($entry->meta) {
            foreach ($entry->meta as $key => $value) {
                $metaField = $this->postType->metaFields->where('name', $key)->first();
                if ($metaField && $metaField->type === 'repeater' && is_array($value)) {
                    $subFields = $metaField->options['repeater_fields'] ?? [];
                    $normalizedRows = [];
                    foreach ($value as $row) {
                        if (! is_array($row)) {
                            $normalizedRows[] = $row;

                            continue;
                        }
                        $normalizedRow = $row;
                        foreach ($subFields as $loopIdx => $subField) {
                            $targetKey = $subField['name'] ?? $subField['id'] ?? Str::snake($subField['label'] ?? 'field_'.$loopIdx);
                            $camelKey = Str::camel($targetKey);
                            $snakeKey = Str::snake($targetKey);

                            if (! isset($normalizedRow[$targetKey])) {
                                if (isset($normalizedRow[$camelKey])) {
                                    $normalizedRow[$targetKey] = $normalizedRow[$camelKey];
                                } elseif (isset($normalizedRow[$snakeKey])) {
                                    $normalizedRow[$targetKey] = $normalizedRow[$snakeKey];
                                }
                            }
                        }
                        $normalizedRows[] = $normalizedRow;
                    }
                    $this->meta[$key] = $normalizedRows;
                } else {
                    $this->meta[$key] = $value;
                }
            }
        }

        // Load selected terms by taxonomy
        foreach ($entry->terms as $term) {
            $this->selectedTerms[$term->taxonomy_id][] = $term->id;
        }

        // Load relationship values from pivot table
        $rels = CptEntryRelationship::where('parent_entry_id', $entry->id)
            ->orderBy('order')
            ->get();
        foreach ($rels as $rel) {
            $metaField = MetaField::find($rel->meta_field_id);
            if ($metaField) {
                $cardinality = $metaField->options['cardinality'] ?? 'many_to_many';
                if ($cardinality === 'one_to_many') {
                    $this->meta[$metaField->name] = (string) $rel->child_entry_id;
                } else {
                    if (! isset($this->meta[$metaField->name]) || ! is_array($this->meta[$metaField->name])) {
                        $this->meta[$metaField->name] = [];
                    }
                    $this->meta[$metaField->name][] = (string) $rel->child_entry_id;
                }
            }
        }

        // Load reverse relationship (where current entry is child_entry_id)
        $childRels = CptEntryRelationship::where('child_entry_id', $entry->id)->get();
        foreach ($childRels as $cRel) {
            $parentVendorField = $this->postType->metaFields->where('name', 'parent_vendor_id')->first();
            if ($parentVendorField) {
                $this->meta['parent_vendor_id'] = (string) $cRel->parent_entry_id;
            }
        }

        // Hydrate per-locale snapshots from translations JSON column AND meta _translations
        $defaultLocale = CptEntry::defaultLocale();
        $defaultMeta = $entry->meta ?? [];
        $metaTranslations = $defaultMeta['_translations'] ?? [];
        unset($defaultMeta['_translations']);

        $this->localizedSnapshots[$defaultLocale] = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'meta' => $defaultMeta,
        ];

        $translations = $entry->translations ?? [];
        $allLocales = array_unique(array_merge(array_keys($translations), array_keys($metaTranslations)));
        foreach ($allLocales as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $fields = $translations[$locale] ?? [];
            $mFields = $metaTranslations[$locale] ?? [];

            $this->localizedSnapshots[$locale] = [
                'title' => $fields['title'] ?? '',
                'slug' => $fields['slug'] ?? '',
                'content' => $fields['content'] ?? '',
                'excerpt' => $fields['excerpt'] ?? '',
                'meta' => $mFields,
            ];
        }

        if ($this->editingLocale !== $defaultLocale) {
            $this->meta = $this->syncMediaKeysFromDefault($defaultMeta, $this->localizedSnapshots[$this->editingLocale]['meta'] ?? []);
        }

    }

    /**
     * Overlay default locale (EN) icon, image, and media keys onto non-default locale (ID) meta.
     */
    protected function syncMediaKeysFromDefault(array $defaultMeta, array $targetMeta): array
    {
        $merged = array_merge($defaultMeta, $targetMeta);

        foreach ($merged as $key => &$val) {
            if (is_array($val) && isset($defaultMeta[$key]) && is_array($defaultMeta[$key])) {
                foreach ($val as $idx => &$item) {
                    if (is_array($item) && isset($defaultMeta[$key][$idx]) && is_array($defaultMeta[$key][$idx])) {
                        foreach (['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'] as $mediaKey) {
                            if (isset($defaultMeta[$key][$idx][$mediaKey])) {
                                $item[$mediaKey] = $defaultMeta[$key][$idx][$mediaKey];
                            }
                        }
                    }
                }
                unset($item);
            }
        }
        unset($val);

        return $merged;
    }

    /**
     * Push any icon, image, and media keys updated in non-default locale (ID) back to the default locale (EN) snapshot.
     */
    protected function pushMediaKeysToDefault(array $sourceMeta, array &$defaultMeta): void
    {
        $mediaKeyList = ['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'];

        foreach ($sourceMeta as $key => $val) {
            // Top-level media keys
            if (in_array($key, $mediaKeyList, true) && ! empty($val)) {
                $defaultMeta[$key] = $val;
            }

            // Array media keys (e.g. benefits_cards, features)
            if (is_array($val) && isset($defaultMeta[$key]) && is_array($defaultMeta[$key])) {
                foreach ($val as $idx => $item) {
                    if (is_array($item) && isset($defaultMeta[$key][$idx]) && is_array($defaultMeta[$key][$idx])) {
                        foreach ($mediaKeyList as $mKey) {
                            if (isset($item[$mKey]) && ! empty($item[$mKey])) {
                                $defaultMeta[$key][$idx][$mKey] = $item[$mKey];
                            }
                        }
                    }
                }
            }
        }
    }

    /** Switch the form between locale tabs (mirrors PageForm pattern). */
    public function switchLocale(string $newLocale): void
    {
        if ($newLocale === $this->editingLocale) {
            return;
        }
        if (! in_array($newLocale, $this->availableLocales, true)) {
            return;
        }

        $defaultLocale = CptEntry::defaultLocale();

        // Snapshot current form into the OLD locale's slot.
        if ($this->editingLocale === $defaultLocale) {
            $this->localizedSnapshots[$defaultLocale] = $this->currentLocaleFormSnapshot();
        } else {
            $snapshot = $this->currentLocaleFormSnapshot();
            $defaultMeta = &$this->localizedSnapshots[$defaultLocale]['meta'];

            // Push any media/icon edits from ID tab to EN snapshot
            $this->pushMediaKeysToDefault($snapshot['meta'], $defaultMeta);

            // Extract only non-media meta keys whose values differ from default locale
            $deltaMeta = [];
            $mediaKeyList = ['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'];
            foreach ($snapshot['meta'] as $key => $value) {
                if (in_array($key, $mediaKeyList, true)) {
                    continue;
                }
                if (! array_key_exists($key, $defaultMeta) || $value !== ($defaultMeta[$key] ?? null)) {
                    $deltaMeta[$key] = $value;
                }
            }
            $snapshot['meta'] = $deltaMeta;
            $this->localizedSnapshots[$this->editingLocale] = $snapshot;
        }

        // Load NEW locale's snapshot (blank if none yet)
        $next = $this->localizedSnapshots[$newLocale] ?? [];
        $this->title = $next['title'] ?? '';
        $this->slug = $next['slug'] ?? '';
        $this->content = $next['content'] ?? '';
        $this->excerpt = $next['excerpt'] ?? '';

        // Load NEW locale's meta
        $defaultMeta = $this->localizedSnapshots[$defaultLocale]['meta'] ?? [];
        $newLocaleMeta = $next['meta'] ?? [];

        if ($newLocale === $defaultLocale) {
            $this->meta = $defaultMeta;
        } else {
            $this->meta = $this->syncMediaKeysFromDefault($defaultMeta, $newLocaleMeta);
        }

        // Notify SeoMetaBox to switch locale
        $this->dispatch('seo-locale-switched', locale: $newLocale);

        $this->editingLocale = $newLocale;
        $this->resetErrorBag();
    }

    protected function currentLocaleFormSnapshot(): array
    {
        $cleanMeta = $this->meta;
        unset($cleanMeta['_translations']);

        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'meta' => $cleanMeta,
        ];
    }

    public function updatedTitle($value)
    {
        if (! $this->isEdit && empty($this->slug)) {
            $this->slug = $this->ensureUniqueSlug(Str::slug($value));
        }
    }

    public function generateSlug()
    {
        $this->slug = $this->ensureUniqueSlug(Str::slug($this->title));
    }

    protected function ensureUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $slugQuery = CptEntry::withTrashed()
                ->where('post_type_id', $this->postType->id)
                ->where('slug', $slug);

            if ($this->isEdit) {
                $slugQuery->where('id', '!=', $this->entryId);
            }

            if (! $slugQuery->exists()) {
                break;
            }

            $counter++;
            $slug = $originalSlug.'-'.$counter;
        }

        return $slug;
    }

    public function toggleTerm(int $taxonomyId, int $termId)
    {
        if (! isset($this->selectedTerms[$taxonomyId])) {
            $this->selectedTerms[$taxonomyId] = [];
        }

        if (in_array($termId, $this->selectedTerms[$taxonomyId])) {
            $this->selectedTerms[$taxonomyId] = array_values(
                array_filter($this->selectedTerms[$taxonomyId], fn ($id) => $id !== $termId)
            );
        } else {
            $this->selectedTerms[$taxonomyId][] = $termId;
        }
    }

    public function setFeaturedImage(?string $path)
    {
        $this->featuredImage = $path;
        $this->showMediaPicker = false;
    }

    public function removeFeaturedImage()
    {
        $this->featuredImage = null;
    }

    #[On('icon-selected')]
    public function handleIconSelected(string $field, ?string $value): void
    {
        if (str_starts_with($field, 'meta.')) {
            $path = substr($field, 5);
            data_set($this->meta, $path, $value);
        }
    }

    #[On('set-value')]
    public function handleSetValue(string $path, mixed $value): void
    {
        if (str_starts_with($path, 'meta.')) {
            $cleanPath = substr($path, 5);
            data_set($this->meta, $cleanPath, $value);
        }
    }

    public function save()
    {
        // Mirror current form into the active locale's snapshot before validating.
        // For non-default locales, store only the delta (same logic as switchLocale).
        $defaultLocale = CptEntry::defaultLocale();
        if ($this->editingLocale === $defaultLocale) {
            $this->localizedSnapshots[$defaultLocale] = $this->currentLocaleFormSnapshot();
        } else {
            $snapshot = $this->currentLocaleFormSnapshot();
            $defaultMeta = &$this->localizedSnapshots[$defaultLocale]['meta'];

            // Push any media/icon edits from ID tab to EN snapshot
            $this->pushMediaKeysToDefault($snapshot['meta'], $defaultMeta);

            // Extract only non-media meta keys whose values differ from default locale
            $deltaMeta = [];
            $mediaKeyList = ['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'];
            foreach ($snapshot['meta'] as $key => $value) {
                if (in_array($key, $mediaKeyList, true)) {
                    continue;
                }
                if (! array_key_exists($key, $defaultMeta) || $value !== ($defaultMeta[$key] ?? null)) {
                    $deltaMeta[$key] = $value;
                }
            }
            $snapshot['meta'] = $deltaMeta;
            $this->localizedSnapshots[$this->editingLocale] = $snapshot;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'There are validation errors. Please check the form.',
            ]);
            throw $e;
        }

        $defaultSnap = $this->localizedSnapshots[$defaultLocale] ?? $this->currentLocaleFormSnapshot();

        // Default-locale slug uniqueness — only enforce when we have a real slug to dedupe
        if (! empty($defaultSnap['slug'])) {
            $defaultSnap['slug'] = $this->ensureUniqueSlug($defaultSnap['slug']);
        }

        // Build translations JSON and meta _translations JSON from non-default locale snapshots
        $translations = [];
        $metaTranslations = [];

        foreach ($this->localizedSnapshots as $locale => $snap) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $localeFields = array_filter([
                'title' => ($snap['title'] ?? '') ?: null,
                'slug' => ($snap['slug'] ?? '') ?: null,
                'content' => ($snap['content'] ?? '') ?: null,
                'excerpt' => ($snap['excerpt'] ?? '') ?: null,
            ], fn ($v) => $v !== null);
            if (! empty($localeFields)) {
                $translations[$locale] = $localeFields;
            }

            if (! empty($snap['meta']) && is_array($snap['meta'])) {
                $mSnap = $snap['meta'];
                $defaultMetaSnap = $defaultSnap['meta'] ?? [];
                foreach ($mSnap as $mKey => &$mVal) {
                    if (is_array($mVal) && isset($defaultMetaSnap[$mKey]) && is_array($defaultMetaSnap[$mKey])) {
                        foreach ($mVal as $idx => &$subItem) {
                            if (is_array($subItem) && isset($defaultMetaSnap[$mKey][$idx]) && is_array($defaultMetaSnap[$mKey][$idx])) {
                                foreach (['icon', 'icon_type', 'image', 'logo', 'media', 'banner_logo', 'about_image'] as $mediaKey) {
                                    if (isset($defaultMetaSnap[$mKey][$idx][$mediaKey])) {
                                        $subItem[$mediaKey] = $defaultMetaSnap[$mKey][$idx][$mediaKey];
                                    }
                                }
                            }
                        }
                        unset($subItem);
                    }
                }
                unset($mVal);
                $metaTranslations[$locale] = $mSnap;
            }
        }

        $finalMeta = $defaultSnap['meta'] ?? $this->meta;
        unset($finalMeta['_translations']);
        if (! empty($metaTranslations)) {
            $finalMeta['_translations'] = $metaTranslations;
        }

        $data = [
            'post_type_id' => $this->postType->id,
            'title' => $defaultSnap['title'] ?? '',
            'slug' => $defaultSnap['slug'] ?? '',
            'content' => ($defaultSnap['content'] ?? '') ?: null,
            'excerpt' => ($defaultSnap['excerpt'] ?? '') ?: null,
            'featured_image' => $this->featuredImage,
            'status' => $this->status,
            'published_at' => $this->status === 'published' && ! $this->publishedAt
                ? now()
                : ($this->publishedAt ? Carbon::parse($this->publishedAt) : null),
            'parent_id' => $this->parentId,
            'menu_order' => $this->menuOrder,
            'updated_by' => auth()->id(),
            'meta' => $finalMeta,
            'translations' => $translations ?: null,
        ];

        if ($this->isEdit) {
            $entry = CptEntry::findOrFail($this->entryId);
            $entry->update($data);
        } else {
            $entry = CptEntry::create($data);
            $this->entryId = $entry->id;
        }

        $this->releaseLock();

        // Sync taxonomy terms
        $allTerms = [];
        foreach ($this->selectedTerms as $termIds) {
            $allTerms = array_merge($allTerms, $termIds);
        }
        $entry->terms()->sync($allTerms);

        // Sync Relationship fields into cpt_entry_relationships table
        foreach ($this->postType->metaFields as $field) {
            /** @var MetaField $field */
            if ($field->type === 'relationship') {
                if ($field->name === 'parent_vendor_id') {
                    $parentMetaFieldId = MetaField::where('name', 'product_id')
                        ->where('options->target_cpt', 'tech-products')
                        ->value('id') ?? 43;

                    CptEntryRelationship::where('child_entry_id', $entry->id)
                        ->where('meta_field_id', $parentMetaFieldId)
                        ->delete();

                    $parentVendorId = $this->meta['parent_vendor_id'] ?? null;
                    if (! empty($parentVendorId)) {
                        $parentEntryId = (int) (is_array($parentVendorId) ? ($parentVendorId[0] ?? 0) : $parentVendorId);
                        if ($parentEntryId > 0) {
                            CptEntryRelationship::create([
                                'parent_entry_id' => $parentEntryId,
                                'child_entry_id' => $entry->id,
                                'meta_field_id' => $parentMetaFieldId,
                                'order' => 0,
                            ]);

                            $parentEntry = CptEntry::find($parentEntryId);
                            if ($parentEntry) {
                                $metaData = $entry->meta ?? [];
                                $metaData['parent_vendor'] = $parentEntry->slug;
                                $entry->meta = $metaData;
                                if (empty($entry->featured_image) && ! empty($parentEntry->featured_image)) {
                                    $entry->featured_image = $parentEntry->featured_image;
                                }
                                $entry->save();
                            }
                        }
                    }
                } else {
                    CptEntryRelationship::where('parent_entry_id', $entry->id)
                        ->where('meta_field_id', $field->id)
                        ->delete();

                    $val = $this->meta[$field->name] ?? null;
                    if (! empty($val)) {
                        $childIds = is_array($val) ? $val : [$val];
                        $uniqueChildIds = array_values(array_unique(array_filter($childIds)));
                        foreach ($uniqueChildIds as $order => $childId) {
                            CptEntryRelationship::updateOrCreate([
                                'parent_entry_id' => $entry->id,
                                'child_entry_id' => (int) $childId,
                                'meta_field_id' => $field->id,
                            ], [
                                'order' => $order,
                            ]);
                        }
                    }
                }
            }
        }

        // Notify SeoMetaBox to save/attach
        $this->dispatch('seo-attach', id: $entry->id);

        // Create revision snapshot
        CptEntryRevision::create([
            'cpt_entry_id' => $entry->id,
            'user_id' => auth()->id(),
            'title' => $entry->title,
            'slug' => $entry->slug,
            'status' => $entry->status,
            'meta' => $entry->meta,
            'translations' => $entry->translations,
            'is_autosave' => false,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->isEdit
                ? "'{$this->title}' updated successfully."
                : "'{$this->title}' created successfully.",
        ]);

        $queryParams = array_filter([
            'lang' => $this->editingLocale !== CptEntry::defaultLocale() ? $this->editingLocale : null,
            'tab' => $this->activeTab ?: null,
        ]);

        return redirect()->route('admin.cpt.entries.edit', array_merge(
            ['postTypeSlug' => $this->postType->slug, 'id' => $entry->id],
            $queryParams
        ));
    }

    public function saveAsDraft()
    {
        $originalStatus = $this->status;
        $this->status = 'draft';

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->status = $originalStatus;
            throw $e;
        }
    }

    public function publish()
    {
        $originalStatus = $this->status;
        $originalPublishedAt = $this->publishedAt;

        $this->status = 'published';
        $this->publishedAt = now()->format('Y-m-d\TH:i');

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->status = $originalStatus;
            $this->publishedAt = $originalPublishedAt;
            throw $e;
        }
    }

    public function createTerm(int $taxonomyId)
    {
        $name = trim($this->newTermInput[$taxonomyId] ?? '');

        if (empty($name)) {
            return;
        }

        // Check for duplicate name in this taxonomy to prevent errors
        $exists = TaxonomyTerm::where('taxonomy_id', $taxonomyId)
            ->where('name', $name)
            ->exists();

        if ($exists) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Term '{$name}' already exists.",
            ]);

            return;
        }

        // Create term with auto-slug
        // Assuming slug should be unique per taxonomy
        $slug = Str::slug($name);
        // Handle slug collision simplistically if needed, but for now simple slug

        $term = TaxonomyTerm::create([
            'taxonomy_id' => $taxonomyId,
            'name' => $name,
            'slug' => $slug,
            // 'order' => 0, // default
        ]);

        // Auto-select the new term
        if (! isset($this->selectedTerms[$taxonomyId])) {
            $this->selectedTerms[$taxonomyId] = [];
        }
        // Assuming selectedTerms is array of IDs
        $this->selectedTerms[$taxonomyId][] = $term->id;

        // Clear input
        $this->newTermInput[$taxonomyId] = '';

        // Notify
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Term '{$term->name}' created.",
        ]);
    }

    public function render()
    {
        $taxonomies = CustomTaxonomy::active()
            ->forPostType($this->postType->slug)
            ->with(['metaFields'])
            ->get();

        // Get terms for each taxonomy
        $taxonomyTerms = [];
        foreach ($taxonomies as $taxonomy) {
            $allTerms = TaxonomyTerm::ofTaxonomy($taxonomy->id)
                ->orderBy('order')
                ->get();

            if ($taxonomy->is_hierarchical) {
                $taxonomyTerms[$taxonomy->id] = $this->flattenTerms($allTerms);
            } else {
                $taxonomyTerms[$taxonomy->id] = $allTerms;
                foreach ($taxonomyTerms[$taxonomy->id] as $term) {
                    $term->depth = 0;
                }
            }
        }

        // Get possible parents for hierarchical post types
        $possibleParents = [];
        if ($this->postType->is_hierarchical) {
            $query = CptEntry::where('post_type_id', $this->postType->id)
                ->where('status', '!=', 'archived')
                ->orderBy('title');

            if ($this->isEdit) {
                $query->where('id', '!=', $this->entryId);
            }

            $possibleParents = $query->get();
        }

        // Get metaboxes and group fields
        $metaBoxes = $this->postType->settings['meta_boxes'] ?? [];
        $groupedFields = [];

        foreach ($this->postType->metaFields as $field) {
            /** @var MetaField $field */
            $group = $field->field_group ?: 'default';
            $groupedFields[$group][] = $field;
        }

        $normalBoxes = collect($metaBoxes)->filter(fn ($box) => ($box['context'] ?? 'normal') === 'normal' && isset($groupedFields[$box['id']]));
        if (empty($this->activeTab) && $normalBoxes->isNotEmpty()) {
            $this->activeTab = $normalBoxes->first()['id'];
        }

        $targetEntriesByField = [];
        foreach ($this->postType->metaFields as $field) {
            /** @var MetaField $field */
            if ($field->type === 'relationship') {
                $targetCptId = $field->options['target_post_type_id'] ?? null;
                if (! $targetCptId && ! empty($field->options['target_cpt'])) {
                    $targetCpt = CustomPostType::where('slug', $field->options['target_cpt'])->first();
                    $targetCptId = $targetCpt?->id;
                }

                $selectedValue = $this->meta[$field->name] ?? [];
                $selectedRaw = is_array($selectedValue) ? $selectedValue : [$selectedValue];
                $selectedIds = [];
                foreach ($selectedRaw as $v) {
                    if ($v !== '' && $v !== null) {
                        $selectedIds[] = (int) $v;
                    }
                }
                $selectedIds = array_filter($selectedIds);

                $query = CptEntry::query()
                    ->where('status', 'published')
                    ->where('id', '!=', $this->entryId ?? 0);

                if ($targetCptId) {
                    $query->where(function ($q) use ($targetCptId, $selectedIds) {
                        $q->where('post_type_id', $targetCptId);
                        if (! empty($selectedIds)) {
                            $q->orWhereIn('id', $selectedIds);
                        }
                    });
                } else {
                    if (! empty($selectedIds)) {
                        $query->whereIn('id', $selectedIds);
                    }
                }

                $targetEntriesByField[$field->id] = $query->orderBy('title')->get(['id', 'title', 'slug']);
            }
        }

        $entryModel = ($this->isEdit && $this->entryId) ? CptEntry::find($this->entryId) : null;
        if (! $entryModel) {
            $entryModel = new CptEntry([
                'post_type_id' => $this->postType->id,
                'slug' => $this->slug ?: 'example-slug',
            ]);
            $entryModel->setRelation('postType', $this->postType);
        }

        $frontendUrl = $entryModel->getUrl($this->editingLocale ?: null);
        if ($this->isEdit && $this->status !== 'published') {
            $previewUrl = route('admin.cpt-entries.preview', $this->entryId);
        } else {
            $previewUrl = $frontendUrl;
        }

        $fullPath = parse_url($frontendUrl, PHP_URL_PATH) ?? '/'.$this->postType->slug.'/'.$this->slug;
        $trimmedPath = rtrim($fullPath, '/');
        $lastSlashPos = strrpos($trimmedPath, '/');
        $permalinkPrefix = ($lastSlashPos !== false) ? substr($trimmedPath, 0, $lastSlashPos + 1) : '/'.$this->postType->slug.'/';

        $revisions = ($this->isEdit && $this->entryId)
            ? CptEntryRevision::with('user')->where('cpt_entry_id', $this->entryId)->latest()->take(20)->get()
            : collect();

        if ($this->isEdit && $this->entryId && auth()->check()) {
            $lockService = app(ContentLockService::class);
            $this->activeLock = $lockService->check('cpt_entry', $this->entryId, auth()->id());
            if (! $this->activeLock) {
                $lockService->acquire('cpt_entry', $this->entryId, auth()->id());
            }
        }

        return view('livewire.admin.cpt.entries.entry-form', [
            'taxonomies' => $taxonomies,
            'taxonomyTerms' => $taxonomyTerms,
            'possibleParents' => $possibleParents,
            'metaBoxes' => $metaBoxes,
            'groupedFields' => $groupedFields,
            'targetEntriesByField' => $targetEntriesByField,
            'previewUrl' => $previewUrl,
            'permalinkPrefix' => $permalinkPrefix,
            'revisions' => $revisions,
        ]);
    }

    public function refreshLock()
    {
        if ($this->isEdit && $this->entryId) {
            $entry = CptEntry::find($this->entryId);
            if ($entry && ! $entry->isLockedByOther(auth()->id())) {
                $entry->lock(auth()->id());
            }
        }
    }

    public function takeOverLock()
    {
        if ($this->isEdit && $this->entryId) {
            $entry = CptEntry::find($this->entryId);
            if ($entry) {
                $entry->lock(auth()->id());
            }
        }
    }

    public function releaseLock()
    {
        if ($this->isEdit && $this->entryId) {
            $entry = CptEntry::find($this->entryId);
            if ($entry && ! $entry->isLockedByOther(auth()->id())) {
                $entry->unlock();
            }
        }
    }

    public function openRevisionsModal()
    {
        $this->showRevisionsModal = true;
    }

    public function closeRevisionsModal()
    {
        $this->showRevisionsModal = false;
    }

    public function restoreRevision(int $revisionId)
    {
        if (! $this->isEdit || ! $this->entryId) {
            return;
        }

        $revision = CptEntryRevision::where('cpt_entry_id', $this->entryId)->findOrFail($revisionId);

        $this->title = $revision->title ?? $this->title;
        $this->slug = $revision->slug ?? $this->slug;
        $this->status = $revision->status ?? $this->status;
        if (! empty($revision->meta) && is_array($revision->meta)) {
            $this->meta = $revision->meta;
        }

        $this->save();
        $this->closeRevisionsModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Restored from CPT revision successfully!']);
    }

    private function flattenTerms($allTerms, $parentId = null, $depth = 0)
    {
        $result = collect();
        $items = $allTerms->where('parent_id', $parentId);

        foreach ($items as $item) {
            $item->depth = $depth;
            $result->push($item);
            $result = $result->merge($this->flattenTerms($allTerms, $item->id, $depth + 1));
        }

        return $result;
    }
}
