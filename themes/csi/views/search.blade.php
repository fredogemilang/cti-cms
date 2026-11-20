@extends('csi::layouts.app')

@section('content')
@php
    use Themes\Csi\Support\CsiSearchService;

    $currentLocale = $locale ?? app()->getLocale();
    $isId = $currentLocale === 'id';
    $rawQuery = trim((string) ($query ?? ''));

    $searchData = CsiSearchService::searchCategorized($rawQuery, $currentLocale);
    $solutions = $searchData['solutions'];
    $products = $searchData['products'];
    $articles = $searchData['articles'];
    $totalResults = $searchData['total'];

    $solutionsAllUrl = $isId ? localized_url('/solusi') : localized_url('/solutions');
    $productsAllUrl = $isId ? localized_url('/mitra-teknologi') : localized_url('/technology-alliances');
    $articlesAllUrl = localized_url('/blog');
    $searchFormAction = localized_url('/search');

    $searchInputPlaceholder = t('search_page.input_placeholder', $isId ? 'Cari solusi, produk, atau artikel...' : 'Search solutions, products, or articles...');
    $popularKeywords = ['Infrastructure', 'Cloud', 'Security', 'AI', 'Server', 'Storage', 'NetApp'];
@endphp

<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines & Ambient Glow (Figma 841:9177) -->
  <img src="{{ theme_asset('images/about/about-arc-right.svg') }}"
       alt=""
       class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[19px] w-[162px] h-[717px] z-0" />
  <img src="{{ theme_asset('images/about/about-arc-left.svg') }}"
       alt=""
       class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-[122px] w-[162px] h-[717px] rotate-180 z-0" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}"
       alt=""
       class="pointer-events-none select-none absolute left-1/2 -translate-x-1/2 top-[110px] w-[897px] max-w-none h-[427px] opacity-40 blur-[100px] -scale-y-100 rotate-180 z-0" />

  <!-- ========================================================================= -->
  <!-- 1. SEARCH HEADER + REFINE SEARCH BAR (Figma 844:9474)                     -->
  <!-- ========================================================================= -->
  <section id="search-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[52px] sm:pt-[60px] pb-[20px]">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 lg:gap-[48px] w-full py-[10px]">
      <div class="flex-1 min-w-0">
        @if($rawQuery !== '')
          <h1 id="search-page-title" class="text-[30px] sm:text-[38px] xl:text-[46px] font-bold leading-[1.2] text-black">
            <span>{{ t('search_page.results_for', $isId ? 'Hasil pencarian untuk' : 'Search results for') }} </span>
            <span class="text-[#ED7F22]">“{{ $rawQuery }}”</span>
          </h1>
        @else
          <h1 id="search-page-title" class="text-[30px] sm:text-[38px] xl:text-[46px] font-bold leading-[1.2] text-black">
            <span>{{ t('search_page.default_title_prefix', $isId ? 'Telusuri' : 'Search') }} </span>
            <span class="text-[#ED7F22]">{{ t('search_page.default_title_accent', $isId ? 'Solusi & Produk' : 'Solutions & Products') }}</span>
          </h1>
        @endif
      </div>

      <!-- Header Search Input Slot (Figma 844:9482) -->
      <div class="w-full lg:w-[445px] shrink-0">
        <form action="{{ $searchFormAction }}" method="GET"
              class="flex items-center gap-2 bg-white border border-[#E4EBFA] focus-within:border-[#04B1C7] rounded-[12px] p-1.5 shadow-[0_6px_24px_rgba(4,177,199,0.06)] transition-colors">
          <div class="flex items-center gap-2.5 flex-1 min-w-0 pl-3">
            <img src="{{ theme_asset('images/nav-search-icon.svg') }}" alt="" class="w-4 h-4 shrink-0 opacity-60" />
            <input type="search"
                   name="q"
                   value="{{ $rawQuery }}"
                   placeholder="{{ $searchInputPlaceholder }}"
                   aria-label="{{ t('search.submit', $isId ? 'Cari' : 'Search') }}"
                   class="w-full bg-transparent text-[14px] text-black placeholder:text-black/45 focus:outline-none py-2" />
          </div>
          <button type="submit"
                  class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-[14px] font-semibold px-[18px] py-[10px] rounded-[8px] transition-colors shrink-0 cursor-pointer">
            {{ t('search.submit', $isId ? 'Cari' : 'Search') }}
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- ========================================================================= -->
  <!-- 2. GROUPED SEARCH RESULTS (Figma 841:9244)                                -->
  <!-- ========================================================================= -->
  <section id="search-results-sections" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[24px] pb-[60px] flex flex-col gap-[56px]">

    {{-- SECTION 1: SOLUTIONS --}}
    @if($solutions->isNotEmpty())
      <div id="search-solutions-section" class="flex flex-col gap-[18px] w-full">
        <div class="flex flex-wrap items-center justify-between gap-4 w-full">
          <h2 class="text-[24px] sm:text-[30px] lg:text-[32px] font-bold leading-[1.3] text-black">
            {{ t('search_page.solutions_related', $isId ? 'Solusi terkait' : 'Solutions related to') }} “{{ $rawQuery }}”
          </h2>

          <a href="{{ $solutionsAllUrl }}"
             class="inline-flex items-center gap-1.5 text-[15px] sm:text-[16px] font-bold text-[#04B1C7] hover:text-[#2296E4] transition-colors shrink-0">
            <span>{{ t('search_page.view_all_solutions', $isId ? 'Lihat Semua Solusi' : 'View All Solutions') }}</span>
            <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4" />
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-[18px] w-full">
          @foreach($solutions as $sol)
            <div class="border-[0.5px] border-[#04B1C7] rounded-[14px] overflow-hidden flex flex-col sm:flex-row items-stretch"
                 style="background-image: linear-gradient(106.76deg, rgb(239, 253, 255) 0%, rgb(255, 255, 255) 50%, rgb(255, 246, 238) 100%);">
              <div class="sm:w-[215px] md:w-[235px] min-h-[190px] sm:min-h-[225px] shrink-0 relative bg-[#0B1726] overflow-hidden">
                @if(!empty($sol['image']))
                  <img src="{{ $sol['image'] }}"
                       alt="{{ $sol['title'] }}"
                       class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                @endif
              </div>

              <div class="flex-1 min-w-0 p-[22px] sm:py-[26px] sm:px-[26px] flex flex-col justify-between gap-[20px]">
                <div class="flex flex-col gap-[10px] items-start">
                  <span class="inline-flex items-center justify-center px-[10px] h-[21px] rounded-[6px] bg-[#F0F5FF] text-[#656F80] text-[10px] font-normal">
                    {{ $sol['badge'] }}
                  </span>

                  <h3 class="text-[20px] sm:text-[21px] font-bold leading-[28px] text-black">
                    {{ $sol['title'] }}
                  </h3>

                  @if(!empty($sol['description']))
                    <p class="text-[13px] font-normal leading-[18px] text-black line-clamp-3">
                      {{ $sol['description'] }}
                    </p>
                  @endif
                </div>

                <div>
                  <a href="{{ $sol['url'] }}"
                     class="inline-flex items-center justify-center px-[15px] py-[10px] rounded-[6px] bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-[14px] font-medium leading-[16px] transition-colors">
                    {{ t('search_page.view_solution_detail', $isId ? 'Lihat Detail Solusi' : 'View Solution Details') }}
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- SECTION 2: PRODUCTS (TECHNOLOGY ALLIANCES) --}}
    @if($products->isNotEmpty())
      <div id="search-products-section" class="flex flex-col gap-[18px] w-full">
        <div class="flex flex-wrap items-center justify-between gap-4 w-full">
          <h2 class="text-[24px] sm:text-[30px] lg:text-[32px] font-bold leading-[1.3] text-black">
            {{ t('search_page.products_related', $isId ? 'Produk terkait' : 'Products related to') }} “{{ $rawQuery }}”
          </h2>

          <a href="{{ $productsAllUrl }}"
             class="inline-flex items-center gap-1.5 text-[15px] sm:text-[16px] font-bold text-[#04B1C7] hover:text-[#2296E4] transition-colors shrink-0">
            <span>{{ t('search_page.view_all_products', $isId ? 'Lihat Semua Produk' : 'View All Products') }}</span>
            <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4" />
          </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-[18px] w-full">
          @foreach($products as $prod)
            <a href="{{ $prod['url'] }}"
               aria-label="{{ $prod['name'] }}"
               title="{{ $prod['name'] }}"
               class="group border border-[#D1DBE5] hover:border-[#04B1C7] h-[84px] rounded-[10px] px-[22px] py-[16px] flex items-center justify-center overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(4,177,199,0.12)]"
               style="background-image: linear-gradient(106.22deg, rgb(239, 253, 255) 0%, rgb(255, 255, 255) 35%, rgb(255, 255, 255) 70%, rgb(255, 247, 240) 100%);">
              @if(!empty($prod['logo']))
                <img src="{{ $prod['logo'] }}"
                     alt="{{ $prod['name'] }}"
                     class="max-h-full max-w-full w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" loading="lazy" />
              @else
                <span class="text-xs font-bold text-[#213C41] text-center">{{ $prod['name'] }}</span>
              @endif
            </a>
          @endforeach
        </div>
      </div>
    @endif

    {{-- SECTION 3: ARTICLES (Hidden automatically if empty or posts plugin disabled) --}}
    @if($articles->isNotEmpty())
      <div id="search-articles-section" class="flex flex-col gap-[18px] w-full">
        <div class="flex flex-wrap items-center justify-between gap-4 w-full">
          <h2 class="text-[24px] sm:text-[30px] lg:text-[32px] font-bold leading-[1.3] text-black">
            {{ t('search_page.articles_related', $isId ? 'Artikel terkait' : 'Articles related to') }} “{{ $rawQuery }}”
          </h2>

          <a href="{{ $articlesAllUrl }}"
             class="inline-flex items-center gap-1.5 text-[15px] sm:text-[16px] font-bold text-[#04B1C7] hover:text-[#2296E4] transition-colors shrink-0">
            <span>{{ t('search_page.view_all_articles', $isId ? 'Lihat Semua Artikel' : 'View All Articles') }}</span>
            <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4" />
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[18px] w-full">
          @foreach($articles as $article)
            <a href="{{ $article['url'] }}"
               class="group relative flex flex-col justify-between gap-[16px] p-[21px] pb-[28px] rounded-[14px] overflow-hidden border border-[#E4EBFA]/70 hover:border-[#04B1C7]/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_30px_rgba(4,177,199,0.12)]"
               style="background-image: linear-gradient(119.91deg, rgb(249, 251, 255) 1.49%, rgb(255, 244, 234) 100.11%);">
              <div class="flex flex-col gap-[16px] w-full">
                <div class="w-full h-[169px] rounded-[5px] overflow-hidden bg-slate-100">
                  @if(!empty($article['image']))
                    <img src="{{ $article['image'] }}"
                         alt="{{ $article['title'] }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                  @endif
                </div>

                <h3 class="text-[19px] sm:text-[21px] font-bold leading-[28px] text-black group-hover:text-[#04B1C7] transition-colors line-clamp-3">
                  {{ $article['title'] }}
                </h3>
              </div>

              <div class="flex flex-col gap-[10px] w-full">
                <div class="h-px w-full bg-[#ED7F22]"></div>
                <div class="flex items-center justify-end gap-1.5 py-[4px]">
                  <span class="text-[13px] font-semibold leading-[18px] text-[#ED7F22]">
                    {{ t('search_page.read_more', 'Read More') }}
                  </span>
                  <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                </div>
              </div>

              <div class="absolute bottom-0 inset-x-0 h-[10px] bg-[#04B1C7]"></div>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    {{-- EMPTY STATE (Shown only when ALL 3 sections are empty) --}}
    @if($totalResults === 0)
      <div id="search-empty-state"
           class="border-[0.5px] border-[#04B1C7] rounded-[14px] p-[28px] sm:p-[40px] text-center flex flex-col items-center gap-5"
           style="background-image: linear-gradient(116.19deg, rgb(239, 253, 255) 0%, rgb(255, 255, 255) 50%, rgb(255, 246, 238) 100%);">
        <div class="w-14 h-14 rounded-2xl bg-[#F0F5FF] border border-[#04B1C7]/30 flex items-center justify-center">
          <img src="{{ theme_asset('images/nav-search-icon.svg') }}" alt="" class="w-6 h-6 opacity-70" />
        </div>

        <div class="space-y-2 max-w-[600px]">
          <h2 class="text-[22px] sm:text-[26px] font-bold text-black">
            @if($rawQuery !== '')
              {{ t('search_page.empty_title', $isId ? 'Tidak ada hasil ditemukan untuk kata kunci tersebut' : 'No matching results found') }}
            @else
              {{ t('search_page.empty_query_title', $isId ? 'Masukkan kata kunci untuk mencari' : 'Enter a keyword to search') }}
            @endif
          </h2>
          <p class="text-sm sm:text-base text-[#656F80] leading-relaxed">
            {{ t('search_page.empty_subtitle', $isId ? 'Coba kata kunci lain seperti Infrastructure, Cloud, Security, AI, Server, Storage, atau nama vendor mitra kami.' : 'Try another keyword such as Infrastructure, Cloud, Security, AI, Server, Storage, or one of our technology partners.') }}
          </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
          <span class="text-xs font-semibold text-[#656F80] mr-1">{{ t('search_page.popular_searches', $isId ? 'Pencarian populer:' : 'Popular:') }}</span>
          @foreach($popularKeywords as $kw)
            <a href="{{ $searchFormAction }}?q={{ urlencode($kw) }}"
               class="px-3 py-1 rounded-full text-xs font-semibold bg-white border border-[#D1DBE5] hover:border-[#04B1C7] hover:text-[#04B1C7] text-[#213C41] transition-colors">
              {{ $kw }}
            </a>
          @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
          <a href="{{ $solutionsAllUrl }}"
             class="px-[18px] py-[11px] rounded-[6px] bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-sm font-semibold transition-colors">
            {{ t('search_page.view_all_solutions', $isId ? 'Lihat Semua Solusi' : 'View All Solutions') }}
          </a>
          <a href="{{ $productsAllUrl }}"
             class="px-[18px] py-[11px] rounded-[6px] bg-white border border-[#04B1C7] text-[#04B1C7] hover:bg-[#EFFDFF] text-sm font-semibold transition-colors">
            {{ t('search_page.view_all_products', $isId ? 'Lihat Semua Produk' : 'View All Products') }}
          </a>
        </div>
      </div>
    @endif
  </section>
</div>

<!-- ========================================================================= -->
<!-- 3. CONTACT CHANNELS GRADIENT STRIP                                        -->
<!-- ========================================================================= -->
@include('csi::partials.contact-strip')

<!-- ========================================================================= -->
<!-- 4. SHARED CONTACT FORM FOOTER SECTION                                     -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
