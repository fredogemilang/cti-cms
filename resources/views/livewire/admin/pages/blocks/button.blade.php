{{-- Button Block --}}
@php
    $val = $block['value'] ?? [];
    if (is_string($val)) {
        $val = json_decode($val, true) ?: [];
    }
@endphp

<div class="w-full">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Button Text / Label</label>
            <input wire:model="blocks.{{ $index }}.value.text" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. Learn More" />
        </div>
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Button URL / Link</label>
            <input wire:model="blocks.{{ $index }}.value.url" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. #areas-of-expertise or https://..." />
        </div>
    </div>
</div>
