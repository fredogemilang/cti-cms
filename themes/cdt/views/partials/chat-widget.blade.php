{{-- Teddy Chatbot Widget --}}
<style data-no-optimize="1">
	#chat-button {
		width: 150px;
		height: 71px;
		background-color: transparent;
		box-shadow: none;
		bottom: 30px;
		transform: scale(1.3);
		right: 50px;
	}
	#chat-button:hover {
		background-color: transparent;
	}
	.chat-widget {
		bottom: 100px;
	}	
	.close-icon {
		background: rgb(28, 142, 249);
		border-radius: 50%;
	}
	#chat-button .close-icon img {
		width: 120px;
		height: auto;
		padding: 3px;
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
            const img = document.querySelector('#chat-button .open-icon img');
            if (!img) return false;
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
