@php
    use Themes\Csi\Support\CsiDataProvider;

    $currentLocale = app()->getLocale();
    $headerItems = CsiDataProvider::menuItems('header');
    $moreLinks = $headerItems->where('item_type', 'link')->values();
    $solutionsForSheet = CsiDataProvider::solutionsList();
    $allianceCategoriesForSheet = CsiDataProvider::allianceCategories();
    $allianceTabsForSheet = CsiDataProvider::allianceCategoryTabs($allianceCategoriesForSheet);
    $postsActive = function_exists('is_plugin_active') && is_plugin_active('posts');
    $dockContactUrl = $currentLocale === 'id' ? localized_url('/hubungi-kami') : localized_url('/contact-us');
@endphp

<!-- MOBILE BOTTOM NAVIGATION DOCK -->
<div id="mobile-bottom-nav" class="fixed bottom-0 inset-x-0 z-40 lg:hidden pointer-events-none">
  <div class="w-full relative pointer-events-auto bg-white/95 backdrop-blur-xl border-t border-slate-200/80 shadow-[0_-4px_24px_rgba(0,0,0,0.08)] pb-[env(safe-area-inset-bottom,0px)]">
    <div class="flex items-center justify-around h-[68px] px-2 relative">
      <!-- 1. Solutions Tab -->
      <button id="dock-btn-solutions" type="button" class="dock-btn flex-1 flex flex-col items-center justify-center py-1 text-slate-500 hover:text-[#04B1C7] transition-all cursor-pointer group" data-sheet="solutions">
        <div class="w-6 h-6 flex items-center justify-center mb-0.5 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/></svg>
        </div>
        <span class="text-[11px] font-medium tracking-tight">{{ t('mobile_dock.solutions', 'Solutions') }}</span>
      </button>

      <!-- 2. Technology Alliances Tab -->
      <button id="dock-btn-alliances" type="button" class="dock-btn flex-1 flex flex-col items-center justify-center py-1 text-slate-500 hover:text-[#04B1C7] transition-all cursor-pointer group" data-sheet="alliances">
        <div class="w-6 h-6 flex items-center justify-center mb-0.5 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><ellipse cx="12" cy="12" rx="9" ry="4" stroke-width="1.8" transform="rotate(-25 12 12)"/></svg>
        </div>
        <span class="text-[11px] font-medium tracking-tight">{{ t('mobile_dock.alliances', 'Alliances') }}</span>
      </button>

      <!-- 3. Center Raised Action Button (Contact Us) -->
      <div class="flex-shrink-0 px-1 -mt-6">
        <a href="{{ $dockContactUrl }}" class="flex flex-col items-center justify-center w-[54px] h-[54px] rounded-2xl bg-[#ED7F22] hover:bg-[#d86f1b] text-white shadow-[0_8px_20px_rgba(237,127,34,0.4)] border-4 border-white active:scale-95 transition-all cursor-pointer" title="{{ t('mobile_dock.contact', 'Contact Converion') }}">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </a>
      </div>

      <!-- 4. Insights Tab -->
      <a href="#case-studies" class="dock-btn flex-1 flex flex-col items-center justify-center py-1 text-slate-500 hover:text-[#04B1C7] transition-all cursor-pointer group">
        <div class="w-6 h-6 flex items-center justify-center mb-0.5 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <span class="text-[11px] font-medium tracking-tight">{{ t('mobile_dock.insights', 'Insights') }}</span>
      </a>

      <!-- 5. More Menu Tab -->
      <button id="dock-btn-more" type="button" class="dock-btn flex-1 flex flex-col items-center justify-center py-1 text-slate-500 hover:text-[#04B1C7] transition-all cursor-pointer group" data-sheet="more">
        <div class="w-6 h-6 flex items-center justify-center mb-0.5 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.5" stroke-width="2"/><circle cx="19" cy="12" r="1.5" stroke-width="2"/><circle cx="5" cy="12" r="1.5" stroke-width="2"/></svg>
        </div>
        <span class="text-[11px] font-medium tracking-tight">{{ t('mobile_dock.more', 'More') }}</span>
      </button>
    </div>
  </div>
</div>

<!-- MOBILE BOTTOM SHEETS BACKDROP & CONTAINERS -->
<div id="bottom-sheet-backdrop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>

<!-- SHEET 1: SOLUTIONS -->
<div id="sheet-solutions" data-lenis-prevent class="bottom-sheet fixed bottom-0 inset-x-0 z-50 max-h-[82vh] bg-white rounded-t-[24px] shadow-2xl flex flex-col overflow-hidden transform translate-y-full transition-transform duration-300 ease-out lg:hidden">
  <div class="w-10 h-1.5 bg-slate-300 rounded-full mx-auto mt-3 mb-2 shrink-0"></div>
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between shrink-0">
    <div>
      <span class="block text-base font-bold text-slate-900">{{ t('nav.comprehensive_solutions', 'Comprehensive Solutions') }}</span>
      <p class="text-xs text-slate-500">{{ t('mobile_dock.solutions_subtitle', 'Enterprise IT architecture & services') }}</p>
    </div>
    <button type="button" class="sheet-close-btn p-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors cursor-pointer" aria-label="Close">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="p-5 overflow-y-auto space-y-3.5 pb-8">
    @foreach($solutionsForSheet as $sol)
      <a href="{{ $sol['url'] }}" class="sheet-item flex items-start gap-3.5 p-3 rounded-xl bg-slate-50 hover:bg-[#EFFDFF] transition-all border border-slate-100">
        <div class="w-10 h-10 rounded-lg bg-[#EFFDFF] border border-[#04B1C7]/30 flex items-center justify-center shrink-0">
          <img src="{{ $sol['icon'] }}" alt="" class="w-5 h-5" />
        </div>
        <div>
          <div class="text-sm font-bold text-slate-900">{{ $sol['title'] }}</div>
          <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $sol['subtitle'] }}</p>
        </div>
      </a>
    @endforeach
    @php
        $solutionsArchiveUrl = $currentLocale === 'id' ? localized_url('/solusi') : localized_url('/solutions');
    @endphp
    <a href="{{ $solutionsArchiveUrl }}" class="sheet-item block w-full py-3 rounded-xl bg-[#04B1C7] text-white text-center font-bold text-sm shadow-sm hover:bg-[#0398ab] transition-colors mt-2">
      {{ t('nav.explore_all_solutions', 'Explore All Solutions') }} →
    </a>
  </div>
</div>

<!-- SHEET 2: ALLIANCES -->
<div id="sheet-alliances" data-lenis-prevent class="bottom-sheet fixed bottom-0 inset-x-0 z-50 max-h-[82vh] bg-white rounded-t-[24px] shadow-2xl flex flex-col overflow-hidden transform translate-y-full transition-transform duration-300 ease-out lg:hidden">
  <div class="w-10 h-1.5 bg-slate-300 rounded-full mx-auto mt-3 mb-2 shrink-0"></div>
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between shrink-0">
    <div>
      <span class="block text-base font-bold text-slate-900">{{ t('nav.technology_alliances', 'Technology Alliances') }}</span>
      <p class="text-xs text-slate-500">{{ t('nav.partner_ecosystem', 'World-Class Partner Ecosystem') }}</p>
    </div>
    <button type="button" class="sheet-close-btn p-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors cursor-pointer" aria-label="Close">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="p-5 overflow-y-auto space-y-4 pb-8">
    @if(count($allianceCategoriesForSheet) > 0)
      <div class="grid {{ count($allianceCategoriesForSheet) === 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-3 text-xs">
        @foreach($allianceCategoriesForSheet as $catKey => $catItems)
          @php
              $isOrangeCat = in_array($catKey, ['security', 'ai'], true);
              $titleColor = $isOrangeCat ? 'text-[#ED7F22]' : 'text-[#04B1C7]';
              $dotBg = $isOrangeCat ? 'bg-[#ED7F22]' : 'bg-[#04B1C7]';
          @endphp
          <div class="sheet-alliance-card p-3 rounded-xl bg-slate-50 border border-slate-100 transition-all" data-category="{{ $catKey }}">
            <div class="font-bold {{ $titleColor }} mb-2 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full {{ $dotBg }}"></span>
              {{ $allianceTabsForSheet[$catKey] ?? ucwords(str_replace(['-', '_'], ' ', (string) $catKey)) }}
            </div>
            <div class="flex flex-wrap gap-1.5">
              @foreach($catItems as $partner)
                <a
                  href="{{ $partner['url'] }}"
                  onclick="event.stopPropagation()"
                  class="inline-flex items-center px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-700 hover:text-[#04B1C7] hover:border-[#04B1C7]/40 text-[11px] font-medium transition-colors"
                >{{ $partner['name'] }}</a>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-500 text-center py-2">{{ t('nav.no_alliances', 'No technology alliances available yet.') }}</p>
    @endif
    <a href="{{ localized_url('/technology-alliances') }}" class="sheet-item block w-full py-3 rounded-xl bg-[#ED7F22] text-white text-center font-bold text-sm shadow-sm hover:bg-[#d86f1b] transition-colors mt-2">
      {{ t('nav.explore_all_alliances', 'Explore All Alliances') }} →
    </a>
  </div>
</div>

<!-- SHEET 3: MORE -->
<div id="sheet-more" data-lenis-prevent class="bottom-sheet fixed bottom-0 inset-x-0 z-50 max-h-[82vh] bg-white rounded-t-[24px] shadow-2xl flex flex-col overflow-hidden transform translate-y-full transition-transform duration-300 ease-out lg:hidden">
  <div class="w-10 h-1.5 bg-slate-300 rounded-full mx-auto mt-3 mb-2 shrink-0"></div>
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between shrink-0">
    <div>
      <span class="block text-base font-bold text-slate-900">{{ t('mobile_dock.menu_more', 'Menu & More') }}</span>
      <p class="text-xs text-slate-500">{{ t('mobile_dock.menu_subtitle', 'Quick navigation & settings') }}</p>
    </div>
    <button type="button" class="sheet-close-btn p-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors cursor-pointer" aria-label="Close">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="p-5 overflow-y-auto space-y-4 pb-8">
    <button id="search-trigger-sheet" type="button" class="w-full p-3 rounded-xl bg-slate-50 hover:bg-[#EFFDFF] border border-slate-100 flex items-center justify-between text-left text-xs font-medium text-slate-600 cursor-pointer transition-colors">
      <span class="flex items-center gap-2.5">
        <img src="{{ theme_asset('images/nav-search-icon.svg') }}" alt="" class="w-4 h-4 text-slate-500" />
        <span>{{ t('mobile_dock.search_hint', 'Search solutions, partners...') }}</span>
      </span>
      <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-[10px] text-slate-400 font-mono">⌘K</span>
    </button>

    <div class="divide-y divide-slate-100 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold text-slate-800">
      @foreach($moreLinks as $item)
        @php
          $rawUrl = trim((string) ($item->getRawOriginal('url') ?? ''));
          if (! $postsActive && str_contains($rawUrl, 'blog')) {
              continue;
          }
          $itemActive = $item->isActive($currentLocale);
        @endphp
        <a href="{{ $item->resolvedUrl($currentLocale) }}" target="{{ $item->target }}" @if($itemActive) aria-current="page" @endif class="sheet-item flex items-center justify-between p-3.5 {{ $itemActive ? 'text-[#04B1C7]' : 'hover:text-[#04B1C7]' }} transition-colors">
          <span>{{ $item->localizedTitle($currentLocale) }}</span>
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      @endforeach
    </div>

    <a href="{{ $dockContactUrl }}" class="sheet-item block w-full py-3 rounded-xl bg-[#ED7F22] text-white text-center font-bold text-sm shadow-sm hover:bg-[#d86f1b] transition-colors">
      {{ t('mobile_dock.contact_direct', 'Contact Us Directly') }}
    </a>
  </div>
</div>
