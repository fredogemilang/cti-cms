@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : 'About Us — ' . setting('site_name', 'Central Data Technology'))

@section('content')
  @php
    $heroImg = $page?->block('hero_bg_image');
    $heroImgUrl = $heroImg ? (str_starts_with($heroImg, 'http') || str_starts_with($heroImg, 'themes/') || str_starts_with($heroImg, 'assets/') ? asset($heroImg) : asset('storage/' . $heroImg)) : asset('themes/cdt/assets/about-us-bg.webp');
  @endphp

  <!-- About Us Hero V2: Full width dark immersive -->
  <section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
    <!-- Immersive background -->
    <div class="absolute inset-0 z-0">
      <x-image :src="$heroImgUrl" class="w-full h-full object-cover" alt="About Us" />
      <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
    </div>
    
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
      <div class="max-w-3xl text-white">
        <!-- Breadcrumb -->
        <div class="mb-8 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
          <x-seo-breadcrumbs :entity="$page" />
        </div>

        <div class="overflow-hidden mb-2">
          <h2 class="text-xl md:text-2xl font-light" data-gsap="fade-up">{{ $page?->block('hero_subtitle_small') ?? 'Company' }}</h2>
        </div>
        <div class="overflow-hidden">
          <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight" data-gsap="fade-up" data-gsap-delay="0.1">
            {!! $page?->block('hero_title') ?? 'About Us' !!}
          </h1>
        </div>
      </div>
    </div>
  </section>

  <!-- About Intro Section -->
  <section class="py-16 md:py-24 bg-white relative">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl border-l-4 border-primary pl-8 space-y-6" data-gsap="fade-up">
        @if($introContent = $page?->block('intro_content'))
          <div class="text-gray-700 text-lg md:text-xl leading-relaxed font-light space-y-6">
            {!! $introContent !!}
          </div>
        @else
          <p class="text-gray-700 text-lg md:text-xl leading-relaxed font-light">
            As a part of CTI Group, PT. Central Data Technology (CDT) was established in 2011 as one of CTI’s subsidiaries. With more than ten years of experience in the middle of high business demand and rapid market changes, CDT has transformed into an efficient company with future-oriented technology and continues to innovate to build an ecosystem that grows sustainability and transparently.
          </p>
          <p class="text-gray-700 text-lg md:text-xl leading-relaxed font-light">
            CDT focuses on three main solution areas in response to technological advances: Cloud, Security, and Observability. We call it the Three Waves of business growth catalyst.
          </p>
        @endif
      </div>
    </div>
  </section>

  <!-- Vision & Mission V2 Section -->
  <section class="py-24 md:py-32 bg-zinc-100 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-zinc-50 rounded-full pointer-events-none -translate-y-1/2 translate-x-1/4"></div>

    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Vision Block -->
      <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 mb-24 items-center" data-gsap="fade-up">
        <div class="w-full lg:w-5/12">
          <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {{ $page?->block('vision_title_prefix') ?? 'Our' }}<br>
            <span class="font-bold text-dark">{{ $page?->block('vision_title_main') ?? 'Vision' }}</span>
          </h2>
          <div class="w-16 h-1 bg-primary mt-6" data-gsap="line-grow"></div>
        </div>
        <div class="w-full lg:w-7/12">
          <div class="bg-white border border-gray-100 rounded-3xl p-10 md:p-14 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] relative overflow-hidden group hover:border-primary/20 hover:shadow-[0_20px_50px_-12px_rgba(189,42,42,0.1)] transition-all duration-500">
            <div class="absolute -right-6 -top-10 text-9xl text-gray-50 font-black group-hover:scale-110 group-hover:text-primary/5 transition-all duration-700">V</div>
            <div class="text-2xl md:text-3xl lg:text-4xl leading-tight font-light text-gray-800 relative z-10">
              {!! $page?->block('vision_text') ?? 'To be <span class="font-bold text-primary">The Most Reliable</span> IT Transformation Solution Partner in Southeast Asia' !!}
            </div>
          </div>
        </div>
      </div>

      <!-- Mission Block -->
      <div class="flex flex-col lg:flex-row-reverse gap-12 lg:gap-20 items-center" data-gsap="fade-up" data-gsap-delay="0.1">
        <div class="w-full lg:w-5/12">
          <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {{ $page?->block('mission_title_prefix') ?? 'Our' }}<br>
            <span class="font-bold text-dark">{{ $page?->block('mission_title_main') ?? 'Mission' }}</span>
          </h2>
          <div class="w-16 h-1 bg-primary mt-6" data-gsap="line-grow"></div>
        </div>
        <div class="w-full lg:w-7/12 grid grid-cols-1 md:grid-cols-2 gap-6">
          @php
            $missionList = $page?->repeaterBlock('mission_list') ?? [];
          @endphp
          @if(count($missionList))
            @foreach($missionList as $index => $m)
              <div class="bg-white border border-gray-100 rounded-3xl p-10 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] group hover:-translate-y-2 hover:shadow-[0_20px_50px_-12px_rgba(189,42,42,0.1)] transition-all duration-500 h-full flex flex-col">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-primary mb-8 border border-red-100 shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $index === 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' }}"/>
                  </svg>
                </div>
                <div class="text-xl leading-relaxed text-gray-600 font-light mt-auto relative z-10">
                  {!! $m['text'] ?? $m['content'] ?? '' !!}
                </div>
              </div>
            @endforeach
          @else
            <div class="bg-white border border-gray-100 rounded-3xl p-10 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] group hover:-translate-y-2 hover:shadow-[0_20px_50px_-12px_rgba(189,42,42,0.1)] transition-all duration-500 h-full flex flex-col">
              <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-primary mb-8 border border-red-100 shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
              </div>
              <p class="text-xl leading-relaxed text-gray-600 font-light mt-auto relative z-10">
                Providing Expertise to Enhance Customer's <span class="font-bold text-gray-900 group-hover:text-primary transition-colors duration-300">Continuous Growth</span>
              </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-3xl p-10 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] group hover:-translate-y-2 hover:shadow-[0_20px_50px_-12px_rgba(189,42,42,0.1)] transition-all duration-500 h-full flex flex-col">
              <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-primary mb-8 border border-red-100 shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              </div>
              <p class="text-xl leading-relaxed text-gray-600 font-light mt-auto relative z-10">
                Empowering Individual Development Through <span class="font-bold text-gray-900 group-hover:text-primary transition-colors duration-300">Intrapreneurship</span>
              </p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  @include('cdt::partials.contact-section')
@endsection
