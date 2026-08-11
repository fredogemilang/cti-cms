@php
    $productCategoryTax = \App\Models\CustomTaxonomy::where('slug', 'product_category')->first();
    $categories = $productCategoryTax
        ? $productCategoryTax->terms()->orderBy('order')->get()
        : collect();

    $postsActive = is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Setting::class);
    $archiveSlug = 'blog';
    if ($postsActive) {
        $archiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');
    }
    if (Schema::hasTable('settings')) {
        $archiveSlug = \App\Models\Setting::get('permalink_post_base', $archiveSlug);
    }
    
    $currentLocale = app()->getLocale();
    $defaultLocale = setting('default_locale', config('app.locale', 'en'));
    $searchAction = ($currentLocale !== $defaultLocale) ? url($currentLocale . '/search') : url('/search');
@endphp

<!-- Header Section -->
<header class="site-header" id="main-header">
  <div class="header-container">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo-link" id="header-logo-link" aria-label="Xyora Home">
      <img src="{{ theme_asset('images/logo.svg') }}" alt="Xyora Logo" class="logo-img" />
    </a>

    <!-- Navigation Menu -->
    <nav class="main-nav" id="navigation-bar" aria-label="Main Navigation">
      <ul class="nav-list">
        <li class="nav-item {{ request()->is('/') ? 'active' : '' }}" id="menu-beranda">
          <a href="{{ url('/') }}" class="nav-link">{{ t('nav.home', 'Beranda') }}</a>
        </li>
        <li class="nav-item dropdown {{ request()->is('*products*') ? 'active' : '' }}" id="menu-produk">
          <a href="{{ url('/products') }}" class="nav-link">
            {{ t('nav.products', 'Produk') }}
            <!-- Chevron SVG -->
            <svg class="chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </a>

          <!-- Desktop Mega Menu -->
          <div class="mega-menu" id="produk-mega-menu">
            @foreach($categories as $category)
              @php
                $children = $category->entries()->where('status', 'published')->orderBy('menu_order')->take(3)->get();
                $iconName = 'icon-wifi.png';
                if (str_contains(strtolower($category->slug), 'gateway')) {
                    $iconName = 'icon-gateway.png';
                } elseif (str_contains(strtolower($category->slug), 'switch')) {
                    $iconName = 'icon-switch.png';
                }
              @endphp
              <!-- Column -->
              <div class="mega-menu-column">
                <div class="mega-menu-header">
                  <div class="column-icon green-circle">
                    <img src="{{ theme_asset('icons/' . $iconName) }}" alt="{{ $category->name }}" width="20" height="20" style="object-fit: contain" />
                  </div>
                  <span class="mega-column-title">{{ $category->name }}</span>
                </div>
                <div class="mega-menu-items">
                  @foreach($children as $child)
                    @php
                      // Get first image from meta gallery or featured_image
                      $childImg = '';
                      if ($child->featured_image) {
                          $childImg = resolve_block_asset($child->featured_image);
                      } else {
                          // Try loading wifi/gateway/switch placeholder image based on slug
                          if (str_contains($child->slug, 'wifi') || str_contains($child->slug, 'access-point')) {
                              $childImg = theme_asset('images/wifi1.png');
                          } elseif (str_contains($child->slug, 'gateway')) {
                              $childImg = theme_asset('images/smart-gateway.png');
                          } else {
                              $childImg = theme_asset('images/switch.png');
                          }
                      }
                    @endphp
                    <a href="{{ $child->getUrl() }}" class="mega-product-item">
                      <img src="{{ $childImg }}" alt="{{ $child->title }}" class="mega-product-img" />
                      <div class="mega-product-info">
                        <span class="mega-product-name">{{ $child->getTranslation('title') }}</span>
                        <span class="mega-product-cat">{{ $category->name }}</span>
                      </div>
                    </a>
                  @endforeach
                </div>
                <button class="lihat-semua-btn" onclick="location.href = '{{ url('/' . $category->slug) }}'">
                  {{ t('nav.view_all', 'Lihat Semua') }}
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            @endforeach
          </div>
        </li>
        <li class="nav-item dropdown {{ request()->is('*usecase*') ? 'active' : '' }}" id="menu-usecase">
          <a href="#" class="nav-link">
            {{ t('nav.usecase', 'Use Case') }}
            <!-- Chevron SVG -->
            <svg class="chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </a>
          <ul class="dropdown-menu">
            <li><a href="{{ url('/usecase-sekolah-kampus') }}">{{ t('nav.usecase_school', 'Sekolah & Kampus') }}</a></li>
            <li><a href="{{ url('/usecase-hotel-resort') }}">{{ t('nav.usecase_hotel', 'Hotel & Resort') }}</a></li>
            <li><a href="{{ url('/usecase-gedung-bertingkat') }}">{{ t('nav.usecase_building', 'Gedung Bertingkat') }}</a></li>
          </ul>
        </li>
        <li class="nav-item {{ request()->is('*'.$archiveSlug.'*') ? 'active' : '' }}" id="menu-artikel">
          <a href="{{ url('/' . $archiveSlug) }}" class="nav-link">{{ t('nav.blog', 'Artikel') }}</a>
        </li>
        <li class="nav-item {{ request()->is('*tentang*') ? 'active' : '' }}" id="menu-tentang">
          <a href="{{ url('/tentang') }}" class="nav-link">{{ t('nav.about', 'Tentang Kami') }}</a>
        </li>
        <li class="nav-item" id="menu-hubungi">
          <a href="{{ url('/kontak') }}" class="nav-link hubungi">
            {{ t('nav.contact', 'Hubungi Kami') }}
            <!-- Chevron Circle SVG -->
            <span class="circle-chevron">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="10" height="10">
                <path d="M9 18l6-6-6-6" />
              </svg>
            </span>
          </a>
        </li>
      </ul>

      <!-- Search bar inside nav for responsive layouts -->
      <div class="header-search mobile-only-search">
        <form role="search" method="get" action="{{ $searchAction }}" id="search-form-mobile">
          <label for="search-input-mobile" class="sr-only">{{ t('search.placeholder', 'Cari produk atau artikel') }}</label>
          <input type="search" name="q" id="search-input-mobile" class="search-input" placeholder="{{ t('search.text', 'Search') }}" aria-label="Search" value="{{ request('q') }}" />
          <button type="submit" class="search-btn" aria-label="Submit Search">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </button>
        </form>
      </div>
    </nav>

    <!-- Search bar (Desktop View) -->
    <div class="header-search desktop-only-search" id="search-container-desktop">
      <form role="search" method="get" action="{{ $searchAction }}" id="search-form-desktop">
        <label for="search-input-desktop" class="sr-only">{{ t('search.placeholder', 'Cari produk atau artikel') }}</label>
        <input type="search" name="q" id="search-input-desktop" class="search-input" placeholder="{{ t('search.text', 'Search') }}" aria-label="Search" value="{{ request('q') }}" />
        <button type="submit" class="search-btn" aria-label="Submit Search">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
        </button>
      </form>
    </div>

    <!-- Hamburger Menu Toggle Button (Mobile/Tablet View) -->
    <button class="mobile-menu-toggle" id="menu-toggle-btn" aria-label="Toggle navigation menu" aria-controls="navigation-bar" aria-expanded="false">
      <!-- Open Icon -->
      <svg class="menu-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" x2="20" y1="12" y2="12" />
        <line x1="4" x2="20" y1="6" y2="6" />
        <line x1="4" x2="20" y1="18" y2="18" />
      </svg>
      <!-- Close Icon -->
      <svg class="menu-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none">
        <line x1="18" x2="6" y1="6" y2="18" />
        <line x1="6" x2="18" y1="6" y2="18" />
      </svg>
    </button>
  </div>
</header>
