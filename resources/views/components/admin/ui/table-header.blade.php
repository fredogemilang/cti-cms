@props([
    'sortBy' => null,
    'field' => null,
    'direction' => null,
    'align' => 'left'
])

<th {{ $attributes->merge(['class' => 'px-4 py-6 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest' . ($align === 'right' ? ' text-right' : '')]) }}>
    @if($sortBy)
    <button type="button" wire:click="sortBy('{{ $sortBy }}')" class="flex items-center gap-1 hover:text-[#2563EB] transition-colors focus:outline-none {{ $align === 'right' ? 'justify-end w-full' : '' }}">
        {{ $slot }}
        @if($field === $sortBy)
            <span class="material-symbols-outlined text-base text-[#2563EB]">{{ $direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
        @else
            <span class="material-symbols-outlined text-base opacity-30">unfold_more</span>
        @endif
    </button>
    @else
        {{ $slot }}
    @endif
</th>
