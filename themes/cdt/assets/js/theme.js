import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Alpine from 'alpinejs';
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// Expose Swiper globally for inline scripts in partials that do `new Swiper(...)`.
window.Swiper = Swiper;

// Expose & start Alpine so x-data/x-show directives in partials work.
window.Alpine = Alpine;
Alpine.start();

// Register GSAP Plugin
gsap.registerPlugin(ScrollTrigger);

// Initialize Lenis
const lenis = new Lenis({
  autoRaf: false,
});

// Sync GSAP ScrollTrigger with Lenis
lenis.on('scroll', ScrollTrigger.update);
gsap.ticker.add((time) => {
  lenis.raf(time * 1000);
});
gsap.ticker.lagSmoothing(0);

// Smart Sticky Header Logic
const header = document.getElementById('main-header');
if (header) {
  ScrollTrigger.create({
    start: 'top -50',
    end: 99999,
    toggleClass: {className: 'shadow-md', targets: header}
  });

  let showAnim = gsap.from(header, { 
    yPercent: -100,
    paused: true,
    duration: 0.3,
    ease: "power2.out"
  }).progress(1);

  ScrollTrigger.create({
    start: "top top",
    end: "max",
    onUpdate: (self) => {
      if (self.direction === 1) {
        showAnim.reverse();
      } else {
        showAnim.play();
      }
    }
  });
}

// Back to Top Logic
const backToTopBtn = document.getElementById('back-to-top');
if (backToTopBtn) {
  lenis.on('scroll', (e) => {
    if (window.scrollY > 300) {
      backToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
      backToTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
    } else {
      backToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
      backToTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
    }
  });

  backToTopBtn.addEventListener('click', (e) => {
    e.preventDefault();
    lenis.scrollTo(0, { duration: 1.2, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
  });
}

// Smooth Anchor Scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    const targetEl = document.querySelector(targetId);
    if (targetEl) {
      e.preventDefault();
      lenis.scrollTo(targetEl, { offset: -80, duration: 1.2, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
    }
  });
});

// ==========================================
// BLOG SIDEBAR STICKY (GSAP Pin)
// ==========================================
const blogSidebar = document.getElementById('blog-sidebar');
const blogSidebarCol = document.getElementById('blog-sidebar-col');
if (blogSidebar && blogSidebarCol) {
  // Find the main content column (sibling) to match its height
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

  // Active TOC highlighting (text + dot)
  const tocLinks = blogSidebar.querySelectorAll('a.toc-link[href^="#"]');

  tocLinks.forEach(link => {
    const targetId = link.getAttribute('href');
    const targetEl = document.querySelector(targetId);
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

  function setActiveToc(activeLink) {
    tocLinks.forEach(l => {
      l.classList.remove('!text-primary', '!font-bold', 'before:!border-primary', 'before:!bg-primary');
    });
    activeLink.classList.add('!text-primary', '!font-bold', 'before:!border-primary', 'before:!bg-primary');
  }
}

// ==========================================
// 1. UNIVERSAL DATA-GSAP ATTRIBUTES
// ==========================================
const gsapElements = gsap.utils.toArray('[data-gsap]');
gsapElements.forEach((el) => {
  const effect = el.getAttribute('data-gsap');
  const delay = parseFloat(el.getAttribute('data-gsap-delay') || 0);
  
  if (effect === 'fade-up') {
    gsap.from(el, {
      scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none reverse' },
      y: 50, opacity: 0, duration: 0.8, ease: 'power3.out', delay: delay
    });
  } 
  else if (effect === 'fade-in') {
    gsap.from(el, {
      scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none reverse' },
      opacity: 0, duration: 1, ease: 'power2.out', delay: delay
    });
  }
  else if (effect === 'curtain-reveal') {
    // Elegant left-to-right wipe
    gsap.fromTo(el, 
      { clipPath: 'inset(0 100% 0 0)' }, 
      {
        scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none reverse' },
        clipPath: 'inset(0 0% 0 0)', duration: 1.2, ease: 'power4.inOut', delay: delay
      }
    );
  }
  else if (effect === 'blur-reveal') {
    // Wipe up to reveal from blur
    gsap.fromTo(el, 
      { filter: 'blur(20px)', clipPath: 'inset(100% 0 0 0)', scale: 1.1 }, 
      {
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none reverse' },
        filter: 'blur(0px)', clipPath: 'inset(0% 0% 0% 0%)', scale: 1, duration: 1.4, ease: 'power3.out', delay: delay
      }
    );
  }
  else if (effect === 'line-grow') {
    // For the red underline decorations
    gsap.fromTo(el,
      { width: 0 },
      {
        scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none reverse' },
        width: '3rem', duration: 0.8, ease: 'power3.out', delay: delay
      }
    )
  }
});

// ==========================================
// 2. CUSTOM SECTION TIMELINES
// ==========================================

// A. Hero Section — animations REMOVED (2026-08-13).
// The gsap.from() load animation (bg scale 1.15 over 2s + text stagger) delayed
// the LCP paint by ~1.9s (Lighthouse elementRenderDelay). The hero now renders
// statically; keep this block deleted so rebuilds don't reintroduce it.

// B. Expertise Section (Staggered Spring Cards)
const expertiseCards = gsap.utils.toArray('.expertise-card');
if (expertiseCards.length > 0) {
  gsap.from(expertiseCards, {
    scrollTrigger: { trigger: '.expertise-section', start: 'top 75%', toggleActions: 'play none none reverse' },
    y: 80, opacity: 0, rotation: 2, duration: 0.8, ease: 'back.out(1.2)', stagger: 0.15
  });
}

// C. Alliance Section (Pop-up Logo Grid)
const allianceLogos = gsap.utils.toArray('.alliance-logo');
if (allianceLogos.length > 0) {
  gsap.from(allianceLogos, {
    scrollTrigger: { trigger: '.alliance-section', start: 'top 80%', toggleActions: 'play none none reverse' },
    scale: 0, opacity: 0, duration: 0.6, ease: 'back.out(1.5)', stagger: 0.05
  });
}

// D. AWS Offers Section (Pop-up Logo Grid)
const awsLogos = gsap.utils.toArray('.aws-logo');
if (awsLogos.length > 0) {
  gsap.from(awsLogos, {
    scrollTrigger: { trigger: '.aws-offers-section', start: 'top 80%', toggleActions: 'play none none reverse' },
    scale: 0, opacity: 0, duration: 0.6, ease: 'back.out(1.5)', stagger: 0.05
  });
}

// ==========================================
// 3. ALLIANCE HOVER SHOWCASE (14 EFFECTS)
// ==========================================
const allianceLinks = document.querySelectorAll('.alliance-link');

allianceLinks.forEach((link) => {
  const img = link.querySelector('img');
  const effect = link.getAttribute('data-hover-effect');
  
  // Create a timeline for each link that is paused by default
  const hoverTl = gsap.timeline({ paused: true });
  
  switch(effect) {
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
      // Fallback simple vibrate
      hoverTl.to(img, { x: 2, duration: 0.05, yoyo: true, repeat: 5 })
             .to(img, { x: -2, duration: 0.05, yoyo: true, repeat: 5 }, 0);
      break;
    case 'color-reveal':
      // Image starts as grayscale via Tailwind class
      // We animate it to full color using CSS filter
      hoverTl.to(img, { filter: 'grayscale(0%)', scale: 1.1, duration: 0.4, ease: 'power2.out' });
      break;
  }
  
  link.addEventListener('mouseenter', () => hoverTl.play());
  link.addEventListener('mouseleave', () => {
    // For repeating animations like pulse, we smoothly return to start
    if (effect === 'pulse' || effect === 'vibrate' || effect === 'jiggle') {
       gsap.to(img, { scale: 1, rotation: 0, x: 0, y: 0, duration: 0.3, overwrite: true });
       hoverTl.pause(0);
    } else {
       hoverTl.reverse();
    }
  });
});

// ==========================================
// 4. SWIPER JS INITIALIZATION
// ==========================================
if (typeof Swiper !== 'undefined') {
  new Swiper('.testimonials-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: '.swiper-button-next-custom',
      prevEl: '.swiper-button-prev-custom',
    },
    pagination: {
      el: '.swiper-pagination-custom',
      type: 'fraction'
    }
  });

  new Swiper('.product-testimonials-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: '.swiper-button-next-product',
      prevEl: '.swiper-button-prev-product',
    },
    pagination: {
      el: '.product-testimonials-pagination',
      type: 'fraction'
    }
  });
}
