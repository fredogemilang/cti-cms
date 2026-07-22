@if($enabled && !empty($items))
    <nav class="seo-breadcrumbs font-medium text-xs text-[#6F767E] dark:text-[#9A9FA5] flex items-center flex-wrap gap-2 py-2" aria-label="Breadcrumb">
        @if($prefix !== '')
            <span class="breadcrumb-prefix text-gray-400 mr-1">{{ $prefix }}</span>
        @endif

        @foreach($items as $index => $item)
            @php
                $isLast = ($index === count($items) - 1);
            @endphp

            @if($index > 0)
                <span class="breadcrumb-separator opacity-60 select-none px-0.5">{{ $separator }}</span>
            @endif

            @if($isLast)
                <span class="breadcrumb-current {{ $boldLast ? 'font-bold text-[#111827] dark:text-[#FCFCFC]' : '' }}" aria-current="page">
                    {{ $item['name'] }}
                </span>
            @else
                <a href="{{ $item['url'] }}" class="breadcrumb-link hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    {{ $item['name'] }}
                </a>
            @endif
        @endforeach
    </nav>
@endif
