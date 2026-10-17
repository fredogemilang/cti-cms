@push('styles')
<style>
.kv-stage-wrap { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.kv-stage-0 { width: 380px; display: flex; flex-direction: column; gap: 12px; font-size: 14px; line-height: 1.5; }
.kv-stage-1 { width: 470px; display: flex; align-items: center; }
.kv-stage-2 { width: 420px; display: flex; flex-direction: column; gap: 16px; }
.kv-product-info { display: flex; flex-direction: column; gap: 16px; }
.kv-mockup-wrap { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.kv-mockup-wiki { width: 360px; max-width: 100%; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column; }
.kv-mockup-taskforce { width: 400px; max-width: 100%; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.kv-mockup-playground { width: 380px; max-width: 100%; background: #FFFFFF; border-radius: 18px; box-shadow: 0 10px 30px rgba(7,28,51,0.1); padding: 18px; display: flex; flex-direction: column; gap: 14px; }
.kv-mockup-workflow { width: 440px; height: 440px; position: relative; overflow: hidden; border-radius: 14px; }

/* --- AI Products Section Layout & Components --- */
.kv-ai-products-section {
    padding: 48px 80px 104px;
    display: flex;
    flex-direction: column;
    gap: 32px;
    scroll-margin-top: 100px;
}
.kv-products-heading {
    font-size: 44px;
    line-height: 1.1;
}
.kv-products-layout {
    display: grid;
    grid-template-columns: 320px minmax(0, 1fr);
    gap: 24px;
    align-items: stretch;
}
.kv-product-tablist {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.kv-product-tab {
    flex: 1 1 0;
    min-height: 88px;
    padding: 16px 18px;
    border-radius: 16px;
    border: 1px solid #E3E8E6;
    background: #FFFFFF;
    color: #0E1A24;
    display: flex;
    align-items: center;
    gap: 14px;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s ease;
    box-sizing: border-box;
    width: 100%;
}
.kv-product-tab-icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.kv-product-tab-name {
    font-size: 17px;
    font-weight: 800;
}
.kv-product-tab-badge {
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 3px 8px;
    border-radius: 999px;
    background: #FFE9C7;
    color: #7A4A00;
}
.kv-product-tab-role {
    font-size: 13px;
    line-height: 1.4;
    opacity: 0.85;
}
.kv-product-tab-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: background 0.3s;
}
.kv-product-panel {
    background: #FFFFFF;
    border: 1px solid #E3E8E6;
    border-radius: 24px;
    padding: 40px;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 440px;
    gap: 40px;
    align-items: start;
}
.kv-product-info-col {
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 0;
}
.kv-product-mockup-col {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
}
.kv-product-mockup-container {
    height: 440px;
    width: 100%;
    border-radius: 18px;
    background: #F4F6F5;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.kv-product-replay-btn {
    align-self: flex-end;
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

/* Channel Marquee Card Styles */
.kv-channel-card:hover {
    background: rgba(255,255,255,0.09) !important;
    border-color: rgba(43,212,106,0.45) !important;
    transform: translateY(-2px);
}

/* ============ WHY SECTION & CARDS HOVER EFFECT ============ */
.kv-why-section {
    padding: 104px 80px;
    background: #071C33;
    color: #FFFFFF;
    display: flex;
    flex-direction: column;
    gap: 48px;
    position: relative;
    overflow: hidden;
}
.kv-why-heading {
    margin: 0;
    font-size: 44px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: -0.02em;
    position: relative;
}
.kv-why-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
    position: relative;
}
.kv-why-card {
    position: relative;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-height: 300px;
    box-sizing: border-box;
    cursor: default;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                border-color 0.25s ease,
                background-color 0.25s ease;
    will-change: transform, box-shadow;
}
.kv-why-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #2BD46A, transparent);
    opacity: 0;
    transition: opacity 0.35s ease;
    pointer-events: none;
}
.kv-why-card::after {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, rgba(43, 212, 106, 0.18) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
    border-radius: 50%;
}
.kv-why-card:hover {
    transform: translateY(-6px);
    background-color: rgba(255, 255, 255, 0.08);
    border-color: rgba(43, 212, 106, 0.45);
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.65), 0 0 24px rgba(43, 212, 106, 0.15);
}
.kv-why-card:hover::before,
.kv-why-card:hover::after {
    opacity: 1;
}
.kv-why-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: #2BD46A;
    color: #06251A;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(43, 212, 106, 0.25);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.kv-why-card:hover .kv-why-icon {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 14px 34px rgba(43, 212, 106, 0.45);
}
.kv-why-icon svg {
    transition: transform 0.3s ease;
}
.kv-why-card:hover .kv-why-icon svg {
    transform: scale(1.06);
}
.kv-why-title {
    font-size: 22px;
    line-height: 1.25;
    font-weight: 800;
    padding-top: 10px;
}
.kv-why-desc {
    font-size: 16px;
    line-height: 1.55;
    color: #B9C6D3;
}
.kv-why-badge {
    margin-top: auto;
    align-self: flex-start;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(43, 212, 106, 0.12);
    color: #2BD46A;
    border: 1px solid transparent;
    transition: background-color 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
}
.kv-why-card:hover .kv-why-badge {
    background-color: rgba(43, 212, 106, 0.22);
    border-color: rgba(43, 212, 106, 0.4);
    transform: translateX(4px);
}

/* ============================================================
   RESPONSIVE MEDIA QUERIES FOR AI PRODUCTS SECTION
   ============================================================ */

/* Tablet & Smaller Screens (<= 1024px) */
@media (max-width: 1024px) {
    .kv-why-section {
        padding: 80px 32px !important;
        gap: 36px !important;
    }
    .kv-why-heading {
        font-size: 34px !important;
    }
    .kv-why-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }
    .kv-why-card {
        padding: 24px !important;
        min-height: 260px !important;
    }

    .kv-ai-products-section {
        padding: 40px 32px 72px !important;
        gap: 24px !important;
        scroll-margin-top: 80px !important;
    }

    .kv-products-heading {
        font-size: 34px !important;
    }

    .kv-products-layout {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }

    .kv-product-tablist {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .kv-product-panel {
        grid-template-columns: 1fr !important;
        gap: 32px !important;
        padding: 32px 24px !important;
    }

    .kv-product-mockup-container {
        height: auto !important;
        min-height: 400px !important;
        padding: 24px 16px !important;
        box-sizing: border-box !important;
    }

    .kv-product-replay-btn {
        align-self: center !important;
    }
}

/* Mobile Screens (<= 768px) */
@media (max-width: 768px) {
    .kv-integrate-card {
        padding: 24px 0 !important;
    }

    .kv-integrate-header {
        padding: 0 20px !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 6px !important;
    }

    .kv-channel-card {
        width: 136px !important;
        height: 84px !important;
        padding: 10px 10px !important;
    }

    .kv-channel-card img {
        width: 26px !important;
        height: 26px !important;
    }

    .kv-channel-card span {
        font-size: 12px !important;
    }

    .kv-why-section {
        padding: 60px 20px !important;
        gap: 28px !important;
    }

    .kv-why-heading {
        font-size: 28px !important;
        line-height: 1.2 !important;
    }

    .kv-why-grid {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
    }

    .kv-why-card {
        padding: 22px 20px !important;
        min-height: auto !important;
    }

    .kv-ai-products-section {
        padding: 32px 20px 60px !important;
        gap: 20px !important;
    }

    .kv-products-heading {
        font-size: 28px !important;
        line-height: 1.2 !important;
    }

    .kv-linked-pill {
        white-space: normal !important;
        height: auto !important;
        min-height: 44px !important;
        padding: 10px 14px !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        border-radius: 16px !important;
        font-size: 13.5px !important;
    }

    .kv-btn-show-all {
        margin-left: 0 !important;
        font-size: 12px !important;
        min-height: 32px !important;
        padding: 4px 12px !important;
    }

    .kv-product-tablist {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
    }

    .kv-product-tab {
        min-height: 72px !important;
        padding: 12px 12px !important;
        border-radius: 14px !important;
        gap: 10px !important;
        align-items: flex-start !important;
    }

    .kv-product-tab-icon {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
    }

    .kv-product-tab-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .kv-product-tab-name {
        font-size: 14.5px !important;
        line-height: 1.25 !important;
    }

    .kv-product-tab-badge {
        font-size: 9px !important;
        padding: 2px 6px !important;
    }

    .kv-product-tab-role {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
    }

    .kv-product-tab-dot {
        display: none !important;
    }

    .kv-product-panel {
        padding: 24px 18px !important;
        border-radius: 20px !important;
        gap: 24px !important;
    }

    .kv-product-info > span:first-child {
        font-size: 24px !important;
        line-height: 1.2 !important;
    }

    .kv-product-info > span:nth-child(2),
    .kv-product-info > span:nth-child(3) {
        font-size: 14.5px !important;
        line-height: 1.5 !important;
    }

    .kv-product-mockup-container {
        min-height: 360px !important;
        padding: 16px 8px !important;
    }

    .kv-mockup-wiki {
        width: 100% !important;
        max-width: 360px !important;
    }

    .kv-mockup-taskforce {
        width: 100% !important;
        max-width: 380px !important;
        padding: 0 4px !important;
        box-sizing: border-box !important;
    }

    .kv-mockup-playground {
        width: 100% !important;
        max-width: 360px !important;
    }

    .kv-mockup-workflow {
        transform: scale(0.82);
        transform-origin: center center;
        margin: -25px 0;
    }
}

/* Small Mobile Screens (<= 480px) */
@media (max-width: 480px) {
    .kv-ai-products-section {
        padding: 24px 16px 48px !important;
    }

    .kv-products-heading {
        font-size: 24px !important;
    }

    .kv-product-tablist {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .kv-product-tab {
        padding: 10px 8px !important;
        border-radius: 12px !important;
        gap: 8px !important;
        min-height: 64px !important;
    }

    .kv-product-tab-icon {
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
    }

    .kv-product-tab-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    .kv-product-tab-name {
        font-size: 13px !important;
        line-height: 1.2 !important;
    }

    .kv-product-tab-role {
        font-size: 10.5px !important;
    }

    .kv-product-panel {
        padding: 20px 14px !important;
        border-radius: 16px !important;
        gap: 20px !important;
    }

    .kv-mockup-workflow {
        transform: scale(0.70);
        transform-origin: center center;
        margin: -40px 0;
    }
}

/* Extra Small (<= 360px) */
@media (max-width: 360px) {
    .kv-product-tablist {
        grid-template-columns: 1fr !important;
        gap: 8px !important;
    }

    .kv-mockup-workflow {
        transform: scale(0.60);
        transform-origin: center center;
        margin: -55px 0;
    }
}
</style>
@endpush

@extends('kouventa::layouts.app')

@php
$stages = [
    [
        'key' => 'answer',
        'num' => '01',
        'name' => t('kouventa.Answer', 'Answer'),
        'headline' => t('kouventa.Give everyone fast, accurate answers from your own knowledge.', 'Give everyone fast, accurate answers from your own knowledge.'),
        'sub' => t("kouventa.Kouventa AI turns your company's documents and knowledge into an AI assistant that helps customers and employees find the right information instantly, without searching through files or waiting for another team.", "Kouventa AI turns your company's documents and knowledge into an AI assistant that helps customers and employees find the right information instantly, without searching through files or waiting for another team."),
        'defaultProduct' => 'wiki'
    ],
    [
        'key' => 'analyze',
        'num' => '02',
        'name' => t('kouventa.Analyze', 'Analyze'),
        'headline' => t('kouventa.Turn your data into insights that drive better decisions.', 'Turn your data into insights that drive better decisions.'),
        'sub' => t('kouventa.Go beyond answering questions. Kouventa AI analyzes business data and conversations to uncover patterns, highlight risks and opportunities, and recommend what to do next.', 'Go beyond answering questions. Kouventa AI analyzes business data and conversations to uncover patterns, highlight risks and opportunities, and recommend what to do next.'),
        'defaultProduct' => 'taskforce'
    ],
    [
        'key' => 'execute',
        'num' => '03',
        'name' => t('kouventa.Execute', 'Execute'),
        'headline' => t('kouventa.Let AI take action, with your approval.', 'Let AI take action, with your approval.'),
        'sub' => t('kouventa.Once a recommendation is approved, Kouventa AI agents reason, plan, and carry out the task across connected systems, so insights turn into completed work.', 'Once a recommendation is approved, Kouventa AI agents reason, plan, and carry out the task across connected systems, so insights turn into completed work.'),
        'defaultProduct' => 'taskforce'
    ]
];

$stageRoles = [
    'answer' => [
        'wiki' => t('kouventa.One trusted knowledge base for answering questions', 'One trusted knowledge base for answering questions'),
    ],
    'analyze' => [
        'taskforce' => t('kouventa.AI agents that analyze data and produce actionable recommendations', 'AI agents that analyze data and produce actionable recommendations'),
        'wiki' => t('kouventa.Ground every analysis in your own business knowledge', 'Ground every analysis in your own business knowledge'),
    ],
    'execute' => [
        'taskforce' => t('kouventa.AI agents that plan and execute business processes autonomously · MCP Connectivity: Connect AI agents to your business systems', 'AI agents that plan and execute business processes autonomously · MCP Connectivity: Connect AI agents to your business systems'),
    ],
];

$stageButtons = [
    [
        'key' => 'answer',
        'num' => '01',
        'name' => t('kouventa.Answer', 'Answer'),
        'left' => '16.667%',
        'svg' => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"></path><path d="M10 9.5a2 2 0 1 1 2.8 1.8c-.5.3-.8.7-.8 1.2v.5M12 16h.01"></path>'
    ],
    [
        'key' => 'analyze',
        'num' => '02',
        'name' => t('kouventa.Analyze', 'Analyze'),
        'left' => '50%',
        'svg' => '<path d="M3 3v18h18"></path><path d="M8 16v-4M12 16V8M16 16v-6"></path>'
    ],
    [
        'key' => 'execute',
        'num' => '03',
        'name' => t('kouventa.Execute', 'Execute'),
        'left' => '83.333%',
        'svg' => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"></path>'
    ]
];

$productsList = [
    [
        'id' => 'wiki',
        'name' => 'AI Company Wikipedia',
        'tag' => t('kouventa.Knowledge assistant', 'Knowledge assistant'),
        'soon' => false,
        'svg' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path>'
    ],
    [
        'id' => 'playground',
        'name' => 'Chat Playground',
        'tag' => t('kouventa.Works with Wikipedia & Task Force', 'Works with Wikipedia & Task Force'),
        'soon' => false,
        'svg' => '<path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path>'
    ],
    [
        'id' => 'taskforce',
        'name' => 'AI Task Force',
        'tag' => t('kouventa.Agentic AI', 'Agentic AI'),
        'soon' => false,
        'svg' => '<circle cx="12" cy="7" r="3"></circle><circle cx="5" cy="17" r="2.5"></circle><circle cx="19" cy="17" r="2.5"></circle><path d="M10 9.5 6.5 15M14 9.5l3.5 5.5"></path>'
    ],
    [
        'id' => 'workflow',
        'name' => 'TaskO',
        'tag' => t('kouventa.Task Automation', 'Task Automation'),
        'soon' => true,
        'svg' => '<rect x="3" y="3" width="7" height="6" rx="1.5"></rect><rect x="14" y="15" width="7" height="6" rx="1.5"></rect><path d="M6.5 9v3a3 3 0 0 0 3 3H14"></path>'
    ]
];

// ── Page Blocks from CMS Content Builder ──────────────
$heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'One AI platform is', 'main' => 'all you need.']);
$heroSubtitle = $page?->block('hero_subtitle', 'Build AI agents with ease and put them to work on what your organization needs, with control you can measure.');
$heroCta = $page?->buttonBlock('hero_cta', ['text' => 'Book a demo', 'url' => '/book-demo']);
$heroSecondaryCta = $page?->buttonBlock('hero_secondary_cta', ['text' => 'Explore products', 'url' => '#ai-products']);
$challengesTitle = $page?->titleBlock('challenges_title', ['prefix' => 'Too many AI tools,', 'main' => 'too little control.']);
$productsTitle = $page?->block('products_title', 'Four specialized products, one unified platform.');
$bottomCtaTitle = $page?->block('bottom_cta_title', 'Ready to take control of AI in your organization?');
$bottomCtaSubtitle = $page?->block('bottom_cta_subtitle', 'Book a demo to see how Kouventa unifies AI adoption with enterprise control.');
@endphp

@section('content')
<div class="kv-canvas-wrap" x-data="productAiPage">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Products" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ t('kouventa.Products', 'Products') }}</span>
<a href="{{ localized_url('/product/ai') }}" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</a>
<a href="{{ localized_url('/product/customer-engagement') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</a>
</nav>

<div style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 520px; gap: 64px; align-items: center; position: relative">
<div style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'One AI platform is' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'all you need.' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 600px">{!! nl2br(e($heroSubtitle)) !!}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" class="kv-btn-action kv-btn-primary" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ $heroCta['text'] ?? 'Book a demo' }}</a>
<a href="{{ $heroSecondaryCta['url'] ?? '#ai-products' }}" class="kv-btn-action kv-btn-outline" style="color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28); text-decoration: none">{{ $heroSecondaryCta['text'] ?? 'Explore products' }}</a>
</div>
</div>

<!-- hero: scattered tools converge into one platform -->
<div style="display: flex; flex-direction: column; align-items: center; gap: 12px">
<div role="img" aria-label="Animated illustration: scattered AI tools for chat, documents, dashboards and billing drift together into one governed AI workspace" style="width: 520px; height: 440px; position: relative">
<div style="position: absolute; left: 50%; top: 50%; width: 380px; height: 380px; margin: -190px 0 0 -190px; border-radius: 50%; border: 1px dashed rgba(255,255,255,0.12)"></div>
<div style="position: absolute; left: 50%; top: 50%; width: 240px; height: 240px; margin: -120px 0 0 -120px; border-radius: 50%; border: 1px dashed rgba(255,255,255,0.12)"></div>

<div x-show="heroOn" style="position: absolute; inset: 0">
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -190px; --y: -130px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg></span>
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 180px; --y: -150px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 210px; --y: 60px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 16v-4M12 16V8M17 16v-6"></path></svg></span>
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 90px; --y: 170px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"></path><path d="M9 7h6M9 11h6M9 15h3"></path></svg></span>
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -150px; --y: 150px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg></span>
<span style="position: absolute; left: 50%; top: 50%; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -220px; --y: 20px; animation: kvConverge 5s ease 0s 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
<div style="position: absolute; left: 50%; top: 50%; width: 300px; margin: -118px 0 0 -150px; box-sizing: border-box; border-radius: 22px; background: #0C2A48; border: 1px solid rgba(43,212,106,0.45); padding: 16px; display: flex; flex-direction: column; gap: 12px; animation: kvLogo 5s ease 0s 1 forwards, kvGlow 5s ease 0s 1 forwards"><div style="display: flex; justify-content: space-between; align-items: center"><span style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #FFFFFF"><span style="width: 8px; height: 8px; border-radius: 50%; background: #2BD46A"></span>{{ t('kouventa.Your AI workspace', 'Your AI workspace') }}</span><span style="font-size: 10.5px; font-weight: 700; padding: 4px 8px; border-radius: 999px; background: rgba(43,212,106,0.15); color: #2BD46A">{{ t('kouventa.All models', 'All models') }}</span></div><div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px"><span style="background: rgba(255,255,255,0.06); border-radius: 10px; padding: 9px 8px; display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #FFFFFF"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg>Chat</span><span style="background: rgba(255,255,255,0.06); border-radius: 10px; padding: 9px 8px; display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #FFFFFF"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6"></path></svg>{{ t('kouventa.Documents', 'Documents') }}</span><span style="background: rgba(255,255,255,0.06); border-radius: 10px; padding: 9px 8px; display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #FFFFFF"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 16v-4M12 16V8M17 16v-6"></path></svg>{{ t('kouventa.Insights', 'Insights') }}</span><span style="background: rgba(255,255,255,0.06); border-radius: 10px; padding: 9px 8px; display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #FFFFFF"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"></path><path d="M9 7h6M9 11h6M9 15h3"></path></svg>Billing</span></div><div style="display: flex; align-items: center; gap: 8px; background: #FFFFFF; border-radius: 10px; padding: 8px 8px 8px 12px"><span style="flex-grow: 1; font-size: 12px; color: #5B6875">{{ t('kouventa.Ask anything about your business…', 'Ask anything about your business…') }}</span><span style="width: 26px; height: 26px; border-radius: 8px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></div><div style="display: flex; gap: 6px; font-size: 10.5px; font-weight: 700"><span style="padding: 4px 8px; border-radius: 999px; background: rgba(255,255,255,0.08); color: #D5DEE8">{{ t('kouventa.PII masked', 'PII masked') }}</span><span style="padding: 4px 8px; border-radius: 999px; background: rgba(255,255,255,0.08); color: #D5DEE8">{{ t('kouventa.Role access', 'Role access') }}</span><span style="padding: 4px 8px; border-radius: 999px; background: rgba(255,255,255,0.08); color: #D5DEE8">{{ t('kouventa.Budget on track', 'Budget on track') }}</span></div></div>
</div>

</div>
<button type="button" @click="replayHero()" style="min-height: 40px; padding: 8px 16px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: #B9C6D3; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>{{ t('kouventa.Replay', 'Replay') }}</button>
</div>
</div>
</section>

<!-- ============ CHALLENGES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $challengesTitle['prefix'] ?? 'Too many AI tools,' }} <span style="color: #13803F">{{ $challengesTitle['main'] ?? 'too little control.' }}</span></h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px">
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.2s both">
<div aria-hidden="true" style="height: 56px; position: relative; width: 120px">
<span style="position: absolute; left: 0; top: 10px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: -4px; --y: -8px; animation: kvJitter 2s ease 0.4s 1 both"></span>
<span style="position: absolute; left: 44px; top: 0; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: 6px; --y: 10px; animation: kvJitter 2s ease 0.4s 1 both"></span>
<span style="position: absolute; left: 86px; top: 18px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; --x: 8px; --y: -6px; animation: kvJitter 2s ease 0.4s 1 both"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.AI tools are scattered across teams', 'AI tools are scattered across teams') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Different teams use different AI tools based on their needs, creating a fragmented AI environment across the organization.', 'Different teams use different AI tools based on their needs, creating a fragmented AI environment across the organization.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.8s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; transform-origin: 50% 80%; animation: kvWobble 1.4s ease 1.2s 1 both"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 7.5-2"></path></svg></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Limited control over AI usage', 'Limited control over AI usage') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.With AI tools managed separately, it becomes harder to control how AI is used and ensure business data is handled securely.', 'With AI tools managed separately, it becomes harder to control how AI is used and ensure business data is handled securely.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 1.4s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: flex-end; gap: 6px">
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #F0C4BA; --h: 18px; animation: kvGrowH 1.2s ease 1.8s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #E79F8E; --h: 30px; animation: kvGrowH 1.2s ease 1.9s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #D9735D; --h: 42px; animation: kvGrowH 1.2s ease 2.0s 1 both"></span>
<span style="width: 16px; border-radius: 4px 4px 0 0; background: #B4412A; --h: 56px; animation: kvGrowH 1.2s ease 2.1s 1 both"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.AI usage & billing are hard to manage', 'AI usage & billing are hard to manage') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Multiple AI subscriptions make it difficult to centralize usage, manage subscriptions, and keep track of spending across teams.', 'Multiple AI subscriptions make it difficult to centralize usage, manage subscriptions, and keep track of spending across teams.') }}</p>
</article>
</div>
</section>

<!-- ============ ENTERPRISE AI JOURNEY ============ -->
<section id="journey" style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 14px; max-width: 900px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Bring your AI together,', 'Bring your AI together,') }} <span style="color: #13803F">{{ t('kouventa.from answers to action.', 'from answers to action.') }}</span></h2>
<p style="margin: 0; font-size: 18px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Kouventa AI helps businesses turn AI into practical business outcomes, from answering questions and uncovering insights to autonomously executing business processes across systems.', 'Kouventa AI helps businesses turn AI into practical business outcomes, from answering questions and uncovering insights to autonomously executing business processes across systems.') }}</p>
</div>

<!-- Journey Tablist -->
<div role="tablist" aria-label="Enterprise AI journey" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 28px 0 26px">
<div style="position: relative; height: 176px">
<!-- Dashed background track -->
<span aria-hidden="true" style="position: absolute; top: 76px; left: 16.667%; width: 66.667%; height: 4px; border-radius: 4px; background: repeating-linear-gradient(90deg, #C9D2CE 0 10px, transparent 10px 18px)"></span>

<!-- Active green progress bar -->
<span aria-hidden="true" style="position: absolute; top: 76px; left: 16.667%; height: 4px; border-radius: 4px; background: #13803F; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1)"
      :style="{ width: stage === 0 ? '0%' : (stage === 1 ? '33.333%' : '66.667%') }"></span>

<!-- Floating badge "Your AI needs" -->
<span aria-hidden="true" style="position: absolute; top: 0; transform: translateX(-50%); transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; z-index: 10"
      :style="{ left: stage === 0 ? '16.667%' : (stage === 1 ? '50%' : '83.333%') }">
<span style="animation: kvFloat 2.4s ease-in-out infinite; display: flex; flex-direction: column; align-items: center">
<span style="display: flex; align-items: center; gap: 8px; padding: 5px 14px 5px 5px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-size: 13px; font-weight: 700; white-space: nowrap; box-shadow: 0 6px 16px rgba(7, 28, 51, 0.18)">
<span style="width: 26px; height: 26px; border-radius: 50%; background: #2BD46A; color: #071C33; display: flex; align-items: center; justify-content: center">
<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 4.6L18.5 9.5l-4.7 1.9L12 16l-1.8-4.6L5.5 9.5l4.7-1.9Z"></path><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8Z"></path></svg>
</span>{{ t('kouventa.Your AI needs', 'Your AI needs') }}
</span>
<span style="width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 7px solid #071C33"></span>
</span>
</span>

@foreach($stageButtons as $idx => $btn)
<button type="button" role="tab" :aria-selected="stage === {{ $idx }}" @click="pickStage({{ $idx }})"
        style="position: absolute; top: 50px; left: {{ $btn['left'] }}; transform: translateX(-50%); width: 240px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
    <span style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s; {{ $idx === 0 ? 'background: #071C33; border: 2px solid #071C33; color: #2BD46A; animation: kvPulse 2s ease-out infinite;' : 'background: #FFFFFF; border: 2px solid #C9D2CE; color: #6B7A88;' }}"
          :style="{
              background: stage === {{ $idx }} ? '#071C33' : (stage > {{ $idx }} ? '#EAF7EF' : '#FFFFFF'),
              border: stage === {{ $idx }} ? '2px solid #071C33' : (stage > {{ $idx }} ? '2px solid #13803F' : '2px solid #C9D2CE'),
              color: stage === {{ $idx }} ? '#2BD46A' : (stage > {{ $idx }} ? '#13803F' : '#6B7A88'),
              animation: stage === {{ $idx }} ? 'kvPulse 2s ease-out infinite' : 'none'
          }">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            {!! $btn['svg'] !!}
        </svg>
    </span>
    <span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; {{ $idx === 0 ? 'color: #13803F;' : 'color: #6B7A88;' }}"
          :style="{ color: stage >= {{ $idx }} ? '#13803F' : '#6B7A88' }">{{ $btn['num'] }}</span>
    <span style="font-size: 20px; font-weight: 800; {{ $idx === 0 ? 'color: #0E1A24;' : 'color: #4A5866;' }}"
          :style="{ color: stage === {{ $idx }} ? '#0E1A24' : '#4A5866' }">{{ $btn['name'] }}</span>
</button>
@endforeach

</div>
</div>

<!-- Journey Tabpanel -->
<div role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 44px; display: grid; grid-template-columns: minmax(0, 1fr) 520px; gap: 48px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 16px">
<span style="font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em" x-text="stages[stage].headline">{{ $stages[0]['headline'] }}</span>
<span style="font-size: 16px; line-height: 1.6; color: #4A5866" x-text="stages[stage].sub">{{ $stages[0]['sub'] }}</span>
<button type="button" @click="seeTools(stages[stage].defaultProduct)" style="align-self: flex-start; margin-top: 8px; min-height: 52px; padding: 14px 22px; border-radius: 12px; border: 0; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px; cursor: pointer">
<span>{{ t('kouventa.See the products for', 'See the products for') }} <span x-text="stages[stage].name">{{ $stages[0]['name'] }}</span></span>
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
</button>
</div>

<div style="display: flex; flex-direction: column; gap: 10px">
<div role="img" style="height: 400px; border-radius: 18px; background: #F4F6F5; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center">
<div class="kv-stage-wrap" x-show="jOn">

<!-- Stage 0: Answer Animation -->
<div class="kv-stage-0" x-show="stage === 0">
<div style="align-self: flex-end; max-width: 75%; background: #071C33; color: #FFFFFF; padding: 11px 15px; border-radius: 16px 4px 16px 16px; animation: kvIn .4s ease .2s both">{{ t('kouventa.What is our leave policy?', 'What is our leave policy?') }}</div>
<div style="display: grid">
<div style="grid-area: 1 / 1; align-self: start; justify-self: start; background: #FFFFFF; padding: 13px 15px; border-radius: 4px 16px 16px 16px; display: flex; gap: 5px; animation: kvIn .3s ease .9s both, kvOut .2s ease 2.1s 1 forwards"><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease infinite"></span><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 7px; height: 7px; border-radius: 50%; background: #4A5866; animation: kvBlink 1s ease .4s infinite"></span></div>
<div style="grid-area: 1 / 1; justify-self: start; max-width: 88%; background: #FFFFFF; padding: 13px 15px; border-radius: 4px 16px 16px 16px; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 10px 30px rgba(7,28,51,0.06); animation: kvIn .4s ease 2.3s both">
<span><span style="font-size: 11px; font-weight: 700; color: #13803F">Kouventa AI</span><br>{{ t('kouventa.Full-time employees get 12 days of annual leave per year. Requests are submitted to your manager at least 7 days in advance.', 'Full-time employees get 12 days of annual leave per year. Requests are submitted to your manager at least 7 days in advance.') }}</span>
<div style="display: flex; gap: 6px; flex-wrap: wrap; animation: kvIn .4s ease 3.4s both">
<span style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 6px 10px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6"></path></svg>HR Policy.pdf</span>
<span style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 6px 10px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6"></path></svg>Employee Handbook</span>
</div>
</div>
</div>
</div>

<!-- Stage 1: Analyze Animation -->
<div class="kv-stage-1" x-show="stage === 1" x-cloak>
<div style="width: 108px; flex-shrink: 0; display: flex; flex-direction: column; gap: 14px">
<span style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; padding: 10px; border-radius: 10px; background: #FFFFFF; animation: kvIn .4s ease .1s both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="8" ry="3"></ellipse><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"></path></svg>Database</span>
<span style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; padding: 10px; border-radius: 10px; background: #FFFFFF; animation: kvIn .4s ease .3s both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6"></path></svg>{{ t('kouventa.Documents', 'Documents') }}</span>
<span style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; padding: 10px; border-radius: 10px; background: #FFFFFF; animation: kvIn .4s ease .5s both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg>{{ t('kouventa.Chats', 'Chats') }}</span>
</div>
<div style="width: 46px; flex-shrink: 0; height: 130px; display: flex; flex-direction: column; justify-content: space-around">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 38px; animation: kvTravel .9s linear .8s 3 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 38px; animation: kvTravel .9s linear 1.0s 3 both"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 38px; animation: kvTravel .9s linear 1.2s 3 both"></span></div>
</div>
<div style="width: 76px; height: 76px; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center">
<span style="position: absolute; inset: 0; border-radius: 50%; border: 3px solid #DCE3E0; border-top-color: #13803F; animation: kvSpin 1s linear .8s 3 both"></span>
<span style="width: 56px; height: 56px; border-radius: 50%; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800">AGENT</span>
</div>
<div style="width: 20px; flex-shrink: 0; height: 2px; background: #DCE3E0"></div>
<div style="flex-grow: 1; display: flex; flex-direction: column; gap: 10px">
<div style="background: #FFFFFF; border-radius: 12px; padding: 12px; height: 92px; box-sizing: border-box; display: flex; align-items: flex-end; gap: 8px; animation: kvIn .4s ease 3.6s both">
<span style="flex-grow: 1; background: #C9E8D3; border-radius: 4px 4px 0 0; --h: 30px; animation: kvGrowH .6s ease 3.8s both"></span>
<span style="flex-grow: 1; background: #9ED3B0; border-radius: 4px 4px 0 0; --h: 46px; animation: kvGrowH .6s ease 3.9s both"></span>
<span style="flex-grow: 1; background: #5BB880; border-radius: 4px 4px 0 0; --h: 38px; animation: kvGrowH .6s ease 4.0s both"></span>
<span style="flex-grow: 1; background: #B4412A; border-radius: 4px 4px 0 0; --h: 64px; animation: kvGrowH .6s ease 4.1s both"></span>
</div>
<div style="background: #071C33; color: #FFFFFF; border-radius: 12px; padding: 12px 14px; display: flex; flex-direction: column; gap: 4px; animation: kvIn .4s ease 4.6s both">
<span style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #2BD46A">{{ t('kouventa.RECOMMENDATION', 'RECOMMENDATION') }}</span>
<span style="font-size: 14px; font-weight: 700">{{ t('kouventa.Prioritize 3 overdue invoices', 'Prioritize 3 overdue invoices') }}</span>
</div>
</div>
</div>

<!-- Stage 2: Execute Animation -->
<div class="kv-stage-2" x-show="stage === 2" x-cloak>
<div style="background: #FFFFFF; border-radius: 16px; padding: 18px; display: flex; justify-content: space-between; align-items: center; gap: 16px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvIn .4s ease .1s both">
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #13803F">{{ t('kouventa.RECOMMENDATION', 'RECOMMENDATION') }}</span><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Prioritize 3 overdue invoices', 'Prioritize 3 overdue invoices') }}</span></div>
<div style="position: relative; display: grid; flex-shrink: 0">
<span style="grid-area: 1 / 1; padding: 10px 18px; border-radius: 10px; background: #071C33; color: #FFFFFF; font-size: 14px; font-weight: 700; text-align: center">{{ t('kouventa.Approve', 'Approve') }}</span>
<span style="grid-area: 1 / 1; padding: 10px 14px; border-radius: 10px; background: #13803F; color: #FFFFFF; font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 6px; animation: kvPopIn .35s ease 1.5s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>{{ t('kouventa.Approved', 'Approved') }}</span>
<span aria-hidden="true" style="position: absolute; left: 50%; top: 60%; animation: kvCursor 1.6s ease .2s 1 both"><svg width="22" height="22" viewBox="0 0 24 24" fill="#0E1A24" stroke="#FFFFFF" stroke-width="1.2"><path d="M5 3l14 8-6 1.5L10 19z"></path></svg></span>
</div>
</div>
<div style="background: #FFFFFF; border-radius: 16px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px">
<span style="font-size: 12px; font-weight: 700; color: #4A5866; animation: kvIn .3s ease 2.6s both">{{ t('kouventa.Agent is executing', 'Agent is executing') }}</span>
<div style="display: flex; align-items: center; gap: 10px; font-size: 14px; animation: kvIn .3s ease 2.8s both"><span style="width: 44px; font-size: 11px; font-weight: 800; padding: 5px 0; text-align: center; border-radius: 6px; background: #E3E8E6">ERP</span>{{ t('kouventa.Mark invoices as priority', 'Mark invoices as priority') }}<span style="margin-left: auto; width: 22px; height: 22px; border-radius: 50%; background: #13803F; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.2s both"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span></div>
<div style="display: flex; align-items: center; gap: 10px; font-size: 14px; animation: kvIn .3s ease 3.4s both"><span style="width: 44px; font-size: 11px; font-weight: 800; padding: 5px 0; text-align: center; border-radius: 6px; background: #E3E8E6">CRM</span>{{ t('kouventa.Update customer accounts', 'Update customer accounts') }}<span style="margin-left: auto; width: 22px; height: 22px; border-radius: 50%; background: #13803F; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.8s both"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span></div>
<div style="display: flex; align-items: center; gap: 10px; font-size: 14px; animation: kvIn .3s ease 4.0s both"><span style="width: 44px; font-size: 11px; font-weight: 800; padding: 5px 0; text-align: center; border-radius: 6px; background: #E3E8E6">Email</span>{{ t('kouventa.Send payment reminders', 'Send payment reminders') }}<span style="margin-left: auto; width: 22px; height: 22px; border-radius: 50%; background: #13803F; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 4.4s both"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span></div>
</div>
<span style="align-self: center; display: flex; align-items: center; gap: 8px; background: #071C33; color: #FFFFFF; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; animation: kvPopIn .4s ease 5s both"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>{{ t('kouventa.Task completed', 'Task completed') }}</span>
</div>

</div>
</div>

<button type="button" @click="replayJourney()" style="align-self: flex-end; min-height: 40px; padding: 8px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #4A5866; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>
    {{ t('kouventa.Replay', 'Replay') }}
</button>
</div>
</div>
</section>

<!-- ============ OUR PRODUCTS ============ -->
<section id="ai-products" class="kv-ai-products-section">
<div style="display: flex; flex-direction: column; align-items: flex-start; gap: 16px">
<h2 class="kv-products-heading" style="margin: 0; font-weight: 800; letter-spacing: -0.02em">{{ $productsTitle }}</h2>
<span class="kv-linked-pill" x-show="linked" x-cloak>
    <span class="kv-pill-dot"></span>
    <span>{{ t('kouventa.Highlighted: how each product helps in', 'Highlighted: how each product helps in') }} <strong style="color: #0E1A24" x-text="stages[stage].name">{{ $stages[0]['name'] }}</strong></span>
    <button type="button" @click="linked = false" class="kv-btn-show-all">{{ t('kouventa.Show all products', 'Show all products') }}<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></button>
</span>
<span class="kv-unlinked-pill" x-show="!linked">
    <a href="#journey" @click.prevent="document.getElementById('journey')?.scrollIntoView({ behavior: 'smooth' })" class="kv-unlinked-link">
        <span>{{ t('kouventa.Pick a journey stage above to see which products help', 'Pick a journey stage above to see which products help') }}</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
    </a>
</span>
</div>

<div class="kv-products-layout">
<div role="tablist" class="kv-product-tablist" aria-label="Products" aria-orientation="vertical">

@foreach($productsList as $p)
<button type="button" role="tab" class="kv-product-tab"
        :aria-selected="product === '{{ $p['id'] }}'"
        @click="product = '{{ $p['id'] }}'; replayProduct()"
        :style="{
            border: '1px solid ' + (product === '{{ $p['id'] }}' ? '#071C33' : (hasRole('{{ $p['id'] }}') ? '#13803F' : '#E3E8E6')),
            background: product === '{{ $p['id'] }}' ? '#071C33' : '#FFFFFF',
            color: product === '{{ $p['id'] }}' ? '#FFFFFF' : '#0E1A24',
            opacity: getOpacity('{{ $p['id'] }}')
        }">
    <span class="kv-product-tab-icon"
          :style="{
              background: product === '{{ $p['id'] }}' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
              color: product === '{{ $p['id'] }}' ? '#2BD46A' : '#13803F'
          }">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            {!! $p['svg'] !!}
        </svg>
    </span>
    <span style="flex-grow: 1; display: flex; flex-direction: column; gap: 4px; min-width: 0; text-align: left">
        <span style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap">
            <span class="kv-product-tab-name">{{ $p['name'] }}</span>
            @if($p['soon'])
                <span class="kv-product-tab-badge">{{ t('kouventa.COMING SOON', 'COMING SOON') }}</span>
            @endif
        </span>
        <span class="kv-product-tab-role" x-show="hasRole('{{ $p['id'] }}')" x-text="getRole('{{ $p['id'] }}')"></span>
        <span class="kv-product-tab-role" x-show="!hasRole('{{ $p['id'] }}')">{{ $p['tag'] }}</span>
    </span>
    <span class="kv-product-tab-dot"
          :style="{
              background: hasRole('{{ $p['id'] }}') ? (product === '{{ $p['id'] }}' ? '#2BD46A' : '#13803F') : 'transparent'
          }"></span>
</button>
@endforeach

</div>

<!-- Product Detail Panels -->
<div role="tabpanel" class="kv-product-panel">

<!-- Left info column -->
<div class="kv-product-info-col">

<!-- Wikipedia Info -->
<div class="kv-product-info" x-show="product === 'wiki'">
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">AI Company Wikipedia</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.An AI-powered chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.', 'An AI-powered chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Reliable answers, faster', 'Reliable answers, faster') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Ask a question and get the right information in seconds.', 'Ask a question and get the right information in seconds.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Source citations', 'Source citations') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Every answer is backed by sources for greater transparency.', 'Every answer is backed by sources for greater transparency.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.19+ data source formats', '19+ data source formats') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Search across documents, databases, websites, and business systems.', 'Search across documents, databases, websites, and business systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Human handover', 'Human handover') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Seamlessly hand conversations over to human agents when needed.', 'Seamlessly hand conversations over to human agents when needed.') }}</span></span>
        </div>
    </div>
</div>

<!-- Playground Info -->
<div class="kv-product-info" x-show="product === 'playground'" x-cloak>
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">Chat Playground</span>
    <span style="font-size: 13px; font-weight: 700; color: #13803F">{{ t('kouventa.Works with Wikipedia & Task Force', 'Works with Wikipedia & Task Force') }}</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Chat Playground is your controlled workspace to chat with AI Company Wikipedia, and to trigger, run, and monitor AI Task Force agents, securely, efficiently, and within your boundaries.', 'Chat Playground is your controlled workspace to chat with AI Company Wikipedia, and to trigger, run, and monitor AI Task Force agents, securely, efficiently, and within your boundaries.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Trigger agentic AI workflows', 'Trigger agentic AI workflows') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Analyze, act, and deliver results across your existing tools, including email, Slack, dashboards, and more.', 'Analyze, act, and deliver results across your existing tools, including email, Slack, dashboards, and more.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Controlled runners & agents', 'Controlled runners & agents') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Use only what admins have authorized, such as approved runners and permitted agents.', 'Use only what admins have authorized, such as approved runners and permitted agents.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Role-based hierarchy access', 'Role-based hierarchy access') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Give the right access to the right people based on role, hierarchy, and department.', 'Give the right access to the right people based on role, hierarchy, and department.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Cost control & budget management', 'Cost control & budget management') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Track usage and spending in real time with per-user budgets, limits, and overage alerts.', 'Track usage and spending in real time with per-user budgets, limits, and overage alerts.') }}</span></span>
        </div>
    </div>
</div>

<!-- Task Force Info -->
<div class="kv-product-info" x-show="product === 'taskforce'" x-cloak>
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">AI Task Force</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.An Agentic AI-powered solution that understands your business objectives and executes complex workflows across connected business systems, reasoning through tasks and seeking your approval at critical steps.', 'An Agentic AI-powered solution that understands your business objectives and executes complex workflows across connected business systems, reasoning through tasks and seeking your approval at critical steps.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.End-to-end automation', 'End-to-end automation') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Automate business processes across multiple systems.', 'Automate business processes across multiple systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Context-aware analysis', 'Context-aware analysis') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Analyze context and data to determine the best course of action.', 'Analyze context and data to determine the best course of action.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Multi-agent collaboration', 'Multi-agent collaboration') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Specialized agents work together to complete complex tasks.', 'Specialized agents work together to complete complex tasks.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">MCP connectors</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Execute actions across ERP, CRM, OMS, HRIS, and more.', 'Execute actions across ERP, CRM, OMS, HRIS, and more.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Human approval gates', 'Human approval gates') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Review and approve critical actions before they run.', 'Review and approve critical actions before they run.') }}</span></span>
        </div>
    </div>
</div>

<!-- TaskO Info -->
<div class="kv-product-info" x-show="product === 'workflow'" x-cloak>
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap">
        <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">TaskO</span>
        <span style="font-size: 12px; font-weight: 800; letter-spacing: 0.06em; padding: 6px 10px; border-radius: 999px; background: #FFE9C7; color: #7A4A00">{{ t('kouventa.COMING SOON', 'COMING SOON') }}</span>
    </div>
    <span style="font-size: 13px; font-weight: 700; color: #13803F">{{ t('kouventa.Part of AI Task Force', 'Part of AI Task Force') }}</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.TaskO (Task Automation) lets businesses design and automate end-to-end processes by connecting tasks, AI agents, business systems, and data, without complex coding.', 'TaskO (Task Automation) lets businesses design and automate end-to-end processes by connecting tasks, AI agents, business systems, and data, without complex coding.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Visual drag-and-drop builder', 'Visual drag-and-drop builder') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Design workflows visually using pre-built blocks and connectors.', 'Design workflows visually using pre-built blocks and connectors.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.No-code agent builder', 'No-code agent builder') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Configure role-specific AI agents, define behavior, set guardrails, and test in real scenarios.', 'Configure role-specific AI agents, define behavior, set guardrails, and test in real scenarios.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Connect your systems & data', 'Connect your systems & data') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Integrate with databases, APIs, SaaS apps, and enterprise systems.', 'Integrate with databases, APIs, SaaS apps, and enterprise systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ t('kouventa.Guardrails & approvals', 'Guardrails & approvals') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ t('kouventa.Add validations, human approvals, and policies to ensure safe and compliant execution.', 'Add validations, human approvals, and policies to ensure safe and compliant execution.') }}</span></span>
        </div>
    </div>
</div>

</div>

<!-- Right animated mockup column -->
<div class="kv-product-mockup-col">
<div role="img" class="kv-product-mockup-container">
<div class="kv-mockup-wrap" x-show="pOn">

<!-- Mockup Wiki -->
<div class="kv-mockup-wiki" x-show="product === 'wiki'">
<div style="background: #071C33; color: #FFFFFF; padding: 14px 16px; display: flex; align-items: center; gap: 10px"><span style="width: 28px; height: 28px; border-radius: 8px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">K</span><span style="font-size: 14px; font-weight: 700">Company Wikipedia</span></div>
<div style="padding: 16px; display: flex; flex-direction: column; gap: 12px; font-size: 13px; line-height: 1.5">
<div style="align-self: flex-end; max-width: 80%; background: #071C33; color: #FFFFFF; padding: 10px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .4s ease .2s both">{{ t('kouventa.How do I claim travel expenses?', 'How do I claim travel expenses?') }}</div>
<div style="align-self: flex-start; max-width: 90%; background: #F4F6F5; padding: 10px 13px; border-radius: 4px 14px 14px 14px; animation: kvIn .4s ease 1.3s both">{{ t('kouventa.Submit receipts in the finance portal within 30 days. Claims are approved by your direct manager.', 'Submit receipts in the finance portal within 30 days. Claims are approved by your direct manager.') }}</div>
<div style="display: flex; gap: 6px; flex-wrap: wrap; animation: kvIn .4s ease 2.4s both">
<span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 7px; background: #DDF3E4; color: #0B5E2C">[1] Finance SOP.pdf</span>
<span style="font-size: 11px; font-weight: 700; padding: 5px 9px; border-radius: 7px; background: #DDF3E4; color: #0B5E2C">[2] Intranet · Claims</span>
</div>
<span style="align-self: stretch; text-align: center; font-size: 13px; font-weight: 700; padding: 11px; border-radius: 10px; animation: kvLight .5s ease 3.4s both">{{ t('kouventa.Hand over to human agent', 'Hand over to human agent') }}</span>
</div>
</div>

<!-- Mockup Taskforce -->
<div class="kv-mockup-taskforce" x-show="product === 'taskforce'" x-cloak>
<div style="background: #071C33; color: #FFFFFF; border-radius: 12px; padding: 10px 16px; font-size: 13px; animation: kvIn .4s ease .2s both"><span style="color: #2BD46A; font-weight: 700">{{ t('kouventa.Objective', 'Objective') }}</span> · {{ t('kouventa.Reduce overdue receivables', 'Reduce overdue receivables') }}</div>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; width: 100%">
<span style="background: #FFFFFF; border-radius: 10px; padding: 10px; text-align: center; font-size: 12px; font-weight: 700; animation: kvIn .4s ease 1s both">{{ t('kouventa.Analyst agent', 'Analyst agent') }}</span>
<span style="background: #FFFFFF; border-radius: 10px; padding: 10px; text-align: center; font-size: 12px; font-weight: 700; animation: kvIn .4s ease 1.2s both">{{ t('kouventa.Planner agent', 'Planner agent') }}</span>
<span style="background: #FFFFFF; border-radius: 10px; padding: 10px; text-align: center; font-size: 12px; font-weight: 700; animation: kvIn .4s ease 1.4s both">{{ t('kouventa.Executor agent', 'Executor agent') }}</span>
</div>
<div style="height: 2px; width: 80%; background: #DCE3E0; position: relative; overflow: hidden"><span style="position: absolute; left: 0; top: 0; height: 2px; background: #13803F; --w: 100%; animation: kvGrowW .8s ease 1.8s both"></span></div>
<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; width: 100%">
<span style="padding: 8px 0; text-align: center; border-radius: 8px; background: #E3E8E6; font-size: 11px; font-weight: 800; animation: kvPopIn .3s ease 2.0s both">ERP</span>
<span style="padding: 8px 0; text-align: center; border-radius: 8px; background: #E3E8E6; font-size: 11px; font-weight: 800; animation: kvPopIn .3s ease 2.2s both">CRM</span>
<span style="padding: 8px 0; text-align: center; border-radius: 8px; background: #E3E8E6; font-size: 11px; font-weight: 800; animation: kvPopIn .3s ease 2.4s both">OMS</span>
<span style="padding: 8px 0; text-align: center; border-radius: 8px; background: #E3E8E6; font-size: 11px; font-weight: 800; animation: kvPopIn .3s ease 2.6s both">HRIS</span>
</div>
<div style="width: 100%; box-sizing: border-box; background: #FFF6E5; border: 1px solid #F2C46D; border-radius: 12px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; animation: kvIn .4s ease 3.1s both">
<span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 10px; font-weight: 800; letter-spacing: 0.08em; color: #7A4A00">{{ t('kouventa.APPROVAL GATE', 'APPROVAL GATE') }}</span><span style="font-size: 13px; font-weight: 700">{{ t('kouventa.Send payment reminders?', 'Send payment reminders?') }}</span></span>
<span style="display: grid"><span style="grid-area: 1 / 1; font-size: 12px; font-weight: 700; padding: 7px 12px; border-radius: 8px; background: #071C33; color: #FFFFFF">{{ t('kouventa.Approve', 'Approve') }}</span><span style="grid-area: 1 / 1; font-size: 12px; font-weight: 700; padding: 7px 10px; border-radius: 8px; background: #13803F; color: #FFFFFF; text-align: center; animation: kvPopIn .3s ease 4s both">{{ t('kouventa.Approved', 'Approved') }}</span></span>
</div>
<span style="display: flex; align-items: center; gap: 8px; background: #071C33; color: #FFFFFF; padding: 9px 16px; border-radius: 999px; font-size: 13px; font-weight: 700; animation: kvPopIn .4s ease 4.8s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>{{ t('kouventa.Done · result sent to Finance', 'Done · result sent to Finance') }}</span>
</div>

<!-- Mockup Playground -->
<div class="kv-mockup-playground" x-show="product === 'playground'" x-cloak>
<div style="display: flex; justify-content: space-between; align-items: center"><span style="font-size: 14px; font-weight: 800">Chat Playground</span><span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Finance team', 'Finance team') }}</span></div>
<div style="display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Permitted agents', 'Permitted agents') }}</span>
<div style="display: grid">
<span style="grid-area: 1 / 1; padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; font-size: 13px; font-weight: 600">Invoice Reconciliation Agent</span>
<span style="grid-area: 1 / 1; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #13803F; background: #DDF3E4; font-size: 13px; font-weight: 700; animation: kvIn .3s ease .6s both">Invoice Reconciliation Agent</span>
</div>
<span style="padding: 10px 12px; border-radius: 10px; border: 1px solid #E3E8E6; font-size: 13px; font-weight: 600; color: #4A5866">Cash Flow Report Agent</span>
</div>
<div style="display: flex; align-items: center; justify-content: space-between">
<span style="padding: 10px 18px; border-radius: 10px; background: #071C33; color: #FFFFFF; font-size: 13px; font-weight: 700; animation: kvPress .3s ease 1.1s 1 both">Trigger</span>
<span style="display: grid; font-size: 12px; font-weight: 800">
<span style="grid-area: 1 / 1; padding: 6px 12px; border-radius: 999px; background: #E3E8E6; color: #33414E; animation: kvOut .2s ease 1.4s 1 forwards">{{ t('kouventa.Ready', 'Ready') }}</span>
<span style="grid-area: 1 / 1; padding: 6px 12px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; display: flex; align-items: center; gap: 6px; animation: kvIn .2s ease 1.5s both, kvOut .2s ease 3.4s 1 forwards"><span style="width: 6px; height: 6px; border-radius: 50%; background: #C77D00; animation: kvBlink .8s ease infinite"></span>{{ t('kouventa.Running', 'Running') }}</span>
<span style="grid-area: 1 / 1; padding: 6px 12px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 3.6s both">{{ t('kouventa.Completed', 'Completed') }}</span>
</span>
</div>
<div style="display: flex; flex-direction: column; gap: 6px; padding-top: 12px; border-top: 1px solid #E3E8E6">
<div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: #4A5866"><span>{{ t('kouventa.Monthly AI budget', 'Monthly AI budget') }}</span><span>{{ t('kouventa.Within limit', 'Within limit') }}</span></div>
<div style="height: 10px; border-radius: 999px; background: #EEF2F0; overflow: hidden"><div style="height: 10px; border-radius: 999px; background: #13803F; --w: 42%; width: 28%; animation: kvGrowW 1s ease 3.8s both"></div></div>
</div>
</div>

<!-- Mockup TaskO -->
<div class="kv-mockup-workflow" x-show="product === 'workflow'" x-cloak>
<div style="position: absolute; inset: 0; background-color: #071C33; background-image: radial-gradient(rgba(255,255,255,0.09) 1px, transparent 1px); background-size: 18px 18px"></div>
<span style="position: absolute; top: 14px; right: 14px; z-index: 3; font-size: 11px; font-weight: 800; letter-spacing: 0.06em; padding: 6px 10px; border-radius: 999px; background: #FFE9C7; color: #7A4A00">{{ t('kouventa.COMING SOON', 'COMING SOON') }}</span>
<span style="position: absolute; top: 16px; left: 16px; z-index: 3; display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #93A4B6"><span style="width: 7px; height: 7px; border-radius: 50%; background: #2BD46A; animation: kvBlink 1s ease 5 both"></span>TaskO · Invoice approval flow</span>
<div style="position: absolute; left: 0; top: 30px; width: 440px; height: 440px">
<svg width="440" height="440" viewBox="0 0 440 440" fill="none" style="position: absolute; inset: 0">
<path d="M136 70 C148 70 148 70 160 70" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .45s ease 0.5s both"></path>
<path d="M280 70 C292 70 292 70 304 70" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .45s ease 1.3s both"></path>
<path d="M364 100 C364 150 364 150 364 200" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .45s ease 2.0s both"></path>
<path d="M320 100 C300 220 220 230 220 330" stroke="#5B6875" stroke-width="2" fill="none" stroke-dasharray="5 5" style="animation: kvFade .4s ease 2.0s both"></path>
<path d="M364 260 C364 330 330 360 280 360" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .45s ease 3.6s both"></path>
<path d="M190 100 L190 118" stroke="#5B6875" stroke-width="1.5" stroke-dasharray="3 3"></path>
<path d="M252 100 L252 118" stroke="#5B6875" stroke-width="1.5" stroke-dasharray="3 3"></path>
</svg>
<span style="position: absolute; left: 0; top: 0; width: 8px; height: 8px; margin: -4px 0 0 -4px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 10px #2BD46A; offset-path: path('M136 70 C148 70 148 70 160 70'); offset-rotate: 0deg; animation: kvPath .7s linear 0.5s 1 both"></span><span style="position: absolute; left: 0; top: 0; width: 8px; height: 8px; margin: -4px 0 0 -4px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 10px #2BD46A; offset-path: path('M280 70 C292 70 292 70 304 70'); offset-rotate: 0deg; animation: kvPath .7s linear 1.3s 1 both"></span><span style="position: absolute; left: 0; top: 0; width: 8px; height: 8px; margin: -4px 0 0 -4px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 10px #2BD46A; offset-path: path('M364 100 C364 150 364 150 364 200'); offset-rotate: 0deg; animation: kvPath .7s linear 2.0s 1 both"></span><span style="position: absolute; left: 0; top: 0; width: 8px; height: 8px; margin: -4px 0 0 -4px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 10px #2BD46A; offset-path: path('M364 260 C364 330 330 360 280 360'); offset-rotate: 0deg; animation: kvPath .7s linear 3.6s 1 both"></span>
<span style="position: absolute; left: 372px; top: 140px; font-size: 10px; font-weight: 800; color: #2BD46A; animation: kvIn .3s ease 2.1s both">true</span>
<span style="position: absolute; left: 236px; top: 196px; font-size: 10px; font-weight: 800; color: #93A4B6; animation: kvIn .3s ease 2.1s both">false</span>
<div style="position: absolute; left: 16px; top: 40px; width: 120px; height: 60px; box-sizing: border-box; background: #0F3357; border: 1px solid rgba(255,255,255,0.16); border-radius: 14px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.25); animation: kvPopIn .35s ease 0.2s both">
<span style="width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: #7C5CFF; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"></path></svg></span>
<span style="display: flex; flex-direction: column; min-width: 0"><span style="font-size: 11.5px; font-weight: 800; color: #FFFFFF; line-height: 1.2">Trigger</span><span style="font-size: 10px; color: #93A4B6; line-height: 1.3">{{ t('kouventa.New invoice', 'New invoice') }}</span></span>
<span style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 0.5s both"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#06251A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span style="position: absolute; left: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
<span style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
</div>
<div style="position: absolute; left: 160px; top: 40px; width: 120px; height: 60px; box-sizing: border-box; background: #0F3357; border: 1px solid rgba(255,255,255,0.16); border-radius: 14px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.25); animation: kvPopIn .35s ease 0.9s both">
<span style="width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: #13803F; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="7" width="14" height="12" rx="3"></rect><path d="M12 7V4M9 12h.01M15 12h.01M9 16h6"></path></svg></span>
<span style="display: flex; flex-direction: column; min-width: 0"><span style="font-size: 11.5px; font-weight: 800; color: #FFFFFF; line-height: 1.2">AI Agent</span><span style="font-size: 10px; color: #93A4B6; line-height: 1.3">{{ t('kouventa.Check & match PO', 'Check & match PO') }}</span></span>
<span style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 1.3s both"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#06251A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span style="position: absolute; left: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
<span style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
</div>
<span style="position: absolute; left: 164px; top: 118px; font-size: 9.5px; font-weight: 700; padding: 4px 7px; border-radius: 6px; background: rgba(255,255,255,0.08); color: #D5DEE8; animation: kvIn .3s ease 1.1s both">Model</span>
<span style="position: absolute; left: 222px; top: 118px; font-size: 9.5px; font-weight: 700; padding: 4px 7px; border-radius: 6px; background: rgba(255,255,255,0.08); color: #D5DEE8; animation: kvIn .3s ease 1.2s both">ERP tool</span>
<div style="position: absolute; left: 304px; top: 40px; width: 120px; height: 60px; box-sizing: border-box; background: #0F3357; border: 1px solid rgba(255,255,255,0.16); border-radius: 14px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.25); animation: kvPopIn .35s ease 1.6s both">
<span style="width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: #C77D00; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v12M18 9v12M6 15a6 6 0 0 0 6 6h6M18 9a6 6 0 0 0-6-6H6"></path></svg></span>
<span style="display: flex; flex-direction: column; min-width: 0"><span style="font-size: 11.5px; font-weight: 800; color: #FFFFFF; line-height: 1.2">If</span><span style="font-size: 10px; color: #93A4B6; line-height: 1.3">{{ t('kouventa.Amount > Rp 50 jt?', 'Amount > Rp 50 jt?') }}</span></span>
<span style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.0s both"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#06251A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span style="position: absolute; left: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
<span style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
</div>
<div style="position: absolute; left: 304px; top: 200px; width: 120px; height: 60px; box-sizing: border-box; background: #0F3357; border: 1px solid rgba(255,255,255,0.16); border-radius: 14px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.25); animation: kvPopIn .35s ease 2.4s both">
<span style="width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: #B4412A; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg></span>
<span style="display: flex; flex-direction: column; min-width: 0"><span style="font-size: 11.5px; font-weight: 800; color: #FFFFFF; line-height: 1.2">{{ t('kouventa.Human approval', 'Human approval') }}</span><span style="font-size: 10px; color: #93A4B6; line-height: 1.3">{{ t('kouventa.Finance manager', 'Finance manager') }}</span></span><span style="position: absolute; left: 10px; bottom: -22px; display: grid; font-size: 10px; font-weight: 800"><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #FFFFFF; color: #071C33; animation: kvIn .25s ease 2.7s both, kvOut .2s ease 3.3s 1 forwards">{{ t('kouventa.Waiting…', 'Waiting…') }}</span><span style="grid-area: 1 / 1; padding: 3px 8px; border-radius: 999px; background: #2BD46A; color: #06251A; animation: kvPopIn .3s ease 3.4s both">{{ t('kouventa.Approved', 'Approved') }}</span></span>
<span style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.5s both"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#06251A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span style="position: absolute; left: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
<span style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
</div>
<div style="position: absolute; left: 160px; top: 330px; width: 120px; height: 60px; box-sizing: border-box; background: #0F3357; border: 1px solid rgba(255,255,255,0.16); border-radius: 14px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.25); animation: kvPopIn .35s ease 4.0s both">
<span style="width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: #1D4F91; display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="8" ry="3"></ellipse><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"></path></svg></span>
<span style="display: flex; flex-direction: column; min-width: 0"><span style="font-size: 11.5px; font-weight: 800; color: #FFFFFF; line-height: 1.2">{{ t('kouventa.Post to ERP', 'Post to ERP') }}</span><span style="font-size: 10px; color: #93A4B6; line-height: 1.3">{{ t('kouventa.Journal entry', 'Journal entry') }}</span></span>
<span style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 4.4s both"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#06251A" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
<span style="position: absolute; left: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
<span style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #071C33; border: 2px solid #5B6875; box-sizing: border-box"></span>
</div>
</div>
</div>

</div>
</div>

<button type="button" @click="replayProduct()" class="kv-product-replay-btn" style="min-height: 40px; padding: 8px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #4A5866; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>
    {{ t('kouventa.Replay', 'Replay') }}
</button>
</div>
</div>

</div>
</section>

<!-- ============ PLATFORM CONTROL ============ -->
<section style="padding: 0 80px 104px; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Govern AI across your organization.', 'Govern AI across your organization.') }}</h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px">
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="M9 12h6"></path></svg>
<span style="font-size: 19px; font-weight: 700">AI Guardrails</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Control what AI can access, process, say, and do based on defined rules and boundaries, including masking sensitive personally identifiable information (PII).', 'Control what AI can access, process, say, and do based on defined rules and boundaries, including masking sensitive personally identifiable information (PII).') }}</span>
</div>
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
<span style="font-size: 19px; font-weight: 700">{{ t('kouventa.Role-Based Access', 'Role-Based Access') }}</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Control access to AI resources and capabilities by user role, hierarchy, and permissions.', 'Control access to AI resources and capabilities by user role, hierarchy, and permissions.') }}</span>
</div>
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"></path></svg>
<span style="font-size: 19px; font-weight: 700">{{ t('kouventa.Usage Analytics', 'Usage Analytics') }}</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Track AI usage and activity to understand adoption, usage patterns, and resource consumption.', 'Track AI usage and activity to understand adoption, usage patterns, and resource consumption.') }}</span>
</div>
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0-4 10.5V17h8v-3.5A6 6 0 0 0 12 3Z"></path><path d="M9 21h6"></path></svg>
<span style="font-size: 19px; font-weight: 700">AI Learning</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Use feedback and corrections to continuously improve AI responses and performance.', 'Use feedback and corrections to continuously improve AI responses and performance.') }}</span>
</div>
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4h10a1 1 0 0 1 1 1v15H8a3 3 0 0 1-3-3V8"></path><path d="M5 8a3 3 0 0 1 3-4M11 9h6M11 13h6M11 17h4"></path></svg>
<span style="font-size: 19px; font-weight: 700">Audit Trail Logs</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Record user activities, access, actions, and outcomes for traceability and governance.', 'Record user activities, access, actions, and outcomes for traceability and governance.') }}</span>
</div>
<div class="kv-hover" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="m9 15 2 2 4-4"></path></svg>
<span style="font-size: 19px; font-weight: 700">{{ t('kouventa.Knowledge Change Management', 'Knowledge Change Management') }}</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A5866">{{ t('kouventa.Require changes to AI resources to go through review and approval before they are applied.', 'Require changes to AI resources to go through review and approval before they are applied.') }}</span>
</div>
</div>
<div class="kv-hover kv-integrate-card" style="background: #071C33; color: #FFFFFF; border: 1px solid #071C33; border-radius: 18px; padding: 28px 0; display: flex; flex-direction: column; gap: 20px; overflow: hidden">
<div class="kv-integrate-header" style="padding: 0 28px; display: flex; justify-content: space-between; align-items: baseline; gap: 24px; flex-wrap: wrap">
<span style="font-size: 19px; font-weight: 700">{{ t('kouventa.Integrate with Any Channel', 'Integrate with Any Channel') }}</span>
<span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Plug your AI into any app or channel your teams and customers already use.', 'Plug your AI into any app or channel your teams and customers already use.') }}</span>
</div>
<div aria-label="Example channels: WhatsApp, Email, Slack, Microsoft Teams, Telegram, SAP, Salesforce, internal apps" role="img" style="overflow: hidden">
<div style="display: flex; gap: 14px; width: max-content; animation: kvMarquee 40s linear infinite">
@php
$channels = [
    ['name' => 'WhatsApp', 'icon' => 'images/channels/whatsapp.svg'],
    ['name' => 'Email', 'icon' => 'images/channels/email.svg'],
    ['name' => 'Slack', 'icon' => 'images/channels/slack.svg'],
    ['name' => 'Microsoft Teams', 'icon' => 'images/channels/teams.svg'],
    ['name' => 'Telegram', 'icon' => 'images/channels/telegram.svg'],
    ['name' => 'SAP', 'icon' => 'images/channels/sap.svg'],
    ['name' => 'Salesforce', 'icon' => 'images/channels/salesforce.svg'],
    ['name' => t('kouventa.Internal apps', 'Internal apps'), 'icon' => 'images/channels/internal-apps.svg'],
];
$channelsDbl = array_merge($channels, $channels);
@endphp
@foreach($channelsDbl as $ch)
<span class="kv-channel-card" style="width: 168px; height: 96px; flex-shrink: 0; box-sizing: border-box; padding: 12px 14px; border-radius: 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease">
    <img src="{{ theme_asset($ch['icon']) }}" alt="{{ $ch['name'] }}" style="width: 32px; height: 32px; object-fit: contain; display: block" loading="lazy">
    <span style="font-size: 13px; font-weight: 700; color: #D5DEE8; text-align: center; line-height: 1.3">{{ $ch['name'] }}</span>
</span>
@endforeach
</div>
</div>
</div>
</section>

<!-- ============ WHY ============ -->
<section class="kv-why-section">
<div aria-hidden="true" style="position: absolute; left: -200px; bottom: -320px; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07); pointer-events: none"></div>
<div aria-hidden="true" style="position: absolute; right: -160px; top: -260px; width: 560px; height: 560px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07); pointer-events: none"></div>
<h2 class="kv-why-heading">{{ t('kouventa.What held your AI back,', 'What held your AI back,') }} <span style="color: #2BD46A">{{ t('kouventa.now solved.', 'now solved.') }}</span></h2>
<div class="kv-why-grid">
<div class="kv-why-card">
<span class="kv-why-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect></svg></span>
<span class="kv-why-title">Multi-Model AI</span>
<span class="kv-why-desc">{{ t('kouventa.Access multiple AI models and choose the right model for each business need, all in one platform.', 'Access multiple AI models and choose the right model for each business need, all in one platform.') }}</span>
<span class="kv-why-badge">{{ t('kouventa.Solves: scattered AI tools', 'Solves: scattered AI tools') }}</span>
</div>
<div class="kv-why-card">
<span class="kv-why-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"></path><circle cx="16" cy="6" r="2"></circle><circle cx="10" cy="12" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg></span>
<span class="kv-why-title">{{ t('kouventa.Controlled AI', 'Controlled AI') }}</span>
<span class="kv-why-desc">{{ t('kouventa.Control how AI operates across multiple business processes with defined permissions, workflows, and execution rules.', 'Control how AI operates across multiple business processes with defined permissions, workflows, and execution rules.') }}</span>
<span class="kv-why-badge">{{ t('kouventa.Solves: limited control', 'Solves: limited control') }}</span>
</div>
<div class="kv-why-card">
<span class="kv-why-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
<span class="kv-why-title">{{ t('kouventa.Secure AI', 'Secure AI') }}</span>
<span class="kv-why-desc">{{ t('kouventa.Keep AI processing and deployment secure with data protection measures such as PII masking, while keeping sensitive business data under your control.', 'Keep AI processing and deployment secure with data protection measures such as PII masking, while keeping sensitive business data under your control.') }}</span>
<span class="kv-why-badge">{{ t('kouventa.Solves: limited control', 'Solves: limited control') }}</span>
</div>
<div class="kv-why-card">
<span class="kv-why-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18M7 15h4"></path></svg></span>
<span class="kv-why-title">{{ t('kouventa.Budget Management', 'Budget Management') }}</span>
<span class="kv-why-desc">{{ t('kouventa.Monitor and control AI usage and spending with budgets per user, usage limits, and overage alerts.', 'Monitor and control AI usage and spending with budgets per user, usage limits, and overage alerts.') }}</span>
<span class="kv-why-badge">{{ t('kouventa.Solves: hard-to-manage billing', 'Solves: hard-to-manage billing') }}</span>
</div>
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 104px 80px">
<div class="kv-panel" style="background: #2BD46A; border-radius: 28px; padding: 72px 64px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 32px; color: #06251A">
<h2 style="margin: 0; font-size: 44px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">{{ $bottomCtaTitle }}</h2>
<p style="margin: 0; font-size: 18px; line-height: 1.55; max-width: 680px">{!! nl2br(e($bottomCtaSubtitle)) !!}</p>
<div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap">
<a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-navy" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap; text-decoration: none">{{ t('kouventa.Book a demo', 'Book a demo') }}</a>
<a href="{{ localized_url('/pricing') }}" class="kv-btn-action kv-btn-outline" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap; text-decoration: none">{{ t('kouventa.See pricing', 'See pricing') }}</a>
</div>
</div>
</section>

</div>

@push('scripts')
<script>
(() => {
    const initProductAi = () => {
        if (!window.Alpine) return;
        Alpine.data('productAiPage', () => ({
            stage: 0,
            product: 'wiki',
            linked: false,
            heroOn: true,
            jOn: true,
            pOn: true,
            stages: @json($stages),
            roles: @json($stageRoles),
            replayHero() {
                this.heroOn = false;
                setTimeout(() => { this.heroOn = true; }, 40);
            },
            replayJourney() {
                this.jOn = false;
                setTimeout(() => { this.jOn = true; }, 40);
            },
            replayProduct() {
                this.pOn = false;
                setTimeout(() => { this.pOn = true; }, 40);
            },
            pickStage(idx) {
                if (this.linked && this.stage === idx) {
                    this.linked = false;
                    return;
                }
                this.stage = idx;
                this.product = this.stages[idx].defaultProduct;
                this.linked = true;
                this.replayJourney();
                this.replayProduct();
            },
            seeTools(prod) {
                this.linked = true;
                this.product = prod;
                this.replayProduct();
                this.$nextTick(() => {
                    const el = document.getElementById('ai-products');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            },
            getRole(prodId) {
                if (!this.linked) return null;
                const stageKey = this.stages[this.stage].key;
                return (this.roles[stageKey] && this.roles[stageKey][prodId]) ? this.roles[stageKey][prodId] : null;
            },
            hasRole(prodId) {
                return this.getRole(prodId) !== null;
            },
            getOpacity(prodId) {
                if (!this.linked || this.hasRole(prodId) || this.product === prodId) return 1;
                return 0.6;
            }
        }));
    };

    if (window.Alpine) {
        initProductAi();
    } else {
        document.addEventListener('alpine:init', initProductAi);
    }
})();
</script>
@endpush
@endsection
