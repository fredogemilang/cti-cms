@extends('kouventa::layouts.app')


@php
    $isIdLocale = app()->getLocale() === 'id';
    $teams = [
        [
            'name' => t('kouventa.Customer Service', 'Customer Service'),
            'line' => t('kouventa.Resolve routine inquiries, route the rest.', 'Resolve routine inquiries, route the rest.'),
            'tab' => 0,
            'url' => localized_url('/solutions/ai?dept=0#solution-tabs'),
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
            'url' => localized_url('/solutions/ai?dept=1#solution-tabs'),
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
            'url' => localized_url('/solutions/ai?dept=2#solution-tabs'),
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
            'url' => localized_url('/solutions/ai?dept=3#solution-tabs'),
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
            'url' => localized_url('/solutions/ai?dept=4#solution-tabs'),
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
            'url' => localized_url('/solutions/ai?dept=6#solution-tabs'),
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
        $isIdLocale = app()->getLocale() === 'id';
        $stories = [
            [
                'company' => 'DPay',
                'stat' => '+60%',
                'statLabel' => t('kouventa.reduction in customer service tickets.', 'reduction in customer service tickets.'),
                'stat2' => '+40%',
                'stat2Label' => t('kouventa.efficiency gain in operational lead time.', 'efficiency gain in operational lead time.'),
                'quote' => $isIdLocale
                    ? 'Solusi otomasi WhatsApp dari Kouventa merevolusi pelacakan logistik kami, memangkas pertanyaan pelanggan hingga 60%, dan memberi keleluasaan bagi tim kami untuk fokus pada inovasi layanan. API yang fleksibel serta dukungan yang responsif membuat proses integrasi berjalan mulus.'
                    : 'Kouventa’s WhatsApp automation solution revolutionized our logistics tracking, cutting customer inquiries by 60% and freeing our team to focus on service innovation. Their flexible API and responsive support made integration seamless.',
                'name' => 'Sebastian Hadinata',
                'role' => t('kouventa.General Manager of DPay', 'General Manager of DPay'),
                'logo' => theme_asset('images/logos/logo-dpay.png'),
            ],
            [
                'company' => 'Liberta Hotel',
                'stat' => '+70%',
                'statLabel' => t('kouventa.time saved in sending promotional broadcasts & confirmations.', 'time saved in sending promotional broadcasts & confirmations.'),
                'stat2' => '+45%',
                'stat2Label' => t('kouventa.increase in guest response rates.', 'increase in guest response rates.'),
                'quote' => $isIdLocale
                    ? 'Otomasi WhatsApp dari Kouventa menyederhanakan alur promosi dan reservasi kami, menghemat 70% waktu tim sekaligus meningkatkan tingkat respons tamu hingga 45%. Platform yang intuitif dan dukungan yang berdedikasi menjadikan mereka mitra sejati dalam bisnis perhotelan kami.'
                    : 'Kouventa’s WhatsApp automation streamlined our promotions and reservations, saving 70% of our team’s time while boosting guest response rates by 45%. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.',
                'name' => 'Novellinda Putri Kairo',
                'role' => t('kouventa.Corporate Sales Admin at Liberta', 'Corporate Sales Admin at Liberta'),
                'logo' => theme_asset('images/logos/customer-liberta-hotels.png'),
            ],
            [
                'company' => 'PakeDriver',
                'stat' => '+45%',
                'statLabel' => t('kouventa.improvement in customer engagement', 'improvement in customer engagement'),
                'stat2' => '+30%',
                'stat2Label' => t('kouventa.faster customer response time', 'faster customer response time'),
                'quote' => $isIdLocale
                    ? 'Kouventa memudahkan kami berinteraksi langsung dengan pelanggan secara mudah, mulai dari pembaruan layanan hingga penawaran promo. Fitur AI yang intuitif memperkuat citra profesional kami serta mendongkrak interaksi dengan menciptakan pengalaman pelanggan yang lebih cerdas dan terhubung.'
                    : 'Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement by creating a smarter, more connected customer experience.',
                'name' => 'Yudhistira',
                'role' => t('kouventa.CEO of PakeDriver', 'CEO of PakeDriver'),
                'logo' => theme_asset('images/logos/customer-pakedriver.png'),
            ],
            [
                'company' => "Luna's Doughnuts",
                'stat' => '+55%',
                'statLabel' => t('kouventa.time savings in daily communication and coordination', 'time savings in daily communication and coordination'),
                'stat2' => '+35%',
                'stat2Label' => t('kouventa.reduction in operational costs', 'reduction in operational costs'),
                'quote' => $isIdLocale
                    ? 'Kouventa menghadirkan solusi komunikasi yang efisien dan dapat disesuaikan sesuai kebutuhan bisnis kami. Sejak menggunakannya, kami meraih waktu respons pelanggan yang lebih cepat serta penghematan biaya yang signifikan.'
                    : 'Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we\'ve achieved faster customer responses and significant cost savings.',
                'name' => 'Caca Patricia',
                'role' => t('kouventa.Partnership Specialist, PT Kaluna Indonesia Group', 'Partnership Specialist, PT Kaluna Indonesia Group'),
                'logo' => theme_asset('images/logos/customer-lunas-doughnuts.png'),
            ],
        ];
    }

    // ── Page Blocks from CMS Content Builder ──────────────
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'AI adoption,', 'main' => 'done right.']);
    $heroSubtitle = $page?->block('hero_subtitle', $isIdLocale
        ? "Untuk pelanggan Anda. Untuk tim Anda.\nSatu platform AI enterprise, dengan aturan organisasi Anda."
        : "For your customers. For your teams.\nOne enterprise AI platform, your organization’s rules.");
    if ($isIdLocale && (str_contains($heroSubtitle, 'For your customers') || empty($heroSubtitle))) {
        $heroSubtitle = "Untuk pelanggan Anda. Untuk tim Anda.\nSatu platform AI enterprise, dengan aturan organisasi Anda.";
    }
    if ($heroSubtitle) {
        $heroSubtitle = str_replace(
            ['For your customers. For your teams. ', 'Untuk pelanggan Anda. Untuk tim Anda. '],
            ["For your customers. For your teams.\n", "Untuk pelanggan Anda. Untuk tim Anda.\n"],
            $heroSubtitle
        );
    }
    $heroCta = $page?->buttonBlock('hero_cta', ['text' => 'See how it fits your business', 'url' => '#pick-team']);
    $pathsTitle = $page?->titleBlock('paths_title', ['main' => $isIdLocale ? 'Mau mulai dari mana?' : 'Where do you want to start?']);
    $path1Badge = $page?->block('path1_badge', $isIdLocale ? 'AI yang bekerja' : 'AI that works');
    $path1Title = $page?->titleBlock('path1_title', [
        'prefix' => $isIdLocale ? 'Di dalam' : 'Inside your',
        'main' => $isIdLocale ? 'organisasi Anda.' : 'organization.'
    ]);
    $path1Desc = $page?->block('path1_desc', $isIdLocale
        ? 'Jawaban instan dari pengetahuan perusahaan Anda, dan agent yang mengambil alih tugas rutin tim Anda.'
        : 'Instant answers from your company knowledge, and agents that take routine tasks off your teams.');
    $path1Btn = $page?->buttonBlock('path1_button', [
        'text' => $isIdLocale ? 'Pelajari lebih lanjut' : 'Explore more',
        'url' => '/product/ai'
    ]);
    $path2Badge = $page?->block('path2_badge', $isIdLocale ? 'AI yang berinteraksi' : 'AI that engages');
    $path2Title = $page?->titleBlock('path2_title', [
        'prefix' => $isIdLocale ? 'Bersama' : 'With your',
        'main' => $isIdLocale ? 'pelanggan Anda.' : 'customers.'
    ]);
    $path2Desc = $page?->block('path2_desc', $isIdLocale
        ? 'Chatbot membalas lebih dulu di WhatsApp, Instagram, Facebook, dan Live Chat, lalu meneruskan ke agen Anda saat diperlukan.'
        : 'Chatbots reply first on WhatsApp, Instagram, Facebook and Live Chat, then hand off to your agents when needed.');
    $path2Btn = $page?->buttonBlock('path2_button', [
        'text' => $isIdLocale ? 'Pelajari lebih lanjut' : 'Explore more',
        'url' => '/product/customer-engagement'
    ]);
    $industriesTitle = $page?->block('industries_title', $isIdLocale ? 'Terbukti di berbagai industri.' : 'Proven across industries.');
    $teamsTitle = $page?->block('teams_title', $isIdLocale ? 'Pilih tim Anda untuk melihat cara kerjanya di organisasi Anda.' : 'Pick your team to see how it works for your organization.');
    $seeHowItWorksRaw = t('kouventa.See how it works →', 'See how it works →');
    $seeHowItWorksHasArrow = str_ends_with($seeHowItWorksRaw, '→') || str_ends_with($seeHowItWorksRaw, '->');
    $seeHowItWorksText = $seeHowItWorksHasArrow ? rtrim(str_replace(['→', '->'], '', $seeHowItWorksRaw)) : $seeHowItWorksRaw;
    $whyTitle = $page?->titleBlock('why_title', [
        'prefix' => $isIdLocale ? 'Siap untuk skala enterprise' : 'Enterprise ready',
        'main' => $isIdLocale ? 'sejak hari pertama.' : 'from day one.'
    ]);
    $partnersTitle = $page?->block('partners_title', 'Technology Partners');
    $customersTitle = $page?->block('customers_title', $isIdLocale ? 'Mereka sudah percaya.' : 'They trusted us.');
    if ($isIdLocale && ($customersTitle === 'They trusted us.' || $customersTitle === 'Mereka mempercayai kami.' || empty($customersTitle))) {
        $customersTitle = 'Mereka sudah percaya.';
    }
    $resultsTitle = $page?->block('results_title', $isIdLocale ? 'Hasil nyata, dari cerita mereka.' : 'Real results, in their words.');
    $ctaTitle = $page?->block('cta_title', $isIdLocale ? 'Siap lihat langsung hasilnya untuk bisnis Anda?' : 'Ready to see it work for your business?');
    if ($isIdLocale && ($ctaTitle === 'Ready to see it work for your business?' || $ctaTitle === 'Siap mengubah cara bisnis Anda beroperasi dengan AI?' || empty($ctaTitle))) {
        $ctaTitle = 'Siap lihat langsung hasilnya untuk bisnis Anda?';
    }
    $ctaSubtitle = $page?->block('cta_subtitle', $isIdLocale ? 'Jadwalkan demo dan kami tunjukkan langsung dengan use case Anda sendiri.' : "Book a demo and we'll walk you through it with your own use case.");
    $ctaDemoBtn = $page?->buttonBlock('cta_demo_button', ['text' => $isIdLocale ? 'Jadwalkan demo' : 'Book a demo', 'url' => '/book-demo']);
    $ctaPricingBtn = $page?->buttonBlock('cta_pricing_button', ['text' => $isIdLocale ? 'Lihat harga' : 'See pricing', 'url' => '/pricing']);
    $videosTitle = $page?->block('videos_title', $isIdLocale ? 'Lihat cara kerja Kouventa.' : 'See Kouventa in action.');
    $videos = [
        [
            'id' => 'oOY1BnbQPUc',
            'url' => 'https://www.youtube.com/watch?v=oOY1BnbQPUc',
            'title' => 'Tim Finance Masih Perlu Workflow Manual di Era Agentic AI?',
            'category' => 'Artificial Intelligence',
        ],
        [
            'id' => 'N76UFyc7v9s',
            'url' => 'https://www.youtube.com/watch?v=N76UFyc7v9s',
            'title' => 'Kouventa Agentic AI | A New Generation of AI for Business',
            'category' => 'Artificial Intelligence',
        ],
        [
            'id' => '6_6n4erRFXQ',
            'url' => 'https://www.youtube.com/watch?v=6_6n4erRFXQ',
            'title' => 'Cara Mendapatkan Centang Biru di WhatsApp untuk Akun Bisnis!',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => 'w1OtL8yThnc',
            'url' => 'https://www.youtube.com/watch?v=w1OtL8yThnc',
            'title' => 'Stop Kirim Pesan Manual! Ini Cara Upgrade CRM Otomatis Tanpa Ribet',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => '9lcV7QXol_g',
            'url' => 'https://www.youtube.com/watch?v=9lcV7QXol_g',
            'title' => '5 MASALAH CUSTOMER SERVICE WHATSAPP | Live Chat Agent',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => 'O0E_XfPl5CE',
            'url' => 'https://www.youtube.com/watch?v=O0E_XfPl5CE',
            'title' => 'Evolusi Cara Bisnis Melayani Pelanggan: Kenapa Omnichannel Jadi Penting?',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => '1xvLiyz3zJk',
            'url' => 'https://www.youtube.com/watch?v=1xvLiyz3zJk',
            'title' => 'RAHASIA CS NGGAK KEWALAHAN SAAT ACARA KANTOR!',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => 'DbQ0ZBY_XSs',
            'url' => 'https://www.youtube.com/watch?v=DbQ0ZBY_XSs',
            'title' => 'Tim HR/IT Pusing Ticketing? Ini Cara Tracking Tanpa Ribet!',
            'category' => 'Artificial Intelligence',
        ],
        [
            'id' => '21WZzU5uou4',
            'url' => 'https://www.youtube.com/watch?v=21WZzU5uou4',
            'title' => 'STOP Pakai WhatsApp Biasa untuk Bisnis! Ini Alasannya',
            'category' => 'Customer Engagement',
        ],
        [
            'id' => 'UbcQOWUxJHI',
            'url' => 'https://www.youtube.com/watch?v=UbcQOWUxJHI',
            'title' => 'JANGAN SEMBARANGAN INPUT DATA KE AI! Data Kantormu Bisa Bocor',
            'category' => 'Artificial Intelligence',
        ],
    ];
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
<span style="display: flex; align-items: center; gap: 12px"><span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21a7 7 0 0 1 14 0"></path><path d="M16 11l2 2 4-4"></path></svg></span><span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ $isIdLocale ? 'PERSETUJUAN MANUSIA' : t('kouventa.HUMAN APPROVAL', 'HUMAN APPROVAL') }}</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35">{{ $isIdLocale ? 'Cairkan pembayaran vendor' : t('kouventa.Release vendor payment', 'Release vendor payment') }}</span><span style="font-size: 17px; font-weight: 800; letter-spacing: -0.01em; white-space: nowrap">Rp 245.000.000</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 600; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span>{{ $isIdLocale ? 'Cocok dengan PO & surat jalan' : t('kouventa.Matched to PO & delivery note', 'Matched to PO & delivery note') }}</span>
<span style="display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 1px solid #E3E8E6"><span style="width: 22px; height: 22px; border-radius: 50%; background: #E6F8EC; color: #13803F; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">AW</span><span style="font-size: 12px; font-weight: 600; color: #4A5866">Andi W. · CFO</span><span style="margin-left: auto; display: flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; background: #13803F; color: #FFFFFF; font-size: 11px; font-weight: 800"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>{{ $isIdLocale ? 'Disetujui' : t('kouventa.Approved', 'Approved') }}</span></span>
</div>
<div style="position: absolute; right: 70px; bottom: 80px; width: 256px; animation: kvBubIn .5s ease .65s both, kvBob 4.8s ease-in-out 1.2s infinite; box-sizing: border-box; padding: 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 10px">
<span style="display: flex; align-items: center; gap: 10px"><span style="width: 32px; height: 32px; flex-shrink: 0; border-radius: 50%; background: #13803F; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800">YB</span><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span style="display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 800">{{ t('kouventa.Your Brand', 'Your Brand') }}<svg width="14" height="14" viewBox="0 0 24 24" aria-label="{{ t('kouventa.Verified', 'Verified') }}" style="flex-shrink: 0"><circle cx="12" cy="12" r="10" fill="#25D366"></circle><path d="M7.5 12.5l3 3 6-6.5" fill="none" stroke="#FFFFFF" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span><span style="font-size: 11px; color: #5B6B78">{{ $isIdLocale ? 'Akun bisnis resmi' : t('kouventa.Official business account', 'Official business account') }}</span></span><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="margin-left: auto; width: 20px; height: 20px; display: block"></span>
<span style="align-self: flex-end; max-width: 220px; box-sizing: border-box; padding: 9px 11px 7px; border-radius: 12px 12px 4px 12px; background: #D9FDD3; display: flex; flex-direction: column; gap: 3px"><span style="font-size: 13px; font-weight: 600; line-height: 1.4">{{ $isIdLocale ? 'Halo Rina, pesanan Anda sedang dikirim. Lacak di sini.' : t('kouventa.Hi Rina, your order is on its way. Track it here.', 'Hi Rina, your order is on its way. Track it here.') }}</span><span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 11px; color: #5B6B78">10:24<svg width="16" height="11" viewBox="0 0 16 11" fill="none" stroke="#3B9EE8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="{{ t('kouventa.Read', 'Read') }}"><path d="M1 6l3 3 6-7"></path><path d="M6 9l1 1 6-8"></path></svg></span></span>
</div>
<div style="position: absolute; left: 110px; top: 100px; animation: kvBubIn .5s ease .4s both, kvBob 3.8s ease-in-out 1.3s infinite; box-sizing: border-box; padding: 12px 16px 12px 12px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; align-items: center; gap: 12px">
<span style="width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path></svg></span>
<span style="display: flex; flex-direction: column; gap: 2px"><span style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F"><span style="width: 7px; height: 7px; border-radius: 50%; background: #2BD46A; animation: kvBlinkDot 1.2s ease-in-out infinite"></span>{{ $isIdLocale ? 'AI TASK FORCE · BERJALAN' : t('kouventa.AI TASK FORCE · WORKING', 'AI TASK FORCE · WORKING') }}</span><span style="font-size: 14px; font-weight: 700; line-height: 1.35; white-space: nowrap">{{ $isIdLocale ? 'Mencocokkan invoice dengan PO di ERP' : t('kouventa.Matching invoices to POs in ERP', 'Matching invoices to POs in ERP') }}</span></span>
</div>
<div style="position: absolute; right: 60px; top: 70px; width: 268px; animation: kvBubIn .5s ease .2s both, kvBob 5s ease-in-out .7s infinite; box-sizing: border-box; padding: 14px; border-radius: 16px; background: #FFFFFF; color: #0E1A24; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 9px">
<span style="display: flex; align-items: center; gap: 8px"><span style="width: 26px; height: 26px; border-radius: 8px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M8.5 12l2.5 2.5 4.5-5"></path></svg></span><span style="font-size: 14px; font-weight: 800">{{ $isIdLocale ? 'AI yang aman' : t('kouventa.Secure AI', 'Secure AI') }}</span><span style="margin-left: auto; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; color: #13803F">{{ $isIdLocale ? 'PII DISAMARKAN' : t('kouventa.PII MASKED', 'PII MASKED') }}</span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">{{ $isIdLocale ? 'Nama' : t('kouventa.Name', 'Name') }}</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">Budi S.</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">B*** S.</span></span></span>
<span style="display: grid; grid-template-columns: 50px 1fr; align-items: center; gap: 6px; font-size: 12px"><span style="color: #6B7A88; font-weight: 600">{{ $isIdLocale ? 'Telepon' : t('kouventa.Phone', 'Phone') }}</span><span style="display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 600"><span style="color: #9AA7B3; text-decoration: line-through">0812…789</span><span style="color: #13803F">→</span><span style="background: #DDF3E4; color: #0B5E2C; padding: 2px 6px; border-radius: 5px">••••789</span></span></span>
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 9px; border-radius: 8px; background: #F4F6F5; font-size: 12px; font-weight: 700; color: #2B3947"><span style="color: #13803F; display: flex"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>{{ $isIdLocale ? 'Tidak dipakai untuk melatih AI publik' : t('kouventa.Never used to train public AI', 'Never used to train public AI') }}</span>
</div>
</div>
<div style="display: flex; gap: 12px; padding-top: 8px">
<a href="{{ $heroCta['url'] ?? '#pick-team' }}" onclick="if('{{ $heroCta['url'] ?? '#pick-team' }}' === '#pick-team'){document.getElementById('pick-team')?.scrollIntoView({ behavior: 'smooth' }); return false;}" class="kv-btn-action kv-btn-primary" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none">{{ $heroCta['text'] ?? 'See how it fits your business' }}<svg class="kv-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a>
</div>
</div>
</section>

<!-- ============ TWO PATHS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
@php
    $pathsMain = $pathsTitle['main'] ?? ($isIdLocale ? 'Mau mulai dari mana?' : 'Where do you want to start?');
    if ($isIdLocale && ($pathsMain === 'Where do you want to start?' || $pathsMain === 'Di mana Anda ingin memulai?' || empty($pathsMain))) {
        $pathsMain = 'Mau mulai dari mana?';
    }
@endphp
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $pathsMain }}</h2>
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
<a :href="teams[activeTeam].url" href="{{ localized_url('/solutions/ai?dept=0#solution-tabs') }}" class="kv-card-link" style="align-self: flex-start; font-size: 16px; font-weight: 700; color: #13803F; text-decoration: none">{{ $seeHowItWorksText }}@if($seeHowItWorksHasArrow) <span class="kv-arrow">→</span>@endif</a>
</div>
<div style="background: #F4F6F5; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px; min-height: 290px">
<span style="font-size: 13px; font-weight: 600; color: #4A5866; padding-bottom: 4px">{{ t('kouventa.Your AI agent handles', 'Your AI agent handles') }}</span>

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
@php
    $whyPrefix = $whyTitle['prefix'] ?? ($isIdLocale ? 'Siap untuk skala enterprise' : 'Enterprise ready');
    if ($isIdLocale && ($whyPrefix === 'Enterprise ready' || empty($whyPrefix))) {
        $whyPrefix = 'Siap untuk skala enterprise';
    } else {
        $whyPrefix = t('kouventa.' . $whyPrefix, $whyPrefix);
    }

    $whyMain = $whyTitle['main'] ?? ($isIdLocale ? 'sejak hari pertama.' : 'from day one.');
    if ($isIdLocale && ($whyMain === 'from day one.' || empty($whyMain))) {
        $whyMain = 'sejak hari pertama.';
    } else {
        $whyMain = t('kouventa.' . $whyMain, $whyMain);
    }
@endphp
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $whyPrefix }} <span style="color: #13803F">{{ $whyMain }}</span></h2>
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
    <img src="{{ theme_asset('images/logos/techonology-partners/meta.webp') }}" alt="Meta" title="Meta" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/techonology-partners/aws.webp') }}" alt="AWS" title="Amazon Web Services" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/techonology-partners/microsoft-foundry.webp') . '?v=2' }}" alt="Microsoft Foundry" title="Microsoft Foundry" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/techonology-partners/deka-llm.webp') }}" alt="Lintasarta Cloudeka" title="Lintasarta Cloudeka" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/techonology-partners/motadata.webp') }}" alt="Motadata" title="Motadata" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
<div class="kv-logo-card kv-partner-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.12); background: #071C33; display: flex; align-items: center; justify-content: center; padding: 14px 18px; box-shadow: 0 4px 16px rgba(7, 28, 51, 0.12)">
    <img src="{{ theme_asset('images/logos/techonology-partners/tokeknku.webp') }}" alt="TokenKu" title="TokenKu.ai" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
</div>
</div>
</div>
</section>

<!-- ============ CUSTOMERS ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $customersTitle }}</h2>
<style>
    .kv-customer-grid {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }
    @media (max-width: 1200px) {
        .kv-customer-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }
    }
    @media (max-width: 1024px) {
        .kv-customer-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }
    }
    @media (max-width: 640px) {
        .kv-customer-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px 10px !important;
        }
    }
    .kv-more-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 26px -6px rgba(7, 28, 51, 0.09) !important;
        border-color: #2BD46A !important;
    }
</style>
<div class="kv-customer-grid" style="width: 100%; display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 16px">
    <!-- Row 1 (from mockup) -->
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-maybank.svg') }}" alt="Maybank" title="Maybank" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-baf.png') }}" alt="Bussan Auto Finance" title="Bussan Auto Finance" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-mtf.png') }}" alt="Mandiri Tunas Finance" title="Mandiri Tunas Finance" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-sriwijaya-air.png') }}" alt="Sriwijaya Air" title="Sriwijaya Air" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-pelita-air.png') }}" alt="Pelita Air" title="Pelita Air" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-astra-otoparts.png') }}" alt="Astra Otoparts" title="Astra Otoparts" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>

    <!-- Row 2 (from mockup) -->
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-garuda-yamato-steel.webp') }}" alt="Garuda Yamato Steel" title="Garuda Yamato Steel" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-sbm.svg') }}" alt="Sekolah Bunda Mulia" title="Sekolah Bunda Mulia" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-rs-pluit.png') }}" alt="RS Pluit" title="Rumah Sakit Pluit" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-lunas-doughnuts.png') }}" alt="Luna's Doughnuts" title="Luna's Doughnuts" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>
    <div class="kv-logo-card kv-customer-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #E3E8E6; background: #FFFFFF; display: flex; align-items: center; justify-content: center; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02)">
        <img src="{{ theme_asset('images/logos/customer-aki-gs.png') }}" alt="AKI GS" title="AKI GS" style="max-height: 44px; max-width: 100%; width: auto; height: auto; object-fit: contain">
    </div>

    <!-- 12th card: + And many more -->
    <div class="kv-logo-card kv-customer-card kv-more-card" style="width: 100%; height: 88px; box-sizing: border-box; border-radius: 12px; border: 1px solid #D4EFE0; background: #EAF7EF; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; box-shadow: 0 2px 8px rgba(7, 28, 51, 0.02); color: #0B5E2C; transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1); cursor: default">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span style="font-size: 16px; font-weight: 700; letter-spacing: -0.01em; white-space: nowrap">
            {{ t('kouventa.And many more', 'And many more') }}
        </span>
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
<section 
    x-data="{
        activeVideo: null,
        canPrev: false,
        canNext: true,
        scrollPrev() {
            const el = this.$refs.slider;
            if (!el) return;
            const card = el.querySelector('.kv-video-card');
            const step = card ? (card.offsetWidth + 20) * 3 : (el.clientWidth || 900);
            el.scrollBy({ left: -step, behavior: 'smooth' });
        },
        scrollNext() {
            const el = this.$refs.slider;
            if (!el) return;
            const card = el.querySelector('.kv-video-card');
            const step = card ? (card.offsetWidth + 20) * 3 : (el.clientWidth || 900);
            el.scrollBy({ left: step, behavior: 'smooth' });
        },
        checkScroll() {
            const el = this.$refs.slider;
            if (!el) return;
            this.canPrev = el.scrollLeft > 10;
            this.canNext = el.scrollLeft < (el.scrollWidth - el.clientWidth - 15);
        }
    }"
    x-init="setTimeout(() => checkScroll(), 100); window.addEventListener('resize', () => checkScroll())"
    style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 32px"
>
    <style>
        .kv-video-slider {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 20px !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scroll-snap-type: x mandatory !important;
            scroll-behavior: smooth !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            padding: 10px 4px 20px !important;
            margin: -10px -4px -20px !important;
        }
        .kv-video-slider::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .kv-video-slider .kv-video-card {
            flex: 0 0 calc((100% - 40px) / 3) !important;
            width: calc((100% - 40px) / 3) !important;
            max-width: calc((100% - 40px) / 3) !important;
            min-width: 300px !important;
            box-sizing: border-box !important;
            scroll-snap-align: start !important;
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease !important;
        }
        .kv-video-slider .kv-video-card:hover {
            transform: translateY(-4px) !important;
            border-color: #0E1A24 !important;
            box-shadow: 0 16px 36px -12px rgba(14, 26, 36, 0.16), 0 0 0 1px #0E1A24 !important;
        }
        .kv-video-slider .kv-video-card:hover .kv-video-thumb {
            transform: scale(1.05) !important;
        }
        .kv-video-slider .kv-video-card:hover .kv-play-btn {
            transform: scale(1.1) !important;
            box-shadow: 0 8px 24px rgba(255, 0, 51, 0.45) !important;
            filter: brightness(1.08) !important;
        }
        .kv-slider-nav-btn:hover:not(:disabled) {
            background: #F4F6F5 !important;
            border-color: #0E1A24 !important;
            color: #0E1A24 !important;
        }
        .kv-video-modal-backdrop {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999 !important;
            display: grid;
            place-items: center;
            padding: 24px !important;
            box-sizing: border-box !important;
            background: rgba(7, 28, 51, 0.88) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }
        .kv-video-modal-backdrop[style*="display: none"] {
            display: none !important;
        }
        .kv-video-modal-container {
            position: relative !important;
            width: 100% !important;
            max-width: 960px !important;
            margin: auto !important;
            background: #000000 !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(255, 255, 255, 0.18) !important;
            box-sizing: border-box !important;
        }
        @media (max-width: 1024px) {
            .kv-video-slider .kv-video-card {
                flex: 0 0 calc((100% - 20px) / 2) !important;
                width: calc((100% - 20px) / 2) !important;
                max-width: calc((100% - 20px) / 2) !important;
                min-width: 260px !important;
            }
        }
        @media (max-width: 640px) {
            .kv-video-slider .kv-video-card {
                flex: 0 0 86% !important;
                width: 86% !important;
                max-width: 86% !important;
                min-width: 240px !important;
            }
        }
    </style>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap">
        <div>
            <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $videosTitle }}</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap">
            <a href="{{ setting('social_youtube', 'https://www.youtube.com/@heliosinformatikanusantara4415') }}" target="_blank" rel="noopener noreferrer" class="kv-btn-action kv-btn-outline" style="display: inline-flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 700; color: #0E1A24; padding: 10px 18px; border-radius: 12px; border: 1px solid #C9D2CE; background: #FFFFFF; white-space: nowrap; text-decoration: none">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4" fill="#FF0033"></rect><path d="M10 9v6l5-3Z" fill="#FFFFFF"></path></svg>
                {{ t('kouventa.Watch more on YouTube', 'Watch more on YouTube') }}
            </a>
            <div style="display: flex; gap: 8px">
                <button type="button" 
                        @click="scrollPrev()" 
                        :disabled="!canPrev" 
                        class="kv-slider-nav-btn" 
                        style="width: 44px; height: 44px; border-radius: 12px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease" 
                        :style="!canPrev ? 'opacity: 0.35; cursor: not-allowed' : ''" 
                        aria-label="{{ t('kouventa.Previous video', 'Previous video') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" 
                        @click="scrollNext()" 
                        :disabled="!canNext" 
                        class="kv-slider-nav-btn" 
                        style="width: 44px; height: 44px; border-radius: 12px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease" 
                        :style="!canNext ? 'opacity: 0.35; cursor: not-allowed' : ''" 
                        aria-label="{{ t('kouventa.Next video', 'Next video') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Horizontal Video Slider -->
    <div class="kv-video-slider" 
         x-ref="slider" 
         @scroll.passive="checkScroll()"
         style="display: flex; flex-direction: row; flex-wrap: nowrap; gap: 20px; overflow-x: auto; overflow-y: hidden; scroll-snap-type: x mandatory; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; scrollbar-width: none; padding: 10px 4px 20px; margin: -10px -4px -20px">
        @foreach($videos as $video)
        <a href="{{ $video['url'] }}" 
           @click.prevent="activeVideo = '{{ $video['id'] }}'" 
           class="kv-video-card kv-card kv-hover" 
           style="flex: 0 0 calc((100% - 40px) / 3); width: calc((100% - 40px) / 3); max-width: calc((100% - 40px) / 3); min-width: 300px; box-sizing: border-box; scroll-snap-align: start; display: flex; flex-direction: column; gap: 14px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 14px 14px 20px; color: #0E1A24; text-decoration: none; cursor: pointer"
           title="{{ $video['title'] }}">
            <div role="img" aria-label="{{ $video['title'] }}" style="position: relative; aspect-ratio: 16 / 9; border-radius: 14px; background: #071C33; display: flex; align-items: center; justify-content: center; overflow: hidden">
                <img src="{{ theme_asset('images/youtube/' . $video['id'] . '.jpg') }}" 
                     onerror="this.onerror=null; this.src='https://i.ytimg.com/vi/{{ $video['id'] }}/hqdefault.jpg';" 
                     alt="{{ $video['title'] }}" 
                     class="kv-video-thumb"
                     loading="lazy" 
                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s ease">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7,28,51,0.05) 0%, rgba(7,28,51,0.45) 100%); pointer-events: none"></div>
                <span class="kv-play-btn" style="position: absolute; width: 58px; height: 40px; border-radius: 12px; background: #FF0033; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(0,0,0,0.3); transition: all 0.25s ease">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#FFFFFF" aria-hidden="true"><path d="M8 5v14l11-7Z"></path></svg>
                </span>
            </div>
            <span style="align-self: flex-start; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: {{ $video['category'] === 'Artificial Intelligence' ? '#EAF7EF' : '#E6F0FA' }}; color: {{ $video['category'] === 'Artificial Intelligence' ? '#0B5E2C' : '#0F4C81' }}; margin: 0 6px">
                {{ t('kouventa.' . $video['category'], $video['category']) }}
            </span>
            <span style="font-size: 16px; line-height: 1.4; font-weight: 700; margin: 0 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 44px">
                {{ $video['title'] }}
            </span>
        </a>
        @endforeach
    </div>

    <!-- Video Player Modal -->
    <template x-teleport="body">
        <div x-show="activeVideo" 
             x-cloak 
             @keydown.escape.window="activeVideo = null"
             @click.self="activeVideo = null"
             class="kv-video-modal-backdrop"
             style="display: none;">
            <template x-if="activeVideo">
                <div @click.outside="activeVideo = null" 
                     class="kv-video-modal-container">
                    <button type="button" 
                            @click="activeVideo = null" 
                            aria-label="{{ t('kouventa.Close modal', 'Close modal') }}"
                            style="position: absolute; top: 12px; right: 12px; z-index: 50; width: 42px; height: 42px; border-radius: 50%; background: rgba(7, 28, 51, 0.85); border: 1px solid rgba(255,255,255,0.3); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0,0,0,0.5)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                    <div style="position: relative; width: 100%; padding-top: 56.25%">
                        <iframe :src="'https://www.youtube-nocookie.com/embed/' + activeVideo + '?autoplay=1&rel=0'" 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </template>
        </div>
    </template>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
