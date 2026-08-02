{{-- Expandable menu item with children, flyout support --}}
@php
    $childrenCount = count($item['children'] ?? []);
    $maxHeight = max(200, $childrenCount * 55 + 50);
@endphp

<li class="relative"
    x-data="{ 
        open: {{ $isActive ? 'true' : 'false' }}, 
        flyoutOpen: false,
        checkActive() {
            if ($el.querySelector('.text-\[\#2563EB\]')) {
                this.open = true;
            }
        }
    }"
    x-init="checkActive()"
    @livewire:navigated.window="checkActive()"
    @click.away="flyoutOpen = false"
    :class="{ 'flyout-active': flyoutOpen }">

    {{-- Toggle Button --}}
    <button
        @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
        class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
        :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
        <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
            <span class="material-symbols-outlined shrink-0">{{ $icon }}</span>
            <span class="font-semibold text-[15px] sidebar-text">{{ $title }}</span>
        </div>
        <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
        <span class="sidebar-tooltip">{{ $title }}</span>
    </button>

    {{-- Flyout Dropdown (collapsed sidebar) --}}
    <div
        x-show="sidebarCollapsed && flyoutOpen"
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95 -translate-x-2"
        x-transition:enter-end="opacity-100 scale-100 translate-x-0"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-x-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-x-2"
        x-cloak
        class="absolute left-[calc(100%+12px)] top-0 z-[100] min-w-[200px] w-max rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2.5 shadow-2xl text-white">
        <div class="absolute -left-3 top-4 w-0 h-0 border-[6px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
        <div class="space-y-1">
            <div class="px-3 py-1 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1.5 pb-1 whitespace-nowrap">
                {{ $title }}
            </div>
            @foreach($item['children'] as $child)
                @php
                    $childPerm = $child['permission'] ?? null;
                    $childRoute = $child['route'] ?? null;
                    $childParams = $child['routeParams'] ?? [];
                    $childUrl = $child['url'] ?? null;
                    $childHref = '#';
                    if ($childRoute) {
                        try { $childHref = route($childRoute, $childParams); } catch (\Exception $e) { $childHref = '#'; }
                    } elseif ($childUrl) {
                        $childHref = $childUrl;
                    }
                @endphp
                @if($childPerm)
                    @can($childPerm)
                    <a wire:navigate href="{{ $childHref }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                        {{ $child['title'] }}
                    </a>
                    @endcan
                @else
                    <a wire:navigate href="{{ $childHref }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                        {{ $child['title'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Expanded Children List --}}
    <div class="submenu-container overflow-hidden" :style="open ? 'max-height: {{ $maxHeight }}px; opacity: 1' : 'max-height: 0; opacity: 0'">
        <ul class="submenu-list mt-1 space-y-1">
            @foreach($item['children'] as $child)
                @php
                    $childPerm = $child['permission'] ?? null;
                    $childRoute = $child['route'] ?? null;
                    $childParams = $child['routeParams'] ?? [];
                    $childUrl = $child['url'] ?? null;
                    $childHref = '#';
                    if ($childRoute) {
                        try { $childHref = route($childRoute, $childParams); } catch (\Exception $e) { $childHref = '#'; }
                    } elseif ($childUrl) {
                        $childHref = $childUrl;
                    }

                    // Determine child active state
                    $childActive = false;
                    $childActivePattern = $child['activeRoutePattern'] ?? null;
                    $settingsSlug = $child['_settingsSlug'] ?? null;
                    
                    if ($settingsSlug) {
                        // Settings special case: match route + slug param
                        $childActive = request()->routeIs('admin.settings.show') && request()->route('group') === $settingsSlug;
                    } elseif ($childActivePattern) {
                        foreach (explode('|', $childActivePattern) as $cp) {
                            $cp = trim($cp);
                            if ($cp && request()->routeIs($cp)) {
                                $childActive = true;
                                break;
                            }
                        }
                    } elseif ($childRoute) {
                        // Fallback: check if the route itself matches (with params for CPTs)
                        if ($isCpt && !empty($childParams)) {
                            $childActive = request()->routeIs($childRoute) && request()->route('postTypeSlug') === ($childParams['postTypeSlug'] ?? null);
                            // Also check taxonomy param
                            if (!$childActive && isset($childParams['taxonomy'])) {
                                $childActive = request()->routeIs($childRoute) && request()->route('taxonomy') == $childParams['taxonomy'];
                            }
                        } else {
                            $childActive = request()->routeIs($childRoute);
                        }
                    }
                @endphp

                @if($childPerm)
                    @can($childPerm)
                    <li class="relative pl-6 py-1">
                        <div class="submenu-item-connector"></div>
                        <a wire:navigate
                           class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ $childActive ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
                           href="{{ $childHref }}">
                            <span class="text-[14px] font-medium">{{ $child['title'] }}</span>
                        </a>
                    </li>
                    @endcan
                @else
                    <li class="relative pl-6 py-1">
                        <div class="submenu-item-connector"></div>
                        <a wire:navigate
                           class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ $childActive ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
                           href="{{ $childHref }}">
                            <span class="text-[14px] font-medium">{{ $child['title'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</li>
