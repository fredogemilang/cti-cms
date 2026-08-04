@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $title = $post->getTranslation('title', $currentLocale) ?: $post->title;
    $content = $post->getTranslation('content', $currentLocale) ?: $post->content;
    $category = $post->category ? $post->category->name : 'Technology';
    $author = $post->author ? $post->author->name : 'CDT Editorial';
    $dateFormat = $dateFormat ?? \Plugins\Posts\Models\Setting::get('date_format', 'M d, Y');
    $date = $post->published_at ? $post->published_at->format($dateFormat) : $post->created_at->format($dateFormat);
    $featImg = $post->featured_image ? resolve_block_asset($post->featured_image) : null;
    $blogSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::get('archive_slug', 'blog') : 'blog';
    $blogUrl = localized_url('/' . $blogSlug);
    
    // Sidebar Recent Posts
    $recentPosts = \Plugins\Posts\Models\Post::published()
        ->where('id', '!=', $post->id)
        ->latest()
        ->take(4)
        ->get();

    // Previous & Next Posts
    $prevPost = \Plugins\Posts\Models\Post::published()
        ->where('id', '<', $post->id)
        ->latest('id')
        ->first();

    $nextPost = \Plugins\Posts\Models\Post::published()
        ->where('id', '>', $post->id)
        ->oldest('id')
        ->first();

    // Related Posts (3 items)
    $relatedPosts = \Plugins\Posts\Models\Post::published()
        ->where('id', '!=', $post->id)
        ->when($post->category_id, fn($q) => $q->where('category_id', $post->category_id))
        ->latest()
        ->take(3)
        ->get();

    if ($relatedPosts->count() < 3) {
        $relatedPosts = \Plugins\Posts\Models\Post::published()
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();
    }
@endphp

@section('content')
<!-- Article Header & Hero Section -->
<section class="pt-12 pb-10 md:pt-32 md:pb-16 bg-white relative overflow-hidden">
  <!-- Subtle Red Gradient Orbs -->
  <div class="absolute -top-10 left-0 md:left-1/4 w-[500px] h-[500px] bg-primary/8 rounded-full blur-[80px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute top-40 right-0 md:right-1/6 w-[600px] h-[600px] bg-red-500/6 rounded-full blur-[100px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute -bottom-20 left-1/3 w-[400px] h-[400px] bg-rose-500/5 rounded-full blur-[60px] pointer-events-none mix-blend-multiply"></div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    
    <!-- Breadcrumb Component (Integrated with SEO & Structured Data) -->
    <x-seo-breadcrumbs :entity="$post" class="text-zinc-400 mb-6 md:mb-10 text-left whitespace-nowrap overflow-hidden" />

    <!-- Header Meta & Title -->
    <div class="max-w-4xl mx-auto text-center">
      <div class="flex items-center justify-center gap-3 mb-6">
        <span class="px-4 py-1.5 bg-red-100 text-primary text-xs font-bold uppercase tracking-wider rounded-full">{{ $category }}</span>
        <span class="text-gray-400">•</span>
        <span class="text-sm font-medium text-gray-600">{{ $date }}</span>
      </div>
      <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6 md:mb-8 tracking-tight">
        {{ $title }}
      </h1>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 text-sm font-medium">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
            {{ substr($author, 0, 1) }}
          </div>
          <div class="text-left">
            <p class="text-gray-900 font-bold leading-tight">{{ $author }}</p>
            <p class="text-gray-500 text-xs leading-tight">CDT Editorial</p>
          </div>
        </div>
        <div class="hidden sm:block h-10 border-r border-gray-300"></div>
        <div class="text-left">
          <p class="text-gray-500 text-xs leading-tight">Published</p>
          <p class="text-gray-900 font-bold leading-tight">{{ $date }}</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Main Article Section (Split Layout) -->
<section class="py-6 md:py-12 bg-white relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
      
      <!-- Main Content Area (Left: 8 Cols) -->
      <div class="lg:col-span-8 lg:pr-8">
        
        <!-- Featured Image -->
        @if($featImg)
        <div class="relative w-full overflow-hidden rounded-2xl shadow-lg bg-zinc-900 mb-8 md:mb-10">
          <div class="relative w-full aspect-[16/9]">
            <img src="{{ $featImg }}" alt="{{ $title }}" class="w-full h-full object-cover">
          </div>
        </div>
        @endif

        <!-- Typography Rich Content -->
        <article class="prose prose-lg prose-zinc max-w-none prose-headings:font-bold prose-h2:text-3xl prose-h2:mt-12 prose-h2:mb-6 prose-h2:text-gray-900 prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-4 prose-p:text-gray-600 prose-p:leading-relaxed prose-a:text-primary hover:prose-a:text-red-800 prose-a:transition-colors prose-strong:text-gray-900 prose-img:rounded-2xl prose-img:shadow-md prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:bg-zinc-50 prose-blockquote:p-6 prose-blockquote:rounded-r-xl prose-blockquote:text-gray-700 prose-blockquote:font-medium prose-blockquote:italic">
          {!! $content !!}
        </article>

        <!-- Social Share / Author Footer -->
        <div class="mt-12 pt-8 border-t border-gray-200 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-gray-700">Share article:</span>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
              <x-icon name="lucide:linkedin" class="w-4 h-4" />
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($title) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
              <x-icon name="lucide:twitter" class="w-4 h-4" />
            </a>
          </div>

          <a href="{{ $blogUrl }}" class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
            ← Back to Blog & News
          </a>
        </div>
      </div>

      <!-- Sidebar Widget Area (Right: 4 Cols) -->
      <div class="lg:col-span-4 space-y-8 sticky top-24">
        
        <!-- Table of Contents Widget -->
        <div id="toc-widget" class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm">
          <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            Table of Contents
          </h4>
          <nav class="toc-nav">
            <ul id="toc-list" class="space-y-2.5 text-sm font-medium text-gray-500 relative border-l-2 border-zinc-200 ml-2 pl-5">
            </ul>
          </nav>
        </div>

        <!-- Share Widget -->
        <div class="bg-zinc-50 rounded-2xl border border-zinc-200 p-6 shadow-sm text-center">
          <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Share this Article</h4>
          <div class="flex justify-center gap-3">
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-gray-600 hover:bg-[#0077b5] hover:text-white hover:border-[#0077b5] transition-all shadow-sm" title="Share on LinkedIn">
              <x-icon name="lucide:linkedin" class="w-4 h-4" />
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-gray-600 hover:bg-[#1877f2] hover:text-white hover:border-[#1877f2] transition-all shadow-sm" title="Share on Facebook">
              <x-icon name="lucide:facebook" class="w-4 h-4" />
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($title) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white hover:border-black transition-all shadow-sm" title="Share on Twitter">
              <x-icon name="lucide:twitter" class="w-4 h-4" />
            </a>
            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!')" class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm" title="Copy Link">
              <x-icon name="lucide:link" class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Recent Articles Card Widget -->
        @if($recentPosts->isNotEmpty())
        <div class="bg-zinc-50 rounded-3xl p-8 border border-zinc-100">
          <h3 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200 flex items-center gap-2">
            <x-icon name="lucide:newspaper" class="w-5 h-5 text-primary" />
            Recent Articles
          </h3>
          
          <div class="space-y-6">
            @foreach($recentPosts as $rec)
              @php
                $rTitle = $rec->getTranslation('title', $currentLocale) ?: $rec->title;
                $rDate = $rec->published_at ? $rec->published_at->format($dateFormat) : $rec->created_at->format($dateFormat);
                $rImg = $rec->featured_image ? resolve_block_asset($rec->featured_image) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
              @endphp
              <div class="flex items-center gap-4 group">
                <img src="{{ $rImg }}" alt="{{ $rTitle }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0 group-hover:scale-105 transition-transform">
                <div>
                  <span class="text-[11px] text-gray-400 block mb-1 font-medium">{{ $rDate }}</span>
                  <a href="{{ $rec->getUrl() }}" class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                    {{ $rTitle }}
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Newsletter Subscription Widget -->
        <div class="bg-gradient-to-br from-primary to-zinc-900 rounded-3xl p-8 text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
          <h3 class="text-2xl font-bold mb-3 relative z-10">Stay Updated</h3>
          <p class="text-sm text-white/80 leading-relaxed mb-6 relative z-10">
            Subscribe to our newsletter for the latest tech insights and enterprise news.
          </p>
          <a href="#subscribe-modal" onclick="if(window.openModal) openModal(event)" class="inline-block w-full text-center py-3 px-6 bg-white text-primary rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors relative z-10">
            Subscribe Now
          </a>
        </div>

      </div>

    </div>

  </div>
</section>

<!-- Previous / Next Article Navigation -->
@if($prevPost || $nextPost)
<section class="py-12 border-y border-zinc-200 bg-zinc-50 relative z-10">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-6">
      
      <!-- Prev Article -->
      @if($prevPost)
        @php
          $prevTitle = $prevPost->getTranslation('title', $currentLocale) ?: $prevPost->title;
        @endphp
        <a href="{{ $prevPost->getUrl() }}" class="flex-1 flex items-center gap-6 p-6 rounded-2xl bg-white border border-zinc-200 hover:border-primary/50 hover:shadow-lg transition-all group">
          <div class="w-12 h-12 rounded-full bg-zinc-100 flex items-center justify-center text-gray-500 group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </div>
          <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Previous Article</span>
            <h4 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-2">{{ $prevTitle }}</h4>
          </div>
        </a>
      @else
        <div class="flex-1"></div>
      @endif

      <!-- Next Article -->
      @if($nextPost)
        @php
          $nextTitle = $nextPost->getTranslation('title', $currentLocale) ?: $nextPost->title;
        @endphp
        <a href="{{ $nextPost->getUrl() }}" class="flex-1 flex items-center gap-6 p-6 rounded-2xl bg-white border border-zinc-200 hover:border-primary/50 hover:shadow-lg transition-all group text-right flex-row-reverse md:flex-row md:text-right">
          <div class="w-12 h-12 rounded-full bg-zinc-100 flex items-center justify-center text-gray-500 group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
          <div class="md:ml-auto">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Next Article</span>
            <h4 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-2">{{ $nextTitle }}</h4>
          </div>
        </a>
      @else
        <div class="flex-1"></div>
      @endif

    </div>
  </div>
</section>
@endif

<!-- Related Posts -->
@if($relatedPosts->isNotEmpty())
<section class="py-12 pb-32 md:py-24 md:pb-24 bg-white relative z-10">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">You Might Also Like</h2>
      <p class="text-gray-600">Rekomendasi artikel terbaik dari pakar industri kami.</p>
    </div>

    <!-- Unified 3-Column Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($relatedPosts as $rel)
        @php
          $rTitle = $rel->getTranslation('title', $currentLocale) ?: $rel->title;
          $rExcerpt = $rel->getTranslation('excerpt', $currentLocale) ?: ($rel->excerpt ?: Str::limit(strip_tags($rel->content), 100));
          $rImg = $rel->featured_image ? resolve_block_asset($rel->featured_image) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
          $rCat = $rel->category ? $rel->category->name : 'Technology';
          $rDate = $rel->published_at ? $rel->published_at->format($dateFormat) : $rel->created_at->format($dateFormat);
        @endphp
        <div class="group flex flex-col bg-white rounded-[1.5rem] border border-zinc-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 relative">
          <div class="relative h-48 overflow-hidden z-10 bg-zinc-100">
            <img src="{{ $rImg }}" alt="{{ $rTitle }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
            <div class="absolute top-4 left-4">
              <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">{{ $rCat }}</span>
            </div>
          </div>
          <div class="p-6 md:p-8 flex-grow flex flex-col relative z-10 bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
              <a href="{{ $rel->getUrl() }}">{{ $rTitle }}</a>
            </h3>
            <p class="text-sm text-gray-600 font-light leading-relaxed mb-6 flex-grow line-clamp-2">
              {{ $rExcerpt }}
            </p>
            <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100">
              <span class="text-xs font-bold text-gray-500 uppercase">{{ $rDate }}</span>
              <a href="{{ $rel->getUrl() }}" class="text-primary hover:text-red-800 transition-colors bg-red-50 p-2 rounded-full group-hover:bg-primary group-hover:text-white">
                <svg class="w-4 h-4 transform group-hover:-rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const article = document.querySelector("article");
  const tocList = document.getElementById("toc-list");
  const tocWidget = document.getElementById("toc-widget");
  if (!article || !tocList) return;

  const headings = article.querySelectorAll("h2, h3");
  if (headings.length === 0) {
    if (tocWidget) tocWidget.style.display = "none";
    return;
  }

  headings.forEach((heading, index) => {
    if (!heading.id) {
      heading.id = "heading-" + index;
    }
    const li = document.createElement("li");
    const isH3 = heading.tagName.toLowerCase() === "h3";
    if (isH3) {
      li.className = "pl-3 text-xs text-gray-400";
    }

    const a = document.createElement("a");
    a.href = "#" + heading.id;
    a.textContent = heading.textContent;
    a.className = "toc-link hover:text-primary transition-all duration-300 block leading-snug relative " +
      (isH3 ? "" : "before:absolute before:-left-[25px] before:top-[7px] before:w-[8px] before:h-[8px] before:rounded-full before:bg-zinc-300 before:transition-all before:duration-300 before:z-10");
    
    a.addEventListener("click", function (e) {
      e.preventDefault();
      heading.scrollIntoView({ behavior: "smooth" });
    });

    li.appendChild(a);
    tocList.appendChild(li);
  });
});
</script>

@endsection
