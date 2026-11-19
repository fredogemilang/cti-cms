<div class="space-y-6">
    {{-- 1. Page Header --}}
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Bulk Title & Meta Description Editor</h1>
        <p class="text-sm font-normal text-[#6F767E] dark:text-[#9A9FA5] mt-1">Batch edit Meta Titles & Descriptions for pages and posts without opening each item individually.</p>
    </div>

    {{-- 2. Filter Type Tabs (Pages / Posts Pill Bar) --}}
    <div>
        <div class="inline-flex flex-wrap w-fit items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30] gap-1">
            <button
                wire:click="setFilterType('page')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $filterType === 'page' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <span>Pages</span>
                <span class="px-2 py-0.5 rounded-lg {{ $filterType === 'page' ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                    {{ $counts['page'] }}
                </span>
            </button>

            @if(class_exists(\Plugins\Posts\Models\Post::class))
            <button
                wire:click="setFilterType('post')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $filterType === 'post' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <span>Posts</span>
                <span class="px-2 py-0.5 rounded-lg {{ $filterType === 'post' ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                    {{ $counts['post'] }}
                </span>
            </button>
            @endif
        </div>
    </div>

    {{-- 3. Search & Display Rows Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search box -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative group w-full md:w-[320px]">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors z-10">search</span>
                <x-admin.ui.input
                    name="search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="!pl-12 !py-2.5 !rounded-2xl !h-12 text-sm !w-full"
                    placeholder="Search {{ $filterType }}s by title..." 
                />
            </div>
            @if($search !== '')
                <x-admin.ui.button
                    wire:click="$set('search', '')"
                    variant="secondary"
                    class="h-12 !px-4 !rounded-2xl text-sm font-semibold flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-lg">close</span>
                    <span>Clear</span>
                </x-admin.ui.button>
            @endif
        </div>

        <!-- Display Row Size -->
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-sm font-medium text-[#6F767E]">Display:</span>
            <select
                wire:model.live="perPage"
                class="h-12 rounded-2xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer shadow-sm">
                <option value="10">10 Rows</option>
                <option value="25">25 Rows</option>
                <option value="50">50 Rows</option>
                <option value="100">100 Rows</option>
            </select>
        </div>
    </div>

    {{-- 4. Bulk Meta Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#272B30] text-[11px] font-bold text-[#6F767E] uppercase tracking-wider">
                        <th class="py-3 px-4">Entity / Title</th>
                        <th class="py-3 px-4">SEO Title Override</th>
                        <th class="py-3 px-4">SEO Description Override</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30] text-sm">
                    @forelse($items as $item)
                        @php
                            $modelType = get_class($item);
                            $key = "{$modelType}_{$item->id}";
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#121212] transition-colors">
                            <td class="py-4 px-4 font-semibold text-[#111827] dark:text-[#FCFCFC] max-w-[200px]">
                                <div class="truncate">{{ $item->title }}</div>
                                <span class="text-[11px] text-[#6F767E] font-normal block font-mono">/{{ $item->slug }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <input type="text" wire:model="titles.{{ $key }}" placeholder="Defaults to: {{ $item->title }}" class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-3">
                            </td>
                            <td class="py-4 px-4">
                                <input type="text" wire:model="descriptions.{{ $key }}" placeholder="Defaults to excerpt or content snippet" class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-3">
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button type="button" wire:click="saveSeoRow('{{ addslashes($modelType) }}', {{ $item->id }})" class="px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-100 text-xs font-bold transition-colors">
                                    Save
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-xs text-[#6F767E]">
                                No {{ $filterType }} entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'links'))
            <div class="pt-4 border-t border-gray-100 dark:border-[#272B30]">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
