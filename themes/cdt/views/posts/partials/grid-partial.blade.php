@php
    $currentLocale = app()->getLocale();
    $blogArchiveSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveSlug($currentLocale) : 'blog-news';
    $baseUrl = localized_url('/' . $blogArchiveSlug);
    $dateFormat = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::get('date_format', 'M d, Y') : 'M d, Y';
@endphp

@if($posts->isNotEmpty())
<!-- Unified 3-Column Grid for All Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
  @foreach($posts as $post)
    @php
      $pTitle = $post->getTranslation('title', $currentLocale) ?: $post->title;
      $pExcerpt = $post->getTranslation('excerpt', $currentLocale) ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 120));
      $pImg = $post->featured_image ? resolve_block_asset($post->featured_image) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
      $pCatObj = $post->categories->first();
      $pCategory = $pCatObj ? ($pCatObj->getTranslation('name', $currentLocale) ?: $pCatObj->name) : 'Technology';
      $pAuthor = $post->author ? $post->author->name : 'CDT Editorial';
      $pDate = $post->published_at ? $post->published_at->format($dateFormat) : $post->created_at->format($dateFormat);
      $pReadTime = method_exists($post, 'getReadingTime') ? $post->getReadingTime($currentLocale) : 1;
    @endphp
    <div class="group flex flex-col bg-white rounded-[1.5rem] border border-zinc-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 relative">
      <a href="{{ $post->getUrl() }}" class="relative block h-56 overflow-hidden z-10 bg-zinc-100">
        <img src="{{ $pImg }}" alt="{{ $pTitle }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
        <div class="absolute top-4 left-4">
          <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">{{ $pCategory }}</span>
        </div>
      </a>
      <div class="p-6 md:p-8 flex-grow flex flex-col relative z-10 bg-white">
        <div class="flex items-center justify-between mb-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest">
          <span>{{ $pDate }}</span>
          <span class="flex items-center gap-1"><svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $pReadTime }} min</span>
        </div>
        <a href="{{ $post->getUrl() }}">
          <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
            {{ $pTitle }}
          </h3>
        </a>
        <p class="text-sm md:text-base text-gray-600 font-light leading-relaxed mb-6 flex-grow line-clamp-3">
          {{ $pExcerpt }}
        </p>
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
              {{ substr($pAuthor, 0, 1) }}
            </div>
            <span class="text-sm font-medium text-gray-700">{{ $pAuthor }}</span>
          </div>
          <a href="{{ $post->getUrl() }}" class="flex items-center gap-2 text-primary font-bold text-xs hover:text-red-800 transition-colors group/btn">
            <span>{{ t('common.read_more', 'Read More') }}</span>
            <span class="bg-red-50 p-2 rounded-full group-hover:bg-primary group-hover:text-white group-hover/btn:bg-primary group-hover/btn:text-white transition-colors">
              <svg class="w-4 h-4 transform group-hover:-rotate-45 group-hover/btn:-rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </span>
          </a>
        </div>
      </div>
    </div>
  @endforeach
</div>
@else
<!-- Empty State Section Outside Grid -->
<div class="w-full py-16 md:py-24 px-4 text-center flex flex-col items-center justify-center">
  <div class="w-full max-w-2xl mx-auto flex flex-col items-center justify-center text-center">
    
    <!-- Red Transparent Circle Icon -->
    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-6 group">
      <svg class="w-10 h-10 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>
    </div>

    <!-- Title: Larger & text-zinc-400 -->
    <h3 class="text-3xl md:text-4xl font-extrabold text-zinc-400 mb-4 tracking-tight w-full text-center">
      {{ t('blog.no_posts_title', 'Artikel Tidak Ditemukan') }}
    </h3>

    <!-- Description: Larger & text-zinc-300 -->
    <p class="text-zinc-300 font-normal text-base md:text-lg leading-relaxed mb-8 max-w-xl mx-auto text-center">
      {{ t('blog.no_posts_desc', 'Maaf, tidak ada artikel yang sesuai dengan filter atau pencarian Anda. Silakan coba kata kunci lain atau reset filter.') }}
    </p>

    <!-- Reset Filter Button -->
    <div class="w-full flex justify-center text-center">
      <button type="button" @click="resetFilters()" class="inline-flex items-center gap-2.5 bg-primary hover:bg-red-700 text-white text-xs md:text-sm font-bold py-3.5 px-8 rounded-full uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-xl shadow-primary/20 transform hover:-translate-y-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>{{ t('blog.reset_filters', 'Reset Filter') }}</span>
      </button>
    </div>

  </div>
</div>
@endif

@if(method_exists($posts, 'links'))
  <div class="mt-12 blog-pagination-nav">
    {{ $posts->links('cdt::partials.pagination') }}
  </div>
@endif
