@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $cptTitle = isset($cpt) ? ($cpt->getTranslation('plural_label', $currentLocale) ?: $cpt->plural_label) : t('nav.industry', 'Industry');
@endphp

@section('content')
<!-- Archive Industry Hero Section -->
<section class="relative h-[360px] md:h-[420px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <img src="{{ asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp') }}" class="w-full h-full object-cover object-left opacity-40" alt="{{ $cptTitle }}">
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
    <div class="max-w-3xl text-white">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-white/70 mb-8" aria-label="Breadcrumb">
        <a href="{{ localized_url('/') }}" class="hover:text-white transition-colors">Home</a>
        <svg class="w-3 h-3 text-white/40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-white font-bold" aria-current="page">{{ $cptTitle }}</span>
      </nav>

      <div class="overflow-hidden mb-2">
        <p class="text-lg md:text-xl font-light text-white/90">Tailored Solutions</p>
      </div>
      <div class="overflow-hidden mb-4">
        <h1 class="text-4xl md:text-5xl font-bold leading-tight">
          {{ $cptTitle }}
        </h1>
      </div>
      <p class="text-sm md:text-base text-white/80 max-w-2xl">
        Explore tailored IT solutions designed to meet the unique challenges of your specific industry.
      </p>
    </div>
  </div>
</section>

<!-- Industry Grid Section -->
<section class="py-16 md:py-24 bg-white relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @forelse($entries as $ind)
        @php
          $indTitle = $ind->getTranslation('title', $currentLocale) ?: $ind->title;
          $indContent = $ind->getTranslation('content', $currentLocale) ?: $ind->content;
          $indUrl = $ind->getUrl();
          $badgeText = $ind->getMeta('badge_text');
          $indImg = $ind->featured_image ?? $ind->meta['featured_image'] ?? null;
          $indImgUrl = $indImg ? resolve_block_asset($indImg) : null;
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-8 hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-2xl flex flex-col justify-between">
          <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          
          <div>
            @if($indImgUrl)
              <div class="h-44 -mx-8 -mt-8 mb-6 overflow-hidden relative">
                <img src="{{ $indImgUrl }}" alt="{{ $indTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                @if(!empty($badgeText))
                  <span class="absolute top-4 right-4 text-xs bg-primary text-white px-3 py-1 rounded-full font-bold uppercase tracking-wider shadow-md z-10">
                    {{ $badgeText }}
                  </span>
                @endif
              </div>
            @else
              <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-primary group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                  <x-icon name="lucide:building-2" class="w-7 h-7" />
                </div>
                @if(!empty($badgeText))
                  <span class="text-xs bg-red-100 text-primary px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                    {{ $badgeText }}
                  </span>
                @endif
              </div>
            @endif

            <h3 class="text-2xl font-bold text-gray-900 group-hover:text-primary transition-colors mb-3">
              {{ $indTitle }}
            </h3>
            
            <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed mb-6 font-light">
              {{ Str::limit(strip_tags($indContent), 140) }}
            </p>
          </div>

          <a href="{{ $indUrl }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-red-800 transition-colors group/link">
            <span>Explore {{ $indTitle }}</span>
            <span class="group-hover/link:translate-x-1 transition-transform">→</span>
          </a>
        </div>
      @empty
        <div class="col-span-full text-center py-16 text-gray-500">
          No industry entries available at the moment.
        </div>
      @endforelse
    </div>

    @if(method_exists($entries, 'links'))
      <div class="mt-12">
        {{ $entries->links() }}
      </div>
    @endif
  </div>
</section>

@endsection
