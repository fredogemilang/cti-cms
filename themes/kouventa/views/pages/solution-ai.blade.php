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
        'prefix' => $isIdLocale ? 'Dari pengetahuan ke aksi' : 'From knowledge to action',
        'main' => $isIdLocale ? 'dengan AI.' : 'with AI.',
    ];
    $heroSubtitle = rtrim($sHero['subtitle'] ?? ($isIdLocale
        ? 'Dari pencarian pengetahuan hingga otomasi proses, Kouventa AI membantu bisnis mengubah informasi menjadi insight yang bisa ditindaklanjuti dan mengotomasi proses bisnis dengan agent AI yang otonom.'
        : 'From knowledge retrieval to process automation, Kouventa AI helps businesses turn information into actionable insights and automate business processes with AI-powered autonomous agents.'), '.');
    $heroCta = $sHero['primary_cta'] ?? [
        'text' => $isIdLocale ? 'Minta Demo' : 'Request a Demo',
        'url' => '/book-demo',
    ];
    $heroSecondaryBtn = $sHero['secondary_btn'] ?? [
        'text' => $isIdLocale ? 'Lihat Cara Kerjanya' : 'See How It Works',
    ];

    // Section 2: Departments
    $sDepts = $page?->section('departments', []);
    $deptCs = $sDepts['cs'] ?? [];
    $deptRec = $sDepts['recruitment'] ?? [];
    $deptSales = $sDepts['sales_marketing'] ?? [];
    $deptOps = $sDepts['operations'] ?? [];
    $deptPp = $sDepts['payment_prioritization'] ?? [];
    $deptBr = $sDepts['bank_reconciliation'] ?? [];
    $deptLegal = $sDepts['legal_compliance'] ?? [];

    // Section 3: Journey
    $sJourney = $page?->section('journey', []);
    $journeyTitle = $sJourney['title'] ?? ($isIdLocale ? 'Tahapan implementasi' : 'Implementation journey');
    $journeySteps = $sJourney['steps'] ?? [];

    // Section 4: Closing CTA
    $sCta = $page?->section('cta', []);
    $bottomCtaTitle = rtrim($sCta['title'] ?? ($isIdLocale ? 'Mulai dari yang sederhana. Kembangkan dengan yakin.' : 'Start simple. Scale with confidence.'), '.');
    $bottomCtaSubtitle = rtrim($sCta['subtitle'] ?? ($isIdLocale
        ? 'Mulai dengan AI untuk pekerjaan sehari-hari karyawan, dengan tata kelola dari organisasi Anda. Kembangkan ke agent AI saat Anda siap.'
        : "Begin with everyday AI for your employees, governed by your organization. Grow into AI agents when you're ready."), '.');
    $ctaPrimary = $sCta['primary_btn'] ?? ['text' => ($isIdLocale ? 'Temukan use case pertama Anda' : 'Find your first use case'), 'url' => '#solution-tabs'];
    $ctaSecondary = $sCta['secondary_btn'] ?? ['text' => ($isIdLocale ? 'Hubungi sales' : 'Talk to Sales'), 'url' => '#whatsapp'];
@endphp
<div class="kv-canvas-wrap" x-data="{
    tab: {{ $initialTab ?? 0 }},
    sol: -1,
    activeSol: 0,
    _autoTimer: null,
    panelOn: true,
    stepOn: Array({{ max(4, count($journeySteps ?? [])) }}).fill(true),
    replayed: Array({{ max(4, count($journeySteps ?? [])) }}).fill(false),
    implSeen: false,
    _lockT: null,
    tabSlugs: {
        0: 'customer-service',
        1: '{{ $isIdLocale ? 'rekrutmen' : 'recruitment' }}',
        2: 'sales-marketing',
        3: '{{ $isIdLocale ? 'operasional-bisnis' : 'business-operations' }}',
        4: 'finance',
        5: '{{ $isIdLocale ? 'rekonsiliasi-bank' : 'bank-reconciliation' }}',
        6: 'legal-compliance'
    },
    baseSolutionPath: '{{ rtrim(localized_url('/solutions/ai'), '/') }}',
    getTabUrl(i) {
        const slug = this.tabSlugs[i] || 'customer-service';
        const base = this.baseSolutionPath.replace(/\/+$/, '');
        return `${base}/${slug}/`;
    },
    aiSols: {
        0: [{ delay: 1.0, dur: 1.6 }, { delay: 2.8, dur: 2.8 }],
        1: [{ delay: 0.2, dur: 3.4 }, { delay: 3.9, dur: 1.1 }, { delay: 5.0, dur: 1.4 }, { delay: 6.5, dur: 1.5 }],
        2: [{ delay: 0.3, dur: 3.7 }, { delay: 4.2, dur: 1.5 }, { delay: 5.8, dur: 2.2 }],
        3: [{ delay: 1.5, dur: 1.7 }, { delay: 3.2, dur: 3.4 }],
        4: [{ delay: 0.2, dur: 7.4 }],
        5: [{ delay: 0.3, dur: 1.6 }, { delay: 2.0, dur: 1.8 }, { delay: 4.1, dur: 3.0 }, { delay: 7.1, dur: 1.0 }],
        6: [{ delay: 0.3, dur: 2.2 }, { delay: 2.5, dur: 1.5 }, { delay: 4.1, dur: 2.5 }]
    },
    aiIllIds: {
        0: 'kv-ill-cs',
        1: 'kv-ill-rec',
        2: 'kv-ill-sales',
        3: 'kv-ill-ops',
        4: 'kv-ill-pp',
        5: 'kv-ill-br',
        6: 'kv-ill-legal'
    },
    startAutoTimer() {
        if (this._autoTimer) clearTimeout(this._autoTimer);
        const scheduleNext = () => {
            if (this.sol !== -1) return;
            let duration = 5800;
            if (this.tab === 0) {
                // Scene 0: 5800ms, Scene 1: 6500ms
                duration = (this.activeSol === 0) ? 5800 : 6500;
            } else if (this.tab === 1) {
                duration = (this.activeSol === 1) ? 5800 : 5500;
            } else if (this.tab === 2 || this.tab === 3 || this.tab === 5 || this.tab === 6) {
                duration = 5800;
            }
            this._autoTimer = setTimeout(() => {
                if (this.sol === -1) {
                    const count = (this.aiSols[this.tab] || []).length;
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
            if (path.includes('/customer-service') || path.includes('/dukungan-pelanggan')) return 0;
            if (path.includes('/recruitment') || path.includes('/rekrutmen')) return 1;
            if (path.includes('/sales-marketing') || path.includes('/penjualan-pemasaran')) return 2;
            if (path.includes('/business-operations') || path.includes('/operasional-bisnis')) return 3;
            if (path.includes('/bank-reconciliation') || path.includes('/rekonsiliasi-bank')) return 5;
            if (path.includes('/finance') || path.includes('/payment-prioritization') || path.includes('/prioritas-pembayaran')) return 4;
            if (path.includes('/legal-compliance') || path.includes('/legal')) return 6;

            const p = u.searchParams;
            const raw = p.get('dept') !== null ? p.get('dept') : p.get('tab');
            if (raw !== null) {
                let v = parseInt(raw, 10);
                if (!isNaN(v) && v >= 0 && v <= 6) return v;
            }
        } catch(e) {}
        return {{ $initialTab ?? 0 }};
    },
    pickTab(i, scroll = false, updateUrl = true) {
        clearTimeout(this._lockT);
        this.panelOn = false;
        this.tab = i;
        this.sol = -1;
        this.activeSol = 0;
        this.startAutoTimer();
        if (updateUrl && window.history && window.history.pushState) {
            const targetUrl = this.getTabUrl(i);
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
    replayStep(i) {
        this.stepOn[i] = false;
        this.replayed[i] = true;
        this.implSeen = true;
        setTimeout(() => { this.stepOn[i] = true; }, 40);
    },
    init() {
        this.tab = this.parseTabFromUrl();
        this.sol = -1;
        this.activeSol = 0;
        this.panelOn = true;
        this.startAutoTimer();

        // If landing with dept, tab, clean subpage, or #solution-tabs, scroll smoothly to tabs section
        const p = new URLSearchParams(window.location.search);
        const path = window.location.pathname.toLowerCase();
        const hasSubSlug = path.includes('/customer-service') || path.includes('/recruitment') || path.includes('/rekrutmen') || path.includes('/sales-marketing') || path.includes('/business-operations') || path.includes('/operasional-bisnis') || path.includes('/finance') || path.includes('/bank-reconciliation') || path.includes('/rekonsiliasi-bank') || path.includes('/legal-compliance');
        if (hasSubSlug || p.has('dept') || p.has('tab') || window.location.hash.includes('solution-tabs')) {
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
                if (u.pathname.includes('/solutions/ai')) {
                    const target = this.parseTabFromUrl(u.href);
                    if (target !== undefined && target !== null && target >= 0 && target <= 6) {
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
            clearTimeout(this._lockT);
            this.panelOn = false;
            this.tab = this.parseTabFromUrl();
            this.sol = -1;
            setTimeout(() => { this.panelOn = true; }, 40);
            const el = document.getElementById('solution-tabs');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        const triggerImplSeen = () => {
            if (this.implSeen) return;
            this.implSeen = true;
            if (io) {
                try { io.disconnect(); } catch(e) {}
            }
            window.removeEventListener('scroll', checkScroll);
            window.removeEventListener('touchmove', checkScroll);
        };

        const checkScroll = () => {
            if (this.implSeen) return;
            const el = document.getElementById('impl-journey');
            if (!el) { triggerImplSeen(); return; }
            const rect = el.getBoundingClientRect();
            const vh = window.innerHeight || document.documentElement.clientHeight || 800;
            if (rect.top <= vh + 250 && rect.bottom >= -200) {
                triggerImplSeen();
            }
        };

        let io = null;
        if (typeof IntersectionObserver !== 'undefined') {
            try {
                io = new IntersectionObserver((entries) => {
                    if (entries.some(e => e.isIntersecting || (e.intersectionRatio && e.intersectionRatio > 0))) {
                        triggerImplSeen();
                    }
                }, { threshold: [0, 0.05], rootMargin: '150px 0px 150px 0px' });
            } catch (err) {}
        }

        const observeEl = () => {
            const el = document.getElementById('impl-journey');
            if (el) {
                if (io) {
                    try { io.observe(el); } catch(e) {}
                }
                checkScroll();
            } else {
                triggerImplSeen();
            }
        };

        this.$nextTick(() => {
            observeEl();
            setTimeout(observeEl, 60);
        });

        window.addEventListener('scroll', checkScroll, { passive: true });
        window.addEventListener('touchmove', checkScroll, { passive: true });

        setTimeout(triggerImplSeen, 2000);
    }
}">

<!-- ============ NAV + HERO ============ -->
<section class="kv-hero-container" style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Solutions" style="height: 56px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4; width: 100%;">
<div class="kv-container" style="height: 56px; display: flex; align-items: center; gap: 32px;">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ $heroSubnavLabel }}</span>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ $heroSubnavAi }}</a>
<a href="{{ localized_url('/solutions/customer-engagement') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ $heroSubnavCe }}</a>
</div>
</nav>

<div class="kv-container kv-hero-section" style="height: 704px; box-sizing: border-box; display: grid; grid-template-columns: minmax(0, 1fr) 440px; gap: 64px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? '' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? '' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">{{ $heroSubtitle }}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">{{ $heroCta['text'] ?? 'Request a Demo' }}</a>
<button type="button" @click="pickTab(tab, true)" style="background: transparent; color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28)">{{ $heroSecondaryBtn['text'] ?? 'See How It Works' }}</button>
</div>
</div>

<!-- header illustration: one AI hub powering agents for each team; tasks complete in turn -->
<div class="kv-sol-ai-hero-wrap">
    <!-- Desktop Constellation Layout (> 768px) -->
    <div class="kv-sol-ai-hero-desktop" role="img" aria-label="Animated illustration: Kouventa AI powers an agent for each team, and tasks are completed in turn for Customer Service, Recruitment, Sales and Marketing, Business Operations, Finance, and Legal and Compliance">
        <svg width="440" height="288" viewBox="0 0 440 288" style="position: absolute; left: 0; top: 0" aria-hidden="true">
            <line x1="156" y1="34" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="156" y1="34" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 1s infinite"></line>
            <line x1="284" y1="34" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="284" y1="34" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 3s infinite"></line>
            <line x1="156" y1="144" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="156" y1="144" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 5s infinite"></line>
            <line x1="284" y1="144" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="284" y1="144" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 7s infinite"></line>
            <line x1="156" y1="254" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="156" y1="254" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 9s infinite"></line>
            <line x1="284" y1="254" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="284" y1="254" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 11s infinite"></line>
        </svg>
        <span style="position: absolute; left: 182px; top: 106px; width: 76px; height: 76px; border-radius: 50%; background: #2BD46A; color: #06251A; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; animation: kvPopIn .4s ease .6s both, kvHubPulse 2s ease-out 1s infinite"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3Z"></path><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"></path></svg><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em">AI AGENTS</span></span>
        <span style="position: absolute; left: 0; top: 12px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.10s both, kvRoleHi 12s ease 1s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>{{ t('kouventa.Customer Service', 'Customer Service') }}</span>
        <span style="position: absolute; right: 0; top: 12px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.18s both, kvRoleHi 12s ease 3s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg></span>{{ t('kouventa.Recruitment', 'Recruitment') }}</span>
        <span style="position: absolute; left: 0; top: 122px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.26s both, kvRoleHi 12s ease 5s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg></span>{{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}</span>
        <span style="position: absolute; right: 0; top: 122px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.34s both, kvRoleHi 12s ease 7s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>{{ t('kouventa.Business Ops', 'Business Ops') }}</span>
        <span style="position: absolute; left: 0; top: 232px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.42s both, kvRoleHi 12s ease 9s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H5a2 2 0 0 1 0-4h13v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h15V7"></path><path d="M16 14h.01"></path></svg></span>{{ t('kouventa.Finance', 'Finance') }}</span>
        <span style="position: absolute; right: 0; top: 232px; width: 156px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.50s both, kvRoleHi 12s ease 11s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>{{ t('kouventa.Legal & Compliance', 'Legal & Compliance') }}</span>
        <div style="position: absolute; left: 0; right: 0; top: 304px; display: grid">
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 1s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.CUSTOMER SERVICE AGENT · DONE', 'CUSTOMER SERVICE AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Ticket created and assigned to Billing', 'Ticket created and assigned to Billing') }}</span></span></span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 3s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.RECRUITING AGENT · DONE', 'RECRUITING AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Top candidates shortlisted', 'Top candidates shortlisted') }}</span></span></span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 5s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.SALES & MARKETING AGENT · DONE', 'SALES & MARKETING AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Order status sent to customer', 'Order status sent to customer') }}</span></span></span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 7s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.BUSINESS OPS AGENT · DONE', 'BUSINESS OPS AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Answer found in the SOP', 'Answer found in the SOP') }}</span></span></span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 9s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.FINANCE AGENT · DONE', 'FINANCE AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Payment batch ready to approve', 'Payment batch ready to approve') }}</span></span></span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 11s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.LEGAL & COMPLIANCE AGENT · DONE', 'LEGAL & COMPLIANCE AGENT · DONE') }}</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">{{ t('kouventa.Key clauses flagged for review', 'Key clauses flagged for review') }}</span></span></span>
        </div>
    </div>

    <!-- Mobile Hexagonal / Orbit Layout (<= 768px) -->
    <div class="kv-sol-ai-hero-mobile" role="img" aria-label="Animated illustration: Kouventa AI powers an agent for each team, and tasks are completed in turn for Customer Service, Recruitment, Sales and Marketing, Business Operations, Finance, and Legal and Compliance">
        <!-- SVG Connecting Lines -->
        <svg width="330" height="290" viewBox="0 0 330 290" style="position: absolute; left: 0; top: 0; pointer-events: none" aria-hidden="true">
            <!-- Base lines -->
            <line x1="125" y1="52" x2="146" y2="124" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="205" y1="52" x2="184" y2="124" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="128" y1="145" x2="136" y2="145" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="202" y1="145" x2="194" y2="145" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="125" y1="238" x2="146" y2="166" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <line x1="205" y1="238" x2="184" y2="166" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line>
            <!-- Active glowing lines -->
            <line x1="125" y1="52" x2="146" y2="124" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 1s infinite"></line>
            <line x1="205" y1="52" x2="184" y2="124" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 3s infinite"></line>
            <line x1="128" y1="145" x2="136" y2="145" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 5s infinite"></line>
            <line x1="202" y1="145" x2="194" y2="145" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 7s infinite"></line>
            <line x1="125" y1="238" x2="146" y2="166" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 9s infinite"></line>
            <line x1="205" y1="238" x2="184" y2="166" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 11s infinite"></line>
        </svg>

        <!-- Center AI Hub -->
        <span style="position: absolute; left: 136px; top: 116px; width: 58px; height: 58px; border-radius: 50%; background: #2BD46A; color: #06251A; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; box-shadow: 0 0 20px rgba(43,212,106,0.35); animation: kvPopIn .4s ease .6s both, kvHubPulse 2s ease-out 1s infinite; z-index: 2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3Z"></path>
                <path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"></path>
            </svg>
            <span style="font-size: 8px; font-weight: 800; letter-spacing: 0.05em">AI AGENTS</span>
        </span>

        <!-- Top 2 Roles -->
        <span style="position: absolute; left: 8px; top: 10px; width: 148px; height: 42px; box-sizing: border-box; padding: 0 8px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.10s both, kvRoleHi 12s ease 1s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>
            {{ t('kouventa.Customer Service', 'Customer Service') }}
        </span>

        <span style="position: absolute; right: 8px; top: 10px; width: 148px; height: 42px; box-sizing: border-box; padding: 0 8px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.18s both, kvRoleHi 12s ease 3s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg></span>
            {{ t('kouventa.Recruitment', 'Recruitment') }}
        </span>

        <!-- Middle 2 Roles (Sides) -->
        <span style="position: absolute; left: 6px; top: 124px; width: 122px; height: 42px; box-sizing: border-box; padding: 0 6px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 5px; font-size: 10.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.26s both, kvRoleHi 12s ease 5s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg></span>
            {{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}
        </span>

        <span style="position: absolute; right: 6px; top: 124px; width: 122px; height: 42px; box-sizing: border-box; padding: 0 6px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 5px; font-size: 10.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.34s both, kvRoleHi 12s ease 7s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>
            {{ t('kouventa.Business Ops', 'Business Ops') }}
        </span>

        <!-- Bottom 2 Roles -->
        <span style="position: absolute; left: 8px; top: 238px; width: 148px; height: 42px; box-sizing: border-box; padding: 0 8px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.42s both, kvRoleHi 12s ease 9s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H5a2 2 0 0 1 0-4h13v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h15V7"></path><path d="M16 14h.01"></path></svg></span>
            {{ t('kouventa.Finance', 'Finance') }}
        </span>

        <span style="position: absolute; right: 8px; top: 238px; width: 148px; height: 42px; box-sizing: border-box; padding: 0 8px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.50s both, kvRoleHi 12s ease 11s infinite; z-index: 2">
            <span style="color: #2BD46A; display: flex"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
            {{ t('kouventa.Legal & Compliance', 'Legal & Compliance') }}
        </span>

        <!-- Bottom Sequential Toasts -->
        <div style="position: absolute; left: 0; right: 0; top: 298px; display: grid; z-index: 3">
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 1s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.CUSTOMER SERVICE AGENT · DONE', 'CUSTOMER SERVICE AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Ticket created and assigned to Billing', 'Ticket created and assigned to Billing') }}</span></span>
            </span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 3s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.RECRUITING AGENT · DONE', 'RECRUITING AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Top candidates shortlisted', 'Top candidates shortlisted') }}</span></span>
            </span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 5s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.SALES & MARKETING AGENT · DONE', 'SALES & MARKETING AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Order status sent to customer', 'Order status sent to customer') }}</span></span>
            </span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 7s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.BUSINESS OPS AGENT · DONE', 'BUSINESS OPS AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Answer found in the SOP', 'Answer found in the SOP') }}</span></span>
            </span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 9s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.FINANCE AGENT · DONE', 'FINANCE AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Payment batch ready to approve', 'Payment batch ready to approve') }}</span></span>
            </span>
            <span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 12px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 10px 24px rgba(0,0,0,0.25); max-width: calc(100% - 16px); box-sizing: border-box; opacity: 0; animation: kvToast 12s ease 11s infinite">
                <span style="width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>
                <span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; color: #13803F; white-space: nowrap">{{ t('kouventa.LEGAL & COMPLIANCE AGENT · DONE', 'LEGAL & COMPLIANCE AGENT · DONE') }}</span><span style="font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Key clauses flagged for review', 'Key clauses flagged for review') }}</span></span>
            </span>
        </div>
    </div>
</div>
</div>
</section>

<!-- ============ TABS ============ -->
<section id="solution-tabs" style="width: 100%;">
<div class="kv-container" style="padding-top: 88px; padding-bottom: 104px; display: flex; flex-direction: column; gap: 40px">
<div style="display: flex; flex-direction: column; gap: 14px">
<div role="tablist" aria-label="Solutions by role" class="kv-sol-ai-tablist">
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 0) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 0" @click.prevent="pickTab(0)">{{ $deptCs['name'] ?? ($isIdLocale ? 'Customer Service' : 'Customer Service') }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 1) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 1" @click.prevent="pickTab(1)">{{ $deptRec['name'] ?? ($isIdLocale ? 'Rekrutmen' : 'Recruitment') }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 2) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 2" @click.prevent="pickTab(2)">{{ $deptSales['name'] ?? ($isIdLocale ? 'Sales & Marketing' : 'Sales & Marketing') }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 3) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 3" @click.prevent="pickTab(3)">{{ $deptOps['name'] ?? ($isIdLocale ? 'Operasional Bisnis' : 'Business Operations') }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 4) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 4 || tab === 5" @click.prevent="if (tab !== 4 && tab !== 5) pickTab(4)">{{ $isIdLocale ? 'Finance' : 'Finance' }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 6) }}" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 6" @click.prevent="pickTab(6)">{{ $deptLegal['name'] ?? ($isIdLocale ? 'Legal & Compliance' : 'Legal & Compliance') }}</a>
</div>

<!-- Finance Sub-tabs Pill -->
<div role="tablist" aria-label="Finance solutions" x-show="tab === 4 || tab === 5" class="kv-sol-fin-wrap">
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 4) }}" role="tab" class="kv-sol-fin-tab" :aria-selected="tab === 4" @click.prevent="pickTab(4)">{{ $deptPp['name'] ?? ($isIdLocale ? 'Prioritas pembayaran' : 'Payment Prioritization') }}</a>
<a href="{{ \Themes\Kouventa\Handlers\SolutionRouteHandler::url('ai', 5) }}" role="tab" class="kv-sol-fin-tab" :aria-selected="tab === 5" @click.prevent="pickTab(5)">{{ $deptBr['name'] ?? ($isIdLocale ? 'Rekonsiliasi Bank' : 'Bank Reconciliation') }}</a>
</div>
</div>

<template x-if="panelOn">
<div>
<!-- ===== TAB 0: Customer Service ===== -->
<div role="tabpanel" x-show="tab === 0" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $csChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1.5"></rect><rect x="3" y="11" width="18" height="5" rx="1.5"></rect><path d="M7 20h10"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="8" height="8" rx="2"></rect><rect x="14" y="13" width="8" height="8" rx="2"></rect><path d="M14 7h4a2 2 0 0 1 2 2v1M10 17H6a2 2 0 0 1-2-2v-1"></path><path d="M18 8l2 2 2-2M6 16l-2-2-2 2"></path></svg>',
    ];
@endphp
@foreach(($deptCs['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $csChallengeIcons[$i % count($csChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptCs['solutions'][0]['title'] ?? t('kouventa.Customer Support Agent', 'Customer Support Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptCs['solutions'][0]['desc'] ?? t('kouventa.Understand customer questions in context, provide accurate information from your business knowledge, and resolve routine inquiries automatically.', 'Understand customer questions in context, provide accurate information from your business knowledge, and resolve routine inquiries automatically.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M13 6v2M13 11v2M13 16v2"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptCs['solutions'][1]['title'] ?? t('kouventa.Ticket Management Agent', 'Ticket Management Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptCs['solutions'][1]['desc'] ?? t('kouventa.Automatically create support tickets from customer conversations, capture the necessary details, and assign them to the right team.', 'Automatically create support tickets from customer conversations, capture the necessary details, and assign them to the right team.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-cs" role="img" aria-label="Animated illustration: a routine question is answered with a knowledge source and resolved, while a complex complaint becomes a ticket assigned to the right team" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
@php
    $isIdLocale = app()->getLocale() === 'id';
@endphp

<style>
/* Active Solution Button: Solid green badge with pure white icon for crisp separation */
.kv-help-sol-btn[aria-pressed="true"] .kv-help-sol-icon {
  background: #13803F !important;
  color: #FFFFFF !important;
  box-shadow: 0 4px 12px rgba(19, 128, 63, 0.25) !important;
}
.kv-help-sol-btn[aria-pressed="true"] .kv-help-sol-icon svg {
  stroke: #FFFFFF !important;
}

/* Rigid 2-column layout for CS illustration scenes */
#kv-ill-cs .kv-cs-scene {
  position: absolute;
  inset: 0;
  padding: 22px 24px;
  box-sizing: border-box;
  display: none;
  grid-template-columns: 270px 266px !important;
  justify-content: space-between !important;
  align-items: center !important;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-cs .kv-cs-scene.kv-active {
  display: grid !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Recruitment illustrations */
#kv-ill-rec .kv-rec-scene {
  position: absolute;
  inset: 0;
  padding: 22px 24px;
  box-sizing: border-box;
  display: none;
  flex-direction: column;
  justify-content: space-between;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-rec .kv-rec-scene.kv-active {
  display: flex !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Sales & Marketing illustrations */
#kv-ill-sales .kv-sales-scene {
  position: absolute;
  inset: 0;
  padding: 22px 24px;
  box-sizing: border-box;
  display: none;
  grid-template-columns: 270px 266px !important;
  justify-content: space-between !important;
  align-items: center !important;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-sales .kv-sales-scene.kv-active {
  display: grid !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Business Operations illustrations */
#kv-ill-ops .kv-ops-scene {
  position: absolute;
  inset: 0;
  padding: 22px 24px;
  box-sizing: border-box;
  display: none;
  grid-template-columns: 270px 266px !important;
  justify-content: space-between !important;
  align-items: center !important;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-ops .kv-ops-scene.kv-active {
  display: grid !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Bank Reconciliation illustrations */
#kv-ill-br .kv-br-scene {
  position: absolute;
  inset: 0;
  padding: 24px 26px;
  box-sizing: border-box;
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 14px;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-br .kv-br-scene.kv-active {
  display: flex !important;
  opacity: 1 !important;
}

/* Dedicated scenes for Legal & Compliance illustrations */
#kv-ill-legal .kv-legal-scene {
  position: absolute;
  inset: 0;
  padding: 22px 24px;
  box-sizing: border-box;
  display: none;
  grid-template-columns: 270px 266px !important;
  justify-content: space-between !important;
  align-items: center !important;
  opacity: 0;
  transition: opacity 0.35s ease;
}
#kv-ill-legal .kv-legal-scene.kv-active {
  display: grid !important;
  opacity: 1 !important;
}
.kv-arsir-pattern {
  background: repeating-linear-gradient(-45deg, #E2E8F0, #E2E8F0 3px, #F8FAFA 3px, #F8FAFA 7px);
}
@keyframes kvTypingDot {
  0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
  40% { transform: translateY(-4px); opacity: 1; }
}
@keyframes kvDashLeft {
  from { stroke-dashoffset: 0; }
  to { stroke-dashoffset: 18; }
}
@keyframes kvDashRight {
  from { stroke-dashoffset: 18; }
  to { stroke-dashoffset: 0; }
}
@keyframes kvBarFill {
  from { width: 0%; }
}
@keyframes kvMatchDoc {
  0% { border-color: #CBD5E1; background: #F8FAFA; transform: scale(0.98); }
  50% { border-color: #13803F; background: #FFFFFF; transform: scale(1.02); box-shadow: 0 8px 24px rgba(19, 128, 63, 0.14); }
  100% { border-color: #13803F; background: #FFFFFF; transform: scale(1); box-shadow: 0 4px 16px rgba(19, 128, 63, 0.08); }
}
@keyframes kvMatchDocBlue {
  0% { border-color: #CBD5E1; background: #F8FAFA; transform: scale(0.98); }
  50% { border-color: #0284C7; background: #FFFFFF; transform: scale(1.02); box-shadow: 0 8px 24px rgba(2, 132, 199, 0.16); }
  100% { border-color: #0284C7; background: #FFFFFF; transform: scale(1); box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08); }
}
@keyframes kvMorphOut {
  0% { opacity: 1; transform: scale(1); }
  100% { opacity: 0; transform: scale(0.85); }
}
@keyframes kvMorphIn {
  0% { opacity: 0; transform: scale(0.6); }
  65% { opacity: 1; transform: scale(1.03); }
  100% { opacity: 1; transform: scale(1); }
}
</style>

<!-- SCENE 0: Customer Support Agent (Left: Background Process Canvas, Right: Customer WhatsApp Card) -->
<div class="kv-cs-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }">

  <!-- LEFT: Background Knowledge Retrieval Process (Hidden initially until arrow arrives at 1.9s) -->
  <div style="width: 270px; height: 430px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 1.9s both;">
    
    <!-- Background Process Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 22px; height: 22px; border-radius: 6px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M6 6h10M6 10h10M6 14h6"></path></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Proses Basis Pengetahuan' : 'Knowledge Base Pipeline' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0B5E2C; background: #EAF7EF; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F"></span>
        Vector RAG
      </span>
    </div>

    <!-- Document Repository Stack (Arsir / Hatched schematic items) -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center; padding: 8px 0">

      <!-- 1. Hatched Document: Katalog_Produk_2026.pdf (Background/Unmatched) -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.95s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 11px; font-weight: 700; color: #64748B">Katalog_Produk_2026.pdf</span>
          </div>
          <span style="font-size: 9px; font-weight: 600; color: #94A3B8">98 chunks</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px">
          <span class="kv-arsir-pattern" style="height: 6px; width: 90%; border-radius: 3px; display: block"></span>
          <span class="kv-arsir-pattern" style="height: 6px; width: 65%; border-radius: 3px; display: block"></span>
        </div>
      </div>

      <!-- 2. Target Matched Document: SOP_Operasional_Toko.pdf (Lights up on match!) -->
      <div style="border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px 14px; background: #F8FAFA; display: flex; flex-direction: column; gap: 8px; transition: all 0.3s ease; animation: kvIn .35s ease 2.0s both, kvMatchDoc .5s ease 2.2s 1 forwards">
        <!-- Document Title & Header -->
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <span style="width: 22px; height: 22px; border-radius: 5px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </span>
            <span style="font-size: 12px; font-weight: 800; color: #0E1A24">SOP_Operasional_Toko.pdf</span>
          </div>
          <span style="font-size: 9px; font-weight: 700; color: #13803F; background: #DDF3E4; padding: 1px 6px; border-radius: 4px">SOP</span>
        </div>

        <!-- Badges (99.4% Match, Confidence 99%) -->
        <div style="display: flex; align-items: center; gap: 6px; animation: kvPopIn .35s ease 2.4s both">
          <span style="font-size: 10px; font-weight: 800; color: #0B5E2C; background: #EAF7EF; border: 1px solid #C9E8D3; padding: 2px 7px; border-radius: 5px; display: inline-flex; align-items: center; gap: 3px">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            99.4% Match
          </span>
          <span style="font-size: 10px; font-weight: 800; color: #1D4ED8; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 2px 7px; border-radius: 5px">
            Confidence 99%
          </span>
        </div>

        <!-- Extracted Text Snippet -->
        <div style="background: #F8FAFA; border-left: 3px solid #13803F; border-radius: 6px; padding: 7px 10px; display: flex; flex-direction: column; gap: 3px; animation: kvIn .35s ease 2.6s both">
          <span style="font-size: 10px; font-weight: 800; color: #13803F">{{ $isIdLocale ? 'Bagian 3.1 · Jam Operasional' : 'Section 3.1 · Store Hours' }}</span>
          <span style="font-size: 11px; line-height: 1.4; color: #1E293B; font-weight: 500">
            {{ $isIdLocale ? 'Seluruh gerai beroperasi normal setiap hari dari jam 08:00 sampai 21:00 tanpa libur, termasuk akhir pekan dan tanggal merah.' : 'All store branches operate daily from 08:00 to 21:00 without exception, including weekends and public holidays.' }}
          </span>
        </div>
      </div>

      <!-- 3. Hatched Document: Kebijakan_Garansi_Retur.docx (Background/Unmatched) -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 2.05s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 11px; font-weight: 700; color: #64748B">Kebijakan_Garansi_Retur.docx</span>
          </div>
          <span style="font-size: 9px; font-weight: 600; color: #94A3B8">64 chunks</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px">
          <span class="kv-arsir-pattern" style="height: 6px; width: 85%; border-radius: 3px; display: block"></span>
          <span class="kv-arsir-pattern" style="height: 6px; width: 55%; border-radius: 3px; display: block"></span>
        </div>
      </div>

    </div>
  </div>

  <!-- Animated Dashed Connector Line (Signal flows from behind WhatsApp Card to SOP Document) -->
  <svg style="position: absolute; left: 245px; top: 215px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 1.5s both, kvOut .25s ease 3.15s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#13803F" />
  </svg>

  <!-- RIGHT: Customer WhatsApp Chat Card (Foreground Interface - Sits on top with z-index: 5) -->
  <div style="width: 266px; height: 430px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; overflow: hidden; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2z"></path></svg>
        </span>
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Pelanggan · WhatsApp' : 'Customer · WhatsApp' }}</span>
          <span style="font-size: 9px; font-weight: 600; color: #16A34A">● online</span>
        </div>
      </div>
      <span style="display: grid; font-size: 10px; font-weight: 800">
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #E3E8E6; color: #4A5866">{{ $isIdLocale ? 'Baru' : 'New' }}</span>
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 3.55s both">{{ $isIdLocale ? 'Selesai' : 'Resolved' }}</span>
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center">
      <!-- 1. Customer Message (Pops in at 0.45s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 9px 12px; display: flex; flex-direction: column; gap: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.45s both">
        <span style="font-size: 12px; font-weight: 600; line-height: 1.45; color: #0E1A24">
          {{ $isIdLocale ? 'Halo kak! Apakah hari ini tokonya buka? Tutupnya jam berapa ya?' : 'Hi there! Are you guys open today? What time do you close?' }}
        </span>
        <span style="align-self: flex-end; font-size: 9px; font-weight: 600; color: #94A3B8">10:14 AM</span>
      </div>

      <!-- 2 & 3: Typing Indicator morphs into AI Agent Response in the EXACT SAME slot! -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        
        <!-- Typing Indicator (Sits at top-left of this slot, appears at 0.9s, morphs out at 3.15s) -->
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 10px 14px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; opacity: 0; animation: kvIn .3s ease 0.9s both, kvMorphOut .2s ease 3.15s 1 forwards">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <!-- AI Agent Response (Occupies the exact same slot, expands/morphs out from the typing origin at 3.2s) -->
        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 12px 12px 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; border: 1px solid #C9E8D3; box-shadow: 0 3px 10px rgba(19, 128, 63, 0.08); transform-origin: top left; opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 3.2s both">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 11px; font-weight: 800; color: #13803F">{{ $isIdLocale ? 'Customer Support Agent' : 'Customer Support Agent' }}</span>
            <span style="font-size: 9px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 1px 5px; border-radius: 4px">0.3s</span>
          </div>
          <span style="font-size: 12px; line-height: 1.45; color: #0E1A24">
            {{ $isIdLocale ? 'Halo! Iya kak, seluruh gerai kami buka setiap hari jam 08:00–21:00. Ditunggu kedatangannya ya! 😊' : 'Hi! Yes, we\'re open every day from 08:00 to 21:00 across all outlets. See you soon! 😊' }}
          </span>
          <span style="align-self: flex-start; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 5px; background: #DDF3E4; color: #0B5E2C; display: inline-flex; align-items: center; gap: 4px">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            SOP_Operasional_Toko.pdf
          </span>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- SCENE 1: Ticket Management Agent (Left: Background Triage & Routing Canvas, Right: Customer Live Chat Card) -->
<div class="kv-cs-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">

  <!-- LEFT: Background Triage & Routing Process (Open canvas without outer card border) -->
  <div style="width: 270px; height: 430px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 1.70s both;">

    <!-- Background Process Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 22px; height: 22px; border-radius: 6px; background: #F0F9FF; color: #0284C7; display: flex; align-items: center; justify-content: center">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Triase & Klasifikasi AI' : 'AI Triage & Routing Engine' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0369A1; background: #E0F2FE; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #0284C7"></span>
        Auto-Triage
      </span>
    </div>

    <!-- Main Triage Pipeline Stack (Staggered 1 per 1) -->
    <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; justify-content: center; padding: 4px 0">

      <!-- 1. Knowledge Base Policy Check (1.90s) & Complexity Flag (2.20s) -->
      <div style="border: 1px solid #CBD5E1; border-radius: 9px; padding: 7px 10px; background: #F8FAFA; display: flex; flex-direction: column; gap: 4px; animation: kvIn .35s ease 1.90s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 5px">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 10px; font-weight: 700; color: #475569">SOP_Refund_Retur.pdf</span>
          </div>
          <span style="font-size: 8.5px; font-weight: 700; color: #DC2626; background: #FEE2E2; padding: 1px 5px; border-radius: 3px">{{ $isIdLocale ? 'Di Luar SOP' : 'Out-of-SOP' }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 5px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 5px; padding: 3px 6px; opacity: 0; animation: kvPopIn .3s ease 2.20s both">
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706; flex-shrink: 0"></span>
          <span style="font-size: 9.5px; font-weight: 800; color: #92400E">{{ $isIdLocale ? 'Keluhan Kompleks · Butuh Eskalasi Tim' : 'Complex Issue · Needs Team Escalation' }}</span>
        </div>
      </div>

      <!-- 2. Structured Ticket Card Generated (2.55s) -->
      <div style="background: #071C33; color: #FFFFFF; border-radius: 10px; padding: 8px 12px; display: flex; flex-direction: column; gap: 4px; font-size: 9.5px; box-shadow: 0 4px 14px rgba(7,28,51,0.12); opacity: 0; animation: kvIn .35s ease 2.55s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.05em; color: #2BD46A">TICKET #KV-8921</span>
          <span style="font-size: 8.5px; font-weight: 800; color: #FCA5A5; background: rgba(239, 68, 68, 0.25); padding: 1px 5px; border-radius: 4px">{{ $isIdLocale ? 'P1 · Tinggi (SLA 2j)' : 'P1 · High (SLA 2h)' }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; color: #CBD5E1">
          <span>{{ $isIdLocale ? 'Kategori' : 'Category' }}:</span>
          <strong style="color: #FFFFFF">Refund & Billing</strong>
        </div>
        <div style="display: flex; justify-content: space-between; color: #CBD5E1">
          <span>{{ $isIdLocale ? 'Ringkasan' : 'Summary' }}:</span>
          <strong style="color: #FFFFFF">{{ $isIdLocale ? 'Keterlambatan refund + dispute fee' : 'Delayed refund + fee discrepancy' }}</strong>
        </div>
      </div>

      <!-- 3. Animated Routing Indicator from Ticket to Department (2.90s) -->
      <div style="display: flex; align-items: center; justify-content: center; gap: 5px; padding: 1px 0; opacity: 0; animation: kvIn .3s ease 2.90s both">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2.5"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
        <span style="font-size: 9px; font-weight: 800; color: #0284C7; letter-spacing: 0.04em; text-transform: uppercase">{{ $isIdLocale ? 'Assign ke Tim Relevan' : 'Route to Best-Fit Team' }}</span>
      </div>

      <!-- 4. Multi-Department Routing Choices (3.00s) -->
      <div style="display: flex; flex-direction: column; gap: 4px; opacity: 0; animation: kvIn .35s ease 3.00s both">

        <!-- Dept 1: General CS Ops (Dimmed) -->
        <div style="border: 1px dashed #CBD5E1; border-radius: 6px; padding: 4px 8px; background: #F8FAFA; opacity: 0.55; display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 9.5px; font-weight: 600; color: #64748B">1. CS General Operations</span>
          <span style="font-size: 8.5px; color: #94A3B8">—</span>
        </div>

        <!-- Dept 2: Billing & Finance Team (TARGET - lights up at 3.35s!) -->
        <div style="border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 6px 9px; background: #F8FAFA; display: flex; flex-direction: column; gap: 2px; transition: all 0.3s ease; animation: kvMatchDocBlue .5s ease 3.35s 1 forwards">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="display: flex; align-items: center; gap: 6px">
              <span style="width: 18px; height: 18px; border-radius: 4px; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800">BIL</span>
              <span style="font-size: 11px; font-weight: 800; color: #0C4A6E">{{ $isIdLocale ? 'Tim Billing & Finance' : 'Billing & Finance Team' }}</span>
            </div>
            <span style="font-size: 8.5px; font-weight: 800; color: #0284C7; background: #E0F2FE; padding: 1px 5px; border-radius: 4px">Target Queue</span>
          </div>
          <div style="display: flex; align-items: center; gap: 4px; padding-left: 24px; opacity: 0; animation: kvPopIn .3s ease 3.50s both">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="font-size: 9px; font-weight: 700; color: #0284C7">{{ $isIdLocale ? 'Ditugaskan ke antrean tim (SLA 2 jam)' : 'Assigned to team queue (SLA 2h)' }}</span>
          </div>
        </div>

        <!-- Dept 3: Logistics & Warehouse (Dimmed) -->
        <div style="border: 1px dashed #CBD5E1; border-radius: 6px; padding: 4px 8px; background: #F8FAFA; opacity: 0.55; display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 9.5px; font-weight: 600; color: #64748B">{{ $isIdLocale ? '3. Logistik & Pergudangan' : '3. Logistics & Warehouse' }}</span>
          <span style="font-size: 8.5px; color: #94A3B8">—</span>
        </div>

      </div>

    </div>
  </div>

  <!-- Animated Dashed Connector Line (Blue signal flows from behind Live Chat Card to Background Triage) -->
  <svg style="position: absolute; left: 245px; top: 185px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 1.40s both, kvOut .25s ease 3.70s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#0284C7" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#0284C7" />
  </svg>

  <!-- RIGHT: Customer Live Chat Card (Foreground Interface) -->
  <div style="width: 266px; height: 430px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; overflow: hidden; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #0284C7; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </span>
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Pelanggan · Live Chat' : 'Customer · Live Chat' }}</span>
          <span style="font-size: 9px; font-weight: 600; color: #0284C7">● live</span>
        </div>
      </div>
      <span style="display: grid; font-size: 10px; font-weight: 800">
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #E3E8E6; color: #4A5866">{{ $isIdLocale ? 'Baru' : 'New' }}</span>
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvPopIn .3s ease 4.10s both">{{ $isIdLocale ? 'Tiket Dibuat' : 'Ticket Created' }}</span>
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center">
      <!-- 1. Customer Complaint Message (0.45s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 9px 12px; display: flex; flex-direction: column; gap: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.45s both">
        <span style="font-size: 12px; font-weight: 600; line-height: 1.45; color: #0E1A24">
          {{ $isIdLocale ? 'Dana refund saya belum masuk setelah 2 minggu, dan ada potongan biaya yang tidak sesuai.' : 'My refund still hasn\'t arrived after two weeks, and I was charged an extra fee.' }}
        </span>
        <span style="align-self: flex-end; font-size: 9px; font-weight: 600; color: #94A3B8">10:15 AM</span>
      </div>

      <!-- 2 & 3: Typing Indicator morphs into Agent Confirmation in the EXACT SAME slot! -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        
        <!-- Typing Indicator (0.90s to 3.70s) -->
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 10px 14px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; opacity: 0; animation: kvIn .3s ease 0.90s both, kvMorphOut .2s ease 3.70s 1 forwards">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <!-- System Escalation Response (3.75s) -->
        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 12px 12px 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; border: 1px solid #BAE6FD; box-shadow: 0 3px 10px rgba(2, 132, 199, 0.08); transform-origin: top left; opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 3.75s both">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 11px; font-weight: 800; color: #0284C7">{{ $isIdLocale ? 'Ticket Management Agent' : 'Ticket Management Agent' }}</span>
            <span style="font-size: 9px; font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 1px 5px; border-radius: 4px">0.4s</span>
          </div>
          <span style="font-size: 12px; line-height: 1.45; color: #0E1A24">
            {{ $isIdLocale ? 'Keluhan Anda telah kami buatkan tiket resmi dan dialihkan ke Tim Billing & Finance untuk investigasi langsung.' : 'Your issue has been converted to an official ticket and routed to our Billing & Finance Team for immediate investigation.' }}
          </span>
          <span style="align-self: flex-start; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 5px; background: #FEF3C7; color: #92400E; display: inline-flex; align-items: center; gap: 4px">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
            TICKET #KV-8921 · Escalated
          </span>
        </div>

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
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $csImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
    ];
@endphp
@foreach(($deptCs['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $csImpactIcons[$i % count($csImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 1: Recruitment ===== -->
<div role="tabpanel" x-show="tab === 1" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $recChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="8" height="8" rx="2"></rect><rect x="14" y="13" width="8" height="8" rx="2"></rect><path d="M14 7h4a2 2 0 0 1 2 2v1M10 17H6a2 2 0 0 1-2-2v-1"></path><path d="M18 8l2 2 2-2M6 16l-2-2-2 2"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg>',
    ];
@endphp
@foreach(($deptRec['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $recChallengeIcons[$i % count($recChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][0]['title'] ?? t('kouventa.Candidate Screening and Sourcing Agent', 'Candidate Screening and Sourcing Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][0]['desc'] ?? t('kouventa.Source candidates from multiple job portals, then screen and match candidates based on job requirements and qualifications.', 'Source candidates from multiple job portals, then screen and match candidates based on job requirements and qualifications.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][1]['title'] ?? t('kouventa.Candidate Communication Agent', 'Candidate Communication Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][1]['desc'] ?? t('kouventa.Send interview and psychotest invitations, provide recruitment updates, and answer candidate questions throughout the hiring process.', 'Send interview and psychotest invitations, provide recruitment updates, and answer candidate questions throughout the hiring process.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][2]['title'] ?? t('kouventa.Candidate Scheduling Agent', 'Candidate Scheduling Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][2]['desc'] ?? t('kouventa.Coordinate and schedule interviews and psychotests based on the availability of HR, interviewers, and candidates.', 'Coordinate and schedule interviews and psychotests based on the availability of HR, interviewers, and candidates.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="(sol === 3) || (sol === -1 && activeSol === 3)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][3]['title'] ?? t('kouventa.Recruitment Insight Agent', 'Recruitment Insight Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][3]['desc'] ?? t('kouventa.Analyze candidate profiles based on interview and psychotest results to identify key insights and provide recommendations.', 'Analyze candidate profiles based on interview and psychotest results to identify key insights and provide recommendations.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-rec" role="img" aria-label="Animated illustration for Kouventa Recruitment AI Agents" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- SCENE 0: Candidate Screening and Sourcing Agent -->
<div class="kv-rec-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }" style="justify-content: center;">

  <!-- Body: Sources -> AI Filter -> Leaderboard -->
  <div style="display: grid; grid-template-columns: 140px 48px minmax(0, 1fr); align-items: center; gap: 14px; width: 100%;">
    
    <!-- Left: Job Portals Sources -->
    <div style="display: flex; flex-direction: column; gap: 10px">
      <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B">{{ $isIdLocale ? 'Sumber Portal' : 'Job Portals' }}</span>
      
      <div style="background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 9px; padding: 9px 12px; display: flex; justify-content: space-between; align-items: center; animation: kvIn .3s ease .15s both">
        <span style="font-size: 12px; font-weight: 700; color: #0E1A24">Jobstreet</span>
        <span style="font-size: 9.5px; font-weight: 700; background: #E2E8F0; color: #475569; padding: 2px 6px; border-radius: 4px">48 CV</span>
      </div>
      
      <div style="background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 9px; padding: 9px 12px; display: flex; justify-content: space-between; align-items: center; animation: kvIn .3s ease .30s both">
        <span style="font-size: 12px; font-weight: 700; color: #0E1A24">LinkedIn</span>
        <span style="font-size: 9.5px; font-weight: 700; background: #E2E8F0; color: #475569; padding: 2px 6px; border-radius: 4px">31 CV</span>
      </div>

      <div style="background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 9px; padding: 9px 12px; display: flex; justify-content: space-between; align-items: center; animation: kvIn .3s ease .45s both">
        <span style="font-size: 12px; font-weight: 700; color: #0E1A24">Glints</span>
        <span style="font-size: 9.5px; font-weight: 700; background: #E2E8F0; color: #475569; padding: 2px 6px; border-radius: 4px">22 CV</span>
      </div>
    </div>

    <!-- Center: Signal Beam & Agent Badge -->
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px">
      <svg width="40" height="14" viewBox="0 0 40 14" fill="none">
        <path d="M 2 7 L 34 7" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 3" style="animation: kvDashRight 0.6s linear infinite" />
        <polygon points="32,3 38,7 32,11" fill="#13803F" />
      </svg>
      <div style="width: 42px; height: 42px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(7,28,51,0.2); animation: kvPopIn .3s ease .6s both">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
      </div>
      <span style="font-size: 9px; font-weight: 800; color: #13803F; letter-spacing: 0.05em; white-space: nowrap">AI Agent</span>
    </div>

    <!-- Right: Candidates Ranked by Job Match -->
    <div style="display: flex; flex-direction: column; gap: 8px">
      <div style="display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B">{{ $isIdLocale ? 'Kandidat Terpilih' : 'Ranked Candidates' }}</span>
        <span style="font-size: 9.5px; font-weight: 700; color: #13803F">{{ $isIdLocale ? 'Kecocokan Kualifikasi' : 'Requirement Fit' }}</span>
      </div>

      <!-- Candidate 1: Rani Pratama (TOP MATCH) -->
      <div style="border: 1.5px solid #CBD5E1; border-radius: 10px; padding: 8px 12px; background: #F8FAFA; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; animation: kvIn .3s ease 1.0s both, kvMatchDoc .5s ease 1.8s 1 forwards">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #C9E8D3; color: #0B5E2C; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">RP</span>
        <div style="display: flex; flex-direction: column; flex: 1">
          <div style="display: flex; align-items: center; gap: 6px">
            <span style="font-size: 13px; font-weight: 800; color: #0E1A24">Rani Pratama</span>
            <span style="font-size: 8.5px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 1px 5px; border-radius: 3px">Top Match</span>
          </div>
          <span style="font-size: 10px; color: #475569">Senior Backend · 5 thn exp</span>
        </div>
        <div style="text-align: right">
          <span style="font-size: 14px; font-weight: 800; color: #0B5E2C">92%</span>
          <span style="display: block; font-size: 8.5px; color: #13803F; font-weight: 700">Shortlisted</span>
        </div>
      </div>

      <!-- Candidate 2: Dimas A. -->
      <div style="border: 1px solid #E2E8F0; border-radius: 9px; padding: 7px 11px; background: #F8FAFA; display: flex; align-items: center; gap: 10px; animation: kvIn .3s ease 1.2s both">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #CFDCEB; color: #1E3A8A; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800">DA</span>
        <div style="display: flex; flex-direction: column; flex: 1">
          <span style="font-size: 12px; font-weight: 700; color: #0E1A24">Dimas Aditya</span>
          <span style="font-size: 9.5px; color: #64748B">Backend Dev · 4 thn exp</span>
        </div>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">87%</span>
      </div>

      <!-- Candidate 3: Sinta W. -->
      <div style="border: 1px solid #E2E8F0; border-radius: 9px; padding: 7px 11px; background: #F8FAFA; display: flex; align-items: center; gap: 10px; animation: kvIn .3s ease 1.4s both">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #F2DCC4; color: #7C2D12; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800">SW</span>
        <div style="display: flex; flex-direction: column; flex: 1">
          <span style="font-size: 12px; font-weight: 700; color: #0E1A24">Sinta Wijaya</span>
          <span style="font-size: 9.5px; color: #64748B">Fullstack Dev · 3 thn exp</span>
        </div>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">81%</span>
      </div>

      <!-- Candidate 4: Yoga H. -->
      <div style="border: 1px solid #E2E8F0; border-radius: 9px; padding: 7px 11px; background: #F8FAFA; display: flex; align-items: center; gap: 10px; opacity: 0.65; animation: kvIn .3s ease 1.6s both">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #DCD3EE; color: #4C1D95; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800">YH</span>
        <div style="display: flex; flex-direction: column; flex: 1">
          <span style="font-size: 12px; font-weight: 700; color: #0E1A24">Yoga Hermanto</span>
          <span style="font-size: 9.5px; color: #64748B">Junior Backend · 2 thn exp</span>
        </div>
        <span style="font-size: 13px; font-weight: 800; color: #64748B">74%</span>
      </div>

    </div>
  </div>
</div>

<!-- SCENE 1: Candidate Communication Agent -->
<div class="kv-rec-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }" style="justify-content: center;">
  <!-- WhatsApp Chat Card Wrapper -->
  <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); width: 100%; box-sizing: border-box; overflow: hidden;">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px">
      <div style="display: flex; align-items: center; gap: 8px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        </span>
        <div>
          <div style="display: flex; align-items: center; gap: 6px">
            <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Asisten Rekrutmen Kouventa' : 'Kouventa Recruitment Assistant' }}</span>
            <span style="font-size: 9px; font-weight: 600; color: #16A34A">● online</span>
          </div>
          <span style="font-size: 10px; color: #64748B">Rani Pratama · Candidate #1</span>
        </div>
      </div>
      <span style="font-size: 10px; font-weight: 800; color: #0284C7; background: #E0F2FE; padding: 2px 8px; border-radius: 999px">
        WhatsApp
      </span>
    </div>

    <!-- Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px;">

      <!-- Message 1: Agent Automated Invite & Psychotest (0.3s) -->
      <div style="align-self: flex-start; max-width: 90%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.3s both">
        <span style="font-size: 11.5px; line-height: 1.45; color: #0E1A24">
          {{ $isIdLocale ? 'Halo Rani! Selamat, profil kamu lolos seleksi berkas posisi Senior Backend Dev. Kami mengundang kamu ke sesi Wawancara Teknis & Psikotes Online.' : 'Hi Rani! Congrats, you passed resume screening for Senior Backend Dev. You are invited to Technical Interview & Online Psychotest.' }}
        </span>
        <!-- Attached Invitation Details Card -->
        <div style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 6px; padding: 6px 8px; display: flex; flex-direction: column; gap: 3px; font-size: 10px">
          <span style="font-weight: 700; color: #0E1A24">📅 {{ $isIdLocale ? 'Wawancara: Kamis, 10:00 WIB' : 'Interview: Thursday, 10:00 AM' }}</span>
          <span style="color: #0284C7; font-weight: 600">📝 {{ $isIdLocale ? 'Link Psikotes: Aktif (durasi 45 menit)' : 'Psychotest Link: Active (45m)' }}</span>
        </div>
        <span style="align-self: flex-end; font-size: 8.5px; font-weight: 600; color: #94A3B8">10:00 AM</span>
      </div>

      <!-- Candidate Typing Dots (0.9s - 1.5s) -->
      <div style="align-self: flex-end; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px 12px 2px 12px; padding: 8px 12px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .25s ease 0.9s both, kvOut .2s ease 1.5s 1 forwards">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
      </div>

      <!-- Message 2: Candidate Confirmation (1.6s) -->
      <div style="align-self: flex-end; max-width: 88%; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px 12px 2px 12px; padding: 9px 12px; display: flex; flex-direction: column; gap: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 1.6s both">
        <span style="font-size: 11.5px; line-height: 1.45; color: #1E3A8A">
          {{ $isIdLocale ? 'Halo, terima kasih banyak! Saya konfirmasi hadir hari Kamis jam 10:00 WIB, dan psikotes sudah selesai saya kerjakan barusan 👍' : 'Thank you so much! Confirming I will attend Thursday at 10:00 AM, and I just finished the psychotest 👍' }}
        </span>
        <span style="align-self: flex-end; font-size: 8.5px; font-weight: 600; color: #3B82F6">10:02 AM · ✓✓</span>
      </div>

      <!-- Message 3: Agent Confirmation & Sync (2.4s) -->
      <div style="align-self: flex-start; max-width: 90%; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px 12px 12px 2px; padding: 9px 12px; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 2.4s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; color: #13803F">Kouventa Agent</span>
          <span style="font-size: 8.5px; font-weight: 700; color: #13803F; background: #DDF3E4; padding: 1px 5px; border-radius: 3px">Auto-Sync</span>
        </div>
        <span style="font-size: 11.5px; line-height: 1.45; color: #0B5E2C">
          {{ $isIdLocale ? 'Konfirmasi kamu telah dikunci ke kalender Google Meet & dashboard HR. Pengingat otomatis akan dikirim H-1.' : 'Your confirmation is locked into Google Meet calendar & HR dashboard. An automated reminder will be sent 1 day prior.' }}
        </span>
      </div>

    </div>

  </div>
</div>

<!-- SCENE 2: Candidate Scheduling Agent -->
<div class="kv-rec-scene" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }" style="justify-content: center; gap: 14px;">

  <!-- Multi-Party Availability Grid -->
  <div style="background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; overflow: hidden; animation: kvIn .35s ease 0.2s both">
    
    <!-- Time Header -->
    <div style="display: grid; grid-template-columns: 120px repeat(5, 1fr); gap: 6px; align-items: center; font-size: 10.5px; font-weight: 800; color: #64748B; padding-bottom: 6px; border-bottom: 1px solid #E2E8F0">
      <span>{{ $isIdLocale ? 'Peserta Kalender' : 'Participants' }}</span>
      <span style="text-align: center">09:00</span>
      <span style="text-align: center; color: #13803F; background: #DDF3E4; border-radius: 4px; padding: 1px 0">10:00 ★</span>
      <span style="text-align: center">11:00</span>
      <span style="text-align: center">13:00</span>
      <span style="text-align: center">14:00</span>
    </div>

    <!-- Row 1: HR Manager -->
    <div style="display: grid; grid-template-columns: 120px repeat(5, 1fr); gap: 6px; align-items: center; font-size: 11px">
      <span style="font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 5px">
        <span style="width: 7px; height: 7px; border-radius: 50%; background: #3B82F6"></span>
        Sarah (HR)
      </span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
      <span style="height: 24px; border-radius: 6px; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; box-shadow: 0 2px 6px rgba(19,128,63,0.3)">Match</span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
    </div>

    <!-- Row 2: Lead Tech Interviewer -->
    <div style="display: grid; grid-template-columns: 120px repeat(5, 1fr); gap: 6px; align-items: center; font-size: 11px">
      <span style="font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 5px">
        <span style="width: 7px; height: 7px; border-radius: 50%; background: #8B5CF6"></span>
        Budi (Lead)
      </span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
      <span style="height: 24px; border-radius: 6px; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; box-shadow: 0 2px 6px rgba(19,128,63,0.3)">Match</span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
    </div>

    <!-- Row 3: Candidate Rani P. -->
    <div style="display: grid; grid-template-columns: 120px repeat(5, 1fr); gap: 6px; align-items: center; font-size: 11px">
      <span style="font-weight: 700; color: #0E1A24; display: flex; align-items: center; gap: 5px">
        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981"></span>
        Rani (Kandidat)
      </span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
      <span style="height: 24px; border-radius: 6px; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; box-shadow: 0 2px 6px rgba(19,128,63,0.3)">Match</span>
      <span style="height: 24px; border-radius: 6px; background: #C9E8D3; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #0B5E2C">Free</span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
      <span style="height: 24px; border-radius: 6px; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94A3B8">Busy</span>
    </div>

  </div>

  <!-- Locked Calendar Event Card -->
  <div style="background: #FFFFFF; border: 1.5px solid #13803F; border-left: 4px solid #13803F; border-radius: 10px; padding: 12px 16px; display: flex; flex-direction: column; gap: 6px; box-shadow: 0 4px 14px rgba(19, 128, 63, 0.08); overflow: hidden; animation: kvIn .35s ease 1.5s both">
    <div style="display: flex; justify-content: space-between; align-items: center">
      <span style="font-size: 13px; font-weight: 800; color: #0E1A24">Wawancara Teknis — Rani Pratama</span>
      <span style="font-size: 9.5px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 2px 7px; border-radius: 4px">✓ Slot Terkunci (100% Match)</span>
    </div>
    
    <div style="display: flex; gap: 16px; font-size: 11px; color: #475569">
      <span>⏰ <strong>Kamis, 10:00 – 11:00 WIB</strong></span>
      <span>📹 <strong>Google Meet</strong> (link otomatis dibuat)</span>
    </div>

    <div style="display: flex; align-items: center; gap: 6px; padding-top: 3px">
      <span style="font-size: 10px; font-weight: 700; color: #64748B">Undangan terkirim:</span>
      <span style="font-size: 9.5px; background: #F1F5F9; color: #334155; padding: 2px 7px; border-radius: 4px">sarah@company.com</span>
      <span style="font-size: 9.5px; background: #F1F5F9; color: #334155; padding: 2px 7px; border-radius: 4px">budi@company.com</span>
      <span style="font-size: 9.5px; background: #F1F5F9; color: #334155; padding: 2px 7px; border-radius: 4px">rani.p@email.com</span>
    </div>
  </div>
</div>

<!-- SCENE 3: Recruitment Insight Agent (Executive Dark Dossier Canvas) -->
<div class="kv-rec-scene" :class="{ 'kv-active': (sol === 3) || (sol === -1 && activeSol === 3) }" style="background: #071C33; color: #FFFFFF; border-radius: 19px; padding: 26px; justify-content: center; gap: 14px; overflow: hidden; box-sizing: border-box;">
  <!-- Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 12px">
    <div style="display: flex; align-items: center; gap: 10px">
      <span style="width: 36px; height: 36px; border-radius: 50%; background: rgba(43, 212, 106, 0.15); border: 1.5px solid #2BD46A; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800">RP</span>
      <div>
        <div style="display: flex; align-items: center; gap: 6px">
          <span style="font-size: 14px; font-weight: 800; color: #FFFFFF">Rani Pratama</span>
          <span style="font-size: 9.5px; font-weight: 800; color: #2BD46A; background: rgba(43,212,106,0.16); padding: 1px 6px; border-radius: 4px">#REC-4902</span>
        </div>
        <span style="font-size: 11px; color: #94A3B8">Senior Backend Developer · {{ $isIdLocale ? 'Hasil Wawancara & Psikotes' : 'Interview & Psychotest Results' }}</span>
      </div>
    </div>
    <div style="text-align: right">
      <span style="font-size: 18px; font-weight: 900; color: #2BD46A">94<span style="font-size: 12px; color: #94A3B8; font-weight: 600">/100</span></span>
      <span style="display: block; font-size: 9px; font-weight: 800; color: #2BD46A; text-transform: uppercase; letter-spacing: 0.05em">Top 2% Candidate</span>
    </div>
  </div>

  <!-- Body: 3-Dimensional Score Bars -->
  <div style="display: flex; flex-direction: column; gap: 10px; padding: 2px 0">
    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #94A3B8">{{ $isIdLocale ? 'Analisis Kompetensi AI' : 'AI Competency Matrix' }}</span>
    
    <!-- Bar 1: Technical -->
    <div style="display: flex; flex-direction: column; gap: 3px">
      <div style="display: flex; justify-content: space-between; font-size: 10.5px">
        <span style="font-weight: 700; color: #E2E8F0">{{ $isIdLocale ? 'Kompetensi Teknis (Go, SQL, Arsitektur)' : 'Technical Mastery (Go, SQL, Architecture)' }}</span>
        <strong style="color: #2BD46A">95%</strong>
      </div>
      <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden">
        <div style="height: 100%; width: 95%; background: #2BD46A; border-radius: 3px; animation: kvBarFill 1s ease 0.3s both"></div>
      </div>
    </div>

    <!-- Bar 2: Psychotest -->
    <div style="display: flex; flex-direction: column; gap: 3px">
      <div style="display: flex; justify-content: space-between; font-size: 10.5px">
        <span style="font-weight: 700; color: #E2E8F0">{{ $isIdLocale ? 'Psikotes & Penalaran Logika' : 'Psychotest & Cognitive Logic' }}</span>
        <strong style="color: #38BDF8">92%</strong>
      </div>
      <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden">
        <div style="height: 100%; width: 92%; background: #38BDF8; border-radius: 3px; animation: kvBarFill 1s ease 0.5s both"></div>
      </div>
    </div>

    <!-- Bar 3: Culture & Comms -->
    <div style="display: flex; flex-direction: column; gap: 3px">
      <div style="display: flex; justify-content: space-between; font-size: 10.5px">
        <span style="font-weight: 700; color: #E2E8F0">{{ $isIdLocale ? 'Komunikasi & Problem Solving' : 'Communication & Problem Solving' }}</span>
        <strong style="color: #A78BFA">90%</strong>
      </div>
      <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden">
        <div style="height: 100%; width: 90%; background: #A78BFA; border-radius: 3px; animation: kvBarFill 1s ease 0.7s both"></div>
      </div>
    </div>
  </div>

  <!-- Key Observation Synthesis Box -->
  <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 9px; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .35s ease 1.0s both">
    <span style="font-size: 9.5px; font-weight: 800; color: #FBBF24; display: flex; align-items: center; gap: 4px">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
      {{ $isIdLocale ? 'Sintesis Wawasan AI' : 'AI Behavioral Insight' }}
    </span>
    <span style="font-size: 11px; line-height: 1.45; color: #CBD5E1">
      {{ $isIdLocale ? 'Kandidat menunjukkan keunggulan kuat pada optimasi database dan microservices. Skor psikotes mencerminkan konsistensi tinggi di bawah tekanan deadline.' : 'Candidate shows exceptional strength in database optimization & microservices. Psychotest indicates high consistency under deadline pressure.' }}
    </span>
  </div>

  <!-- Final Recommendation Card -->
  <div style="background: rgba(43, 212, 106, 0.12); border: 1.5px solid #2BD46A; border-radius: 10px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; animation: kvPopIn .35s ease 1.4s both">
    <div style="display: flex; flex-direction: column; gap: 2px">
      <span style="font-size: 12px; font-weight: 800; color: #2BD46A">{{ $isIdLocale ? '✓ Rekomendasi: Lanjut ke Final Interview' : '✓ Recommendation: Proceed to Final Interview' }}</span>
      <span style="font-size: 10px; color: #CBD5E1">{{ $isIdLocale ? 'Draft penawaran kerja (Offering Letter) otomatis disiapkan' : 'Automated draft offering letter pre-generated for HR approval' }}</span>
    </div>
    <span style="font-size: 9.5px; font-weight: 800; color: #071C33; background: #2BD46A; padding: 3px 8px; border-radius: 4px; white-space: nowrap">
      Approved
    </span>
  </div>
</div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $recImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg>',
    ];
@endphp
@foreach(($deptRec['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $recImpactIcons[$i % count($recImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 2: Sales & Marketing ===== -->
<div role="tabpanel" x-show="tab === 2" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $salesChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 2.6.5 4.4 1.2 5.7M17 17H3s3-2 3-9c0-.8.1-1.5.4-2.2"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0M2 2l20 20"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3M11 8v3M11 14h.01"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M17 8l5 5M22 8l-5 5"></path></svg>',
    ];
@endphp
@foreach(($deptSales['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $salesChallengeIcons[$i % count($salesChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][0]['title'] ?? t('kouventa.Sales Assistant Agent', 'Sales Assistant Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][0]['desc'] ?? t('kouventa.Understand customer needs, recommend relevant products, answer product and availability questions, and follow up with customers who don\'t respond.', 'Understand customer needs, recommend relevant products, answer product and availability questions, and follow up with customers who don\'t respond.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8v8l9 5 9-5Z"></path><path d="M3 8l9 5 9-5M12 13v8"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][1]['title'] ?? t('kouventa.Order Agent', 'Order Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][1]['desc'] ?? t('kouventa.Create orders, process payments, verify transactions, and confirm purchases.', 'Create orders, process payments, verify transactions, and confirm purchases.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][2]['title'] ?? t('kouventa.Shipment Tracking Agent', 'Shipment Tracking Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][2]['desc'] ?? t('kouventa.Track order and shipment status, provide delivery updates, and answer post-purchase questions.', 'Track order and shipment status, provide delivery updates, and answer post-purchase questions.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-sales" role="img" aria-label="Animated illustration for Kouventa Sales and Marketing AI Agents" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- SCENE 0: Sales Assistant Agent (Left: Live Catalog & Stock Check, Right: WhatsApp Customer Chat & Proactive Follow-up) -->
<div class="kv-sales-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }">

  <!-- LEFT: Catalog & Live Stock Engine (Open Background Process Canvas) -->
  <div style="width: 270px; height: 456px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 0.6s both;">
    
    <!-- Background Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 24px; height: 24px; border-radius: 6px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Katalog & Inventaris Real-Time' : 'Live Catalog & Stock Engine' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0B5E2C; background: #DDF3E4; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F"></span>
        Live Stock
      </span>
    </div>

    <!-- Product Inventory Items -->
    <div style="display: flex; flex-direction: column; gap: 9px; flex: 1; justify-content: center; padding: 6px 0">
      
      <!-- Product 1: Runner Sneakers (Blue / 42) -> TARGET MATCH -->
      <div style="border: 1.5px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; display: flex; flex-direction: column; gap: 6px; transition: all 0.3s ease; animation: kvMatchDoc .5s ease 1.10s 1 forwards">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 8px">
            <span style="width: 28px; height: 28px; border-radius: 6px; background: #1D4F91; display: flex; align-items: center; justify-content: center; color: #FFFFFF">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 17h20M2 12h20M7 7h10"/></svg>
            </span>
            <div style="display: flex; flex-direction: column">
              <span style="font-size: 12px; font-weight: 800; color: #0E1A24">Runner Sneakers · Blue</span>
              <span style="font-size: 9.5px; color: #64748B">SKU #SNK-042 · Size 42</span>
            </div>
          </div>
          <span style="font-size: 11px; font-weight: 800; color: #0E1A24">Rp 899.000</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px; border-top: 1px dashed #CBD5E1">
          <span style="font-size: 9.5px; font-weight: 700; color: #0B5E2C; display: flex; align-items: center; gap: 4px">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            {{ $isIdLocale ? 'Stok Tersedia (Sisa 3 pasang)' : 'In Stock (3 pairs left)' }}
          </span>
          <span style="font-size: 8.5px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 1px 6px; border-radius: 4px">Ready Stock</span>
        </div>
      </div>

      <!-- Product 2: Obsidian Black (Out of Stock / Dimmed Arsir) -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 9px; padding: 8px 12px; opacity: 0.55; display: flex; justify-content: space-between; align-items: center" class="kv-arsir-pattern">
        <div style="display: flex; align-items: center; gap: 8px">
          <span style="width: 24px; height: 24px; border-radius: 50%; background: #334155"></span>
          <div style="display: flex; flex-direction: column">
            <span style="font-size: 11px; font-weight: 700; color: #475569">Runner Sneakers · Black</span>
            <span style="font-size: 9px; color: #94A3B8">SKU #SNK-043 · Size 42</span>
          </div>
        </div>
        <span style="font-size: 8.5px; font-weight: 700; color: #94A3B8">{{ $isIdLocale ? 'Habis (PO 7 hari)' : 'Sold Out (7d PO)' }}</span>
      </div>

      <!-- Automation Follow-Up Trigger Card -->
      <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 9px; padding: 8px 10px; display: flex; flex-direction: column; gap: 3px; opacity: 0; animation: kvIn .35s ease 2.30s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 9.5px; font-weight: 800; color: #13803F; display: flex; align-items: center; gap: 4px">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $isIdLocale ? 'Pemicu Follow-Up Otomatis' : 'Automated Follow-Up Trigger' }}
          </span>
          <span style="font-size: 8px; font-weight: 700; color: #13803F; background: #DDF3E4; padding: 1px 5px; border-radius: 3px">Active</span>
        </div>
        <span style="font-size: 9px; color: #0B5E2C; line-height: 1.35">
          {{ $isIdLocale ? 'Kirim follow-up reservasi jika calon pembeli tidak merespons dalam 2 jam.' : 'Send proactive reservation reminder if customer goes quiet for > 2 hours.' }}
        </span>
      </div>

    </div>
  </div>

  <!-- Animated Dashed Query Signal Arrow (Behind WhatsApp Card) -->
  <svg style="position: absolute; left: 245px; top: 125px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 0.70s both, kvOut .25s ease 1.70s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#13803F" />
  </svg>

  <!-- RIGHT: Customer WhatsApp Card -->
  <div style="width: 266px; height: 456px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 12px 12px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3); flex-shrink: 0">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </span>
        <div style="display: flex; flex-direction: column; line-height: 1.2">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">WhatsApp</span>
          <span style="font-size: 8.5px; font-weight: 600; color: #16A34A">● online</span>
        </div>
      </div>
      <span style="font-size: 9px; font-weight: 800; color: #071C33; background: #F1F5F9; border: 1px solid #E2E8F0; padding: 2px 7px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap">
        <span style="width: 4px; height: 4px; border-radius: 50%; background: #2BD46A"></span>
        22:30 · {{ $isIdLocale ? 'Malam' : 'Night' }}
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 6px; flex: 1; justify-content: center; padding: 4px 0">
      
      <!-- 0. Time / Status Divider -->
      <div style="align-self: center; font-size: 8.5px; font-weight: 700; color: #64748B; background: #F1F5F9; border-radius: 999px; padding: 2px 8px; margin-bottom: 2px">
        22:30 · {{ $isIdLocale ? 'Di luar jam kerja' : 'After hours' }}
      </div>

      <!-- 1. Customer Inquiry (0.35s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.35s both">
        <span style="font-size: 11px; font-weight: 600; line-height: 1.35; color: #0E1A24">
          {{ $isIdLocale ? 'Halo min, apakah Runner Sneakers warna biru size 42 masih ada stok?' : 'Hi! Is Runner Sneakers in blue size 42 still in stock?' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #94A3B8">22:30</span>
      </div>

      <!-- 2. Typing Indicator morphs into AI Instant Answer & Product Card (0.75s to 1.70s) -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; opacity: 0; animation: kvIn .3s ease 0.75s both, kvMorphOut .2s ease 1.70s 1 forwards">
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 10px 10px 10px; padding: 7px 9px; display: flex; flex-direction: column; gap: 4px; border: 1px solid #C9E8D3; box-shadow: 0 3px 10px rgba(19, 128, 63, 0.08); transform-origin: top left; opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 1.75s both">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 10px; font-weight: 800; color: #13803F">Sales Assistant Agent</span>
            <span style="font-size: 8px; font-weight: 700; color: #13803F; background: #EAF7EF; padding: 1px 4px; border-radius: 3px">0.2s</span>
          </div>
          <span style="font-size: 10.5px; line-height: 1.35; color: #0E1A24">
            {{ $isIdLocale ? 'Iya kak! Warna Biru size 42 ready sisa 3 pasang. Mau langsung kami bantu amankan malam ini? 😊' : 'Yes! Blue size 42 is in stock with 3 pairs left. Would you like me to hold a pair for you? 😊' }}
          </span>
          <div style="display: flex; align-items: center; gap: 6px; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 5px; padding: 4px 6px">
            <span style="width: 18px; height: 18px; border-radius: 3px; background: #1D4F91; flex-shrink: 0"></span>
            <div style="display: flex; flex-direction: column; flex: 1">
              <span style="font-size: 9.5px; font-weight: 700; color: #0E1A24">Runner Sneakers · Blue (42)</span>
              <span style="font-size: 8.5px; font-weight: 800; color: #13803F">Rp 899.000 · In stock</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Time Lapse Pill (2.70s) -->
      <div style="align-self: center; background: #F1F5F9; border-radius: 999px; padding: 2px 8px; font-size: 8.5px; font-weight: 700; color: #64748B; opacity: 0; animation: kvPopIn .3s ease 2.70s both">
        ⏰ {{ $isIdLocale ? '2 jam kemudian · belum ada balasan' : '2 hours later · no response' }}
      </div>

      <!-- 4. Smart Proactive Follow-Up (3.15s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 2px 10px 10px 10px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 3.15s both">
        <span style="font-size: 10.5px; font-weight: 700; line-height: 1.35; color: #0B5E2C">
          {{ $isIdLocale ? 'Halo kak, stok biru size 42 tinggal 1 pasang nih. Mau kami bantu simpankan sebelum kehabisan? 🙏' : 'Hi again! Just a heads up, only 1 pair left in blue 42. Can I help reserve it before it sells out? 🙏' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #16A34A">00:30</span>
      </div>

      <!-- 5. Customer Conversion Response (4.20s) -->
      <div style="align-self: flex-end; max-width: 90%; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px 10px 2px 10px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 4.20s both">
        <span style="font-size: 11px; font-weight: 600; line-height: 1.35; color: #1E3A8A">
          {{ $isIdLocale ? 'Wah boleh kak, saya ambil 1 ya! Tolong dibantu pesan. 👍' : 'Yes please, I will take it! Help me order. 👍' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #3B82F6">00:32 · ✓✓</span>
      </div>

    </div>
  </div>

</div>

<!-- SCENE 1: Order Agent (Left: Order Engine & Auto Payment Gateway, Right: WhatsApp Order & Instant Verification) -->
<div class="kv-sales-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">

  <!-- LEFT: Checkout & Payment Verification Canvas -->
  <div style="width: 270px; height: 456px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 0.6s both;">
    
    <!-- Background Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 24px; height: 24px; border-radius: 6px; background: #F0F9FF; color: #0284C7; display: flex; align-items: center; justify-content: center">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Mesin Pesanan & Verifikasi Billing' : 'Order & Payment Verification' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0369A1; background: #E0F2FE; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #0284C7"></span>
        Auto-Billing
      </span>
    </div>

    <!-- Main Payment & Order Processing Stack -->
    <div style="display: flex; flex-direction: column; gap: 9px; flex: 1; justify-content: center; padding: 6px 0">
      
      <!-- Order Summary Card (0.90s) -->
      <div style="background: #071C33; color: #FFFFFF; border-radius: 10px; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 4px 14px rgba(7,28,51,0.12); opacity: 0; animation: kvIn .35s ease 0.90s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.05em; color: #2BD46A">ORDER #ORD-8821</span>
          <span style="font-size: 8.5px; font-weight: 800; color: #FFFFFF; background: rgba(255,255,255,0.16); padding: 1px 6px; border-radius: 4px">1 Item</span>
        </div>
        <div style="font-size: 11px; font-weight: 700; color: #E2E8F0">Runner Sneakers · Blue · Size 42</div>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 10px; color: #94A3B8; padding-top: 4px; border-top: 1px solid rgba(255,255,255,0.1)">
          <span>{{ $isIdLocale ? 'Ongkos Kirim' : 'Shipping' }}: <strong style="color: #2BD46A">{{ $isIdLocale ? 'Gratis' : 'Free' }}</strong></span>
          <span style="font-size: 12px; font-weight: 900; color: #FFFFFF">Rp 899.000</span>
        </div>
      </div>

      <!-- Virtual Account & Payment Status Card (1.30s to 2.80s transitions to Paid) -->
      <div style="border: 1.5px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; display: flex; flex-direction: column; gap: 6px; opacity: 0; animation: kvIn .35s ease 1.30s both, kvMatchDoc .5s ease 2.90s 1 forwards">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <span style="font-size: 10.5px; font-weight: 800; color: #0284C7; background: #E0F2FE; padding: 2px 6px; border-radius: 4px">BCA VA</span>
            <span style="font-size: 11.5px; font-weight: 800; color: #0E1A24">8271 0812 3456 7890</span>
          </div>
          <!-- Dynamic Status: Waiting -> Paid -->
          <span style="display: grid; font-size: 9px; font-weight: 800">
            <span style="grid-area: 1 / 1; padding: 1px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E; opacity: 1; animation: kvOut .2s ease 2.80s 1 forwards">{{ $isIdLocale ? 'Menunggu Bayar' : 'Awaiting Payment' }}</span>
            <span style="grid-area: 1 / 1; padding: 1px 6px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C; opacity: 0; animation: kvPopIn .3s ease 2.90s both">{{ $isIdLocale ? '✓ LUNAS' : '✓ PAID' }}</span>
          </span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 9.5px; color: #64748B">
          <span>{{ $isIdLocale ? 'Batas Waktu: 24 Jam' : 'Time Limit: 24 Hours' }}</span>
          <span style="font-weight: 700; color: #0E1A24">Nominal: Rp 899.000</span>
        </div>
      </div>

      <!-- Bank Webhook Verification Signal (Pops in at 2.60s) -->
      <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; padding: 7px 10px; display: flex; align-items: center; justify-content: space-between; opacity: 0; animation: kvIn .35s ease 2.60s both">
        <div style="display: flex; align-items: center; gap: 6px">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          <span style="font-size: 10px; font-weight: 800; color: #0B5E2C">{{ $isIdLocale ? 'Mutasi Bank Terdeteksi' : 'Bank Webhook Received' }}</span>
        </div>
        <span style="font-size: 9px; font-weight: 700; color: #13803F">INV/2026/0491</span>
      </div>

    </div>
  </div>

  <!-- Animated Dashed Query Signal Arrow -->
  <svg style="position: absolute; left: 245px; top: 135px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 0.70s both, kvOut .25s ease 1.70s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#0284C7" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#0284C7" />
  </svg>

  <!-- RIGHT: Customer WhatsApp Card -->
  <div style="width: 266px; height: 456px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 12px 12px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3); flex-shrink: 0">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </span>
        <div style="display: flex; flex-direction: column; line-height: 1.2">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">WhatsApp</span>
          <span style="font-size: 8.5px; font-weight: 600; color: #16A34A">● online</span>
        </div>
      </div>
      <span style="display: grid; font-size: 9px; font-weight: 800; white-space: nowrap">
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #FFF1D6; color: #7A4A00">{{ $isIdLocale ? 'Pesanan Baru' : 'New Order' }}</span>
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; opacity: 0; animation: kvPopIn .3s ease 3.50s both">{{ $isIdLocale ? '✓ Lunas' : '✓ Paid' }}</span>
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 7px; flex: 1; justify-content: center; padding: 4px 0">
      
      <!-- 1. Customer Shipping Address (0.35s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.35s both">
        <span style="font-size: 11px; font-weight: 600; line-height: 1.35; color: #0E1A24">
          {{ $isIdLocale ? 'Kirim ke alamat Dimas, Jl. Sudirman Kav 24 Jakarta Selatan ya min.' : 'Please ship to Dimas, Jl. Sudirman Kav 24 South Jakarta.' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #94A3B8">00:33</span>
      </div>

      <!-- 2. Agent Order Created & Payment Invoice (1.10s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #FFFFFF; border: 1px solid #BAE6FD; border-radius: 2px 10px 10px 10px; padding: 7px 9px; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08); opacity: 0; animation: kvIn .35s ease 1.10s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; color: #0284C7">Order Agent</span>
          <span style="font-size: 8px; font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 1px 4px; border-radius: 3px">#ORD-8821</span>
        </div>
        <span style="font-size: 10.5px; line-height: 1.35; color: #0E1A24">
          {{ $isIdLocale ? 'Pesanan berhasil dibuat kak! Total Rp 899.000. Silakan transfer via BCA VA berikut ya:' : 'Order successfully created! Total $89. Transfer via BCA VA:' }}
        </span>
        <div style="background: #F0F9FF; border: 1px dashed #7DD3FC; border-radius: 5px; padding: 4px 7px; display: flex; justify-content: space-between; align-items: center">
          <strong style="font-size: 11px; color: #0369A1">8271 0812 3456 7890</strong>
          <span style="font-size: 7.5px; font-weight: 800; color: #0284C7; background: #FFFFFF; padding: 1px 4px; border-radius: 3px">Salin</span>
        </div>
      </div>

      <!-- 3. Typing Indicator Morphs into Payment Confirmed Bubble (2.80s to 3.45s) -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; opacity: 0; animation: kvIn .3s ease 2.80s both, kvMorphOut .2s ease 3.40s 1 forwards">
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 10px 10px 10px; padding: 7px 9px; display: flex; flex-direction: column; gap: 4px; border: 1px solid #C9E8D3; box-shadow: 0 3px 10px rgba(19, 128, 63, 0.08); transform-origin: top left; opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 3.45s both">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 10px; font-weight: 800; color: #13803F">Order Agent</span>
            <span style="font-size: 8px; font-weight: 700; color: #0B5E2C; background: #DDF3E4; padding: 1px 4px; border-radius: 3px">✓ Payment Verified</span>
          </div>
          <span style="font-size: 10.5px; line-height: 1.35; color: #0E1A24">
            {{ $isIdLocale ? 'Pembayaran Rp 899.000 telah kami terima & terverifikasi otomatis! Invoice INV/2026/0491 telah terbit. Pesanan segera kami kemas ya kak 🎁' : 'Payment of $89 received & verified automatically! Invoice INV/2026/0491 issued. Packing your order now 🎁' }}
          </span>
          <span style="align-self: flex-start; font-size: 8.5px; font-weight: 800; color: #0B5E2C; background: #EAF7EF; padding: 2px 5px; border-radius: 4px">
            {{ $isIdLocale ? '✓ Pembelian Dikonfirmasi' : '✓ Purchase Confirmed' }}
          </span>
        </div>
      </div>

    </div>
  </div>

</div>

<!-- SCENE 2: Shipment Tracking Agent (Left: Live Courier API Tracking, Right: WhatsApp Real-Time Delivery Check) -->
<div class="kv-sales-scene" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }">

  <!-- LEFT: Logistics & Courier Tracking Canvas -->
  <div style="width: 270px; height: 456px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 0.6s both;">
    
    <!-- Background Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 24px; height: 24px; border-radius: 6px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Pelacakan Ekspedisi Real-Time' : 'Live Courier API Tracking' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #0284C7"></span>
        JNE Express API
      </span>
    </div>

    <!-- Stepper & Tracking Details -->
    <div style="display: flex; flex-direction: column; gap: 9px; flex: 1; justify-content: center; padding: 6px 0">
      
      <!-- AWB Header Card -->
      <div style="border: 1px solid #E2E8F0; border-radius: 9px; padding: 8px 11px; background: #F8FAFA; display: flex; justify-content: space-between; align-items: center">
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 11.5px; font-weight: 800; color: #0E1A24">JNE-88291048201</span>
          <span style="font-size: 9.5px; color: #64748B">Dimas A. · Jakarta Selatan</span>
        </div>
        <span style="font-size: 9px; font-weight: 800; color: #13803F; background: #DDF3E4; padding: 2px 6px; border-radius: 4px">Regular</span>
      </div>

      <!-- Stepper Tracking Timeline -->
      <div style="display: flex; flex-direction: column; gap: 6px; padding: 4px 6px">
        
        <!-- Step 1: Picked Up -->
        <div style="display: flex; align-items: center; gap: 8px; font-size: 10px; color: #64748B">
          <span style="width: 14px; height: 14px; border-radius: 50%; background: #C9E8D3; color: #0B5E2C; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800">✓</span>
          <span>09:30 · {{ $isIdLocale ? 'Paket Dipickup Kurir' : 'Picked Up by Courier' }}</span>
        </div>

        <!-- Step 2: Sorting Hub -->
        <div style="display: flex; align-items: center; gap: 8px; font-size: 10px; color: #64748B">
          <span style="width: 14px; height: 14px; border-radius: 50%; background: #C9E8D3; color: #0B5E2C; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800">✓</span>
          <span>14:15 · {{ $isIdLocale ? 'Tiba di Sorting Hub Jakarta' : 'Arrived at Sorting Hub' }}</span>
        </div>

        <!-- Step 3: Out for Delivery (ACTIVE TARGET) -->
        <div style="border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 7px 10px; background: #F8FAFA; display: flex; flex-direction: column; gap: 2px; animation: kvMatchDoc .5s ease 1.30s 1 forwards">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="display: flex; align-items: center; gap: 6px">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #13803F; box-shadow: 0 0 0 3px rgba(19,128,63,0.25)"></span>
              <span style="font-size: 10.5px; font-weight: 800; color: #0E1A24">18:40 · {{ $isIdLocale ? 'Dalam Pengantaran' : 'Out for Delivery' }}</span>
            </div>
            <span style="font-size: 8.5px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 1px 5px; border-radius: 3px">Active</span>
          </div>
          <span style="font-size: 9.5px; color: #475569; padding-left: 14px">
            {{ $isIdLocale ? 'Kurir: Pak Joko (+62 812-9921-xxxx)' : 'Courier: Joko W. (+62 812-9921-xxxx)' }}
          </span>
        </div>

      </div>

      <!-- ETA Card (1.80s) -->
      <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center; opacity: 0; animation: kvIn .35s ease 1.80s both">
        <span style="font-size: 10px; font-weight: 700; color: #0B5E2C">
          ⏱️ {{ $isIdLocale ? 'Estimasi Tiba: Hari Ini sebelum 21:00 WIB' : 'Estimated: Today before 9:00 PM' }}
        </span>
        <span style="font-size: 8.5px; font-weight: 800; color: #13803F; background: #DDF3E4; padding: 1px 5px; border-radius: 3px">On Time</span>
      </div>

    </div>
  </div>

  <!-- Animated Dashed Query Signal Arrow -->
  <svg style="position: absolute; left: 245px; top: 125px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 0.70s both, kvOut .25s ease 1.70s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#13803F" />
  </svg>

  <!-- RIGHT: Customer WhatsApp Card -->
  <div style="width: 266px; height: 456px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 12px 12px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3); flex-shrink: 0">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </span>
        <div style="display: flex; flex-direction: column; line-height: 1.2">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">WhatsApp</span>
          <span style="font-size: 8.5px; font-weight: 600; color: #16A34A">● online</span>
        </div>
      </div>
      <span style="font-size: 9px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 2px 7px; border-radius: 999px; white-space: nowrap">
        {{ $isIdLocale ? 'Lacak Resi' : 'Track Package' }}
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 7px; flex: 1; justify-content: center; padding: 4px 0">
      
      <!-- 1. Customer Tracking Inquiry (0.35s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.35s both">
        <span style="font-size: 11px; font-weight: 600; line-height: 1.35; color: #0E1A24">
          {{ $isIdLocale ? 'Sore min, boleh tolong cek paket sepatu saya resi JNE-88291048201 sudah sampai mana ya?' : 'Hi! Could you check where my shoe package with tracking JNE-88291048201 is right now?' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #94A3B8">18:42</span>
      </div>

      <!-- 2. Typing Indicator Morphs into Live Delivery Update Bubble (0.75s to 1.70s) -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px 10px 10px 2px; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; opacity: 0; animation: kvIn .3s ease 0.75s both, kvMorphOut .2s ease 1.70s 1 forwards">
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 10px 10px 10px; padding: 7px 9px; display: flex; flex-direction: column; gap: 4px; border: 1px solid #C9E8D3; box-shadow: 0 3px 10px rgba(19, 128, 63, 0.08); transform-origin: top left; opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 1.75s both">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 10px; font-weight: 800; color: #13803F">Shipment Tracking Agent</span>
            <span style="font-size: 8px; font-weight: 700; color: #0B5E2C; background: #DDF3E4; padding: 1px 4px; border-radius: 3px">Live GPS</span>
          </div>
          <span style="font-size: 10.5px; line-height: 1.35; color: #0E1A24">
            {{ $isIdLocale ? 'Halo kak Dimas! Paket kamu sedang dibawa kurir (Pak Joko) menuju alamat kakak. Estimasi tiba hari ini sebelum jam 21:00 ya 🚚📦' : 'Hi Dimas! Your package is currently out for delivery with courier Joko. Estimated arrival today before 9:00 PM 🚚📦' }}
          </span>
          <div style="display: flex; align-items: center; gap: 6px; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 5px; padding: 3px 6px">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; animation: kvTypingDot 1.2s infinite ease-in-out"></span>
            <span style="font-size: 9px; font-weight: 700; color: #0E1A24">{{ $isIdLocale ? 'Kurir di Jalan (SLA Tepat Waktu)' : 'Courier on Road (On-Time SLA)' }}</span>
          </div>
        </div>
      </div>

      <!-- 3. Customer Satisfaction Reply (3.10s) -->
      <div style="align-self: flex-end; max-width: 90%; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px 10px 2px 10px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 3.10s both">
        <span style="font-size: 11px; font-weight: 600; line-height: 1.35; color: #1E3A8A">
          {{ $isIdLocale ? 'Wah mantap cepat sekali infonya, terima kasih ya min! 👍' : 'Awesome, so quick! Thank you so much! 👍' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #3B82F6">18:44 · ✓✓</span>
      </div>

      <!-- 4. Agent Polite Wrap-Up (4.00s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 2px 10px 10px 10px; padding: 6px 9px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); opacity: 0; animation: kvIn .35s ease 4.00s both">
        <span style="font-size: 10.5px; line-height: 1.35; color: #0E1A24">
          {{ $isIdLocale ? 'Sama-sama kak! Kalau ada kendala saat paket diterima, langsung hubungi kami ya 😊' : 'You are welcome! If anything comes up when the package arrives, let us know 😊' }}
        </span>
        <span style="align-self: flex-end; font-size: 8px; font-weight: 600; color: #94A3B8">18:45</span>
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
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $salesImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"></path><path d="M14 7h7v7"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 12 0V3h-4v7a2 2 0 0 1-4 0V3Z"></path><path d="M6 7h4M14 7h4"></path></svg>',
    ];
@endphp
@foreach(($deptSales['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $salesImpactIcons[$i % count($salesImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
@if(is_array($imp) && !empty($imp['desc']))
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ $imp['desc'] }}</span>
@endif
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 3: Business Operations ===== -->
<div role="tabpanel" x-show="tab === 3" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $opsChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3M11 8v3M11 14h.01"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg>',
    ];
@endphp
@foreach(($deptOps['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $opsChallengeIcons[$i % count($opsChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptOps['solutions'][0]['title'] ?? t('kouventa.Knowledge Assistant Agent', 'Knowledge Assistant Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptOps['solutions'][0]['desc'] ?? t('kouventa.Find and explain information from the company\'s internal documents, policies, SOPs, and business knowledge.', 'Find and explain information from the company\'s internal documents, policies, SOPs, and business knowledge.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptOps['solutions'][1]['title'] ?? t('kouventa.Action Agent', 'Action Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptOps['solutions'][1]['desc'] ?? t('kouventa.Turn employee requests into actions by connecting with internal systems, submitting requests, and executing routine tasks with the required approvals.', 'Turn employee requests into actions by connecting with internal systems, submitting requests, and executing routine tasks with the required approvals.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-ops" role="img" aria-label="Animated illustration: Knowledge Assistant Agent and Action Agent for internal operations" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- SCENE 0: Knowledge Assistant Agent -->
<div class="kv-ops-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }">

  <!-- LEFT: Internal Knowledge Base & Policy Repository -->
  <div style="width: 270px; height: 456px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; animation: kvIn .4s ease .15s both">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 22px; height: 22px; border-radius: 6px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M6 6h10M6 10h10M6 14h6"></path></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Repositori Kebijakan Internal' : 'Internal Policy Repository' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #0B5E2C; background: #EAF7EF; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        <span style="width: 5px; height: 5px; border-radius: 50%; background: #13803F"></span>
        Vector RAG
      </span>
    </div>

    <!-- Document Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center; padding: 8px 0">
      <!-- Hatched doc 1 -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease .2s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 11px; font-weight: 700; color: #64748B">Panduan_Karyawan_2026.pdf</span>
          </div>
          <span style="font-size: 9px; font-weight: 600; color: #94A3B8">112 chunks</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px">
          <span class="kv-arsir-pattern" style="height: 6px; width: 90%; border-radius: 3px; display: block"></span>
          <span class="kv-arsir-pattern" style="height: 6px; width: 65%; border-radius: 3px; display: block"></span>
        </div>
      </div>

      <!-- Active Matched Doc: Kebijakan_Cuti_Karyawan.pdf -->
      <div style="border: 1.5px solid #CBD5E1; border-radius: 10px; padding: 12px; background: #F8FAFA; display: flex; flex-direction: column; gap: 8px; animation: kvMatchDoc .6s ease 1.8s both; position: relative">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 11px; font-weight: 800; color: #0E1A24">Kebijakan_Cuti_Karyawan.pdf</span>
          </div>
          <span style="font-size: 9px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 1px 6px; border-radius: 4px">SOP HR</span>
        </div>
        <div style="display: flex; gap: 6px; animation: kvPopIn .3s ease 2.0s both">
          <span style="font-size: 9px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 2px 6px; border-radius: 4px">✓ 99.8% Match</span>
          <span style="font-size: 9px; font-weight: 700; color: #1E40AF; background: #DBEAFE; padding: 2px 6px; border-radius: 4px">Confidence 99%</span>
        </div>
        <div style="background: #FFFFFF; border-left: 3px solid #13803F; border-radius: 4px; padding: 6px 8px; font-size: 10px; line-height: 1.45; color: #334155; animation: kvIn .35s ease 2.2s both">
          <strong style="color: #0E1A24; display: block; font-size: 10px">{{ $isIdLocale ? 'Pasal 4.2 · Cuti Tahunan' : 'Section 4.2 · Annual Leave' }}</strong>
          {{ $isIdLocale ? 'Karyawan tetap berhak atas 12 hari kerja cuti tahunan per tahun. Sisa cuti dapat dibawa ke tahun depan maks. 6 hari.' : 'Permanent employees receive 12 days of annual leave per year. Up to 6 unused days may be carried over.' }}
        </div>
      </div>

      <!-- Hatched doc 2 -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease .25s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div style="display: flex; align-items: center; gap: 6px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span style="font-size: 11px; font-weight: 700; color: #64748B">Struktur_Organisasi_HR.docx</span>
          </div>
          <span style="font-size: 9px; font-weight: 600; color: #94A3B8">48 chunks</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px">
          <span class="kv-arsir-pattern" style="height: 6px; width: 80%; border-radius: 3px; display: block"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Animated Dashed Query Beam -->
  <svg style="position: absolute; left: 245px; top: 215px; width: 95px; height: 30px; z-index: 2; pointer-events: none; opacity: 0; animation: kvIn .35s ease 1.3s both, kvOut .25s ease 2.9s 1 forwards">
    <path d="M 90 15 L 8 15" fill="none" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#13803F" />
  </svg>

  <!-- RIGHT: Enterprise Employee HR Chat Card -->
  <div style="width: 266px; height: 456px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; overflow: hidden; animation: kvIn .35s ease .15s both">
    
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800">
          AI
        </span>
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Portal Karyawan · HR' : 'Employee Portal · HR' }}</span>
          <span style="font-size: 9px; font-weight: 600; color: #16A34A">● online</span>
        </div>
      </div>
      <span style="font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 3.3s both">
        {{ $isIdLocale ? 'SOP Terverifikasi' : 'SOP Verified' }}
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center">
      <!-- 1. Employee Question (0.45s) -->
      <div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; font-size: 11px; line-height: 1.45; max-width: 88%; animation: kvIn .35s ease 0.45s both">
        {{ $isIdLocale ? 'Berapa jatah cuti tahunan untuk karyawan tetap? Bisa di-carry over?' : 'How many annual leave days do permanent employees get? Can unused days carry over?' }}
        <span style="display: block; font-size: 8.5px; color: #94A3B8; text-align: right; margin-top: 3px">09:15</span>
      </div>

      <!-- 2. Morphing AI Response Area -->
      <div style="display: grid; align-self: flex-start; max-width: 95%;">
        <!-- Typing Dots (0.85s to 2.85s) -->
        <div style="grid-area: 1 / 1; align-self: flex-start; background: #F4F6F5; border-radius: 4px 12px 12px 12px; padding: 8px 12px; display: inline-flex; align-items: center; gap: 4px; animation: kvIn .3s ease 0.85s both, kvMorphOut .25s ease 2.9s 1 forwards">
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1s infinite 0s"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1s infinite 0.2s"></span>
          <span style="width: 5px; height: 5px; border-radius: 50%; background: #64748B; animation: kvTypingDot 1s infinite 0.4s"></span>
        </div>

        <!-- Full Grounded Answer (Springs in at 2.95s) -->
        <div style="grid-area: 1 / 1; align-self: flex-start; background: #F4F6F5; border-radius: 4px 14px 14px 14px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); opacity: 0; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 2.95s 1 forwards">
          <div style="display: flex; justify-content: space-between; align-items: center">
            <span style="font-size: 10px; font-weight: 800; color: #13803F">Knowledge Assistant Agent</span>
            <span style="font-size: 8.5px; color: #64748B">0.2s</span>
          </div>
          <span style="font-size: 11px; line-height: 1.45; color: #1E293B">
            {{ $isIdLocale ? 'Sesuai Kebijakan Cuti Pasal 4.2: Karyawan tetap berhak 12 hari cuti kerja per tahun. Sisa cuti dapat dibawa ke tahun berikutnya maksimal 6 hari kerja.' : 'Per Leave Policy Section 4.2: Permanent employees get 12 annual leave days. Up to 6 unused days can carry over to the next year.' }}
          </span>
          <span style="align-self: flex-start; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C; display: flex; align-items: center; gap: 4px">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
            Kebijakan_Cuti_Karyawan.pdf · Pasal 4.2
          </span>
        </div>
      </div>
    </div>

    <!-- Clean Bottom Badge -->
    <div style="display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; color: #0B5E2C; background: #EAF7EF; padding: 6px 10px; border-radius: 8px; animation: kvIn .3s ease 3.4s both">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
      {{ $isIdLocale ? '100% Berbasis Dokumen Resmi Perusahaan' : '100% Grounded in Official Company Docs' }}
    </div>

  </div>

</div>

<!-- SCENE 1: Action Agent (Executing Leave Request & System Integration) -->
<div class="kv-ops-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">

  <!-- LEFT: HRIS & Enterprise Workflow Engine -->
  <div style="width: 270px; height: 456px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; animation: kvIn .4s ease .15s both">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 22px; height: 22px; border-radius: 6px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>
        </span>
        <span style="font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 0.02em">{{ $isIdLocale ? 'Mesin Alur Kerja HRIS' : 'HRIS & Workflow Engine' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 700; color: #1E40AF; background: #DBEAFE; padding: 2px 7px; border-radius: 999px; display: flex; align-items: center; gap: 4px">
        API Live
      </span>
    </div>

    <!-- Integration Systems Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center; padding: 8px 0">
      
      <!-- System 1: HRIS Quota Validation -->
      <div style="border: 1.5px solid #CBD5E1; border-radius: 10px; padding: 12px; background: #FFFFFF; display: flex; flex-direction: column; gap: 6px; animation: kvMatchDocBlue .6s ease .6s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 11px; font-weight: 800; color: #0E1A24">HRIS (Workday / SAP)</span>
          <span style="font-size: 9px; font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 1px 6px; border-radius: 4px">Active Session</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: #475569; padding-top: 2px">
          <span>{{ $isIdLocale ? 'Sisa Saldo Cuti' : 'Leave Balance' }}</span>
          <strong style="color: #0E1A24">8 {{ $isIdLocale ? 'hari' : 'days' }}</strong>
        </div>
        <div style="font-size: 9.5px; font-weight: 700; color: #0B5E2C; background: #DDF3E4; padding: 2px 6px; border-radius: 4px; align-self: flex-start; animation: kvIn .3s ease 1.0s both">
          ✓ {{ $isIdLocale ? 'Kuota Mencukupi (3 hari diajukan)' : 'Quota Valid (3 days requested)' }}
        </div>
      </div>

      <!-- System 2: Manager Approval Chain -->
      <div style="border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px; background: #F8FAFA; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.4s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 11px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Persetujuan Manajer' : 'Manager Approval' }}</span>
          <span style="font-size: 9px; font-weight: 800; color: #0B5E2C; background: #DDF3E4; padding: 1px 6px; border-radius: 4px; animation: kvPopIn .3s ease 2.2s both">✓ {{ $isIdLocale ? 'Disetujui' : 'Approved' }}</span>
        </div>
        <span style="font-size: 10px; color: #64748B">Budi Santoso · Engineering Lead</span>
        <span style="font-size: 9px; color: #94A3B8; animation: kvIn .3s ease 2.3s both">14:32 WIB · {{ $isIdLocale ? 'Persetujuan otomatis via Slack/Email' : 'Auto-approved via Slack/Email' }}</span>
      </div>

      <!-- System 3: Calendar & Attendance Sync -->
      <div style="border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px; background: #F8FAFA; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 2.5s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 11px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Kalender & Jadwal' : 'Calendar & Schedule' }}</span>
          <span style="font-size: 9px; font-weight: 700; color: #1E40AF; background: #DBEAFE; padding: 1px 6px; border-radius: 4px">Google / Outlook</span>
        </div>
        <span style="font-size: 10px; color: #64748B">{{ $isIdLocale ? 'Status OOO otomatis terpasang untuk 12–14 Nov' : 'OOO status auto-set for Nov 12–14' }}</span>
      </div>

    </div>
  </div>

  <!-- RIGHT: Action Execution Card -->
  <div style="width: 266px; height: 456px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: relative; z-index: 5; overflow: hidden; animation: kvIn .35s ease .15s both">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 8px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px">
          ⚡
        </span>
        <div style="display: flex; flex-direction: column">
          <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Action Agent · HRIS' : 'Action Agent · HRIS' }}</span>
          <span style="font-size: 9px; font-weight: 600; color: #16A34A">● {{ $isIdLocale ? 'siap eksekusi' : 'ready' }}</span>
        </div>
      </div>
      <span style="font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 2.7s both">
        ✓ {{ $isIdLocale ? 'Tereksekusi' : 'Executed' }}
      </span>
    </div>

    <!-- Messages & Action Flow Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center">
      <!-- 1. Employee Command (0.45s) -->
      <div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; font-size: 11px; line-height: 1.45; max-width: 88%; animation: kvIn .35s ease 0.45s both">
        {{ $isIdLocale ? 'Tolong ajukan cuti tahunan saya untuk 12–14 Nov (3 hari).' : 'Submit my annual leave request for Nov 12–14 (3 days).' }}
        <span style="display: block; font-size: 8.5px; color: #94A3B8; text-align: right; margin-top: 3px">09:16</span>
      </div>

      <!-- 2. Interactive Action Card (1.2s) -->
      <div style="background: #F8FAFA; border: 1.5px solid #13803F; border-radius: 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; box-shadow: 0 4px 12px rgba(19,128,63,0.06); animation: kvIn .35s ease 1.2s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 9.5px; font-weight: 800; color: #13803F; letter-spacing: 0.05em">ACTION AGENT · LEAVE REQUEST</span>
          <span style="font-size: 9px; font-weight: 700; color: #64748B">#LR-8821</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center">
          <div>
            <strong style="font-size: 12px; color: #0E1A24; display: block">3 {{ $isIdLocale ? 'Hari Kerja · 12–14 Nov' : 'Work Days · Nov 12–14' }}</strong>
            <span style="font-size: 9.5px; color: #64748B">{{ $isIdLocale ? 'Cuti Tahunan Pribadi' : 'Personal Annual Leave' }}</span>
          </div>
          <!-- Approve Button State -->
          <div style="position: relative; display: grid">
            <span style="grid-area: 1 / 1; padding: 5px 10px; border-radius: 6px; background: #071C33; color: #FFFFFF; font-size: 10px; font-weight: 800">{{ $isIdLocale ? 'Proses' : 'Submit' }}</span>
            <span style="grid-area: 1 / 1; padding: 5px 10px; border-radius: 6px; background: #13803F; color: #FFFFFF; font-size: 10px; font-weight: 800; text-align: center; animation: kvPopIn .3s ease 2.1s both">✓ {{ $isIdLocale ? 'Disetujui' : 'Approved' }}</span>
          </div>
        </div>
      </div>

      <!-- 3. Final Execution Confirmation Message (2.6s) -->
      <div style="align-self: flex-start; background: #F4F6F5; border-radius: 4px 14px 14px 14px; padding: 9px 12px; font-size: 11px; line-height: 1.45; color: #1E293B; animation: kvIn .35s ease 2.6s both">
        <strong style="color: #13803F; font-size: 10px; display: block">{{ $isIdLocale ? 'Action Agent Sukses' : 'Action Agent Success' }}</strong>
        {{ $isIdLocale ? 'Cuti telah disetujui manajer dan tercatat di sistem HRIS. Kalender OOO Anda sudah aktif!' : 'Leave has been approved by your manager and logged in HRIS. Your OOO calendar is now active!' }}
      </div>
    </div>

    <!-- Bottom Status Confirmation -->
    <div style="display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; padding: 6px 10px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 2.9s both">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
      {{ $isIdLocale ? 'Tersinkronisasi ke HRIS & Google Calendar' : 'Synced to HRIS & Google Calendar' }}
    </div>

  </div>

</div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $opsImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>',
    ];
@endphp
@foreach(($deptOps['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $opsImpactIcons[$i % count($opsImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 4: Payment Prioritization ===== -->
<div role="tabpanel" x-show="tab === 4" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $ppChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg>',
    ];
@endphp
@foreach(($deptPp['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $ppChallengeIcons[$i % count($ppChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)" style="align-items: flex-start; max-height: none; flex: 0 0 auto;">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H5a2 2 0 0 1 0-4h13v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h15V7"></path><path d="M16 14h.01"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptPp['solutions'][0]['title'] ?? t('kouventa.Payment Prioritization Agent', 'Payment Prioritization Agent') }}</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px">
@foreach(($deptPp['solutions'][0]['bullets'] ?? []) as $bullet)
<li style="display: flex; gap: 10px; align-items: flex-start; font-size: 14px; line-height: 1.5; color: #2B3947"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><path d="M20 6 9 17l-5-5"></path></svg>{{ is_array($bullet) ? ($bullet['text'] ?? '') : $bullet }}</li>
@endforeach
</ul>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-pp" role="img" aria-label="Animated illustration: invoices, cash flow and vendor data flow into an agent, invoices are re-ordered by priority, a payment batch is approved by a person and paid through the ERP" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 36px 32px; display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 28px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 10px; position: relative">
<span style="font-size: 12px; font-weight: 800; color: #4A5866">Invoices</span>
<div style="display: grid">
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease .1s both, kvOut .3s ease 2.7s 1 forwards">
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px"><span>Vendor C</span><span style="color: #4A5866">Due 28 Nov</span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px"><span>Vendor A</span><span style="color: #4A5866">Due 14 Nov</span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px"><span>Vendor E</span><span style="color: #4A5866">Due 30 Nov</span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px"><span>Vendor B</span><span style="color: #4A5866">Due 12 Nov</span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px"><span>Vendor D</span><span style="color: #4A5866">Due 15 Nov</span></span>
</div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 2.9s both">
<span style="display: flex; justify-content: space-between; align-items: center; padding: 12px 12px; border-radius: 10px; border: 1px solid #13803F; font-size: 13px; animation: kvGreen .3s ease 6.2s both"><span><strong>1</strong> · Vendor B</span><span style="display: grid"><span style="grid-area: 1 / 1; color: #4A5866">Due 12 Nov</span><span style="grid-area: 1 / 1; font-weight: 800; color: #0B5E2C; background: #DDF3E4; animation: kvIn .2s ease 6.2s both">✓ Paid</span></span></span>
<span style="display: flex; justify-content: space-between; align-items: center; padding: 12px 12px; border-radius: 10px; border: 1px solid #13803F; font-size: 13px; animation: kvGreen .3s ease 6.5s both"><span><strong>2</strong> · Vendor A</span><span style="display: grid"><span style="grid-area: 1 / 1; color: #4A5866">Due 14 Nov</span><span style="grid-area: 1 / 1; font-weight: 800; color: #0B5E2C; background: #DDF3E4; animation: kvIn .2s ease 6.5s both">✓ Paid</span></span></span>
<span style="display: flex; justify-content: space-between; align-items: center; padding: 12px 12px; border-radius: 10px; border: 1px solid #13803F; font-size: 13px; animation: kvGreen .3s ease 6.8s both"><span><strong>3</strong> · Vendor D</span><span style="display: grid"><span style="grid-area: 1 / 1; color: #4A5866">Due 15 Nov</span><span style="grid-area: 1 / 1; font-weight: 800; color: #0B5E2C; background: #DDF3E4; animation: kvIn .2s ease 6.8s both">✓ Paid</span></span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px; color: #4A5866"><span><strong>4</strong> · Vendor C</span><span>Next batch</span></span>
<span style="display: flex; justify-content: space-between; padding: 12px 12px; border-radius: 10px; background: #F4F6F5; font-size: 13px; color: #4A5866"><span><strong>5</strong> · Vendor E</span><span>Next batch</span></span>
</div>
</div>
<div style="display: flex; gap: 6px; flex-wrap: wrap; padding-top: 6px; animation: kvIn .3s ease .4s both">
<span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #F4F6F5">Cash flow</span>
<span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #F4F6F5">Vendor data</span>
</div>
</div>
<div style="display: flex; flex-direction: column; gap: 24px; align-items: stretch">
<div style="display: flex; align-items: center; gap: 12px">
<div style="width: 40px; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 32px; animation: kvTravel .5s linear .8s 2 both"></span></div>
<span style="width: 64px; height: 64px; position: relative; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><span style="position: absolute; inset: 0; border-radius: 50%; border: 3px solid #DCE3E0; border-top-color: #13803F; animation: kvSpin .5s linear 1.6s 2 both"></span><span style="width: 48px; height: 48px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800">AGENT</span></span>
<span style="font-size: 12px; font-weight: 700; color: #4A5866; animation: kvIn .3s ease 2.7s both">Prioritized by due date, cash flow and vendor terms</span>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px; overflow: hidden; animation: kvIn .35s ease 3.6s both">
<span style="display: flex; flex-direction: column"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #13803F">RECOMMENDED BATCH</span><strong style="font-size: 14px">Batch 1: 12 invoices, due this week</strong></span>
<span style="position: relative; display: grid; flex-shrink: 0"><span style="grid-area: 1 / 1; padding: 8px 14px; border-radius: 8px; background: #071C33; color: #FFFFFF; font-size: 12px; font-weight: 800">Approve</span><span style="grid-area: 1 / 1; padding: 12px 12px; border-radius: 10px; background: #13803F; color: #FFFFFF; font-size: 12px; font-weight: 800; text-align: center; animation: kvPopIn .3s ease 4.7s both">Approved</span><span aria-hidden="true" style="position: absolute; left: 50%; top: 55%; animation: kvCursor 1.2s ease 3.8s 1 both"><svg width="20" height="20" viewBox="0 0 24 24" fill="#0E1A24" stroke="#FFFFFF" stroke-width="1.2"><path d="M5 3l14 8-6 1.5L10 19z"></path></svg></span></span>
</div>
<div style="display: flex; align-items: center; gap: 10px; animation: kvIn .3s ease 5.8s both">
<span style="padding: 10px 14px; border-radius: 10px; background: #071C33; color: #FFFFFF; font-size: 12px; font-weight: 800">ERP</span>
<span style="font-size: 13px; font-weight: 700">Executing approved payments</span>
</div>
</div>
</div>
</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $ppImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.6-1 2.9 1 2.9-2.5 1.6-.9 2.9-3-.2L12 20l-2.4-1.8-3 .2-.9-2.9-2.5-1.6 1-2.9-1-2.9 2.5-1.6.9-2.9 3 .2Z"></path><path d="M8.5 11.5l2.5 2.5 4.5-5"></path></svg>',
    ];
@endphp
@foreach(($deptPp['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $ppImpactIcons[$i % count($ppImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 5: Bank Reconciliation ===== -->
<div role="tabpanel" x-show="tab === 5" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $brChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3M11 8v3M11 14h.01"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-7"></path></svg>',
    ];
@endphp
@foreach(($deptBr['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $brChallengeIcons[$i % count($brChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"></path><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][0]['title'] ?? t('kouventa.Transaction Matching Agent', 'Transaction Matching Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][0]['desc'] ?? t('kouventa.Automatically matches bank transactions with invoices, including partial and bulk transactions.', 'Automatically matches bank transactions with invoices, including partial and bulk transactions.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][1]['title'] ?? t('kouventa.Reconciliation Analysis Agent', 'Reconciliation Analysis Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][1]['desc'] ?? t('kouventa.Handles foreign exchange and tax considerations, identifies unmatched transactions and discrepancies, and explains the root causes.', 'Handles foreign exchange and tax considerations, identifies unmatched transactions and discrepancies, and explains the root causes.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l2.5 2.5L16 9.5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][2]['title'] ?? t('kouventa.Clearing Agent', 'Clearing Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][2]['desc'] ?? t('kouventa.Prepares clearing proposals and executes ERP clearing.', 'Prepares clearing proposals and executes ERP clearing.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="(sol === 3) || (sol === -1 && activeSol === 3)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][3]['title'] ?? t('kouventa.Audit & Notification Agent', 'Audit & Notification Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][3]['desc'] ?? t('kouventa.Logs activities and sends relevant notifications to related teams.', 'Logs activities and sends relevant notifications to related teams.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-br" role="img" aria-label="Animated illustration: bank transactions are matched to invoices, foreign exchange discrepancies are analyzed, clearing proposals are executed in ERP, and immutable audit logs notify the finance team" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

  <!-- ==================== SCENE 0: Transaction Matching Agent ==================== -->
  <div class="kv-br-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 12px">
      <div style="display: flex; align-items: center; gap: 8px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 13px">
          🔗
        </span>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Pencocokan Transaksi Otomatis' : 'Automated Transaction Matching' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 1.6s both">
        ✓ 100% {{ $isIdLocale ? 'Cocok' : 'Matched' }}
      </span>
    </div>

    <!-- Match Visualizer Layout -->
    <div style="position: relative; height: 320px; display: flex; justify-content: space-between; align-items: center">
      <!-- Left Column: Bank Transactions -->
      <div style="width: 230px; display: flex; flex-direction: column; gap: 10px; z-index: 2">
        <span style="font-size: 11px; font-weight: 800; color: #64748B; letter-spacing: 0.05em; text-transform: uppercase">{{ $isIdLocale ? 'Rekening Koran (Bank)' : 'Bank Transactions' }}</span>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .1s both">
          <div><strong style="color: #0E1A24; display: block">TRX 0412</strong><span style="font-size: 9.5px; color: #64748B">BCA Virtual Account</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 25,0 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .2s both">
          <div><strong style="color: #0E1A24; display: block">TRX 0413 · bulk</strong><span style="font-size: 9.5px; color: #64748B">Mandiri Corporate</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 40,0 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .3s both">
          <div><strong style="color: #0E1A24; display: block">TRX 0414</strong><span style="font-size: 9.5px; color: #64748B">BSI Giro</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 12,5 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .4s both">
          <div><strong style="color: #0E1A24; display: block">TRX 0415 · USD</strong><span style="font-size: 9.5px; color: #64748B">Citi Wire ($1,000)</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 15,9 jt</span>
        </div>
      </div>

      <!-- Center Connector SVG Lines -->
      <svg width="88" height="260" viewBox="0 0 88 260" fill="none" style="z-index: 1; flex-shrink: 0">
        <path d="M 0 32 C 44 32, 44 32, 88 32" stroke="#13803F" stroke-width="2.5" stroke-dasharray="90" style="--len: 90; animation: kvDraw .4s ease .5s both"></path>
        <path d="M 0 96 C 44 96, 44 96, 88 96" stroke="#13803F" stroke-width="2.5" stroke-dasharray="90" style="--len: 90; animation: kvDraw .4s ease .8s both"></path>
        <path d="M 0 96 C 44 96, 44 160, 88 160" stroke="#13803F" stroke-width="2.5" stroke-dasharray="110" style="--len: 110; animation: kvDraw .4s ease .9s both"></path>
        <path d="M 0 160 C 44 160, 44 224, 88 224" stroke="#13803F" stroke-width="2.5" stroke-dasharray="110" style="--len: 110; animation: kvDraw .4s ease 1.2s both"></path>
      </svg>

      <!-- Right Column: Invoices -->
      <div style="width: 230px; display: flex; flex-direction: column; gap: 10px; z-index: 2">
        <span style="font-size: 11px; font-weight: 800; color: #64748B; letter-spacing: 0.05em; text-transform: uppercase">{{ $isIdLocale ? 'Faktur Penjualan (ERP)' : 'Invoices' }}</span>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .1s both">
          <div><strong style="color: #0E1A24; display: block">INV-101</strong><span style="font-size: 9.5px; color: #13803F">✓ {{ $isIdLocale ? '1-ke-1 Cocok' : '1:1 Matched' }}</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 25,0 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .2s both">
          <div><strong style="color: #0E1A24; display: block">INV-102</strong><span style="font-size: 9.5px; color: #13803F">✓ {{ $isIdLocale ? 'Bulk Split' : 'Bulk Split' }}</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 22,0 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .3s both">
          <div><strong style="color: #0E1A24; display: block">INV-103</strong><span style="font-size: 9.5px; color: #13803F">✓ {{ $isIdLocale ? 'Bulk Split' : 'Bulk Split' }}</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 18,0 jt</span>
        </div>
        <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; animation: kvIn .3s ease .4s both">
          <div><strong style="color: #0E1A24; display: block">INV-104</strong><span style="font-size: 9.5px; color: #13803F">✓ {{ $isIdLocale ? 'Cocok' : 'Matched' }}</span></div>
          <span style="font-weight: 700; color: #0E1A24">Rp 12,5 jt</span>
        </div>
      </div>
    </div>

    <!-- Bottom Match Summary -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 10px; padding: 9px 14px; font-size: 11px; animation: kvPopIn .35s ease 1.7s both">
      <span style="color: #64748B">{{ $isIdLocale ? 'Hasil Pencocokan:' : 'Matching Result:' }} <strong style="color: #0E1A24">4 {{ $isIdLocale ? 'transaksi ditautkan ke 4 faktur' : 'transactions linked to 4 invoices' }}</strong></span>
      <span style="font-weight: 800; color: #13803F">● {{ $isIdLocale ? 'Siap kliring ERP' : 'Ready for ERP clearing' }}</span>
    </div>
  </div>

  <!-- ==================== SCENE 1: Reconciliation Analysis Agent ==================== -->
  <div class="kv-br-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 12px">
      <div style="display: flex; align-items: center; gap: 8px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #D97706; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 13px">
          🔍
        </span>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Analisis Selisih & Valuta Asing (FX)' : 'Discrepancy & FX Analysis' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #FEF3C7; color: #92400E; animation: kvPopIn .3s ease 0.6s both">
        ⚠ {{ $isIdLocale ? 'Selisih Terdeteksi' : 'Discrepancy Flagged' }}
      </span>
    </div>

    <!-- Comparison Cards -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px">
      <!-- Bank Wire -->
      <div style="background: #F8FAFA; border: 1.5px solid #F59E0B; border-radius: 12px; padding: 12px 14px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .3s ease .15s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; color: #B45309">TRX 0415 · BANK WIRE</span>
          <span style="font-size: 9px; padding: 2px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E; font-weight: 700">Settled 28 Oct</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 4px">
          <strong style="font-size: 15px; color: #0E1A24">Rp 15.900.000</strong>
          <span style="font-size: 11px; font-weight: 600; color: #64748B">USD 1,000 @ 15.900</span>
        </div>
      </div>

      <!-- ERP Invoice -->
      <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 12px; padding: 12px 14px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .3s ease .25s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; color: #4A5866">INV-105 · ERP RECORD</span>
          <span style="font-size: 9px; padding: 2px 6px; border-radius: 4px; background: #E2E8F0; color: #475569; font-weight: 700">Issued 15 Oct</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 4px">
          <strong style="font-size: 15px; color: #0E1A24">Rp 16.200.000</strong>
          <span style="font-size: 11px; font-weight: 600; color: #64748B">USD 1,000 @ 16.200</span>
        </div>
      </div>
    </div>

    <!-- Variance Pill -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFBEB; border: 1px dashed #F59E0B; border-radius: 10px; padding: 8px 14px; font-size: 11px; animation: kvIn .3s ease .5s both">
      <span style="font-weight: 700; color: #92400E">{{ $isIdLocale ? 'Variansi Nominal:' : 'Net Variance:' }} <strong style="color: #B45309">-Rp 300.000</strong></span>
      <span style="font-size: 10px; font-weight: 700; color: #78350F">{{ $isIdLocale ? 'Fluktuasi Kurs USD/IDR (16.200 → 15.900)' : 'Exchange Rate Movement (16,200 → 15,900)' }}</span>
    </div>

    <!-- AI Root Cause & Recommendation Card -->
    <div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease 1.1s both">
      <div style="display: flex; justify-content: space-between; align-items: center">
        <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #F2C46D">{{ $isIdLocale ? 'AKAR MASALAH (AI ROOT CAUSE)' : 'AI ROOT CAUSE ANALYSIS' }}</span>
        <span style="font-size: 9.5px; font-weight: 700; color: #2BD46A">Confidence 99.8%</span>
      </div>
      <p style="margin: 0; font-size: 11.5px; line-height: 1.5; color: #E2E8F0">
        {{ $isIdLocale ? 'Selisih Rp 300.000 terverifikasi murni akibat selisih kurs valas terealisasi (Realized FX Loss) antara tanggal terbit invoice dan tanggal penerimaan bank wire.' : 'The Rp 300,000 difference is verified as a Realized Foreign Exchange Loss between the invoice issue date and wire settlement date.' }}
      </p>
      <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 8px; margin-top: 2px">
        <span style="font-size: 10.5px; color: #94A3B8">{{ $isIdLocale ? 'Rekomendasi Tindakan:' : 'Recommended Action:' }} <strong style="color: #FFFFFF">{{ $isIdLocale ? 'Jurnal Otomatis ke GL #7120 (Rugi Selisih Kurs)' : 'Auto-post to GL #7120 (Realized FX Loss)' }}</strong></span>
        <span style="font-size: 10px; font-weight: 800; color: #2BD46A">✓ {{ $isIdLocale ? 'Siap posting' : 'Ready' }}</span>
      </div>
    </div>
  </div>

  <!-- ==================== SCENE 2: Clearing Agent ==================== -->
  <div class="kv-br-scene" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }">
    <!-- Clearing Proposal Header -->
    <div style="background: #F4F6F5; border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px; animation: kvIn .3s ease .1s both">
      <div style="display: flex; flex-direction: column">
        <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #13803F">CLEARING PROPOSAL · #CLR-2026-081</span>
        <strong style="font-size: 13.5px; color: #0E1A24">{{ $isIdLocale ? 'Kliring 4 transaksi cocok + penyesuaian selisih kurs FX' : 'Clear 4 matched items + FX adjustment' }}</strong>
      </div>
      <!-- Approve Button with Cursor Click Animation -->
      <div style="position: relative; display: grid; flex-shrink: 0">
        <span style="grid-area: 1 / 1; padding: 7px 14px; border-radius: 8px; background: #071C33; color: #FFFFFF; font-size: 11px; font-weight: 800">{{ $isIdLocale ? 'Setujui' : 'Approve' }}</span>
        <span style="grid-area: 1 / 1; padding: 7px 14px; border-radius: 8px; background: #13803F; color: #FFFFFF; font-size: 11px; font-weight: 800; text-align: center; animation: kvPopIn .3s ease 1.5s both">✓ {{ $isIdLocale ? 'Disetujui' : 'Approved' }}</span>
        <span aria-hidden="true" style="position: absolute; left: 50%; top: 55%; animation: kvCursor 1.1s ease .8s 1 both">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#0E1A24" stroke="#FFFFFF" stroke-width="1.2"><path d="M5 3l14 8-6 1.5L10 19z"></path></svg>
        </span>
      </div>
    </div>

    <!-- ERP Clearing Table -->
    <div style="border: 1px solid #E3E8E6; border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 7px; animation: kvIn .3s ease .3s both">
      <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 4px; border-bottom: 1px solid #E3E8E6">
        <span style="font-size: 10.5px; font-weight: 800; color: #64748B; text-transform: uppercase">{{ $isIdLocale ? 'Daftar Kliring ERP (SAP S/4HANA)' : 'ERP Clearing Batch (SAP S/4HANA)' }}</span>
        <span style="font-size: 9.5px; font-weight: 700; color: #13803F">Batch #POST-9921</span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 11.5px; animation: kvGreen .3s ease 1.9s both">
        <span style="color: #0E1A24">TRX 0412 ↔ INV-101</span>
        <strong style="color: #0B5E2C">✓ {{ $isIdLocale ? 'Terkonfirmasi Kliring' : 'Cleared' }}</strong>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 11.5px; animation: kvGreen .3s ease 2.1s both">
        <span style="color: #0E1A24">TRX 0413 ↔ INV-102, INV-103</span>
        <strong style="color: #0B5E2C">✓ {{ $isIdLocale ? 'Terkonfirmasi Kliring' : 'Cleared' }}</strong>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 11.5px; animation: kvGreen .3s ease 2.3s both">
        <span style="color: #0E1A24">TRX 0414 ↔ INV-104</span>
        <strong style="color: #0B5E2C">✓ {{ $isIdLocale ? 'Terkonfirmasi Kliring' : 'Cleared' }}</strong>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 11.5px; animation: kvGreen .3s ease 2.5s both">
        <span style="color: #0E1A24">TRX 0415 ↔ INV-105 + FX (GL #7120)</span>
        <strong style="color: #0B5E2C">✓ {{ $isIdLocale ? 'Terkonfirmasi Kliring' : 'Cleared' }}</strong>
      </div>
    </div>

    <!-- Execution Footer -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #DDF3E4; color: #0B5E2C; border-radius: 10px; padding: 8px 12px; font-size: 10.5px; font-weight: 700; animation: kvPopIn .3s ease 2.8s both">
      <span>✓ {{ $isIdLocale ? 'Semua jurnal penyesuaian berhasil di-posting ke Buku Besar' : 'All journal entries posted to General Ledger' }}</span>
      <span style="font-weight: 800">{{ $isIdLocale ? 'Saldo Rekonsiliasi: 0' : 'Variance: 0' }}</span>
    </div>
  </div>

  <!-- ==================== SCENE 3: Audit & Notification Agent ==================== -->
  <div class="kv-br-scene" :class="{ 'kv-active': (sol === 3) || (sol === -1 && activeSol === 3) }">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 12px">
      <div style="display: flex; align-items: center; gap: 8px">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #071C33; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 13px">
          📋
        </span>
        <span style="font-size: 13px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Jejak Audit & Notifikasi Otomatis' : 'Audit Trail Log & Team Notification' }}</span>
      </div>
      <span style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C">
        ● {{ $isIdLocale ? 'Terekam Permanen' : 'Immutable Record' }}
      </span>
    </div>

    <!-- Immutable Audit Trail Card -->
    <div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .15s both">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 6px">
        <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">AUDIT LOG #REC-8841</span>
        <span style="font-family: 'JetBrains Mono', monospace; font-size: 9px; color: #94A3B8">SHA-256: 7f8a9e...4b</span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 6px; font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: #D5DEE8">
        <div style="display: flex; gap: 8px; animation: kvIn .25s ease .3s both">
          <span style="color: #64748B">09:30:12</span>
          <span>{{ $isIdLocale ? 'Pencocokan rekening koran batch #BR-402 selesai (4 transaksi).' : 'Bank batch #BR-402 matched (4 transactions).' }}</span>
        </div>
        <div style="display: flex; gap: 8px; animation: kvIn .25s ease .6s both">
          <span style="color: #64748B">09:30:14</span>
          <span>{{ $isIdLocale ? 'Selisih FX Rp 300k dialokasikan ke Akun #7120.' : 'FX diff Rp 300k allocated to GL #7120.' }}</span>
        </div>
        <div style="display: flex; gap: 8px; animation: kvIn .25s ease .9s both">
          <span style="color: #64748B">09:30:16</span>
          <span style="color: #2BD46A">✓ {{ $isIdLocale ? 'Proposal kliring disetujui Manajer Keuangan.' : 'Clearing proposal approved by Finance Mgr.' }}</span>
        </div>
        <div style="display: flex; gap: 8px; animation: kvIn .25s ease 1.2s both">
          <span style="color: #64748B">09:30:18</span>
          <span style="color: #2BD46A">✓ {{ $isIdLocale ? 'Status SAP ERP: 4 transaksi berhasil di-posting ke GL.' : 'SAP ERP Status: 4 transactions posted to GL.' }}</span>
        </div>
      </div>
    </div>

    <!-- Notification Cards Stack -->
    <div style="display: flex; flex-direction: column; gap: 8px">
      <!-- Slack / Teams Broadcast Card -->
      <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; animation: kvIn .35s ease 1.6s both">
        <div style="display: flex; align-items: center; gap: 10px">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #4A154B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">#</div>
          <div>
            <div style="display: flex; align-items: center; gap: 6px">
              <strong style="font-size: 11.5px; color: #0E1A24">#finance-treasury</strong>
              <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; background: #E2E8F0; color: #475569">Slack</span>
            </div>
            <span style="font-size: 10px; color: #64748B; display: block">{{ $isIdLocale ? 'Rekonsiliasi harian selesai: 4 transaksi (Rp 93,4 jt) klir 100%.' : 'Daily reconciliation complete: 4 items (Rp 93.4M) 100% cleared.' }}</span>
          </div>
        </div>
        <span style="font-size: 9px; color: #94A3B8">09:30</span>
      </div>

      <!-- Verified Status Pill -->
      <div style="display: flex; align-items: center; justify-content: space-between; background: #DDF3E4; color: #0B5E2C; border-radius: 10px; padding: 8px 12px; font-size: 10.5px; font-weight: 700; animation: kvPopIn .3s ease 2.0s both">
        <span style="display: flex; align-items: center; gap: 6px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
          {{ $isIdLocale ? 'Tim Keuangan & Akuntansi telah menerima notifikasi' : 'Finance & Treasury teams notified' }}
        </span>
        <span style="font-size: 9.5px; font-weight: 800">Delivered</span>
      </div>
    </div>
  </div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $brImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"></path><path d="M14 7h7v7"></path></svg>',
    ];
@endphp
@foreach(($deptBr['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $brImpactIcons[$i % count($brImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>

</div>

<!-- ===== TAB 6: Legal & Compliance ===== -->
<div role="tabpanel" x-show="tab === 6" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $legalChallengeIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4M12 15.5h.01"></path></svg>',
    ];
@endphp
@foreach(($deptLegal['challenges'] ?? []) as $i => $ch)
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease {{ sprintf('%.2fs', 0.15 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center">{!! $legalChallengeIcons[$i % count($legalChallengeIcons)] !!}</span>
<h4 style="margin: 0; font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ is_array($ch) ? ($ch['text'] ?? '') : $ch }}</h4>
</div>
@endforeach
</div>
</div>

<div class="kv-sol-split-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa AI helps', 'How Kouventa AI helps') }}</h2>
<div class="kv-sol-btn-col" :class="{ 'has-sol': sol >= 0 }">
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][0]['title'] ?? t('kouventa.Contract Review Agent', 'Contract Review Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][0]['desc'] ?? t('kouventa.Review contracts and identify key clauses, terms, and potential issues.', 'Review contracts and identify key clauses, terms, and potential issues.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][1]['title'] ?? t('kouventa.Legal Research Agent', 'Legal Research Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][1]['desc'] ?? t('kouventa.Find relevant policies, regulations, and legal information based on the context of the contract.', 'Find relevant policies, regulations, and legal information based on the context of the contract.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="(sol === 2) || (sol === -1 && activeSol === 2)">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][2]['title'] ?? t('kouventa.Contract Insights Agent', 'Contract Insights Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][2]['desc'] ?? t('kouventa.Answer questions and summarize key terms and important information from contracts.', 'Answer questions and summarize key terms and important information from contracts.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-legal" role="img" aria-label="Animated illustration: contract clauses are reviewed and highlighted for risks, legal regulations are researched and cross-referenced, and conversational contract insights extract key terms with clause citations" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

  <!-- ==================== SCENE 0: Contract Review Agent ==================== -->
  <div class="kv-legal-scene" :class="{ 'kv-active': (sol === 0) || (sol === -1 && activeSol === 0) }">
    <!-- Left: Contract Viewer Document -->
    <div style="background: #F4F6F5; border: 1px solid #E3E8E6; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 9px; box-sizing: border-box; box-shadow: 0 4px 14px rgba(0,0,0,0.03); animation: kvIn .35s ease .1s both">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 6px">
        <span style="font-size: 11.5px; font-weight: 800; color: #0E1A24">Service_Agreement_2026.pdf</span>
        <span style="font-size: 9px; font-weight: 700; color: #64748B">Page 4/18</span>
      </div>
      <div style="height: 5px; width: 90%; border-radius: 3px; background: #D5DCD9"></div>
      <div style="height: 5px; width: 75%; border-radius: 3px; background: #D5DCD9"></div>
      
      <!-- Highlight 1: Term -->
      <div style="font-size: 11px; line-height: 1.45; padding: 5px 8px; border-radius: 6px; --c: #C9E8D3; animation: kvMark .3s ease .3s both">
        <strong style="color: #0B5E2C">Clause 3.1 Term:</strong> {{ $isIdLocale ? '24 bulan sejak tanggal penandatanganan' : '24 months from execution date' }}
      </div>
      <div style="height: 5px; width: 85%; border-radius: 3px; background: #D5DCD9"></div>
      
      <!-- Highlight 2: SLA -->
      <div style="font-size: 11px; line-height: 1.45; padding: 5px 8px; border-radius: 6px; --c: #CFDCEB; animation: kvMark .3s ease .6s both">
        <strong style="color: #1E40AF">Clause 5.2 SLA:</strong> {{ $isIdLocale ? 'Penyedia menjamin ketersediaan sistem 99.5%' : 'Provider maintains 99.5% uptime commitment' }}
      </div>
      <div style="height: 5px; width: 70%; border-radius: 3px; background: #D5DCD9"></div>
      
      <!-- Highlight 3: Renewal (Potential Risk) -->
      <div style="font-size: 11px; line-height: 1.45; padding: 5px 8px; border-radius: 6px; --c: #FFE1A8; animation: kvMark .3s ease .9s both">
        <strong style="color: #92400E">Clause 8.2 Renewal:</strong> {{ $isIdLocale ? 'Perpanjangan otomatis kecuali pembatalan 90 hari' : 'Renews automatically unless 90 days notice given' }}
      </div>
      <div style="height: 5px; width: 80%; border-radius: 3px; background: #D5DCD9"></div>
      
      <!-- Highlight 4: Termination -->
      <div style="font-size: 11px; line-height: 1.45; padding: 5px 8px; border-radius: 6px; --c: #FBE6E1; animation: kvMark .3s ease 1.2s both">
        <strong style="color: #991B1B">Clause 12.2 Termination:</strong> {{ $isIdLocale ? 'Pemberitahuan tertulis 30 hari untuk pengakhiran' : '30 days written notice for termination' }}
      </div>
      <div style="height: 5px; width: 65%; border-radius: 3px; background: #D5DCD9"></div>
    </div>

    <!-- Right: AI Review Findings Card -->
    <div style="display: flex; flex-direction: column; gap: 12px; justify-content: center">
      <!-- Risk Finding Card -->
      <div style="background: #FFF6E5; border: 1.5px solid #F2C46D; border-radius: 14px; padding: 12px 14px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.5s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 9.5px; font-weight: 800; letter-spacing: 0.08em; color: #7A4A00">{{ $isIdLocale ? 'TEMUAN RISIKO · KONFLIK KLAUSUL' : 'POTENTIAL ISSUE · CLAUSE CONFLICT' }}</span>
          <span style="font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E">{{ $isIdLocale ? 'Perhatian Tinggi' : 'High Priority' }}</span>
        </div>
        <p style="margin: 0; font-size: 11px; line-height: 1.45; color: #78350F">
          {{ $isIdLocale ? 'Clause 8.2 mensyaratkan 90 hari sebelum perpanjangan otomatis, namun Clause 12.2 memperbolehkan terminasi 30 hari. Berisiko timbul perselisihan perpanjangan otomatis tak terduga.' : 'Clause 8.2 requires 90 days notice for non-renewal, but Clause 12.2 allows convenience termination on 30 days. High risk of unintended renewal lock-in.' }}
        </p>
        <div style="border-top: 1px dashed #F2C46D; padding-top: 6px; margin-top: 2px; font-size: 10px; font-weight: 700; color: #92400E">
          {{ $isIdLocale ? 'Saran Redaksi: Samakan jendela notifikasi menjadi 60 hari.' : 'Recommendation: Standardize notice window to 60 days.' }}
        </div>
      </div>

      <!-- Compliance Summary Card -->
      <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 12px; padding: 10px 14px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.8s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Audit Komitmen SLA' : 'SLA Obligation Audit' }}</span>
          <span style="font-size: 9px; font-weight: 700; color: #16A34A">✓ {{ $isIdLocale ? 'Sesuai Standar' : 'Compliant' }}</span>
        </div>
        <span style="font-size: 10.5px; color: #64748B">{{ $isIdLocale ? 'Target 99.5% uptime memenuhi pedoman vendor standar perusahaan.' : '99.5% uptime commitment aligns with corporate vendor policy.' }}</span>
      </div>

      <!-- Bottom Status Pill -->
      <div style="display: flex; align-items: center; justify-content: space-between; background: #DDF3E4; color: #0B5E2C; border-radius: 10px; padding: 8px 12px; font-size: 10.5px; font-weight: 700; animation: kvPopIn .3s ease 2.1s both">
        <span>✓ {{ $isIdLocale ? 'Audit kontrak selesai · 1 konflik ditemukan' : 'Contract review complete · 1 issue flagged' }}</span>
        <span style="font-weight: 800">100% Scanned</span>
      </div>
    </div>
  </div>

  <!-- ==================== SCENE 1: Legal Research Agent ==================== -->
  <div class="kv-legal-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">
    <!-- Left: Regulatory & Statutory Knowledge Base -->
    <div style="background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; box-sizing: border-box; box-shadow: 0 4px 14px rgba(0,0,0,0.03); animation: kvIn .35s ease .1s both">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 6px">
        <span style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase">{{ $isIdLocale ? 'Repositori Regulasi & Hukum' : 'Regulatory Knowledge Base' }}</span>
        <span style="font-size: 9.5px; font-weight: 700; color: #13803F">● {{ $isIdLocale ? 'Tersinkronisasi' : 'Synced' }}</span>
      </div>

      <!-- Law 1: UU PDP -->
      <div style="background: #FFFFFF; border: 1.5px solid #13803F; border-radius: 10px; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .3s ease .3s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <strong style="font-size: 11px; color: #0E1A24">{{ $isIdLocale ? 'UU No. 27/2022 (UU PDP)' : 'Law No. 27/2022 (PDP Law)' }}</strong>
          <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C; font-weight: 700">Pasal 56</span>
        </div>
        <p style="margin: 0; font-size: 10px; line-height: 1.4; color: #4A5866">
          {{ $isIdLocale ? 'Transfer Data Pribadi ke Luar Negeri wajib memastikan negara penerima memiliki tingkat pelindungan setara atau Standard Contractual Clauses (SCC).' : 'Cross-border personal data transfer requires equivalent level of data protection or binding Standard Contractual Clauses.' }}
        </p>
      </div>

      <!-- Policy 2: Corporate Policy -->
      <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .3s ease .5s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <strong style="font-size: 11px; color: #0E1A24">{{ $isIdLocale ? 'Kebijakan Tata Kelola Data Perusahaan' : 'Corporate Data Governance Policy' }}</strong>
          <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; background: #E2E8F0; color: #475569; font-weight: 700">SOP § 7.3</span>
        </div>
        <p style="margin: 0; font-size: 10px; line-height: 1.4; color: #4A5866">
          {{ $isIdLocale ? 'Setiap vendor cloud asing wajib menandatangani Data Processing Addendum (DPA) versi terbaru.' : 'All offshore cloud vendors must execute standard Data Processing Addendum (DPA).' }}
        </p>
      </div>
    </div>

    <!-- Right: Legal Research Compliance Memo -->
    <div style="display: flex; flex-direction: column; gap: 12px; justify-content: center">
      <div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .8s both">
        <div style="display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">LEGAL RESEARCH AGENT · OPINI KEPATUHAN</span>
          <span style="font-size: 9px; font-family: 'JetBrains Mono', monospace; color: #94A3B8">REF #LEG-402</span>
        </div>
        <div style="font-size: 10.5px; color: #94A3B8; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 6px">
          {{ $isIdLocale ? 'Konteks Klausul:' : 'Context:' }} <strong style="color: #FFFFFF">{{ $isIdLocale ? 'Pasal 14 · Penyimpanan Cloud di Singapura' : 'Clause 14 · Cloud Data Storage in Singapore' }}</strong>
        </div>
        <p style="margin: 0; font-size: 11px; line-height: 1.5; color: #E2E8F0">
          {{ $isIdLocale ? 'Berdasarkan Pasal 56 UU No. 27/2022, transfer data ke pusat data Singapura sah secara hukum bila melampirkan klausul perlindungan data setara (Standard Contractual Clauses).' : 'Under Article 56 Law No. 27/2022, data hosting in Singapore data center is compliant provided Standard Contractual Clauses (SCC) are incorporated into the agreement.' }}
        </p>
        <div style="background: rgba(255,255,255,0.06); border-radius: 8px; padding: 8px 10px; font-size: 10px; color: #F2C46D">
          {{ $isIdLocale ? 'Rekomendasi Tindakan: Lampirkan DPA v2.4 Kouventa sebelum penandatanganan.' : 'Action Required: Attach Kouventa standard DPA v2.4 before signing.' }}
        </div>
      </div>

      <!-- Bottom Status Pill -->
      <div style="display: flex; align-items: center; justify-content: space-between; background: #DDF3E4; color: #0B5E2C; border-radius: 10px; padding: 8px 12px; font-size: 10.5px; font-weight: 700; animation: kvPopIn .3s ease 1.4s both">
        <span>✓ {{ $isIdLocale ? 'Tervalidasi sesuai UU PDP & Kebijakan Internal' : 'Validated against PDP Law & Internal Policy' }}</span>
        <span style="font-weight: 800">Verified</span>
      </div>
    </div>
  </div>

  <!-- ==================== SCENE 2: Contract Insights Agent ==================== -->
  <div class="kv-legal-scene" :class="{ 'kv-active': (sol === 2) || (sol === -1 && activeSol === 2) }">
    <!-- Left: Executive Key Terms Summary -->
    <div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 12px; box-sizing: border-box; box-shadow: 0 4px 14px rgba(0,0,0,0.05); animation: kvIn .35s ease .1s both">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 6px">
        <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">{{ $isIdLocale ? 'RINGKASAN PARAMETER KUNCI' : 'KEY TERMS SUMMARY' }}</span>
        <span style="font-size: 9px; color: #94A3B8">Service Agreement</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px; font-size: 11px">
        <div style="display: flex; justify-content: space-between; padding: 6px 8px; background: rgba(255,255,255,0.05); border-radius: 6px">
          <span style="color: #94A3B8">{{ $isIdLocale ? 'Jangka Waktu' : 'Contract Term' }}</span>
          <strong style="color: #FFFFFF">24 {{ $isIdLocale ? 'Bulan (2 Tahun)' : 'Months' }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 8px; background: rgba(255,255,255,0.05); border-radius: 6px">
          <span style="color: #94A3B8">{{ $isIdLocale ? 'Komitmen SLA' : 'SLA Commitment' }}</span>
          <strong style="color: #FFFFFF">99.5% Uptime</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 8px; background: rgba(255,255,255,0.05); border-radius: 6px">
          <span style="color: #94A3B8">{{ $isIdLocale ? 'Pemberitahuan Terminasi' : 'Termination Notice' }}</span>
          <strong style="color: #2BD46A">30 {{ $isIdLocale ? 'Hari Tertulis' : 'Days Written' }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 8px; background: rgba(255,255,255,0.05); border-radius: 6px">
          <span style="color: #94A3B8">{{ $isIdLocale ? 'Yurisdiksi & Sengketa' : 'Governing Law' }}</span>
          <strong style="color: #FFFFFF">{{ $isIdLocale ? 'Indonesia · BANI' : 'Indonesia Law · BANI' }}</strong>
        </div>
      </div>

      <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 6px; font-size: 9.5px; color: #94A3B8; text-align: center">
        {{ $isIdLocale ? '18 parameter klausul telah diekstraksi secara instan' : '18 contract parameters extracted instantly' }}
      </div>
    </div>

    <!-- Right: Conversational Insights Assistant -->
    <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; box-sizing: border-box; box-shadow: 0 4px 14px rgba(0,0,0,0.03); animation: kvIn .35s ease .2s both">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 6px">
        <span style="font-size: 11px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Tanya Jawab Kontrak Instan' : 'Instant Contract Q&A' }}</span>
        <span style="font-size: 9.5px; font-weight: 700; color: #16A34A">● {{ $isIdLocale ? 'Online' : 'Online' }}</span>
      </div>

      <!-- User Inquiry Bubble -->
      <div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 8px 12px; border-radius: 14px 4px 14px 14px; font-size: 11px; max-width: 90%; animation: kvIn .3s ease .4s both">
        {{ $isIdLocale ? 'Berapa hari periode pemberitahuan untuk pengakhiran kontrak?' : 'What is the termination notice period?' }}
      </div>

      <!-- Agent Answer Bubble with Reference Citation -->
      <div style="align-self: flex-start; max-width: 95%; background: #F4F6F5; border-radius: 4px 14px 14px 14px; padding: 10px 12px; font-size: 11px; line-height: 1.45; color: #1E293B; animation: kvIn .3s ease .9s both">
        <strong style="font-size: 10px; color: #13803F; display: block; margin-bottom: 2px">Contract Insights Agent</strong>
        {{ $isIdLocale ? 'Periode pemberitahuan resmi adalah 30 hari secara tertulis untuk pengakhiran sepihak tanpa denda khusus.' : '30 days written notice is required for standard termination without additional penalty.' }}
        <div style="margin-top: 6px">
          <span style="font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C">
            ✓ {{ $isIdLocale ? 'Pasal 12.2 · Halaman 14' : 'Clause 12.2 · Page 14' }}
          </span>
        </div>
      </div>

      <!-- Bottom Citation Guarantee -->
      <div style="display: flex; align-items: center; justify-content: space-between; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 8px; padding: 6px 10px; font-size: 10px; color: #64748B; animation: kvPopIn .3s ease 1.3s both">
        <span>{{ $isIdLocale ? 'Jawaban 100% dikutip langsung dari naskah kontrak' : 'Answer grounded directly in contract text' }}</span>
        <strong style="color: #13803F">Verified</strong>
      </div>
    </div>
  </div>

</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px">
@php
    $legalImpactIcons = [
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg>',
        '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg>',
    ];
@endphp
@foreach(($deptLegal['impacts'] ?? []) as $i => $imp)
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease {{ sprintf('%.2fs', 0.20 + ($i * 0.15)) }} both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center">{!! $legalImpactIcons[$i % count($legalImpactIcons)] !!}</span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800">{{ is_array($imp) ? ($imp['text'] ?? '') : $imp }}</span>
</div>
@endforeach
</div>
</div>
</div>
</div>
</template>
</div>
</section>

<!-- ============ IMPLEMENTATION JOURNEY ============ -->
<section id="impl-journey" style="width: 100%;">
<div class="kv-container" style="padding-bottom: 88px; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $journeyTitle }}</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px">

@foreach(($journeySteps ?? []) as $i => $step)
@php
    $stepMod = $i % 4;
    $sTitle = is_array($step) ? ($step['title'] ?? '') : '';
    $sDesc = is_array($step) ? ($step['desc'] ?? '') : '';
@endphp
<button type="button" @click="replayStep({{ $i }})" aria-label="Step {{ $i + 1 }}: {{ $sTitle }} (replay illustration)" style="text-align: left; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.06); display: flex; flex-direction: column; color: #0E1A24; cursor: pointer">
<div style="height: 156px; background: #E8F3EC; position: relative; overflow: hidden">
<template x-if="stepOn[{{ $i }}]">
<div style="position: absolute; inset: 0">
@if($stepMod === 0)
<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 10px">
<span style="width: 50px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08)"></span><span style="width: 14px; height: 2px; background: #C9D2CE"></span>
<span style="width: 50px; height: 34px; border-radius: 8px; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvAmber .3s ease ' + (replayed[{{ $i }}] ? '0.6s' : '0.6s') + ' both') : 'none' }"></span><span style="width: 14px; height: 2px; background: #C9D2CE"></span>
<span style="width: 50px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08)"></span>
</div>
<span style="position: absolute; left: 24px; top: 46px; --d: 170px;" :style="{ animation: implSeen ? ('kvScan 1.4s ease ' + (replayed[{{ $i }}] ? '0.1s' : '0.1s') + ' 1 both') : 'none' }"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#071C33" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6"></circle><path d="m20 20-4.5-4.5"></path></svg></span>
@elseif($stepMod === 1)
<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 14px">
<div style="display: flex; flex-direction: column; gap: 5px">
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.1s' : '2.1s') + ' both') : 'none' }">{{ t('kouventa.Documents', 'Documents') }}</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.6s' : '2.6s') + ' both') : 'none' }">CRM</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '1.0s' : '3.0s') + ' both') : 'none' }">ERP</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '1.4s' : '3.4s') + ' both') : 'none' }">Ticketing</span>
</div>
<div style="width: 40px; height: 2px; background: #13803F; --w: 40px;" :style="{ animation: implSeen ? ('kvGrowW .4s ease ' + (replayed[{{ $i }}] ? '1.4s' : '3.4s') + ' both') : 'none' }"></div>
<span style="width: 52px; height: 52px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800">AGENT</span>
</div>
@elseif($stepMod === 2)
<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 16px">
<div style="display: flex; flex-direction: column; gap: 5px; width: 90px">
<span style="height: 18px; border-radius: 5px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.1s' : '4.1s') + ' both') : 'none' }"></span>
<span style="height: 18px; border-radius: 5px; background: #FFF6E5; border: 1px solid #F2C46D; font-size: 9px; font-weight: 800; color: #7A4A00; display: flex; align-items: center; justify-content: center;" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.6s' : '4.6s') + ' both') : 'none' }">{{ t('kouventa.Approval', 'Approval') }}</span>
<span style="height: 18px; border-radius: 5px; background: #FFFFFF; border: 1px solid #C9D2CE; box-shadow: 0 2px 6px rgba(7, 28, 51, 0.08);" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '1.0s' : '5.0s') + ' both') : 'none' }"></span>
</div>
<div style="display: flex; flex-direction: column; gap: 6px; font-size: 10px; font-weight: 700">
<span :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.6s' : '4.6s') + ' both') : 'none' }">✓ {{ t('kouventa.Rules', 'Rules') }}</span>
<span :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '1.0s' : '5.0s') + ' both') : 'none' }">✓ {{ t('kouventa.Actions', 'Actions') }}</span>
<span style="display: flex; align-items: center; gap: 4px;" :style="{ animation: implSeen ? ('kvPopIn .3s ease ' + (replayed[{{ $i }}] ? '1.4s' : '5.4s') + ' both') : 'none' }"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path></svg>{{ t('kouventa.Guardrails', 'Guardrails') }}</span>
</div>
</div>
@else
<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 16px">
<div style="display: flex; flex-direction: column; align-items: center; gap: 6px">
<span style="width: 44px; height: 24px; border-radius: 999px; background: #9AA8A2; padding: 2px; box-sizing: border-box; display: flex;" :style="{ animation: implSeen ? ('kvLiveOn .3s ease ' + (replayed[{{ $i }}] ? '0.6s' : '6.6s') + ' both') : 'none' }"><span style="width: 20px; height: 20px; border-radius: 50%; background: #FFFFFF; box-shadow: 0 1px 3px rgba(7,28,51,0.3);" :style="{ animation: implSeen ? ('kvKnob .3s ease ' + (replayed[{{ $i }}] ? '0.1s' : '6.1s') + ' both') : 'none' }"></span></span>
<span style="font-size: 11px; font-weight: 800; color: #0B5E2C;" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.6s' : '6.6s') + ' both') : 'none' }">Live</span>
</div>
<div style="display: flex; align-items: flex-end; gap: 4px; height: 40px;" :style="{ animation: implSeen ? ('kvIn .25s ease ' + (replayed[{{ $i }}] ? '0.6s' : '6.6s') + ' both') : 'none' }"><span style="width: 8px; height: 14px; background: #4FAE74; border-radius: 2px"></span><span style="width: 8px; height: 24px; background: #2A8F52; border-radius: 2px"></span><span style="width: 8px; height: 32px; background: #0B5E2C; border-radius: 2px"></span></div>
<div style="display: grid; grid-template-columns: repeat(3, 16px); gap: 5px">
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33;" :style="{ animation: implSeen ? ('kvPopIn .25s ease ' + (replayed[{{ $i }}] ? '1.0s' : '7.0s') + ' both') : 'none' }"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33;" :style="{ animation: implSeen ? ('kvPopIn .25s ease ' + (replayed[{{ $i }}] ? '1.0s' : '7.0s') + ' both') : 'none' }"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33;" :style="{ animation: implSeen ? ('kvPopIn .25s ease ' + (replayed[{{ $i }}] ? '1.4s' : '7.4s') + ' both') : 'none' }"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33;" :style="{ animation: implSeen ? ('kvPopIn .25s ease ' + (replayed[{{ $i }}] ? '1.4s' : '7.4s') + ' both') : 'none' }"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33;" :style="{ animation: implSeen ? ('kvPopIn .25s ease ' + (replayed[{{ $i }}] ? '1.4s' : '7.4s') + ' both') : 'none' }"></span>
</div>
</div>
@endif
</div>
</template>
</div>
<div style="padding: 20px 22px 24px; display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 10px"><span style="width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #4A5866; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800;" :style="{ animation: implSeen ? ('kvStepOn .3s ease ' + (replayed[{{ $i }}] ? '0s' : '{{ $stepMod * 2 }}s') + ' both') : 'none' }">{{ $i + 1 }}</span><span style="font-size: 20px; font-weight: 800">{{ $sTitle }}</span></span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ $sDesc }}</span>
</div>
</button>
@endforeach

</div>
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
<a href="{{ $ctaPrimary['url'] ?? '#solution-tabs' }}" @click.prevent="pickTab(0, true)" class="kv-btn-action kv-btn-navy" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; white-space: nowrap; text-decoration: none">{{ $ctaPrimary['text'] ?? ($isIdLocale ? 'Temukan use case pertama Anda' : 'Find your first use case') }}</a>
<a href="{{ $ctaSecondary['url'] ?? '#whatsapp' }}" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-outline" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; white-space: nowrap; text-decoration: none; cursor: pointer">{{ $ctaSecondary['text'] ?? ($isIdLocale ? 'Hubungi sales' : 'Talk to Sales') }}</a>
</div>
</div>
</div>
</section>

</div>
@endsection
