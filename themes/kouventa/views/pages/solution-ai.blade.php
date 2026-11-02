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
    tab: 0,
    sol: -1,
    activeSol: 0,
    _autoTimer: null,
    panelOn: true,
    stepOn: Array({{ max(4, count($journeySteps ?? [])) }}).fill(true),
    replayed: Array({{ max(4, count($journeySteps ?? [])) }}).fill(false),
    implSeen: false,
    _lockT: null,
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
        if (this._autoTimer) clearInterval(this._autoTimer);
        this._autoTimer = setInterval(() => {
            if (this.sol === -1) {
                const count = (this.aiSols[this.tab] || []).length;
                if (count > 1) {
                    this.activeSol = (this.activeSol + 1) % count;
                }
            }
        }, 6000);
    },
    lockSol(i) {
        this.sol = i;
        this.activeSol = i;
        if (this._autoTimer) clearInterval(this._autoTimer);
        if (this.tab === 0) return;
        const sols = this.aiSols[this.tab];
        const illId = this.aiIllIds[this.tab];
        if (!sols || !sols[i]) return;
        const s = sols[i];
        const st = parseFloat(s.delay) * 1000;
        let en = (parseFloat(s.delay) + parseFloat(s.dur)) * 1000 + 250;
        if (sols[i + 1]) {
            en = Math.min(en, parseFloat(sols[i + 1].delay) * 1000 - 20);
        }
        clearTimeout(this._lockT);
        setTimeout(() => {
            try {
                const root = document.getElementById(illId);
                if (!root || !root.getAnimations) return;
                const an = root.getAnimations({ subtree: true });
                an.forEach((a) => {
                    a.currentTime = st;
                    a.play();
                });
                this._lockT = setTimeout(() => {
                    an.forEach((a) => {
                        const tm = a.effect && a.effect.getTiming ? a.effect.getTiming() : {};
                        if (tm.iterations === Infinity) return;
                        a.pause();
                        a.currentTime = en;
                    });
                }, Math.max(0, en - st));
            } catch (e) {}
        }, 30);
    },
    parseTabFromUrl(url = window.location.href) {
        try {
            const u = new URL(url, window.location.origin);
            const p = u.searchParams;
            const raw = p.get('dept') !== null ? p.get('dept') : p.get('tab');
            if (raw !== null) {
                let v = parseInt(raw, 10);
                if (v === 5 && !p.has('tab')) v = 6;
                if (!isNaN(v) && v >= 0 && v <= 6) return v;
            }
        } catch(e) {}
        return 0;
    },
    pickTab(i, scroll = false) {
        clearTimeout(this._lockT);
        this.panelOn = false;
        this.tab = i;
        this.sol = -1;
        this.activeSol = 0;
        this.startAutoTimer();
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

        // If landing with dept, tab, or #solution-tabs, scroll smoothly to tabs section
        const p = new URLSearchParams(window.location.search);
        if (p.has('dept') || p.has('tab') || window.location.hash.includes('solution-tabs')) {
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
                if (u.pathname === window.location.pathname && (u.searchParams.has('dept') || u.searchParams.has('tab') || u.hash.includes('solution-tabs'))) {
                    const raw = u.searchParams.get('dept') !== null ? u.searchParams.get('dept') : u.searchParams.get('tab');
                    if (raw !== null) {
                        let target = parseInt(raw, 10);
                        if (target === 5 && !u.searchParams.has('tab')) target = 6;
                        if (!isNaN(target) && target >= 0 && target <= 6) {
                            this.pickTab(target, true);
                            window.history.pushState(null, '', u.href);
                            e.preventDefault();
                            return;
                        }
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
<nav aria-label="Solutions" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ $heroSubnavLabel }}</span>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ $heroSubnavAi }}</a>
<a href="{{ localized_url('/solutions/customer-engagement') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ $heroSubnavCe }}</a>
</nav>

<div class="kv-hero-section" style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 440px; gap: 64px; align-items: center; position: relative">
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
<section id="solution-tabs" style="padding: 88px 80px 104px; display: flex; flex-direction: column; gap: 40px">
<div style="display: flex; flex-direction: column; gap: 14px">
<div role="tablist" aria-label="Solutions by role" class="kv-sol-ai-tablist">
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 0" @click="pickTab(0)">{{ $deptCs['name'] ?? ($isIdLocale ? 'Customer Service' : 'Customer Service') }}</button>
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 1" @click="pickTab(1)">{{ $deptRec['name'] ?? ($isIdLocale ? 'Rekrutmen' : 'Recruitment') }}</button>
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 2" @click="pickTab(2)">{{ $deptSales['name'] ?? ($isIdLocale ? 'Sales & Marketing' : 'Sales & Marketing') }}</button>
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 3" @click="pickTab(3)">{{ $deptOps['name'] ?? ($isIdLocale ? 'Operasional Bisnis' : 'Business Operations') }}</button>
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 4 || tab === 5" @click="if (tab !== 4 && tab !== 5) pickTab(4)">{{ $isIdLocale ? 'Finance' : 'Finance' }}</button>
<button type="button" role="tab" class="kv-sol-ai-tab" :aria-selected="tab === 6" @click="pickTab(6)">{{ $deptLegal['name'] ?? ($isIdLocale ? 'Legal & Compliance' : 'Legal & Compliance') }}</button>
</div>

<!-- Finance Sub-tabs Pill -->
<div role="tablist" aria-label="Finance solutions" x-show="tab === 4 || tab === 5" class="kv-sol-fin-wrap">
<button type="button" role="tab" class="kv-sol-fin-tab" :aria-selected="tab === 4" @click="pickTab(4)">{{ $deptPp['name'] ?? ($isIdLocale ? 'Prioritas pembayaran' : 'Payment Prioritization') }}</button>
<button type="button" role="tab" class="kv-sol-fin-tab" :aria-selected="tab === 5" @click="pickTab(5)">{{ $deptBr['name'] ?? ($isIdLocale ? 'Rekonsiliasi Bank' : 'Bank Reconciliation') }}</button>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="(sol === 0) || (sol === -1 && activeSol === 0)" style="animation: kvHi 1.6s ease 1s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptCs['solutions'][0]['title'] ?? t('kouventa.Customer Support Agent', 'Customer Support Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptCs['solutions'][0]['desc'] ?? t('kouventa.Understand customer questions in context, provide accurate information from your business knowledge, and resolve routine inquiries automatically.', 'Understand customer questions in context, provide accurate information from your business knowledge, and resolve routine inquiries automatically.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="(sol === 1) || (sol === -1 && activeSol === 1)" style="animation: kvHi 2.8s ease 2.8s 1 both">
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
.kv-arsir-pattern {
  background: repeating-linear-gradient(-45deg, #E2E8F0, #E2E8F0 3px, #F8FAFA 3px, #F8FAFA 7px);
}
@keyframes kvTypingDot {
  0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
  40% { transform: translateY(-4px); opacity: 1; }
}
@keyframes kvDashLeft {
  from { stroke-dashoffset: 0; }
  to { stroke-dashoffset: 16; }
}
@keyframes kvMatchDoc {
  0% { border-color: #CBD5E1; background: #F8FAFA; transform: scale(0.98); }
  50% { border-color: #13803F; background: #FFFFFF; transform: scale(1.02); box-shadow: 0 8px 24px rgba(19, 128, 63, 0.14); }
  100% { border-color: #13803F; background: #FFFFFF; transform: scale(1); box-shadow: 0 4px 16px rgba(19, 128, 63, 0.08); }
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

  <!-- LEFT: Background Knowledge Retrieval Process (Hidden initially until typing dots start at 1.0s) -->
  <div style="width: 270px; height: 440px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; position: relative; opacity: 0; animation: kvIn .45s ease 1.0s both;">
    
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
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.05s both">
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
      <div style="border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px 14px; background: #F8FAFA; display: flex; flex-direction: column; gap: 8px; transition: all 0.3s ease; animation: kvIn .35s ease 1.1s both, kvMatchDoc .5s ease 1.4s 1 forwards">
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
        <div style="display: flex; align-items: center; gap: 6px; animation: kvPopIn .35s ease 1.6s both">
          <span style="font-size: 10px; font-weight: 800; color: #0B5E2C; background: #EAF7EF; border: 1px solid #C9E8D3; padding: 2px 7px; border-radius: 5px; display: inline-flex; align-items: center; gap: 3px">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            99.4% Match
          </span>
          <span style="font-size: 10px; font-weight: 800; color: #1D4ED8; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 2px 7px; border-radius: 5px">
            Confidence 99%
          </span>
        </div>

        <!-- Extracted Text Snippet -->
        <div style="background: #F8FAFA; border-left: 3px solid #13803F; border-radius: 6px; padding: 7px 10px; display: flex; flex-direction: column; gap: 3px; animation: kvIn .35s ease 1.8s both">
          <span style="font-size: 10px; font-weight: 800; color: #13803F">{{ $isIdLocale ? 'Bagian 3.1 · Jam Operasional' : 'Section 3.1 · Store Hours' }}</span>
          <span style="font-size: 11px; line-height: 1.4; color: #1E293B; font-weight: 500">
            {{ $isIdLocale ? 'Seluruh gerai beroperasi normal setiap hari dari jam 08:00 sampai 21:00 tanpa libur, termasuk akhir pekan dan tanggal merah.' : 'All store branches operate daily from 08:00 to 21:00 without exception, including weekends and public holidays.' }}
          </span>
        </div>
      </div>

      <!-- 3. Hatched Document: Kebijakan_Garansi_Retur.docx (Background/Unmatched) -->
      <div style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 10px 12px; background: #F8FAFA; opacity: 0.7; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.15s both">
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

    <!-- Pipeline Footer Metric -->
    <div style="background: #F1F5F9; border-radius: 8px; padding: 7px 10px; display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: #475569; animation: kvIn .35s ease 1.2s both">
      <span style="font-weight: 700; color: #1E293B">⚡ {{ $isIdLocale ? 'Pencarian Semantik: 0.18s' : 'Semantic Search: 0.18s' }}</span>
      <span style="color: #64748B">{{ $isIdLocale ? '1.420 potongan dipindai' : '1,420 chunks scanned' }}</span>
    </div>
  </div>

  <!-- Animated Dashed Connector Line (Signal flows from WhatsApp Typing to SOP Document) -->
  <svg style="position: absolute; left: 245px; top: 215px; width: 85px; height: 30px; z-index: 10; pointer-events: none; animation: kvIn .3s ease 1.05s both, kvOut .25s ease 2.35s 1 forwards">
    <path d="M 80 15 L 8 15" fill="none" stroke="#13803F" stroke-width="2.5" stroke-dasharray="5 4" style="animation: kvDashLeft 0.5s linear infinite;" />
    <polygon points="10,10 2,15 10,20" fill="#13803F" />
  </svg>

  <!-- RIGHT: Customer WhatsApp Chat Card (Foreground Interface) -->
  <div style="width: 266px; height: 440px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.04); animation: kvIn .35s ease .15s both">
    
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
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 2.75s both">{{ $isIdLocale ? 'Selesai' : 'Resolved' }}</span>
      </span>
    </div>

    <!-- Chat Messages Stack -->
    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: center">
      <!-- 1. Customer Message (Pops in at 0.5s) -->
      <div style="align-self: flex-start; max-width: 95%; background: #F8FAFA; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 9px 12px; display: flex; flex-direction: column; gap: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.5s both">
        <span style="font-size: 12px; font-weight: 600; line-height: 1.45; color: #0E1A24">
          {{ $isIdLocale ? 'Halo kak! Apakah hari ini tokonya buka? Tutupnya jam berapa ya?' : 'Hi there! Are you guys open today? What time do you close?' }}
        </span>
        <span style="align-self: flex-end; font-size: 9px; font-weight: 600; color: #94A3B8">10:14 AM</span>
      </div>

      <!-- 2 & 3: Typing Indicator morphs into AI Agent Response in the EXACT SAME slot! -->
      <div style="display: grid; align-self: flex-start; width: 100%; max-width: 98%;">
        
        <!-- Typing Indicator (Sits at top-left of this slot, morphs out at 2.35s) -->
        <div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 10px 14px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform-origin: top left; animation: kvIn .3s ease 1.0s both, kvMorphOut .2s ease 2.35s 1 forwards">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.2s;"></span>
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #13803F; display: inline-block; animation: kvTypingDot 1.2s infinite ease-in-out 0.4s;"></span>
        </div>

        <!-- AI Agent Response (Occupies the exact same slot, expands/morphs out from the typing origin at 2.4s) -->
        <div style="grid-area: 1 / 1; background: #FFFFFF; border-radius: 2px 12px 12px 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; border: 1px solid #C9E8D3; box-shadow: 0 3px 10px rgba(19, 128, 63, 0.08); transform-origin: top left; animation: kvMorphIn .38s cubic-bezier(0.34, 1.35, 0.64, 1) 2.4s both">
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

    <!-- Bottom Status Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; font-weight: 700; color: #15803D; background: #DCFCE7; padding: 6px 10px; border-radius: 6px; animation: kvIn .3s ease 2.8s both">
      <span>✓ {{ $isIdLocale ? 'Terjawab otomatis dari SOP' : 'Auto-resolved from SOP' }}</span>
      <span style="color: #065F46; font-weight: 800">{{ $isIdLocale ? '100% Akurat' : '100% Grounded' }}</span>
    </div>
  </div>
</div>

<!-- SCENE 1: Ticket Management Agent (Left: Complaint, Right: Ticket Triage) -->
<div class="kv-cs-scene" :class="{ 'kv-active': (sol === 1) || (sol === -1 && activeSol === 1) }">

  <!-- LEFT: Customer Dispute Complaint via Live Chat -->
  <div style="width: 264px; height: 430px; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 4px 16px rgba(0,0,0,0.03); animation: kvIn .35s ease .15s both">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 10px">
      <div style="display: flex; align-items: center; gap: 7px">
        <span style="width: 26px; height: 26px; border-radius: 50%; background: #0284C7; color: #FFFFFF; display: flex; align-items: center; justify-content: center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </span>
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Pelanggan · Live Chat' : 'Customer · Live Chat' }}</span>
      </div>
      <span style="display: grid; font-size: 10px; font-weight: 800">
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #E3E8E6; color: #4A5866">{{ $isIdLocale ? 'Baru' : 'New' }}</span>
        <span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvPopIn .3s ease 1.0s both">{{ $isIdLocale ? 'Tiket dibuat' : 'Ticket created' }}</span>
      </span>
    </div>

    <!-- Message Bubble -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px 12px 12px 2px; padding: 12px; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); animation: kvIn .35s ease 0.35s both">
      <span style="font-size: 13px; font-weight: 600; line-height: 1.5; color: #0E1A24">
        {{ $isIdLocale ? 'Refund saya belum masuk setelah dua minggu, dan ada potongan biaya tambahan yang tidak sesuai.' : 'My refund still hasn\'t arrived after two weeks, and I was charged an extra fee.' }}
      </span>
      <span style="align-self: flex-end; font-size: 9px; font-weight: 600; color: #94A3B8">10:15 AM</span>
    </div>

    <!-- Complex complaint tag -->
    <div style="padding: 8px 10px; border-radius: 8px; background: #FEF3C7; border: 1px solid #FDE68A; display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; color: #92400E; animation: kvIn .3s ease 0.6s both">
      <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706"></span>
      <span>{{ $isIdLocale ? 'Keluhan kompleks terdeteksi · Eskalasi tiket' : 'Complex complaint · Escalating to ticket' }}</span>
    </div>
  </div>

  <!-- RIGHT: Structured Ticket Card & Assignment -->
  <div style="width: 264px; height: 430px; background: #F8FAFA; border: 1px solid #E3E8E6; border-radius: 16px; padding: 18px 16px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; box-shadow: 0 4px 16px rgba(0,0,0,0.03); animation: kvIn .35s ease .15s both; position: relative">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E3E8E6; padding-bottom: 10px">
      <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Triase Otomatis' : 'Automated Triage' }}</span>
      <span style="font-size: 10px; font-weight: 800; color: #991B1B; background: #FEE2E2; padding: 2px 7px; border-radius: 999px">P1 · High</span>
    </div>

    <!-- Structured Ticket Card -->
    <div style="background: #071C33; color: #FFFFFF; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; gap: 8px; font-size: 11px; box-shadow: 0 4px 14px rgba(7,28,51,0.15); animation: kvIn .35s ease 0.6s both">
      <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">TICKET #KV-8921</span>
      <div style="display: flex; justify-content: space-between"><span style="color: #93A4B6">{{ $isIdLocale ? 'Kategori' : 'Category' }}</span><strong>Refund & Billing</strong></div>
      <div style="display: flex; flex-direction: column; gap: 2px"><span style="color: #93A4B6">{{ $isIdLocale ? 'Ringkasan' : 'Summary' }}</span><strong>{{ $isIdLocale ? 'Keterlambatan refund + selisih biaya' : 'Delayed refund + fee discrepancy' }}</strong></div>
      <div style="display: flex; justify-content: space-between"><span style="color: #93A4B6">{{ $isIdLocale ? 'Prioritas' : 'Priority' }}</span><strong style="color: #F87171">High (SLA 2h)</strong></div>
    </div>

    <!-- Assigned Destination Box -->
    <div style="border: 1.5px dashed #0284C7; border-radius: 12px; padding: 12px; display: flex; align-items: center; gap: 10px; background: #F0F9FF; animation: kvIn .35s ease 1.4s both">
      <span style="width: 34px; height: 34px; border-radius: 8px; background: #E0F2FE; color: #0369A1; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">BIL</span>
      <div style="display: flex; flex-direction: column">
        <span style="font-size: 12px; font-weight: 800; color: #0E1A24">{{ $isIdLocale ? 'Tim Billing & Finance' : 'Billing & Finance Team' }}</span>
        <span style="font-size: 10px; font-weight: 700; color: #0284C7; animation: kvPopIn .3s ease 2.2s both">✓ {{ $isIdLocale ? 'Ditugaskan ke antrean tim' : 'Assigned to team queue' }}</span>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="animation: kvHi 3.4s ease 0.2s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][0]['title'] ?? t('kouventa.Candidate Screening and Sourcing Agent', 'Candidate Screening and Sourcing Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][0]['desc'] ?? t('kouventa.Source candidates from multiple job portals, then screen and match candidates based on job requirements and qualifications.', 'Source candidates from multiple job portals, then screen and match candidates based on job requirements and qualifications.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="sol === 1" style="animation: kvHi 1.1s ease 3.9s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][1]['title'] ?? t('kouventa.Candidate Communication Agent', 'Candidate Communication Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][1]['desc'] ?? t('kouventa.Send interview and psychotest invitations, provide recruitment updates, and answer candidate questions throughout the hiring process.', 'Send interview and psychotest invitations, provide recruitment updates, and answer candidate questions throughout the hiring process.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="sol === 2" style="animation: kvHi 1.4s ease 5s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][2]['title'] ?? t('kouventa.Candidate Scheduling Agent', 'Candidate Scheduling Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][2]['desc'] ?? t('kouventa.Coordinate and schedule interviews and psychotests based on the availability of HR, interviewers, and candidates.', 'Coordinate and schedule interviews and psychotests based on the availability of HR, interviewers, and candidates.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="sol === 3" style="animation: kvHi 1.5s ease 6.5s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptRec['solutions'][3]['title'] ?? t('kouventa.Recruitment Insight Agent', 'Recruitment Insight Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptRec['solutions'][3]['desc'] ?? t('kouventa.Analyze candidate profiles based on interview and psychotest results to identify key insights and provide recommendations.', 'Analyze candidate profiles based on interview and psychotest results to identify key insights and provide recommendations.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-rec" role="img" aria-label="Animated illustration: CVs from job portals are ranked by match score, invitations are sent, a common interview slot is locked, and a candidate insight with a recommendation appears" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: 120px 70px minmax(0, 1fr); align-items: center; animation: kvOut .4s ease 3.6s 1 forwards">
<div style="display: flex; flex-direction: column; gap: 10px">
<span style="font-size: 12px; font-weight: 800; color: #4A5866">Job portals</span>
<span style="font-size: 12px; font-weight: 700; padding: 9px 10px; border-radius: 8px; background: #F4F6F5; animation: kvIn .3s ease .1s both">Portal 1 · 48 CVs</span>
<span style="font-size: 12px; font-weight: 700; padding: 9px 10px; border-radius: 8px; background: #F4F6F5; animation: kvIn .3s ease .25s both">Portal 2 · 31 CVs</span>
<span style="font-size: 12px; font-weight: 700; padding: 9px 10px; border-radius: 8px; background: #F4F6F5; animation: kvIn .3s ease .4s both">Portal 3 · 22 CVs</span>
</div>
<div style="display: flex; flex-direction: column; align-items: center; gap: 10px">
<div style="width: 50px; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 2px; background: #13803F; --d: 42px; animation: kvTravel .6s linear .5s 2 both"></span></div>
<span style="width: 56px; height: 56px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; animation: kvPopIn .3s ease .6s both">AGENT</span>
</div>
<div style="display: flex; flex-direction: column; gap: 8px">
<span style="font-size: 12px; font-weight: 800; color: #4A5866; animation: kvIn .3s ease 1.6s both">Ranked by job match</span>
<div style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #13803F; background: #EAF7EF; animation: kvIn .3s ease 1.8s both"><span style="width: 30px; height: 30px; border-radius: 50%; background: #C9E8D3"></span><span style="flex-grow: 1; font-size: 13px; font-weight: 700">Rani P.</span><span style="font-size: 13px; font-weight: 800; color: #0B5E2C">92%</span></div>
<div style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: #F4F6F5; animation: kvIn .3s ease 2.0s both"><span style="width: 30px; height: 30px; border-radius: 50%; background: #CFDCEB"></span><span style="flex-grow: 1; font-size: 13px; font-weight: 700">Dimas A.</span><span style="font-size: 13px; font-weight: 800">87%</span></div>
<div style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: #F4F6F5; animation: kvIn .3s ease 2.2s both"><span style="width: 30px; height: 30px; border-radius: 50%; background: #F2DCC4"></span><span style="flex-grow: 1; font-size: 13px; font-weight: 700">Sinta W.</span><span style="font-size: 13px; font-weight: 800">81%</span></div>
<div style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: #F4F6F5; animation: kvIn .3s ease 2.4s both"><span style="width: 30px; height: 30px; border-radius: 50%; background: #DCD3EE"></span><span style="flex-grow: 1; font-size: 13px; font-weight: 700">Yoga H.</span><span style="font-size: 13px; font-weight: 800">74%</span></div>
</div>
</div>
<div style="position: absolute; inset: 0; padding: 28px; display: flex; flex-direction: column; justify-content: center; gap: 14px; animation: kvIn .4s ease 3.8s both">
<div style="align-self: flex-start; max-width: 85%; background: #F4F6F5; border-radius: 4px 14px 14px 14px; padding: 12px 14px; font-size: 13px; line-height: 1.5; animation: kvIn .35s ease 4.0s both"><strong style="font-size: 11px; color: #13803F">To Rani P.</strong><br>You're invited to an interview on Thursday and an online psychotest. Please confirm your slot.</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: grid; grid-template-columns: 90px repeat(5, minmax(0, 1fr)); gap: 6px; align-items: center; font-size: 11px; font-weight: 700; animation: kvIn .35s ease 5.0s both">
<span></span><span style="text-align: center; color: #4A5866">09:00</span><span style="text-align: center; color: #4A5866">10:00</span><span style="text-align: center; color: #4A5866">11:00</span><span style="text-align: center; color: #4A5866">13:00</span><span style="text-align: center; color: #4A5866">14:00</span>
<span>HR</span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span>
<span>Interviewer</span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span>
<span>Candidate</span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span><span style="height: 18px; border-radius: 4px; background: #E3E8E6"></span><span style="height: 18px; border-radius: 4px; background: #C9E8D3"></span>
<span></span><span></span><span style="text-align: center; padding: 4px 0; border-radius: 6px; background: #13803F; color: #FFFFFF; font-weight: 800; animation: kvPopIn .3s ease 5.9s both">Locked</span><span></span><span></span><span></span>
</div>
<div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 6px; font-size: 13px; line-height: 1.5; animation: kvIn .35s ease 6.6s both">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">CANDIDATE INSIGHT · RANI P.</span>
<span>Interview: strong problem-solving and communication. Psychotest: high attention to detail.</span>
<span style="font-weight: 800; color: #2BD46A">Recommendation: proceed to final interview</span>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="animation: kvHi 3.7s ease 0.3s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][0]['title'] ?? t('kouventa.Sales Assistant Agent', 'Sales Assistant Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][0]['desc'] ?? t('kouventa.Understand customer needs, recommend relevant products, answer product and availability questions, and follow up with customers who don\'t respond.', 'Understand customer needs, recommend relevant products, answer product and availability questions, and follow up with customers who don\'t respond.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="sol === 1" style="animation: kvHi 1.5s ease 4.2s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8v8l9 5 9-5Z"></path><path d="M3 8l9 5 9-5M12 13v8"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][1]['title'] ?? t('kouventa.Order Agent', 'Order Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][1]['desc'] ?? t('kouventa.Create orders, process payments, verify transactions, and confirm purchases.', 'Create orders, process payments, verify transactions, and confirm purchases.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="sol === 2" style="animation: kvHi 2.2s ease 5.8s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptSales['solutions'][2]['title'] ?? t('kouventa.Shipment Tracking Agent', 'Shipment Tracking Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptSales['solutions'][2]['desc'] ?? t('kouventa.Track order and shipment status, provide delivery updates, and answer post-purchase questions.', 'Track order and shipment status, provide delivery updates, and answer post-purchase questions.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-sales" role="img" aria-label="Animated illustration: after hours, an agent answers a product question, follows up when the customer goes quiet, confirms the order and payment, then answers a shipment status question" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 28px; display: flex; flex-direction: column; justify-content: center; gap: 12px; font-size: 13px; line-height: 1.5; animation: kvOut .4s ease 4.0s 1 forwards">
<span style="align-self: center; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #071C33; color: #FFFFFF; animation: kvIn .3s ease .1s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>22:30 · outside working hours</span>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 10px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease .4s both">Is this available in blue?</div>
<div style="align-self: flex-start; max-width: 80%; background: #F4F6F5; padding: 10px 13px; border-radius: 4px 14px 14px 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 1.1s both"><span><strong style="font-size: 11px; color: #13803F">Sales Assistant Agent</strong><br>Yes, it's in stock in blue. Here it is:</span>
<span style="display: flex; align-items: center; gap: 10px; background: #FFFFFF; border-radius: 10px; padding: 8px"><span style="width: 44px; height: 44px; border-radius: 8px; background: #1D4F91"></span><span style="display: flex; flex-direction: column"><strong>Runner Sneakers · Blue</strong><span style="font-size: 11px; color: #13803F; font-weight: 700">In stock</span></span></span></div>
<span style="align-self: center; font-size: 11px; font-weight: 700; color: #4A5866; padding: 4px 10px; border-radius: 999px; background: #F4F6F5; animation: kvIn .3s ease 2.1s both">2 hours later · no reply</span>
<div style="align-self: flex-start; max-width: 80%; background: #F4F6F5; padding: 10px 13px; border-radius: 4px 14px 14px 14px; animation: kvIn .3s ease 2.6s both">Still interested? I can hold the blue pair for you today.</div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 10px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease 3.3s both">Yes, I'll take it.</div>
</div>
<div style="position: absolute; inset: 0; padding: 28px; display: flex; flex-direction: column; justify-content: center; gap: 14px; font-size: 13px; line-height: 1.5; animation: kvIn .4s ease 4.2s both">
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 8px">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #4A5866">ORDER</span>
<strong>Runner Sneakers · Blue · Size 42</strong>
<div style="display: flex; gap: 8px">
<span style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; padding: 5px 10px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 4.8s both">✓ Payment verified</span>
<span style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; padding: 5px 10px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 5.3s both">Purchase confirmed</span>
</div>
</div>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; font-size: 12px; font-weight: 800; text-align: center">
<span style="padding: 9px 0; border-radius: 8px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 5.9s both">Packed</span>
<span style="padding: 9px 0; border-radius: 8px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 6.2s both">Shipped</span>
<span style="padding: 9px 0; border-radius: 8px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 6.5s both">Out for delivery</span>
</div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 10px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease 6.8s both">Where is my order?</div>
<div style="align-self: flex-start; max-width: 80%; background: #F4F6F5; padding: 10px 13px; border-radius: 4px 14px 14px 14px; animation: kvIn .3s ease 7.3s both"><strong style="font-size: 11px; color: #13803F">Shipment Tracking Agent</strong><br>It's out for delivery and should arrive today.</div>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="animation: kvHi 1.7s ease 1.5s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptOps['solutions'][0]['title'] ?? t('kouventa.Knowledge Assistant Agent', 'Knowledge Assistant Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptOps['solutions'][0]['desc'] ?? t('kouventa.Find and explain information from the company\'s internal documents, policies, SOPs, and business knowledge.', 'Find and explain information from the company\'s internal documents, policies, SOPs, and business knowledge.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="sol === 1" style="animation: kvHi 3.4s ease 3.2s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptOps['solutions'][1]['title'] ?? t('kouventa.Action Agent', 'Action Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptOps['solutions'][1]['desc'] ?? t('kouventa.Turn employee requests into actions by connecting with internal systems, submitting requests, and executing routine tasks with the required approvals.', 'Turn employee requests into actions by connecting with internal systems, submitting requests, and executing routine tasks with the required approvals.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-ops" role="img" aria-label="Animated illustration: scattered documents, SOPs and systems are pulled into one agent, which answers a policy question with a source, then prepares a leave request that is approved and submitted" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; left: 0; right: 0; top: 0; height: 150px">
<span style="position: absolute; left: 50%; top: 60px; margin-left: -40px; width: 80px; padding: 6px 0; text-align: center; border-radius: 8px; background: #F4F6F5; font-size: 11px; font-weight: 800; --x: -200px; --y: -30px; animation: kvConverge 1.3s ease .1s 1 forwards">Documents</span>
<span style="position: absolute; left: 50%; top: 60px; margin-left: -40px; width: 80px; padding: 6px 0; text-align: center; border-radius: 8px; background: #F4F6F5; font-size: 11px; font-weight: 800; --x: 200px; --y: -30px; animation: kvConverge 1.3s ease .1s 1 forwards">SOPs</span>
<span style="position: absolute; left: 50%; top: 60px; margin-left: -40px; width: 80px; padding: 6px 0; text-align: center; border-radius: 8px; background: #F4F6F5; font-size: 11px; font-weight: 800; --x: -170px; --y: 40px; animation: kvConverge 1.3s ease .1s 1 forwards">HR system</span>
<span style="position: absolute; left: 50%; top: 60px; margin-left: -40px; width: 80px; padding: 6px 0; text-align: center; border-radius: 8px; background: #F4F6F5; font-size: 11px; font-weight: 800; --x: 170px; --y: 40px; animation: kvConverge 1.3s ease .1s 1 forwards">Intranet</span>
<span style="position: absolute; left: 50%; top: 44px; margin-left: -32px; width: 64px; height: 64px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; animation: kvPopIn .35s ease 1.2s both">AGENT</span>
</div>
<div style="position: absolute; left: 28px; right: 28px; top: 140px; display: flex; flex-direction: column; gap: 10px; font-size: 13px; line-height: 1.5">
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease 1.6s both">What is the leave policy?</div>
<div style="align-self: flex-start; max-width: 82%; background: #F4F6F5; padding: 9px 13px; border-radius: 4px 14px 14px 14px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .3s ease 2.2s both"><span><strong style="font-size: 11px; color: #13803F">Knowledge Assistant Agent</strong><br>Permanent employees get 12 days of annual leave per year.</span><span style="align-self: flex-start; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #DDF3E4; color: #0B5E2C">HR Policy.pdf</span></div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease 3.2s both">Submit my leave request for 12–14 Nov.</div>
<div style="align-self: flex-start; width: 82%; box-sizing: border-box; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 14px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; gap: 12px; animation: kvIn .3s ease 3.7s both">
<span style="display: flex; flex-direction: column"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #13803F">ACTION AGENT · LEAVE REQUEST</span><strong>3 days · 12–14 Nov</strong></span>
<span style="position: relative; display: grid; flex-shrink: 0"><span style="grid-area: 1 / 1; padding: 8px 14px; border-radius: 8px; background: #071C33; color: #FFFFFF; font-size: 12px; font-weight: 800">Approve</span><span style="grid-area: 1 / 1; padding: 8px 10px; border-radius: 8px; background: #13803F; color: #FFFFFF; font-size: 12px; font-weight: 800; text-align: center; animation: kvPopIn .3s ease 4.6s both">Approved</span><span aria-hidden="true" style="position: absolute; left: 50%; top: 55%; animation: kvCursor 1.2s ease 3.7s 1 both"><svg width="20" height="20" viewBox="0 0 24 24" fill="#0E1A24" stroke="#FFFFFF" stroke-width="1.2"><path d="M5 3l14 8-6 1.5L10 19z"></path></svg></span></span>
</div>
<span style="align-self: flex-start; display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; padding: 7px 12px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 5.8s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Submitted to HRIS</span>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="align-items: flex-start; max-height: none; flex: 0 0 auto; animation: kvHi 7.4s ease 0.2s 1 both">
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
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px; animation: kvIn .35s ease 3.6s both">
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="animation: kvHi 1.6s ease 0.3s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"></path><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][0]['title'] ?? t('kouventa.Transaction Matching Agent', 'Transaction Matching Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][0]['desc'] ?? t('kouventa.Automatically matches bank transactions with invoices, including partial and bulk transactions.', 'Automatically matches bank transactions with invoices, including partial and bulk transactions.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="sol === 1" style="animation: kvHi 1.8s ease 2s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][1]['title'] ?? t('kouventa.Reconciliation Analysis Agent', 'Reconciliation Analysis Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][1]['desc'] ?? t('kouventa.Handles foreign exchange and tax considerations, identifies unmatched transactions and discrepancies, and explains the root causes.', 'Handles foreign exchange and tax considerations, identifies unmatched transactions and discrepancies, and explains the root causes.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="sol === 2" style="animation: kvHi 3s ease 4.1s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l2.5 2.5L16 9.5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][2]['title'] ?? t('kouventa.Clearing Agent', 'Clearing Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][2]['desc'] ?? t('kouventa.Prepares clearing proposals and executes ERP clearing.', 'Prepares clearing proposals and executes ERP clearing.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(3)" :aria-pressed="sol === 3" style="animation: kvHi 1s ease 7.1s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptBr['solutions'][3]['title'] ?? t('kouventa.Audit & Notification Agent', 'Audit & Notification Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptBr['solutions'][3]['desc'] ?? t('kouventa.Logs activities and sends relevant notifications to related teams.', 'Logs activities and sends relevant notifications to related teams.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-br" role="img" aria-label="Animated illustration: bank transactions are matched to invoices including a split payment, an FX discrepancy is explained, a clearing proposal is approved and cleared in the ERP, and the activity is logged and notified" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; animation: kvOut .4s ease 3.9s 1 forwards">
<span style="position: absolute; left: 28px; top: 28px; font-size: 12px; font-weight: 800; color: #4A5866">Bank transactions</span>
<span style="position: absolute; left: 372px; top: 28px; font-size: 12px; font-weight: 800; color: #4A5866">Invoices</span>
<svg width="600" height="400" viewBox="0 0 600 400" fill="none" style="position: absolute; left: 0; top: 0">
<path d="M228 90 L372 90" stroke="#13803F" stroke-width="2" stroke-dasharray="150" style="--len: 150; animation: kvDraw .4s ease .4s both"></path>
<path d="M228 142 C 300 142, 300 142, 372 142" stroke="#13803F" stroke-width="2" stroke-dasharray="160" style="--len: 160; animation: kvDraw .4s ease .8s both"></path>
<path d="M228 142 C 300 142, 300 194, 372 194" stroke="#13803F" stroke-width="2" stroke-dasharray="170" style="--len: 170; animation: kvDraw .4s ease .9s both"></path>
<path d="M228 194 C 300 194, 300 246, 372 246" stroke="#13803F" stroke-width="2" stroke-dasharray="170" style="--len: 170; animation: kvDraw .4s ease 1.3s both"></path>
</svg>
<span style="position: absolute; left: 28px; top: 70px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>TRX 0412</span><span>Rp 25,0 jt</span></span>
<span style="position: absolute; left: 28px; top: 122px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>TRX 0413 · bulk</span><span>Rp 40,0 jt</span></span>
<span style="position: absolute; left: 28px; top: 174px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>TRX 0414</span><span>Rp 12,5 jt</span></span>
<span style="position: absolute; left: 28px; top: 226px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600; animation: kvAmber .3s ease 2.0s both"><span>TRX 0415 · USD</span><span>Rp 15,9 jt</span></span>
<span style="position: absolute; left: 372px; top: 70px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>INV-101</span><span>Rp 25,0 jt</span></span>
<span style="position: absolute; left: 372px; top: 122px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>INV-102</span><span>Rp 22,0 jt</span></span>
<span style="position: absolute; left: 372px; top: 174px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>INV-103</span><span>Rp 18,0 jt</span></span>
<span style="position: absolute; left: 372px; top: 226px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600"><span>INV-104</span><span>Rp 12,5 jt</span></span>
<span style="position: absolute; left: 372px; top: 278px; width: 200px; height: 40px; box-sizing: border-box; padding: 0 10px; border-radius: 8px; border: 1px solid #E3E8E6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600; animation: kvAmber .3s ease 2.0s both"><span>INV-105 · USD 1,000</span><span>Rp 16,2 jt</span></span>
<div style="position: absolute; left: 28px; right: 28px; top: 350px; background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 4px; font-size: 13px; animation: kvIn .35s ease 2.5s both">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #F2C46D">DISCREPANCY · TRX 0415 vs INV-105</span>
<span>Difference caused by exchange rate (USD → IDR) between invoice date and payment date.</span>
</div>
</div>
<div style="position: absolute; inset: 0; padding: 28px; display: flex; flex-direction: column; justify-content: center; gap: 14px; animation: kvIn .4s ease 4.1s both">
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px">
<span style="display: flex; flex-direction: column"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #13803F">CLEARING PROPOSAL</span><strong style="font-size: 14px">Clear 4 matched items + FX adjustment</strong></span>
<span style="position: relative; display: grid; flex-shrink: 0"><span style="grid-area: 1 / 1; padding: 8px 14px; border-radius: 8px; background: #071C33; color: #FFFFFF; font-size: 12px; font-weight: 800">Approve</span><span style="grid-area: 1 / 1; padding: 8px 10px; border-radius: 8px; background: #13803F; color: #FFFFFF; font-size: 12px; font-weight: 800; text-align: center; animation: kvPopIn .3s ease 5.0s both">Approved</span><span aria-hidden="true" style="position: absolute; left: 50%; top: 55%; animation: kvCursor 1.1s ease 4.2s 1 both"><svg width="20" height="20" viewBox="0 0 24 24" fill="#0E1A24" stroke="#FFFFFF" stroke-width="1.2"><path d="M5 3l14 8-6 1.5L10 19z"></path></svg></span></span>
</div>
<div style="border: 1px solid #E3E8E6; border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 11px; font-weight: 800; color: #4A5866">ERP clearing</span>
<span style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 12px; animation: kvGreen .3s ease 6.0s both"><span>TRX 0412 ↔ INV-101</span><strong style="color: #0B5E2C">Cleared</strong></span>
<span style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 12px; animation: kvGreen .3s ease 6.3s both"><span>TRX 0413 ↔ INV-102, INV-103</span><strong style="color: #0B5E2C">Cleared</strong></span>
<span style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 12px; animation: kvGreen .3s ease 6.6s both"><span>TRX 0414 ↔ INV-104</span><strong style="color: #0B5E2C">Cleared</strong></span>
<span style="display: flex; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 12px; animation: kvGreen .3s ease 6.9s both"><span>TRX 0415 ↔ INV-105 + FX</span><strong style="color: #0B5E2C">Cleared</strong></span>
</div>
<div style="display: flex; gap: 10px">
<span style="flex-grow: 1; font-family: 'JetBrains Mono', monospace; font-size: 11px; padding: 10px 12px; border-radius: 10px; background: #071C33; color: #D5DEE8; animation: kvIn .3s ease 7.2s both">log · 4 items cleared · approved by Finance Mgr</span>
<span style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; padding: 10px 12px; border-radius: 10px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 7.6s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>Finance team notified</span>
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
<button type="button" class="kv-help-sol-btn" @click="lockSol(0)" :aria-pressed="sol === 0" style="animation: kvHi 2.2s ease 0.3s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][0]['title'] ?? t('kouventa.Contract Review Agent', 'Contract Review Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][0]['desc'] ?? t('kouventa.Review contracts and identify key clauses, terms, and potential issues.', 'Review contracts and identify key clauses, terms, and potential issues.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(1)" :aria-pressed="sol === 1" style="animation: kvHi 1.5s ease 2.5s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][1]['title'] ?? t('kouventa.Legal Research Agent', 'Legal Research Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][1]['desc'] ?? t('kouventa.Find relevant policies, regulations, and legal information based on the context of the contract.', 'Find relevant policies, regulations, and legal information based on the context of the contract.') }}</span>
</span>
</button>
<button type="button" class="kv-help-sol-btn" @click="lockSol(2)" :aria-pressed="sol === 2" style="animation: kvHi 2.5s ease 4.1s 1 both">
<span class="kv-help-sol-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg></span>
<span class="kv-help-sol-text">
<h3 class="kv-help-sol-title" style="margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; color: #0E1A24">{{ $deptLegal['solutions'][2]['title'] ?? t('kouventa.Contract Insights Agent', 'Contract Insights Agent') }}</h3>
<span class="kv-help-sol-desc">{{ $deptLegal['solutions'][2]['desc'] ?? t('kouventa.Answer questions and summarize key terms and important information from contracts.', 'Answer questions and summarize key terms and important information from contracts.') }}</span>
</span>
</button>
</div>

<div class="kv-sol-ill-wrap">
<div id="kv-ill-legal" role="img" aria-label="Animated illustration: an agent highlights key clauses in a long contract, flags an auto-renewal clause with related policy references, then answers a notice period question with a clause reference and a key terms summary" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 22px; align-content: center">
<div style="background: #F4F6F5; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; position: relative">
<span style="font-size: 12px; font-weight: 800">Service Agreement.pdf</span>
<span style="height: 6px; width: 90%; border-radius: 3px; background: #D5DCD9"></span>
<span style="height: 6px; width: 75%; border-radius: 3px; background: #D5DCD9"></span>
<span style="font-size: 11px; line-height: 1.5; padding: 4px 6px; border-radius: 6px; --c: #C9E8D3; animation: kvMark .3s ease .4s both"><strong>Term:</strong> 24 months from signing</span>
<span style="height: 6px; width: 85%; border-radius: 3px; background: #D5DCD9"></span>
<span style="font-size: 11px; line-height: 1.5; padding: 4px 6px; border-radius: 6px; --c: #CFDCEB; animation: kvMark .3s ease .8s both"><strong>Obligation:</strong> provider maintains 99.5% uptime</span>
<span style="height: 6px; width: 70%; border-radius: 3px; background: #D5DCD9"></span>
<span style="font-size: 11px; line-height: 1.5; padding: 4px 6px; border-radius: 6px; --c: #FFE1A8; animation: kvMark .3s ease 1.2s both"><strong>Renewal:</strong> renews automatically unless cancelled</span>
<span style="height: 6px; width: 80%; border-radius: 3px; background: #D5DCD9"></span>
<span style="font-size: 11px; line-height: 1.5; padding: 4px 6px; border-radius: 6px; --c: #C9E8D3; animation: kvMark .3s ease 1.4s both"><strong>12.2 Termination:</strong> 30 days' written notice</span>
<span style="height: 6px; width: 60%; border-radius: 3px; background: #D5DCD9"></span>
<span style="height: 6px; width: 88%; border-radius: 3px; background: #D5DCD9"></span>
</div>
<div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px; line-height: 1.5">
<div style="background: #FFF6E5; border: 1px solid #F2C46D; border-radius: 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .3s ease 1.8s both">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #7A4A00">POTENTIAL ISSUE · AUTO-RENEWAL</span>
<div style="display: flex; gap: 6px; flex-wrap: wrap">
<span style="font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .3s ease 2.6s both">Internal Contract Policy §4</span>
<span style="font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .3s ease 2.9s both">Related regulation</span>
</div>
</div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .3s ease 4.1s both">What is the termination notice period?</div>
<div style="align-self: flex-start; max-width: 90%; background: #F4F6F5; padding: 9px 13px; border-radius: 4px 14px 14px 14px; animation: kvIn .3s ease 4.7s both"><strong style="font-size: 11px; color: #13803F">Contract Insights Agent</strong><br>30 days' written notice. <span style="font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 6px; background: #DDF3E4; color: #0B5E2C">Clause 12.2</span></div>
<div style="background: #071C33; color: #FFFFFF; border-radius: 12px; padding: 12px 14px; display: flex; flex-direction: column; gap: 6px; font-size: 12px; animation: kvIn .35s ease 5.5s both">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">KEY TERMS SUMMARY</span>
<span>Term 24 months · Auto-renewal · Notice 30 days · Uptime 99.5%</span>
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
</template>
</section>

<!-- ============ IMPLEMENTATION JOURNEY ============ -->
<section id="impl-journey" style="padding: 0 80px 88px; display: flex; flex-direction: column; gap: 32px">
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
</section>

<!-- ============ CLOSING CTA ============ -->
<section style="padding: 0 80px 104px">
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
</section>

</div>
@endsection
