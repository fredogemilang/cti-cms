<footer style="flex-grow: 0; flex-shrink: 0; margin-top: auto; background: #071C33; color: #FFFFFF; padding: 64px 80px 36px; display: flex; flex-direction: column; gap: 48px">
    <div style="display: grid; grid-template-columns: 1.3fr 1fr 1fr 1.6fr 1.1fr; gap: 40px; align-items: start">
        <div style="display: flex; flex-direction: column; gap: 24px">
            <img src="{{ theme_asset('images/kouventa-logo.png') }}" alt="Kouventa" style="height: 40px; width: auto; align-self: flex-start">
            <div style="display: flex; gap: 10px">
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Kouventa on Instagram" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.08); color: #FFFFFF; display: flex; align-items: center; justify-content: center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><path d="M17.5 6.5h.01"></path></svg>
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="Kouventa on LinkedIn" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.08); color: #FFFFFF; display: flex; align-items: center; justify-content: center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M8 10v7M8 7h.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"></path></svg>
                </a>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 14px">
            <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Product', 'Product') }}</span>
            <a href="{{ localized_url('/product/ai') }}" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</a>
            <a href="{{ localized_url('/product/customer-engagement') }}" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</a>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 14px">
            <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Insights', 'Insights') }}</span>
            <a href="{{ localized_url('/about') }}" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.About us', 'About us') }}</a>
            <a href="{{ localized_url('/insight') }}" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.Blog', 'Blog') }}</a>
            <a href="#" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.Terms of service', 'Terms of service') }}</a>
            <a href="#" style="color: #B9C6D3; font-size: 15px">{{ t('kouventa.Privacy policy', 'Privacy policy') }}</a>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 14px">
            <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Contact us', 'Contact us') }}</span>
            <span style="font-size: 15px; line-height: 1.55; color: #B9C6D3">Centennial Tower, 12th Floor<br>Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta</span>
            <a href="tel:+62811849689" style="color: #B9C6D3; font-size: 15px">+62 8118 4968 89</a>
            <a href="mailto:kouventa@helios.id" style="color: #B9C6D3; font-size: 15px">kouventa@helios.id</a>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 14px">
            <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Certified', 'Certified') }}</span>
            <div style="display: flex; gap: 12px; align-items: center">
                <div style="background: #FFFFFF; border-radius: 10px; height: 80px; width: 96px; box-sizing: border-box; padding: 8px; display: flex; align-items: center; justify-content: center">
                    <img src="{{ theme_asset('images/badge-iso-27001.png') }}" alt="ISO 27001 — KAN accredited" style="width: 100%; height: auto">
                </div>
                <div style="background: #FFFFFF; border-radius: 10px; height: 80px; width: 80px; box-sizing: border-box; padding: 6px; display: flex; align-items: center; justify-content: center">
                    <img src="{{ theme_asset('images/badge-kominfo.png') }}" alt="Terdaftar di Kominfo" style="height: 100%; width: auto">
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1)">
        <span style="font-size: 14px; color: #93A4B6">© {{ date('Y') }} Kouventa by PT Helios Informatika Nusantara</span>
    </div>
</footer>

<!-- Sticky Floating WhatsApp Button -->
<a href="{{ function_exists('setting') ? setting('whatsapp_url', 'https://wa.me/62811849689') : 'https://wa.me/62811849689' }}"
   target="_blank"
   rel="noopener noreferrer"
   class="kv-wa-sticky"
   aria-label="Chat on WhatsApp">
    <span class="kv-wa-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path>
        </svg>
        <span class="kv-wa-ping"></span>
    </span>
    <span class="kv-wa-text">{{ t('kouventa.Chat on WhatsApp', 'Chat on WhatsApp') }}</span>
</a>
