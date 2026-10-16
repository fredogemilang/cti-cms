@push('styles')
<style>
.kv-ce-track-fill {
    position: absolute;
    top: 76px;
    left: 12.5%;
    height: 4px;
    border-radius: 4px;
    background: #13803F;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.kv-ce-customer-pointer {
    position: absolute;
    top: 0;
    transform: translateX(-50%);
    transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 10;
}

/* --- Products Highlight Pill Styles --- */
.kv-linked-pill {
    min-height: 48px;
    box-sizing: border-box;
    display: inline-flex !important;
    align-items: center;
    gap: 10px;
    padding: 6px 6px 6px 16px;
    border-radius: 999px;
    background: #FFFFFF;
    border: 1px solid #E3E8E6;
    font-size: 15px;
    color: #4A5866;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(7, 28, 51, 0.04);
}
.kv-pill-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #13803F;
    flex-shrink: 0;
    display: inline-block;
}
.kv-btn-show-all {
    margin-left: 6px;
    white-space: nowrap;
    flex-shrink: 0;
    min-height: 36px;
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid #C9D2CE;
    background: #FFFFFF;
    color: #0E1A24;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.kv-btn-show-all:hover {
    background: #071C33;
    border-color: #071C33;
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(7, 28, 51, 0.15);
}
.kv-btn-show-all:hover svg {
    stroke: #2BD46A;
}
.kv-btn-show-all:active {
    transform: translateY(0);
}
.kv-unlinked-pill {
    min-height: 48px;
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: #4A5866;
}
.kv-unlinked-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #4A5866;
    text-decoration: none;
    font-size: 15px;
    transition: color 0.2s;
    cursor: pointer;
}
.kv-unlinked-link:hover {
    color: #13803F;
}
.kv-unlinked-link svg {
    transition: transform 0.2s;
}
.kv-unlinked-link:hover svg {
    transform: translateY(-2px);
    stroke: #13803F;
}

</style>
@endpush

@extends('kouventa::layouts.app')

@php
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'The smarter way to', 'main' => 'engage customers.']);
    $heroSubtitle = $page?->block('hero_subtitle', 'Reach more customers, respond faster, and keep every conversation seamless — on WhatsApp and beyond.');
    $heroCta = $page?->buttonBlock('hero_cta', ['text' => 'Book a demo', 'url' => '/book-demo']);
    $channelsTitle = $page?->titleBlock('channels_title', ['prefix' => 'Every missed message is a', 'main' => 'missed sale.']);
    $featuresTitle = $page?->titleBlock('features_title', ['prefix' => 'One platform.', 'main' => 'Zero missed messages.']);
    $whyTitle = $page?->titleBlock('why_title', ['prefix' => 'Why Kouventa for customer', 'main' => 'engagement']);
    $bottomCtaTitle = $page?->block('bottom_cta_title', 'Start engaging customers on WhatsApp.');
    $bottomCtaSubtitle = $page?->block('bottom_cta_subtitle', 'Connect with our team to set up your WhatsApp Business Platform account, tailored templates, and verified badge.');
@endphp

@section('content')
<div class="kv-canvas-wrap" x-data="{
    stage: 0,
    product: 'broadcast',
    linked: false,
    stages: [
        {
            name: '{{ t('kouventa.Prospecting', 'Prospecting') }}',
            headline: '{{ t('kouventa.Turn prospects into customers.', 'Turn prospects into customers.') }}',
            sub: '{{ t('kouventa.Reach and engage potential customers with targeted campaigns across channels.', 'Reach and engage potential customers with targeted campaigns across channels.') }}',
            tools: {
                broadcast: '{{ t('kouventa.Reach more prospects at scale', 'Reach more prospects at scale') }}',
                chatbot: '{{ t('kouventa.Engage prospects instantly', 'Engage prospects instantly') }}',
                omni: '{{ t('kouventa.Connect across channels', 'Connect across channels') }}'
            },
            defaultTool: 'broadcast'
        },
        {
            name: '{{ t('kouventa.Onboarding', 'Onboarding') }}',
            headline: '{{ t('kouventa.Make onboarding effortless.', 'Make onboarding effortless.') }}',
            sub: '{{ t('kouventa.Guide new customers through registration, verification and data submission — all in chat.', 'Guide new customers through registration, verification and data submission — all in chat.') }}',
            tools: {
                flows: '{{ t('kouventa.Simple registration and data entry', 'Simple registration and data entry') }}',
                auth: '{{ t('kouventa.Verify identity securely', 'Verify identity securely') }}',
                integration: '{{ t('kouventa.As Utility Messages: Timely onboarding updates', 'As Utility Messages: Timely onboarding updates') }}',
                chatbot: '{{ t('kouventa.Guide and answer questions', 'Guide and answer questions') }}',
                omni: '{{ t('kouventa.One conversation, every channel', 'One conversation, every channel') }}'
            },
            defaultTool: 'flows'
        },
        {
            name: '{{ t('kouventa.Upselling', 'Upselling') }}',
            headline: '{{ t('kouventa.Grow value from every customer.', 'Grow value from every customer.') }}',
            sub: '{{ t('kouventa.Send relevant offers and personalized recommendations based on what customers like.', 'Send relevant offers and personalized recommendations based on what customers like.') }}',
            tools: {
                broadcast: '{{ t('kouventa.Reach customers with relevant offers', 'Reach customers with relevant offers') }}',
                chatbot: '{{ t('kouventa.Recommend products, drive engagement', 'Recommend products, drive engagement') }}'
            },
            defaultTool: 'broadcast'
        },
        {
            name: '{{ t('kouventa.Support', 'Support') }}',
            headline: '{{ t('kouventa.Faster support, every time.', 'Faster support, every time.') }}',
            sub: '{{ t('kouventa.Help customers find answers, fix common issues, and reach your team easily.', 'Help customers find answers, fix common issues, and reach your team easily.') }}',
            tools: {
                chatbot: '{{ t('kouventa.Resolve questions instantly', 'Resolve questions instantly') }}',
                omni: '{{ t('kouventa.Support customers on any channel', 'Support customers on any channel') }}'
            },
            defaultTool: 'chatbot'
        }
    ],
    products: {
        auth: {
            name: '{{ t('kouventa.Authentication', 'Authentication') }}',
            headline: '{{ t('kouventa.Secure every login.', 'Secure every login.') }}',
            desc: '{{ t('kouventa.Send one-time passwords on WhatsApp to verify users quickly and safely.', 'Send one-time passwords on WhatsApp to verify users quickly and safely.') }}',
            tag: '{{ t('kouventa.Secure OTP verification', 'Secure OTP verification') }}',
            bullets: [
                '{{ t('kouventa.Fast, secure user verification', 'Fast, secure user verification') }}',
                '{{ t('kouventa.OTP login for every account', 'OTP login for every account') }}',
                '{{ t('kouventa.Protection from unauthorized access', 'Protection from unauthorized access') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: a one-time password arrives on WhatsApp and the user is verified', 'Animated illustration: a one-time password arrives on WhatsApp and the user is verified') }}'
        },
        broadcast: {
            name: '{{ t('kouventa.Broadcasting', 'Broadcasting') }}',
            headline: '{{ t('kouventa.Reach more customers, at scale.', 'Reach more customers, at scale.') }}',
            desc: '{{ t('kouventa.Send promotions, campaigns and feedback requests on WhatsApp.', 'Send promotions, campaigns and feedback requests on WhatsApp.') }}',
            tag: '{{ t('kouventa.Campaigns at scale', 'Campaigns at scale') }}',
            bullets: [
                '{{ t('kouventa.Schedule message and promo blasts', 'Schedule message and promo blasts') }}',
                '{{ t('kouventa.Built-in campaign reports and analytics', 'Built-in campaign reports and analytics') }}',
                '{{ t('kouventa.Better campaign reach and awareness', 'Better campaign reach and awareness') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: one promotion is delivered to many customers and the campaign report fills in', 'Animated illustration: one promotion is delivered to many customers and the campaign report fills in') }}'
        },
        chatbot: {
            name: '{{ t('kouventa.Chatbot', 'Chatbot') }}',
            headline: '{{ t('kouventa.AI-powered customer support.', 'AI-powered customer support.') }}',
            desc: '{{ t('kouventa.AI and predefined chatbots answer fast, and hand over to a human when needed.', 'AI and predefined chatbots answer fast, and hand over to a human when needed.') }}',
            tag: '{{ t('kouventa.AI & predefined chatbots', 'AI & predefined chatbots') }}',
            bullets: [
                '{{ t('kouventa.Resolve inquiries faster', 'Resolve inquiries faster') }}',
                '{{ t('kouventa.Answer customers 24/7', 'Answer customers 24/7') }}',
                '{{ t('kouventa.Turn conversations into support tickets', 'Turn conversations into support tickets') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: a static bot greets the customer, an AI bot takes over the free-text question, then the chat is handed over to a human agent with a ticket', 'Animated illustration: a static bot greets the customer, an AI bot takes over the free-text question, then the chat is handed over to a human agent with a ticket') }}'
        },
        integration: {
            name: '{{ t('kouventa.Cross Platform Integration', 'Cross Platform Integration') }}',
            headline: '{{ t('kouventa.Keep customers updated, automatically.', 'Keep customers updated, automatically.') }}',
            desc: '{{ t('kouventa.Trigger WhatsApp messages from your business systems — reminders, orders, deliveries, appointments.', 'Trigger WhatsApp messages from your business systems — reminders, orders, deliveries, appointments.') }}',
            tag: '{{ t('kouventa.Automated system updates', 'Automated system updates') }}',
            bullets: [
                '{{ t('kouventa.Messages triggered by business events', 'Messages triggered by business events') }}',
                '{{ t('kouventa.Personalized, ready-to-use templates', 'Personalized, ready-to-use templates') }}',
                '{{ t('kouventa.Real-time customer updates', 'Real-time customer updates') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: an order event in a business system becomes an automatic WhatsApp update', 'Animated illustration: an order event in a business system becomes an automatic WhatsApp update') }}'
        },
        flows: {
            name: '{{ t('kouventa.WhatsApp Flows', 'WhatsApp Flows') }}',
            headline: '{{ t('kouventa.Collect customer data, right in the chat.', 'Collect customer data, right in the chat.') }}',
            desc: '{{ t('kouventa.Customers fill in interactive forms without leaving WhatsApp.', 'Customers fill in interactive forms without leaving WhatsApp.') }}',
            tag: '{{ t('kouventa.Forms inside the chat', 'Forms inside the chat') }}',
            bullets: [
                '{{ t('kouventa.No app or website needed', 'No app or website needed') }}',
                '{{ t('kouventa.Forms tailored to your business', 'Forms tailored to your business') }}',
                '{{ t('kouventa.A simpler data collection journey', 'A simpler data collection journey') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: a customer fills in a registration form inside WhatsApp', 'Animated illustration: a customer fills in a registration form inside WhatsApp') }}'
        },
        omni: {
            name: '{{ t('kouventa.Omnichannel', 'Omnichannel') }}',
            headline: '{{ t('kouventa.All conversations, one place.', 'All conversations, one place.') }}',
            desc: '{{ t('kouventa.Manage chats, DMs and comments from WhatsApp, Instagram, Facebook and Live Chat in a single platform.', 'Manage chats, DMs and comments from WhatsApp, Instagram, Facebook and Live Chat in a single platform.') }}',
            tag: '{{ t('kouventa.One inbox for all channels', 'One inbox for all channels') }}',
            bullets: [
                '{{ t('kouventa.One inbox for every channel', 'One inbox for every channel') }}',
                '{{ t('kouventa.Track agent performance and SLA', 'Track agent performance and SLA') }}',
                '{{ t('kouventa.Measure satisfaction with CSAT', 'Measure satisfaction with CSAT') }}'
            ],
            alt: '{{ t('kouventa.Animated illustration: messages from four channels flow into one unified inbox', 'Animated illustration: messages from four channels flow into one unified inbox') }}'
        }
    },
    pickStage(i) {
        if (this.linked && this.stage === i) {
            this.linked = false;
            return;
        }
        this.stage = i;
        this.product = this.stages[i].defaultTool;
        this.linked = true;
    },
    seeTools() {
        this.linked = true;
        this.product = this.stages[this.stage].defaultTool;
        $nextTick(() => {
            const el = document.getElementById('products');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    },
    isToolInStage(toolId) {
        if (!this.linked) return false;
        return !!this.stages[this.stage].tools[toolId];
    },
    getToolRole(toolId) {
        if (!this.linked) return '';
        return this.stages[this.stage].tools[toolId] || '';
    }
}">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Products" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ t('kouventa.Products', 'Products') }}</span>
<a href="{{ localized_url('/product/ai') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</a>
<a href="{{ localized_url('/product/customer-engagement') }}" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 80px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<span style="align-self: flex-start; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 8px 14px; border-radius: 999px; background: rgba(43,212,106,0.12); color: #2BD46A"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.Official Meta Business Solution Provider', 'Official Meta Business Solution Provider') }}</span>
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'The smarter way to' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'engage customers.' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">{!! nl2br(e($heroSubtitle)) !!}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ $heroCta['text'] ?? 'Book a demo' }}</a>
</div>
</div>

<!-- hero phone -->
<div role="img" aria-label="Animated illustration: a customer receives a promotion, asks a question, gets an instant answer and a verification code on WhatsApp" style="width: 340px; height: 560px; justify-self: center; background: #0C2A48; border-radius: 44px; padding: 12px; box-sizing: border-box; box-shadow: 0 40px 80px rgba(0,0,0,0.4); animation: kvFloat 6s ease-in-out infinite">
<div style="width: 100%; height: 100%; background: #EFEAE2; border-radius: 34px; overflow: hidden; display: flex; flex-direction: column">
<div style="background: #FFFFFF; color: #111B21; padding: 22px 14px 10px 10px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #E9EDEF; flex-shrink: 0">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#54656F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
<span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">YB</span>
<div style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 4px; font-size: 14.5px; font-weight: 700">Your Brand<svg width="15" height="15" viewBox="0 0 24 24" aria-label="Verified business" style="flex-shrink: 0"><path d="M12 1.8l2.4 1.8 3-.2.9 2.9 2.5 1.7-.9 2.9.9 2.9-2.5 1.7-.9 2.9-3-.2L12 20.2l-2.4-1.8-3 .2-.9-2.9-2.5-1.7.9-2.9-.9-2.9 2.5-1.7.9-2.9 3 .2Z" fill="#1D9BF0"></path><path d="M8 11.5l2.6 2.6L16 8.8" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span style="font-size: 11px; color: #667781">{{ t('kouventa.Official business account', 'Official business account') }}</span></div>
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#54656F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path></svg>
<svg width="16" height="16" viewBox="0 0 24 24" fill="#54656F" aria-hidden="true"><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
</div>
<div style="flex-grow: 1; min-height: 0; overflow: hidden; padding: 8px 10px; display: flex; flex-direction: column; gap: 7px; font-size: 13.5px; line-height: 1.45; color: #0E1A24">
<div style="align-self: flex-start; width: 82%; background: #FFFFFF; border-radius: 4px 14px 14px 14px; overflow: hidden; flex-shrink: 0; animation: kvH1 16s ease infinite both">
<div style="height: 46px; background: #071C33; display: flex; align-items: center; justify-content: center; color: #2BD46A; font-weight: 800; font-size: 17px">WEEKEND SALE</div>
<div style="padding: 9px 12px 7px">{{ t('kouventa.New collection is live. Tap below to shop.', 'New collection is live. Tap below to shop.') }}<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:20</span></div>
<div style="border-top: 1px solid #E9EDEF; padding: 9px; display: flex; align-items: center; justify-content: center; gap: 6px; color: #027EB5; font-weight: 600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>{{ t('kouventa.Shop now', 'Shop now') }}</div>
</div>
<div style="align-self: flex-end; max-width: 75%; background: #D9FDD3; color: #111B21; padding: 7px 10px 7px 12px; border-radius: 12px 4px 12px 12px; box-shadow: 0 1px 1px rgba(0,0,0,0.06); animation: kvH2 16s ease infinite both">{{ t('kouventa.Is size M still available?', 'Is size M still available?') }}<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:21<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#53BDEB" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read" style="vertical-align: -1px"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></div>
<div style="display: grid">
<div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; padding: 11px 12px; border-radius: 4px 14px 14px 14px; display: flex; gap: 4px; animation: kvHt1 16s ease infinite both"><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 82%; background: #FFFFFF; padding: 8px 12px; border-radius: 4px 14px 14px 14px; animation: kvH3 16s ease infinite both">{{ t('kouventa.Yes, size M is in stock. Want me to reserve it for you?', 'Yes, size M is in stock. Want me to reserve it for you?') }}<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:21</span></div>
</div>
<div style="align-self: flex-end; max-width: 75%; background: #D9FDD3; color: #111B21; padding: 7px 10px 7px 12px; border-radius: 12px 4px 12px 12px; box-shadow: 0 1px 1px rgba(0,0,0,0.06); animation: kvH4 16s ease infinite both">{{ t('kouventa.Yes, please!', 'Yes, please!') }}<span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:22<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#53BDEB" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read" style="vertical-align: -1px"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></div>
<div style="display: grid">
<div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; padding: 11px 12px; border-radius: 4px 14px 14px 14px; display: flex; gap: 4px; animation: kvHt2 16s ease infinite both"><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 6px; height: 6px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 85%; background: #FFFFFF; padding: 8px 12px; border-radius: 4px 14px 14px 14px; animation: kvH5 16s ease infinite both">{{ t('kouventa.Reserved! To confirm, your verification code is', 'Reserved! To confirm, your verification code is') }} <strong style="font-family: 'JetBrains Mono', monospace; letter-spacing: 0.1em">482 917</strong><span style="float: right; margin: 7px 0 -3px 10px; font-size: 10.5px; color: #667781; display: inline-flex; align-items: center; gap: 3px">10:22</span></div>
</div>
<div style="align-self: center; display: flex; align-items: center; gap: 8px; background: #DDF3E4; color: #0B5E2C; padding: 8px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; animation: kvH6 16s ease infinite both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>{{ t('kouventa.Verified · Order reserved', 'Verified · Order reserved') }}</div>
</div>
<div style="flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 6px 10px 10px">
<span style="flex-grow: 1; height: 38px; border-radius: 999px; background: #FFFFFF; display: flex; align-items: center; padding: 0 14px; font-size: 13px; color: #8696A0">{{ t('kouventa.Message', 'Message') }}</span>
<span style="width: 38px; height: 38px; flex-shrink: 0; border-radius: 50%; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"></rect><path d="M5 11a7 7 0 0 0 14 0M12 18v3"></path></svg></span>
</div>
</div>
</div>
</div>
</section>

<!-- ============ CHALLENGES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">@if(!empty($channelsTitle['prefix'])){{ $channelsTitle['prefix'] }} <span style="color: #13803F">{{ $channelsTitle['main'] }}</span>@else{{ $channelsTitle['main'] ?? $channelsTitle }}@endif</h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px">
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.2s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center; gap: 10px">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; transform-origin: 50% 50%; animation: kvWobble 1.4s ease .6s 1 both"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2M9 2h6"></path></svg></span>
<span style="display: flex; gap: 4px"><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .4s infinite"></span></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Customers wait too long for a reply', 'Customers wait too long for a reply') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.', 'Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.8s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center; gap: 12px">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m4.9 4.9 14.2 14.2"></path></svg></span>
<span style="display: flex; gap: 6px"><span style="width: 16px; height: 6px; border-radius: 3px; background: #F0C4BA"></span><span style="width: 24px; height: 6px; border-radius: 3px; background: #E79F8E"></span><span style="width: 12px; height: 6px; border-radius: 3px; background: #F0C4BA"></span></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Campaigns don\'t reach the right people', 'Campaigns don\'t reach the right people') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Promotions sent manually reach too few customers, and there\'s no clear view of who received, read, or acted on them.', 'Promotions sent manually reach too few customers, and there\'s no clear view of who received, read, or acted on them.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 1.4s both">
<div aria-hidden="true" style="height: 56px; position: relative; width: 140px">
<span style="position: absolute; left: 0; top: 12px; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span>
<span style="position: absolute; left: 44px; top: 0; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></span>
<span style="position: absolute; left: 88px; top: 16px; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Conversations are scattered across channels', 'Conversations are scattered across channels') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.', 'WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.') }}</p>
</article>
</div>
</section>

<!-- ============ JOURNEY ============ -->
<section id="journey" style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 14px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Never miss a', 'Never miss a') }} <span style="color: #13803F">{{ t('kouventa.message.', 'message.') }}</span></h2>
<p style="margin: 0; font-size: 18px; line-height: 1.5; color: #4A5866">{{ t('kouventa.From first hello to after-sales. Pick a stage to see which products help.', 'From first hello to after-sales. Pick a stage to see which products help.') }}</p>
</div>
<div role="tablist" aria-label="Customer journey stage" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 28px 0 26px">
<div style="position: relative; height: 176px">
<span aria-hidden="true" style="position: absolute; top: 76px; left: 12.5%; width: 75%; height: 4px; border-radius: 4px; background: repeating-linear-gradient(90deg, #C9D2CE 0 10px, transparent 10px 18px)"></span>
<span aria-hidden="true" class="kv-ce-track-fill" style="position: absolute; top: 76px; left: 12.5%; height: 4px; border-radius: 4px; background: #13803F; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1)" :style="{ width: (stage * 25) + '%' }"></span>
<span aria-hidden="true" class="kv-ce-customer-pointer" style="position: absolute; top: 0; transform: translateX(-50%); transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; z-index: 10" :style="{ left: (12.5 + stage * 25) + '%' }">
<span style="animation: kvFloat 2.4s ease-in-out infinite; display: flex; flex-direction: column; align-items: center">
<span style="display: flex; align-items: center; gap: 8px; padding: 5px 14px 5px 5px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-size: 13px; font-weight: 700; white-space: nowrap; box-shadow: 0 6px 16px rgba(7, 28, 51, 0.18)"><span style="width: 26px; height: 26px; border-radius: 50%; background: #2BD46A; color: #071C33; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg></span>{{ t('kouventa.Your customer', 'Your customer') }}</span>
<span style="width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 7px solid #071C33"></span>
</span>
</span>

<!-- Stage 0: Prospecting -->
<button type="button" role="tab" :aria-selected="stage === 0" @click="pickStage(0)" style="position: absolute; top: 50px; left: 12.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
<span :style="{
    background: stage === 0 ? '#071C33' : (stage > 0 ? '#EAF7EF' : '#FFFFFF'),
    borderColor: stage === 0 ? '#071C33' : (stage > 0 ? '#13803F' : '#C9D2CE'),
    color: stage === 0 ? '#2BD46A' : (stage > 0 ? '#13803F' : '#6B7A88'),
    animation: stage === 0 ? 'kvPulse 2s ease-out infinite' : 'none'
}" style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle></svg>
</span>
<span :style="{ color: stage >= 0 ? '#13803F' : '#6B7A88' }" style="font-family: 'JetBrains Mono', monospace; font-size: 12px">01</span>
<span :style="{ color: stage === 0 ? '#0E1A24' : '#4A5866' }" style="font-size: 20px; font-weight: 800">{{ t('kouventa.Prospecting', 'Prospecting') }}</span>
</button>

<!-- Stage 1: Onboarding -->
<button type="button" role="tab" :aria-selected="stage === 1" @click="pickStage(1)" style="position: absolute; top: 50px; left: 37.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
<span :style="{
    background: stage === 1 ? '#071C33' : (stage > 1 ? '#EAF7EF' : '#FFFFFF'),
    borderColor: stage === 1 ? '#071C33' : (stage > 1 ? '#13803F' : '#C9D2CE'),
    color: stage === 1 ? '#2BD46A' : (stage > 1 ? '#13803F' : '#6B7A88'),
    animation: stage === 1 ? 'kvPulse 2s ease-out infinite' : 'none'
}" style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="8" r="4"></circle><path d="M2 21a8 8 0 0 1 13.3-6"></path><path d="M19 14v6M16 17h6"></path></svg>
</span>
<span :style="{ color: stage >= 1 ? '#13803F' : '#6B7A88' }" style="font-family: 'JetBrains Mono', monospace; font-size: 12px">02</span>
<span :style="{ color: stage === 1 ? '#0E1A24' : '#4A5866' }" style="font-size: 20px; font-weight: 800">{{ t('kouventa.Onboarding', 'Onboarding') }}</span>
</button>

<!-- Stage 2: Upselling -->
<button type="button" role="tab" :aria-selected="stage === 2" @click="pickStage(2)" style="position: absolute; top: 50px; left: 62.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
<span :style="{
    background: stage === 2 ? '#071C33' : (stage > 2 ? '#EAF7EF' : '#FFFFFF'),
    borderColor: stage === 2 ? '#071C33' : (stage > 2 ? '#13803F' : '#C9D2CE'),
    color: stage === 2 ? '#2BD46A' : (stage > 2 ? '#13803F' : '#6B7A88'),
    animation: stage === 2 ? 'kvPulse 2s ease-out infinite' : 'none'
}" style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"></path><path d="M15 7h6v6"></path></svg>
</span>
<span :style="{ color: stage >= 2 ? '#13803F' : '#6B7A88' }" style="font-family: 'JetBrains Mono', monospace; font-size: 12px">03</span>
<span :style="{ color: stage === 2 ? '#0E1A24' : '#4A5866' }" style="font-size: 20px; font-weight: 800">{{ t('kouventa.Upselling', 'Upselling') }}</span>
</button>

<!-- Stage 3: Support -->
<button type="button" role="tab" :aria-selected="stage === 3" @click="pickStage(3)" style="position: absolute; top: 50px; left: 87.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
<span :style="{
    background: stage === 3 ? '#071C33' : '#FFFFFF',
    borderColor: stage === 3 ? '#071C33' : '#C9D2CE',
    color: stage === 3 ? '#2BD46A' : '#6B7A88',
    animation: stage === 3 ? 'kvPulse 2s ease-out infinite' : 'none'
}" style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect><path d="M20 20a3 3 0 0 1-3 3h-3"></path></svg>
</span>
<span :style="{ color: stage >= 3 ? '#13803F' : '#6B7A88' }" style="font-family: 'JetBrains Mono', monospace; font-size: 12px">04</span>
<span :style="{ color: stage === 3 ? '#0E1A24' : '#4A5866' }" style="font-size: 20px; font-weight: 800">{{ t('kouventa.Support', 'Support') }}</span>
</button>
</div>
</div>

<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px 44px; display: flex; justify-content: space-between; align-items: center; gap: 48px">
<div style="display: flex; flex-direction: column; gap: 10px; max-width: 760px">
<span x-text="stages[stage].headline" style="font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">{{ t('kouventa.Turn prospects into customers.', 'Turn prospects into customers.') }}</span>
<span x-text="stages[stage].sub" style="font-size: 17px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Reach and engage potential customers with targeted campaigns across channels.', 'Reach and engage potential customers with targeted campaigns across channels.') }}</span>
</div>
<button type="button" @click="seeTools()" style="flex-shrink: 0; min-height: 52px; padding: 14px 22px; border-radius: 12px; border: 0; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px; cursor: pointer">
<span>{{ t('kouventa.See the products for', 'See the products for') }} <span x-text="stages[stage].name">{{ t('kouventa.Prospecting', 'Prospecting') }}</span></span>
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
</button>
</div>
</section>

<!-- ============ PRODUCTS ============ -->
<section id="products" style="padding: 48px 80px 104px; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; align-items: flex-start; gap: 16px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">@if(!empty($featuresTitle['prefix'])){{ $featuresTitle['prefix'] }} <span style="color: #13803F">{{ $featuresTitle['main'] }}</span>@else{{ $featuresTitle['main'] ?? $featuresTitle }}@endif</h2>
<span class="kv-linked-pill" x-show="linked" x-cloak style="min-height: 48px; box-sizing: border-box; display: inline-flex; align-items: center; gap: 10px; padding: 6px 6px 6px 16px; border-radius: 999px; background: #FFFFFF; border: 1px solid #E3E8E6; font-size: 15px; color: #4A5866; white-space: nowrap">
<span class="kv-pill-dot" style="width: 10px; height: 10px; border-radius: 50%; background: #13803F; flex-shrink: 0; display: inline-block"></span>
<span>{{ t('kouventa.Highlighted: how each product helps in', 'Highlighted: how each product helps in') }} <strong style="color: #0E1A24" x-text="stages[stage].name">{{ t('kouventa.Prospecting', 'Prospecting') }}</strong></span>
<button type="button" @click="linked = false" class="kv-btn-show-all" style="margin-left: 6px; white-space: nowrap; flex-shrink: 0; min-height: 36px; padding: 6px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer">{{ t('kouventa.Show all products', 'Show all products') }}<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></button>
</span>
<span class="kv-unlinked-pill" x-show="!linked" style="min-height: 48px; display: inline-flex; align-items: center; font-size: 15px; color: #4A5866">
<a href="#journey" @click.prevent="document.getElementById('journey')?.scrollIntoView({ behavior: 'smooth' })" class="kv-unlinked-link" style="display: inline-flex; align-items: center; gap: 8px; color: #4A5866; text-decoration: none; cursor: pointer">
<span>{{ t('kouventa.Pick a journey stage above to see which products help', 'Pick a journey stage above to see which products help') }}</span>
<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</a>
</span>
</div>

<div style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; align-items: stretch">
<div role="tablist" aria-label="Products" aria-orientation="vertical" style="display: flex; flex-direction: column; gap: 10px">

<!-- Auth Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'auth'" @click="product = 'auth'"
    :style="{
        background: product === 'auth' ? '#071C33' : '#FFFFFF',
        color: product === 'auth' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'auth' ? '#071C33' : (isToolInStage('auth') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('auth') || product === 'auth') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'auth' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'auth' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Authentication', 'Authentication') }}</span>
<span x-show="isToolInStage('auth')" x-text="getToolRole('auth')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('auth')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Secure OTP verification', 'Secure OTP verification') }}</span>
</span>
<span :style="{ background: isToolInStage('auth') ? (product === 'auth' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Broadcast Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'broadcast'" @click="product = 'broadcast'"
    :style="{
        background: product === 'broadcast' ? '#071C33' : '#FFFFFF',
        color: product === 'broadcast' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'broadcast' ? '#071C33' : (isToolInStage('broadcast') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('broadcast') || product === 'broadcast') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'broadcast' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'broadcast' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Broadcasting', 'Broadcasting') }}</span>
<span x-show="isToolInStage('broadcast')" x-text="getToolRole('broadcast')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('broadcast')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Campaigns at scale', 'Campaigns at scale') }}</span>
</span>
<span :style="{ background: isToolInStage('broadcast') ? (product === 'broadcast' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Chatbot Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'chatbot'" @click="product = 'chatbot'"
    :style="{
        background: product === 'chatbot' ? '#071C33' : '#FFFFFF',
        color: product === 'chatbot' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'chatbot' ? '#071C33' : (isToolInStage('chatbot') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('chatbot') || product === 'chatbot') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'chatbot' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'chatbot' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Chatbot', 'Chatbot') }}</span>
<span x-show="isToolInStage('chatbot')" x-text="getToolRole('chatbot')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('chatbot')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.AI & predefined chatbots', 'AI & predefined chatbots') }}</span>
</span>
<span :style="{ background: isToolInStage('chatbot') ? (product === 'chatbot' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Integration Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'integration'" @click="product = 'integration'"
    :style="{
        background: product === 'integration' ? '#071C33' : '#FFFFFF',
        color: product === 'integration' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'integration' ? '#071C33' : (isToolInStage('integration') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('integration') || product === 'integration') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'integration' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'integration' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Cross Platform Integration', 'Cross Platform Integration') }}</span>
<span x-show="isToolInStage('integration')" x-text="getToolRole('integration')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('integration')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Automated system updates', 'Automated system updates') }}</span>
</span>
<span :style="{ background: isToolInStage('integration') ? (product === 'integration' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Flows Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'flows'" @click="product = 'flows'"
    :style="{
        background: product === 'flows' ? '#071C33' : '#FFFFFF',
        color: product === 'flows' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'flows' ? '#071C33' : (isToolInStage('flows') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('flows') || product === 'flows') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'flows' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'flows' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.WhatsApp Flows', 'WhatsApp Flows') }}</span>
<span x-show="isToolInStage('flows')" x-text="getToolRole('flows')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('flows')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Forms inside the chat', 'Forms inside the chat') }}</span>
</span>
<span :style="{ background: isToolInStage('flows') ? (product === 'flows' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Omni Product Tab -->
<button type="button" role="tab" :aria-selected="product === 'omni'" @click="product = 'omni'"
    :style="{
        background: product === 'omni' ? '#071C33' : '#FFFFFF',
        color: product === 'omni' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'omni' ? '#071C33' : (isToolInStage('omni') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('omni') || product === 'omni') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span :style="{
    background: product === 'omni' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'omni' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg>
</span>
<span style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span style="font-size: 16px; font-weight: 800">{{ t('kouventa.Omnichannel', 'Omnichannel') }}</span>
<span x-show="isToolInStage('omni')" x-text="getToolRole('omni')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span x-show="!isToolInStage('omni')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.One inbox for all channels', 'One inbox for all channels') }}</span>
</span>
<span :style="{ background: isToolInStage('omni') ? (product === 'omni' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

</div>

<!-- Product Detail Panel (Right Side) -->
<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1fr) 460px; gap: 40px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 18px">
<span x-text="products[product].name" style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">{{ t('kouventa.Broadcasting', 'Broadcasting') }}</span>
<span x-text="products[product].headline" style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Reach more customers, at scale.', 'Reach more customers, at scale.') }}</span>
<span x-text="products[product].desc" style="font-size: 17px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Send promotions, campaigns and feedback requests on WhatsApp.', 'Send promotions, campaigns and feedback requests on WhatsApp.') }}</span>
<ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px">
<template x-for="(bullet, bIdx) in products[product].bullets" :key="bIdx">
<li style="display: flex; gap: 12px; align-items: center; font-size: 16px; font-weight: 600"><span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span x-text="bullet"></span></li>
</template>
</ul>
</div>

<!-- illustrations -->
<div role="img" :aria-label="products[product].alt" style="height: 420px; border-radius: 18px; background: #F4F6F5; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center">

<!-- Auth Illustration -->
<div class="kv-flex-col" x-show="product === 'auth'" style="width: 300px; gap: 14px">
<div style="background: #FFFFFF; border-radius: 16px; padding: 18px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 13px; font-weight: 700; color: #4A5866">{{ t('kouventa.Sign in to Your Brand', 'Sign in to Your Brand') }}</span>
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
<div style="align-self: flex-start; background: #FFFFFF; border-radius: 4px 14px 14px 14px; padding: 12px 14px; font-size: 13px; line-height: 1.5; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 8s ease 0.3s infinite both"><strong>Your Brand</strong> · WhatsApp<br>{{ t('kouventa.Your code is', 'Your code is') }} <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600">482917</span>. {{ t('kouventa.. Do not share it.', '. Do not share it.') }}</div>
<div style="align-self: center; display: flex; align-items: center; gap: 8px; background: #071C33; color: #FFFFFF; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; animation: kvPop 8s ease 1s infinite both"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.Verified', 'Verified') }}</div>
</div>

<!-- Broadcast Illustration -->
<div class="kv-flex-col" x-show="product === 'broadcast'" style="width: 400px; gap: 22px">
<div style="display: flex; align-items: center; gap: 0">
<div style="width: 150px; flex-shrink: 0; background: #FFFFFF; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvPulse 2.4s ease infinite">
<div style="height: 54px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">PROMO</div>
<div style="padding: 8px 10px; font-size: 11px; line-height: 1.4">{{ t('kouventa.Weekend sale is live.', 'Weekend sale is live.') }}</div>
<div style="border-top: 1px solid #E3E8E6; padding: 6px; text-align: center; font-size: 11px; color: #13803F; font-weight: 700">{{ t('kouventa.Shop now', 'Shop now') }}</div>
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
<div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700"><span>{{ t('kouventa.Campaign report', 'Campaign report') }}</span><span style="color: #4A5866; font-weight: 500">{{ t('kouventa.Scheduled · Sat 09:00', 'Scheduled · Sat 09:00') }}</span></div>
<div style="display: grid; grid-template-columns: 70px minmax(0, 1fr); gap: 8px 10px; align-items: center; font-size: 12px; color: #4A5866">
<span>{{ t('kouventa.Delivered', 'Delivered') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #13803F; --w: 92%; animation: kvGrow 6s ease infinite both"></div></div>
<span>{{ t('kouventa.Read', 'Read') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #2E9E5B; --w: 70%; animation: kvGrow 6s ease 0.3s infinite both"></div></div>
<span>{{ t('kouventa.Clicked', 'Clicked') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px"><div style="height: 8px; border-radius: 999px; background: #071C33; --w: 38%; animation: kvGrow 6s ease 0.6s infinite both"></div></div>
</div>
</div>
</div>

<!-- Chatbot Illustration -->
<div class="kv-flex-col" x-show="product === 'chatbot'" style="width: 350px; gap: 8px; font-size: 12.5px; line-height: 1.45">
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 10px 12px; border-radius: 4px 14px 14px 14px; display: flex; flex-direction: column; gap: 8px; animation: kvLoop 12s ease 0.3s infinite both">
<span><span style="font-size: 10.5px; font-weight: 800; color: #4A5866">{{ t('kouventa.STATIC BOT', 'STATIC BOT') }}</span><br>{{ t('kouventa.Hi! How can we help you today?', 'Hi! How can we help you today?') }}</span>
<div style="display: flex; gap: 6px; flex-wrap: wrap"><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">{{ t('kouventa.Track order', 'Track order') }}</span><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">Promo</span><span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 999px; border: 1px solid #13803F; color: #13803F">{{ t('kouventa.Store hours', 'Store hours') }}</span></div>
</div>
<div style="align-self: flex-end; max-width: 80%; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; animation: kvLoop 12s ease 1.6s infinite both">{{ t('kouventa.My package says delivered, but I don\'t have it.', 'My package says delivered, but I don\'t have it.') }}</div>
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 14px 14px 14px; animation: kvLoop 12s ease 2.8s infinite both"><span style="font-size: 10.5px; font-weight: 800; color: #13803F">{{ t('kouventa.AI BOT · TOOK OVER', 'AI BOT · TOOK OVER') }}</span><br>{{ t('kouventa.I checked your order: it was left at the front desk at 14:10. Want me to connect you to our team?', 'I checked your order: it was left at the front desk at 14:10. Want me to connect you to our team?') }}</div>
<div style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 9px 12px; border-radius: 14px 4px 14px 14px; animation: kvLoop 12s ease 4.2s infinite both">{{ t('kouventa.Yes, please.', 'Yes, please.') }}</div>
<div style="align-self: center; background: #DDF3E4; color: #0B5E2C; padding: 7px 14px; border-radius: 999px; font-size: 11.5px; font-weight: 800; animation: kvLoop 12s ease 5.2s infinite both">{{ t('kouventa.Handed over to human agent · Ticket created', 'Handed over to human agent · Ticket created') }}</div>
<div style="align-self: flex-start; max-width: 88%; background: #FFFFFF; padding: 9px 12px; border-radius: 4px 14px 14px 14px; animation: kvLoop 12s ease 6.2s infinite both"><span style="font-size: 10.5px; font-weight: 800; color: #071C33">DEWI · SUPPORT AGENT</span><br>{{ t('kouventa.Hi, I can see the full chat. Let me sort this out with the courier.', 'Hi, I can see the full chat. Let me sort this out with the courier.') }}</div>
</div>

<!-- Integration Illustration -->
<div class="kv-flex kv-items-center" x-show="product === 'integration'" style="width: 420px">
<div style="width: 118px; flex-shrink: 0; background: #FFFFFF; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 12px; font-weight: 700">{{ t('kouventa.Your system', 'Your system') }}</span>
<span style="font-size: 11px; color: #4A5866">ERP · CRM · POS</span>
<span style="font-size: 11px; font-weight: 700; padding: 6px 8px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C; animation: kvLoop 6s ease 0.2s infinite both">{{ t('kouventa.Order shipped', 'Order shipped') }}</span>
</div>
<div style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear infinite"></span></div>
<div style="width: 56px; height: 56px; flex-shrink: 0; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 22px; animation: kvPulse 2.4s ease infinite">K</div>
<div style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear 0.8s infinite"></span></div>
<div style="width: 160px; flex-shrink: 0; display: flex; flex-direction: column; gap: 8px; font-size: 12px; line-height: 1.45">
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 1.4s infinite both">{{ t('kouventa.Hi Rina, your order is on the way.', 'Hi Rina, your order is on the way.') }} <span style="color: #13803F; font-weight: 700">{{ t('kouventa.Track order', 'Track order') }}</span></div>
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 2.4s infinite both">{{ t('kouventa.Reminder: your appointment is tomorrow, 10:00.', 'Reminder: your appointment is tomorrow, 10:00.') }}</div>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4A5866">template: order_update</span>
</div>
</div>

<!-- Flows Illustration -->
<div class="kv-flex-col" x-show="product === 'flows'" style="width: 300px; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1)">
<div style="background: #071C33; color: #FFFFFF; padding: 14px 16px; font-size: 14px; font-weight: 700">{{ t('kouventa.Open an account', 'Open an account') }}</div>
<div style="padding: 16px; display: flex; flex-direction: column; gap: 12px">
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Full name', 'Full name') }}</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 60%; animation: kvGrow 7s ease 0.2s infinite both"></div></div></div>
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">Email</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 80%; animation: kvGrow 7s ease 1s infinite both"></div></div></div>
<div style="display: flex; flex-direction: column; gap: 5px"><span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.City', 'City') }}</span><div style="height: 36px; border-radius: 8px; border: 1px solid #E3E8E6; padding: 0 10px; display: flex; align-items: center"><div style="height: 8px; border-radius: 4px; background: #0E1A24; --w: 40%; animation: kvGrow 7s ease 1.8s infinite both"></div></div></div>
<div style="display: flex; gap: 8px; font-size: 12px"><span style="padding: 6px 10px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-weight: 600; animation: kvLoop 7s ease 2.4s infinite both">Personal</span><span style="padding: 6px 10px; border-radius: 999px; border: 1px solid #E3E8E6">Business</span></div>
<div style="height: 42px; border-radius: 10px; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700">{{ t('kouventa.Submit', 'Submit') }}</div>
<div style="align-self: center; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #0B5E2C; animation: kvPop 7s ease 2.6s infinite both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>{{ t('kouventa.Data received — without leaving the chat', 'Data received — without leaving the chat') }}</div>
</div>
</div>

<!-- Omni Illustration -->
<div class="kv-flex kv-items-center" x-show="product === 'omni'" style="width: 420px; gap: 0">
<div style="width: 118px; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px">
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>WhatsApp</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">Chat</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Instagram</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.DM & comments', 'DM & comments') }}</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Facebook</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.DM & comments', 'DM & comments') }}</span></span></span>
<span style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Live Chat</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.Website chat', 'Website chat') }}</span></span></span>
</div>
<div style="width: 50px; flex-shrink: 0; height: 214px; display: flex; flex-direction: column; justify-content: space-around">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.45s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.9s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 1.35s infinite"></span></div>
</div>
<div style="flex-grow: 1; background: #FFFFFF; border-radius: 16px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<span style="font-size: 12px; font-weight: 700">{{ t('kouventa.Unified inbox', 'Unified inbox') }}</span>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 0.4s infinite both"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Rina</strong><span style="padding: 1px 6px; border-radius: 5px; background: #E6EAEE; color: #33414E; font-size: 9.5px; font-weight: 800">Chat</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Where\'s my order?', 'Where\'s my order?') }}</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 1s infinite both"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Sari</strong><span style="padding: 1px 6px; border-radius: 5px; background: #FFF1D6; color: #8A5A00; font-size: 9.5px; font-weight: 800">{{ t('kouventa.Comment', 'Comment') }}</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Is this available in size M?', 'Is this available in size M?') }}</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 1.6s infinite both"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Budi</strong><span style="padding: 1px 6px; border-radius: 5px; background: #FFF1D6; color: #8A5A00; font-size: 9.5px; font-weight: 800">{{ t('kouventa.Comment', 'Comment') }}</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.How much for 2 pcs?', 'How much for 2 pcs?') }}</span></span></div>
<div style="display: flex; align-items: center; gap: 8px; padding: 7px 8px; border-radius: 8px; background: #F4F6F5; font-size: 11px; animation: kvLoop 8s ease 2.2s infinite both"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="" style="width: 18px; height: 18px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0; flex-grow: 1"><span style="display: flex; align-items: center; gap: 6px"><strong style="font-weight: 700">Andi</strong><span style="padding: 1px 6px; border-radius: 5px; background: #E6EAEE; color: #33414E; font-size: 9.5px; font-weight: 800">Chat</span></span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ t('kouventa.Can I change my address?', 'Can I change my address?') }}</span></span></div>
<div style="display: flex; gap: 6px; padding-top: 4px; font-size: 10px; font-weight: 700"><span style="padding: 4px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C">{{ t('kouventa.SLA on track', 'SLA on track') }}</span><span style="padding: 4px 8px; border-radius: 999px; background: #071C33; color: #FFFFFF">{{ t('kouventa.CSAT collected', 'CSAT collected') }}</span></div>
</div>
</div>

</div>
</div>
</div>
</section>

<!-- ============ PLATFORM CONTROL ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Stay in control of every conversation.', 'Stay in control of every conversation.') }}</h2>
<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px">
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 36px 32px; display: flex; flex-direction: column; gap: 14px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">{{ t('kouventa.Supervisor mode', 'Supervisor mode') }}</span>
<h3 style="margin: 0; font-size: 24px; font-weight: 800">{{ t('kouventa.Role-based access', 'Role-based access') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Leads see their team\'s chats. Agents see only their own.', 'Leads see their team\'s chats. Agents see only their own.') }}</p>
<ul style="margin: 0; padding: 14px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #4A5866">
<li style="display: flex; gap: 10px; align-items: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">✓</span>{{ t('kouventa.Every chat stored for visibility and follow-up.', 'Every chat stored for visibility and follow-up.') }}</li>
<li style="display: flex; gap: 10px; align-items: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">✓</span>{{ t('kouventa.Decide who can use which modules, features and accounts.', 'Decide who can use which modules, features and accounts.') }}</li>
</ul>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 36px 32px; display: flex; flex-direction: column; gap: 14px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">{{ t('kouventa.Multi-account', 'Multi-account') }}</span>
<h3 style="margin: 0; font-size: 24px; font-weight: 800">{{ t('kouventa.All business accounts and numbers, one platform.', 'All business accounts and numbers, one platform.') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Manage multiple brands, entities or branches under a single roof.', 'Manage multiple brands, entities or branches under a single roof.') }}</p>
<ul style="margin: 0; padding: 14px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #4A5866">
<li style="display: flex; gap: 10px; align-items: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">✓</span>{{ t('kouventa.Multi-brand and branch organization structure.', 'Multi-brand and branch organization structure.') }}</li>
<li style="display: flex; gap: 10px; align-items: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">✓</span>{{ t('kouventa.Shared templates and unified governance.', 'Shared templates and unified governance.') }}</li>
</ul>
</article>
</div>
</section>

<!-- ============ WHY ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<div style="display: flex; flex-direction: column; gap: 12px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">{{ t('kouventa.Why Kouventa', 'Why Kouventa') }}</span>
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $whyTitle['prefix'] ?? 'Why Kouventa for customer' }} <span style="color: #13803F">{{ $whyTitle['main'] ?? 'engagement' }}</span></h2>
</div>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 24px">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
<h3 style="margin: 0; font-size: 20px; font-weight: 800">{{ t('kouventa.Official partner', 'Official partner') }}</h3>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Trusted access to the WhatsApp Business Platform.', 'Trusted access to the WhatsApp Business Platform.') }}</p>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg></span>
<h3 style="margin: 0; font-size: 20px; font-weight: 800">{{ t('kouventa.Built around your business', 'Built around your business') }}</h3>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Customized to your industry, flows and systems.', 'Customized to your industry, flows and systems.') }}</p>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg></span>
<h3 style="margin: 0; font-size: 20px; font-weight: 800">{{ t('kouventa.Post-paid messaging', 'Post-paid messaging') }}</h3>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Send first. Pay later, for what you actually use.', 'Send first. Pay later, for what you actually use.') }}</p>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
<h3 style="margin: 0; font-size: 20px; font-weight: 800">{{ t('kouventa.Secure & trusted', 'Secure & trusted') }}</h3>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Enterprise-grade security for business and customer data.', 'Enterprise-grade security for business and customer data.') }}</p>
</div>
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 104px 80px 104px">
<div style="background: #071C33; color: #FFFFFF; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 40px; position: relative; overflow: hidden">
<div style="position: absolute; right: -120px; top: -140px; width: 440px; height: 440px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.06); pointer-events: none"></div>
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 600px; position: relative; z-index: 2">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #2BD46A">{{ t('kouventa.Certified & Registered', 'Certified & Registered') }}</span>
<h2 style="margin: 0; font-size: 40px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ $bottomCtaTitle }}</h2>
<p style="margin: 0; font-size: 17px; line-height: 1.6; color: #B9C6D3">{!! nl2br(e($bottomCtaSubtitle)) !!}</p>
</div>
<div style="display: flex; gap: 14px; position: relative; z-index: 2; flex-shrink: 0">
<a href="{{ localized_url('/pricing') }}" style="background: transparent; color: #FFFFFF; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28); text-decoration: none">{{ t('kouventa.See pricing', 'See pricing') }}</a>
<a href="{{ localized_url('/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 26px; border-radius: 12px; text-decoration: none">{{ t('kouventa.Book a demo', 'Book a demo') }}</a>
</div>
</div>
</section>

</div>
@endsection
