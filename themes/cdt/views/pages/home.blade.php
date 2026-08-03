@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : setting('site_name', 'Trusted IT Consultant for Scalable and Secure Growth - Central Data Technology'))

@section('content')
  <!-- From index.html: full width background image, red gradient overlay on the left -->
  <section class="hero-section relative h-screen flex items-center overflow-hidden" x-data="{ catalogueOpen: false }"
    x-on:keydown.escape.window="catalogueOpen = false"
    x-effect="
      document.documentElement.style.overflow = catalogueOpen ? 'hidden' : '';
      document.body.style.overflow = catalogueOpen ? 'hidden' : '';
    ">
    <!-- Background Image -->
    <div class="absolute inset-0">
      @php
        $heroImg = $page?->block('hero_image');
        $heroImgUrl = $heroImg ? (str_starts_with($heroImg, 'http') || str_starts_with($heroImg, 'themes/') || str_starts_with($heroImg, 'assets/') ? asset($heroImg) : asset('storage/' . $heroImg)) : asset('themes/cdt/assets/banner_hero-DHYDqbF8.jpg');
      @endphp
      <x-image :src="$heroImgUrl" alt="{{ setting('site_name', 'Central Data Technology') }} Hero Banner" title="{{ setting('site_name', 'Central Data Technology') }}" class="hero-bg-img w-full h-full object-cover object-[right_center]" onerror="this.src='{{ asset('themes/cdt/assets/photo-1451187580459-43490279c0fa-w2072-DWLGXPRP.jpg') }}'" />
    </div>
  
    <!-- Red Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/80 to-transparent w-full md:w-2/3 lg:w-3/4">
    </div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] w-full px-4 sm:px-6 lg:px-8">
      <div class="max-w-xl text-white">
        <div class="overflow-hidden">
          <h1 class="hero-text-anim">
            <span class="block text-xl md:text-2xl font-light mb-2">{{ $page?->block('hero_prefix') ?? 'Speed Up Your' }}</span>
            <span class="block text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight mb-6">{!! $page?->block('hero_title') ?? 'Transformation Journey' !!}</span>
          </h1>
        </div>
        <div class="overflow-hidden">
          <p class="hero-text-anim text-white/90 text-base mb-8 max-w-md leading-relaxed font-light">
            {{ $page?->block('hero_subtitle') ?? 'Accelerate IT transformation journey with our end-to-end expertise, from strategy to execution across cloud, security, and observability.' }}
          </p>
        </div>
        <div class="hero-text-anim flex items-center gap-3 sm:gap-6">
          <a href="{{ $page?->block('hero_cta_url') ?? '#areas-of-expertise' }}" x-link
            class="inline-flex items-center justify-center whitespace-nowrap bg-white text-primary px-5 py-2.5 sm:px-8 sm:py-3 text-xs sm:text-sm font-bold uppercase tracking-wider hover:bg-zinc-100 transition rounded-full">
            {{ $page?->block('hero_cta_text') ?? t('home.learn_more', 'Learn More') }}
          </a>
          <a href="#" @click.prevent="catalogueOpen = true"
            class="group inline-flex items-center gap-2 whitespace-nowrap text-white text-xs sm:text-sm font-semibold hover:text-white/80 transition-colors cursor-pointer">
            {{ $page?->block('hero_catalogue_text') ?? t('home.access_solutions_catalogue', 'Access Solutions Catalogue') }} <span
              class="text-base sm:text-lg transition-transform duration-300 group-hover:translate-x-1">→</span>
          </a>
        </div>
      </div>
    </div>
  
    <!-- Access Solutions Catalogue: Download Modal -->
    <div x-show="catalogueOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" style="display: none;" role="dialog"
      aria-modal="true" aria-labelledby="catalogue-modal-title">
      <!-- Backdrop with blur -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="catalogueOpen = false"></div>
  
      <!-- Dialog -->
      <div x-show="catalogueOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden text-white max-h-[90vh] overflow-y-auto">
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
        <div class="relative z-10 px-6 py-10 sm:px-10 sm:py-12">
          <p id="catalogue-modal-title"
            class="text-center text-lg sm:text-xl font-bold leading-snug mb-6 sm:mb-8 max-w-md mx-auto">
            {{ t('home.catalogue_modal_title', 'Please fill out the form below to be able to download our Digital Solution Guide') }}
          </p>
  
          @php
            $guideForm = \App\Models\Form::where('slug', 'digital-solution-guide')->where('is_active', true)->with('fields')->first();
          @endphp
          @if($guideForm)
            @include('cdt::partials.tailwind-form', ['form' => $guideForm, 'variant' => 'dark'])
          @else
            <p class="text-white/60 text-sm text-center">Download form is being configured.</p>
          @endif
        </div>
      </div>
    </div>
  </section>

  <!-- Area of Expertise Section -->
  <section class="expertise-section py-24 bg-zinc-50/50" id="areas-of-expertise" x-link>
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column: Title -->
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {!! $page?->block('expertise_title_prefix') ?? 'Area Of' !!}<br>
            <span class="font-bold text-dark">{!! $page?->block('expertise_title_main') ?? 'Expertise' !!}</span>
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
  <section class="alliance-section py-24 bg-white">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {{ t('home.alliance_title_prefix', 'Technology') }}<br>
            <span class="font-bold text-dark">{{ t('home.alliance_title_main', 'Alliance') }}</span>
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
                  <img src="{{ $logoUrl }}" alt="{{ $partner->title }}" title="{{ $partner->title }}" class="alliance-logo w-full h-full object-contain" loading="lazy" />
                </a>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- AWS Private Offers Section -->
  <section class="aws-offers-section py-24 bg-white border-t border-zinc-100">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {{ t('home.aws_title_prefix', 'AWS') }}<br>
            <span class="font-bold text-dark">{{ t('home.aws_title_main', 'Private Offers') }}</span>
          </h2>
          <div class="h-1 bg-primary mt-4" data-gsap="line-grow"></div>
        </div>
        
        <!-- Right Column -->
        <div class="lg:w-3/4">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12 items-center [&:hover_div.aws-item]:opacity-20">
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/confluent-logo-1024x562-BFo8llUh.png') }}" alt="Confluent" title="Confluent" class="aws-logo w-full h-full object-contain"></div>
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/datadog-logo-1024x1024-BBaPl4Qq.png') }}" alt="Datadog" title="Datadog" class="aws-logo w-full h-full object-contain"></div>
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/PT-Urun-Bangun-Negeri-BLb9ARg2.png') }}" alt="Fortinet" title="Fortinet" class="aws-logo w-full h-full object-contain"></div>
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/GitLab-logo-BBxYVl-u.svg') }}" alt="GitLab" title="GitLab" class="aws-logo w-full h-full object-contain"></div>
            
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/Mongo-DB-Logo-0iY8tsMG.svg') }}" alt="MongoDB" title="MongoDB" class="aws-logo w-full h-full object-contain"></div>
            <div class="aws-item flex items-center justify-center aspect-[27/17] p-6 bg-white relative transition-opacity duration-500 hover:!opacity-100 rounded-2xl"><img src="{{ asset('themes/cdt/assets/tapway-logo-hd--DjdHTKHP.png') }}" alt="Tapway" title="Tapway" class="aws-logo w-full h-full object-contain"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why CDT? Section -->
  <section class="py-24 bg-white">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column -->
        <div class="lg:w-1/4 shrink-0 overflow-hidden">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
            {!! $page?->block('why_cdt_title_prefix') ?? 'Why' !!}<br>
            <span class="font-bold text-dark italic">{!! $page?->block('why_cdt_title_main') ?? ($page?->block('why_cdt_title') ?? 'CDT?') !!}</span>
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

  <!-- Testimonials Section -->
  <section class="py-24 relative overflow-hidden bg-zinc-50/50" id="testimonials">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 bg-testimonial-image opacity-[0.5] bg-cover bg-center blur-sm"></div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">{{ t('home.testimonial_title_prefix', 'What Our') }} <span
            class="font-bold text-dark">{{ t('home.testimonial_title_main', 'Client Says') }}</span></h2>
        <div class="h-1 bg-primary mt-4 mx-auto" style="width: 50px;" data-gsap="line-grow"></div>
      </div>
  
      <div class="max-w-6xl mx-auto relative" data-gsap="fade-up" data-gsap-delay="0.2">
        <!-- Top Control Bar -->
        <div class="flex justify-between items-end mb-6 px-2">
          <div
            class="swiper-pagination-custom text-zinc-400 font-medium tracking-widest uppercase text-xl [&_.swiper-pagination-current]:text-dark [&_.swiper-pagination-current]:font-bold [&_.swiper-pagination-current]:text-3xl">
          </div>
          <div class="flex gap-4">
            <button
              class="swiper-button-prev-custom w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              class="swiper-button-next-custom w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
          <div class="swiper testimonials-swiper">
          <div class="swiper-wrapper">
  
            @foreach($testimonials as $testimonial)
              @php
                $locale = app()->getLocale();
                $translations = $testimonial->meta['_translations'][$locale] ?? [];
                $meta = array_merge($testimonial->meta ?? [], $translations);

                $logoPath = $testimonial->featured_image;
                $logoUrl = resolve_block_asset($logoPath);
                if ($logoUrl && (str_contains($logoUrl, '/customer-success/') || !preg_match('/\.(jpg|png|webp|svg|jpeg)$/i', parse_url($logoUrl, PHP_URL_PATH) ?? ''))) {
                    $logoUrl = null;
                }

                $companyName = $meta['client_name'] ?? ($translations['title'] ?? $testimonial->title);

                $rawAuthor = $meta['quote_author'] ?? $meta['testimonial_author'] ?? $meta['person'] ?? null;
                if ($rawAuthor && str_contains($rawAuthor, ' - ')) {
                    $personName = trim(\Illuminate\Support\Str::before($rawAuthor, ' - '));
                    $position = trim(\Illuminate\Support\Str::after($rawAuthor, ' - '));
                } else {
                    $personName = $rawAuthor ?: $companyName;
                    $position = $meta['position'] ?? '';
                }

                $testimonialContent = !empty($meta['quote']) ? $meta['quote']
                    : (!empty($meta['testimonial_quote']) ? $meta['testimonial_quote']
                    : (!empty($translations['content']) ? $translations['content'] : $testimonial->content));
              @endphp
              <div class="swiper-slide h-full">
                <div
                  class="bg-white rounded-2xl shadow-xl overflow-hidden h-full flex flex-col lg:flex-row border border-zinc-100 min-h-[400px]">
                  <div class="lg:w-1/3 bg-zinc-50 p-12 flex flex-col justify-between border-r border-zinc-100">
                    @if($logoUrl)
                      <div class="h-32 flex justify-start items-center mb-8">
                        <img src="{{ $logoUrl }}" alt="{{ $companyName }}" title="{{ $companyName }}" class="max-h-full w-auto max-w-[280px] object-contain object-left mix-blend-multiply" />
                      </div>
                    @else
                      <div class="h-32 mb-8"></div>
                    @endif
                    <div>
                      <div class="flex text-primary mb-4">
                        @for($i=0; $i<5; $i++)
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        @endfor
                      </div>
                      <h3 class="font-bold text-xl text-dark mb-1">{{ $personName }}</h3>
                      @if(!empty($position))
                        <p class="text-sm text-zinc-500 uppercase tracking-wider mb-2">{{ $position }}</p>
                      @endif
                      @if($companyName !== $personName)
                        <p class="text-sm font-semibold text-primary uppercase">{{ $companyName }}</p>
                      @endif
                    </div>
                  </div>
                  <div class="lg:w-2/3 p-12 flex items-center relative">
                    <svg class="absolute top-8 left-8 w-24 h-24 text-zinc-100 -z-10 transform -scale-x-100"
                      fill="currentColor" viewBox="0 0 24 24">
                      <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                    <div class="relative z-10 prose max-w-none text-zinc-700 leading-relaxed space-y-4">
                      {!! $testimonialContent !!}
                    </div>
                  </div>
                </div>
              </div>
            @endforeach

          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Callouts Section -->
  <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Callout 1 -->
      <div class="relative h-64 overflow-hidden group" data-gsap="curtain-reveal" data-gsap-delay="0">
        <img src="{{ asset('themes/cdt/assets/photo-1551288049-bebda4e38f71-w1000-CbVNUoo0.jpg') }}" alt="{{ t('home.blog_title', 'Blog, News & Video') }}" title="{{ t('home.blog_title', 'Blog, News & Video') }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
        <div class="absolute inset-0 bg-zinc-900/60"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
          <h2 class="text-3xl font-bold mb-6">{{ t('home.blog_title', 'Blog, News & Video') }}</h2>
          <a href="{{ url('/insights') }}" class="bg-primary text-white px-8 py-2.5 text-[13px] font-bold uppercase tracking-wider hover:bg-red-700 transition rounded-full">
            {{ t('home.explore', 'Explore') }}
          </a>
        </div>
      </div>
      
      <!-- Callout 2 -->
      <div class="relative h-64 overflow-hidden group" data-gsap="curtain-reveal" data-gsap-delay="0.2">
        <img src="{{ asset('themes/cdt/assets/photo-1522071820081-009f0129c71c-w1000-CEqXLUmA.jpg') }}" alt="{{ t('home.life_title', 'Life at Central Data Technology') }}" title="{{ t('home.life_title', 'Life at Central Data Technology') }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
        <div class="absolute inset-0 bg-zinc-900/60"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center">
          <h2 class="text-3xl font-bold mb-6">{{ t('home.life_title', 'Life at Central Data Technology') }}</h2>
          <a href="{{ url('/careers') }}" class="bg-primary text-white px-8 py-2.5 text-[13px] font-bold uppercase tracking-wider hover:bg-red-700 transition rounded-full">
            {{ t('home.learn_more', 'Learn More') }}
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form Section -->
  @include('cdt::partials.contact-section')
@endsection
