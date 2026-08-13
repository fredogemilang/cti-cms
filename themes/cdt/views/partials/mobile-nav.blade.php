@php
    $currentLocale = app()->getLocale();

    if (!isset($allianceProducts)) {
        $allianceProducts = \App\Models\CptEntry::published()
            ->whereHas('postType', fn($q) => $q->whereIn('slug', ['technology-alliance', 'products']))
            ->where(fn($q) => $q->whereNull('parent_id')->orWhere('parent_id', 0))
            ->orderBy('menu_order')
            ->orderBy('title')
            ->limit(12)
            ->get();
    }

    if (!isset($solutionEntries)) {
        $solutionEntries = \App\Models\CptEntry::published()
            ->whereHas('postType', fn($q) => $q->whereIn('slug', ['solution', 'solutions']))
            ->where(fn($q) => $q->whereNull('parent_id')->orWhere('parent_id', 0))
            ->orderBy('menu_order')
            ->orderBy('title')
            ->get();
    }

    if (!isset($industryEntries)) {
        $industryEntries = \App\Models\CptEntry::published()
            ->whereHas('postType', fn($q) => $q->whereIn('slug', ['industry', 'industries']))
            ->where(fn($q) => $q->whereNull('parent_id')->orWhere('parent_id', 0))
            ->orderBy('menu_order')
            ->orderBy('title')
            ->get();
    }
    $blogSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveSlug($currentLocale) : 'blog-news';
    $blogTitle = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveTitle($currentLocale) : t('nav.blog_news', 'Blog & News');
    $blogUrl = localized_url('/' . $blogSlug);
@endphp

<!-- Mobile Bottom Navigation Bar & Bottom Sheets -->
<div id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 left-0 right-0 pointer-events-none" style="z-index: 9999;">
  <div class="w-full h-[100px] relative transition-transform duration-300 ease-out pointer-events-auto"
    :style="showMenu ? 'transform: translateY(0px);' : 'transform: translateY(100%);'">

    <!-- White Bar -->
    <div class="absolute inset-0 bg-white border-t border-gray-200 px-2 py-2 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
      <div class="flex items-end justify-between h-full relative pb-safe">
        <!-- Logo -->
        <a href="{{ localized_url('/') }}" class="flex-shrink-0 px-2 self-center">
          @if(setting('site_logo'))
            <img src="{{ resolve_block_asset(setting('site_logo')) }}" alt="{{ setting('site_name', 'CDT') }}" class="h-6 w-auto">
          @else
            <img src="{{ asset('themes/cdt/assets/images/cropped-logo-cdt.png') }}" alt="CDT" class="h-6 w-auto">
          @endif
        </a>

        <!-- About Us -->
        <a href="#" @click.prevent="activeSheet = activeSheet === 'about' ? null : 'about'"
          class="flex flex-col items-center justify-center w-16 gap-1 hover:text-primary transition-colors"
          :class="activeSheet === 'about' ? 'text-primary' : 'text-[#223959]'">
          <div
            class="border rounded-[12px] w-9 h-9 flex items-center justify-center shadow-sm transition-all duration-200"
            :class="activeSheet === 'about' ? 'border-primary bg-red-50/50 text-primary shadow-[0_2px_8px_rgba(189,42,42,0.15)]' : 'border-gray-300 bg-white'">
            <svg class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M17 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <span class="text-[9px] font-bold tracking-tight mt-0.5 uppercase">{{ t('nav.about_us', 'ABOUT US') }}</span>
        </a>

        <!-- Tech Alliance -->
        <a href="#" @click.prevent="activeSheet = activeSheet === 'tech' ? null : 'tech'"
          class="flex flex-col items-center justify-center w-16 gap-1 hover:text-primary transition-colors"
          :class="activeSheet === 'tech' ? 'text-primary' : 'text-[#223959]'">
          <div
            class="border rounded-[12px] w-9 h-9 flex items-center justify-center shadow-sm transition-all duration-200"
            :class="activeSheet === 'tech' ? 'border-primary bg-red-50/50 text-primary shadow-[0_2px_8px_rgba(189,42,42,0.15)]' : 'border-gray-300 bg-white'">
            <svg class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
          </div>
          <span class="text-[9px] font-bold text-center leading-[1.1] tracking-tight mt-0.5 uppercase">TECH<br>ALLIANCE</span>
        </a>

        <!-- Solutions -->
        <a href="#" @click.prevent="activeSheet = activeSheet === 'solutions' ? null : 'solutions'"
          class="flex flex-col items-center justify-center w-16 gap-1 hover:text-primary transition-colors"
          :class="activeSheet === 'solutions' ? 'text-primary' : 'text-[#223959]'">
          <div
            class="border rounded-[12px] w-9 h-9 flex items-center justify-center shadow-sm transition-all duration-200"
            :class="activeSheet === 'solutions' ? 'border-primary bg-red-50/50 text-primary shadow-[0_2px_8px_rgba(189,42,42,0.15)]' : 'border-gray-300 bg-white'">
            <svg class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <span class="text-[9px] font-bold tracking-tight mt-0.5 uppercase">{{ t('nav.solutions', 'SOLUTIONS') }}</span>
        </a>

        <!-- Others -->
        <a href="#" @click.prevent="activeSheet = activeSheet === 'others' ? null : 'others'"
          class="flex flex-col items-center justify-center w-16 gap-1 hover:text-primary transition-colors"
          :class="activeSheet === 'others' ? 'text-primary' : 'text-[#223959]'">
          <div
            class="border rounded-[12px] w-9 h-9 flex items-center justify-center shadow-sm transition-all duration-200"
            :class="activeSheet === 'others' ? 'border-primary bg-red-50/50 text-primary shadow-[0_2px_8px_rgba(189,42,42,0.15)]' : 'border-gray-300 bg-white'">
            <svg class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M5 12h.01M12 12h.01M19 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
            </svg>
          </div>
          <span class="text-[9px] font-bold tracking-tight mt-0.5 uppercase">OTHERS</span>
        </a>
      </div>
    </div>

    <!-- Floating Red Hamburger Menu Button -->
    <button @click="showMenu = !showMenu" aria-label="{{ t('a11y.toggle_menu', 'Toggle navigation menu') }}" title="{{ t('a11y.toggle_menu', 'Toggle navigation menu') }}"
      :aria-expanded="showMenu" aria-controls="mobile-bottom-nav"
      class="absolute bottom-full mb-[-12px] left-1/2 -translate-x-1/2 w-[56px] h-[56px] bg-[#bd2a2a] rounded-[18px] text-white flex items-center justify-center shadow-[0_8px_16px_rgba(189,42,42,0.4)] hover:bg-red-800 transition-all duration-300 border-[3px] border-white box-content cursor-pointer" style="z-index: 10000;"
      :style="showMenu ? 'transform: translateY(0px) scale(1);' : 'transform: translateY(3px) scale(0.95);'">
      <svg x-show="showMenu" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <svg x-show="!showMenu" style="display: none;" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>
</div>

<!-- Mobile Slide-Up Menu Overlay -->
<div x-show="activeSheet !== null" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
  class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm" @click="activeSheet = null" style="display: none; z-index: 10001;">
</div>

<!-- About Us Bottom Sheet -->
<div x-show="activeSheet === 'about'" x-transition:enter="transition ease-out duration-300 transform"
  x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
  x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
  x-transition:leave-end="translate-y-full"
  class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl pt-4 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between"
  style="display: none; z-index: 10002; min-height: 70vh; max-height: 90vh;">
  
  <div>
    <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto mb-3"></div>

    <div class="px-6 space-y-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0">{{ t('nav.about_us', 'About Us') }}</h3>
        <button @click="activeSheet = null" class="p-2 -mr-2 text-gray-500 hover:text-primary bg-gray-100 rounded-full transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="space-y-3">
        <!-- Overview -->
        <a href="{{ localized_url('/about-us') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 hover:bg-red-50/50 border border-gray-100 hover:border-red-100 transition-all group">
          <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
            <x-icon name="lucide:info" class="w-5 h-5 text-primary" />
          </div>
          <div class="flex-1">
            <div class="font-bold text-gray-800 group-hover:text-primary transition-colors text-sm">{{ t('nav.overview', 'Overview') }}</div>
            <p class="text-xs text-gray-500 mt-0.5">Central Data Technology at a glance</p>
          </div>
          <span class="text-gray-400 group-hover:text-primary transition-colors group-hover:translate-x-1 transition-transform">→</span>
        </a>

        <!-- Management -->
        <a href="{{ localized_url('/about-management') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 hover:bg-red-50/50 border border-gray-100 hover:border-red-100 transition-all group">
          <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
            <x-icon name="lucide:users" class="w-5 h-5 text-primary" />
          </div>
          <div class="flex-1">
            <div class="font-bold text-gray-800 group-hover:text-primary transition-colors text-sm">{{ t('nav.management', 'About Management') }}</div>
            <p class="text-xs text-gray-500 mt-0.5">Meet our leadership team</p>
          </div>
          <span class="text-gray-400 group-hover:text-primary transition-colors group-hover:translate-x-1 transition-transform">→</span>
        </a>
      </div>
    </div>
  </div>
  <div class="h-28 w-full"></div>
</div>

<!-- Technology Alliance Bottom Sheet -->
<div x-show="activeSheet === 'tech'" x-transition:enter="transition ease-out duration-300 transform"
  x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
  x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
  x-transition:leave-end="translate-y-full"
  class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl pt-4 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between"
  style="display: none; z-index: 10002; min-height: 70vh; max-height: 90vh;">
  
  <div>
    <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto mb-3"></div>

    <div class="px-6 space-y-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0">{{ t('nav.technology_alliance', 'Technology Alliance') }}</h3>
        <button @click="activeSheet = null" class="p-2 -mr-2 text-gray-500 hover:text-primary bg-gray-100 rounded-full transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-2 gap-3 text-xs">
        @if(isset($allianceProducts) && $allianceProducts->isNotEmpty())
          @foreach($allianceProducts as $subP)
            @php
              $subPTitle = $subP->getTranslation('title', $currentLocale ?? app()->getLocale()) ?: $subP->title;
              $subPUrl = $subP->getUrl();
              $subPBadge = $subP->getMeta('badge_text', '');
            @endphp
            @if(!empty($subPBadge))
              <a href="{{ $subPUrl }}" title="{{ $subPTitle }}" class="flex flex-col justify-center items-start gap-1 px-4 py-3 bg-gray-50 hover:bg-red-50/50 border border-gray-100 hover:border-red-100 rounded-xl transition-all font-semibold text-gray-700 hover:text-primary group">
                <span>{{ $subPTitle }}</span>
                <span class="text-[9px] font-bold bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded group-hover:bg-red-100 group-hover:text-primary transition-colors">{{ $subPBadge }}</span>
              </a>
            @else
              <a href="{{ $subPUrl }}" title="{{ $subPTitle }}" class="flex justify-between items-center px-4 py-3.5 bg-gray-50 hover:bg-red-50/50 border border-gray-100 hover:border-red-100 rounded-xl transition-all font-semibold text-gray-700 hover:text-primary group">
                <span>{{ $subPTitle }}</span>
                <span class="text-gray-300 group-hover:text-primary transition-colors">→</span>
              </a>
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>
  <div class="h-28 w-full"></div>
</div>

<!-- Solutions Bottom Sheet -->
<div x-show="activeSheet === 'solutions'" x-transition:enter="transition ease-out duration-300 transform"
  x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
  x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
  x-transition:leave-end="translate-y-full"
  class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl pt-4 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between"
  style="display: none; z-index: 10002; min-height: 70vh; max-height: 90vh;">
  
  <div>
    <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto mb-3"></div>

    <div class="px-6 space-y-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0">{{ t('nav.solutions', 'Solutions') }}</h3>
        <button @click="activeSheet = null" class="p-2 -mr-2 text-gray-500 hover:text-primary bg-gray-100 rounded-full transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="space-y-3">
        @if(isset($solutionEntries) && $solutionEntries->isNotEmpty())
          @foreach($solutionEntries as $sol)
            @php
              $solTitle = $sol->getTranslation('title', $currentLocale ?? app()->getLocale()) ?: $sol->title;
              $solUrl = $sol->getUrl();
              $solIcon = 'lucide:layers';
              if (isset($sol->postType) && !empty($sol->postType->settings['meta_boxes'])) {
                  $iconField = $sol->postType->metaFields()->where('name', 'icon')->first();
                  if ($iconField) {
                      $rawIcon = $sol->getMeta('icon');
                      $solIcon = $rawIcon ?: 'lucide:layers';
                  }
              }
            @endphp
            <a href="{{ $solUrl }}" class="flex items-center gap-4 p-3.5 rounded-2xl bg-gray-50 hover:bg-red-50/50 border border-gray-100 hover:border-red-100 transition-all group">
              <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                <x-icon :name="$solIcon" class="w-5 h-5 text-primary" />
              </div>
              <div class="flex-1">
                <div class="font-bold text-gray-800 group-hover:text-primary transition-colors text-sm">{{ $solTitle }}</div>
                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit(strip_tags($sol->getTranslation('excerpt', $currentLocale ?? app()->getLocale()) ?: $sol->excerpt), 60) }}</p>
              </div>
              <span class="text-gray-400 group-hover:text-primary transition-colors group-hover:translate-x-1 transition-transform">→</span>
            </a>
          @endforeach
        @endif
      </div>
    </div>
  </div>
  <div class="h-28 w-full"></div>
</div>

<!-- Others (More Menu) Bottom Sheet -->
<div x-show="activeSheet === 'others'" x-transition:enter="transition ease-out duration-300 transform"
  x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
  x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
  x-transition:leave-end="translate-y-full"
  class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl pt-4 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between"
  style="display: none; z-index: 10002; min-height: 70vh; max-height: 90vh;">

  <div>
    <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto mb-3"></div>

    <div class="px-6 space-y-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0">More Menu</h3>
        <button @click="activeSheet = null" class="p-2 -mr-2 text-gray-500 hover:text-primary bg-gray-100 rounded-full transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="space-y-2">
        <!-- Industry -->
        @if(isset($industryEntries) && $industryEntries->isNotEmpty())
        <div x-data="{ expanded: false }" class="border-b border-gray-100 py-2">
          <button @click="expanded = !expanded"
            class="flex items-center justify-between w-full text-left font-bold text-gray-800">
            <span class="flex items-center gap-2">
              <x-icon name="lucide:building" class="w-4 h-4 text-primary" />
              {{ t('nav.industry', 'Industry') }}
            </span>
            <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div x-show="expanded" style="display: none;" class="mt-3 pl-6 space-y-3 text-sm text-gray-600">
            @foreach($industryEntries as $ind)
              @php
                $indTitle = $ind->getTranslation('title', $currentLocale ?? app()->getLocale()) ?: $ind->title;
                $indUrl = $ind->getUrl();
              @endphp
              <a href="{{ $indUrl }}" class="block hover:text-primary font-medium">{{ $indTitle }}</a>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Insight -->
        <div x-data="{ expanded: false }" class="border-b border-gray-100 py-2">
          <button @click="expanded = !expanded"
            class="flex items-center justify-between w-full text-left font-bold text-gray-800">
            <span class="flex items-center gap-2">
              <x-icon name="lucide:book-open" class="w-4 h-4 text-primary" />
              {{ t('nav.insights', 'Insight') }}
            </span>
            <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div x-show="expanded" style="display: none;" class="mt-3 pl-6 space-y-3 text-sm text-gray-600">
            @php
              $csCpt = \App\Models\CustomPostType::where('slug', 'customer-success')->first();
              $csUrl = $csCpt ? $csCpt->getArchiveUrl($currentLocale ?? app()->getLocale()) : localized_url('/customer-success');
              $csTitle = $csCpt ? ($csCpt->getTranslation('plural_label', $currentLocale ?? app()->getLocale()) ?: $csCpt->plural_label) : t('nav.customer_success', 'Customer Success');
            @endphp
            <a href="{{ $csUrl }}" class="block hover:text-primary font-medium">{{ $csTitle }}</a>
            <a href="{{ $blogUrl }}" class="block hover:text-primary font-medium">{{ $blogTitle }}</a>
            <a href="{{ localized_url('/video') }}" class="block hover:text-primary font-medium">{{ t('nav.video', 'Video') }}</a>
          </div>
        </div>

        <!-- Career -->
        <div x-data="{ expanded: false }" class="border-b border-gray-100 py-2">
          <button @click="expanded = !expanded"
            class="flex items-center justify-between w-full text-left font-bold text-gray-800">
            <span class="flex items-center gap-2">
              <x-icon name="lucide:briefcase" class="w-4 h-4 text-primary" />
              {{ t('nav.careers', 'Career') }}
            </span>
            <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div x-show="expanded" style="display: none;" class="mt-3 pl-6 space-y-3 text-sm text-gray-600">
            <a href="{{ localized_url('/careers#why-cdt') }}" class="block hover:text-primary font-medium">{{ t('nav.why_cdt', 'Why CDT') }}</a>
            <a href="{{ localized_url('/careers#life-at-cdt') }}" class="block hover:text-primary font-medium">{{ t('nav.life_at_cdt', 'Life at CDT') }}</a>
            <a href="{{ localized_url('/careers#job-vacancy') }}" class="block hover:text-primary font-medium">{{ t('nav.job_vacancy', 'Job Vacancy') }}</a>
          </div>
        </div>

        <!-- Contact Us -->
        <div class="py-3">
          <a href="{{ localized_url('/contact-us') }}" class="font-bold text-gray-800 hover:text-primary flex items-center gap-2">
            <x-icon name="lucide:mail" class="w-4 h-4 text-primary" />
            {{ t('nav.contact_us', 'Contact Us') }}
          </a>
        </div>
      </div>

      <!-- Language Switcher -->
      <div class="mt-6 flex gap-3">
        @if(($currentLocale ?? app()->getLocale()) === 'id')
          <span class="flex-1 py-2.5 text-center bg-primary text-white rounded-lg font-bold text-sm shadow-sm cursor-default">ID</span>
          <a href="{{ current_page_localized_url('en') }}" class="flex-1 py-2.5 text-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 text-sm transition-colors">EN</a>
        @else
          <a href="{{ current_page_localized_url('id') }}" class="flex-1 py-2.5 text-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 text-sm transition-colors">ID</a>
          <span class="flex-1 py-2.5 text-center bg-primary text-white rounded-lg font-bold text-sm shadow-sm cursor-default">EN</span>
        @endif
      </div>
    </div>
  </div>
  <div class="h-28 w-full"></div>
</div>
