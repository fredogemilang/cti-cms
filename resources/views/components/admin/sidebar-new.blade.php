<ul class="space-y-1 pb-12">
    <!-- Main Section -->
    <li class="px-4 pt-4 pb-2">
        <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">Main</span>
    </li>
    <li>
        <a wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
            href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined shrink-0">dashboard</span>
            <span class="font-semibold text-[15px] sidebar-text">Dashboard</span>
            <span class="sidebar-tooltip">Dashboard</span>
        </a>
    </li>

    <!-- Content Section -->
    <li class="px-4 pt-4 pb-2">
        <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">Content</span>
    </li>
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.pages.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; $dispatch('submenu-toggle'); }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    Pages
                </div>
                <a wire:navigate href="{{ route('admin.pages.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    All Pages
                </a>
                <a wire:navigate href="{{ route('admin.pages.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Add Page
                </a>
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
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 hover:bg-white hover:shadow-sm dark:hover:bg-[#272B30] dark:hover:shadow-none {{ request()->routeIs('admin.pages.create') ? 'text-[#2563EB] font-semibold dark:text-[#FCFCFC] dark:bg-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}" href="{{ route('admin.pages.create') }}">
                        <span class="text-[14px] font-medium">Add Page</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    {{-- Dynamic CPT Menus --}}
    @php
        $cpts = \App\Models\CustomPostType::active()->inMenu()->get();
    @endphp

    @foreach($cpts as $cpt)
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
            <div class="flex items-center gap-3">
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    {{ $cpt->plural_label }}
                </div>
                <a wire:navigate href="{{ route('admin.cpt.entries.index', $cpt->slug) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    All {{ $cpt->plural_label }}
                </a>
                <a wire:navigate href="{{ route('admin.cpt.entries.create', $cpt->slug) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Add {{ $cpt->singular_label }}
                </a>
                @foreach($cptTaxonomies as $taxonomy)
                    <a wire:navigate href="{{ route('admin.taxonomies.terms.index', $taxonomy->id) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
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
    @endforeach
    @can('media.view')
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.media.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined shrink-0">perm_media</span>
                <span class="font-semibold text-[15px] sidebar-text">Media</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">Media</span>
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    Media
                </div>
                <a wire:navigate href="{{ route('admin.media.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Library
                </a>
                @can('media.upload')
                <a wire:navigate href="{{ route('admin.media.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Add Media
                </a>
                @endcan
            </div>
        </div>
        <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 200px; opacity: 1' : 'max-height: 0; opacity: 0'">
            <ul class="submenu-list mt-1 space-y-1">
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.media.index') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.media.index') }}">
                        <span class="text-[14px] font-medium">Library</span>
                    </a>
                </li>
                @can('media.upload')
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.media.create') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.media.create') }}">
                        <span class="text-[14px] font-medium">Add Media</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcan

    {{-- Forms Menu --}}
    @can('forms.view')
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.forms.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined shrink-0">description</span>
                <span class="font-semibold text-[15px] sidebar-text">Forms</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">Forms</span>
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    Forms
                </div>
                <a wire:navigate href="{{ route('admin.forms.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    All Forms
                </a>
                @can('forms.create')
                <a wire:navigate href="{{ route('admin.forms.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Create Form
                </a>
                @endcan
                @can('forms.edit')
                <a wire:navigate href="{{ route('admin.forms.assignments') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Form Assignments
                </a>
                @endcan
            </div>
        </div>
        <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 200px; opacity: 1' : 'max-height: 0; opacity: 0'">
            <ul class="submenu-list mt-1 space-y-1">
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.forms.index') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.forms.index') }}">
                        <span class="text-[14px] font-medium">All Forms</span>
                    </a>
                </li>
                @can('forms.create')
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.forms.create') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.forms.create') }}">
                        <span class="text-[14px] font-medium">Create Form</span>
                    </a>
                </li>
                @endcan
                @can('forms.edit')
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.forms.assignments') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" href="{{ route('admin.forms.assignments') }}">
                        <span class="text-[14px] font-medium">Form Assignments</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcan

    {{-- Dynamic Plugin Menus (PRD Section 9.1) --}}
    @php
        $pluginMenuEvent = new \App\Events\RenderAdminMenu();
        event($pluginMenuEvent);
        $pluginMenus = collect($pluginMenuEvent->getMenuItems())->filter(fn($item) => str_starts_with($item['source'] ?? '', 'plugin:'));
    @endphp
    
    @if($pluginMenus->isNotEmpty())
    <li class="px-4 pt-4 pb-2">
        <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">Plugins</span>
    </li>
    @foreach($pluginMenus as $pluginMenu)
        @can($pluginMenu['permission'] ?? '')
        <li x-data="{ open: {{ request()->routeIs($pluginMenu['route'] . '*') ? 'true' : 'false' }} }">
            @if(!empty($pluginMenu['children']))
                <button
                    @click="open = !open"
                    class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined shrink-0">{{ $pluginMenu['icon'] ?? 'extension' }}</span>
                        <span class="font-semibold text-[15px] sidebar-text">{{ $pluginMenu['title'] }}</span>
                    </div>
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
                    <span class="sidebar-tooltip">{{ $pluginMenu['title'] }}</span>
                </button>
                <div class="submenu-container overflow-hidden" :style="open ? 'max-height: 300px; opacity: 1' : 'max-height: 0; opacity: 0'">
                    <ul class="submenu-list mt-1 space-y-1">
                        @foreach($pluginMenu['children'] as $child)
                            @can($child['permission'] ?? '')
                            <li class="relative pl-6 py-1">
                                <div class="submenu-item-connector"></div>
                                <a class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs($child['route']) ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                                   href="{{ $child['url'] ?? '#' }}">
                                    <span class="text-[14px] font-medium">{{ $child['title'] }}</span>
                                </a>
                            </li>
                            @endcan
                        @endforeach
                    </ul>
                </div>
            @else
                <a class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs($pluginMenu['route']) ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
                    href="{{ $pluginMenu['url'] ?? '#' }}">
                    <span class="material-symbols-outlined shrink-0">{{ $pluginMenu['icon'] ?? 'extension' }}</span>
                    <span class="font-semibold text-[15px] sidebar-text">{{ $pluginMenu['title'] }}</span>
                    <span class="sidebar-tooltip">{{ $pluginMenu['title'] }}</span>
                </a>
            @endif
        </li>
        @endcan
    @endforeach
    @endif

    <!-- Management Section -->
    <li class="px-4 pt-4 pb-2">
        <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">Management</span>
    </li>
    <!-- CPT Menu -->
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.cpt.index') || request()->routeIs('admin.cpt.create') || request()->routeIs('admin.cpt.edit') || (request()->routeIs('admin.taxonomies.*') && !request()->routeIs('admin.taxonomies.terms.*')) ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined shrink-0">layers</span>
                <span class="font-semibold text-[15px] sidebar-text">CPT</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">CPT</span>
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    CPT
                </div>
                <a wire:navigate href="{{ route('admin.cpt.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Post Types
                </a>
                <a wire:navigate href="{{ route('admin.taxonomies.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Taxonomies
                </a>
            </div>
        </div>
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

    @canany(['users.view', 'users.create', 'menus.view'])
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.profile.*') || request()->routeIs('admin.role-permission.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.menus.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    User
                </div>
                @can('users.view')
                <a wire:navigate href="{{ route('admin.users.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    All Users
                </a>
                @endcan
                @can('users.create')
                <a wire:navigate href="{{ route('admin.users.create') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Add User
                </a>
                @endcan
                <a wire:navigate href="{{ route('admin.profile.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Profile
                </a>
                @can('roles.view')
                <a wire:navigate href="{{ route('admin.role-permission.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Role & Permission
                </a>
                @endcan
                @can('menus.view')
                <a wire:navigate href="{{ route('admin.menus.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                    Menu Access
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
                @can('menus.view')
                <li class="relative pl-6 py-1">
                    <div class="submenu-item-connector"></div>
                    <a wire:navigate class="flex items-center rounded-xl px-4 py-2.5 transition-all duration-200 relative z-10 {{ request()->routeIs('admin.menus.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC] font-semibold' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}" 
                       href="{{ route('admin.menus.index') }}">
                        <span class="text-[14px] font-medium">Menu Access</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcanany

    <!-- System Section -->
    <li class="px-4 pt-4 pb-2">
        <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-widest sidebar-text">System</span>
    </li>
    @can('plugins.view')
    <li>
        <a wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs('admin.plugins.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
            href="{{ route('admin.plugins.index') }}">
            <span class="material-symbols-outlined shrink-0">extension</span>
            <span class="font-semibold text-[15px] sidebar-text">Plugins</span>
            <span class="sidebar-tooltip">Plugins</span>
        </a>
    </li>
    @endcan
    @can('activity.view')
    <li>
        <a wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 nav-item overflow-hidden {{ request()->routeIs('admin.activity.*') ? 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]' : 'text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none' }}"
            href="{{ route('admin.activity.index') }}">
            <span class="material-symbols-outlined shrink-0">history</span>
            <span class="font-semibold text-[15px] sidebar-text">Activity Log</span>
            <span class="sidebar-tooltip">Activity Log</span>
        </a>
    </li>
    @endcan
    @can('themes.view')
    <li class="relative" x-data="{ open: {{ request()->routeIs('admin.themes.*') ? 'true' : 'false' }}, flyoutOpen: false }" @click.away="flyoutOpen = false" :class="{ 'flyout-active': flyoutOpen }">
        <button
            @click="if (sidebarCollapsed) { flyoutOpen = !flyoutOpen; } else { open = !open; }"
            class="w-full group flex items-center justify-between rounded-xl px-4 py-3 text-[#6F767E] hover:text-[#111827] hover:bg-white hover:shadow-sm dark:hover:text-[#FCFCFC] dark:hover:bg-[#272B30] dark:hover:shadow-none transition-all duration-200 cursor-pointer focus:outline-none nav-item overflow-hidden"
            :class="{ 'bg-blue-100 text-[#2563EB] dark:bg-[#272B30] dark:text-[#FCFCFC]': sidebarCollapsed && flyoutOpen }">
            <div class="flex items-center gap-3">
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    Appearance
                </div>
                <a wire:navigate href="{{ route('admin.themes.index') }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
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
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="font-semibold text-[15px] sidebar-text">Settings</span>
            </div>
            <span class="material-symbols-outlined text-xl transition-transform duration-300 expand-icon" :class="{ 'rotate-180': open }">expand_more</span>
            <span class="sidebar-tooltip">Settings</span>
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
            class="absolute left-[calc(100%+12px)] top-1/2 -translate-y-1/2 z-[100] w-52 rounded-2xl bg-[#1E2430] dark:bg-[#1A1A1A] border border-gray-700/50 dark:border-[#272B30] p-2 shadow-2xl text-white">
            <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-[7px] border-solid border-r-[#1E2430] dark:border-r-[#1A1A1A] border-y-transparent border-l-transparent"></div>
            <div class="space-y-1">
                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/40 dark:border-gray-800 mb-1">
                    Settings
                </div>
                @foreach($settingsGroups as $sg)
                    @can($sg['permission'] ?? 'settings.view')
                    <a wire:navigate href="{{ route('admin.settings.show', $sg['slug']) }}" @click="flyoutOpen = false" class="flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-white/10 transition-colors">
                        {{ $sg['label'] }}
                    </a>
                    @endcan
                @endforeach
            </div>
        </div>
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
    
<!-- Litespeed Cache menu removed — now under Settings > Cache -->
</ul>
