@extends('cdt::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
  <!-- Management Hero V2: Full width dark immersive -->
  <section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
    <!-- Immersive background -->
    <div class="absolute inset-0 z-0">
      @php
        $heroBg = $page->getBlockValue('hero_bg_image', 'themes/cdt/assets/banner_hero-DHYDqbF8.jpg');
        $heroBgUrl = resolve_block_asset($heroBg);
      @endphp
      <x-image :src="$heroBgUrl" alt="{{ $page->getBlockValue('hero_title', 'Management') }} Banner" title="{{ $page->getBlockValue('hero_title', 'Management') }}" class="w-full h-full object-cover object-[right_center]" />
      <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4" /></div>
    </div>
    
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
      <div class="max-w-3xl text-white">
        <!-- Breadcrumb -->
        <x-seo-breadcrumbs :entity="$page" class="text-white/70 mb-10" />

        <div class="overflow-hidden mb-2"><span class="block text-xl md:text-2xl font-light" data-gsap="fade-up">{{ $page->getBlockValue('hero_subtitle_small', 'About Us') }}</span></div>
        <div class="overflow-hidden"><h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight" data-gsap="fade-up" data-gsap-delay="0.1">
          {{ $page->getBlockValue('hero_title', 'Management') }}
        </h1></div>
      </div>
    </div>
  </section>

  <!-- Management Section V2: Premium Editorial Layout -->
  <section class="pt-16 pb-16 md:pt-24 md:pb-24 bg-white relative overflow-hidden selection:bg-primary selection:text-white">

    <!-- Subtle Background Element -->
    <div
      class="absolute top-0 right-0 w-1/2 h-full bg-gray-50/50 skew-x-12 transform -translate-x-10 pointer-events-none hidden lg:block">
    </div>

    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">

      <!-- ==============================================
           SECTION 1: BOARD OF DIRECTORS
           ============================================== -->
      @php
        $directors = $page->getBlockValue('directors_list', []);
        if (is_string($directors)) {
            $directors = json_decode($directors, true) ?? [];
        }
      @endphp
      <div class="mb-24 md:mb-32">
        <div class="mb-16" data-gsap="fade-up">
          <h2 class="text-4xl md:text-5xl leading-tight mb-4">
            <span class="font-light text-zinc-500 block">{!! $page->getBlockValue('directors_title_prefix', 'Board of') !!}</span>
            <span class="font-extrabold text-gray-900 block mt-1">{!! $page->getBlockValue('directors_title_main', 'Directors') !!}</span>
          </h2>
          <div class="w-16 h-1.5 bg-primary"></div>
        </div>

        <div class="space-y-24 md:space-y-32">
          @foreach($directors as $index => $dir)
            @php
              $photoPath = $dir['photo'] ?? 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg';
              $photoUrl = resolve_block_asset($photoPath);
              $name = $dir['name'] ?? '';
              $position = $dir['position'] ?? '';
              $bio = $dir['bio'] ?? '';
              $linkedin = $dir['linkedin_url'] ?? '';
              $isEven = $index % 2 === 1;
            @endphp

            @if(!$isEven)
              <!-- Lugas Mondo Satrio (Image Left) -->
              <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16 lg:gap-20 group" data-gsap="fade-up">
                <!-- Image Column -->
                <div class="w-full relative" style="max-width: 340px; flex-shrink: 0;">
                  <div
                    class="relative w-full aspect-[1025/1536] rounded-2xl overflow-hidden shadow-2xl transition-transform duration-700 group-hover:-translate-y-2">
                    <x-image :src="$photoUrl"
                      alt="{{ $name }}" title="{{ $name }}" class="absolute inset-0 w-full h-full object-cover object-top" />
                    <div
                      class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                    </div>
                  </div>
                  <!-- Decorative Dots -->
                  <div
                    class="absolute -bottom-4 right-0 w-24 h-24 bg-[radial-gradient(#ED1C24_2px,transparent_2px)] [background-size:10px_10px] -z-10 opacity-30 hidden lg:block">
                  </div>
                </div>
                <!-- Text Column -->
                <div class="w-full md:flex-1 flex flex-col justify-center">
                  <div class="mb-6">
                    <h3 class="text-3xl md:text-5xl font-bold text-gray-900 mb-2">{{ $name }}</h3>
                    <p class="text-lg md:text-xl text-primary font-medium tracking-wide uppercase">{{ $position }}</p>
                  </div>
                  <div class="text-base md:text-lg text-gray-600 leading-relaxed space-y-6">
                    {!! $bio !!}
                  </div>
                  @if(!empty($linkedin) && $linkedin !== '#')
                  <div class="mt-10">
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}"
                      class="inline-flex items-center gap-3 text-gray-500 hover:text-[#0A66C2] transition-colors font-medium">
                      <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                          d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                      </svg>
                      <span>Connect on LinkedIn</span>
                    </a>
                  </div>
                  @endif
                </div>
              </div>
            @else
              <!-- Fenny (Image Right) -->
              <div class="flex flex-col md:flex-row-reverse items-center gap-10 md:gap-16 lg:gap-20 group"
                data-gsap="fade-up">
                <!-- Image Column -->
                <div class="w-full relative" style="max-width: 340px; flex-shrink: 0;">
                  <div
                    class="relative w-full aspect-[1025/1536] rounded-2xl overflow-hidden shadow-2xl transition-transform duration-700 group-hover:-translate-y-2">
                    <x-image :src="$photoUrl"
                      alt="{{ $name }}" title="{{ $name }}" class="absolute inset-0 w-full h-full object-cover object-top" />
                    <div
                      class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                    </div>
                  </div>
                  <!-- Decorative Dots -->
                  <div
                    class="absolute -bottom-4 left-0 w-24 h-24 bg-[radial-gradient(#ED1C24_2px,transparent_2px)] [background-size:10px_10px] -z-10 opacity-30 hidden lg:block">
                  </div>
                </div>
                <!-- Text Column -->
                <div class="w-full md:flex-1 flex flex-col justify-center text-left md:text-right">
                  <div class="mb-6">
                    <h3 class="text-3xl md:text-5xl font-bold text-gray-900 mb-2">{{ $name }}</h3>
                    <p class="text-lg md:text-xl text-primary font-medium tracking-wide uppercase">{{ $position }}</p>
                  </div>
                  <div class="text-base md:text-lg text-gray-600 leading-relaxed space-y-6">
                    {!! $bio !!}
                  </div>
                  @if(!empty($linkedin) && $linkedin !== '#')
                  <div class="mt-10 flex md:justify-end">
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}"
                      class="inline-flex flex-row-reverse items-center gap-3 text-gray-500 hover:text-[#0A66C2] transition-colors font-medium">
                      <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                          d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                      </svg>
                      <span>Connect on LinkedIn</span>
                    </a>
                  </div>
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>


      <!-- ==============================================
           SECTION 2: EXECUTIVE MANAGEMENT
           ============================================== -->
      @php
        $executives = $page->getBlockValue('management_list', []);
        if (is_string($executives)) {
            $executives = json_decode($executives, true) ?? [];
        }
      @endphp
      @if(!empty($executives))
      <div class="pt-16">
        <div class="mb-16" data-gsap="fade-up">
          <h2 class="text-4xl md:text-5xl leading-tight mb-4">
            <span class="font-light text-zinc-500 block">{!! $page->getBlockValue('executive_title_prefix', 'Executive') !!}</span>
            <span class="font-extrabold text-gray-900 block mt-1">{!! $page->getBlockValue('executive_title_main', 'Management') !!}</span>
          </h2>
          <div class="w-16 h-1.5 bg-primary"></div>
        </div>

        <!-- 2-Column Masonry/Grid approach for Managers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-16">
          @foreach($executives as $index => $exec)
            @php
              $photoPath = $exec['photo'] ?? 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg';
              $photoUrl = resolve_block_asset($photoPath);
              $name = $exec['name'] ?? '';
              $position = $exec['position'] ?? '';
              $bio = $exec['bio'] ?? '';
              $linkedin = $exec['linkedin_url'] ?? '';
              $delay = ($index % 2) * 0.1;
            @endphp

            <div class="flex flex-col sm:flex-row gap-6 group" data-gsap="fade-up" data-gsap-delay="{{ $delay }}">
              <div class="w-full" style="max-width: 220px; flex-shrink: 0;">
                <div
                  class="w-full aspect-[1025/1536] rounded-xl overflow-hidden shadow-lg transition-transform duration-500 group-hover:-translate-y-1">
                  <x-image :src="$photoUrl"
                    alt="{{ $name }}" title="{{ $name }}" class="w-full h-full object-cover object-top" />
                </div>
              </div>
              <div class="w-full sm:flex-1 flex flex-col pt-2" />
                <h3 class="text-2xl font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">{{ $name }}</h3>
                <p class="text-sm text-primary font-bold uppercase tracking-wider mb-4">{{ $position }}</p>
                <div class="text-gray-600 text-sm leading-relaxed space-y-3 mb-6">
                  {!! $bio !!}
                </div>
                @if(!empty($linkedin) && $linkedin !== '#')
                <div class="mt-auto">
                  <a href="{{ $linkedin }}" target="_blank" rel="noopener" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}"
                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:text-white hover:bg-[#0A66C2] transition-colors"><svg
                      class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                      <path
                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg></a>
                </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
      @endif

    </div>
  </section>

  <!-- Shared Contact Partial -->
  @include('cdt::partials.contact-section')
@endsection
