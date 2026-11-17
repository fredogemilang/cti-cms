@extends('csi::layouts.app')

@section('content')
@php
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

    $introHeading = $page->block('intro_heading', 'Converion, Established in 2026');
    $introBody = $page->block('intro_body', '');
    $introPrimaryCta = $page->buttonBlock('intro_primary_cta', [
        'text' => 'Our Solutions',
        'url' => '/#solutions',
        'target' => '_self',
    ]);
    $introSecondaryCta = $page->buttonBlock('intro_secondary_cta', [
        'text' => 'Our Products',
        'url' => '/#alliances',
        'target' => '_self',
    ]);
    $introImage = $page->block('intro_image', '');

    $visionTitle = $page->block('vision_title', 'Vision');
    $visionDescription = $page->block('vision_description', '');
    $missionTitle = $page->block('mission_title', 'Mission');
    $missionItems = $page->repeaterBlock('mission_items', []);

    $competenciesTitle = $page->block('competencies_title', 'Our Competencies');
    $competenciesCards = $page->repeaterBlock('competencies_cards', []);

    $awardsTitle = $page->block('awards_title', 'Our Awards');
    $awardsList = $page->repeaterBlock('awards_list', []);
@endphp

<!-- ========================================================================= -->
<!-- 1. ABOUT HERO, COMPANY INTRO & VISION/MISSION                             -->
<!-- ========================================================================= -->
<section id="about-hero" class="relative pt-14 pb-16 bg-white overflow-x-clip">
  <!-- Decorative Arc Lines -->
  <img id="about-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block absolute -left-[64px] top-0 w-[162px] h-[717px] rotate-180 pointer-events-none select-none z-0 will-change-transform" />
  <img id="about-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block absolute right-0 translate-x-[52px] top-[19px] w-[162px] h-[717px] pointer-events-none select-none z-0 will-change-transform" />

  <!-- Ambient Blur Layer -->
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute left-1/2 -translate-x-[20%] top-[360px] w-[997px] h-[504px] opacity-50 blur-[100px] -scale-y-100 rotate-180 pointer-events-none select-none z-0" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col gap-[42px]">
    <!-- Top Hero -->
    <div id="about-hero-top" class="flex flex-col items-center text-center gap-[18px]">
      <h1 id="about-hero-title" class="text-[36px] sm:text-[46px] font-bold leading-tight bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        @if(!empty($heroTitle['prefix']))
          {{ $heroTitle['prefix'] }}<br />
        @endif
        {{ $heroTitle['main'] ?? '' }}
      </h1>

      @if(!empty($heroSubtitle))
        <p id="about-hero-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black max-w-[860px] font-normal">
          {!! nl2br(e($heroSubtitle)) !!}
        </p>
      @endif

      @if(!empty($heroCta['text']))
        <div id="about-hero-cta" class="pt-1 flex justify-center">
          <a href="{{ $heroCta['url'] ?? '#contact' }}" target="{{ $heroCta['target'] ?? '_self' }}"
             class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-semibold text-base px-[21px] py-[16px] rounded-[6px] transition-colors leading-none inline-block shadow-sm">
            {{ $heroCta['text'] }}
          </a>
        </div>
      @endif
    </div>

    <!-- Split Company Intro -->
    <div id="about-intro" class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-[60px] items-center">
      <div id="about-intro-text" class="lg:col-span-6 flex flex-col gap-[21px] items-start text-left">
        @if(!empty($introHeading))
          <h2 class="text-[21px] font-bold leading-[28px] text-black">
            {{ $introHeading }}
          </h2>
        @endif

        @if(!empty($introBody))
          <div class="csi-prose text-base leading-[24px] text-black space-y-4 [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-black [&_h3]:pt-1 [&_strong]:font-bold">
            {!! $introBody !!}
          </div>
        @endif

        <div class="flex flex-wrap gap-[21px] items-center pt-1">
          @if(!empty($introPrimaryCta['text']))
            @php
              $pUrl = $introPrimaryCta['url'] ?? '/#solutions';
              $pHref = str_starts_with($pUrl, '/#') ? localized_url('/').substr($pUrl, 1) : (str_starts_with($pUrl, '/') ? localized_url($pUrl) : $pUrl);
            @endphp
            <a href="{{ $pHref }}" target="{{ $introPrimaryCta['target'] ?? '_self' }}"
               class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-base px-[21px] py-[10px] rounded-[6px] transition-colors shadow-xs">
              {{ $introPrimaryCta['text'] }}
            </a>
          @endif

          @if(!empty($introSecondaryCta['text']))
            @php
              $sUrl = $introSecondaryCta['url'] ?? '/#alliances';
              $sHref = str_starts_with($sUrl, '/#') ? localized_url('/').substr($sUrl, 1) : (str_starts_with($sUrl, '/') ? localized_url($sUrl) : $sUrl);
            @endphp
            <a href="{{ $sHref }}" target="{{ $introSecondaryCta['target'] ?? '_self' }}"
               class="bg-white border border-[#ED7F22] hover:bg-[#FFF7F0] text-black font-bold text-base px-[21px] py-[10px] rounded-[6px] transition-colors">
              {{ $introSecondaryCta['text'] }}
            </a>
          @endif
        </div>
      </div>

      <div id="about-intro-media" class="lg:col-span-6">
        <div class="w-full h-[320px] sm:h-[400px] lg:h-[462px] rounded-[14px] overflow-hidden shadow-md bg-slate-100">
          @if($introImage)
            <x-image id="about-intro-img" :src="$introImage" alt="{{ $introHeading }}" loading="eager" fetchpriority="high" sizes="(max-width: 1024px) 100vw, 50vw" class="w-full h-full object-cover rounded-[14px] will-change-transform" />
          @endif
        </div>
      </div>
    </div>

    <!-- Vision & Mission Banner Card -->
    <div id="about-vision-mission" class="relative rounded-[30px] p-[10px] overflow-hidden shadow-sm"
         style="background: linear-gradient(90deg, #04B1C7 0%, #268BE4 100%);">
      <div id="about-vision-icon" class="hidden lg:block absolute left-[213px] top-[197px] w-[123px] h-[123px] -rotate-45 pointer-events-none select-none opacity-90 will-change-transform">
        <img src="{{ theme_asset('images/about/vision-icon.svg') }}" alt="" class="w-full h-full object-contain" />
      </div>

      <div class="relative z-10 flex flex-col lg:flex-row gap-[10px] items-stretch">
        <!-- Vision Panel -->
        <div id="about-vision-panel" class="lg:w-[321px] shrink-0 rounded-[20px] p-[18px] flex flex-col gap-[26px] text-left">
          <h2 class="text-[21px] font-bold leading-[28px] text-white">{{ $visionTitle }}</h2>
          <p class="text-base leading-[24px] text-white font-normal">{{ $visionDescription }}</p>
        </div>

        <!-- Mission Panel -->
        <div id="about-mission-panel" class="flex-1 rounded-[20px] border border-white p-[18px] relative overflow-hidden flex flex-col gap-[10px] text-left"
             style="background: linear-gradient(109deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
          <img id="about-mission-icon" src="{{ theme_asset('images/about/mission-icon.svg') }}" alt="" class="hidden sm:block absolute right-[8px] top-[9px] w-[169px] h-[169px] pointer-events-none select-none opacity-90 will-change-transform" />

          <h2 class="relative z-10 text-[21px] font-bold leading-[28px] text-black">{{ $missionTitle }}</h2>

          @foreach($missionItems as $mItem)
            <div class="about-mission-item relative z-10 rounded-[10px] p-[18px] pl-6 overflow-hidden bg-gradient-to-r from-[#F0F5FF] from-25% to-transparent flex flex-col gap-[10px]">
              <div class="absolute left-0 top-0 bottom-0 w-[8px] bg-[#ED7F22]"></div>
              @if(!empty($mItem['title']))
                <h3 class="text-base font-bold text-black leading-snug">{{ $mItem['title'] }}</h3>
              @endif
              @if(!empty($mItem['description']))
                <p class="text-base leading-[24px] text-black font-normal">{{ $mItem['description'] }}</p>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 2. OUR COMPETENCIES (Interactive 3-Card Fan Deck)                         -->
<!-- ========================================================================= -->
@if(!empty($competenciesCards))
@php
    $cardCount = count($competenciesCards);
    $defaultCompIdx = $cardCount >= 2 ? 1 : 0;
@endphp
<section id="about-capabilities" class="py-[60px] bg-[#F7FAFF] relative overflow-hidden"
         x-data="{
           active: {{ $defaultCompIdx }},
           total: {{ $cardCount }},
           positionOf(idx) {
             if (this.total <= 1) return 'center';
             if (idx === this.active) return 'center';
             const prev = (this.active - 1 + this.total) % this.total;
             return idx === prev ? 'left' : 'right';
           }
         }">
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[1235px] h-[624px] opacity-50 blur-[100px] pointer-events-none select-none z-0" />
  <img src="{{ theme_asset('images/solutions-bg-grid.svg') }}" alt="" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[1468px] max-w-none h-[465px] opacity-85 pointer-events-none select-none z-0" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[42px]">
    <h2 id="about-cap-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black text-center">
      {{ $competenciesTitle }}
    </h2>

    <!-- Desktop 3-Card Perspective Fan Stage (click any card to switch) -->
    <div id="about-cap-stage" class="hidden lg:block relative w-full max-w-[1200px] h-[615px]">
      @foreach($competenciesCards as $idx => $comp)
        @php
          $col1Lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($comp['col1_items'] ?? '')))));
          $col2Lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($comp['col2_items'] ?? '')))));
        @endphp
        <div @click="active = {{ $idx }}"
             :class="{
               'left-1/2 -translate-x-1/2 top-0 w-[650px] z-30 scale-100 opacity-100 blur-0 cursor-default': positionOf({{ $idx }}) === 'center',
               'left-[2%] top-[52px] w-[620px] z-10 -rotate-5 scale-[0.74] opacity-90 blur-[1.5px] hover:blur-0 hover:opacity-100 cursor-pointer': positionOf({{ $idx }}) === 'left',
               'right-[2%] top-[52px] w-[620px] z-10 rotate-5 scale-[0.74] opacity-90 blur-[1.5px] hover:blur-0 hover:opacity-100 cursor-pointer': positionOf({{ $idx }}) === 'right'
             }"
             class="absolute transition-all duration-500 ease-out backdrop-blur-[8.25px] bg-white/50 border border-white p-[14px] rounded-[20px] shadow-[0_21px_45px_rgba(145,165,197,0.18)] text-left select-none">
          <div class="rounded-[10px] p-[18px] flex flex-col gap-[18px]"
               style="background: linear-gradient(98deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
            <!-- Card Header -->
            <div class="flex items-center gap-[18px] p-[10px]">
              <div class="w-[60px] h-[60px] rounded-full p-[7px] bg-white/50 border border-[#ED7F22]/25 flex items-center justify-center shrink-0">
                <div class="w-full h-full rounded-full p-1 bg-gradient-to-b from-white via-white to-[#F0F5FF] border border-white shadow-sm flex items-center justify-center">
                  @if(!empty($comp['icon']))
                    <x-image :src="$comp['icon']" :alt="$comp['title'] ?? ''" sizes="28px" :placeholder="false" class="w-[26px] h-[26px] object-contain" />
                  @endif
                </div>
              </div>
              <div class="flex-1">
                <h3 class="text-base font-bold text-black leading-snug">{{ $comp['title'] ?? '' }}</h3>
                <p class="text-base text-black font-normal leading-[24px] mt-1">{{ $comp['description'] ?? '' }}</p>
              </div>
            </div>

            <div class="h-px bg-black/10 w-full"></div>

            <!-- Top Outreach Box -->
            <div class="bg-[#F0F5FF] border-[1.5px] border-white rounded-[10px] p-[21px] flex flex-col gap-[10px]">
              <div class="text-base font-bold text-black">{{ $comp['outreach_title'] ?? '' }}</div>
              <div class="grid grid-cols-2 gap-[21px]">
                <div class="flex items-stretch gap-[10px]">
                  <div class="w-[3px] rounded-[99px] bg-[#ED7F22] shrink-0"></div>
                  <div>
                    <div class="text-[14px] font-bold text-black leading-[18px]">{{ $comp['outreach_p1_title'] ?? '' }}</div>
                    <p class="text-[13px] text-black leading-[18px] mt-1">{{ $comp['outreach_p1_desc'] ?? '' }}</p>
                  </div>
                </div>
                <div class="flex items-stretch gap-[10px]">
                  <div class="w-[3px] rounded-[99px] bg-[#ED7F22] shrink-0"></div>
                  <div>
                    <div class="text-[14px] font-bold text-black leading-[18px]">{{ $comp['outreach_p2_title'] ?? '' }}</div>
                    <p class="text-[13px] text-black leading-[18px] mt-1">{{ $comp['outreach_p2_desc'] ?? '' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom 2 Checklist Boxes -->
            <div class="grid grid-cols-2 gap-[18px]">
              <div class="bg-[#F0F5FF] border-[1.5px] border-white rounded-[10px] p-[21px] flex flex-col gap-[10px]">
                <div class="text-base font-bold text-black">{{ $comp['col1_title'] ?? '' }}</div>
                <ul class="space-y-1.5">
                  @foreach($col1Lines as $line)
                    <li class="flex items-center gap-[10px] text-[13px] text-black leading-[18px]">
                      <img src="{{ theme_asset('images/about/comp-check.svg') }}" alt="" class="w-[14px] h-[14px] shrink-0" />
                      <span>{{ $line }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
              <div class="bg-[#F0F5FF] border-[1.5px] border-white rounded-[10px] p-[21px] flex flex-col gap-[10px]">
                <div class="text-base font-bold text-black">{{ $comp['col2_title'] ?? '' }}</div>
                <ul class="space-y-1.5">
                  @foreach($col2Lines as $line)
                    <li class="flex items-center gap-[10px] text-[13px] text-black leading-[18px]">
                      <img src="{{ theme_asset('images/about/comp-check.svg') }}" alt="" class="w-[14px] h-[14px] shrink-0" />
                      <span>{{ $line }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Mobile / Tablet Vertical Stacked Cards (all visible) -->
    <div class="lg:hidden w-full flex flex-col gap-6">
      @foreach($competenciesCards as $idx => $comp)
        @php
          $col1Lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($comp['col1_items'] ?? '')))));
          $col2Lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($comp['col2_items'] ?? '')))));
        @endphp
        <div class="about-cap-mobile-card w-full backdrop-blur-[8.25px] bg-white/60 border border-white p-3 rounded-[20px] shadow-[0_12px_32px_rgba(145,165,197,0.14)] text-left">
          <div class="rounded-[10px] p-4 flex flex-col gap-4" style="background: linear-gradient(98deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
            <div class="flex items-center gap-3.5">
              <div class="w-12 h-12 rounded-full p-1.5 bg-white/60 border border-[#ED7F22]/25 flex items-center justify-center shrink-0">
                <div class="w-full h-full rounded-full bg-gradient-to-b from-white to-[#F0F5FF] flex items-center justify-center">
                  @if(!empty($comp['icon']))
                    <x-image :src="$comp['icon']" :alt="$comp['title'] ?? ''" sizes="24px" :placeholder="false" class="w-6 h-6 object-contain" />
                  @endif
                </div>
              </div>
              <div>
                <h3 class="text-base font-bold text-black">{{ $comp['title'] ?? '' }}</h3>
                <p class="text-sm text-black/80 leading-relaxed mt-0.5">{{ $comp['description'] ?? '' }}</p>
              </div>
            </div>

            <div class="bg-[#F0F5FF] border border-white rounded-[10px] p-4 space-y-3">
              <div class="text-sm font-bold text-black">{{ $comp['outreach_title'] ?? '' }}</div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-stretch gap-2.5">
                  <div class="w-[3px] rounded-full bg-[#ED7F22] shrink-0"></div>
                  <div>
                    <div class="text-xs font-bold text-black">{{ $comp['outreach_p1_title'] ?? '' }}</div>
                    <p class="text-xs text-black/80 mt-0.5">{{ $comp['outreach_p1_desc'] ?? '' }}</p>
                  </div>
                </div>
                <div class="flex items-stretch gap-2.5">
                  <div class="w-[3px] rounded-full bg-[#ED7F22] shrink-0"></div>
                  <div>
                    <div class="text-xs font-bold text-black">{{ $comp['outreach_p2_title'] ?? '' }}</div>
                    <p class="text-xs text-black/80 mt-0.5">{{ $comp['outreach_p2_desc'] ?? '' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="bg-[#F0F5FF] border border-white rounded-[10px] p-4 space-y-2">
                <div class="text-sm font-bold text-black">{{ $comp['col1_title'] ?? '' }}</div>
                <ul class="space-y-1.5">
                  @foreach($col1Lines as $line)
                    <li class="flex items-center gap-2 text-xs text-black">
                      <img src="{{ theme_asset('images/about/comp-check.svg') }}" alt="" class="w-3.5 h-3.5 shrink-0" />
                      <span>{{ $line }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
              <div class="bg-[#F0F5FF] border border-white rounded-[10px] p-4 space-y-2">
                <div class="text-sm font-bold text-black">{{ $comp['col2_title'] ?? '' }}</div>
                <ul class="space-y-1.5">
                  @foreach($col2Lines as $line)
                    <li class="flex items-center gap-2 text-xs text-black">
                      <img src="{{ theme_asset('images/about/comp-check.svg') }}" alt="" class="w-3.5 h-3.5 shrink-0" />
                      <span>{{ $line }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 3. OUR AWARDS SECTION (Interactive Carousel)                              -->
<!-- ========================================================================= -->
@if(!empty($awardsList))
<section id="about-awards" class="py-[60px] bg-white relative overflow-hidden"
         x-data="{
           currentIndex: 0,
           total: {{ count($awardsList) }},
           timer: null,
           init() {
             if (this.total > 1) {
               this.startAutoplay();
             }
           },
           destroy() {
             this.stopAutoplay();
           },
           startAutoplay() {
             this.stopAutoplay();
             if (this.total <= 1) return;
             this.timer = setInterval(() => {
               this.currentIndex = (this.currentIndex + 1) % this.total;
               this.scrollToCurrent();
             }, 3500);
           },
           stopAutoplay() {
             if (this.timer) {
               clearInterval(this.timer);
               this.timer = null;
             }
           },
           prev() { this.currentIndex = (this.currentIndex - 1 + this.total) % this.total; this.scrollToCurrent(); this.startAutoplay(); },
           next() { this.currentIndex = (this.currentIndex + 1) % this.total; this.scrollToCurrent(); this.startAutoplay(); },
           goTo(i) { this.currentIndex = i; this.scrollToCurrent(); this.startAutoplay(); },
           scrollToCurrent() {
             const track = this.$refs.awardsTrack;
             const card = track?.children[this.currentIndex];
             if (track && card) {
               const targetLeft = card.offsetLeft - (track.clientWidth - card.clientWidth) / 2;
               track.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
             }
           }
         }"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()"
         @focusin="stopAutoplay()"
         @focusout="startAutoplay()">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[36px]">
    <h2 id="about-awards-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black text-center">
      {{ $awardsTitle }}
    </h2>

    <div id="about-awards-stage" class="relative w-full py-[10px] rounded-[20px]">
      <!-- Edge Fade Masks -->
      <div class="hidden lg:block pointer-events-none absolute left-0 top-0 bottom-0 w-44 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
      <div class="hidden lg:block pointer-events-none absolute right-0 top-0 bottom-0 w-44 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

      <div x-ref="awardsTrack" class="flex gap-[21px] overflow-x-auto scroll-smooth snap-x snap-mandatory py-2 px-4 lg:px-32 no-scrollbar" style="scrollbar-width: none;">
        @foreach($awardsList as $idx => $award)
          @php $barColor = !empty($award['accent_color']) ? $award['accent_color'] : '#04B1C7'; @endphp
          <div @click="goTo({{ $idx }})"
               class="about-award-card snap-center shrink-0 w-[300px] sm:w-[320px] rounded-[14px] pt-[10px] pb-[18px] px-[10px] relative overflow-hidden cursor-pointer transition-[box-shadow,translate] duration-300 hover:-translate-y-1 shadow-[0_6px_24px_rgba(0,0,0,0.04)]"
               style="background: linear-gradient(119deg, #F9FBFF 1.5%, #FFF4EA 100%);">
            <div class="flex flex-col items-center gap-[21px] w-full">
              <!-- Pedestal Stage + Plaque -->
              <div class="relative w-[200px] h-[200px] rounded-[7px] overflow-hidden flex items-center justify-center">
                <img src="{{ theme_asset('images/about/award-stage.png') }}" alt="" class="absolute inset-0 w-full h-full object-cover mix-blend-multiply pointer-events-none" />
                @if(!empty($award['image']))
                  <div class="relative z-10 w-[111px] h-[157px] -mt-6 flex items-center justify-center">
                    <x-image :src="$award['image']" :alt="$award['title'] ?? 'Award'" sizes="120px" :placeholder="false" class="w-full h-full object-contain" />
                  </div>
                @endif
              </div>

              <div class="text-center px-2">
                <p class="text-[14px] font-bold leading-[18px] text-black">{{ $award['title'] ?? '' }}</p>
                @if(!empty($award['organization']))
                  <p class="text-[14px] font-bold leading-[18px] text-black">{{ $award['organization'] }}</p>
                @endif
                @if(!empty($award['year']))
                  <p class="text-[14px] font-normal leading-[20px] text-black">{{ $award['year'] }}</p>
                @endif
              </div>
            </div>

            <div class="absolute bottom-0 inset-x-0 h-[6px]" style="background-color: {{ $barColor }};"></div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Navigation Controls & Dots -->
    <div id="about-awards-controls" class="flex flex-wrap justify-center items-center gap-[13px]">
      <button type="button" @click="prev()" aria-label="Previous award"
              class="w-[36px] h-[36px] rounded-full border-2 border-[#ED7F22] flex items-center justify-center text-[#ED7F22] hover:bg-[#ED7F22] hover:text-white transition-colors cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
      </button>

      @foreach($awardsList as $idx => $award)
        <button type="button" @click="goTo({{ $idx }})" aria-label="Award {{ $idx + 1 }}"
                :class="currentIndex === {{ $idx }} ? 'bg-[#ED7F22] w-4' : 'bg-[#E4EBFA] w-2'"
                class="h-2 rounded-full transition-all cursor-pointer"></button>
      @endforeach

      <button type="button" @click="next()" aria-label="Next award"
              class="w-[36px] h-[36px] rounded-full border-2 border-[#ED7F22] flex items-center justify-center text-[#ED7F22] hover:bg-[#ED7F22] hover:text-white transition-colors cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 4. CONTACT FORM SECTION                                                   -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
