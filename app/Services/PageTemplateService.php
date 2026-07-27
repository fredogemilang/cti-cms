<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageBlock;

class PageTemplateService
{
    public function __construct(protected ThemeLoader $themeLoader) {}

    /**
     * Get block preset definitions for a template from the active theme.
     * Returns array of ['name', 'type', 'label', 'default', 'options'].
     */
    public function getTemplateSchema(string $templateName): array
    {
        $theme = $this->themeLoader->getActiveTheme();

        return $theme ? $theme->getTemplateBlockSchema($templateName) : [];
    }

    /**
     * Seed blocks from template preset onto a page.
     * Only creates blocks that don't already exist on the page (matched by name).
     * Returns names of newly created blocks.
     */
    public function seedBlocks(Page $page): array
    {
        $seeded = [];
        $schema = $this->getTemplateSchema($page->template);
        $schemaBlockNames = collect($schema)->pluck('name')->toArray();

        // 1. Delete orphaned blocks no longer defined in template schema
        if (! empty($schemaBlockNames)) {
            PageBlock::where('page_id', $page->id)
                ->whereNull('parent_block_id')
                ->whereNotIn('name', $schemaBlockNames)
                ->delete();
        }

        $existingNames = $page->allBlocks()->pluck('name')->toArray();

        foreach ($schema as $order => $blockDef) {
            if (in_array($blockDef['name'], $existingNames)) {
                continue; // already exists, skip
            }

            $defaultValue = $blockDef['default'] ?? $this->defaultForType($blockDef['type']);
            if (is_array($defaultValue)) {
                $defaultValue = json_encode($defaultValue);
            }

            $options = $blockDef['options'] ?? [];
            if (isset($blockDef['children'])) {
                $options['children'] = $blockDef['children'];
            }

            PageBlock::create([
                'page_id' => $page->id,
                'name' => $blockDef['name'],
                'type' => $blockDef['type'],
                'label' => $blockDef['label'] ?? ucfirst(str_replace('_', ' ', $blockDef['name'])),
                'value' => $defaultValue,
                'options' => $options,
                'order' => $order,
                'is_active' => true,
            ]);

            $seeded[] = $blockDef['name'];
        }

        return $seeded;
    }

    /**
     * Get default value for a block type.
     */
    protected function defaultForType(string $type): mixed
    {
        return match ($type) {
            'switcher' => false,
            'number' => 0,
            'checkbox', 'gallery', 'posts', 'repeater' => '[]',
            default => '',
        };
    }
}
