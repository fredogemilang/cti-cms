@php
    use Themes\Csi\Support\CsiDataProvider;

    $currentLocale = app()->getLocale();
    $availableLocales = available_locales();
    $showLocaleSwitcher = filter_var(setting('locale_switcher_enabled', false), FILTER_VALIDATE_BOOLEAN) && count($availableLocales) > 1;

    $siteLogoSetting = setting('site_logo');
    $siteLogoUrl = $siteLogoSetting ? resolve_block_asset($siteLogoSetting, 'sm') : theme_asset('images/logo-bpt-white.png');
    $siteName = setting('site_name', 'Converion Solution Informatica');

    $headerItems = CsiDataProvider::menuItems('header');
    $ctaItem = $headerItems->firstWhere('item_type', 'button');
    $navItems = $headerItems->where('item_type', '!=', 'button')->values();

    $solutionsForMenu = CsiDataProvider::solutionsList();
    $allianceCategoriesForMenu = CsiDataProvider::allianceCategories();
    $allianceTabsForMenu = CsiDataProvider::allianceCategoryTabs($allianceCategoriesForMenu);
    $totalAlliancePartners = collect($allianceCategoriesForMenu)->flatten(1)->count();
    $postsActive = function_exists('is_plugin_active') && is_plugin_active('posts');
@endphp

<header id="main-header" class="sticky top-0 z-50 bg-white border-b-2 border-[#F0F5FF] transition-all duration-300">
  <div id="header-container" class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] h-[82px] flex items-center justify-between transition-all duration-300">
    <!-- Logo -->
    <a href="{{ localized_url('/') }}" class="flex items-center shrink-0" aria-label="{{ $siteName }}">
      <img id="header-logo" src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain transition-all duration-300" />
    </a>

    <!-- Desktop Nav Menu -->
    <nav class="hidden lg:flex items-center gap-7 text-[14px] text-black font-medium" aria-label="Main navigation">
      @foreach($navItems as $item)
        @php
          $rawUrl = trim((string) ($item->getRawOriginal('url') ?? ''));
          if (! $postsActive && str_contains($rawUrl, 'blog')) {
              continue;
          }
          $itemLabel = $item->localizedTitle($currentLocale);
          $itemUrl = $item->resolvedUrl($currentLocale);
          $isActive = $item->isActive($currentLocale);
        @endphp

        @if($item->item_type === 'mega_solutions')
          <!-- Solutions Mega Menu -->
          <div class="relative group cursor-pointer flex items-center gap-1 {{ $isActive ? 'text-[#04B1C7]' : 'hover:text-[#04B1C7]' }} transition-colors py-4" @if($isActive) aria-current="page" @endif>
            <span class="font-medium">{{ $itemLabel }}</span>
            <img src="{{ theme_asset('images/chevron-down.svg') }}" alt="" class="w-3.5 h-3.5 opacity-60 group-hover:rotate-180 transition-transform" />

            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[880px] bg-white rounded-2xl shadow-2xl border border-slate-100 p-7 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 text-left pointer-events-none group-hover:pointer-events-auto">
              <div class="text-sm font-bold tracking-wider text-slate-400 uppercase mb-4 px-1">{{ t('nav.comprehensive_solutions', 'Comprehensive Solutions') }}</div>

              <div class="grid grid-cols-2 gap-4">
                @foreach($solutionsForMenu as $sol)
                  <a href="{{ $sol['url'] }}" class="flex items-start gap-4 p-3.5 rounded-xl hover:bg-[#F7FAFF] transition-all group/item border border-transparent hover:border-[#04B1C7]/30">
                    <div class="w-12 h-12 rounded-xl bg-[#EFFDFF] border border-[#04B1C7]/40 flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                      <img src="{{ $sol['icon'] }}" alt="{{ $sol['title'] }}" class="w-6 h-6" />
                    </div>
                    <div>
                      <div class="text-[17px] font-bold text-slate-900 group-hover/item:text-[#04B1C7] transition-colors">{{ $sol['title'] }}</div>
                      <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $sol['subtitle'] }}</p>
                    </div>
                  </a>
                @endforeach
              </div>

              <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-sm">
                <span class="text-slate-500">{{ t('nav.solutions_footer_desc', 'Discover all tailored enterprise solutions.') }}</span>
                <a href="{{ $itemUrl }}" class="text-[#ED7F22] hover:text-[#d86f1b] font-semibold hover:underline flex items-center gap-1.5 transition-colors">
                  <span>{{ t('nav.explore_all_solutions', 'Explore All Solutions') }}</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
              </div>
            </div>
          </div>

        @elseif($item->item_type === 'mega_alliances')
          @php
              $partnerCountLabel = $totalAlliancePartners === 1
                  ? t('nav.partner_count_singular', ':count Global Technology Partner', ['count' => 1])
                  : t('nav.partner_count_plural', ':count Global Technology Partners', ['count' => $totalAlliancePartners]);

              $totalGridCols = 0;
              foreach ($allianceCategoriesForMenu as $catItems) {
                  $totalGridCols += count($catItems) > 8 ? 2 : 1;
              }
              $totalGridCols = max(1, $totalGridCols);
              $megaWidthClass = match (true) {
                  $totalGridCols <= 1 => 'w-[520px]',
                  $totalGridCols === 2 => 'w-[600px]',
                  $totalGridCols === 3 => 'w-[760px]',
                  $totalGridCols === 4 => 'w-[900px]',
                  default => 'w-[1040px]',
              };
              $megaGridColsClass = match ($totalGridCols) {
                  1 => 'grid-cols-1',
                  2 => 'grid-cols-2',
                  3 => 'grid-cols-3',
                  4 => 'grid-cols-4',
                  default => 'grid-cols-5',
              };
          @endphp
          <!-- Technology Alliances Mega Menu -->
          <div class="relative group cursor-pointer flex items-center gap-1 {{ $isActive ? 'text-[#04B1C7]' : 'hover:text-[#04B1C7]' }} transition-colors py-4" @if($isActive) aria-current="page" @endif>
            <span class="font-medium">{{ $itemLabel }}</span>
            <img src="{{ theme_asset('images/chevron-down.svg') }}" alt="" class="w-3.5 h-3.5 opacity-60 group-hover:rotate-180 transition-transform" />

            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-1 {{ $megaWidthClass }} max-w-[94vw] bg-white rounded-2xl shadow-2xl border border-slate-100 p-7 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 text-left pointer-events-none group-hover:pointer-events-auto">
              <div class="mb-5 pb-3 border-b border-slate-100 flex items-center justify-between gap-4">
                <span class="text-sm font-bold tracking-wider text-slate-400 uppercase">{{ t('nav.partner_ecosystem', 'World-Class Partner Ecosystem') }}</span>
                <span class="text-xs font-semibold text-[#04B1C7] bg-[#EFFDFF] px-2.5 py-1 rounded-full border border-[#04B1C7]/20 shrink-0">{{ $partnerCountLabel }}</span>
              </div>

              @if(count($allianceCategoriesForMenu) > 0)
                <div class="grid {{ $megaGridColsClass }} gap-7">
                  @foreach($allianceCategoriesForMenu as $catKey => $catItems)
                    @php
                        $isOrangeCat = in_array($catKey, ['security', 'ai'], true);
                        $dotClass = $isOrangeCat ? 'bg-[#ED7F22]' : 'bg-[#04B1C7]';
                        $hoverTextClass = $isOrangeCat ? 'hover:text-[#ED7F22]' : 'hover:text-[#04B1C7]';
                        $splitColumns = count($catItems) > 8;
                    @endphp
                    <div @class(['col-span-2' => $splitColumns])>
                      <span class="font-bold text-slate-900 flex items-center gap-2 text-[15px] mb-3 pb-2 border-b border-slate-100">
                        <span class="w-2.5 h-2.5 rounded-full {{ $dotClass }}"></span>
                        {{ $allianceTabsForMenu[$catKey] ?? ucwords(str_replace(['-', '_'], ' ', (string) $catKey)) }}
                      </span>
                      @if($splitColumns)
                        @php $chunks = array_chunk($catItems, (int) ceil(count($catItems) / 2)); @endphp
                        <div class="grid grid-cols-2 gap-x-5 text-slate-700 text-xs font-medium">
                          @foreach($chunks as $chunk)
                            <ul class="space-y-2">
                              @foreach($chunk as $partner)
                                @php $isAnchor = str_starts_with($partner['url'] ?? '#alliances', '#'); @endphp
                                <li>
                                  <a
                                    href="{{ $partner['url'] }}"
                                    @if($isAnchor) data-filter-target="{{ $catKey }}" @endif
                                    class="megamenu-product-link {{ $hoverTextClass }} transition-colors block"
                                  >{{ $partner['name'] }}</a>
                                </li>
                              @endforeach
                            </ul>
                          @endforeach
                        </div>
                      @else
                        <ul class="space-y-2 text-slate-700 text-xs font-medium">
                          @foreach($catItems as $partner)
                            @php $isAnchor = str_starts_with($partner['url'] ?? '#alliances', '#'); @endphp
                            <li>
                              <a
                                href="{{ $partner['url'] }}"
                                @if($isAnchor) data-filter-target="{{ $catKey }}" @endif
                                class="megamenu-product-link {{ $hoverTextClass }} transition-colors block"
                              >{{ $partner['name'] }}</a>
                            </li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  @endforeach
                </div>
              @else
                <p class="text-xs text-slate-500 py-2">{{ t('nav.no_alliances', 'No technology alliances available yet.') }}</p>
              @endif

              <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-sm gap-4">
                <span class="text-slate-500">{{ t('nav.alliances_footer_desc', 'Discover all technology partners powering digital transformation.') }}</span>
                <a href="{{ $itemUrl }}" class="text-[#ED7F22] hover:text-[#d86f1b] font-semibold hover:underline flex items-center gap-1.5 transition-colors shrink-0">
                  <span>{{ t('nav.explore_all_alliances', 'Explore All Alliances') }}</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
              </div>
            </div>
          </div>

        @else
          <a href="{{ $itemUrl }}" target="{{ $item->target }}" @if($isActive) aria-current="page" @endif class="{{ $isActive ? 'text-[#04B1C7]' : 'hover:text-[#04B1C7]' }} transition-colors py-4">{{ $itemLabel }}</a>
        @endif
      @endforeach
    </nav>

    <!-- Right Action Items -->
    <div class="hidden lg:flex items-center gap-5">
      @if($ctaItem)
        <a href="{{ $ctaItem->resolvedUrl($currentLocale) }}" target="{{ $ctaItem->target }}" class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-[14px] font-medium px-[23px] py-[11px] rounded-[6px] leading-[16px] transition-all shadow-xs hover:shadow-md active:scale-95">
          {{ $ctaItem->localizedTitle($currentLocale) }}
        </a>
      @else
        <a href="#contact" class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-[14px] font-medium px-[23px] py-[11px] rounded-[6px] leading-[16px] transition-all shadow-xs hover:shadow-md active:scale-95">
          {{ t('nav.contact_us', 'Contact Us') }}
        </a>
      @endif

      <!-- Search Icon Trigger -->
      <button id="search-modal-trigger" type="button" aria-label="{{ t('search.submit', 'Search') }}" class="p-2 text-slate-700 hover:text-[#ED7F22] transition-colors rounded-full hover:bg-slate-100 cursor-pointer">
        <img src="{{ theme_asset('images/nav-search-icon.svg') }}" alt="Search" class="w-5 h-5" />
      </button>

      <!-- Language Switcher (CMS Settings Compliant) -->
      @if($showLocaleSwitcher)
        <div class="relative flex items-center bg-[#F3F4F6] rounded-full p-1 border border-slate-200/60 shadow-xs" role="group" aria-label="Language">
          <img src="{{ theme_asset('images/nav-globe-icon.svg') }}" alt="Language" class="w-3.5 h-3.5 ml-1 mr-1 text-slate-500 opacity-70" />
          @foreach($availableLocales as $loc)
            <a href="{{ current_page_localized_url($loc) }}"
               class="px-2 py-0.5 rounded-full text-xs transition-all {{ $currentLocale === $loc ? 'font-bold bg-white text-[#ED7F22] shadow-xs' : 'font-medium text-slate-500 hover:text-slate-800' }}">
              {{ strtoupper($loc) }}
            </a>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Mobile Right Controls: Search & Language Switcher -->
    <div class="lg:hidden flex items-center gap-2.5">
      <button id="search-modal-trigger-mob" type="button" aria-label="{{ t('search.submit', 'Search') }}" class="p-2 text-slate-700 hover:text-[#ED7F22] transition-colors rounded-full hover:bg-slate-100 cursor-pointer">
        <img src="{{ theme_asset('images/nav-search-icon.svg') }}" alt="Search" class="w-5 h-5" />
      </button>

      @if($showLocaleSwitcher)
        <div class="relative flex items-center bg-[#F3F4F6] rounded-full p-1 border border-slate-200/60 shadow-xs" role="group" aria-label="Language">
          <img src="{{ theme_asset('images/nav-globe-icon.svg') }}" alt="Language" class="w-3.5 h-3.5 ml-1 mr-1 text-slate-500 opacity-70" />
          @foreach($availableLocales as $loc)
            <a href="{{ current_page_localized_url($loc) }}"
               class="px-2 py-0.5 rounded-full text-xs transition-all {{ $currentLocale === $loc ? 'font-bold bg-white text-[#ED7F22] shadow-xs' : 'font-medium text-slate-500 hover:text-slate-800' }}">
              {{ strtoupper($loc) }}
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</header>
