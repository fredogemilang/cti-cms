@extends('xyora::layouts.app')

@section('title', $page->title ?? 'XYORA - Tentang Kami')

@section('content')
@php
  $heroBg = $page->block('hero_bg', 'images/bg-about.jpg');
  $heroBgUrl = resolve_block_asset($heroBg);
  
  $aboutTitle = $page->block('about_title', 'Tentang Kami');
  $aboutText = $page->block('about_text', 'Xyora adalah brand teknologi jaringan Indonesia yang menghadirkan solusi konektivitas modern yang simple, seamless, dan relevan untuk kebutuhan rumah modern, gedung bertingkat, SOHO (Smart Office Home Office), ritel, hospitality, ruang publik, sekolah, dan kampus.');
  
  $visionTitle = $page->block('vision_title', 'Visi Kami');
  $visionText = $page->block('vision_text', 'Xyora memberikan konektivitas modern yang stabil, fleksibel, dan siap bertumbuh untuk membantu bisnis di berbagai industri, SMB, kampus, sekolah, dan gedung bertingkat di Indonesia untuk menjalankan operasional secara lebih efisien dan produktif.');
  
  $missionTitle = $page->block('mission_title', 'Misi Kami');
  $missionCards = $page->repeaterBlock('mission_cards');
@endphp
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ t('about.h1_seo', 'XYORA - Tentang Kami, Brand Teknologi Jaringan Indonesia') }}
</h1>

<!-- Main Content Area -->
<main class="about-page-main" style="background-image: url('{{ $heroBgUrl }}'); background-size: cover; background-position: center;">
  <div class="about-page-container">
    <div class="about-grid">
      <!-- Spacer to let the background globe graphic on the left show through -->
      <div class="about-spacer"></div>

      <!-- Content section on the right -->
      <div class="about-content">
        <h2 class="about-title">{{ $aboutTitle }}</h2>
        <p class="about-text">
          {!! $aboutText !!}
        </p>
      </div>
    </div>
  </div>
</main>

<!-- Vision Section -->
<section class="about-vision-section">
  <div class="about-vision-container">
    <h2 class="vision-title">{{ $visionTitle }}</h2>
    <p class="vision-text">
      {!! $visionText !!}
    </p>
  </div>
</section>

<!-- Mission Section -->
<section class="about-mission-section">
  <div class="about-mission-container">
    <h2 class="mission-title">{{ $missionTitle }}</h2>
    <div class="mission-grid">
      @foreach($missionCards as $card)
        <div class="mission-card">
          <img src="{{ resolve_block_asset($card['icon'] ?? 'icons/misi1.png') }}" alt="{{ $card['title'] ?? 'Mission Icon' }}" class="mission-icon" />
          <h3 class="mission-card-title">{{ $card['title'] ?? '' }}</h3>
          <p class="mission-card-text">
            {!! $card['text'] ?? '' !!}
          </p>
        </div>
      @endforeach
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
              <img src="{{ resolve_block_asset($post->featured_image) }}" alt="{{ $post->title }}" class="artikel-image" style="width: 100%; height: 100%; object-fit: cover;" />
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
@endsection
