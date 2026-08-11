@extends('xyora::layouts.app')

@section('title', $page->title ?? 'XYORA - Jaringan Cepat, Stabil, Mudah Dikelola')

@section('content')
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ t('home.h1_seo', 'XYORA - Solusi Jaringan Wi-Fi Premium dengan Desain Estetis') }}
</h1>

@php
  // 1. Slider Section
  $sliderItems = $page->repeaterBlock('slider_items', [
    [
      'title' => 'WiFi Kencang,<br />Desain Elegan',
      'subtitle' => 'In-Wall Access Point Wifi 7',
      'button_text' => 'Info Lengkap',
      'button_link' => '/products/access-point/wi-fi-7-in-wall-access-point',
      'image' => 'images/slider1.png'
    ],
    [
      'title' => 'Performa Andal <br />di Segala Kondisi',
      'subtitle' => 'Outdoor Access Point',
      'button_text' => 'Info Lengkap',
      'button_link' => '/products/access-point/outdoor-access-point',
      'image' => 'images/slider2.png'
    ],
    [
      'title' => 'Integrasi Mudah, <br />Operasional Efisien',
      'subtitle' => 'Smart Network Switch',
      'button_text' => 'Info Lengkap',
      'button_link' => '/products/switch/poe-switch',
      'image' => 'images/slider3.png'
    ]
  ]);

  // 2. Overview Section
  $overviewTitle = $page->block('overview_title', 'Jaringan Cepat, Stabil, dan Mudah Dikelola');
  $overviewText = $page->block('overview_text', 'Dirancang untuk menghadirkan konektivitas yang andal, terintegrasi, dan siap mendukung kebutuhan operasional yang terus berkembang untuk kantor, ritel, dan berbagai bisnis modern dari enterprise hingga SMB.');
  $overviewImage = $page->block('overview_image', 'images/overview.png');
  $overviewBtnText = $page->block('overview_btn_text', 'Pelajari Selengkapnya');
  $overviewBtnLink = $page->block('overview_btn_link', '/tentang');

  // 3. Why Section
  $whyTitle = $page->block('why_title', 'Mengapa Xyora?');
  $whySubtitle = $page->block('why_subtitle', 'Teknologi mutakhir untuk performa jaringan terbaik bisnis Anda.');
  $whyBackground = $page->block('why_background', '');
  $whyCards = $page->repeaterBlock('why_cards', [
    [
      'title' => 'Sesuai dengan Kebutuhan Bisnis',
      'text' => 'Xyora mendukung kebutuhan operasional bisnis yang dinamis dan mudah beradaptasi dengan pertumbuhan perangkat, infrastruktur, dan ekspansi lokal.',
      'image' => 'images/why1.jpg'
    ],
    [
      'title' => 'Local Brand dengan Local Support',
      'text' => 'Sebagai brand lokal Indonesia, Xyora memahami kebutuhan SMB lokal dan memberikan layanan yang lebih cepat, responsif, dan mudah dijangkau.',
      'image' => 'images/why2.jpg'
    ],
    [
      'title' => 'Performa Sesuai Kebutuhan Bisnis',
      'text' => 'Performa jaringan andal dengan investasi tetap efisien, cocok untuk bisnis yang membutuhkan koneksi stabil.',
      'image' => 'images/why3.jpg'
    ],
    [
      'title' => 'Mudah Terintegrasi',
      'text' => 'Desain modern yang dirancang agar mudah terintegrasi dengan perangkat dan sistem bisnis tanpa mengganggu operasional.',
      'image' => 'images/why4.jpg'
    ]
  ]);

  // 4. Bisnis Section
  $bisnisTitle = $page->block('bisnis_title', 'Bagaimana Xyora Membantu Bisnis Anda?');
  $bisnisBackground = $page->block('bisnis_background', '');
  $bisnisSlides = $page->repeaterBlock('bisnis_slides', [
    [
      'image_caption' => 'Gedung Bertingkat',
      'challenge_text' => "Pertumbuhan jumlah pengguna dan perangkat yang terhubung secara bersamaan di gedung bertingkat seperti perkantoran, mal, dan apartemen menuntut jaringan yang lebih andal dan berkapasitas tinggi.\n\nNamun, banyak infrastruktur yang belum dirancang untuk mendukung kebutuhan digital saat ini, sehingga berpotensi menimbulkan gangguan konektivitas, pengalaman pengguna yang tidak konsisten, serta menurunkan produktivitas operasional.",
      'solution_text' => "Xyora menghadirkan solusi jaringan yang stabil, cepat, dan aman untuk mendukung kebutuhan operasional bisnis dan hunian modern. Dirancang untuk menangani pertumbuhan pengguna, perangkat, dan aplikasi yang terus meningkat, Xyora membantu memastikan konektivitas tetap optimal di seluruh area.\n\nDengan performa yang andal and pengelolaan yang efisien, bisnis dapat beroperasi lebih produktif tanpa hambatan jaringan.",
      'button_link' => '/usecase-gedung-bertingkat',
      'image' => 'images/industri1.jpg'
    ],
    [
      'image_caption' => 'Hotel & Resort',
      'challenge_text' => "Access point yang sudah tidak optimal kerap kesulitan menangani lonjakan jumlah pengguna dan perangkat yang terhubung secara bersamaan di hotel maupun resort sehingga berpotensi menyebabkan koneksi melambat, cakupan Wi-Fi yang tidak merata, pengalaman internet yang kurang konsisten, serta mengganggu kelancaran operasional dan kepuasan tamu.",
      'solution_text' => "Xyora menyediakan solusi jaringan yang dirancang untuk memenuhi kebutuhan konektivitas di lingkungan hospitality. Performa yang stabil, cakupan yang luas, serta kapasitas tinggi memungkinkan tamu menikmati akses internet yang lancar di seluruh area hotel maupun resort.\n\nSementara itu, pengelolaan jaringan yang efisien membantu tim operasional memastikan layanan tetap optimal, sehingga pengalaman menginap dan kualitas pelayanan dapat terus terjaga.",
      'button_link' => '/usecase-hotel-resort',
      'image' => 'images/industri2.jpg'
    ],
    [
      'image_caption' => 'Sekolah & Kampus',
      'challenge_text' => "Jaringan Wi-Fi sekolah atau kampus yang lambat dan sering terputus menghambat metode belajar mengajar digital (e-learning). Sinyal yang lemah di beberapa area kelas dan kesulitan dalam mengelola ratusan perangkat siswa/mahasiswa secara efisien sering kali menjadi kendala utama.",
      'solution_text' => "Xyora menyediakan Wi-Fi 6 berkecepatan tinggi yang stabil untuk mendukung aktivitas e-learning secara seamless. Dengan jangkauan luas di area kelas dan kapasitas besar untuk menampung ratusan perangkat sekaligus, koneksi tetap lancar.\n\nManajemen terpusat yang mudah membantu staf IT mengontrol jaringan secara efisien demi menunjang kualitas pendidikan terbaik.",
      'button_link' => '/usecase-sekolah-kampus',
      'image' => 'images/industri3.jpg'
    ]
  ]);
@endphp

<!-- Hero Slider Section -->
<section class="hero-slider-section" id="xyora-hero-slider" aria-label="Featured Products Slider">
  <div class="slider-container">
    <!-- Scroller of Slides -->
    <div class="slides-wrapper" id="carousel-scroller">
      @foreach($sliderItems as $index => $slide)
        <article class="slide" id="slide-{{ $index + 1 }}" aria-roledescription="slide" aria-label="{{ $index + 1 }} of {{ count($sliderItems) }}">
          <div class="slide-content">
            <h2 class="slide-title">{!! $slide['title'] ?? '' !!}</h2>
            <p class="slide-subtitle">{{ $slide['subtitle'] ?? '' }}</p>
            <button class="slide-btn" onclick="location.href = '{{ url($slide['button_link'] ?? '#') }}'">
              {{ $slide['button_text'] ?? 'Info Lengkap' }}
            </button>
          </div>
          <div class="slide-visual">
            <img src="{{ resolve_block_asset($slide['image'] ?? '') }}" alt="{{ $slide['subtitle'] ?? '' }}" class="slide-image" {!! $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' !!} />
          </div>
        </article>
      @endforeach
    </div>

    <!-- Navigation Indicators (Dots) -->
    <div class="slider-pagination" id="carousel-dots-indicator" role="tablist" aria-label="Slide Selection">
      @foreach($sliderItems as $index => $slide)
        <button class="page-dot {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="slide-{{ $index + 1 }}" aria-label="Slide {{ $index + 1 }}"></button>
      @endforeach
    </div>
  </div>
</section>

<!-- Products Overview Info Section -->
<section class="info-section" id="xyora-info-section" aria-label="Jaringan Cepat dan Stabil">
  <!-- Ambient Green Glow & Diagonal Light Rays -->
  <div class="glow-bg-container" aria-hidden="true">
    <div class="green-radial-glow"></div>
    <div class="green-diagonal-stripes"></div>
  </div>

  <div class="info-container">
    <div class="info-visual">
      <img src="{{ resolve_block_asset($overviewImage) }}" alt="{{ $overviewTitle }}" class="info-img" />
    </div>
    <div class="info-content">
      <h2 class="info-title">
        {{ $overviewTitle }}
      </h2>
      <p class="info-text">
        {{ $overviewText }}
      </p>
      <button class="info-btn" onclick="location.href = '{{ url($overviewBtnLink) }}'">
        {{ $overviewBtnText }}
      </button>
    </div>
  </div>
</section>

<!-- Mengapa Xyora Section -->
<section class="mengapa-section" id="mengapa-xyora" aria-label="Mengapa Xyora?" @if($whyBackground) style="background: url('{{ resolve_block_asset($whyBackground) }}') no-repeat center/cover;" @endif>
  <div class="mengapa-container">
    <h2 class="mengapa-title">{{ $whyTitle }}</h2>

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
      @foreach($whyCards as $card)
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
<section class="bisnis-section" id="bisnis-slider-section" aria-label="Bagaimana Xyora Membantu Bisnis Anda?" @if($bisnisBackground) style="background: url('{{ resolve_block_asset($bisnisBackground) }}') no-repeat center/cover;" @endif>
  <div class="bisnis-container">
    <h2 class="bisnis-section-title">
      {{ $bisnisTitle }}
    </h2>

    <div class="bisnis-slider-wrapper">
      @foreach($bisnisSlides as $index => $slide)
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
            <img src="{{ resolve_block_asset($slide['image'] ?? '') }}" alt="{{ $slide['image_caption'] ?? '' }}" class="bisnis-image" />
            <h3 class="bisnis-image-caption">{{ $slide['image_caption'] ?? '' }}</h3>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Navigation Dots (Bisnis Section) -->
    <div class="bisnis-dots">
      @foreach($bisnisSlides as $index => $slide)
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
            
            // Calc read time
            $wordCount = str_word_count(strip_tags($post->content));
            $readTime = max(1, ceil($wordCount / 200));

            $postImg = '';
            if ($post->featured_image) {
                $postImg = resolve_block_asset($post->featured_image);
            }
          @endphp
          <div class="artikel-card">
            <div class="artikel-img-wrapper">
              @if($postImg)
                <img src="{{ $postImg }}" alt="{{ $post->getTranslation('title') }}" style="width: 100%; height: 100%; object-fit: cover;" />
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

<!-- Contact Section -->
<section class="contact-section" id="contact" style="background-image: url('{{ theme_asset('images/bg-contact.png') }}')">
  <div class="contact-overlay"></div>
  <div class="contact-container">
    <div class="contact-header">
      <h2 class="contact-title">{{ t('home.contact_title', 'Perlu Konsultasi Lebih Lanjut?') }}</h2>
      <p class="contact-subtitle">
        {{ t('home.contact_subtitle', 'Silakan isi form berikut untuk konsultasi GRATIS terkait kebutuhan jaringan di rumah atau bisnis Anda.') }}
      </p>
    </div>

    @if (session('success'))
      <div class="success-alert" style="background: rgba(137, 197, 92, 0.2); border: 1px solid #89C55C; color: #538d24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; position: relative; z-index: 10;">
        ✓ {{ session('success') }}
      </div>
    @endif
    @php
      $contactForm = \App\Models\Form::where('slug', 'contact-form')->with('fields')->first();
    @endphp

    @if ($contactForm)
      <form action="{{ route('forms.submit', $contactForm->slug) }}" method="POST" class="contact-form">
        @csrf
        
        @php
          $fields = $contactForm->fields->sortBy('order');
          $groupedRows = [];
          $tempRow = [];
          foreach ($fields as $field) {
              if ($field->type === 'textarea') {
                  if (!empty($tempRow)) {
                      $groupedRows[] = $tempRow;
                      $tempRow = [];
                  }
                  $groupedRows[] = [$field];
              } else {
                  $tempRow[] = $field;
                  if (count($tempRow) === 2) {
                      $groupedRows[] = $tempRow;
                      $tempRow = [];
                  }
              }
          }
          if (!empty($tempRow)) {
              $groupedRows[] = $tempRow;
          }
        @endphp

        @foreach ($groupedRows as $row)
          @if (count($row) === 2)
            <div class="form-row">
              @foreach ($row as $field)
                <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                       name="{{ $field->field_id }}" 
                       value="{{ old($field->field_id) }}" 
                       placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                       class="form-input" 
                       {{ $field->is_required ? 'required' : '' }} />
              @endforeach
            </div>
          @else
            @php $field = $row[0]; @endphp
            @if ($field->type === 'textarea')
              <textarea name="{{ $field->field_id }}" 
                        placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                        class="form-input form-textarea" 
                        style="grid-column: 1 / -1; height: 100px; resize: vertical; margin-bottom: 1.25rem;"
                        {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_id) ?: 'Konsultasi gratis via formulir beranda Xyora.' }}</textarea>
            @else
              <div class="form-row" style="display: block;">
                <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                       name="{{ $field->field_id }}" 
                       value="{{ old($field->field_id) }}" 
                       placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                       class="form-input" 
                       style="width: 100%;" 
                       {{ $field->is_required ? 'required' : '' }} />
              </div>
            @endif
          @endif
        @endforeach

        <div class="form-checkbox">
          <input type="checkbox" id="consent" class="custom-checkbox" required />
          <label for="consent">
            {{ t('home.contact_consent', 'Dengan mengisi data di atas, Anda mengizinkan Xyora dan pihak terkait untuk mengumpulkan dan memproses sesuai kebutuhan.') }}
          </label>
        </div>

        <div class="form-action">
          <div class="recaptcha-placeholder">
            @php
              $captchaProvider = $contactForm->spam_protection['captcha_provider'] ?? 'none';
              $captchaService = new \App\Services\CaptchaService;
              $captchaHtml = $captchaService->renderWidget($captchaProvider);
            @endphp
            @if(!empty($captchaHtml))
              {!! $captchaHtml !!}
            @else
              <div class="recaptcha-box">
                <div class="recaptcha-left">
                  <input type="checkbox" id="recaptcha-mock" class="recaptcha-check" required />
                  <label for="recaptcha-mock">I'm not a robot</label>
                </div>
                <div class="recaptcha-logo">
                  <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" width="24" />
                  <span>reCAPTCHA<br />Privacy - Terms</span>
                </div>
              </div>
            @endif
          </div>
          <button type="submit" class="btn-kirim">{{ t('home.contact_submit', 'Kirim') }}</button>
        </div>
      </form>
    @endif
  </div>
</section>
@endsection
