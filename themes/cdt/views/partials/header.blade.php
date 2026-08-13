@php
    $currentLocale = app()->getLocale();
    $altLocale = $currentLocale === 'en' ? 'id' : 'en';

    // Technology Alliance CPT & Entries
    $allianceCpt = \App\Models\CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance'])->first();
    $allianceHasArchive = (bool)($allianceCpt && $allianceCpt->has_archive);
    $allianceCptUrl = $allianceHasArchive ? localized_url('/technology-alliance') : null;
    $allianceProducts = $allianceCpt ? \App\Models\CptEntry::published()
        ->where('post_type_id', $allianceCpt->id)
        ->orderBy('menu_order')
        ->orderBy('title')
        ->limit(14)
        ->get() : collect();

    // Solutions CPT & Entries
    $solutionCpt = \App\Models\CustomPostType::whereIn('slug', ['solutions', 'solution'])->first();
    $solutionHasArchive = (bool)($solutionCpt && $solutionCpt->has_archive);
    $solutionCptUrl = $solutionHasArchive ? localized_url('/solution') : null;
    $solutionCategories = $solutionCpt ? \App\Models\CptEntry::published()
        ->where('post_type_id', $solutionCpt->id)
        ->whereNull('parent_id')
        ->orderBy('menu_order')
        ->orderBy('title')
        ->get() : collect();

    // Industry CPT & Entries
    $industryCpt = \App\Models\CustomPostType::whereIn('slug', ['industry', 'industries'])->first();
    $industryHasArchive = (bool)($industryCpt && $industryCpt->has_archive);
    $industryCptUrl = $industryHasArchive ? localized_url('/industry') : null;
    $industryItems = $industryCpt ? \App\Models\CptEntry::published()
        ->where('post_type_id', $industryCpt->id)
        ->whereNull('parent_id')
        ->orderBy('menu_order')
        ->orderBy('title')
        ->get() : collect();

    // Dynamic Blog URL & Title from Posts Settings
    $blogSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveSlug($currentLocale) : 'blog-news';
    $blogTitle = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveTitle($currentLocale) : t('nav.blog_news', 'Blog & News');
    $blogUrl = localized_url('/' . $blogSlug);

    // Site logo setting fallback
    $siteLogoSetting = setting('site_logo');
    $siteLogoUrl = $siteLogoSetting ? resolve_block_asset($siteLogoSetting) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');
@endphp

<!-- Mobile Top Header Bar -->
<div class="lg:hidden bg-white border-b border-zinc-100 sticky top-0 z-[100]">
  <div class="flex items-center justify-between px-4 py-3">
    <!-- Logo -->
    <a href="{{ localized_url('/') }}" title="Central Data Technology - Home" aria-label="Home" class="flex-shrink-0">
      <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Central Data Technology') }}" title="{{ setting('site_name', 'Central Data Technology') }}" class="h-12 w-auto object-contain">
    </a>
    <!-- Right Actions: Language Switcher & Hamburger Toggle -->
    <div class="flex items-center gap-3">
      @if(is_locale_available_for_current_page('id'))
      <div class="flex items-center gap-2 text-[13px] font-bold">
        @if($currentLocale === 'en')
          <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
        @else
          <a href="{{ current_page_localized_url('en') }}" title="Switch to English" aria-label="Switch to English" class="text-zinc-400 hover:text-zinc-800 transition-colors">EN</a>
        @endif
        <span class="text-zinc-300">|</span>
        @if($currentLocale === 'id')
          <span class="text-primary border-b-2 border-primary cursor-default">ID</span>
        @else
          <a href="{{ current_page_localized_url('id') }}" title="Beralih ke Bahasa Indonesia" aria-label="Beralih ke Bahasa Indonesia" class="text-zinc-400 hover:text-zinc-800 transition-colors">ID</a>
        @endif
      </div>
      @else
      <div class="flex items-center gap-2 text-[13px] font-bold">
        <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
      </div>
      @endif


    </div>
  </div>
</div>

<header id="main-header" 
  x-data="{ 
    visible: true, 
    lastScrollY: 0,
    init() {
      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;
        if (currentScrollY <= 20) {
          this.visible = true;
        } else if (currentScrollY > this.lastScrollY && currentScrollY > 60) {
          /* Scroll DOWN -> Hide header */
          this.visible = false;
        } else if (currentScrollY < this.lastScrollY) {
          /* Scroll UP -> Show header */
          this.visible = true;
        }
        this.lastScrollY = currentScrollY;
      }, { passive: true });
    }
  }"
  :class="visible ? 'translate-y-0' : '-translate-y-full'"
  class="hidden lg:block fixed w-full top-0 z-[100] transition-transform duration-300 ease-in-out shadow-sm"
>
  @auth
    @include('cdt::partials.admin-bar')
  @endauth

  <div class="bg-white border-b border-zinc-100">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <nav class="flex items-center justify-between h-20">
      <!-- Logo -->
      <a href="{{ localized_url('/') }}" title="Central Data Technology - Home" aria-label="Home" class="flex-shrink-0">
        <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Central Data Technology') }}" title="{{ setting('site_name', 'Central Data Technology') }}" class="h-16 w-auto object-contain">
      </a>

      <!-- Desktop Menu -->
      <ul class="hidden lg:flex items-center gap-6 text-[13px] font-bold text-zinc-800 uppercase tracking-wide h-full">
        
        <!-- About Us Dropdown -->
        <li class="group relative flex items-center h-full py-6">
          <span class="cursor-pointer hover:text-primary transition duration-300 flex items-center gap-1">
            {{ t('nav.about_us', 'About Us') }}
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </span>
          <div class="absolute left-0 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-56 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2 flex flex-col normal-case tracking-normal">
              <a href="{{ localized_url('/about-us') }}" title="{{ t('nav.overview', 'Overview') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.overview', 'Overview') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ localized_url('/about-management') }}" title="{{ t('nav.about_management', 'About Management') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.about_management', 'About Management') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
            </div>
          </div>
        </li>

        <!-- Technology Alliance (Mega Menu - Split Editorial Design) -->
        <li class="group relative flex items-center h-full py-6">
          @if($allianceHasArchive && $allianceCptUrl)
            <a href="{{ $allianceCptUrl }}" class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.technology_alliance', 'Technology Alliance') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          @else
            <span class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.technology_alliance', 'Technology Alliance') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </span>
          @endif
          <div
            class="absolute left-1/2 -translate-x-1/2 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-[900px] max-w-[90vw] transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex">
              <div
                class="w-1/3 bg-gradient-to-bl from-primary to-zinc-900 p-8 text-white relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div>
                  <span class="text-xl font-bold mb-4 relative z-10 leading-tight block">{{ t('nav.technology_alliance', 'Technology Alliance') }}</span>
                  <p
                    class="text-sm text-white/80 leading-relaxed relative z-10 normal-case font-normal tracking-normal">
                    {{ t('nav.technology_alliance_desc', 'Explore our comprehensive ecosystem of global technology partners designed to empower your business transformation.') }}</p>
                </div>
              </div>
              <div class="w-2/3 p-8 normal-case tracking-normal">
                <div class="grid grid-cols-2 gap-y-2 gap-x-6">
                  @forelse($allianceProducts as $prod)
                    @php
                      $prodMeta = $prod->meta ?? [];
                      $prodBadges = $prod->getMeta('badge_text') ?? $prodMeta['badge_text'] ?? $prodMeta['badges'] ?? $prodMeta['badge'] ?? [];
                      $validBadges = [];
                      if (is_array($prodBadges)) {
                          foreach ($prodBadges as $b) {
                              $txt = is_array($b) ? ($b['text'] ?? $b['title'] ?? '') : (string)$b;
                              if (!empty(trim($txt))) {
                                  $validBadges[] = trim($txt);
                              }
                          }
                      } elseif (is_string($prodBadges) && !empty(trim($prodBadges))) {
                          $validBadges[] = trim($prodBadges);
                      }
                    @endphp

                    @if(!empty($validBadges))
                      <a href="{{ $prod->getUrl() }}" title="{{ $prod->title }}"
                        class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2 px-3 -mx-3 hover:bg-gray-50 rounded-md flex flex-col justify-center items-start gap-1.5 group/link">
                        <span>{{ $prod->title }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                          @foreach($validBadges as $badgeTxt)
                            <span
                              class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full group-hover/link:bg-red-100 group-hover/link:text-primary transition-colors whitespace-nowrap">{{ $badgeTxt }}</span>
                          @endforeach
                        </div>
                      </a>
                    @else
                      <a href="{{ $prod->getUrl() }}" title="{{ $prod->title }}"
                        class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex justify-between items-center group/link">
                        <span>{{ $prod->title }}</span>
                        <span
                          class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
                      </a>
                    @endif
                  @empty
                    <span class="text-sm text-gray-500 py-2">{{ t('nav.no_technology_alliances', 'No technology partners available') }}</span>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </li>

        <!-- Solutions Mega Menu -->
        <li class="group relative flex items-center h-full py-6">
          @if($solutionHasArchive && $solutionCptUrl)
            <a href="{{ $solutionCptUrl }}" title="{{ t('nav.solutions', 'Solutions') }}" class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.solutions', 'Solutions') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          @else
            <span class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.solutions', 'Solutions') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </span>
          @endif
          <div class="absolute left-1/2 -translate-x-1/2 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-[700px] max-w-[90vw] transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex">
              <div class="w-2/5 bg-gradient-to-bl from-primary to-zinc-900 p-8 text-white relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div>
                  <span class="text-xl font-bold mb-4 relative z-10 leading-tight block">{{ t('nav.solutions', 'Solutions') }}</span>
                  <p class="text-sm text-white/80 leading-relaxed relative z-10 normal-case font-normal tracking-normal">{{ t('nav.solutions_desc', 'Discover our comprehensive range of IT solutions tailored to drive your business forward.') }}</p>
                </div>
              </div>
              <div class="w-3/5 p-8 normal-case tracking-normal">
                <div class="flex flex-col gap-y-2">
                  @forelse($solutionCategories as $sol)
                    @php
                      $solMeta = $sol->meta ?? [];
                      $solBadges = $sol->getMeta('badge_text') ?? $solMeta['badge_text'] ?? $solMeta['badges'] ?? $solMeta['badge'] ?? [];
                      $validSolBadges = [];
                      if (is_array($solBadges)) {
                          foreach ($solBadges as $b) {
                              $txt = is_array($b) ? ($b['text'] ?? $b['title'] ?? '') : (string)$b;
                              if (!empty(trim($txt))) {
                                  $validSolBadges[] = trim($txt);
                              }
                          }
                      } elseif (is_string($solBadges) && !empty(trim($solBadges))) {
                          $validSolBadges[] = trim($solBadges);
                      }
                    @endphp

                    @if(!empty($validSolBadges))
                      <a href="{{ $sol->getUrl() }}" title="{{ $sol->title }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex flex-col justify-center items-start gap-1.5 group/link">
                        <span>{{ $sol->title }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                          @foreach($validSolBadges as $badgeTxt)
                            <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full group-hover/link:bg-red-100 group-hover/link:text-primary transition-colors whitespace-nowrap">{{ $badgeTxt }}</span>
                          @endforeach
                        </div>
                      </a>
                    @else
                      <a href="{{ $sol->getUrl() }}" title="{{ $sol->title }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex justify-between items-center group/link">
                        <span>{{ $sol->title }}</span>
                        <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
                      </a>
                    @endif
                  @empty
                    <span class="text-sm text-gray-500 py-2">{{ t('nav.no_solutions', 'No solutions available') }}</span>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </li>

        <!-- Industry Mega Menu -->
        <li class="group relative flex items-center h-full py-6">
          @if($industryHasArchive && $industryCptUrl)
            <a href="{{ $industryCptUrl }}" title="{{ t('nav.industry', 'Industry') }}" class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.industry', 'Industry') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          @else
            <span class="hover:text-primary transition duration-300 flex items-center gap-1 cursor-pointer">
              {{ t('nav.industry', 'Industry') }}
              <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </span>
          @endif
          <div class="absolute left-1/2 -translate-x-1/2 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-[850px] max-w-[90vw] transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex">
              <div class="w-1/3 bg-gradient-to-bl from-primary to-zinc-900 p-8 text-white relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div>
                  <span class="text-xl font-bold mb-4 relative z-10 leading-tight block">{{ t('nav.industry', 'Industry') }}</span>
                  <p class="text-sm text-white/80 leading-relaxed relative z-10 normal-case font-normal tracking-normal">{{ t('nav.industry_desc', 'Explore tailored IT solutions designed to meet the unique challenges of your specific industry.') }}</p>
                </div>
              </div>
              <div class="w-2/3 p-8 normal-case tracking-normal">
                <div class="grid grid-cols-2 gap-y-2 gap-x-6">
                  @forelse($industryItems as $ind)
                    @php
                      $indMeta = $ind->meta ?? [];
                      $indBadges = $ind->getMeta('badge_text') ?? $indMeta['badge_text'] ?? $indMeta['badges'] ?? $indMeta['badge'] ?? [];
                      $validIndBadges = [];
                      if (is_array($indBadges)) {
                          foreach ($indBadges as $b) {
                              $txt = is_array($b) ? ($b['text'] ?? $b['title'] ?? '') : (string)$b;
                              if (!empty(trim($txt))) {
                                  $validIndBadges[] = trim($txt);
                              }
                          }
                      } elseif (is_string($indBadges) && !empty(trim($indBadges))) {
                          $validIndBadges[] = trim($indBadges);
                      }
                    @endphp

                    @if(!empty($validIndBadges))
                      <a href="{{ $ind->getUrl() }}" title="{{ $ind->title }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex flex-col justify-center items-start gap-1.5 group/link">
                        <span>{{ $ind->title }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                          @foreach($validIndBadges as $badgeTxt)
                            <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full group-hover/link:bg-red-100 group-hover/link:text-primary transition-colors whitespace-nowrap">{{ $badgeTxt }}</span>
                          @endforeach
                        </div>
                      </a>
                    @else
                      <a href="{{ $ind->getUrl() }}" title="{{ $ind->title }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex justify-between items-center group/link">
                        <span>{{ $ind->title }}</span>
                        <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
                      </a>
                    @endif
                  @empty
                    <span class="text-sm text-gray-500 py-2">{{ t('nav.no_industries', 'No industries available') }}</span>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </li>

        <!-- Insight Simple Dropdown -->
        <li class="group relative flex items-center h-full py-6">
          <span class="cursor-pointer hover:text-primary transition duration-300 flex items-center gap-1">
            {{ t('nav.insights', 'Insight') }}
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </span>
          <div class="absolute left-0 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-56 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2 flex flex-col normal-case tracking-normal">
              <a href="{{ localized_url('/customer-success') }}" title="{{ t('nav.customer_success', 'Customer Success') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.customer_success', 'Customer Success') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ $blogUrl }}" title="{{ $blogTitle }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ $blogTitle }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ localized_url('/video') }}" title="{{ t('nav.video', 'Video') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.video', 'Video') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
            </div>
          </div>
        </li>

        <!-- Careers Simple Dropdown -->
        <li class="group relative flex items-center h-full py-6">
          <a href="{{ localized_url('/careers') }}" class="hover:text-primary transition duration-300 flex items-center gap-1">
            {{ t('nav.careers', 'Careers') }}
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </a>
          <div class="absolute left-0 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-56 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2 flex flex-col normal-case tracking-normal">
              <a href="{{ localized_url('/careers#why-cdt') }}" title="{{ t('nav.why_cdt', 'Why CDT') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.why_cdt', 'Why CDT') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ localized_url('/careers#life-at-cdt') }}" title="{{ t('nav.life_at_cdt', 'Life at CDT') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.life_at_cdt', 'Life at CDT') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ localized_url('/careers#job-vacancy') }}" title="{{ t('nav.job_vacancy', 'Job Vacancy') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.job_vacancy', 'Job Vacancy') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
            </div>
          </div>
        </li>

        <!-- Contact Us -->
        <li>
          <a href="{{ localized_url('/contact-us') }}" title="{{ t('nav.contact_us', 'Contact Us') }}" class="hover:text-primary transition duration-300">{{ t('nav.contact_us', 'Contact Us') }}</a>
        </li>
      </ul>

      <!-- Right Header & Language Switcher -->
      <div class="hidden lg:flex items-center gap-6">
        <!-- Language Switcher -->
        @if(is_locale_available_for_current_page('id'))
        <div class="flex items-center gap-2 text-[13px] font-bold">
          @if($currentLocale === 'en')
            <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
          @else
            <a href="{{ current_page_localized_url('en') }}" title="Switch to English" aria-label="Switch to English" class="text-zinc-400 hover:text-zinc-800 transition-colors">EN</a>
          @endif
          <span class="text-zinc-300">|</span>
          @if($currentLocale === 'id')
            <span class="text-primary border-b-2 border-primary cursor-default">ID</span>
          @else
            <a href="{{ current_page_localized_url('id') }}" title="Beralih ke Bahasa Indonesia" aria-label="Beralih ke Bahasa Indonesia" class="text-zinc-400 hover:text-zinc-800 transition-colors">ID</a>
          @endif
        </div>
        @else
        <div class="flex items-center gap-2 text-[13px] font-bold">
          <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
        </div>
        @endif
      </div>
    </nav>
  </div>
</header>
@guest
<div class="h-20 hidden lg:block"></div>
@endguest
