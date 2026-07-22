<div class="space-y-6">
    {{-- Status Filter Buttons --}}
    <div>
        <div class="inline-flex flex-wrap w-fit items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30] gap-1">
            <button 
                wire:click="$set('status', '')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === '' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
            >
                All
                <span class="px-2 py-0.5 rounded-lg {{ $status === '' ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                    {{ $statusCounts['all'] }}
                </span>
            </button>
            <button 
                wire:click="$set('status', 'active')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === 'active' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
            >
                Active
                <span class="px-2 py-0.5 rounded-lg {{ $status === 'active' ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                    {{ $statusCounts['active'] }}
                </span>
            </button>
            <button 
                wire:click="$set('status', 'inactive')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === 'inactive' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
            >
                Inactive
                <span class="px-2 py-0.5 rounded-lg {{ $status === 'inactive' ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                    {{ $statusCounts['inactive'] }}
                </span>
            </button>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search box -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative group w-full md:w-[320px]">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors z-10">search</span>
                <x-admin.ui.input
                    name="search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="!pl-12 !py-2.5 !rounded-xl !h-12 text-sm !w-full"
                    placeholder="Search post types..."
                />
            </div>
            
            @if($search || $status)
            <x-admin.ui.button
                wire:click="$set('status', ''); $set('search', '')"
                variant="outline"
                class="!h-12 !py-0 !rounded-xl text-sm"
            >
                <span class="material-symbols-outlined text-lg mr-2">close</span>
                Clear
            </x-admin.ui.button>
            @endif
        </div>
        
        <!-- Display Row Size -->
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-[#6F767E]">Display:</span>
            <select 
                wire:model.live="perPage"
                class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer"
            >
                <option value="10">10 Rows</option>
                <option value="25">25 Rows</option>
                <option value="50">50 Rows</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header sortBy="plural_label" :field="$sortField" :direction="$sortDirection" class="px-8">
                Post Type
            </x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="slug" :field="$sortField" :direction="$sortDirection" class="px-4">
                Slug
            </x-admin.ui.table-header>
            <x-admin.ui.table-header class="px-4">Fields</x-admin.ui.table-header>
            <x-admin.ui.table-header class="px-4">Status</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($postTypes as $cpt)
            <x-admin.ui.table-row wire:key="cpt-{{ $cpt->id }}">
                <x-admin.ui.table-cell class="px-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-symbols-outlined text-white text-[20px]">{{ $cpt->icon }}</span>
                        </div>
                        <div>
                            <div class="font-semibold text-[#111827] dark:text-[#FCFCFC]">{{ $cpt->plural_label }}</div>
                            <div class="text-xs text-[#6F767E]">ID : {{ $cpt->name }}</div>
                        </div>
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="px-4">
                    <code class="px-2.5 py-1 bg-gray-100 dark:bg-[#272B30] text-[#2563EB] rounded-lg text-xs font-mono">{{ $cpt->slug }}</code>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="px-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-[#272B30] text-[#6F767E] dark:text-[#FCFCFC] rounded-lg text-xs font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-sm">list</span>
                        {{ $cpt->metaFields->count() }} fields
                    </span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="px-4">
                    <button 
                        wire:click="toggleStatus({{ $cpt->id }})"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all {{ $cpt->is_active ? 'bg-[#3F8C5826] text-[#83BF6E]' : 'bg-gray-100 dark:bg-[#272B30] text-[#6F767E]' }}"
                    >
                        <span class="material-symbols-outlined text-sm">{{ $cpt->is_active ? 'check_circle' : 'cancel' }}</span>
                        {{ $cpt->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-8">
                    <div class="flex items-center justify-end gap-1">
                        <a 
                            href="{{ route('admin.cpt.entries.index', $cpt->slug) }}"
                            class="h-9 w-9 rounded-xl text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="View Entries"
                        >
                            <span class="material-symbols-outlined text-[20px]">folder_open</span>
                        </a>
                        <a 
                            href="{{ route('admin.cpt.edit', $cpt->id) }}"
                            class="h-9 w-9 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Edit"
                        >
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <button 
                            wire:click="confirmDelete({{ $cpt->id }})"
                            class="h-9 w-9 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Delete"
                        >
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell colspan="5" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-3xl text-gray-400">layers</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No post types yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first custom post type to get started</p>
                        <x-admin.ui.button href="{{ route('admin.cpt.create') }}" variant="primary">
                            <span class="material-symbols-outlined text-lg mr-1">add</span>
                            <span>Create Post Type</span>
                        </x-admin.ui.button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @endforelse
    </x-admin.ui.table>
        
        <!-- Pagination -->
        @if($postTypes->hasPages())
        <div class="px-8 py-6 border-t border-gray-100 dark:border-[#272B30] flex items-center justify-between">
            <p class="text-sm font-medium text-[#6F767E]">
                Showing {{ $postTypes->firstItem() }} to {{ $postTypes->lastItem() }} of {{ $postTypes->total() }} post types
            </p>
            <div class="flex items-center gap-2">
                @if($postTypes->onFirstPage())
                <button disabled
                    class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed">
                    <span class="material-symbols-outlined text-xl">chevron_left</span>
                </button>
                @else
                <button wire:click="previousPage"
                    class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                    <span class="material-symbols-outlined text-xl">chevron_left</span>
                </button>
                @endif

                @foreach($postTypes->getUrlRange(max(1, $postTypes->currentPage() - 2), min($postTypes->lastPage(), $postTypes->currentPage() + 2)) as $page => $url)
                    @if($page == $postTypes->currentPage())
                    <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                    @else
                    <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                    @endif
                @endforeach

                @if($postTypes->hasMorePages())
                <button wire:click="nextPage"
                    class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                    <span class="material-symbols-outlined text-xl">chevron_right</span>
                </button>
                @else
                <button disabled
                    class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed">
                    <span class="material-symbols-outlined text-xl">chevron_right</span>
                </button>
                @endif
            </div>
        </div>
        @endif

    <!-- Delete Confirmation Modal -->
    <div 
        x-data="{ show: @entangle('showDeleteModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 dark:bg-[#1A1A1A]/75 backdrop-blur-sm transition-opacity"
        ></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div 
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1A1A1A] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ring-1 ring-black/5 dark:ring-white/10"
                @click.away="show = false; $wire.cancelDelete()"
            >
                <div class="bg-white dark:bg-[#1A1A1A] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Delete Post Type</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete this post type? This action cannot be undone and will permanently remove all associated meta fields and entries.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-[#1A1A1A]/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 dark:border-[#272B30]">
                    <button 
                        type="button" 
                        wire:click="performDelete"
                        class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-all"
                    >
                        Delete
                    </button>
                    <button 
                        type="button" 
                        wire:click="cancelDelete"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-[#272B30] px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-[#2C3035] sm:mt-0 sm:w-auto transition-all"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
