@props([
    'type' => 'success'
])

@php
    $classes = $type === 'success' 
        ? 'bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400' 
        : 'bg-rose-50 dark:bg-rose-950/20 border border-rose-500/30 text-rose-600 dark:text-rose-400';
    $icon = $type === 'success' ? 'check_circle' : 'error';
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-xl flex items-center gap-3 {$classes}"]) }}>
    <span class="material-symbols-outlined">{{ $icon }}</span>
    <span class="font-medium text-sm">{{ $slot }}</span>
</div>
