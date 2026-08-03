@php
    $activeThemeSlug = app(App\Services\ThemeLoader::class)->getActiveTheme()?->slug ?? 'cdt';
    $singleView = $activeThemeSlug . '::posts.single';
    $layoutView = view()->exists($activeThemeSlug . '::layouts.app')
        ? ($activeThemeSlug . '::layouts.app')
        : (view()->exists('cdt::layouts.app') ? 'cdt::layouts.app' : 'default::layouts.app');
@endphp

@if(view()->exists($singleView))
    @include($singleView)
@else
    @extends($layoutView)

    @php
        $locale = app()->getLocale();
        $archiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');

        $postTitle = $post->getTranslation('title', $locale) ?: $post->title;
        $rawContent = $post->getTranslation('content', $locale) ?: $post->content;
        $postExcerpt = $post->getTranslation('excerpt', $locale) ?: ($post->excerpt ?: Str::limit(strip_tags($rawContent), 160));
        $category = $post->categories->first()?->name ?? 'Technology';
        $featuredImage = $post->featured_image ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : asset('storage/' . ltrim($post->featured_image, '/'))) : null;
        $publishedDate = $post->published_at ? $post->published_at->format('F d, Y') : now()->format('F d, Y');
        $authorName = $post->author?->name ?? 'Admin';
        $readTime = max(1, (int) ceil(str_word_count(strip_tags($rawContent)) / 200));

        // Extract TOC
        $tocItems = [];
        $headingCount = 0;
        $postContent = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h[23]>/i', function ($matches) use (&$tocItems, &$headingCount) {
            $level = (int) $matches[1];
            $attributes = $matches[2];
            $titleRaw = $matches[3];
            $titleText = trim(strip_tags($titleRaw));
            
            $headingCount++;
            if (preg_match('/id=["\']([^"\']+)["\']/i', $attributes, $idMatch)) {
                $slug = $idMatch[1];
            } else {
                $slug = Str::slug($titleText) ?: ('heading-' . $headingCount);
                $attributes .= ' id="' . $slug . '"';
            }

            $tocItems[] = [
                'level' => $level,
                'slug' => $slug,
                'title' => $titleText,
            ];

            return "<h{$level}{$attributes}>{$titleRaw}</h{$level}>";
        }, $rawContent);

        $prevPost = \Plugins\Posts\Models\Post::where('status', 'published')->where('id', '<', $post->id)->latest('published_at')->first();
        $nextPost = \Plugins\Posts\Models\Post::where('status', 'published')->where('id', '>', $post->id)->oldest('published_at')->first();

        $relatedPosts = \Plugins\Posts\Models\Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();
    @endphp

    @section('title', $postTitle . ' - ' . setting('site_name', 'Central Data Technology'))

    @section('content')
    <!-- Article Header & Hero Section -->
    <section class="pt-10 pb-10 md:pt-16 md:pb-16 bg-white relative overflow-hidden">
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-zinc-400 mb-6 md:mb-10 text-left whitespace-nowrap overflow-hidden" aria-label="Breadcrumb">
          <a href="/" class="hover:text-primary transition-colors shrink-0">Home</a>
          <svg class="w-3 h-3 text-zinc-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <a href="{{ url($archiveSlug) }}" class="hover:text-primary transition-colors shrink-0">Blog &amp; News</a>
          <svg class="w-3 h-3 text-zinc-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <span class="text-zinc-800 font-bold truncate">{{ $postTitle }}</span>
        </nav>

        <div class="max-w-4xl mx-auto text-center">
          <div class="flex items-center justify-center gap-3 mb-6">
            <span class="px-4 py-1.5 bg-red-100 text-primary text-xs font-bold uppercase tracking-wider rounded-full">{{ $category }}</span>
            <span class="text-gray-400">•</span>
            <span class="text-sm font-medium text-gray-600">{{ $readTime }} Min Read</span>
          </div>
          <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6 md:mb-8 tracking-tight">
            {{ $postTitle }}
          </h1>
          <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-sm font-medium">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs shadow-sm">
                {{ strtoupper(substr($authorName, 0, 2)) }}
              </div>
              <div class="text-left">
                <p class="text-gray-900 font-bold leading-tight">{{ $authorName }}</p>
                <p class="text-gray-500 text-xs leading-tight">Author</p>
              </div>
            </div>

            <div class="hidden sm:block h-10 border-r border-gray-300"></div>

            <div class="text-left">
              <p class="text-gray-500 text-xs leading-tight">Published</p>
              <p class="text-gray-900 font-bold leading-tight">{{ $publishedDate }}</p>
            </div>

            <div class="hidden sm:block h-10 border-r border-gray-300"></div>

            <div class="text-left">
              <p class="text-gray-500 text-xs leading-tight mb-1">Share Article</p>
              <div class="flex items-center gap-2">
                <button onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}', '_blank')" class="w-7 h-7 rounded-full bg-zinc-100 flex items-center justify-center text-gray-600 hover:bg-[#0077b5] hover:text-white transition-all shadow-sm" title="Share on LinkedIn">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </button>
                <button onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}', '_blank')" class="w-7 h-7 rounded-full bg-zinc-100 flex items-center justify-center text-gray-600 hover:bg-[#1877f2] hover:text-white transition-all shadow-sm" title="Share on Facebook">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                </button>
                <button onclick="window.open('https://x.com/intent/post?text={{ urlencode($postTitle) }}&url={{ urlencode(url()->current()) }}', '_blank')" class="w-7 h-7 rounded-full bg-zinc-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-all shadow-sm" title="Share on X">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </button>
                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!');" class="w-7 h-7 rounded-full bg-zinc-100 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-all shadow-sm" title="Copy Link">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Article Section -->
    <section class="py-6 md:py-12 bg-white relative">
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          <div class="lg:col-span-8 lg:pr-8">
            @if($featuredImage)
            <div class="relative w-full overflow-hidden rounded-2xl shadow-lg bg-zinc-900 mb-8 md:mb-10">
              <div class="relative w-full pt-[56.25%]">
                <img src="{{ $featuredImage }}" alt="{{ $postTitle }}" class="absolute top-0 left-0 w-full h-full object-contain">
              </div>
            </div>
            @endif

            <article class="prose prose-lg prose-zinc max-w-none prose-headings:font-bold prose-h2:text-3xl prose-h2:mt-12 prose-h2:mb-6 prose-h2:text-gray-900 prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-4 prose-p:text-gray-600 prose-p:leading-relaxed prose-a:text-primary hover:prose-a:text-red-800 prose-a:transition-colors prose-strong:text-gray-900 prose-img:rounded-2xl prose-img:shadow-md">
              {!! $postContent !!}
            </article>
          </div>
          
          <div class="lg:col-span-4 hidden lg:block">
            <div class="sticky top-28 space-y-8">
              @if(!empty($tocItems))
              <div class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm" id="toc-widget">
                <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                  Table of Contents
                </h4>
                <nav class="toc-nav">
                  <ul class="space-y-2.5 text-sm font-medium text-gray-500 relative border-l-2 border-zinc-200 ml-2 pl-5" id="toc-list">
                    @foreach($tocItems as $item)
                    @php $isH3 = ($item['level'] === 3); @endphp
                    <li class="{{ $isH3 ? 'pl-3 text-xs text-gray-400' : '' }}">
                      <a href="#{{ $item['slug'] }}" class="toc-link hover:text-primary transition-all duration-300 block leading-snug {{ !$isH3 ? 'relative before:absolute before:-left-[25px] before:top-[7px] before:w-[8px] before:h-[8px] before:rounded-full before:bg-zinc-300 before:transition-all before:duration-300 before:z-10' : '' }}">
                        {{ $item['title'] }}
                      </a>
                    </li>
                    @endforeach
                  </ul>
                </nav>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
    @endsection
@endif
