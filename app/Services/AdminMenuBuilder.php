<?php

namespace App\Services;

use App\Events\RenderAdminMenu;
use App\Models\CustomPostType;
use App\Models\MenuItem;
use App\Models\Plugin;

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
        // 1. Core menu items
        $coreItems = MenuItem::whereNull('parent_id')
            ->orderBy('order')
            ->with('children')
            ->get()
            ->map(function ($m) {
                return [
                    'key' => 'core:'.$m->id,
                    'id' => $m->id,
                    'title' => $m->title,
                    'route' => $m->route,
                    'url' => $m->url,
                    'icon' => $m->icon,
                    'permission' => $m->permission,
                    'is_active' => $m->is_active,
                    'source' => 'core',
                    'source_label' => 'Core System',
                    'children' => $m->children->map(fn ($c) => [
                        'id' => $c->id,
                        'title' => $c->title,
                        'route' => $c->route,
                        'icon' => $c->icon,
                        'permission' => $c->permission,
                        'is_active' => $c->is_active,
                    ])->toArray(),
                ];
            })->toArray();

        // 2. CPT menu items
        $cptItems = CustomPostType::active()->inMenu()->get()->map(function ($cpt) {
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

            return [
                'key' => 'cpt:'.$cpt->slug,
                'title' => $cpt->plural_label,
                'slug' => $cpt->slug,
                'icon' => $cpt->icon ?? 'article',
                'source' => 'cpt',
                'source_label' => 'Content (CPT)',
                'is_active' => true,
                'children' => $children,
            ];
        })->toArray();

        // 3. Plugin menu items
        $eventItems = $this->build();
        $pluginItems = collect($eventItems)
            ->filter(fn ($item) => str_starts_with($item['source'] ?? '', 'plugin:'))
            ->map(function ($p) {
                $p['key'] = $p['source'];
                $p['source_label'] = 'Plugin: '.str_replace('plugin:', '', $p['source']);

                return $p;
            })
            ->values()
            ->toArray();

        $unified = array_merge($coreItems, $cptItems, $pluginItems);

        return $this->sortItemsByCustomOrder($unified);
    }

    /**
     * Sort menu items according to user custom order stored in settings.
     */
    public function sortItemsByCustomOrder(array $items): array
    {
        $customOrder = setting('admin_sidebar_custom_order', []);
        if (empty($customOrder) || ! is_array($customOrder)) {
            return $items;
        }

        $orderMap = array_flip(array_values($customOrder));

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
