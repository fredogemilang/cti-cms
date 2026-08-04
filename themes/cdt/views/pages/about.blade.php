@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : 'About Us — ' . setting('site_name', 'Central Data Technology'))

@section('content')
  @php
    $heroImg = $page?->block('hero_bg_image');
    $heroImgUrl = $heroImg ? (str_starts_with($heroImg, 'http') || str_starts_with($heroImg, 'themes/') || str_starts_with($heroImg, 'assets/') ? asset($heroImg) : asset('storage/' . $heroImg)) : asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp');
  @endphp

  <!-- About Us Hero V2: Full width dark immersive -->
  <section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
    <!-- Immersive background -->
    <div class="absolute inset-0 z-0">
      <x-image :src="$heroImgUrl" class="w-full h-full object-cover opacity-60" alt="About Us" />
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
            {{ $page?->block('vision_title_prefix') ?? 'Our' }} <br>
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
            {{ $page?->block('mission_title_prefix') ?? 'Our' }} <br>
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

  <!-- Core Values V2 Section -->
  <section class="py-24 md:py-32 bg-white relative overflow-hidden">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="text-center mb-16 lg:mb-24" data-gsap="fade-up">
        <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-6">
          {{ $page?->block('values_title_prefix') ?? 'Our' }} <span class="font-bold text-dark">{{ $page?->block('values_title_main') ?? 'Values' }}</span>
        </h2>
        <div class="w-24 h-1 bg-primary mx-auto"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @php
          $valuesList = $page?->repeaterBlock('values_list') ?? [];
        @endphp
        @foreach($valuesList as $index => $v)
          @php
            $vImg = $v['image'] ?? null;
            $vImgUrl = $vImg ? (str_starts_with($vImg, 'http') || str_starts_with($vImg, 'themes/') || str_starts_with($vImg, 'assets/') ? asset($vImg) : asset('storage/' . $vImg)) : asset('themes/cdt/assets/cdt_integrity-CJ_Wn1Pj.jpg');
          @endphp
          <div class="bg-primary text-white border border-red-800/50 rounded-3xl p-6 shadow-2xl group hover:-translate-y-2 hover:shadow-[0_30px_60px_-15px_rgba(189,42,42,0.5)] transition-all duration-500 h-full flex flex-col" data-gsap="fade-up" data-gsap-delay="{{ $index * 0.1 }}">
            <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-6 relative">
              <x-image :src="$vImgUrl" alt="{{ $v['title'] ?? 'Value' }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" />
              <div class="absolute inset-0 bg-primary/20 mix-blend-multiply group-hover:bg-transparent transition-colors duration-500"></div>
              <div class="absolute top-4 right-4 w-12 h-12 bg-white/90 backdrop-blur-md rounded-xl flex items-center justify-center text-primary font-black text-xl shadow-sm">
                {{ sprintf('%02d', $index + 1) }}
              </div>
            </div>
            <div class="px-2 pb-4 flex flex-col flex-grow mt-2">
              <h3 class="text-2xl font-bold mb-4 text-white transition-colors duration-300">{{ $v['title'] ?? '' }}</h3>
              <p class="text-white/90 font-light leading-relaxed">
                {{ $v['description'] ?? $v['text'] ?? '' }}
              </p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Awards & Certifications V2 Section -->
  <section class="py-24 md:py-32 bg-zinc-50 relative overflow-hidden border-y border-zinc-200" x-data="{ activeTab: 'awards' }">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-red-50/50 blur-3xl"></div>
      <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] rounded-full bg-zinc-50/80 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="text-center mb-12" data-gsap="fade-up">
        <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-6">
          {{ $page?->block('awards_title_prefix') ?? 'Our' }} <span class="font-bold text-dark">{{ $page?->block('awards_title_main') ?? 'Awards & Certifications' }}</span>
        </h2>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
      </div>

      <!-- Premium Pill Tabs -->
      <div class="flex justify-center mb-20" data-gsap="fade-up" data-gsap-delay="0.1">
        <div class="inline-flex bg-zinc-100/80 backdrop-blur-sm p-1.5 rounded-full relative shadow-inner border border-zinc-200/50">
          <button @click="activeTab = 'awards'" 
            :class="activeTab === 'awards' ? 'bg-white text-gray-900 shadow-[0_2px_10px_rgba(0,0,0,0.08)]' : 'text-gray-500 hover:text-gray-800'"
            class="px-8 py-3 rounded-full font-bold text-sm md:text-base transition-all duration-500 relative min-w-[160px]">
            {{ t('about.awards_tab', 'Awards') }}
          </button>
          <button @click="activeTab = 'certification'" 
            :class="activeTab === 'certification' ? 'bg-white text-gray-900 shadow-[0_2px_10px_rgba(0,0,0,0.08)]' : 'text-gray-500 hover:text-gray-800'"
            class="px-8 py-3 rounded-full font-bold text-sm md:text-base transition-all duration-500 relative min-w-[160px]">
            {{ t('about.certifications_tab', 'Certifications') }}
          </button>
        </div>
      </div>

      <!-- Awards Grid -->
      <div x-show="activeTab === 'awards'" x-transition:enter="transition ease-out duration-500" class="grid grid-cols-1 md:grid-cols-3 gap-8 xl:gap-12">
        @php
          $awardsList = $page?->repeaterBlock('awards_list') ?? [];
        @endphp
        @foreach($awardsList as $award)
          @php
            $aImg = $award['image'] ?? null;
            $aImgUrl = $aImg ? (str_starts_with($aImg, 'http') || str_starts_with($aImg, 'themes/') || str_starts_with($aImg, 'assets/') ? asset($aImg) : asset('storage/' . $aImg)) : asset('themes/cdt/assets/AWS-Partner-Awards-_-Consulting-Partner-of-the-Year-2026-BKvKVlUl.png');
          @endphp
          <div class="group relative bg-white rounded-[2rem] border border-gray-100 p-6 xl:p-8 shadow-xl hover:border-red-100 hover:shadow-[0_20px_60px_-15px_rgba(226,35,26,0.12)] transition-all duration-500 flex flex-col h-full overflow-hidden">
            <div class="mb-8 flex flex-col items-center text-center">
              @if(!empty($award['year_badge']))
                <div class="inline-block px-4 py-1.5 bg-red-50 text-primary text-xs font-extrabold tracking-widest rounded-full uppercase mb-6 shadow-sm border border-red-100">
                  {{ $award['year_badge'] }}
                </div>
              @endif
              <h3 class="text-lg md:text-xl font-extrabold text-gray-900 leading-tight mb-2 h-[56px] flex items-center justify-center">{{ $award['title'] ?? '' }}</h3>
              <p class="text-sm text-gray-500 font-medium">{{ $award['subtitle'] ?? '' }}</p>
            </div>
            <div class="relative flex-grow flex items-center justify-center rounded-2xl overflow-hidden bg-[#050505] shadow-inner group-hover:shadow-2xl transition-all duration-500 min-h-[320px]">
              <div class="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10 pointer-events-none"></div>
              <x-image :src="$aImgUrl" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="{{ $award['title'] ?? 'Award' }}" />
            </div>
          </div>
        @endforeach
      </div>

      <!-- Certification Grid -->
      <div x-show="activeTab === 'certification'" x-transition:enter="transition ease-out duration-500" style="display: none;">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-8 lg:gap-12 items-center justify-center max-w-[1200px] mx-auto">
          @php
            $certificationsList = $page?->repeaterBlock('certifications_list') ?? [];
          @endphp
          @foreach($certificationsList as $cert)
            @php
              $cImg = $cert['image'] ?? null;
              $cImgUrl = $cImg ? (str_starts_with($cImg, 'http') || str_starts_with($cImg, 'themes/') || str_starts_with($cImg, 'assets/') ? asset($cImg) : asset('storage/' . $cImg)) : asset('themes/cdt/assets/AWS-Advanced-Networking.png-CqnflKau.webp');
            @endphp
            <div class="flex justify-center group relative">
              <div class="relative transition-all duration-500 group-hover:-translate-y-4 cursor-pointer w-full flex justify-center">
                <div class="absolute inset-0 bg-primary/30 blur-2xl rounded-full scale-50 opacity-0 group-hover:scale-110 group-hover:opacity-100 transition-all duration-500 pointer-events-none"></div>
                <x-image :src="$cImgUrl" alt="{{ $cert['title'] ?? 'Certification' }}" class="relative z-10 w-full max-w-[140px] drop-shadow-xl group-hover:drop-shadow-[0_10px_25px_rgba(226,35,26,0.3)] transition-all duration-500" />
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </section>

  <!-- Management Statements V2 Section -->
  <section class="py-24 md:py-32 bg-zinc-50 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-full h-full overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-[10%] -right-[5%] w-[40%] h-[40%] rounded-full bg-red-50/60 blur-3xl"></div>
      <div class="absolute bottom-[20%] -left-[10%] w-[30%] h-[30%] rounded-full bg-zinc-200/50 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="text-center mb-20" data-gsap="fade-up">
        <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-6">
          {{ $page?->block('statements_title_prefix') ?? 'Management' }} <span class="font-bold text-dark">{{ $page?->block('statements_title_main') ?? 'Statements' }}</span>
        </h2>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
      </div>

      <div class="space-y-32">
        @php
          $statementsList = $page?->repeaterBlock('statements_list') ?? [];
        @endphp
        @foreach($statementsList as $sIndex => $s)
          @php
            $isEven = $sIndex % 2 === 0;
            $sPhoto = $s['photo'] ?? null;
            $sPhotoUrl = $sPhoto ? (str_starts_with($sPhoto, 'http') || str_starts_with($sPhoto, 'themes/') || str_starts_with($sPhoto, 'assets/') ? asset($sPhoto) : asset('storage/' . $sPhoto)) : asset('themes/cdt/assets/Lugas_2-1025x1536.jpg-DPfoNaI3.webp');
          @endphp
          <div class="flex flex-col {{ $isEven ? 'lg:flex-row' : 'lg:flex-row-reverse' }} items-center gap-12 lg:gap-20 relative">
            <div class="w-full lg:w-[45%] relative z-10" data-gsap="{{ $isEven ? 'fade-right' : 'fade-left' }}">
              <div class="absolute -inset-4 {{ $isEven ? 'bg-gradient-to-tr from-red-100' : 'bg-gradient-to-tl from-zinc-200' }} to-transparent rounded-[2.5rem] transform {{ $isEven ? '-rotate-3' : 'rotate-3' }} z-0"></div>
              
              <div class="relative z-10 rounded-[2rem] overflow-hidden shadow-[0_20px_50px_-15px_rgba(226,35,26,0.2)] group">
                <div class="aspect-[4/5] bg-gray-200 relative">
                  <x-image :src="$sPhotoUrl" alt="{{ $s['name'] ?? 'Management' }}" class="w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-700" />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>
                
                <div class="absolute bottom-6 left-6 right-6 bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-2xl border border-white/20 transform group-hover:-translate-y-2 transition-transform duration-500 flex justify-between items-center">
                  <div>
                    <h3 class="font-extrabold text-gray-900 text-lg md:text-xl tracking-wide">{{ $s['name'] ?? '' }}</h3>
                    <p class="text-sm font-semibold text-primary mt-1 uppercase tracking-wider">{{ $s['position'] ?? '' }}</p>
                  </div>
                  @if(!empty($s['linkedin_url']))
                    <a href="{{ $s['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center flex-shrink-0 hover:bg-primary hover:text-white text-gray-400 transition-colors shadow-sm">
                      <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                  @endif
                </div>
              </div>
            </div>

            <div class="w-full lg:w-[55%] relative" data-gsap="{{ $isEven ? 'fade-left' : 'fade-right' }}">
              <div class="absolute -top-10 {{ $isEven ? '-left-10 text-red-500 opacity-[0.03]' : '-right-10 text-zinc-400 opacity-[0.05]' }} text-[180px] leading-none font-serif select-none pointer-events-none">"</div>
              
              <div class="relative z-10 {{ $isEven ? 'pl-6 border-l-4 border-primary' : 'pr-6 border-r-4 border-zinc-300 text-right' }}">
                @if(!empty($s['quote']))
                  <p class="text-lg md:text-xl text-gray-700 leading-relaxed font-light mb-6 italic">
                    "{!! trim($s['quote']) !!}"
                  </p>
                @endif
                @if(!empty($s['content']))
                  <div class="text-base text-gray-600 leading-relaxed font-light space-y-4 [&_strong]:font-bold [&_strong]:text-gray-800">
                    {!! $s['content'] !!}
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @include('cdt::partials.contact-section')
@endsection
