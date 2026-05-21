@php
    if (!setting('locale_switcher_enabled', false)) return;

    $labels = ['id' => 'ID', 'en' => 'EN', 'ja' => 'JP', 'fr' => 'FR', 'de' => 'DE', 'es' => 'ES'];
    $current = app()->getLocale();
    $locales = available_locales();
@endphp

<div x-data="{ open: false }" class="relative inline-block" @click.away="open = false" {{ $attributes }}>
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-current hover:bg-black/5 dark:hover:bg-white/10 transition"
    >
        <span class="material-symbols-outlined text-[18px]">language</span>
        <span>{{ $labels[$current] ?? strtoupper($current) }}</span>
        <span class="material-symbols-outlined text-[16px]" :class="open ? 'rotate-180' : ''">expand_more</span>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 mt-2 w-32 bg-white dark:bg-[#1A1A1A] rounded-lg shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-50"
    >
        @foreach($locales as $code)
            <a
                href="{{ request()->fullUrlWithQuery(['locale' => $code]) }}"
                @class([
                    'block px-4 py-2 text-sm transition',
                    'bg-blue-50 text-[#2563EB] dark:bg-blue-500/10 font-semibold' => $code === $current,
                    'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' => $code !== $current,
                ])
            >
                {{ $labels[$code] ?? strtoupper($code) }}
            </a>
        @endforeach
    </div>
</div>
