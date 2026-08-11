{{-- Card Block --}}
@php
    $val = $block['value'] ?? [];
    if (is_string($val)) {
        $val = json_decode($val, true) ?: [];
    }
    $img = $val['image'] ?? '';
@endphp

<div class="space-y-4 w-full">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Title</label>
            <input wire:model="blocks.{{ $index }}.value.title" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. Blog, News & Video" />
        </div>
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Image Path / URL</label>
            <div class="flex gap-2">
                <input wire:model="blocks.{{ $index }}.value.image" type="text"
                    class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                    placeholder="e.g. themes/cdt/assets/... or uploads/..." />
                <button wire:click="openMediaPicker('block_{{ $index }}.image')" type="button"
                    class="h-10 px-3 shrink-0 rounded-lg bg-[#EFEFEF] dark:bg-[#272B30] hover:bg-gray-300 dark:hover:bg-[#353940] text-xs font-semibold flex items-center gap-1 transition-colors">
                    <span class="material-symbols-outlined text-base">perm_media</span> Select
                </button>
            </div>
        </div>
    </div>
    <div class="space-y-1 w-full">
        <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Description</label>
        <textarea wire:model="blocks.{{ $index }}.value.description" rows="2"
            class="w-full rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary p-3"
            placeholder="e.g. Explore our latest insights and news..."></textarea>
    </div>
    @if(!empty($img))
        <div class="mt-2">
            <img src="{{ resolve_block_asset($img) }}" alt="Preview" class="h-24 w-auto object-cover rounded-lg border border-gray-200 dark:border-[#272B30]" />
        </div>
    @endif
</div>
