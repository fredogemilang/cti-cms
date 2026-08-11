<?php

namespace App\Services;

use App\Events\RenderAdminMenu;
use App\Models\CustomPostType;
use App\Models\MenuItem;
use App\Models\Plugin;
use App\Models\Setting;

/**
 * Service for building the admin sidebar menu.
 *
 * All menu items — core DB entries, CPTs, plugins, and built-in features —
 * are registered through getUnifiedMenuList() using a standardized data contract.
 *
 * The sidebar template (sidebar-new.blade.php) renders items generically
 * using a single component, so adding a new menu item here automatically
 * makes it appear in both the sidebar and the Menu Customizer.
 *
 * @see RenderAdminMenu  Plugin hook for registering menu items
 * @see resources/views/components/admin/sidebar-item.blade.php  Generic renderer
 */
class AdminMenuBuilder
{
    /**
     * Build the complete admin menu (legacy method used by filterByPermissions).
     *
     * @return array Menu items including core and plugin items
     */
    public function build(): array
    {
        $coreItems = $this->getCoreMenuItems();
        $event = new RenderAdminMenu;
        $event->addMenuItems($coreItems);
        event($event);

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

        if ($user->isSuperAdmin()) {
            return array_filter($items, fn ($item) => $item['is_active'] ?? true);
        }

        return array_filter($items, function ($item) use ($user) {
            if (! ($item['is_active'] ?? true)) {
                return false;
            }
            if (empty($item['permission'])) {
                return true;
            }

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
     * Get unified list of all menu items with standardized data contract.
     *
     * Every item in the returned array follows this contract:
     *
     *   'key'                => string   Unique identifier (e.g. 'core:1', 'cpt:posts', 'builtin:forms')
     *   'title'              => string   Display name
     *   'icon'               => string   Material Symbols icon name
     *   'permission'         => ?string  Required permission (null = visible to all authenticated)
     *   'source'             => string   'core'|'cpt'|'plugin' — determines Menu Customizer badge color
     *   'source_label'       => string   Badge text for Menu Customizer
     *   'section'            => string   'MAIN'|'CONTENT'|'PLUGINS'|'SYSTEM'
     *   'is_active'          => bool     Visibility toggle
     *   'activeRoutePattern' => string   Route pattern(s) for sidebar active state (pipe-separated)
     *   'url'                => ?string  Direct URL (used by plugins; core items use route())
     *   'children'           => array    Child menu items, each with:
     *       'title'              => string
     *       'route'              => string   Named route
     *       'routeParams'        => array    Route parameters (default [])
     *       'permission'         => ?string  Per-child permission
     *       'activeRoutePattern' => ?string  Override active check (defaults to 'route')
     *
     * @return array<int, array>
     */
    public function getUnifiedMenuList(): array
    {
        $defaultSectionMap = [
            'core:1' => 'MAIN',
            'core:2' => 'CONTENT',
            'builtin:media' => 'CONTENT',
            'cpt:technology-alliance' => 'CONTENT',
            'cpt:solutions' => 'CONTENT',
            'cpt:customer-success' => 'CONTENT',
            'cpt:client-says' => 'CONTENT',
            'cpt:tech-products' => 'CONTENT',
            'plugin:google-site-kit' => 'PLUGINS',
            'plugin:posts' => 'PLUGINS',
            'builtin:seo' => 'SYSTEM',
            'core:5' => 'SYSTEM',
            'core:9' => 'SYSTEM',
            'core:10' => 'SYSTEM',
            'builtin:form-builder' => 'SYSTEM',
            'builtin:cpt-manager' => 'SYSTEM',
            'builtin:plugins-manager' => 'SYSTEM',
            'builtin:activity-log' => 'SYSTEM',
            'builtin:api-tokens' => 'SYSTEM',
            'builtin:settings' => 'SYSTEM',
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

        // 1. Core menu items from DB — enriched with rendering metadata
        $coreItemsMeta = $this->getCoreItemsMeta();

        $coreItems = MenuItem::whereNull('parent_id')
            ->orderBy('order')
            ->with('children')
            ->get()
            ->map(function (MenuItem $m) use ($activeSectionMap, $coreItemsMeta) {
                $key = 'core:'.$m->id;
                $section = $activeSectionMap[$key] ?? ($m->id == 1 ? 'MAIN' : 'SYSTEM');
                $meta = $coreItemsMeta[$m->id] ?? [];

                // Use metadata children if defined (overrides DB children)
                $children = [];
                if (! empty($meta['children'])) {
                    $children = $meta['children'];
                } else {
                    $children = $m->children->map(fn ($c) => [
                        'title' => $c->getAttribute('title'),
                        'route' => $c->getAttribute('route'),
                        'routeParams' => [],
                        'permission' => $c->getAttribute('permission'),
                        'activeRoutePattern' => $c->getAttribute('route'),
                    ])->toArray();
                }

                return [
                    'key' => $key,
                    'id' => $m->id,
                    'title' => $meta['title'] ?? $m->title,
                    'icon' => $meta['icon'] ?? ($m->icon === 'users' ? 'group' : $m->icon),
                    'permission' => $meta['permission'] ?? $m->permission,
                    'is_active' => $m->is_active,
                    'source' => 'core',
                    'source_label' => 'Core System',
                    'section' => $section,
                    'activeRoutePattern' => $meta['activeRoutePattern'] ?? $m->route ?? '',
                    'url' => ! empty($m->route) ? route($m->route) : null,
                    'children' => $children,
                ];
            })->toArray();

        // 2. CPT menu items
        $cptItems = CustomPostType::active()->inMenu()->get()->map(function ($cpt) use ($activeSectionMap) {
            $taxonomies = $cpt->taxonomies();
            $children = [
                [
                    'title' => 'All '.$cpt->plural_label,
                    'route' => 'admin.cpt.entries.index',
                    'routeParams' => ['postTypeSlug' => $cpt->slug],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.cpt.entries.index|admin.cpt.entries.edit',
                ],
                [
                    'title' => 'Add '.$cpt->singular_label,
                    'route' => 'admin.cpt.entries.create',
                    'routeParams' => ['postTypeSlug' => $cpt->slug],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.cpt.entries.create',
                ],
            ];

            foreach ($taxonomies as $tax) {
                $children[] = [
                    'title' => $tax->plural_label,
                    'route' => 'admin.taxonomies.terms.index',
                    'routeParams' => ['taxonomy' => $tax->id],
                    'permission' => null,
                    'activeRoutePattern' => null,
                ];
            }

            $key = 'cpt:'.$cpt->slug;
            $section = $activeSectionMap[$key] ?? 'CONTENT';

            return [
                'key' => $key,
                'title' => $cpt->plural_label,
                'slug' => $cpt->slug,
                'icon' => $cpt->icon ?? 'article',
                'permission' => null,
                'source' => 'cpt',
                'source_label' => 'Content (CPT)',
                'section' => $section,
                'is_active' => true,
                'activeRoutePattern' => null,
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

                $children = [];
                foreach ($p['children'] ?? [] as $child) {
                    $children[] = [
                        'title' => $child['title'],
                        'route' => $child['route'] ?? null,
                        'routeParams' => $child['params'] ?? [],
                        'permission' => $child['permission'] ?? null,
                        'activeRoutePattern' => $child['route'] ?? null,
                        'url' => $child['url'] ?? null,
                    ];
                }

                return [
                    'key' => $key,
                    'title' => $p['title'],
                    'icon' => $p['icon'] ?? 'extension',
                    'permission' => $p['permission'] ?? null,
                    'source' => 'plugin',
                    'source_label' => 'Plugin: '.str_replace('plugin:', '', $key),
                    'section' => $section,
                    'is_active' => $p['is_active'] ?? true,
                    'activeRoutePattern' => $p['route'] ?? '',
                    'url' => $p['url'] ?? null,
                    'children' => $children,
                ];
            })
            ->values()
            ->toArray();

        // 4. Built-in system items
        $builtinItems = $this->getBuiltinItems($activeSectionMap);

        $unified = array_merge($coreItems, $cptItems, $pluginItems, $builtinItems);

        return $this->sortItemsByCustomOrder($unified);
    }

    /**
     * Metadata overrides for core DB menu items.
     *
     * These define the rendering details (routes, children, permissions, active patterns)
     * that cannot be stored in the simple menu_items DB table.
     *
     * To add a new core menu item:
     *   1. Add the menu_item row in the DB
     *   2. Add its rendering metadata here
     *   3. It automatically appears in the sidebar and Menu Customizer
     */
    protected function getCoreItemsMeta(): array
    {
        return [
            // Dashboard (core:1) — no children, simple link
            1 => [
                'icon' => 'dashboard',
                'permission' => 'dashboard.view',
                'activeRoutePattern' => 'admin.dashboard',
                'children' => [],
            ],

            // Pages (core:2)
            2 => [
                'icon' => 'article',
                'permission' => 'pages.view',
                'activeRoutePattern' => 'admin.pages.*',
                'children' => [
                    [
                        'title' => 'All Pages',
                        'route' => 'admin.pages.index',
                        'routeParams' => [],
                        'permission' => null,
                        'activeRoutePattern' => 'admin.pages.index|admin.pages.edit|admin.pages.show',
                    ],
                    [
                        'title' => 'Add Page',
                        'route' => 'admin.pages.create',
                        'routeParams' => [],
                        'permission' => 'pages.create',
                        'activeRoutePattern' => 'admin.pages.create',
                    ],
                ],
            ],

            // User Management (core:5)
            5 => [
                'title' => 'User',
                'icon' => 'group',
                'permission' => 'users.view',
                'activeRoutePattern' => 'admin.users.*|admin.profile.*|admin.role-permission.*|admin.roles.*',
                'children' => [
                    [
                        'title' => 'All Users',
                        'route' => 'admin.users.index',
                        'routeParams' => [],
                        'permission' => 'users.view',
                        'activeRoutePattern' => 'admin.users.index|admin.users.edit|admin.users.show',
                    ],
                    [
                        'title' => 'Add User',
                        'route' => 'admin.users.create',
                        'routeParams' => [],
                        'permission' => 'users.create',
                        'activeRoutePattern' => 'admin.users.create',
                    ],
                    [
                        'title' => 'Profile',
                        'route' => 'admin.profile.index',
                        'routeParams' => [],
                        'permission' => null,
                        'activeRoutePattern' => 'admin.profile.*',
                    ],
                    [
                        'title' => 'Role & Permission',
                        'route' => 'admin.role-permission.index',
                        'routeParams' => [],
                        'permission' => 'roles.view',
                        'activeRoutePattern' => 'admin.role-permission.*|admin.roles.*',
                    ],
                    [
                        'title' => 'API Tokens',
                        'route' => 'admin.api-tokens.index',
                        'routeParams' => [],
                        'permission' => 'api-tokens.view',
                        'activeRoutePattern' => 'admin.api-tokens.*',
                    ],
                ],
            ],

            // Menu Customizer (core:9) — no children, simple link
            9 => [
                'icon' => 'reorder',
                'title' => 'Menu Customizer',
                'permission' => 'menus.view',
                'activeRoutePattern' => 'admin.menus.*',
                'children' => [],
            ],

            // Appearance (core:10)
            10 => [
                'icon' => 'palette',
                'permission' => 'themes.view',
                'activeRoutePattern' => 'admin.themes.*',
                'children' => [
                    [
                        'title' => 'Themes',
                        'route' => 'admin.themes.index',
                        'routeParams' => [],
                        'permission' => null,
                        'activeRoutePattern' => 'admin.themes.*',
                    ],
                ],
            ],
        ];
    }

    /**
     * Built-in system items that are not stored in DB and not plugins.
     *
     * To add a new built-in menu item:
     *   1. Add the item array here
     *   2. Add its key to the $defaultSectionMap in getUnifiedMenuList()
     *   3. Add its key to the $defaultOrderKeys in sortItemsByCustomOrder()
     *   4. It automatically appears in the sidebar and Menu Customizer
     */
    protected function getBuiltinItems(array $activeSectionMap): array
    {
        $items = [];

        // Media
        $items[] = [
            'key' => 'builtin:media',
            'title' => 'Media',
            'icon' => 'perm_media',
            'permission' => 'media.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:media'] ?? 'CONTENT',
            'is_active' => true,
            'activeRoutePattern' => 'admin.media.*',
            'children' => [
                [
                    'title' => 'All Media',
                    'route' => 'admin.media.index',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.media.index|admin.media.edit|admin.media.show',
                ],
                [
                    'title' => 'Add Media',
                    'route' => 'admin.media.create',
                    'routeParams' => [],
                    'permission' => 'media.upload',
                    'activeRoutePattern' => 'admin.media.create',
                ],
            ],
        ];

        // Form Builder
        $items[] = [
            'key' => 'builtin:form-builder',
            'title' => 'Form Builder',
            'icon' => 'dynamic_form',
            'permission' => 'forms.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:form-builder'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.forms.*',
            'children' => [
                [
                    'title' => 'All Forms',
                    'route' => 'admin.forms.index',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.forms.index|admin.forms.edit|admin.forms.builder|admin.forms.entries.*',
                ],
                [
                    'title' => 'Create Form',
                    'route' => 'admin.forms.create',
                    'routeParams' => [],
                    'permission' => 'forms.create',
                    'activeRoutePattern' => 'admin.forms.create',
                ],
                [
                    'title' => 'Form Assignments',
                    'route' => 'admin.forms.assignments',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.forms.assignments',
                ],
                [
                    'title' => 'Webhooks',
                    'route' => 'admin.webhooks.index',
                    'routeParams' => [],
                    'permission' => 'webhooks.view',
                    'activeRoutePattern' => 'admin.webhooks.*',
                ],
            ],
        ];

        // CPT Manager
        $items[] = [
            'key' => 'builtin:cpt-manager',
            'title' => 'CPT Manager',
            'icon' => 'layers',
            'permission' => 'cpt.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:cpt-manager'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.cpt.index|admin.cpt.create|admin.cpt.edit|admin.taxonomies.*',
            'children' => [
                [
                    'title' => 'Post Types',
                    'route' => 'admin.cpt.index',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.cpt.index|admin.cpt.create|admin.cpt.edit',
                ],
                [
                    'title' => 'Taxonomies',
                    'route' => 'admin.taxonomies.index',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.taxonomies.index|admin.taxonomies.create|admin.taxonomies.edit',
                ],
            ],
        ];

        // SEO & GEO
        $items[] = [
            'key' => 'builtin:seo',
            'title' => 'SEO & GEO',
            'icon' => 'travel_explore',
            'permission' => 'settings.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:seo'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.seo.*',
            'children' => [
                [
                    'title' => 'Overview',
                    'route' => 'admin.seo.index',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.seo.index',
                ],
                [
                    'title' => 'General Settings',
                    'route' => 'admin.seo.settings',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.seo.settings',
                ],
                [
                    'title' => 'Instant Indexing',
                    'route' => 'admin.seo.indexnow',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.seo.indexnow',
                ],
                [
                    'title' => 'Redirects',
                    'route' => 'admin.seo.redirects',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.seo.redirects',
                ],
                [
                    'title' => 'Bulk Editor',
                    'route' => 'admin.seo.bulk-editor',
                    'routeParams' => [],
                    'permission' => null,
                    'activeRoutePattern' => 'admin.seo.bulk-editor',
                ],
            ],
        ];

        // Plugins Manager
        $items[] = [
            'key' => 'builtin:plugins-manager',
            'title' => 'Plugins Manager',
            'icon' => 'extension',
            'permission' => 'plugins.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:plugins-manager'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.plugins.*',
            'url' => route('admin.plugins.index'),
            'children' => [],
        ];

        // Activity Log
        $items[] = [
            'key' => 'builtin:activity-log',
            'title' => 'Activity Log',
            'icon' => 'history',
            'permission' => 'activity.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:activity-log'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.activity.*',
            'url' => route('admin.activity.index'),
            'children' => [],
        ];

        // API Tokens
        $items[] = [
            'key' => 'builtin:api-tokens',
            'title' => 'API Tokens',
            'icon' => 'key',
            'permission' => 'api-tokens.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:api-tokens'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.api-tokens.*',
            'url' => route('admin.api-tokens.index'),
            'children' => [],
        ];

        // Settings — children are dynamic from SettingsRegistry
        $settingsGroups = app(SettingsRegistry::class)->groups();
        $settingsChildren = [];
        foreach ($settingsGroups as $sg) {
            $settingsChildren[] = [
                'title' => $sg['label'],
                'route' => 'admin.settings.show',
                'routeParams' => ['group' => $sg['slug']],
                'permission' => $sg['permission'] ?? 'settings.view',
                'activeRoutePattern' => null,
                '_settingsSlug' => $sg['slug'],
            ];
        }

        $items[] = [
            'key' => 'builtin:settings',
            'title' => 'Settings',
            'icon' => 'settings',
            'permission' => 'settings.view',
            'source' => 'core',
            'source_label' => 'Core System',
            'section' => $activeSectionMap['builtin:settings'] ?? 'SYSTEM',
            'is_active' => true,
            'activeRoutePattern' => 'admin.settings.*',
            'children' => $settingsChildren,
        ];

        return $items;
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
                'builtin:media', // Media (CONTENT)
                'cpt:technology-alliance',
                'cpt:solutions',
                'cpt:customer-success',
                'cpt:client-says',
                'cpt:tech-products',
                'plugin:google-site-kit', // PLUGINS
                'plugin:posts',
                'builtin:seo', // SEO & GEO (SYSTEM)
                'core:5', // User Management (SYSTEM)
                'core:9', // Menu Customizer (SYSTEM)
                'core:10', // Appearance (SYSTEM)
                'builtin:form-builder', // Form Builder (SYSTEM)
                'builtin:cpt-manager', // CPT Manager (SYSTEM)
                'builtin:plugins-manager', // Plugins Manager (SYSTEM)
                'builtin:activity-log', // Activity Log (SYSTEM)
                'builtin:api-tokens', // API Tokens (SYSTEM)
                'builtin:settings', // Settings (SYSTEM)
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
