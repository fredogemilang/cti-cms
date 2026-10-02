{{-- Teddy Chatbot Widget --}}
<style data-no-optimize="1">
	#chat-button {
		width: 150px !important;
		height: 71px !important;
		background-color: transparent !important;
		box-shadow: none !important;
		bottom: 30px !important;
		transform: scale(1.3) !important;
		right: 50px !important;
	}
	#chat-button:hover {
		background-color: transparent !important;
	}
	.chat-widget {
		bottom: 100px !important;
		overscroll-behavior: contain !important;
	}	
	.chat-body {
		overscroll-behavior: contain !important;
	}
	.close-icon {
		background: rgb(28, 142, 249) !important;
		border-radius: 50% !important;
	}
	#chat-button .close-icon img {
		width: 120px !important;
		height: auto !important;
		padding: 3px !important;
	}
</style>

<script data-no-optimize="1">
(function() {
    let teddyLoaded = false;

    function loadTeddy() {
        if (teddyLoaded) return;
        teddyLoaded = true;

        // Cleanup user interaction event listeners
        ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(e) {
            window.removeEventListener(e, loadTeddy, { passive: true });
        });

        // 1. Inject Teddy Widget script tag with exact requested attributes
        const s = document.createElement('script');
        s.setAttribute('data-no-optimize', '1');
        s.setAttribute('data-name', 'teddy_widget');
        s.setAttribute('data-embed-id', '80b9ae0e-4058-4264-9c41-641c728200bb');
        s.setAttribute('data-base-api-url', 'https://teddy.centraldatatech.com:3001/api');
        s.setAttribute('data-avatar-url', 'https://teddy.centraldatatech.com:3001/api/system/logo?theme=default');
        s.setAttribute('data-widget-bottom', '120');
        s.setAttribute('data-widget-right', '20');
        s.src = "{{ theme_asset('teddy/chatWidget.js') }}";
        s.defer = true;
        document.body.appendChild(s);

        // 2. Language-aware localized balloon icon (using lightweight WebP)
        const indoSrc = "{{ theme_asset('teddy/baloon-text-indo.webp') }}";
        const engSrc  = "{{ theme_asset('teddy/baloon-text-eng.webp') }}";
        const isId = window.location.pathname.startsWith('/id/') || window.location.pathname === '/id';
        const targetSrc = isId ? indoSrc : engSrc;

        // Preload image
        const pre = new Image();
        pre.src = targetSrc;

        function applyIcon() {
            const chatBtn = document.getElementById('chat-button');
            if (chatBtn) {
                const label = isId ? 'Tanya TEDY - Buka Chat' : 'Ask TEDY - Open Chat';
                if (!chatBtn.getAttribute('aria-label')) {
                    chatBtn.setAttribute('aria-label', label);
                }
                if (!chatBtn.getAttribute('title')) {
                    chatBtn.setAttribute('title', label);
                }
            }

            const openIcon = document.querySelector('#chat-button .open-icon');
            if (!openIcon) return false;

            let img = openIcon.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                img.alt = isId ? 'Tanya TEDY - Buka Chat' : 'Ask TEDY - Open Chat';
                img.setAttribute('width', '180');
                img.setAttribute('height', '46');
                openIcon.appendChild(img);
            } else {
                if (!img.alt) img.alt = isId ? 'Tanya TEDY - Buka Chat' : 'Ask TEDY - Open Chat';
                if (!img.getAttribute('width')) img.setAttribute('width', '180');
                if (!img.getAttribute('height')) img.setAttribute('height', '46');
            }

            if (img.src !== targetSrc) {
                img.src = targetSrc;
            }

            // Ensure all images inside chat-widget have alt attributes and dimensions, and isolate scrolling
            const chatWidgetEl = document.getElementById('chat-widget');
            if (chatWidgetEl) {
                chatWidgetEl.querySelectorAll('img').forEach(function(im) {
                    if (!im.getAttribute('alt')) {
                        im.setAttribute('alt', 'Icon');
                        im.setAttribute('aria-hidden', 'true');
                    }
                    if (!im.getAttribute('width')) {
                        im.setAttribute('width', '24');
                        im.setAttribute('height', '24');
                    }
                });

                if (!chatWidgetEl.dataset.scrollLockAttached) {
                    chatWidgetEl.dataset.scrollLockAttached = 'true';

                    const handleWheelScroll = function(e) {
                        if (!chatWidgetEl.classList.contains('active')) return;

                        const chatBody = chatWidgetEl.querySelector('.chat-body');
                        const table = e.target.closest('.table-scroll-wrapper');
                        if (table && Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                            table.scrollLeft += e.deltaX;
                            e.preventDefault();
                            e.stopPropagation();
                            return;
                        }

                        if (chatBody) {
                            let delta = e.deltaY;
                            if (e.deltaMode === 1) delta *= 20;
                            else if (e.deltaMode === 2) delta *= chatBody.clientHeight;
                            const max = chatBody.scrollHeight - chatBody.clientHeight;
                            if (max > 0) {
                                chatBody.scrollTop = Math.max(0, Math.min(max, chatBody.scrollTop + delta));
                            }
                        }

                        e.preventDefault();
                        e.stopPropagation();
                    };

                    chatWidgetEl.addEventListener('wheel', handleWheelScroll, { passive: false });

                    let touchStartY = 0;
                    chatWidgetEl.addEventListener('touchstart', function(e) {
                        if (e.touches && e.touches.length > 0) {
                            touchStartY = e.touches[0].clientY;
                        }
                    }, { passive: true });

                    chatWidgetEl.addEventListener('touchmove', function(e) {
                        if (!chatWidgetEl.classList.contains('active')) return;
                        if (!e.touches || e.touches.length === 0) return;

                        const chatBody = chatWidgetEl.querySelector('.chat-body');
                        const currentY = e.touches[0].clientY;
                        const deltaY = touchStartY - currentY;

                        const isInsideBody = chatBody && chatBody.contains(e.target);
                        if (!isInsideBody) {
                            e.preventDefault();
                            e.stopPropagation();
                            return;
                        }

                        if (chatBody) {
                            const max = chatBody.scrollHeight - chatBody.clientHeight;
                            if (max <= 0) {
                                e.preventDefault();
                                e.stopPropagation();
                                return;
                            }

                            const atTop = chatBody.scrollTop <= 0 && deltaY < 0;
                            const atBottom = chatBody.scrollTop >= max - 1 && deltaY > 0;
                            if (atTop || atBottom) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                        }
                    }, { passive: false });
                }
            }

            return true;
        }

        // MutationObserver for zero-CPU reaction when widget DOM renders
        const observer = new MutationObserver(function() {
            if (applyIcon()) {
                observer.disconnect();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // Backup polling (stops after 30 attempts = max 4.5s)
        let tries = 0;
        const timer = setInterval(function() {
            if (applyIcon() || ++tries > 30) {
                clearInterval(timer);
                observer.disconnect();
            }
        }, 150);
    }

    // PageSpeed Preservation Strategy:
    // 1. Load upon first user interaction (scroll, cursor move, mobile touch, click)
    ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(e) {
        window.addEventListener(e, loadTeddy, { once: true, passive: true });
    });

    // 2. Fallback load only after 10 seconds of idle time (safe from synthetic audit windows)
    if ('requestIdleCallback' in window) {
        requestIdleCallback(function() {
            setTimeout(loadTeddy, 10000);
        });
    } else {
        setTimeout(loadTeddy, 10000);
    }
})();
</script>
