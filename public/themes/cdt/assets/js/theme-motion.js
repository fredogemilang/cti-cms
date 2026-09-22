// Lazy-loaded motion layer: GSAP + ScrollTrigger (+ Lenis on desktop).
//
// This chunk is imported on demand by theme.js (first scroll/touch/pointer,
// or idle on desktop) so the ~150KB of animation runtime never competes with
// the LCP image, fonts and critical CSS on the initial mobile load.
//
// Because it now runs *after* first paint, anything already inside the
// viewport when init() runs is left static instead of being hidden and
// re-animated (that would flash content and, on vendor pages, push LCP back
// behind the animation — the same reason the hero load animation was removed
// on 2026-08-13).
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

let lenis = null;
let initialized = false;

const inViewport = (el) => {
  const r = el.getBoundingClientRect();
  return r.bottom > 0 && r.top < window.innerHeight;
};

const scrollEase = (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t));

/** Smooth-scroll helper used by theme.js for anchors / back-to-top. */
export function scrollTo(target, offset = 0) {
  if (lenis) {
    lenis.scrollTo(target, { offset, duration: 1.2, easing: scrollEase });
    return;
  }
  const top = typeof target === 'number'
    ? target
    : target.getBoundingClientRect().top + window.scrollY + offset;
  window.scrollTo({ top, behavior: 'smooth' });
}

/** Recalculate trigger positions after DOM is injected (deferred sections). */
export function refresh() {
  ScrollTrigger.refresh();
}

export function init({ smoothScroll = false, hoverEffects = true } = {}) {
  if (initialized) return;
  initialized = true;

  // ------------------------------------------------------------------
  // Lenis smooth scrolling — desktop only. On touch devices native scroll
  // is smoother and cheaper, and the permanent gsap.ticker RAF loop Lenis
  // needs was burning main-thread time on phones for nothing.
  // ------------------------------------------------------------------
  if (smoothScroll) {
    lenis = new Lenis({ autoRaf: false });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
  }

  // ------------------------------------------------------------------
  // Blog sidebar sticky (GSAP pin) + active TOC highlighting
  // ------------------------------------------------------------------
  const blogSidebar = document.getElementById('blog-sidebar');
  const blogSidebarCol = document.getElementById('blog-sidebar-col');
  if (blogSidebar && blogSidebarCol) {
    const mainContent = blogSidebarCol.previousElementSibling;
    if (mainContent) {
      ScrollTrigger.create({
        trigger: blogSidebarCol,
        pin: blogSidebar,
        start: 'top 90px',
        end: () => `+=${mainContent.offsetHeight - blogSidebar.offsetHeight}`,
        pinSpacing: false,
      });
    }

    const tocLinks = blogSidebar.querySelectorAll('a.toc-link[href^="#"]');
    const setActiveToc = (activeLink) => {
      tocLinks.forEach((l) => {
        l.classList.remove('!text-primary', '!font-bold', 'before:!border-primary', 'before:!bg-primary');
      });
      activeLink.classList.add('!text-primary', '!font-bold', 'before:!border-primary', 'before:!bg-primary');
    };
    tocLinks.forEach((link) => {
      const targetEl = document.querySelector(link.getAttribute('href'));
      if (targetEl) {
        ScrollTrigger.create({
          trigger: targetEl,
          start: 'top 40%',
          end: 'bottom 40%',
          onEnter: () => setActiveToc(link),
          onEnterBack: () => setActiveToc(link),
        });
      }
    });
  }

  // ------------------------------------------------------------------
  // 1. Universal data-gsap attributes (below-the-fold only, see header)
  // ------------------------------------------------------------------
  gsap.utils.toArray('[data-gsap]').forEach((el) => {
    if (inViewport(el)) return;

    const effect = el.getAttribute('data-gsap');
    const delay = parseFloat(el.getAttribute('data-gsap-delay') || 0);
    const scrollTrigger = { trigger: el, start: 'top 85%', toggleActions: 'play none none reverse' };

    if (effect === 'fade-up') {
      gsap.from(el, { scrollTrigger, y: 50, opacity: 0, duration: 0.8, ease: 'power3.out', delay });
    } else if (effect === 'fade-in') {
      gsap.from(el, { scrollTrigger, opacity: 0, duration: 1, ease: 'power2.out', delay });
    } else if (effect === 'curtain-reveal') {
      // Elegant left-to-right wipe
      gsap.fromTo(el,
        { clipPath: 'inset(0 100% 0 0)' },
        { scrollTrigger, clipPath: 'inset(0 0% 0 0)', duration: 1.2, ease: 'power4.inOut', delay });
    } else if (effect === 'blur-reveal') {
      // Wipe up to reveal from blur
      gsap.fromTo(el,
        { filter: 'blur(20px)', clipPath: 'inset(100% 0 0 0)', scale: 1.1 },
        { scrollTrigger: { ...scrollTrigger, start: 'top 90%' }, filter: 'blur(0px)', clipPath: 'inset(0% 0% 0% 0%)', scale: 1, duration: 1.4, ease: 'power3.out', delay });
    } else if (effect === 'line-grow') {
      // For the red underline decorations
      gsap.fromTo(el,
        { width: 0 },
        { scrollTrigger: { ...scrollTrigger, start: 'top 90%' }, width: '3rem', duration: 0.8, ease: 'power3.out', delay });
    }
  });

  // ------------------------------------------------------------------
  // 2. Section timelines (staggered groups). Hero animations stay removed
  //    (2026-08-13) — they delayed LCP by ~1.9s.
  // ------------------------------------------------------------------
  const staggerGroup = (itemSelector, sectionSelector, vars) => {
    const items = gsap.utils.toArray(itemSelector);
    const section = document.querySelector(sectionSelector);
    if (!items.length || !section || inViewport(section)) return;
    gsap.from(items, { scrollTrigger: { trigger: section, ...vars.trigger }, ...vars.from });
  };

  // B. Expertise Section (Staggered Spring Cards)
  staggerGroup('.expertise-card', '.expertise-section', {
    trigger: { start: 'top 75%', toggleActions: 'play none none reverse' },
    from: { y: 80, opacity: 0, rotation: 2, duration: 0.8, ease: 'back.out(1.2)', stagger: 0.15 },
  });
  // C. Alliance Section (Pop-up Logo Grid)
  staggerGroup('.alliance-logo', '.alliance-section', {
    trigger: { start: 'top 80%', toggleActions: 'play none none reverse' },
    from: { scale: 0, opacity: 0, duration: 0.6, ease: 'back.out(1.5)', stagger: 0.05 },
  });
  // D. AWS Offers Section (Pop-up Logo Grid)
  staggerGroup('.aws-logo', '.aws-offers-section', {
    trigger: { start: 'top 80%', toggleActions: 'play none none reverse' },
    from: { scale: 0, opacity: 0, duration: 0.6, ease: 'back.out(1.5)', stagger: 0.05 },
  });

  // ------------------------------------------------------------------
  // 3. Alliance hover showcase (14 effects) — pointer devices only
  // ------------------------------------------------------------------
  if (hoverEffects) {
    document.querySelectorAll('.alliance-link').forEach((link) => {
      const img = link.querySelector('img');
      if (!img) return;
      const effect = link.getAttribute('data-hover-effect');
      const hoverTl = gsap.timeline({ paused: true });

      switch (effect) {
        case 'scale-bounce':
          hoverTl.to(img, { scale: 1.15, duration: 0.4, ease: 'back.out(2)' });
          break;
        case 'lift-up':
          hoverTl.to(img, { y: -8, duration: 0.3, ease: 'power2.out' });
          break;
        case 'flip-y':
          hoverTl.to(img, { rotationY: 180, duration: 0.5, ease: 'power2.inOut' });
          break;
        case 'pulse':
          hoverTl.to(img, { scale: 1.1, duration: 0.3, yoyo: true, repeat: -1, ease: 'sine.inOut' });
          break;
        case 'jiggle':
          hoverTl.to(img, { rotation: 10, duration: 0.1, yoyo: true, repeat: 3, ease: 'sine.inOut' });
          break;
        case 'swing':
          gsap.set(img, { transformOrigin: 'top center' });
          hoverTl.to(img, { rotation: 15, duration: 0.4, ease: 'back.out(1.5)' });
          break;
        case 'elastic':
          hoverTl.to(img, { scaleX: 1.25, scaleY: 0.75, duration: 0.2 })
                 .to(img, { scaleX: 1, scaleY: 1, duration: 0.6, ease: 'elastic.out(1, 0.3)' });
          break;
        case 'spin':
          hoverTl.to(img, { rotation: 360, duration: 0.6, ease: 'power2.inOut' });
          break;
        case 'skew-slide':
          hoverTl.to(img, { skewX: -15, x: 10, duration: 0.3, ease: 'power1.out' });
          break;
        case 'shrink-fade':
          hoverTl.to(img, { scale: 0.85, opacity: 0.6, duration: 0.3, ease: 'power2.out' });
          break;
        case 'glow-pop':
          hoverTl.to(img, { scale: 1.1, filter: 'drop-shadow(0px 10px 10px rgba(0,0,0,0.2))', duration: 0.3, ease: 'back.out(1.5)' });
          break;
        case 'flip-x':
          hoverTl.to(img, { rotationX: 180, duration: 0.5, ease: 'power2.inOut' });
          break;
        case 'vibrate':
          hoverTl.to(img, { x: 2, duration: 0.05, yoyo: true, repeat: 5 })
                 .to(img, { x: -2, duration: 0.05, yoyo: true, repeat: 5 }, 0);
          break;
        case 'color-reveal':
          // Image starts as grayscale via Tailwind class; animate to full color
          hoverTl.to(img, { filter: 'grayscale(0%)', scale: 1.1, duration: 0.4, ease: 'power2.out' });
          break;
      }

      link.addEventListener('mouseenter', () => hoverTl.play());
      link.addEventListener('mouseleave', () => {
        // Repeating animations return to rest smoothly instead of reversing
        if (effect === 'pulse' || effect === 'vibrate' || effect === 'jiggle') {
          gsap.to(img, { scale: 1, rotation: 0, x: 0, y: 0, duration: 0.3, overwrite: true });
          hoverTl.pause(0);
        } else {
          hoverTl.reverse();
        }
      });
    });
  }
}
