@extends('cdt::layouts.app')

@php
    $locale = app()->getLocale();
    $title = $entry->getTranslation('title', $locale) ?? $entry->title;
    $excerpt = $entry->getTranslation('excerpt', $locale) ?? $entry->excerpt ?? '';
    $content = $entry->getTranslation('content', $locale) ?? $entry->content ?? '';
    $meta = $entry->meta ?? [];
    $translations = $meta['_translations'][$locale] ?? [];

    // CPT Meta Fields
    $industry = $translations['industry'] ?? $meta['industry'] ?? null;
    $impact = $translations['impact'] ?? $meta['impact'] ?? null;
    $quote = $translations['quote'] ?? $translations['testimonial_quote'] ?? $meta['quote'] ?? $meta['testimonial_quote'] ?? null;
    $quoteAuthor = $translations['quote_author'] ?? $translations['testimonial_author'] ?? $meta['quote_author'] ?? $meta['testimonial_author'] ?? $meta['client_name'] ?? null;
    $quoteRole = $meta['quote_role'] ?? $meta['author_role'] ?? null;

    // Client logo & banner image
    $logoUrl = $entry->featured_image ? (str_starts_with($entry->featured_image, 'http') || str_starts_with($entry->featured_image, 'themes/') ? asset($entry->featured_image) : asset('storage/' . $entry->featured_image)) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');

    // Products & Technology Used
    $relatedProductIds = $meta['related_products'] ?? $meta['products'] ?? $meta['technology_used'] ?? [];
    if (!is_array($relatedProductIds)) {
        $relatedProductIds = json_decode($relatedProductIds, true) ?? array_filter([$relatedProductIds]);
    }
    $relatedProducts = !empty($relatedProductIds)
        ? \App\Models\CptEntry::published()->whereIn('id', array_filter($relatedProductIds))->get()
        : collect();
@endphp

@section('title', $title . ' — Customer Success | ' . setting('site_name', 'Central Data Technology'))

@section('content')
<!-- Detail Hero Section -->
<section class="relative min-h-[400px] md:min-h-[500px] flex items-center py-12 md:py-0 pt-24 md:pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <x-image :src="asset('themes/cdt/assets/photo-1522071820081-009f0129c71c-w2070.jpg')" class="w-full h-full object-cover opacity-50" alt="{{ $title }}" />
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
  </div>
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">

    <!-- Breadcrumb -->
    <div class="mb-10 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
      <x-seo-breadcrumbs :entity="$entry" />
    </div>

    <div class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start">
      <!-- Client Logo -->
      <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-3xl p-6 shadow-xl flex-shrink-0 flex items-center justify-center" data-gsap="fade-up">
        <x-image :src="$logoUrl" alt="{{ $title }}" class="max-w-full max-h-full object-contain" />
      </div>

      <!-- Title & Intro -->
      <div class="max-w-4xl" data-gsap="fade-up" data-gsap-delay="0.1">
        <!-- Industry Category Badge -->
        @if(!empty($industry))
          <span class="inline-block px-3.5 py-1 bg-white/15 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full uppercase tracking-wider mb-4">
            {{ $industry }}
          </span>
        @endif

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-6 tracking-tight leading-tight">{{ $title }}</h1>
        
        @if(!empty($impact))
          <div class="p-4 md:p-6 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 text-white/95 font-semibold text-lg md:text-xl leading-relaxed mb-6">
            <span class="text-primary-light font-bold uppercase tracking-wider text-xs block mb-1 text-red-400">{{ t('cs.impact_heading', 'Key Impact') }}</span>
            {{ $impact }}
          </div>
        @endif

        @if(!empty($excerpt))
          <div class="text-lg md:text-xl text-white/90 font-light leading-relaxed">
            {!! $excerpt !!}
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Content Section -->
<section class="py-16 md:py-24 bg-white relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">

      <!-- Left Column: Main Story -->
      <div class="w-full lg:w-2/3 space-y-8 order-2 lg:order-1" data-gsap="fade-up">
        @if(!empty($content))
          <div class="prose prose-lg prose-zinc max-w-none text-gray-700 font-light leading-loose text-lg md:text-xl space-y-6 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-gray-800 [&_strong]:font-semibold [&_strong]:text-gray-900 [&_ul]:list-disc [&_ul]:pl-6">
            {!! $content !!}
          </div>
        @endif

        <!-- Client Quote Block -->
        @if(!empty($quote))
          <blockquote class="relative p-8 md:p-12 bg-zinc-50 rounded-3xl border border-zinc-100 mt-12 overflow-hidden group hover:border-primary/30 transition-colors duration-300 pl-12 md:pl-16">
            <div class="absolute top-0 left-0 w-2 h-full bg-primary"></div>
            <svg class="w-12 h-12 text-zinc-300 mb-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
            </svg>
            <p class="relative z-10 text-xl md:text-2xl font-light text-gray-800 leading-relaxed italic mb-8">
              "{!! trim($quote) !!}"
            </p>
            @if(!empty($quoteAuthor))
              <footer>
                <p class="font-bold text-gray-900 text-lg">{{ $quoteAuthor }}</p>
                @if(!empty($quoteRole))
                  <p class="text-sm text-gray-500 uppercase tracking-widest mt-1">{{ $quoteRole }}</p>
                @else
                  <p class="text-sm text-gray-500 uppercase tracking-widest mt-1">{{ t('cs.valued_customer', 'Valued Customer') }}</p>
                @endif
              </footer>
            @endif
          </blockquote>
        @endif

        <div class="pt-8 border-t border-gray-100">
          <a href="{{ localized_url('/customer-success') }}" class="inline-flex items-center text-primary font-bold text-sm tracking-wider uppercase hover:text-red-700 transition-colors">
            ← {{ t('cs.back_to_stories', 'Back to Customer Success') }}
          </a>
        </div>
      </div>

      <!-- Right Column: Sidebar (Products & Technology Used) -->
      @if($relatedProducts->isNotEmpty())
        <div class="w-full lg:w-1/3 space-y-8 lg:sticky lg:top-32 order-1 lg:order-2" data-gsap="fade-up" data-gsap-delay="0.2">
          <div class="bg-white rounded-3xl border border-zinc-200 p-8 shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300 relative overflow-hidden">
            <h4 class="text-sm font-bold tracking-widest text-zinc-400 uppercase mb-8 flex items-center gap-3 relative z-10">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              {{ t('cs.products_used', 'Products & Technology Used') }}
            </h4>

            <div class="flex flex-col gap-8 relative z-10 max-h-[60vh] overflow-y-auto overscroll-contain pr-4" style="scrollbar-width: thin;">
              @foreach($relatedProducts as $index => $prod)
                @php
                  $prodLocale = app()->getLocale();
                  $prodTitle = $prod->getTranslation('title', $prodLocale) ?? $prod->title;
                  $prodExcerpt = $prod->getTranslation('excerpt', $prodLocale) ?? $prod->excerpt ?? strip_tags($prod->getTranslation('content', $prodLocale) ?? $prod->content ?? '');
                  $prodLogoUrl = $prod->featured_image ? (str_starts_with($prod->featured_image, 'http') || str_starts_with($prod->featured_image, 'themes/') ? asset($prod->featured_image) : asset('storage/' . $prod->featured_image)) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');
                @endphp
                <div class="group/item">
                  <a href="{{ $prod->getUrl($prodLocale) }}" class="flex items-center justify-center p-6 bg-zinc-50 rounded-2xl border border-zinc-100 group-hover/item:bg-white group-hover/item:border-primary/40 group-hover/item:shadow-sm transition-all duration-300 h-28">
                    <x-image :src="$prodLogoUrl" alt="{{ $prodTitle }}" class="max-w-full max-h-full object-contain" />
                  </a>
                  <div class="mt-5">
                    <h5 class="font-bold text-gray-900 text-base mb-2">
                      <a href="{{ $prod->getUrl($prodLocale) }}" class="hover:text-primary transition-colors">{{ $prodTitle }}</a>
                    </h5>
                    @if(!empty($prodExcerpt))
                      <p class="text-sm text-gray-600 font-light leading-relaxed">
                        {{ Str::limit(strip_tags($prodExcerpt), 120) }}
                      </p>
                    @endif
                  </div>
                </div>

                @if(!$loop->last)
                  <hr class="border-zinc-100">
                @endif
              @endforeach
            </div>
          </div>
        </div>
      @endif

    </div>

  </div>
</section>

@include('cdt::partials.contact-section')
@endsection
