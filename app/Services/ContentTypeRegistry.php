<?php

namespace App\Services;

use App\Models\CustomPostType;

class ContentTypeRegistry
{
    /**
     * Store statically registered content types (e.g. from plugins).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $contentTypes = [];

    public function __construct()
    {
        // Register default CMS Content Types out-of-the-box
        $this->register('pages', [
            'slug' => 'pages',
            'label' => 'Pages',
            'singular' => 'Page',
            'icon' => 'description',
            'default_title_pattern' => '{title} {sep} {site}',
            'default_schema_type' => 'WebPage',
            'order' => 10,
        ]);

        $this->register('posts', [
            'slug' => 'posts',
            'label' => 'Posts',
            'singular' => 'Post',
            'icon' => 'article',
            'default_title_pattern' => '{title} {sep} {site}',
            'default_schema_type' => 'Article',
            'order' => 20,
        ]);
    }

    /**
     * Register a new Content Type.
     *
     * @param  array<string, mixed>  $config
     */
    public function register(string $slug, array $config): void
    {
        $this->contentTypes[$slug] = array_merge([
            'slug' => $slug,
            'label' => ucfirst($slug),
            'singular' => ucfirst(rtrim($slug, 's')),
            'icon' => 'article',
            'default_title_pattern' => '{title} {sep} {site}',
            'default_schema_type' => 'WebPage',
            'order' => 50,
        ], $config);
    }

    /**
     * Unregister a Content Type by slug.
     */
    public function unregister(string $slug): void
    {
        unset($this->contentTypes[$slug]);
    }

    /**
     * Get all registered content types sorted by order,
     * including active Custom Post Types created in CMS Database.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $types = $this->contentTypes;

        // Auto-discover ACTIVE Custom Post Types from database
        try {
            if (class_exists(CustomPostType::class)) {
                $dbCpts = CustomPostType::query()->where('is_active', true)->get();
                foreach ($dbCpts as $cpt) {
                    $slug = $cpt->slug;
                    if (! isset($types[$slug])) {
                        $types[$slug] = [
                            'slug' => $slug,
                            'label' => $cpt->plural_label ?: ucfirst($cpt->name),
                            'singular' => $cpt->singular_label ?: ucfirst($cpt->name),
                            'icon' => $cpt->icon ?: 'folder_open',
                            'default_title_pattern' => '{title} {sep} {site}',
                            'default_schema_type' => 'WebPage',
                            'order' => 30,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence DB exceptions during migration or CLI bootstrap
        }

        uasort($types, fn ($a, $b) => ($a['order'] ?? 50) <=> ($b['order'] ?? 50));

        return $types;
    }

    /**
     * Get specific content type config by slug.
     *
     * @return array<string, mixed>|null
     */
    public function get(string $slug): ?array
    {
        $all = $this->all();

        return $all[$slug] ?? null;
    }

    /**
     * Check if a content type slug is registered and active.
     */
    public function has(string $slug): bool
    {
        $all = $this->all();

        return isset($all[$slug]);
    }
}
