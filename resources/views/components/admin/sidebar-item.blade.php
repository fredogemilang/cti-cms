{{--
    Generic Sidebar Menu Item Renderer
    
    Renders any menu item from AdminMenuBuilder::getUnifiedMenuList().
    Handles: permission gates, active state, icon, children, flyout (collapsed mode).
    
    Usage: <x-admin.sidebar-item :item="$item" />
    
    Data contract — see AdminMenuBuilder::getUnifiedMenuList() docblock.
--}}

@props(['item'])

@php
    $hasChildren = !empty($item['children']);
    $permission = $item['permission'] ?? null;
    $icon = $item['icon'] ?? 'widgets';
    $title = $item['title'] ?? 'Untitled';
    $activePattern = $item['activeRoutePattern'] ?? '';
    $itemUrl = $item['url'] ?? null;
    $isCpt = ($item['source'] ?? '') === 'cpt';
    $cptSlug = $item['slug'] ?? null;
    
    // Check active state using pipe-separated route patterns, path matching, and children active state
    $isActive = false;
    $currentRoute = request()->route()?->getName();
    $currentPath = trim(request()->path(), '/');

    if ($activePattern) {
        foreach (explode('|', $activePattern) as $pattern) {
            $pattern = trim($pattern);
            if ($pattern && request()->routeIs($pattern)) {
                // For CPT items, also check the slug matches
                if ($isCpt && $cptSlug) {
                    $currentSlug = request()->route('postTypeSlug') ?? request()->route('slug');
                    if ($currentSlug === $cptSlug) {
                        $isActive = true;
                        break;
                    }
                } else {
                    $isActive = true;
                    break;
                }
            }
        }
    }

    // Check parent URL path match if not active yet
    if (!$isActive && $itemUrl && $itemUrl !== '#') {
        $parentPath = trim((string) parse_url($itemUrl, PHP_URL_PATH), '/');
        if ($parentPath !== '' && ($currentPath === $parentPath || str_starts_with($currentPath, $parentPath.'/'))) {
            $isActive = true;
        }
    }

    // Check if any child item is active (for expandable CPTs, Plugins, or Core items)
    if (!$isActive && $hasChildren) {
        foreach ($item['children'] as $child) {
            $childActivePattern = $child['activeRoutePattern'] ?? null;
            $childRoute = $child['route'] ?? null;
            $childParams = $child['routeParams'] ?? [];
            $childUrl = $child['url'] ?? null;
            $settingsSlug = $child['_settingsSlug'] ?? null;

            // 1. Settings special case
            if ($settingsSlug && request()->routeIs('admin.settings.show') && request()->route('group') === $settingsSlug) {
                $isActive = true;
                break;
            }

            // 2. Child Route pattern check
            if ($childActivePattern) {
                foreach (explode('|', $childActivePattern) as $cp) {
                    $cp = trim($cp);
                    if ($cp && request()->routeIs($cp)) {
                        $isActive = true;
                        break 2;
                    }
                }
            }

            // 3. Child Named route check (with route params for CPTs)
            if ($childRoute && request()->routeIs($childRoute)) {
                if ($isCpt && !empty($childParams)) {
                    $currentSlug = request()->route('postTypeSlug') ?? request()->route('slug');
                    if ($currentSlug === ($childParams['postTypeSlug'] ?? null)) {
                        $isActive = true;
                        break;
                    }
                } else {
                    $isActive = true;
                    break;
                }
            }

            // 4. Child URL / Path check (essential for plugins)
            if ($childUrl && $childUrl !== '#') {
                $childPath = trim((string) parse_url($childUrl, PHP_URL_PATH), '/');
                if ($childPath !== '' && ($currentPath === $childPath || str_starts_with($currentPath, $childPath.'/'))) {
                    $isActive = true;
                    break;
                }
            }
        }
    }

    // For items without children, resolve the href
    if (!$hasChildren && !empty($item['children'])) {
        // shouldn't happen
    }
    $href = null;
    if (!$hasChildren) {
        if (!empty($item['route'] ?? null)) {
            try { $href = route($item['route']); } catch (\Exception $e) { $href = '#'; }
        } elseif ($itemUrl) {
            $href = $itemUrl;
        } else {
            // Try the url field
            $href = $itemUrl ?? '#';
        }
    }
@endphp

@if($permission)
    @can($permission)
        @if($hasChildren)
            @include('components.admin.sidebar-item-expandable', [
                'item' => $item,
                'icon' => $icon,
                'title' => $title,
                'isActive' => $isActive,
                'isCpt' => $isCpt,
                'cptSlug' => $cptSlug,
            ])
        @else
            @include('components.admin.sidebar-item-link', [
                'item' => $item,
                'icon' => $icon,
                'title' => $title,
                'isActive' => $isActive,
                'href' => $href,
            ])
        @endif
    @endcan
@else
    @if($hasChildren)
        @include('components.admin.sidebar-item-expandable', [
            'item' => $item,
            'icon' => $icon,
            'title' => $title,
            'isActive' => $isActive,
            'isCpt' => $isCpt,
            'cptSlug' => $cptSlug,
        ])
    @else
        @include('components.admin.sidebar-item-link', [
            'item' => $item,
            'icon' => $icon,
            'title' => $title,
            'isActive' => $isActive,
            'href' => $href,
        ])
    @endif
@endif
