<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Redirects Manager</h1>
        <p class="text-sm font-normal text-[#6F767E] dark:text-[#9A9FA5] mt-1">Manage 301/302 URL redirects and regex rewrite rules</p>
    </div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6 items-center">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] z-10">search</span>
            <x-admin.ui.input 
                name="search" 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                class="!pl-12 !py-2.5 !rounded-xl !h-12 text-sm !w-full" 
                placeholder="Search by path, target, or notes..." 
            />
        </div>
        <x-admin.ui.button
            type="button"
            wire:click="add"
            variant="{{ $editingId !== 0 ? 'primary' : 'secondary' }}"
            class="!h-12 !px-6 !rounded-xl text-sm whitespace-nowrap"
            ::class="{ 'opacity-50 cursor-not-allowed': editingId === 0 }"
            ::disabled="editingId === 0"
        >
            <span class="material-symbols-outlined text-[20px] mr-2">add</span>
            Add Redirect
        </x-admin.ui.button>
    </div>

    {{-- Inline form (new or edit) --}}
    @if($editingId !== null)
        <x-admin.ui.card padding="p-6" class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">
                    {{ $editingId === 0 ? 'Add Redirect Rule' : 'Edit Redirect Rule' }}
                </h3>
                <button wire:click="cancel" class="text-sm text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]">
                    <span class="material-symbols-outlined align-middle text-[18px]">close</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <x-admin.ui.input
                        name="from_path"
                        type="text"
                        label="From Path"
                        wire:model.lazy="form.from_path"
                        placeholder="/old-url or ^/blog/(\d+)$"
                        class="!font-mono !py-2.5"
                    />
                    @error('form.from_path')
                        <p class="text-xs text-[#FF6A55] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-5">
                    <x-admin.ui.input
                        name="to_url"
                        type="text"
                        label="To URL"
                        wire:model.lazy="form.to_url"
                        placeholder="/new-url or https://other.com/page or /posts/$1"
                        class="!font-mono !py-2.5"
                    />
                    @error('form.to_url')
                        <p class="text-xs text-[#FF6A55] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <x-admin.ui.select
                        name="status_code"
                        label="Status"
                        wire:model.lazy="form.status_code"
                        class="!py-2.5"
                    >
                        <option value="301">301 Permanent</option>
                        <option value="302">302 Temporary</option>
                        <option value="307">307 Temp (keep method)</option>
                        <option value="308">308 Perm (keep method)</option>
                    </x-admin.ui.select>
                </div>

                <div class="md:col-span-12">
                    <x-admin.ui.input
                        name="notes"
                        type="text"
                        label="Notes (optional)"
                        wire:model.lazy="form.notes"
                        placeholder="Why this redirect exists, e.g. 'Migrated from old blog'"
                        class="!py-2.5"
                    />
                </div>

                <div class="md:col-span-12 flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" wire:model.lazy="form.is_regex" class="h-4 w-4 rounded accent-[#2563EB]" />
                        <span class="text-[#111827] dark:text-[#FCFCFC]">Regex pattern</span>
                        <span class="text-xs text-[#6F767E]">— use `$1`, `$2` in target for capture groups</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" wire:model.lazy="form.is_active" class="h-4 w-4 rounded accent-[#2563EB]" />
                        <span class="text-[#111827] dark:text-[#FCFCFC]">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <x-admin.ui.button
                    type="button"
                    wire:click="cancel"
                    variant="secondary"
                    class="!py-2.5 text-sm"
                >
                    Cancel
                </x-admin.ui.button>
                <x-admin.ui.button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    variant="primary"
                    class="!py-2.5 text-sm"
                >
                    <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px] mr-2">save</span>
                    <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin mr-2">progress_activity</span>
                    {{ $editingId === 0 ? 'Create' : 'Update' }}
                </x-admin.ui.button>
            </div>
        </x-admin.ui.card>
    @endif

    {{-- Table --}}
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header>From</x-admin.ui.table-header>
            <x-admin.ui.table-header>To</x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">Code</x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">Type</x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">Hits</x-admin.ui.table-header>
            <x-admin.ui.table-header>Last hit</x-admin.ui.table-header>
            <x-admin.ui.table-header class="text-center">Active</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-6 w-24">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($redirects as $r)
            <x-admin.ui.table-row @class(['hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors', 'bg-blue-50 dark:bg-blue-500/10' => $editingId === $r->id])>
                <x-admin.ui.table-cell class="font-mono text-xs text-[#111827] dark:text-[#FCFCFC] break-all max-w-[280px]">{{ $r->from_path }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="font-mono text-xs text-[#6F767E] break-all max-w-[280px]">{{ $r->to_url }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-center">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg
                        {{ in_array($r->status_code, [301,308]) ? 'bg-purple-500/15 text-purple-600 dark:text-purple-400' : 'bg-blue-500/15 text-blue-600 dark:text-blue-400' }}">
                        {{ $r->status_code }}
                    </span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-center">
                    @if($r->is_regex)
                        <span class="text-xs font-bold text-orange-500 bg-orange-500/15 px-2 py-1 rounded-lg">regex</span>
                    @else
                        <span class="text-xs text-[#6F767E]">exact</span>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-center text-[#111827] dark:text-[#FCFCFC] font-bold">{{ number_format($r->hit_count) }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-xs text-[#6F767E]">{{ $r->last_hit_at?->diffForHumans() ?? '—' }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-center">
                    <button
                        type="button"
                        wire:click="toggleActive({{ $r->id }})"
                        title="{{ $r->is_active ? 'Click to disable' : 'Click to enable' }}"
                        class="inline-flex h-5 w-9 items-center rounded-full transition cursor-pointer
                            {{ $r->is_active ? 'bg-[#83BF6E]' : 'bg-gray-300 dark:bg-[#272B30]' }}"
                    >
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $r->is_active ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                    </button>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    <div class="flex items-center justify-end gap-1">
                        <button
                            type="button"
                            wire:click="edit({{ $r->id }})"
                            class="h-9 w-9 rounded-xl hover:bg-gray-100 dark:hover:bg-[#272B30] text-[#6F767E] hover:text-[#2563EB] flex items-center justify-center transition-colors"
                            title="Edit"
                        >
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button
                            type="button"
                            wire:click="delete({{ $r->id }})"
                            wire:confirm="Delete this redirect rule?"
                            class="h-9 w-9 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 text-[#6F767E] hover:text-[#FF6A55] flex items-center justify-center transition-colors"
                            title="Delete"
                        >
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
            @if($r->notes)
                <tr class="bg-transparent">
                    <td colspan="8" class="px-6 pb-4 -mt-2">
                        <p class="text-xs text-[#6F767E] italic">{{ $r->notes }}</p>
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="8" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-[40px] text-[#6F767E]">trending_flat</span>
                        <p class="text-sm font-medium text-[#6F767E]">No redirect rules yet.</p>
                        <p class="text-xs text-[#6F767E]">Click "Add Redirect" to create your first rule.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>

    <!-- Pagination -->
    @if($redirects->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $redirects->firstItem() }} to {{ $redirects->lastItem() }} of {{ $redirects->total() }} redirects
        </p>
        <div class="flex items-center gap-2">
            @if($redirects->onFirstPage())
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

            @foreach($redirects->getUrlRange(max(1, $redirects->currentPage() - 2), min($redirects->lastPage(), $redirects->currentPage() + 2)) as $page => $url)
                @if($page == $redirects->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($redirects->hasMorePages())
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
