@extends('csi::layouts.app')

@section('content')
@php
    use Themes\Csi\Support\CsiDataProvider;

    $heroTitle = $page->block('hero_title', 'Partner Program');
    $heroSubtitle = $page->block('hero_subtitle', '');
    $heroPrimaryCta = $page->buttonBlock('hero_primary_cta', [
        'text' => 'Become a Partner',
        'url' => '#contact',
        'target' => '_self',
    ]);
    $heroSecondaryCta = $page->buttonBlock('hero_secondary_cta', [
        'text' => 'Explore Our Programs',
        'url' => '#partner-programs',
        'target' => '_self',
    ]);

    $promoImage = $page->block('promo_image', '');
    $promoTitle = $page->block('promo_title', 'Annual Partner Incentive Trip 2027');
    $promoDescription = $page->block('promo_description', '');
    $promoPeriod = $page->block('promo_period', '');
    $promoCta = $page->buttonBlock('promo_cta', [
        'text' => 'Explore the Partner Reward Catalog',
        'url' => '#contact',
        'target' => '_self',
    ]);

    $storiesTitle = $page->block('stories_title', 'Partner Success Stories');
    $storiesSubtitle = $page->block('stories_subtitle', '');
    $storiesHighlight = $page->block('stories_highlight', '');
    $storiesList = CsiDataProvider::partnerStories()->all();
    if (empty($storiesList)) {
        $storiesList = $page->repeaterBlock('stories_list', []);
    }
    $realStoryCount = count($storiesList);
    $loopStories = $storiesList;
    if ($realStoryCount > 1) {
        $loopStories = [];
        $repeatTimes = $realStoryCount === 2 ? 6 : 3;
        for ($r = 0; $r < $repeatTimes; $r++) {
            foreach ($storiesList as $item) {
                $loopStories[] = $item;
            }
        }
    }

    $growTitle = $page->block('grow_title', 'Start Growing With Us');
    $growSubtitle = $page->block('grow_subtitle', '');
    $growCards = $page->repeaterBlock('grow_cards', []);

    $programsTitle = $page->block('programs_title', 'Featured Partner Programs');
    $programsSubtitle = $page->block('programs_subtitle', '');
    $programsList = $page->repeaterBlock('programs_list', []);

    $bannerDescription = $page->block('banner_description', '');
    $bannerHighlight = $page->block('banner_highlight', '');
    $bannerCta = $page->buttonBlock('banner_cta', [
        'text' => 'Find Out More',
        'url' => '/#solutions',
        'target' => '_self',
    ]);

    $resolveButtonHref = function (string $url): string {
        if (str_starts_with($url, '/#')) {
            return localized_url('/').substr($url, 1);
        }
        if (str_starts_with($url, '/')) {
            return localized_url($url);
        }
        return $url;
    };
@endphp

<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines & Seamless Ambient Glow across Hero + Partner Success Stories -->
  <img id="partner-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block absolute -left-[53px] top-[26px] w-[149px] h-[664px] rotate-180 pointer-events-none select-none z-0 will-change-transform" />
  <img id="partner-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block absolute -right-[53px] top-[-70px] w-[165px] h-[731px] pointer-events-none select-none z-0 will-change-transform" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="absolute left-1/2 -translate-x-1/2 top-[290px] w-[1341px] h-[592px] opacity-55 blur-[100px] -scale-y-100 rotate-180 pointer-events-none select-none z-0" />

<!-- ========================================================================= -->
<!-- 1. PARTNER PROGRAM HERO & FEATURED INCENTIVE BANNER                       -->
<!-- ========================================================================= -->
<section id="partner-hero" class="relative z-10 pt-14 pb-16 bg-transparent">

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[42px]">
    <!-- Top Hero -->
    <div id="partner-hero-top" class="flex flex-col items-center text-center gap-[18px]">
      <h1 id="partner-hero-title" class="text-[36px] sm:text-[46px] font-bold leading-[1.28] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $heroTitle }}
      </h1>

      @if(!empty($heroSubtitle))
        <p id="partner-hero-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black max-w-[942px] font-normal">
          {!! nl2br(e($heroSubtitle)) !!}
        </p>
      @endif

      <div id="partner-hero-ctas" class="pt-1 flex flex-wrap justify-center items-center gap-[21px]">
        @if(!empty($heroPrimaryCta['text']))
          <a href="{{ $resolveButtonHref($heroPrimaryCta['url'] ?? '#contact') }}" target="{{ $heroPrimaryCta['target'] ?? '_self' }}"
             class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-base px-[21px] py-[16px] rounded-[6px] transition-colors leading-none inline-block shadow-sm">
            {{ $heroPrimaryCta['text'] }}
          </a>
        @endif

        @if(!empty($heroSecondaryCta['text']))
          <a href="{{ $resolveButtonHref($heroSecondaryCta['url'] ?? '#partner-programs') }}" target="{{ $heroSecondaryCta['target'] ?? '_self' }}"
             class="bg-white border border-[#ED7F22] hover:bg-[#FFF7F0] text-black font-semibold text-base px-[21px] py-[16px] rounded-[6px] transition-colors leading-none inline-block">
            {{ $heroSecondaryCta['text'] }}
          </a>
        @endif
      </div>
    </div>

    <!-- Annual Partner Incentive Trip Promo Card -->
    <div id="partner-promo-card" class="w-full max-w-[1100px] rounded-[30px] p-[10px] overflow-hidden shadow-sm"
         style="background: linear-gradient(90deg, #04B1C7 0%, #268BE4 100%);">
      <div class="rounded-[20px] border border-white p-[10px] grid grid-cols-1 lg:grid-cols-12 gap-[10px] items-stretch"
           style="background: linear-gradient(114deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
        <!-- Left Promo Poster Image -->
        <div class="lg:col-span-6 relative rounded-[14px] overflow-hidden min-h-[240px] lg:min-h-[287px] bg-slate-100">
          @if($promoImage)
            <x-image id="partner-promo-img" :src="$promoImage" alt="{{ $promoTitle }}" loading="eager" fetchpriority="high" sizes="(max-width: 1024px) 100vw, 520px" class="w-full h-full object-cover rounded-[14px] will-change-transform" style="object-position: 50% 24%;" />
          @endif
        </div>

        <!-- Right Promo Text -->
        <div class="lg:col-span-6 p-[18px] flex flex-col justify-center items-start gap-[21px] text-left">
          @if(!empty($promoTitle))
            <h2 class="text-[21px] font-bold leading-[28px] text-black">
              {{ $promoTitle }}
            </h2>
          @endif

          @if(!empty($promoDescription))
            <p class="text-base leading-[24px] text-black font-normal">
              {!! nl2br(e($promoDescription)) !!}
            </p>
          @endif

          @if(!empty($promoPeriod))
            <p class="text-base font-bold leading-[24px] text-black">
              {{ $promoPeriod }}
            </p>
          @endif

          @if(!empty($promoCta['text']))
            <div>
              <a href="{{ $resolveButtonHref($promoCta['url'] ?? '#contact') }}" target="{{ $promoCta['target'] ?? '_self' }}"
                 class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-base px-[21px] py-[10px] rounded-[6px] transition-colors inline-block shadow-xs">
                {{ $promoCta['text'] }}
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 2. PARTNER SUCCESS STORIES SECTION (CPT `partner_story` · Slide by 3)     -->
<!-- ========================================================================= -->
@if($realStoryCount > 0)
<section id="partner-stories" class="relative z-10 pt-[30px] pb-[60px] bg-transparent overflow-x-clip">
  <div class="w-full flex flex-col items-center gap-[36px]">
    <!-- Headline + Desc -->
    <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] w-full flex flex-col items-center">
      <div class="max-w-[816px] flex flex-col gap-[21px] items-center text-center">
        <h2 id="partner-stories-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black">
          {{ $storiesTitle }}
        </h2>
        @if(!empty($storiesSubtitle))
          <p id="partner-stories-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal">
            {{ $storiesSubtitle }}
          </p>
        @endif
        @if(!empty($storiesHighlight))
          <p id="partner-stories-highlight" class="text-lg sm:text-[21px] font-bold leading-[28px] text-black">
            {{ $storiesHighlight }}
          </p>
        @endif
      </div>
    </div>

    <!-- Centered 3-Card Looped Carousel Stage with Left & Right Fade Masks -->
    <div id="partner-stories-stage" class="relative w-full max-w-[1440px] mx-auto">
      <!-- Symmetrical Left & Right Edge Fade Masks -->
      <div class="hidden sm:block pointer-events-none absolute left-0 top-0 bottom-0 w-16 md:w-32 lg:w-44 xl:w-56 bg-gradient-to-r from-white via-white/85 to-transparent z-20"></div>
      <div class="hidden sm:block pointer-events-none absolute right-0 top-0 bottom-0 w-16 md:w-32 lg:w-44 xl:w-56 bg-gradient-to-l from-white via-white/85 to-transparent z-20"></div>

      <div id="partner-stories-swiper" class="swiper py-2" data-real-count="{{ $realStoryCount }}">
        <div class="swiper-wrapper items-stretch">
          @foreach($loopStories as $idx => $story)
            @php
              $realIdx = $idx % $realStoryCount;
              $barColor = !empty($story['accent_color']) ? $story['accent_color'] : ($realIdx % 2 === 0 ? '#ED7F22' : '#04B1C7');
              $storyHref = !empty($story['link_url']) ? $resolveButtonHref($story['link_url']) : '#contact';
            @endphp
            <div class="swiper-slide !w-[300px] !h-auto flex">
              <article class="partner-story-card w-[300px] min-h-[460px] rounded-[14px] p-[21px] pb-[26px] relative overflow-hidden flex flex-col justify-between gap-[10px] shadow-[0_6px_24px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-[box-shadow,translate] duration-300"
                       style="background: linear-gradient(109deg, #F9FBFF 1.5%, #FFF4EA 100%);">
                <div class="flex flex-col gap-[21px] items-center w-full">
                  <div class="w-full h-[169px] rounded-[5px] overflow-hidden bg-slate-100">
                    @if(!empty($story['image']))
                      <x-image :src="$story['image']" :alt="$story['partner_name'] ?? 'Partner Story'" sizes="260px" :placeholder="false" class="w-full h-full object-cover rounded-[5px]" />
                    @endif
                  </div>

                  <div class="w-full text-left text-[13px] leading-[18px] text-black">
                    @if(!empty($story['partner_name']))
                      <span class="font-bold">{{ $story['partner_name'] }}</span>
                    @endif
                    @if(!empty($story['summary']))
                      <span class="font-normal"> {{ $story['summary'] }}</span>
                    @endif
                  </div>
                </div>

                <div class="w-full flex flex-col gap-[10px] pt-2">
                  <div class="h-px w-full bg-[#ED7F22]"></div>
                  <a href="{{ $storyHref }}" class="flex items-center justify-end gap-[5px] py-[5px] text-[13px] font-semibold text-[#ED7F22] hover:opacity-80 transition-opacity">
                    <span>{{ $story['link_text'] ?: 'Read More' }}</span>
                    <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-4 h-4 shrink-0" />
                  </a>
                </div>

                <div class="absolute bottom-0 inset-x-0 h-[10px]" style="background-color: {{ $barColor }};"></div>
              </article>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Carousel Arrows & Dots -->
    <div id="partner-stories-controls" class="flex flex-wrap justify-center items-center gap-[13px]">
      <button id="partner-stories-prev" type="button" aria-label="Previous stories"
              class="w-[36px] h-[36px] rounded-full border-2 border-[#ED7F22] flex items-center justify-center text-[#ED7F22] hover:bg-[#ED7F22] hover:text-white transition-colors cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
      </button>

      @for($dotIdx = 0; $dotIdx < $realStoryCount; $dotIdx++)
        <button type="button"
                data-story-dot="{{ $dotIdx }}"
                aria-label="Story {{ $dotIdx + 1 }}"
                class="partner-story-dot h-2 rounded-full transition-all cursor-pointer {{ $dotIdx === 0 ? 'bg-[#ED7F22] w-4' : 'bg-[#E4EBFA] w-2' }}"></button>
      @endfor

      <button id="partner-stories-next" type="button" aria-label="Next stories"
              class="w-[36px] h-[36px] rounded-full border-2 border-[#ED7F22] flex items-center justify-center text-[#ED7F22] hover:bg-[#ED7F22] hover:text-white transition-colors cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>
</section>
@endif
</div>

<!-- ========================================================================= -->
<!-- 3. START GROWING WITH US SECTION (7 Cards: 4 + 3 Centered)                -->
<!-- ========================================================================= -->
@if(!empty($growCards))
<section id="partner-grow" class="py-[60px] bg-[#F7FAFF] relative overflow-hidden">
  <!-- Decorative Halftone & Loops -->
  <img id="partner-grow-halftone" src="{{ theme_asset('images/case-halftone.svg') }}" alt="" class="hidden md:block absolute -left-[280px] lg:-left-[220px] top-[120px] w-[522px] h-[521px] pointer-events-none select-none z-0 opacity-85 will-change-transform" />
  <img id="partner-grow-loops" src="{{ theme_asset('images/case-loops.svg') }}" alt="" class="hidden md:block absolute -right-[160px] lg:-right-[90px] top-[10px] w-[372px] h-[388px] pointer-events-none select-none z-0 opacity-90 will-change-transform" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[36px]">
    <div class="max-w-[816px] flex flex-col gap-[21px] items-center text-center">
      <h2 id="partner-grow-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black">
        {{ $growTitle }}
      </h2>
      @if(!empty($growSubtitle))
        <p id="partner-grow-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal">
          {{ $growSubtitle }}
        </p>
      @endif
    </div>

    <!-- 4 + 3 Centered Card Grid -->
    <div class="w-full max-w-[1040px] flex flex-wrap justify-center gap-[16px]">
      @foreach($growCards as $idx => $gCard)
        @php
          $barColor = !empty($gCard['accent_color']) ? $gCard['accent_color'] : ($idx % 2 === 0 ? '#04B1C7' : '#ED7F22');
          $isOrange = strtolower($barColor) === '#ed7f22';
          $iconBg = $isOrange ? 'bg-[#ED7F22]/10' : 'bg-[#04B1C7]/10';
          $hoverShadow = $isOrange ? 'hover:shadow-[0_16px_36px_rgba(237,127,34,0.16)]' : 'hover:shadow-[0_16px_36px_rgba(4,177,199,0.16)]';
        @endphp
        <div class="partner-grow-card w-full sm:w-[240px] h-[290px] rounded-[20px] p-[10px] bg-black/[0.02] border border-white shadow-[0_12px_28px_rgba(188,196,215,0.22)] {{ $hoverShadow }} hover:-translate-y-1 transition-[box-shadow,translate] duration-300 flex flex-col group">
          <div class="w-full flex-1 rounded-[10px] p-[21px] bg-gradient-to-b from-white from-[46%] to-[#F0F5FF] relative overflow-hidden flex flex-col items-center text-center gap-[10px]">
            <div class="absolute top-0 inset-x-0 h-[7px]" style="background-color: {{ $barColor }};"></div>

            <h3 class="relative z-10 text-[14px] font-bold leading-[18px] text-black">
              {{ $gCard['title'] ?? '' }}
            </h3>
            <p class="relative z-10 text-[13px] font-normal leading-[18px] text-black">
              {{ $gCard['description'] ?? '' }}
            </p>

            <!-- Floating Circular Icon Badge -->
            <div class="absolute left-1/2 -translate-x-1/2 top-[151px] z-20 w-[64px] h-[64px] rounded-full bg-white p-[7px] shadow-[0_7px_14px_rgba(170,186,213,0.28)] flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
              <div class="w-full h-full rounded-full {{ $iconBg }} flex items-center justify-center p-2">
                @if(!empty($gCard['icon']))
                  <x-image :src="$gCard['icon']" :alt="$gCard['title'] ?? 'Program Pillar'" sizes="32px" :placeholder="false" class="w-[28px] h-[28px] object-contain" />
                @endif
              </div>
            </div>

            <!-- Bottom Curved Lines -->
            <img src="{{ theme_asset('images/why-card-arcs.svg') }}" alt="" class="absolute left-1/2 -translate-x-1/2 top-[181px] w-[383px] max-w-none pointer-events-none select-none z-10 opacity-90" />
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 4. FEATURED PARTNER PROGRAMS + BOTTOM CTA BANNER                          -->
<!-- ========================================================================= -->
<section id="partner-programs" class="pt-[60px] pb-[60px] relative overflow-hidden"
         style="background: linear-gradient(102deg, #D3F5FA 0%, #FFFFFF 50%, #FFEFE2 100%);">
  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[56px]">
    <!-- Programs Headline + 3 Cards -->
    @if(!empty($programsList))
    <div class="w-full flex flex-col items-center gap-[36px]">
      <div class="max-w-[816px] flex flex-col gap-[21px] items-center text-center">
        <h2 id="partner-programs-heading" class="text-3xl sm:text-[32px] font-bold leading-[42px] text-black">
          {{ $programsTitle }}
        </h2>
        @if(!empty($programsSubtitle))
          <p id="partner-programs-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal">
            {{ $programsSubtitle }}
          </p>
        @endif
      </div>

      <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-[18px] items-stretch">
        @foreach($programsList as $idx => $prog)
          @php
            $accent = !empty($prog['accent_color']) ? strtolower($prog['accent_color']) : ($idx % 2 === 1 ? '#ed7f22' : '#04b1c7');
            $isOrangeProg = $accent === '#ed7f22';
            $bottomGrad = $isOrangeProg
              ? 'linear-gradient(90deg, #ED7F22 35%, #FFB87D 70%)'
              : 'linear-gradient(90deg, #04B1C7 35%, #2296E4 70%)';
          @endphp
          <div class="partner-program-card rounded-[20px] p-[10px] bg-black/[0.02] border-[0.5px] border-[#ED7F22]/25 flex flex-col hover:-translate-y-1 transition-[box-shadow,translate] duration-300 hover:shadow-[0_16px_36px_rgba(4,177,199,0.12)]">
            <div class="flex-1 rounded-[10px] border border-white p-[21px] pb-[30px] relative overflow-hidden flex flex-col gap-[18px] text-left"
                 style="background: linear-gradient(98deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
              <div class="w-full h-[161px] rounded-[5px] overflow-hidden bg-slate-100">
                @if(!empty($prog['image']))
                  <x-image :src="$prog['image']" :alt="$prog['title'] ?? 'Partner Program'" sizes="(max-width: 768px) 100vw, 380px" :placeholder="false" class="w-full h-full object-cover" />
                @endif
              </div>

              <div class="flex flex-col gap-[10px]">
                <h3 class="text-[21px] font-bold leading-[28px] text-black">
                  {{ $prog['title'] ?? '' }}
                </h3>
                <p class="text-[13px] font-normal leading-[18px] text-black">
                  {{ $prog['description'] ?? '' }}
                </p>
              </div>

              <div class="absolute bottom-0 inset-x-0 h-[7px]" style="background: {{ $bottomGrad }};"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Partnership CTA Banner -->
    <div id="partner-cta-banner" class="w-full rounded-[30px] p-[10px] overflow-hidden shadow-md"
         style="background: linear-gradient(90deg, #04B1C7 0%, #268BE4 100%);">
      <div class="flex flex-col lg:flex-row items-stretch justify-between gap-[21px]">
        <div class="flex-1 p-[18px] flex flex-col justify-center gap-[21px] text-left text-white">
          @if(!empty($bannerDescription))
            <p class="text-base leading-[24px] font-normal">
              {{ $bannerDescription }}
            </p>
          @endif
          @if(!empty($bannerHighlight))
            <p class="text-lg sm:text-[21px] font-bold leading-[28px]">
              {{ $bannerHighlight }}
            </p>
          @endif
        </div>

        @if(!empty($bannerCta['text']))
          <div class="lg:w-[341px] shrink-0 p-[10px] sm:p-[18px] flex items-center justify-center">
            <a href="{{ $resolveButtonHref($bannerCta['url'] ?? '/#solutions') }}" target="{{ $bannerCta['target'] ?? '_self' }}"
               class="w-full h-full min-h-[110px] rounded-[20px] p-[10px] bg-white/36 border-[0.5px] border-white flex group transition-transform duration-300 hover:scale-[1.02]">
              <div class="flex-1 rounded-[10px] p-[18px] flex items-center justify-center gap-[10px]"
                   style="background: linear-gradient(110deg, #D3F5FA 0%, #FFFFFF 50%, #FFEFE2 100%);">
                <span class="text-[21px] font-bold leading-[28px] text-[#ED7F22]">
                  {{ $bannerCta['text'] }}
                </span>
                <img src="{{ theme_asset('images/arrow-right-circle.svg') }}" alt="" class="w-5 h-5 shrink-0 group-hover:translate-x-1 transition-transform" />
              </div>
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 5. CONTACT FORM SECTION                                                   -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
