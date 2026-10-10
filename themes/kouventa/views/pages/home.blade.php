@extends('kouventa::layouts.app')


@php
    $teams = [
        [
            'name' => t('kouventa.Customer Service', 'Customer Service'),
            'line' => t('kouventa.Resolve routine inquiries, route the rest.', 'Resolve routine inquiries, route the rest.'),
            'tab' => 0,
            'tasks' => [
                t('kouventa.Answer routine questions from your knowledge', 'Answer routine questions from your knowledge'),
                t('kouventa.Create and assign support tickets', 'Create and assign support tickets'),
                t('kouventa.Escalate complex cases to the right team', 'Escalate complex cases to the right team')
            ]
        ],
        [
            'name' => t('kouventa.Recruiting', 'Recruiting'),
            'line' => t('kouventa.Hire faster, with less admin.', 'Hire faster, with less admin.'),
            'tab' => 1,
            'tasks' => [
                t('kouventa.Candidate screening and sourcing', 'Candidate screening and sourcing'),
                t('kouventa.Interview scheduling', 'Interview scheduling'),
                t('kouventa.Insights from candidate data', 'Insights from candidate data')
            ]
        ],
        [
            'name' => t('kouventa.Sales & Marketing', 'Sales & Marketing'),
            'line' => t('kouventa.Reply faster, follow up, close more.', 'Reply faster, follow up, close more.'),
            'tab' => 2,
            'tasks' => [
                t('kouventa.Product and availability answers', 'Product and availability answers'),
                t('kouventa.Order and payment confirmation', 'Order and payment confirmation'),
                t('kouventa.Shipment tracking updates', 'Shipment tracking updates')
            ]
        ],
        [
            'name' => t('kouventa.Business Operations', 'Business Operations'),
            'line' => t('kouventa.Answers from your SOPs, actions in your systems.', 'Answers from your SOPs, actions in your systems.'),
            'tab' => 3,
            'tasks' => [
                t('kouventa.Answers from policies and SOPs', 'Answers from policies and SOPs'),
                t('kouventa.Requests submitted on your behalf', 'Requests submitted on your behalf'),
                t('kouventa.Updates across business systems', 'Updates across business systems')
            ]
        ],
        [
            'name' => t('kouventa.Finance', 'Finance'),
            'line' => t('kouventa.Reconcile in minutes, with reasons.', 'Reconcile in minutes, with reasons.'),
            'tab' => 4,
            'tasks' => [
                t('kouventa.Payment prioritization', 'Payment prioritization'),
                t('kouventa.Transaction matching', 'Transaction matching'),
                t('kouventa.Audit trail and notifications', 'Audit trail and notifications')
            ]
        ],
        [
            'name' => t('kouventa.Legal & Compliance', 'Legal & Compliance'),
            'line' => t('kouventa.Never miss a clause or regulation.', 'Never miss a clause or regulation.'),
            'tab' => 5,
            'tasks' => [
                t('kouventa.Ask your documents', 'Ask your documents'),
                t('kouventa.Summarize key terms', 'Summarize key terms'),
                t('kouventa.Find relevant regulations', 'Find relevant regulations')
            ]
        ]
    ];

    $stories = [
        [
            'company' => "Luna's Doughnuts",
            'stat' => '+55%',
            'statLabel' => t('kouventa.time saved in daily communication and coordination', 'time saved in daily communication and coordination'),
            'stat2' => '35%',
            'stat2Label' => t('kouventa.lower operational costs', 'lower operational costs'),
            'quote' => t('kouventa.Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we\'ve achieved faster customer responses and significant cost savings.', 'Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we\'ve achieved faster customer responses and significant cost savings.'),
            'name' => 'Caca Patricia',
            'role' => 'Partnership Specialist, PT Kaluna Indonesia Group'
        ],
        [
            'company' => 'PakeDriver',
            'stat' => '+45%',
            'statLabel' => t('kouventa.improvement in customer engagement', 'improvement in customer engagement'),
            'stat2' => '30%',
            'stat2Label' => t('kouventa.faster customer response time', 'faster customer response time'),
            'quote' => t('kouventa.Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement.', 'Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement.'),
            'name' => 'Yudhistira',
            'role' => t('kouventa.CEO of PakeDriver', 'CEO of PakeDriver')
        ],
        [
            'company' => 'Liberta Hotel',
            'stat' => '70%',
            'statLabel' => t('kouventa.time saved sending promotional broadcasts and confirmations', 'time saved sending promotional broadcasts and confirmations'),
            'stat2' => '+45%',
            'stat2Label' => t('kouventa.increase in guest response rates', 'increase in guest response rates'),
            'quote' => t('kouventa.Kouventa\'s WhatsApp automation streamlined our promotions and reservations. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.', 'Kouventa\'s WhatsApp automation streamlined our promotions and reservations. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.'),
            'name' => 'Novellinda Putri Kairo',
            'role' => 'Corporate Sales Admin, Liberta'
        ],
        [
            'company' => 'DPay',
            'stat' => '60%',
            'statLabel' => t('kouventa.fewer customer service tickets', 'fewer customer service tickets'),
            'stat2' => '+40%',
            'stat2Label' => t('kouventa.efficiency gain in operational lead time', 'efficiency gain in operational lead time'),
            'quote' => t('kouventa.Kouventa\'s WhatsApp automation solution revolutionized our logistics tracking and freed our team to focus on service innovation. Their flexible API and responsive support made integration seamless.', 'Kouventa\'s WhatsApp automation solution revolutionized our logistics tracking and freed our team to focus on service innovation. Their flexible API and responsive support made integration seamless.'),
            'name' => 'Sebastian Hadinata',
            'role' => t('kouventa.General Manager of DPay', 'General Manager of DPay')
        ]
    ];
@endphp


@section('content')
<div class="kv-canvas-wrap" x-data="{
    activeTeam: 0,
    activeStory: 0,
    teams: @js($teams),
    stories: @js($stories)
}">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative; overflow: hidden">
<div style="padding: 88px 80px 112px; display: flex; flex-direction: column; align-items: center; gap: 28px; position: relative">
<h1 style="margin: 0; font-size: 96px; line-height: 1.0; text-align: center; font-weight: 800; letter-spacing: -0.035em">AI adoption, <span style="color: #2BD46A">done right.</span></h1>
<p style="margin: 0; text-align: center; font-size: 24px; line-height: 1.5; color: #B9C6D3">For your customers. For your teams.<br>One enterprise AI platform, your organization’s rules.</p>
<div style="position: relative; width: 1080px; height: 500px; margin-top: 8px">
<span aria-hidden="true" style="position: absolute; left: 50%; bottom: 40px; width: 520px; height: 520px; transform: translateX(-50%); border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.30), rgba(43, 212, 106, 0) 66%)"></span>
<img src="{{ theme_asset('images/kouventa-mascot.png') }}" alt="Kouventa mascot, a penguin with a headset and visor" style="position: absolute; left: 50%; bottom: 0; transform: translateX(-50%); width: 420px; -webkit-mask-image: linear-gradient(to bottom, #000 78%, transparent 100%); mask-image: linear-gradient(to bottom, #000 78%, transparent 100%); height: auto; display: block; filter: drop-shadow(0 24px 40px rgba(0, 0, 0, 0.35))">
<div style="position: absolute; left: 60px; bottom: 70px; width: 272px; animation: kvBubIn .5s ease 1.1s both, kvBob 4.4s ease-in-out 1.6s infinite; box-sizing: border-box; padding: 12px 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 12px"><span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21a7 7 0 0 1 14 0"></path><path d="M16 11l2 2 4-4"></path></svg></span><span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">HUMAN APPROVAL</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35">Release vendor payment</span><span style="font-size: 17px; font-weight: 800; letter-spacing: -0.01em; white-space: nowrap">Rp 245.000.000</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 600; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>Matched to PO &amp; delivery note</span>
<span style="display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 1px solid #E3E8E6"><span style="width: 22px; height: 22px; border-radius: 50%; background: #E6F8EC; color: #13803F; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">AW</span><span style="font-size: 12px; font-weight: 600; color: #4A5866">Andi W. · CFO</span><span style="margin-left: auto; display: flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; font-size: 11px; font-weight: 800"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>Approved</span></span>
</div>
<div style="position: absolute; right: 70px; bottom: 80px; width: 256px; animation: kvBubIn .5s ease .65s both, kvBob 4.8s ease-in-out 1.2s infinite; box-sizing: border-box; padding: 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 10px"><span style="width: 32px; height: 32px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800">YB</span><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 800">Your Brand<svg width="14" height="14" viewBox="0 0 24 24" aria-label="Verified" style="flex-shrink: 0"><circle cx="12" cy="12" r="10" fill="#25D366"></circle><path d="M7.5 12.5l3 3 6-6.5" fill="none" stroke="#FFFFFF" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span style="font-size: 11px; color: #5B6B78">Official business account</span></span><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="margin-left: auto; width: 20px; height: 20px; display: block"></span>
<span style="align-self: flex-end; max-width: 220px; box-sizing: border-box; padding: 9px 11px 7px; border-radius: 12px 12px 4px 12px; background: #D9FDD3; display: flex; flex-direction: column; gap: 3px"><span style="font-size: 13px; font-weight: 600; line-height: 1.4">Hi Rina, your order is on its way. Track it here.</span><span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 11px; color: #5B6B78">10:24<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#3B9EE8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></span>
</div>
<div style="position: absolute; left: 110px; top: 100px; animation: kvBubIn .5s ease .4s both, kvBob 3.8s ease-in-out 1.3s infinite; box-sizing: border-box; padding: 12px 16px 12px 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; align-items: center; gap: 12px">
<span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 2px"><span style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F"><span style="width: 7px; height: 7px; border-radius: 50%; background: #2BD46A; animation: kvBlinkDot 1.2s ease-in-out infinite"></span>AI TASK FORCE · WORKING</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35; white-space: nowrap">Matching invoices to POs in ERP</span></span>
</div>
<div style="position: absolute; right: 60px; top: 70px; width: 268px; animation: kvBubIn .5s ease .2s both, kvBob 5s ease-in-out .7s infinite; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 9px">
<span style="display: flex; align-items: center; gap: 8px"><span style="width: 26px; height: 26px; border-radius: 8px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg></span><span style="font-size: 14px; font-weight: 800">Secure AI</span><span style="margin-left: auto; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">PII MASKED</span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">Name</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">Budi S.</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">B*** S.</span></span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">Phone</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">0812…789</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">••••789</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 700; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>Never used to train public AI</span>
</div>
</div>
<div style="display: flex; gap: 12px; padding-top: 8px">
<a href="#pick-team" @click="document.getElementById('pick-team')?.scrollIntoView({ behavior: 'smooth' })" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; display: flex; align-items: center; gap: 10px">See how it fits your business<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a>
</div>
</div>
</section>

<!-- ============ TWO PATHS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Where do you want to start?</h2>
<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px">
<a href="{{ url('/product/ai') }}" style="background: #071C33; border-radius: 24px; padding: 40px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF">
<span style="display: flex; align-items: center; gap: 14px">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"></path><path d="M9 21v-6h6v6M9 10h.01M15 10h.01"></path></svg></span>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #2BD46A">AI that works</span>
</span>
<span style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">Inside your <span style="color: #2BD46A">organization.</span></span>
<span style="font-size: 18px; line-height: 1.55; color: #B9C6D3">Instant answers from your company knowledge, and agents that take routine tasks off your teams.</span>
<span style="font-size: 16px; font-weight: 700; color: #2BD46A; padding-top: 8px">Explore more →</span>
</a>
<a href="{{ url('/product/customer-engagement') }}" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: flex; flex-direction: column; gap: 16px; color: #0E1A24">
<span style="display: flex; align-items: center; gap: 14px">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M8 11h.01M12 11h.01M16 11h.01"></path></svg></span>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">AI that engages</span>
</span>
<span style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">With your <span style="color: #13803F">customers.</span></span>
<span style="font-size: 18px; line-height: 1.55; color: #4A5866">Chatbots reply first on WhatsApp, Instagram, Facebook and Live Chat, then hand off to your agents when needed.</span>
<span style="font-size: 16px; font-weight: 700; color: #13803F; padding-top: 8px">Explore more →</span>
</a>
</div>
</section>

<!-- ============ INDUSTRIES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Proven across industries.</h2>
</div>
<div style="width: 100%; display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px">
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V10M19 21V10M9 21V10M15 21V10M2 10l10-6 10 6"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Government</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20M6 15h4"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">BFSI</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 9 12 4 2 9l10 5 10-5Z"></path><path d="M6 11v5c3 2 9 2 12 0v-5"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Education</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10Z"></path><path d="M12 10v4M10 12h4"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Healthcare</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h12l1 14H5L6 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Retail</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21V10l6 4V10l6 4V6h6v15H3Z"></path><path d="M7 17h2M13 17h2"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Manufacturing</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18V7M3 13h18v5M21 18v-5a3 3 0 0 0-3-3h-7v3"></path><circle cx="7" cy="10" r="2"></circle></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Hospitality</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 3v8a3 3 0 0 0 6 0V3M7 3v18M17 3c-2 0-3 2-3 5s1 4 3 4v9"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">F&amp;B</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"></path></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Services</span></div>
<div style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg></span><span style="font-size: 16px; font-weight: 700; color: #0E1A24">Transportation</span></div>
</div>
</div>
</section>

<!-- ============ WHO NEEDS THIS (tabs) ============ -->
<section id="pick-team" style="padding: 104px 80px; display: flex; flex-direction: column; gap: 32px; scroll-margin-top: 60px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Pick your team to see how it works for your organization.</h2>
<div role="tablist" aria-label="Teams" style="display: flex; gap: 8px; flex-wrap: wrap">

@foreach($teams as $i => $t)
    <button type="button" @click="activeTeam = {{ $i }}"
            :class="{ 'is-active': activeTeam === {{ $i }} }"
            class="kv-tab-team">
        {{ $t['name'] }}
    </button>
@endforeach

</div>
<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 48px; display: grid; grid-template-columns: minmax(0, 1fr) 480px; gap: 64px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 20px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F"><span x-text="teams[activeTeam].name"></span></span>
<span style="font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em"><span x-text="teams[activeTeam].line"></span></span>
<a href="{{ url('/solutions/ai') }}"  style="align-self: flex-start; font-size: 16px; font-weight: 700">See how it works →</a>
</div>
<div style="background: #F4F6F5; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px">
<span style="font-size: 13px; font-weight: 600; color: #4A5866; padding-bottom: 4px">Your agent handles</span>

<template x-for="(task, idx) in teams[activeTeam].tasks" :key="idx">

<div style="background: #FFFFFF; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; font-size: 16px; font-weight: 600">
<span style="width: 28px; height: 28px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span x-text="task"></span>
</div>

</template>

</div>
</div>
</section>

<!-- ============ WHY ============ -->
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Enterprise-ready <span style="color: #13803F">from day one.</span></h2>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; align-items: stretch">
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">Secure by design</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">Your business and customer data stays private. Personal data is masked to keep sensitive information protected, backed by ISO 27001 certified security practices.</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">ISO 27001</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Data masking</span></span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a4.5 4.5 0 0 1-.5-9A6 6 0 0 1 18 8a4 4 0 0 1-1 10H7Z"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">Deploy anywhere</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">Run Kouventa in the cloud or on-premise on your own servers, so it fits your IT policies, infrastructure and data residency needs.</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Cloud</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">On-premise</span></span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"></path><circle cx="16" cy="6" r="2"></circle><circle cx="10" cy="12" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">You're in control</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">Decide who can see and do what with role-based access, review every activity with a complete audit trail, and set custom rules so AI works within your policies.</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Role-based access</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Audit trail</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Custom rules</span></span>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">Local support</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">A local team that understands your business, from onboarding to daily operations, with support up to 24/7, available online and on-site.</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Up to 24/7</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">Online</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">On-site</span></span>
</div>
</div>
</section>

<!-- ============ TECHNOLOGY PARTNERS ============ -->
<section style="padding: 0 80px; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Supported by leading technology partners.</h2>
<div style="width: 100%; display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 16px">
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Meta</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">AWS</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Azure OpenAI</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Tokenku</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Deka LLM</span></div>
</div>
</div>
</section>

<!-- ============ CUSTOMERS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">They trusted us.</h2>
<div style="width: 100%; display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 16px">
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Maybank</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Bussan Auto Finance</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Mandiri Tunas Finance</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Sriwijaya Air</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Pelita Air</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Astra Otoparts</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Garuda Yamato Steel</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Sekolah Bunda Mulia</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Rumah Sakit Pluit</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">Luna's Doughnuts</span></div>
<div style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1.5px dashed #C9D2CE; background: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9AA7B3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path></svg><span style="font-size: 13px; font-weight: 700; color: #4A5866; text-align: center; line-height: 1.3">AKI GS</span></div>
</div>
</section>

<!-- ============ STORIES (carousel) ============ -->
<section style="padding: 72px 80px 104px; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; justify-content: space-between; align-items: end">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">Real results, in their words.</h2>
<div style="display: flex; gap: 8px">
<button type="button" aria-label="Previous story" @click="activeStory = (activeStory - 1 + stories.length) % stories.length" class="kv-slider-nav-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg></button>
<button type="button" aria-label="Next story" @click="activeStory = (activeStory + 1) % stories.length" class="kv-slider-nav-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg></button>
</div>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; display: grid; grid-template-columns: 420px minmax(0, 1fr); overflow: hidden">
<div style="background: #071C33; color: #FFFFFF; padding: 48px; display: flex; flex-direction: column; justify-content: space-between; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 88px; line-height: 1; font-weight: 800; letter-spacing: -0.04em; color: #2BD46A"><span x-text="stories[activeStory].stat"></span></span>
<span style="font-size: 18px; line-height: 1.4; color: #D5DEE8"><span x-text="stories[activeStory].statLabel"></span></span>
</div>
<div style="display: flex; flex-direction: column; gap: 6px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.14)">
<span style="font-size: 36px; font-weight: 800; letter-spacing: -0.02em"><span x-text="stories[activeStory].stat2"></span></span>
<span style="font-size: 15px; color: #B9C6D3"><span x-text="stories[activeStory].stat2Label"></span></span>
</div>
</div>
<div style="padding: 48px; display: flex; flex-direction: column; gap: 24px">
<img src="{{ theme_asset('images/kouventa-logo.png') }}" :alt="stories[activeStory].company" style="height: 48px; width: auto; align-self: flex-start">
<p style="margin: 0; font-size: 22px; line-height: 1.55; font-weight: 500">“<span x-text="stories[activeStory].quote"></span>”</p>
<div style="display: flex; flex-direction: column; gap: 2px; margin-top: auto">
<span style="font-size: 16px; font-weight: 700" x-text="stories[activeStory].name"></span>
<span style="font-size: 14px; color: #4A5866" x-text="stories[activeStory].role"></span>
</div>
</div>
</div>
<div style="display: flex; gap: 8px; justify-content: center; align-items: center">
@foreach($stories as $i => $s)
    <button type="button" @click="activeStory = {{ $i }}"
            class="kv-story-btn"
            aria-label="{{ t('kouventa.Show story:', 'Show story:') }} {{ $s['company'] }}"
            :aria-pressed="activeStory === {{ $i }}">
        <span class="kv-story-dot-pill" :class="{ 'is-active': activeStory === {{ $i }} }"></span>
    </button>
@endforeach
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 0 80px 104px">
<div style="background: #2BD46A; border-radius: 28px; padding: 72px 64px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 16px; color: #06251A">
<h2 style="margin: 0; font-size: 44px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">Ready to see it work for your business?</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55; max-width: 640px">Book a demo and we'll walk you through it with your own use case.</p>
<div style="display: flex; gap: 14px; justify-content: center; padding-top: 16px">
<a href="{{ url('/book-demo') }}" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap">Book a demo</a>
<a href="{{ url('/pricing') }}" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap">See pricing</a>
</div>
</div>
</section>


<!-- ============ FOOTER ============ -->
</div>
@endsection
