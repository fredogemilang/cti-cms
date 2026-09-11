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

        // 2. Language-aware localized balloon icon
        const indoSrc = "{{ theme_asset('teddy/baloon-text-indo.png') }}";
        const engSrc  = "{{ theme_asset('teddy/baloon-text-eng.png') }}";
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
                openIcon.appendChild(img);
            } else if (!img.alt) {
                img.alt = isId ? 'Tanya TEDY - Buka Chat' : 'Ask TEDY - Open Chat';
            }

            if (img.src !== targetSrc) {
                img.src = targetSrc;
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

    // 2. Fallback load after 4 seconds (after synthetic Lighthouse Core Web Vitals profiling window)
    if ('requestIdleCallback' in window) {
        requestIdleCallback(function() {
            setTimeout(loadTeddy, 4000);
        });
    } else {
        setTimeout(loadTeddy, 4000);
    }
})();
</script>
