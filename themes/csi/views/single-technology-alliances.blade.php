@extends('csi::layouts.app')

@section('content')
@php
    $entryTitle = $entry->getTranslation('title') ?? $entry->title;
    $brandLogo = $entry->getMeta('brand_logo') ?: $entry->featured_image;
    $heroHeadline = $entry->getMeta('hero_headline', '');
    $heroDescription = $entry->getMeta('hero_description', '');
    $heroImage = $entry->getMeta('hero_image', '');

    $whyHeading = $entry->getMeta('why_heading', 'Why Choose '.$entryTitle.'?');
    $whyCards = $entry->getMeta('why_cards', []);
    if (! is_array($whyCards)) {
        $whyCards = [];
    }

    $featuresHeading = $entry->getMeta('features_heading', $entryTitle.' Key Features');
    $featuresBgImage = $entry->getMeta('features_bg_image', '');
    $featuresList = $entry->getMeta('features_list', []);
    if (! is_array($featuresList)) {
        $featuresList = [];
    }

    $converionHeading = $entry->getMeta('converion_heading', 'Make the Most of '.$entryTitle.' with Converion');
    $converionCards = $entry->getMeta('converion_cards', []);
    if (! is_array($converionCards)) {
        $converionCards = [];
    }

    $solutionsHeading = $entry->getMeta('solutions_heading', $entryTitle.' Solutions');
    $solutionsTabs = $entry->getMeta('solutions_tabs', []);
    if (! is_array($solutionsTabs)) {
        $solutionsTabs = [];
    }
    $solutionsItems = $entry->getMeta('solutions_items', []);
    if (! is_array($solutionsItems)) {
        $solutionsItems = [];
    }

    $defaultTabKey = $solutionsTabs[0]['tab_key'] ?? 'default';
@endphp

<!-- ========================================================================= -->
<!-- 1 & 2. SEAMLESS GLOW WRAPPER FOR HERO + WHY CHOOSE + CONVERION VALUE      -->
<!-- ========================================================================= -->
<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines -->
  <img id="alliance-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block absolute -left-[53px] top-[5px] w-[149px] h-[664px] rotate-180 pointer-events-none select-none z-0 will-change-transform" />
  <img id="alliance-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block absolute -right-[53px] top-[249px] w-[165px] h-[731px] pointer-events-none select-none z-0 will-change-transform" />

  <!-- Top Right-Biased Ambient Glow -->
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute left-[calc(50%+260px)] -translate-x-1/2 top-[125px] w-[796px] h-[351px] opacity-50 blur-[100px] -scale-y-100 rotate-180 pointer-events-none select-none z-0" />

  <!-- HERO SECTION (Above-the-Fold LCP Safe) -->
  <section id="alliance-hero" class="relative z-10 py-[60px] bg-transparent">
    <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-[60px] items-center">
        <!-- Left Brand Identity & Pitch -->
        <div id="alliance-hero-text" class="lg:col-span-6 flex flex-col gap-[18px] items-start text-left py-[10px]">
          @if(!empty($brandLogo))
            <div class="h-[29px] flex items-center">
              <x-image :src="$brandLogo" :alt="$entryTitle" loading="eager" sizes="180px" :placeholder="false" class="h-[29px] w-auto max-w-[180px] object-contain" />
            </div>
          @else
            <span class="text-xl font-bold text-black">{{ $entryTitle }}</span>
          @endif

          <div class="h-px w-full bg-gradient-to-r from-[#04B1C7] to-[#268BE4]"></div>

          <h1 id="alliance-hero-title" class="text-[34px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.28] pb-1 bg-gradient-to-r from-[#04B1C7] to-[#268BE4] bg-clip-text text-transparent">
            {{ $heroHeadline }}
          </h1>

          @if(!empty($heroDescription))
            <p id="alliance-hero-desc" class="text-base sm:text-[18px] leading-[26px] text-black font-normal">
              {{ $heroDescription }}
            </p>
          @endif
        </div>

        <!-- Right Visual Artwork -->
        <div id="alliance-hero-media" class="lg:col-span-6">
          <div class="w-full h-[260px] sm:h-[310px] lg:h-[331px] rounded-[14px] overflow-hidden shadow-md bg-slate-900">
            @if(!empty($heroImage))
              <x-image id="alliance-hero-img" :src="$heroImage" :alt="$entryTitle" loading="eager" fetchpriority="high" sizes="(max-width: 1024px) 100vw, 580px" class="w-full h-full object-cover rounded-[14px] will-change-transform" />
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHY CHOOSE + KEY FEATURES + MAKE THE MOST SECTION -->
  <section id="alliance-why-and-converion" class="relative z-10 pt-[30px] pb-[60px]"
           style="background: linear-gradient(180deg, rgba(255,255,255,0) 0%, #F7FAFF 100%);">
    <!-- Decorative Background Elements -->
    <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute left-1/2 -translate-x-1/2 bottom-[140px] w-[796px] h-[367px] opacity-25 blur-[100px] -scale-y-100 rotate-180 pointer-events-none select-none z-0" />
    <img id="alliance-why-halftone" src="{{ theme_asset('images/case-halftone.svg') }}" alt="" class="hidden md:block absolute -right-[220px] top-[240px] w-[522px] h-[521px] pointer-events-none select-none z-0 opacity-85 will-change-transform" />
    <img id="alliance-triangle-lines" src="{{ theme_asset('images/alliances/triangle-lines.svg') }}" alt="" class="hidden md:block absolute -left-[220px] bottom-[40px] w-[372px] h-[388px] rotate-180 pointer-events-none select-none z-0 will-change-transform" />

    <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col gap-[60px]">
      <!-- Part A: Why Choose + Key Features -->
      <div id="alliance-why" class="flex flex-col gap-[36px]">
        @if(!empty($whyHeading))
          <h2 id="alliance-why-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black text-left">
            {{ $whyHeading }}
          </h2>
        @endif

        <div class="flex flex-col lg:flex-row gap-[21px] items-stretch">
          <!-- Left 2x2 Feature Cards Grid -->
          @if(!empty($whyCards))
          <div class="w-full lg:w-[60%] max-w-[715px] grid grid-cols-1 sm:grid-cols-2 gap-[12px]">
            @foreach($whyCards as $idx => $wCard)
              @php
                $isOrange = ! empty($wCard['accent_variant']) ? strtolower($wCard['accent_variant']) === 'orange' : ($idx === 1 || $idx === 2);
                $barColor = $isOrange ? '#ED7F22' : '#04B1C7';
                $iconBg = $isOrange ? 'bg-[#ED7F22]/10' : 'bg-[#04B1C7]/10';
                $hoverShadow = $isOrange ? 'hover:shadow-[0_14px_34px_rgba(237,127,34,0.14)]' : 'hover:shadow-[0_14px_34px_rgba(4,177,199,0.14)]';
              @endphp
              <div @class([
                  'alliance-why-card min-h-[205px] rounded-[20px] p-[10px] bg-white/80 border border-[#E4EBFA] hover:-translate-y-1 transition-[box-shadow,translate] duration-300 flex',
                  $hoverShadow,
                  'sm:col-span-2' => $loop->last && count($whyCards) % 2 === 1,
              ])>
                <div class="w-full flex-1 rounded-[10px] border border-white p-[21px] pb-[26px] bg-gradient-to-b from-[#F0F5FF] to-white to-[54%] relative overflow-hidden flex flex-col justify-between gap-[18px] text-left">
                  <img src="{{ theme_asset('images/alliances/card-arc-top.svg') }}" alt="" class="absolute left-1/2 -translate-x-1/2 -top-[215px] w-[413px] max-w-none -scale-y-100 pointer-events-none select-none opacity-80" />

                  <div class="relative z-10 flex items-center gap-[18px]">
                    <div class="w-[64px] h-[64px] rounded-full bg-white p-[7px] shadow-[0_7px_16px_rgba(170,186,213,0.25)] flex items-center justify-center shrink-0">
                      <div class="w-full h-full rounded-full {{ $iconBg }} flex items-center justify-center p-2">
                        @if(!empty($wCard['icon']))
                          <x-image :src="$wCard['icon']" :alt="$wCard['title'] ?? ''" sizes="32px" :placeholder="false" class="w-[30px] h-[30px] object-contain" />
                        @endif
                      </div>
                    </div>
                    <h3 class="text-base font-bold leading-snug text-black flex-1">
                      {{ $wCard['title'] ?? '' }}
                    </h3>
                  </div>

                  <p class="relative z-10 text-[13px] font-normal leading-[18px] text-black">
                    {{ $wCard['description'] ?? '' }}
                  </p>

                  <div class="absolute bottom-0 inset-x-0 h-[7px]" style="background-color: {{ $barColor }};"></div>
                </div>
              </div>
            @endforeach
          </div>
          @endif

          <!-- Divider Line -->
          <div class="hidden lg:block w-[2px] rounded-full bg-[#E4EBFA] shrink-0"></div>

          <!-- Right Key Features Highlight Card -->
          @if(!empty($featuresList))
          <div id="alliance-features-card" class="flex-1 rounded-[20px] p-[5px] border border-white/10 flex flex-col"
               style="background: linear-gradient(90deg, #04B1C7 35%, #2296E4 70%);">
            <div class="relative flex-1 rounded-[15px] overflow-hidden p-[5px] pt-[52px] flex flex-col justify-end bg-[#07192F]">
              @if(!empty($featuresBgImage))
                <x-image :src="$featuresBgImage" alt="{{ $featuresHeading }}" sizes="(max-width: 1024px) 100vw, 460px" :placeholder="false" class="absolute inset-0 w-full h-full object-cover opacity-95 pointer-events-none" />
              @endif

              <div class="relative z-10 rounded-[10px] p-[21px] backdrop-blur-[7.5px] bg-gradient-to-b from-white from-[30%] to-white/90 flex flex-col gap-[20px] text-left">
                <div class="flex items-center justify-between gap-3">
                  <h3 class="text-[24px] sm:text-[28px] font-bold leading-tight text-black">
                    {{ $featuresHeading }}
                  </h3>
                  <img src="{{ theme_asset('images/alliances/star-badge.svg') }}" alt="" class="w-[46px] h-[46px] shrink-0" />
                </div>

                <ul class="flex flex-col gap-[10px]">
                  @foreach($featuresList as $feat)
                    <li class="flex items-center gap-[10px] text-base leading-[24px] text-black">
                      <img src="{{ theme_asset('images/alliances/feature-check.svg') }}" alt="" class="w-4 h-4 shrink-0" />
                      <span>{{ $feat['label'] ?? '' }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- Part B: Make the Most with Converion (4 Stepped Horizontal Cards) -->
      @if(!empty($converionCards))
      <div id="alliance-converion" class="flex flex-col gap-[36px]">
        @if(!empty($converionHeading))
          <h2 id="alliance-converion-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black text-left">
            {{ $converionHeading }}
          </h2>
        @endif

        @php
          $stepOffsets = count($converionCards) >= 5
            ? [
                'lg:ml-0',
                'lg:ml-[9%]',
                'lg:ml-[18%]',
                'lg:ml-[27%]',
                'lg:ml-[36%]',
              ]
            : [
                'lg:ml-0',
                'lg:ml-[14%]',
                'lg:ml-[28%]',
                'lg:ml-[42%]',
              ];
        @endphp

        <div class="flex flex-col gap-[12px] w-full">
          @foreach($converionCards as $idx => $cCard)
            @php
              $isOrange = strtolower($cCard['accent_variant'] ?? '') === 'orange' || ($idx % 2 === 1);
              $badgeColor = $isOrange ? 'bg-[#ED7F22]' : 'bg-[#04B1C7]';
              $offsetClass = $stepOffsets[$idx % count($stepOffsets)] ?? 'lg:ml-0';
            @endphp
            <div class="alliance-step-card {{ $offsetClass }} w-full lg:w-[675px] max-w-full rounded-[20px] border border-[#E4EBFA] p-[21px] flex items-center gap-[21px] text-left shadow-[0_6px_22px_rgba(145,165,197,0.08)] hover:-translate-y-0.5 transition-[box-shadow,translate] duration-300"
                 style="background: linear-gradient(133deg, #D3F5FA 0%, #FFFFFF 25%, #FFFFFF 75%, #FFEFE2 100%);">
              <div class="w-[50px] h-[50px] rounded-[10px] {{ $badgeColor }} p-[9px] shadow-[0_8px_18px_rgba(170,186,213,0.3)] flex items-center justify-center shrink-0">
                @if(!empty($cCard['icon']))
                  <x-image :src="$cCard['icon']" :alt="$cCard['title'] ?? ''" sizes="32px" :placeholder="false" class="w-full h-full object-contain" />
                @endif
              </div>

              <div class="flex flex-col gap-[5px]">
                <h3 class="text-base font-bold text-black leading-snug">
                  {{ $cCard['title'] ?? '' }}
                </h3>
                <p class="text-[13px] font-normal leading-[18px] text-black">
                  {{ $cCard['description'] ?? '' }}
                </p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </section>
</div>

<!-- ========================================================================= -->
<!-- 3. VENDOR SOLUTIONS SECTION (Interactive Category Tabs + Hardware Grid)   -->
<!-- ========================================================================= -->
@if(!empty($solutionsTabs) || !empty($solutionsItems))
<section id="alliance-solutions" class="py-[60px] relative overflow-hidden"
         style="background: linear-gradient(116deg, #D3F5FA 0%, #FFFFFF 25%, #FFFFFF 75%, #FFEFE2 100%);"
         x-data="{ activeTab: '{{ $defaultTabKey }}' }">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col gap-[21px] items-start text-left">
    @if(!empty($solutionsHeading))
      <h2 id="alliance-solutions-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black">
        {{ $solutionsHeading }}
      </h2>
    @endif

    @if(!empty($solutionsTabs))
    <div id="alliance-solutions-tabs" class="flex flex-wrap gap-[10px] items-center">
      @foreach($solutionsTabs as $tab)
        @php $tKey = $tab['tab_key'] ?? ''; @endphp
        <button type="button"
                @click="activeTab = '{{ $tKey }}'"
                :class="activeTab === '{{ $tKey }}'
                  ? 'text-black font-bold text-[14px]'
                  : 'bg-[#F0F5FF] text-[#656F80] hover:text-black font-normal text-[13px]'"
                :style="activeTab === '{{ $tKey }}'
                  ? 'background: linear-gradient(115deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%)'
                  : ''"
                class="relative rounded-[10px] border border-white px-[21px] pt-[15px] pb-[20px] overflow-hidden cursor-pointer transition-colors">
          <span>{{ $tab['tab_label'] ?? $tKey }}</span>
          <span :class="activeTab === '{{ $tKey }}' ? 'opacity-100' : 'opacity-0'"
                class="absolute bottom-0 inset-x-0 h-[5px] transition-opacity"
                style="background: linear-gradient(90deg, #04B1C7 35%, #2296E4 70%);"></span>
          <span :class="activeTab === '{{ $tKey }}' ? 'opacity-0' : 'opacity-100'"
                class="absolute bottom-0 inset-x-0 h-[5px] bg-[#D1DBE5] transition-opacity"></span>
        </button>
      @endforeach
    </div>
    @endif

    @if(!empty($solutionsItems))
    <div id="alliance-solutions-panels" class="w-full pt-1">
      @foreach($solutionsTabs as $tab)
        @php
          $tKey = $tab['tab_key'] ?? 'default';
          $matchingItems = array_values(array_filter($solutionsItems, fn ($item) => ($item['tab_key'] ?? $defaultTabKey) === $tKey));
          if (empty($matchingItems)) {
              $matchingItems = array_slice($solutionsItems, 0, 5);
          }
        @endphp
        <div x-show="activeTab === '{{ $tKey }}'"
             x-transition.opacity.duration.250ms
             class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-[15px] w-full">
          @foreach($matchingItems as $item)
            <div class="alliance-sol-item h-[120px] rounded-[10px] border border-[#04B1C7]/25 bg-white/65 p-3 flex items-center justify-center hover:border-[#04B1C7] hover:-translate-y-1 transition-[box-shadow,border-color,translate] duration-300 shadow-xs"
                 title="{{ $item['product_name'] ?? $entryTitle }}">
              @if(!empty($item['product_image']))
                <x-image :src="$item['product_image']" :alt="$item['product_name'] ?? $entryTitle" sizes="240px" :placeholder="false" class="max-h-[96px] w-auto object-contain" />
              @endif
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
    @endif
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 4. SHARED CONTACT FORM SECTION                                            -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
