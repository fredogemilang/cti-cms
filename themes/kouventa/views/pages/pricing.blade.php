@extends('kouventa::layouts.app')

@section('content')
<div x-data="{
    product: (new URLSearchParams(window.location.search).get('tab') === 'ai' ? 'ai' : 'ce'),
    ...whatsappCalculator()
}" class="kv-canvas-wrap">

<!-- ============ HERO & PLATFORM TOGGLE ============ -->
<section style="background: #071C33; color: #FFFFFF; padding: 96px 80px 104px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 24px; position: relative">
    <div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none">
        <div style="position: absolute; left: 50%; top: -200px; width: 1000px; height: 1000px; margin-left: -500px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
    </div>
    
    <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.1em; color: #2BD46A; text-transform: uppercase">{{ t('kouventa.Pricing', 'PRICING') }}</span>
    
    <h1 style="margin: 0; font-size: 72px; line-height: 1.04; font-weight: 800; letter-spacing: -0.035em; max-width: 960px">
        Intelligence you <span style="color: #2BD46A">control,</span><br>priced for how you grow.
    </h1>
    
    <p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 660px">
        Two platforms, three tiers each. Start where your team is today, and scale without switching vendors.
    </p>

    <!-- Platform Toggle -->
    <div id="pricing-toggle" role="group" aria-label="Choose a platform" style="align-self: center; display: flex; padding: 5px; background: rgba(255,255,255,0.08); border-radius: 14px; gap: 4px; margin-top: 12px; z-index: 10">
        <button type="button" @click="product = 'ai'"
                :style="product === 'ai' ? 'background: #2BD46A; color: #06251A;' : 'background: transparent; color: #FFFFFF;'"
                style="border: 0; padding: 14px 26px; min-height: 48px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s">
            Artificial Intelligence
        </button>
        <button type="button" @click="product = 'ce'"
                :style="product === 'ce' ? 'background: #2BD46A; color: #06251A;' : 'background: transparent; color: #FFFFFF;'"
                style="border: 0; padding: 14px 26px; min-height: 48px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s">
            Customer Engagement
        </button>
    </div>
</section>

<!-- ============ PRODUCT PANEL ============ -->
<section style="padding: 104px 80px; display: flex; flex-direction: column; gap: 104px; background: #F4F6F5">

    <!-- Tier Cards Header -->
    <div style="display: flex; flex-direction: column; gap: 24px">
        <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24"
            x-text="product === 'ai' ? 'Artificial Intelligence' : 'Customer Engagement'">
            Customer Engagement
        </h2>

        <!-- ============ AI TIERS ============ -->
        <div x-show="product === 'ai'" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; align-items: stretch">
            <!-- AI Starter -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Starter</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">Bring AI to your first workflow.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 5.991.667</span>
                    <span style="font-size: 14px; color: #4A5866">per month, billed annually</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Unlimited Company Wikipedia use cases</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Generative AI with source citation</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>19+ supported data source types</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Curated LLM with dynamic selection</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>100 GB storage, 100k API calls/month</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>AI guardrails & PII masking</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Livechat delivery, SDK access</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: #071C33; color: #FFFFFF; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Talk to sales</a>
            </article>

            <!-- AI Grow (Most Popular) -->
            <article style="background: #071C33; color: #FFFFFF; border: 1px solid #071C33; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative; box-shadow: 0 24px 60px rgba(7,28,51,0.2)">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Grow</span>
                    <span style="font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">Most popular</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #B9C6D3">Add agentic capability across your teams.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Custom pricing</span>
                    <span style="font-size: 14px; color: #B9C6D3">annual subscription</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: rgba(43,212,106,0.14); color: #7BE8A6; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Unlimited Wikipedia + 3 Task Force use cases</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid rgba(255,255,255,0.14); display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Everything in Starter</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Agentic AI with multi-agent orchestration</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Custom LLM support</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Connectors: Slack, SAP, SQL, custom</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>500 GB storage, chat playground</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>File & chart generation tools</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Talk to sales</a>
            </article>

            <!-- AI Scale -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Scale</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">Enterprise AI, fully orchestrated.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Custom pricing</span>
                    <span style="font-size: 14px; color: #4A5866">custom scope, one-time or subscription</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Unlimited use cases & workflows</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Everything in Grow</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Full workflow automation orchestration</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Hybrid, private cloud, or on-premise</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Custom connectors & fine-tuning</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Dedicated engineering pod</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Audit trail & change approval gates</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: transparent; color: #0E1A24; font-size: 16px; font-weight: 700; padding: 15px 24px; border-radius: 12px; border: 1.5px solid #0E1A24; text-align: center; text-decoration: none">Talk to sales</a>
            </article>
        </div>

        <!-- ============ CE TIERS ============ -->
        <div x-show="product === 'ce'" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; align-items: stretch">
            <!-- CE Starter -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Starter</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">For teams starting their omnichannel journey.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 1.499.000</span>
                    <span style="font-size: 14px; color: #4A5866">per month</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Up to 3,000 monthly active users</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>WhatsApp, Instagram, Facebook</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Unlimited customer service agents</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Static chatbot & agent routing</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>10,000 contact database</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Analytics, role-based access, MFA</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>SaaS cloud deployment</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: #071C33; color: #FFFFFF; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Talk to sales</a>
            </article>

            <!-- CE Professional (Most Popular) -->
            <article style="background: #071C33; color: #FFFFFF; border: 1px solid #071C33; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative; box-shadow: 0 24px 60px rgba(7,28,51,0.2)">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Professional</span>
                    <span style="font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">Most popular</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #B9C6D3">For growing teams scaling engagement across channels.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 4.499.000</span>
                    <span style="font-size: 14px; color: #B9C6D3">per month</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: rgba(43,212,106,0.14); color: #7BE8A6; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Up to 15,000 monthly active users</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid rgba(255,255,255,0.14); display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Everything in Starter</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Conversation search across history</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>45,000 contact database</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>WhatsApp Flow forms & templates</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Broadcast scheduling & CSAT</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Standard 9×5 support, 99.5% uptime</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">Talk to sales</a>
            </article>

            <!-- CE Enterprise -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">Enterprise</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">Full control for enterprise-scale operations.</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Custom pricing</span>
                    <span style="font-size: 14px; color: #4A5866">custom scope, one-time or subscription</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Customizable MAU & contact database</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Everything in Professional</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Hybrid, private cloud, or on-premise</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Platinum 24×7 support</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>99.9% uptime SLA</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Dedicated implementation team</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>Data masking & audit trail</span></li>
                </ul>
                <a href="{{ url('/book-demo') }}" class="kv-btn" style="margin-top: 8px; background: transparent; color: #0E1A24; font-size: 16px; font-weight: 700; padding: 15px 24px; border-radius: 12px; border: 1.5px solid #0E1A24; text-align: center; text-decoration: none">Talk to sales</a>
            </article>
        </div>
    </div>

    <!-- ============ WHATSAPP CALCULATOR ============ -->
    <div style="display: flex; flex-direction: column; gap: 32px">
        <div style="display: flex; flex-direction: column; gap: 10px">
            <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24">Estimate your WhatsApp cost</h2>
            <p style="margin: 0; font-size: 16px; color: #4A5866">Enter your expected monthly conversations per category to see your estimated monthly and yearly cost.</p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr); gap: 40px; align-items: start">
            <div style="display: flex; flex-direction: column; gap: 20px">
                <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; font-size: 12px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7A88">
                    <span>Conversations / month</span><span></span><span style="text-align: center">Rate (IDR)</span><span></span><span style="text-align: right">Subtotal (IDR)</span>
                </div>

                <!-- Marketing -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">Marketing<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
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
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">Utility<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
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
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">Authentication<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
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
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">Service<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
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
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">Monthly active users<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
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
<section style="padding: 0 80px 104px; background: #F4F6F5">
    <div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
        <div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
            <h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Ready to see how your team would use it?</h2>
            <p style="margin: 0; font-size: 18px; line-height: 1.55">Book a working session with our solution team. We'll map your use cases to the right tier and deployment.</p>
        </div>
        <div style="display: flex; gap: 12px; flex-shrink: 0">
            <a href="{{ url('/book-demo') }}" class="kv-btn" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">Book a working session</a>
            <a href="{{ url('/book-demo') }}" class="kv-btn" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; text-decoration: none">Contact sales</a>
        </div>
    </div>
</section>

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
