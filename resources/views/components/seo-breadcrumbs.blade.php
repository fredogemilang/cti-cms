@if($enabled && !empty($items))
    <nav {{ $attributes->merge(['class' => 'flex items-center space-x-2 text-xs font-semibold tracking-wide py-2 min-w-0 max-w-full']) }} aria-label="Breadcrumb">
        @if($prefix !== '')
            <span class="breadcrumb-prefix opacity-60 mr-1 shrink-0">{{ $prefix }}</span>
        @endif

        @foreach($items as $index => $item)
            @php
                $isLast = ($index === count($items) - 1);
            @endphp

            @if($index > 0)
                <svg class="w-3 h-3 opacity-40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @endif

            @if($isLast)
                <span class="breadcrumb-current font-bold opacity-100 truncate max-w-[160px] sm:max-w-none inline-block align-bottom" aria-current="page" title="{{ $item['name'] }}">
                    {{ $item['name'] }}
                </span>
            @else
                <a href="{{ $item['url'] }}" class="breadcrumb-link hover:underline opacity-80 hover:opacity-100 transition-opacity truncate max-w-[120px] sm:max-w-none inline-block align-bottom shrink-0" title="{{ $item['name'] }}">
                    {{ $item['name'] }}
                </a>
            @endif
        @endforeach
    </nav>
@endif
