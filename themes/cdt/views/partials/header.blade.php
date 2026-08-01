@php
    $currentLocale = app()->getLocale();
    $altLocale = $currentLocale === 'en' ? 'id' : 'en';

    // Get active products/technology alliances for Technology Alliance Mega Menu
    $allianceProducts = \App\Models\CptEntry::published()
        ->whereHas('postType', fn($q) => $q->whereIn('slug', ['technology-alliance', 'products']))
        ->orderBy('menu_order')
        ->orderBy('title')
        ->limit(12)
        ->get();

    // Site logo setting fallback
    $siteLogoSetting = setting('site_logo');
    $siteLogoUrl = $siteLogoSetting ? resolve_block_asset($siteLogoSetting) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');
@endphp

<!-- Mobile Top Header Bar -->
<div class="lg:hidden bg-white border-b border-zinc-100">
  <div class="flex items-center justify-between px-4 py-3">
    <!-- Logo -->
    <a href="{{ localized_url('/') }}" class="flex-shrink-0">
      <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Central Data Technology') }}" class="h-12 w-auto object-contain">
    </a>
    <!-- Language Switcher -->
    @if(is_locale_available_for_current_page('id'))
    <div class="flex items-center gap-2 text-[13px] font-bold">
      @if($currentLocale === 'en')
        <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
      @else
        <a href="{{ current_page_localized_url('en') }}" class="text-zinc-400 hover:text-zinc-800 transition-colors">EN</a>
      @endif
      <span class="text-zinc-300">|</span>
      @if($currentLocale === 'id')
        <span class="text-primary border-b-2 border-primary cursor-default">ID</span>
      @else
        <a href="{{ current_page_localized_url('id') }}" class="text-zinc-400 hover:text-zinc-800 transition-colors">ID</a>
      @endif
    </div>
    @else
    <div class="flex items-center gap-2 text-[13px] font-bold">
      <span class="text-primary border-b-2 border-primary cursor-default">EN</span>
    </div>
    @endif
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
          // Scroll DOWN -> Hide header
          this.visible = false;
        } else if (currentScrollY < this.lastScrollY) {
          // Scroll UP -> Show header
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
      <a href="{{ localized_url('/') }}" class="flex-shrink-0">
        <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Central Data Technology') }}" class="h-16 w-auto object-contain">
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
              <a href="{{ url('/about') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.overview', 'Overview') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ url('/management') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                {{ t('nav.about_management', 'About Management') }} <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
            </div>
          </div>
        </li>

        <!-- Technology Alliance (Mega Menu - Split Editorial Design) -->
        <li class="group relative flex items-center h-full py-6">
          <span class="cursor-pointer hover:text-primary transition duration-300 flex items-center gap-1">
            {{ t('nav.technology_alliance', 'Technology Alliance') }}
            <svg
              class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </span>
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
              <div class="w-2/3 p-8">
                <div class="grid grid-cols-2 gap-y-2 gap-x-6">
                  @forelse($allianceProducts as $prod)
                    @php
                      $prodMeta = $prod->meta ?? [];
                      $prodBadges = $prodMeta['badges'] ?? [];
                      $validBadges = [];
                      if (is_array($prodBadges)) {
                          foreach ($prodBadges as $b) {
                              $txt = is_array($b) ? ($b['text'] ?? $b['title'] ?? '') : (string)$b;
                              if (!empty(trim($txt))) {
                                  $validBadges[] = trim($txt);
                              }
                          }
                      }
                    @endphp

                    @if(!empty($validBadges))
                      <a href="{{ $prod->getUrl() }}"
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
                      <a href="{{ $prod->getUrl() }}"
                        class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex justify-between items-center group/link">
                        <span>{{ $prod->title }}</span>
                        <span
                          class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
                      </a>
                    @endif
                  @empty
                    <a href="{{ url('/technology-alliance') }}"
                      class="text-sm font-semibold text-gray-700 hover:text-primary border-b border-gray-200 py-2.5 px-3">{{ t('nav.all_technology_partners', 'All Technology Partners') }} →</a>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </li>

        <!-- Solutions -->
        <li>
          <a href="{{ url('/solutions') }}" class="hover:text-primary transition duration-300">{{ t('nav.solutions', 'Solutions') }}</a>
        </li>

        <!-- Customer Success -->
        <li>
          <a href="{{ url('/customer-success') }}" class="hover:text-primary transition duration-300">{{ t('nav.customer_success', 'Customer Success') }}</a>
        </li>

        <!-- Insights / Blog -->
        <li>
          <a href="{{ url('/blog') }}" class="hover:text-primary transition duration-300">{{ t('nav.insights', 'Insights') }}</a>
        </li>

        <!-- Careers -->
        <li>
          <a href="{{ url('/careers') }}" class="hover:text-primary transition duration-300">{{ t('nav.careers', 'Careers') }}</a>
        </li>

        <!-- Contact Us -->
        <li>
          <a href="{{ url('/contact') }}" class="hover:text-primary transition duration-300">{{ t('nav.contact_us', 'Contact Us') }}</a>
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
            <a href="{{ current_page_localized_url('en') }}" class="text-zinc-400 hover:text-zinc-800 transition-colors">EN</a>
          @endif
          <span class="text-zinc-300">|</span>
          @if($currentLocale === 'id')
            <span class="text-primary border-b-2 border-primary cursor-default">ID</span>
          @else
            <a href="{{ current_page_localized_url('id') }}" class="text-zinc-400 hover:text-zinc-800 transition-colors">ID</a>
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
