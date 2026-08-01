@extends('cdt::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
  <!-- Hero Section -->
  <section class="relative bg-zinc-900 text-white py-24 md:py-32 overflow-hidden">
    <!-- Background Image with Dark Overlay -->
    <div class="absolute inset-0 z-0">
      @php
        $heroBg = $page->getBlockValue('hero_bg_image', 'themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
        $heroBgUrl = resolve_block_asset($heroBg);
      @endphp
      <img src="{{ $heroBgUrl }}" alt="{{ $page->getBlockValue('hero_title', 'About Management') }} Background" title="{{ $page->getBlockValue('hero_title', 'About Management') }}" class="w-full h-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/90 via-zinc-900/80 to-zinc-900/40"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl text-white">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-white/70 mb-10" aria-label="Breadcrumb" data-gsap="fade-in">
          <a href="{{ localized_url('/') }}" title="Home" aria-label="Home" class="hover:text-white transition-colors">Home</a>
          <svg class="w-3 h-3 text-white/40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <span class="text-white font-bold" aria-current="page">{{ $page->getBlockValue('hero_title', 'About Management') }}</span>
        </nav>

        <div class="overflow-hidden mb-2">
          <span class="block text-xl md:text-2xl font-light text-primary tracking-wide uppercase font-semibold" data-gsap="fade-up">
            {{ $page->getBlockValue('hero_subtitle_small', 'Company') }}
          </span>
        </div>
        <div class="overflow-hidden">
          <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight" data-gsap="fade-up" data-gsap-delay="0.1">
            {{ $page->getBlockValue('hero_title', 'About Management') }}
          </h1>
        </div>
      </div>
    </div>
  </section>

  <!-- Intro Overview Section -->
  @php
    $introContent = $page->getBlockValue('intro_content');
  @endphp
  @if(!empty($introContent))
  <section class="py-16 bg-white border-b border-zinc-100">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl text-zinc-700 text-lg md:text-xl leading-relaxed font-light" data-gsap="fade-up">
        {!! $introContent !!}
      </div>
    </div>
  </section>
  @endif

  <!-- Executive Leadership Grid Section -->
  @php
    $executives = $page->getBlockValue('management_list', []);
    if (is_string($executives)) {
        $executives = json_decode($executives, true) ?? [];
    }
  @endphp
  <section class="py-24 bg-zinc-50/60" id="executive-team">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="flex flex-col lg:flex-row gap-8 mb-16 items-start lg:items-end justify-between">
        <div>
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {!! $page->getBlockValue('management_title_prefix', 'Our') !!}<br>
            <span class="font-bold text-dark">{!! $page->getBlockValue('management_title_main', 'Management') !!}</span>
          </h2>
          <div class="h-1 bg-primary mt-4 w-20" data-gsap="line-grow"></div>
        </div>
        <p class="text-zinc-500 text-base max-w-md" data-gsap="fade-up">
          Experienced IT leaders driving strategic vision, commercial excellence, and technological innovation.
        </p>
      </div>

      <!-- Executives Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
        @foreach($executives as $index => $exec)
          @php
            $photoPath = $exec['photo'] ?? 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg';
            $photoUrl = resolve_block_asset($photoPath);
            $name = $exec['name'] ?? 'Executive';
            $position = $exec['position'] ?? '';
            $bio = $exec['bio'] ?? '';
            $linkedin = $exec['linkedin_url'] ?? '';
            $delay = ($index % 2) * 0.15;
          @endphp
          <div class="bg-white rounded-3xl p-8 border border-zinc-200/80 shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col justify-between group" data-gsap="fade-up" data-gsap-delay="{{ $delay }}">
            <div>
              <!-- Executive Header Info -->
              <div class="flex items-start gap-6 mb-6 pb-6 border-b border-zinc-100">
                <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl overflow-hidden shrink-0 bg-zinc-100 border border-zinc-200/60 shadow-inner">
                  <img src="{{ $photoUrl }}" alt="{{ $name }}" title="{{ $name }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="flex-1 min-w-0 pt-2">
                  <div class="flex items-start justify-between gap-2">
                    <h3 class="text-xl md:text-2xl font-bold text-zinc-900 leading-snug group-hover:text-primary transition-colors">
                      {{ $name }}
                    </h3>
                    @if(!empty($linkedin) && $linkedin !== '#')
                      <a href="{{ $linkedin }}" title="LinkedIn {{ $name }}" aria-label="LinkedIn {{ $name }}" target="_blank" rel="noopener" class="w-9 h-9 bg-zinc-100 rounded-full flex items-center justify-center text-zinc-500 hover:bg-primary hover:text-white transition-all shrink-0">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                      </a>
                    @endif
                  </div>
                  <p class="text-sm font-semibold text-primary mt-1.5 uppercase tracking-wider">
                    {{ $position }}
                  </p>
                </div>
              </div>

              <!-- Executive Bio Text -->
              <div class="text-zinc-600 text-sm md:text-base leading-relaxed space-y-3 font-light">
                {!! $bio !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </section>

  <!-- Shared Contact Section Partial -->
  @include('cdt::partials.contact-section')
@endsection
