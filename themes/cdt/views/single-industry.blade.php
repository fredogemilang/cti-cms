@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $title = $entry->getTranslation('title', $currentLocale) ?: $entry->title;
    $content = $entry->getTranslation('content', $currentLocale) ?: $entry->content;
    
    // Meta Lists
    $meta = $entry->meta ?? [];
    $infraList = $entry->getMeta('infrastructure_list') ?? $meta['infrastructure_list'] ?? [];
    $securityList = $entry->getMeta('security_list') ?? $meta['security_list'] ?? [];
    $cloudList = $entry->getMeta('cloud_list') ?? $meta['cloud_list'] ?? [];
    
    // Banner Background Image (Featured Image prioritized)
    $bannerImage = $entry->featured_image 
        ?? $entry->meta['featured_image'] 
        ?? $entry->meta['banner_image'] 
        ?? $entry->meta['hero_image'] 
        ?? null;
    $bannerImageUrl = $bannerImage ? resolve_block_asset($bannerImage) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
@endphp

@section('content')
<!-- Industry Hero Section -->
<section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <img src="{{ $bannerImageUrl }}" class="w-full h-full object-cover object-left" alt="{{ $title }}">
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
    <div class="max-w-3xl text-white">
      <!-- Breadcrumb Component (Integrated with SEO & Structured Data) -->
      <x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-10" />

      <div class="overflow-hidden mb-2">
        <p class="text-lg md:text-xl font-light text-white/90">{{ t('nav.industry', 'Industry') }}</p>
      </div>
      <div class="overflow-hidden mb-6">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight">
          {{ $title }}
        </h1>
      </div>
    </div>
  </div>
</section>

<!-- Description Section -->
<section class="py-16 md:py-24 bg-white relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl border-l-4 border-primary pl-8">
      <div class="space-y-6 text-gray-700 text-lg md:text-xl font-light leading-relaxed">
        {!! nl2br(e($content)) !!}
      </div>
    </div>
  </div>
</section>

<!-- Infrastructure Section -->
@if(!empty($infraList) && is_array($infraList))
<section class="relative py-24 bg-white text-gray-900 overflow-hidden border-t border-zinc-100">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('industry.infrastructure', 'Infrastructure') }}</h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
      @foreach($infraList as $idx => $item)
        @php
          $itemTitle = is_array($item) ? ($item['title'] ?? '') : (string)$item;
          $itemLink = is_array($item) ? ($item['link'] ?? '#') : '#';
          $itemIcon = is_array($item) ? ($item['icon'] ?? 'database') : 'database';
          if (empty($itemIcon)) $itemIcon = 'database';
          $iconName = (str_starts_with($itemIcon, 'lucide:') || str_starts_with($itemIcon, 'heroicon:')) ? $itemIcon : 'lucide:' . $itemIcon;
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-8 hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-2xl flex flex-col items-center text-center">
          <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="relative z-10 w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center text-primary mb-6 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
            @if(str_starts_with($itemIcon, '<svg') || str_starts_with($itemIcon, 'http') || str_contains($itemIcon, '/'))
              {!! render_icon($itemIcon, 'w-10 h-10') !!}
            @else
              <x-icon :name="$iconName" class="w-10 h-10" />
            @endif
          </div>
          @if(!empty($itemLink) && $itemLink !== '#')
            <a href="{{ $itemLink }}" target="_blank" rel="noopener noreferrer" class="relative z-10 text-xl font-bold text-gray-900 leading-snug hover:text-primary transition-colors">
              {{ $itemTitle }}
            </a>
          @else
            <h3 class="relative z-10 text-xl font-bold text-gray-900 leading-snug">{{ $itemTitle }}</h3>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Cloud Section -->
@if(!empty($cloudList) && is_array($cloudList))
<section class="relative py-24 bg-white text-gray-900 overflow-hidden border-t border-zinc-100">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('industry.cloud', 'Cloud') }}</h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
      @foreach($cloudList as $idx => $item)
        @php
          $itemTitle = is_array($item) ? ($item['title'] ?? '') : (string)$item;
          $itemLink = is_array($item) ? ($item['link'] ?? '#') : '#';
          $itemIcon = is_array($item) ? ($item['icon'] ?? 'cloud') : 'cloud';
          if (empty($itemIcon)) $itemIcon = 'cloud';
          $iconName = (str_starts_with($itemIcon, 'lucide:') || str_starts_with($itemIcon, 'heroicon:')) ? $itemIcon : 'lucide:' . $itemIcon;
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-8 hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-2xl flex flex-col items-center text-center">
          <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="relative z-10 w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center text-primary mb-6 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
            @if(str_starts_with($itemIcon, '<svg') || str_starts_with($itemIcon, 'http') || str_contains($itemIcon, '/'))
              {!! render_icon($itemIcon, 'w-10 h-10') !!}
            @else
              <x-icon :name="$iconName" class="w-10 h-10" />
            @endif
          </div>
          @if(!empty($itemLink) && $itemLink !== '#')
            <a href="{{ $itemLink }}" target="_blank" rel="noopener noreferrer" class="relative z-10 text-xl font-bold text-gray-900 leading-snug hover:text-primary transition-colors">
              {{ $itemTitle }}
            </a>
          @else
            <h3 class="relative z-10 text-xl font-bold text-gray-900 leading-snug">{{ $itemTitle }}</h3>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Security Section -->
@if(!empty($securityList) && is_array($securityList))
<section class="relative py-24 bg-zinc-50 text-gray-900 overflow-hidden border-t border-zinc-100">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('industry.security', 'Security') }}</h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
      @foreach($securityList as $idx => $item)
        @php
          $itemTitle = is_array($item) ? ($item['title'] ?? '') : (string)$item;
          $itemLink = is_array($item) ? ($item['link'] ?? '#') : '#';
          $itemIcon = is_array($item) ? ($item['icon'] ?? 'shield-check') : 'shield-check';
          if (empty($itemIcon)) $itemIcon = 'shield-check';
          $iconName = (str_starts_with($itemIcon, 'lucide:') || str_starts_with($itemIcon, 'heroicon:')) ? $itemIcon : 'lucide:' . $itemIcon;
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-8 hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-2xl flex flex-col items-center text-center">
          <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="relative z-10 w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center text-primary mb-6 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
            @if(str_starts_with($itemIcon, '<svg') || str_starts_with($itemIcon, 'http') || str_contains($itemIcon, '/'))
              {!! render_icon($itemIcon, 'w-10 h-10') !!}
            @else
              <x-icon :name="$iconName" class="w-10 h-10" />
            @endif
          </div>
          @if(!empty($itemLink) && $itemLink !== '#')
            <a href="{{ $itemLink }}" target="_blank" rel="noopener noreferrer" class="relative z-10 text-xl font-bold text-gray-900 leading-snug hover:text-primary transition-colors">
              {{ $itemTitle }}
            </a>
          @else
            <h3 class="relative z-10 text-xl font-bold text-gray-900 leading-snug">{{ $itemTitle }}</h3>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection
