@if($enabled && !empty($items))
    <style>
        .seo-breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.025em;
            padding: 8px 0;
            min-width: 0;
            max-width: 100%;
            flex-wrap: nowrap;
            overflow: hidden;
            white-space: nowrap;
        }
        .seo-breadcrumb-nav .breadcrumb-link {
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.15s;
            flex-shrink: 0;
        }
        .seo-breadcrumb-nav .breadcrumb-link:hover {
            opacity: 1;
            text-decoration: underline;
        }
        .seo-breadcrumb-nav .breadcrumb-current {
            font-weight: 700;
            opacity: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .seo-breadcrumb-nav .breadcrumb-title-dots {
            display: none;
        }
        .seo-breadcrumb-nav .breadcrumb-title-full {
            display: inline;
        }
        @media (max-width: 640px) {
            .seo-breadcrumb-nav {
                flex-wrap: nowrap !important;
                white-space: nowrap !important;
                overflow-x: auto !important;
                scrollbar-width: none !important;
                -ms-overflow-style: none !important;
            }
            .seo-breadcrumb-nav::-webkit-scrollbar {
                display: none !important;
            }
            .seo-breadcrumb-nav .breadcrumb-title-full {
                display: none !important;
            }
            .seo-breadcrumb-nav .breadcrumb-title-dots {
                display: inline !important;
            }
        }
    </style>
    <nav {{ $attributes->merge(['class' => 'seo-breadcrumb-nav flex items-center space-x-2 text-xs font-semibold tracking-wide py-2 min-w-0 max-w-full']) }} aria-label="Breadcrumb">
        @if($prefix !== '')
            <span class="breadcrumb-prefix opacity-60 mr-1 shrink-0">{{ $prefix }}</span>
        @endif

        @foreach($items as $index => $item)
            @php
                $isLast = ($index === count($items) - 1);
            @endphp

            @if($index > 0)
                <svg width="12" height="12" style="width:12px;height:12px;flex-shrink:0;display:inline-block;" class="w-3 h-3 opacity-40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @endif

            @if($isLast)
                <span class="breadcrumb-current font-bold opacity-100 truncate max-w-[160px] sm:max-w-none inline-block align-bottom shrink-0" aria-current="page" title="{{ $item['name'] }}">
                    <span class="breadcrumb-title-full hidden sm:inline">{{ $item['name'] }}</span>
                    <span class="breadcrumb-title-dots sm:hidden" aria-hidden="true">...</span>
                </span>
            @else
                <a href="{{ $item['url'] }}" class="breadcrumb-link hover:underline opacity-80 hover:opacity-100 transition-opacity truncate max-w-[120px] sm:max-w-none inline-block align-bottom shrink-0" title="{{ $item['name'] }}">
                    {{ $item['name'] }}
                </a>
            @endif
        @endforeach
    </nav>
@endif
