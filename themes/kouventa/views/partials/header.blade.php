@php
    $siteLogo = setting('site_logo') ? resolve_block_asset(setting('site_logo')) : theme_asset('images/kouventa-logo.png');
    $siteName = setting('site_name', 'Kouventa');
    $currentLocale = app()->getLocale();
@endphp

<!-- Mobile Top Header Bar -->
<div class="kv-mobile-header">
    <!-- Logo -->
    <a href="{{ localized_url('/') }}" style="flex-shrink: 0; display: flex; align-items: center; text-decoration: none">
        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height: 32px; width: auto; display: block">
    </a>

    <!-- Right Actions: Language Switcher & Book Demo -->
    <div style="display: flex; align-items: center; gap: 8px">
        <div role="group" aria-label="Language" style="display: flex; padding: 2px; gap: 2px; border-radius: 8px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); font-size: 12px; font-weight: 700">
            <a href="{{ current_page_localized_url('en') }}" style="padding: 4px 8px; border-radius: 6px; text-decoration: none; {{ $currentLocale === 'en' ? 'background: #FFFFFF; color: #071C33;' : 'color: rgba(255,255,255,0.8);' }}">EN</a>
            <a href="{{ current_page_localized_url('id') }}" style="padding: 4px 8px; border-radius: 6px; text-decoration: none; {{ $currentLocale === 'id' ? 'background: #FFFFFF; color: #071C33;' : 'color: rgba(255,255,255,0.8);' }}">ID</a>
        </div>
        <a href="{{ localized_url('/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none">
            {{ t('kouventa.Book demo', 'Book demo') }}
        </a>
    </div>
</div>

<!-- Desktop Header -->
<header x-data="{ productOpen: false, solutionOpen: false, pricingOpen: false }"
        @click.away="productOpen = false; solutionOpen = false; pricingOpen = false"
        class="kv-desktop-header"
        style="height: 80px; padding: 0 80px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; flex-shrink: 0; box-sizing: border-box; background: #071C33; color: #FFFFFF; border-bottom: 1px solid rgba(255,255,255,0.06)">
    
    @php
        $siteLogo = setting('site_logo') ? resolve_block_asset(setting('site_logo')) : theme_asset('images/kouventa-logo.png');
        $siteName = setting('site_name', 'Kouventa');
    @endphp
    <a href="{{ localized_url('/') }}" style="display: flex; align-items: center; text-decoration: none">
        <img src="{{ $siteLogo }}" alt="{{ $siteName }} home" style="height: 34px; width: auto; display: block">
    </a>

    <!-- Desktop Nav -->
    <nav aria-label="Main" style="display: flex; gap: 40px; align-items: center">
        <!-- Product Dropdown -->
        <div style="position: relative">
            <button type="button" @click="productOpen = !productOpen; solutionOpen = false; pricingOpen = false"
                    :aria-expanded="productOpen"
                    style="background: transparent; border: 0; padding: 12px 0; font-size: 15px; color: #D5DEE8; font-weight: 500; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 6px; cursor: pointer">
                {{ t('kouventa.Product', 'Product') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" :style="productOpen ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s ease"><path d="m6 9 6 6 6-6"></path></svg>
            </button>
            <div x-show="productOpen" x-transition x-cloak
                 class="kv-dropdown-menu"
                 style="position: absolute; top: 52px; left: -16px; width: 300px; background: #FFFFFF; border-radius: 14px; padding: 8px; box-shadow: 0 24px 48px rgba(0,0,0,0.3); z-index: 20">
                <a href="{{ localized_url('/product/ai') }}" style="background: transparent; padding: 14px 16px; border-radius: 10px; color: #0E1A24; display: flex; flex-direction: column; gap: 2px; text-decoration: none">
                    <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
                    <span style="font-size: 13px; color: #4A5866">{{ t('kouventa.Knowledge & automation agents', 'Knowledge & automation agents') }}</span>
                </a>
                <a href="{{ localized_url('/product/customer-engagement') }}" style="background: transparent; padding: 14px 16px; border-radius: 10px; color: #0E1A24; display: flex; flex-direction: column; gap: 2px; text-decoration: none">
                    <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
                    <span style="font-size: 13px; color: #4A5866">{{ t('kouventa.WhatsApp & omnichannel', 'WhatsApp & omnichannel') }}</span>
                </a>
            </div>
        </div>

        <!-- Solution Dropdown -->
        <div style="position: relative">
            <button type="button" @click="solutionOpen = !solutionOpen; productOpen = false; pricingOpen = false"
                    :aria-expanded="solutionOpen"
                    style="background: transparent; border: 0; padding: 12px 0; font-size: 15px; color: #D5DEE8; font-weight: 500; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 6px; cursor: pointer">
                {{ t('kouventa.Solution', 'Solution') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" :style="solutionOpen ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s ease"><path d="m6 9 6 6 6-6"></path></svg>
            </button>
            <div x-show="solutionOpen" x-transition x-cloak
                 class="kv-dropdown-solution"
                 style="position: absolute; top: 52px; left: -200px; width: 560px; background: #FFFFFF; border-radius: 16px; padding: 20px; box-shadow: 0 24px 48px rgba(0,0,0,0.3); color: #0E1A24; z-index: 20">
                <div style="display: flex; flex-direction: column; gap: 2px">
                    <span style="font-size: 12px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #4A5866; padding: 0 12px 8px">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
                    <a href="{{ localized_url('/solutions/ai?dept=0') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Customer Service', 'Customer Service') }}</a>
                    <a href="{{ localized_url('/solutions/ai?dept=1') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Recruitment', 'Recruitment') }}</a>
                    <a href="{{ localized_url('/solutions/ai?dept=2') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}</a>
                    <a href="{{ localized_url('/solutions/ai?dept=3') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Business Operation', 'Business Operation') }}</a>
                    <a href="{{ localized_url('/solutions/ai?dept=4') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Finance', 'Finance') }}</a>
                    <a href="{{ localized_url('/solutions/ai?dept=5') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Legal & Compliance', 'Legal & Compliance') }}</a>
                </div>
                <div style="display: flex; flex-direction: column; gap: 2px">
                    <span style="font-size: 12px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #4A5866; padding: 0 12px 8px">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
                    <a href="{{ localized_url('/solutions/customer-engagement?case=0') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}</a>
                    <a href="{{ localized_url('/solutions/customer-engagement?case=1') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Customer Service', 'Customer Service') }}</a>
                    <a href="{{ localized_url('/solutions/customer-engagement?case=2') }}" class="kv-dropdown-item" style="text-align: left; padding: 10px 12px; border-radius: 8px; border: 0; background: transparent; color: #0E1A24; font-size: 15px; font-weight: 600; text-decoration: none; display: block">{{ t('kouventa.Operation', 'Operation') }}</a>
                </div>
            </div>
        </div>

        <!-- Pricing Dropdown -->
        <div style="position: relative">
            <button type="button" @click="pricingOpen = !pricingOpen; productOpen = false; solutionOpen = false"
                    :aria-expanded="pricingOpen"
                    style="background: transparent; border: 0; padding: 12px 0; font-size: 15px; color: #D5DEE8; font-weight: 500; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 6px; cursor: pointer">
                {{ t('kouventa.Pricing', 'Pricing') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" :style="pricingOpen ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s ease"><path d="m6 9 6 6 6-6"></path></svg>
            </button>
            <div x-show="pricingOpen" x-transition x-cloak
                 class="kv-dropdown-menu"
                 style="position: absolute; top: 52px; left: -16px; width: 300px; background: #FFFFFF; border-radius: 14px; padding: 8px; box-shadow: 0 24px 48px rgba(0,0,0,0.3); z-index: 20">
                <a href="{{ localized_url('/pricing?tab=ai') }}" style="background: transparent; padding: 14px 16px; border-radius: 10px; color: #0E1A24; display: flex; flex-direction: column; gap: 2px; text-decoration: none">
                    <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
                    <span style="font-size: 13px; color: #4A5866">Starter · Grow · Scale</span>
                </a>
                <a href="{{ localized_url('/pricing?tab=ce') }}" style="background: transparent; padding: 14px 16px; border-radius: 10px; color: #0E1A24; display: flex; flex-direction: column; gap: 2px; text-decoration: none">
                    <span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
                    <span style="font-size: 13px; color: #4A5866">Starter · Professional · Enterprise</span>
                </a>
            </div>
        </div>

        <a href="{{ localized_url('/about') }}" style="color: #D5DEE8; font-size: 15px; font-weight: 500; text-decoration: none">{{ t('kouventa.About us', 'About us') }}</a>
        <a href="{{ localized_url('/insight') }}" style="color: #D5DEE8; font-size: 15px; font-weight: 500; text-decoration: none">{{ t('kouventa.Insight', 'Insight') }}</a>
    </nav>

    <!-- Header Actions (Lang Switcher & Book Demo) -->
    <div style="display: flex; align-items: center; gap: 12px">
        @php
            $currentLocale = app()->getLocale();
        @endphp
        <div role="group" aria-label="Language" style="display: flex; padding: 3px; gap: 2px; border-radius: 11px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14)">
            <a href="{{ current_page_localized_url('en') }}" style="border: 0; min-width: 40px; min-height: 36px; padding: 6px 10px; border-radius: 8px; font-size: 13px; font-weight: 800; letter-spacing: 0.04em; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; {{ $currentLocale === 'en' ? 'background: #FFFFFF; color: #071C33;' : 'background: transparent; color: #D5DEE8;' }}">EN</a>
            <a href="{{ current_page_localized_url('id') }}" style="border: 0; min-width: 40px; min-height: 36px; padding: 6px 10px; border-radius: 8px; font-size: 13px; font-weight: 800; letter-spacing: 0.04em; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; {{ $currentLocale === 'id' ? 'background: #FFFFFF; color: #071C33;' : 'background: transparent; color: #D5DEE8;' }}">ID</a>
        </div>

        <a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-primary" style="background: #2BD46A; color: #06251A; font-size: 15px; font-weight: 700; padding: 12px 22px; border-radius: 12px; display: inline-block; text-decoration: none">
            {{ t('kouventa.Book a demo', 'Book a demo') }}
        </a>
    </div>
</header>
