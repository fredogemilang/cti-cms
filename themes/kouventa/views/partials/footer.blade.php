<footer style="flex-grow: 0; flex-shrink: 0; margin-top: auto; background: #071C33; color: #FFFFFF; padding: 64px 80px 36px; display: flex; flex-direction: column; gap: 48px">
    <div style="display: grid; grid-template-columns: 1.3fr 1fr 1fr 1.6fr 1.1fr; gap: 40px; align-items: start">
        <div style="display: flex; flex-direction: column; gap: 24px">
            <img src="{{ theme_asset('images/kouventa-logo.png') }}" alt="Kouventa" style="height: 40px; width: auto; align-self: flex-start">
            <div style="display: flex; gap: 10px">
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Kouventa on Instagram" class="kv-btn-action" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.08); color: #FFFFFF; display: flex; align-items: center; justify-content: center; text-decoration: none">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><path d="M17.5 6.5h.01"></path></svg>
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="Kouventa on LinkedIn" class="kv-btn-action" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.08); color: #FFFFFF; display: flex; align-items: center; justify-content: center; text-decoration: none">
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
            <a href="https://www.google.com/maps/search/?api=1&query=Centennial+Tower+Jl.+Jend.+Gatot+Subroto+Kav.+24-25+Jakarta" target="_blank" rel="noopener noreferrer" aria-label="Open address in Google Maps" style="color: #B9C6D3; font-size: 15px; display: flex; gap: 10px; align-items: flex-start; text-decoration: none; line-height: 1.55">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0; margin-top: 3px"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Centennial Tower, 12th Floor<br>Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta</span>
            </a>
            <a href="https://wa.me/628118496889" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" style="color: #B9C6D3; font-size: 15px; display: flex; gap: 10px; align-items: center; text-decoration: none">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><path d="M3 21l1.65-4.6A9 9 0 1 1 8.6 20.1Z"></path><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 .8a4 4 0 0 1-2.3-2.3l.8-1-1-2Z"></path></svg>
                <span>+62 811 8496 889</span>
            </a>
            <a href="mailto:kouventa@helios.id" aria-label="Send an email" style="color: #B9C6D3; font-size: 15px; display: flex; gap: 10px; align-items: center; text-decoration: none">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                <span>kouventa@helios.id</span>
            </a>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 14px">
            <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Certified & Registered', 'Certified & Registered') }}</span>
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

<!-- WhatsApp Chat Form Widget (Loaded from Backend Form Studio) -->
@include('kouventa::partials.whatsapp-modal')
