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
    
    // Check active state using pipe-separated route patterns
    $isActive = false;
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
