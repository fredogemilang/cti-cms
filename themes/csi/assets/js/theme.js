import Alpine from 'alpinejs';
import Lenis from 'lenis';
import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

window.Alpine = Alpine;
Alpine.start();

gsap.registerPlugin(ScrollTrigger);

function onReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  } else {
    fn();
  }
}

onReady(() => {
  const csiData = window.__CSI_DATA__ || {};
  const wheelEase = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));
  const anchorGlideEase = (t) => (t < 0.5 ? 8 * t * t * t * t : 1 - Math.pow(-2 * t + 2, 4) / 2);

  // =========================================================================
  // 1. LENIS SMOOTH INERTIAL SCROLLING ENGINE & ANCHOR GLIDE
  // =========================================================================
  const lenis = new Lenis({
    duration: 1.2,
    easing: wheelEase,
    smoothWheel: true,
    touchMultiplier: 1.4,
    respectReducedMotion: false,
  });

  lenis.on('scroll', ScrollTrigger.update);

  gsap.ticker.add((time) => {
    lenis.raf(time * 1000);
  });

  gsap.ticker.lagSmoothing(0);

  function smoothScrollToTarget(targetEl) {
    if (!targetEl) return;
    if (typeof closeAllSheets === 'function') closeAllSheets();
    lenis.start();
    lenis.resize();
    lenis.scrollTo(targetEl, {
      offset: -76,
      duration: 1.45,
      easing: anchorGlideEase,
      immediate: false,
      force: true,
    });
  }

  // Global delegated smooth scroll for same-page hash anchors (e.g. #contact, /#solutions, /id/#alliances)
  document.addEventListener('click', (e) => {
    const anchor = e.target?.closest?.('a[href*="#"]');
    if (!anchor) return;

    const rawHref = anchor.getAttribute('href') || '';
    if (!rawHref || rawHref === '#') return;

    let parsedUrl;
    try {
      parsedUrl = new URL(anchor.href, window.location.href);
    } catch {
      return;
    }

    const sameOrigin = parsedUrl.origin === window.location.origin;
    const samePath = parsedUrl.pathname.replace(/\/+$/, '') === window.location.pathname.replace(/\/+$/, '');
    const hashId = parsedUrl.hash;

    if (!sameOrigin || !samePath || !hashId || hashId.length <= 1) return;

    let targetEl = null;
    try {
      targetEl = document.getElementById(decodeURIComponent(hashId.slice(1))) || document.querySelector(hashId);
    } catch {
      return;
    }

    if (targetEl) {
      e.preventDefault();
      smoothScrollToTarget(targetEl);
    }
  });

  // Smoothly glide to URL hash if landed on page from another route (e.g. /about-us -> /#solutions)
  if (window.location.hash && window.location.hash.length > 1) {
    const initialTarget = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
    if (initialTarget) {
      requestAnimationFrame(() => {
        setTimeout(() => smoothScrollToTarget(initialTarget), 120);
      });
    }
  }

  // =========================================================================
  // 2. SCROLL-DOWN HEADER GLASSMORPHISM & COMPACT HEIGHT
  // =========================================================================
  const mainHeader = document.getElementById('main-header');
  const headerContainer = document.getElementById('header-container');
  const headerLogo = document.getElementById('header-logo');

  function handleHeaderScroll() {
    if (!mainHeader || !headerContainer || !headerLogo) return;
    if (window.scrollY > 20) {
      mainHeader.classList.add('bg-white/85', 'backdrop-blur-md', 'shadow-sm', 'border-slate-200/60');
      mainHeader.classList.remove('bg-white', 'border-[#F0F5FF]');
      headerContainer.classList.add('h-[64px]');
      headerContainer.classList.remove('h-[82px]');
      headerLogo.classList.add('h-8');
      headerLogo.classList.remove('h-10');
    } else {
      mainHeader.classList.remove('bg-white/85', 'backdrop-blur-md', 'shadow-sm', 'border-slate-200/60');
      mainHeader.classList.add('bg-white', 'border-[#F0F5FF]');
      headerContainer.classList.remove('h-[64px]');
      headerContainer.classList.add('h-[82px]');
      headerLogo.classList.remove('h-8');
      headerLogo.classList.add('h-10');
    }
  }

  window.addEventListener('scroll', handleHeaderScroll, { passive: true });
  handleHeaderScroll();

  // =========================================================================
  // 3. SEARCH MODAL
  // =========================================================================
  const searchTrigger = document.getElementById('search-modal-trigger');
  const searchTriggerMob = document.getElementById('search-modal-trigger-mob');
  const searchModal = document.getElementById('search-modal');
  const searchCard = document.getElementById('search-modal-card');
  const searchClose = document.getElementById('search-modal-close');
  const searchInput = document.getElementById('search-modal-input');
  const searchSubmit = document.getElementById('search-modal-submit');

  function openSearchModal() {
    if (!searchModal || !searchCard) return;
    lenis.stop();
    searchModal.classList.remove('hidden');
    searchModal.offsetHeight;
    searchModal.classList.remove('opacity-0');
    searchCard.classList.remove('scale-95');
    searchCard.classList.add('scale-100');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => {
      if (searchInput) searchInput.focus();
    }, 50);
  }

  function closeSearchModal() {
    if (!searchModal || !searchCard) return;
    searchModal.classList.add('opacity-0');
    searchCard.classList.add('scale-95');
    searchCard.classList.remove('scale-100');
    document.body.classList.remove('overflow-hidden');
    lenis.start();
    setTimeout(() => {
      searchModal.classList.add('hidden');
    }, 200);
  }

  function submitSearch() {
    if (!searchInput) return;
    const q = searchInput.value.trim();
    if (!q) {
      searchInput.focus();
      return;
    }
    const baseUrl = csiData.searchUrl || '/search';
    window.location.href = `${baseUrl}?q=${encodeURIComponent(q)}`;
  }

  if (searchTrigger) searchTrigger.addEventListener('click', openSearchModal);
  if (searchTriggerMob) searchTriggerMob.addEventListener('click', openSearchModal);
  if (searchClose) searchClose.addEventListener('click', closeSearchModal);

  if (searchModal) {
    searchModal.addEventListener('click', (e) => {
      if (e.target === searchModal) closeSearchModal();
    });
  }

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && searchModal && !searchModal.classList.contains('hidden')) {
      closeSearchModal();
    }
    if (
      e.key === '/' &&
      searchModal &&
      searchModal.classList.contains('hidden') &&
      document.activeElement?.tagName !== 'INPUT' &&
      document.activeElement?.tagName !== 'TEXTAREA' &&
      document.activeElement?.tagName !== 'SELECT'
    ) {
      e.preventDefault();
      openSearchModal();
    }
  });

  if (searchSubmit && searchInput) {
    searchSubmit.addEventListener('click', submitSearch);
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        submitSearch();
      }
    });
  }

  // =========================================================================
  // 4. MOUSE SPOTLIGHT TRACKING ON SOLUTION CARDS
  // =========================================================================
  document.querySelectorAll('.solution-card').forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      card.style.setProperty('--mouse-x', `${x}px`);
      card.style.setProperty('--mouse-y', `${y}px`);
    });
  });

  // =========================================================================
  // 5. REAL INSIGHTS CASE STUDIES TAB FILTER & SLIDER
  // =========================================================================
  const caseStudiesData = csiData.caseStudies || {};
  const categoryKeys = Object.keys(caseStudiesData);
  let currentInsightCategory = categoryKeys[0] || 'education';
  let currentSlideIndex = 0;

  function getCurrentCaseList(category) {
    return caseStudiesData[category] || caseStudiesData[categoryKeys[0]] || [];
  }

  const caseTitle = document.getElementById('case-story-title');
  const caseQuote = document.getElementById('case-story-quote');
  const caseAuthor = document.getElementById('case-story-author');
  const caseRole = document.getElementById('case-story-role');
  const caseLogoImg = document.getElementById('case-story-logo-img');
  const caseImg = document.getElementById('case-story-image');
  const caseInner = document.getElementById('case-card-inner');
  const casePrevBtn = document.getElementById('case-prev-btn');
  const caseNextBtn = document.getElementById('case-next-btn');
  const caseMobileNav = document.getElementById('case-mobile-nav');
  const casePaginationContainer = document.getElementById('case-pagination-container');

  function updatePaginationControls(list, activeIdx) {
    const isMultiSlide = list.length > 1;

    if (casePrevBtn) casePrevBtn.style.display = isMultiSlide ? '' : 'none';
    if (caseNextBtn) caseNextBtn.style.display = isMultiSlide ? '' : 'none';
    if (caseMobileNav) caseMobileNav.style.display = isMultiSlide ? '' : 'none';

    if (casePaginationContainer) {
      if (!isMultiSlide) {
        casePaginationContainer.style.display = 'none';
        casePaginationContainer.innerHTML = '';
      } else {
        casePaginationContainer.style.display = 'flex';
        casePaginationContainer.innerHTML = '';
        list.forEach((_, idx) => {
          const dot = document.createElement('button');
          dot.type = 'button';
          dot.setAttribute('aria-label', `Slide ${idx + 1}`);
          dot.className = idx === activeIdx
            ? 'case-pagination-dot w-6 h-2 rounded-full bg-[#ED7F22] transition-all cursor-pointer'
            : 'case-pagination-dot w-2 h-2 rounded-full bg-[#E4EBFA] hover:bg-slate-300 transition-all cursor-pointer';
          dot.addEventListener('click', () => {
            currentSlideIndex = idx;
            renderCaseStory(currentInsightCategory, currentSlideIndex);
          });
          casePaginationContainer.appendChild(dot);
        });
      }
    }
  }

  function renderCaseStory(category, slideIdx) {
    const list = getCurrentCaseList(category);
    if (!list.length) return;
    const safeIdx = slideIdx % list.length;
    const story = list[safeIdx];
    if (!story) return;

    if (caseInner) {
      gsap.to(caseInner, {
        opacity: 0,
        y: 8,
        duration: 0.18,
        onComplete: () => {
          if (caseTitle) caseTitle.textContent = story.title;
          if (caseQuote) caseQuote.textContent = story.quote;
          if (caseAuthor) caseAuthor.textContent = story.author || story.client || '';
          if (caseRole) {
            if (story.role) {
              caseRole.textContent = story.role;
              caseRole.style.display = 'block';
            } else {
              caseRole.textContent = '';
              caseRole.style.display = 'none';
            }
          }
          if (caseLogoImg && story.logoImg) {
            caseLogoImg.src = story.logoImg;
            caseLogoImg.alt = story.author || 'Client Logo';
          }
          if (caseImg && story.image) {
            caseImg.src = story.image;
            caseImg.alt = story.author || 'Client Case Study';
          }

          updatePaginationControls(list, safeIdx);
          gsap.to(caseInner, { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out' });
        },
      });
    } else {
      updatePaginationControls(list, safeIdx);
    }
  }

  const insightTabs = document.querySelectorAll('.insights-tab');
  insightTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      insightTabs.forEach((t) => {
        t.className = 'insights-tab px-4 py-1.5 rounded-[6px] text-xs font-normal text-[#656F80] bg-[#E9EAEB] hover:bg-[#dfe1e3] transition-colors cursor-pointer';
      });
      tab.className = 'insights-tab px-4 py-1.5 rounded-[6px] text-xs font-bold bg-[#ED7F22] text-white shadow-sm transition-all cursor-pointer';

      currentInsightCategory = tab.getAttribute('data-insights-cat') || 'education';
      currentSlideIndex = 0;
      renderCaseStory(currentInsightCategory, currentSlideIndex);
    });
  });

  if (casePrevBtn) {
    casePrevBtn.addEventListener('click', () => {
      const list = getCurrentCaseList(currentInsightCategory);
      if (list.length <= 1) return;
      currentSlideIndex = (currentSlideIndex - 1 + list.length) % list.length;
      renderCaseStory(currentInsightCategory, currentSlideIndex);
    });
  }

  if (caseNextBtn) {
    caseNextBtn.addEventListener('click', () => {
      const list = getCurrentCaseList(currentInsightCategory);
      if (list.length <= 1) return;
      currentSlideIndex = (currentSlideIndex + 1) % list.length;
      renderCaseStory(currentInsightCategory, currentSlideIndex);
    });
  }

  if (categoryKeys.length) {
    updatePaginationControls(getCurrentCaseList(currentInsightCategory), 0);
  }

  // =========================================================================
  // 6. HERO VIDEO MODAL PLAYER (YouTube Embed)
  // =========================================================================
  const heroVideoCard = document.getElementById('hero-video-card');
  const heroPlayBtn = document.getElementById('hero-play-button');
  const heroVideoModal = document.getElementById('hero-video-modal');
  const heroVideoContainer = document.getElementById('hero-video-iframe-container');
  const heroVideoClose = document.getElementById('hero-video-close');
  const youtubeVideoId = csiData.youtubeVideoId || 'C3w39WEDYeg';

  function openHeroVideo() {
    if (!heroVideoModal || !heroVideoContainer) return;
    lenis.stop();
    heroVideoContainer.innerHTML = `<iframe class="w-full h-full rounded-2xl" src="https://www.youtube-nocookie.com/embed/${encodeURIComponent(youtubeVideoId)}?autoplay=1&rel=0&modestbranding=1" title="${heroVideoModal.getAttribute('aria-label') || 'Converion Video Player'}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
    heroVideoModal.classList.remove('hidden');
    heroVideoModal.offsetHeight;
    heroVideoModal.classList.remove('opacity-0');
    const dialog = document.getElementById('hero-video-dialog');
    if (dialog) {
      dialog.classList.remove('scale-95');
      dialog.classList.add('scale-100');
    }
    document.body.classList.add('overflow-hidden');
  }

  function closeHeroVideo() {
    if (!heroVideoModal || !heroVideoContainer) return;
    heroVideoModal.classList.add('opacity-0');
    const dialog = document.getElementById('hero-video-dialog');
    if (dialog) {
      dialog.classList.remove('scale-100');
      dialog.classList.add('scale-95');
    }
    document.body.classList.remove('overflow-hidden');
    lenis.start();
    setTimeout(() => {
      heroVideoModal.classList.add('hidden');
      heroVideoContainer.innerHTML = '';
    }, 250);
  }

  if (heroVideoCard) {
    heroVideoCard.addEventListener('click', openHeroVideo);
    heroVideoCard.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openHeroVideo();
      }
    });
  }
  if (heroPlayBtn) {
    heroPlayBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      openHeroVideo();
    });
  }
  if (heroVideoClose) heroVideoClose.addEventListener('click', closeHeroVideo);
  if (heroVideoModal) {
    heroVideoModal.addEventListener('click', (e) => {
      if (e.target === heroVideoModal) closeHeroVideo();
    });
  }
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && heroVideoModal && !heroVideoModal.classList.contains('hidden')) {
      closeHeroVideo();
    }
  });

  // =========================================================================
  // 7. DYNAMIC ORBIT CANVAS SCALING & 3D PLANETARY REVOLUTION ENGINE
  // =========================================================================
  const orbitWrapper = document.getElementById('alliances-orbit-wrapper');
  const orbitCanvas = document.getElementById('alliances-orbit-canvas');

  function updateOrbitScale() {
    if (!orbitWrapper || !orbitCanvas) return;
    const isMobile = window.innerWidth < 1024;
    const baseWidth = isMobile ? 592 : 1080;
    const w = orbitWrapper.clientWidth;
    if (w > 0) {
      const scale = w / baseWidth;
      orbitCanvas.style.transform = `scale(${scale})`;
      orbitCanvas.style.transformOrigin = 'top left';
    }
  }

  window.addEventListener('resize', updateOrbitScale, { passive: true });
  window.addEventListener('orientationchange', updateOrbitScale, { passive: true });
  updateOrbitScale();
  setTimeout(updateOrbitScale, 300);

  (() => {
    const capsules = Array.from(document.querySelectorAll('.alliance-partner-capsule'));
    const beads = Array.from(document.querySelectorAll('.alliance-orbit-bead'));
    const categoryProducts = csiData.allianceCategories || {};
    const categoryKeys = Object.keys(categoryProducts);
    if (capsules.length < 16 || !categoryKeys.length) return;

    let currentCategory = categoryKeys[0];
    let isSwitchingCategory = false;

    const outerCapsulesDesktop = [
      capsules[0], capsules[1], capsules[2], capsules[3],
      capsules[7], capsules[6], capsules[5], capsules[4],
    ];
    const middleCapsulesDesktop = [
      capsules[8], capsules[9], capsules[10], capsules[11],
      capsules[12], capsules[13], capsules[14], capsules[15],
    ];

    const desktopConfig = {
      outer: { cx: 540, cy: 240, rx: 460, ry: 165, period: 65 },
      middle: { cx: 540, cy: 240, rx: 265, ry: 92, period: 45 },
      inner: { cx: 540, cy: 265, rx: 140, ry: 45, period: 28 },
    };

    const beadConfigsDesktop = [
      { type: 'outer', offset: Math.PI / 8 },
      { type: 'outer', offset: (5 * Math.PI) / 8 },
      { type: 'outer', offset: (9 * Math.PI) / 8 },
      { type: 'inner', offset: Math.PI },
      { type: 'outer', offset: (13 * Math.PI) / 8 },
    ];

    const outerCapsulesMobile = [
      capsules[8], capsules[2], capsules[3], capsules[7], capsules[6],
      capsules[12], capsules[5], capsules[4], capsules[0], capsules[1],
    ];
    const innerCapsulesMobile = [
      capsules[9], capsules[10], capsules[11],
      capsules[13], capsules[14], capsules[15],
    ];

    const mobileConfig = {
      outer: { cx: 296, cy: 345, rx: 248, ry: 236, period: 65 },
      inner: { cx: 296, cy: 345, rx: 142, ry: 118, period: 42 },
    };

    const beadConfigsMobile = [
      { type: 'outer', offset: (-36 * Math.PI) / 180 },
      { type: 'outer', offset: 0 },
      { type: 'outer', offset: (108 * Math.PI) / 180 },
      { type: 'inner', offset: (150 * Math.PI) / 180 },
      { type: 'outer', offset: (-144 * Math.PI) / 180 },
    ];

    let isMobile = window.innerWidth < 1024;
    window.addEventListener('resize', () => {
      isMobile = window.innerWidth < 1024;
    }, { passive: true });

    let elapsedTime = 0;
    let isHoverPaused = false;
    let hoveredCapsule = null;
    let isOrbitActive = false;

    capsules.forEach((c) => {
      c._currentScale = 1.0;
      c.animScale = 1.0;
      c.animOpacity = 1.0;
      c._currentZ = -1;
      c.style.left = '0px';
      c.style.top = '0px';
    });

    beads.forEach((b) => {
      b._currentZ = -1;
      b.style.left = '0px';
      b.style.top = '0px';
    });

    function syncCapsules(category, instant = false) {
      const items = categoryProducts[category] || [];
      capsules.forEach((capsule, idx) => {
        if (idx < items.length) {
          const item = items[idx];
          const img = capsule.querySelector('.partner-logo-img');
          if (img) {
            img.src = item.logo;
            img.alt = item.name;
          }
          capsule.dataset.url = item.url || '';
          if (item.name) {
            capsule.setAttribute('aria-label', item.name);
            capsule.title = item.name;
          }
          capsule.style.display = 'flex';
          capsule.style.pointerEvents = 'auto';
          if (instant) {
            capsule.animOpacity = 1.0;
            capsule.animScale = 1.0;
            capsule.style.opacity = '1';
            capsule._currentScale = 1.0;
          } else {
            capsule.animOpacity = 0.0;
            capsule.animScale = 0.1;
            capsule.style.opacity = '0';
          }
        } else {
          capsule.dataset.url = '';
          capsule.removeAttribute('aria-label');
          capsule.removeAttribute('title');
          capsule.style.display = 'none';
          capsule.style.pointerEvents = 'none';
          capsule.style.opacity = '0';
          capsule.animOpacity = 0.0;
          capsule.animScale = 0.0;
        }
      });
    }

    function updateTabStyles(category) {
      document.querySelectorAll('.alliance-tab').forEach((tab) => {
        const cat = tab.getAttribute('data-category');
        if (cat === category) {
          tab.className = 'alliance-tab px-4 py-1.5 rounded-[6px] text-xs font-semibold bg-white border border-[#ED7F22] text-black shadow-sm transition-all cursor-pointer';
        } else {
          tab.className = 'alliance-tab px-4 py-1.5 rounded-[6px] text-xs font-normal text-[#656F80] bg-[#E9EAEB] hover:bg-[#DFE1E3] hover:text-black border border-transparent transition-all cursor-pointer';
        }
      });
    }

    function switchCategory(newCat) {
      if (newCat === currentCategory || isSwitchingCategory) return;
      if (!categoryProducts[newCat]) return;
      isSwitchingCategory = true;
      isHoverPaused = false;
      hoveredCapsule = null;
      isOrbitActive = true;

      updateTabStyles(newCat);

      const oldCat = currentCategory;
      const oldItems = categoryProducts[oldCat] || [];
      const activeOldCapsules = capsules.filter((_, i) => i < oldItems.length);

      gsap.killTweensOf(capsules);

      gsap.to(activeOldCapsules, {
        animScale: 0.1,
        animOpacity: 0.0,
        duration: 0.24,
        ease: 'power2.in',
        stagger: { each: 0.01, from: 'start' },
        onComplete: () => {
          currentCategory = newCat;
          syncCapsules(newCat, false);

          const newItems = categoryProducts[newCat] || [];
          const activeNewCapsules = capsules.filter((_, i) => i < newItems.length);

          activeNewCapsules.forEach((c) => {
            c.animScale = 0.1;
            c.animOpacity = 0.0;
            c.style.opacity = '0';
          });

          renderOrbitTick(0, 0);

          gsap.to(activeNewCapsules, {
            animScale: 1.0,
            animOpacity: 1.0,
            duration: 0.4,
            stagger: { each: 0.02, from: 'start' },
            ease: 'back.out(1.25)',
            onComplete: () => {
              isSwitchingCategory = false;
            },
          });
        },
      });
    }

    document.querySelectorAll('.alliance-tab').forEach((tab) => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        const cat = tab.getAttribute('data-category');
        if (cat) switchCategory(cat);
      });
    });

    document.querySelectorAll('.megamenu-product-link[data-filter-target]').forEach((link) => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href') || '';
        const cat = link.getAttribute('data-filter-target');
        if (cat && href.startsWith('#')) {
          switchCategory(cat);
          const alliancesSec = document.getElementById('alliances');
          if (alliancesSec) {
            e.preventDefault();
            smoothScrollToTarget(alliancesSec);
          }
        }
      });
    });

    document.querySelectorAll('.sheet-alliance-card[data-category]').forEach((card) => {
      card.addEventListener('click', (e) => {
        const cat = card.getAttribute('data-category');
        if (cat) {
          switchCategory(cat);
          closeAllSheets();
          const alliancesSec = document.getElementById('alliances');
          if (alliancesSec) {
            e.preventDefault();
            setTimeout(() => {
              smoothScrollToTarget(alliancesSec);
            }, 220);
          }
        }
      });
    });

    if (orbitCanvas) {
      orbitCanvas.addEventListener('mouseenter', () => {
        if (!isSwitchingCategory) isHoverPaused = true;
      });
      orbitCanvas.addEventListener('mouseleave', () => {
        isHoverPaused = false;
        hoveredCapsule = null;
      });
      orbitCanvas.addEventListener('touchstart', () => {
        if (!isSwitchingCategory) isHoverPaused = true;
      }, { passive: true });
      orbitCanvas.addEventListener('touchend', () => {
        isHoverPaused = false;
        hoveredCapsule = null;
      }, { passive: true });
    }

    capsules.forEach((capsule) => {
      capsule.addEventListener('mouseenter', () => {
        if (isSwitchingCategory) return;
        hoveredCapsule = capsule;
        isHoverPaused = true;
      });
      capsule.addEventListener('mouseleave', () => {
        if (hoveredCapsule === capsule) hoveredCapsule = null;
      });
      capsule.addEventListener('touchstart', () => {
        if (isSwitchingCategory) return;
        hoveredCapsule = capsule;
        isHoverPaused = true;
      }, { passive: true });
      capsule.addEventListener('touchend', () => {
        if (hoveredCapsule === capsule) hoveredCapsule = null;
        isHoverPaused = false;
      }, { passive: true });
      capsule.addEventListener('click', () => {
        const targetUrl = capsule.dataset.url;
        if (targetUrl && targetUrl !== '#alliances') {
          window.location.href = targetUrl;
        }
      });
      capsule.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          const targetUrl = capsule.dataset.url;
          if (targetUrl && targetUrl !== '#alliances') {
            e.preventDefault();
            window.location.href = targetUrl;
          }
        }
      });
    });

    function positionCapsule(el, cx, cy, rx, ry, theta, zHigh, zLow, baseScaleMin, scaleDepthFactor) {
      const x = cx + rx * Math.cos(theta) - 58.5;
      const y = cy + ry * Math.sin(theta) - 15;
      const depth = Math.sin(theta);

      const targetZ = depth > 0 ? zHigh : zLow;
      if (el._currentZ !== targetZ) {
        el._currentZ = targetZ;
        el.style.zIndex = targetZ;
      }

      const isHovered = hoveredCapsule === el && !isSwitchingCategory;
      const depthNorm = (depth + 1) / 2;
      const baseScale = baseScaleMin + scaleDepthFactor * depthNorm;
      const targetScale = isHovered ? 1.18 : baseScale;

      if (el._currentScale === undefined || Number.isNaN(el._currentScale)) {
        el._currentScale = targetScale;
      } else {
        el._currentScale += (targetScale - el._currentScale) * 0.18;
      }

      const animScale = el.animScale !== undefined ? el.animScale : 1.0;
      const finalScale = el._currentScale * animScale;
      const animOpacity = el.animOpacity !== undefined ? el.animOpacity : 1.0;

      el.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${finalScale.toFixed(3)})`;
      el.style.opacity = animOpacity.toFixed(3);
    }

    function renderOrbitTick(_, deltaTime) {
      if (!isOrbitActive) return;

      const deltaSec = isHoverPaused ? 0 : Math.min(Math.max((deltaTime || 16.6) / 1000, 0), 0.1);
      if (!isHoverPaused) {
        elapsedTime += deltaSec;
      }

      const activeItems = categoryProducts[currentCategory] || [];
      const activeCount = Math.min(activeItems.length, capsules.length);

      if (isMobile) {
        if (activeCount === 16) {
          outerCapsulesMobile.forEach((el, i) => {
            if (!el || el.style.display === 'none') return;
            const theta = -Math.PI / 2 + i * ((2 * Math.PI) / 10) - ((2 * Math.PI) / mobileConfig.outer.period) * elapsedTime;
            positionCapsule(el, mobileConfig.outer.cx, mobileConfig.outer.cy, mobileConfig.outer.rx, mobileConfig.outer.ry, theta, 30, 14, 0.86, 0.2);
          });
          innerCapsulesMobile.forEach((el, i) => {
            if (!el || el.style.display === 'none') return;
            const theta = -Math.PI / 3 + i * ((2 * Math.PI) / 6) + ((2 * Math.PI) / mobileConfig.inner.period) * elapsedTime;
            positionCapsule(el, mobileConfig.inner.cx, mobileConfig.inner.cy, mobileConfig.inner.rx, mobileConfig.inner.ry, theta, 28, 12, 0.88, 0.18);
          });
        } else if (activeCount > 0 && activeCount <= 3) {
          const startAngle = activeCount === 1 ? Math.PI / 4 : -Math.PI / 2;
          for (let i = 0; i < activeCount; i++) {
            const el = capsules[i];
            if (!el || el.style.display === 'none') continue;
            const theta = startAngle + i * ((2 * Math.PI) / activeCount) - ((2 * Math.PI) / mobileConfig.outer.period) * elapsedTime;
            positionCapsule(el, mobileConfig.outer.cx, mobileConfig.outer.cy, mobileConfig.outer.rx, mobileConfig.outer.ry, theta, 30, 14, 0.9, 0.2);
          }
        } else if (activeCount > 3) {
          const outerCount = activeCount <= 8 ? activeCount : Math.ceil(activeCount * 0.6);
          const innerCount = activeCount - outerCount;
          for (let i = 0; i < outerCount; i++) {
            const el = capsules[i];
            if (!el || el.style.display === 'none') continue;
            const theta = -Math.PI / 2 + i * ((2 * Math.PI) / outerCount) - ((2 * Math.PI) / mobileConfig.outer.period) * elapsedTime;
            positionCapsule(el, mobileConfig.outer.cx, mobileConfig.outer.cy, mobileConfig.outer.rx, mobileConfig.outer.ry, theta, 30, 14, 0.88, 0.2);
          }
          for (let j = 0; j < innerCount; j++) {
            const el = capsules[outerCount + j];
            if (!el || el.style.display === 'none') continue;
            const theta = -Math.PI / 3 + j * ((2 * Math.PI) / innerCount) + ((2 * Math.PI) / mobileConfig.inner.period) * elapsedTime;
            positionCapsule(el, mobileConfig.inner.cx, mobileConfig.inner.cy, mobileConfig.inner.rx, mobileConfig.inner.ry, theta, 28, 12, 0.9, 0.18);
          }
        }

        beads.forEach((bead, i) => {
          const cfg = beadConfigsMobile[i];
          if (!cfg) return;
          let x;
          let y;
          let depth;
          if (cfg.type === 'outer') {
            const theta = cfg.offset - ((2 * Math.PI) / mobileConfig.outer.period) * elapsedTime;
            x = mobileConfig.outer.cx + mobileConfig.outer.rx * Math.cos(theta) - 7;
            y = mobileConfig.outer.cy + mobileConfig.outer.ry * Math.sin(theta) - 6;
            depth = Math.sin(theta);
          } else {
            const theta = cfg.offset + ((2 * Math.PI) / mobileConfig.inner.period) * elapsedTime;
            x = mobileConfig.inner.cx + mobileConfig.inner.rx * Math.cos(theta) - 7;
            y = mobileConfig.inner.cy + mobileConfig.inner.ry * Math.sin(theta) - 6;
            depth = Math.sin(theta);
          }
          const depthNorm = (depth + 1) / 2;
          const beadScale = 0.84 + 0.26 * depthNorm;
          const targetZ = cfg.type === 'inner' ? (depth > 0 ? 22 : 18) : (depth > 0 ? 25 : 10);
          if (bead._currentZ !== targetZ) {
            bead._currentZ = targetZ;
            bead.style.zIndex = targetZ;
          }
          bead.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${beadScale.toFixed(3)})`;
        });
      } else {
        if (activeCount === 16) {
          outerCapsulesDesktop.forEach((el, i) => {
            if (!el || el.style.display === 'none') return;
            const theta = (i * Math.PI) / 4 - ((2 * Math.PI) / desktopConfig.outer.period) * elapsedTime;
            positionCapsule(el, desktopConfig.outer.cx, desktopConfig.outer.cy, desktopConfig.outer.rx, desktopConfig.outer.ry, theta, 30, 14, 0.83, 0.25);
          });
          middleCapsulesDesktop.forEach((el, i) => {
            if (!el || el.style.display === 'none') return;
            const theta = (i * Math.PI) / 4 + Math.PI / 8 + ((2 * Math.PI) / desktopConfig.middle.period) * elapsedTime;
            positionCapsule(el, desktopConfig.middle.cx, desktopConfig.middle.cy, desktopConfig.middle.rx, desktopConfig.middle.ry, theta, 28, 12, 0.84, 0.24);
          });
        } else if (activeCount > 0 && activeCount <= 3) {
          const startAngle = activeCount === 1 ? Math.PI / 4 : 0;
          for (let i = 0; i < activeCount; i++) {
            const el = capsules[i];
            if (!el || el.style.display === 'none') continue;
            const theta = startAngle + (i * 2 * Math.PI) / activeCount + ((2 * Math.PI) / desktopConfig.middle.period) * elapsedTime;
            positionCapsule(el, desktopConfig.middle.cx, desktopConfig.middle.cy, desktopConfig.middle.rx, desktopConfig.middle.ry, theta, 28, 12, 0.92, 0.2);
          }
        } else if (activeCount > 3) {
          const outerCount = activeCount <= 8 ? Math.ceil(activeCount / 2) : Math.min(8, Math.ceil(activeCount / 2));
          const middleCount = activeCount - outerCount;
          for (let i = 0; i < outerCount; i++) {
            const el = capsules[i];
            if (!el || el.style.display === 'none') continue;
            const theta = (i * 2 * Math.PI) / outerCount - ((2 * Math.PI) / desktopConfig.outer.period) * elapsedTime;
            positionCapsule(el, desktopConfig.outer.cx, desktopConfig.outer.cy, desktopConfig.outer.rx, desktopConfig.outer.ry, theta, 30, 14, 0.85, 0.22);
          }
          for (let j = 0; j < middleCount; j++) {
            const el = capsules[outerCount + j];
            if (!el || el.style.display === 'none') continue;
            const theta = (j * 2 * Math.PI) / middleCount + Math.PI / 6 + ((2 * Math.PI) / desktopConfig.middle.period) * elapsedTime;
            positionCapsule(el, desktopConfig.middle.cx, desktopConfig.middle.cy, desktopConfig.middle.rx, desktopConfig.middle.ry, theta, 28, 12, 0.88, 0.22);
          }
        }

        beads.forEach((bead, i) => {
          const cfg = beadConfigsDesktop[i];
          if (!cfg) return;
          let x;
          let y;
          let depth;
          if (cfg.type === 'outer') {
            const theta = cfg.offset - ((2 * Math.PI) / desktopConfig.outer.period) * elapsedTime;
            x = desktopConfig.outer.cx + desktopConfig.outer.rx * Math.cos(theta) - 7;
            y = desktopConfig.outer.cy + desktopConfig.outer.ry * Math.sin(theta) - 6;
            depth = Math.sin(theta);
          } else {
            const theta = cfg.offset + ((2 * Math.PI) / desktopConfig.inner.period) * elapsedTime;
            x = desktopConfig.inner.cx + desktopConfig.inner.rx * Math.cos(theta) - 7;
            y = desktopConfig.inner.cy + desktopConfig.inner.ry * Math.sin(theta) - 6;
            depth = Math.sin(theta);
          }
          const depthNorm = (depth + 1) / 2;
          const beadScale = 0.82 + 0.28 * depthNorm;
          const targetZ = cfg.type === 'inner' ? (depth > 0 ? 22 : 18) : (depth > 0 ? 25 : 10);
          if (bead._currentZ !== targetZ) {
            bead._currentZ = targetZ;
            bead.style.zIndex = targetZ;
          }
          bead.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${beadScale.toFixed(3)})`;
        });
      }
    }

    syncCapsules(currentCategory, true);
    isOrbitActive = true;
    renderOrbitTick(0, 16.6);
    gsap.ticker.add(renderOrbitTick);

    ScrollTrigger.create({
      trigger: '#alliances',
      start: 'top bottom',
      end: 'bottom top',
      onEnter: () => { isOrbitActive = true; },
      onLeave: () => { isOrbitActive = false; },
      onEnterBack: () => { isOrbitActive = true; },
      onLeaveBack: () => { isOrbitActive = false; },
    });
  })();

  // =========================================================================
  // 8. MOBILE BOTTOM DOCK & BOTTOM SHEETS CONTROLLER
  // =========================================================================
  const dockButtons = document.querySelectorAll('.dock-btn[data-sheet]');
  const bottomSheetBackdrop = document.getElementById('bottom-sheet-backdrop');
  const allSheets = document.querySelectorAll('.bottom-sheet');
  const sheetCloseButtons = document.querySelectorAll('.sheet-close-btn');
  const sheetItems = document.querySelectorAll('.sheet-item');
  const searchTriggerSheet = document.getElementById('search-trigger-sheet');

  let activeSheetName = null;

  function openSheet(name) {
    const targetSheet = document.getElementById(`sheet-${name}`);
    if (!targetSheet) return;
    lenis.stop();

    allSheets.forEach((s) => {
      s.classList.add('translate-y-full');
      s.classList.remove('translate-y-0');
    });

    dockButtons.forEach((btn) => {
      btn.classList.remove('text-[#04B1C7]', 'font-bold');
      btn.classList.add('text-slate-500');
    });

    if (bottomSheetBackdrop) {
      bottomSheetBackdrop.classList.remove('hidden');
      bottomSheetBackdrop.offsetHeight;
      bottomSheetBackdrop.classList.remove('opacity-0');
    }

    targetSheet.classList.remove('translate-y-full');
    targetSheet.classList.add('translate-y-0');

    const activeBtn = document.querySelector(`.dock-btn[data-sheet="${name}"]`);
    if (activeBtn) {
      activeBtn.classList.remove('text-slate-500');
      activeBtn.classList.add('text-[#04B1C7]', 'font-bold');
    }

    activeSheetName = name;
    document.body.classList.add('overflow-hidden');
  }

  function closeAllSheets() {
    allSheets.forEach((s) => {
      s.classList.add('translate-y-full');
      s.classList.remove('translate-y-0');
    });

    if (bottomSheetBackdrop) {
      bottomSheetBackdrop.classList.add('opacity-0');
      setTimeout(() => {
        bottomSheetBackdrop.classList.add('hidden');
      }, 300);
    }

    dockButtons.forEach((btn) => {
      btn.classList.remove('text-[#04B1C7]', 'font-bold');
      btn.classList.add('text-slate-500');
    });

    activeSheetName = null;
    document.body.classList.remove('overflow-hidden');
    lenis.start();
  }

  dockButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const sheetName = btn.getAttribute('data-sheet');
      if (activeSheetName === sheetName) {
        closeAllSheets();
      } else {
        openSheet(sheetName);
      }
    });
  });

  sheetCloseButtons.forEach((btn) => btn.addEventListener('click', closeAllSheets));
  if (bottomSheetBackdrop) bottomSheetBackdrop.addEventListener('click', closeAllSheets);
  sheetItems.forEach((item) => item.addEventListener('click', closeAllSheets));

  if (searchTriggerSheet) {
    searchTriggerSheet.addEventListener('click', () => {
      closeAllSheets();
      setTimeout(openSearchModal, 150);
    });
  }

  // =========================================================================
  // 8B. PARTNER SUCCESS STORIES 3-CARD CENTERED LOOPED CAROUSEL (SWIPER)
  // =========================================================================
  const partnerSwiperEl = document.getElementById('partner-stories-swiper');
  if (partnerSwiperEl) {
    const realCount = parseInt(partnerSwiperEl.getAttribute('data-real-count') || '0', 10);
    const prevBtn = document.getElementById('partner-stories-prev');
    const nextBtn = document.getElementById('partner-stories-next');
    const dots = Array.from(document.querySelectorAll('.partner-story-dot'));

    const updateStoryDots = (swiper) => {
      if (!realCount || !dots.length) return;
      const activeRealIdx = ((swiper.realIndex % realCount) + realCount) % realCount;
      dots.forEach((dot, idx) => {
        if (idx === activeRealIdx) {
          dot.classList.add('bg-[#ED7F22]', 'w-4');
          dot.classList.remove('bg-[#E4EBFA]', 'w-2');
        } else {
          dot.classList.remove('bg-[#ED7F22]', 'w-4');
          dot.classList.add('bg-[#E4EBFA]', 'w-2');
        }
      });
    };

    const partnerSwiper = new Swiper(partnerSwiperEl, {
      modules: [Autoplay],
      slidesPerView: 'auto',
      spaceBetween: 21,
      centeredSlides: true,
      loop: realCount > 1,
      initialSlide: realCount > 2 ? 1 : 0,
      speed: 750,
      slidesPerGroup: 1,
      breakpoints: {
        768: {
          slidesPerGroup: 2,
        },
        1024: {
          slidesPerGroup: 3,
        },
      },
      autoplay: realCount > 1 ? {
        delay: 4200,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      } : false,
      on: {
        init(sw) {
          updateStoryDots(sw);
        },
        slideChange(sw) {
          updateStoryDots(sw);
        },
      },
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', () => partnerSwiper.slidePrev());
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => partnerSwiper.slideNext());
    }
    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        const targetIdx = parseInt(dot.getAttribute('data-story-dot') || '0', 10);
        partnerSwiper.slideToLoop(targetIdx);
      });
    });
  }

  // =========================================================================
  // 9. GSAP & SCROLLTRIGGER AWWWARDS-GRADE REVERSIBLE MOTION ENGINE
  // =========================================================================
  {
    ScrollTrigger.config({
      ignoreMobileResize: true,
      limitCallbacks: true,
    });

    // Bidirectional reveal helper: plays on enter, reverses smoothly when scrolling back from bottom to top
    const revealMotion = (targets, fromVars, toVars = {}, triggerOpts = {}) => {
      const elements = gsap.utils.toArray(targets);
      if (!elements.length) return null;

      const triggerTarget = triggerOpts.trigger || elements[0];
      const scrollConfig = {
        trigger: triggerTarget,
        start: triggerOpts.start || 'top 88%',
        end: triggerOpts.end || 'bottom 12%',
        toggleActions: triggerOpts.toggleActions || 'play reverse play reverse',
        fastScrollEnd: true,
        invalidateOnRefresh: true,
      };

      return gsap.fromTo(
        elements,
        fromVars,
        {
          duration: 0.72,
          ease: 'power4.out',
          ...toVars,
          scrollTrigger: scrollConfig,
        }
      );
    };

    // Masked editorial heading reveal (clip-path curtain + GPU Y)
    const revealHeading = (target, triggerEl = null) => {
      return revealMotion(
        target,
        { y: 26, opacity: 0, clipPath: 'inset(0% 0% 100% 0%)' },
        { y: 0, opacity: 1, clipPath: 'inset(0% 0% 0% 0%)', duration: 0.78, ease: 'expo.out' },
        { trigger: triggerEl || target, start: 'top 90%', end: 'bottom 12%' }
      );
    };

    // Initialize motion after first paint frame so FCP / LCP remain unblocked
    requestAnimationFrame(() => {
      // --- PAGE SPEED SAFE ABOVE-THE-FOLD: HOME HERO (Never opacity: 0 on LCP) ---
      if (document.getElementById('hero-title')) {
        gsap.from('#hero-cta-wrapper', { y: 12, duration: 0.6, ease: 'expo.out', clearProps: 'transform' });
      }
      if (document.getElementById('hero-arc-left')) {
        gsap.from('#hero-arc-left', { x: -24, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
        gsap.to('#hero-arc-left', {
          y: 65,
          x: -16,
          scrollTrigger: { trigger: '#hero-arc-left', start: 'top top', end: 'bottom top', scrub: 1 },
        });
      }
      if (document.getElementById('hero-arc-right')) {
        gsap.from('#hero-arc-right', { x: 24, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
        gsap.to('#hero-arc-right', {
          y: -55,
          x: 16,
          scrollTrigger: { trigger: '#hero-arc-right', start: 'top top', end: 'bottom top', scrub: 1 },
        });
      }
      if (document.getElementById('hero-video-card')) {
        gsap.from('#hero-video-card', { y: 14, scale: 0.985, duration: 0.7, ease: 'expo.out', clearProps: 'transform' });
        gsap.to('#hero-video-card', {
          scrollTrigger: { trigger: '#hero-video-card', start: 'top 38%', end: 'bottom top', scrub: 1 },
          y: 44,
          scale: 0.975,
        });
      }
      if (document.getElementById('hero-halftone')) {
        gsap.to('#hero-halftone', {
          scrollTrigger: { trigger: 'main', start: 'top top', end: '900px top', scrub: 1.2 },
          y: -60,
        });
      }

      // --- PAGE SPEED SAFE ABOVE-THE-FOLD: ABOUT US HERO & INTRO (Never opacity: 0 on LCP) ---
      if (document.getElementById('about-hero')) {
        if (document.getElementById('about-arc-left')) {
          gsap.from('#about-arc-left', { x: -20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#about-arc-left', {
            y: 75,
            scrollTrigger: { trigger: '#about-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('about-arc-right')) {
          gsap.from('#about-arc-right', { x: 20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#about-arc-right', {
            y: -65,
            scrollTrigger: { trigger: '#about-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('about-hero-cta')) {
          gsap.from('#about-hero-cta', { y: 10, duration: 0.55, ease: 'expo.out', clearProps: 'transform' });
        }
        if (document.getElementById('about-intro-img')) {
          gsap.fromTo(
            '#about-intro-img',
            { scale: 1.09, yPercent: -2 },
            {
              scale: 1.07,
              yPercent: 2,
              scrollTrigger: { trigger: '#about-intro-media', start: 'top 90%', end: 'bottom 10%', scrub: 0.9 },
            }
          );
        }
      }

      // --- ABOUT US BELOW-THE-FOLD: VISION & MISSION ---
      if (document.getElementById('about-vision-mission')) {
        revealMotion(
          '#about-vision-mission',
          { y: 40, opacity: 0, scale: 0.97 },
          { y: 0, opacity: 1, scale: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#about-vision-mission', start: 'top 88%', end: 'bottom 12%' }
        );
        revealMotion(
          '#about-vision-panel',
          { x: -24, opacity: 0 },
          { x: 0, opacity: 1, duration: 0.68, delay: 0.08 },
          { trigger: '#about-vision-mission', start: 'top 86%', end: 'bottom 14%' }
        );
        revealMotion(
          '.about-mission-item',
          { y: 26, x: 18, opacity: 0 },
          { y: 0, x: 0, opacity: 1, stagger: 0.1, duration: 0.65 },
          { trigger: '#about-mission-panel', start: 'top 86%', end: 'bottom 14%' }
        );
        if (document.getElementById('about-vision-icon')) {
          gsap.to('#about-vision-icon', {
            y: -26,
            rotation: -34,
            scrollTrigger: { trigger: '#about-vision-mission', start: 'top bottom', end: 'bottom top', scrub: 1.2 },
          });
        }
        if (document.getElementById('about-mission-icon')) {
          gsap.to('#about-mission-icon', {
            y: 28,
            scrollTrigger: { trigger: '#about-vision-mission', start: 'top bottom', end: 'bottom top', scrub: 1.2 },
          });
        }
      }

      // --- ABOUT US: OUR CAPABILITIES ---
      if (document.getElementById('about-capabilities')) {
        revealHeading('#about-cap-heading', '#about-capabilities');
        revealMotion(
          '#about-cap-stage',
          { y: 46, scale: 0.96, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.8, ease: 'expo.out' },
          { trigger: '#about-cap-stage', start: 'top 88%', end: 'bottom 14%' }
        );
        gsap.utils.toArray('.about-cap-mobile-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 34, scale: 0.96, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.65, delay: i * 0.04 },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
      }

      // --- ABOUT US: AWARDS & RECOGNITION ---
      if (document.getElementById('about-awards')) {
        revealHeading('#about-awards-heading', '#about-awards');
        revealMotion(
          '#about-awards-stage',
          { y: 38, scale: 0.98, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#about-awards-stage', start: 'top 88%', end: 'bottom 14%' }
        );
        revealMotion(
          '#about-awards-controls',
          { y: 18, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.6 },
          { trigger: '#about-awards-controls', start: 'top 92%', end: 'bottom 8%' }
        );
      }

      // --- PARTNER PROGRAM: HERO & PROMO (Above-the-fold LCP safe) ---
      if (document.getElementById('partner-hero')) {
        if (document.getElementById('partner-arc-left')) {
          gsap.from('#partner-arc-left', { x: -20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#partner-arc-left', {
            y: 75,
            scrollTrigger: { trigger: '#partner-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('partner-arc-right')) {
          gsap.from('#partner-arc-right', { x: 20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#partner-arc-right', {
            y: -65,
            scrollTrigger: { trigger: '#partner-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('partner-hero-ctas')) {
          gsap.from('#partner-hero-ctas', { y: 10, duration: 0.55, ease: 'expo.out', clearProps: 'transform' });
        }
        if (document.getElementById('partner-promo-img')) {
          gsap.fromTo(
            '#partner-promo-img',
            { scale: 1.09, yPercent: -2 },
            {
              scale: 1.07,
              yPercent: 2,
              scrollTrigger: { trigger: '#partner-promo-card', start: 'top 90%', end: 'bottom 10%', scrub: 0.9 },
            }
          );
        }
      }

      // --- PARTNER PROGRAM: SUCCESS STORIES ---
      if (document.getElementById('partner-stories')) {
        revealHeading('#partner-stories-heading', '#partner-stories');
        revealMotion(
          '#partner-stories-subtitle, #partner-stories-highlight',
          { y: 24, opacity: 0 },
          { y: 0, opacity: 1, stagger: 0.08, duration: 0.66 },
          { trigger: '#partner-stories-subtitle', start: 'top 88%', end: 'bottom 14%' }
        );
        revealMotion(
          '#partner-stories-stage',
          { y: 38, scale: 0.98, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#partner-stories-stage', start: 'top 88%', end: 'bottom 14%' }
        );
        revealMotion(
          '#partner-stories-controls',
          { y: 18, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.6 },
          { trigger: '#partner-stories-controls', start: 'top 92%', end: 'bottom 8%' }
        );
      }

      // --- PARTNER PROGRAM: START GROWING WITH US ---
      if (document.getElementById('partner-grow')) {
        revealHeading('#partner-grow-heading', '#partner-grow');
        revealMotion(
          '#partner-grow-subtitle',
          { y: 22, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.65 },
          { trigger: '#partner-grow-subtitle', start: 'top 88%', end: 'bottom 14%' }
        );
        gsap.utils.toArray('.partner-grow-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 44, scale: 0.95, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.68, delay: (i % 4) * 0.055, ease: 'power4.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
        if (document.getElementById('partner-grow-halftone')) {
          gsap.to('#partner-grow-halftone', {
            scrollTrigger: { trigger: '#partner-grow', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
            y: -60,
          });
        }
        if (document.getElementById('partner-grow-loops')) {
          gsap.to('#partner-grow-loops', {
            scrollTrigger: { trigger: '#partner-grow', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
            y: 55,
            rotation: -10,
          });
        }
      }

      // --- PARTNER PROGRAM: FEATURED PARTNER PROGRAMS & BANNER ---
      if (document.getElementById('partner-programs')) {
        revealHeading('#partner-programs-heading', '#partner-programs');
        revealMotion(
          '#partner-programs-subtitle',
          { y: 22, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.65 },
          { trigger: '#partner-programs-subtitle', start: 'top 88%', end: 'bottom 14%' }
        );
        gsap.utils.toArray('.partner-program-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 44, scale: 0.95, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.7, delay: (i % 3) * 0.08, ease: 'power4.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
        revealMotion(
          '#partner-cta-banner',
          { y: 36, scale: 0.97, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#partner-cta-banner', start: 'top 88%', end: 'bottom 12%' }
        );
      }

      // --- TECHNOLOGY ALLIANCES DETAIL CPT: HERO, WHY CHOOSE, CONVERION, SOLUTIONS ---
      if (document.getElementById('alliance-hero')) {
        if (document.getElementById('alliance-arc-left')) {
          gsap.from('#alliance-arc-left', { x: -20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#alliance-arc-left', {
            y: 75,
            scrollTrigger: { trigger: '#alliance-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('alliance-arc-right')) {
          gsap.from('#alliance-arc-right', { x: 20, duration: 0.8, ease: 'expo.out', clearProps: 'transform' });
          gsap.to('#alliance-arc-right', {
            y: -65,
            scrollTrigger: { trigger: '#alliance-hero', start: 'top top', end: 'bottom top', scrub: 1.1 },
          });
        }
        if (document.getElementById('alliance-hero-img')) {
          gsap.fromTo(
            '#alliance-hero-img',
            { scale: 1.09, yPercent: -2 },
            {
              scale: 1.07,
              yPercent: 2,
              scrollTrigger: { trigger: '#alliance-hero-media', start: 'top 90%', end: 'bottom 10%', scrub: 0.9 },
            }
          );
        }
      }

      if (document.getElementById('alliance-why')) {
        revealHeading('#alliance-why-heading', '#alliance-why');
        gsap.utils.toArray('.alliance-why-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 40, scale: 0.95, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.68, delay: (i % 2) * 0.07, ease: 'power4.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
        revealMotion(
          '#alliance-features-card',
          { y: 40, scale: 0.96, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#alliance-features-card', start: 'top 88%', end: 'bottom 12%' }
        );
        if (document.getElementById('alliance-why-halftone')) {
          gsap.to('#alliance-why-halftone', {
            scrollTrigger: { trigger: '#alliance-why-and-converion', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
            y: -65,
          });
        }
        if (document.getElementById('alliance-triangle-lines')) {
          gsap.to('#alliance-triangle-lines', {
            scrollTrigger: { trigger: '#alliance-why-and-converion', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
            y: 55,
            rotation: 170,
          });
        }
      }

      if (document.getElementById('alliance-converion')) {
        revealHeading('#alliance-converion-heading', '#alliance-converion');
        gsap.utils.toArray('.alliance-step-card').forEach((card, i) => {
          revealMotion(
            card,
            { x: -32, y: 20, opacity: 0 },
            { x: 0, y: 0, opacity: 1, duration: 0.72, delay: i * 0.06, ease: 'expo.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
      }

      if (document.getElementById('alliance-solutions')) {
        revealHeading('#alliance-solutions-heading', '#alliance-solutions');
        revealMotion(
          '#alliance-solutions-tabs',
          { y: 20, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.62 },
          { trigger: '#alliance-solutions-tabs', start: 'top 90%', end: 'bottom 10%' }
        );
        revealMotion(
          '#alliance-solutions-panels',
          { y: 24, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.65, ease: 'power4.out' },
          { trigger: '#alliance-solutions-tabs', start: 'top 92%', end: 'bottom 8%' }
        );
      }

      // --- HOMEPAGE: CLIENTS MARQUEE ---
      if (document.getElementById('clients')) {
        revealHeading('#clients-heading', '#clients');
        revealMotion(
          '#clients-marquee-wrap',
          { y: 24, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.7 },
          { trigger: '#clients-marquee-wrap', start: 'top 90%', end: 'bottom 12%' }
        );
      }

      // --- HOMEPAGE: COMPREHENSIVE SOLUTIONS ---
      if (document.getElementById('solutions')) {
        revealHeading('#solutions-heading', '#solutions');
        gsap.utils.toArray('.solution-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 44, scale: 0.95, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.68, delay: (i % 3) * 0.06, ease: 'power4.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
        gsap.to('#solutions img[src*="solutions-bg-grid"]', {
          scrollTrigger: { trigger: '#solutions', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
          y: -45,
        });
      }

      // --- HOMEPAGE: TECHNOLOGY ALLIANCES ---
      if (document.getElementById('alliances')) {
        revealHeading('#alliances-heading', '#alliances');
        revealMotion(
          '#alliances #alliances-desc, #alliances #alliances-cta, #alliances-tabs',
          { y: 28, opacity: 0 },
          { y: 0, opacity: 1, stagger: 0.08, duration: 0.68 },
          { trigger: '#alliances', start: 'top 86%', end: 'bottom 14%' }
        );

        const orbitTL = gsap.timeline({
          scrollTrigger: {
            trigger: '#alliances-orbit-wrapper',
            start: 'top 86%',
            end: 'bottom 14%',
            toggleActions: 'play reverse play reverse',
            fastScrollEnd: true,
          },
        });
        orbitTL.fromTo('svg[id^="alliances-orbit-svg"]', { scale: 0.86, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.7, ease: 'expo.out' }, 0);
        orbitTL.fromTo('#alliances-core-sphere', { scale: 0.65, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.72, ease: 'back.out(1.4)' }, 0.08);
        orbitTL.fromTo(
          '.alliance-partner-capsule',
          { opacity: 0 },
          {
            opacity: (_, el) => (el.style.display === 'none' ? 0 : 1),
            duration: 0.58,
            stagger: { each: 0.025, from: 'center' },
            ease: 'power3.out',
          },
          0.16
        );

        gsap.to('img[src*="alliances-star-1"]', {
          scrollTrigger: { trigger: '#alliances', start: 'top bottom', end: 'bottom top', scrub: 1.6 },
          y: -60,
          x: 25,
        });
        gsap.to('img[src*="alliances-star-2"]', {
          scrollTrigger: { trigger: '#alliances', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
          y: 70,
          x: -30,
        });
      }

      // --- HOMEPAGE: WHY CONVERION ---
      if (document.getElementById('about')) {
        revealHeading('#why-heading', '#about');
        revealMotion(
          '#why-subtitle, #about #why-desc',
          { y: 26, opacity: 0 },
          { y: 0, opacity: 1, stagger: 0.08, duration: 0.68 },
          { trigger: '#why-subtitle', start: 'top 88%', end: 'bottom 14%' }
        );
        gsap.utils.toArray('.why-card').forEach((card, i) => {
          revealMotion(
            card,
            { y: 46, scale: 0.94, opacity: 0 },
            { y: 0, scale: 1, opacity: 1, duration: 0.7, delay: (i % 5) * 0.055, ease: 'power4.out' },
            { trigger: card, start: 'top 90%', end: 'bottom 10%' }
          );
        });
        gsap.to('#why-puzzle-left', {
          scrollTrigger: { trigger: '#about', start: 'top bottom', end: 'bottom top', scrub: 1.3 },
          y: -55,
          rotation: -6,
        });
        gsap.to('#why-puzzle-right', {
          scrollTrigger: { trigger: '#about', start: 'top bottom', end: 'bottom top', scrub: 1.3 },
          y: 65,
          rotation: 6,
        });
      }

      // --- HOMEPAGE: CUSTOMER SUCCESS STORIES ---
      if (document.getElementById('case-studies')) {
        revealHeading('#case-heading', '#case-studies');
        revealMotion(
          '#case-studies #case-subtitle, .insights-tab',
          { y: 22, opacity: 0 },
          { y: 0, opacity: 1, stagger: 0.055, duration: 0.64 },
          { trigger: '#case-studies', start: 'top 86%', end: 'bottom 16%' }
        );
        revealMotion(
          '#case-card-outer',
          { y: 42, scale: 0.96, opacity: 0 },
          { y: 0, scale: 1, opacity: 1, duration: 0.78, ease: 'expo.out' },
          { trigger: '#case-card-outer', start: 'top 88%', end: 'bottom 14%' }
        );
        gsap.to('#case-halftone', {
          scrollTrigger: { trigger: '#case-studies', start: 'top bottom', end: 'bottom top', scrub: 1.6 },
          y: -60,
        });
        gsap.to('#case-loops', {
          scrollTrigger: { trigger: '#case-studies', start: 'top bottom', end: 'bottom top', scrub: 1.6 },
          y: 55,
          rotation: -10,
        });
      }

      // --- ALLIANCES ARCHIVE: ARC PARALLAX (Above-the-Fold stays opacity: 1) ---
      if (document.getElementById('alliances-archive-hero')) {
        gsap.to('#archive-alliances-arc-left', {
          scrollTrigger: { trigger: '#alliances-archive-hero', start: 'top bottom', end: 'bottom top', scrub: 1.5 },
          y: -45,
        });
        gsap.to('#archive-alliances-arc-right', {
          scrollTrigger: { trigger: '#alliances-archive-hero', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
          y: 45,
        });
      }

      // --- SOLUTIONS SINGLE & ARCHIVE: ARC PARALLAX + SERVICES REVEAL ---
      if (document.getElementById('solution-hero')) {
        gsap.to('#solution-arc-left', {
          scrollTrigger: { trigger: '#solution-hero', start: 'top bottom', end: 'bottom top', scrub: 1.5 },
          y: -45,
        });
        gsap.to('#solution-arc-right', {
          scrollTrigger: { trigger: '#solution-hero', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
          y: 45,
        });
        if (document.getElementById('solution-services')) {
          revealHeading('#solution-services-heading', '#solution-services');
          gsap.utils.toArray('.solution-service-card').forEach((card, i) => {
            revealMotion(
              card,
              { y: 28, opacity: 0 },
              { y: 0, opacity: 1, delay: (i % 3) * 0.08, duration: 0.64, ease: 'power3.out' },
              { trigger: card, start: 'top 90%', end: 'bottom 10%' }
            );
          });
        }
      }

      if (document.getElementById('solutions-archive-hero')) {
        gsap.to('#archive-solutions-arc-left', {
          scrollTrigger: { trigger: '#solutions-archive-hero', start: 'top bottom', end: 'bottom top', scrub: 1.5 },
          y: -45,
        });
        gsap.to('#archive-solutions-arc-right', {
          scrollTrigger: { trigger: '#solutions-archive-hero', start: 'top bottom', end: 'bottom top', scrub: 1.4 },
          y: 45,
        });
      }

      // --- SHARED: CONTACT SECTION ---
      if (document.getElementById('contact')) {
        revealMotion(
          '#contact-form-column',
          { x: -28, y: 22, opacity: 0 },
          { x: 0, y: 0, opacity: 1, duration: 0.76, ease: 'expo.out' },
          { trigger: '#contact', start: 'top 88%', end: 'bottom 12%' }
        );
        revealMotion(
          '#contact-image-column',
          { x: 28, y: 22, opacity: 0 },
          { x: 0, y: 0, opacity: 1, duration: 0.76, ease: 'expo.out' },
          { trigger: '#contact', start: 'top 88%', end: 'bottom 12%' }
        );
        const contactPhoto = document.querySelector('#contact-side-photo-wrap img');
        if (contactPhoto) {
          gsap.fromTo(
            contactPhoto,
            { scale: 1.09, yPercent: -2.5 },
            {
              scale: 1.08,
              yPercent: 2.5,
              scrollTrigger: { trigger: '#contact-image-column', start: 'top 90%', end: 'bottom 10%', scrub: 0.9 },
            }
          );
        }
        gsap.to('#contact-bg-glow', {
          scrollTrigger: { trigger: '#contact', start: 'top bottom', end: 'bottom top', scrub: 1.3 },
          y: -65,
        });
      }

      // --- SHARED: FOOTER (stays visible at bottom, reverts cleanly when scrolling up past fold) ---
      gsap.fromTo(
        'footer .grid > div',
        { y: 28, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.66,
          stagger: 0.075,
          ease: 'power4.out',
          scrollTrigger: {
            trigger: 'footer',
            start: 'top 92%',
            end: 'bottom top',
            toggleActions: 'play none none reverse',
            fastScrollEnd: true,
          },
        }
      );

      // Recalculate exact trigger coordinates once webfonts settle
      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => {
          ScrollTrigger.refresh();
          lenis.resize();
        });
      }
    });
  }
});
