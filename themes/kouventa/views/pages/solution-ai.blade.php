@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap" x-data="{ dept: parseInt(new URLSearchParams(window.location.search).get('dept')) || 0, finTab: 0 }">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Solutions" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">Solutions</span>
<a href="#" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">Artificial Intelligence</a>
<a href="" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">Customer Engagement</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 440px; gap: 64px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">From knowledge to action <span style="color: #2BD46A">with AI.</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">From knowledge retrieval to process automation, Kouventa AI helps businesses turn information into actionable insights and automate business processes with AI-powered autonomous agents.</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">Request a Demo</a>
<button type="button"  style="background: transparent; color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28)">See How It Works</button>
</div>
</div>

<!-- header illustration: one AI hub powering agents for each team; tasks complete in turn -->
<div role="img" aria-label="Animated illustration: Kouventa AI powers an agent for each team, and tasks are completed in turn for Customer Service, Recruiting, Sales and Marketing, Business Operations, Finance, and Legal and Compliance" style="width: 440px; height: 360px; position: relative">
<svg width="440" height="288" viewBox="0 0 440 288" style="position: absolute; left: 0; top: 0" aria-hidden="true"><line x1="162" y1="34" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="162" y1="34" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 1s infinite"></line><line x1="278" y1="34" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="278" y1="34" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 3s infinite"></line><line x1="162" y1="144" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="162" y1="144" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 5s infinite"></line><line x1="278" y1="144" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="278" y1="144" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 7s infinite"></line><line x1="162" y1="254" x2="186" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="162" y1="254" x2="186" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 9s infinite"></line><line x1="278" y1="254" x2="254" y2="144" stroke="rgba(255,255,255,0.16)" stroke-width="2"></line><line x1="278" y1="254" x2="254" y2="144" stroke="#2BD46A" stroke-width="2.5" stroke-linecap="round" style="opacity: 0; animation: kvLineHi 12s ease 11s infinite"></line></svg>
<span style="position: absolute; left: 182px; top: 106px; width: 76px; height: 76px; border-radius: 50%; background: #2BD46A; color: #06251A; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; animation: kvPopIn .4s ease .6s both, kvHubPulse 2s ease-out 1s infinite"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3Z"></path><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"></path></svg><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em">AI AGENTS</span></span>
<span style="position: absolute; left: 0; top: 12px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.10s both, kvRoleHi 12s ease 1s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>Customer Service</span>
<span style="position: absolute; right: 0; top: 12px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.18s both, kvRoleHi 12s ease 3s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg></span>Recruiting</span>
<span style="position: absolute; left: 0; top: 122px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.26s both, kvRoleHi 12s ease 5s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg></span>Sales &amp; Marketing</span>
<span style="position: absolute; right: 0; top: 122px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.34s both, kvRoleHi 12s ease 7s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>Business Ops</span>
<span style="position: absolute; left: 0; top: 232px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.42s both, kvRoleHi 12s ease 9s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H5a2 2 0 0 1 0-4h13v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h15V7"></path><path d="M16 14h.01"></path></svg></span>Finance</span>
<span style="position: absolute; right: 0; top: 232px; width: 162px; height: 44px; box-sizing: border-box; padding: 0 10px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.16); color: #FFFFFF; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; white-space: nowrap; animation: kvIn .35s ease 0.50s both, kvRoleHi 12s ease 11s infinite"><span style="color: #2BD46A; display: flex"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>Legal &amp; Compliance</span>
<div style="position: absolute; left: 0; right: 0; top: 304px; display: grid">
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 1s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">CUSTOMER SERVICE AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Ticket created and assigned to Billing</span></span></span>
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 3s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">RECRUITING AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Top candidates shortlisted</span></span></span>
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 5s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">SALES &amp; MARKETING AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Order status sent to customer</span></span></span>
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 7s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">BUSINESS OPS AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Answer found in the SOP</span></span></span>
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 9s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">FINANCE AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Payment batch ready to approve</span></span></span>
<span style="grid-area: 1 / 1; justify-self: center; display: flex; align-items: center; gap: 10px; padding: 10px 14px 10px 10px; border-radius: 14px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 12px 30px rgba(0,0,0,0.25); opacity: 0; animation: kvToast 12s ease 11s infinite"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span><span style="display: flex; flex-direction: column; gap: 1px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">LEGAL &amp; COMPLIANCE AGENT · DONE</span><span style="font-size: 13px; font-weight: 700; white-space: nowrap">Key clauses flagged for review</span></span></span>
</div>
</div>
</div>
</section>

<!-- ============ TABS ============ -->
<section id="solution-tabs" style="padding: 88px 80px 104px; display: flex; flex-direction: column; gap: 40px">
<div style="display: flex; flex-direction: column; gap: 14px">
<div role="tablist" aria-label="Solutions by role" style="display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 10px">

<button type="button" role="tab" aria-selected=""  style="min-height: 60px; padding: 12px 14px; border-radius: 14px; border: 1px solid ; background: ; color: ; font-size: 16px; font-weight: 800; text-align: center; white-space: nowrap"></button>

</div>

<div role="tablist" aria-label="Finance solutions" style="align-self: center; display: flex; padding: 4px; gap: 4px; border-radius: 12px; background: #E3E8E6">

<button type="button" role="tab" aria-selected=""  style="min-height: 44px; padding: 10px 20px; border-radius: 9px; border: 0; background: ; color: ; font-size: 15px; font-weight: 700; box-shadow: "></button>

</div>

</div>


<div role="tabpanel" style="display: flex; flex-direction: column; gap: 48px">

<div style="display: flex; flex-direction: column; gap: 18px">
<h2 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">Challenges</h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px">

<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-top: 4px solid #E0563B; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05); animation: kvIn .45s ease  both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6M12 2v3"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"></path><path d="M3 11V9a3 3 0 0 1 3-3h15"></path><path d="M7 22l-4-4 4-4"></path><path d="M21 13v2a3 3 0 0 1-3 3H3"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1.5"></rect><rect x="3" y="11" width="18" height="5" rx="1.5"></rect><path d="M7 20h10"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="8" height="8" rx="2"></rect><rect x="14" y="13" width="8" height="8" rx="2"></rect><path d="M14 7h4a2 2 0 0 1 2 2v1M10 17H6a2 2 0 0 1-2-2v-1"></path><path d="M18 8l2 2 2-2M6 16l-2-2-2 2"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 2.6.5 4.4 1.2 5.7M17 17H3s3-2 3-9c0-.8.1-1.5.4-2.2"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0M2 2l20 20"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3M11 8v3M11 14h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M17 8l5 5M22 8l-5 5"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-3 3V4a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2Z"></path><path d="M18 9h1a2 2 0 0 1 2 2v10l-3-3h-6a2 2 0 0 1-2-2v-1"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M12 12v3M12 18h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-7"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4M12 15.5h.01"></path></svg></span>
<span style="font-size: 18px; line-height: 1.5; font-weight: 700; color: #0E1A24"></span>
</div>

</div>
</div>

<div style="display: grid; grid-template-columns: minmax(0, 1fr) 600px; column-gap: 24px; row-gap: 18px; align-items: stretch">
<h2 style="grid-column: 1 / -1; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em">How Kouventa AI helps</h2>
<div style="display: flex; flex-direction: column; justify-content: center; gap: 10px">

<button type="button"  aria-pressed="" style="flex: ; min-height: 88px; max-height: ; box-sizing: border-box; width: 100%; text-align: left; font-family: inherit; color: inherit; cursor: pointer; border: 1px solid ; background: ; box-shadow: ; border-radius: 16px; padding: 14px 20px; display: flex; align-items: ; gap: 16px; animation: ; transition: background .2s ease, border-color .2s ease">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M13 6v2M13 11v2M13 16v2"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="4"></circle><path d="M2 21a8 8 0 0 1 10.5-7.6"></path><circle cx="17.5" cy="17.5" r="3"></circle><path d="m22 22-2.3-2.3"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h11L21 7H6"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8v8l9 5 9-5Z"></path><path d="M3 8l9 5 9-5M12 13v8"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H5a2 2 0 0 1 0-4h13v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h15V7"></path><path d="M16 14h.01"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"></path><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l2.5 2.5L16 9.5"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 6px; min-width: 0">
<span style="font-size: 16px; font-weight: 800"></span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866"></span>

<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px">

<li style="display: flex; gap: 10px; align-items: flex-start; font-size: 14px; line-height: 1.5; color: #2B3947"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><path d="M20 6 9 17l-5-5"></path></svg></li>

</ul>

</span>
</button>

</div>

<div id="kv-ill" role="img" aria-label="" style="height: 520px; border-radius: 20px; background: #FFFFFF; border: 1px solid #E3E8E6; position: relative; overflow: hidden">

<!-- ===== Customer Service ===== -->

<div style="position: absolute; inset: 0; padding: 28px; display: grid; grid-template-columns: minmax(0, 1fr) 210px; gap: 18px; align-content: center">
<div style="display: flex; flex-direction: column; gap: 14px">
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .2s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #4A5866">Customer · WhatsApp</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; animation: kvPopIn .3s ease 2.1s both">Resolved</span></span></div>
<span style="font-size: 14px; font-weight: 600">What are your opening hours?</span>
<div style="background: #FFFFFF; border-radius: 4px 10px 10px 10px; padding: 9px 11px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .35s ease 1.2s both"><span style="font-size: 11px; font-weight: 800; color: #13803F">Customer Support Agent</span><span style="font-size: 13px">We're open every day, 08:00–21:00.</span><span style="align-self: flex-start; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #DDF3E4; color: #0B5E2C">Store FAQ.pdf</span></div>
</div>
<div style="background: #F4F6F5; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; animation: kvIn .35s ease .5s both">
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 12px; font-weight: 800; color: #4A5866">Customer · Live Chat</span><span style="display: grid; font-size: 11px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #E3E8E6">New</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvPopIn .3s ease 2.8s both">Ticket created</span></span></div>
<span style="font-size: 14px; font-weight: 600">My refund still hasn't arrived after two weeks, and I was charged a fee.</span>
</div>
</div>
<div style="display: flex; flex-direction: column; gap: 16px; position: relative">
<div style="background: #071C33; color: #FFFFFF; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; font-size: 12px; position: relative; z-index: 2; --mx: 0px; --my: 165px; animation: kvIn .35s ease 3.2s both, kvMove .6s ease 4.6s 1 forwards">
<span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">TICKET</span>
<span style="display: flex; justify-content: space-between; animation: kvIn .25s ease 3.5s both"><span style="color: #93A4B6">Category</span><strong>Refund</strong></span>
<span style="display: flex; flex-direction: column; gap: 2px; animation: kvIn .25s ease 3.7s both"><span style="color: #93A4B6">Summary</span><strong>Refund delayed, extra fee charged</strong></span>
<span style="display: flex; justify-content: space-between; animation: kvIn .25s ease 3.9s both"><span style="color: #93A4B6">Team</span><strong>Billing</strong></span>
</div>
<div style="position: absolute; left: 0; right: 0; top: 300px; border: 1.5px dashed #C9D2CE; border-radius: 14px; padding: 14px; display: flex; align-items: center; gap: 10px; animation: kvIn .3s ease 4.2s both">
<span style="width: 36px; height: 36px; border-radius: 10px; background: #EAF7EF; color: #0B5E2C; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">BIL</span>
<span style="display: flex; flex-direction: column"><span style="font-size: 13px; font-weight: 800">Billing team</span><span style="font-size: 11px; font-weight: 700; color: #13803F; animation: kvIn .3s ease 5.3s both">Assigned</span></span>
</div>
</div>
</div>


<!-- ===== Recruiting (two scenes) ===== -->

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


<!-- ===== Sales & Marketing (two scenes) ===== -->

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


<!-- ===== Business Operations ===== -->

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


<!-- ===== Finance: Payment Prioritization ===== -->

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


<!-- ===== Finance: Bank Reconciliation (two scenes) ===== -->

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


<!-- ===== Legal & Compliance ===== -->

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

<div style="position: relative; overflow: hidden; background: #071C33; border-radius: 28px; padding: 44px; display: flex; flex-direction: column; gap: 24px">
<span aria-hidden="true" style="position: absolute; right: -120px; top: -160px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.22), rgba(43, 212, 106, 0) 70%)"></span>
<h2 style="position: relative; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #FFFFFF">Business impact</h2>
<div style="position: relative; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px">

<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-top: 4px solid #2BD46A; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; animation: kvIn .45s ease  both">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"></path><path d="M3.3 19a10 10 0 1 1 17.4 0"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"></path><path d="M14 7h7v7"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 12 0V3h-4v7a2 2 0 0 1-4 0V3Z"></path><path d="M6 7h4M14 7h4"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.6-1 2.9 1 2.9-2.5 1.6-.9 2.9-3-.2L12 20l-2.4-1.8-3 .2-.9-2.9-2.5-1.6 1-2.9-1-2.9 2.5-1.6.9-2.9 3 .2Z"></path><path d="M8.5 11.5l2.5 2.5 4.5-5"></path></svg><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg></span>
<span style="font-size: 18px; line-height: 1.4; font-weight: 800"></span>
<span style="font-size: 15px; line-height: 1.6; color: #B9C6D3"></span>
</div>

</div>
</div>

</div>

</section>

<!-- ============ IMPLEMENTATION JOURNEY ============ -->
<section id="impl-journey" style="padding: 0 80px 88px; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Implementation journey</h2>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px">

<button type="button"  aria-label="" style="text-align: left; border: 1px solid #E3E8E6; background: #FFFFFF; border-radius: 20px; padding: 0; overflow: hidden; display: flex; flex-direction: column; color: #0E1A24">
<div style="height: 6px; background: #E3E8E6; position: relative"><span style="position: absolute; left: 0; top: 0; height: 6px; background: #13803F; --w: 100%; animation: kvGrowW 1.6s linear  1 both"></span></div>
<div style="height: 150px; background: #F4F6F5; position: relative; overflow: hidden">


<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 10px">
<span style="width: 50px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid #E3E8E6"></span><span style="width: 14px; height: 2px; background: #C9D2CE"></span>
<span style="width: 50px; height: 34px; border-radius: 8px; border: 1px solid #E3E8E6; animation: kvAmber .3s ease  both"></span><span style="width: 14px; height: 2px; background: #C9D2CE"></span>
<span style="width: 50px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid #E3E8E6"></span>
</div>
<span style="position: absolute; left: 24px; top: 46px; --d: 170px; animation: kvScan 1.4s ease  1 both"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#071C33" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6"></circle><path d="m20 20-4.5-4.5"></path></svg></span>


<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 14px">
<div style="display: flex; flex-direction: column; gap: 5px">
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .25s ease  both">Documents</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .25s ease  both">CRM</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .25s ease  both">ERP</span>
<span style="font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #FFFFFF; animation: kvIn .25s ease  both">Ticketing</span>
</div>
<div style="width: 40px; height: 2px; background: #13803F; --w: 40px; animation: kvGrowW .4s ease  both"></div>
<span style="width: 52px; height: 52px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800">AGENT</span>
</div>


<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 16px">
<div style="display: flex; flex-direction: column; gap: 5px; width: 90px">
<span style="height: 18px; border-radius: 5px; background: #FFFFFF; border: 1px solid #E3E8E6; animation: kvIn .25s ease  both"></span>
<span style="height: 18px; border-radius: 5px; background: #FFF6E5; border: 1px solid #F2C46D; font-size: 9px; font-weight: 800; color: #7A4A00; display: flex; align-items: center; justify-content: center; animation: kvIn .25s ease  both">Approval</span>
<span style="height: 18px; border-radius: 5px; background: #FFFFFF; border: 1px solid #E3E8E6; animation: kvIn .25s ease  both"></span>
</div>
<div style="display: flex; flex-direction: column; gap: 6px; font-size: 10px; font-weight: 700">
<span style="animation: kvIn .25s ease  both">✓ Rules</span>
<span style="animation: kvIn .25s ease  both">✓ Actions</span>
<span style="display: flex; align-items: center; gap: 4px; animation: kvPopIn .3s ease  both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path></svg>Guardrails</span>
</div>
</div>


<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 16px">
<div style="display: flex; flex-direction: column; align-items: center; gap: 6px">
<span style="width: 44px; height: 24px; border-radius: 999px; background: #C9D2CE; padding: 2px; box-sizing: border-box; display: flex; animation: kvGreen .3s ease  both"><span style="width: 20px; height: 20px; border-radius: 50%; background: #FFFFFF; animation: kvKnob .3s ease  both"></span></span>
<span style="font-size: 11px; font-weight: 800; color: #0B5E2C; animation: kvIn .25s ease  both">Live</span>
</div>
<div style="display: flex; align-items: flex-end; gap: 4px; height: 40px; animation: kvIn .25s ease  both"><span style="width: 8px; height: 14px; background: #9ED3B0; border-radius: 2px"></span><span style="width: 8px; height: 24px; background: #5BB880; border-radius: 2px"></span><span style="width: 8px; height: 32px; background: #13803F; border-radius: 2px"></span></div>
<div style="display: grid; grid-template-columns: repeat(3, 16px); gap: 5px">
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33; animation: kvPopIn .25s ease  both"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33; animation: kvPopIn .25s ease  both"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33; animation: kvPopIn .25s ease  both"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33; animation: kvPopIn .25s ease  both"></span>
<span style="width: 16px; height: 16px; border-radius: 50%; background: #071C33; animation: kvPopIn .25s ease  both"></span>
</div>
</div>


</div>
<div style="padding: 20px 22px 24px; display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 10px"><span style="width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #C9D2CE; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; animation: kvStepOn .3s ease  both"></span><span style="font-size: 20px; font-weight: 800"></span></span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866"></span>
</div>
</button>

</div>
</section>

<!-- ============ CLOSING CTA ============ -->
<section style="padding: 0 80px 104px">
<div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
<h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Start simple. Scale with confidence.</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55">Begin with everyday AI for your employees, governed by your organization. Grow into AI agents when you're ready.</p>
</div>
<div style="display: flex; gap: 12px; flex-shrink: 0">
<a href="#" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; white-space: nowrap">Find your first use case</a>
<a href="" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; white-space: nowrap">Talk to Sales</a>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
