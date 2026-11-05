@extends('kouventa::layouts.app')

@section('content')
@php
    $isIdLocale = app()->getLocale() === 'id';
    $isPluginActive = is_plugin_active('kouventa-pages');

    // Section 1: Hero & Subnav
    $sHero = $page?->section('hero', []);
    $heroSubnavLabel = $sHero['subnav']['label'] ?? ($isIdLocale ? 'Solusi' : 'Solutions');
    $heroSubnavAi = $sHero['subnav']['ai_label'] ?? 'Artificial Intelligence';
    $heroSubnavCe = $sHero['subnav']['ce_label'] ?? 'Customer Engagement';
    $heroTitle = $sHero['title'] ?? [
        'prefix' => $isIdLocale ? 'Pengalaman pelanggan omnichannel' : 'Seamless omnichannel',
        'main' => $isIdLocale ? 'yang terintegrasi' : 'customer experience',
    ];
    $heroSubtitle = rtrim($sHero['subtitle'] ?? ($isIdLocale
        ? 'Dari kampanye pemasaran dan layanan pelanggan hingga update operasional, Kouventa membantu bisnis terhubung, merespons, dan berkomunikasi dengan lebih efektif.'
        : 'From marketing campaigns and customer support to operational updates, Kouventa helps businesses connect, respond, and communicate more effectively.'), '.');
    $heroCta = $sHero['primary_cta'] ?? [
        'text' => $isIdLocale ? 'Minta Demo' : 'Request a Demo',
        'url' => '/book-demo',
    ];
    $heroSecondaryBtn = $sHero['secondary_btn'] ?? [
        'text' => $isIdLocale ? 'Lihat Cara Kerjanya' : 'See How It Works',
    ];

    // Section 2: Roles
    $sRoles = $page?->section('roles', []);
    $roleSm = $sRoles['sales_marketing'] ?? [];
    $roleCs = $sRoles['customer_service'] ?? [];
    $roleOps = $sRoles['operations'] ?? [];

    // Section 3: Closing CTA
    $sCta = $page?->section('cta', []);
    $bottomCtaTitle = rtrim($sCta['title'] ?? ($isIdLocale ? 'Siap meningkatkan kualitas percakapan dengan pelanggan?' : 'Ready to improve every customer conversation?'), '.');
    $bottomCtaSubtitle = rtrim($sCta['subtitle'] ?? ($isIdLocale
        ? 'Ceritakan kebutuhan tim Anda, dan kami tunjukkan bagaimana Kouventa bisa membantu.'
        : "Tell us about your team's needs and we'll show you how Kouventa fits."), '.');
    $ctaPrimary = $sCta['primary_btn'] ?? ['text' => ($isIdLocale ? 'Minta Demo' : 'Request a Demo'), 'url' => '/book-demo'];
    $ctaSecondary = $sCta['secondary_btn'] ?? ['text' => ($isIdLocale ? 'Hubungi sales' : 'Talk to Sales'), 'url' => '#whatsapp'];
@endphp
<div class="kv-canvas-wrap" x-data="{
    tab: {{ $initialCase ?? 0 }},
    sol: -1,
    activeSol: 0,
    panelOn: true,
    _autoTimer: null,
    ceCounts: {
        0: 5,
        1: 4,
        2: 1
    },
    caseSlugs: {
        0: 'sales-marketing',
        1: 'customer-service',
        2: '{{ $isIdLocale ? 'operasional' : 'operations' }}'
    },
    baseSolutionPath: '{{ localized_url('/solutions/customer-engagement') }}',
    getCaseUrl(i) {
        const slug = this.caseSlugs[i] || 'sales-marketing';
        return `${this.baseSolutionPath}/${slug}`;
    },
    startAutoTimer() {
        if (this._autoTimer) clearTimeout(this._autoTimer);
        const scheduleNext = () => {
            if (this.sol !== -1) return;
            const duration = 5800;
            this._autoTimer = setTimeout(() => {
                if (this.sol === -1) {
                    const count = this.ceCounts[this.tab] || 1;
                    if (count > 1) {
                        this.activeSol = (this.activeSol + 1) % count;
                    }
                    scheduleNext();
                }
            }, duration);
        };
        scheduleNext();
    },
    lockSol(i) {
        this.sol = i;
        this.activeSol = i;
        if (this._autoTimer) clearTimeout(this._autoTimer);
    },
    parseTabFromUrl(url = window.location.href) {
        try {
            const u = new URL(url, window.location.origin);
            const path = u.pathname.toLowerCase();
            if (path.includes('/sales-marketing') || path.includes('/penjualan-pemasaran')) return 0;
            if (path.includes('/customer-service') || path.includes('/dukungan-pelanggan')) return 1;
            if (path.includes('/operations') || path.includes('/operasional')) return 2;

            const p = u.searchParams;
            const raw = p.get('case') !== null ? p.get('case') : p.get('tab');
            if (raw !== null) {
                const v = parseInt(raw, 10);
                if (!isNaN(v) && v >= 0 && v <= 2) return v;
            }
        } catch(e) {}
        return {{ $initialCase ?? 0 }};
    },
    pickTab(i, scroll = false, updateUrl = true) {
        if (this._autoTimer) clearTimeout(this._autoTimer);
        this.panelOn = false;
        this.tab = i;
        this.sol = -1;
        this.activeSol = 0;
        this.startAutoTimer();
        if (updateUrl && window.history && window.history.pushState) {
            const targetUrl = this.getCaseUrl(i);
            try {
                const cur = new URL(window.location.href);
                const tgt = new URL(targetUrl, window.location.origin);
                if (cur.pathname !== tgt.pathname) {
                    window.history.pushState({ tab: i }, '', targetUrl);
                }
            } catch(e) {}
        }
        setTimeout(() => {
            this.panelOn = true;
        }, 40);
        if (scroll) {
            setTimeout(() => {
                const el = document.getElementById('solution-tabs');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        }
    },
    init() {
        this.tab = this.parseTabFromUrl();
        this.sol = -1;
        this.activeSol = 0;
        this.panelOn = true;
        this.startAutoTimer();

        // If landing with case, tab, clean subpage, or #solution-tabs, scroll smoothly to tabs section
        const p = new URLSearchParams(window.location.search);
        const path = window.location.pathname.toLowerCase();
        const hasSubSlug = path.includes('/sales-marketing') || path.includes('/customer-service') || path.includes('/operations') || path.includes('/operasional');
        if (hasSubSlug || p.has('case') || p.has('tab') || window.location.hash.includes('solution-tabs')) {
            setTimeout(() => {
                const el = document.getElementById('solution-tabs');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }

        // Intercept internal tab clicks from header/mobile menu while already on this page
        document.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href) return;
            try {
                const u = new URL(href, window.location.href);
                if (u.pathname.includes('/solutions/customer-engagement')) {
                    const target = this.parseTabFromUrl(u.href);
                    if (target !== undefined && target !== null && target >= 0 && target <= 2) {
                        this.pickTab(target, true, true);
                        e.preventDefault();
                        return;
                    }
                    if (u.hash.includes('solution-tabs')) {
                        const el = document.getElementById('solution-tabs');
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            } catch(err) {}
        });

        window.addEventListener('popstate', () => {
            if (this._autoTimer) clearTimeout(this._autoTimer);
            this.panelOn = false;
            this.tab = this.parseTabFromUrl();
            this.sol = -1;
            this.activeSol = 0;
            this.startAutoTimer();
            setTimeout(() => { this.panelOn = true; }, 40);
            const el = document.getElementById('solution-tabs');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
}">

<!-- ============ NAV + HERO ============ -->
<section class="kv-hero-container" style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Solutions" style="height: 56px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4; width: 100%;">
<div class="kv-container" style="height: 56px; display: flex; align-items: center; gap: 32px;">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ $heroSubnavLabel }}</span>
<a href="{{ localized_url('/solutions/ai') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ $heroSubnavAi }}</a>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ $heroSubnavCe }}</a>
</div>
</nav>

<div class="kv-container kv-hero-section" style="height: 704px; box-sizing: border-box; display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 72px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? '' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? '' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">{{ $heroSubtitle }}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">{{ $heroCta['text'] ?? 'Request a Demo' }}</a>
<button type="button" @click="pickTab(tab, true)" style="background: transparent; color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28)">{{ $heroSecondaryBtn['text'] ?? ($isIdLocale ? 'Lihat Cara Kerjanya' : 'See How It Works') }}</button>
</div>
</div>

<!-- header illustration: four channels flow into one seamless customer experience -->
<div class="kv-sol-ce-hero-wrap">
    <!-- Desktop Horizontal Layout (> 768px) -->
    <div class="kv-sol-ce-hero-desktop" role="img" aria-label="Animated illustration: WhatsApp, Instagram, Facebook and Live Chat flow into one customer view with a five-star experience">
        <svg width="420" height="340" viewBox="0 0 420 340" fill="none" style="position: absolute; inset: 0">
            <path d="M60 48 C 110 48, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.2s both"></path>
            <path d="M60 128 C 110 128, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.4s both"></path>
            <path d="M60 208 C 110 208, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.6s both"></path>
            <path d="M60 288 C 110 288, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.8s both"></path>
            <path d="M214 170 L 240 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="40" style="--len: 40; animation: kvDraw .4s ease 2.5s both"></path>
        </svg>
        <span style="position: absolute; left: 4px; top: 20px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.2s both"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="{{ t('kouventa.Ikon Resmi WhatsApp Business API untuk Komunikasi Pelanggan Otomatis', 'Ikon Resmi WhatsApp Business API untuk Komunikasi Pelanggan Otomatis') }}" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 4px; top: 100px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.5s both"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="{{ t('kouventa.Ikon Kanal Pesan Instagram Direct Message (DM) Terintegrasi Kouventa', 'Ikon Kanal Pesan Instagram Direct Message (DM) Terintegrasi Kouventa') }}" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 4px; top: 180px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.7s both"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="{{ t('kouventa.Ikon Kanal Pesan Facebook Messenger Omnichannel Kouventa', 'Ikon Kanal Pesan Facebook Messenger Omnichannel Kouventa') }}" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 4px; top: 260px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.9s both"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="{{ t('kouventa.Ikon Web Live Chat Widget Interaktif Situs Web Kouventa', 'Ikon Web Live Chat Widget Interaktif Situs Web Kouventa') }}" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 150px; top: 138px; width: 64px; height: 64px; border-radius: 20px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(43,212,106,0.35); animation: kvPopIn .45s ease 2.1s both"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path></svg></span>
        <div style="position: absolute; left: 240px; top: 112px; width: 180px; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 8px; animation: kvPopIn .45s ease 2.8s both">
            <span style="display: flex; align-items: center; gap: 8px"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F2DCC4; color: #8A5A00; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center">R</span><span style="display: flex; flex-direction: column"><span style="font-size: 13px; font-weight: 800">Rina</span><span style="font-size: 11px; color: #4A5866; white-space: nowrap">{{ t('kouventa.1 profile · 4 channels', '1 profile · 4 channels') }}</span></span></span>
            <span style="display: flex; align-items: center; gap: 3px; color: #E8A200; padding-top: 8px; border-top: 1px solid #E3E8E6"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.30s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.42s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.54s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.66s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.78s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg></span>
        </div>
    </div>

    <!-- Mobile Vertical Layout (<= 768px) -->
    <div class="kv-sol-ce-hero-mobile" role="img" aria-label="Animated illustration: WhatsApp, Instagram, Facebook and Live Chat flow into one customer view with a five-star experience">
        <svg width="320" height="340" viewBox="0 0 320 340" fill="none" style="position: absolute; inset: 0">
            <path d="M 30 58 C 30 108, 160 92, 160 136" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="160" style="--len: 160; animation: kvDraw .7s ease 1.2s both"></path>
            <path d="M 117 58 C 117 105, 160 98, 160 136" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="120" style="--len: 120; animation: kvDraw .7s ease 1.4s both"></path>
            <path d="M 203 58 C 203 105, 160 98, 160 136" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="120" style="--len: 120; animation: kvDraw .7s ease 1.6s both"></path>
            <path d="M 290 58 C 290 108, 160 92, 160 136" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="160" style="--len: 160; animation: kvDraw .7s ease 1.8s both"></path>
            <path d="M 160 192 L 160 230" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="40" style="--len: 40; animation: kvDraw .4s ease 2.5s both"></path>
        </svg>
        <span style="position: absolute; left: 6px; top: 10px; width: 48px; height: 48px; border-radius: 14px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.2s both"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="{{ t('kouventa.Ikon Resmi WhatsApp Business API untuk Komunikasi Pelanggan Otomatis', 'Ikon Resmi WhatsApp Business API untuk Komunikasi Pelanggan Otomatis') }}" style="width: 26px; height: 26px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 93px; top: 10px; width: 48px; height: 48px; border-radius: 14px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.5s both"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="{{ t('kouventa.Ikon Kanal Pesan Instagram Direct Message (DM) Terintegrasi Kouventa', 'Ikon Kanal Pesan Instagram Direct Message (DM) Terintegrasi Kouventa') }}" style="width: 26px; height: 26px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 179px; top: 10px; width: 48px; height: 48px; border-radius: 14px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.7s both"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="{{ t('kouventa.Ikon Kanal Pesan Facebook Messenger Omnichannel Kouventa', 'Ikon Kanal Pesan Facebook Messenger Omnichannel Kouventa') }}" style="width: 26px; height: 26px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 266px; top: 10px; width: 48px; height: 48px; border-radius: 14px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.9s both"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="{{ t('kouventa.Ikon Web Live Chat Widget Interaktif Situs Web Kouventa', 'Ikon Web Live Chat Widget Interaktif Situs Web Kouventa') }}" style="width: 26px; height: 26px; object-fit: contain; display: block"></span>
        <span style="position: absolute; left: 132px; top: 136px; width: 56px; height: 56px; border-radius: 18px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 36px rgba(43,212,106,0.35); animation: kvPopIn .45s ease 2.1s both"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path></svg></span>
        <div style="position: absolute; left: 45px; top: 230px; width: 230px; box-sizing: border-box; padding: 14px 16px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 8px; animation: kvPopIn .45s ease 2.8s both">
            <span style="display: flex; align-items: center; gap: 8px"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F2DCC4; color: #8A5A00; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center">R</span><span style="display: flex; flex-direction: column"><span style="font-size: 13px; font-weight: 800">Rina</span><span style="font-size: 11px; color: #4A5866; white-space: nowrap">{{ t('kouventa.1 profile · 4 channels', '1 profile · 4 channels') }}</span></span></span>
            <span style="display: flex; align-items: center; gap: 3px; color: #E8A200; padding-top: 8px; border-top: 1px solid #E3E8E6"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.30s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.42s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.54s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.66s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.78s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg></span>
        </div>
    </div>
</div>
</div>
</section>

<!-- ============ TABS ============ -->
<section id="solution-tabs" style="width: 100%;">
<div class="kv-container" style="padding-top: 88px; padding-bottom: 104px; display: flex; flex-direction: column; gap: 40px">
<style>
/* Solution Button Active Highlight */
.kv-help-sol-btn[aria-pressed="true"] .kv-help-sol-icon {
  background: #13803F !important;
  color: #FFFFFF !important;
  box-shadow: 0 4px 12px rgba(19, 128, 63, 0.25) !important;
}
.kv-help-sol-btn[aria-pressed="true"] .kv-help-sol-icon svg {
  stroke: #FFFFFF !important;
}

/* Dedicated scenes for Sales & Marketing (Tab 0) */
#kv-ill-sales .kv-ce-sm-scene {
  position: absolute;
  inset: 0;
  padding: 24px 26px;
  box-sizing: border-box;
  display: none;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-sales .kv-ce-sm-scene.kv-active {
  display: flex !important;
  opacity: 1 !important;
}
#kv-ill-sales .kv-ce-sm-scene.kv-scene-grid.kv-active {
  display: grid !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Customer Service (Tab 1) */
#kv-ill-cs .kv-ce-cs-scene {
  position: absolute;
  inset: 0;
  padding: 24px 26px;
  box-sizing: border-box;
  display: none;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-cs .kv-ce-cs-scene.kv-active {
  display: flex !important;
  opacity: 1 !important;
}
#kv-ill-cs .kv-ce-cs-scene.kv-scene-grid.kv-active {
  display: grid !important;
  opacity: 1 !important;
}

/* Dedicated scene for Operations (Tab 2) */
#kv-ill-ops .kv-ce-ops-scene {
  position: absolute;
  inset: 0;
  padding: 24px 26px;
  box-sizing: border-box;
  display: none;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-ops .kv-ce-ops-scene.kv-active {
  display: grid !important;
  opacity: 1 !important;
}
</style>

<div role="tablist" aria-label="Solutions by role" class="kv-sol-ce-tablist">
<button type="button" role="tab" class="kv-sol-ce-tab" :aria-selected="tab === 0" @click="pickTab(0)">
<span class="kv-ce-tab-badge">{{ $roleSm['badge'] ?? 'S&M' }}</span>
<span class="kv-ce-tab-title">{{ $roleSm['name'] ?? ($isIdLocale ? 'Sales & Marketing' : 'Sales & Marketing') }}</span>
</button>
<button type="button" role="tab" class="kv-sol-ce-tab" :aria-selected="tab === 1" @click="pickTab(1)">
<span class="kv-ce-tab-badge">{{ $roleCs['badge'] ?? 'CS' }}</span>
<span class="kv-ce-tab-title">{{ $roleCs['name'] ?? ($isIdLocale ? 'Customer Service' : 'Customer Service') }}</span>
</button>
<button type="button" role="tab" class="kv-sol-ce-tab" :aria-selected="tab === 2" @click="pickTab(2)">
<span class="kv-ce-tab-badge">{{ $roleOps['badge'] ?? 'OPS' }}</span>
<span class="kv-ce-tab-title">{{ $roleOps['name'] ?? ($isIdLocale ? 'Operasional' : 'Operations') }}</span>
</button>
</div>

<template x-if="panelOn">
<div>
<!-- ===== TAB 0: Sales & Marketing ===== -->
<div role="tabpanel" x-show="tab === 0" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px">
@php
    $smChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9l5 6M21 9l-5 6"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4M12 15.5h.01"></path></svg>',
    ];
@endphp
@foreach(($roleSm['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $smChallengeIcons[$i % count($smChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleSm['solutions'][0]['title'] ?? t('kouventa.WhatsApp Broadcast', 'WhatsApp Broadcast') }}</h3>
<span class="kv-help-sol-desc">{{ $roleSm['solutions'][0]['desc'] ?? t('kouventa.Send promotional campaigns, product launches, and seasonal offers to customers at scale.', 'Send promotional campaigns, product launches, and seasonal offers to customers at scale.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleSm['solutions'][1]['title'] ?? t('kouventa.Chatbot', 'Chatbot') }}</h3>
<span class="kv-help-sol-desc">{{ $roleSm['solutions'][1]['desc'] ?? t('kouventa.Answer product and service inquiries with predefined or AI chatbots, or escalate to a human agent for more complex requests.', 'Answer product and service inquiries with predefined or AI chatbots, or escalate to a human agent for more complex requests.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleSm['solutions'][2]['title'] ?? t('kouventa.Omnichannel', 'Omnichannel') }}</h3>
<span class="kv-help-sol-desc">{{ $roleSm['solutions'][2]['desc'] ?? t('kouventa.Manage customer conversations and respond to comments from WhatsApp, Instagram, Facebook, and Live Chat in one platform.', 'Manage customer conversations and respond to comments from WhatsApp, Instagram, Facebook, and Live Chat in one platform.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="(sol === 3) || (sol === -1 && activeSol === 3)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleSm['solutions'][3]['title'] ?? t('kouventa.Authentication', 'Authentication') }}</h3>
<span class="kv-help-sol-desc">{{ $roleSm['solutions'][3]['desc'] ?? t('kouventa.Secure customer logins and transactions with fast, seamless WhatsApp OTP verification.', 'Secure customer logins and transactions with fast, seamless WhatsApp OTP verification.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(4)" :aria-pressed="(sol === 4) || (sol === -1 && activeSol === 4)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleSm['solutions'][4]['title'] ?? t('kouventa.WhatsApp Flows', 'WhatsApp Flows') }}</h3>
<span class="kv-help-sol-desc">{{ $roleSm['solutions'][4]['desc'] ?? t('kouventa.Simplify customer data collection for purchases, loyalty program registration, membership sign-ups, and more.', 'Simplify customer data collection for purchases, loyalty program registration, membership sign-ups, and more.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-sales" role="img" aria-label="Animated illustration for Sales & Marketing: WhatsApp Broadcast, AI Chatbot, Omnichannel Inbox, Authentication, and WhatsApp Flows" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- Scene 0: WhatsApp Broadcast -->
<div class="kv-ce-sm-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }" style="flex-direction: column; justify-content: center; gap: 16px">
  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between">
    <div style="display: flex; align-items: center; gap: 10px">
      <span style="width: 32px; height: 32px; border-radius: 8px; background: #25D366; display: flex; align-items: center; justify-content: center; flex-shrink: 0">
        <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 18px; height: 18px; object-fit: contain">
      </span>
      <div style="display: flex; flex-direction: column">
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Kampanye Siaran Resmi' : 'Official Broadcast Campaign' }}</span>
        <span style="font-size: 10.5px; color: #64748B">{{ $isIdLocale ? 'WhatsApp Business API Terverifikasi' : 'Verified WhatsApp Business API' }}</span>
      </div>
    </div>
    <span style="font-size: 11px; font-weight: 700; color: #13803F; background: #EAF7EF; border: 1px solid rgba(19, 128, 63, 0.2); padding: 3px 9px; border-radius: 999px">● Live Broadcast</span>
  </div>

  <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 16px 18px; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04); display: flex; flex-direction: column; gap: 10px">
    <div style="background: #071C33; color: #FFFFFF; border-radius: 10px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center">
      <span style="font-size: 12.5px; font-weight: 800; letter-spacing: 0.04em">SUMMER SPECIAL · 20% OFF</span>
      <span style="font-size: 11px; color: #2BD46A; font-weight: 700">CODE: SUMMER20</span>
    </div>
    <div style="font-size: 13px; line-height: 1.55; color: #1E293B">
      {{ $isIdLocale ? 'Halo Kak Rina! Koleksi terbaru sneakers musim panas sudah rilis. Dapatkan promo diskon eksklusif 20% khusus pelanggan setia hari ini.' : 'Hi Rina! Our newest summer sneakers collection is now live. Enjoy an exclusive 20% discount reserved for our loyal customers today.' }}
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px solid #F1F5F9">
      <span style="font-size: 12px; font-weight: 700; color: #13803F; display: flex; align-items: center; gap: 4px">
        {{ $isIdLocale ? 'Lihat Katalog Promo →' : 'Shop Featured Catalog →' }}
      </span>
      <span style="font-size: 11px; color: #64748B; display: flex; align-items: center; gap: 4px">
        10:00 <span style="color: #2563EB; font-weight: 800">✓✓</span>
      </span>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px">
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px">
      <span style="width: 26px; height: 26px; border-radius: 50%; background: #C9E8D3; color: #0B5E2C; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">RP</span>
      <div style="display: flex; flex-direction: column; overflow: hidden">
        <span style="font-size: 11px; font-weight: 700; color: #0E1A24; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">Rina P.</span>
        <span style="font-size: 9.5px; color: #13803F; font-weight: 600">Read ✓✓</span>
      </div>
    </div>
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px">
      <span style="width: 26px; height: 26px; border-radius: 50%; background: #CFDCEB; color: #1E3A8A; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">DA</span>
      <div style="display: flex; flex-direction: column; overflow: hidden">
        <span style="font-size: 11px; font-weight: 700; color: #0E1A24; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">Dimas A.</span>
        <span style="font-size: 9.5px; color: #13803F; font-weight: 600">Read ✓✓</span>
      </div>
    </div>
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px">
      <span style="width: 26px; height: 26px; border-radius: 50%; background: #F2DCC4; color: #854D0E; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">SW</span>
      <div style="display: flex; flex-direction: column; overflow: hidden">
        <span style="font-size: 11px; font-weight: 700; color: #0E1A24; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">Sinta W.</span>
        <span style="font-size: 9.5px; color: #64748B; font-weight: 600">Delivered ✓✓</span>
      </div>
    </div>
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px">
      <span style="width: 26px; height: 26px; border-radius: 50%; background: #DCD3EE; color: #581C87; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">YH</span>
      <div style="display: flex; flex-direction: column; overflow: hidden">
        <span style="font-size: 11px; font-weight: 700; color: #0E1A24; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">Yoga H.</span>
        <span style="font-size: 9.5px; color: #64748B; font-weight: 600">Delivered ✓✓</span>
      </div>
    </div>
  </div>
</div>

<!-- Scene 1: Chatbot -->
<div class="kv-ce-sm-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }" style="flex-direction: column; justify-content: center; gap: 14px">
  <div style="align-self: flex-end; max-width: 82%; background: #071C33; color: #FFFFFF; padding: 12px 16px; border-radius: 16px 16px 4px 16px; font-size: 13.5px; line-height: 1.5; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.08); animation: kvIn .35s ease .1s both">
    <div>{{ $isIdLocale ? 'Halo! Apakah Runner Sneakers warna Blue size 42 ready stock?' : 'Hi! Is Runner Sneakers in blue size 42 in stock?' }}</div>
    <div style="font-size: 10px; color: rgba(255,255,255,0.6); text-align: right; margin-top: 4px">10:15</div>
  </div>

  <div style="align-self: flex-start; max-width: 90%; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px 16px 16px 4px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 4px 14px rgba(7, 28, 51, 0.04); animation: kvIn .35s ease .3s both">
    <div style="display: flex; justify-content: space-between; align-items: center">
      <span style="font-size: 12px; font-weight: 800; color: #13803F; display: flex; align-items: center; gap: 6px">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2BD46A"></span>
        Chatbot Agent
      </span>
      <span style="font-size: 10px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 2px 8px; border-radius: 999px">0.8s response</span>
    </div>

    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 12px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px">
      <div style="display: flex; align-items: center; gap: 10px">
        <span style="width: 38px; height: 38px; border-radius: 8px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 16px">👟</span>
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 12.5px; font-weight: 800; color: #0E1A24">Runner Sneakers · Blue</span>
          <span style="font-size: 10.5px; color: #64748B">SKU #SNK-042 · Size 42</span>
        </div>
      </div>
      <div style="text-align: right">
        <div style="font-size: 12.5px; font-weight: 800; color: #0E1A24">Rp 899.000</div>
        <span style="font-size: 10px; font-weight: 700; color: #13803F">✓ Ready (3 pairs)</span>
      </div>
    </div>

    <div style="font-size: 12.5px; line-height: 1.5; color: #334155">
      {{ $isIdLocale ? 'Halo Kak! Blue size 42 ready stock 3 pasang lagi. Mau kami bantu simpankan sebelum kehabisan?' : 'Yes! Blue size 42 is in stock with 3 pairs left. Would you like me to hold a pair for you?' }}
    </div>
  </div>

  <div style="align-self: flex-end; max-width: 82%; background: #071C33; color: #FFFFFF; padding: 12px 16px; border-radius: 16px 16px 4px 16px; font-size: 13.5px; line-height: 1.5; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.08); animation: kvIn .35s ease .5s both">
    <div>{{ $isIdLocale ? 'Boleh, tolong bantu pesankan sekarang ya 👍' : 'Yes please, I will take it! Help me order. 👍' }}</div>
    <div style="font-size: 10px; color: rgba(255,255,255,0.6); text-align: right; margin-top: 4px">10:16</div>
  </div>
</div>

<!-- Scene 2: Omnichannel -->
<div class="kv-ce-sm-scene kv-scene-grid" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }" style="grid-template-columns: 160px 32px minmax(0, 1fr) !important; align-items: center; justify-content: space-between">
  <div style="display: flex; flex-direction: column; gap: 8px">
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 16px; height: 16px; object-fit: contain"> WhatsApp
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #EAF7EF; color: #13803F; padding: 2px 6px; border-radius: 999px">14</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 16px; height: 16px; object-fit: contain"> Instagram
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #F3E8FF; color: #6B21A8; padding: 2px 6px; border-radius: 999px">8</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 16px; height: 16px; object-fit: contain"> Facebook
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #EFF6FF; color: #1D4ED8; padding: 2px 6px; border-radius: 999px">5</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-livechat.png') }}" alt="Live Chat" style="width: 16px; height: 16px; object-fit: contain"> Live Chat
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 999px">3</span>
    </div>
  </div>

  <div style="display: flex; flex-direction: column; gap: 28px">
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .2s infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .4s infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .6s infinite"></span></div>
  </div>

  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px; padding: 14px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04)">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 6px; border-bottom: 1px solid #E3E8E6">
      <span style="font-size: 13px; font-weight: 800; color: #0E1A24">One Inbox</span>
      <span style="font-size: 10.5px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 2px 8px; border-radius: 999px">Synced 100%</span>
    </div>
    <div style="display: flex; flex-direction: column; gap: 8px">
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Rina · Is this available?</span>
          <span style="font-size: 10px; color: #64748B">Sneakers Runner Blue (42)</span>
        </div>
        <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 15px; height: 15px; object-fit: contain">
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Sari · Comment on post</span>
          <span style="font-size: 10px; color: #64748B">Bisa kirim hari ini ke Bandung?</span>
        </div>
        <img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 15px; height: 15px; object-fit: contain">
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Budi · Price question</span>
          <span style="font-size: 10px; color: #64748B">Estimasi ongkos kirim Surabaya</span>
        </div>
        <img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 15px; height: 15px; object-fit: contain">
      </div>
    </div>
  </div>
</div>

<!-- Scene 3: Authentication -->
<div class="kv-ce-sm-scene" :class="{ 'kv-active': (sol === 3) || (sol === -1 && activeSol === 3) }" style="flex-direction: column; justify-content: center; gap: 16px; max-width: 480px; margin: 0 auto">
  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04)">
    <div style="display: flex; align-items: center; justify-content: space-between">
      <div style="display: flex; align-items: center; gap: 10px">
        <span style="width: 32px; height: 32px; border-radius: 8px; background: #25D366; display: flex; align-items: center; justify-content: center">
          <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 18px; height: 18px; object-fit: contain">
        </span>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Verifikasi WhatsApp OTP' : 'WhatsApp OTP Verification' }}</span>
      </div>
      <span style="font-size: 11px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 3px 9px; border-radius: 999px">2FA Secure</span>
    </div>

    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; gap: 8px">
      <span style="font-size: 12.5px; color: #475569">
        {{ $isIdLocale ? 'Kode rahasia verifikasi login Anda adalah:' : 'Your secure login verification code is:' }}
      </span>
      <div style="display: flex; gap: 8px; justify-content: center; padding: 8px 0">
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">4</span>
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">8</span>
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">2</span>
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">9</span>
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">1</span>
        <span style="width: 38px; height: 44px; border-radius: 8px; background: #F1F5F9; border: 1.5px solid #13803F; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #0E1A24; font-family: 'JetBrains Mono', monospace">7</span>
      </div>
      <span style="font-size: 11px; color: #94A3B8; text-align: center">
        {{ $isIdLocale ? 'Berlaku 5 menit. Jangan bagikan kode ini kepada siapa pun.' : 'Valid for 5 mins. Do not share this code with anyone.' }}
      </span>
    </div>

    <div style="background: #13803F; color: #FFFFFF; border-radius: 12px; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; font-weight: 700">
      <span style="display: flex; align-items: center; gap: 8px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
        {{ $isIdLocale ? 'Nomor Terverifikasi Berhasil' : 'WhatsApp Number Verified' }}
      </span>
      <span style="font-size: 11px; opacity: 0.85">0.6s</span>
    </div>
  </div>
</div>

<!-- Scene 4: WhatsApp Flows -->
<div class="kv-ce-sm-scene" :class="{ 'kv-active': (sol === 4) || (sol === -1 && activeSol === 4) }" style="flex-direction: column; justify-content: center; gap: 14px; max-width: 480px; margin: 0 auto">
  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 18px; padding: 20px; display: flex; flex-direction: column; gap: 12px; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04)">
    <div style="display: flex; justify-content: space-between; align-items: center">
      <div style="display: flex; align-items: center; gap: 8px">
        <span style="width: 28px; height: 28px; border-radius: 8px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 12px">⚡</span>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">WhatsApp Flows · Member Sign-Up</span>
      </div>
      <span style="font-size: 10.5px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 2px 8px; border-radius: 999px">In-Chat Form</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px">
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 11px; color: #64748B; font-weight: 600">{{ $isIdLocale ? 'Nama Lengkap' : 'Full Name' }}</span>
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">Rani Pratama</span>
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 11px; color: #64748B; font-weight: 600">{{ $isIdLocale ? 'Nomor WhatsApp' : 'WhatsApp No.' }}</span>
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">+62 811-9876-5432</span>
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 11px; color: #64748B; font-weight: 600">{{ $isIdLocale ? 'Kategori Favorit' : 'Preference' }}</span>
        <span style="font-size: 11.5px; font-weight: 800; color: #13803F">Sneakers & Apparel</span>
      </div>
    </div>

    <div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center">
      <div style="display: flex; flex-direction: column">
        <span style="font-size: 10px; font-weight: 700; color: #2BD46A; letter-spacing: 0.08em">KOUVENTA VIP CLUB</span>
        <span style="font-size: 14px; font-weight: 800">RANI PRATAMA</span>
        <span style="font-size: 10.5px; color: #94A3B8; font-family: 'JetBrains Mono', monospace">#KV-MEMBER-8891</span>
      </div>
      <span style="font-size: 11.5px; font-weight: 800; background: #2BD46A; color: #06251A; padding: 6px 12px; border-radius: 999px">
        ✓ Registered
      </span>
    </div>
  </div>
</div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px">
@php
    $smImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 12 0V3h-4v7a2 2 0 0 1-4 0V3Z"></path><path d="M6 7h4M14 7h4"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg>',
    ];
@endphp
@foreach(($roleSm['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $smImpactIcons[$i % count($smImpactIcons)] !!}</span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ is_array($imp) ? ($imp['title'] ?? $imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 1: Customer Service ===== -->
<div role="tabpanel" x-show="tab === 1" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $csCeChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1.5"></rect><rect x="3" y="11" width="18" height="5" rx="1.5"></rect><path d="M7 20h10"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg>',
    ];
@endphp
@foreach(($roleCs['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $csCeChallengeIcons[$i % count($csCeChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleCs['solutions'][0]['title'] ?? t('kouventa.Omnichannel', 'Omnichannel') }}</h3>
<span class="kv-help-sol-desc">{{ $roleCs['solutions'][0]['desc'] ?? t('kouventa.Centralize customer complaints from WhatsApp, Instagram, Facebook, and Live Chat in one platform, making it easier to track and respond to every issue.', 'Centralize customer complaints from WhatsApp, Instagram, Facebook, and Live Chat in one platform, making it easier to track and respond to every issue.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleCs['solutions'][1]['title'] ?? t('kouventa.Chatbot', 'Chatbot') }}</h3>
<span class="kv-help-sol-desc">{{ $roleCs['solutions'][1]['desc'] ?? t('kouventa.Handle common customer complaints instantly with predefined or AI-powered chatbots, while escalating complex issues to human agents when needed.', 'Handle common customer complaints instantly with predefined or AI-powered chatbots, while escalating complex issues to human agents when needed.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M13 6v2M13 11v2M13 16v2"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleCs['solutions'][2]['title'] ?? t('kouventa.Ticket Creation', 'Ticket Creation') }}</h3>
<span class="kv-help-sol-desc">{{ $roleCs['solutions'][2]['desc'] ?? t('kouventa.Create support tickets from customer complaints, reducing manual work and minimizing errors or miscommunication during issue handling.', 'Create support tickets from customer complaints, reducing manual work and minimizing errors or miscommunication during issue handling.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="(sol === 3) || (sol === -1 && activeSol === 3)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleCs['solutions'][3]['title'] ?? t('kouventa.CSAT Survey', 'CSAT Survey') }}</h3>
<span class="kv-help-sol-desc">{{ $roleCs['solutions'][3]['desc'] ?? t('kouventa.Automatically collect customer feedback after complaint resolution to measure satisfaction and identify opportunities to improve service quality.', 'Automatically collect customer feedback after complaint resolution to measure satisfaction and identify opportunities to improve service quality.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-cs" role="img" aria-label="Animated illustration for Customer Service: Omnichannel Support Queue, AI Chatbot Resolution, Ticket Creation Escalation, and CSAT Survey Rating" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- Scene 0: Omnichannel -->
<div class="kv-ce-cs-scene kv-scene-grid" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }" style="grid-template-columns: 160px 32px minmax(0, 1fr) !important; align-items: center; justify-content: space-between">
  <div style="display: flex; flex-direction: column; gap: 8px">
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 16px; height: 16px; object-fit: contain"> WhatsApp
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #FEE2E2; color: #991B1B; padding: 2px 6px; border-radius: 999px">P1</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 16px; height: 16px; object-fit: contain"> Instagram
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #FEF3C7; color: #92400E; padding: 2px 6px; border-radius: 999px">P2</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 16px; height: 16px; object-fit: contain"> Facebook
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #FEF3C7; color: #92400E; padding: 2px 6px; border-radius: 999px">P2</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.04)">
      <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 6px">
        <img src="{{ theme_asset('images/icon-livechat.png') }}" alt="Live Chat" style="width: 16px; height: 16px; object-fit: contain"> Live Chat
      </span>
      <span style="font-size: 10.5px; font-weight: 800; background: #EAF7EF; color: #13803F; padding: 2px 6px; border-radius: 999px">P3</span>
    </div>
  </div>

  <div style="display: flex; flex-direction: column; gap: 28px">
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .2s infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .4s infinite"></span></div>
    <div style="height: 2px; background: #E2E8F0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 26px; animation: kvTravel .9s linear .6s infinite"></span></div>
  </div>

  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px; padding: 14px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04)">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 6px; border-bottom: 1px solid #E3E8E6">
      <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Antrean Keluhan Terpusat' : 'Centralized Support Queue' }}</span>
      <span style="font-size: 10.5px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 2px 8px; border-radius: 999px">Active SLA</span>
    </div>
    <div style="display: flex; flex-direction: column; gap: 8px">
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-left: 3px solid #EF4444; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Order #8821 · Double charge</span>
          <span style="font-size: 10px; color: #64748B">SLA: 15m remaining</span>
        </div>
        <img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 15px; height: 15px; object-fit: contain">
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-left: 3px solid #F59E0B; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Order #8804 · Wrong item size</span>
          <span style="font-size: 10px; color: #64748B">SLA: 42m remaining</span>
        </div>
        <img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 15px; height: 15px; object-fit: contain">
      </div>
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-left: 3px solid #10B981; border-radius: 10px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 700; color: #0E1A24">Invoice inquiry · Tax copy</span>
          <span style="font-size: 10px; color: #64748B">SLA: 1h 20m remaining</span>
        </div>
        <img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 15px; height: 15px; object-fit: contain">
      </div>
    </div>
  </div>
</div>

<!-- Scene 1: Chatbot -->
<div class="kv-ce-cs-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }" style="flex-direction: column; justify-content: center; gap: 14px">
  <div style="align-self: flex-end; max-width: 82%; background: #071C33; color: #FFFFFF; padding: 12px 16px; border-radius: 16px 16px 4px 16px; font-size: 13.5px; line-height: 1.5; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.08); animation: kvIn .35s ease .1s both">
    <div>{{ $isIdLocale ? 'Dimana pesanan saya? Order #KV-4421 belum bergerak sejak kemarin.' : 'Where is my order? Order #KV-4421 has not moved since yesterday.' }}</div>
    <div style="font-size: 10px; color: rgba(255,255,255,0.6); text-align: right; margin-top: 4px">09:40</div>
  </div>

  <div style="align-self: flex-start; max-width: 90%; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px 16px 16px 4px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 4px 14px rgba(7, 28, 51, 0.04); animation: kvIn .35s ease .3s both">
    <div style="display: flex; justify-content: space-between; align-items: center">
      <span style="font-size: 12px; font-weight: 800; color: #13803F; display: flex; align-items: center; gap: 6px">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2BD46A"></span>
        AI Support Chatbot
      </span>
      <span style="font-size: 10px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 2px 8px; border-radius: 999px">Instant Resolution</span>
    </div>

    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px">
      <div style="display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">Express Delivery · #EXP-8891024</span>
        <span style="font-size: 10.5px; font-weight: 700; color: #13803F">Out for Delivery</span>
      </div>
      <div style="font-size: 11px; color: #475569">
        {{ $isIdLocale ? 'Paket sedang diantar oleh Kurir Doni. Estimasi tiba hari ini pukul 14:00 - 16:00.' : 'Package is on delivery route with Courier Doni. Estimated arrival today 14:00 - 16:00.' }}
      </div>
    </div>

    <div style="font-size: 12.5px; line-height: 1.5; color: #334155">
      {{ $isIdLocale ? 'Halo Kak! Paket Anda dalam keadaan aman dan sedang dalam perjalanan kurir hari ini.' : 'Hello! Your package is safe and on the final delivery route today.' }}
    </div>
  </div>

  <div style="align-self: flex-end; max-width: 82%; background: #071C33; color: #FFFFFF; padding: 10px 14px; border-radius: 16px 16px 4px 16px; font-size: 13px; line-height: 1.5; box-shadow: 0 4px 12px rgba(7, 28, 51, 0.08); animation: kvIn .35s ease .5s both">
    <div>{{ $isIdLocale ? 'Wah terima kasih banyak infonya, sangat cepat! 👍' : 'Awesome, thank you for the super fast update! 👍' }}</div>
    <div style="font-size: 10px; color: rgba(255,255,255,0.6); text-align: right; margin-top: 4px">09:41</div>
  </div>
</div>

<!-- Scene 2: Ticket Creation -->
<div class="kv-ce-cs-scene kv-scene-grid" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }" style="grid-template-columns: minmax(0, 1fr) 250px !important; align-items: center; gap: 16px">
  <div style="display: flex; flex-direction: column; gap: 12px">
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px">
      <div style="display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 12px; font-weight: 800; color: #7C3AED; display: flex; align-items: center; gap: 6px">
          <img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 14px; height: 14px; object-fit: contain">
          Instagram Direct Message
        </span>
        <span style="font-size: 10.5px; font-weight: 700; color: #9A3412; background: #FFEDD5; padding: 2px 7px; border-radius: 999px">Needs Action</span>
      </div>
      <div style="font-size: 12.5px; font-weight: 700; color: #0E1A24">
        {{ $isIdLocale ? 'Saya terpotong dua kali untuk pembayaran pesanan #8821 via kartu kredit.' : 'I was charged twice for order #8821 payment via credit card.' }}
      </div>
      <div style="font-size: 11px; color: #64748B">
        {{ $isIdLocale ? 'AI mendeteksi keluhan keuangan kompleks ➔ Meneruskan ke tiket tim Billing' : 'AI detected complex billing dispute ➔ Escalated to Billing ticket queue' }}
      </div>
    </div>

    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; gap: 10px">
      <span style="width: 30px; height: 30px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">DW</span>
      <div style="display: flex; flex-direction: column">
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">Dewi · Finance & Billing Support</span>
        <span style="font-size: 10.5px; color: #13803F; font-weight: 600">Assigned & Reviewing</span>
      </div>
    </div>
  </div>

  <div style="background: #071C33; color: #FFFFFF; border-radius: 16px; padding: 16px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 10px 30px rgba(7, 28, 51, 0.12)">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px">
      <span style="font-size: 11px; font-weight: 800; color: #2BD46A; letter-spacing: 0.08em">TICKET CREATED</span>
      <span style="font-size: 10px; font-weight: 800; background: #EF4444; color: #FFFFFF; padding: 2px 7px; border-radius: 999px">P1 · High</span>
    </div>
    <div style="display: flex; flex-direction: column; gap: 7px; font-size: 11.5px">
      <div style="display: flex; justify-content: space-between">
        <span style="color: #94A3B8">Ticket ID</span>
        <strong style="font-family: 'JetBrains Mono', monospace">#KV-8921</strong>
      </div>
      <div style="display: flex; justify-content: space-between">
        <span style="color: #94A3B8">Category</span>
        <strong>Refund & Billing</strong>
      </div>
      <div style="display: flex; justify-content: space-between">
        <span style="color: #94A3B8">SLA Target</span>
        <strong style="color: #FBBF24">2 Hours</strong>
      </div>
      <div style="display: flex; flex-direction: column; gap: 2px; padding-top: 4px; border-top: 1px solid rgba(255,255,255,0.08)">
        <span style="color: #94A3B8">Summary</span>
        <strong style="font-size: 11px; line-height: 1.35">Double charge discrepancy on Order #8821</strong>
      </div>
    </div>
  </div>
</div>

<!-- Scene 3: CSAT Survey -->
<div class="kv-ce-cs-scene" :class="{ 'kv-active': (sol === 3) || (sol === -1 && activeSol === 3) }" style="flex-direction: column; justify-content: center; gap: 14px; max-width: 460px; margin: 0 auto">
  <div style="background: #EAF7EF; border: 1px solid rgba(19, 128, 63, 0.2); border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between">
    <span style="font-size: 12px; font-weight: 700; color: #13803F; display: flex; align-items: center; gap: 6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
      {{ $isIdLocale ? 'Masalah Selesai · Tiket #KV-8921 Ditutup' : 'Issue Resolved · Ticket #KV-8921 Closed' }}
    </span>
    <span style="font-size: 10.5px; color: #13803F; font-weight: 800">100% SLA Met</span>
  </div>

  <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 18px; padding: 20px; display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; box-shadow: 0 8px 24px rgba(7, 28, 51, 0.04)">
    <span style="font-size: 13.5px; font-weight: 800; color: #0E1A24">
      {{ $isIdLocale ? 'Bagaimana kualitas layanan kami hari ini?' : 'How would you rate our service today?' }}
    </span>
    <div style="display: flex; gap: 6px; font-size: 30px; line-height: 1; color: #EAB308">
      <span style="animation: kvStar .3s ease .1s both">★</span>
      <span style="animation: kvStar .3s ease .2s both">★</span>
      <span style="animation: kvStar .3s ease .3s both">★</span>
      <span style="animation: kvStar .3s ease .4s both">★</span>
      <span style="animation: kvStar .3s ease .5s both">★</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px">
      <span style="font-size: 14px; font-weight: 800; color: #13803F">5.0 / 5.0</span>
      <span style="font-size: 11.5px; color: #64748B">· {{ $isIdLocale ? 'Sangat Puas' : 'Very Satisfied' }}</span>
    </div>
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 8px 12px; font-size: 11.5px; color: #334155; font-style: italic">
      "{{ $isIdLocale ? 'Respon sangat cepat dan masalah langsung tuntas. Luar biasa!' : 'Super fast response and resolved immediately. Great support!' }}"
    </div>
  </div>
</div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat({{ min(3, max(1, count($roleCs['impacts'] ?? []))) }}, minmax(0, 1fr)); gap: 16px">
@php
$csImpactIcons = [
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"></path></svg>',
];
@endphp
@foreach(($roleCs['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ 0.20 + ($i * 0.15) }}s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $csImpactIcons[$i % count($csImpactIcons)] !!}</span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ $imp['title'] ?? '' }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ $imp['desc'] ?? '' }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 2: Operations ===== -->
<div role="tabpanel" x-show="tab === 2" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat({{ min(2, max(1, count($roleOps['challenges'] ?? []))) }}, minmax(0, 1fr)); gap: 16px">
@php
$opsChallengeIcons = [
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 2.6.5 4.4 1.2 5.7M17 17H3s3-2 3-9c0-.8.1-1.5.4-2.2"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0M2 2l20 20"></path></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-7"></path></svg>',
];
@endphp
@foreach(($roleOps['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ 0.15 + ($i * 0.15) }}s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $opsChallengeIcons[$i % count($opsChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ $ch['text'] ?? '' }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $roleOps['solutions'][0]['title'] ?? t('kouventa.Cross Platform Integrations', 'Cross Platform Integrations') }}</h3>
<span class="kv-help-sol-desc">{{ $roleOps['solutions'][0]['desc'] ?? t('kouventa.Trigger WhatsApp messages from business systems based on operational events, such as order confirmations, delivery updates, payment reminders, and appointment notifications.', 'Trigger WhatsApp messages from business systems based on operational events, such as order confirmations, delivery updates, payment reminders, and appointment notifications.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-ops" role="img" aria-label="Animated illustration for Operations: order, delivery, payment and schedule events in business systems automatically trigger WhatsApp updates on a customer phone" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div class="kv-ce-ops-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }" style="display: grid; grid-template-columns: 140px 80px minmax(0, 1fr) !important; align-items: center; justify-content: space-between">
<div style="display: flex; flex-direction: column; gap: 36px">
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease .3s both">Order</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 1.5s both">Delivery</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 2.7s both">Payment</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 3.9s both">Schedule</span>
</div>
<div style="display: flex; flex-direction: column; gap: 76px">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 70px; animation: kvTravel .6s linear .4s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 70px; animation: kvTravel .6s linear 1.6s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 70px; animation: kvTravel .6s linear 2.8s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 70px; animation: kvTravel .6s linear 4.0s 1 both"></span></div>
</div>
<div style="justify-self: center; width: 250px; height: 430px; background: #071C33; border-radius: 34px; padding: 10px; box-sizing: border-box">
<div style="width: 100%; height: 100%; background: #EEF2F0; border-radius: 26px; overflow: hidden; display: flex; flex-direction: column">
<div style="background: #0C2A48; color: #FFFFFF; padding: 18px 14px 10px; font-size: 13px; font-weight: 700">Your Brand</div>
<div style="padding: 10px; display: flex; flex-direction: column; gap: 8px; font-size: 12px">
<span style="align-self: flex-start; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 12px 12px 12px; display: flex; gap: 8px; animation: kvIn .35s ease 1.0s both">Order confirmed<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 12px 12px 12px; display: flex; gap: 8px; animation: kvIn .35s ease 2.2s both">Out for delivery<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 12px 12px 12px; display: flex; gap: 8px; animation: kvIn .35s ease 3.4s both">Payment reminder<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 12px 12px 12px; display: flex; gap: 8px; animation: kvIn .35s ease 4.6s both">Appointment tomorrow<span style="color: #13803F; font-weight: 800">✓✓</span></span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat({{ min(2, max(1, count($roleOps['impacts'] ?? []))) }}, minmax(0, 1fr)); gap: 16px">
@php
$opsImpactIcons = [
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg>',
    '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.6-1 2.9 1 2.9-2.5 1.6-.9 2.9-3-.2L12 20l-2.4-1.8-3 .2-.9-2.9-2.5-1.6 1-2.9-1-2.9 2.5-1.6.9-2.9 3 .2Z"></path><path d="M8.5 11.5l2.5 2.5 4.5-5"></path></svg>',
];
@endphp
@foreach(($roleOps['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ 0.20 + ($i * 0.15) }}s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $opsImpactIcons[$i % count($opsImpactIcons)] !!}</span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ $imp['title'] ?? '' }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ $imp['desc'] ?? '' }}</span>
</div>
@endforeach
</div>
</div>
</div>
</div>
</template>
</div>
</section>

<!-- ============ CLOSING CTA ============ -->
<section style="width: 100%;">
<div class="kv-container" style="padding-bottom: 104px">
<div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
<h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">{{ $bottomCtaTitle }}</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55">{{ $bottomCtaSubtitle }}</p>
</div>
<div style="display: flex; gap: 12px; flex-shrink: 0; flex-wrap: wrap">
<a href="{{ localized_url($ctaPrimary['url'] ?? '/book-demo') }}" class="kv-btn-action kv-btn-navy" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ $ctaPrimary['text'] ?? ($isIdLocale ? 'Minta Demo' : 'Request a Demo') }}</a>
<a href="{{ $ctaSecondary['url'] ?? '#whatsapp' }}" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-outline" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; text-decoration: none; cursor: pointer">{{ $ctaSecondary['text'] ?? ($isIdLocale ? 'Hubungi sales' : 'Talk to Sales') }}</a>
</div>
</div>
</div>
</section>

</div>
@endsection
