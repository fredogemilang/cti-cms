@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    
    // Featured Posts (for Swiper hero slider - minimum 3 items)
    if (!isset($featuredPosts) || $featuredPosts->count() < 3) {
        $featuredPosts = \Plugins\Posts\Models\Post::published()
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();
            
        if ($featuredPosts->count() < 3) {
            $featuredPosts = \Plugins\Posts\Models\Post::published()
                ->latest()
                ->take(3)
                ->get();
        }
    }

    // Date format and Posts pagination from Posts Settings
    $dateFormat = $dateFormat ?? \Plugins\Posts\Models\Setting::get('date_format', 'M d, Y');
    if (!isset($posts)) {
        $perPage = (int) \Plugins\Posts\Models\Setting::get('posts_per_page', 9);
        $posts = \Plugins\Posts\Models\Post::published()->latest()->paginate($perPage);
    }
@endphp

@section('content')
<!-- Minimal Page Header & Featured Section -->
<section class="pt-32 pb-16 bg-white relative overflow-hidden">
  <!-- Strong Red Gradient Orbs -->
  <div class="absolute -top-10 left-0 md:left-1/4 w-[500px] h-[500px] bg-primary/20 rounded-full blur-[80px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute top-40 right-0 md:right-1/6 w-[600px] h-[600px] bg-red-500/15 rounded-full blur-[100px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute -bottom-20 left-1/3 w-[400px] h-[400px] bg-rose-500/10 rounded-full blur-[60px] pointer-events-none mix-blend-multiply"></div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 text-center">
    <!-- Breadcrumb Component (Integrated with SEO & Structured Data) -->
    <x-seo-breadcrumbs class="text-zinc-400 mb-10 text-left" />

    <div class="overflow-hidden">
      <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold text-gray-900 leading-tight">
        Blog & News
      </h1>
    </div>

    <!-- Featured Articles Swiper Hero Slider -->
    @if($featuredPosts->isNotEmpty())
    <div class="swiper featured-slider mt-16 relative rounded-[2rem] overflow-hidden bg-gradient-to-br from-white to-red-50 border border-zinc-100 shadow-2xl w-full h-auto md:aspect-[16/9]">
      <div class="swiper-wrapper">
        @foreach($featuredPosts as $feat)
          @php
            $featTitle = $feat->getTranslation('title', $currentLocale) ?: $feat->title;
            $featExcerpt = $feat->getTranslation('excerpt', $currentLocale) ?: ($feat->excerpt ?: Str::limit(strip_tags($feat->content), 160));
            $featImg = $feat->featured_image ? resolve_block_asset($feat->featured_image) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
            $featCategory = $feat->category ? $feat->category->name : 'Technology';
            $featAuthor = $feat->author ? $feat->author->name : 'CDT Editorial';
            $featDate = $feat->published_at ? $feat->published_at->format($dateFormat) : $feat->created_at->format($dateFormat);
          @endphp
          <div class="swiper-slide relative w-full h-full flex flex-col pb-16 md:pb-0 md:block">
            <img src="{{ $featImg }}" class="relative md:absolute inset-0 w-full aspect-[16/9] md:aspect-auto md:h-full object-cover" alt="{{ $featTitle }}">
            
            <!-- Glassmorphic Card -->
            <div class="relative md:absolute mt-4 md:mt-0 mx-4 md:mx-0 mb-0 w-[calc(100%-2rem)] md:w-auto md:max-w-xl bottom-auto md:bottom-12 right-auto md:right-12 left-auto z-20 backdrop-blur-xl bg-white/80 border border-white/40 p-6 md:p-8 rounded-3xl text-gray-900 shadow-2xl text-left">
              <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">{{ $featCategory }}</span>
                <span class="text-gray-500 text-xs font-semibold flex items-center gap-1">
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  {{ $featDate }}
                </span>
              </div>
              <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold leading-tight mb-4 text-gray-900 hover:text-primary transition-colors">
                <a href="{{ $feat->getUrl() }}">{{ $featTitle }}</a>
              </h2>
              <p class="text-gray-600 font-light text-xs md:text-sm lg:text-base leading-relaxed mb-6">
                {{ $featExcerpt }}
              </p>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200/50">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                    {{ substr($featAuthor, 0, 1) }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900">{{ $featAuthor }}</p>
                    <p class="text-xs text-primary font-medium">CDT Team</p>
                  </div>
                </div>
                <a href="{{ $feat->getUrl() }}" class="flex items-center gap-2 text-primary font-bold text-xs hover:text-red-800 transition-colors group/btn">
                  <span>Read More</span>
                  <span class="bg-red-50 p-2 rounded-full group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg class="w-4 h-4 transform group-hover:-rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                  </span>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Slider Navigation (Top Right on Mobile, Edge Chevrons on Desktop) -->
      <div class="absolute bottom-4 right-4 md:bottom-auto md:top-1/2 md:-translate-y-1/2 md:left-6 md:right-6 z-30 flex md:justify-between items-center gap-2 md:gap-0 pointer-events-none">
        <button class="swiper-button-prev-opt2 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/80 backdrop-blur-md border border-zinc-200 text-gray-800 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-white transition-all cursor-pointer shadow-lg pointer-events-auto">
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="swiper-button-next-opt2 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/80 backdrop-blur-md border border-zinc-200 text-gray-800 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-white transition-all cursor-pointer shadow-lg pointer-events-auto">
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>

      <!-- Top thin progress bar on Mobile, Bottom on Desktop -->
      <div class="swiper-pagination-opt2-progress absolute !top-0 !bottom-auto md:!top-auto md:!bottom-0 !left-0 !w-full z-30"></div>
    </div>
    @endif

  </div>
</section>

<!-- Custom Styles for Featured Slider -->
<style>
  .featured-slider .swiper-pagination-progressbar {
    background: rgba(0, 0, 0, 0.1) !important;
    height: 4px !important;
    border-radius: 99px !important;
  }
  .featured-slider .swiper-pagination-progressbar-fill {
    background: #e30613 !important;
    border-radius: 99px !important;
  }
</style>

<!-- Swiper Initialization for Featured Slider -->
<script>
  (function initFeaturedSlider() {
    if (typeof Swiper === 'undefined') {
      setTimeout(initFeaturedSlider, 100);
      return;
    }

    new Swiper('.featured-slider', {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: true,
      speed: 800,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.swiper-pagination-opt2-progress',
        type: 'progressbar',
      },
      navigation: {
        nextEl: '.swiper-button-next-opt2',
        prevEl: '.swiper-button-prev-opt2',
      },
    });
  })();
</script>

<!-- Blog Grid Section -->
<section class="py-16 md:py-24 bg-zinc-50 relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @forelse($posts as $post)
        @php
          $pTitle = $post->getTranslation('title', $currentLocale) ?: $post->title;
          $pExcerpt = $post->getTranslation('excerpt', $currentLocale) ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 120));
          $pImg = $post->featured_image ? resolve_block_asset($post->featured_image) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
          $pCategory = $post->category ? $post->category->name : 'Technology';
          $pDate = $post->published_at ? $post->published_at->format($dateFormat) : $post->created_at->format($dateFormat);
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-6 hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-2xl flex flex-col justify-between">
          <div>
            <div class="h-48 -mx-6 -mt-6 mb-6 overflow-hidden relative">
              <img src="{{ $pImg }}" alt="{{ $pTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
              <span class="absolute top-4 left-4 text-xs bg-primary text-white px-3 py-1 rounded-full font-bold uppercase tracking-wider shadow-md">
                {{ $pCategory }}
              </span>
            </div>

            <div class="text-xs text-gray-500 mb-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ $pDate }}
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 leading-snug group-hover:text-primary transition-colors">
              <a href="{{ $post->getUrl() }}">{{ $pTitle }}</a>
            </h2>

            <p class="text-sm text-gray-600 font-light leading-relaxed line-clamp-3 mb-6">
              {{ $pExcerpt }}
            </p>
          </div>

          <div class="pt-4 border-t border-zinc-100 flex items-center justify-between mt-auto">
            <a href="{{ $post->getUrl() }}" class="text-xs font-bold text-primary hover:text-red-800 transition-colors flex items-center gap-1 group/link">
              <span>Read Article</span>
              <span class="group-hover/link:translate-x-1 transition-transform">→</span>
            </a>
          </div>
        </div>
      @empty
        <div class="col-span-full text-center py-16 text-gray-500">
          No blog posts available at the moment.
        </div>
      @endforelse
    </div>

    @if(method_exists($posts, 'links'))
      <div class="mt-12">
        {{ $posts->links('cdt::partials.pagination') }}
      </div>
    @endif
  </div>
</section>

@endsection
