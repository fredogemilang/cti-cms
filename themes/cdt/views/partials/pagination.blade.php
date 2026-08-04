@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $elements = [];

        if ($lastPage <= 7) {
            $range = range(1, $lastPage);
            $elements[] = array_combine($range, array_map(fn($p) => $paginator->url($p), $range));
        } else {
            if ($currentPage <= 3) {
                $range = range(1, 5);
                $elements[] = array_combine($range, array_map(fn($p) => $paginator->url($p), $range));
                $elements[] = '...';
                $elements[] = [
                    $lastPage - 1 => $paginator->url($lastPage - 1),
                    $lastPage => $paginator->url($lastPage)
                ];
            } elseif ($currentPage >= $lastPage - 2) {
                $elements[] = [
                    1 => $paginator->url(1),
                    2 => $paginator->url(2)
                ];
                $elements[] = '...';
                $range = range($lastPage - 4, $lastPage);
                $elements[] = array_combine($range, array_map(fn($p) => $paginator->url($p), $range));
            } else {
                $elements[] = [
                    1 => $paginator->url(1),
                    2 => $paginator->url(2)
                ];
                $elements[] = '...';
                $range = range($currentPage - 1, $currentPage + 1);
                $elements[] = array_combine($range, array_map(fn($p) => $paginator->url($p), $range));
                $elements[] = '...';
                $elements[] = [
                    $lastPage - 1 => $paginator->url($lastPage - 1),
                    $lastPage => $paginator->url($lastPage)
                ];
            }
        }
    @endphp

    <div class="flex items-center justify-start sm:justify-center gap-1.5 sm:gap-2 max-w-full overflow-x-auto scrollbar-hide py-3 px-2" style="scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-zinc-100 text-zinc-300 cursor-not-allowed select-none opacity-50" aria-disabled="true">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-white border border-zinc-200 text-gray-600 hover:border-primary hover:text-primary shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all" aria-label="Previous Page">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-white border border-zinc-200 text-gray-400 font-bold text-xs sm:text-sm select-none" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-primary text-white font-bold text-xs sm:text-sm shadow-md transform hover:-translate-y-0.5 transition-all" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-white border border-zinc-200 text-gray-600 font-bold text-xs sm:text-sm hover:border-primary hover:text-primary shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-white border border-zinc-200 text-gray-600 hover:border-primary hover:text-primary shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all" aria-label="Next Page">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 flex items-center justify-center bg-zinc-100 text-zinc-300 cursor-not-allowed select-none opacity-50" aria-disabled="true">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        @endif
    </div>
@endif
