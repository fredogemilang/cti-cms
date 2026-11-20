@extends('csi::layouts.app')

@section('content')
@php
    use App\Models\CptEntry;

    $currentLocale = app()->getLocale();
    $entryTitle = $entry->getTranslation('title', $currentLocale) ?? $entry->title;
    $heroSubtitle = $entry->getMeta('hero_subtitle') ?: ($entry->getTranslation('excerpt', $currentLocale) ?? $entry->excerpt ?? '');
    $modulesHeading = $entry->getMeta('modules_heading') ?: t('solutions.modules_heading', 'Comprehensive Solution Modules');
    $defaultModulesImage = $entry->getMeta('modules_image') ?: $entry->featured_image;

    $rawMeta = $entry->meta ?? [];
    $defaultModulesRaw = is_array($rawMeta['modules'] ?? null) ? $rawMeta['modules'] : [];
    $modulesRaw = $entry->getMeta('modules', []);
    if (! is_array($modulesRaw)) {
        $modulesRaw = [];
    }

    // Normalize per-module related vendor IDs (inheriting from EN default if omitted on a locale override)
    $allVendorIds = [];
    $modules = [];
    foreach ($modulesRaw as $idx => $mod) {
        if (! is_array($mod) || trim((string) ($mod['title'] ?? '')) === '') {
            continue;
        }
        $relIds = $mod['related_products'] ?? ($defaultModulesRaw[$idx]['related_products'] ?? []);
        if (! is_array($relIds)) {
            $relIds = $relIds !== null && $relIds !== '' ? [$relIds] : [];
        }
        $relIds = array_values(array_unique(array_filter(array_map('intval', $relIds), fn (int $id) => $id > 0)));
        foreach ($relIds as $vid) {
            $allVendorIds[] = $vid;
        }
        $modules[] = [
            'title' => trim((string) ($mod['title'] ?? '')),
            'description' => trim((string) ($mod['description'] ?? '')),
            'image' => ! empty($mod['image']) ? $mod['image'] : $defaultModulesImage,
            'related_ids' => $relIds,
        ];
    }

    $vendorsById = CptEntry::resolveRelatedByIds($allVendorIds)->keyBy('id');

    $servicesHeading = $entry->getMeta('services_heading') ?: t('solutions.services_heading_prefix', 'Service Related to').' '.$entryTitle.' '.t('solutions.solution_suffix', 'Solution');
    $servicesSubtitle = $entry->getMeta('services_subtitle', '');
    $servicesRaw = $entry->getMeta('services_cards', []);
    if (! is_array($servicesRaw)) {
        $servicesRaw = [];
    }
    $serviceCards = [];
    foreach ($servicesRaw as $svc) {
        if (! is_array($svc) || trim((string) ($svc['title'] ?? '')) === '') {
            continue;
        }
        $checks = preg_split('/\r\n|\r|\n/', (string) ($svc['checklist_items'] ?? '')) ?: [];
        $checks = array_values(array_filter(array_map('trim', $checks), fn ($line) => $line !== ''));
        $serviceCards[] = [
            'icon' => $svc['icon'] ?? '',
            'title' => trim((string) ($svc['title'] ?? '')),
            'description' => trim((string) ($svc['description'] ?? '')),
            'checks' => $checks,
        ];
    }

    $activeAccent = $entry->getMeta('accent_variant', 'cyan');
@endphp

<!-- ========================================================================= -->
<!-- 1. HERO + 2. SOLUTION MODULES ACCORDION (Figma 488:6049 / 788 / 819)      -->
<!-- ========================================================================= -->
<div class="relative bg-white overflow-x-clip">
  <!-- Decorative Arc Lines -->
  <img id="solution-arc-left" src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-[122px] w-[162px] h-[717px] rotate-180 z-0 will-change-transform" />
  <img id="solution-arc-right" src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[19px] w-[162px] h-[717px] z-0 will-change-transform" />

  <!-- Top Ambient Glow -->
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="pointer-events-none select-none absolute left-[calc(50%+180px)] -translate-x-1/2 top-[110px] w-[897px] max-w-none h-[427px] opacity-50 blur-[100px] -scale-y-100 rotate-180 z-0" />

  <!-- 1. HERO HEADER (Above-the-Fold LCP Safe) -->
  <section id="solution-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[52px] pb-[48px]">
    <div class="flex flex-col items-center text-center gap-[18px] w-full">
      <x-seo-breadcrumbs :entity="$entry" class="mb-2 justify-center" />

      <h1 id="solution-hero-title" class="text-[36px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.22] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $entryTitle }}
      </h1>

      @if($heroSubtitle !== '')
        <p id="solution-hero-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal max-w-[820px]">
          {{ $heroSubtitle }}
        </p>
      @endif
    </div>
  </section>

  <!-- 2. SOLUTION MODULES ACCORDION + RELATED PRODUCTS + RIGHT PREVIEW -->
  @if(count($modules) > 0)
  <section id="solution-modules"
           class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[24px] pb-[64px]"
           x-data="{ activeModule: 0 }">
    <div class="flex flex-col items-center gap-[32px]">
      <h2 id="solution-modules-heading" class="text-[26px] sm:text-[32px] font-bold leading-[42px] text-black text-center max-w-[764px]">
        {{ $modulesHeading }}
      </h2>

      <div class="flex flex-col lg:flex-row items-start justify-between gap-[36px] w-full py-[10px]">
        <!-- Left Accordion Column -->
        <div class="w-full lg:w-[590px] shrink-0 flex flex-col">
          @foreach($modules as $idx => $mod)
            <div class="solution-module-item flex flex-col w-full">
              <div class="h-px w-full bg-[#E4EBFA]"></div>

              <button type="button"
                      @click="activeModule = (activeModule === {{ $idx }} ? -1 : {{ $idx }})"
                      :aria-expanded="(activeModule === {{ $idx }}).toString()"
                      class="w-full flex items-start justify-between gap-4 px-[18px] py-[18px] text-left cursor-pointer group focus:outline-none">
                <span class="text-[16px] font-bold leading-snug text-black group-hover:text-[#04B1C7] transition-colors flex-1"
                      :class="activeModule === {{ $idx }} ? 'text-[#04B1C7]' : ''">
                  {{ $mod['title'] }}
                </span>

                <span class="shrink-0 w-[30px] h-[30px] flex items-center justify-center text-[#1E293B] transition-transform duration-300"
                      :class="activeModule === {{ $idx }} ? 'rotate-45 text-[#04B1C7]' : 'rotate-0'">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                  </svg>
                </span>
              </button>

              <div x-show="activeModule === {{ $idx }}"
                   x-collapse
                   @if($idx !== 0) x-cloak @endif
                   class="px-[18px] pb-[20px]">
                @if($mod['description'] !== '')
                  <p class="text-[14px] leading-[22px] text-[#334155] font-normal">
                    {{ $mod['description'] }}
                  </p>
                @endif

                @if(count($mod['related_ids']) > 0)
                  <div class="mt-4 pt-3.5 border-t border-[#E4EBFA]/80">
                    <span class="block text-[11px] font-bold tracking-[0.14em] uppercase text-[#656F80] mb-2.5">
                      {{ t('solutions.related_products', 'Related Products') }}
                    </span>

                    <div class="flex flex-wrap gap-2">
                      @foreach($mod['related_ids'] as $vid)
                        @php $vendor = $vendorsById->get($vid); @endphp
                        @if($vendor)
                          @php
                              $vendorName = $vendor->getTranslation('title', $currentLocale) ?? $vendor->title;
                              $vendorLogo = $vendor->getMeta('brand_logo') ?: $vendor->featured_image;
                              $vendorUrl = $vendor->getUrl($currentLocale);
                          @endphp
                          <a href="{{ $vendorUrl }}"
                             class="group/vendor inline-flex items-center gap-2 rounded-[8px] border border-[#D1DBE5] bg-white px-3 py-1.5 shadow-2xs hover:border-[#04B1C7] hover:-translate-y-0.5 hover:shadow-[0_6px_18px_rgba(4,177,199,0.14)] transition-all duration-200"
                             title="{{ $vendorName }}">
                            @if(!empty($vendorLogo))
                              <img src="{{ resolve_block_asset($vendorLogo) }}" alt="{{ $vendorName }}" loading="lazy" class="h-[18px] max-w-[64px] w-auto object-contain shrink-0" />
                            @endif
                            <span class="text-[12px] font-semibold text-slate-800 group-hover/vendor:text-[#04B1C7] transition-colors whitespace-nowrap">
                              {{ $vendorName }}
                            </span>
                          </a>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
          <div class="h-px w-full bg-[#E4EBFA]"></div>
        </div>

        <!-- Right Frosted Glass Visual Showcase -->
        <div class="w-full lg:flex-1 lg:max-w-[574px] lg:sticky lg:top-[112px]">
          <div class="backdrop-blur-[25px] bg-[rgba(209,219,229,0.5)] border border-white rounded-[20px] p-[14px] shadow-[0_20px_50px_rgba(148,163,184,0.18)]">
            <div class="relative w-full h-[300px] sm:h-[380px] lg:h-[410px] rounded-[12px] overflow-hidden bg-[#0B1726]">
              @foreach($modules as $idx => $mod)
                @php $modImg = $mod['image'] ?: $defaultModulesImage; @endphp
                @if(!empty($modImg))
                  <div x-show="activeModule === {{ $idx }} || (activeModule === -1 && {{ $idx }} === 0)"
                       x-transition.opacity.duration.300ms
                       @if($idx !== 0) x-cloak @endif
                       class="absolute inset-0 w-full h-full">
                    <x-image :src="$modImg" :alt="$mod['title']" sizes="(max-width: 1024px) 100vw, 560px" :placeholder="false" class="w-full h-full object-cover" />
                  </div>
                @endif
              @endforeach

              @if(count($modules) === 0 && !empty($defaultModulesImage))
                <x-image :src="$defaultModulesImage" :alt="$entryTitle" sizes="(max-width: 1024px) 100vw, 560px" :placeholder="false" class="w-full h-full object-cover" />
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  @endif
</div>

<!-- ========================================================================= -->
<!-- 3. SERVICE RELATED SECTION (Figma 488:6176 / 788 / 819)                   -->
<!-- ========================================================================= -->
@if(count($serviceCards) > 0)
<section id="solution-services" class="relative bg-white overflow-x-clip py-[56px] sm:py-[64px]">
  <!-- Subtle Wave Vectors -->
  <img src="{{ theme_asset('images/solutions/solutions-services-wave.svg') }}" alt="" class="pointer-events-none select-none absolute left-1/2 -translate-x-1/2 top-[150px] w-[1468px] max-w-none h-[465px] z-0 opacity-90" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] flex flex-col items-center gap-[36px]">
    <div class="flex flex-col items-center gap-[16px] text-center max-w-[816px]">
      <h2 id="solution-services-heading" class="text-[26px] sm:text-[32px] font-bold leading-[42px] text-black">
        {{ $servicesHeading }}
      </h2>

      @if($servicesSubtitle !== '')
        <p class="text-base sm:text-[18px] leading-[26px] text-black font-normal">
          {{ $servicesSubtitle }}
        </p>
      @endif
    </div>

    <div class="flex flex-wrap gap-[14px] items-stretch justify-center w-full drop-shadow-[0_18px_36px_rgba(194,196,214,0.18)]">
      @foreach($serviceCards as $svc)
        <div class="solution-service-card w-full md:w-[calc(50%-7px)] lg:w-[calc(33.333%-10px)] max-w-[393px] flex">
          <div class="bg-[rgba(0,0,0,0.02)] p-[10px] rounded-[20px] w-full flex flex-col hover:-translate-y-1 transition-[transform,box-shadow] duration-300">
            <div class="relative flex-1 flex flex-col gap-[21px] items-start rounded-[10px] px-[21px] pt-[21px] pb-[34px] overflow-hidden text-left w-full"
                 style="background-image: linear-gradient(100deg, #EFFDFF 0%, #FFFFFF 35%, #FFFFFF 70%, #FFF7F0 100%);">
              <!-- Icon Badge -->
              <div class="size-[64px] rounded-full bg-white p-[7px] shadow-[0_7px_16px_rgba(170,186,213,0.22)] shrink-0">
                <div class="w-full h-full rounded-full bg-[rgba(237,127,34,0.1)] flex items-center justify-center p-[8px]">
                  @if(!empty($svc['icon']))
                    <x-image :src="$svc['icon']" :alt="$svc['title']" sizes="32px" :placeholder="false" class="w-[32px] h-[32px] object-contain" />
                  @else
                    <span class="w-4 h-4 rounded-full bg-[#ED7F22]"></span>
                  @endif
                </div>
              </div>

              <!-- Title + Description/Checklist -->
              <div class="flex flex-col gap-[15px] items-start w-full">
                <h3 class="text-[20px] sm:text-[21px] font-bold leading-[28px] text-black w-full">
                  {{ $svc['title'] }}
                </h3>

                @if($svc['description'] !== '')
                  <p class="text-[13px] font-normal leading-[19px] text-black w-full">
                    {{ $svc['description'] }}
                  </p>
                @endif

                @if(count($svc['checks']) > 0)
                  <ul class="flex flex-col gap-[8px] items-start w-full">
                    @foreach($svc['checks'] as $line)
                      <li class="flex items-start gap-[10px] w-full">
                        <img src="{{ theme_asset('images/solutions/service-check.svg') }}" alt="" class="w-[14px] h-[14px] shrink-0 mt-[2px]" />
                        <span class="text-[13px] font-normal leading-[18px] text-black flex-1">{{ $line }}</span>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </div>

              <!-- Bottom Gradient Accent Bar -->
              <div class="absolute bottom-0 inset-x-0 h-[10px]"
                   style="background: {{ $activeAccent === 'orange' ? 'linear-gradient(90deg, #ED7F22 30%, #FFB87D 90%)' : 'linear-gradient(90deg, #04B1C7 35%, #2296E4 70%)' }};"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ========================================================================= -->
<!-- 4. SHARED CONTACT FORM SECTION                                            -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
