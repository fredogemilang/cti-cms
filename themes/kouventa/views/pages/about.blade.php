@extends('kouventa::layouts.app')

@php
    $heroBadge = $page?->block('hero_badge', 'About us');
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'From business communication', 'main' => 'to enterprise AI']);
    $teamImage = $page?->block('team_image', 'images/kouventa-team.png');
    $valuesBadge = $page?->block('values_badge', 'Our value');
    $valuesList = $page?->repeaterBlock('values_list', []);
    if (empty($valuesList)) {
        $valuesList = [
            [
                'title' => t('kouventa.Agile', 'Agile'),
                'description' => t('kouventa.We focus on collaboration, responsiveness, and delivering working solutions to foster innovation and exceed stakeholder expectations.', 'We focus on collaboration, responsiveness, and delivering working solutions to foster innovation and exceed stakeholder expectations.'),
                'icon' => '',
            ],
            [
                'title' => t('kouventa.Adaptive', 'Adaptive'),
                'description' => t('kouventa.We thrive on adaptability, shaping solutions to evolving needs. Our success is rooted in embracing change, growth, and innovation.', 'We thrive on adaptability, shaping solutions to evolving needs. Our success is rooted in embracing change, growth, and innovation.'),
                'icon' => '',
            ],
            [
                'title' => t('kouventa.AI Friendly', 'AI Friendly'),
                'description' => t('kouventa.We excel with AI-friendly innovation, leveraging technology to enhance customer experiences and create meaningful, future-ready interactions.', 'We excel with AI-friendly innovation, leveraging technology to enhance customer experiences and create meaningful, future-ready interactions.'),
                'icon' => '',
            ],
        ];
    }
    $historyTitle = $page?->block('history_title', 'History');
    $historyTimeline = $page?->repeaterBlock('history_timeline', []);
    if (empty($historyTimeline)) {
        $historyTimeline = [
            [
                'year' => '2014',
                'title' => t('kouventa.Established', 'Established'),
                'description' => t('kouventa.PT Helios Informatika Nusantara (Helios) was established in 2014 as IT Value Added Distributor (VAD). As a member of PT Computrade Technology International (CTI Group), Helios collaborates with some of the world\'s leading IT global companies to provide our clients with cutting-edge and innovative technologies.', 'PT Helios Informatika Nusantara (Helios) was established in 2014 as IT Value Added Distributor (VAD). As a member of PT Computrade Technology International (CTI Group), Helios collaborates with some of the world\'s leading IT global companies to provide our clients with cutting-edge and innovative technologies.'),
                'image' => 'images/about-milestone-office.webp',
            ],
            [
                'year' => '2021',
                'title' => t('kouventa.Embracing The Digital Era', 'Embracing The Digital Era'),
                'description' => t('kouventa.We created Kouventa, an All-In-One Customer Engagement solution, to help businesses maintain and grow their customer engagement. We are also committed to providing great service and support to our clients who wish to maximize the usefulness of technology such as Generative AI technology, and API Integration.', 'We created Kouventa, an All-In-One Customer Engagement solution, to help businesses maintain and grow their customer engagement. We are also committed to providing great service and support to our clients who wish to maximize the usefulness of technology such as Generative AI technology, and API Integration.'),
                'image' => 'images/about-milestone-planning.webp',
            ],
            [
                'year' => '2022',
                'title' => t('kouventa.Advancing The Generative AI Solutions', 'Advancing The Generative AI Solutions'),
                'description' => t('kouventa.Kouventa provides an AI Company Wikipedia solution based on Gen-AI technology, crafted to address enterprise demands for advanced AI adoption in complex operations.', 'Kouventa provides an AI Company Wikipedia solution based on Gen-AI technology, crafted to address enterprise demands for advanced AI adoption in complex operations.'),
                'image' => 'images/about-milestone-genai.webp',
            ],
            [
                'year' => '2026',
                'title' => t('kouventa.Agentic AI with AI Task Force', 'Agentic AI with AI Task Force'),
                'description' => t('kouventa.Kouventa launched AI Task Force, its Agentic AI solution, allowing intelligent agents to reason, plan, and take action across enterprise applications to automate business processes from start to finish.', 'Kouventa launched AI Task Force, its Agentic AI solution, allowing intelligent agents to reason, plan, and take action across enterprise applications to automate business processes from start to finish.'),
                'image' => 'images/about-milestone-agentic.webp',
            ],
        ];
    }

    $valIcons = [
        '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path></svg>',
        '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 1-15.5 6.2"></path><path d="M3 12a9 9 0 0 1 15.5-6.2"></path><path d="M18 2v4h-4"></path><path d="M6 22v-4h4"></path><circle cx="12" cy="12" r="3"></circle></svg>',
        '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="14" height="14" rx="3"></rect><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"></path><path d="M9.5 15l1.5-6 1.5 6M10 13.5h2M15 9v6"></path></svg>',
    ];
@endphp

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative; width: 100%;">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<div class="kv-container" style="padding-top: 88px; padding-bottom: 240px; display: flex; flex-direction: column; gap: 18px; position: relative">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6">{{ $heroBadge }}</span>
<h1 style="margin: 0; font-size: 64px; line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'From business communication' }}<br><span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'to enterprise AI' }}</span></h1>
</div>
</section>

<!-- ============ TEAM + VALUES ============ -->
<section style="width: 100%; margin-top: -170px; position: relative; z-index: 2">
<div class="kv-container">
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 24px; align-items: stretch">
<img src="{{ resolve_block_asset($teamImage ?: 'images/kouventa-team.png') }}" alt="{{ t('kouventa.Tim Profesional dan Solution Architect Kouventa Indonesia', 'Tim Profesional dan Solution Architect Kouventa Indonesia') }}" style="width: 100%; height: 100%; max-height: 640px; object-fit: cover; object-position: center top; border-radius: 22px; display: block; box-shadow: 0 24px 60px rgba(7,28,51,0.18)">
<div style="display: flex; flex-direction: column; gap: 16px">
<span style="height: 24px; font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6; text-align: right">{{ $valuesBadge }}</span>
@foreach($valuesList as $idx => $val)
<div style="flex: 1 1 0; background: #FFFFFF; border-radius: 18px; padding: 24px 26px; display: flex; flex-direction: column; justify-content: center; gap: 12px; box-shadow: 0 18px 40px rgba(7,28,51,0.12)">
<div style="display: flex; align-items: center; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0">
    @if(!empty($val['icon']) && str_starts_with(trim($val['icon']), '<svg'))
        {!! $val['icon'] !!}
    @else
        {!! $valIcons[$idx % count($valIcons)] !!}
    @endif
</span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em">{{ $val['title'] ?? '' }}</span>
</div>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">{{ $val['description'] ?? '' }}</p>
</div>
@endforeach
</div>
</div>
</div>
</section>

<!-- ============ HISTORY ============ -->
<section id="kv-history" style="width: 100%;">
<div class="kv-container" style="padding-top: 120px; padding-bottom: 128px; display: flex; flex-direction: column; gap: 56px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $historyTitle }}</h2>
<div class="kv-history-wrap">
    <!-- Static dashed track line connecting 2014 down to 2026 -->
    <div class="kv-tl-track"></div>
    <!-- Dynamic scroll-filled green line -->
    <div class="kv-tl-fill"></div>

    @foreach($historyTimeline as $item)
    @php
        $itemYear = $item['year'] ?? '';
        $itemTitle = $item['title'] ?? '';
        $itemDesc = $item['description'] ?? '';
        $itemImgRaw = !empty($item['image']) ? $item['image'] : 'images/about-milestone-office.webp';
        $itemImgUrl = resolve_block_asset($itemImgRaw);
    @endphp
    <div class="kv-tl-row" data-year="{{ $itemYear }}">
        <span class="kv-tl-year">{{ $itemYear }}</span>
        <span class="kv-tl-dot-wrap"><span class="kv-tl-dot"></span></span>
        <img src="{{ $itemImgUrl }}" alt="{{ $itemTitle }}" class="kv-tl-img">
        <div class="kv-tl-content">
            <h3 class="kv-tl-title">{{ $itemTitle }}</h3>
            <p class="kv-tl-desc">{{ $itemDesc }}</p>
        </div>
    </div>
    @endforeach
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    function initScrollTimeline() {
        const historySection = document.getElementById('kv-history');
        if (!historySection) return;

        const fill = historySection.querySelector('.kv-tl-fill');
        const rows = Array.from(historySection.querySelectorAll('.kv-tl-row'));
        if (!fill || !rows.length) return;

        // Check reduced motion preference
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            fill.style.transform = 'scaleY(1)';
            rows.forEach(r => r.classList.add('is-active'));
            return;
        }

        let ticking = false;

        function updateTimeline() {
            const viewportH = window.innerHeight;
            // The trigger line where user's eye naturally reads (~62% down the screen)
            const triggerY = viewportH * 0.62;

            const firstRow = rows[0];
            const lastRow = rows[rows.length - 1];
            const firstDot = firstRow.querySelector('.kv-tl-dot-wrap');
            const lastDot = lastRow.querySelector('.kv-tl-dot-wrap');

            if (!firstDot || !lastDot) {
                ticking = false;
                return;
            }

            const firstRect = firstDot.getBoundingClientRect();
            const lastRect = lastDot.getBoundingClientRect();

            const startY = firstRect.top + firstRect.height / 2;
            const endY = lastRect.top + lastRect.height / 2;
            const totalDist = endY - startY;

            let progress = 0;
            if (totalDist > 0) {
                progress = (triggerY - startY) / totalDist;
                progress = Math.max(0, Math.min(1, progress));
            }

            fill.style.transform = `scaleY(${progress})`;

            // Update active state on each row
            rows.forEach(row => {
                const dot = row.querySelector('.kv-tl-dot-wrap');
                if (!dot) return;
                const r = dot.getBoundingClientRect();
                const dotCenter = r.top + r.height / 2;

                if (triggerY >= dotCenter - 10) {
                    row.classList.add('is-active');
                } else {
                    row.classList.remove('is-active');
                }
            });

            ticking = false;
        }

        function requestUpdate() {
            if (!ticking) {
                requestAnimationFrame(updateTimeline);
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestUpdate, { passive: true });
        window.addEventListener('resize', requestUpdate, { passive: true });

        // Initial run
        requestUpdate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollTimeline);
    } else {
        initScrollTimeline();
    }
})();
</script>
@endpush
