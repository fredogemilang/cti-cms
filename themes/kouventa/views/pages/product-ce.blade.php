@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap" x-data="{ stage: 0, tab: 0 }">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Products" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">Products</span>
<a href="" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">Artificial Intelligence</a>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">Customer Engagement</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 80px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<span style="align-self: flex-start; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 8px 14px; border-radius: 999px; background: rgba(43,212,106,0.12); color: #2BD46A"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>Official Meta Business Partner</span>
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">The smarter way to <span style="color: #2BD46A">engage customers.</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">Reach more customers, respond faster, and keep every conversation seamless — on WhatsApp and beyond.</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">Book a demo</a>
</div>
</div>

<!-- hero phone -->
<div role="img" aria-label="Animated illustration: a customer receives a promotion, asks a question, gets an instant answer and a verification code on WhatsApp" style="width: 340px; height: 560px; justify-self: center; background: #0C2A48; border-radius: 44px; padding: 12px; box-sizing: border-box; box-shadow: 0 40px 80px rgba(0,0,0,0.4); animation: kvFloat 6s ease-in-out infinite">
<div style="width: 100%; height: 100%; background: #EFEAE2; border-radius: 34px; overflow: hidden; display: flex; flex-direction: column">
<div style="background: #FFFFFF; color: #111B21; padding: 22px 14px 10px 10px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #E9EDEF; flex-shrink: 0">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#54656F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
<span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">YB</span>
<div style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 4px; font-size: 14.5px; font-weight: 700">Your Brand<svg width="15" height="15" viewBox="0 0 24 24" aria-label="Verified business" style="flex-shrink: 0"><path d="M12 1.8l2.4 1.8 3-.2.9 2.9 2.5 1.7-.9 2.9.9 2.9-2.5 1.7-.9 2.9-3-.2L12 20.2l-2.4-1.8-3 .2-.9-2.9-2.5-1.7.9-2.9-.9-2.9 2.5-1.7.9-2.9 3 .2Z" fill="#1D9BF0"></path><path d="M8 11.5l2.6 2.6L16 8.8" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span style="font-size: 11px; color: #667781">Official business account</span></div>
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#54656F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path></svg>
<svg width="16" height="16" viewBox="0 0 24 24" fill="#54656F" aria-hidden="true"><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
</div>
<div style="flex-grow: 1; min-height: 0; overflow: hidden; padding: 8px 10px; display: flex; flex-direction: column; gap: 7px; font-size: 13.5px; line-height: 1.45; color: #0E1A24">
<div style="align-self: flex-start; width: 82%; background: #FFFFFF; border-radius: 4px 14px 14px 14px; overflow: hidden; flex-shrink: 0; animation: kvH1 16s ease infinite both">
<div style="height: 46px; background: #071C33; display: flex; align-items: center; justify-content: center; color: #2BD46A; font-weight: 800; font-size: 17px">WEEKEND SALE</div>
<div style="padding: 9px 12px 7px">New collection is live. Tap below to shop.<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:20</span></div>
<div style="border-top: 1px solid #E9EDEF; padding: 9px; display: flex; align-items: center; justify-content: center; gap: 6px; color: #027EB5; font-weight: 600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>Shop now</div>
</div>
<div style="align-self: flex-end; max-width: 75%; background: #D9FDD3; color: #111B21; padding: 7px 10px 7px 12px; border-radius: 12px 4px 12px 12px; box-shadow: 0 1px 1px rgba(0,0,0,0.06); animation: kvH2 16s ease infinite both">Is size M still available?<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:21<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#53BDEB" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read" style="vertical-align: -1px"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></div>
<div style="display: grid">
<div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; padding: 11px 12px; border-radius: 4px 14px 14px 14px; display: flex; gap: 4px; animation: kvHt1 16s ease infinite both"><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 82%; background: #FFFFFF; padding: 8px 12px; border-radius: 4px 14px 14px 14px; animation: kvH3 16s ease infinite both">Yes, size M is in stock. Want me to reserve it for you?<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:21</span></div>
</div>
<div style="align-self: flex-end; max-width: 75%; background: #D9FDD3; color: #111B21; padding: 7px 10px 7px 12px; border-radius: 12px 4px 12px 12px; box-shadow: 0 1px 1px rgba(0,0,0,0.06); animation: kvH4 16s ease infinite both">Yes, please!<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:22<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#53BDEB" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read" style="vertical-align: -1px"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></div>
<div style="display: grid">
<div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; padding: 11px 12px; border-radius: 4px 14px 14px 14px; display: flex; gap: 4px; animation: kvHt2 16s ease infinite both"><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 85%; background: #FFFFFF; padding: 8px 12px; border-radius: 4px 14px 14px 14px; animation: kvH5 16s ease infinite both">Reserved! To confirm, your verification code is <strong style="font-family: 'JetBrains Mono', monospace; letter-spacing: 0.1em">482 917</strong><span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:22</span></div>
</div>
<div style="align-self: center; display: flex; align-items: center; gap: 8px; background: #DDF3E4; color: #0B5E2C; padding: 8px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; animation: kvH6 16s ease infinite both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>Verified · Order reserved</div>
</div>
<div style="flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 6px 10px 10px">
<span style="flex-grow: 1; height: 38px; border-radius: 999px; background: #FFFFFF; display: flex; align-items: center; padding: 0 14px; font-size: 13px; color: #8696A0">Message</span>
<span style="width: 38px; height: 38px; flex-shrink: 0; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"></rect><path d="M5 11a7 7 0 0 0 14 0M12 18v3"></path></svg></span>
</div>
</div>
</div>
</div>
</section>

<!-- ============ CHALLENGES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Every missed message is a <span style="color: #13803F">missed sale.</span></h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px">
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.2s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center; gap: 10px">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; transform-origin: 50% 50%; animation: kvWobble 1.4s ease .6s 1 both"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2M9 2h6"></path></svg></span>
<span style="display: flex; gap: 4px"><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .4s infinite"></span></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Customers wait too long for a reply</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.8s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: flex-end; gap: 6px">
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #B4412A; --h: 52px; animation: kvGrowH 1s ease 1.2s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #D9735D; --h: 36px; animation: kvGrowH 1s ease 1.3s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #E79F8E; --h: 22px; animation: kvGrowH 1s ease 1.4s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #F0C4BA; --h: 12px; animation: kvGrowH 1s ease 1.5s 1 both"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Campaigns don't reach the right people</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">Promotions sent manually reach too few customers, and there's no clear view of who received, read, or acted on them.</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 1.4s both">
<div aria-hidden="true" style="height: 56px; position: relative; width: 120px">
<span style="position: absolute; left: 0; top: 10px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: -4px; --y: -8px; animation: kvJitter 2s ease 1.8s 1 both"></span>
<span style="position: absolute; left: 44px; top: 0; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: 6px; --y: 10px; animation: kvJitter 2s ease 1.8s 1 both"></span>
<span style="position: absolute; left: 86px; top: 18px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: 8px; --y: -6px; animation: kvJitter 2s ease 1.8s 1 both"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Conversations are scattered across channels</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.</p>
</article>
</div>
</section>

<!-- ============ JOURNEY ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 14px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">Never miss a <span style="color: #13803F">message.</span></h2>
<p style="margin: 0; font-size: 18px; line-height: 1.5; color: #4A5866">From first hello to after-sales. Pick a stage to see which products help.</p>
</div>
<div role="tablist" aria-label="Customer journey stage" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 28px 0 26px">
<div style="position: relative; height: 176px">
<span aria-hidden="true" style="position: absolute; top: 76px; left: 12.5%; width: 75%; height: 4px; border-radius: 4px; background: repeating-linear-gradient(90deg, #C9D2CE 0 10px, transparent 10px 18px)"></span>
<span aria-hidden="true" style="position: absolute; top: 76px; left: 12.5%; width: ; height: 4px; border-radius: 4px; background: #13803F; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1)"></span>
<span aria-hidden="true" style="position: absolute; top: 0; left: ; transform: translateX(-50%); transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center">
<span style="animation: kvFloat 2.4s ease-in-out infinite; display: flex; flex-direction: column; align-items: center">
<span style="display: flex; align-items: center; gap: 8px; padding: 5px 14px 5px 5px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-size: 13px; font-weight: 700; white-space: nowrap; box-shadow: 0 6px 16px rgba(7, 28, 51, 0.18)"><span style="width: 26px; height: 26px; border-radius: 50%; background: #2BD46A; color: #071C33; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg></span>Your customer</span>
<span style="width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 7px solid #071C33"></span>
</span>
</span>

<button type="button" role="tab" aria-selected=""  style="position: absolute; top: 50px; left: ; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
<span style="width: 56px; height: 56px; border-radius: 50%; background: ; border: 2px solid ; color: ; display: flex; align-items: center; justify-content: center; animation: ; transition: background 0.3s, border-color 0.3s, color 0.3s"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="8" r="4"></circle><path d="M2 21a8 8 0 0 1 13.3-6"></path><path d="M19 14v6M16 17h6"></path></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"></path><path d="M15 7h6v6"></path></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect><path d="M20 20a3 3 0 0 1-3 3h-3"></path></svg></span>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: "></span>
<span style="font-size: 20px; font-weight: 800; color: "></span>
</button>

</div>
</div>
<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px 44px; display: flex; justify-content: space-between; align-items: center; gap: 48px">
<div style="display: flex; flex-direction: column; gap: 10px; max-width: 760px">
<span style="font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em"></span>
<span style="font-size: 17px; line-height: 1.6; color: #4A5866"></span>
</div>
<button type="button"  style="flex-shrink: 0; min-height: 52px; padding: 14px 22px; border-radius: 12px; border: 0; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"></path></svg></button>
</div>
</section>

<!-- ============ PRODUCTS ============ -->
<section id="products" style="padding: 48px 80px 104px; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; align-items: flex-start; gap: 16px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">One platform. <span style="color: #13803F">Zero missed messages.</span></h2>
<span style="min-height: 48px; box-sizing: border-box; display: flex; align-items: center; gap: 10px; padding: 6px 6px 6px 16px; border-radius: 999px; background: #FFFFFF; border: 1px solid #E3E8E6; font-size: 15px; color: #4A5866; white-space: nowrap"><span style="width: 10px; height: 10px; border-radius: 50%; background: #13803F"></span><span>Highlighted: how each product helps in <strong style="color: #0E1A24"></strong></span><button type="button"  style="margin-left: 4px; white-space: nowrap; flex-shrink: 0; min-height: 36px; padding: 6px 12px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px">Show all products<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></button></span>
<span style="min-height: 48px; display: flex; align-items: center; font-size: 15px; color: #4A5866">Pick a journey stage above to see which products help</span>
</div>
<div style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; align-items: stretch">
<div role="tablist" aria-label="Products" aria-orientation="vertical" style="display: flex; flex-direction: column; gap: 10px">

<button type="button" role="tab" aria-selected=""  style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid ; background: ; color: ; display: flex; align-items: center; gap: 14px; text-align: left; opacity: ">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: ; color: ; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg></span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800"></span>
<span style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span><span style="font-size: 13px; line-height: 1.4; opacity: 0.75"></span>
</span>
<span style="width: 10px; height: 10px; border-radius: 50%; background: ; flex-shrink: 0"></span>
</button>

</div>
<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1fr) 460px; gap: 40px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 18px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F"></span>
<span style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em"></span>
<span style="font-size: 17px; line-height: 1.6; color: #4A5866"></span>
<ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px">

<li style="display: flex; gap: 12px; align-items: center; font-size: 16px; font-weight: 600"><span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span></li>

</ul>
</div>

<!-- illustrations -->
<div role="img" aria-label="" style="height: 420px; border-radius: 18px; background: #F4F6F5; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center">


<div style="width: 300px; display: flex; flex-direction: column; gap: 14px">
<div style="background: #FFFFFF; border-radius: 16px; padding: 18px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 13px; font-weight: 700; color: #4A5866">Sign in to Your Brand</span>
<div style="height: 40px; border-radius: 10px; border: 1px solid #E3E8E6; display: flex; align-items: center; padding: 0 12px; font-size: 14px; color: #0E1A24">+62 812 •••• 4567</div>
<div style="display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 6px">
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 1.2s infinite both">4</span>
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 1.4s infinite both">8</span>
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 1.6s infinite both">2</span>
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 1.8s infinite both">9</span>
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 2.0s infinite both">1</span>
<span style="height: 44px; border-radius: 10px; background: #F4F6F5; display: flex; align-items: center; justify-content: center; font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 500; animation: kvLoop 8s ease 2.2s infinite both">7</span>
</div>
</div>
<div style="align-self: flex-start; background: #FFFFFF; border-radius: 4px 14px 14px 14px; padding: 12px 14px; font-size: 13px; line-height: 1.5; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 8s ease 0.3s infinite both"><strong>Your Brand</strong> · WhatsApp<br>Your code is <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600">482917</span>. Do not share it.</div>
<div style="align-self: center; display: flex; align-items: center; gap: 8px; background: #071C33; color: #FFFFFF; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; animation: kvPop 8s ease 1s infinite both"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>Verified</div>
</div>



<div style="width: 400px; display: flex; flex-direction: column; gap: 22px">
<div style="display: flex; align-items: center; gap: 0">
<div style="width: 150px; flex-shrink: 0; background: #FFFFFF; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvPulse 2.4s ease infinite">
<div style="height: 54px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">PROMO</div>
<div style="padding: 8px 10px; font-size: 11px; line-height: 1.4">Weekend sale is live.</div>
<div style="border-top: 1px solid #E3E8E6; padding: 6px; text-align: center; font-size: 11px; color: #13803F; font-weight: 700">Shop now</div>
</div>
<div style="flex-grow: 1; height: 120px; position: relative; display: flex; flex-direction: column; justify-content: space-around">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 90px; animation: kvTravel 2s linear 0s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 90px; animation: kvTravel 2s linear 0.5s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 90px; animation: kvTravel 2s linear 1s infinite"></span></div>
</div>
<div style="width: 140px; flex-shrink: 0; display: flex; flex-direction: column; gap: 8px">
<div style="background: #FFFFFF; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 12px; animation: kvLoop 6s ease 0.6s infinite both"><span style="width: 22px; height: 22px; border-radius: 50%; background: #C9E8D3"></span>Rina<span style="margin-left: auto; color: #13803F; font-weight: 700">✓✓</span></div>
<div style="background: #FFFFFF; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 12px; animation: kvLoop 6s ease 1.0s infinite both"><span style="width: 22px; height: 22px; border-radius: 50%; background: #CFDCEB"></span>Budi<span style="margin-left: auto; color: #13803F; font-weight: 700">✓✓</span></div>
<div style="background: #FFFFFF; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 12px; animation: kvLoop 6s ease 1.4s infinite both"><span style="width: 22px; height: 22px; border-radius: 50%; background: #F2DCC4"></span>Sari<span style="margin-left: auto; color: #13803F; font-weight: 700">✓✓</span></div>
<div style="background: #FFFFFF; border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 12px; animation: kvLoop 6s ease 1.8s infinite both"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DCD3EE"></span>Andi<span style="margin-left: auto; color: #13803F; font-weight: 700">✓✓</span></div>
</div>
</div>
<div style="background: #FFFFFF; border-radius: 14px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700"><span>Campaign report</span><span style="color: #4A5866; font-weight: 500">Scheduled · Sat 09:00</span></div>
<div style="display: grid; grid-template-columns: 70px minmax(0, 1fr); gap: 8px 10px; align-items: center; font-size: 12px; color: #4A5866">
<span>Delivered</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #13803F; --w: 92%; animation: kvGrow 6s ease infinite both"></div></div>
<span>Read</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #2E9E5B; --w: 70%; animation: kvGrow 6s ease 0.3s infinite both"></div></div>
<span>Clicked</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #071C33; --w: 38%; animation: kvGrow 6s ease 0.6s infinite both"></div></div>
</div>
</div>
</div>



<div style="width: 350px; display: flex; flex-direction: column; gap: 8px; font-size: 12.5px; line-height: 1.45">
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 10px 12px; border-radius: 4px 14px 14px 14px; display: flex; flex-direction: column; gap: 8px; animation: kvLoop 12s ease 0.3s infinite both">
<span><span style="font-size: 10.5px; font-weight: 800; color: #4A5866">STATIC BOT</span><br>Hi! How can we help you today?</span>
<div style="display: flex; gap: 6px; flex-wrap: wrap"><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">Track order</span><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">Promo</span><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">Store hours</span></div>
</div>
<div style="align-self: flex-end; max-width: 80%; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; animation: kvLoop 12s ease 1.6s infinite both">My package says delivered, but I don't have it.</div>
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 14px 14px 14px; animation: kvLoop 12s ease 2.8s infinite both"><span style="font-size: 10.5px; font-weight: 800; color: #13803F">AI BOT · TOOK OVER</span><br>I checked your order: it was left at the front desk at 14:10. Want me to connect you to our team?</div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; animation: kvLoop 12s ease 4.2s infinite both">Yes, please.</div>
<div style="align-self: center; background: #DDF3E4; color: #0B5E2C; padding: 7px 14px; border-radius: 999px; font-size: 11.5px; font-weight: 800; animation: kvLoop 12s ease 5.2s infinite both">Handed over to human agent · Ticket created</div>
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 14px 14px 14px; animation: kvLoop 12s ease 6.2s infinite both"><span style="font-size: 10.5px; font-weight: 800; color: #071C33">DEWI · SUPPORT AGENT</span><br>Hi, I can see the full chat. Let me sort this out with the courier.</div>
</div>



<div style="width: 420px; display: flex; align-items: center">
<div style="width: 118px; flex-shrink: 0; background: #FFFFFF; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 12px; font-weight: 700">Your system</span>
<span style="font-size: 11px; color: #4A5866">ERP · CRM · POS</span>
<span style="font-size: 11px; font-weight: 700; padding: 6px 8px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C; animation: kvLoop 6s ease 0.2s infinite both">Order shipped</span>
</div>
<div style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear infinite"></span></div>
<div style="width: 56px; height: 56px; flex-shrink: 0; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 22px; animation: kvPulse 2.4s ease infinite">K</div>
<div style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear 0.8s infinite"></span></div>
<div style="width: 160px; flex-shrink: 0; display: flex; flex-direction: column; gap: 8px; font-size: 12px; line-height: 1.45">
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 1.4s infinite both">Hi Rina, your order is on the way. <span style="color: #13803F; font-weight: 700">Track order</span></div>
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 2.4s infinite both">Reminder: your appointment is tomorrow, 10:00.</div>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4A5866">template: order_update</span>
</div>
</div>



<div style="width: 300px; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column">
<div style="background: #071C33; color: #FFFFFF; padding: 14px 16px; font-size: 14px; font-weight: 700">Open an account</div>
<div style="padding: 16px; display: flex; flex-direction: column; gap: 12px">
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">Full name</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 60%; animation: kvGrow 7s ease 0.2s infinite both"></div></div></div>
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">Email</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 80%; animation: kvGrow 7s ease 1s infinite both"></div></div></div>
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">City</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 40%; animation: kvGrow 7s ease 1.8s infinite both"></div></div></div>
<div style="display: flex; gap: 8px; font-size: 12px"><span style="padding: 6px 10px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-weight: 600; animation: kvLoop 7s ease 2.4s infinite both">Personal</span><span style="padding: 6px 10px; border-radius: 999px; border: 1px solid #E3E8E6">Business</span></div>
<div style="height: 42px; border-radius: 10px; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700">Submit</div>
<div style="align-self: center; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #0B5E2C; animation: kvPop 7s ease 2.6s infinite both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Data received — without leaving the chat</div>
</div>
</div>



<div style="width: 420px; display: flex; align-items: center; gap: 0">
<div style="width: 118px; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px">
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>WhatsApp</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">Chat</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Instagram</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">DM &amp; comments</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Facebook</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">DM &amp; comments</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Live Chat</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">Website chat</span></span></span>
</div>
<div style="width: 50px; flex-shrink: 0; height: 214px; display: flex; flex-direction: column; justify-content: space-around">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.45s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.9s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 1.35s infinite"></span></div>
</div>
<div style="flex-grow: 1; background: #FFFFFF; border-radius: 16px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 12px; font-weight: 700">Unified inbox</span>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 0.4s infinite both"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Rina</strong><span style="padding: 1px 6px; border-radius: 5px; background: #E6EAEE; color: #33414E; font-size: 9.5px; font-weight: 800">Chat</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">Where's my order?</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 1s infinite both"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Sari</strong><span style="padding: 1px 6px; border-radius: 5px; background: #FFF1D6; color: #8A5A00; font-size: 9.5px; font-weight: 800">Comment</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">Is this available in size M?</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 1.6s infinite both"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Budi</strong><span style="padding: 1px 6px; border-radius: 5px; background: #FFF1D6; color: #8A5A00; font-size: 9.5px; font-weight: 800">Comment</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">How much for 2 pcs?</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 2.2s infinite both"><img src="" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Andi</strong><span style="padding: 1px 6px; border-radius: 5px; background: #E6EAEE; color: #33414E; font-size: 9.5px; font-weight: 800">Chat</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">Can I change my address?</span></span></div>
<div style="display: flex; gap: 6px; padding-top: 4px; font-size: 10px; font-weight: 700"><span style="padding: 4px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C">SLA on track</span><span style="padding: 4px 8px; border-radius: 999px; background: #071C33; color: #FFFFFF">CSAT collected</span></div>
</div>
</div>


</div>
</div>
</div>
</section>

<!-- ============ PLATFORM CONTROL ============ -->
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Stay in control of every conversation.</h2>
<div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
<span style="font-size: 18px; font-weight: 700">Supervisor mode</span>
<span style="font-size: 15px; line-height: 1.5; color: #4A5866">Leads see their team's chats. Agents see only their own.</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="8" height="7" rx="1.5"></rect><rect x="13" y="4" width="8" height="7" rx="1.5"></rect><rect x="3" y="13" width="8" height="7" rx="1.5"></rect><rect x="13" y="13" width="8" height="7" rx="1.5"></rect></svg>
<span style="font-size: 18px; font-weight: 700">Multi-account</span>
<span style="font-size: 15px; line-height: 1.5; color: #4A5866">All business accounts and numbers, one platform.</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
<span style="font-size: 18px; font-weight: 700">Role-based access</span>
<span style="font-size: 15px; line-height: 1.5; color: #4A5866">Decide who can use which modules, features and accounts.</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5M12 7v5l3 2"></path></svg>
<span style="font-size: 18px; font-weight: 700">Conversation history</span>
<span style="font-size: 15px; line-height: 1.5; color: #4A5866">Every chat stored for visibility and follow-up.</span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4V8Z"></path><path d="M10 6v12"></path></svg>
<span style="font-size: 18px; font-weight: 700">Ticketing</span>
<span style="font-size: 15px; line-height: 1.5; color: #4A5866">Chat with an agent? A ticket is created automatically.</span>
</div>
</div>
</section>

<!-- ============ WHY ============ -->
<section style="padding: 104px 80px; background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; gap: 48px; position: relative; overflow: hidden">
<div aria-hidden="true" style="position: absolute; left: -200px; bottom: -320px; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
<div aria-hidden="true" style="position: absolute; right: -160px; top: -260px; width: 560px; height: 560px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; position: relative">Why Kouventa for customer <span style="color: #2BD46A">engagement</span></h2>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; position: relative">
<div class="kv-why" style="position: relative; overflow: hidden; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 14px; min-height: 260px; box-sizing: border-box">
<span style="width: 60px; height: 60px; border-radius: 16px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(43,212,106,0.25)"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"></circle><path d="m9 14-1.5 7L12 19l4.5 2L15 14"></path></svg></span>
<span style="font-size: 22px; line-height: 1.25; font-weight: 800; padding-top: 10px">Official Meta Business Partner</span>
<span style="font-size: 16px; line-height: 1.55; color: #B9C6D3">Trusted access to the WhatsApp Business Platform.</span>
</div>
<div class="kv-why" style="position: relative; overflow: hidden; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 14px; min-height: 260px; box-sizing: border-box">
<span style="width: 60px; height: 60px; border-radius: 16px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(43,212,106,0.25)"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"></path><circle cx="16" cy="6" r="2"></circle><circle cx="10" cy="12" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg></span>
<span style="font-size: 22px; line-height: 1.25; font-weight: 800; padding-top: 10px">Built around your business</span>
<span style="font-size: 16px; line-height: 1.55; color: #B9C6D3">Customized to your industry, flows and systems.</span>
</div>
<div class="kv-why" style="position: relative; overflow: hidden; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 14px; min-height: 260px; box-sizing: border-box">
<span style="width: 60px; height: 60px; border-radius: 16px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(43,212,106,0.25)"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
<span style="font-size: 22px; line-height: 1.25; font-weight: 800; padding-top: 10px">Secure &amp; trusted</span>
<span style="font-size: 16px; line-height: 1.55; color: #B9C6D3">Enterprise-grade security for business and customer data.</span>
</div>
<div class="kv-why" style="position: relative; overflow: hidden; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 14px; min-height: 260px; box-sizing: border-box">
<span style="width: 60px; height: 60px; border-radius: 16px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(43,212,106,0.25)"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18M7 15h4"></path></svg></span>
<span style="font-size: 22px; line-height: 1.25; font-weight: 800; padding-top: 10px">Post-paid messaging</span>
<span style="font-size: 16px; line-height: 1.55; color: #B9C6D3">Send first. Pay later, for what you actually use.</span>
</div>
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 104px 80px">
<div style="background: #2BD46A; border-radius: 28px; padding: 72px 64px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 32px; color: #06251A">
<h2 style="margin: 0; font-size: 44px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Start engaging customers on WhatsApp.</h2>
<div style="display: flex; gap: 14px; justify-content: center">
<a href="" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap">Book a demo</a>
<a href=""  style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap">See pricing</a>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
