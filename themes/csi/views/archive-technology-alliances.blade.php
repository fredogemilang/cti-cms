@extends('csi::layouts.app')

@section('content')
@php
    $currentLocale = app()->getLocale();
    $archiveTitle = t('alliances_archive.hero_title', $postType->getTranslation('name', $currentLocale) ?: 'Technology Alliances');
    $archiveSubtitle = t('alliances_archive.hero_subtitle', 'Collaborating with global tech giants to enhance our product capabilities and integrate cutting-edge technology into our solutions.');
    $partnersHeading = t('alliances_archive.partners_heading', 'Our Strategic Partners');
    $sortedEntries = $entries->getCollection()
        ->sortBy(
            fn ($item) => mb_strtolower((string) ($item->getTranslation('title', $currentLocale) ?? $item->title)),
            SORT_NATURAL
        )
        ->values();
@endphp

<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines & Ambient Glow (Figma 263:3246) -->
  <img id="archive-alliances-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-0 w-[162px] h-[717px] rotate-180 z-0 will-change-transform" />
  <img id="archive-alliances-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[18px] w-[162px] h-[717px] z-0 will-change-transform" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="pointer-events-none select-none absolute left-1/2 -translate-x-1/2 top-[270px] w-[1119px] max-w-none h-[566px] opacity-50 blur-[100px] -scale-y-100 rotate-180 z-0" />

  <!-- ========================================================================= -->
  <!-- 1. ARCHIVE HERO HEADER (Above-the-Fold LCP Safe)                          -->
  <!-- ========================================================================= -->
  <section id="alliances-archive-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[60px] pb-[60px]">
    <div class="flex flex-col gap-[18px] items-center text-center w-full">
      <h1 id="alliances-archive-title" class="font-bold text-[36px] sm:text-[46px] leading-[1.28] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $archiveTitle }}
      </h1>

      <p id="alliances-archive-subtitle" class="font-normal text-[16px] sm:text-[18px] leading-[26px] text-black max-w-[624px]">
        {{ $archiveSubtitle }}
      </p>
    </div>
  </section>

  <!-- ========================================================================= -->
  <!-- 2. OUR STRATEGIC PARTNERS (Dynamic Published CPT Grid)                    -->
  <!-- ========================================================================= -->
  <section id="alliances-partners-grid" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[30px] pb-[60px] flex flex-col items-center gap-[36px]">
    <h2 id="alliances-partners-heading" class="font-bold text-[28px] sm:text-[32px] leading-[42px] text-black text-center w-full">
      {{ $partnersHeading }}
    </h2>

    @if($sortedEntries->count() > 0)
      <div class="flex flex-wrap gap-[21px] items-start justify-center w-full">
        @foreach($sortedEntries as $partner)
          @php
              $partnerName = $partner->getTranslation('title', $currentLocale) ?? $partner->title;
              $partnerLogo = $partner->getMeta('brand_logo') ?: $partner->featured_image;
              $partnerUrl = $partner->getUrl($currentLocale);
          @endphp
          <a href="{{ $partnerUrl }}"
             aria-label="{{ $partnerName }}"
             title="{{ $partnerName }}"
             class="alliance-archive-card group relative border border-[#D1DBE5] border-solid h-[100px] w-[calc(50%-10.5px)] sm:w-[182.5px] rounded-[10px] px-[30px] py-[21px] flex items-center justify-center overflow-hidden transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-[#04B1C7] hover:shadow-[0_12px_28px_rgba(4,177,199,0.14)]"
             style="background-image: linear-gradient(103.14deg, rgb(239, 253, 255) 0%, rgb(255, 255, 255) 35%, rgb(255, 255, 255) 70%, rgb(255, 247, 240) 100%);">
            @if(!empty($partnerLogo))
              <div class="relative flex-1 h-full w-full flex items-center justify-center p-[6px]">
                <img src="{{ resolve_block_asset($partnerLogo) }}"
                     alt="{{ $partnerName }}"
                     class="max-h-full max-w-full w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" />
              </div>
            @else
              <span class="text-sm font-bold text-[#213C41] text-center">{{ $partnerName }}</span>
            @endif
          </a>
        @endforeach
      </div>
    @else
      <p class="text-sm text-[#656F80] text-center py-6">
        {{ t('alliances_archive.empty_notice', 'No technology alliances published yet.') }}
      </p>
    @endif
  </section>
</div>

<!-- ========================================================================= -->
<!-- 3. SHARED CONTACT FORM FOOTER SECTION                                     -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
