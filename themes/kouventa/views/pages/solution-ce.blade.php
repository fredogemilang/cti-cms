@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap" x-data="{ caseTab: parseInt(new URLSearchParams(window.location.search).get('case')) || 0 }">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Solutions" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">Solutions</span>
<a href="" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">Artificial Intelligence</a>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">Customer Engagement</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 72px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">Seamless omnichannel <span style="color: #2BD46A">customer experience.</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">From marketing campaigns and customer support to operational updates, Kouventa helps businesses connect, respond, and communicate more effectively.</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">Request a Demo</a>
<button type="button"  style="background: transparent; color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28)">See How It Works</button>
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
<span style="position: absolute; left: 4px; top: 20px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.2s both"><img src="" alt="WhatsApp" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 100px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.5s both"><img src="" alt="Instagram" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 180px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.7s both"><img src="" alt="Facebook" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 4px; top: 260px; width: 56px; height: 56px; border-radius: 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.25); animation: kvPopIn .4s ease 0.9s both"><img src="" alt="Live Chat" style="width: 30px; height: 30px; object-fit: contain; display: block"></span>
<span style="position: absolute; left: 150px; top: 138px; width: 64px; height: 64px; border-radius: 20px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(43,212,106,0.35); animation: kvPopIn .45s ease 2.1s both"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path></svg></span>
<div style="position: absolute; left: 240px; top: 112px; width: 180px; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 8px; animation: kvPopIn .45s ease 2.8s both">
<span style="display: flex; align-items: center; gap: 8px"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F2DCC4; color: #8A5A00; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center">R</span><span style="display: flex; flex-direction: column"><span style="font-size: 13px; font-weight: 800">Rina</span><span style="font-size: 11px; color: #4A5866; white-space: nowrap">1 profile · 4 channels</span></span></span>
<span style="display: flex; align-items: center; gap: 3px; color: #E8A200; padding-top: 8px; border-top: 1px solid #E3E8E6"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.30s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.42s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.54s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.66s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="animation: kvStar .4s ease 3.78s both"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9Z"></path></svg></span>
</div>
</div>
</div>
</section>

<!-- ============ TABS ============ -->
<section id="solution-tabs" style="padding: 88px 80px 104px; display: flex; flex-direction: column; gap: 40px">
<div role="tablist" aria-label="Solutions by role" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px">

<button type="button" role="tab" aria-selected=""  style="min-height: 76px; padding: 16px 22px; border-radius: 16px; border: 1px solid ; background: ; color: ; display: flex; align-items: center; gap: 14px; text-align: left">
<span style="width: 44px; height: 44px; border-radius: 12px; background: ; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 800; color: "></span>
<span style="font-size: 20px; font-weight: 800"></span>
</button>

</div>


<div role="tabpanel" style="display: flex; flex-direction: column; gap: 48px">

<!-- challenges -->
<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">Challenges</h2>
<div style="display: grid; grid-template-columns: repeat(, minmax(0, 1fr)); gap: 16px">

<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease  both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9l5 6M21 9l-5 6"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4M12 15.5h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1.5"></rect><rect x="3" y="11" width="18" height="5" rx="1.5"></rect><path d="M7 20h10"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 2.6.5 4.4 1.2 5.7M17 17H3s3-2 3-9c0-.8.1-1.5.4-2.2"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0M2 2l20 20"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-7"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24"></span>
</div>

</div>
</div>

<!-- how kouventa helps + illustration -->
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">How Kouventa helps</h2>
<div style="display: flex; flex-direction: column; justify-content: center; gap: 10px">

<button type="button"  aria-pressed="" style="flex: 1 1 0; min-height: 88px; max-height: 140px; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid ; background: ; box-shadow: ; border-radius: 16px; padding: 14px 20px; display: flex; align-items: center; gap: 16px; animation: ; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M13 6v2M13 11v2M13 16v2"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
<span style="font-size: 16px; font-weight: 800"></span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866"></span>
</span>
</button>

</div>

<div id="kv-ill" role="img" aria-label="" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- Sales & Marketing -->

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
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">WhatsApp</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Instagram</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Facebook</span>
<span style="font-size: 12px; font-weight: 700; padding: 6px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px; white-space: nowrap"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain">Live Chat</span>
</div>
<div style="width: 60px; flex-shrink: 0; display: flex; flex-direction: column; gap: 26px">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 4.6s 2 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 4.8s 2 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 52px; animation: kvTravel .8s linear 5.0s 2 both"></span></div>
</div>
<div style="flex-grow: 1; background: #F4F6F5; border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 12px; font-weight: 800">One inbox</span>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 4.8s both"><span>Rina · Is this available?</span><img src="" alt="WhatsApp" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 5.0s both"><span>Sari · Comment on post</span><img src="" alt="Instagram" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 10px; border-radius: 8px; background: #FFFFFF; font-size: 12px; animation: kvIn .3s ease 5.2s both"><span>Budi · Price question</span><img src="" alt="Facebook" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain"></div>
</div>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; animation: kvIn .35s ease 5.6s both">
<span style="font-size: 12px; font-weight: 800; color: #4A5866; display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">WhatsApp</span>
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


<!-- Customer Service -->

<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: minmax(0, 1fr) 230px; gap: 18px">
<div style="display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 800; animation: kvIn .3s ease .1s both">One inbox</span>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .2s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #0B5E2C; display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">WhatsApp</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 2.0s both">Resolved</span></span></div>
<span style="font-size: 14px; font-weight: 600">Where is my order?</span>
<span style="font-size: 13px; line-height: 1.5; background: #FFFFFF; padding: 8px 10px; border-radius: 4px 10px 10px 10px; animation: kvIn .35s ease 1.4s both"><strong style="font-size: 11px; color: #13803F">Chatbot</strong><br>It's with the courier and arrives tomorrow.</span>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .5s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #5B2E86; display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 16px; height: 16px; flex-shrink: 0; object-fit: contain">Instagram</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvPopIn .3s ease 2.4s both">Escalated</span></span></div>
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


<!-- Operations -->

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

<!-- business impact -->
<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">Business impact</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(, minmax(0, 1fr)); gap: 16px">

<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease  both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 12 0V3h-4v7a2 2 0 0 1-4 0V3Z"></path><path d="M6 7h4M14 7h4"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.6-1 2.9 1 2.9-2.5 1.6-.9 2.9-3-.2L12 20l-2.4-1.8-3 .2-.9-2.9-2.5-1.6 1-2.9-1-2.9 2.5-1.6.9-2.9 3 .2Z"></path><path d="M8.5 11.5l2.5 2.5 4.5-5"></path></svg></span>
<span style="font-size: 20px; line-height: 1.35; font-weight: 800"></span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3"></span>
</div>

</div>
</div>


</div>

</section>

<!-- ============ CLOSING CTA ============ -->
<section style="padding: 0 80px 104px">
<div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
<h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Ready to improve every customer conversation?</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55">Tell us about your team's needs and we'll show you how Kouventa fits.</p>
</div>
<div style="display: flex; gap: 12px; flex-shrink: 0">
<a href="" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">Request a Demo</a>
<a href="" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A">Talk to Sales</a>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
