@php
    $sidebarUnifiedList = app(\App\Services\AdminMenuBuilder::class)->getUnifiedMenuList();
    $currentSidebarSection = null;

    // Cache CPT items keyed by slug for fast lookup
    $allCptsBySlug = \App\Models\CustomPostType::active()->inMenu()->get()->keyBy('slug');
@endphp

<ul class="space-y-1 pb-12">
    @foreach($sidebarUnifiedList as $item)
        @php
            $itemSec = $item['section'] ?? 'CONTENT';
        @endphp

        {{-- Output Section Header Banner when section changes --}}
        @if($itemSec !== $currentSidebarSection)
            @php $currentSidebarSection = $itemSec; @endphp
            <li class="px-4 pt-4 pb-2">
                <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">{{ $currentSidebarSection }}</span>
            </li>
        @endif

        {{-- Render Item based on Key / Type --}}

        {{-- 1. Dashboard (core:1) --}}
        @if($item['key'] === 'core:1')
            @can('dashboard.view')
            <li>
                <a wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined shrink-0">dashboard</span>
                    <span class="font-semibold text-[15px] sidebar-text">Dashboard</span>
                    <span class="sidebar-tooltip">Dashboard</span>
                </a>
            </li>
            @endcan

        {{-- 2. Pages (core:2) --}}
        @elseif($item['key'] === 'core:2')
            @can('pages.view')
            <li class="relative" x-data="{ open: {{ request()->routeIs('admin.pages.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
                <button
                    @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; $dispatch('submenu-toggle'); }"
                    class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
                    :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
                    <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                        <span class="material-symbols-outlined shrink-0">article</span>
                        <span class="font-semibold text-[15px] sidebar-text">Pages</span>
                    </div>
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                    <span class="sidebar-tooltip">Pages</span>
                </button>

                <!-- Flyout Dropdown for Collapsed Sidebar -->
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
                            Pages
                        </div>
                        <a wire:navigate href="{{ route('admin.pages.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            All Pages
                        </a>
                        @can('pages.create')
                        <a wire:navigate href="{{ route('admin.pages.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            Add Page
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 200px; opacity: 1' : 'max-height: 0; opacity: 0'">
                    <ul class="submenu-list mt-1 space-y-1">
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ request()->routeIs('admin.pages.index') ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" href="{{ route('admin.pages.index') }}">
                                <span class="text-[14px] font-medium">All Pages</span>
                            </a>
                        </li>
                        @can('pages.create')
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ request()->routeIs('admin.pages.create') ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" href="{{ route('admin.pages.create') }}">
                                <span class="text-[14px] font-medium">Add Page</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

        {{-- 3. CPT Item (cpt:slug) --}}
        @elseif(str_starts_with($item['key'], 'cpt:'))
            @php
                $cptSlug = str_replace('cpt:', '', $item['key']);
                $cpt = $allCptsBySlug[$cptSlug] ?? null;
            @endphp

            @if($cpt)
                @php
                    $cptTaxonomies = $cpt->taxonomies();
                    $isCptActive = (request()->routeIs('admin.cpt.entries.*') && request()->route('postTypeSlug') === $cpt->slug);
                    $isTaxonomyActive = (request()->routeIs('admin.taxonomies.terms.*') && $cptTaxonomies->where('id', request()->route('taxonomy'))->isNotEmpty());
                @endphp
                <li class="relative" x-data="{ open: {{ $isCptActive || $isTaxonomyActive ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
                    <button
                        @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; $dispatch('submenu-toggle'); }"
                        class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
                        :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
                        <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                            <span class="material-symbols-outlined shrink-0">{{ $cpt->icon ?? 'article' }}</span>
                            <span class="font-semibold text-[15px] sidebar-text">{{ $cpt->plural_label }}</span>
                        </div>
                        <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                        <span class="sidebar-tooltip">{{ $cpt->plural_label }}</span>
                    </button>

                    <!-- Flyout Dropdown for Collapsed Sidebar -->
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
                                {{ $cpt->plural_label }}
                            </div>
                            <a wire:navigate href="{{ route('admin.cpt.entries.index', $cpt->slug) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                                All {{ $cpt->plural_label }}
                            </a>
                            <a wire:navigate href="{{ route('admin.cpt.entries.create', $cpt->slug) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                                Add {{ $cpt->singular_label }}
                            </a>
                            @foreach($cptTaxonomies as $taxonomy)
                                <a wire:navigate href="{{ route('admin.taxonomies.terms.index', $taxonomy->id) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                                    {{ $taxonomy->plural_label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 500px; opacity: 1' : 'max-height: 0; opacity: 0'">
                        <ul class="submenu-list mt-1 space-y-1">
                            <li class="relative pl-6 py-1">
                                <div class="submenu-item-connector"></div>
                                <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ (request()->routeIs('admin.cpt.entries.index') && request()->route('postTypeSlug') === $cpt->slug) ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" 
                                   href="{{ route('admin.cpt.entries.index', $cpt->slug) }}">
                                    <span class="text-[14px] font-medium">All {{ $cpt->plural_label }}</span>
                                </a>
                            </li>
                            <li class="relative pl-6 py-1">
                                <div class="submenu-item-connector"></div>
                                <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ (request()->routeIs('admin.cpt.entries.create') && request()->route('postTypeSlug') === $cpt->slug) ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" 
                                   href="{{ route('admin.cpt.entries.create', $cpt->slug) }}">
                                    <span class="text-[14px] font-medium">Add {{ $cpt->singular_label }}</span>
                                </a>
                            </li>
                            
                            {{-- Taxonomies --}}
                            @foreach($cptTaxonomies as $taxonomy)
                            <li class="relative pl-6 py-1">
                                <div class="submenu-item-connector"></div>
                                <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ (request()->routeIs('admin.taxonomies.terms.*') && request()->route('taxonomy') == $taxonomy->id) ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" 
                                   href="{{ route('admin.taxonomies.terms.index', $taxonomy->id) }}">
                                    <span class="text-[14px] font-medium">{{ $taxonomy->plural_label }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

        {{-- 4. Plugin Item (plugin:name) --}}
        @elseif(str_starts_with($item['key'], 'plugin:'))
            @can($item['permission'] ?? '')
            <li class="relative" x-data="{ open: {{ request()->routeIs(($item['route'] ?? '') . '*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
                @if(!empty($item['children']))
                    <button
                        @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
                        class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
                        :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
                        <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                            <span class="material-symbols-outlined shrink-0">{{ $item['icon'] ?? 'extension' }}</span>
                            <span class="font-semibold text-[15px] sidebar-text">{{ $item['title'] }}</span>
                        </div>
                        <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                        <span class="sidebar-tooltip">{{ $item['title'] }}</span>
                    </button>

                    <!-- Flyout Dropdown for Collapsed Sidebar -->
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
                                {{ $item['title'] }}
                            </div>
                            @foreach($item['children'] as $child)
                                @can($child['permission'] ?? '')
                                <a href="{{ $child['url'] ?? '#' }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                                    {{ $child['title'] }}
                                </a>
                                @endcan
                            @endforeach
                        </div>
                    </div>

                    <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 400px; opacity: 1' : 'max-height: 0; opacity: 0'">
                        <ul class="submenu-list mt-1 space-y-1">
                            @foreach($item['children'] as $child)
                                @can($child['permission'] ?? '')
                                <li class="relative pl-6 py-1">
                                    <div class="submenu-item-connector"></div>
                                    <a class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs($child['route'] ?? '') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                                       href="{{ $child['url'] ?? '#' }}">
                                        <span class="text-[14px] font-medium">{{ $child['title'] }}</span>
                                    </a>
                                </li>
                                @endcan
                            @endforeach
                        </ul>
                    </div>
                @else
                    <a class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs($item['route'] ?? '') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                        href="{{ $item['url'] ?? '#' }}">
                        <span class="material-symbols-outlined shrink-0">{{ $item['icon'] ?? 'extension' }}</span>
                        <span class="font-semibold text-[15px] sidebar-text">{{ $item['title'] }}</span>
                        <span class="sidebar-tooltip">{{ $item['title'] }}</span>
                    </a>
                @endif
            </li>
            @endcan

        {{-- 5. User Management (core:5) --}}
        @elseif($item['key'] === 'core:5')
            @canany(['users.view', 'users.create', 'menus.view'])
            <li class="relative" x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.profile.*') || request()->routeIs('admin.role-permission.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.menus.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
                <button
                    @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
                    class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
                    :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
                    <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                        <span class="material-symbols-outlined shrink-0">group</span>
                        <span class="font-semibold text-[15px] sidebar-text">User</span>
                    </div>
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                    <span class="sidebar-tooltip">User</span>
                </button>

                <!-- Flyout Dropdown for Collapsed Sidebar -->
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
                            User
                        </div>
                        @can('users.view')
                        <a wire:navigate href="{{ route('admin.users.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            All Users
                        </a>
                        @endcan
                        @can('users.create')
                        <a wire:navigate href="{{ route('admin.users.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            Add User
                        </a>
                        @endcan
                        <a wire:navigate href="{{ route('admin.profile.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            Profile
                        </a>
                        @can('roles.view')
                        <a wire:navigate href="{{ route('admin.role-permission.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            Role & Permission
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 350px; opacity: 1' : 'max-height: 0; opacity: 0'">
                    <ul class="submenu-list mt-1 space-y-1">
                        @can('users.view')
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.users.index') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                               href="{{ route('admin.users.index') }}">
                                <span class="text-[14px] font-medium">All Users</span>
                            </a>
                        </li>
                        @endcan
                        @can('users.create')
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.users.create') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                               href="{{ route('admin.users.create') }}">
                                <span class="text-[14px] font-medium">Add User</span>
                            </a>
                        </li>
                        @endcan
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.profile.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                               href="{{ route('admin.profile.index') }}">
                                <span class="text-[14px] font-medium">Profile</span>
                            </a>
                        </li>
                        @can('roles.view')
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.role-permission.*') || request()->routeIs('admin.roles.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                               href="{{ route('admin.role-permission.index') }}">
                                <span class="text-[14px] font-medium">Role & Permission</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

        {{-- 6. Menu Management (core:9) --}}
        @elseif($item['key'] === 'core:9')
            @can('menus.view')
            <li>
                <a wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs('admin.menus.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                    href="{{ route('admin.menus.index') }}">
                    <span class="material-symbols-outlined shrink-0">reorder</span>
                    <span class="font-semibold text-[15px] sidebar-text">Menu Customizer</span>
                    <span class="sidebar-tooltip">Menu Customizer</span>
                </a>
            </li>
            @endcan

        {{-- 7. Appearance (core:10) --}}
        @elseif($item['key'] === 'core:10')
            @can('themes.view')
            <li class="relative" x-data="{ open: {{ request()->routeIs('admin.themes.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
                <button
                    @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
                    class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
                    :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
                    <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                        <span class="material-symbols-outlined shrink-0">palette</span>
                        <span class="font-semibold text-[15px] sidebar-text">Appearance</span>
                    </div>
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                    <span class="sidebar-tooltip">Appearance</span>
                </button>

                <!-- Flyout Dropdown for Collapsed Sidebar -->
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
                            Appearance
                        </div>
                        <a wire:navigate href="{{ route('admin.themes.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors whitespace-nowrap">
                            Themes
                        </a>
                    </div>
                </div>
                <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 200px; opacity: 1' : 'max-height: 0; opacity: 0'">
                    <ul class="submenu-list mt-1 space-y-1">
                        <li class="relative pl-6 py-1">
                            <div class="submenu-item-connector"></div>
                            <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.themes.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                               href="{{ route('admin.themes.index') }}">
                                <span class="text-[14px] font-medium">Themes</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
        @endif
    @endforeach

    {{-- System Core Items (CPT Manager, SEO, Activity Log, Settings) --}}
    @can('cpt.view')
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.cpt.index') || request()->routeIs('admin.cpt.create') || request()->routeIs('admin.cpt.edit') || (request()->routeIs('admin.taxonomies.*') && !request()->routeIs('admin.taxonomies.terms.*')) ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                <span class="material-symbols-outlined shrink-0">layers</span>
                <span class="font-semibold text-[15px] sidebar-text">CPT Manager</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">CPT Manager</span>
        </button>

        <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 200px; opacity: 1' : 'max-height: 0; opacity: 0'">
            <ul class="submenu-list mt-1 space-y-1">
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.cpt.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.cpt.index') }}">
                        <span class="text-[14px] font-medium">Post Types</span>
                    </a>
                </li>
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ (request()->routeIs('admin.taxonomies.*') && !request()->routeIs('admin.taxonomies.terms.*')) ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.taxonomies.index') }}">
                        <span class="text-[14px] font-medium">Taxonomies</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    @endcan

    @can('settings.view')
    @php
        $settingsGroups = app(\App\Services\SettingsRegistry::class)->groups();
    @endphp
    @if(!empty($settingsGroups))
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3 min-w-0 flex-1 overflow-hidden">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="font-semibold text-[15px] sidebar-text">Settings</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">Settings</span>
        </button>

        <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 3000px; opacity: 1' : 'max-height: 0; opacity: 0'">
            <ul class="submenu-list mt-1 space-y-1">
                @foreach($settingsGroups as $sg)
                    @can($sg['permission'] ?? 'settings.view')
                    <li class="relative pl-6 py-1">
                        <div class="submenu-item-connector"></div>
                        <a wire:navigate
                           class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10
                                {{ request()->routeIs('admin.settings.show') && request()->route('group') === $sg['slug']
                                    ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold'
                                    : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                           href="{{ route('admin.settings.show', $sg['slug']) }}">
                            <span class="text-[14px] font-medium">{{ $sg['label'] }}</span>
                        </a>
                    </li>
                    @endcan
                @endforeach
            </ul>
        </div>
    </li>
    @endif
    @endcan
</ul>
