<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="flex flex-col sm:flex-row gap-4 items-center">
        <!-- Search -->
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10">search</span>
            <x-admin.ui.input 
                name="search" 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                class="!pl-10 !py-2 !rounded-xl !h-12 text-sm !w-full md:!w-[320px]" 
                placeholder="Search terms..." 
            />
        </div>
        
        <!-- Per Page -->
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
            <x-admin.ui.table-header sortBy="name" :field="$sortField" :direction="$sortDirection" class="px-8">
                Name
            </x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="slug" :field="$sortField" :direction="$sortDirection">
                Slug
            </x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8 text-right w-24">Count</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($terms as $term)
            <x-admin.ui.table-row class="group">
                <x-admin.ui.table-cell class="px-8">
                    <div class="flex items-center gap-2" style="padding-left: {{ ($term->depth ?? 0) * 1.5 }}rem">
                        @if(($term->depth ?? 0) > 0)
                            <span class="material-symbols-outlined text-[#6F767E] text-base shrink-0 select-none">subdirectory_arrow_right</span>
                        @endif
                        <div>
                            <div class="font-semibold text-[#111827] dark:text-[#FCFCFC] group-hover:text-[#2563EB] transition-colors">{{ $term->name }}</div>
                            
                            <!-- WP Style Hover Actions -->
                            <div class="flex items-center gap-2 mt-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                <button 
                                    wire:click="$dispatch('edit-term', { id: {{ $term->id }} })"
                                    class="text-[11px] font-bold text-[#2563EB] hover:underline uppercase tracking-wider"
                                >
                                    Edit
                                </button>
                                <span class="text-gray-300 dark:text-[#272B30]">|</span>
                                <button 
                                    wire:click="confirmDelete({{ $term->id }})"
                                    class="text-[11px] font-bold text-[#FF6A55] hover:underline uppercase tracking-wider"
                                >
                                    Delete
                                </button>
                            </div>

                            @if($term->description)
                                <div class="text-xs text-[#6F767E] truncate max-w-xs mt-1">{{ $term->description }}</div>
                            @endif
                        </div>
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <code class="px-2.5 py-1 bg-gray-100 dark:bg-[#272B30] text-[#2563EB] rounded-lg text-xs font-mono">{{ $term->slug }}</code>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-8">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#2563EB] dark:bg-blue-900/30">
                        {{ $term->entries_count }}
                    </span>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <tr>
                <td colspan="3" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-3xl text-gray-400">category</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No terms found</h3>
                        <p class="text-gray-500 dark:text-gray-400">Use the form on the left to add a new term.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>
    
    <!-- Pagination -->
    @if($terms->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $terms->firstItem() }} to {{ $terms->lastItem() }} of {{ $terms->total() }} terms
        </p>
        <div class="flex items-center gap-2">
            @if($terms->onFirstPage())
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @else
            <button wire:click="previousPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @endif

            @foreach($terms->getUrlRange(max(1, $terms->currentPage() - 2), min($terms->lastPage(), $terms->currentPage() + 2)) as $page => $url)
                @if($page == $terms->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($terms->hasMorePages())
            <button wire:click="nextPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
            @else
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
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
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Delete Term</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete this term? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-[#1A1A1A]/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 dark:border-[#272B30] gap-3">
                    <x-admin.ui.button 
                        type="button" 
                        variant="danger"
                        wire:click="performDelete"
                        class="w-full sm:ml-3 sm:w-auto !py-2 !px-4 text-sm"
                    >
                        Delete
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button" 
                        variant="secondary"
                        wire:click="cancelDelete"
                        class="mt-3 w-full sm:mt-0 sm:w-auto !py-2 !px-4 text-sm"
                    >
                        Cancel
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>
</div>

