@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $pageTitle = $currentLocale === 'id' ? 'Hasil Pencarian' : 'Search Results';
    $placeholder = $currentLocale === 'id' ? 'Ketik kata kunci pencarian...' : 'Type keywords to search...';
    $searchBtnText = $currentLocale === 'id' ? 'Cari' : 'Search';
    $resultsCount = $results->total();
@endphp

@section('content')
<!-- Search Hero Header -->
<section class="relative bg-[#0B0F19] text-white pt-28 pb-16 overflow-hidden border-b border-white/10">
    <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#E30613] rounded-full filter blur-[120px]"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600 rounded-full filter blur-[120px]"></div>
    </div>

    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-white/60 mb-6" aria-label="Breadcrumb">
            <a href="{{ localized_url('/') }}" class="hover:text-white transition-colors">{{ $currentLocale === 'id' ? 'Beranda' : 'Home' }}</a>
            <svg class="w-3 h-3 text-white/30" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white font-bold" aria-current="page">{{ $pageTitle }}</span>
        </nav>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-6">
            {{ $pageTitle }}
        </h1>

        <!-- Search Form -->
        <form action="{{ $currentLocale === 'id' ? url('/id/search') : url('/search') }}" method="GET" class="max-w-2xl">
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="search" 
                    name="q" 
                    value="{{ $query }}" 
                    placeholder="{{ $placeholder }}" 
                    required
                    class="w-full pl-12 pr-32 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E30613] focus:border-transparent transition-all shadow-xl"
                >
                <button 
                    type="submit" 
                    class="absolute right-2 px-6 py-2.5 bg-[#E30613] hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-md shadow-red-600/30 text-sm active:scale-95"
                >
                    {{ $searchBtnText }}
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Search Results Content -->
<section class="py-12 lg:py-16 bg-[#F8FAFC] dark:bg-[#07090E] min-h-[500px]">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        @if(!empty($query))
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-4">
                <p class="text-base text-gray-600 dark:text-gray-300">
                    @if($currentLocale === 'id')
                        Menampilkan <strong>{{ $resultsCount }}</strong> hasil untuk pencarian &ldquo;<span class="text-[#E30613] font-semibold">{{ $query }}</span>&rdquo;
                    @else
                        Showing <strong>{{ $resultsCount }}</strong> results for &ldquo;<span class="text-[#E30613] font-semibold">{{ $query }}</span>&rdquo;
                    @endif
                </p>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">
                    Page {{ $results->currentPage() }} of {{ max(1, $results->lastPage()) }}
                </span>
            </div>
        @endif

        @if($results->isNotEmpty())
            <div class="space-y-6">
                @foreach($results as $item)
                    <article class="bg-white dark:bg-[#111622] rounded-2xl p-6 sm:p-8 border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-md hover:border-red-500/30 dark:hover:border-red-500/30 transition-all group">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                {{ class_basename($item->searchable_type) }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-xs sm:max-w-md">
                                {{ $item->url }}
                            </span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white group-hover:text-[#E30613] transition-colors mb-3">
                            <a href="{{ $item->url }}" class="focus:outline-none">
                                {!! $searchService->highlight($item->title, $query, 60) !!}
                            </a>
                        </h2>

                        @php
                            $snippetSource = !empty($item->excerpt) ? $item->excerpt : $item->body;
                        @endphp
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base mb-4 line-clamp-3">
                            {!! $searchService->highlight($snippetSource ?? '', $query, 140) !!}
                        </p>

                        <div class="flex items-center text-sm font-semibold text-[#E30613]">
                            <a href="{{ $item->url }}" class="inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                                <span>{{ $currentLocale === 'id' ? 'Buka tautan' : 'Read more' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $results->links() }}
            </div>
        @elseif(!empty($query))
            <!-- No Results Found -->
            <div class="text-center py-16 bg-white dark:bg-[#111622] rounded-3xl border border-gray-100 dark:border-white/5 p-8 max-w-2xl mx-auto shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-red-50 dark:bg-red-950/40 text-[#E30613] flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $currentLocale === 'id' ? 'Tidak Ada Hasil Ditemukan' : 'No Results Found' }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    @if($currentLocale === 'id')
                        Kami tidak dapat menemukan kecocokan untuk &ldquo;<strong>{{ $query }}</strong>&rdquo;. Silakan coba dengan kata kunci lain atau periksa kembali ejaan Anda.
                    @else
                        We couldn't find any content matching &ldquo;<strong>{{ $query }}</strong>&rdquo;. Please try different keywords or check your spelling.
                    @endif
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ localized_url('/') }}" class="inline-flex items-center px-6 py-3 bg-[#E30613] hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-md shadow-red-600/20 text-sm">
                        {{ $currentLocale === 'id' ? 'Kembali ke Beranda' : 'Return Home' }}
                    </a>
                </div>
            </div>
        @else
            <!-- Initial Search Prompt -->
            <div class="text-center py-20 bg-white dark:bg-[#111622] rounded-3xl border border-gray-100 dark:border-white/5 p-8 max-w-xl mx-auto shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $currentLocale === 'id' ? 'Mulai Pencarian' : 'Start Searching' }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ $currentLocale === 'id' ? 'Masukkan kata kunci di atas untuk mencari halaman, solusi, dan artikel kami.' : 'Enter keywords above to search across our pages, solutions, and publications.' }}
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
