{{-- Gallery Block --}}
@php
    $value = $block['value'] ?? null;
    $images = is_array($value) ? $value : (is_string($value) ? (json_decode($value, true) ?? []) : []);
@endphp

<div class="space-y-4" x-data="{
    draggedIndex: null,
    dragOverIndex: null,
    onDragStart(e, index) {
        this.draggedIndex = index;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', index);
    },
    onDragOver(e, index) {
        e.preventDefault();
        this.dragOverIndex = index;
    },
    onDrop(e, toIndex) {
        e.preventDefault();
        if (this.draggedIndex !== null && this.draggedIndex !== toIndex) {
            $wire.reorderGalleryImages({{ $index }}, this.draggedIndex, toIndex);
        }
        this.draggedIndex = null;
        this.dragOverIndex = null;
    },
    onDragEnd() {
        this.draggedIndex = null;
        this.dragOverIndex = null;
    }
}">
    <div class="flex items-center justify-between">
        <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider flex items-center gap-1.5">
            <span>Gallery Images</span>
            @if(count($images) > 1)
                <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500 normal-case">(drag & drop to reorder)</span>
            @endif
        </label>
        <span class="text-xs text-[#6F767E]">{{ count($images) }} / {{ $block['options']['max_items'] ?? 10 }} images</span>
    </div>

    <div class="grid grid-cols-3 gap-2">
        @foreach($images as $imageIndex => $image)
            <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-black/20 cursor-grab active:cursor-grabbing transition-all duration-200"
                :class="{
                    'opacity-40 scale-95 border-dashed border-primary': draggedIndex === {{ $imageIndex }},
                    'ring-2 ring-primary ring-offset-2 dark:ring-offset-gray-900 scale-105': dragOverIndex === {{ $imageIndex }} && draggedIndex !== {{ $imageIndex }}
                }"
                draggable="true"
                @dragstart="onDragStart($event, {{ $imageIndex }})"
                @dragover="onDragOver($event, {{ $imageIndex }})"
                @dragleave="dragOverIndex = null"
                @drop="onDrop($event, {{ $imageIndex }})"
                @dragend="onDragEnd()"
                wire:key="gallery-image-{{ $index }}-{{ $imageIndex }}">
                
                <img src="{{ resolve_block_asset($image) }}" alt="Gallery Image"
                    class="w-full h-full object-cover pointer-events-none select-none" />

                <!-- Drag handle overlay icon -->
                <div class="absolute top-1 left-1 h-6 w-6 rounded-md bg-black/60 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none shadow-sm">
                    <span class="material-symbols-outlined text-xs">drag_indicator</span>
                </div>

                <button type="button"
                    wire:click="removeGalleryImage({{ $index }}, {{ $imageIndex }})"
                    class="absolute top-1 right-1 h-6 w-6 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center hover:bg-red-600 shadow-sm">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        @endforeach

        @if(count($images) < ($block['options']['max_items'] ?? 10))
            <div wire:click="openMediaPicker('block_{{ $index }}')"
                class="aspect-square rounded-lg bg-[#F4F5F6] dark:bg-[#0B0B0B] border-2 border-dashed border-gray-300 dark:border-[#272B30] flex flex-col items-center justify-center gap-1 hover:bg-gray-100 dark:hover:bg-[#1A1A1A] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-2xl text-gray-300 dark:text-[#272B30]">add_photo_alternate</span>
                <span class="text-[10px] text-[#6F767E]">Add</span>
            </div>
        @endif
    </div>
</div>
