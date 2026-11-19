document.addEventListener('DOMContentLoaded', () => {
  // --- Mobile Menu Toggle ---
  const mobileToggle = document.querySelector('.mobile-menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  const toggleIconOpen = mobileToggle.querySelector('.menu-open');
  const toggleIconClose = mobileToggle.querySelector('.menu-close');

  const toggleMenu = () => {
    const isOpen = mainNav.classList.toggle('open');
    if (isOpen) {
      toggleIconOpen.style.display = 'none';
      toggleIconClose.style.display = 'block';
      document.body.style.overflow = 'hidden'; // Lock background scroll
    } else {
      toggleIconOpen.style.display = 'block';
      toggleIconClose.style.display = 'none';
      document.body.style.overflow = ''; // Restore background scroll
    }
  };

  mobileToggle.addEventListener('click', toggleMenu);

  // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    if (mainNav.classList.contains('open') && 
        !mainNav.contains(e.target) && 
        !mobileToggle.contains(e.target)) {
      toggleMenu();
    }
  });

  // Mobile dropdown toggle
  const dropdowns = document.querySelectorAll('.nav-item.dropdown');
  dropdowns.forEach(dropdown => {
    const link = dropdown.querySelector('.nav-link');
    link.addEventListener('click', (e) => {
      if (window.innerWidth <= 1024) {
        e.preventDefault(); // Prevent navigating to href
        
        // Accordion behavior: Check if current one is open
        const isOpen = dropdown.classList.contains('open-dropdown');
        
        // Close all dropdowns
        dropdowns.forEach(d => d.classList.remove('open-dropdown'));
        
        // Open the clicked one if it was previously closed
        if (!isOpen) {
          dropdown.classList.add('open-dropdown');
        }
      }
    });
  });

  // --- Slider Carousel ---
  const wrapper = document.querySelector('.slides-wrapper');
  const dots = document.querySelectorAll('.page-dot');
  let autoplayInterval;
  let isUserInteracting = false;

  if (wrapper) {
  // Update dots active class based on scroll position
  const updateDots = () => {
    const width = wrapper.clientWidth;
    const scrollLeft = wrapper.scrollLeft;
    // Math.round handles half-scrolled slides accurately
    const activeIndex = Math.round(scrollLeft / width);
    
    dots.forEach((dot, idx) => {
      if (idx === activeIndex) {
        dot.classList.add('active');
        dot.setAttribute('aria-current', 'true');
      } else {
        dot.classList.remove('active');
        dot.removeAttribute('aria-current');
      }
    });
  };

  // Scroll to slide when clicking dots
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      stopAutoplay();
      isUserInteracting = true;
      
      const width = wrapper.clientWidth;
      wrapper.scrollTo({
        left: width * index,
        behavior: 'smooth'
      });
    });
  });

  // Sync dots while scrolling
  wrapper.addEventListener('scroll', updateDots, { passive: true });

  // Autoplay functionality
  const startAutoplay = () => {
    if (isUserInteracting) return;
    
    autoplayInterval = setInterval(() => {
      const width = wrapper.clientWidth;
      if (width === 0) return; // Hidden or unrendered
      
      const scrollLeft = wrapper.scrollLeft;
      const currentIndex = Math.round(scrollLeft / width);
      const nextIndex = (currentIndex + 1) % dots.length;
      
      wrapper.scrollTo({
        left: width * nextIndex,
        behavior: 'smooth'
      });
    }, 5000); // Transition every 5 seconds
  };

  const stopAutoplay = () => {
    if (autoplayInterval) {
      clearInterval(autoplayInterval);
    }
  };

  // Start autoplay immediately
  startAutoplay();

  // Stop autoplay on drag/swipe/touch interactions
  const handleInteractionStart = () => {
    isUserInteracting = true;
    stopAutoplay();
  };

  wrapper.addEventListener('touchstart', handleInteractionStart, { passive: true });
  wrapper.addEventListener('mousedown', handleInteractionStart);
  
  // Adjust scroll offset on window resize to keep current slide centered
  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      const activeDot = document.querySelector('.page-dot.active');
      if (activeDot) {
        const index = parseInt(activeDot.getAttribute('data-slide-index'), 10);
        const width = wrapper.clientWidth;
        wrapper.scrollLeft = width * index;
      }
    }, 100);
  });
  }

  // --- Header Scroll Effect (Sticky background color change) ---
  const header = document.getElementById('main-header');
  const handleScroll = () => {
    if (window.scrollY > 15) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', handleScroll, { passive: true });
  // Initial check on load
  handleScroll();

  // --- Bisnis Section Slider ---
  const bisnisSlides = document.querySelectorAll('.bisnis-slide');
  const bisnisDots = document.querySelectorAll('.bisnis-dot');
  
  if (bisnisSlides.length > 0 && bisnisDots.length > 0) {
    let currentBisnisSlide = 0;
    
    const showBisnisSlide = (index) => {
      bisnisSlides.forEach(slide => slide.classList.remove('active'));
      bisnisDots.forEach(dot => dot.classList.remove('active'));
      
      bisnisSlides[index].classList.add('active');
      bisnisDots[index].classList.add('active');
      currentBisnisSlide = index;
    };
    
    bisnisDots.forEach(dot => {
      dot.addEventListener('click', () => {
        const index = parseInt(dot.getAttribute('data-index'), 10);
        showBisnisSlide(index);
      });
    });
    
    // Auto slide for bisnis section (every 7 seconds)
    setInterval(() => {
      let nextSlide = (currentBisnisSlide + 1) % bisnisSlides.length;
      showBisnisSlide(nextSlide);
    }, 7000);
  }

  // --- Product Gallery Slider ---
  const mainImg = document.querySelector('.gallery-main-img');
  const thumbItems = document.querySelectorAll('.gallery-thumb-item');
  const arrowLeft = document.querySelector('.thumb-arrow.arrow-left');
  const arrowRight = document.querySelector('.thumb-arrow.arrow-right');
  
  if (thumbItems.length > 0 && mainImg) {
    let currentIdx = 0;
    
    const updateGallery = (index) => {
      // Bounds checking
      if (index < 0) index = thumbItems.length - 1;
      if (index >= thumbItems.length) index = 0;
      
      currentIdx = index;
      
      // Update active thumbnail class
      thumbItems.forEach((item, idx) => {
        if (idx === currentIdx) {
          item.classList.add('active');
        } else {
          item.classList.remove('active');
        }
      });
      
      // Update main image source
      const newImgSrc = thumbItems[currentIdx].getAttribute('data-img');
      if (newImgSrc) {
        mainImg.src = newImgSrc;
      }
    };
    
    // Thumbnail click handlers
    thumbItems.forEach((item, index) => {
      item.addEventListener('click', () => {
        updateGallery(index);
      });
    });
    
    // Left/Right arrow handlers
    if (arrowLeft) {
      arrowLeft.addEventListener('click', () => {
        updateGallery(currentIdx - 1);
      });
    }
    
    if (arrowRight) {
      arrowRight.addEventListener('click', () => {
        updateGallery(currentIdx + 1);
      });
    }
  }

  // --- Product Tabs Navigation ---
  const tabSpecs = document.getElementById('tab-specs');
  const tabDatasheet = document.getElementById('tab-datasheet');
  const contentSpecs = document.getElementById('content-specs');
  const contentDatasheet = document.getElementById('content-datasheet');
  
  if (tabSpecs && tabDatasheet && contentSpecs && contentDatasheet) {
    tabSpecs.addEventListener('click', () => {
      tabSpecs.classList.add('active');
      tabDatasheet.classList.remove('active');
      contentSpecs.style.display = 'block';
      contentDatasheet.style.display = 'none';
    });
    
    tabDatasheet.addEventListener('click', () => {
      tabDatasheet.classList.add('active');
      tabSpecs.classList.remove('active');
      contentSpecs.style.display = 'none';
      contentDatasheet.style.display = 'block';
    });
  }
});
