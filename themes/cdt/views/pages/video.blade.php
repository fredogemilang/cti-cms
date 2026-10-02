@extends('cdt::layouts.app')

@php
    $currentLocale = app()->getLocale();
    $dbVideos = class_exists(\Plugins\Youtube\Models\YoutubeVideo::class)
        ? \Plugins\Youtube\Models\YoutubeVideo::where('is_visible', true)->orderBy('published_at', 'desc')->get()
        : collect();

    $playlists = class_exists(\Plugins\Youtube\Models\YoutubePlaylist::class)
        ? \Plugins\Youtube\Models\YoutubePlaylist::where('is_visible', true)->orderBy('sort_order')->get()
        : collect();

    // Map playlist relationships
    $playlistVideoMap = [];
    if (class_exists(\Plugins\Youtube\Models\YoutubePlaylist::class)) {
        $pvRows = \Illuminate\Support\Facades\DB::table('youtube_playlist_videos')->get();
        foreach ($pvRows as $row) {
            $playlistVideoMap[$row->video_id][] = (string) $row->playlist_id;
        }
    }

    $formattedVideos = [];
    $defaultVideoId = null;
    foreach ($dbVideos as $v) {
        $plIds = $playlistVideoMap[$v->id] ?? [];
        $isFeatured = (bool) $v->is_featured;
        if ($isFeatured && ! $defaultVideoId) {
            $defaultVideoId = $v->youtube_id;
        }
        $formattedVideos[] = [
            'id' => $v->youtube_id,
            'title' => $v->title,
            'description' => $v->description ?: '',
            'category' => $v->category ?: 'Webinar',
            'playlists' => $plIds,
            'date' => $v->published_at ? $v->published_at->format('M d, Y') : date('M d, Y'),
            'duration' => $v->duration ?: '',
            'author' => $v->channel_title ?: 'Central Data Technology',
            'thumbnail' => $v->getBestThumbnail(),
            'is_featured' => $isFeatured,
        ];
    }
    if (! $defaultVideoId && ! empty($formattedVideos)) {
        $defaultVideoId = $formattedVideos[0]['id'];
    }
@endphp

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

  /* Featured tag */
  .featured-tag {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 3px 10px;
    border-radius: 6px;
    z-index: 5;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
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
<section class="pt-6 lg:pt-8 pb-20 bg-white relative overflow-hidden">
  <!-- Strong Red Gradient Orbs -->
  <div class="absolute -top-10 left-0 md:left-1/4 w-[500px] h-[500px] bg-primary/20 rounded-full blur-[80px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute top-40 right-0 md:right-1/6 w-[600px] h-[600px] bg-red-500/15 rounded-full blur-[100px] pointer-events-none mix-blend-multiply"></div>
  <div class="absolute -bottom-20 left-1/3 w-[400px] h-[400px] bg-rose-500/10 rounded-full blur-[60px] pointer-events-none mix-blend-multiply"></div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <!-- Breadcrumb Component -->
    <x-seo-breadcrumbs :entity="$page" class="text-zinc-400 mb-6 text-left" data-gsap="fade-in" />

    <div class="mx-auto max-w-[1200px] relative">

      <!-- Centered Title -->
      <div class="overflow-hidden text-center mb-10">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold text-gray-900 leading-tight" data-gsap="fade-up">
          {{ isset($page) && $page ? ($page->getTranslation('title', $currentLocale) ?: $page->title) : t('video.title', 'Video Library') }}
        </h1>
      </div>

      <!-- Main Player -->
      <div class="player-glow mb-6" data-gsap="fade-up" data-gsap-delay="0.1">
        <div class="relative w-full bg-black rounded-2xl overflow-hidden shadow-xl aspect-video z-10" id="player-aspect-wrap">
          <iframe id="main-player" class="w-full h-full relative z-10" src="about:blank" title="Video Player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>
        </div>
      </div>

      <!-- Video Details -->
      <div id="details-container-col" class="mb-4" data-gsap="fade-up" data-gsap-delay="0.2">
        <h2 id="main-title" class="text-xl md:text-2xl font-bold text-gray-900 leading-snug">
          Transformasi Digital Enterprise
        </h2>
        <div id="main-meta-line" class="video-meta mt-3">
          <span id="main-category-badge" class="inline-block px-2.5 py-0.5 bg-red-50 border border-red-100 rounded-full text-[11px] font-bold text-primary uppercase tracking-wider">
            Video
          </span>
          <span class="dot"></span>
          <span id="main-date" class="text-gray-400 text-xs">Recently Added</span>
          <span class="dot"></span>
          <span id="main-author" class="text-gray-400 text-xs">CDT</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- All Videos Section -->
<section class="pb-20 bg-gray-50/60" data-gsap="fade-up">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    <!-- Section Header + Filters -->
    <div class="pt-10 mb-8">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 mb-8">
        <span class="section-label">{{ t('video.all_videos', 'All Videos') }}</span>

        <!-- Search -->
        <div class="relative w-full md:w-72">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </span>
          <input type="text" id="video-search" placeholder="{{ t('video.search_placeholder', 'Search videos...') }}" class="search-light">
          <button id="clear-search" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 hidden transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Pills (Dynamic from YouTube Playlists) -->
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pt-0 pl-3 pr-3 pb-4">
        <button class="cat-pill active" data-category="All">{{ t('video.all_videos', 'All Videos') }}</button>
        @foreach($playlists as $pl)
          <button class="cat-pill" data-category="{{ $pl->id }}">{{ $pl->title }}</button>
        @endforeach
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
  // Initial DB Video Dataset
  const DB_VIDEOS = @json($formattedVideos);

  const FALLBACK_VIDEOS = [
    {
      id: "dQw4w9WgXcQ",
      title: "Transformasi Digital Enterprise: Roadmap Menuju Cloud-Native Architecture",
      description: "Pelajari bagaimana perusahaan terkemuka di Indonesia melakukan transformasi digital end-to-end.",
      category: "Webinar",
      playlists: [],
      date: "May 15, 2025",
      duration: "12:00",
      author: "CDT Engineering",
      thumbnail: "https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg"
    }
  ];

  const DEFAULT_VIDEO_ID = @json($defaultVideoId);
  const VIDEO_DATA = DB_VIDEOS.length > 0 ? DB_VIDEOS : FALLBACK_VIDEOS;
  const STRINGS = {
    featuredVideo: @json(t('video.featured_video', 'Featured Video')),
    nowPlaying: @json(t('video.now_playing', 'Now Playing')),
    featured: @json(t('video.featured', 'Featured')),
    noVideosFound: @json(t('video.no_videos_found', 'No videos found')),
    noVideosDesc: @json(t('video.no_videos_desc', 'Try adjusting your filters or search terms.'))
  };

  // App States
  let currentVideoId = DEFAULT_VIDEO_ID || (VIDEO_DATA.length > 0 ? VIDEO_DATA[0].id : '');
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
      pill.addEventListener("click", function (e) {
        e.preventDefault();
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

  function playVideo(id, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    currentVideoId = id;

    // Update URL query string using replaceState to prevent browser navigation / new tab jumps
    const newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?v=' + id;
    window.history.replaceState({ path: newurl }, '', newurl);

    const playerWrapper = document.getElementById("player-aspect-wrap");
    const headerOffset = 110;
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
      }, 600);
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
    if (catEl) {
      if (video.is_featured) {
        catEl.className = "inline-block px-2.5 py-0.5 bg-amber-50 border border-amber-200 rounded-full text-[11px] font-bold text-amber-700 uppercase tracking-wider";
        catEl.textContent = "★ " + STRINGS.featuredVideo;
      } else {
        catEl.className = "inline-block px-2.5 py-0.5 bg-red-50 border border-red-100 rounded-full text-[11px] font-bold text-primary uppercase tracking-wider";
        catEl.textContent = video.category || "Video";
      }
    }
    if (dateEl) dateEl.textContent = video.date;
    if (authorEl) authorEl.textContent = video.author;
  }

  function generateCardHTML(video, isActive) {
    const activeClass = isActive ? 'is-active' : '';
    const nowPlaying = isActive
      ? `<span class="now-playing-badge"><span class="eq-dot"></span><span class="eq-dot"></span><span class="eq-dot"></span>${STRINGS.nowPlaying}</span>`
      : '';
    const thumbUrl = video.thumbnail || `https://img.youtube.com/vi/${video.id}/hqdefault.jpg`;
    const featuredTag = video.is_featured ? `<div class="featured-tag">★ ${STRINGS.featured}</div>` : '';

    return `
    <div role="button" tabindex="0" class="video-card ${activeClass} video-item flex flex-col cursor-pointer select-none" data-video="${video.id}">
      <div class="card-thumb relative w-full aspect-video bg-zinc-900">
        <img src="${thumbUrl}" class="w-full h-full object-cover" alt="${video.title}" loading="lazy">
        <div class="cat-tag">${video.category}</div>
        ${featuredTag}
        ${video.duration ? `<div class="duration-badge">${video.duration}</div>` : ''}
        <div class="play-overlay">
          <div class="play-btn-circle">
            <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
          </div>
        </div>
      </div>
      <div class="p-4 flex flex-col flex-1">
        ${nowPlaying}
        <h3 class="text-[14px] font-semibold text-gray-900 leading-snug line-clamp-2 ${isActive ? 'mt-2' : ''}">${video.title}</h3>
        <div class="video-meta mt-auto pt-2">
          <span>${video.author}</span>
          <span class="dot"></span>
          <span>${video.date}</span>
        </div>
      </div>
    </div>
  `;
  }

  function renderPlaylist() {
    const allContainer = document.getElementById("playlist-items");
    if (!allContainer) return;

    let filtered = VIDEO_DATA;
    if (activeCategory !== "All") {
      filtered = filtered.filter(v => 
        (v.playlists && v.playlists.includes(activeCategory)) || 
        v.category === activeCategory
      );
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
        <p class="text-gray-600 font-semibold mb-1">${STRINGS.noVideosFound}</p>
        <p class="text-sm text-gray-400">${STRINGS.noVideosDesc}</p>
      </div>
    `;
    }

    allContainer.innerHTML = allHtml;

    const items = document.querySelectorAll(".video-item");
    items.forEach(item => {
      item.addEventListener("click", function (e) {
        playVideo(this.dataset.video, e);
      });
    });
  }
</script>

@endsection
