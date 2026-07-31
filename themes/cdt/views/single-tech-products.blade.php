@extends('cdt::layouts.app')

@php
    $parentVendorSlug = $entry->getMeta('parent_vendor');
    $parentProduct = $entry->parentRelatedEntries()->first() ?? ($parentVendorSlug ? \App\Models\CptEntry::where('slug', $parentVendorSlug)->first() : null);
    $benefitsCards = $entry->getMeta('benefits_cards') ?? [];

    if (empty($benefitsCards)) {
        $benefitsCards = [
            [
                'icon' => 'shield-check',
                'title' => 'Adaptive Protections with Fast Onboarding Process',
                'description' => 'Ensure the latest cybersecurity updates are automatically applied to your apps, website server, and APIs with minimal configuration.'
            ],
            [
                'icon' => 'layers',
                'title' => 'Advanced API Discovery',
                'description' => 'Manage risk from unknown or new APIs while defending against malicious payloads.'
            ],
            [
                'icon' => 'activity',
                'title' => 'Deep Attack Visibility',
                'description' => 'Custom dashboards, real-time alerts, and SIEM integration provide detailed insight into attacks targeting your website servers and APIs.'
            ],
            [
                'icon' => 'cloud-lightning',
                'title' => 'Advanced Security Management',
                'description' => 'Benefit from flexible configuration and automated security processes to enhance cybersecurity management.'
            ],
            [
                'icon' => 'bot',
                'title' => 'Bot Mitigation',
                'description' => 'Detect and prevent malicious bot activities before they escalate into serious threats to your website servers.'
            ],
            [
                'icon' => 'server',
                'title' => 'DDoS Protection',
                'description' => 'Instantly stop network-layer DDoS attacks and mitigate application-layer attacks in seconds to protect your systems.'
            ]
        ];
    }

    $siblingProducts = collect();
    if ($parentProduct) {
        $siblingProducts = $parentProduct->relatedEntries('product_id')->where('cpt_entries.id', '!=', $entry->id)->get();
        if ($siblingProducts->isEmpty() && $parentProduct->slug) {
            $siblingProducts = \App\Models\CptEntry::where('id', '!=', $entry->id)
                ->whereHas('postType', fn($q) => $q->where('slug', 'tech-products'))
                ->where('meta->parent_vendor', $parentProduct->slug)
                ->get();
        }
    }
@endphp

@section('title', $entry->title . ($parentProduct ? ' - ' . $parentProduct->title : ''))

@section('content')
  <!-- Section 1: Sub-Product Hero -->
  <section class="relative w-full pt-4 lg:pt-8 pb-20 flex items-center justify-center overflow-hidden bg-white">
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-zinc-50 to-white"></div>
      <div class="absolute -top-48 -right-48 w-[600px] h-[600px] bg-[radial-gradient(circle,rgba(227,6,19,0.18)_0%,rgba(227,6,19,0)_70%)] rounded-full blur-3xl"></div>
      <div class="absolute bottom-[-10%] -left-48 w-[500px] h-[500px] bg-[radial-gradient(circle,rgba(227,6,19,0.10)_0%,rgba(227,6,19,0)_70%)] rounded-full blur-3xl"></div>
    </div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 w-full">
      <!-- Breadcrumbs -->
      <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-zinc-400 mb-10" aria-label="Breadcrumb">
        <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Home</a>
        <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ url('/technology-alliance') }}" class="hover:text-primary transition-colors">Technology Alliance</a>
        @if($parentProduct)
          <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <a href="{{ $parentProduct->getUrl() }}" class="hover:text-primary transition-colors">{{ $parentProduct->title }}</a>
        @endif
        <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-zinc-800 font-bold">{{ $entry->title }}</span>
      </nav>
  
      <div class="max-w-5xl mx-auto flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-100 border border-zinc-200 text-sm font-bold uppercase tracking-widest text-primary mb-8">
          <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> 
          {{ $entry->getMeta('hero_badge') ?: ($parentProduct ? $parentProduct->title . ' Solutions' : 'Akamai Solutions') }}
        </div>
  
        <div class="overflow-hidden mb-6">
          <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-zinc-900 leading-[1.1] tracking-tight">
            {{ $entry->getMeta('hero_title') ?: $entry->title }}
          </h1>
        </div>
  
        <div class="overflow-hidden mb-12 max-w-3xl">
          <p class="text-lg md:text-xl text-zinc-600 font-light leading-relaxed">
            {{ $entry->getMeta('hero_description') ?: ($entry->content ? strip_tags($entry->content) : $entry->excerpt) }}
          </p>
        </div>
  
        <div>
          <a href="#explore" class="inline-flex items-center justify-center px-10 py-4 font-bold text-white uppercase tracking-wider transition-all duration-300 bg-primary rounded-full shadow-lg shadow-primary/30 hover:bg-red-700 hover:shadow-xl hover:-translate-y-1 gap-3 group">
            {{ $entry->getMeta('hero_cta') ?: 'Call Us for FREE Consultation!' }}
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 2: Sub-Product Promo Banner -->
  <section id="banner" class="py-10 md:py-14 bg-gradient-to-r from-red-800 via-primary to-red-700 relative overflow-hidden z-20 shadow-inner">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-1/2 left-0 w-64 h-64 bg-white/10 rounded-full blur-[60px] -translate-y-1/2 -translate-x-1/2"></div>
      <div class="absolute top-1/2 right-0 w-96 h-96 bg-black/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/3"></div>
      <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] mix-blend-overlay"></div>
    </div>
  
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12">
        @php
            $bannerLogo = $entry->getMeta('banner_logo');
            if (! $bannerLogo && is_array($entry->getMeta('banner'))) {
                $bannerLogo = $entry->getMeta('banner')['logo'] ?? null;
            }
            if ($bannerLogo && ! str_starts_with($bannerLogo, 'http://') && ! str_starts_with($bannerLogo, 'https://')) {
                $cleanRel = ltrim(str_replace('/storage/', '', $bannerLogo), '/');
                $unhashedRel = preg_replace('/-\d+-[a-zA-Z0-9]+\.([a-zA-Z0-9]+)$/', '.$1', $cleanRel);
                if (! file_exists(public_path('storage/'.$cleanRel)) && ! file_exists(public_path('storage/'.$unhashedRel)) && ! file_exists(public_path($bannerLogo))) {
                    $bannerLogo = null;
                }
            }
            if (! $bannerLogo) {
                $bannerLogo = $entry->featured_image ?: ($parentProduct?->featured_image ?: '');
            }
        @endphp
        @if($bannerLogo)
        <div class="flex-shrink-0">
          <div class="w-24 h-24 md:w-28 md:h-28 bg-white rounded-full flex items-center justify-center p-4 shadow-[0_10px_25px_rgba(0,0,0,0.3)] ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500">
            <img src="{{ resolve_block_asset($bannerLogo) }}" alt="{{ $entry->title }} Logo" class="w-full h-auto object-contain drop-shadow-sm" />
          </div>
        </div>
        @endif

        <div class="flex-1 text-center md:text-left">
          <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/20 border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white mb-3 backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Limited Time Offer
          </div>
          <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-2 tracking-tight">
            {!! $entry->getMeta('banner_headline') ?: 'Start Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-100">30-Day Free Trial</span>' !!}
          </h2>
          <p class="text-white/80 text-base md:text-lg font-light max-w-2xl mx-auto md:mx-0">
            {{ $entry->getMeta('banner_description') ?: 'Get a Free Proof of Concept (POC) and Assessment for ' . $entry->title . ($parentProduct ? ' from ' . $parentProduct->title : '') . '.' }}
          </p>
        </div>

        <div class="flex-shrink-0 mt-4 md:mt-0">
          <a href="#explore" class="inline-flex items-center justify-center px-8 py-4 font-bold text-primary transition-all duration-300 bg-white rounded-full hover:bg-zinc-100 hover:scale-105 shadow-[0_10px_30px_rgba(0,0,0,0.2)] group/btn relative overflow-hidden">
            <span class="relative z-10 flex items-center gap-2 text-base tracking-wide uppercase">
              {{ $entry->getMeta('banner_cta') ?: 'Get Started Today' }}
              <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 3: Sub-Product About -->
  <section class="py-24 md:py-32 bg-zinc-50 relative border-b border-zinc-100">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row lg:items-start items-center gap-16 lg:gap-24 relative">
        @php
            $aboutImg = $entry->getMeta('about_image');
            if (! $aboutImg && is_array($entry->getMeta('about'))) {
                $aboutImg = $entry->getMeta('about')['image'] ?? null;
            }
        @endphp
        @if($aboutImg)
        <div class="w-full lg:w-1/2 lg:sticky lg:top-32">
          <div class="relative w-full aspect-square md:aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl border border-zinc-200/80 bg-gradient-to-br from-primary/5 to-zinc-100 flex items-center justify-center">
            <img src="{{ resolve_block_asset($aboutImg) }}" alt="{{ $entry->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
          </div>
        </div>
        @endif

        <div class="w-full {{ $aboutImg ? 'lg:w-1/2' : 'w-full' }} flex flex-col justify-center">
          <h2 class="text-4xl font-light text-zinc-500 leading-tight">About <br>
            <span class="font-bold text-zinc-900">{{ $entry->getMeta('about_title') ?: $entry->title }}</span>
          </h2>
          <div class="h-1 w-16 bg-primary mt-4 mb-8"></div>

          <div class="space-y-6 text-zinc-600 text-base md:text-lg font-light leading-relaxed mb-10">
            {!! preg_replace('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', '', $entry->getMeta('about_content') ?: $entry->content) !!}
          </div>

          <div>
            <a href="#explore" class="inline-flex items-center justify-center bg-primary hover:bg-red-700 text-white px-8 py-4 font-bold uppercase tracking-wide transition-colors rounded-full shadow-lg shadow-primary/30 hover:shadow-xl group">
              {{ $entry->getMeta('about_cta') ?: 'Talk to Our Experts' }}
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 4: Benefits Cards Section -->
  <section class="relative py-24 md:py-32 bg-white">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgc3Ryb2tlPSIjMDAwMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIj48cGF0aCBkPSJNMCAwdjYwaDYwIi8+PC9nPjwvc3ZnPg==')] mix-blend-multiply opacity-50"></div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="mb-16 text-center">
        @php
          $rawBenefitsTitle = $entry->getMeta('benefits_title') ?: $entry->title;
          $cleanBenefitsTitle = trim(preg_replace('/^(Benefits\s+of|Manfaat)\s+/i', '', $rawBenefitsTitle));
          $prefix = app()->getLocale() === 'id' ? 'Manfaat' : 'Benefits of';
        @endphp
        <h2 class="text-4xl font-light text-zinc-500 leading-tight">{{ $prefix }} <br>
          <span class="font-bold text-gray-900">{{ $cleanBenefitsTitle }}</span>
        </h2>
        <div class="h-1 w-16 bg-primary mt-4 mx-auto"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        @foreach($benefitsCards as $card)
        <div class="group bg-zinc-50 border border-zinc-200/80 p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          @if(!empty($card['icon']))
          <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
            <x-icon :name="'lucide:' . $card['icon']" class="w-6 h-6 text-primary" />
          </div>
          @endif
          <h3 class="text-xl font-bold text-zinc-900 mb-3 leading-snug">{{ $card['title'] ?? '' }}</h3>
          <p class="text-zinc-600 text-base leading-relaxed">{{ $card['description'] ?? '' }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Section 5: Customer Success Section -->
  <section class="py-24 md:py-32 bg-zinc-50 relative border-t border-zinc-100">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="mb-16">
        <h2 class="text-4xl font-light text-zinc-500 leading-tight">Customer Success <br>
          <span class="font-bold text-gray-900">Customer Success</span>
        </h2>
        <div class="h-1 w-16 bg-primary mt-4"></div>
      </div>

      <div class="space-y-12">
        <!-- Story 1 -->
        <div class="flex flex-col lg:flex-row items-center gap-10 p-10 bg-white rounded-3xl border border-zinc-200/80 hover:shadow-xl transition-shadow duration-300">
          <div class="w-full lg:w-3/5">
            <h4 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4 leading-tight">Finanstra Ushers in The Future of Banking - Securely</h4>
            <p class="text-zinc-600 text-base leading-relaxed">Fintech leader protects open finance apps and APIs with Akamai</p>
          </div>
          <div class="w-full lg:w-2/5 lg:border-l border-zinc-200 lg:pl-10">
            <ul class="space-y-4 mb-8">
              <li class="flex items-center gap-3 text-zinc-800 font-medium">
                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Stronger Security
              </li>
              <li class="flex items-center gap-3 text-zinc-800 font-medium">
                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Faster Innovation
              </li>
            </ul>
          </div>
        </div>

        <!-- Story 2 -->
        <div class="flex flex-col lg:flex-row items-center gap-10 p-10 bg-white rounded-3xl border border-zinc-200/80 hover:shadow-xl transition-shadow duration-300">
          <div class="w-full lg:w-3/5">
            <h4 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4 leading-tight">Xcaret's Travel Sites Stay Fast and Secure with Akamai's Security Solution</h4>
            <p class="text-zinc-600 text-base leading-relaxed">Mexican tourism giant Group Xcaret relies on Akamai for app and API protection, bot migration, fast and seamless site delivery</p>
          </div>
          <div class="w-full lg:w-2/5 lg:border-l border-zinc-200 lg:pl-10">
            <ul class="space-y-4 mb-8">
              <li class="flex items-center gap-3 text-zinc-800 font-medium">
                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                100% Controlled Attack Environment
              </li>
              <li class="flex items-center gap-3 text-zinc-800 font-medium">
                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Bot Mitigation
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 6: Explore & Form Section -->
  <section id="explore" class="relative bg-zinc-50 py-20 md:py-28 overflow-hidden border-t border-zinc-100">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-1/2 left-0 w-[500px] h-[500px] bg-primary/3 rounded-full blur-[130px] opacity-40 -translate-y-1/2 -translate-x-1/2"></div>
      <div class="absolute top-1/2 right-0 w-[500px] h-[500px] bg-zinc-200/40 rounded-full blur-[130px] opacity-30 -translate-y-1/2 translate-x-1/2"></div>
    </div>
  
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
  
        <!-- Left Column: Explore Features -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center">
          <div class="mb-10">
            <h2 class="text-4xl font-light text-zinc-500 leading-tight">
              Explore {{ $parentProduct ? $parentProduct->title : 'Akamai' }}<br>
              <span class="font-bold text-zinc-900">with CDT</span>
            </h2>
            <div class="h-1 bg-primary mt-4 w-16"></div>
          </div>
  
          <div class="space-y-8">
            <!-- Feature 1 -->
            <div class="flex items-start gap-5 group">
              <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div>
                <h4 class="text-lg font-bold text-zinc-900 mb-1">Advanced Action and Review</h4>
                <p class="text-base text-zinc-500 font-light leading-relaxed">PT Central Data Technology (CDT) is a subsidiary of the CTI Group that focuses on distributing IT infrastructure solutions to customers.</p>
              </div>
            </div>
  
            <!-- Feature 2 -->
            <div class="flex items-start gap-5 group">
              <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
              </div>
              <div>
                <h4 class="text-lg font-bold text-zinc-900 mb-1">Understand IT Expert</h4>
                <p class="text-base text-zinc-500 font-light leading-relaxed">By providing IT experts, we have secured CDT's presence in a variety of industries in Indonesia, Malaysia, and other countries in the world to overcome challenges related to digital operations.</p>
              </div>
            </div>
  
            <!-- Feature 3 -->
            <div class="flex items-start gap-5 group">
              <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
              </div>
              <div>
                <h4 class="text-lg font-bold text-zinc-900 mb-1">Certified Specialist</h4>
                <p class="text-base text-zinc-500 font-light leading-relaxed">CDT IT specialists are certified to ensure solution quality follows with strict implementation standards.</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Right Column: Form Card -->
        <div class="w-full lg:w-1/2">
          <div class="bg-white rounded-3xl border border-zinc-200/60 p-8 md:p-12 shadow-sm">
            <div class="mb-8">
              <span class="text-xs font-bold text-primary uppercase tracking-widest block mb-2">Request Consultation</span>
              <h3 class="text-2xl font-bold text-gray-900">Manage Your Business With Us!</h3>
              <p class="text-sm text-zinc-400 mt-1 font-light">Fill out the fields below, and our solutions team will connect with you.</p>
            </div>

            @php
              $theme = active_theme();
              $assignments = setting("theme_{$theme->slug}_form_assignments", []);
              $formId = $assignments['alliance_form'] ?? $assignments['gated_content_form'] ?? $assignments['contact_form'] ?? null;
              $formModel = $formId ? \App\Models\Form::where('id', $formId)->where('is_active', true)->with('fields')->first() : null;
            @endphp

            @if($formModel)
              @include('cdt::partials.tailwind-form', ['form' => $formModel, 'entry' => $entry])
            @else
              <a href="{{ url('/contact') }}" class="block text-center bg-primary text-white font-bold py-4 px-6 rounded-xl text-sm uppercase tracking-wider">
                Contact Sales
              </a>
            @endif
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Section 7: See More Solutions -->
  @if($siblingProducts->isNotEmpty())
  <section class="py-24 relative overflow-hidden bg-zinc-50/50">
    <div class="absolute inset-0 bg-testimonial-image opacity-[0.5] bg-cover bg-center blur-sm pointer-events-none" style="background-image: url('{{ asset('themes/cdt/assets/bg-testimonial-CvlJnS23.webp') }}');"></div>

    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="mb-16 text-center md:text-left">
        <h2 class="text-4xl font-light text-zinc-500 leading-tight">See More <span class="font-bold text-gray-900">{{ $parentProduct ? $parentProduct->title : '' }} Solutions</span></h2>
        <div class="h-1 w-16 bg-primary mt-4"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($siblingProducts as $sibling)
        <div onclick="window.location.href='{{ $sibling->getUrl() }}'" class="bg-white p-10 md:p-12 rounded-3xl border border-zinc-200/80 shadow-sm flex flex-col items-center text-center group hover:-translate-y-1 hover:shadow-xl transition-all duration-300 cursor-pointer">
          <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
          </div>
          <h3 class="text-xl font-bold text-zinc-900 mb-3">{{ $sibling->title }}</h3>
          <p class="text-zinc-600 text-base leading-relaxed mb-8">{{ $sibling->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($sibling->content), 120) }}</p>
          <a href="{{ $sibling->getUrl() }}" class="inline-block bg-primary hover:bg-red-700 text-white text-xs font-bold py-3 px-8 rounded-full uppercase tracking-wider transition-colors mt-auto shadow-md hover:shadow-lg transform hover:-translate-y-0.5 duration-300">Read More</a>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif
@endsection
