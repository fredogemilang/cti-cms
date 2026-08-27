@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : 'Get Up to 6 Months Free AWS Cloud Credits — Central Data Technology')

@section('content')

@push('head')
  @if(isset($page))
    <x-seo.head :entity="$page" />
  @else
    <!-- Primary Meta Tags -->
    <meta name="title" content="Get Up to 6 Months Free AWS Cloud Credits | Central Data Technology">
    <meta name="description" content="Accelerate your AI and cloud transformation with up to 6 months of free AWS Cloud Credits managed by Central Data Technology (CDT), an official AWS Premier Tier Partner. Open to all industries with minimum MRR $2K.">
    <meta name="keywords" content="AWS Cloud Credits, Amazon Web Services, AWS Partner Indonesia, Free AWS Credits, Amazon Bedrock, Amazon Quick, Central Data Technology, CDT AWS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Get Up to 6 Months Free AWS Cloud Credits | Central Data Technology">
    <meta property="og:description" content="Accelerate your AI and cloud transformation with up to 6 months of free AWS Cloud Credits managed by CDT, an official AWS Premier Tier Partner.">
    <meta property="og:image" content="{{ asset('storage/media/logo-premier-tier.webp') }}">
    <meta property="og:site_name" content="Central Data Technology">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Get Up to 6 Months Free AWS Cloud Credits | Central Data Technology">
    <meta property="twitter:description" content="Accelerate your AI and cloud transformation with up to 6 months of free AWS Cloud Credits managed by CDT, an official AWS Premier Tier Partner.">
    <meta property="twitter:image" content="{{ asset('storage/media/logo-premier-tier.webp') }}">
  @endif

  @php
    $schemaData = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      'name' => 'Get Up to 6 Months Free AWS Cloud Credits',
      'description' => 'Accelerate your AI and cloud transformation with up to 6 months of free AWS Cloud Credits managed by Central Data Technology, an official AWS Premier Tier Partner.',
      'url' => url()->current(),
      'publisher' => [
        '@type' => 'Organization',
        'name' => 'Central Data Technology',
        'url' => 'https://centraldatatech.com',
        'logo' => asset('storage/media/logo-premier-tier.webp'),
      ],
      'mainEntity' => [
        '@type' => 'Service',
        'name' => 'AWS Cloud Credit Assessment & Management',
        'provider' => [
          '@type' => 'Organization',
          'name' => 'Central Data Technology',
        ],
        'areaServed' => 'ID',
        'description' => 'Up to 6 months free AWS Cloud credits for AI initiatives (Amazon Bedrock & Amazon Quick) and up to 3 months for all AWS workloads.',
      ],
    ];
  @endphp
  <!-- Structured Data JSON-LD -->
  <script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<style>
  @keyframes bounceX {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(6px); }
  }
  @keyframes bounceY {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(6px); }
  }
  .arrow-desktop {
    display: none;
  }
  .arrow-mobile {
    display: inline-flex;
    animation: bounceY 1.5s infinite ease-in-out;
  }
  @media (min-width: 1024px) {
    .arrow-desktop {
      display: inline-flex;
      animation: bounceX 1.5s infinite ease-in-out;
    }
    .arrow-mobile {
      display: none;
    }
  }
</style>

@php
  $pageObj = $page ?? null;

  // Compound PageBlocks
  $heroTitleBlock = $pageObj?->titleBlock('hero_title', ['prefix' => 'Get Up to', 'main' => '6 Months of Free AWS Cloud Credits']);
  $heroCtaBlock = $pageObj?->buttonBlock('hero_cta', ['text' => 'Claim Your Credits', 'url' => '#formaws']);
  $heroSecondaryCtaBlock = $pageObj?->buttonBlock('hero_secondary_cta', ['text' => 'Request Free Assessment', 'url' => '#formaws']);

  $primaryCtaUrl = trim($heroCtaBlock['url'] ?? '');
  $hasPrimaryUrl = !empty($primaryCtaUrl);

  $secondaryCtaUrl = trim($heroSecondaryCtaBlock['url'] ?? '');
  $hasSecondaryUrl = !empty($secondaryCtaUrl);
  $whatYouGetHeaderBlock = $pageObj?->titleBlock('what_you_get_header', ['prefix' => 'PROGRAM BENEFITS', 'main' => 'What You Get']);
  $qualifiesAiHeaderBlock = $pageObj?->titleBlock('qualifies_ai_header', ['prefix' => 'AI INITIATIVES', 'main' => 'What Qualifies as an AI Project?']);

  // Standard & Cards PageBlocks
  $heroSubtitleText = $pageObj?->block('hero_subtitle', 'Accelerate your AI and cloud transformation with AWS credits managed by Central Data Technology, an official AWS Consulting Partner. No migration required. Open to all industries.');
  $zeroCostHeadingText = $pageObj?->block('zero_cost_heading', 'Up to 6 Months Free on AWS');
  $zeroCostSubtitleText = $pageObj?->block('zero_cost_text', 'Use AWS credits to run workloads, AI experiments, and migrations. Credit amount is based on your assessment and project scope. Available to new and existing AWS customers.');
  $whatYouGetDescText = $pageObj?->block('what_you_get_desc', 'Reduce financial risk while you explore, build, and scale on AWS.');
  $qualifiesAiDescText = $pageObj?->block('qualifies_ai_desc', 'The 6-month credit window is reserved for AI initiatives using these services.');
  $supportWorkloadHeaderBlock = $pageObj?->titleBlock('support_workload_header', ['prefix' => 'SUPPORTED WORKLOADS', 'main' => 'Support Workload']);
  $supportWorkloadDescText = $pageObj?->block('support_workload_desc', 'Credits applicable to these AWS service categories.');
  $eligibilityHeaderBlock = $pageObj?->titleBlock('eligibility_header', ['prefix' => 'ELIGIBILITY', 'main' => 'Who Can Apply?']);
  $programTermsHeaderBlock = $pageObj?->titleBlock('program_terms_header', ['prefix' => 'TRANSPARENCY', 'main' => 'Program Terms']);

  $whatYouGetCards = $pageObj?->repeaterBlock('what_you_get_cards', []);
  $qualifiesAiCards = $pageObj?->repeaterBlock('qualifies_ai_cards', []);
  $eligibilityCards = $pageObj?->repeaterBlock('eligibility_cards', []);
  $programTermsCard = $pageObj?->cardBlock('program_terms_card', []);
  $heroFeatures = $pageObj?->repeaterBlock('hero_features', []);
  $zeroCostStats = $pageObj?->repeaterBlock('zero_cost_stats', []);

  // Dynamic Support Workload CPT Entries from Database
  $workloadSlugs = [
    'aws-back-up-disaster-recovery' => 'lucide:database-backup',
    'aws-container-microservices' => 'lucide:boxes',
    'aws-analytics' => 'lucide:bar-chart-3',
    'aws-application-database-migration' => 'lucide:arrow-right-left',
    'aws-storage-service' => 'lucide:hard-drive',
    'windows-workload' => 'lucide:layout-grid',
    'aws-artificial-intelligence' => 'lucide:sparkles',
    'aws-cloud-security' => 'lucide:shield-lock',
  ];

  $workloadProducts = \App\Models\CptEntry::whereIn('slug', array_keys($workloadSlugs))->get()->keyBy('slug');

  // Form Studio Instance via Form Assignments
  $awsForm = get_assigned_form('aws_credits_form');
@endphp

<!-- Section 1: Hero Section with Form Above the Fold -->
<section class="relative text-zinc-900 overflow-hidden pt-6 lg:pt-8 pb-20" style="background-color: #f4f4f5;">
  <!-- Subtle Background Glows & Grid -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-[radial-gradient(circle,rgba(227,6,19,0.12)_0%,transparent_70%)] rounded-full blur-3xl"></div>
    <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-[radial-gradient(circle,rgba(227,6,19,0.08)_0%,transparent_70%)] rounded-full blur-3xl"></div>
    <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:24px_24px]"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    
    <!-- SEO Breadcrumbs -->
    @if(isset($page))
      <div class="mb-6 text-xs font-semibold text-zinc-600 [&_a]:text-zinc-600 [&_a:hover]:text-red-600 [&_span]:text-zinc-400 [&_.breadcrumb-current]:text-zinc-900 [&_.breadcrumb-current]:font-bold">
        <x-seo-breadcrumbs :entity="$page" />
      </div>
    @endif

    <!-- Main Unified Split Hero Card -->
    <div class="rounded-3xl border shadow-2xl overflow-hidden flex flex-col lg:flex-row items-stretch" style="background-color: #16202e; border-color: #2d3e56;">
      
      <!-- Left Column: Dark Charcoal Tech Card Info (#182332) -->
      <div class="w-full lg:w-7/12 p-8 sm:p-12 lg:p-14 text-white flex flex-col justify-between space-y-8 relative" style="background-color: #182332; color: #ffffff;">
        <div class="space-y-6">
          <!-- AWS Premier Tier Partner Logo Header -->
          <div class="flex items-center gap-5">
            <img src="{{ resolve_block_asset('media/logo-premier-tier.webp') }}" alt="AWS Premier Tier Partner" class="h-24 w-auto object-contain shrink-0" style="height: 6rem;" onerror="this.src='{{ asset('storage/media/logo-awspng-1785241172-yPnfNkus.webp') }}'" />
            <div>
              <span class="text-xs uppercase tracking-widest font-bold block" style="color: #cbd5e1;">{{ t('aws_credits.badge_transform', 'Transform Faster with CDT') }}</span>
              <span class="text-sm sm:text-base font-extrabold flex items-center gap-1.5 mt-0.5" style="color: #ff9900;">
                <span style="color: #ff9900;" class="shrink-0 flex items-center"><x-icon name="lucide:award" class="w-4 h-4 text-[#ff9900]" /></span>
                {{ t('aws_credits.badge_premier_partner', 'AWS Premier Tier Partner') }}
              </span>
            </div>
          </div>

          <!-- Main Title -->
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight pt-2" style="color: #ffffff;">
            {{ $heroTitleBlock['prefix'] ?? 'Get Up to' }} <span style="color: #ff9900;">{{ $heroTitleBlock['main'] ?? '6 Months of Free AWS Cloud Credits' }}</span>
          </h1>

          <!-- Body Description -->
          <p class="text-base sm:text-lg font-light leading-relaxed" style="color: #e2e8f0;">
            {{ $heroSubtitleText }}
          </p>

          <!-- Checklists 2x2 Grid (Dynamic Repeater Block) -->
          @if(!empty($heroFeatures))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-semibold pt-2" style="color: #f8fafc;">
              @foreach($heroFeatures as $feat)
                @php
                  $featText = $feat['text'] ?? '';
                  $featIcon = $feat['icon'] ?? 'lucide:check-circle-2';
                @endphp
                <div class="flex items-center gap-3">
                  <span style="color: #ff9900;" class="shrink-0 flex items-center">
                    <x-icon :name="$featIcon" class="w-5 h-5 text-[#ff9900]" />
                  </span>
                  <span>{{ $featText }}</span>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <!-- Hero Actions -->
        <div class="space-y-3 pt-4">
          <!-- Primary CTA -->
          @if($hasPrimaryUrl)
            <a href="{{ $primaryCtaUrl }}" @if($primaryCtaUrl === '#formaws') onclick="claimCreditsHandler()" @endif target="{{ $heroCtaBlock['target'] ?? '_self' }}" class="w-full py-4 rounded-xl font-black text-base transition-all text-center block shadow-lg active:scale-[0.99] cursor-pointer" style="background-color: #ff9900; color: #09090b;">
              {{ $heroCtaBlock['text'] ?? t('aws_credits.btn_claim', 'Claim Your Credits') }}
            </a>
          @else
            <div class="w-full py-4 rounded-xl font-black text-base text-center block shadow-lg select-none pointer-events-none" style="background-color: #ff9900; color: #09090b;">
              {{ $heroCtaBlock['text'] ?? t('aws_credits.btn_claim', 'Claim Your Credits') }}
            </div>
          @endif
          
          <!-- Secondary CTA -->
          @if($hasSecondaryUrl)
            <a href="{{ $secondaryCtaUrl }}" @if($secondaryCtaUrl === '#formaws') onclick="claimCreditsHandler()" @endif target="{{ $heroSecondaryCtaBlock['target'] ?? '_self' }}" class="w-full text-center text-sm font-semibold py-2 flex items-center justify-center gap-2 transition-all hover:text-white cursor-pointer" style="color: #cbd5e1;">
              <span>{{ $heroSecondaryCtaBlock['text'] ?? t('aws_credits.request_free_assessment', 'Request Free Assessment') }}</span>
              <!-- Desktop: Right Arrow animated towards form -->
              <span style="color: #ff9900;" class="arrow-desktop shrink-0 items-center">
                <x-icon name="lucide:arrow-right" class="w-5 h-5 text-[#ff9900]" />
              </span>
              <!-- Mobile/Tablet: Down Arrow animated towards form below -->
              <span style="color: #ff9900;" class="arrow-mobile shrink-0 items-center">
                <x-icon name="lucide:arrow-down" class="w-5 h-5 text-[#ff9900]" />
              </span>
            </a>
          @else
            <div class="w-full text-center text-sm font-semibold py-2 flex items-center justify-center gap-2 select-none pointer-events-none" style="color: #cbd5e1;">
              <span>{{ $heroSecondaryCtaBlock['text'] ?? t('aws_credits.request_free_assessment', 'Request Free Assessment') }}</span>
              <!-- Desktop: Right Arrow animated towards form -->
              <span style="color: #ff9900;" class="arrow-desktop shrink-0 items-center">
                <x-icon name="lucide:arrow-right" class="w-5 h-5 text-[#ff9900]" />
              </span>
              <!-- Mobile/Tablet: Down Arrow animated towards form below -->
              <span style="color: #ff9900;" class="arrow-mobile shrink-0 items-center">
                <x-icon name="lucide:arrow-down" class="w-5 h-5 text-[#ff9900]" />
              </span>
            </div>
          @endif
        </div>
      </div>

      <!-- Right Column: Light Assessment Form (#formaws) -->
      <div id="formaws" class="w-full lg:w-5/12 p-8 sm:p-10 lg:p-12 text-zinc-900 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-zinc-200 shadow-xl transition-all duration-500" style="background-color: #ffffff; color: #0f172a;">
        <div>
          <div class="mb-6">
            <h2 class="text-2xl font-black tracking-tight" style="color: #0f172a;">{{ $awsForm ? $awsForm->name : t('aws_credits.form_title', 'Start Your Free Credit Assessment') }}</h2>
            <p class="text-xs mt-1 font-medium" style="color: #64748b;">{{ $awsForm && $awsForm->description ? $awsForm->description : t('aws_credits.form_subtitle', 'Our AWS specialist will contact you within 1 business day.') }}</p>
          </div>

          @if($awsForm)
            @include('cdt::partials.tailwind-form', ['form' => $awsForm, 'variant' => 'light'])
          @else
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Assessment request submitted! Our AWS specialist will contact you shortly.');" class="space-y-4">
              <!-- First Name & Last Name -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                  <input type="text" required placeholder="{{ t('aws_credits.first_name', 'First Name') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
                <div>
                  <input type="text" required placeholder="{{ t('aws_credits.last_name', 'Last Name') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
              </div>

              <!-- Company Name & Job Title -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                  <input type="text" required placeholder="{{ t('aws_credits.company_name', 'Company Name') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
                <div>
                  <input type="text" required placeholder="{{ t('aws_credits.job_title', 'Job Title') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
              </div>

              <!-- Corporate Email -->
              <div>
                <input type="email" required placeholder="{{ t('aws_credits.corporate_email', 'Corporate Email') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
              </div>

              <!-- Phone Number & Industry -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                  <input type="tel" required placeholder="{{ t('aws_credits.phone_number', 'Phone Number') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
                <div>
                  <input type="text" required placeholder="{{ t('aws_credits.industry', 'Industry') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
                </div>
              </div>

              <!-- AWS Status -->
              <div>
                <input type="text" placeholder="{{ t('aws_credits.aws_status', 'AWS Status (Existing / New Customer)') }}" class="w-full px-4 py-3.5 text-sm rounded-xl border border-zinc-300 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition-all" style="background-color: #f8fafc; color: #0f172a; border-color: #cbd5e1;" />
              </div>

              <!-- Privacy Policy Consent Notice -->
              <p class="text-[11px] leading-tight pt-1" style="color: #64748b;">
                {{ t('aws_credits.privacy_policy_notice', 'By submitting your personal data, PT Central Data Technology and its affiliates collect and proceed with such data. Refer to: PT Central Data Technology\'s') }} <a href="#" class="underline font-semibold" style="color: #e30613;">{{ t('aws_credits.privacy_policy_link', 'Privacy Policy') }}</a>
              </p>

              <!-- reCAPTCHA Widget Box -->
              <div class="p-3.5 border rounded-xl flex items-center justify-between" style="background-color: #f8fafc; border-color: #cbd5e1;">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" required class="w-5 h-5 rounded border-zinc-300 cursor-pointer" style="accent-color: #e30613;" />
                  <span class="text-xs font-semibold select-none" style="color: #334155;">{{ t('aws_credits.recaptcha_label', "I'm not a robot") }}</span>
                </label>
                <div class="flex flex-col items-center justify-center pl-3 border-l" style="border-color: #cbd5e1;">
                  <x-icon name="lucide:shield-check" class="w-5 h-5" style="color: #94a3b8;" />
                  <span class="text-[9px] uppercase font-bold tracking-widest mt-0.5" style="color: #94a3b8;">reCAPTCHA</span>
                </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="w-full py-4 rounded-xl text-white font-extrabold text-base tracking-wider transition-all shadow-lg uppercase active:scale-[0.99] flex items-center justify-center gap-2 mt-2" style="background-color: #e30613; color: #ffffff;">
                <span>{{ t('aws_credits.btn_send_request', 'SEND ASSESSMENT REQUEST') }}</span>
                <x-icon name="lucide:send" class="w-4 h-4" />
              </button>
            </form>
          @endif
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Section 2: Zero Upfront Cost Banner (#182332 background) -->
<section class="py-20 text-white border-t border-b relative overflow-hidden" style="background-color: #182332; color: #ffffff; border-color: #2d3e56;">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="flex flex-col lg:flex-row items-center gap-12">
      
      <!-- Text Left -->
      <div class="w-full lg:w-4/12 space-y-4 text-center lg:text-left">
        <h2 class="text-3xl sm:text-4xl font-black leading-tight" style="color: #ffffff;">
          {{ $zeroCostHeadingText }}
        </h2>
        <p class="text-base leading-relaxed font-light" style="color: #cbd5e1;">
          {{ $zeroCostSubtitleText }}
        </p>
      </div>

      <!-- 3 Stat Cards Right (#222f42 card background) -->
      @if(!empty($zeroCostStats))
        <div class="w-full lg:w-8/12 grid grid-cols-1 sm:grid-cols-3 gap-6">
          @foreach($zeroCostStats as $statCard)
            @php
              $sStat = $statCard['stat'] ?? '';
              $sDesc = $statCard['description'] ?? '';
              $sIcon = $statCard['icon'] ?? 'lucide:sparkles';
            @endphp
            <div class="p-6 rounded-2xl border transition-all text-center space-y-3 shadow-lg group" style="background-color: #222f42; border-color: #2d3e56;">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform" style="background-color: rgba(255, 153, 0, 0.15); color: #ff9900;">
                <span style="color: #ff9900;" class="flex items-center justify-center">
                  <x-icon :name="$sIcon" class="w-6 h-6 text-[#ff9900]" />
                </span>
              </div>
              <span class="text-5xl font-black block" style="color: #ff9900;">{{ $sStat }}</span>
              <p class="text-xs font-bold" style="color: #e2e8f0;">{{ $sDesc }}</p>
            </div>
          @endforeach
        </div>
      @endif

    </div>
  </div>
</section>  <!-- Section 3: What You Get (Clean Standard 4-Column Cards) -->
<section class="py-24 relative border-t border-zinc-200" style="background-color: #f8fafc;">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-black" style="color: #0f172a;">{{ $whatYouGetHeaderBlock['main'] ?? 'What You Get' }}</h2>
      <div class="w-16 h-1 rounded-full mx-auto mt-4 mb-6" style="background-color: #e30613;"></div>
      <p class="text-base max-w-2xl mx-auto" style="color: #475569;">{{ $whatYouGetDescText }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      @foreach($whatYouGetCards as $card)
        @php
          $cTitle = $card['title'] ?? '';
          $cDesc = $card['description'] ?? '';
          $cIcon = $card['icon'] ?? 'lucide:coins';
        @endphp
        <div class="p-8 rounded-2xl border shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center flex flex-col justify-between group" style="background-color: #ffffff; border-color: #e2e8f0;">
          <div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:scale-110 transition-transform" style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #e30613;">
              <x-icon :name="$cIcon" class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-extrabold mb-3" style="color: #0f172a;">{{ $cTitle }}</h3>
            <p class="text-xs leading-relaxed" style="color: #475569;">
              {{ $cDesc }}
            </p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Section 4: What Qualifies as an AI Project? -->
<section class="py-24 relative border-t border-zinc-200" style="background-color: #ffffff;">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-black" style="color: #0f172a;">{{ $qualifiesAiHeaderBlock['main'] ?? 'What Qualifies as an AI Project?' }}</h2>
      <div class="w-16 h-1 rounded-full mx-auto mt-4 mb-6" style="background-color: #e30613;"></div>
      <p class="text-base max-w-2xl mx-auto" style="color: #475569;">{{ $qualifiesAiDescText }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      @foreach($qualifiesAiCards as $card)
        @php
          $cTitle = $card['title'] ?? '';
          $cDesc = $card['description'] ?? '';
          $cIcon = $card['icon'] ?? 'lucide:brain';
        @endphp
        <div class="p-8 rounded-2xl border shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center group" style="background-color: #f8fafc; border-color: #e2e8f0;">
          <div class="w-14 h-14 rounded-2xl border flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:scale-110 transition-transform" style="background-color: #ffffff; border-color: #cbd5e1; color: #e30613;">
            <x-icon :name="$cIcon" class="w-7 h-7" />
          </div>
          <h3 class="text-lg font-extrabold mb-3" style="color: #0f172a;">{{ $cTitle }}</h3>
          <p class="text-xs leading-relaxed" style="color: #475569;">
            {{ $cDesc }}
          </p>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Section 5: Support Workload -->
<section class="py-24 border-t border-zinc-200" style="background-color: #f8fafc;">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-black" style="color: #0f172a;">{{ $supportWorkloadHeaderBlock['main'] ?? 'Support Workload' }}</h2>
      <div class="w-16 h-1 rounded-full mx-auto mt-4 mb-6" style="background-color: #e30613;"></div>
      <p class="text-base max-w-2xl mx-auto" style="color: #475569;">{{ $supportWorkloadDescText }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-6xl mx-auto">
      @foreach($workloadSlugs as $wSlug => $defaultIcon)
        @php
          $product = $workloadProducts->get($wSlug);
          
          // Localized Title
          $productTitle = null;
          if ($product) {
              if (method_exists($product, 'getTranslation')) {
                  $productTitle = $product->getTranslation('title');
              }
              if (!$productTitle && is_array($product->title)) {
                  $productTitle = $product->title[app()->getLocale()] ?? $product->title['en'] ?? reset($product->title);
              }
              if (!$productTitle) {
                  $productTitle = $product->title;
              }
          }

          // Icon from CPT Meta DB
          $productIcon = $product->meta['icon'] ?? $product->meta['product_icon'] ?? $defaultIcon;
          
          // Dynamic URL
          $productUrl = $product ? url('/amazon-web-services/' . $product->slug) : url('/amazon-web-services/' . $wSlug);
        @endphp
        <a href="{{ $productUrl }}" class="p-6 rounded-2xl border shadow-sm hover:shadow-md transition-all flex items-center gap-4 group cursor-pointer" style="background-color: #ffffff; border-color: #e2e8f0;">
          <div class="w-12 h-12 rounded-xl border flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform" style="background-color: #fee2e2; border-color: #fca5a5; color: #e30613;">
            <x-icon :name="$productIcon" class="w-6 h-6" />
          </div>
          <span class="font-extrabold text-base" style="color: #0f172a;">{{ $productTitle ?? t('aws_credits.workload_' . $wSlug, 'Workload') }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- Section 6: Who Can Apply? -->
<section class="py-24 border-t border-zinc-200" style="background-color: #ffffff;">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-black" style="color: #0f172a;">{{ $eligibilityHeaderBlock['main'] ?? 'Who Can Apply?' }}</h2>
      <div class="w-16 h-1 rounded-full mx-auto mt-4 mb-6" style="background-color: #e30613;"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
      @foreach($eligibilityCards as $card)
        @php
          $cTitle = $card['title'] ?? '';
          $cIcon = $card['icon'] ?? 'lucide:check-square';
          $listIcon = $card['list_icon'] ?? 'lucide:check-circle-2';
          $rawItems = $card['list_items'] ?? '';
          $items = is_array($rawItems) ? $rawItems : array_filter(array_map('trim', explode("\n", (string)$rawItems)));
        @endphp
        <div class="p-8 rounded-2xl border space-y-5 hover:shadow-md transition-shadow" style="background-color: #f8fafc; border-color: #e2e8f0;">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl border flex items-center justify-center shrink-0" style="background-color: #fee2e2; border-color: #fca5a5; color: #e30613;">
              <x-icon :name="$cIcon" class="w-5 h-5" />
            </div>
            <h3 class="text-base font-extrabold uppercase tracking-wider" style="color: #0f172a;">{{ $cTitle }}</h3>
          </div>
          @if(!empty($items))
            <ul class="space-y-3 text-sm font-medium" style="color: #334155;">
              @foreach($items as $itemText)
                <li class="flex items-start gap-3">
                  <x-icon :name="$listIcon" class="w-4 h-4 shrink-0 mt-0.5" style="color: #e30613;" />
                  <span>{{ $itemText }}</span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Section 7: Program Terms -->
<section class="py-24 border-t border-zinc-200" style="background-color: #f8fafc;">
  <div class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-black" style="color: #0f172a;">{{ $programTermsHeaderBlock['main'] ?? 'Program Terms' }}</h2>
      <div class="w-16 h-1 rounded-full mx-auto mt-4 mb-6" style="background-color: #e30613;"></div>
    </div>

    @php
      $termsTitle = $programTermsCard['title'] ?? t('aws_credits.terms_box_title', 'AWS CDT CLOUD CREDIT PROGRAM - KEY TERMS');
      $termsIcon = $programTermsCard['icon'] ?? 'lucide:file-text';
      $listIcon = $programTermsCard['list_icon'] ?? 'lucide:info';
      $termsRaw = $programTermsCard['list_items'] ?? '';
      $termItems = is_array($termsRaw) ? $termsRaw : array_filter(array_map('trim', explode("\n", (string)$termsRaw)));
    @endphp
    <div class="p-8 sm:p-12 rounded-3xl border shadow-lg space-y-6 max-w-4xl mx-auto relative overflow-hidden" style="background-color: #ffffff; border-color: #e2e8f0;">
      <h3 class="text-sm font-extrabold uppercase tracking-wider border-b pb-4 flex items-center gap-2" style="color: #0f172a; border-color: #cbd5e1;">
        <x-icon :name="$termsIcon" class="w-5 h-5" style="color: #e30613;" />
        {{ $termsTitle }}
      </h3>

      @if(!empty($termItems))
        <ul class="space-y-4 text-sm leading-relaxed font-medium" style="color: #334155;">
          @foreach($termItems as $termText)
            <li class="flex items-start gap-3.5">
              <x-icon :name="$listIcon" class="w-5 h-5 shrink-0 mt-0.5" style="color: #e30613;" />
              <span>{{ $termText }}</span>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</section>

<script>
function claimCreditsHandler() {
  const formEl = document.getElementById('formaws');
  if (!formEl) return;

  // Scroll to form smoothly
  formEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

  // Focus the first input field after smooth scroll
  setTimeout(() => {
    const firstInput = formEl.querySelector('input');
    if (firstInput) {
      firstInput.focus();
    }
  }, 400);

  // Add temporary glow highlight around form
  formEl.style.boxShadow = '0 0 0 4px #ff9900, 0 25px 50px -12px rgba(0, 0, 0, 0.25)';
  setTimeout(() => {
    formEl.style.boxShadow = '';
  }, 2000);
}
</script>

@endsection
