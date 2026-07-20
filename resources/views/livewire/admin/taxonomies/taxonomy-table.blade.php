<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="flex flex-col sm:flex-row gap-4 items-center">
        <!-- Search -->
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10">search</span>
                <x-admin.ui.input 
                    name="search" 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="!pl-10 !py-2 !rounded-xl !h-12 text-sm !w-full md:!w-[320px]" 
                    placeholder="Search taxonomies..." 
                />
            </div>
            
            <!-- Status Filter -->
            <div class="inline-flex w-fit items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30]">
                <button 
                    wire:click="$set('status', '')"
                    class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === '' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
                >
                    All
                </button>
                <button 
                    wire:click="$set('status', 'active')"
                    class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === 'active' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
                >
                    Active
                </button>
                <button 
                    wire:click="$set('status', 'inactive')"
                    class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === 'inactive' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
                >
                    Inactive
                </button>
            </div>
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

        <!-- Add Button -->
        <a href="{{ route('admin.taxonomies.create') }}" 
           class="px-6 py-3 font-bold rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white text-sm h-12 whitespace-nowrap">
            <span>Add Taxonomy</span>
        </a>
    </div>

    <!-- Table -->
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header sortBy="plural_label" :field="$sortField" :direction="$sortDirection" class="px-8">
                Taxonomy
            </x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="slug" :field="$sortField" :direction="$sortDirection">
                Slug
            </x-admin.ui.table-header>
            <x-admin.ui.table-header>Post Types</x-admin.ui.table-header>
            <x-admin.ui.table-header>Type</x-admin.ui.table-header>
            <x-admin.ui.table-header>Status</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8 w-24">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($taxonomies as $taxonomy)
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="px-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <span class="material-symbols-outlined text-white text-[20px]">{{ $taxonomy->is_hierarchical ? 'account_tree' : 'label' }}</span>
                        </div>
                        <div>
                            <div class="font-semibold text-[#111827] dark:text-[#FCFCFC]">{{ $taxonomy->plural_label }}</div>
                            <div class="text-xs text-[#6F767E]">ID : {{ $taxonomy->name }}</div>
                        </div>
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <code class="px-2.5 py-1 bg-gray-100 dark:bg-[#272B30] text-[#2563EB] rounded-lg text-xs font-mono">{{ $taxonomy->slug }}</code>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    @if(count($taxonomy->post_types ?? []) > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($taxonomy->post_types as $postType)
                                <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] rounded-lg text-xs font-bold uppercase tracking-wider">{{ $postType }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-[#6F767E] text-xs">Not assigned</span>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $taxonomy->is_hierarchical ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' }} rounded-lg text-xs font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[16px]">{{ $taxonomy->is_hierarchical ? 'account_tree' : 'label' }}</span>
                        {{ $taxonomy->is_hierarchical ? 'Hierarchical' : 'Flat' }}
                    </span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <button 
                        wire:click="toggleStatus({{ $taxonomy->id }})"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all {{ $taxonomy->is_active ? 'bg-[#3F8C5826] text-[#83BF6E]' : 'bg-gray-100 dark:bg-[#272B30] text-[#6F767E]' }}"
                    >
                        <span class="material-symbols-outlined text-sm">{{ $taxonomy->is_active ? 'check_circle' : 'cancel' }}</span>
                        {{ $taxonomy->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-8">
                    <div class="flex items-center justify-end gap-1">
                         <a 
                            href="{{ route('admin.taxonomies.terms.index', $taxonomy->id) }}"
                            class="h-9 w-9 rounded-xl hover:bg-gray-100 dark:hover:bg-[#272B30] text-[#6F767E] hover:text-emerald-500 flex items-center justify-center transition-colors"
                            title="Manage Terms"
                        >
                            <span class="material-symbols-outlined text-[20px]">list</span>
                        </a>
                        <a 
                            href="{{ route('admin.taxonomies.edit', $taxonomy->id) }}"
                            class="h-9 w-9 rounded-xl hover:bg-gray-100 dark:hover:bg-[#272B30] text-[#6F767E] hover:text-[#2563EB] flex items-center justify-center transition-colors"
                            title="Edit"
                        >
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <button 
                            wire:click="delete({{ $taxonomy->id }})"
                            wire:confirm="Are you sure you want to delete this taxonomy?"
                            class="h-9 w-9 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 text-[#6F767E] hover:text-[#FF6A55] flex items-center justify-center transition-colors"
                            title="Delete"
                        >
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <tr>
                <td colspan="6" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-3xl text-gray-400">category</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No taxonomies yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first taxonomy to get started</p>
                        <a href="{{ route('admin.taxonomies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#2563EB] hover:bg-blue-600 text-white font-medium rounded-xl transition-all">
                            <span class="material-symbols-outlined text-lg">add</span>
                            <span>Create Taxonomy</span>
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>
    
    <!-- Pagination -->
    @if($taxonomies->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $taxonomies->firstItem() }} to {{ $taxonomies->lastItem() }} of {{ $taxonomies->total() }} taxonomies
        </p>
        <div class="flex items-center gap-2">
            @if($taxonomies->onFirstPage())
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

            @foreach($taxonomies->getUrlRange(max(1, $taxonomies->currentPage() - 2), min($taxonomies->lastPage(), $taxonomies->currentPage() + 2)) as $page => $url)
                @if($page == $taxonomies->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($taxonomies->hasMorePages())
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
</div>

