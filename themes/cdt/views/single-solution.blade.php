@extends('cdt::layouts.app', ['title' => ($entry->getTranslation('title', app()->getLocale()) ?? $entry->title) . ' Solutions — ' . setting('site_name', config('app.name', 'Central Data Technology'))])

@section('content')

@php
    $locale = app()->getLocale();
    $title = $entry->getTranslation('title', $locale) ?? $entry->title;
    $excerpt = $entry->getTranslation('excerpt', $locale) ?? $entry->excerpt;
    
    $metaDesc = trim($entry->getMeta('banner_description') ?? '');
    $excerptText = trim($excerpt ?? '');
    $contentText = trim(strip_tags($entry->getTranslation('content', $locale) ?? ($entry->content ?? '')));

    $bannerDesc = !empty($metaDesc) ? $metaDesc : (!empty($excerptText) ? $excerptText : $contentText);

    $heroImage = $entry->featured_image 
        ? asset('storage/' . $entry->featured_image) 
        : '/assets/images/unsplash/photo-1585123607190-72ec2979a269-w2070.jpg';
@endphp

<!-- Solutions Hero Section V2 -->
<section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <x-image :src="$heroImage" class="w-full h-full object-cover object-left" alt="{{ $title }}" />
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
    <div class="max-w-3xl text-white">
      <!-- Breadcrumb -->
      <div class="mb-8 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
          <x-seo-breadcrumbs :entity="$entry" />
      </div>

      <div class="overflow-hidden mb-2">
        <p class="text-lg md:text-xl font-light text-white/90">{{ t('Solutions') }}</p>
      </div>
      <div class="overflow-hidden">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight">
          {{ $title }}
        </h1>
      </div>
    </div>
  </div>
</section>

<!-- Description / Intro Section -->
@if(!empty($bannerDesc))
<section class="py-16 md:py-24 bg-white relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl border-l-4 border-primary pl-8">
      <div class="space-y-6 text-gray-700 text-lg md:text-xl font-light leading-relaxed">
        <p>{!! nl2br($bannerDesc) !!}</p>
      </div>
    </div>
  </div>
</section>
@endif

<!-- Sub-Solutions Alternating V2 -->
<section class="py-24 bg-zinc-50 relative overflow-hidden">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    @if($entry->children && $entry->children->count() > 0)
      @foreach($entry->children as $index => $child)
        @php
            $childTitle = $child->getTranslation('title', $locale) ?? $child->title;
            $childExcerpt = trim($child->getTranslation('excerpt', $locale) ?? ($child->excerpt ?? ''));
            $childMetaDesc = trim($child->getMeta('banner_description') ?? '');
            $childDesc = !empty($childExcerpt) ? $childExcerpt : $childMetaDesc;
            
            $childLoopImage = $child->getMeta('loop_image');
            $childImage = $childLoopImage 
                ? asset('storage/' . $childLoopImage) 
                : ($child->featured_image 
                    ? asset('storage/' . $child->featured_image) 
                    : '/assets/images/unsplash/photo-1504868584819-f8e8b4b6d7e3-w1000.jpg');

            $isEven = ($index % 2 === 0);
            $numFormatted = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $isLast = ($index === $entry->children->count() - 1);
        @endphp

        <div class="flex flex-col {{ $isEven ? 'lg:flex-row' : 'lg:flex-row-reverse' }} items-center gap-12 lg:gap-20 {{ $isLast ? '' : 'mb-24 lg:mb-32' }} group">
          <div class="w-full lg:w-1/2 relative">
            <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl relative">
              <x-image :src="$childImage" alt="{{ $childTitle }}" class="w-full h-full object-cover transform group-hover:scale-105 group-hover:grayscale transition-all duration-700" />
              <div
                class="absolute bottom-0 left-0 right-0 h-[40%] group-hover:h-[80%] bg-gradient-to-t from-primary/90 to-transparent mix-blend-multiply transition-all duration-700 pointer-events-none">
              </div>
            </div>
            <!-- Decorative blob -->
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl -z-10"></div>
          </div>
          <div class="w-full lg:w-1/2">
            <div class="flex items-center gap-4 mb-6">
              <span class="text-6xl font-black text-gray-200">{{ $numFormatted }}</span>
              <div class="h-px bg-gray-300 w-20"></div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">{{ $childTitle }}</h3>
            @if(!empty($childDesc))
              <p class="text-xl text-gray-600 mb-10 leading-relaxed font-light">{{ Str::limit(strip_tags($childDesc), 200) }}</p>
            @endif
            <a href="{{ $child->getUrl($locale) }}"
              class="inline-flex items-center gap-3 text-primary font-bold uppercase tracking-wider group-hover:gap-5 transition-all">
              {{ t('btn.learn_more', 'Learn More') }}
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
          </div>
        </div>
      @endforeach
    @else
      <div class="text-center py-16 bg-white rounded-3xl border border-zinc-200 shadow-sm p-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Sub-Solutions Found</h3>
        <p class="text-gray-500 max-w-md mx-auto font-light">Explore our range of technology solutions or contact our team for assistance.</p>
      </div>
    @endif

  </div>
</section>

<!-- Why with CDT V2: Light Mode with Blurred Background -->
<section class="py-24 bg-zinc-50 relative overflow-hidden">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">

    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('why.prefix', 'Why') }} <span class="font-bold text-gray-900">{{ t('why.suffix', 'with CDT?') }}</span></h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

      <!-- Feature 1 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card1_title', 'Free Consultation') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card1_desc', 'Explore the right security strategy without upfront cost.') }}</p>
      </div>

      <!-- Feature 2 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card2_title', 'Certified IT Expert') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card2_desc', 'Work with professionals backed by global certifications.') }}</p>
      </div>

      <!-- Feature 3 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 00-9 9v3.75c0 1.243 1.007 2.25 2.25 2.25H6a1.5 1.5 0 001.5-1.5V13.5A1.5 1.5 0 006 12H4.5A7.5 7.5 0 0112 4.5a7.5 7.5 0 017.5 7.5H18a1.5 1.5 0 00-1.5 1.5v3a1.5 1.5 0 001.5 1.5h.75a2.25 2.25 0 002.25-2.25V12a9 9 0 00-9-9z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 16.5a3 3 0 01-3 3h-2.25" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card3_title', 'Local Support') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card3_desc', "Reliable assistance that's always within your reach.") }}</p>
      </div>

    </div>
  </div>
</section>

<!-- Contact Form Section -->
@include('cdt::partials.contact-section')

@endsection
