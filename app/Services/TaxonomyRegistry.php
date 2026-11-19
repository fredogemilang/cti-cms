<?php

namespace App\Services;

use App\Models\CustomTaxonomy;

class TaxonomyRegistry
{
    /**
     * Store statically registered taxonomies (e.g. from core or plugins).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $taxonomies = [];

    public function __construct()
    {
        // Register default CMS Taxonomies out-of-the-box
        $this->register('categories', [
            'slug' => 'categories',
            'label' => 'Categories',
            'singular' => 'Category',
            'icon' => 'folder',
            'default_title_pattern' => '{term} Archives {sep} {site}',
            'default_schema_type' => 'CollectionPage',
            'order' => 10,
        ]);

        $this->register('tags', [
            'slug' => 'tags',
            'label' => 'Tags',
            'singular' => 'Tag',
            'icon' => 'label',
            'default_title_pattern' => '{term} Archives {sep} {site}',
            'default_schema_type' => 'CollectionPage',
            'order' => 20,
        ]);
    }

    /**
     * Register a new Taxonomy.
     *
     * @param  array<string, mixed>  $config
     */
    public function register(string $slug, array $config): void
    {
        $this->taxonomies[$slug] = array_merge([
            'slug' => $slug,
            'label' => ucfirst($slug),
            'singular' => ucfirst(rtrim($slug, 's')),
            'icon' => 'label',
            'default_title_pattern' => '{term} Archives {sep} {site}',
            'default_schema_type' => 'CollectionPage',
            'order' => 50,
        ], $config);
    }

    /**
     * Unregister a Taxonomy by slug.
     */
    public function unregister(string $slug): void
    {
        unset($this->taxonomies[$slug]);
    }

    /**
     * Get all registered taxonomies sorted by order,
     * including active CustomTaxonomy models from CMS Database.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $taxonomies = $this->taxonomies;

        // Auto-discover ACTIVE Custom Taxonomies from database (/ctrlpanel/taxonomies)
        try {
            if (class_exists(CustomTaxonomy::class)) {
                $dbTaxonomies = CustomTaxonomy::query()->where('is_active', true)->get();
                foreach ($dbTaxonomies as $tax) {
                    $slug = $tax->slug;
                    if (! isset($taxonomies[$slug])) {
                        $taxonomies[$slug] = [
                            'slug' => $slug,
                            'label' => $tax->plural_label ?: ucfirst($tax->name),
                            'singular' => $tax->singular_label ?: ucfirst($tax->name),
                            'icon' => 'folder_open',
                            'default_title_pattern' => '{term} Archives {sep} {site}',
                            'default_schema_type' => 'CollectionPage',
                            'order' => 30,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence DB exceptions during migration or CLI bootstrap
        }

        uasort($taxonomies, fn ($a, $b) => ($a['order'] ?? 50) <=> ($b['order'] ?? 50));

        return $taxonomies;
    }

    /**
     * Get specific taxonomy config by slug.
     *
     * @return array<string, mixed>|null
     */
    public function get(string $slug): ?array
    {
        $all = $this->all();

        return $all[$slug] ?? null;
    }

    /**
     * Check if a taxonomy slug is registered and active.
     */
    public function has(string $slug): bool
    {
        $all = $this->all();

        return isset($all[$slug]);
    }
}
