@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : setting('site_name', 'Trusted IT Consultant for Scalable and Secure Growth - Central Data Technology'))

@section('content')

  <!-- From index.html: full width background image, red gradient overlay on the left -->
  <section class="hero-section relative h-screen flex items-center overflow-hidden" x-data="{ catalogueOpen: false }"
    x-on:keydown.escape.window="catalogueOpen = false"
    x-init="$watch('catalogueOpen', val => { if (val) { document.documentElement.style.overflow = 'hidden'; document.body.style.overflow = 'hidden'; } else { document.documentElement.style.overflow = ''; document.body.style.overflow = ''; } })"
    >
    <!-- Background Image -->
    <div class="absolute inset-0">
      @php
        $heroImg = $page?->block('hero_image');
        $heroImgUrl = $heroImg ? (str_starts_with($heroImg, 'http') || str_starts_with($heroImg, 'themes/') || str_starts_with($heroImg, 'assets/') ? asset($heroImg) : asset('storage/' . $heroImg)) : asset('themes/cdt/assets/banner_hero-DHYDqbF8.jpg');
      @endphp
      <x-image :src="$heroImgUrl" alt="{{ setting('site_name', 'Central Data Technology') }} Hero Banner" title="{{ setting('site_name', 'Central Data Technology') }}" class="hero-bg-img w-full h-full object-cover origin-center" loading="eager" decoding="sync" fetchpriority="high" onerror="this.src='{{ asset('themes/cdt/assets/photo-1451187580459-43490279c0fa-w2072-DWLGXPRP.jpg') }}'" />
    </div>
  
    <!-- Red Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/80 to-transparent w-full md:w-2/3 lg:w-3/4">
    </div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] w-full px-4 sm:px-6 lg:px-8">
      <div class="max-w-xl text-white">
        <div class="overflow-hidden">
          @php
            $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey']);
          @endphp
          <h1 class="hero-text-anim">
            @if(!empty($heroTitle['prefix']))
              <span class="block text-xl md:text-2xl font-light mb-2">{!! $heroTitle['prefix'] !!}</span>
            @endif
            <span class="block text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight mb-6">{!! $heroTitle['main'] !!}</span>
          </h1>
        </div>
        <div class="overflow-hidden">
          <p class="hero-text-anim text-white/90 text-base mb-8 max-w-md leading-relaxed font-light">
            {{ $page?->block('hero_subtitle') ?? 'Accelerate IT transformation journey with our end-to-end expertise, from strategy to execution across cloud, security, and observability.' }}
          </p>
        </div>
        @php
          $heroCta = $page?->buttonBlock('hero_cta', ['text' => 'Learn More', 'url' => '#areas-of-expertise']);
        @endphp
        <div class="hero-text-anim flex items-center gap-3 sm:gap-6">
          <a href="{{ $heroCta['url'] ?? '#areas-of-expertise' }}" x-link
            aria-label="{{ ($heroCta['text'] ?? t('home.learn_more', 'Learn More')) }}: {{ setting('site_name', 'Central Data Technology') }}"
            class="inline-flex items-center justify-center whitespace-nowrap bg-white text-primary px-5 py-2.5 sm:px-8 sm:py-3 text-xs sm:text-sm font-bold uppercase tracking-wider hover:bg-zinc-100 transition rounded-full">
            {{ $heroCta['text'] ?? t('home.learn_more', 'Learn More') }}
          </a>
          <button type="button" @click="catalogueOpen = true"
            class="group inline-flex items-center gap-2 whitespace-nowrap text-white text-xs sm:text-sm font-semibold hover:text-white/80 transition-colors cursor-pointer">
            {{ t('home.access_solutions_catalogue', 'Access Solutions Catalogue') }} <span
              class="text-base sm:text-lg transition-transform duration-300 group-hover:translate-x-1">→</span>
          </button>
        </div>
      </div>
    </div>
  
    <!-- Access Solutions Catalogue: Download Modal -->
    <template x-teleport="body">
      <div x-show="catalogueOpen" style="display: none;">
        <!-- Backdrop -->
        <div x-show="catalogueOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
          class="modal-sheet-backdrop fixed inset-0 z-[10003] bg-black/60 backdrop-blur-sm"
          @click="catalogueOpen = false"></div>

        <!-- Content -->
        <div x-show="catalogueOpen"
          x-transition:enter="transition ease-out duration-300 transform"
          x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
          x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
          x-transition:leave="transition ease-in duration-200 transform"
          x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
          x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
          class="modal-sheet-wrapper fixed inset-0 z-[10004] flex items-end md:items-center justify-center md:p-6"
          role="dialog" aria-modal="true" aria-labelledby="catalogue-modal-title">

          <div class="modal-sheet-card relative w-full md:max-w-2xl rounded-t-3xl md:rounded-2xl shadow-2xl overflow-hidden text-white md:max-h-[90vh] overflow-y-auto">
            <!-- Drag Handle (mobile only) -->
            <div class="w-12 h-1 bg-white/30 rounded-full mx-auto mt-3 mb-1 md:hidden"></div>

            <!-- Background image + red overlay -->
            <div class="absolute inset-0 bg-form-image bg-cover bg-center pointer-events-none" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-primary/90 pointer-events-none" aria-hidden="true"></div>

            <!-- Close -->
            <button type="button" @click.stop="catalogueOpen = false"
              class="absolute top-3 right-3 sm:top-4 sm:right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full text-white/90 hover:text-white hover:bg-white/15 transition-colors cursor-pointer"
              aria-label="Close">
              <svg class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Form -->
            <div class="modal-sheet-body relative z-10 px-6 py-10 sm:px-10 sm:py-12 flex-1">
              <p id="catalogue-modal-title"
                class="text-center text-lg sm:text-xl font-bold leading-snug mb-6 sm:mb-8 max-w-md mx-auto">
                {{ t('home.catalogue_modal_title', 'Please fill out the form below to be able to download our Digital Solution Guide') }}
              </p>

              @php
                $guideForm = get_assigned_form('solution_guide_form');
              @endphp
              @if($guideForm)
                @include('cdt::partials.tailwind-form', ['form' => $guideForm, 'variant' => 'dark'])
              @else
                <p class="text-white/60 text-sm text-center">Download form is being configured.</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </template>
  </section>

  <!-- Area of Expertise Section -->
  <section class="expertise-section deferred-section py-24 bg-zinc-50/50" id="areas-of-expertise" x-link>
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column: Title -->
        @php
          $expTitle = $page?->titleBlock('expertise_title', ['prefix' => 'Area Of', 'main' => 'Expertise']);
        @endphp
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            @if(!empty($expTitle['prefix']))
              {!! $expTitle['prefix'] !!} <br>
            @endif
            <span class="font-bold text-dark">{!! $expTitle['main'] !!}</span>
          </h2>
          <div class="h-1 bg-primary mt-4" data-gsap="line-grow"></div>
        </div>

        @php
          $expertiseItems = $page?->block('expertise_list');
          if (is_string($expertiseItems)) {
              $expertiseItems = json_decode($expertiseItems, true);
          }
          if (empty($expertiseItems) || !is_array($expertiseItems)) {
              $expertiseItems = [
                  [
                      'image' => 'themes/cdt/assets/security-DrNRARC-.webp',
                      'title' => 'Security',
                      'description' => "In the modern environment, it's essential for businesses to work together to ensure applications are secure. CDT's security solutions allow you to take a preventative approach against cyber threats by helping you keep tabs on potential weak spots, reduce impact in the event of an attack, and build a more powerful defense to keep your most critical assets secure. Additionally, you can tailor our security solutions to fit your specific requirements."
                  ],
                  [
                      'image' => 'themes/cdt/assets/clouds.png-Doka7eSJ.webp',
                      'title' => 'Cloud',
                      'description' => "Cloud technology opens the door to new innovations, promoting emerging markets like cloud-native development. CDT is a cloud expert with certified teams, so we see this as an opportunity to help businesses reap the benefits of the cloud by providing a variety of cloud-based solutions. Our competence has also earned the reputation of AWS Advanced Consulting Partner of the year 2022, AWS Security Expert, AWS Migration consultant, AWS Infrastructure provider, AWS Analytics, and AWS DevOps specialty."
                  ],
                  [
                      'image' => 'themes/cdt/assets/analytics.png-Bdc2CvaB.webp',
                      'title' => 'Observability',
                      'description' => "Observability in IT refers to the practice of monitoring and analyzing system and application performance in real-time. It provides insight into the behavior and health of software systems, helping organizations detect and resolve issues quickly and effectively. CDT can help using observability in business and can ensure yours IT systems are performing optimally, identify and resolve problems before they impact customers, and improve overall reliability and customer satisfaction."
                  ]
              ];
          }
        @endphp
  
        <!-- Right Column: Cards -->
        <div class="lg:w-3/4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($expertiseItems as $item)
              @php
                $itemImgUrl = resolve_block_asset($item['image'] ?? '');
              @endphp
              <div class="expertise-card bg-white shadow-sm border border-zinc-100 overflow-hidden hover:shadow-md transition-shadow rounded-2xl flex flex-col">
                @if($itemImgUrl)
                  <div class="h-40 w-full overflow-hidden shrink-0">
                    <x-image :src="$itemImgUrl" alt="{{ $item['title'] ?? 'Expertise' }}" title="{{ $item['title'] ?? 'Expertise' }}" class="w-full h-full object-cover" />
                  </div>
                @endif
                <div class="p-6 flex-1 flex flex-col justify-start">
                  <h3 class="font-bold text-xl mb-3">{{ $item['title'] ?? '' }}</h3>
                  <p class="text-base text-zinc-500 leading-relaxed">
                    {{ $item['description'] ?? '' }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Technology Alliance Section -->
  <section class="alliance-section deferred-section py-24 bg-white">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        @php
          $allianceTitle = $page?->titleBlock('alliance_title', ['prefix' => 'Technology', 'main' => 'Alliance']);
        @endphp
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            @if(!empty($allianceTitle['prefix']))
              {!! $allianceTitle['prefix'] !!} <br>
            @endif
            <span class="font-bold text-dark">{!! $allianceTitle['main'] !!}</span>
          </h2>
          <div class="h-1 bg-primary mt-4" data-gsap="line-grow"></div>
        </div>
        
        <!-- Right Column -->
        <div class="lg:w-3/4">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12 items-center has-[a:hover]:[&_a]:opacity-20">
            @foreach(($partners ?? []) as $partner)
              @php
                $logoPath = $partner->featured_image ?: $partner->getMeta('logo');
                $logoUrl = $logoPath
                    ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
                    : null;
              @endphp
              @if($logoUrl)
                <a href="{{ $partner->getUrl() }}" x-link
                  class="alliance-link flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-all duration-500 hover:!opacity-100 hover:scale-105 hover:bg-zinc-50 rounded-2xl"
                  data-hover-effect="scale-bounce">
                  <x-image :src="$logoUrl" alt="{{ $partner->title }}" title="{{ $partner->title }}" class="alliance-logo w-full h-full object-contain" />
                </a>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- AWS Private Offers Section -->
  <section class="aws-offers-section deferred-section py-24 bg-white border-t border-zinc-100">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        @php
          $awsTitle = $page?->titleBlock('aws_title', ['prefix' => 'AWS', 'main' => 'Private Offers']);
        @endphp
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            @if(!empty($awsTitle['prefix']))
              {!! $awsTitle['prefix'] !!} <br>
            @endif
            <span class="font-bold text-dark">{!! $awsTitle['main'] !!}</span>
          </h2>
          <div class="h-1 bg-primary mt-4" data-gsap="line-grow"></div>
        </div>
        
        @php
          $awsLogos = $page?->block('aws_offers_gallery');
          if (is_string($awsLogos)) {
              $awsLogos = json_decode($awsLogos, true);
          }
          if (empty($awsLogos) || !is_array($awsLogos)) {
              $awsLogos = [
                  'themes/cdt/assets/confluent-logo-1024x562-BFo8llUh.png',
                  'themes/cdt/assets/datadog-logo-1024x1024-BBaPl4Qq.png',
                  'themes/cdt/assets/PT-Urun-Bangun-Negeri-BLb9ARg2.png',
                  'themes/cdt/assets/GitLab-logo-BBxYVl-u.svg',
                  'themes/cdt/assets/Mongo-DB-Logo-0iY8tsMG.svg',
                  'themes/cdt/assets/tapway-logo-hd--DjdHTKHP.png'
              ];
          }
        @endphp

        <!-- Right Column -->
        <div class="lg:w-3/4">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12 items-center [&:hover_div.aws-item]:opacity-20">
            @foreach($awsLogos as $logo)
              <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl">
                <img src="{{ resolve_block_asset($logo) }}" alt="AWS Private Offer Partner" class="aws-logo w-full h-full object-contain">
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why CDT? Section -->
  <section class="deferred-section py-24 bg-white">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        @php
          $whyTitle = $page?->titleBlock('why_cdt_title', ['prefix' => 'Why', 'main' => 'CDT?']);
        @endphp
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            @if(!empty($whyTitle['prefix']))
              {!! $whyTitle['prefix'] !!} <br>
            @endif
            <span class="font-bold text-dark italic">{!! $whyTitle['main'] !!}</span>
          </h2>
          <div class="h-1 bg-primary mt-4" data-gsap="line-grow"></div>
        </div>
        
        @php
          $whyCdtItems = $page?->block('why_cdt_list');
          if (is_string($whyCdtItems)) {
              $whyCdtItems = json_decode($whyCdtItems, true);
          }
          if (empty($whyCdtItems) || !is_array($whyCdtItems)) {
              $whyCdtItems = [
                  [
                      'title' => 'NUMBER ONE IT SERVICE DELIVERY',
                      'description' => "Guarantee the best quality of IT service delivery with every stage delivery involves many IT experts' role and ensure that service-level agreement (SLA) is applied.",
                      'image' => 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg'
                  ],
                  [
                      'title' => 'EXCELLENT CUSTOMER SERVICES',
                      'description' => "24/7 customer response center, and many other convenient services were given fulfill customer requirement in today's digital era.",
                      'image' => 'themes/cdt/assets/photo-1522071820081-009f0129c71c-w800-D1mgrB8h.jpg'
                  ],
                  [
                      'title' => 'YEARS OF EXPERIENCE EXPERTS',
                      'description' => "With years of experience and numerous of project portfolios, professional IT experts will measure and manage risk to ensure accuracy in implementing solutions into customer's IT environment.",
                      'image' => 'themes/cdt/assets/photo-1552664730-d307ca884978-w800-DNfMnljE.jpg'
                  ]
              ];
          }
        @endphp

        <!-- Right Column -->
        <div class="lg:w-3/4">
          <div class="grid grid-cols-1 md:grid-cols-3">
            @foreach($whyCdtItems as $index => $item)
              @php
                $bgImgUrl = resolve_block_asset($item['image'] ?? '');
                if (!$bgImgUrl) {
                    $bgImgUrl = resolve_block_asset('photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg');
                }
                $delay = $index * 0.2;
              @endphp
              <div class="relative h-[320px] group overflow-hidden" data-gsap="curtain-reveal" data-gsap-delay="{{ $delay }}">
                <x-image :src="$bgImgUrl" alt="{!! strip_tags($item['title'] ?? 'Why CDT') !!}" title="{!! strip_tags($item['title'] ?? 'Why CDT') !!}" class="absolute inset-0 w-full h-full object-cover grayscale group-hover:scale-105 group-hover:blur-[3px] transition-all duration-700" />
                <div class="absolute inset-0 bg-[#4F5B53]/85 group-hover:bg-[#dc2626]/90 transition-colors duration-500 mix-blend-multiply"></div>
                <div class="absolute inset-0 p-10 flex flex-col justify-start items-center text-center text-white z-10">
                  <h3 class="font-bold text-lg mb-4 uppercase tracking-wider leading-snug">{!! $item['title'] ?? '' !!}</h3>
                  <p class="text-sm text-white/90 leading-relaxed max-w-[90%]">
                    {{ $item['description'] ?? '' }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section (deferred via AJAX — 97KB rendered) -->
  <div class="deferred-ajax bg-zinc-50/50" data-section="testimonials" id="testimonials"
       style="min-height:600px;content-visibility:auto;contain-intrinsic-size:auto 600px"></div>

  <!-- Blog Callouts Section -->
  @php
    $blogCard = $page?->cardBlock('blog_callout', [
      'title' => 'Blog, News & Video',
      'description' => '',
      'image' => 'themes/cdt/assets/photo-1551288049-bebda4e38f71-w1000-CbVNUoo0.jpg',
      'button_text' => 'Explore',
      'button_url' => '/insights'
    ]);
    $lifeCard = $page?->cardBlock('life_callout', [
      'title' => 'Life at Central Data Technology',
      'description' => '',
      'image' => 'themes/cdt/assets/photo-1522071820081-009f0129c71c-w1000-CEqXLUmA.jpg',
      'button_text' => 'Learn More',
      'button_url' => '/careers'
    ]);
  @endphp
  <section class="deferred-section max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Callout 1 -->
      <div class="relative h-64 overflow-hidden group" data-gsap="curtain-reveal" data-gsap-delay="0">
        <img src="{{ resolve_block_asset($blogCard['image'] ?: 'themes/cdt/assets/photo-1551288049-bebda4e38f71-w1000-CbVNUoo0.jpg') }}" alt="{{ $blogCard['title'] }}" title="{{ $blogCard['title'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
        <div class="absolute inset-0 bg-zinc-900/60"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-6">
          <h2 class="text-3xl font-bold mb-3">{!! $blogCard['title'] !!}</h2>
          @if(!empty($blogCard['description']))
            <p class="text-sm text-white/80 mb-4 max-w-sm">{!! $blogCard['description'] !!}</p>
          @endif
          <a href="{{ url($blogCard['button_url'] ?: '/insights') }}" aria-label="{{ $blogCard['title'] }}: {{ $blogCard['button_text'] ?: t('home.explore', 'Explore') }}" class="bg-primary text-white px-8 py-2.5 text-[13px] font-bold uppercase tracking-wider hover:bg-red-700 transition rounded-full">
            {{ $blogCard['button_text'] ?: t('home.explore', 'Explore') }}
          </a>
        </div>
      </div>
      
      <!-- Callout 2 -->
      <div class="relative h-64 overflow-hidden group" data-gsap="curtain-reveal" data-gsap-delay="0.2">
        <img src="{{ resolve_block_asset($lifeCard['image'] ?: 'themes/cdt/assets/photo-1522071820081-009f0129c71c-w1000-CEqXLUmA.jpg') }}" alt="{{ $lifeCard['title'] }}" title="{{ $lifeCard['title'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
        <div class="absolute inset-0 bg-zinc-900/60"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-6">
          <h2 class="text-3xl font-bold mb-3">{!! $lifeCard['title'] !!}</h2>
          @if(!empty($lifeCard['description']))
            <p class="text-sm text-white/80 mb-4 max-w-sm">{!! $lifeCard['description'] !!}</p>
          @endif
          <a href="{{ url($lifeCard['button_url'] ?: '/careers') }}" aria-label="{{ $lifeCard['title'] }}: {{ $lifeCard['button_text'] ?: t('home.learn_more', 'Learn More') }}" class="bg-primary text-white px-8 py-2.5 text-[13px] font-bold uppercase tracking-wider hover:bg-red-700 transition rounded-full">
            {{ $lifeCard['button_text'] ?: t('home.learn_more', 'Learn More') }}
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form Section (deferred via AJAX — 68KB rendered) -->
  <div class="deferred-ajax" data-section="contact" id="contact"
       style="min-height:500px;content-visibility:auto;contain-intrinsic-size:auto 500px"></div>
@endsection

