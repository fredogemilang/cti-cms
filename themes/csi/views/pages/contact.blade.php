@extends('csi::layouts.app')

@section('content')
@php
    $heroBgImage = $page->block('hero_bg_image', '');
    $heroTitle = $page->block('hero_title', 'Let’s Connect and Grow Together');
    $heroSubtitle = $page->block(
        'hero_subtitle',
        'Want to learn more about our solutions, services, or how Converion can accelerate your business’s digital transformation? Our team is ready to support you every step of the way.'
    );
    $heroCta = $page->buttonBlock('hero_cta', [
        'text' => 'Contact Us',
        'url' => '#contact',
        'target' => '_self',
    ]);

    $officesHeading = $page->block('offices_heading', 'PT Converion Solution Informatica');
    $officesList = array_values(array_filter(
        $page->repeaterBlock('offices_list', []),
        fn ($item) => is_array($item) && trim((string) ($item['name'] ?? '')) !== ''
    ));

    if (empty($officesList)) {
        $officesList = [
            [
                'name' => 'Jakarta Office',
                'floor' => 'Centennial Tower 12th Floor',
                'address' => 'Jl. Jend. Gatot Subroto Kav. 24 - 25, Jakarta 12930, Indonesia',
                'phone' => 'P: +6221 806 22278',
                'embed_url' => 'https://maps.google.com/maps?q=Centennial+Tower+Jakarta+Jl.+Jend.+Gatot+Subroto+Kav.+24-25&t=&z=16&ie=UTF8&iwloc=&output=embed',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Centennial+Tower+Jakarta+Jl.+Jend.+Gatot+Subroto+Kav.+24-25',
            ],
            [
                'name' => 'Surabaya Office',
                'floor' => 'Graha SA Lantai 8, Suite 801',
                'address' => 'Jl. Raya Gubeng No. 19-21, Surabaya - Indonesia',
                'phone' => 'P: +6231 501 8978',
                'embed_url' => 'https://maps.google.com/maps?q=Graha+SA+Jl.+Raya+Gubeng+No.+19-21+Surabaya&t=&z=16&ie=UTF8&iwloc=&output=embed',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Graha+SA+Jl.+Raya+Gubeng+No.+19-21+Surabaya',
            ],
        ];
    }

    $contactHighlights = array_values(array_filter(
        $page->repeaterBlock('contact_highlights', []),
        fn ($item) => is_array($item) && trim((string) ($item['value'] ?? '')) !== ''
    ));

    if (empty($contactHighlights)) {
        $contactHighlights = [
            [
                'icon_type' => 'phone',
                'label' => 'Phone',
                'value' => '+6221 806 22278',
                'url' => 'tel:+622180622278',
            ],
            [
                'icon_type' => 'whatsapp',
                'label' => 'Converion Care Hub (WA)',
                'value' => '+62822 9992 2278',
                'url' => 'https://wa.me/6282299922278',
            ],
            [
                'icon_type' => 'email',
                'label' => 'Email',
                'value' => 'marketing@converion.com',
                'url' => 'mailto:marketing@converion.com',
            ],
        ];
    }

    $formatOfficeFloor = static function (string $floor): string {
        $escaped = e($floor);

        return preg_replace('/(\d+)(st|nd|rd|th)\b/i', '$1<sup class="text-[9px] font-bold leading-none">$2</sup>', $escaped) ?? $escaped;
    };
@endphp

<!-- ========================================================================= -->
<!-- 1. CONTACT HERO BANNER (Figma 722:6016)                                   -->
<!-- ========================================================================= -->
<section id="contact-hero" class="relative w-full overflow-hidden py-[56px] sm:py-[60px] min-h-[340px] sm:min-h-[354px] flex items-center bg-[#0B2239]">
  @if(!empty($heroBgImage))
    <img src="{{ resolve_block_asset($heroBgImage) }}"
         alt=""
         class="absolute inset-0 w-full h-full object-cover object-bottom pointer-events-none select-none z-0" />
  @endif

  {{-- Figma 722:6272 teal glass overlay --}}
  <div class="absolute inset-0 backdrop-blur-[5px] mix-blend-multiply pointer-events-none z-0"
       style="background: linear-gradient(0deg, #04B1C7 0%, rgba(4, 177, 199, 0) 100%);"></div>
  <div class="absolute inset-0 bg-[#081A2E]/35 pointer-events-none z-0"></div>

  <div class="relative z-10 max-w-[1440px] mx-auto w-full px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <div class="flex flex-col gap-[18px] items-center text-center w-full p-[10px]">
      <x-seo-breadcrumbs :entity="$page" class="text-white/75 mb-1 justify-center" />

      <h1 id="contact-hero-title"
          class="text-[32px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.2] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent max-w-[960px]">
        {{ $heroTitle }}
      </h1>

      @if(!empty($heroSubtitle))
        <p id="contact-hero-subtitle" class="text-base sm:text-[18px] leading-[26px] text-white font-normal max-w-[860px]">
          {!! nl2br(e($heroSubtitle)) !!}
        </p>
      @endif

      @if(!empty($heroCta['text']))
        <div class="pt-[10px] flex justify-center">
          <a href="{{ $heroCta['url'] ?? '#contact' }}" target="{{ $heroCta['target'] ?? '_self' }}"
             class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-semibold text-[16px] leading-none px-[21px] py-[16px] rounded-[6px] transition-colors inline-block shadow-sm">
            {{ $heroCta['text'] }}
          </a>
        </div>
      @endif
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 2. PT CONVERION OFFICES & INTERACTIVE GOOGLE MAPS (Figma 722:5702)        -->
<!-- ========================================================================= -->
<section id="contact-offices"
         class="relative bg-white overflow-x-clip py-[48px] sm:py-[60px]"
         x-data="{
            activeOffice: 0,
            offices: {{ \Illuminate\Support\Js::from($officesList) }},
            get currentEmbedUrl() {
              const item = this.offices[this.activeOffice] || this.offices[0];
              return item && item.embed_url ? item.embed_url : '';
            }
         }">
  <!-- Decorative Arc Lines matching Figma 722:5635 / 722:5662 -->
  <img src="{{ theme_asset('images/contact/arc-right.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[15px] w-[140px] h-[619px] z-0" />
  <img src="{{ theme_asset('images/contact/arc-left.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-[35px] w-[136px] h-[604px] rotate-180 z-0" />

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] py-[10px]">
    <div class="flex flex-col gap-[10px] items-start w-full">
      @if(!empty($officesHeading))
        <h2 id="contact-offices-heading" class="text-[21px] font-bold leading-[28px] text-black">
          {{ $officesHeading }}
        </h2>
      @endif

      <!-- Interactive Google Maps Iframe -->
      <div class="w-full h-[300px] sm:h-[355px] rounded-[5px] overflow-hidden bg-[#F0F5FF] relative">
        <template x-if="currentEmbedUrl">
          <iframe
            :src="currentEmbedUrl"
            :title="(offices[activeOffice] && offices[activeOffice].name) ? offices[activeOffice].name : 'Office Map'"
            class="w-full h-full border-0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen></iframe>
        </template>
      </div>

      <!-- Office Address Cards -->
      @if(count($officesList) > 0)
        <div class="grid grid-cols-1 {{ count($officesList) > 1 ? 'md:grid-cols-2' : '' }} gap-[10px] w-full">
          @foreach($officesList as $idx => $office)
            <div @click="activeOffice = {{ $idx }}"
                 role="button"
                 tabindex="0"
                 @keydown.enter="activeOffice = {{ $idx }}"
                 @keydown.space.prevent="activeOffice = {{ $idx }}"
                 class="bg-[#F0F5FF] hover:bg-[#E8F0FE] rounded-[10px] px-[21px] py-[18px] flex gap-[15px] items-start cursor-pointer transition-colors text-left">
              <!-- Figma 32x32 White Pin Badge (772:7294) -->
              <div class="w-[32px] h-[32px] bg-white rounded-[5px] shrink-0 relative overflow-hidden flex items-center justify-center" aria-hidden="true">
                <img src="{{ theme_asset('images/contact/pin-base.svg') }}" alt="" class="absolute bottom-[3.96px] left-1/2 -translate-x-1/2 w-[12.24px] h-[3.39px]" />
                <img src="{{ theme_asset('images/contact/pin-body.svg') }}" alt="" class="absolute top-[4.06px] left-1/2 -translate-x-1/2 w-[15.58px] h-[22.16px]" />
                <img src="{{ theme_asset('images/contact/pin-dot.svg') }}" alt="" class="absolute top-[8px] left-1/2 -translate-x-1/2 w-[6.58px] h-[6.58px]" />
              </div>

              <div class="flex-1 min-w-0 flex flex-col gap-[10px] text-black">
                <h3 class="text-[16px] font-bold leading-none text-black">
                  {{ $office['name'] ?? '' }}
                </h3>

                <div class="text-[13px] leading-[18px] text-black">
                  @if(!empty($office['floor']))
                    <p class="font-bold leading-[21px]">{!! $formatOfficeFloor((string) $office['floor']) !!}</p>
                  @endif
                  @if(!empty($office['address']))
                    <p class="font-normal leading-[18px]">{{ $office['address'] }}</p>
                  @endif
                  @if(!empty($office['phone']))
                    <p class="font-normal leading-[18px]">{{ $office['phone'] }}</p>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 3. CONTACT CHANNELS GRADIENT STRIP (Figma 722:6186)                       -->
<!-- ========================================================================= -->
@include('csi::partials.contact-strip', ['contactHighlights' => $contactHighlights])

<!-- ========================================================================= -->
<!-- 4. SHARED CONTACT FORM FOOTER SECTION (Figma 722:5704)                    -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
