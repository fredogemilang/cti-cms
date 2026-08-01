@extends('cdt::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
  <!-- Hero Section matching management.html -->
  <section class="hero-section relative min-h-[380px] md:min-h-[440px] flex items-center bg-zinc-900 text-white overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
      @php
        $heroBg = $page->getBlockValue('hero_bg_image', 'themes/cdt/assets/banner_hero-DHYDqbF8.jpg');
        $heroBgUrl = resolve_block_asset($heroBg);
      @endphp
      <img src="{{ $heroBgUrl }}" alt="{{ $page->getBlockValue('hero_title', 'Management') }} Banner" title="{{ $page->getBlockValue('hero_title', 'Management') }}" class="hero-bg-img w-full h-full object-cover origin-center">
    </div>

    <!-- Red Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full md:w-2/3 lg:w-3/4"></div>

    <div class="relative z-10 mx-auto max-w-[1400px] w-full px-4 sm:px-6 lg:px-8 py-16">
      <div class="max-w-2xl text-white">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-white/80 mb-8" aria-label="Breadcrumb" data-gsap="fade-in">
          <a href="{{ localized_url('/') }}" title="Home" aria-label="Home" class="hover:text-white transition-colors">Home</a>
          <svg class="w-3 h-3 text-white/50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <a href="{{ localized_url('/about-us') }}" title="About Us" aria-label="About Us" class="hover:text-white transition-colors">About Us</a>
          <svg class="w-3 h-3 text-white/50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <span class="text-white font-bold" aria-current="page">{{ $page->getBlockValue('hero_title', 'Management') }}</span>
        </nav>

        <div class="overflow-hidden">
          <span class="block text-xl md:text-2xl font-light mb-2 text-white/90" data-gsap="fade-up">
            {{ $page->getBlockValue('hero_subtitle_small', 'About Us') }}
          </span>
          <h1 class="text-4xl md:text-5xl lg:text-[56px] font-extrabold leading-tight tracking-tight" data-gsap="fade-up" data-gsap-delay="0.1">
            {{ $page->getBlockValue('hero_title', 'Management') }}
          </h1>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 1: Board of Directors -->
  @php
    $directors = $page->getBlockValue('directors_list', []);
    if (is_string($directors)) {
        $directors = json_decode($directors, true) ?? [];
    }
  @endphp
  <section class="py-24 bg-white" id="board-of-directors">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      
      <!-- Section Title -->
      <div class="mb-16" data-gsap="fade-up">
        <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight">
          {!! $page->getBlockValue('directors_title_prefix', 'Board of') !!}
        </h2>
        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mt-1">
          {!! $page->getBlockValue('directors_title_main', 'Directors') !!}
        </h2>
        <div class="w-16 h-1.5 bg-primary mt-4" data-gsap="line-grow"></div>
      </div>

      <!-- Directors Alternating List -->
      <div class="space-y-24">
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

          <div class="flex flex-col {{ $isEven ? 'lg:flex-row-reverse' : 'lg:flex-row' }} gap-12 lg:gap-16 items-center" data-gsap="fade-up">
            <!-- Photo Column -->
            <div class="w-full lg:w-[35%] shrink-0 relative group flex justify-center">
              <!-- Decorative Dot Matrix Accent -->
              <div class="absolute -bottom-6 {{ $isEven ? '-left-6' : '-right-6' }} w-32 h-32 bg-[radial-gradient(#ED1C24_2px,transparent_2px)] [background-size:12px_12px] opacity-70 -z-10 hidden sm:block"></div>
              
              <div class="relative aspect-[1025/1536] w-full max-w-[360px] rounded-2xl overflow-hidden shadow-2xl border border-zinc-100 transition-transform duration-500 group-hover:-translate-y-2">
                <img src="{{ $photoUrl }}" alt="{{ $name }}" title="{{ $name }}" class="w-full h-full object-cover object-top">
              </div>
            </div>

            <!-- Content Column -->
            <div class="w-full lg:w-[65%] flex flex-col justify-center">
              <div class="flex items-center gap-4 mb-2">
                <h3 class="text-3xl md:text-4xl font-extrabold text-gray-900">
                  {{ $name }}
                </h3>
                @if(!empty($linkedin) && $linkedin !== '#')
                  <a href="{{ $linkedin }}" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-[#0A66C2] transition-colors shrink-0">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                  </a>
                @endif
              </div>

              <p class="text-base font-bold text-primary uppercase tracking-wider mb-6">
                {{ $position }}
              </p>

              <div class="text-zinc-600 text-base md:text-lg leading-relaxed space-y-4 font-light">
                {!! $bio !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </section>

  <!-- Section 2: Executive Management Grid -->
  @php
    $executives = $page->getBlockValue('management_list', []);
    if (is_string($executives)) {
        $executives = json_decode($executives, true) ?? [];
    }
  @endphp
  @if(!empty($executives))
  <section class="py-24 bg-zinc-50/60 border-t border-zinc-100" id="executive-management">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      
      <!-- Section Title -->
      <div class="mb-16" data-gsap="fade-up">
        <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight">
          {!! $page->getBlockValue('executive_title_prefix', 'Executive') !!}
        </h2>
        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mt-1">
          {!! $page->getBlockValue('executive_title_main', 'Management') !!}
        </h2>
        <div class="w-16 h-1.5 bg-primary mt-4" data-gsap="line-grow"></div>
      </div>

      <!-- Grid Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        @foreach($executives as $index => $exec)
          @php
            $photoPath = $exec['photo'] ?? 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg';
            $photoUrl = resolve_block_asset($photoPath);
            $name = $exec['name'] ?? '';
            $position = $exec['position'] ?? '';
            $bio = $exec['bio'] ?? '';
            $linkedin = $exec['linkedin_url'] ?? '';
            $delay = ($index % 2) * 0.15;
          @endphp

          <div class="flex flex-col sm:flex-row gap-6 lg:gap-8 group bg-white p-6 md:p-8 rounded-3xl border border-zinc-200/80 shadow-sm hover:shadow-xl transition-all duration-500" data-gsap="fade-up" data-gsap-delay="{{ $delay }}">
            <!-- Photo Column -->
            <div class="w-full sm:w-[38%] shrink-0 relative aspect-[1025/1536] max-h-[320px] sm:max-h-none rounded-2xl overflow-hidden shadow-md group-hover:-translate-y-1 transition-transform duration-500">
              <img src="{{ $photoUrl }}" alt="{{ $name }}" title="{{ $name }}" class="w-full h-full object-cover object-top">
            </div>

            <!-- Content Column -->
            <div class="w-full sm:w-[62%] flex flex-col justify-start pt-1">
              <div class="flex items-center justify-between gap-2 mb-1">
                <h3 class="text-xl md:text-2xl font-extrabold text-gray-900 group-hover:text-primary transition-colors leading-snug">
                  {{ $name }}
                </h3>
                @if(!empty($linkedin) && $linkedin !== '#')
                  <a href="{{ $linkedin }}" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-[#0A66C2] transition-colors shrink-0">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                  </a>
                @endif
              </div>

              <p class="text-xs md:text-sm font-bold text-primary uppercase tracking-wider mb-4">
                {{ $position }}
              </p>

              <div class="text-zinc-600 text-sm leading-relaxed font-light">
                {!! $bio !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </section>
  @endif

  <!-- Shared Contact Partial -->
  @include('cdt::partials.contact-section')
@endsection
