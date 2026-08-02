@if(setting('enable_cookie_consent', false))
<style>
    [x-cloak] { display: none !important; }
    .cdt-cookie-banner {
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        left: auto !important;
        max-width: 440px !important;
        width: calc(100% - 48px) !important;
        z-index: 99999 !important;
    }
    .cdt-cookie-btn-primary {
        background-color: #F53003 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border: 1px solid #F53003 !important;
        box-shadow: 0 4px 14px rgba(245, 48, 3, 0.35) !important;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        line-height: 1.25 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }
    .cdt-cookie-btn-primary:hover {
        background-color: #d92900 !important;
        border-color: #d92900 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }
    .cdt-cookie-btn-secondary {
        background-color: #f3f4f6 !important;
        color: #1f2937 !important;
        font-weight: 600 !important;
        border: 1px solid #e5e7eb !important;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        line-height: 1.25 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }
    .cdt-cookie-btn-secondary:hover {
        background-color: #e5e7eb !important;
        color: #111827 !important;
    }
    @media (max-width: 640px) {
        .cdt-cookie-banner {
            left: 16px !important;
            right: 16px !important;
            bottom: 16px !important;
            max-width: none !important;
            width: auto !important;
        }
    }
</style>

<div x-data="{ 
        accepted: localStorage.getItem('cookie_consent') === 'accepted',
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.accepted = true;
        }
    }" 
    x-show="!accepted" 
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 transform translate-y-8 scale-95"
    x-cloak
    class="cdt-cookie-banner bg-white/95 dark:bg-[#141414]/95 backdrop-blur-xl border border-gray-200/80 dark:border-white/10 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.18)] p-5 text-sm text-gray-800 dark:text-gray-200 antialiased font-sans">
    
    <div class="flex items-start gap-3.5">
        {{-- Cookie Icon SVG --}}
        <div class="h-10 w-10 rounded-xl bg-red-50 dark:bg-red-950/40 text-[#F53003] dark:text-red-400 flex items-center justify-center shrink-0 shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 8.5h.01M15.5 8.5h.01M12 12.5h.01M9.5 16h.01M14.5 15.5h.01"></path>
            </svg>
        </div>

        <div class="space-y-2.5 flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <h4 class="font-bold text-base tracking-tight text-gray-900 dark:text-white">We value your privacy</h4>
                <button type="button" @click="accept()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-lg transition-colors" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                We use essential cookies to ensure our website functions properly and to enhance your browsing experience.
            </p>

            <div class="flex items-center gap-2.5 pt-1.5">
                <button type="button" @click="accept()" class="cdt-cookie-btn-primary flex-1">
                    Accept All
                </button>
                <button type="button" @click="accept()" class="cdt-cookie-btn-secondary flex-1">
                    Essential Only
                </button>
            </div>
        </div>
    </div>
</div>
@endif
