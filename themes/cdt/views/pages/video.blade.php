@extends('cdt::layouts.app')

@section('content')
<style>
  /* ===== Video V3 Light Modern Styles ===== */

  /* Ambient glow behind player */
  .player-glow {
    position: relative;
  }

  .player-glow::before {
    content: '';
    position: absolute;
    inset: -30px;
    background: radial-gradient(ellipse at center, rgba(227, 6, 19, 0.06) 0%, transparent 70%);
    filter: blur(50px);
    pointer-events: none;
    z-index: 0;
  }

  /* Video card hover effects */
  .video-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }

  .video-card:hover {
    border-color: rgba(227, 6, 19, 0.15);
    transform: translateY(-5px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(227, 6, 19, 0.06);
  }

  .video-card .card-thumb {
    position: relative;
    overflow: hidden;
  }

  .video-card .card-thumb img {
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .video-card:hover .card-thumb img {
    transform: scale(1.06);
  }

  .video-card .play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.25);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .video-card:hover .play-overlay {
    opacity: 1;
  }

  .video-card .play-btn-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(227, 6, 19, 0.92);
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(0.85);
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 6px 20px rgba(227, 6, 19, 0.3);
  }

  .video-card:hover .play-btn-circle {
    transform: scale(1);
  }

  /* Active card */
  .video-card.is-active {
    border-color: rgba(227, 6, 19, 0.3);
    box-shadow: 0 4px 20px rgba(227, 6, 19, 0.08);
    background: #fffbfb;
  }

  /* Now Playing indicator */
  .now-playing-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(227, 6, 19, 0.08);
    color: #e30613;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  .eq-dot {
    width: 3px;
    border-radius: 2px;
    background: #e30613;
    display: inline-block;
  }

  .eq-dot:nth-child(1) {
    height: 8px;
    animation: eqBounce 0.6s ease-in-out infinite alternate;
  }

  .eq-dot:nth-child(2) {
    height: 12px;
    animation: eqBounce 0.6s ease-in-out 0.15s infinite alternate;
  }

  .eq-dot:nth-child(3) {
    height: 6px;
    animation: eqBounce 0.6s ease-in-out 0.3s infinite alternate;
  }

  @keyframes eqBounce {
    0% {
      transform: scaleY(0.4);
    }
    100% {
      transform: scaleY(1);
    }
  }

  /* Category pills */
  .cat-pill {
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    color: #6b7280;
    background: #fff;
    transition: all 0.25s ease;
    white-space: nowrap;
    cursor: pointer;
  }

  .cat-pill:hover {
    color: #e30613;
    border-color: rgba(227, 6, 19, 0.25);
    background: #fff5f5;
  }

  .cat-pill.active {
    background: linear-gradient(135deg, #e30613, #c00510);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 14px rgba(227, 6, 19, 0.25);
  }

  /* Search input */
  .search-light {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    color: #111;
    font-size: 14px;
    padding: 10px 14px 10px 40px;
    width: 100%;
    transition: all 0.25s ease;
  }

  .search-light::placeholder {
    color: #9ca3af;
  }

  .search-light:focus {
    outline: none;
    border-color: rgba(227, 6, 19, 0.4);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(227, 6, 19, 0.08);
  }

  /* Duration badge */
  .duration-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
    letter-spacing: 0.02em;
  }

  /* Category tag */
  .cat-tag {
    position: absolute;
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, #e30613, #c00510);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 3px 10px;
    border-radius: 6px;
    z-index: 5;
  }

  /* Empty state */
  .empty-state {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    border: 1px dashed #e5e7eb;
    border-radius: 16px;
    background: #fafafa;
  }

  /* Section label */
  .section-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #9ca3af;
  }

  .section-label::after {
    content: '';
    display: inline-block;
    width: 40px;
    height: 2px;
    background: linear-gradient(90deg, #e30613, transparent);
    border-radius: 2px;
  }

  /* Scrollbar hide for pills */
  .scrollbar-none::-webkit-scrollbar {
    display: none;
  }

  .scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  /* Video meta line */
  .video-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    font-size: 12px;
    color: #9ca3af;
    margin-top: 8px;
  }

  .video-meta .dot {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: #d1d5db;
  }
</style>

<!-- Hero Section (Video Player & Header) -->
<section class="pt-8 lg:pt-28 pb-20 bg-white relative overflow-hidden">
  <!-- Strong Red Gradient Orbs -->
  <div class="absolute -top-10 left-0 md:left-1/4 w-[500px] h-[500px] bg-primary/20 rounded-full blur-[80px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute top-40 right-0 md:right-1/6 w-[600px] h-[600px] bg-red-500/15 rounded-full blur-[100px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute -bottom-20 left-1/3 w-[400px] h-[400px] bg-rose-500/10 rounded-full blur-[60px] pointer-events-none mix-blend-multiply"></div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <!-- Breadcrumb Component (Integrated with SEO & Structured Data) -->
    <x-seo-breadcrumbs :entity="$page" class="text-zinc-400 mb-10 text-left" />

    <div class="mx-auto max-w-[1200px] relative">

      <!-- Centered Title -->
      <div class="overflow-hidden text-center mb-10">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold text-gray-900 leading-tight">
          Video Library
        </h1>
      </div>

      <!-- Main Player -->
      <div class="player-glow mb-6">
        <div class="relative w-full bg-black rounded-2xl overflow-hidden shadow-xl aspect-video z-10" id="player-aspect-wrap">
          <iframe id="main-player" class="w-full h-full relative z-10" src="about:blank" title="Video Player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>
        </div>
      </div>

      <!-- Video Details -->
      <div id="details-container-col" class="mb-4">
        <h2 id="main-title" class="text-xl md:text-2xl font-bold text-gray-900 leading-snug">
          Transformasi Digital Enterprise: Roadmap Menuju Cloud-Native Architecture
        </h2>
        <div id="main-meta-line" class="video-meta mt-3">
          <span id="main-category-badge" class="inline-block px-2.5 py-0.5 bg-red-50 border border-red-100 rounded-full text-[11px] font-bold text-primary uppercase tracking-wider">
            Webinar
          </span>
          <span class="dot"></span>
          <span id="main-date" class="text-gray-400 text-xs">May 15, 2025</span>
          <span class="dot"></span>
          <span id="main-author" class="text-gray-400 text-xs">CDT Engineering</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- All Videos Section -->
<section class="pb-20 bg-gray-50/60">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    <!-- Section Header + Filters -->
    <div class="pt-10 mb-8">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 mb-8">
        <span class="section-label">All Videos</span>

        <!-- Search -->
        <div class="relative w-full md:w-72">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </span>
          <input type="text" id="video-search" placeholder="Search videos..." class="search-light">
          <button id="clear-search" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 hidden transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Pills -->
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pt-0 pl-3 pr-3 pb-4">
        <button class="cat-pill active" data-category="All">All</button>
        <button class="cat-pill" data-category="Webinar">Webinar</button>
        <button class="cat-pill" data-category="Security">Security</button>
        <button class="cat-pill" data-category="Engineering">Engineering</button>
        <button class="cat-pill" data-category="Events">Events</button>
        <button class="cat-pill" data-category="Products">Products</button>
      </div>
    </div>

    <!-- Grid -->
    <div id="playlist-items" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Rendered Dynamically via JS -->
    </div>
  </div>
</section>

<!-- Dynamic logic script -->
<script>
  // The Videos Dataset
  const VIDEO_DATA = [
    {
      id: "dQw4w9WgXcQ",
      title: "Transformasi Digital Enterprise: Roadmap Menuju Cloud-Native Architecture",
      description: "Pelajari bagaimana perusahaan terkemuka di Indonesia melakukan transformasi digital end-to-end dengan pendekatan cloud-native yang terukur dan aman.",
      category: "Webinar",
      date: "May 15, 2025",
      duration: "12:00",
      seconds: 720,
      author: "CDT Engineering",
      company: "Central Data Technology",
      avatar: "CDT"
    },
    {
      id: "ScMzIvxBSi4",
      title: "Cybersecurity Best Practices untuk Enterprise di Era AI",
      description: "Bagaimana mengamankan infrastruktur TI perusahaan Anda dari serangan siber modern berbasis AI? Temukan praktik terbaik dan solusi mitigasi dari tim ahli kami.",
      category: "Security",
      date: "Apr 20, 2025",
      duration: "8:24",
      seconds: 504,
      author: "CDT Security",
      company: "Central Data Technology",
      avatar: "SEC"
    },
    {
      id: "9bZkp7q19f0",
      title: "Setup Monitoring Infrastructure dengan Dynatrace",
      description: "Panduan lengkap langkah demi langkah melakukan monitoring performa sistem secara real-time dan otomatis menggunakan platform Dynatrace APM.",
      category: "Engineering",
      date: "Apr 10, 2025",
      duration: "15:30",
      seconds: 930,
      author: "CDT Engineering",
      company: "Central Data Technology",
      avatar: "CDT"
    },
    {
      id: "jNQXAC9IVRw",
      title: "Highlights: CDT Annual IT Infrastructure Summit 2025",
      description: "Kumpulan momen terbaik, diskusi panel, dan keynote session dari acara IT Infrastructure Summit terbesar tahun ini yang diselenggarakan oleh CDT.",
      category: "Events",
      date: "Mar 28, 2025",
      duration: "5:12",
      seconds: 312,
      author: "CDT Events",
      company: "Central Data Technology",
      avatar: "EVE"
    },
    {
      id: "LXb3EKWsInQ",
      title: "Demo: Akamai Cloud Computing untuk Workload Enterprise",
      description: "Lihat langsung bagaimana Akamai Cloud Computing (sebelumnya Linode) menjalankan beban kerja komputasi skala besar dengan performa tinggi dan efisiensi biaya.",
      category: "Products",
      date: "Mar 15, 2025",
      duration: "22:15",
      seconds: 1335,
      author: "CDT Products",
      company: "Central Data Technology",
      avatar: "PRO"
    },
    {
      id: "aircAruvnKk",
      title: "Data Lakehouse vs Data Warehouse: Mana yang Tepat?",
      description: "Analisis mendalam perbedaan arsitektur data modern. Pelajari kapan harus menggunakan data lakehouse, data warehouse, atau kombinasi keduanya untuk kebutuhan analytics bisnis.",
      category: "Engineering",
      date: "Feb 28, 2025",
      duration: "18:45",
      seconds: 1125,
      author: "CDT Engineering",
      company: "Central Data Technology",
      avatar: "CDT"
    },
    {
      id: "YQHsXMglC9A",
      title: "Getting Started: Zscaler Zero Trust Security",
      description: "Pelajari konsep dasar arsitektur keamanan Zero Trust dan bagaimana mengimplementasikan solusi Zscaler untuk mengamankan koneksi pengguna ke aplikasi internal perusahaan.",
      category: "Security",
      date: "Feb 10, 2025",
      duration: "10:30",
      seconds: 630,
      author: "CDT Security",
      company: "Central Data Technology",
      avatar: "SEC"
    },
    {
      id: "aqz-KE-bpKQ",
      title: "NetGain Systems: Network Performance Monitoring",
      description: "Bagaimana mengawasi seluruh perangkat jaringan di berbagai kantor cabang secara terpusat? Tonton demo singkat penggunaan NetGain untuk monitoring performa jaringan.",
      category: "Products",
      date: "Jan 22, 2025",
      duration: "7:20",
      seconds: 440,
      author: "CDT Products",
      company: "Central Data Technology",
      avatar: "PRO"
    }
  ];

  // App States
  let currentVideoId = "dQw4w9WgXcQ";
  let activeCategory = "All";
  let searchFilter = "";
  let ytPlayer = null;

  function buildEmbedUrl(videoId, autoplay) {
    const params = new URLSearchParams({
      enablejsapi: "1",
      rel: "0",
      origin: window.location.origin,
      widget_referrer: window.location.origin
    });
    if (autoplay) params.set("autoplay", "1");
    return "https://www.youtube-nocookie.com/embed/" + videoId + "?" + params.toString();
  }

  document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const vParam = urlParams.get('v');
    if (vParam && VIDEO_DATA.some(v => v.id === vParam)) {
      currentVideoId = vParam;
    }

    const player = document.getElementById("main-player");
    if (player) player.src = buildEmbedUrl(currentVideoId, false);

    renderPlaylist();
    updateMainVideoDetails(currentVideoId);
    loadYouTubeAPI();
    setupEventListeners();
  });

  function setupEventListeners() {
    const searchInput = document.getElementById("video-search");
    const clearSearchBtn = document.getElementById("clear-search");

    if (searchInput) {
      searchInput.addEventListener("input", function (e) {
        searchFilter = e.target.value.toLowerCase().trim();
        if (clearSearchBtn) clearSearchBtn.classList.toggle("hidden", searchFilter.length === 0);
        renderPlaylist();
      });
    }

    if (clearSearchBtn) {
      clearSearchBtn.addEventListener("click", function () {
        if (searchInput) searchInput.value = "";
        searchFilter = "";
        clearSearchBtn.classList.add("hidden");
        renderPlaylist();
      });
    }

    const pills = document.querySelectorAll(".cat-pill");
    pills.forEach(pill => {
      pill.addEventListener("click", function () {
        pills.forEach(p => p.classList.remove("active"));
        this.classList.add("active");
        activeCategory = this.dataset.category;
        renderPlaylist();
      });
    });
  }

  function loadYouTubeAPI() {
    if (!window.YT) {
      const tag = document.createElement("script");
      tag.src = "https://www.youtube.com/iframe_api";
      const firstScriptTag = document.getElementsByTagName("script")[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    } else {
      onYouTubeIframeAPIReady();
    }
  }

  window.onYouTubeIframeAPIReady = function () {
    ytPlayer = new YT.Player("main-player", {
      events: { "onStateChange": onPlayerStateChange }
    });
  };

  function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.ENDED) {
      // Auto-play next if needed
    }
  }

  function playVideo(id) {
    currentVideoId = id;

    const newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?v=' + id;
    window.history.pushState({ path: newurl }, '', newurl);

    const playerWrapper = document.getElementById("player-aspect-wrap");
    const headerOffset = 100;
    let scrollTarget = 0;
    if (playerWrapper) {
      const rect = playerWrapper.getBoundingClientRect();
      scrollTarget = rect.top + window.pageYOffset - headerOffset;
      if (scrollTarget < 0) scrollTarget = 0;
    }

    const player = document.getElementById("main-player");
    if (player) {
      player.style.pointerEvents = "none";
      player.setAttribute("tabindex", "-1");
      player.src = buildEmbedUrl(id, true);
    }

    updateMainVideoDetails(id);
    renderPlaylist();

    document.activeElement?.blur();

    setTimeout(function () {
      window.scrollTo({ top: scrollTarget, behavior: 'smooth' });

      setTimeout(function () {
        if (player) {
          player.style.pointerEvents = "";
          player.removeAttribute("tabindex");
        }
      }, 800);
    }, 50);
  }

  function updateMainVideoDetails(id) {
    const video = VIDEO_DATA.find(v => v.id === id);
    if (!video) return;

    const titleEl = document.getElementById("main-title");
    const catEl = document.getElementById("main-category-badge");
    const dateEl = document.getElementById("main-date");
    const authorEl = document.getElementById("main-author");

    if (titleEl) titleEl.textContent = video.title;
    if (catEl) catEl.textContent = video.category;
    if (dateEl) dateEl.textContent = video.date;
    if (authorEl) authorEl.textContent = video.author;
  }

  function generateCardHTML(video, isActive) {
    const activeClass = isActive ? 'is-active' : '';
    const nowPlaying = isActive
      ? `<span class="now-playing-badge"><span class="eq-dot"></span><span class="eq-dot"></span><span class="eq-dot"></span>Now Playing</span>`
      : '';
    const thumbUrl = `https://img.youtube.com/vi/${video.id}/hqdefault.jpg`;

    return `
    <a href="#" class="video-card ${activeClass} video-item flex flex-col" data-video="${video.id}">
      <div class="card-thumb relative w-full aspect-video bg-zinc-900">
        <img src="${thumbUrl}" class="w-full h-full object-cover" alt="${video.title}" loading="lazy">
        <div class="cat-tag">${video.category}</div>
        <div class="duration-badge">${video.duration}</div>
        <div class="play-overlay">
          <div class="play-btn-circle">
            <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
          </div>
        </div>
      </div>
      <div class="p-4 flex flex-col flex-1">
        ${nowPlaying}
        <h4 class="text-[14px] font-semibold text-gray-900 leading-snug line-clamp-2 ${isActive ? 'mt-2' : ''}">${video.title}</h4>
        <div class="video-meta mt-auto pt-2">
          <span>${video.author}</span>
          <span class="dot"></span>
          <span>${video.date}</span>
        </div>
      </div>
    </a>
  `;
  }

  function renderPlaylist() {
    const allContainer = document.getElementById("playlist-items");
    if (!allContainer) return;

    let filtered = VIDEO_DATA;
    if (activeCategory !== "All") {
      filtered = filtered.filter(v => v.category === activeCategory);
    }
    if (searchFilter.length > 0) {
      filtered = filtered.filter(v =>
        v.title.toLowerCase().includes(searchFilter) ||
        v.description.toLowerCase().includes(searchFilter) ||
        v.author.toLowerCase().includes(searchFilter)
      );
    }

    let allHtml = "";
    filtered.forEach(video => {
      allHtml += generateCardHTML(video, video.id === currentVideoId);
    });

    if (filtered.length === 0) {
      allHtml = `
      <div class="empty-state">
        <svg class="w-14 h-14 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
        <h4 class="text-gray-600 font-semibold mb-1">No videos found</h4>
        <p class="text-sm text-gray-400">Try adjusting your filters or search terms.</p>
      </div>
    `;
    }

    allContainer.innerHTML = allHtml;

    const items = document.querySelectorAll(".video-item");
    items.forEach(item => {
      const newItem = item.cloneNode(true);
      item.parentNode.replaceChild(newItem, item);
      newItem.addEventListener("click", function (e) {
        e.preventDefault();
        playVideo(this.dataset.video);
      });
    });
  }
</script>

@endsection
