@extends('csi::layouts.app')

@section('content')
@php
    $currentLocale = app()->getLocale();
    $archiveTitle = t('solutions_archive.hero_title', $postType->getTranslation('name', $currentLocale) ?: 'Our Solutions');
    $archiveSubtitle = t('solutions_archive.hero_subtitle', 'Empowering your business with cutting-edge technology landscapes.');
    $sectionHeadline = t('solutions_archive.section_headline', 'Accelerate Your Digital Transformation with an Integrated Enterprise Ecosystem');
    $sectionDesc = t('solutions_archive.section_desc', 'A comprehensive, integrated enterprise technology portfolio designed to drive efficiency, scalability, and operational reliability.');
    $exploreLabel = t('solutions_archive.explore_button', 'Explore Solution');

    $solutionItems = $entries->getCollection()
        ->sortBy([['menu_order', 'asc'], ['id', 'asc']])
        ->values();
@endphp

<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines & Ambient Glow (Figma 192:2125) -->
  <img id="archive-solutions-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-px w-[162px] h-[717px] rotate-180 z-0 will-change-transform" />
  <img id="archive-solutions-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[19px] w-[162px] h-[717px] z-0 will-change-transform" />
  <img src="{{ theme_asset('images/solutions/halftone-graphic.svg') }}" alt="" class="hidden lg:block pointer-events-none select-none absolute -right-[80px] top-[340px] w-[420px] h-auto opacity-80 -scale-x-100 z-0" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="pointer-events-none select-none absolute left-[calc(50%+260px)] -translate-x-1/2 top-[300px] w-[793px] max-w-none h-[400px] opacity-50 blur-[100px] -scale-y-100 rotate-180 z-0" />

  <!-- ========================================================================= -->
  <!-- 1. HERO HEADER (Above-the-Fold LCP Safe)                                  -->
  <!-- ========================================================================= -->
  <section id="solutions-archive-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[52px] pb-[48px]">
    <div class="flex flex-col gap-[18px] items-center text-center w-full">
      <x-seo-breadcrumbs :entity="$postType" class="mb-2 justify-center" />

      <h1 id="solutions-archive-title" class="text-[36px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.22] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $archiveTitle }}
      </h1>

      <p id="solutions-archive-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal max-w-[720px]">
        {{ $archiveSubtitle }}
      </p>
    </div>
  </section>

  <!-- ========================================================================= -->
  <!-- 2. INTERACTIVE SOLUTIONS OVERVIEW ACCORDION (Figma 192:2138)              -->
  <!-- ========================================================================= -->
  <section id="solutions-archive-list"
           class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[24px] pb-[64px]"
           x-data="{ activeSolution: 0 }">
    <div class="flex flex-col gap-[21px] items-start w-full">
      <div class="flex flex-col gap-[16px] items-start text-left max-w-[764px]">
        <h2 id="solutions-archive-headline" class="text-[26px] sm:text-[32px] font-bold leading-[1.32] text-black">
          {{ $sectionHeadline }}
        </h2>
        <p class="text-base sm:text-[18px] font-normal leading-[26px] text-black">
          {{ $sectionDesc }}
        </p>
      </div>

      @if($solutionItems->count() > 0)
      <div class="flex flex-col lg:flex-row gap-[36px] items-start justify-between w-full pt-[10px]">
        <!-- Left Accordion -->
        <div class="w-full lg:w-[590px] shrink-0 flex flex-col">
          @foreach($solutionItems as $idx => $sol)
            @php
                $solTitle = $sol->getTranslation('title', $currentLocale) ?? $sol->title;
                $solDesc = $sol->getMeta('hero_subtitle') ?: ($sol->getMeta('subtitle') ?: ($sol->getTranslation('excerpt', $currentLocale) ?? $sol->excerpt ?? ''));
                $solCta = $sol->getMeta('cta_text') ?: $exploreLabel;
                $solUrl = $sol->getUrl($currentLocale);
            @endphp
            <div class="flex flex-col w-full">
              <div class="h-px w-full bg-[#E4EBFA]"></div>

              <button type="button"
                      @click="activeSolution = {{ $idx }}"
                      :aria-expanded="(activeSolution === {{ $idx }}).toString()"
                      class="w-full flex items-start justify-between gap-4 px-[18px] py-[18px] text-left cursor-pointer group focus:outline-none">
                <div class="flex flex-col gap-[10px] flex-1">
                  <span class="text-[16px] font-bold leading-snug text-black group-hover:text-[#04B1C7] transition-colors"
                        :class="activeSolution === {{ $idx }} ? 'text-[#04B1C7]' : ''">
                    {{ $solTitle }}
                  </span>

                  <div x-show="activeSolution === {{ $idx }}"
                       x-collapse
                       @if($idx !== 0) x-cloak @endif
                       class="flex flex-col gap-3 items-start">
                    @if($solDesc !== '')
                      <p class="text-[13px] font-normal leading-[19px] text-black">
                        {{ $solDesc }}
                      </p>
                    @endif
                    <a href="{{ $solUrl }}"
                       @click.stop
                       class="inline-flex items-center gap-1.5 text-[13px] font-bold text-[#ED7F22] hover:text-[#d86f1b] hover:underline transition-colors">
                      <span>{{ $solCta }}</span>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                      </svg>
                    </a>
                  </div>
                </div>

                <span class="shrink-0 w-[30px] h-[30px] flex items-center justify-center text-[#1E293B] transition-transform duration-300"
                      :class="activeSolution === {{ $idx }} ? 'rotate-45 text-[#04B1C7]' : 'rotate-0'">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                  </svg>
                </span>
              </button>
            </div>
          @endforeach
          <div class="h-px w-full bg-[#E4EBFA]"></div>
        </div>

        <!-- Right Visual Panel -->
        <div class="w-full lg:flex-1 lg:max-w-[574px]">
          <div class="backdrop-blur-[25px] bg-[rgba(209,219,229,0.5)] border border-white rounded-[20px] p-[14px] shadow-[0_20px_50px_rgba(148,163,184,0.18)]">
            <div class="relative w-full h-[320px] sm:h-[420px] lg:h-[465px] rounded-[12px] overflow-hidden bg-[#0B1726]">
              @foreach($solutionItems as $idx => $sol)
                @php
                    $solImg = $sol->getMeta('modules_image') ?: $sol->featured_image;
                    $solTitle = $sol->getTranslation('title', $currentLocale) ?? $sol->title;
                    $solUrl = $sol->getUrl($currentLocale);
                @endphp
                @if(!empty($solImg))
                  <a href="{{ $solUrl }}"
                     x-show="activeSolution === {{ $idx }}"
                     x-transition.opacity.duration.300ms
                     @if($idx !== 0) x-cloak @endif
                     class="block absolute inset-0 w-full h-full group/img">
                    <x-image :src="$solImg" :alt="$solTitle" sizes="(max-width: 1024px) 100vw, 560px" :placeholder="false" class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-105" />
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </section>
</div>

<!-- ========================================================================= -->
<!-- 3. SHARED CONTACT FORM SECTION                                            -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
