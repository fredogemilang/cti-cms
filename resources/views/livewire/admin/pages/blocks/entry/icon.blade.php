{{-- Icon Block Entry --}}
<div class="space-y-2">
    <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">{{ $block['label'] ?? 'Icon' }}</label>

    <livewire:admin.icon-picker 
        :field="'blocks.' . $index . '.value'"
        :value="$block['value'] ?? null"
        :label="$block['label'] ?? 'Select Icon'"
        :key="'top-block-icon-' . $index"
    />
</div>
