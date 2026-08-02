@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2 my-8">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-100/60 rounded-xl cursor-not-allowed" aria-disabled="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}#video-list" rel="prev" class="inline-flex items-center justify-center w-10 h-10 text-gray-700 bg-white border border-gray-200 rounded-xl shadow-xs hover:bg-gray-50 hover:text-red-600 transition-colors" aria-label="@lang('pagination.previous')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-400">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold rounded-xl shadow-sm" style="background-color: #e30613 !important; color: #ffffff !important;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}#video-list" class="inline-flex items-center justify-center w-10 h-10 text-sm font-semibold bg-white border border-gray-200 rounded-xl shadow-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors" style="color: #374151;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}#video-list" rel="next" class="inline-flex items-center justify-center w-10 h-10 text-gray-700 bg-white border border-gray-200 rounded-xl shadow-xs hover:bg-gray-50 hover:text-red-600 transition-colors" aria-label="@lang('pagination.next')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-100/60 rounded-xl cursor-not-allowed" aria-disabled="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </nav>
@endif
