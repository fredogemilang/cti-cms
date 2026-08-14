@extends('xyora::layouts.app')

@section('title', $page->title ?? 'XYORA - Gedung Bertingkat')

@section('content')
@php
  $heroBg = $page->block('hero_bg', 'images/gedung-bertingkat.png');
  $heroBgUrl = resolve_block_asset($heroBg);
  
  $aboutImage = $page->block('about_image', 'images/about-gedung-bertingkat.png');
  $aboutImageUrl = resolve_block_asset($aboutImage);
  
  $solusiCards = $page->repeaterBlock('solusi_cards');
  
  $manajemenImage = $page->block('manajemen_image', 'images/bg-manajemen-jaringan.png');
  $manajemenImageUrl = resolve_block_asset($manajemenImage);
  
  $manajemenTitle = $page->block('manajemen_title', 'Manajemen <br /><span>Jaringan Terpusat</span>');
  $manajemenText = $page->block('manajemen_text', 'Seluruh infrastruktur jaringan dapat dikelola secara terpusat melalui Xyora XA-GW411S Smart Gateway, yang mampu mengelola hingga 128 access point dalam satu sistem terintegrasi. Dilengkapi fitur cloud management, security firewall, distributed AP networking, dan remote management, solusi ini memudahkan tim IT untuk memantau, mengamankan, dan mengoptimalkan seluruh jaringan dari satu platform.');
@endphp
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ t('usecase.building_h1_seo', 'XYORA - Solusi Jaringan Wi-Fi Premium untuk Gedung Bertingkat') }}
</h1>

<!-- Main Content Area -->
<main>
  <!-- Usecase Detail Section -->
  <section class="usecase-detail-section">
    <div class="usecase-hero" style="background-image: url('{{ $heroBgUrl }}');">
      <h1 class="usecase-hero-title">{{ $page->title }}</h1>
    </div>

    <div class="usecase-content-container">
      <div class="usecase-challenge-card" style="background-image: linear-gradient(90deg, #ffffff 0%, #ffffff 55%, rgba(255, 255, 255, 0) 100%), url('{{ $aboutImageUrl }}');">
        <!-- Left: Info & Text -->
        <div class="usecase-challenge-info">
          <h2>{{ t('usecase.challenge', 'Tantangan') }}</h2>
          <div class="green-line"></div>
          <p>
            {{ t('usecase.building_challenge_p1', 'Di era kerja hybrid dan operasional serba digital, jaringan di gedung bertingkat seperti apartemen, pusat perbelanjaan (mal), dan gedung perkantoran menjadi fondasi utama produktivitas bisnis. Ketika puluhan hingga ratusan perangkat terkoneksi secara bersamaan untuk mengakses aplikasi cloud, video conference, ERP, CRM, dan berbagai sistem bisnis, infrastruktur WiFi yang sudah lama digunakan sering kali kesulitan menjaga performa tetap stabil. Akibatnya, pengguna mengalami koneksi lambat, meeting online terganggu, hingga penurunan produktivitas karena akses jaringan yang tidak konsisten.') }}
          </p>
          <p>
            {{ t('usecase.building_challenge_p2', 'Tantangan semakin kompleks pada lingkungan gedung perkantoran, mal, dan apartemen dengan banyak area, ruang kerja, ruang meeting, and area bersama. Struktur bangunan dapat menyebabkan cakupan sinyal tidak merata, sementara perangkat yang sudah usang lebih rentan terhadap gangguan performa dan belum mendukung standar keamanan terbaru. Kondisi ini membuat manajemen gedung membutuhkan infrastruktur jaringan yang lebih modern, andal, dan siap mendukung kebutuhan operasional jangka panjang.') }}
          </p>
        </div>

        <!-- Right: Image -->
        <div class="usecase-challenge-image">
          <x-image :src="$aboutImage" alt="Tantangan Jaringan Gedung Bertingkat" class="w-full h-full object-cover" sizes="100vw" />
        </div>
      </div>
    </div>
  </section>

  <!-- Solusi Xyora Section -->
  <section class="solusi-section">
    <div class="solusi-header-wrapper">
      <h2 class="solusi-title">{{ t('usecase.xyora_solution_title', 'Solusi Xyora') }}</h2>
      <div class="green-line"></div>
    </div>

    <div class="solusi-grid">
      @foreach($solusiCards as $card)
        <div class="solusi-card">
          <div class="solusi-badge">
            <x-image :src="$card['badge'] ?? 'icons/verified.png'" alt="Verified Badge" sizes="100vw" />
          </div>
          <div class="solusi-card-image">
            <x-image :src="$card['image'] ?? 'images/solusi1.png'" alt="{{ $card['title'] ?? 'Solusi' }}" class="w-full h-full object-cover" sizes="100vw" />
          </div>
          <h3 class="solusi-card-title">{{ $card['title'] ?? '' }}</h3>
          <div class="solusi-card-body">
            <p>{!! $card['text'] ?? '' !!}</p>
            @if(!empty($card['subtext']))
              <p>{!! $card['subtext'] !!}</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <!-- Manajemen Jaringan Section -->
  <section class="manajemen-section">
    <div class="manajemen-container">
      <div class="manajemen-card" style="background: linear-gradient(270deg, rgba(255, 255, 255, 0) 0%, #E0EEFB 55.92%), url('{{ $manajemenImageUrl }}') no-repeat right center / cover;">
        <!-- Left Column: Info & Text -->
        <div class="manajemen-info">
          <h2>{!! $manajemenTitle !!}</h2>
          <div class="green-line"></div>
          <p>
            {!! $manajemenText !!}
          </p>
        </div>

        <!-- Right Column: Image (visible on mobile) -->
        <div class="manajemen-image">
          <x-image :src="$manajemenImage" alt="Manajemen Jaringan Terpusat Xyora" class="w-full h-full object-cover" sizes="100vw" />
        </div>
      </div>
    </div>
  </section>

  <!-- Artikel Section (Dynamic Blog Posts) -->
  @php
    $postsActive = is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Post::class);
    $latestPosts = $postsActive
        ? \Plugins\Posts\Models\Post::where('status', 'published')->latest()->take(3)->get()
        : collect();

    $archiveSlug = 'blog';
    if (is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Setting::class)) {
        $archiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');
    }
    if (Schema::hasTable('settings')) {
        $archiveSlug = \App\Models\Setting::get('permalink_post_base', $archiveSlug);
    }
  @endphp

  @if($latestPosts->isNotEmpty())
  <section class="artikel-section" id="artikel" aria-label="Baca Lebih Lanjut">
    <div class="artikel-container">
      <h2 class="artikel-title">{{ t('about.read_more_title', 'Baca Lebih Lanjut') }}</h2>
      <p class="artikel-subtitle">
        {{ t('about.read_more_subtitle', 'Temukan berbagai artikel terbaru tentang teknologi jaringan, mulai dari gateway, switch, dan wireless access point hingga tips memilih perangkat terbaik sesuai kebutuhan rumah hingga bisnis Anda.') }}
      </p>

      <div class="artikel-grid">
        @foreach($latestPosts as $post)
          <div class="artikel-card">
            <div class="artikel-img-wrapper">
              @if($post->featured_image)
                <x-image :src="$post->featured_image" alt="{{ $post->title }}" class="artikel-image w-full h-full object-cover" sizes="100vw" />
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
                  {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : '' }}
                </span>
              </div>
              <h3 class="artikel-card-title">
                {{ $post->getTranslation('title') }}
              </h3>
              <a href="{{ $post->getUrl() }}" class="artikel-link">
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
</main>
@endsection
