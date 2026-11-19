@props([
    'loading' => false,
    'padding' => 'p-6'
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl shadow-sm overflow-hidden relative']) }}>
    @if($loading)
    <div class="absolute top-0 left-0 right-0 h-1 z-20 overflow-hidden">
        <div class="h-full bg-[#2563EB] animate-indeterminate origin-left"></div>
    </div>
    @endif
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
