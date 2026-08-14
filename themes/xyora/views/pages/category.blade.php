@extends('xyora::layouts.app')

@section('title', $page->title)

@section('content')
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ $page->getTranslation('title') }} - {{ t('products.h1_seo_cat', 'Solusi Perangkat Jaringan Terbaik') }}
</h1>

@php
  $heroBg = '';
  $rawHeroBg = $page->block('hero_bg');
  if ($rawHeroBg) {
      $heroBg = resolve_block_asset($rawHeroBg);
  } else {
      $heroBg = theme_asset('images/main-product1.png');
      if (str_contains($page->slug, 'gateway')) {
          $heroBg = theme_asset('images/main-product2.png');
      } elseif (str_contains($page->slug, 'switch')) {
          $heroBg = theme_asset('images/main-product3.png');
      }
  }

  // Retrieve products for the corresponding category term
  $term = \App\Models\TaxonomyTerm::whereHas('taxonomy', function($q) {
      $q->where('slug', 'product_category');
  })->where('slug', $page->slug)->first();

  $childProducts = $term 
      ? $term->entries()->where('status', 'published')->orderBy('menu_order')->get() 
      : collect();

  $categoryTitle = $page->block('category_title', $page->getTranslation('title'));
  $categoryExcerpt = $page->block('category_excerpt', $page->getTranslation('excerpt') ?: t('products.default_cat_desc', 'Solusi perangkat jaringan andal untuk bisnis Anda.'));
  $categoryDescription = $page->block('category_description', $page->getTranslation('content') ?: '');
@endphp

<!-- Hero Banner Section -->
<section class="main-prod-hero" style="background-image: url('{{ $heroBg }}')">
  <div class="main-prod-hero-container">
    <div class="main-prod-hero-grid">
      <!-- Left spacer to let the background image graphics show through -->
      <div class="main-prod-hero-spacer"></div>

      <!-- Right text content -->
      <div class="main-prod-hero-content">
        <h2 class="main-prod-hero-title">{{ $categoryTitle }}</h2>
        <p class="main-prod-hero-text">
          {{ $categoryExcerpt }}
        </p>
        @if($categoryDescription)
          <p class="main-prod-hero-subtext">
            {!! strip_tags($categoryDescription) !!}
          </p>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Product Grid Section -->
<section class="main-prod-grid-section">
  <div class="main-prod-grid-container">
    @if($childProducts->isEmpty())
      <div class="no-products-message" style="text-align: center; padding: 40px; color: #999;">
        {{ t('products.no_items_in_category', 'Belum ada produk di kategori ini.') }}
      </div>
    @else
      <div class="main-prod-grid">
        @foreach($childProducts as $prod)
          @php
            $meta = $prod->meta ?? [];
            $modelCode = $meta['model_code'] ?? '';
            $badge = $meta['badge'] ?? '';
            $prodImg = $prod->featured_image;
            if (! $prodImg) {
                // Try placeholder based on slug
                if (str_contains($prod->slug, 'wifi') || str_contains($prod->slug, 'access-point')) {
                    $prodImg = theme_asset('images/wifi1.png');
                } elseif (str_contains($prod->slug, 'gateway')) {
                    $prodImg = theme_asset('images/smart-gateway.png');
                } else {
                    $prodImg = theme_asset('images/switch.png');
                }
            }
          @endphp
          <!-- Card -->
          <a href="{{ $prod->getUrl() }}" class="product-item-card">
            @if($badge)
              <span class="product-badge-new">{{ $badge }}</span>
            @endif
            <div class="product-img-holder">
              <x-image :src="$prodImg" alt="{{ $prod->getTranslation('title') }}" class="w-full h-full object-cover" sizes="100vw" />
            </div>
            <div class="product-text-holder">
              <h3 class="product-name">
                {!! $prod->getTranslation('title') !!}
              </h3>
              @if($modelCode)
                <span class="product-model">{{ $modelCode }}</span>
              @endif
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</section>

<!-- Mengapa Xyora Section -->
<section class="mengapa-section" id="mengapa-xyora" aria-label="Mengapa Xyora?" @if($whyBackground = ($homePage = \App\Models\Page::where('slug', 'home')->first()) ? $homePage->block('why_background') : '') style="background: url('{{ resolve_block_asset($whyBackground) }}') no-repeat center/cover;" @endif>
  <div class="mengapa-container">
    <h2 class="mengapa-title">{{ $homePage ? $homePage->block('why_title', 'Mengapa Xyora?') : 'Mengapa Xyora?' }}</h2>

    <div class="mengapa-pills">
      <div class="mengapa-pill">
        <div class="pill-icon">
          <img src="{{ theme_asset('icons/icon-why1.png') }}" alt="Adaptive" width="26" height="26" />
        </div>
        <span class="pill-text">Adaptive</span>
      </div>
      <div class="mengapa-pill">
        <div class="pill-icon">
          <img src="{{ theme_asset('icons/icon-why2.png') }}" alt="Seamless" width="26" height="26" />
        </div>
        <span class="pill-text">Seamless</span>
      </div>
      <div class="mengapa-pill">
        <div class="pill-icon">
          <img src="{{ theme_asset('icons/icon-why3.png') }}" alt="Relevant" width="26" height="26" />
        </div>
        <span class="pill-text">Relevant</span>
      </div>
    </div>

    <div class="mengapa-grid">
      @foreach(($homePage ? $homePage->repeaterBlock('why_cards', []) : []) as $card)
        <div class="mengapa-card" style="background-image: url('{{ resolve_block_asset($card['image'] ?? '') }}')">
          <div class="mengapa-card-overlay">
            <h3 class="mengapa-card-title">{{ $card['title'] ?? '' }}</h3>
            <p class="mengapa-card-text">
              {{ $card['text'] ?? '' }}
            </p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Bisnis Slider Section -->
<section class="bisnis-section" id="bisnis-slider-section" aria-label="Bagaimana Xyora Membantu Bisnis Anda?" @if($bisnisBackground = ($homePage ? $homePage->block('bisnis_background') : '')) style="background: url('{{ resolve_block_asset($bisnisBackground) }}') no-repeat center/cover;" @endif>
  <div class="bisnis-container">
    <h2 class="bisnis-section-title">
      {{ $homePage ? $homePage->block('bisnis_title', 'Bagaimana Xyora Membantu Bisnis Anda?') : 'Bagaimana Xyora Membantu Bisnis Anda?' }}
    </h2>

    <div class="bisnis-slider-wrapper">
      @foreach(($homePage ? $homePage->repeaterBlock('bisnis_slides', []) : []) as $index => $slide)
        <div class="bisnis-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
          <div class="bisnis-content-col">
            <div class="bisnis-card">
              <div class="bisnis-card-header">
                <div class="bisnis-card-icon">
                  <img src="{{ theme_asset('icons/icon-tantangan.png') }}" alt="Tantangan" width="24" height="24" />
                </div>
                <h3 class="bisnis-card-title">{{ t('usecase.challenge', 'Tantangan') }}</h3>
                <div class="bisnis-card-chevron">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>
              <div class="bisnis-card-body">
                @foreach(explode("\n\n", $slide['challenge_text'] ?? '') as $paragraph)
                  @if(trim($paragraph))
                    <p>{{ $paragraph }}</p>
                  @endif
                @endforeach
              </div>
            </div>

            <div class="bisnis-card">
              <div class="bisnis-card-header">
                <div class="bisnis-card-icon">
                  <img src="{{ theme_asset('icons/icon-solusi.png') }}" alt="Solusi Xyora" width="24" height="24" />
                </div>
                <h3 class="bisnis-card-title">{{ t('usecase.solution', 'Solusi Xyora') }}</h3>
                <div class="bisnis-card-chevron">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>
              <div class="bisnis-card-body">
                @foreach(explode("\n\n", $slide['solution_text'] ?? '') as $paragraph)
                  @if(trim($paragraph))
                    <p>{{ $paragraph }}</p>
                  @endif
                @endforeach
              </div>
            </div>

            <button class="bisnis-btn" onclick="location.href = '{{ url($slide['button_link'] ?? '#') }}'">
              {{ t('home.learn_more', 'Pelajari Selengkapnya') }}
            </button>
          </div>

          <div class="bisnis-image-col">
            <x-image :src="$slide['image'] ?? ''" alt="{{ $slide['image_caption'] ?? '' }}" class="bisnis-image" sizes="100vw" />
            <h3 class="bisnis-image-caption">{{ $slide['image_caption'] ?? '' }}</h3>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Navigation Dots (Bisnis Section) -->
    <div class="bisnis-dots">
      @foreach(($homePage ? $homePage->repeaterBlock('bisnis_slides', []) : []) as $index => $slide)
        <span class="bisnis-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></span>
      @endforeach
    </div>
  </div>
</section>

@php
  $latestPosts = collect();
  if (is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Post::class)) {
      $latestPosts = \Plugins\Posts\Models\Post::where('status', 'published')
          ->orderBy('published_at', 'desc')
          ->take(3)
          ->get();
  }
@endphp

@if($latestPosts->isNotEmpty())
  <!-- Artikel Section -->
  <section class="artikel-section" id="artikel" aria-label="Baca Lebih Lanjut" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="artikel-container">
      <h2 class="artikel-title">{{ t('home.articles_title', 'Baca Lebih Lanjut') }}</h2>
      <p class="artikel-subtitle">
        {{ t('home.articles_subtitle', 'Temukan berbagai artikel terbaru tentang teknologi jaringan, mulai dari gateway, switch, dan wireless access point hingga tips memilih perangkat terbaik sesuai kebutuhan rumah hingga bisnis Anda.') }}
      </p>

      <div class="artikel-grid">
        @foreach($latestPosts as $post)
          @php
            $dateFormatted = $post->published_at ? $post->published_at->translatedFormat('j F Y') : $post->created_at->translatedFormat('j F Y');
            
            $postImg = $post->featured_image;
          @endphp
          <div class="artikel-card">
            <div class="artikel-img-wrapper">
              @if($postImg)
                <x-image :src="$postImg" alt="{{ $post->getTranslation('title') }}" class="w-full h-full object-cover" sizes="100vw" />
              @else
                <div class="artikel-img-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="64" height="64">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
                </div>
              @endif
            </div>
            <div class="artikel-content">
              <div class="artikel-meta">
                <span class="meta-item">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  {{ $dateFormatted }}
                </span>
              </div>
              <h3 class="artikel-card-title">
                {{ $post->getTranslation('title') }}
              </h3>
              <a href="{{ $post->getUrl() }}" class="artikel-link" style="text-decoration: none;">
                {{ t('blog.read_more', 'Baca lebih lanjut') }}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </a>
            </div>
          </div>
        @endforeach
      </div>

      <div class="artikel-action" style="margin-top: 3rem">
        @php
          $archiveUrl = url('/blog');
          if (is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Setting::class)) {
              $archiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');
              if (Schema::hasTable('settings')) {
                  $archiveSlug = \App\Models\Setting::get('permalink_post_base', $archiveSlug);
              }
              $archiveUrl = url('/' . $archiveSlug);
          }
        @endphp
        <a href="{{ $archiveUrl }}" class="btn-artikel-lainnya" style="text-decoration: none">
          {{ t('blog.view_more_articles', 'Baca Artikel Lainnya') }}
        </a>
      </div>
    </div>
  </section>
@endif
@endsection
