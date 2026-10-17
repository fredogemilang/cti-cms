@extends('kouventa::layouts.app')

@section('content')
@php
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'Seamless omnichannel', 'main' => 'customer experience.']);
    $heroSubtitle = $page?->block('hero_subtitle', 'From marketing campaigns and customer support to operational updates, Kouventa helps businesses connect, respond, and communicate more effectively.');
    $heroCta = $page?->buttonBlock('hero_cta', ['text' => 'Request a Demo', 'url' => '/book-demo']);
    $bottomCtaTitle = $page?->block('bottom_cta_title', 'Ready to improve every customer conversation?');
    $bottomCtaSubtitle = $page?->block('bottom_cta_subtitle', "Tell us about your team's needs and we'll show you how Kouventa fits.");
@endphp
<div class="kv-canvas-wrap" x-data="{
    tab: (function() {
        const p = new URLSearchParams(window.location.search);
        const v = parseInt(p.get('case')) || parseInt(p.get('tab'));
        return (!isNaN(v) && v >= 0 && v <= 2) ? v : 0;
    })(),
    sol: -1,
    pickTab(i, scroll = false) {
        this.tab = i;
        this.sol = -1;
        if (scroll) {
            this.$nextTick(() => {
                const el = document.getElementById('solution-tabs');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    }
}">

<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Solutions" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ t('kouventa.Solutions', 'Solutions') }}</span>
<a href="{{ localized_url('/solutions/ai') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</a>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 72px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? '' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? '' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">{{ $heroSubtitle }}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">{{ $heroCta['text'] ?? 'Request a Demo' }}</a>
<button type="button" @click="pickTab(tab, true)" style="background: transparent; color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28)">{{ t('kouventa.See How It Works', 'See How It Works') }}</button>
</div>
</div>

<!-- header illustration: four channels flow into one seamless customer experience -->
<div role="img" aria-label="Animated illustration: WhatsApp, Instagram, Facebook and Live Chat flow into one customer view with a five-star experience" style="width: 420px; height: 340px; position: relative">
<svg width="420" height="340" viewBox="0 0 420 340" fill="none" style="position: absolute; inset: 0">
<path d="M60 48 C 110 48, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.2s both"></path>
<path d="M60 128 C 110 128, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.4s both"></path>
<path d="M60 208 C 110 208, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.6s both"></path>
<path d="M60 288 C 110 288, 110 170, 150 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="260" style="--len: 260; animation: kvDraw .7s ease 1.8s both"></path>
<path d="M214 170 L 240 170" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-dasharray="40" style="--len: 40; animation: kvDraw .4s ease 2.5s both"></path>
</svg>
<span style="position: absolute; left: 4px; top: 20px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.2s both"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 100px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.5s both"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="Instagram" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 180px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.7s both"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="Facebook" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 260px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.9s both"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="Live Chat" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 150px; top: 138px; width: 64px; height: 64px; border-radius: 20px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(43,212,106,0.35); animation: kvPopIn .45s ease 2.1s both"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path></svg></span>
<div style="position: absolute; left: 240px; top: 112px; width: 180px; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 8px; animation: kvPopIn .45s ease 2.8s both">
<span style="display: flex; align-items: center; gap: 8px"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F2DCC4; color: #8A5A00; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center">R</span><span style="display: flex; flex-direction: column"><span style="font-size: 13px; font-weight: 800">Rina</span><span style="font-size: 11px; color: #4A5866; white-space: nowrap">{{ t('kouventa.1 profile · 4 channels', '1 profile · 4 channels') }}</span></span></span>
<span style="display: flex; align-items: center; gap: 3px; color: #E8A200; padding-top: 8px; border-top: 1px solid #E3E8E6"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.30s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.42s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.54s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.66s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.78s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg></span>
</div>
</div>
</div>
</section>

<!-- ============ TABS ============ -->
<section id="solution-tabs" style="padding: 88px 80px 104px; display: flex; flex-direction: column; gap: 40px">
<div role="tablist" aria-label="Solutions by role" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px">
<button type="button" role="tab" :aria-selected="tab === 0" @click="pickTab(0)" :style="tab === 0 ? 'background: #071C33; color: #FFFFFF; border-color: #071C33' : 'background: #FFFFFF; color: #0E1A24; border-color: #C9D2CE'" style="min-height: 76px; padding: 16px 22px; border-radius: 16px; border: 1px solid #C9D2CE; display: flex; align-items: center; gap: 14px; text-align: left">
<span style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 800" :style="tab === 0 ? 'background: #2BD46A; color: #06251A' : 'background: #EAF7EF; color: #0B5E2C'">S&amp;M</span>
<span style="font-size: 20px; font-weight: 800">{{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}</span>
</button>
<button type="button" role="tab" :aria-selected="tab === 1" @click="pickTab(1)" :style="tab === 1 ? 'background: #071C33; color: #FFFFFF; border-color: #071C33' : 'background: #FFFFFF; color: #0E1A24; border-color: #C9D2CE'" style="min-height: 76px; padding: 16px 22px; border-radius: 16px; border: 1px solid #C9D2CE; display: flex; align-items: center; gap: 14px; text-align: left">
<span style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 800" :style="tab === 1 ? 'background: #2BD46A; color: #06251A' : 'background: #EAF7EF; color: #0B5E2C'">CS</span>
<span style="font-size: 20px; font-weight: 800">{{ t('kouventa.Customer Service', 'Customer Service') }}</span>
</button>
<button type="button" role="tab" :aria-selected="tab === 2" @click="pickTab(2)" :style="tab === 2 ? 'background: #071C33; color: #FFFFFF; border-color: #071C33' : 'background: #FFFFFF; color: #0E1A24; border-color: #C9D2CE'" style="min-height: 76px; padding: 16px 22px; border-radius: 16px; border: 1px solid #C9D2CE; display: flex; align-items: center; gap: 14px; text-align: left">
<span style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 800" :style="tab === 2 ? 'background: #2BD46A; color: #06251A' : 'background: #EAF7EF; color: #0B5E2C'">OPS</span>
<span style="font-size: 20px; font-weight: 800">{{ t('kouventa.Operations', 'Operations') }}</span>
</button>
</div>

<!-- ===== TAB 0: Sales & Marketing ===== -->
<div role="tabpanel" x-show="tab === 0" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.15s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9l5 6M21 9l-5 6"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Limited campaign reach and personalization', 'Limited campaign reach and personalization') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.30s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Risk of losing leads due to slow responses', 'Risk of losing leads due to slow responses') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.45s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Customer conversations scattered across multiple channels', 'Customer conversations scattered across multiple channels') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.60s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4M12 15.5h.01"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Customers need a fast and secure way to verify their identity without adding friction to the customer experience', 'Customers need a fast and secure way to verify their identity without adding friction to the customer experience') }}</span>
</div>
</div>
</div>

<div style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div style="display: flex; flex-direction: column; justify-content: center; gap: 10px">
<button type="button" @click="sol = (sol === 0 ? -1 : 0)" :aria-pressed="sol === 0" :style="sol === 0 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.6s ease 0.2s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.WhatsApp Broadcast', 'WhatsApp Broadcast') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Send promotional campaigns, product launches, and seasonal offers to customers at scale.', 'Send promotional campaigns, product launches, and seasonal offers to customers at scale.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 1 ? -1 : 1)" :aria-pressed="sol === 1" :style="sol === 1 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 2.2s ease 1.8s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Chatbot', 'Chatbot') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Answer product and service inquiries with predefined or AI chatbots, or escalate to a human agent for more complex requests.', 'Answer product and service inquiries with predefined or AI chatbots, or escalate to a human agent for more complex requests.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 2 ? -1 : 2)" :aria-pressed="sol === 2" :style="sol === 2 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.2s ease 4.4s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Omnichannel', 'Omnichannel') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Manage customer conversations and respond to comments from WhatsApp, Instagram, Facebook, and Live Chat in one platform.', 'Manage customer conversations and respond to comments from WhatsApp, Instagram, Facebook, and Live Chat in one platform.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 3 ? -1 : 3)" :aria-pressed="sol === 3" :style="sol === 3 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.1s ease 5.6s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Authentication', 'Authentication') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Secure customer logins and transactions with fast, seamless WhatsApp OTP verification.', 'Secure customer logins and transactions with fast, seamless WhatsApp OTP verification.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 4 ? -1 : 4)" :aria-pressed="sol === 4" :style="sol === 4 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.4s ease 6.8s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.WhatsApp Flows', 'WhatsApp Flows') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Simplify customer data collection for purchases, loyalty program registration, membership sign-ups, and more.', 'Simplify customer data collection for purchases, loyalty program registration, membership sign-ups, and more.') }}</span>
</span>
</button>
</div>

<div id="kv-ill-sales" role="img" aria-label="Animated illustration: a campaign reaches many customers, a chatbot answers a lead instantly, conversations flow into one inbox, a customer is verified by OTP and registers through a form in chat" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 28px; display: flex; flex-direction: column; gap: 18px; animation: kvOut .4s ease 4.2s 1 forwards">
<div style="display: flex; align-items: center; gap: 14px">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg></span>
<div style="background: #F4F6F5; border-radius: 4px 14px 14px 14px; padding: 12px 14px; font-size: 14px; font-weight: 700; animation: kvIn .4s ease .2s both">New launch: 20% off this week</div>
</div>
<div style="display: flex; gap: 12px; padding-left: 70px">
<span style="width: 44px; height: 44px; border-radius: 50%; background: #C9E8D3; animation: kvPopIn .3s ease .8s both"></span>
<span style="width: 44px; height: 44px; border-radius: 50%; background: #CFDCEB; animation: kvPopIn .3s ease .9s both"></span>
<span style="width: 44px; height: 44px; border-radius: 50%; background: #F2DCC4; outline: 3px solid #13803F; outline-offset: 2px; animation: kvPopIn .3s ease 1.0s both"></span>
<span style="width: 44px; height: 44px; border-radius: 50%; background: #DCD3EE; animation: kvPopIn .3s ease 1.1s both"></span>
<span style="width: 44px; height: 44px; border-radius: 50%; background: #E6EAEE; animation: kvPopIn .3s ease 1.2s both"></span>
<span style="width: 44px; height: 44px; border-radius: 50%; background: #FBE6E1; animation: kvPopIn .3s ease 1.3s both"></span>
</div>
<div style="display: flex; flex-direction: column; gap: 10px; padding-top: 10px; font-size: 14px; line-height: 1.5">
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 11px 14px; border-radius: 14px 4px 14px 14px; animation: kvIn .35s ease 1.8s both">Is this available?</div>
<div style="display: grid">
<div style="grid-area: 1 / 1; justify-self: start; background: #F4F6F5; padding: 13px 15px; border-radius: 4px 14px 14px 14px; display: flex; gap: 5px; animation: kvIn .2s ease 2.2s both, kvOut .2s ease 2.8s 1 forwards"><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 80%; background: #F4F6F5; padding: 11px 14px; border-radius: 4px 14px 14px 14px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 2.9s both">
<span>Yes, it's in stock. Want me to reserve one for you?</span>
<span style="align-self: flex-start; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C">Replied in 2s</span>
</div>
</div>
</div>
</div>
<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: auto auto; gap: 16px; animation: kvIn .4s ease 4.4s both">
<div style="grid-column: 1 / 3; display: flex; align-items: center; gap: 12px">
<div style="display: flex; flex-direction: column; gap: 6px; width: 120px; flex-shrink: 0">
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="../assets/images/icon-whatsapp.png" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">WhatsApp</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="../assets/images/icon-channel-1.png" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Instagram</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="../assets/images/icon-channel-2.png" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Facebook</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="../assets/images/icon-channel-3.png" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Live Chat</span>
</div>
<div style="width: 60px; flex-shrink: 0; display: flex; flex-direction: column; gap: 26px">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 4.6s 2 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 4.8s 2 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 5.0s 2 both"></span></div>
</div>
<div style="flex-grow: 1; background: #F4F6F5; border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 12px; font-weight: 800">One inbox</span>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 4.8s both"><span>Rina · Is this available?</span><img src="../assets/images/icon-whatsapp.png" alt="WhatsApp" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 5.0s both"><span>Sari · Comment on post</span><img src="../assets/images/icon-channel-1.png" alt="Instagram" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 5.2s both"><span>Budi · Price question</span><img src="../assets/images/icon-channel-2.png" alt="Facebook" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
</div>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; animation: kvIn .35s ease 5.6s both">
<span style="font-size: 12px; font-weight: 800; color: #4A5866; display: flex; align-items: center; gap: 6px"><img src="../assets/images/icon-whatsapp.png" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">WhatsApp</span>
<span style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; font-size: 13px">Your code is <strong style="font-family: 'JetBrains Mono', monospace; letter-spacing: 0.1em">482917</strong></span>
<span style="align-self: flex-start; display: flex; align-items: center; gap: 6px; background: #13803F; color: #FFFFFF; padding: 7px 12px; border-radius: 999px; font-size: 13px; font-weight: 800; animation: kvPopIn .3s ease 6.2s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Verified</span>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease 6.8s both">
<span style="font-size: 12px; font-weight: 800; color: #4A5866">Member sign-up</span>
<div style="background: #FFFFFF; border-radius: 8px; padding: 8px 10px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 10px; font-weight: 700; color: #4A5866">Name</span><div style="height: 7px; border-radius: 4px; background: #0E1A24; --w: 70%; animation: kvGrowW .5s ease 7.0s both"></div></div>
<div style="background: #FFFFFF; border-radius: 8px; padding: 8px 10px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 10px; font-weight: 700; color: #4A5866">Member no.</span><div style="height: 7px; border-radius: 4px; background: #0E1A24; --w: 50%; animation: kvGrowW .5s ease 7.3s both"></div></div>
<span style="align-self: flex-start; font-size: 13px; font-weight: 800; padding: 7px 12px; border-radius: 999px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 7.8s both">Registered</span>
</div>
</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px">
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.20s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Reach more customers, more effectively', 'Reach more customers, more effectively') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.35s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 12 0V3h-4v7a2 2 0 0 1-4 0V3Z"></path><path d="M6 7h4M14 7h4"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Minimize lost leads and enhance customer experience', 'Minimize lost leads and enhance customer experience') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.50s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.More efficient sales and marketing operations', 'More efficient sales and marketing operations') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.65s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Improve customer engagement across channels', 'Improve customer engagement across channels') }}</span>
</div>
</div>
</div>

</div>

<!-- ===== TAB 1: Customer Service ===== -->
<div role="tabpanel" x-show="tab === 1" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.15s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1.5"></rect><rect x="3" y="11" width="18" height="5" rx="1.5"></rect><path d="M7 20h10"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Customers expect their complaints to be handled quickly, while customer service teams manage multiple issues and requests at the same time', 'Customers expect their complaints to be handled quickly, while customer service teams manage multiple issues and requests at the same time') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.30s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Customer service teams spend significant time handling repetitive questions, limiting their ability to focus on issues that require human assistance', 'Customer service teams spend significant time handling repetitive questions, limiting their ability to focus on issues that require human assistance') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.45s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Manually creating and recording support tickets can lead to human errors, incomplete information, and miscommunication during issue handling', 'Manually creating and recording support tickets can lead to human errors, incomplete information, and miscommunication during issue handling') }}</span>
</div>
</div>
</div>

<div style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div style="display: flex; flex-direction: column; justify-content: center; gap: 10px">
<button type="button" @click="sol = (sol === 0 ? -1 : 0)" :aria-pressed="sol === 0" :style="sol === 0 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.2s ease 0.2s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Omnichannel', 'Omnichannel') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Centralize customer complaints from WhatsApp, Instagram, Facebook, and Live Chat in one platform, making it easier to track and respond to every issue.', 'Centralize customer complaints from WhatsApp, Instagram, Facebook, and Live Chat in one platform, making it easier to track and respond to every issue.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 1 ? -1 : 1)" :aria-pressed="sol === 1" :style="sol === 1 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.8s ease 1.4s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Chatbot', 'Chatbot') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Handle common customer complaints instantly with predefined or AI-powered chatbots, while escalating complex issues to human agents when needed.', 'Handle common customer complaints instantly with predefined or AI-powered chatbots, while escalating complex issues to human agents when needed.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 2 ? -1 : 2)" :aria-pressed="sol === 2" :style="sol === 2 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.3s ease 3.2s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M13 6v2M13 11v2M13 16v2"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Ticket Creation', 'Ticket Creation') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Create support tickets from customer complaints, reducing manual work and minimizing errors or miscommunication during issue handling.', 'Create support tickets from customer complaints, reducing manual work and minimizing errors or miscommunication during issue handling.') }}</span>
</span>
</button>
<button type="button" @click="sol = (sol === 3 ? -1 : 3)" :aria-pressed="sol === 3" :style="sol === 3 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 1.5s ease 4.6s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.CSAT Survey', 'CSAT Survey') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Automatically collect customer feedback after complaint resolution to measure satisfaction and identify opportunities to improve service quality.', 'Automatically collect customer feedback after complaint resolution to measure satisfaction and identify opportunities to improve service quality.') }}</span>
</span>
</button>
</div>

<div id="kv-ill-cs" role="img" aria-label="Animated illustration: two complaints arrive in one inbox, the chatbot resolves one, the other is escalated to an agent with an auto-created ticket, and the customer gives a five-star rating" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: minmax(0, 1fr) 230px; gap: 18px">
<div style="display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 800; animation: kvIn .3s ease .1s both">One inbox</span>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .2s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #0B5E2C; display: flex; align-items: center; gap: 6px"><img src="../assets/images/icon-whatsapp.png" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">WhatsApp</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 2.0s both">Resolved</span></span></div>
<span style="font-size: 14px; font-weight: 600">Where is my order?</span>
<span style="font-size: 13px; line-height: 1.5; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 10px 10px 10px; animation: kvIn .35s ease 1.4s both"><strong style="font-size: 11px; color: #13803F">Chatbot</strong><br>It's with the courier and arrives tomorrow.</span>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .5s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #5B2E86; display: flex; align-items: center; gap: 6px"><img src="../assets/images/icon-channel-1.png" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">Instagram</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvPopIn .3s ease 2.4s both">Escalated</span></span></div>
<span style="font-size: 14px; font-weight: 600">I was charged twice for one order.</span>
<span style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; animation: kvIn .35s ease 2.8s both"><span style="width: 28px; height: 28px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 11px">DW</span>Dewi · Support agent</span>
</div>
</div>
<div style="display: flex; flex-direction: column; gap: 14px">
<div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 8px; font-size: 12px; animation: kvIn .35s ease 3.2s both">
<span style="font-size: 11px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">TICKET CREATED</span>
<span style="display: flex; justify-content: space-between; animation: kvIn .25s ease 3.5s both"><span style="color: #93A4B6">Category</span><strong>Billing</strong></span>
<span style="display: flex; justify-content: space-between; animation: kvIn .25s ease 3.7s both"><span style="color: #93A4B6">Priority</span><strong>High</strong></span>
<span style="display: flex; flex-direction: column; gap: 2px; animation: kvIn .25s ease 3.9s both"><span style="color: #93A4B6">Summary</span><strong>Double charge on one order</strong></span>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; animation: kvIn .35s ease 4.6s both">
<span style="font-size: 13px; font-weight: 700">How was our service?</span>
<div style="display: flex; gap: 4px; font-size: 26px; line-height: 1">
<span style="color: #D5DCD9; animation: kvStar .3s ease 4.9s both">★</span><span style="color: #D5DCD9; animation: kvStar .3s ease 5.05s both">★</span><span style="color: #D5DCD9; animation: kvStar .3s ease 5.2s both">★</span><span style="color: #D5DCD9; animation: kvStar .3s ease 5.35s both">★</span><span style="color: #D5DCD9; animation: kvStar .3s ease 5.5s both">★</span>
</div>
<span style="font-size: 13px; font-weight: 800; color: #0B5E2C; animation: kvIn .3s ease 5.8s both">CSAT 5/5</span>
</div>
</div>
</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px">
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.20s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Faster Complaint Resolution', 'Faster Complaint Resolution') }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t('kouventa.Instant responses and seamless escalation to human agents for more complex issues.', 'Instant responses and seamless escalation to human agents for more complex issues.') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.35s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.More Efficient Customer Service Operations', 'More Efficient Customer Service Operations') }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t('kouventa.Automate repetitive inquiries and reduce manual ticket creation, so teams can focus on more complex issues.', 'Automate repetitive inquiries and reduce manual ticket creation, so teams can focus on more complex issues.') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.50s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Improved Customer Experience', 'Improved Customer Experience') }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t('kouventa.More consistent, responsive support and customer feedback to continuously improve the service experience.', 'More consistent, responsive support and customer feedback to continuously improve the service experience.') }}</span>
</div>
</div>
</div>

</div>

<!-- ===== TAB 2: Operations ===== -->
<div role="tabpanel" x-show="tab === 2" class="kv-flex-col" style="gap: 48px">
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Challenges', 'Challenges') }}</h2>
<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.15s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 2.6.5 4.4 1.2 5.7M17 17H3s3-2 3-9c0-.8.1-1.5.4-2.2"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0M2 2l20 20"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.Sending order, delivery, payment, and appointment updates manually can be time-consuming and may lead to delays, errors, or missed customer notifications', 'Sending order, delivery, payment, and appointment updates manually can be time-consuming and may lead to delays, errors, or missed customer notifications') }}</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease 0.30s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-7"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24">{{ t('kouventa.As transaction volumes grow, manually sending recurring notifications becomes harder to manage consistently and on time', 'As transaction volumes grow, manually sending recurring notifications becomes harder to manage consistently and on time') }}</span>
</div>
</div>
</div>

<div style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.How Kouventa helps', 'How Kouventa helps') }}</h2>
<div style="display: flex; flex-direction: column; justify-content: center; gap: 10px">
<button type="button" @click="sol = (sol === 0 ? -1 : 0)" :aria-pressed="sol === 0" :style="sol === 0 ? 'border-color: #13803F; background: #EAF7EF; box-shadow: 0 0 0 3px rgba(19,128,63,.12); animation: none' : (sol >= 0 ? 'animation: none' : '')" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: kvHi 5.2s ease 0.3s 1 both; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Cross Platform Integrations', 'Cross Platform Integrations') }}</span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Trigger WhatsApp messages from business systems based on operational events, such as order confirmations, delivery updates, payment reminders, and appointment notifications.', 'Trigger WhatsApp messages from business systems based on operational events, such as order confirmations, delivery updates, payment reminders, and appointment notifications.') }}</span>
</span>
</button>
</div>

<div id="kv-ill-ops" role="img" aria-label="Animated illustration: order, delivery, payment and schedule events in business systems automatically become WhatsApp updates on a customer phone" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">
<div style="position: absolute; inset: 0; padding: 32px 28px; display: grid; grid-template-columns: 150px 90px minmax(0, 1fr); align-items: center">
<div style="display: flex; flex-direction: column; gap: 38px">
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease .3s both">Order</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 1.5s both">Delivery</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 2.7s both">Payment</span>
<span style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; animation: kvLit .3s ease 3.9s both">Schedule</span>
</div>
<div style="display: flex; flex-direction: column; gap: 78px">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 80px; animation: kvTravel .6s linear .4s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 80px; animation: kvTravel .6s linear 1.6s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 80px; animation: kvTravel .6s linear 2.8s 1 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 80px; animation: kvTravel .6s linear 4.0s 1 both"></span></div>
</div>
<div style="justify-self: center; width: 260px; height: 440px; background: #071C33; border-radius: 36px; padding: 10px; box-sizing: border-box">
<div style="width: 100%; height: 100%; background: #EEF2F0; border-radius: 28px; overflow: hidden; display: flex; flex-direction: column">
<div style="background: #0C2A48; color: #FFFFFF; padding: 20px 14px 12px; font-size: 13px; font-weight: 700">Your Brand</div>
<div style="padding: 12px; display: flex; flex-direction: column; gap: 10px; font-size: 13px">
<span style="align-self: flex-start; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 12px 12px 12px; display: flex; gap: 10px; animation: kvIn .35s ease 1.0s both">Order confirmed<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 12px 12px 12px; display: flex; gap: 10px; animation: kvIn .35s ease 2.2s both">Out for delivery<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 12px 12px 12px; display: flex; gap: 10px; animation: kvIn .35s ease 3.4s both">Payment reminder<span style="color: #13803F; font-weight: 800">✓✓</span></span>
<span style="align-self: flex-start; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 12px 12px 12px; display: flex; gap: 10px; animation: kvIn .35s ease 4.6s both">Appointment tomorrow, 10:00<span style="color: #13803F; font-weight: 800">✓✓</span></span>
</div>
</div>
</div>
</div>
</div>
</div>

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">{{ t('kouventa.Business impact', 'Business impact') }}</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px">
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.20s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Keep Customers Informed at Every Step', 'Keep Customers Informed at Every Step') }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t('kouventa.Timely notifications triggered by business events.', 'Timely notifications triggered by business events.') }}</span>
</div>
<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease 0.35s both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.6-1 2.9 1 2.9-2.5 1.6-.9 2.9-3-.2L12 20l-2.4-1.8-3 .2-.9-2.9-2.5-1.6 1-2.9-1-2.9 2.5-1.6.9-2.9 3 .2Z"></path><path d="M8.5 11.5l2.5 2.5 4.5-5"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800">{{ t('kouventa.Create More Consistent Customer Experiences', 'Create More Consistent Customer Experiences') }}</span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t('kouventa.Clear, standardized updates across every process.', 'Clear, standardized updates across every process.') }}</span>
</div>
</div>
</div>

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
<a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-navy" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ t('kouventa.Request a Demo', 'Request a Demo') }}</a>
<a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-outline" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; text-decoration: none">{{ t('kouventa.Talk to Sales', 'Talk to Sales') }}</a>
</div>
</div>
</section>

</div>
@endsection
