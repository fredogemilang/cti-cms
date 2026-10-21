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
            'name' => t('kouventa.Recruitment', 'Recruitment'),
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

    // ── Dynamic CPT: Testimonials ────────────────────────
    $cptTestimonials = \App\Models\CustomPostType::where('slug', 'testimonials')->where('is_active', true)->first();
    $stories = [];
    if ($cptTestimonials) {
        $cptEntries = \App\Models\CptEntry::where('post_type_id', $cptTestimonials->id)
            ->published()
            ->orderBy('menu_order')
            ->get();

        if ($cptEntries->isNotEmpty()) {
            $stories = $cptEntries->map(function ($entry) {
                return [
                    'company' => $entry->title,
                    'stat' => $entry->getMeta('stat', ''),
                    'statLabel' => $entry->getMeta('stat_label', ''),
                    'stat2' => $entry->getMeta('stat2', ''),
                    'stat2Label' => $entry->getMeta('stat2_label', ''),
                    'quote' => $entry->getMeta('quote', ''),
                    'name' => $entry->getMeta('client_name', ''),
                    'role' => $entry->getMeta('role', ''),
                    'logo' => $entry->featured_image ? resolve_block_asset($entry->featured_image) : null,
                ];
            })->all();
        }
    }

    // Graceful fallback if no CPT entries exist yet
    if (empty($stories)) {
        $stories = [
            [
                'company' => "Luna's Doughnuts",
                'stat' => '+55%',
                'statLabel' => t('kouventa.time saved in daily communication and coordination', 'time saved in daily communication and coordination'),
                'stat2' => '35%',
                'stat2Label' => t('kouventa.lower operational costs', 'lower operational costs'),
                'quote' => t('kouventa.Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we\'ve achieved faster customer responses and significant cost savings.', 'Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we\'ve achieved faster customer responses and significant cost savings.'),
                'name' => 'Caca Patricia',
                'role' => 'Partnership Specialist, PT Kaluna Indonesia Group',
                'logo' => null,
            ],
            [
                'company' => 'PakeDriver',
                'stat' => '+45%',
                'statLabel' => t('kouventa.improvement in customer engagement', 'improvement in customer engagement'),
                'stat2' => '30%',
                'stat2Label' => t('kouventa.faster customer response time', 'faster customer response time'),
                'quote' => t('kouventa.Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement.', 'Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement.'),
                'name' => 'Yudhistira',
                'role' => t('kouventa.CEO of PakeDriver', 'CEO of PakeDriver'),
                'logo' => null,
            ],
            [
                'company' => 'Liberta Hotel',
                'stat' => '70%',
                'statLabel' => t('kouventa.time saved sending promotional broadcasts and confirmations', 'time saved sending promotional broadcasts and confirmations'),
                'stat2' => '+45%',
                'stat2Label' => t('kouventa.increase in guest response rates', 'increase in guest response rates'),
                'quote' => t('kouventa.Kouventa\'s WhatsApp automation streamlined our promotions and reservations. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.', 'Kouventa\'s WhatsApp automation streamlined our promotions and reservations. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.'),
                'name' => 'Novellinda Putri Kairo',
                'role' => 'Corporate Sales Admin, Liberta',
                'logo' => null,
            ],
            [
                'company' => 'DPay',
                'stat' => '60%',
                'statLabel' => t('kouventa.fewer customer service tickets', 'fewer customer service tickets'),
                'stat2' => '+40%',
                'stat2Label' => t('kouventa.efficiency gain in operational lead time', 'efficiency gain in operational lead time'),
                'quote' => t('kouventa.Kouventa\'s WhatsApp automation solution revolutionized our logistics tracking and freed our team to focus on service innovation. Their flexible API and responsive support made integration seamless.', 'Kouventa\'s WhatsApp automation solution revolutionized our logistics tracking and freed our team to focus on service innovation. Their flexible API and responsive support made integration seamless.'),
                'name' => 'Sebastian Hadinata',
                'role' => t('kouventa.General Manager of DPay', 'General Manager of DPay'),
                'logo' => null,
            ],
        ];
    }

    // ── Page Blocks from CMS Content Builder ──────────────
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'AI adoption,', 'main' => 'done right.']);
    $heroSubtitle = $page?->block('hero_subtitle', "For your customers. For your teams.\nOne enterprise AI platform, your organization’s rules.");
    $heroCta = $page?->buttonBlock('hero_cta', ['text' => 'See how it fits your business', 'url' => '#pick-team']);
    $pathsTitle = $page?->titleBlock('paths_title', ['main' => 'Where do you want to start?']);
    $path1Badge = $page?->block('path1_badge', 'AI that works');
    $path1Title = $page?->titleBlock('path1_title', ['prefix' => 'Inside your', 'main' => 'organization.']);
    $path1Desc = $page?->block('path1_desc', 'Instant answers from your company knowledge, and agents that take routine tasks off your teams.');
    $path1Btn = $page?->buttonBlock('path1_button', ['text' => 'Explore more', 'url' => '/product/ai']);
    $path2Badge = $page?->block('path2_badge', 'AI that engages');
    $path2Title = $page?->titleBlock('path2_title', ['prefix' => 'With your', 'main' => 'customers.']);
    $path2Desc = $page?->block('path2_desc', 'Chatbots reply first on WhatsApp, Instagram, Facebook and Live Chat, then hand off to your agents when needed.');
    $path2Btn = $page?->buttonBlock('path2_button', ['text' => 'Explore more', 'url' => '/product/customer-engagement']);
    $industriesTitle = $page?->block('industries_title', 'Proven across industries.');
    $teamsTitle = $page?->block('teams_title', 'Pick your team to see how it works for your organization.');
    $whyTitle = $page?->titleBlock('why_title', ['prefix' => 'Enterprise ready', 'main' => 'from day one.']);
    $partnersTitle = $page?->block('partners_title', 'Technology Partners');
    $customersTitle = $page?->block('customers_title', 'They trusted us.');
    $resultsTitle = $page?->block('results_title', 'Real results, in their words.');
    $ctaTitle = $page?->block('cta_title', 'Ready to see it work for your business?');
    $ctaSubtitle = $page?->block('cta_subtitle', "Book a demo and we'll walk you through it with your own use case.");
    $ctaDemoBtn = $page?->buttonBlock('cta_demo_button', ['text' => 'Book a demo', 'url' => '/book-demo']);
    $ctaPricingBtn = $page?->buttonBlock('cta_pricing_button', ['text' => 'See pricing', 'url' => '/pricing']);
    $videosTitle = $page?->block('videos_title', 'See Kouventa in action.');
@endphp


@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative; overflow: hidden">
<div style="padding: 88px 80px 112px; display: flex; flex-direction: column; align-items: center; gap: 28px; position: relative">
<h1 style="margin: 0; font-size: 96px; line-height: 1.0; text-align: center; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'AI adoption,' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'done right.' }}</span></h1>
<p style="margin: 0; text-align: center; font-size: 24px; line-height: 1.5; color: #B9C6D3">{!! nl2br(e($heroSubtitle)) !!}</p>
<div style="position: relative; width: 1080px; height: 500px; margin-top: 8px">
<span aria-hidden="true" style="position: absolute; left: 50%; bottom: 40px; width: 520px; height: 520px; transform: translateX(-50%); border-radius: 50%; background: radial-gradient(circle, rgba(43, 212, 106, 0.30), rgba(43, 212, 106, 0) 66%)"></span>
<img src="{{ theme_asset('images/kouventa-mascot.png') }}" alt="{{ t('kouventa.Kouventa mascot, a penguin with a headset and visor', 'Kouventa mascot, a penguin with a headset and visor') }}" style="position: absolute; left: 50%; bottom: 0; transform: translateX(-50%); width: 420px; -webkit-mask-image: linear-gradient(to bottom, #000 78%, transparent 100%); mask-image: linear-gradient(to bottom, #000 78%, transparent 100%); height: auto; display: block; filter: drop-shadow(0 24px 40px rgba(0, 0, 0, 0.35))">
<div style="position: absolute; left: 60px; bottom: 70px; width: 272px; animation: kvBubIn .5s ease 1.1s both, kvBob 4.4s ease-in-out 1.6s infinite; box-sizing: border-box; padding: 12px 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 12px"><span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21a7 7 0 0 1 14 0"></path><path d="M16 11l2 2 4-4"></path></svg></span><span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.HUMAN APPROVAL', 'HUMAN APPROVAL') }}</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35">{{ t('kouventa.Release vendor payment', 'Release vendor payment') }}</span><span style="font-size: 17px; font-weight: 800; letter-spacing: -0.01em; white-space: nowrap">Rp 245.000.000</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 600; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>{{ t('kouventa.Matched to PO & delivery note', 'Matched to PO & delivery note') }}</span>
<span style="display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 1px solid #E3E8E6"><span style="width: 22px; height: 22px; border-radius: 50%; background: #E6F8EC; color: #13803F; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">AW</span><span style="font-size: 12px; font-weight: 600; color: #4A5866">Andi W. · CFO</span><span style="margin-left: auto; display: flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; font-size: 11px; font-weight: 800"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>{{ t('kouventa.Approved', 'Approved') }}</span></span>
</div>
<div style="position: absolute; right: 70px; bottom: 80px; width: 256px; animation: kvBubIn .5s ease .65s both, kvBob 4.8s ease-in-out 1.2s infinite; box-sizing: border-box; padding: 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 10px"><span style="width: 32px; height: 32px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800">YB</span><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 800">{{ t('kouventa.Your Brand', 'Your Brand') }}<svg width="14" height="14" viewBox="0 0 24 24" aria-label="Verified" style="flex-shrink: 0"><circle cx="12" cy="12" r="10" fill="#25D366"></circle><path d="M7.5 12.5l3 3 6-6.5" fill="none" stroke="#FFFFFF" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span style="font-size: 11px; color: #5B6B78">{{ t('kouventa.Official business account', 'Official business account') }}</span></span><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="margin-left: auto; width: 20px; height: 20px; display: block"></span>
<span style="align-self: flex-end; max-width: 220px; box-sizing: border-box; padding: 9px 11px 7px; border-radius: 12px 12px 4px 12px; background: #D9FDD3; display: flex; flex-direction: column; gap: 3px"><span style="font-size: 13px; font-weight: 600; line-height: 1.4">{{ t('kouventa.Hi Rina, your order is on its way. Track it here.', 'Hi Rina, your order is on its way. Track it here.') }}</span><span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 11px; color: #5B6B78">10:24<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#3B9EE8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Read"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></span>
</div>
<div style="position: absolute; left: 110px; top: 100px; animation: kvBubIn .5s ease .4s both, kvBob 3.8s ease-in-out 1.3s infinite; box-sizing: border-box; padding: 12px 16px 12px 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; align-items: center; gap: 12px">
<span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 2px"><span style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F"><span style="width: 7px; height: 7px; border-radius: 50%; background: #2BD46A; animation: kvBlinkDot 1.2s ease-in-out infinite"></span>{{ t('kouventa.AI TASK FORCE · WORKING', 'AI TASK FORCE · WORKING') }}</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35; white-space: nowrap">{{ t('kouventa.Matching invoices to POs in ERP', 'Matching invoices to POs in ERP') }}</span></span>
</div>
<div style="position: absolute; right: 60px; top: 70px; width: 268px; animation: kvBubIn .5s ease .2s both, kvBob 5s ease-in-out .7s infinite; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 9px">
<span style="display: flex; align-items: center; gap: 8px"><span style="width: 26px; height: 26px; border-radius: 8px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg></span><span style="font-size: 14px; font-weight: 800">{{ t('kouventa.Secure AI', 'Secure AI') }}</span><span style="margin-left: auto; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ t('kouventa.PII MASKED', 'PII MASKED') }}</span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">{{ t('kouventa.Name', 'Name') }}</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">Budi S.</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">B*** S.</span></span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">{{ t('kouventa.Phone', 'Phone') }}</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">0812…789</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">••••789</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 700; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>{{ t('kouventa.Never used to train public AI', 'Never used to train public AI') }}</span>
</div>
</div>
<div style="display: flex; gap: 12px; padding-top: 8px">
<a href="{{ $heroCta['url'] ?? '#pick-team' }}" onclick="if('{{ $heroCta['url'] ?? '#pick-team' }}' === '#pick-team'){document.getElementById('pick-team')?.scrollIntoView({ behavior: 'smooth' }); return false;}" class="kv-btn-action kv-btn-primary" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none">{{ $heroCta['text'] ?? 'See how it fits your business' }}<svg class="kv-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a>
</div>
</div>
</section>

<!-- ============ TWO PATHS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $pathsTitle['main'] ?? 'Where do you want to start?' }}</h2>
<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px">
<a href="{{ localized_url($path1Btn['url'] ?? '/product/ai') }}" class="kv-card kv-card-dark" style="background: #071C33; border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; display: flex; flex-direction: column; gap: 16px; color: #FFFFFF; text-decoration: none">
<span style="display: flex; align-items: center; gap: 14px">
<span style="width: 56px; height: 56px; border-radius: 16px; background: rgba(43, 212, 106, 0.16); color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"></path><path d="M9 21v-6h6v6M9 10h.01M15 10h.01"></path></svg></span>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #2BD46A">{{ $path1Badge }}</span>
</span>
<span style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ $path1Title['prefix'] ?? 'Inside your' }} <span style="color: #2BD46A">{{ $path1Title['main'] ?? 'organization.' }}</span></span>
<span style="font-size: 18px; line-height: 1.55; color: #B9C6D3">{{ $path1Desc }}</span>
<span class="kv-card-link" style="font-size: 16px; font-weight: 700; color: #2BD46A; padding-top: 8px">{{ $path1Btn['text'] ?? 'Explore more' }} <span class="kv-arrow">→</span></span>
</a>
<a href="{{ localized_url($path2Btn['url'] ?? '/product/customer-engagement') }}" class="kv-card kv-card-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: flex; flex-direction: column; gap: 16px; color: #0E1A24; text-decoration: none">
<span style="display: flex; align-items: center; gap: 14px">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #EAF7EF; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M8 11h.01M12 11h.01M16 11h.01"></path></svg></span>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F">{{ $path2Badge }}</span>
</span>
<span style="font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ $path2Title['prefix'] ?? 'With your' }} <span style="color: #13803F">{{ $path2Title['main'] ?? 'customers.' }}</span></span>
<span style="font-size: 18px; line-height: 1.55; color: #4A5866">{{ $path2Desc }}</span>
<span class="kv-card-link" style="font-size: 16px; font-weight: 700; color: #13803F; padding-top: 8px">{{ $path2Btn['text'] ?? 'Explore more' }} <span class="kv-arrow">→</span></span>
</a>
</div>
</section>

<!-- ============ INDUSTRIES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 10px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $industriesTitle }}</h2>
</div>
<div class="kv-industry-grid" style="width: 100%; display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px">
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V10M19 21V10M9 21V10M15 21V10M2 10l10-6 10 6"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Government', 'Government') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20M6 15h4"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.BFSI', 'BFSI') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 9 12 4 2 9l10 5 10-5Z"></path><path d="M6 11v5c3 2 9 2 12 0v-5"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Education', 'Education') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10Z"></path><path d="M12 10v4M10 12h4"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Healthcare', 'Healthcare') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h12l1 14H5L6 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Retail', 'Retail') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21V10l6 4V10l6 4V6h6v15H3Z"></path><path d="M7 17h2M13 17h2"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Manufacturing', 'Manufacturing') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18V7M3 13h18v5M21 18v-5a3 3 0 0 0-3-3h-7v3"></path><circle cx="7" cy="10" r="2"></circle></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Hospitality', 'Hospitality') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 3v8a3 3 0 0 0 6 0V3M7 3v18M17 3c-2 0-3 2-3 5s1 4 3 4v9"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.F&B', 'F&B') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"></path></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Services', 'Services') }}</span></div>
<div class="kv-industry-card" style="height: 76px; box-sizing: border-box; border-radius: 16px; border: 1px solid #E3E8E6; background: #FFFFFF; padding: 0 18px; display: flex; align-items: center; gap: 14px"><span class="kv-ind-icon" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #DDF3E4; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle></svg></span><span class="kv-ind-label" style="font-size: 16px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Transportation', 'Transportation') }}</span></div>
</div>
</div>
</section>

<!-- ============ WHO NEEDS THIS (tabs) ============ -->
<section id="pick-team" x-data="{
    activeTeam: 0,
    teams: @js($teams)
}" style="padding: 104px 80px; display: flex; flex-direction: column; gap: 32px; scroll-margin-top: 60px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $teamsTitle }}</h2>
<div role="tablist" aria-label="Teams" style="display: flex; gap: 8px; flex-wrap: wrap">

@foreach($teams as $i => $t)
    <button type="button" @click="activeTeam = {{ $i }}"
            :class="{ 'is-active': activeTeam === {{ $i }} }"
            class="kv-tab-team {{ $i === 0 ? 'is-active' : '' }}">
        {{ $t['name'] }}
    </button>
@endforeach

</div>
<div role="tabpanel" class="kv-panel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 48px; display: grid; grid-template-columns: minmax(0, 1fr) 480px; gap: 64px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 20px">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #13803F"><span x-text="teams[activeTeam].name">{{ $teams[0]['name'] ?? '' }}</span></span>
<span style="font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em"><span x-text="teams[activeTeam].line">{{ $teams[0]['line'] ?? '' }}</span></span>
<a href="{{ localized_url('/solutions/ai') }}" class="kv-card-link" style="align-self: flex-start; font-size: 16px; font-weight: 700; color: #13803F; text-decoration: none">{{ t('kouventa.See how it works', 'See how it works') }} <span class="kv-arrow">→</span></a>
</div>
<div style="background: #F4F6F5; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px; min-height: 290px">
<span style="font-size: 13px; font-weight: 600; color: #4A5866; padding-bottom: 4px">{{ t('kouventa.Your agent handles', 'Your agent handles') }}</span>

<template x-for="(task, idx) in teams[activeTeam].tasks" :key="idx">

<div style="background: #FFFFFF; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; font-size: 16px; font-weight: 600">
<span style="width: 28px; height: 28px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span x-text="task"></span>
</div>

</template>

<noscript>
@foreach(($teams[0]['tasks'] ?? []) as $task)
<div style="background: #FFFFFF; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; font-size: 16px; font-weight: 600">
<span style="width: 28px; height: 28px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ $task }}</span>
</div>
@endforeach
</noscript>

</div>
</div>
</section>

<!-- ============ WHY ============ -->
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $whyTitle['prefix'] ?? 'Enterprise ready' }} <span style="color: #13803F">{{ $whyTitle['main'] ?? 'from day one.' }}</span></h2>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; align-items: stretch">
<div class="kv-card kv-card-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">{{ t('kouventa.Secure by design', 'Secure by design') }}</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">{{ t('kouventa.Your business and customer data stays private. Personal data is masked to keep sensitive information protected, backed by ISO 27001 certified security practices.', 'Your business and customer data stays private. Personal data is masked to keep sensitive information protected, backed by ISO 27001 certified security practices.') }}</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.ISO 27001', 'ISO 27001') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Data masking', 'Data masking') }}</span></span>
</div>
<div class="kv-card kv-card-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a4.5 4.5 0 0 1-.5-9A6 6 0 0 1 18 8a4 4 0 0 1-1 10H7Z"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">{{ t('kouventa.Deploy anywhere', 'Deploy anywhere') }}</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">{{ t('kouventa.Run Kouventa in the cloud, on-premise on your own servers, or hybrid, so it fits your IT policies, infrastructure and data residency needs.', 'Run Kouventa in the cloud, on-premise on your own servers, or hybrid, so it fits your IT policies, infrastructure and data residency needs.') }}</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Cloud', 'Cloud') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.On-premise', 'On-premise') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Hybrid', 'Hybrid') }}</span></span>
</div>
<div class="kv-card kv-card-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"></path><circle cx="16" cy="6" r="2"></circle><circle cx="10" cy="12" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">{{ t('kouventa.You\'re in control', 'You\'re in control') }}</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">{{ t('kouventa.Decide who can see and do what with role-based access, review every activity with a complete audit trail, and set custom rules so AI works within your policies.', 'Decide who can see and do what with role-based access, review every activity with a complete audit trail, and set custom rules so AI works within your policies.') }}</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Role-based access', 'Role-based access') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Audit trail', 'Audit trail') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Custom rules', 'Custom rules') }}</span></span>
</div>
<div class="kv-card kv-card-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 10px 28px rgba(7, 28, 51, 0.05)">
<span style="width: 56px; height: 56px; border-radius: 16px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em; padding-top: 4px">{{ t('kouventa.Local support', 'Local support') }}</span>
<span style="font-size: 16px; color: #4A5866; line-height: 1.6; flex-grow: 1">{{ t('kouventa.A local team that understands your business, from onboarding to daily operations, with support up to 24/7, available online and on-site.', 'A local team that understands your business, from onboarding to daily operations, with support up to 24/7, available online and on-site.') }}</span>
<span style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px"><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Up to 24/7', 'Up to 24/7') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.Online', 'Online') }}</span><span style="font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C">{{ t('kouventa.On-site', 'On-site') }}</span></span>
</div>
</div>
</section>

<!-- ============ TECHNOLOGY PARTNERS ============ -->
<section style="padding: 0 80px; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $partnersTitle }}</h2>
<div class="kv-partner-grid" style="width: 100%; display: grid; gap: 16px">
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/meta-logo-white.png') }}" alt="Meta" title="Meta" style="max-height: 34px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/partner-aws-v2.svg') }}" alt="AWS" title="Amazon Web Services" style="max-height: 34px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/partner-azure.svg') . '?v=4' }}" alt="Azure OpenAI" title="Microsoft Azure" style="max-height: 34px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/partner-tokenku.png') }}" alt="TokenKu" title="TokenKu.ai" style="max-height: 34px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/partner-deka-llm.png') }}" alt="Deka LLM" title="Lintasarta Cloudeka" style="max-height: 34px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/partner-motadata-v2.svg') }}" alt="Motadata" title="Motadata" style="max-height: 30px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
</div>
</div>
</section>

<!-- ============ CUSTOMERS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $customersTitle }}</h2>
<div class="kv-customer-grid" style="width: 100%; display: grid; gap: 16px">
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-pakedriver.png') }}" alt="PakeDriver" title="PakeDriver" style="max-height: 36px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-lunas-doughnuts.png') }}" alt="Luna's Doughnuts" title="Luna's Doughnuts" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-astra-otoparts.png') }}" alt="Astra Otoparts" title="Astra Otoparts" style="max-height: 36px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-navios.png') }}" alt="Navios" title="Navios" style="max-height: 30px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-toqco.png') }}" alt="Toqco" title="Toqco" style="max-height: 36px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-mitra-mandiri.png') }}" alt="Mitra Mandiri Informatika" title="Mitra Mandiri Informatika" style="max-height: 38px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-liberta-hotels.png') }}" alt="Liberta Hotels" title="Liberta Hotels" style="max-height: 38px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
    <img src="{{ theme_asset('images/logos/customer-my-password.png') }}" alt="My Password" title="My Password" style="max-height: 28px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
</div>
</section>

<!-- ============ STORIES (carousel) ============ -->
<section style="padding: 80px 80px 64px; display: flex; flex-direction: column; gap: 32px" 
         x-data="{
             activeStory: 0,
             totalStories: {{ count($stories) }},
             timer: null,
             isPaused: false,
             touchStartX: 0,

             init() {
                 this.startAutoSlide();
             },
             startAutoSlide() {
                 this.clearTimer();
                 this.timer = setInterval(() => {
                     if (!this.isPaused) {
                         this.next();
                     }
                 }, 5000);
             },
             clearTimer() {
                 if (this.timer) {
                     clearInterval(this.timer);
                     this.timer = null;
                 }
             },
             resetAutoSlide() {
                 this.clearTimer();
                 this.startAutoSlide();
             },
             next() {
                 this.activeStory = (this.activeStory + 1) % this.totalStories;
             },
             prev() {
                 this.activeStory = (this.activeStory - 1 + this.totalStories) % this.totalStories;
             },
             goTo(index) {
                 this.activeStory = index;
                 this.resetAutoSlide();
             },
             pause() {
                 this.isPaused = true;
             },
             resume() {
                 this.isPaused = false;
             },
             handleTouchStart(e) {
                 this.pause();
                 if (e.changedTouches && e.changedTouches.length > 0) {
                     this.touchStartX = e.changedTouches[0].screenX;
                 }
             },
             handleTouchEnd(e) {
                 if (e.changedTouches && e.changedTouches.length > 0) {
                     const diff = this.touchStartX - e.changedTouches[0].screenX;
                     if (Math.abs(diff) > 40) {
                         if (diff > 0) {
                             this.next();
                         } else {
                             this.prev();
                         }
                     }
                 }
                 this.resume();
                 this.resetAutoSlide();
             }
         }">
<div style="display: flex; justify-content: space-between; align-items: end">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $resultsTitle }}</h2>
<div style="display: flex; gap: 8px">
<button type="button" aria-label="{{ t('kouventa.Previous story', 'Previous story') }}" @click="prev(); resetAutoSlide()" class="kv-slider-nav-btn kv-btn-action"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg></button>
<button type="button" aria-label="{{ t('kouventa.Next story', 'Next story') }}" @click="next(); resetAutoSlide()" class="kv-slider-nav-btn kv-btn-action"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg></button>
</div>
</div>

<div class="kv-panel kv-story-slider-viewport" 
     @mouseenter="pause()" 
     @mouseleave="resume()"
     @touchstart="handleTouchStart($event)"
     @touchend="handleTouchEnd($event)">
    <div class="kv-story-slider-track" 
         :style="{ transform: 'translateX(-' + (activeStory * 100) + '%)' }">
        @foreach($stories as $i => $story)
            <div class="kv-story-slide">
                <!-- Left stats panel (dark navy) -->
                <div class="kv-story-stats-panel">
                    <div style="display: flex; flex-direction: column; gap: 6px">
                        <span class="kv-story-stat-number">{{ $story['stat'] }}</span>
                        <span class="kv-story-stat-label">{{ $story['statLabel'] }}</span>
                    </div>
                    @if(!empty($story['stat2']))
                    <div style="display: flex; flex-direction: column; gap: 6px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.14)">
                        <span class="kv-story-stat2-number">{{ $story['stat2'] }}</span>
                        <span class="kv-story-stat2-label">{{ $story['stat2Label'] }}</span>
                    </div>
                    @endif
                </div>

                <!-- Right testimonial panel (white) -->
                <div class="kv-story-content-panel">
                    <img src="{{ !empty($story['logo']) ? $story['logo'] : theme_asset('images/kouventa-logo.png') }}" 
                         alt="{{ $story['company'] }}" 
                         class="kv-story-logo">
                    <p class="kv-story-quote">“{{ $story['quote'] }}”</p>
                    <div class="kv-story-author">
                        <span class="kv-story-author-name">{{ $story['name'] }}</span>
                        <span class="kv-story-author-role">{{ $story['role'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Indicator dots -->
<div style="display: flex; gap: 8px; justify-content: center; align-items: center">
@foreach($stories as $i => $s)
    <button type="button" @click="goTo({{ $i }})"
            class="kv-story-btn"
            aria-label="{{ t('kouventa.Show story:', 'Show story:') }} {{ $s['company'] }}"
            :aria-pressed="activeStory === {{ $i }}"
            aria-pressed="{{ $i === 0 ? 'true' : 'false' }}">
        <span class="kv-story-dot-pill {{ $i === 0 ? 'is-active' : '' }}" :class="{ 'is-active': activeStory === {{ $i }} }"></span>
    </button>
@endforeach
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 0 80px 104px">
<div class="kv-panel" style="background: #2BD46A; border-radius: 28px; padding: 72px 64px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 16px; color: #06251A">
<h2 style="margin: 0; font-size: 44px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">{{ $ctaTitle }}</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55; max-width: 640px">{!! nl2br(e($ctaSubtitle)) !!}</p>
<div style="display: flex; gap: 14px; justify-content: center; padding-top: 16px">
<a href="{{ localized_url($ctaDemoBtn['url'] ?? '/book-demo') }}" class="kv-btn-action kv-btn-navy" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap; text-decoration: none">{{ $ctaDemoBtn['text'] ?? 'Book a demo' }}</a>
<a href="{{ localized_url($ctaPricingBtn['url'] ?? '/pricing') }}" class="kv-btn-action kv-btn-outline" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap; text-decoration: none">{{ $ctaPricingBtn['text'] ?? 'See pricing' }}</a>
</div>
</div>
</section>

<!-- ============ VIDEOS ============ -->
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 32px">
    <div style="display: flex; justify-content: space-between; align-items: end; gap: 24px">
        <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $videosTitle }}</h2>
        <a href="https://www.youtube.com/@kouventa" target="_blank" rel="noopener noreferrer" class="kv-btn-action kv-btn-outline" style="display: inline-flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 700; color: #0E1A24; padding: 12px 18px; border-radius: 12px; border: 1px solid #C9D2CE; background: #FFFFFF; white-space: nowrap; text-decoration: none">
            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4" fill="#FF0033"></rect><path d="M10 9v6l5-3Z" fill="#FFFFFF"></path></svg>
            {{ t('kouventa.Watch more on YouTube', 'Watch more on YouTube') }}
        </a>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px">
        <a href="https://www.youtube.com/@kouventa" target="_blank" rel="noopener noreferrer" class="kv-video-card kv-card kv-hover" style="display: flex; flex-direction: column; gap: 14px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 14px 14px 20px; color: #0E1A24; text-decoration: none">
            <div role="img" aria-label="Video thumbnail placeholder" style="position: relative; aspect-ratio: 16 / 9; border-radius: 14px; background: #071C33; display: flex; align-items: center; justify-content: center; overflow: hidden">
                <span class="kv-play-btn" style="width: 64px; height: 44px; border-radius: 12px; background: #FF0033; display: flex; align-items: center; justify-content: center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#FFFFFF" aria-hidden="true"><path d="M8 5v14l11-7Z"></path></svg>
                </span>
            </div>
            <span style="align-self: flex-start; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C; margin: 0 6px">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
            <span style="font-size: 17px; line-height: 1.4; font-weight: 700; margin: 0 6px">{{ t('kouventa.AI Company Wikipedia: instant answers from your company knowledge', 'AI Company Wikipedia: instant answers from your company knowledge') }}</span>
        </a>
        <a href="https://www.youtube.com/@kouventa" target="_blank" rel="noopener noreferrer" class="kv-video-card kv-card kv-hover" style="display: flex; flex-direction: column; gap: 14px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 14px 14px 20px; color: #0E1A24; text-decoration: none">
            <div role="img" aria-label="Video thumbnail placeholder" style="position: relative; aspect-ratio: 16 / 9; border-radius: 14px; background: #071C33; display: flex; align-items: center; justify-content: center; overflow: hidden">
                <span class="kv-play-btn" style="width: 64px; height: 44px; border-radius: 12px; background: #FF0033; display: flex; align-items: center; justify-content: center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#FFFFFF" aria-hidden="true"><path d="M8 5v14l11-7Z"></path></svg>
                </span>
            </div>
            <span style="align-self: flex-start; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C; margin: 0 6px">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
            <span style="font-size: 17px; line-height: 1.4; font-weight: 700; margin: 0 6px">{{ t('kouventa.AI Task Force: automate invoice reconciliation with human approval', 'AI Task Force: automate invoice reconciliation with human approval') }}</span>
        </a>
        <a href="https://www.youtube.com/@kouventa" target="_blank" rel="noopener noreferrer" class="kv-video-card kv-card kv-hover" style="display: flex; flex-direction: column; gap: 14px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 14px 14px 20px; color: #0E1A24; text-decoration: none">
            <div role="img" aria-label="Video thumbnail placeholder" style="position: relative; aspect-ratio: 16 / 9; border-radius: 14px; background: #071C33; display: flex; align-items: center; justify-content: center; overflow: hidden">
                <span class="kv-play-btn" style="width: 64px; height: 44px; border-radius: 12px; background: #FF0033; display: flex; align-items: center; justify-content: center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#FFFFFF" aria-hidden="true"><path d="M8 5v14l11-7Z"></path></svg>
                </span>
            </div>
            <span style="align-self: flex-start; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #EAF7EF; color: #0B5E2C; margin: 0 6px">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
            <span style="font-size: 17px; line-height: 1.4; font-weight: 700; margin: 0 6px">{{ t('kouventa.Omnichannel inbox: from Instagram DM to WhatsApp campaign', 'Omnichannel inbox: from Instagram DM to WhatsApp campaign') }}</span>
        </a>
    </div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
