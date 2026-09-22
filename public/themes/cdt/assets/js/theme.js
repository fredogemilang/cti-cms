// Eager theme bundle — keep this small. It runs on every page before the user
// can interact, so it only contains what the first paint/interaction needs:
// Alpine (nav, sheets, forms) and a few native scroll helpers.
//
// Everything heavy is split into on-demand chunks (Vite dynamic import):
//   theme-motion.js  → GSAP + ScrollTrigger (+ Lenis on desktop)
//   theme-swiper.js  → Swiper core + Autoplay/Navigation/Pagination
// Before the split this file shipped ~270KB of JS eagerly and its evaluation
// was the single 500ms long task blocking mobile TTI (Lighthouse 2026-09-21).
import Alpine from 'alpinejs';
// Swiper CSS stays eager so slider markup is laid out before Swiper JS lands
// (avoids a layout shift when the lazy chunk initialises).
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// Expose & start Alpine so x-data/x-show directives in partials work.
window.Alpine = Alpine;
Alpine.start();

const isTouch = window.matchMedia('(hover: none), (pointer: coarse)').matches;
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const idle = window.requestIdleCallback || ((cb) => setTimeout(cb, 1));

// ==========================================
// LAZY CHUNK LOADERS
// ==========================================
let motionPromise = null;
function loadMotion() {
  if (!motionPromise) {
    motionPromise = import('./theme-motion.js').then((m) => {
      m.init({ smoothScroll: !isTouch, hoverEffects: !isTouch });
      return m;
    });
  }
  return motionPromise;
}

let swiperPromise = null;
function loadSwiper() {
  if (!swiperPromise) {
    swiperPromise = import('./theme-swiper.js').then((m) => m.Swiper);
  }
  return swiperPromise;
}

// Motion is only worth loading when the page has something to animate
// (or on desktop, where Lenis smooth scrolling is part of the experience).
const hasMotionTargets = !!document.querySelector(
  '[data-gsap], .expertise-card, .alliance-logo, .aws-logo, .alliance-link, #blog-sidebar'
);
if (!reducedMotion && (hasMotionTargets || !isTouch)) {
  const events = ['scroll', 'wheel', 'touchstart', 'pointerdown', 'keydown'];
  const onFirstInteraction = () => {
    events.forEach((e) => window.removeEventListener(e, onFirstInteraction));
    loadMotion();
  };
  events.forEach((e) => window.addEventListener(e, onFirstInteraction, { once: true, passive: true }));

  // Desktop: warm it up during idle time so smooth scroll is ready on first wheel.
  // Mobile: strictly on interaction — keeps the ~150KB chunk off the LCP path.
  if (!isTouch) {
    window.addEventListener('load', () => idle(() => loadMotion()), { once: true });
  }
}

// ==========================================
// STICKY HEADER SHADOW + BACK TO TOP (native scroll)
// ==========================================
// Show/hide-on-scroll for #main-header lives in its Alpine x-data (header partial).
const header = document.getElementById('main-header');
const backToTopBtn = document.getElementById('back-to-top');

if (header || backToTopBtn) {
  let ticking = false;
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      ticking = false;
      const y = window.scrollY;
      if (header) header.classList.toggle('shadow-md', y > 50);
      if (backToTopBtn) {
        const show = y > 300;
        backToTopBtn.classList.toggle('opacity-0', !show);
        backToTopBtn.classList.toggle('pointer-events-none', !show);
        backToTopBtn.classList.toggle('translate-y-4', !show);
        backToTopBtn.classList.toggle('opacity-100', show);
        backToTopBtn.classList.toggle('pointer-events-auto', show);
        backToTopBtn.classList.toggle('translate-y-0', show);
      }
    });
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ==========================================
// SMOOTH SCROLLING (anchors + back to top)
// ==========================================
function smoothScrollTo(target, offset = 0) {
  // Once the motion chunk is loaded (desktop) Lenis drives the scroll;
  // otherwise fall back to native smooth scrolling.
  if (motionPromise) {
    motionPromise.then((m) => m.scrollTo(target, offset));
    return;
  }
  const top = typeof target === 'number'
    ? target
    : target.getBoundingClientRect().top + window.scrollY + offset;
  window.scrollTo({ top, behavior: reducedMotion ? 'auto' : 'smooth' });
}

if (backToTopBtn) {
  backToTopBtn.addEventListener('click', (e) => {
    e.preventDefault();
    smoothScrollTo(0);
  });
}

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', function (e) {
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    const targetEl = document.querySelector(targetId);
    if (targetEl) {
      e.preventDefault();
      smoothScrollTo(targetEl, -80);
    }
  });
});

// ==========================================
// SWIPER INITIALISATION (on demand)
// ==========================================
function initTestimonialsSwiper() {
  const el = document.querySelector('.testimonials-swiper');
  if (!el || el.swiper) return;
  loadSwiper().then((Swiper) => {
    if (el.swiper) return;
    new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: true,
      navigation: {
        nextEl: '.swiper-button-next-custom',
        prevEl: '.swiper-button-prev-custom',
      },
      pagination: {
        el: '.swiper-pagination-custom',
        type: 'fraction',
      },
    });
  });
}

function initProductTestimonialsSwiper() {
  const el = document.querySelector('.product-testimonials-swiper');
  if (!el || el.swiper) return;
  loadSwiper().then((Swiper) => {
    if (el.swiper) return;
    new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: true,
      navigation: {
        nextEl: '.swiper-button-next-product',
        prevEl: '.swiper-button-prev-product',
      },
      pagination: {
        el: '.product-testimonials-pagination',
        type: 'fraction',
      },
    });
  });
}

// Init on page load (no-ops if the testimonials section is deferred).
initTestimonialsSwiper();
initProductTestimonialsSwiper();

// Sliders initialised by inline Blade scripts poll for window.Swiper
// (posts/index featured-slider) — start fetching the chunk for them now.
if (document.querySelector('.featured-slider, .swiper')) {
  loadSwiper();
}

// ==========================================
// DEFERRED AJAX SECTIONS
// ==========================================
// Loads below-fold sections (testimonials: 97KB, contact: 68KB) on demand
// via IntersectionObserver to reduce initial HTML from ~239KB to ~74KB.
document.querySelectorAll('.deferred-ajax').forEach((placeholder) => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      observer.unobserve(placeholder);
      const section = placeholder.dataset.section;
      // Detect current locale from URL prefix (e.g. /id/...) or <html lang>
      const pathLocale = location.pathname.split('/').filter(Boolean)[0];
      const htmlLang = document.documentElement.lang;
      const locale = (pathLocale && /^[a-z]{2}$/.test(pathLocale) ? pathLocale : htmlLang) || '';
      const localeParam = locale ? `&locale=${locale}` : '';
      fetch(`/_deferred/${section}?_t=${Date.now()}${localeParam}`, { cache: 'no-store' })
        .then((r) => (r.ok ? r.text() : Promise.reject(r.status)))
        .then((html) => {
          // Swap the placeholder for the fetched markup
          const temp = document.createElement('div');
          temp.innerHTML = html;
          while (temp.firstChild) {
            placeholder.parentNode.insertBefore(temp.firstChild, placeholder);
          }
          placeholder.remove();

          // Re-initialize components on injected DOM
          if (section === 'testimonials') {
            initTestimonialsSwiper();
            // Recalculate ScrollTrigger positions if motion is (being) loaded
            if (motionPromise) motionPromise.then((m) => m.refresh());
          }
          if (section === 'contact') {
            // Re-init Alpine on the new contact form DOM
            if (window.Alpine) {
              window.Alpine.initTree(document.getElementById('contact'));
            }
          }
        })
        .catch((err) => {
          console.warn(`[deferred] Failed to load section "${section}":`, err);
        });
    });
  }, {
    rootMargin: '600px', // Start loading 600px before section enters viewport
  });
  observer.observe(placeholder);
});
