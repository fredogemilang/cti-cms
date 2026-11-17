@extends('csi::layouts.app')

@section('content')
@php
    use Themes\Csi\Support\CsiDataProvider;

    $heroTitle = $page->titleBlock('hero_title', [
        'prefix' => 'Converging Technology,',
        'main' => 'Connecting Business',
    ]);
    $heroSubtitle = $page->block('hero_subtitle', '');
    $heroCta = $page->buttonBlock('hero_cta', [
        'text' => 'Consult with Our Expert',
        'url' => '#contact',
        'target' => '_self',
    ]);
    $heroVideoUrl = $page->block('hero_video_url', 'https://www.youtube.com/watch?v=C3w39WEDYeg');
    $youtubeVideoId = CsiDataProvider::extractYoutubeId($heroVideoUrl, 'C3w39WEDYeg');
    $heroVideoThumbnail = $page->block('hero_video_thumbnail', '');
    $heroVideoBadgeText = $page->block('hero_video_badge_text', 'Discover Converion Solution Informatica');

    $solutionsTitle = $page->block('solutions_title', 'Comprehensive Solutions for Your Business');

    $alliancesTitle = $page->block('alliances_title', 'Technology Alliances');
    $alliancesSubtitle = $page->block('alliances_subtitle', '');
    $alliancesCta = $page->buttonBlock('alliances_cta', [
        'text' => 'Explore All Technology Alliances',
        'url' => '#all-alliances',
        'target' => '_self',
    ]);

    $whyTitle = $page->titleBlock('why_title', [
        'prefix' => 'Why Converion',
        'main' => 'We Are Here To Transform',
    ]);
    $whyDescription = $page->block('why_description', '');
    $whyCards = $page->repeaterBlock('why_cards', []);

    $insightsTitle = $page->block('insights_title', 'Real Insights from Our Clients');
    $insightsSubtitle = $page->block('insights_subtitle', '');

    $initialInsightsCat = array_key_first($insightCategories) ?? 'education';
    $initialInsightStory = $insightCategories[$initialInsightsCat][0] ?? null;
    $allianceTabs = \Themes\Csi\Support\CsiDataProvider::allianceCategoryTabs($allianceCategories);
    $initialAllianceCat = array_key_first($allianceCategories) ?? 'infrastructure';
    $initialAlliancesInfra = $allianceCategories[$initialAllianceCat] ?? [];
@endphp

<!-- ========================================================================= -->
<!-- 2. HERO / HEADER SECTION                                                  -->
<!-- ========================================================================= -->
<section class="relative min-h-[975px] pt-14 pb-20 overflow-hidden bg-white text-center">
  <img id="hero-arc-left" src="{{ theme_asset('images/hero-arc-left.svg') }}" alt="" class="absolute left-0 -translate-x-12 2xl:left-[-53px] 2xl:translate-x-0 top-[26px] w-[212px] h-[941px] pointer-events-none z-0 hidden md:block will-change-transform" />
  <img id="hero-arc-right" src="{{ theme_asset('images/hero-arc-right.svg') }}" alt="" class="absolute right-0 translate-x-12 2xl:right-0 2xl:translate-x-0 top-[-70px] w-[212px] h-[941px] pointer-events-none z-0 hidden md:block will-change-transform" />
  <img id="hero-halftone" src="{{ theme_asset('images/hero-halftone.svg') }}" alt="" class="absolute top-[376px] left-1/2 -translate-x-1/2 w-[888px] h-[887px] pointer-events-none z-0" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute top-[385px] left-1/2 -translate-x-1/2 w-[1235px] h-[624px] pointer-events-none z-0 opacity-50 blur-3xl" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center">
    <h1 id="hero-title" class="text-[44px] leading-[1.12] sm:text-5xl md:text-6xl lg:text-[64px] xl:text-[72px] font-extrabold pb-3 sm:pb-3 lg:pb-4 max-w-[1100px] text-center bg-gradient-to-r from-[#04B1C7] via-[#04B1C7] to-[#2296E4] bg-clip-text text-transparent tracking-tight">
      @if(!empty($heroTitle['prefix']))
        {{ $heroTitle['prefix'] }}<br />
      @endif
      {{ $heroTitle['main'] ?? '' }}
    </h1>

    @if(!empty($heroSubtitle))
      <p id="hero-subtitle" class="mt-4 text-base sm:text-lg text-black max-w-[860px] leading-[24px] font-normal text-center">
        {!! nl2br(e($heroSubtitle)) !!}
      </p>
    @endif

    @if(!empty($heroCta['text']))
      <div id="hero-cta-wrapper" class="mt-6 flex justify-center">
        <a href="{{ $heroCta['url'] ?? '#contact' }}" target="{{ $heroCta['target'] ?? '_self' }}" class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-base px-[21px] py-[16px] rounded-[6px] transition-colors leading-none inline-block shadow-sm">
          {{ $heroCta['text'] }}
        </a>
      </div>
    @endif

    <!-- Thumbnail Video Card -->
    <div id="hero-video-card" class="mt-10 w-full max-w-[800px] aspect-video rounded-[16px] sm:rounded-[20px] p-2 sm:p-[14px] bg-white/50 backdrop-blur-md border border-white shadow-[0_10px_35px_rgba(0,0,0,0.06)] relative overflow-hidden group cursor-pointer" role="button" tabindex="0" aria-label="Play {{ $heroVideoBadgeText }}">
      <div class="w-full h-full rounded-[8px] sm:rounded-[10px] overflow-hidden relative flex items-center justify-center bg-slate-900">
        @if($heroVideoThumbnail)
          <x-image :src="$heroVideoThumbnail" alt="{{ $heroVideoBadgeText }}" loading="eager" fetchpriority="high" sizes="(max-width: 768px) 100vw, 800px" :placeholder="false" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500" />
        @endif

        <button id="hero-play-button" type="button" class="absolute max-w-[85%] sm:max-w-none px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-full bg-black/70 backdrop-blur-sm border border-white/50 text-white flex items-center gap-2 sm:gap-2.5 shadow-lg group-hover:bg-black/85 group-hover:scale-105 transition-all cursor-pointer">
          <img src="{{ theme_asset('images/hero-play-icon.svg') }}" alt="Play" class="w-5 h-5 sm:w-7 sm:h-7 shrink-0" />
          <span class="text-xs sm:text-sm font-medium text-white tracking-wide truncate sm:whitespace-normal">{{ $heroVideoBadgeText }}</span>
        </button>
      </div>
    </div>
  </div>

  <div class="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-[#F7FAFF] to-transparent pointer-events-none"></div>
</section>

<!-- ========================================================================= -->
<!-- 3. OUR CLIENTS SECTION (INFINITE MARQUEE — CPT `client`)                  -->
<!-- ========================================================================= -->
@if($clientLogos->isNotEmpty())
<section id="clients" class="py-14 bg-gradient-to-b from-[#F7FAFF] to-white overflow-hidden text-center border-b border-slate-100">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <h2 id="clients-heading" class="text-lg sm:text-[21px] font-bold text-slate-900 tracking-wider uppercase">
      {{ t('clients.heading_prefix', 'TRUSTED BY') }}
      <span class="text-[#04B1C7]">{{ t('clients.heading_highlight', '4,500+') }}</span>
      {{ t('clients.heading_suffix', 'ENTERPRISES & LEADING ORGANIZATIONS ACROSS INDONESIA') }}
    </h2>

    <div id="clients-marquee-wrap" class="relative w-full max-w-[1200px] mx-auto mt-10 overflow-hidden">
      <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
      <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

      <div class="animate-marquee flex items-center gap-12 py-3">
        @for($loopSet = 0; $loopSet < 2; $loopSet++)
          @foreach($clientLogos as $client)
            @if(!empty($client['website_url']))
              <a href="{{ $client['website_url'] }}" target="_blank" rel="noopener noreferrer" class="shrink-0">
                <x-image :src="$client['logo']" :alt="$client['name']" sizes="140px" loading="eager" :placeholder="false" class="h-11 w-auto max-w-[140px] object-contain opacity-75 hover:opacity-100 transition-opacity" />
              </a>
            @else
              <x-image :src="$client['logo']" :alt="$client['name']" sizes="140px" loading="eager" :placeholder="false" class="h-11 w-auto max-w-[140px] object-contain opacity-75 hover:opacity-100 transition-opacity shrink-0" />
            @endif
          @endforeach
        @endfor
      </div>
    </div>
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 4. COMPREHENSIVE SOLUTIONS SECTION                                        -->
<!-- ========================================================================= -->
<section id="solutions" class="py-24 bg-white relative overflow-hidden">
  <img src="{{ theme_asset('images/solutions-bg-grid.svg') }}" alt="" class="absolute top-[80px] -left-[2%] w-[104%] h-[465px] object-fill pointer-events-none opacity-85 z-0" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] text-center">
    <h2 id="solutions-heading" class="text-3xl sm:text-[32px] font-bold text-slate-900 leading-tight">
      {{ $solutionsTitle }}
    </h2>

    <div class="mt-14 max-w-[900px] mx-auto flex flex-wrap justify-center gap-[21px]">
      @foreach($solutionsList as $sol)
        @php
          $isOrange = ($sol['variant'] ?? 'cyan') === 'orange';
          $spotColor = $isOrange ? 'rgba(237, 127, 34, 0.22)' : 'rgba(4, 177, 199, 0.22)';
          $spotBorder = $isOrange ? 'rgba(237, 127, 34, 0.55)' : 'rgba(4, 177, 199, 0.55)';
          $barColor = $isOrange ? 'bg-[#ED7F22]' : 'bg-[#04B1C7]';
          $hoverShadow = $isOrange ? 'hover:shadow-[0_14px_36px_rgba(237,127,34,0.14)]' : 'hover:shadow-[0_14px_36px_rgba(4,177,199,0.14)]';
          $ringBorder = $isOrange ? 'border-[#04B1C7]/25' : 'border-[#ED7F22]/25';
        @endphp
        <a href="{{ $sol['url'] }}" class="solution-card w-full max-w-[800px] md:w-[285px] rounded-[14px] p-[21px] bg-gradient-to-br from-[#F9FBFF] to-[#FFF4EA] border border-white/80 shadow-[0_4px_20px_rgba(0,0,0,0.03)] {{ $hoverShadow }} hover:-translate-y-1 transition-[box-shadow,border-color,translate] duration-300 relative overflow-hidden group cursor-pointer text-left flex flex-col justify-between"
           style="--spotlight-color: {{ $spotColor }}; --spotlight-border: {{ $spotBorder }};">
          <div class="absolute top-0 inset-x-0 h-[10px] {{ $barColor }} z-20"></div>
          <div class="pointer-events-none absolute inset-0 rounded-[14px] opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"
               style="background: radial-gradient(280px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), var(--spotlight-color), transparent 75%);"></div>
          <div class="pointer-events-none absolute -inset-[1px] rounded-[14px] border-2 border-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20"
               style="border-color: var(--spotlight-border); mask: radial-gradient(220px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), black 0%, transparent 100%); -webkit-mask: radial-gradient(220px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), black 0%, transparent 100%);"></div>

          <div class="relative z-30">
            <div class="w-[60px] h-[60px] rounded-full p-[7px] bg-white/50 border {{ $ringBorder }} flex items-center justify-center mb-3">
              <div class="w-full h-full rounded-full p-1 bg-gradient-to-b from-white via-white to-[#F0F5FF] border border-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <img src="{{ $sol['icon'] }}" alt="{{ $sol['title'] }}" class="{{ $sol['icon_class'] ?? 'w-[30px] h-[30px]' }} object-contain" />
              </div>
            </div>

            <h3 class="text-[16px] font-bold text-black leading-snug">{{ $sol['title'] }}</h3>
            <p class="text-[13px] text-black/90 font-normal leading-[18px] mt-1">{{ $sol['subtitle'] }}</p>
          </div>

          <div class="relative z-30 mt-4 pt-1 flex items-center gap-1.5 text-[13px] font-semibold text-[#ED7F22] opacity-100 max-h-12 md:opacity-0 md:max-h-0 md:group-hover:opacity-100 md:group-hover:max-h-12 overflow-hidden transition-all duration-300 ease-out">
            <span>{{ $sol['cta'] }}</span>
            <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 5. TECHNOLOGY ALLIANCES SECTION                                           -->
<!-- ========================================================================= -->
<section id="alliances" class="py-20 bg-white relative overflow-hidden text-center">
  <div class="pointer-events-none absolute left-1/2 top-[60%] -translate-x-1/2 -translate-y-1/2 w-[1480px] max-w-none h-[740px] z-0 overflow-visible">
    <img src="{{ theme_asset('images/alliances-ambient-glow.png') }}" alt="" class="w-full h-full object-contain filter blur-[70px] opacity-50" />
  </div>

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <h2 id="alliances-heading" class="text-3xl sm:text-[32px] font-bold text-black leading-tight">
      {{ $alliancesTitle }}
    </h2>
    @if(!empty($alliancesSubtitle))
      <p id="alliances-desc" class="mt-4 text-base text-[#475467] max-w-[786px] mx-auto leading-relaxed">
        {{ $alliancesSubtitle }}
      </p>
    @endif

    @if(!empty($alliancesCta['text']))
      @php
        $rawAlliancesUrl = $alliancesCta['url'] ?? '/technology-alliances';
        if ($rawAlliancesUrl === '#all-alliances') {
            $rawAlliancesUrl = '/technology-alliances';
        }
        $resolvedAlliancesUrl = str_starts_with($rawAlliancesUrl, '/') && ! str_starts_with($rawAlliancesUrl, '//')
            ? (str_starts_with($rawAlliancesUrl, '/#') ? localized_url('/').substr($rawAlliancesUrl, 1) : localized_url($rawAlliancesUrl))
            : $rawAlliancesUrl;
      @endphp
      <div class="mt-6">
        <a id="alliances-cta" href="{{ $resolvedAlliancesUrl }}" target="{{ $alliancesCta['target'] ?? '_self' }}" class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-sm px-6 py-2.5 rounded-[6px] shadow-sm transition-all inline-block">
          {{ $alliancesCta['text'] }}
        </a>
      </div>
    @endif

    <!-- Category Filter Tabs (Dynamic from published Technology Alliances CPT entries) -->
    @if(count($allianceTabs) > 0)
      <div id="alliances-tabs" class="mt-8 flex flex-wrap justify-center items-center gap-2 relative z-20">
        @foreach($allianceTabs as $catKey => $catLabel)
          <button
            type="button"
            data-category="{{ $catKey }}"
            class="alliance-tab px-4 py-1.5 rounded-[6px] text-xs transition-all cursor-pointer {{ $catKey === $initialAllianceCat ? 'font-semibold bg-white border border-[#ED7F22] text-black shadow-sm' : 'font-normal text-[#656F80] bg-[#E9EAEB] hover:bg-[#DFE1E3] hover:text-black border border-transparent' }}"
          >
            {{ $catLabel }}
          </button>
        @endforeach
      </div>
    @endif

    <!-- Planetary Galaxy Visual -->
    <div class="mt-8 relative z-10 w-full max-w-[480px] lg:max-w-[1080px] mx-auto flex justify-center items-center">
      <div id="alliances-orbit-wrapper" class="relative z-10 w-full aspect-[592/638] lg:aspect-[1080/480] overflow-hidden rounded-[20px] sm:rounded-[24px] border border-white/90 shadow-[0_20px_50px_rgba(4,177,199,0.12),0_4px_20px_rgba(0,0,0,0.04)] bg-gradient-to-b from-[#F2F7FE] via-white to-[#EBF4FE]">
        <div id="alliances-orbit-canvas" class="w-[592px] h-[638px] lg:w-[1080px] lg:h-[480px] absolute top-0 left-0 origin-top-left">
          <img src="{{ theme_asset('images/alliances-galaxy-bg.png') }}" alt="" class="absolute inset-0 w-full h-full object-cover rounded-[16px] sm:rounded-[24px]" />

          <svg id="alliances-orbit-svg-desktop" class="hidden lg:block absolute inset-0 w-[1080px] h-[480px] pointer-events-none z-10" viewBox="0 0 1080 480" fill="none">
            <defs>
              <filter id="orbit-glow-desktop" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="3" result="blur1"/>
                <feGaussianBlur stdDeviation="6" result="blur2"/>
                <feMerge>
                  <feMergeNode in="blur2"/>
                  <feMergeNode in="blur1"/>
                  <feMergeNode in="SourceGraphic"/>
                </feMerge>
              </filter>
            </defs>
            <ellipse cx="540" cy="240" rx="460" ry="165" stroke="white" stroke-width="1.2" stroke-opacity="0.9" filter="url(#orbit-glow-desktop)" />
            <ellipse cx="540" cy="240" rx="460" ry="165" stroke="#80E8FF" stroke-width="0.8" stroke-opacity="0.6" />
            <ellipse cx="540" cy="240" rx="265" ry="92" stroke="white" stroke-width="1.2" stroke-opacity="0.85" filter="url(#orbit-glow-desktop)" />
            <ellipse cx="540" cy="240" rx="265" ry="92" stroke="#80E8FF" stroke-width="0.8" stroke-opacity="0.5" />
            <ellipse cx="540" cy="265" rx="140" ry="45" stroke="white" stroke-width="1" stroke-opacity="0.9" filter="url(#orbit-glow-desktop)" />
            <ellipse cx="540" cy="265" rx="140" ry="45" stroke="#80E8FF" stroke-width="0.6" stroke-opacity="0.6" />
          </svg>

          <svg id="alliances-orbit-svg-mobile" class="block lg:hidden absolute inset-0 w-[592px] h-[638px] pointer-events-none z-10" viewBox="0 0 592 638" fill="none">
            <defs>
              <filter id="orbit-glow-mobile" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="3" result="blur1"/>
                <feGaussianBlur stdDeviation="6" result="blur2"/>
                <feMerge>
                  <feMergeNode in="blur2"/>
                  <feMergeNode in="blur1"/>
                  <feMergeNode in="SourceGraphic"/>
                </feMerge>
              </filter>
            </defs>
            <ellipse cx="296" cy="345" rx="248" ry="236" stroke="white" stroke-width="1.2" stroke-opacity="0.9" filter="url(#orbit-glow-mobile)" />
            <ellipse cx="296" cy="345" rx="248" ry="236" stroke="#80E8FF" stroke-width="0.8" stroke-opacity="0.6" />
            <ellipse cx="296" cy="345" rx="142" ry="118" stroke="white" stroke-width="1.2" stroke-opacity="0.85" filter="url(#orbit-glow-mobile)" />
            <ellipse cx="296" cy="345" rx="142" ry="118" stroke="#80E8FF" stroke-width="0.8" stroke-opacity="0.5" />
            <ellipse cx="296" cy="345" rx="62" ry="58" stroke="white" stroke-width="1" stroke-opacity="0.7" filter="url(#orbit-glow-mobile)" />
            <ellipse cx="296" cy="345" rx="62" ry="58" stroke="#80E8FF" stroke-width="0.6" stroke-opacity="0.4" />
          </svg>

          <div id="alliances-core-sphere" class="absolute z-20 pointer-events-none left-[242px] top-[291px] lg:left-[486px] lg:top-[186px] w-[108px] h-[108px]">
            <div class="absolute -inset-1.5 rounded-full bg-gradient-to-r from-cyan-300/30 to-blue-400/20 blur-md"></div>
            <img src="{{ theme_asset('images/alliances-core-sphere.png') }}" alt="" class="relative z-10 w-full h-full object-contain filter drop-shadow-[0_8px_20px_rgba(4,177,199,0.25)]" />
            <div class="absolute inset-0 z-20 flex items-center justify-center">
              <img src="{{ theme_asset('images/alliances-converion-logo.png') }}" alt="Converion Core" class="w-[72px] h-[72px] object-contain drop-shadow-sm" />
            </div>
          </div>

          <img src="{{ theme_asset('images/alliances-star-1.png') }}" alt="" class="absolute pointer-events-none z-20 animate-pulse animate-pulse-isolated left-[288px] top-[215px] lg:left-[644px] lg:top-[232px] w-[25px] h-[25px]" />
          <img src="{{ theme_asset('images/alliances-star-2.png') }}" alt="" class="absolute pointer-events-none z-20 animate-pulse animate-pulse-isolated left-[85px] top-[395px] lg:left-[14px] lg:top-[222px] w-[31px] h-[31px] [animation-delay:1s]" />
          <img src="{{ theme_asset('images/alliances-star-2.png') }}" alt="" class="absolute pointer-events-none z-20 animate-pulse animate-pulse-isolated left-[305px] top-[565px] lg:left-[526px] lg:top-[55px] w-[32px] h-[32px] [animation-delay:2s]" />

          <img src="{{ theme_asset('images/alliances-bead-orange.png') }}" alt="" class="alliance-orbit-bead absolute pointer-events-none z-20 drop-shadow-[0_0_6px_rgba(237,127,34,0.8)]" style="left: 0px; top: 0px; width: 14px; height: 12px;" />
          <img src="{{ theme_asset('images/alliances-bead-blue.png') }}" alt="" class="alliance-orbit-bead absolute pointer-events-none z-20 drop-shadow-[0_0_6px_rgba(34,150,228,0.8)]" style="left: 0px; top: 0px; width: 14px; height: 12px;" />
          <img src="{{ theme_asset('images/alliances-bead-blue.png') }}" alt="" class="alliance-orbit-bead absolute pointer-events-none z-20 drop-shadow-[0_0_6px_rgba(34,150,228,0.8)]" style="left: 0px; top: 0px; width: 14px; height: 12px;" />
          <img src="{{ theme_asset('images/alliances-bead-orange.png') }}" alt="" class="alliance-orbit-bead absolute pointer-events-none z-20 drop-shadow-[0_0_6px_rgba(237,127,34,0.8)]" style="left: 0px; top: 0px; width: 14px; height: 12px;" />
          <img src="{{ theme_asset('images/alliances-bead-blue.png') }}" alt="" class="alliance-orbit-bead absolute pointer-events-none z-20 drop-shadow-[0_0_6px_rgba(34,150,228,0.8)]" style="left: 0px; top: 0px; width: 14px; height: 12px;" />

          @for($i = 0; $i < 16; $i++)
            @php $cap = $initialAlliancesInfra[$i] ?? null; @endphp
            <div
              class="alliance-partner-capsule absolute z-20 w-[117px] h-[30px] rounded-full bg-white/95 backdrop-blur-sm border border-white/80 shadow-[0_2px_8px_rgba(0,0,0,0.06),0_1px_2px_rgba(0,0,0,0.04)] flex items-center justify-center px-3 py-1 cursor-pointer select-none"
              data-capsule-index="{{ $i }}"
              data-url="{{ $cap['url'] ?? '' }}"
              @if($cap) role="link" tabindex="0" aria-label="{{ $cap['name'] }}" title="{{ $cap['name'] }}" @else style="display: none; opacity: 0; pointer-events: none;" @endif
            >
              @if($cap)
                <img src="{{ $cap['logo'] }}" alt="{{ $cap['name'] }}" class="partner-logo-img h-3.5 max-w-[85px] w-auto object-contain pointer-events-none" />
              @else
                <img src="" alt="" class="partner-logo-img h-3.5 max-w-[85px] w-auto object-contain pointer-events-none" />
              @endif
            </div>
          @endfor
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 6. WHY CONVERION SECTION                                                  -->
<!-- ========================================================================= -->
<section id="about" class="py-16 sm:py-20 bg-[#04B1C7]/[0.08] relative overflow-hidden text-center">
  <img id="why-puzzle-left" src="{{ theme_asset('images/why-corner-bg-left.svg') }}" alt="" class="absolute -left-[186px] top-[160px] lg:top-[190px] w-[373px] h-[373px] pointer-events-none select-none z-0 will-change-transform" />
  <img id="why-puzzle-right" src="{{ theme_asset('images/why-corner-bg-right.svg') }}" alt="" class="absolute -right-[140px] top-[30px] lg:top-[40px] w-[284px] h-[284px] pointer-events-none select-none z-0 will-change-transform" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    @if(!empty($whyTitle['prefix']))
      <h2 id="why-heading" class="text-3xl sm:text-[32px] font-bold text-slate-900 leading-tight">
        {{ $whyTitle['prefix'] }}
      </h2>
    @endif

    <div class="mt-8 sm:mt-10 max-w-[1100px] mx-auto">
      @if(!empty($whyTitle['main']))
        <h3 id="why-subtitle" class="text-xl sm:text-[21px] font-bold text-slate-900">
          {{ $whyTitle['main'] }}
        </h3>
      @endif

      @if(!empty($whyDescription))
        <div id="why-desc" class="csi-prose mt-4 sm:mt-5 text-[15px] sm:text-base text-[#475467] leading-relaxed max-w-[1050px] mx-auto space-y-3">
          {!! $whyDescription !!}
        </div>
      @endif
    </div>

    @if(!empty($whyCards))
      <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-stretch">
        @foreach($whyCards as $idx => $card)
          @php
            $accent = !empty($card['accent_color']) ? $card['accent_color'] : ($idx % 2 === 1 ? '#ED7F22' : '#04B1C7');
            $isOrangeCard = strtolower($accent) === '#ed7f22';
            $hoverClass = $isOrangeCard ? 'hover:shadow-[0_16px_36px_rgba(237,127,34,0.18)]' : 'hover:shadow-[0_16px_36px_rgba(4,177,199,0.18)]';
            $iconBg = $isOrangeCard ? 'bg-[#ED7F22]/10' : 'bg-[#04B1C7]/10';
          @endphp
          <div class="why-card rounded-[20px] p-2.5 bg-white/40 border border-white/80 shadow-[0_8px_24px_rgba(79,161,155,0.08)] {{ $hoverClass }} hover:-translate-y-1 transition-[box-shadow,border-color,translate] duration-300 flex flex-col group">
            <div class="w-full flex-1 rounded-[10px] px-3.5 pt-7 pb-8 bg-gradient-to-b from-white via-white to-[#F0F5FF] border border-white/60 relative overflow-hidden flex flex-col justify-between items-center text-center">
              <div class="absolute top-0 inset-x-0 h-[7px]" style="background-color: {{ $accent }};"></div>

              <div class="relative z-10 w-full">
                <h4 class="text-[14px] font-bold text-slate-900 leading-[18px]">{{ $card['title'] ?? '' }}</h4>
                <p class="text-[13px] text-[#475467] font-normal leading-[18px] mt-2.5 px-1">
                  {{ $card['description'] ?? '' }}
                </p>
              </div>

              <div class="relative w-full flex flex-col items-center mt-7 mb-1">
                <div class="relative z-10 w-16 h-16 rounded-full bg-white shadow-[0_4px_14px_rgba(170,186,213,0.35)] p-[7px] flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                  <div class="w-full h-full rounded-full flex items-center justify-center {{ $iconBg }}">
                    @if(!empty($card['icon']))
                      <x-image :src="$card['icon']" :alt="$card['title'] ?? 'Value Icon'" sizes="30px" :placeholder="false" class="w-[30px] h-[30px] object-contain" />
                    @endif
                  </div>
                </div>
                <img src="{{ theme_asset('images/why-card-arcs.svg') }}" alt="" class="absolute left-1/2 -translate-x-1/2 top-[42px] w-[383px] max-w-none pointer-events-none select-none opacity-85" />
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>

<!-- ========================================================================= -->
<!-- 7. CUSTOMER SUCCESS STORIES SECTION                                       -->
<!-- ========================================================================= -->
<section id="case-studies" class="py-16 sm:py-20 bg-white relative overflow-hidden text-center">
  <img id="case-halftone" src="{{ theme_asset('images/case-halftone.svg') }}" alt="" class="absolute -left-[280px] sm:-left-[220px] lg:-left-[160px] top-[140px] w-[500px] h-[500px] pointer-events-none select-none z-0 will-change-transform" />
  <img id="case-loops" src="{{ theme_asset('images/case-loops.svg') }}" alt="" class="absolute -right-[160px] sm:-right-[110px] lg:-right-[60px] top-[10px] w-[372px] h-[388px] pointer-events-none select-none z-0 will-change-transform" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <h2 id="case-heading" class="text-3xl sm:text-[32px] font-bold text-slate-900 leading-tight">
      {{ $insightsTitle }}
    </h2>
    @if(!empty($insightsSubtitle))
      <p id="case-subtitle" class="mt-3 text-base text-[#475467] max-w-[750px] mx-auto leading-relaxed">
        {{ $insightsSubtitle }}
      </p>
    @endif

    <div class="mt-8 flex flex-wrap justify-center items-center gap-2">
      @foreach(array_keys($insightCategories) as $idx => $catKey)
        @php $catLabel = ucwords(str_replace('-', ' ', $catKey)); @endphp
        <button type="button" data-insights-cat="{{ $catKey }}"
                class="insights-tab px-4 py-1.5 rounded-[6px] text-xs transition-all cursor-pointer {{ $idx === 0 ? 'font-bold bg-[#ED7F22] text-white shadow-sm' : 'font-normal text-[#656F80] bg-[#E9EAEB] hover:bg-[#dfe1e3]' }}">
          {{ $catLabel }}
        </button>
      @endforeach
    </div>

    @if($initialInsightStory)
      <div class="mt-8 max-w-[960px] mx-auto relative flex items-center justify-center">
        <button id="case-prev-btn" type="button" aria-label="Previous story" class="hidden sm:flex absolute -left-4 lg:-left-6 z-20 w-10 h-10 rounded-full bg-white border border-[#E2ECF8] shadow-md items-center justify-center text-slate-700 hover:text-[#ED7F22] hover:border-[#ED7F22] transition-all hover:scale-105 active:scale-95 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <div id="case-card-outer" class="w-full max-w-[900px] p-2.5 rounded-[20px] bg-[#FAFCFF] border border-[#E2ECF8] shadow-[0_12px_40px_rgba(4,177,199,0.08)] backdrop-blur-md">
          <div id="case-card-inner" class="w-full rounded-[10px] p-6 sm:p-10 lg:p-12 bg-gradient-to-br from-[#EFFDFF] via-white via-40% to-[#FFF7F0] border border-white/90 relative overflow-hidden flex flex-col md:flex-row items-center gap-8 text-left transition-all duration-300">
            <div class="w-full md:w-[200px] shrink-0 rounded-[10px] p-2 bg-white/75 border border-[#04B1C7]/50 shadow-sm">
              <img id="case-story-image" src="{{ $initialInsightStory['image'] }}" alt="{{ $initialInsightStory['author'] }}" class="w-full h-44 object-cover rounded-[5px]" />
            </div>

            <div class="flex-1">
              <h3 id="case-story-title" class="text-xl sm:text-[21px] font-bold text-slate-900 leading-snug">
                {{ $initialInsightStory['title'] }}
              </h3>
              <p id="case-story-quote" class="mt-3 text-sm text-[#475467] leading-relaxed italic">
                {{ $initialInsightStory['quote'] }}
              </p>

              <div class="my-5 h-px bg-[#D9D9D9]"></div>

              <div class="flex items-center justify-between">
                <div>
                  <span id="case-story-author" class="block text-sm font-bold text-slate-900">{{ $initialInsightStory['author'] }}</span>
                  <p id="case-story-role" class="text-xs text-[#656F80] mt-0.5" @if(empty($initialInsightStory['role'])) style="display: none;" @endif>{{ $initialInsightStory['role'] ?? '' }}</p>
                </div>
                <img id="case-story-logo-img" src="{{ $initialInsightStory['logoImg'] }}" alt="{{ $initialInsightStory['author'] }}" class="h-8 max-w-[120px] object-contain" />
              </div>
            </div>

            <div class="absolute bottom-0 inset-x-0 h-[7px] bg-gradient-to-r from-[#04B1C7] via-[#04B1C7] to-[#2296E4]"></div>
          </div>
        </div>

        <button id="case-next-btn" type="button" aria-label="Next story" class="hidden sm:flex absolute -right-4 lg:-right-6 z-20 w-10 h-10 rounded-full bg-white border border-[#E2ECF8] shadow-md items-center justify-center text-slate-700 hover:text-[#ED7F22] hover:border-[#ED7F22] transition-all hover:scale-105 active:scale-95 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <div id="case-mobile-nav" class="flex sm:hidden justify-center items-center gap-4 mt-4">
        <button type="button" onclick="document.getElementById('case-prev-btn')?.click()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-white shadow-xs">← Prev</button>
        <button type="button" onclick="document.getElementById('case-next-btn')?.click()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-white shadow-xs">Next →</button>
      </div>

      <div id="case-pagination-container" class="mt-6 flex justify-center items-center gap-2"></div>
    @endif
  </div>
</section>

<!-- ========================================================================= -->
<!-- 8. CONTACT FORM SECTION (Form Studio + t() Heading & Subtitle)            -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section', ['contactForm' => $contactForm ?? null, 'contactSideImage' => $contactSideImage ?? null])

<!-- ========================================================================= -->
<!-- HERO YOUTUBE VIDEO LIGHTBOX MODAL                                         -->
<!-- ========================================================================= -->
<div id="hero-video-modal" data-lenis-prevent class="fixed inset-0 z-[100] flex items-center justify-center bg-black/85 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 p-4 sm:p-6" role="dialog" aria-modal="true" aria-label="{{ $heroVideoBadgeText }}">
  <div id="hero-video-dialog" class="relative w-full max-w-5xl flex flex-col items-center transform scale-95 transition-transform duration-300">
    <div class="w-full flex items-center justify-between pb-3 text-white">
      <div class="flex items-center gap-2.5">
        <img src="{{ theme_asset('images/hero-play-icon.svg') }}" alt="" class="w-6 h-6 shrink-0" />
        <span class="text-sm sm:text-base font-semibold text-white/95">{{ $heroVideoBadgeText }}</span>
      </div>
      <button id="hero-video-close" type="button" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white/80 hover:text-white transition-all flex items-center gap-1.5 cursor-pointer text-xs group" aria-label="Close video (Esc)">
        <span class="hidden sm:inline text-white/60 group-hover:text-white text-xs mr-1 font-mono">Esc</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div id="hero-video-iframe-container" class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/15 relative"></div>
  </div>
</div>

@push('scripts')
<script>
  window.__CSI_DATA__ = Object.assign(window.__CSI_DATA__ || {}, {
    youtubeVideoId: @json($youtubeVideoId),
    allianceCategories: @json($allianceCategories),
    caseStudies: @json($insightCategories),
  });
</script>
@endpush
@endsection
