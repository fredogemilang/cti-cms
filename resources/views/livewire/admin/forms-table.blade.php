<div>
    {{-- Filters & Search --}}
    <div class="space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Left: Search --}}
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative group w-full sm:w-auto">
                    <input
                        wire:model.live.debounce.300ms="search"
                        class="h-12 w-full sm:w-[320px] rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-12 pr-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-[#6F767E]"
                        placeholder="Search forms by name..." type="text" />
                    <span
                        class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors">search</span>
                    
                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="search" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-5 w-5 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                @if($search || $statusFilter)
                <x-admin.ui.button
                    wire:click="clearFilters"
                    variant="outline"
                    class="!h-12 !py-0 !rounded-xl text-sm"
                >
                    <span class="material-symbols-outlined text-lg mr-2">close</span>
                    Clear
                </x-admin.ui.button>
                @endif
            </div>

            {{-- Right: Display & Add New --}}
            <div class="flex flex-wrap items-center gap-3">
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
                
                @can('forms.create')
                <a href="{{ route('admin.forms.create') }}" wire:navigate
                    class="px-6 py-3 font-bold rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white text-sm h-12 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[20px] mr-2">add</span>
                    Create Form
                </a>
                @endcan
            </div>
        </div>

        {{-- Row 2: Status Filter Buttons --}}
        <div class="mb-4">
             <div class="inline-flex w-fit items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30]">
                @php
                    $statuses = [
                        '' => ['label' => 'All', 'count' => $statusCounts['all']],
                        'active' => ['label' => 'Active', 'count' => $statusCounts['active']],
                        'inactive' => ['label' => 'Inactive', 'count' => $statusCounts['inactive']],
                        'trashed' => ['label' => 'Trash', 'count' => $statusCounts['trashed']],
                    ];
                @endphp

                @foreach($statuses as $value => $data)
                    <button
                        wire:click="$set('statusFilter', '{{ $value }}')"
                        class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $statusFilter === $value ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                        {{ $data['label'] }}
                        <span class="px-2 py-0.5 rounded-lg {{ $statusFilter === $value ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                            {{ $data['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Forms Table --}}
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="px-8 w-10">
                <input
                    wire:model.live="selectAll"
                    class="custom-checkbox"
                    type="checkbox" />
            </x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="name" :field="$sortField" :direction="$sortDirection">
                Form Name
            </x-admin.ui.table-header>
            <x-admin.ui.table-header>Slug</x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">
                <button wire:click="sortBy('entries_count')" class="flex items-center gap-1 hover:text-[#2563EB] transition-colors focus:outline-none mx-auto">
                    Submissions
                    @if($sortField === 'entries_count')
                        <span class="material-symbols-outlined text-base text-[#2563EB]">{{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                    @else
                        <span class="material-symbols-outlined text-base opacity-30">unfold_more</span>
                    @endif
                </button>
            </x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">Status</x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="created_at" :field="$sortField" :direction="$sortDirection">
                Created
            </x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8 w-24">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($forms as $form)
        <x-admin.ui.table-row wire:key="form-{{ $form->id }}">
            <x-admin.ui.table-cell class="px-8">
                <input
                    wire:model.live="selectedForms"
                    value="{{ $form->id }}"
                    class="custom-checkbox"
                    type="checkbox" />
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                <div>
                    <p class="text-[15px] font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $form->name }}</p>
                    @if($form->description)
                    <p class="text-xs text-[#6F767E] line-clamp-1 mt-1">{{ Str::limit($form->description, 60) }}</p>
                    @endif
                </div>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                <code class="text-xs bg-gray-100 dark:bg-[#272B30] text-[#2563EB] px-2 py-1 rounded-lg font-mono">{{ $form->slug }}</code>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell class="text-center">
                <div class="flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[#6F767E] text-[18px]">description</span>
                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ number_format($form->entries_count) }}</span>
                </div>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell class="text-center">
                <button 
                    wire:click="toggleStatus({{ $form->id }})"
                    class="inline-flex items-center gap-1.5 rounded-lg {{ $form->is_active ? 'bg-[#3F8C5826] text-[#83BF6E]' : 'bg-gray-100 dark:bg-[#272B30] text-[#6F767E]' }} px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider hover:opacity-80 transition-opacity">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ $form->is_active ? 'Active' : 'Inactive' }}
                </button>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">{{ $form->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-[#6F767E]">{{ $form->created_at->format('H:i') }}</p>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell align="right" class="px-8">
                <div class="flex gap-2 items-center justify-end">
                    @if($statusFilter === 'trashed')
                        @can('forms.delete')
                        <button 
                            wire:click="restore({{ $form->id }})"
                            class="w-9 h-9 p-2 rounded-xl text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Restore">
                            <span class="material-symbols-outlined text-[20px]">restore_from_trash</span>
                        </button>
                        
                        <button 
                            x-data
                            @click="$dispatch('open-force-delete-modal', { formId: {{ $form->id }}, formName: '{{ addslashes($form->name) }}' })"
                            class="w-9 h-9 p-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Delete Permanently">
                            <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                        </button>
                        @endcan
                    @else
                        @can('forms.view')
                        <a href="{{ route('admin.forms.entries', $form) }}"
                            class="w-9 h-9 p-2 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="View Submissions">
                            <span class="material-symbols-outlined text-[20px]">list_alt</span>
                        </a>
                        @endcan
                        
                        @can('forms.edit')
                        <a href="{{ route('admin.forms.edit', $form) }}" wire:navigate
                            class="w-9 h-9 p-2 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Edit Form">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        @endcan
                        
                        @can('forms.delete')
                        <button 
                            x-data
                            @click="$dispatch('open-delete-modal', { formId: {{ $form->id }}, formName: '{{ addslashes($form->name) }}' })"
                            class="w-9 h-9 p-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center justify-center transition-colors"
                            data-tooltip="Delete Form">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                        @endcan
                    @endif
                </div>
            </x-admin.ui.table-cell>
        </x-admin.ui.table-row>
        @empty
        <tr>
            <td colspan="7" class="px-8 py-16 text-center">
                <div class="flex flex-col items-center">
                    <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-[#6F767E]">description</span>
                    </div>
                    <p class="text-[#6F767E] font-medium">
                        @if($search || $statusFilter)
                            No forms found matching your criteria
                        @else
                            No forms yet. Create your first form!
                        @endif
                    </p>
                    @if($search || $statusFilter)
                    <button wire:click="clearFilters" class="mt-3 text-sm text-[#2563EB] hover:underline">Clear filters</button>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
    </x-admin.ui.table>

    {{-- Pagination --}}
    @if($forms->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $forms->firstItem() }} to {{ $forms->lastItem() }} of {{ $forms->total() }} forms
        </p>
        <div class="flex items-center gap-2">
            @if($forms->onFirstPage())
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

            @foreach($forms->getUrlRange(max(1, $forms->currentPage() - 2), min($forms->lastPage(), $forms->currentPage() + 2)) as $page => $url)
                @if($page == $forms->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($forms->hasMorePages())
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

    {{-- Bulk Action Bar --}}
    @if(count($selectedForms) > 0)
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
        <div class="bg-[#2563EB] border border-[#2563EB] rounded-2xl shadow-2xl px-6 py-3 flex items-center gap-6">
            <div class="flex items-center gap-3 border-r border-blue-400/30 pr-6">
                <span class="bg-white text-[#2563EB] text-xs font-bold px-2.5 py-1 rounded-full min-w-[24px] text-center">{{ count($selectedForms) }}</span>
                <span class="text-sm font-semibold text-white">Selected</span>
            </div>
            <div class="flex items-center gap-4">
                @if($statusFilter === 'trashed')
                <button 
                    wire:click="restoreSelected"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">restore_from_trash</span>
                    Restore
                </button>
                <button 
                    x-data
                    @click="$dispatch('open-bulk-force-delete-modal')"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-[#FF6A55] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                    Delete Permanently
                </button>
                @else
                <button 
                    wire:click="activateSelected"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Activate
                </button>
                <button 
                    wire:click="deactivateSelected"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Deactivate
                </button>
                <button 
                    x-data
                    @click="$dispatch('open-bulk-delete-modal')"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-[#FF6A55] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Delete
                </button>
                @endif
            </div>
            <button wire:click="clearSelection" class="ml-2 w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-white/70 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Delete Modal --}}
    <div 
        x-data="{ show: false, formId: null, formName: '', bulk: false }"
        @open-delete-modal.window="show = true; formId = $event.detail.formId; formName = $event.detail.formName; bulk = false"
        @open-bulk-delete-modal.window="show = true; bulk = true"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm">
        <div 
            @click.outside="show = false"
            x-show="show"
            x-transition
            class="w-full max-w-[440px] bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-[#272B30] rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-[#FF6A55]/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-[#FF6A55] text-3xl">delete_forever</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-[#FCFCFC] mb-3">Delete Form</h3>
                <p class="text-gray-500 dark:text-[#6F767E] leading-relaxed mb-8">
                    <template x-if="bulk">
                        <span>Are you sure you want to delete <span class="font-bold">{{ count($selectedForms) }}</span> selected form(s)? All submissions will also be deleted.</span>
                    </template>
                    <template x-if="!bulk">
                        <span>Are you sure you want to delete "<span class="font-bold" x-text="formName"></span>"? All submissions will also be deleted.</span>
                    </template>
                </p>
                <div class="flex items-center gap-3 w-full">
                    <x-admin.ui.button type="button" variant="secondary" @click="show = false" class="flex-1">
                        Cancel
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button"
                        variant="danger"
                        @click="if (bulk) { $wire.deleteSelected(); } else { $wire.deleteForm(formId); } show = false;"
                        class="flex-1">
                        Delete
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>

    {{-- Force Delete Modal --}}
    <div 
        x-data="{ show: false, formId: null, formName: '', bulk: false }"
        @open-force-delete-modal.window="show = true; formId = $event.detail.formId; formName = $event.detail.formName; bulk = false"
        @open-bulk-force-delete-modal.window="show = true; bulk = true"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm">
        <div 
            @click.outside="show = false"
            x-show="show"
            x-transition
            class="w-full max-w-[440px] bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-[#272B30] rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-[#FF6A55]/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-[#FF6A55] text-3xl">delete_forever</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-[#FCFCFC] mb-3">Delete Permanently</h3>
                <p class="text-gray-500 dark:text-[#6F767E] leading-relaxed mb-8">
                    <template x-if="bulk">
                        <span>Are you sure you want to PERMANENTLY delete <span class="font-bold">{{ count($selectedForms) }}</span> selected form(s)? This action cannot be undone.</span>
                    </template>
                    <template x-if="!bulk">
                        <span>Are you sure you want to PERMANENTLY delete "<span class="font-bold" x-text="formName"></span>"? This action cannot be undone.</span>
                    </template>
                </p>
                <div class="flex items-center gap-3 w-full">
                    <x-admin.ui.button type="button" variant="secondary" @click="show = false" class="flex-1">
                        Cancel
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button"
                        variant="danger"
                        @click="if (bulk) { $wire.forceDeleteSelected(); } else { $wire.forceDelete(formId); } show = false;"
                        class="flex-1">
                        Delete Permanently
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>
</div>

