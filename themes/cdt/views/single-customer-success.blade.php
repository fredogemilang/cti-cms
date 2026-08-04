@extends('cdt::layouts.app')

@php
    $locale = app()->getLocale();
    $title = $entry->getTranslation('title', $locale) ?? $entry->title;
    $excerpt = $entry->getTranslation('excerpt', $locale) ?? $entry->excerpt ?? '';
    $content = $entry->getTranslation('content', $locale) ?? $entry->content ?? '';
    $meta = $entry->meta ?? [];
    $logoUrl = $entry->featured_image ? (str_starts_with($entry->featured_image, 'http') || str_starts_with($entry->featured_image, 'themes/') ? asset($entry->featured_image) : asset('storage/' . $entry->featured_image)) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');
    $outcomes = $meta['outcomes'] ?? $meta['results'] ?? $meta['key_results'] ?? null;
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
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-6 tracking-tight leading-tight">{{ $title }}</h1>
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
          <div class="prose prose-lg max-w-none text-gray-700 font-light leading-relaxed space-y-6 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-gray-800 [&_strong]:font-semibold [&_strong]:text-gray-900 [&_ul]:list-disc [&_ul]:pl-6">
            {!! $content !!}
          </div>
        @endif

        <div class="pt-8 border-t border-gray-100">
          <a href="{{ localized_url('/customer-success') }}" class="inline-flex items-center text-primary font-bold text-sm tracking-wider uppercase hover:text-red-700 transition-colors">
            ← {{ t('cs.back_to_stories', 'Back to Customer Success') }}
          </a>
        </div>
      </div>

      <!-- Right Column: Key Outcomes Sidebar -->
      @if(!empty($outcomes))
        <div class="w-full lg:w-1/3 order-1 lg:order-2" data-gsap="fade-up" data-gsap-delay="0.1">
          <div class="bg-zinc-50 border border-zinc-200 rounded-3xl p-8 lg:p-10 sticky top-28">
            <h3 class="text-xs font-bold tracking-widest text-primary uppercase mb-6">{{ t('cs.key_outcomes', 'Key Outcomes') }}</h3>
            <div class="space-y-4">
              @if(is_array($outcomes))
                @foreach($outcomes as $outcome)
                  <div class="flex items-start gap-3 text-gray-800 font-medium text-base">
                    <span class="text-primary font-bold text-lg">•</span>
                    <span>{{ is_array($outcome) ? ($outcome['text'] ?? $outcome['title'] ?? json_encode($outcome)) : $outcome }}</span>
                  </div>
                @endforeach
              @else
                @foreach(explode("\n", trim($outcomes)) as $line)
                  @if(!empty(trim($line)))
                    <div class="flex items-start gap-3 text-gray-800 font-medium text-base">
                      <span class="text-primary font-bold text-lg">•</span>
                      <span>{{ trim($line) }}</span>
                    </div>
                  @endif
                @endforeach
              @endif
            </div>
          </div>
        </div>
      @endif

    </div>

  </div>
</section>

@include('cdt::partials.contact-section')
@endsection
