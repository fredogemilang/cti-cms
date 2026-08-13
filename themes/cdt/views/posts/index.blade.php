@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    
    // Categories and Tags for Filter Bar & Modal
    $categories = \Plugins\Posts\Models\Category::where('slug', '!=', 'uncategorized')->orderBy('name')->get();
    $tags = \Plugins\Posts\Models\Tag::withCount('posts')->orderBy('name')->get();

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

    $dateFormat = $dateFormat ?? \Plugins\Posts\Models\Setting::get('date_format', 'M d, Y');
    $perPage = (int) \Plugins\Posts\Models\Setting::get('posts_per_page', 9);

    // Active Filters
    $selectedCategory = request('category', $category ?? null);
    $selectedTag = request('tag');
    $searchQuery = request('q');

    // Build Posts Query
    $postsQuery = \Plugins\Posts\Models\Post::published()->with(['categories', 'author', 'tags'])->latest();

    if ($selectedCategory) {
        $postsQuery->whereHas('categories', function ($q) use ($selectedCategory) {
            $q->where('slug', $selectedCategory)
              ->orWhere('id', $selectedCategory);
        });
    }

    if ($selectedTag) {
        $postsQuery->whereHas('tags', function ($q) use ($selectedTag) {
            $q->where('slug', $selectedTag)
              ->orWhere('id', $selectedTag);
        });
    }

    if ($searchQuery) {
        $postsQuery->where(function ($q) use ($searchQuery) {
            $q->where('title', 'like', "%{$searchQuery}%")
              ->orWhere('excerpt', 'like', "%{$searchQuery}%")
              ->orWhere('content', 'like', "%{$searchQuery}%");
        });
    }

    $posts = $postsQuery->paginate($perPage)->withQueryString();
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
        {{ class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveTitle($currentLocale) : 'Blog & News' }}
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
            $featCatObj = $feat->categories->first();
            $featCategory = $featCatObj ? ($featCatObj->getTranslation('name', $currentLocale) ?: $featCatObj->name) : 'Technology';
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
                  <span>{{ t('common.read_more', 'Read More') }}</span>
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
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
@php
  $blogArchiveSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveSlug($currentLocale) : 'blog-news';
  $baseUrl = localized_url('/' . $blogArchiveSlug);
@endphp

<section 
  x-data="blogFilter({
    baseUrl: '{{ $baseUrl }}',
    category: '{{ $selectedCategory }}',
    tag: '{{ $selectedTag }}',
    search: '{{ $searchQuery }}'
  })"
  class="py-12 md:py-20 bg-zinc-50 relative z-10"
>
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    
    <!-- Modern Horizontal Filter Bar -->
    <div class="mb-12 bg-white rounded-2xl border border-zinc-200 p-4 shadow-sm flex flex-col md:flex-row gap-6 items-center justify-between relative z-20">
      
      <!-- Scrollable Category Pills -->
      <div class="w-full md:w-auto flex-1 overflow-x-auto pb-2 md:pb-0 scrollbar-hide" style="scrollbar-width: none;">
        <div class="flex items-center gap-2 min-w-max">
          <button type="button" 
            @click="setCategory('')" 
            :class="!category && !tag && !search ? 'bg-primary text-white font-bold shadow-md transform -translate-y-0.5' : 'bg-zinc-50 text-gray-600 hover:bg-red-50 hover:text-primary font-medium border border-transparent hover:border-red-100'"
            class="px-5 py-2 rounded-full text-sm transition-all cursor-pointer">
            {{ t('blog.all', 'All') }}
          </button>

          @foreach($categories as $cat)
            @php
              $catName = $cat->getTranslation('name', $currentLocale) ?: $cat->name;
              $catSlug = $cat->getTranslation('slug', $currentLocale) ?: $cat->slug;
            @endphp
            <button type="button"
              @click="setCategory('{{ $catSlug }}')"
              :class="category === '{{ $catSlug }}' || category == '{{ $cat->id }}' ? 'bg-primary text-white font-bold shadow-md' : 'bg-zinc-50 text-gray-600 hover:bg-red-50 hover:text-primary font-medium border border-transparent hover:border-red-100'"
              class="px-5 py-2 rounded-full text-sm transition-colors border cursor-pointer">
              {{ $catName }}
            </button>
          @endforeach
          
          <!-- Button for Popular Tags Modal -->
          @if(isset($tags) && $tags->isNotEmpty())
          <div class="ml-2">
            <button type="button" @click="tagsModalOpen = true" class="px-5 py-2 rounded-full bg-zinc-50 text-gray-600 hover:bg-zinc-100 text-sm font-medium transition-colors flex items-center gap-2 border border-zinc-200 cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
              {{ t('blog.popular_tags', 'Popular Tags') }}
              <span x-show="tag" class="w-2 h-2 rounded-full bg-primary inline-block"></span>
            </button>
          </div>
          @endif
        </div>
      </div>

      <!-- Command Search Bar -->
      <form @submit.prevent="fetchPosts(true)" class="w-full md:w-80 relative group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <input type="text" x-model="search" @input.debounce.400ms="fetchPosts(true)" id="blogSearchInput" placeholder="{{ t('blog.search_placeholder', 'Search articles...') }}" class="block w-full pl-11 pr-14 py-2.5 bg-zinc-50 border border-zinc-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-gray-800 placeholder-gray-400">
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
           <span class="text-[10px] text-gray-500 font-bold px-2 py-1 bg-white border border-zinc-200 rounded shadow-sm">⌘K</span>
        </div>
      </form>
    </div>

    <!-- Active Filter Indicators -->
    <div x-show="category || tag || search" x-cloak class="mb-8 flex items-center gap-3 flex-wrap text-sm text-gray-600">
      <span class="font-medium">{{ t('blog.active_filters', 'Active Filters:') }}</span>
      
      <template x-if="category">
        <button type="button" @click="setCategory('')" class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary font-semibold rounded-full text-xs hover:bg-primary/20 transition-colors cursor-pointer">
          <span>Category: <span x-text="category"></span></span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </template>

      <template x-if="tag">
        <button type="button" @click="setTag('')" class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary font-semibold rounded-full text-xs hover:bg-primary/20 transition-colors cursor-pointer">
          <span>Tag: #<span x-text="tag"></span></span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </template>

      <template x-if="search">
        <button type="button" @click="search = ''; fetchPosts(true)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary font-semibold rounded-full text-xs hover:bg-primary/20 transition-colors cursor-pointer">
          <span>Search: "<span x-text="search"></span>"</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </template>

      <button type="button" @click="resetFilters()" class="text-xs text-gray-400 hover:text-primary underline ml-2 cursor-pointer">{{ t('blog.clear_all', 'Clear All') }}</button>
    </div>

    <!-- Blog Container (Skeletons when loading, Partial Grid when loaded) -->
    <div class="relative min-h-[400px]">
      <!-- Loading Skeleton Overlay -->
      <div x-show="loading" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10 animate-pulse">
        @for($i = 0; $i < 6; $i++)
          <div class="bg-white rounded-[1.5rem] border border-zinc-200 overflow-hidden shadow-sm h-[400px] flex flex-col p-6">
            <div class="w-full h-48 bg-zinc-200 rounded-xl mb-4"></div>
            <div class="w-1/3 h-4 bg-zinc-200 rounded mb-3"></div>
            <div class="w-3/4 h-6 bg-zinc-200 rounded mb-2"></div>
            <div class="w-full h-12 bg-zinc-200 rounded mb-4"></div>
            <div class="w-1/2 h-4 bg-zinc-200 rounded mt-auto"></div>
          </div>
        @endfor
      </div>

      <!-- Main HTML Container -->
      <div x-show="!loading" id="blogGridContainer">
        @include('cdt::posts.partials.grid-partial')
      </div>
    </div>
  </div>

  <!-- Popular Tags Modal (Alpine Bottom Sheet) -->
  @if(isset($tags) && $tags->isNotEmpty())
  <template x-teleport="body">
    <div x-show="tagsModalOpen" style="display: none;">
      <!-- Backdrop -->
      <div x-show="tagsModalOpen"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="modal-sheet-backdrop fixed inset-0 z-[10003] bg-black/50 backdrop-blur-sm"
        @click="tagsModalOpen = false"></div>

      <!-- Content -->
      <div x-show="tagsModalOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
        x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
        class="modal-sheet-wrapper fixed inset-0 z-[10004] flex items-end md:items-center justify-center md:p-6"
        role="dialog" aria-modal="true">

        <div class="modal-sheet-card bg-white rounded-t-3xl md:rounded-2xl w-full md:max-w-lg shadow-2xl p-6 relative">
          <!-- Drag Handle (mobile only) -->
          <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto -mt-2 mb-4 md:hidden"></div>

          <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
              {{ t('blog.popular_tags', 'Popular Tags') }}
            </h3>
            <button type="button" @click="tagsModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <div class="modal-sheet-body">
            <p class="text-sm text-gray-500 mb-6">{{ t('blog.modal_tags_desc', 'Discover trending topics across our blog. Select a tag to filter articles.') }}</p>

            <div class="flex flex-wrap gap-3">
              @foreach($tags as $tItem)
                @php
                  $tName = $tItem->getTranslation('name', $currentLocale) ?: $tItem->name;
                  $tSlug = $tItem->getTranslation('slug', $currentLocale) ?: $tItem->slug;
                  $tCount = $tItem->posts_count ?? 0;
                @endphp
                <button type="button"
                  @click="setTag('{{ $tSlug }}')"
                  :class="tag === '{{ $tSlug }}' ? 'bg-primary text-white' : 'bg-zinc-50 text-gray-700 hover:bg-primary hover:text-white'"
                  class="px-4 py-2 text-sm font-medium rounded-xl transition-all border border-zinc-200 shadow-sm hover:shadow-md cursor-pointer">
                  #{{ $tName }} <span class="ml-1 text-xs opacity-60">({{ $tCount }})</span>
                </button>
              @endforeach
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
              <button type="button" @click="tagsModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg text-sm transition-colors cursor-pointer">{{ t('common.close', 'Close') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  @endif
</section>

<!-- Alpine.js Component Script for Blog AJAX Filtering & URL History Sync -->
<script>
  function blogFilter(config) {
    return {
      baseUrl: config.baseUrl,
      category: config.category || '',
      tag: config.tag || '',
      search: config.search || '',
      loading: false,
      tagsModalOpen: false,

      init() {
        window.addEventListener('popstate', () => {
          const path = window.location.pathname;
          const params = new URLSearchParams(window.location.search);

          const catMatch = path.match(/\/category\/([^\/]+)/);
          const tagMatch = path.match(/\/tag\/([^\/]+)/);

          this.category = catMatch ? decodeURIComponent(catMatch[1]) : (params.get('category') || '');
          this.tag = tagMatch ? decodeURIComponent(tagMatch[1]) : (params.get('tag') || '');
          this.search = params.get('q') || params.get('search') || '';

          this.fetchPosts(false);
        });

        // Delegate pagination click events inside container
        document.addEventListener('click', (e) => {
          const paginationLink = e.target.closest('#blogGridContainer .blog-pagination-nav a');
          if (paginationLink && paginationLink.href) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            const path = url.pathname;
            const params = new URLSearchParams(url.search);

            const catMatch = path.match(/\/category\/([^\/]+)/);
            const tagMatch = path.match(/\/tag\/([^\/]+)/);

            this.category = catMatch ? decodeURIComponent(catMatch[1]) : (params.get('category') || '');
            this.tag = tagMatch ? decodeURIComponent(tagMatch[1]) : (params.get('tag') || '');
            this.search = params.get('q') || params.get('search') || '';

            this.loading = true;
            window.history.pushState(null, '', url.href);
            fetch(url.href, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            })
            .then(res => res.json())
            .then(data => {
              this.loading = false;
              if (data.html) {
                const container = document.getElementById('blogGridContainer');
                if (container) container.innerHTML = data.html;
                window.scrollTo({ top: container.offsetTop - 100, behavior: 'smooth' });
              }
            })
            .catch(() => { this.loading = false; });
          }
        });
      },

      setCategory(catSlug) {
        this.category = (this.category === catSlug) ? '' : catSlug;
        if (this.category) this.tag = '';
        this.fetchPosts(true);
      },

      setTag(tagSlug) {
        this.tag = (this.tag === tagSlug) ? '' : tagSlug;
        if (this.tag) this.category = '';
        this.tagsModalOpen = false;
        this.fetchPosts(true);
      },

      resetFilters() {
        this.category = '';
        this.tag = '';
        this.search = '';
        this.fetchPosts(true);
      },

      fetchPosts(pushState = true) {
        this.loading = true;

        let targetUrl = this.baseUrl;
        if (this.category) {
          targetUrl += '/category/' + encodeURIComponent(this.category);
        } else if (this.tag) {
          targetUrl += '/tag/' + encodeURIComponent(this.tag);
        }

        const params = new URLSearchParams();
        if (this.search) {
          params.set('q', this.search);
        }

        const queryString = params.toString();
        const requestUrl = targetUrl + (queryString ? '?' + queryString : '');

        if (pushState) {
          window.history.pushState(null, '', requestUrl);
        }

        fetch(requestUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          this.loading = false;
          if (data.html) {
            const container = document.getElementById('blogGridContainer');
            if (container) {
              container.innerHTML = data.html;
            }
          }
        })
        .catch(err => {
          this.loading = false;
          console.error('Failed to fetch posts:', err);
        });
      }
    }
  }

  document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault();
      const input = document.getElementById('blogSearchInput');
      if (input) input.focus();
    }
  });
</script>

@endsection
