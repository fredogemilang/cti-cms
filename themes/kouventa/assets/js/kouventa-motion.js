/**
 * Kouventa Theme Motion & Scroll Reveal Engine
 * Features:
 * - IntersectionObserver based below-the-fold scroll reveals with smooth wave staggers.
 * - Hardware-accelerated transforms (translateY + opacity).
 * - Automatic detection of grids, cards, logo lists, and section headings.
 * - Instant unobserve to guarantee optimal 60fps performance without layout thrashing.
 * - Prefers-reduced-motion compliance.
 * - Initial-viewport fast-pass & safety fallback so no element can ever remain hidden.
 * - Parent card hover state synchronizer for inner buttons & arrows.
 */
(function () {
  'use strict';

  function initMotion() {
    // Respect user's motion preferences
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      document.querySelectorAll('.kv-reveal, .kv-reveal-item').forEach(function (el) {
        el.classList.add('is-revealed');
      });
      return;
    }

    // Mark HTML root with js-motion enabled so CSS triggers smooth reveal
    document.documentElement.classList.add('js-motion');

    // 1. Identify all main sections
    var main = document.querySelector('main');
    if (!main) return;

    var sections = Array.from(main.querySelectorAll('section'));
    if (!sections.length) return;

    // The first section is above the fold (Hero) - always visible immediately
    var firstSection = sections[0];
    firstSection.classList.add('is-revealed', 'kv-above-fold');

    // All subsequent sections are below the fold
    var belowFoldSections = sections.slice(1);

    // Prepare reveal items inside below-fold sections
    belowFoldSections.forEach(function (section) {
      // A. Section heading/header (h2, preheading, intro paragraph)
      var headings = section.querySelectorAll('h2, [role="heading"]');
      headings.forEach(function (h) {
        if (!h.classList.contains('kv-reveal-item')) {
          h.classList.add('kv-reveal-item');
          h.style.setProperty('--kv-delay', '0ms');
        }
      });

      // B. Grids, lists, and multi-child card containers
      var gridContainers = section.querySelectorAll(
        '[style*="display: grid"], [style*="grid-template-columns"], .grid, .kv-grid'
      );

      gridContainers.forEach(function (grid) {
        var children = Array.from(grid.children);
        var validChildren = children.filter(function (c) {
          return c.tagName !== 'TEMPLATE' && c.tagName !== 'NOSCRIPT';
        });

        validChildren.forEach(function (child, index) {
          if (!child.classList.contains('kv-reveal-item')) {
            child.classList.add('kv-reveal-item');
          }
          // Stagger wave: 65ms per item, capped at 600ms so large lists don't take forever
          var staggerDelay = Math.min(index * 65, 600);
          child.style.setProperty('--kv-delay', staggerDelay + 'ms');

          // If child looks like a card or logo box, assign card hover classes
          assignCardClasses(child);
        });
      });

      // C. Look for standalone cards or big banners inside the section (like CTA banners, story box, tabpanel)
      var standaloneCards = section.querySelectorAll(
        '[role="tabpanel"], .kv-card, [style*="border-radius: 20px"], [style*="border-radius: 24px"], [style*="border-radius: 28px"]'
      );
      standaloneCards.forEach(function (card) {
        if (!card.classList.contains('kv-reveal-item')) {
          card.classList.add('kv-reveal-item');
          card.style.setProperty('--kv-delay', '90ms');
        }
        assignCardClasses(card);
      });
    });

    // Also check footer
    var footer = document.querySelector('footer');
    if (footer) {
      footer.classList.add('kv-reveal-item');
      footer.style.setProperty('--kv-delay', '60ms');
    }

    // 2. IntersectionObserver setup
    var observerOptions = {
      root: null,
      rootMargin: '0px 0px -40px 0px',
      threshold: 0.08
    };

    var revealObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var target = entry.target;
          target.classList.add('is-revealed');

          // Reveal all items inside target
          var items = target.querySelectorAll('.kv-reveal-item, .kv-reveal');
          items.forEach(function (item) {
            item.classList.add('is-revealed');
          });

          // Unobserve to run once smoothly
          observer.unobserve(target);
        }
      });
    }, observerOptions);

    // Observe below fold sections & footer
    belowFoldSections.forEach(function (section) {
      // Check if section is already in initial viewport on page load (e.g. large monitor or scrolled reload)
      var rect = section.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        section.classList.add('is-revealed');
        section.querySelectorAll('.kv-reveal-item, .kv-reveal').forEach(function (i) {
          i.classList.add('is-revealed');
        });
      } else {
        revealObserver.observe(section);
      }
    });

    if (footer) {
      var footerRect = footer.getBoundingClientRect();
      if (footerRect.top < window.innerHeight) {
        footer.classList.add('is-revealed');
      } else {
        revealObserver.observe(footer);
      }
    }

    // 3. Safety Fallback: After 1400ms, force-reveal any elements if not already revealed
    setTimeout(function () {
      document.querySelectorAll('.kv-reveal-item:not(.is-revealed), section:not(.is-revealed)').forEach(function (el) {
        el.classList.add('is-revealed');
      });
    }, 1400);

    // 4. Card & Button Interactive Hover Bindings
    setupCardButtonHoverSync();
  }

  function assignCardClasses(el) {
    if (!el || el.tagName === 'TEMPLATE' || el.tagName === 'NOSCRIPT') return;

    // Detect if this is a partner or customer logo card (has img with logo)
    var hasLogoImg = el.querySelector('img[src*="logo"], img[src*="partner"], img[src*="customer"]');
    if (hasLogoImg && !el.classList.contains('kv-logo-card')) {
      el.classList.add('kv-logo-card');
      return;
    }

    // Detect if this is an industry chip (height: 76px or has icon)
    var hasIndustrySvg = el.querySelector('span svg');
    if (hasIndustrySvg && el.textContent.trim().length < 25 && !el.classList.contains('kv-industry-card')) {
      var parentStyle = el.getAttribute('style') || '';
      if (parentStyle.indexOf('height: 76px') !== -1 || parentStyle.indexOf('76px') !== -1) {
        el.classList.add('kv-industry-card');
        var iconSpan = el.querySelector('span:first-child');
        if (iconSpan && !iconSpan.classList.contains('kv-ind-icon')) {
          iconSpan.classList.add('kv-ind-icon');
        }
        return;
      }
    }

    // General card
    if (!el.classList.contains('kv-card') && !el.classList.contains('kv-card-hover') && !el.classList.contains('kv-logo-card') && !el.classList.contains('kv-video-card')) {
      el.classList.add('kv-card-hover');
    }
  }

  function setupCardButtonHoverSync() {
    // Synchronize card hover to trigger inner button and arrow animation
    var cards = document.querySelectorAll(
      '.kv-card, .kv-card-hover, .kv-hover, .kv-video-card, .kv-card-dark, [data-kv-card]'
    );

    cards.forEach(function (card) {
      // Find arrows or buttons or cta links inside
      var innerLinks = card.querySelectorAll('a, button, .kv-btn-action, .kv-arrow, .kv-card-link');
      if (innerLinks.length > 0) {
        card.addEventListener('mouseenter', function () {
          innerLinks.forEach(function (link) {
            link.classList.add('is-parent-hovered');
          });
        });
        card.addEventListener('mouseleave', function () {
          innerLinks.forEach(function (link) {
            link.classList.remove('is-parent-hovered');
          });
        });
      }
    });
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMotion);
  } else {
    initMotion();
  }
})();
