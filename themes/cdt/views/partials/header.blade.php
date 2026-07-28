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
@endphp

<!-- Mobile Top Header Bar -->
<div class="lg:hidden bg-white border-b border-zinc-100">
  <div class="flex items-center justify-between px-4 py-3">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex-shrink-0">
      <img src="{{ asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png') }}" alt="{{ setting('site_name', 'Central Data Technology') }}" class="h-12 w-auto">
    </a>
    <!-- Language Switcher -->
    <div class="flex items-center gap-2 text-[13px] font-bold">
      <a href="{{ url('/lang/en') }}" class="{{ $currentLocale === 'en' ? 'text-primary border-b-2 border-primary' : 'text-zinc-400 hover:text-zinc-800' }}">EN</a>
      <span class="text-zinc-300">|</span>
      <a href="{{ url('/lang/id') }}" class="{{ $currentLocale === 'id' ? 'text-primary border-b-2 border-primary' : 'text-zinc-400 hover:text-zinc-800' }}">ID</a>
    </div>
  </div>
</div>

<header id="main-header" class="hidden lg:block fixed w-full top-0 z-[100] bg-white border-b border-zinc-100">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <nav class="flex items-center justify-between h-20">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="flex-shrink-0">
        <img src="{{ asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png') }}" alt="{{ setting('site_name', 'Central Data Technology') }}" class="h-16 w-auto">
      </a>

      <!-- Desktop Menu -->
      <ul class="hidden lg:flex items-center gap-6 text-[13px] font-bold text-zinc-800 uppercase tracking-wide h-full">
        
        <!-- About Us Dropdown -->
        <li class="group relative flex items-center h-full py-6">
          <a href="{{ url('/about') }}" class="hover:text-primary transition duration-300 flex items-center gap-1">
            About Us
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </a>
          <div class="absolute left-0 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-56 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2 flex flex-col normal-case tracking-normal">
              <a href="{{ url('/about') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                Overview <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
              <a href="{{ url('/management') }}" class="text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors px-5 py-3 flex justify-between items-center group/link">
                About Management <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
              </a>
            </div>
          </div>
        </li>

        <!-- Technology Alliance Mega Menu -->
        <li class="group relative flex items-center h-full py-6">
          <a href="{{ url('/technology-alliance') }}" class="hover:text-primary transition duration-300 flex items-center gap-1">
            Technology Alliance
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </a>
          <div class="absolute left-1/2 -translate-x-1/2 top-[100%] pt-4 opacity-0 invisible translate-y-2 w-[900px] transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex">
              <div class="w-1/3 bg-gradient-to-bl from-primary to-zinc-900 p-8 text-white relative overflow-hidden flex flex-col">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div>
                  <h3 class="text-xl font-bold mb-4 relative z-10 leading-tight">Technology Alliance</h3>
                  <p class="text-sm text-white/80 leading-relaxed relative z-10 normal-case font-normal tracking-normal">
                    Explore our comprehensive ecosystem of global technology partners designed to empower your business transformation.
                  </p>
                </div>
              </div>
              <div class="w-2/3 p-8">
                <div class="grid grid-cols-2 gap-y-2 gap-x-6">
                  @forelse($allianceProducts as $prod)
                    <a href="{{ $prod->getUrl() }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors border-b border-gray-200 py-2.5 px-3 -mx-3 hover:bg-gray-50 rounded-md flex justify-between items-center group/link">
                      {{ $prod->title }}
                      <span class="text-primary opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all">→</span>
                    </a>
                  @empty
                    <a href="{{ url('/technology-alliance') }}" class="text-sm font-semibold text-gray-700 hover:text-primary border-b border-gray-200 py-2.5 px-3">All Technology Partners →</a>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </li>

        <!-- Solutions -->
        <li>
          <a href="{{ url('/solutions') }}" class="hover:text-primary transition duration-300">Solutions</a>
        </li>

        <!-- Customer Success -->
        <li>
          <a href="{{ url('/customer-success') }}" class="hover:text-primary transition duration-300">Customer Success</a>
        </li>

        <!-- Insights / Blog -->
        <li>
          <a href="{{ url('/blog') }}" class="hover:text-primary transition duration-300">Insights</a>
        </li>

        <!-- Careers -->
        <li>
          <a href="{{ url('/careers') }}" class="hover:text-primary transition duration-300">Careers</a>
        </li>
      </ul>

      <!-- Right Header CTA & Language Switcher -->
      <div class="hidden lg:flex items-center gap-6">
        <!-- Language Switcher -->
        <div class="flex items-center gap-2 text-[13px] font-bold">
          <a href="{{ url('/lang/en') }}" class="{{ $currentLocale === 'en' ? 'text-primary border-b-2 border-primary' : 'text-zinc-400 hover:text-zinc-800' }}">EN</a>
          <span class="text-zinc-300">|</span>
          <a href="{{ url('/lang/id') }}" class="{{ $currentLocale === 'id' ? 'text-primary border-b-2 border-primary' : 'text-zinc-400 hover:text-zinc-800' }}">ID</a>
        </div>

        <a href="{{ url('/contact') }}" class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors">
          Contact Us
        </a>
      </div>
    </nav>
  </div>
</header>
<div class="h-20 hidden lg:block"></div>
