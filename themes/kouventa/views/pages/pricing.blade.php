@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap" x-data="{
    product: new URLSearchParams(window.location.search).get('tab') || 'ai',
    menu: '',
    openCategory: { 0: true, 1: true, 2: true }
}">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="height: 762px; box-sizing: border-box; padding: 0 80px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 28px; position: relative">
<h1 style="margin: 0; font-size: 72px; line-height: 1.04; font-weight: 800; letter-spacing: -0.035em; max-width: 960px">Intelligence you <span style="color: #2BD46A">control,</span><br>priced for how you grow.</h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 660px">Two platforms, three tiers each. Start where your team is today, and scale without switching vendors.</p>
<div id="pricing-toggle" role="group" aria-label="Choose a platform" style="align-self: center; display: flex; padding: 5px; background: rgba(255,255,255,0.08); border-radius: 14px; gap: 4px; margin-top: 12px">

<button type="button" aria-pressed="" sc-camel-on-click="" style="border: 0; padding: 14px 26px; min-height: 48px; border-radius: 10px; font-size: 16px; font-weight: 700; background: ; color: "></button>

</div>
</div>
</section>

<!-- ============ PRODUCT PANEL ============ -->
<section style="padding: 104px 80px; display: flex; flex-direction: column; gap: 104px">

<!-- tier cards -->
<div style="display: flex; flex-direction: column; gap: 24px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em"><span x-text="product === 'ai' ? 'Artificial Intelligence' : 'Customer Engagement'"></span></h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; align-items: stretch">

<article style="background: ; color: ; border: 1px solid ; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
<div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
<span style="font-size: 30px; font-weight: 800"></span>
<span style="font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">Most popular</span>
</div>
<span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: "></span>
<div style="display: flex; flex-direction: column; gap: 4px">
<span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em"></span>
<span style="font-size: 14px; color: "></span>
</div>
<span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: ; color: ; font-size: 13px; font-weight: 700; line-height: 1.35; white-space: nowrap"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg><span></span></span>
<ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid ; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">

<li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span> <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #FFE9C7; color: #7A4A00; white-space: nowrap">Coming soon</span></span></li>

</ul>
<a href="{{ url('/book-demo') }}" class="kv-btn" style="text-align: center; font-size: 16px; font-weight: 700; padding: 15px 20px; border-radius: 12px; background: ; color: ; border: 1.5px solid "></a>
</article>

</div>
</div>

<!-- compare -->
<div style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Compare every feature</h2>
<p style="margin: 0; font-size: 16px; color: #4A5866"><span x-text="product === 'ai' ? 'The full picture of what each Artificial Intelligence tier includes.' : 'The full picture of what each Customer Engagement tier includes.'"></span></p>
</div>
<div style="display: flex; gap: 8px; flex-shrink: 0">
<button type="button" sc-camel-on-click="" style="min-height: 44px; padding: 10px 16px; border-radius: 10px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9l5-5 5 5M7 15l5 5 5-5"></path></svg>Expand all</button>
<button type="button" sc-camel-on-click="" style="min-height: 44px; padding: 10px 16px; border-radius: 10px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4l5 5 5-5M7 20l5-5 5 5"></path></svg>Collapse all</button>
</div>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; height: 640px; overflow: auto">
<div style="position: sticky; top: 0; z-index: 2; background: #071C33; color: #FFFFFF; display: grid; grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(0, 1fr)); font-size: 14px; font-weight: 800">
<span style="padding: 16px 24px">Feature</span>
<span style="padding: 16px 24px"></span>
</div>

<div style="border-bottom: 1px solid #E3E8E6">
<button type="button" class="kv-cat" aria-expanded="" sc-camel-on-click="" style="width: 100%; min-height: 48px; border: 0; background: #F4F6F5; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 800; color: #0E1A24; text-align: left"><svg class="kv-chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform: "><path d="m6 9 6 6 6-6"></path></svg></button>


<div style="display: grid; grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(0, 1fr)); border-top: 1px solid #EEF2F0; font-size: 14px; align-items: center">
<span style="padding: 13px 24px; font-weight: 600"> <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #FFE9C7; color: #7A4A00; white-space: nowrap">Coming soon</span></span>

<span style="padding: 13px 24px; display: flex; align-items: center">
<span aria-label="Included" style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span aria-label="Not included" style="color: #5B6875">—</span>
<span style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: #F4F6F5; color: #4A5866">Add-on</span>
<span></span>
</span>

</div>


</div>

</div>
</div>

<!-- extend -->
<div style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Extend your plan</h2>
<p style="margin: 0; font-size: 16px; color: #4A5866">Add capabilities as you need them, on any tier.</p>
</div>
<div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px">

<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; gap: 8px">
<span style="font-size: 16px; font-weight: 800"></span>
<span style="font-size: 14px; line-height: 1.55; color: #4A5866"></span>
</div>

</div>
<p style="margin: 0; font-size: 16px; color: #4A5866">Available across all plans. <a href="{{ url('/book-demo') }}" style="font-weight: 700">Talk to sales</a> for scoping and pricing.</p>
</div>


<!-- template types -->
<div style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">WhatsApp message templates</h2>
<p style="margin: 0; font-size: 16px; color: #4A5866">WhatsApp charges per conversation, based on the template category you send.</p>
</div>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px">

<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 18px">
<div style="display: flex; align-items: center; gap: 14px; padding-bottom: 18px; border-bottom: 1px solid #E3E8E6">
<span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg></span>
<span style="font-size: 20px; font-weight: 800"></span>
</div>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 12px">

<li style="display: flex; gap: 10px; align-items: flex-start; font-size: 14px; line-height: 1.5; color: #2B3947"><span style="width: 18px; height: 18px; flex-shrink: 0; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; margin-top: 2px"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span></li>

</ul>
</div>

</div>
</div>

<!-- calculator -->
<div x-data="whatsappCalculator()" style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Estimate your WhatsApp cost</h2>
<p style="margin: 0; font-size: 16px; color: #4A5866">Enter your expected monthly conversations per category to see your estimated monthly and yearly cost.</p>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr); gap: 40px; align-items: start">
<div style="display: flex; flex-direction: column; gap: 20px">
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; font-size: 12px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7A88">
  <span>Conversations / month</span><span></span><span style="text-align: center">Rate (IDR)</span><span></span><span style="text-align: right">Subtotal (IDR)</span>
</div>

<!-- Marketing -->
<div style="display: flex; flex-direction: column; gap: 8px">
<span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700">Marketing<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
<input type="number" min="0" inputmode="numeric" aria-label="Marketing conversations per month" x-model.number="mk" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
<span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">616</span>
<span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subMk)">0</span>
</div>
</div>

<!-- Utility -->
<div style="display: flex; flex-direction: column; gap: 8px">
<span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700">Utility<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
<input type="number" min="0" inputmode="numeric" aria-label="Utility conversations per month" x-model.number="ut" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
<span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
<span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subUt)">0</span>
</div>
</div>

<!-- Authentication -->
<div style="display: flex; flex-direction: column; gap: 8px">
<span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700">Authentication<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
<input type="number" min="0" inputmode="numeric" aria-label="Authentication conversations per month" x-model.number="au" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
<span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
<span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subAu)">0</span>
</div>
</div>

<!-- Service -->
<div style="display: flex; flex-direction: column; gap: 8px">
<span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700">Service<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
<input type="number" min="0" inputmode="numeric" aria-label="Service conversations per month" x-model.number="sv" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
<span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
<span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subSv)">0</span>
</div>
</div>

<!-- MAU row -->
<div style="display: flex; flex-direction: column; gap: 8px; padding-top: 16px; border-top: 1px dashed #C9D2CE">
<span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700">Monthly active users<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px minmax(0, 1fr) 24px minmax(0, 1fr); gap: 8px; align-items: end">
<label style="display: flex; flex-direction: column; gap: 6px; font-size: 12px; font-weight: 600; color: #6B7A88">Unique numbers broadcast to<input type="number" min="0" inputmode="numeric" x-model.number="bc" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px"></label>
<span style="height: 52px; display: flex; align-items: center; justify-content: center; color: #6B7A88; font-weight: 700">+</span>
<label style="display: flex; flex-direction: column; gap: 6px; font-size: 12px; font-weight: 600; color: #6B7A88">Unique inbound numbers<input type="number" min="0" inputmode="numeric" x-model.number="ib" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px"></label>
<span style="height: 52px; display: flex; align-items: center; justify-content: center; color: #6B7A88; font-weight: 700">=</span>
<span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(mau)">0</span>
</div>
<span style="font-size: 13px; color: #6B7A88">Up to 3,000 users: Starter. 3,001 to 15,000: Professional. Above 15,000: custom plan.</span>
</div>
<span style="font-size: 13px; color: #6B7A88">Rates are per conversation and follow Meta's WhatsApp pricing, which may change.</span>
</div>

<!-- Estimate Summary Card -->
<div style="background: #071C33; color: #FFFFFF; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 18px">
<span style="font-size: 13px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">YOUR ESTIMATE</span>
<div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)">
  <span style="font-size: 15px; color: #B9C6D3">Your plan</span>
  <span style="font-size: 13px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: rgba(43,212,106,0.16); color: #2BD46A" x-text="plan">Starter</span>
</div>

<!-- In range estimate -->
<template x-if="!isOver">
<div style="display: flex; flex-direction: column; gap: 18px">
  <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">Platform fee / month</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(platformFee)">Rp 1.499.000</span></div>
  <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">Conversation fee / month</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(convTotal)">Rp 0</span></div>
  <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">Platform + conversation / month</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(monthlyTotal)">Rp 1.499.000</span></div>
  <div style="display: flex; flex-direction: column; gap: 4px; padding-top: 4px"><span style="font-size: 15px; color: #B9C6D3">Grand total / year</span><span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #2BD46A" x-text="fmt(yearlyTotal)">Rp 17.988.000</span></div>
  <a href="{{ url('/book-demo') }}" style="margin-top: 6px; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Book a demo</a>
</div>
</template>

<!-- Over 15,000 active users custom enterprise CTA -->
<template x-if="isOver">
<div style="display: flex; flex-direction: column; gap: 18px">
  <div style="display: flex; flex-direction: column; gap: 10px; padding: 8px 0 4px">
    <span style="font-size: 26px; line-height: 1.25; font-weight: 800">More than 15,000 active users?</span>
    <span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">You're ready for an enterprise setup. Talk to our team for a plan tailored to your volume, channels and integrations.</span>
  </div>
  <a href="{{ url('/book-demo') }}" style="margin-top: 6px; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Talk to sales</a>
</div>
</template>

</div>
</div>
</div>
</section>

<!-- ============ CLOSING CTA ============ -->
<section style="padding: 0 80px 104px">
<div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
<h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Ready to see how your team would use it?</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55">Book a working session with our solution team. We'll map your use cases to the right tier and deployment.</p>
</div>
<div style="display: flex; gap: 12px; flex-shrink: 0">
<a href="{{ url('/book-demo') }}" class="kv-btn" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px">Book a working session</a>
<a href="{{ url('/book-demo') }}" class="kv-btn" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A">Contact sales</a>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>

@push('scripts')
<script>
function whatsappCalculator() {
    return {
        mk: 0, ut: 0, au: 0, sv: 0, bc: 0, ib: 0,
        get mau() { return (Number(this.bc) || 0) + (Number(this.ib) || 0); },
        get isOver() { return this.mau > 15000; },
        get plan() { return this.isOver ? 'Enterprise' : (this.mau > 3000 ? 'Professional' : 'Starter'); },
        get platformFee() { return this.isOver ? 0 : (this.mau > 3000 ? 4499000 : 1499000); },
        get subMk() { return (Number(this.mk) || 0) * 616; },
        get subUt() { return (Number(this.ut) || 0) * 375; },
        get subAu() { return (Number(this.au) || 0) * 375; },
        get subSv() { return (Number(this.sv) || 0) * 375; },
        get convTotal() { return this.subMk + this.subUt + this.subAu + this.subSv; },
        get monthlyTotal() { return this.platformFee + this.convTotal; },
        get yearlyTotal() { return this.monthlyTotal * 12; },
        fmt(num) { return 'Rp ' + Math.round(num).toLocaleString('id-ID'); },
        fmtNum(num) { return Math.round(num).toLocaleString('id-ID'); }
    };
}
</script>
@endpush
@endsection
