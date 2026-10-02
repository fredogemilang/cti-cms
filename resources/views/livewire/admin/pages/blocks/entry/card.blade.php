{{-- Card Block Entry --}}
@php
    $val = $block['value'] ?? [];
    if (is_string($val)) {
        $val = json_decode($val, true) ?: [];
    }
    $val = array_merge([
        'title' => '',
        'description' => '',
        'asset_type' => 'image',
        'image' => '',
        'icon' => 'lucide:sparkles',
        'description_type' => 'text',
        'list_icon' => 'lucide:check-circle',
        'list_items' => '',
        'wysiwyg_content' => '',
        'button_text' => '',
        'button_url' => '#',
        'button_target' => '_self',
    ], $val);

    $img = $val['image'] ?? '';
    $assetType = $blocks[$index]['value']['asset_type'] ?? 'image';
    $descType = $blocks[$index]['value']['description_type'] ?? 'text';
@endphp

<div class="space-y-4 w-full">
    {{-- Row 1: Title & Asset Type Choice --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Title</label>
            <input wire:model="blocks.{{ $index }}.value.title" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. Enterprise Security" />
        </div>

        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Visual Asset Type</label>
            <select wire:model.live="blocks.{{ $index }}.value.asset_type"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3">
                <option value="image">Image (Media Storage)</option>
                <option value="icon">Icon (Lucide Icon)</option>
            </select>
        </div>
    </div>

    {{-- Asset Selection Field (Image vs Icon) --}}
    <div class="w-full">
        @if($assetType === 'icon')
            <div class="space-y-1.5 w-full">
                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Icon</label>
                <livewire:admin.icon-picker 
                    :field="'blocks.' . $index . '.value.icon'"
                    :value="$blocks[$index]['value']['icon'] ?? 'lucide:sparkles'"
                    :label="'Select Card Icon'"
                    :key="'card-icon-picker-' . $index"
                />
            </div>
        @else
            <div class="space-y-1 w-full">
                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Image Path / URL</label>
                <div class="flex gap-2">
                    <input wire:model="blocks.{{ $index }}.value.image" type="text"
                        class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                        placeholder="e.g. themes/cdt/assets/... or uploads/..." />
                    <button wire:click="openMediaPicker('block_{{ $index }}.image')" type="button"
                        class="h-10 px-3 shrink-0 rounded-lg bg-[#EFEFEF] dark:bg-[#272B30] hover:bg-gray-300 dark:hover:bg-[#353940] text-xs font-semibold flex items-center gap-1 transition-colors text-[#111827] dark:text-[#FCFCFC]">
                        <span class="material-symbols-outlined text-base">perm_media</span> Select
                    </button>
                </div>
                @if(!empty($img))
                    <div class="mt-2">
                        <img src="{{ resolve_block_asset($img) }}" alt="Preview" class="h-20 w-auto object-cover rounded-lg border border-gray-200 dark:border-[#272B30]" />
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Description Format Selection --}}
    <div class="space-y-1 w-full pt-2 border-t border-[#EFEFEF] dark:border-[#272B30]">
        <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Description Content Format</label>
        <select wire:model.live="blocks.{{ $index }}.value.description_type"
            class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3">
            <option value="text">Plain Text / Paragraph</option>
            <option value="listing">Bullet Listing / Feature Points</option>
            <option value="wysiwyg">WYSIWYG / Rich HTML</option>
        </select>
    </div>

    {{-- Description Content Inputs Based on Format --}}
    @if($descType === 'listing')
        <div class="space-y-4 w-full bg-[#F4F5F6]/60 dark:bg-[#0B0B0B]/60 p-4 rounded-xl border border-[#EFEFEF] dark:border-[#272B30]">
            <div class="space-y-1.5 w-full">
                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Listing Bullet Icon</label>
                <livewire:admin.icon-picker 
                    :field="'blocks.' . $index . '.value.list_icon'"
                    :value="$blocks[$index]['value']['list_icon'] ?? 'lucide:check-circle'"
                    :label="'Select Listing Icon'"
                    :key="'card-list-icon-' . $index"
                />
            </div>

            <div class="space-y-1 w-full">
                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">List Items (One item per line)</label>
                <textarea wire:model="blocks.{{ $index }}.value.list_items" rows="4"
                    class="w-full rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary p-3 font-mono text-xs"
                    placeholder="Feature item 1&#10;Feature item 2&#10;Feature item 3"></textarea>
                <p class="text-[10px] text-[#6F767E] italic">Enter each bullet list item on a new line.</p>
            </div>

            <div class="space-y-1 w-full">
                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Optional Lead Description Above List</label>
                <textarea wire:model="blocks.{{ $index }}.value.description" rows="2"
                    class="w-full rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary p-3"
                    placeholder="Optional introductory text..."></textarea>
            </div>
        </div>
    @elseif($descType === 'wysiwyg')
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Rich Content (HTML / WYSIWYG)</label>
            <textarea wire:model="blocks.{{ $index }}.value.wysiwyg_content" rows="4"
                class="w-full rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary p-3 font-mono text-xs"
                placeholder="<p>Formatted <strong>HTML</strong> content...</p>"></textarea>
        </div>
    @else
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Card Description (Plain Text)</label>
            <textarea wire:model="blocks.{{ $index }}.value.description" rows="3"
                class="w-full rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary p-3"
                placeholder="e.g. Explore our latest insights and news..."></textarea>
        </div>
    @endif

    {{-- Button Name & Button Link --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full pt-2 border-t border-[#EFEFEF] dark:border-[#272B30]">
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Button Name / Text (Optional)</label>
            <input wire:model="blocks.{{ $index }}.value.button_text" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. Explore / Learn More" />
        </div>
        <div class="space-y-1 w-full">
            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Button Link / URL</label>
            <input wire:model="blocks.{{ $index }}.value.button_url" type="text"
                class="w-full h-10 rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-primary px-3"
                placeholder="e.g. /insights or /careers" />
        </div>
    </div>
</div>
