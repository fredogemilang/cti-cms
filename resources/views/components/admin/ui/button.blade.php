@props([
    'variant' => 'primary',
    'type' => 'submit',
    'href' => null,
])

@php
    $baseClasses = 'px-6 py-3 font-bold rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center';
    $variants = [
        'primary' => 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-[#272B30] dark:hover:bg-[#333] dark:text-[#FCFCFC]',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'outline' => 'bg-transparent border border-gray-200 dark:border-[#272B30] text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#1A1A1A]'
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClass}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClass}"]) }}>
        {{ $slot }}
    </button>
@endif

