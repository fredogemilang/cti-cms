/**
 * Kouventa Theme Motion & Scroll Reveal Engine
 * Features:
 * - IntersectionObserver based below-the-fold scroll reveals with smooth cascading waves ("susul-susulan").
 * - Hardware-accelerated transforms (translateY + opacity).
 * - Automatic detection of genuine card grids, logo lists, tablists, section headings, and standalone panels.
 * - Strict isolation: never adds card hover styles to structural containers, tabpanels, or inner text columns.
 * - Triggers progressively as the user scrolls into each block/grid.
 * - Unobserves each item after reveal for 60fps performance without layout thrashing.
 * - Prefers-reduced-motion compliance.
 * - Parent card hover state synchronizer for inner buttons & arrows.
 */
(function () {
  'use strict';

  function initMotion() {
    // Respect user's motion preferences
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      document.querySelectorAll('.kv-reveal, .kv-reveal-item, .kv-reveal-block, .kv-reveal-grid').forEach(function (el) {
        el.classList.add('is-revealed');
      });
      return;
    }

    // Mark HTML root with js-motion enabled so CSS triggers smooth reveal
    document.documentElement.classList.add('js-motion');

    var main = document.querySelector('main');
    if (!main) return;

    var sections = Array.from(main.querySelectorAll('section'));
    if (!sections.length) return;

    // The first section is above the fold (Hero) - always visible immediately without delay
    var firstSection = sections[0];
    firstSection.classList.add('is-revealed', 'kv-above-fold');
    firstSection.querySelectorAll('.kv-reveal-item, .kv-reveal, .kv-reveal-block').forEach(function (el) {
      el.classList.add('is-revealed');
    });

    // All subsequent sections are below the fold and must reveal on scroll
    var belowFoldSections = sections.slice(1);

    // List of groups/elements to observe
    var targetsToObserve = [];

    belowFoldSections.forEach(function (section) {
      // 1. Section Header / Headings (h2, subtitle, action button at top)
      var headings = Array.from(section.querySelectorAll('h2, [role="heading"]'));
      headings.forEach(function (h) {
        // Do NOT separate headings if they are inside a self-contained panel (e.g. CTA banner)
        if (h.closest('.kv-panel, [role="tabpanel"]')) return;

        var headerWrapper = h.closest('div[style*="justify-content: space-between"]') || h.parentElement;
        if (headerWrapper && headerWrapper !== section && !headerWrapper.hasAttribute('data-motion-observed')) {
          headerWrapper.classList.add('kv-reveal-block');
          headerWrapper.setAttribute('data-motion-observed', 'true');
          targetsToObserve.push(headerWrapper);

          // Mark child headings
          h.classList.add('kv-reveal-item');
          h.style.setProperty('--kv-delay', '0ms');
          var subtitle = headerWrapper.querySelector('p, span:not(.kv-arrow)');
          if (subtitle && subtitle !== h) {
            subtitle.classList.add('kv-reveal-item');
            subtitle.style.setProperty('--kv-delay', '70ms');
          }
          var actionLink = headerWrapper.querySelector('a, button');
          if (actionLink && !actionLink.closest('.kv-linked-pill, .kv-unlinked-pill, .kv-pill')) {
            actionLink.classList.add('kv-reveal-item');
            actionLink.style.setProperty('--kv-delay', '120ms');
          }
        } else if (!h.hasAttribute('data-motion-observed')) {
          h.classList.add('kv-reveal-item', 'kv-reveal-block');
          h.setAttribute('data-motion-observed', 'true');
          h.style.setProperty('--kv-delay', '0ms');
          targetsToObserve.push(h);
        }
      });

      // 2. Genuine Card Grids (Two Paths, Industries, Why, Partners, Customers, Videos)
      // Must NOT be inside a card/panel, and must NOT be an internal layout grid
      var allGrids = Array.from(section.querySelectorAll(
        '[style*="display: grid"], [style*="grid-template-columns"], .grid, .kv-grid'
      ));

      var cardGrids = allGrids.filter(function (grid) {
        // Exclude if it's inside a panel or card (like the 2-column layout inside tabpanel or stories)
        if (grid.closest('.kv-panel, [role="tabpanel"], .kv-card')) return false;
        if (grid.classList.contains('kv-panel') || grid.classList.contains('kv-card') || grid.getAttribute('role') === 'tabpanel') return false;
        if (grid.closest('form, header, nav')) return false;

        var validChildren = Array.from(grid.children).filter(function (c) {
          return c.tagName !== 'TEMPLATE' && c.tagName !== 'NOSCRIPT';
        });
        if (validChildren.length < 2) return false;

        var style = grid.getAttribute('style') || '';
        var hasRepeat = style.indexOf('repeat(') !== -1;
        var hasGridClass = grid.classList.contains('grid') || grid.classList.contains('kv-grid') || grid.classList.contains('kv-card-grid');

        return hasRepeat || hasGridClass;
      });

      cardGrids.forEach(function (grid) {
        if (grid.hasAttribute('data-motion-observed')) return;
        grid.setAttribute('data-motion-observed', 'true');
        grid.classList.add('kv-reveal-grid');
        targetsToObserve.push(grid);

        var validChildren = Array.from(grid.children).filter(function (c) {
          return c.tagName !== 'TEMPLATE' && c.tagName !== 'NOSCRIPT';
        });

        // Determine stagger interval based on item count
        var interval = validChildren.length > 8 ? 50 : 80;

        validChildren.forEach(function (child, index) {
          child.classList.add('kv-reveal-item');
          // Stagger delay: wave cascade
          var staggerDelay = Math.min(index * interval, 650);
          child.style.setProperty('--kv-delay', staggerDelay + 'ms');

          // Normalize logo or industry cards if needed
          assignCardClasses(child);
        });
      });

      // 3. Tablists (e.g. Teams tabs)
      var tablists = section.querySelectorAll('[role="tablist"]');
      tablists.forEach(function (tablist) {
        if (tablist.hasAttribute('data-motion-observed')) return;
        // Skip product tabs which manage their own dynamic state / opacity
        if (tablist.classList.contains('kv-product-tablist') || tablist.querySelector('.kv-product-tab') || tablist.closest('#products, #ai-products')) return;
        tablist.setAttribute('data-motion-observed', 'true');
        tablist.classList.add('kv-reveal-grid');
        targetsToObserve.push(tablist);

        var tabs = Array.from(tablist.children);
        tabs.forEach(function (tab, index) {
          tab.classList.add('kv-reveal-item');
          tab.style.setProperty('--kv-delay', (index * 45) + 'ms');
        });
      });

      // 4. Standalone large panels (Tabpanel, Stories container, CTA banner)
      var standalonePanels = section.querySelectorAll(
        '[role="tabpanel"], .kv-panel, .kv-card:not(.kv-reveal-item), [style*="border-radius: 24px"]:not(.kv-reveal-item), [style*="border-radius: 28px"]:not(.kv-reveal-item)'
      );

      standalonePanels.forEach(function (panel) {
        // Exclude if it's already a child of an observed grid
        if (panel.closest('.kv-reveal-grid')) return;
        if (panel.hasAttribute('data-motion-observed')) return;
        panel.setAttribute('data-motion-observed', 'true');
        panel.classList.add('kv-reveal-item', 'kv-reveal-block');
        panel.style.setProperty('--kv-delay', '60ms');
        targetsToObserve.push(panel);
        // Note: Do NOT call assignCardClasses on structural panels
      });
    });

    // 5. Also observe footer
    var footer = document.querySelector('footer');
    if (footer && !footer.hasAttribute('data-motion-observed')) {
      footer.setAttribute('data-motion-observed', 'true');
      footer.classList.add('kv-reveal-block', 'kv-reveal-item');
      footer.style.setProperty('--kv-delay', '0ms');
      targetsToObserve.push(footer);
    }

    // 6. Create IntersectionObserver for below-the-fold reveals
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

          // If target is a grid or block, reveal all items inside
          var childItems = target.querySelectorAll('.kv-reveal-item');
          childItems.forEach(function (item) {
            item.classList.add('is-revealed');
          });

          // Unobserve so it triggers once smoothly
          observer.unobserve(target);
        }
      });
    }, observerOptions);

    // Observe all below-the-fold targets!
    targetsToObserve.forEach(function (target) {
      revealObserver.observe(target);
    });

    // 7. Card & Button Interactive Hover Bindings
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

    // NOTE: NEVER add .kv-card-hover to arbitrary elements!
    // Real cards have their classes explicitly in the Blade templates.
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
