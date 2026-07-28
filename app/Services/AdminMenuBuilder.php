<?php

namespace App\Services;

use App\Events\RenderAdminMenu;
use App\Models\CustomPostType;
use App\Models\MenuItem;
use App\Models\Plugin;
use App\Models\Setting;

/**
 * Service for building the admin sidebar menu.
 * Dispatches RenderAdminMenu event to collect menu items from plugins.
 */
class AdminMenuBuilder
{
    /**
     * Build the complete admin menu.
     *
     * @return array Menu items including core and plugin items
     */
    public function build(): array
    {
        // Start with core menu items from database
        $coreItems = $this->getCoreMenuItems();

        // Create event with core items
        $event = new RenderAdminMenu;
        $event->addMenuItems($coreItems);

        // Dispatch event to allow plugins to add their items
        event($event);

        // Filter items based on user permissions
        return $this->filterByPermissions($event->getMenuItems());
    }

    /**
     * Get core menu items from the menu_items table.
     */
    protected function getCoreMenuItems(): array
    {
        $menuItems = MenuItem::whereNull('parent_id')
            ->orderBy('order')
            ->with('children')
            ->get();

        return $this->formatMenuItems($menuItems);
    }

    /**
     * Format menu items from Eloquent models to array format.
     */
    protected function formatMenuItems($items): array
    {
        return $items->map(function ($item) {
            return [
                'title' => $item->title,
                'route' => $item->route,
                'url' => $item->url,
                'icon' => $item->icon,
                'permission' => $item->permission,
                'is_active' => $item->is_active,
                'source' => 'core',
                'children' => $item->children->isNotEmpty()
                    ? $this->formatMenuItems($item->children)
                    : [],
            ];
        })->toArray();
    }

    /**
     * Filter menu items based on current user's permissions.
     */
    protected function filterByPermissions(array $items): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        // Super admin sees everything
        if ($user->isSuperAdmin()) {
            return array_filter($items, fn ($item) => $item['is_active'] ?? true);
        }

        return array_filter($items, function ($item) use ($user) {
            // Skip inactive items
            if (! ($item['is_active'] ?? true)) {
                return false;
            }

            // No permission required
            if (empty($item['permission'])) {
                return true;
            }

            // Check permission
            return $user->hasPermission($item['permission']);
        });
    }

    /**
     * Get menu items for a specific plugin.
     */
    public function getPluginMenuItems(string $pluginSlug): array
    {
        return collect($this->build())
            ->filter(fn ($item) => ($item['source'] ?? 'core') === "plugin:{$pluginSlug}")
            ->values()
            ->toArray();
    }

    /**
     * Get unified list of Core, CPT, and Plugin menu items with key tracking and custom order applied.
     */
    public function getUnifiedMenuList(): array
    {
        $defaultSectionMap = [
            'core:1' => 'MAIN',
            'core:2' => 'CONTENT',
            'cpt:technology-alliance' => 'CONTENT',
            'cpt:solutions' => 'CONTENT',
            'cpt:customer-success' => 'CONTENT',
            'cpt:client-says' => 'CONTENT',
            'cpt:tech-products' => 'CONTENT',
            'plugin:google-site-kit' => 'PLUGINS',
            'plugin:posts' => 'PLUGINS',
            'core:5' => 'SYSTEM',
            'core:9' => 'SYSTEM',
            'core:10' => 'SYSTEM',
        ];

        $customOrder = Setting::get('admin_sidebar_custom_order', []);
        $dynamicSectionMap = [];

        if (! empty($customOrder) && is_array($customOrder)) {
            $currSec = 'MAIN';
            foreach ($customOrder as $entry) {
                if (str_starts_with($entry, 'SECTION:')) {
                    $currSec = str_replace('SECTION:', '', $entry);
                } else {
                    $dynamicSectionMap[$entry] = $currSec;
                }
            }
        }

        $activeSectionMap = ! empty($dynamicSectionMap) ? $dynamicSectionMap : $defaultSectionMap;

        // 1. Core menu items
        $coreItems = MenuItem::whereNull('parent_id')
            ->orderBy('order')
            ->with('children')
            ->get()
            ->map(function (MenuItem $m) use ($activeSectionMap) {
                $key = 'core:'.$m->id;
                $section = $activeSectionMap[$key] ?? ($m->id == 1 ? 'MAIN' : 'SYSTEM');

                return [
                    'key' => $key,
                    'id' => $m->id,
                    'title' => $m->title,
                    'route' => $m->route,
                    'icon' => $m->icon === 'users' ? 'group' : $m->icon,
                    'permission' => $m->permission,
                    'is_active' => $m->is_active,
                    'source' => 'core',
                    'source_label' => 'Core System',
                    'section' => $section,
                    'children' => $m->children->map(fn ($c) => [
                        'id' => $c->getAttribute('id'),
                        'title' => $c->getAttribute('title'),
                        'route' => $c->getAttribute('route'),
                        'icon' => $c->getAttribute('icon'),
                        'permission' => $c->getAttribute('permission'),
                        'is_active' => $c->getAttribute('is_active'),
                    ])->toArray(),
                ];
            })->toArray();

        // 2. CPT menu items
        $cptItems = CustomPostType::active()->inMenu()->get()->map(function ($cpt) use ($activeSectionMap) {
            $taxonomies = $cpt->taxonomies();
            $children = [
                ['title' => 'All '.$cpt->plural_label, 'route' => 'admin.cpt.entries.index', 'params' => ['postTypeSlug' => $cpt->slug]],
                ['title' => 'Add '.$cpt->singular_label, 'route' => 'admin.cpt.entries.create', 'params' => ['postTypeSlug' => $cpt->slug]],
            ];

            foreach ($taxonomies as $tax) {
                $children[] = [
                    'title' => $tax->plural_label,
                    'route' => 'admin.taxonomies.terms.index',
                    'params' => ['taxonomy' => $tax->id],
                ];
            }

            $key = 'cpt:'.$cpt->slug;
            $section = $activeSectionMap[$key] ?? 'CONTENT';

            return [
                'key' => $key,
                'title' => $cpt->plural_label,
                'slug' => $cpt->slug,
                'icon' => $cpt->icon ?? 'article',
                'source' => 'cpt',
                'source_label' => 'Content (CPT)',
                'section' => $section,
                'is_active' => true,
                'children' => $children,
            ];
        })->toArray();

        // 3. Plugin menu items
        $eventItems = $this->build();
        $pluginItems = collect($eventItems)
            ->filter(fn ($item) => str_starts_with($item['source'] ?? '', 'plugin:'))
            ->map(function ($p) use ($activeSectionMap) {
                $key = $p['source'];
                $section = $activeSectionMap[$key] ?? 'PLUGINS';

                $p['key'] = $key;
                $p['source_label'] = 'Plugin: '.str_replace('plugin:', '', $p['source']);
                $p['section'] = $section;

                return $p;
            })
            ->values()
            ->toArray();

        $unified = array_merge($coreItems, $cptItems, $pluginItems);

        return $this->sortItemsByCustomOrder($unified);
    }

    /**
     * Sort menu items according to user custom order stored in settings or default sidebar sequence.
     */
    public function sortItemsByCustomOrder(array $items): array
    {
        $customOrder = Setting::get('admin_sidebar_custom_order', []);

        if (empty($customOrder) || ! is_array($customOrder)) {
            $defaultOrderKeys = [
                'core:1', // Dashboard (MAIN)
                'core:2', // Pages (CONTENT)
                'cpt:technology-alliance',
                'cpt:solutions',
                'cpt:customer-success',
                'cpt:client-says',
                'cpt:tech-products',
                'plugin:google-site-kit', // PLUGINS
                'plugin:posts',
                'core:5', // User Management (SYSTEM)
                'core:9', // Menu Management
                'core:10', // Appearance
            ];
            $orderMap = array_flip($defaultOrderKeys);
        } else {
            $itemKeysOnly = array_values(array_filter($customOrder, fn ($k) => ! str_starts_with($k, 'SECTION:')));
            $orderMap = array_flip($itemKeysOnly);
        }

        usort($items, function ($a, $b) use ($orderMap) {
            $keyA = $a['key'] ?? $a['source'] ?? '';
            $keyB = $b['key'] ?? $b['source'] ?? '';

            $posA = $orderMap[$keyA] ?? 9999;
            $posB = $orderMap[$keyB] ?? 9999;

            return $posA <=> $posB;
        });

        return $items;
    }
}
