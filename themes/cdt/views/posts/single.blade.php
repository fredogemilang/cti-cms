@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $title = $post->getTranslation('title', $currentLocale) ?: $post->title;
    $content = $post->getTranslation('content', $currentLocale) ?: $post->content;
    $category = $post->category ? $post->category->name : 'Technology';
    $author = $post->author ? $post->author->name : 'CDT Editorial';
    $date = $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y');
    $featImg = $post->featured_image ? resolve_block_asset($post->featured_image) : null;
    
    // Recent related posts
    $recentPosts = \Plugins\Posts\Models\Post::published()
        ->where('id', '!=', $post->id)
        ->latest()
        ->take(4)
        ->get();
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

          <a href="{{ localized_url('/blog') }}" class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
            ← Back to Blog & News
          </a>
        </div>
      </div>

      <!-- Sidebar Widget Area (Right: 4 Cols) -->
      <div class="lg:col-span-4 space-y-8">
        
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
                $rDate = $rec->published_at ? $rec->published_at->format('M d, Y') : $rec->created_at->format('M d, Y');
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

@endsection
