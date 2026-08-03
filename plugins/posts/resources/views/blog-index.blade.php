@if(view()->exists('cdt::posts.index'))
    @include('cdt::posts.index')
@else
    @extends('cdt::layouts.app')

    @php
        $locale = app()->getLocale();
        $archiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');
        $currentCategory = isset($category) && is_string($category) ? $category : (request()->route('category') ?: request()->query('category'));
        
        $query = \Plugins\Posts\Models\Post::where('status', 'published')->latest('published_at');
        
        if ($currentCategory) {
            $catModel = \Plugins\Posts\Models\Category::where('slug', $currentCategory)->first();
            if ($catModel) {
                $query->whereHas('categories', fn($q) => $q->where('categories.id', $catModel->id));
            }
        }

        if ($search = request()->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(12)->withQueryString();

        $featuredPosts = \Plugins\Posts\Models\Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($featuredPosts->isEmpty()) {
            $featuredPosts = \Plugins\Posts\Models\Post::where('status', 'published')
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        $allCategories = \Plugins\Posts\Models\Category::orderBy('name')->get();
        $allTags = \Plugins\Posts\Models\Tag::orderBy('name')->get();
    @endphp

    @section('title', 'Blog & News - ' . setting('site_name', 'Central Data Technology'))

    @section('content')
    <!-- Minimal Page Header & Featured Section -->
    <section class="pt-10 pb-10 md:pt-16 md:pb-16 bg-white relative overflow-hidden">
      <!-- Strong Red Gradient Orbs -->
      <div class="absolute -top-10 left-0 md:left-1/4 w-[500px] h-[500px] bg-primary/20 rounded-full blur-[80px] pointer-events-none mix-blend-multiply"></div>
      <div class="absolute top-40 right-0 md:right-1/6 w-[600px] h-[600px] bg-red-500/15 rounded-full blur-[100px] pointer-events-none mix-blend-multiply"></div>
      <div class="absolute -bottom-20 left-1/3 w-[400px] h-[400px] bg-rose-500/10 rounded-full blur-[60px] pointer-events-none mix-blend-multiply"></div>

      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-zinc-400 mb-10 text-left" aria-label="Breadcrumb" data-gsap="fade-in">
          <a href="/" class="hover:text-primary transition-colors">Home</a>
          <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <span class="text-zinc-800 font-bold" aria-current="page">Blog &amp; News</span>
        </nav>
        <div class="overflow-hidden">
          <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold text-gray-900 leading-tight" data-gsap="fade-up">
            Blog &amp; News
          </h1>
        </div>

        @if($featuredPosts->count() > 0)
        <!-- Single Featured Article (Swiper Slider - Premium Frosted Glass Overlay) -->
        <div class="swiper featured-slider mt-16 relative rounded-[2rem] overflow-hidden bg-gradient-to-br from-white to-red-50 border border-zinc-100 shadow-2xl w-full h-auto md:aspect-[16/9]" data-gsap="fade-up" data-gsap-delay="0.1">
          <div class="swiper-wrapper">
            @foreach($featuredPosts as $fPost)
            @php
                $fTitle = $fPost->getTranslation('title', $locale) ?: $fPost->title;
                $fExcerpt = $fPost->getTranslation('excerpt', $locale) ?: ($fPost->excerpt ?: Str::limit(strip_tags($fPost->content), 160));
                $fCategory = $fPost->categories->first()?->name ?? 'Technology';
                $fImage = $fPost->featured_image ? (str_starts_with($fPost->featured_image, 'http') ? $fPost->featured_image : asset('storage/' . ltrim($fPost->featured_image, '/'))) : asset('themes/cdt/assets/Strategi-IoT-Monitoring-2026-1536x864.jpg');
                $fDate = $fPost->published_at ? $fPost->published_at->format('M d, Y') : now()->format('M d, Y');
                $fAuthor = $fPost->author?->name ?? 'Admin';
                $fPostUrl = url($archiveSlug . '/' . $fPost->slug);
            @endphp
            <div class="swiper-slide relative w-full h-full flex flex-col pb-16 md:pb-0 md:block">
              <img src="{{ $fImage }}" class="relative md:absolute inset-0 w-full aspect-[16/9] md:aspect-auto md:h-full object-cover" alt="{{ $fTitle }}">
              <div class="relative md:absolute mt-4 md:mt-0 mx-4 md:mx-0 mb-0 w-[calc(100%-2rem)] md:w-auto md:max-w-xl bottom-auto md:bottom-12 right-auto md:right-12 left-auto z-20 backdrop-blur-xl bg-white/80 border border-white/40 p-6 md:p-8 rounded-3xl text-gray-900 shadow-2xl text-left">
                <div class="flex items-center gap-3 mb-4">
                  <span class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">{{ $fCategory }}</span>
                  <span class="text-gray-500 text-xs font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $fDate }}
                  </span>
                </div>
                <h3 class="text-xl md:text-2xl lg:text-3xl font-extrabold leading-tight mb-4 text-gray-900 hover:text-primary transition-colors">
                  <a href="{{ $fPostUrl }}">{{ $fTitle }}</a>
                </h3>
                <p class="text-gray-600 font-light text-xs md:text-sm lg:text-base leading-relaxed mb-6 line-clamp-3">
                  {{ $fExcerpt }}
                </p>
                <div class="flex items-center justify-between pt-4 border-t border-gray-200/50">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ strtoupper(substr($fAuthor, 0, 2)) }}
                    </div>
                    <div>
                      <p class="text-sm font-bold text-gray-900">{{ $fAuthor }}</p>
                      <p class="text-xs text-primary font-medium">Editorial Team</p>
                    </div>
                  </div>
                  <a href="{{ $fPostUrl }}" class="flex items-center gap-2 text-primary font-bold text-xs hover:text-red-800 transition-colors group/btn">
                    <span>Read More</span>
                    <span class="bg-red-50 p-2 rounded-full group-hover:bg-primary group-hover:text-white group-hover/btn:bg-primary group-hover/btn:text-white transition-colors">
                      <svg class="w-4 h-4 transform group-hover:-rotate-45 group-hover/btn:-rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                  </a>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </section>

    <!-- Blog Archive Main Section (Full Width Grid) -->
    <section class="py-12 md:py-20 bg-zinc-50 relative z-10">
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        
        <!-- Modern Horizontal Filter Bar -->
        <div class="mb-12 bg-white rounded-2xl border border-zinc-200 p-4 shadow-sm flex flex-col md:flex-row gap-6 items-center justify-between relative z-20" data-gsap="fade-up">
          <div class="w-full md:w-auto flex-1 overflow-x-auto pb-2 md:pb-0 scrollbar-hide" style="scrollbar-width: none;">
            <div class="flex items-center gap-2 min-w-max">
              <a href="{{ url($archiveSlug) }}" class="px-5 py-2 rounded-full {{ empty($currentCategory) ? 'bg-primary text-white font-bold shadow-md' : 'bg-zinc-50 text-gray-600 hover:bg-red-50 hover:text-primary font-medium border border-transparent hover:border-red-100' }} text-sm transition-all">All</a>
              @foreach($allCategories as $cat)
              @php $isActiveCat = ($currentCategory === $cat->slug); @endphp
              <a href="{{ route('posts.category', $cat->slug) }}" class="px-5 py-2 rounded-full {{ $isActiveCat ? 'bg-primary text-white font-bold shadow-md' : 'bg-zinc-50 text-gray-600 hover:bg-red-50 hover:text-primary font-medium border border-transparent hover:border-red-100' }} text-sm transition-all">
                {{ $cat->getTranslation('name', $locale) ?: $cat->name }}
              </a>
              @endforeach
            </div>
          </div>
        </div>
        
        <!-- Unified 3-Column Grid for All Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
          @foreach($posts as $index => $post)
          @php
              $pTitle = $post->getTranslation('title', $locale) ?: $post->title;
              $pExcerpt = $post->getTranslation('excerpt', $locale) ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 140));
              $pCategory = $post->categories->first()?->name ?? 'News';
              $pImage = $post->featured_image ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : asset('storage/' . ltrim($post->featured_image, '/'))) : asset('themes/cdt/assets/modern-data-architecture-1-1536x864.jpg');
              $pDate = $post->published_at ? $post->published_at->format('M d, Y') : now()->format('M d, Y');
              $pAuthor = $post->author?->name ?? 'Admin';
              $pPostUrl = url($archiveSlug . '/' . $post->slug);
              $readTime = max(1, (int) ceil(str_word_count(strip_tags($post->content)) / 200));
          @endphp
          <div class="group flex flex-col bg-white rounded-[1.5rem] border border-zinc-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 relative">
            <a href="{{ $pPostUrl }}" class="relative block h-56 overflow-hidden z-10 bg-zinc-100">
              <img src="{{ $pImage }}" alt="{{ $pTitle }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
              <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">{{ $pCategory }}</span>
              </div>
            </a>
            <div class="p-6 md:p-8 flex-grow flex flex-col relative z-10 bg-white">
              <div class="flex items-center justify-between mb-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest">
                <span>{{ $pDate }}</span>
                <span class="flex items-center gap-1"><svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $readTime }} min</span>
              </div>
              <a href="{{ $pPostUrl }}">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                  {{ $pTitle }}
                </h3>
              </a>
              <p class="text-sm md:text-base text-gray-600 font-light leading-relaxed mb-6 flex-grow line-clamp-3">
                {{ $pExcerpt }}
              </p>
              <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-bold">
                    {{ strtoupper(substr($pAuthor, 0, 2)) }}
                  </div>
                  <span class="text-sm font-medium text-gray-700">{{ $pAuthor }}</span>
                </div>
                <a href="{{ $pPostUrl }}" class="flex items-center gap-2 text-primary font-bold text-xs hover:text-red-800 transition-colors group/btn">
                  <span>Read More</span>
                  <span class="bg-red-50 p-2 rounded-full group-hover:bg-primary group-hover:text-white group-hover/btn:bg-primary group-hover/btn:text-white transition-colors">
                    <svg class="w-4 h-4 transform group-hover:-rotate-45 group-hover/btn:-rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                  </span>
                </a>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="mt-20 flex justify-center items-center gap-2">
          {{ $posts->links() }}
        </div>
      </div>
    </section>
    @endsection
@endif
