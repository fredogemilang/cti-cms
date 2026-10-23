@extends('kouventa::layouts.app')

@push('styles')
<style>
.kv-stage-wrap { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.kv-stage-0 { width: 380px; display: flex; flex-direction: column; gap: 12px; font-size: 14px; line-height: 1.5; }
.kv-stage-1 { width: 470px; display: flex; align-items: center; }
.kv-stage-2 { width: 420px; display: flex; flex-direction: column; gap: 16px; }
.kv-product-info { display: flex; flex-direction: column; gap: 16px; }
.kv-mockup-wrap { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; }
.kv-mockup-wiki { width: 380px; max-width: 100%; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column; }
.kv-mockup-taskforce { width: 400px; max-width: 100%; display: flex; flex-direction: column; align-items: center; gap: 16px; }
.kv-mockup-playground { width: 410px; max-width: 100%; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: grid; grid-template-columns: 118px minmax(0, 1fr); font-size: 11px; color: #0E1A24; }
.kv-mockup-workflow { width: 100%; height: 100%; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }

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
    display: inline-flex;
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
    display: inline-flex;
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

/* ============================================================
   RESPONSIVE MEDIA QUERIES FOR HERO & JOURNEY SECTIONS (MOBILE)
   ============================================================ */
@media (max-width: 768px) {
    /* Hero section */
    .kv-hero-section {
        height: auto !important;
        min-height: auto !important;
        padding: 24px 20px 32px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 20px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: clip !important;
    }
    .kv-hero-content {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
        box-sizing: border-box !important;
    }
    .kv-hero-content h1 {
        font-size: clamp(28px, 7vw, 36px) !important;
        line-height: 1.15 !important;
        letter-spacing: -0.025em !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        max-width: 100% !important;
    }
    .kv-hero-content p {
        font-size: 15px !important;
        line-height: 1.5 !important;
        color: #B9C6D3 !important;
        max-width: 100% !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
    }
    .kv-hero-content div[style*="display: flex; gap: 12px"] {
        flex-direction: column !important;
        width: 100% !important;
        gap: 10px !important;
    }
    .kv-hero-content div[style*="display: flex; gap: 12px"] a {
        width: 100% !important;
        box-sizing: border-box !important;
        text-align: center !important;
        justify-content: center !important;
        padding: 13px 20px !important;
        font-size: 15px !important;
        border-radius: 12px !important;
    }
    .kv-hero-diagram-wrap {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        box-sizing: border-box !important;
    }
    .kv-hero-diagram-canvas {
        width: 520px !important;
        max-width: none !important;
        height: 440px !important;
        flex-shrink: 0 !important;
        transform: scale(0.64) !important;
        transform-origin: top center !important;
        margin: 0 auto -155px !important;
    }
    .kv-hero-tools-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }

    /* Journey Section */
    .kv-journey-section {
        padding: 56px 20px 0 !important;
        gap: 20px !important;
    }
    .kv-journey-section h2 {
        font-size: 32px !important;
        line-height: 1.15 !important;
    }
    .kv-journey-section p {
        font-size: 15px !important;
        line-height: 1.5 !important;
    }

    /* Journey Tablist Track */
    .kv-journey-tablist-wrap {
        padding: 18px 8px 16px !important;
        border-radius: 18px !important;
    }
    .kv-journey-track-container {
        height: 104px !important;
    }
    .kv-journey-track-bg,
    .kv-journey-track-fill {
        top: 36px !important;
        height: 3px !important;
    }
    .kv-journey-pointer {
        display: none !important;
    }

    /* Journey Tab Buttons */
    .kv-journey-tab-btn {
        top: 14px !important;
        width: 86px !important;
        gap: 3px !important;
        padding: 0 !important;
    }
    .kv-journey-tab-btn > span:first-child {
        width: 44px !important;
        height: 44px !important;
    }
    .kv-journey-tab-btn > span:first-child svg {
        width: 20px !important;
        height: 20px !important;
    }
    .kv-journey-tab-btn > span:nth-child(2) {
        font-size: 10px !important;
    }
    .kv-journey-tab-btn > span:last-child {
        font-size: 13.5px !important;
        line-height: 1.15 !important;
    }

    /* Journey Tabpanel Card */
    .kv-journey-tabpanel {
        padding: 24px 16px !important;
        border-radius: 18px !important;
        grid-template-columns: 1fr !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 24px !important;
    }
    .kv-journey-tabpanel span[x-text*="headline"] {
        font-size: 24px !important;
        line-height: 1.2 !important;
    }
    .kv-journey-tabpanel span[x-text*="sub"] {
        font-size: 14px !important;
        line-height: 1.55 !important;
    }
    .kv-journey-tabpanel .kv-btn-stage-action {
        width: 100% !important;
        justify-content: center !important;
        font-size: 14px !important;
        padding: 12px 18px !important;
        min-height: 46px !important;
    }
    .kv-journey-tabpanel .kv-btn-replay {
        width: auto !important;
        align-self: flex-end !important;
    }

    /* Journey Stages Animation Viewport */
    .kv-journey-stage-screen,
    .kv-journey-tabpanel div[role="img"] {
        height: 340px !important;
        width: 100% !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .kv-stage-wrap {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
    }

    /* Stage 0: Answer */
    .kv-stage-0 {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 14px !important;
        box-sizing: border-box !important;
        font-size: 12.5px !important;
        line-height: 1.4 !important;
    }
    .kv-stage-0 > div:first-child {
        max-width: 85% !important;
        font-size: 12.5px !important;
        padding: 9px 12px !important;
    }
    .kv-stage-0 > div:last-child > div:last-child {
        max-width: 100% !important;
        padding: 11px 13px !important;
    }

    /* Stage 1: Analyze (470px scaled uniformly to preserve columns & chart) */
    .kv-stage-1 {
        width: 470px !important;
        max-width: none !important;
        flex-shrink: 0 !important;
        display: flex;
        align-items: center !important;
        transform: scale(0.66) !important;
        transform-origin: center center !important;
    }

    /* Stage 2: Execute (420px scaled uniformly) */
    .kv-stage-2 {
        width: 420px !important;
        max-width: none !important;
        flex-shrink: 0 !important;
        display: flex;
        flex-direction: column !important;
        gap: 16px !important;
        transform: scale(0.72) !important;
        transform-origin: center center !important;
    }

    .kv-stage-0[style*="display: none"],
    .kv-stage-1[style*="display: none"],
    .kv-stage-2[style*="display: none"],
    .kv-stage-0[style*="display:none"],
    .kv-stage-1[style*="display:none"],
    .kv-stage-2[style*="display:none"] {
        display: none !important;
    }
}

@media (max-width: 380px) {
    .kv-hero-diagram-canvas {
        transform: scale(0.56) !important;
        margin: 0 auto -190px !important;
    }
    .kv-stage-1 {
        transform: scale(0.58) !important;
    }
    .kv-stage-2 {
        transform: scale(0.64) !important;
    }
}
</style>
@endpush

@php
$isIdLocale = app()->getLocale() === 'id';

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
        'svg' => '<path d="M12 21a9 9 0 1 0-9-9c0 1.6.4 3.1 1.2 4.4L3 21l4.8-1.1A8.9 8.9 0 0 0 12 21Z"></path><path d="M9.8 9.2a2.3 2.3 0 0 1 4.4.8c0 1.5-2.2 2.2-2.2 3.5"></path><path d="M12 17h.01"></path>'
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
        'tag' => $isIdLocale ? 'Asisten pengetahuan' : t('kouventa.Knowledge assistant', 'Knowledge assistant'),
        'soon' => false,
        'svg' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path>'
    ],
    [
        'id' => 'taskforce',
        'name' => 'AI Task Force',
        'tag' => $isIdLocale ? 'Agentic AI' : t('kouventa.Agentic AI', 'Agentic AI'),
        'soon' => false,
        'svg' => '<circle cx="12" cy="7" r="3"></circle><circle cx="5" cy="17" r="2.5"></circle><circle cx="19" cy="17" r="2.5"></circle><path d="M10 9.5 6.5 15M14 9.5l3.5 5.5"></path>'
    ],
    [
        'id' => 'playground',
        'name' => 'Chat Playground',
        'tag' => $isIdLocale ? 'Bekerja dengan Wikipedia & Task Force' : t('kouventa.Works with Wikipedia & Task Force', 'Works with Wikipedia & Task Force'),
        'soon' => false,
        'svg' => '<path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"></path>'
    ],
    [
        'id' => 'workflow',
        'name' => 'TaskO',
        'tag' => $isIdLocale ? 'Bagian dari AI Task Force' : t('kouventa.Part of AI Task Force', 'Part of AI Task Force'),
        'soon' => true,
        'svg' => '<rect x="3" y="3" width="7" height="6" rx="1.5"></rect><rect x="14" y="15" width="7" height="6" rx="1.5"></rect><path d="M6.5 9v3a3 3 0 0 0 3 3H14"></path>'
    ]
];

// ── Page Blocks from CMS Content Builder ──────────────
$heroTitle = $page?->titleBlock('hero_title', [
    'prefix' => $isIdLocale ? 'Satu platform AI' : 'One AI platform is',
    'main' => $isIdLocale ? 'sudah cukup.' : 'all you need.'
]);
if ($isIdLocale) {
    if (($heroTitle['prefix'] ?? '') === 'Satu platform AI adalah') {
        $heroTitle['prefix'] = 'Satu platform AI';
    }
    if (($heroTitle['main'] ?? '') === 'semua yang Anda butuhkan.') {
        $heroTitle['main'] = 'sudah cukup.';
    }
}

$heroSubtitle = $page?->block('hero_subtitle', $isIdLocale
    ? 'Bangun AI agent dengan mudah dan gunakan untuk kebutuhan organisasi Anda, dengan kontrol yang terukur.'
    : 'Build AI agents with ease and put them to work on what your organization needs, with control you can measure.');
if ($isIdLocale && $heroSubtitle === 'Bangun AI agent dengan mudah dan pekerjakan untuk kebutuhan organisasi Anda, dengan kendali terukur.') {
    $heroSubtitle = 'Bangun AI agent dengan mudah dan gunakan untuk kebutuhan organisasi Anda, dengan kontrol yang terukur.';
}

$heroCta = $page?->buttonBlock('hero_cta', ['text' => 'Book a demo', 'url' => '/book-demo']);
$heroSecondaryCta = $page?->buttonBlock('hero_secondary_cta', ['text' => 'Explore products', 'url' => '#ai-products']);

$challengesTitle = $page?->titleBlock('challenges_title', [
    'prefix' => $isIdLocale ? 'Terlalu banyak tools AI,' : 'Too many AI tools,',
    'main' => $isIdLocale ? 'terlalu sedikit kontrol.' : 'too little control.'
]);
if ($isIdLocale) {
    if (($challengesTitle['prefix'] ?? '') === 'Terlalu banyak AI tool,') {
        $challengesTitle['prefix'] = 'Terlalu banyak tools AI,';
    }
    if (($challengesTitle['main'] ?? '') === 'terlalu minim kendali.') {
        $challengesTitle['main'] = 'terlalu sedikit kontrol.';
    }
}

$productsTitle = $page?->block('products_title', $isIdLocale ? 'Produk yang membuat Anda tetap memegang kendali.' : 'The products that put you in control.');
$bottomCtaTitle = $page?->block('bottom_cta_title', $isIdLocale ? 'Satu platform untuk semua kebutuhan AI organisasi Anda.' : 'Bring all your AI into one platform.');
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

<div class="kv-hero-section" style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 520px; gap: 64px; align-items: center; position: relative">
<div class="kv-hero-content" style="display: flex; flex-direction: column; gap: 26px">
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'One AI platform is' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'all you need.' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 600px">{!! nl2br(e($heroSubtitle)) !!}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" class="kv-btn-action kv-btn-primary" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ $heroCta['text'] ?? 'Book a demo' }}</a>
<a href="{{ $heroSecondaryCta['url'] ?? '#ai-products' }}" class="kv-btn-action kv-btn-outline" style="color: #FFFFFF; font-size: 17px; font-weight: 600; padding: 17px 26px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.28); text-decoration: none">{{ $heroSecondaryCta['text'] ?? 'Explore products' }}</a>
</div>
</div>

<!-- hero: scattered tools converge into one platform -->
<div class="kv-hero-diagram-wrap" style="display: flex; flex-direction: column; align-items: center; gap: 12px">
<div role="img" class="kv-hero-diagram-canvas" aria-label="Animated illustration: scattered AI tools merge into Kouventa AI with AI Company Wikipedia, AI Task Force, Chat Playground and TaskO, governance controls lock around it, and an approved, logged result goes out to channels like WhatsApp, Slack, email, internal apps and ERP, with email highlighted" data-kv-fit="520" style="width: 520px; height: 440px; position: relative">
<div style="position: absolute; left: 50%; top: 160px; width: 360px; height: 360px; margin: -180px 0 0 -180px; border-radius: 50%; border: 1px dashed rgba(255,255,255,0.12)"></div>
<div style="position: absolute; left: 50%; top: 160px; width: 230px; height: 230px; margin: -115px 0 0 -115px; border-radius: 50%; border: 1px dashed rgba(255,255,255,0.12)"></div>

<div :key="heroKey" x-show="heroOn" style="position: absolute; inset: 0">
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -190px; --y: -130px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg></span>
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 180px; --y: -150px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Z"></path><path d="M14 3v6h6M8 13h8M8 17h5"></path></svg></span>
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 210px; --y: 60px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 16v-4M12 16V8M17 16v-6"></path></svg></span>
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: 90px; --y: 170px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"></path><path d="M9 7h6M9 11h6M9 15h3"></path></svg></span>
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -150px; --y: 150px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg></span>
<span style="position: absolute; left: 50%; top: 160px; width: 64px; height: 64px; margin: -32px 0 0 -32px; border-radius: 18px; background: #0F3357; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; --x: -220px; --y: 20px; animation: kvConverge 3.4s ease 1 forwards"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D5DEE8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"></path><circle cx="12" cy="12" r="3"></circle></svg></span>

<div style="position: absolute; left: 80px; top: 60px; width: 360px; height: 200px; box-sizing: border-box; border-radius: 22px; background: #0C2A48; border: 1px solid rgba(43,212,106,0.45); padding: 26px 16px; display: flex; flex-direction: column; gap: 12px; animation: kvLogo 3.4s ease 1 forwards, kvGlow 3.4s ease 1 forwards">
<div style="display: flex; justify-content: space-between; align-items: center">
<span style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #FFFFFF"><svg width="13" height="16" viewBox="0 0 96 121"><rect x="0" y="0" width="40" height="121" fill="#12BB3A"></rect><path d="M40 0H96L40 60.5L96 121H40Z" fill="#82D48B"></path></svg>Kouventa AI</span>
<span style="font-size: 10.5px; font-weight: 700; padding: 4px 8px; border-radius: 999px; background: rgba(43,212,106,0.15); color: #2BD46A">{{ $isIdLocale ? 'Semua model' : t('kouventa.All models', 'All models') }}</span>
</div>
<div class="kv-hero-tools-grid" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px">
<span style="height: 52px; box-sizing: border-box; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 11.5px; line-height: 1.25; font-weight: 700; color: #FFFFFF; animation: kvTileOn 0.45s ease 3.1s 1 forwards">
<span style="width: 26px; height: 26px; border-radius: 8px; background: rgba(43,212,106,0.14); display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"></path><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"></path></svg></span>
<span style="flex-grow: 1">{{ t('kouventa.AI Company Wikipedia', 'AI Company Wikipedia') }}</span>
</span>
<span style="height: 52px; box-sizing: border-box; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 11.5px; line-height: 1.25; font-weight: 700; color: #FFFFFF; animation: kvTileOn 0.45s ease 3.45s 1 forwards">
<span style="width: 26px; height: 26px; border-radius: 8px; background: rgba(43,212,106,0.14); display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0"></path><circle cx="17" cy="9" r="2.5"></circle><path d="M16 14.2a5 5 0 0 1 5 5.8"></path></svg></span>
<span style="flex-grow: 1">{{ t('kouventa.AI Task Force', 'AI Task Force') }}</span>
</span>
<span style="height: 52px; box-sizing: border-box; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 11.5px; line-height: 1.25; font-weight: 700; color: #FFFFFF; animation: kvTileOn 0.45s ease 3.8s 1 forwards">
<span style="width: 26px; height: 26px; border-radius: 8px; background: rgba(43,212,106,0.14); display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg></span>
<span style="flex-grow: 1">{{ t('kouventa.Chat Playground', 'Chat Playground') }}</span>
</span>
<span style="height: 52px; box-sizing: border-box; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; font-size: 11.5px; line-height: 1.25; font-weight: 700; color: #FFFFFF; animation: kvTileOn 0.45s ease 4.15s 1 forwards">
<span style="width: 26px; height: 26px; border-radius: 8px; background: rgba(43,212,106,0.14); display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="6" height="6" rx="1"></rect><rect x="15" y="15" width="6" height="6" rx="1"></rect><path d="M9 6h4a2 2 0 0 1 2 2v7"></path></svg></span>
<span style="flex-grow: 1">{{ t('kouventa.TaskO', 'TaskO') }}</span>
</span>
</div>
</div>

<!-- Top floating pills -->
<div style="position: absolute; left: 0; right: 0; top: 47px; display: flex; justify-content: center; gap: 6px">
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 4.6s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.Guardrails & PII', 'Guardrails & PII') }}</span>
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 4.8s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ $isIdLocale ? 'Akses peran' : t('kouventa.Role access', 'Role access') }}</span>
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 5s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ $isIdLocale ? 'Pemakaian & budget' : t('kouventa.Usage & budget', 'Usage & budget') }}</span>
</div>

<!-- Bottom floating pills -->
<div style="position: absolute; left: 0; right: 0; top: 247px; display: flex; justify-content: center; gap: 6px">
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 5.2s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.AI Learning', 'AI Learning') }}</span>
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 5.4s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.Audit trail', 'Audit trail') }}</span>
<span style="display: flex; align-items: center; gap: 5px; white-space: nowrap; font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; background: #0F3357; border: 1px solid rgba(43,212,106,0.55); color: #D5DEE8; opacity: 0; animation: kvPopIn 0.4s ease 5.6s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ $isIdLocale ? 'Manajemen perubahan' : t('kouventa.Change management', 'Change management') }}</span>
</div>

<!-- Vertical connector -->
<span style="position: absolute; left: 259px; top: 275px; width: 2px; height: 0; background: linear-gradient(#2BD46A, rgba(43,212,106,0.3)); --h: 72px; animation: kvGrowH 0.5s ease 6s 1 forwards"></span>
<span style="position: absolute; left: 255px; top: 275px; width: 10px; height: 10px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 10px #2BD46A; opacity: 0; --d: 66px; animation: kvTravelY 0.8s ease 6.5s 1 forwards"></span>
<span style="position: absolute; left: 272px; top: 297px; display: flex; align-items: center; gap: 5px; font-size: 10.5px; font-weight: 700; padding: 4px 9px; border-radius: 999px; background: rgba(43,212,106,0.15); color: #2BD46A; opacity: 0; animation: kvPopIn 0.4s ease 6.7s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2BD46A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>{{ $isIdLocale ? 'Disetujui · tercatat' : t('kouventa.Approved · logged', 'Approved · logged') }}</span>

<!-- Channels row -->
<div style="position: absolute; left: 0; right: 0; top: 350px; display: flex; justify-content: center; gap: 8px">
<span style="font-size: 11.5px; font-weight: 700; padding: 8px 13px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #D5DEE8; opacity: 0; animation: kvIn 0.4s ease 6.1s 1 forwards">WhatsApp</span>
<span style="font-size: 11.5px; font-weight: 700; padding: 8px 13px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #D5DEE8; opacity: 0; animation: kvIn 0.4s ease 6.2s 1 forwards">Slack</span>
<span style="font-size: 11.5px; font-weight: 700; padding: 8px 13px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #D5DEE8; opacity: 0; animation: kvIn 0.4s ease 6.3s 1 forwards, kvChOn 0.4s ease 7.3s 1 forwards">Email</span>
<span style="font-size: 11.5px; font-weight: 700; padding: 8px 13px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #D5DEE8; opacity: 0; animation: kvIn 0.4s ease 6.4s 1 forwards">{{ $isIdLocale ? 'Aplikasi internal' : t('kouventa.Internal apps', 'Internal apps') }}</span>
<span style="font-size: 11.5px; font-weight: 700; padding: 8px 13px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #D5DEE8; opacity: 0; animation: kvIn 0.4s ease 6.5s 1 forwards">ERP</span>
</div>
</div>

</div>
<button type="button" @click="replayHero()" style="min-height: 40px; padding: 8px 16px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: #B9C6D3; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>{{ $isIdLocale ? 'Putar ulang' : t('kouventa.Replay', 'Replay') }}</button>
</div>
</div>
</section>

<!-- ============ CHALLENGES ============ -->
<section style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 40px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $challengesTitle['prefix'] ?? 'Too many AI tools,' }} <span style="color: #13803F">{{ $challengesTitle['main'] ?? 'too little control.' }}</span></h2>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px">
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.2s both">
<div aria-hidden="true" style="height: 56px; position: relative; width: 120px">
<span style="position: absolute; left: 0; top: 10px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; animation: kvScatterDrift1 3.2s ease-in-out infinite alternate"></span>
<span style="position: absolute; left: 44px; top: 0; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; animation: kvScatterDrift2 2.8s ease-in-out .3s infinite alternate"></span>
<span style="position: absolute; left: 86px; top: 18px; width: 34px; height: 34px; border-radius: 10px; background: #FBE6E1; border: 1px solid #F0C4BA; animation: kvScatterDrift3 3.5s ease-in-out .6s infinite alternate"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.AI tools are scattered across teams', 'AI tools are scattered across teams') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Different teams use different AI tools based on their needs, creating a fragmented AI environment across the organization.', 'Different teams use different AI tools based on their needs, creating a fragmented AI environment across the organization.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.8s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; transform-origin: 50% 80%; animation: kvWobbleLoop 3.2s ease-in-out infinite"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 7.5-2"></path></svg></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Limited control over AI usage', 'Limited control over AI usage') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.With AI tools managed separately, it becomes harder to control how AI is used and ensure business data is handled securely.', 'With AI tools managed separately, it becomes harder to control how AI is used and ensure business data is handled securely.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 1.4s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: flex-end; gap: 6px">
<span style="width: 16px; height: 18px; border-radius: 4px 4px 0 0; background: #F0C4BA; transform-origin: bottom; animation: kvBarFluctuate 2.1s ease-in-out infinite alternate"></span>
<span style="width: 16px; height: 30px; border-radius: 4px 4px 0 0; background: #E79F8E; transform-origin: bottom; animation: kvBarFluctuate 2.5s ease-in-out .3s infinite alternate"></span>
<span style="width: 16px; height: 42px; border-radius: 4px 4px 0 0; background: #D9735D; transform-origin: bottom; animation: kvBarFluctuate 2.3s ease-in-out .6s infinite alternate"></span>
<span style="width: 16px; height: 56px; border-radius: 4px 4px 0 0; background: #B4412A; transform-origin: bottom; animation: kvBarFluctuate 2.7s ease-in-out .15s infinite alternate"></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.AI usage & billing are hard to manage', 'AI usage & billing are hard to manage') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Multiple AI subscriptions make it difficult to centralize usage, manage subscriptions, and keep track of spending across teams.', 'Multiple AI subscriptions make it difficult to centralize usage, manage subscriptions, and keep track of spending across teams.') }}</p>
</article>
</div>
</section>

<!-- ============ ENTERPRISE AI JOURNEY ============ -->
<section id="journey" class="kv-journey-section" style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 28px">
<div style="display: flex; flex-direction: column; gap: 14px; max-width: 900px">
@php
    $journeyPrefix = t('kouventa.Bring your AI together,', $isIdLocale ? 'Semua AI Anda di satu tempat,' : 'Bring your AI together,');
    if ($isIdLocale && ($journeyPrefix === 'Satukan AI Anda,' || $journeyPrefix === 'Bring your AI together,' || empty($journeyPrefix))) {
        $journeyPrefix = 'Semua AI Anda di satu tempat,';
    }
    $journeyMain = t('kouventa.from answers to action.', $isIdLocale ? 'dari menjawab sampai bertindak.' : 'from answers to action.');
    if ($isIdLocale && ($journeyMain === 'dari jawaban hingga aksi.' || $journeyMain === 'from answers to action.' || empty($journeyMain))) {
        $journeyMain = 'dari menjawab sampai bertindak.';
    }
@endphp
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ $journeyPrefix }} <span style="color: #13803F; display: block">{{ $journeyMain }}</span></h2>
<p style="margin: 0; font-size: 18px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Kouventa AI helps businesses turn AI into practical business outcomes, from answering questions and uncovering insights to autonomously executing business processes across systems.', 'Kouventa AI helps businesses turn AI into practical business outcomes, from answering questions and uncovering insights to autonomously executing business processes across systems.') }}</p>
</div>

<!-- Journey Tablist -->
<div role="tablist" class="kv-journey-tablist-wrap" aria-label="Enterprise AI journey" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 28px 0 26px">
<div class="kv-journey-track-container" style="position: relative; height: 176px">
<!-- Dashed background track -->
<span aria-hidden="true" class="kv-journey-track-bg" style="position: absolute; top: 76px; left: 16.667%; width: 66.667%; height: 4px; border-radius: 4px; background: repeating-linear-gradient(90deg, #C9D2CE 0 10px, transparent 10px 18px)"></span>

<!-- Active green progress bar -->
<span aria-hidden="true" class="kv-journey-track-fill" style="position: absolute; top: 76px; left: 16.667%; height: 4px; border-radius: 4px; background: #13803F; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1)"
      :style="{ width: stage === 0 ? '0%' : (stage === 1 ? '33.333%' : '66.667%') }"></span>

<!-- Floating badge "Your AI needs" -->
<span aria-hidden="true" class="kv-journey-pointer" style="position: absolute; top: 0; transform: translateX(-50%); transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; z-index: 10"
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
<button type="button" role="tab" class="kv-journey-tab-btn" :aria-selected="stage === {{ $idx }}" @click="pickStage({{ $idx }})"
        style="position: absolute; top: 50px; left: {{ $btn['left'] }}; transform: translateX(-50%); width: 240px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
    <span style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.3s, border-color 0.3s, color 0.3s; {{ $idx === 0 ? 'background: #071C33; border: 2px solid #071C33; color: #2BD46A; animation: kvPulse 2s ease-out infinite;' : 'background: #FFFFFF; border: 2px solid #C9D2CE; color: #6B7A88;' }}"
          :style="{
              background: stage === {{ $idx }} ? '#071C33' : (stage > {{ $idx }} ? '#EAF7EF' : '#FFFFFF'),
              border: stage === {{ $idx }} ? '2px solid #071C33' : (stage > {{ $idx }} ? '2px solid #13803F' : '2px solid #C9D2CE'),
              color: stage === {{ $idx }} ? '#2BD46A' : (stage > {{ $idx }} ? '#13803F' : '#6B7A88'),
              animation: stage === {{ $idx }} ? 'kvPulse 2s ease-out infinite' : 'none'
          }">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
<div role="tabpanel" class="kv-journey-tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 44px; display: grid; grid-template-columns: minmax(0, 1fr) 520px; gap: 48px; align-items: center">
<div style="display: flex; flex-direction: column; gap: 16px">
<span style="font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em" x-text="stages[stage].headline">{{ $stages[0]['headline'] }}</span>
<span style="font-size: 16px; line-height: 1.6; color: #4A5866" x-text="stages[stage].sub">{{ $stages[0]['sub'] }}</span>
<button type="button" class="kv-btn-stage-action" @click="seeTools(stages[stage].defaultProduct)" style="align-self: flex-start; margin-top: 8px; min-height: 52px; padding: 14px 22px; border-radius: 12px; border: 0; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px; cursor: pointer">
<span>{{ t('kouventa.See the products for', 'See the products for') }} <span x-text="stages[stage].name">{{ $stages[0]['name'] }}</span></span>
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
</button>
</div>

<div style="display: flex; flex-direction: column; gap: 10px">
<div role="img" class="kv-journey-stage-screen" style="height: 400px; border-radius: 18px; background: #F4F6F5; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center">
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

<button type="button" class="kv-btn-replay" @click="replayJourney()" style="align-self: flex-end; min-height: 40px; padding: 8px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #4A5866; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer">
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
    <button type="button" @click="unlink()" class="kv-btn-show-all">{{ t('kouventa.Show all products', 'Show all products') }}<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></button>
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

<!-- Wiki Info -->
<div class="kv-product-info" x-show="product === 'wiki'">
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">AI Company Wikipedia</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ $isIdLocale ? 'Chatbot berbasis AI yang memahami konteks pertanyaan Anda dan memberi jawaban cepat dan akurat dalam hitungan detik, bersumber dari pengetahuan perusahaan, website, database, dan sumber data lain yang terhubung.' : t('kouventa.An AI chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.', 'An AI chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Jawaban akurat, lebih cepat' : t('kouventa.Reliable answers, faster', 'Reliable answers, faster') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Ajukan pertanyaan dan dapatkan informasi yang tepat dalam hitungan detik.' : t('kouventa.Ask a question and get the right information in seconds.', 'Ask a question and get the right information in seconds.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Sumber rujukan' : t('kouventa.Source citations', 'Source citations') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Setiap jawaban disertai sumber agar lebih transparan.' : t('kouventa.Every answer is backed by sources for greater transparency.', 'Every answer is backed by sources for greater transparency.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? '19+ format sumber data' : t('kouventa.19+ data source formats', '19+ data source formats') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Cari di dokumen, database, website, dan sistem bisnis.' : t('kouventa.Search across documents, databases, websites, and business systems.', 'Search across documents, databases, websites, and business systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Pengalihan ke manusia' : t('kouventa.Human handover', 'Human handover') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Alihkan percakapan ke agent manusia dengan mulus saat dibutuhkan.' : t('kouventa.Seamlessly hand conversations over to human agents when needed.', 'Seamlessly hand conversations over to human agents when needed.') }}</span></span>
        </div>
    </div>
</div>

<!-- Task Force Info -->
<div class="kv-product-info" x-show="product === 'taskforce'" x-cloak>
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">AI Task Force</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ $isIdLocale ? 'Solusi berbasis Agentic AI yang memahami tujuan bisnis Anda dan menjalankan workflow kompleks di sistem bisnis yang terhubung, menalar setiap tugas dan meminta persetujuan Anda di tahap penting.' : t('kouventa.An Agentic AI solution that understands your business objectives and executes complex workflows across connected business systems, reasoning through tasks and seeking your approval at critical steps.', 'An Agentic AI solution that understands your business objectives and executes complex workflows across connected business systems, reasoning through tasks and seeking your approval at critical steps.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Otomasi proses menyeluruh' : t('kouventa.Full process automation', 'Full process automation') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Otomasi proses bisnis lintas sistem.' : t('kouventa.Automate business processes across multiple systems.', 'Automate business processes across multiple systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Analisis yang memahami konteks' : t('kouventa.Contextual analysis', 'Contextual analysis') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Menganalisis konteks dan data untuk menentukan langkah terbaik.' : t('kouventa.Analyze context and data to determine the best course of action.', 'Analyze context and data to determine the best course of action.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Kolaborasi multi agent' : t('kouventa.Multi agent collaboration', 'Multi agent collaboration') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Agent dengan keahlian khusus bekerja sama menyelesaikan tugas kompleks.' : t('kouventa.Specialized agents work together to complete complex tasks.', 'Specialized agents work together to complete complex tasks.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Konektor MCP' : t('kouventa.MCP connectors', 'MCP connectors') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Jalankan aksi di ERP, CRM, OMS, HRIS, dan lainnya.' : t('kouventa.Execute actions across ERP, CRM, OMS, HRIS, and more.', 'Execute actions across ERP, CRM, OMS, HRIS, and more.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Gerbang persetujuan manusia' : t('kouventa.Human approval gates', 'Human approval gates') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Tinjau dan setujui aksi penting sebelum dijalankan.' : t('kouventa.Review and approve critical actions before they run.', 'Review and approve critical actions before they run.') }}</span></span>
        </div>
    </div>
</div>

<!-- Playground Info -->
<div class="kv-product-info" x-show="product === 'playground'" x-cloak>
    <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">Chat Playground</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ $isIdLocale ? 'Chat Playground adalah workspace terkontrol untuk chat dengan AI Company Wikipedia, serta memicu, menjalankan, dan memantau agent AI Task Force, secara aman, efisien, dan sesuai batasan Anda.' : t('kouventa.Chat Playground is your controlled workspace to chat with AI Company Wikipedia, and to trigger, run, and monitor AI Task Force agents, securely, efficiently, and within your boundaries.', 'Chat Playground is your controlled workspace to chat with AI Company Wikipedia, and to trigger, run, and monitor AI Task Force agents, securely, efficiently, and within your boundaries.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Picu workflow agentic AI' : t('kouventa.Trigger agentic AI workflows', 'Trigger agentic AI workflows') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Menganalisis, bertindak, dan memberi hasil di tools yang sudah Anda pakai, termasuk email, Slack, dashboard, dan lainnya.' : t('kouventa.Analyze, act, and deliver results across your existing tools, including email, Slack, dashboards, and more.', 'Analyze, act, and deliver results across your existing tools, including email, Slack, dashboards, and more.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Runner & agent yang terkontrol' : t('kouventa.Controlled runners & agents', 'Controlled runners & agents') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Gunakan hanya yang sudah diizinkan admin, seperti runner yang disetujui dan agent yang diizinkan.' : t('kouventa.Use only what admins have authorized, such as approved runners and permitted agents.', 'Use only what admins have authorized, such as approved runners and permitted agents.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Akses berbasis hierarki peran' : t('kouventa.Access by role & hierarchy', 'Access by role & hierarchy') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Beri akses yang tepat ke orang yang tepat berdasarkan peran, hierarki, dan departemen.' : t('kouventa.Give the right access to the right people based on role, hierarchy, and department.', 'Give the right access to the right people based on role, hierarchy, and department.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Kontrol biaya & manajemen budget' : t('kouventa.Cost control & budget management', 'Cost control & budget management') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Pantau pemakaian dan pengeluaran secara real time dengan budget per pengguna, batas pemakaian, dan peringatan saat melebihi batas.' : t('kouventa.Track usage and spending in real time with budgets per user, limits, and overage alerts.', 'Track usage and spending in real time with budgets per user, limits, and overage alerts.') }}</span></span>
        </div>
    </div>
</div>

<!-- TaskO Info -->
<div class="kv-product-info" x-show="product === 'workflow'" x-cloak>
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap">
        <span style="font-size: 30px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">TaskO</span>
        <span style="font-size: 12px; font-weight: 800; letter-spacing: 0.06em; padding: 6px 10px; border-radius: 999px; background: #FFE9C7; color: #7A4A00">{{ $isIdLocale ? 'SEGERA HADIR' : t('kouventa.COMING SOON', 'COMING SOON') }}</span>
    </div>
    <span style="font-size: 13px; font-weight: 700; color: #13803F">{{ $isIdLocale ? 'Bagian dari AI Task Force' : t('kouventa.Part of AI Task Force', 'Part of AI Task Force') }}</span>
    <span style="font-size: 16px; line-height: 1.6; color: #4A5866">{{ $isIdLocale ? 'TaskO (Task Automation) memungkinkan bisnis merancang dan mengotomasi proses secara menyeluruh dengan menghubungkan tugas, agent AI, sistem bisnis, dan data, tanpa coding yang rumit.' : t('kouventa.TaskO (Task Automation) lets businesses design and automate entire processes by connecting tasks, AI agents, business systems, and data, without complex coding.', 'TaskO (Task Automation) lets businesses design and automate entire processes by connecting tasks, AI agents, business systems, and data, without complex coding.') }}</span>
    <div style="display: flex; flex-direction: column; gap: 14px; padding-top: 18px; border-top: 1px solid #E3E8E6">
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Builder visual drag & drop' : t('kouventa.Visual drag & drop builder', 'Visual drag & drop builder') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Rancang workflow secara visual dengan blok dan konektor siap pakai.' : t('kouventa.Design workflows visually using prebuilt blocks and connectors.', 'Design workflows visually using prebuilt blocks and connectors.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Builder agent tanpa coding' : t('kouventa.No code agent builder', 'No code agent builder') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Konfigurasikan agent AI sesuai peran, tentukan perilakunya, pasang guardrails, dan uji di skenario nyata.' : t('kouventa.Configure AI agents for specific roles, define behavior, set guardrails, and test in real scenarios.', 'Configure AI agents for specific roles, define behavior, set guardrails, and test in real scenarios.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Hubungkan sistem & data Anda' : t('kouventa.Connect your systems & data', 'Connect your systems & data') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Integrasikan dengan database, API, aplikasi SaaS, dan sistem enterprise.' : t('kouventa.Integrate with databases, APIs, SaaS apps, and enterprise systems.', 'Integrate with databases, APIs, SaaS apps, and enterprise systems.') }}</span></span>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
            <span style="display: flex; flex-direction: column; gap: 2px"><span style="font-size: 15px; font-weight: 700">{{ $isIdLocale ? 'Guardrails & persetujuan' : t('kouventa.Guardrails & approvals', 'Guardrails & approvals') }}</span><span style="font-size: 14px; line-height: 1.5; color: #4A5866">{{ $isIdLocale ? 'Tambahkan validasi, persetujuan manusia, dan kebijakan agar eksekusi aman dan sesuai aturan.' : t('kouventa.Add validations, human approvals, and policies to ensure safe and compliant execution.', 'Add validations, human approvals, and policies to ensure safe and compliant execution.') }}</span></span>
        </div>
    </div>
</div>

</div>

<!-- Right animated mockup column -->
<div class="kv-product-mockup-col">
<div role="img" class="kv-product-mockup-container">
<div class="kv-mockup-wrap" :key="productKey" x-show="pOn">

<!-- Mockup Wiki -->
<div class="kv-mockup-wiki" x-show="product === 'wiki'">
<div style="width: 380px; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column">
<div style="background: #071C33; color: #FFFFFF; padding: 14px 16px; display: flex; align-items: center; gap: 10px"><span style="width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center"><svg width="11.1" height="14" viewBox="0 0 96 121" role="img" aria-label="Kouventa" style="display: block; flex-shrink: 0"><rect x="0" y="0" width="40" height="121" fill="#12BB3A"></rect><path d="M40 0H96L40 60.5L96 121H40Z" fill="#82D48B"></path></svg></span><span style="font-size: 14px; font-weight: 700">Company Wikipedia</span></div>
<div style="padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; font-size: 13px; line-height: 1.5">
<div style="align-self: flex-end; max-width: 80%; background: #071C33; color: #FFFFFF; padding: 9px 13px; border-radius: 14px 4px 14px 14px; animation: kvIn .4s ease .2s both">{{ $isIdLocale ? 'Bagaimana cara klaim biaya perjalanan dinas?' : t('kouventa.How do I claim travel expenses?', 'How do I claim travel expenses?') }}</div>
<div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; font-size: 10.5px; color: #4A5866; animation: kvIn .3s ease .7s both"><span>{{ $isIdLocale ? 'Dicari di' : t('kouventa.Searched', 'Searched') }}</span><span style="display: flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 700; color: #4A5866; background: #F4F6F5; border: 1px solid #E3E8E6; padding: 3px 7px; border-radius: 6px; animation: kvPopIn .3s ease 0.8s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"></path><path d="M14 3v5h5M9 13h6M9 17h4"></path></svg>{{ $isIdLocale ? 'Dokumen' : t('kouventa.Documents', 'Documents') }}</span><span style="display: flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 700; color: #4A5866; background: #F4F6F5; border: 1px solid #E3E8E6; padding: 3px 7px; border-radius: 6px; animation: kvPopIn .3s ease 0.95s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"></path></svg>{{ $isIdLocale ? 'Intranet' : t('kouventa.Intranet', 'Intranet') }}</span><span style="display: flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 700; color: #4A5866; background: #F4F6F5; border: 1px solid #E3E8E6; padding: 3px 7px; border-radius: 6px; animation: kvPopIn .3s ease 1.1s both"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="8" ry="3"></ellipse><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"></path></svg>{{ $isIdLocale ? 'Database HR' : t('kouventa.HR database', 'HR database') }}</span></div>
<div style="align-self: flex-start; max-width: 92%; background: #F4F6F5; padding: 9px 13px; border-radius: 4px 14px 14px 14px; animation: kvIn .4s ease 1.5s both">{{ $isIdLocale ? 'Unggah bukti pembayaran di portal finance maksimal 30 hari. Klaim disetujui oleh atasan langsung Anda.' : t('kouventa.Submit receipts in the finance portal within 30 days. Claims are approved by your direct manager.', 'Submit receipts in the finance portal within 30 days. Claims are approved by your direct manager.') }}<span style="white-space: nowrap; margin-left: 4px"><span style="display: inline-flex; align-items: center; justify-content: center; min-width: 15px; height: 15px; margin: 0 1px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C; font-size: 9.5px; font-weight: 800; vertical-align: 1px">1</span><span style="display: inline-flex; align-items: center; justify-content: center; min-width: 15px; height: 15px; margin: 0 1px; border-radius: 4px; background: #DDF3E4; color: #0B5E2C; font-size: 9.5px; font-weight: 800; vertical-align: 1px">2</span></span><span style="display: block; margin-top: 4px; font-size: 10.5px; color: #4A5866">{{ $isIdLocale ? 'Dijawab dalam 2 detik' : t('kouventa.Answered in 2 seconds', 'Answered in 2 seconds') }}</span></div>
<div style="display: flex; align-items: center; gap: 9px; padding: 8px 10px; border: 1px solid #E3E8E6; border-radius: 10px; animation: kvIn .35s ease 2.3s both"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 7px; background: #DDF3E4; color: #0B5E2C; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"></path><path d="M14 3v5h5M9 13h6M9 17h4"></path></svg></span><span style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="font-size: 12px; font-weight: 700; color: #0E1A24">{{ $isIdLocale ? 'Finance SOP.pdf' : t('kouventa.Finance SOP.pdf', 'Finance SOP.pdf') }}</span><span style="font-size: 10.5px; color: #4A5866">{{ $isIdLocale ? 'Bagian 4.2 · Klaim perjalanan dinas' : t('kouventa.Section 4.2 · Travel claims', 'Section 4.2 · Travel claims') }}</span></span><span style="font-size: 10px; font-weight: 800; color: #0B5E2C">[1]</span></div>
<div style="display: flex; align-items: center; gap: 9px; padding: 8px 10px; border: 1px solid #E3E8E6; border-radius: 10px; animation: kvIn .35s ease 2.5s both"><span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 7px; background: #DDF3E4; color: #0B5E2C; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"></path></svg></span><span style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="font-size: 12px; font-weight: 700; color: #0E1A24">{{ $isIdLocale ? 'Intranet · Klaim' : t('kouventa.Intranet · Claims', 'Intranet · Claims') }}</span><span style="font-size: 10.5px; color: #4A5866">{{ $isIdLocale ? 'Kebijakan persetujuan' : t('kouventa.Approval policy', 'Approval policy') }}</span></span><span style="font-size: 10px; font-weight: 800; color: #0B5E2C">[2]</span></div>
<span style="align-self: center; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #13803F; animation: kvIn .35s ease 3.1s both"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>{{ $isIdLocale ? 'Butuh bantuan lain? Alihkan ke agent manusia' : t('kouventa.Need more help? Hand over to human agent', 'Need more help? Hand over to human agent') }}</span>
</div>
</div>
</div>

<!-- Mockup Taskforce -->
<div class="kv-mockup-taskforce" x-show="product === 'taskforce'" x-cloak>
<div style="display: flex; flex-direction: column; align-items: center; gap: 16px">
<div style="width: 400px; display: flex; align-items: flex-start"><div style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px"><span style="position: absolute; top: 11px; left: calc(50% + 14px); right: calc(-50% + 14px); height: 2px; background: #DCE3E0"><span style="display: block; height: 2px; background: #13803F; --w: 100%; animation: kvGrowW .4s ease 1.50s both"></span></span><span style="display: grid; width: 24px; height: 24px"><span style="grid-area: 1 / 1; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #8696A0; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">1</span><span style="grid-area: 1 / 1; border-radius: 50%; background: #13803F; color: #FFFFFF; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 0.30s both">1</span></span><span style="font-size: 10px; font-weight: 700; color: #33414E; white-space: nowrap">{{ $isIdLocale ? 'Data source' : t('kouventa.Data source', 'Data source') }}</span></div><div style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px"><span style="position: absolute; top: 11px; left: calc(50% + 14px); right: calc(-50% + 14px); height: 2px; background: #DCE3E0"><span style="display: block; height: 2px; background: #13803F; --w: 100%; animation: kvGrowW .4s ease 3.00s both"></span></span><span style="display: grid; width: 24px; height: 24px"><span style="grid-area: 1 / 1; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #8696A0; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">2</span><span style="grid-area: 1 / 1; border-radius: 50%; background: #13803F; color: #FFFFFF; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 1.80s both">2</span></span><span style="font-size: 10px; font-weight: 700; color: #33414E; white-space: nowrap">{{ $isIdLocale ? 'Connector' : t('kouventa.Connector', 'Connector') }}</span></div><div style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px"><span style="position: absolute; top: 11px; left: calc(50% + 14px); right: calc(-50% + 14px); height: 2px; background: #DCE3E0"><span style="display: block; height: 2px; background: #13803F; --w: 100%; animation: kvGrowW .4s ease 4.50s both"></span></span><span style="display: grid; width: 24px; height: 24px"><span style="grid-area: 1 / 1; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #8696A0; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">3</span><span style="grid-area: 1 / 1; border-radius: 50%; background: #13803F; color: #FFFFFF; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.30s both">3</span></span><span style="font-size: 10px; font-weight: 700; color: #33414E; white-space: nowrap">{{ $isIdLocale ? 'Tools' : t('kouventa.Tools', 'Tools') }}</span></div><div style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px"><span style="position: absolute; top: 11px; left: calc(50% + 14px); right: calc(-50% + 14px); height: 2px; background: #DCE3E0"><span style="display: block; height: 2px; background: #13803F; --w: 100%; animation: kvGrowW .4s ease 8.80s both"></span></span><span style="display: grid; width: 24px; height: 24px"><span style="grid-area: 1 / 1; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #8696A0; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">4</span><span style="grid-area: 1 / 1; border-radius: 50%; background: #13803F; color: #FFFFFF; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 4.80s both">4</span></span><span style="font-size: 10px; font-weight: 700; color: #33414E; white-space: nowrap">{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }}</span></div><div style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px"><span style="display: grid; width: 24px; height: 24px"><span style="grid-area: 1 / 1; border-radius: 50%; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #8696A0; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">5</span><span style="grid-area: 1 / 1; border-radius: 50%; background: #13803F; color: #FFFFFF; font-size: 10.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 9.10s both">5</span></span><span style="font-size: 10px; font-weight: 700; color: #33414E; white-space: nowrap">{{ $isIdLocale ? 'Trial run' : t('kouventa.Trial run', 'Trial run') }}</span></div></div>
<div style="width: 400px; height: 340px; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: grid; grid-template-columns: minmax(0, 1fr); font-size: 11px; color: #0E1A24">
<div style="display: grid; padding: 16px 18px; min-width: 0">
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 0.30s both, kvOut .15s ease 1.65s 1 forwards"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Data source' : t('kouventa.Data sources', 'Data sources') }}</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Unggah dokumen atau hubungkan storage.' : t('kouventa.Upload documents or connect storage.', 'Upload documents or connect storage.') }}</span><div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Permen ESDM No. 2/2022' : t('kouventa.Permen ESDM No. 2/2022', 'Permen ESDM No. 2/2022') }}</span><span style="font-size: 9.5px; color: #4A5866">PDF · Adeline E3</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; white-space: nowrap">{{ $isIdLocale ? 'Siap' : t('kouventa.Ready', 'Ready') }}</span></div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Kebijakan Komersial' : t('kouventa.Kebijakan Komersial', 'Kebijakan Komersial') }}</span><span style="font-size: 9.5px; color: #4A5866">PDF · Adeline E3</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; white-space: nowrap">{{ $isIdLocale ? 'Siap' : t('kouventa.Ready', 'Ready') }}</span></div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Kontrak vendor' : t('kouventa.Vendor contracts', 'Vendor contracts') }}</span><span style="font-size: 9.5px; color: #4A5866">AWS S3 · Adeline E3</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; white-space: nowrap">{{ $isIdLocale ? 'Sinkronisasi' : t('kouventa.Syncing', 'Syncing') }}</span></div></div></div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 1.80s both, kvOut .15s ease 3.15s 1 forwards"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Connector' : t('kouventa.Connectors', 'Connectors') }}</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Hubungkan sistem bisnis lewat MCP.' : t('kouventa.Connect business systems through MCP.', 'Connect business systems through MCP.') }}</span><div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">MCP Web Scrapping</span><span style="font-size: 9.5px; color: #4A5866">internal/web</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; white-space: nowrap">{{ $isIdLocale ? 'Terhubung' : t('kouventa.Connected', 'Connected') }}</span></div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">MCP RAG</span><span style="font-size: 9.5px; color: #4A5866">internal/rag</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; white-space: nowrap">{{ $isIdLocale ? 'Terhubung' : t('kouventa.Connected', 'Connected') }}</span></div><div style="display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid #EEF1F0"><span style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column"><span style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">MCP PDF Creation</span><span style="font-size: 9.5px; color: #4A5866">internal/doc-pdf</span></span><span style="font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; white-space: nowrap">{{ $isIdLocale ? 'Terhubung' : t('kouventa.Connected', 'Connected') }}</span></div></div></div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 3.30s both, kvOut .15s ease 4.65s 1 forwards"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Tools' : t('kouventa.Tools', 'Tools') }}</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Pilih aksi yang boleh dijalankan agent.' : t('kouventa.Pick the actions agents are allowed to run.', 'Pick the actions agents are allowed to run.') }}</span><div style="display: flex; flex-wrap: wrap; gap: 6px"><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">search_regulation</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">make_request</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">create_pdf</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #EEF1F0; color: #4A5866">session_fetch</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #EEF1F0; color: #4A5866">screenshot</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">send_email</span></div></div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 4.80s both, kvOut .15s ease 6.15s 1 forwards"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }}</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Tentukan apa yang diketahui agent dan cara kerjanya.' : t('kouventa.Define what the agent knows and how it works.', 'Define what the agent knows and how it works.') }}</span><div style="display: flex; flex-direction: column; gap: 6px"><span style="font-size: 9.5px; font-weight: 700; color: #4A5866">{{ $isIdLocale ? 'Nama agent' : t('kouventa.Agent name', 'Agent name') }}</span><span style="padding: 6px 8px; border: 1px solid #DCE3E0; border-radius: 6px; font-weight: 700">{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }} Legal Oil and Gas</span><span style="font-size: 9.5px; font-weight: 700; color: #4A5866">{{ $isIdLocale ? 'Data source' : t('kouventa.Data sources', 'Data sources') }}</span><span style="display: flex; flex-wrap: wrap; gap: 4px"><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">{{ $isIdLocale ? 'Permen ESDM No. 2/2022' : t('kouventa.Permen ESDM No. 2/2022', 'Permen ESDM No. 2/2022') }}</span><span style="font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: #2BD46A; color: #06251A">{{ $isIdLocale ? 'Kebijakan Komersial' : t('kouventa.Kebijakan Komersial', 'Kebijakan Komersial') }}</span></span><span style="font-size: 9.5px; font-weight: 700; color: #4A5866">{{ $isIdLocale ? 'System prompt' : t('kouventa.System prompt', 'System prompt') }}</span><span style="padding: 6px 8px; border: 1px solid #DCE3E0; border-radius: 6px; font-size: 10px; line-height: 1.4; color: #33414E">You are the AI Legal &amp; Compliance Checker for oil and gas regulations…</span></div></div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 8px; animation: kvIn .3s ease 6.30s both, kvOut .15s ease 8.95s 1 forwards"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Tim agent' : t('kouventa.Agent team', 'Agent team') }}</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Bekerja sebagai satu agent, atau sebagai tim agent.' : t('kouventa.Work as a single agent, or as a team of agents.', 'Work as a single agent, or as a team of agents.') }}</span><div style="display: flex; padding: 3px; border-radius: 8px; background: #EEF1F0; font-size: 10.5px; font-weight: 700"><span style="flex: 1; text-align: center; padding: 4px; color: #4A5866">{{ $isIdLocale ? 'Single agent' : t('kouventa.Single agent', 'Single agent') }}</span><span style="flex: 1; text-align: center; padding: 4px; border-radius: 6px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(7,28,51,0.12)">{{ $isIdLocale ? 'Multi-agent' : t('kouventa.Multi-agent', 'Multi-agent') }}</span></div><div style="display: flex; align-items: center; gap: 6px; padding: 6px 9px; border-radius: 8px; background: #071C33; color: #FFFFFF; font-size: 10.5px; animation: kvIn .3s ease 6.60s both"><span style="font-weight: 800; color: #2BD46A">{{ $isIdLocale ? 'Supervisor' : t('kouventa.Supervisor', 'Supervisor') }}</span><span>{{ $isIdLocale ? '· Cek kepatuhan kontrak vendor' : t('kouventa.· Check vendor contract compliance', '· Check vendor contract compliance') }}</span></div><div style="display: flex; flex-direction: column; gap: 5px"><div style="display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-radius: 8px; border: 1px solid #2BD46A; background: #F2FBF5; animation: kvIn .3s ease 6.90s both"><span style="width: 18px; height: 18px; flex-shrink: 0; border-radius: 50%; background: #071C33; color: #FFFFFF; font-size: 9.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">1</span><span style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="font-weight: 700; font-size: 10.5px; display: flex; align-items: center">{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }} Legal Oil and Gas</span><span style="font-size: 9.5px; color: #4A5866">{{ $isIdLocale ? 'Cek klausul terhadap regulasi ESDM' : t('kouventa.Checks clauses against ESDM rules', 'Checks clauses against ESDM rules') }}</span></span><span style="font-size: 9px; font-weight: 700; color: #4A5866; white-space: nowrap">{{ $isIdLocale ? 'Agent ini' : t('kouventa.This agent', 'This agent') }}</span></div><div style="display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-radius: 8px; border: 1px solid #DCE3E0; background: #FFFFFF; animation: kvIn .3s ease 7.50s both"><span style="width: 18px; height: 18px; flex-shrink: 0; border-radius: 50%; background: #071C33; color: #FFFFFF; font-size: 9.5px; font-weight: 800; display: flex; align-items: center; justify-content: center">2</span><span style="display: flex; flex-direction: column; min-width: 0; flex-grow: 1"><span style="font-weight: 700; font-size: 10.5px; display: flex; align-items: center">{{ $isIdLocale ? 'Agent PDF' : t('kouventa.PDF\'s agent', 'PDF\'s agent') }}</span><span style="font-size: 9.5px; color: #4A5866">{{ $isIdLocale ? 'Menyusun laporan kepatuhan' : t('kouventa.Writes the compliance report', 'Writes the compliance report') }}</span></span><span style="font-size: 9px; font-weight: 700; color: #4A5866; white-space: nowrap">{{ $isIdLocale ? 'Connector · PDF' : t('kouventa.Connector · PDF', 'Connector · PDF') }}</span></div></div><span style="align-self: flex-start; display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; color: #7A4A00; background: #FFF1D6; padding: 4px 9px; border-radius: 999px; animation: kvIn .3s ease 7.80s both">{{ $isIdLocale ? 'Legal manager menyetujui sebelum dikirim' : t('kouventa.Legal manager approves before sending', 'Legal manager approves before sending') }}</span></div>
<div style="grid-area: 1 / 1; display: flex; flex-direction: column; gap: 7px; animation: kvIn .3s ease 9.10s both"><span style="display: flex; align-items: baseline; justify-content: space-between; gap: 8px"><span style="font-size: 12.5px; font-weight: 800">{{ $isIdLocale ? 'Trial run' : t('kouventa.Trial run', 'Trial run') }}</span><span style="font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid #DCE3E0; color: #4A5866; white-space: nowrap">{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }} Legal Oil and Gas</span></span><span style="font-size: 10px; color: #4A5866; margin-top: -4px">{{ $isIdLocale ? 'Uji dan sempurnakan sebelum dipublish.' : t('kouventa.Test and fine-tune before you publish.', 'Test and fine-tune before you publish.') }}</span><span style="align-self: flex-end; max-width: 86%; background: #2BD46A; color: #06251A; padding: 6px 9px; border-radius: 10px 3px 10px 10px; font-size: 10.5px; line-height: 1.4; animation: kvIn .3s ease 9.40s both">{{ $isIdLocale ? 'Kalau PTK-007 bentrok dengan UU Migas, kita ikut yang mana?' : t('kouventa.If PTK-007 conflicts with the Oil and Gas Law, which one do we follow?', 'If PTK-007 conflicts with the Oil and Gas Law, which one do we follow?') }}</span><span style="display: flex; align-items: center; gap: 6px; font-size: 10px; animation: kvIn .3s ease 9.90s both"><span style="width: 18px; height: 18px; border-radius: 6px; border: 1px solid #DCE3E0; color: #13803F; display: flex; align-items: center; justify-content: center"><svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 6.5l1.7 4.8 4.8 1.7-4.8 1.7L10 19.5l-1.7-4.8L3.5 13l4.8-1.7Z"></path></svg></span><span style="font-weight: 700">{{ $isIdLocale ? 'Thought process' : t('kouventa.Thought process', 'Thought process') }}</span><span style="color: #8696A0; font-size: 9px; font-weight: 700; letter-spacing: .04em">{{ $isIdLocale ? '4 LANGKAH DIJALANKAN' : t('kouventa.4 STEPS EXECUTED', '4 STEPS EXECUTED') }}</span></span><span style="align-self: flex-start; max-width: 92%; background: #F4F6F5; padding: 6px 9px; border-radius: 3px 10px 10px 10px; font-size: 10.5px; line-height: 1.4; animation: kvIn .3s ease 10.40s both">{{ $isIdLocale ? 'Ikuti aturan yang lebih tinggi: UU No. 22/2001 berlaku di atas PTK-007, sesuai hierarki hukum.' : t('kouventa.Follow the higher rule: UU No. 22/2001 prevails over PTK-007, based on the legal hierarchy.', 'Follow the higher rule: UU No. 22/2001 prevails over PTK-007, based on the legal hierarchy.') }}</span><span style="display: flex; align-items: center; gap: 6px; animation: kvIn .3s ease 11.00s both"><span style="width: 22px; height: 22px; border-radius: 6px; background: #DDF3E4; color: #0B5E2C; display: flex; align-items: center; justify-content: center"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 10v11M15 5.9 14 10h5.8a2 2 0 0 1 2 2.3l-1.4 7A2 2 0 0 1 18.4 21H7V10l4-8a3 3 0 0 1 3 3.9Z"></path></svg></span><span style="width: 22px; height: 22px; border-radius: 6px; border: 1px solid #DCE3E0; color: #8696A0; display: flex; align-items: center; justify-content: center; transform: scaleY(-1)"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 10v11M15 5.9 14 10h5.8a2 2 0 0 1 2 2.3l-1.4 7A2 2 0 0 1 18.4 21H7V10l4-8a3 3 0 0 1 3 3.9Z"></path></svg></span><span style="font-size: 10px; font-weight: 700; color: #0B5E2C">{{ $isIdLocale ? 'Feedback tersimpan ke AI Learning' : t('kouventa.Feedback saved to AI Learning', 'Feedback saved to AI Learning') }}</span></span><span style="margin-top: 2px; display: flex; align-items: center; justify-content: center; gap: 6px; height: 30px; border-radius: 8px; background: #13803F; color: #FFFFFF; font-size: 11px; font-weight: 800; animation: kvIn .3s ease 11.60s both">{{ $isIdLocale ? 'Publish ke Chat Playground' : t('kouventa.Publish to Chat Playground', 'Publish to Chat Playground') }}</span></div>
</div>
</div>
</div>
</div>

<!-- Mockup Playground -->
<div class="kv-mockup-playground" x-show="product === 'playground'" x-cloak>
<div style="width: 410px; height: 392px; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: grid; grid-template-columns: 118px minmax(0, 1fr); font-size: 11px; color: #0E1A24">
<div style="background: #F7F8F8; border-right: 1px solid #ECEFEE; padding: 12px 8px; display: flex; flex-direction: column; gap: 2px; min-width: 0">
<span style="font-size: 13px; font-weight: 800; padding: 0 6px 8px">{{ $isIdLocale ? 'Chat' : t('kouventa.Chats', 'Chats') }}</span>
<span style="display: flex; align-items: center; gap: 5px; padding: 5px 4px; border-radius: 6px; white-space: nowrap; font-size: 10px; color: #0E1A24"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><circle cx="12" cy="12" r="9"></circle><path d="m8.5 12 2.5 2.5 4.5-5"></path></svg>{{ $isIdLocale ? 'Permintaan Persetujuan' : t('kouventa.Approval Request', 'Approval Request') }}</span>
<span style="display: flex; align-items: center; gap: 5px; padding: 5px 4px; border-radius: 6px; white-space: nowrap; font-size: 10px; color: #0E1A24"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"></path></svg>{{ $isIdLocale ? 'Proyek' : t('kouventa.Projects', 'Projects') }}</span>
<span style="display: flex; align-items: center; gap: 5px; padding: 5px 4px; border-radius: 6px; white-space: nowrap; font-size: 10px; color: #0E1A24"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M3 10h18M8 3v4M16 3v4"></path></svg>{{ $isIdLocale ? 'Tugas Terjadwal' : t('kouventa.Scheduler Task', 'Scheduler Task') }}</span>
<span style="font-size: 9.5px; color: #8696A0; padding: 12px 6px 4px">{{ $isIdLocale ? '7 hari terakhir' : t('kouventa.Last 7 days', 'Last 7 days') }}</span>
<span style="padding: 4px 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Ringkasan anggaran Q3' : t('kouventa.Q3 budget summary', 'Q3 budget summary') }}</span>
<span style="padding: 4px 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Review kontrak vendor' : t('kouventa.Vendor contract review', 'Vendor contract review') }}</span>
<span style="padding: 4px 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Kebijakan klaim perjalanan' : t('kouventa.Travel claim policy', 'Travel claim policy') }}</span>
<span style="margin-top: auto; display: flex; align-items: center; gap: 6px; padding: 6px"><span style="width: 20px; height: 20px; flex-shrink: 0; border-radius: 50%; background: #DDF3E4; color: #0B5E2C; font-size: 9px; font-weight: 800; display: flex; align-items: center; justify-content: center">DA</span><span style="display: flex; flex-direction: column; min-width: 0"><span style="font-weight: 700">Dina</span><span style="font-size: 9.5px; color: #4A5866">Finance</span></span></span>
</div>
<div style="position: relative; display: flex; flex-direction: column; min-width: 0; padding: 10px 12px 12px">
<div style="display: flex; align-items: center; gap: 6px">
<span style="display: grid">
<span style="grid-area: 1 / 1; display: flex; align-items: center; gap: 6px; padding: 6px 9px; border-radius: 8px; border: 1px solid #DCE3E0; font-weight: 700; animation: kvOut .2s ease 2.4s 1 forwards"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Pilih agent' : t('kouventa.Choose an agent', 'Choose an agent') }}<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 9 4-4 4 4M8 15l4 4 4-4"></path></svg></span>
<span style="grid-area: 1 / 1; display: flex; align-items: center; gap: 6px; padding: 6px 9px; border-radius: 8px; border: 1.5px solid #2BD46A; box-shadow: 0 0 0 3px rgba(43,212,106,0.18); font-weight: 700; white-space: nowrap; animation: kvIn .3s ease 2.45s both"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Invoice Reconciliation' : t('kouventa.Invoice Reconciliation', 'Invoice Reconciliation') }}<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 9 4-4 4 4M8 15l4 4 4-4"></path></svg></span>
</span>
<span style="display: flex; align-items: center; gap: 5px; padding: 6px 9px; border-radius: 8px; border: 1px solid #ECEFEE; color: #8696A0"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>{{ $isIdLocale ? 'Privat' : t('kouventa.Private', 'Private') }}</span>
</div>
<div style="position: absolute; top: 44px; left: 12px; width: 150px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; box-shadow: 0 10px 24px rgba(7,28,51,0.12); padding: 6px; display: flex; flex-direction: column; gap: 1px; z-index: 2; animation: kvIn .25s ease 0.4s both, kvOut .2s ease 2.3s 1 forwards">
<span style="font-size: 9.5px; color: #8696A0; padding: 3px 8px 4px">{{ $isIdLocale ? 'Pilih cara kerjanya' : t('kouventa.Choose how it works', 'Choose how it works') }}</span>
<span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="13" width="8" height="8" rx="2"></rect><path d="M7 11v4a2 2 0 0 0 2 2h4"></path></svg>{{ $isIdLocale ? 'Runner' : t('kouventa.Runner', 'Runner') }}<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-left: auto"><path d="m9 6 6 6-6 6"></path></svg></span>
<span style="display: grid"><span style="grid-area: 1 / 1"><span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }}<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-left: auto"><path d="m9 6 6 6-6 6"></path></svg></span></span><span style="grid-area: 1 / 1; animation: kvIn .3s ease 1.0s both"><span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; background: #EEF2F0; font-weight: 700"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Agent' : t('kouventa.Agent', 'Agent') }}<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-left: auto"><path d="m9 6 6 6-6 6"></path></svg></span></span></span>
</div>
<div style="position: absolute; top: 80px; left: 114px; width: 158px; background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 10px; box-shadow: 0 10px 24px rgba(7,28,51,0.14); padding: 6px; display: flex; flex-direction: column; gap: 1px; z-index: 3; animation: kvIn .25s ease 1.3s both, kvOut .2s ease 2.3s 1 forwards">
<span style="font-size: 9.5px; color: #8696A0; padding: 3px 8px 4px">{{ $isIdLocale ? 'Diizinkan untuk role Anda' : t('kouventa.Permitted for your role', 'Permitted for your role') }}</span>
<span style="display: grid"><span style="grid-area: 1 / 1"><span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Invoice Reconciliation' : t('kouventa.Invoice Reconciliation', 'Invoice Reconciliation') }}</span></span><span style="grid-area: 1 / 1; animation: kvIn .3s ease 1.8s both"><span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; background: #DDF3E4; color: #0B5E2C; font-weight: 700"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Invoice Reconciliation' : t('kouventa.Invoice Reconciliation', 'Invoice Reconciliation') }}</span></span></span>
<span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Contract Legal Check' : t('kouventa.Contract Legal Check', 'Contract Legal Check') }}</span>
<span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Vendor Due Diligence' : t('kouventa.Vendor Due Diligence', 'Vendor Due Diligence') }}</span>
<span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; "><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Cash Flow Report' : t('kouventa.Cash Flow Report', 'Cash Flow Report') }}</span>
<span style="display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 7px; color: #A3AEB4"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink: 0"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>{{ $isIdLocale ? 'HR Payroll' : t('kouventa.HR Payroll', 'HR Payroll') }}<span style="margin-left: auto; font-size: 9px; font-weight: 700">{{ $isIdLocale ? 'Tidak diizinkan' : t('kouventa.Not permitted', 'Not permitted') }}</span></span>
</div>
<div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-end; gap: 8px; padding-top: 10px">
<div style="align-self: flex-end; max-width: 85%; background: #071C33; color: #FFFFFF; padding: 7px 10px; border-radius: 10px 3px 10px 10px; font-size: 11.5px; line-height: 1.4; animation: kvIn .3s ease 2.9s both">{{ $isIdLocale ? 'Cocokkan invoice September dengan PO.' : t('kouventa.Reconcile September invoices with POs.', 'Reconcile September invoices with POs.') }}</div>
<div style="align-self: flex-start; width: 92%; box-sizing: border-box; border: 1px solid #E3E8E6; border-radius: 3px 10px 10px 10px; padding: 8px 10px; display: flex; flex-direction: column; gap: 6px; animation: kvIn .3s ease 3.4s both">
<span style="display: flex; align-items: center; justify-content: space-between; gap: 6px"><span style="display: flex; align-items: center; gap: 5px; font-weight: 700"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 4v4M9 13h.01M15 13h.01"></path></svg>{{ $isIdLocale ? 'Invoice Reconciliation' : t('kouventa.Invoice Reconciliation', 'Invoice Reconciliation') }}</span>
<span style="display: grid; font-size: 9.5px; font-weight: 800">
<span style="grid-area: 1 / 1; display: flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; background: #FFF1D6; color: #7A4A00; animation: kvOut .2s ease 4.8s 1 forwards"><span style="width: 5px; height: 5px; border-radius: 50%; background: #C77D00; animation: kvBlink .8s ease infinite"></span>{{ $isIdLocale ? 'Berjalan' : t('kouventa.Running', 'Running') }}</span>
<span style="grid-area: 1 / 1; padding: 2px 7px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; animation: kvPopIn .3s ease 4.9s both">{{ $isIdLocale ? 'Selesai' : t('kouventa.Completed', 'Completed') }}</span>
</span></span>
<span style="font-size: 11.5px; line-height: 1.4; color: #33414E; animation: kvIn .3s ease 5.0s both">{{ $isIdLocale ? '142 invoice cocok dengan PO. 3 ditandai untuk Anda review.' : t('kouventa.142 invoices matched to POs. 3 flagged for your review.', '142 invoices matched to POs. 3 flagged for your review.') }}</span>
</div>
<div style="display: flex; align-items: center; gap: 8px; font-size: 9.5px; color: #4A5866; animation: kvIn .3s ease 5.3s both"><span style="white-space: nowrap">{{ $isIdLocale ? 'Budget AI bulanan' : t('kouventa.Monthly AI budget', 'Monthly AI budget') }}</span><span style="flex-grow: 1; height: 5px; border-radius: 999px; background: #EEF2F0; overflow: hidden"><span style="display: block; height: 5px; width: 42%; background: #13803F; border-radius: 999px"></span></span><span style="white-space: nowrap; font-weight: 700; color: #0B5E2C">{{ $isIdLocale ? 'Masih dalam batas' : t('kouventa.Within limit', 'Within limit') }}</span></div>
<div style="height: 32px; border-radius: 9px; background: #F4F6F5; border: 1px solid #ECEFEE; display: flex; align-items: center; padding: 0 6px 0 10px; color: #8696A0">{{ $isIdLocale ? 'Kirim pesan...' : t('kouventa.Send a message...', 'Send a message...') }}<span style="margin-left: auto; width: 22px; height: 22px; border-radius: 50%; background: #2BD46A; color: #FFFFFF; display: flex; align-items: center; justify-content: center"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"></path></svg></span></div>
</div>
</div>
</div>
</div>

<!-- Mockup TaskO (Workflow) -->
<div class="kv-mockup-workflow" x-show="product === 'workflow'" x-cloak>
<div style="position: absolute; inset: 14px; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column; color: #0E1A24">
<div style="height: 42px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 0 12px; border-bottom: 1px solid #ECEFEE; font-size: 11px">
<svg width="10" height="12.6" viewBox="0 0 96 121" aria-hidden="true" style="flex-shrink: 0"><rect x="0" y="0" width="40" height="121" fill="#12BB3A"></rect><path d="M40 0H96L40 60.5L96 121H40Z" fill="#82D48B"></path></svg>
<span style="font-weight: 800; white-space: nowrap">{{ $isIdLocale ? 'Rekonsiliasi Bank' : t('kouventa.Bank Reconciliation', 'Bank Reconciliation') }}</span>
<span style="margin-left: auto; font-size: 9.5px; font-weight: 800; letter-spacing: 0.04em; padding: 3px 8px; border-radius: 999px; background: #FFE9C7; color: #7A4A00; white-space: nowrap">{{ $isIdLocale ? 'SEGERA HADIR' : t('kouventa.COMING SOON', 'COMING SOON') }}</span>
<span style="display: flex; align-items: center; gap: 5px; padding: 5px 9px; border: 1px solid #DCE3E0; border-radius: 8px; font-weight: 700"><span style="width: 7px; height: 7px; border-radius: 50%; background: #E0352B"></span>{{ $isIdLocale ? 'Publish' : t('kouventa.Publish', 'Publish') }}</span>
</div>
<div style="align-self: center; margin-top: 8px; display: flex; padding: 3px; border-radius: 999px; border: 1px solid #DCE3E0; font-size: 10.5px; font-weight: 700"><span style="padding: 4px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">{{ $isIdLocale ? 'Editor' : t('kouventa.Editor', 'Editor') }}</span><span style="padding: 4px 12px; color: #4A5866">{{ $isIdLocale ? 'Eksekusi' : t('kouventa.Executions', 'Executions') }}</span></div>
<div style="flex-grow: 1; position: relative; overflow: hidden; display: flex; align-items: center; background-image: radial-gradient(#D5DDD9 1px, transparent 1px); background-size: 14px 14px">
<div style="position: relative; width: 384px; height: 260px; margin: 0 auto">
<div style="position: absolute; left: 0; top: 0; width: 910px; height: 260px; animation: kvPanT 4.3s linear 1 both">
<svg width="910" height="260" viewBox="0 0 910 260" fill="none" style="position: absolute; inset: 0"><path d="M43 130 C62.0 130 62.0 130 81 130" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M43 130 C62.0 130 62.0 130 81 130" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 1.5s both"></path><path d="M119 130 C138.0 130 138.0 64 157 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M119 130 C138.0 130 138.0 64 157 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 1.8s both"></path><path d="M195 64 C214.0 64 214.0 64 233 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M195 64 C214.0 64 214.0 64 233 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 2.1s both"></path><path d="M271 64 C290.0 64 290.0 64 309 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M271 64 C290.0 64 290.0 64 309 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 2.4s both"></path><path d="M119 130 C157.0 130 157.0 196 195 196" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M119 130 C157.0 130 157.0 196 195 196" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 1.8s both"></path><path d="M233 196 C252.0 196 252.0 196 271 196" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M233 196 C252.0 196 252.0 196 271 196" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 2.1s both"></path><path d="M347 64 C366.0 64 366.0 130 385 130" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M347 64 C366.0 64 366.0 130 385 130" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 2.7s both"></path><path d="M309 196 C347.0 196 347.0 130 385 130" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M309 196 C347.0 196 347.0 130 385 130" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 2.7s both"></path><path d="M423 130 C442.0 130 442.0 130 461 130" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M423 130 C442.0 130 442.0 130 461 130" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 3.0s both"></path><path d="M499 130 C518.0 130 518.0 130 537 130" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M499 130 C518.0 130 518.0 130 537 130" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 3.3s both"></path><path d="M575 130 C598.0 130 598.0 64 621 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M575 130 C598.0 130 598.0 64 621 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 3.6s both"></path><path d="M575 130 C598.0 130 598.0 214 621 214" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M659 64 C682.0 64 682.0 64 705 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M659 64 C682.0 64 682.0 64 705 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 4.0s both"></path><path d="M743 64 C762.0 64 762.0 64 781 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M743 64 C762.0 64 762.0 64 781 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 4.4s both"></path><path d="M819 64 C838.0 64 838.0 64 857 64" stroke="#C9D2CE" stroke-width="1.6" fill="none"></path><path d="M819 64 C838.0 64 838.0 64 857 64" pathLength="100" stroke="#2BD46A" stroke-width="2" fill="none" stroke-dasharray="100" style="--len: 100; animation: kvDraw .3s ease 7.2s both"></path><path d="M630 83 C630 114 616 92 616 126" stroke="#9AA8A2" stroke-width="1.3" stroke-dasharray="3 3" fill="none"></path><path d="M650 83 C650 114 664 92 664 126" stroke="#9AA8A2" stroke-width="1.3" stroke-dasharray="3 3" fill="none"></path></svg>
<div style="position: absolute; left: 5px; top: 111px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.10s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#33414E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 16.98h-5.99c-1.1 0-1.95.94-2.48 1.9A4 4 0 0 1 2 17c.01-.7.2-1.4.57-2"></path><path d="m6 17 3.13-5.78c.53-.97.1-2.18-.5-3.1a4 4 0 1 1 6.89-4.06"></path><path d="m12 6 3.13 5.73C15.66 12.7 16.9 13 18 13a4 4 0 0 1 0 8"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 1.4s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Webhook' : t('kouventa.Webhook', 'Webhook') }}</span></div><div style="position: absolute; left: 81px; top: 111px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.15s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 1.7s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Inisialisasi layanan ECC' : t('kouventa.Initialize ECC Service', 'Initialize ECC Service') }}</span></div><div style="position: absolute; left: 157px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.20s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.0s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Ambil tanggal inverse hari ini' : t('kouventa.Get Today Inverse Date', 'Get Today Inverse Date') }}</span></div><div style="position: absolute; left: 233px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.25s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.3s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Ambil kurs via BAPI' : t('kouventa.Get Exchange Rate BAPI', 'Get Exchange Rate BAPI') }}<br><span style="color: #8696A0; font-size: 7.5px">Select</span></span></div><div style="position: absolute; left: 309px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.30s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.6s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Normalisasi ke JSON terstruktur' : t('kouventa.Normalize into Structured JSON', 'Normalize into Structured JSON') }}</span></div><div style="position: absolute; left: 195px; top: 177px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.25s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.0s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Normalisasi path file biner' : t('kouventa.Normalize Binary File Path', 'Normalize Binary File Path') }}</span></div><div style="position: absolute; left: 271px; top: 177px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.30s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"></rect><path d="M4 10h16M10 10v10"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.3s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Konversi file TXT ke string' : t('kouventa.Convert TXT Files into String', 'Convert TXT Files into String') }}<br><span style="color: #8696A0; font-size: 7.5px">{{ $isIdLocale ? 'Ekstrak dari file teks' : t('kouventa.Extract from text file', 'Extract from text file') }}</span></span></div><div style="position: absolute; left: 385px; top: 111px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.35s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="6" r="2"></circle><circle cx="6" cy="18" r="2"></circle><circle cx="18" cy="12" r="2"></circle><path d="M8 6c4 0 4 6 8 6M8 18c4 0 4-6 8-6"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 2.9s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Gabungkan item' : t('kouventa.Merge Items', 'Merge Items') }}<br><span style="color: #8696A0; font-size: 7.5px">{{ $isIdLocale ? 'Append' : t('kouventa.Append', 'Append') }}</span></span></div><div style="position: absolute; left: 461px; top: 111px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.40s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.2s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Normalisasi ke JSON terstruktur' : t('kouventa.Normalize into Structured JSON', 'Normalize into Structured JSON') }}</span></div><div style="position: absolute; left: 537px; top: 111px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.45s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v18M5 7h12l2 2.5L17 12H5Z"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.5s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Validasi data SAP ECC' : t('kouventa.SAP ECC Data Validation', 'SAP ECC Data Validation') }}</span></div><div style="position: absolute; left: 621px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.50s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#33414E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"></path><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 3.9s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'LLM Chain' : t('kouventa.LLM Chain', 'LLM Chain') }}</span></div><div style="position: absolute; left: 705px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.60s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 4.3s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Output LLM ke HTML terstruktur' : t('kouventa.LLM Output Schema into Structured HTML', 'LLM Output Schema into Structured HTML') }}</span></div><div style="position: absolute; left: 781px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.65s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4A000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12M6 21h12M7 3c0 5 10 5 10 9s-10 4-10 9M17 3c0 5-10 5-10 9"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 7.1s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Persetujuan PIC' : t('kouventa.PIC Approval', 'PIC Approval') }}<br><span style="color: #8696A0; font-size: 7.5px">{{ $isIdLocale ? 'via Chat Playground' : t('kouventa.via Chat Playground', 'via Chat Playground') }}</span></span></div><div style="position: absolute; left: 857px; top: 45px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.70s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg></span><span style="position: absolute; right: -4px; bottom: -4px; width: 13px; height: 13px; border-radius: 50%; background: #2BD46A; display: flex; align-items: center; justify-content: center; animation: kvPopIn .3s ease 7.4s both"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Kirim ke stakeholder' : t('kouventa.Send to stakeholders', 'Send to stakeholders') }}<br><span style="color: #8696A0; font-size: 7.5px">{{ $isIdLocale ? 'via Email' : t('kouventa.via Email', 'via Email') }}</span></span></div><div style="position: absolute; left: 621px; top: 195px; width: 38px; height: 38px; animation: kvPopIn .3s ease 0.55s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 8px; box-shadow: 0 2px 6px rgba(7,28,51,0.06); display: flex; align-items: center; justify-content: center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8696A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.8 7L4 20l1-4.9A8 8 0 1 1 21 12Z"></path></svg></span><span style="position: absolute; top: 42px; left: 50%; transform: translateX(-50%); width: 70px; text-align: center; font-size: 8px; line-height: 1.25; color: #33414E">{{ $isIdLocale ? 'Error SAP ECC' : t('kouventa.SAP ECC Error', 'SAP ECC Error') }}<br><span style="color: #8696A0; font-size: 7.5px">{{ $isIdLocale ? 'via Chat Playground' : t('kouventa.via Chat Playground', 'via Chat Playground') }}</span></span></div><div style="position: absolute; left: 600px; top: 126px; width: 32px; height: 32px; animation: kvPopIn .3s ease 0.55s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 50%; display: flex; align-items: center; justify-content: center"><svg width="11" height="13.9" viewBox="0 0 96 121" aria-hidden="true"><rect x="0" y="0" width="40" height="121" fill="#12BB3A"></rect><path d="M40 0H96L40 60.5L96 121H40Z" fill="#82D48B"></path></svg></span><span style="position: absolute; top: 35px; left: 50%; transform: translateX(-50%); width: 56px; text-align: center; font-size: 7.5px; line-height: 1.2; color: #33414E">{{ $isIdLocale ? 'Kouventa AI Chat Model' : t('kouventa.Kouventa AI Chat Model', 'Kouventa AI Chat Model') }}</span></div><div style="position: absolute; left: 648px; top: 126px; width: 32px; height: 32px; animation: kvPopIn .3s ease 0.55s both"><span style="position: absolute; inset: 0; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 50%; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#33414E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 8-4 4 4 4M16 8l4 4-4 4"></path></svg></span><span style="position: absolute; top: 35px; left: 50%; transform: translateX(-50%); width: 56px; text-align: center; font-size: 7.5px; line-height: 1.2; color: #33414E">{{ $isIdLocale ? 'Structured Output Parser' : t('kouventa.Structured Output Parser', 'Structured Output Parser') }}</span></div><span style="position: absolute; left: 584px; top: 96px; font-size: 7.5px; font-weight: 700; color: #13803F">True</span><span style="position: absolute; left: 572px; top: 200px; font-size: 7.5px; font-weight: 700; color: #C0392B">False</span>
</div>
<div style="position: absolute; left: 206px; top: 104px; width: 156px; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 10px; box-shadow: 0 12px 28px rgba(7,28,51,0.18); padding: 9px 10px; display: flex; flex-direction: column; gap: 6px; font-size: 9.5px; z-index: 3; animation: kvPopIn .3s ease 4.7s both, kvOut .2s ease 5.8s 1 forwards"><span style="font-size: 10.5px; font-weight: 800">{{ $isIdLocale ? 'Tugaskan approval' : t('kouventa.Assign approval', 'Assign approval') }}</span><span style="display: flex; justify-content: space-between; gap: 6px"><span style="color: #4A5866">{{ $isIdLocale ? 'Kepada' : t('kouventa.To', 'To') }}</span><span style="font-weight: 700; white-space: nowrap">{{ $isIdLocale ? 'Andi · PIC Finance' : t('kouventa.Andi · PIC Finance', 'Andi · PIC Finance') }}</span></span><span style="display: flex; justify-content: space-between; gap: 6px"><span style="color: #4A5866">{{ $isIdLocale ? 'Batas' : t('kouventa.Due by', 'Due by') }}</span><span style="font-weight: 700">{{ $isIdLocale ? 'Hari ini, 16.00' : t('kouventa.Today, 16:00', 'Today, 16:00') }}</span></span><span style="display: flex; justify-content: space-between; gap: 6px"><span style="color: #4A5866">{{ $isIdLocale ? 'Melalui' : t('kouventa.Via', 'Via') }}</span><span style="font-weight: 700; color: #0B5E2C">Chat Playground</span></span><span style="margin-top: 2px; text-align: center; padding: 5px; border-radius: 7px; background: #13803F; color: #FFFFFF; font-weight: 800; animation: kvPress .3s ease 5.4s 1 both">{{ $isIdLocale ? 'Kirim permintaan' : t('kouventa.Send request', 'Send request') }}</span></div><div style="position: absolute; left: 206px; top: 104px; width: 156px; box-sizing: border-box; background: #FFFFFF; border: 1px solid #DCE3E0; border-radius: 10px; box-shadow: 0 12px 28px rgba(7,28,51,0.18); padding: 9px 10px; display: flex; flex-direction: column; gap: 6px; font-size: 9.5px; z-index: 3; animation: kvPopIn .3s ease 5.9s both, kvOut .2s ease 6.9s 1 forwards"><span style="font-size: 9px; font-weight: 800; color: #0B5E2C">{{ $isIdLocale ? 'Chat Playground · Andi (PIC)' : t('kouventa.Chat Playground · Andi (PIC)', 'Chat Playground · Andi (PIC)') }}</span><span style="background: #F4F6F5; border-radius: 3px 8px 8px 8px; padding: 6px 8px; line-height: 1.35"><b>{{ $isIdLocale ? 'Permintaan Persetujuan' : t('kouventa.Approval request', 'Approval request') }}</b><br>{{ $isIdLocale ? 'Laporan rekonsiliasi bank · batas hari ini 16.00' : t('kouventa.Bank reconciliation report · due today 16:00', 'Bank reconciliation report · due today 16:00') }}</span><span style="display: flex; gap: 6px"><span style="flex: 1; text-align: center; padding: 4px; border-radius: 6px; background: #2BD46A; color: #06251A; font-weight: 800; animation: kvPress .3s ease 6.5s 1 both">{{ $isIdLocale ? 'Setujui' : t('kouventa.Approve', 'Approve') }}</span><span style="flex: 1; text-align: center; padding: 4px; border-radius: 6px; border: 1px solid #DCE3E0; color: #4A5866; font-weight: 700">{{ $isIdLocale ? 'Tolak' : t('kouventa.Reject', 'Reject') }}</span></span></div>
</div>
</div>
<div style="flex-shrink: 0; display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 0 12px 12px">
<span style="display: flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 999px; background: #E0352B; color: #FFFFFF; font-size: 11.5px; font-weight: 800; box-shadow: 0 6px 14px rgba(224,53,43,0.3); animation: kvPress .3s ease 1.1s 1 both"><svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 4v16l14-8Z"></path></svg>{{ $isIdLocale ? 'Jalankan workflow' : t('kouventa.Execute workflow', 'Execute workflow') }}</span>
<span style="align-self: stretch; display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-top: 1px solid #ECEFEE; font-size: 10.5px"><span style="font-weight: 800">{{ $isIdLocale ? 'Log' : t('kouventa.Logs', 'Logs') }}</span><span style="padding: 2px 8px; border-radius: 999px; background: #DDF3E4; color: #0B5E2C; font-weight: 700; white-space: nowrap; animation: kvIn .3s ease 7.6s both">{{ $isIdLocale ? 'Sukses dalam 1,98 dtk' : t('kouventa.Success in 1.98s', 'Success in 1.98s') }}</span><span style="color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; animation: kvIn .3s ease 7.7s both">{{ $isIdLocale ? 'Disetujui PIC · terkirim ke stakeholder via email' : t('kouventa.Approved by PIC · emailed to stakeholders', 'Approved by PIC · emailed to stakeholders') }}</span></span>
</div>
</div>
</div>

</div>
</div>

<button type="button" @click="replayProduct()" class="kv-product-replay-btn" style="align-self: flex-end; min-height: 40px; padding: 8px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #4A5866; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer">
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
<div aria-label="Example channels: WhatsApp, Email, Slack, Telegram, SAP, Salesforce, internal apps" role="img" style="overflow: hidden">
<div style="display: flex; gap: 14px; width: max-content; animation: kvMarquee 40s linear infinite">
@php
$channels = [
    ['name' => 'WhatsApp', 'icon' => 'images/channels/whatsapp.svg'],
    ['name' => 'Email', 'icon' => 'images/channels/email.svg'],
    ['name' => 'Slack', 'icon' => 'images/channels/slack.svg'],
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
<div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap">
<a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-navy" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap; text-decoration: none">{{ t('kouventa.Book a demo', 'Book a demo') }}</a>
<a href="{{ localized_url('/pricing?tab=ai') }}" class="kv-btn-action kv-btn-outline" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap; text-decoration: none">{{ t('kouventa.See pricing', 'See pricing') }}</a>
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
            linked: true,
            heroOn: true,
            heroKey: 0,
            jOn: true,
            pOn: true,
            productKey: 0,
            stages: @json($stages),
            roles: @json($stageRoles),
            replayHero() {
                this.heroKey = (this.heroKey || 0) + 1;
                this.heroOn = false;
                setTimeout(() => { this.heroOn = true; }, 40);
            },
            replayJourney() {
                this.jOn = false;
                setTimeout(() => { this.jOn = true; }, 40);
            },
            replayProduct() {
                this.productKey = (this.productKey || 0) + 1;
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
            unlink() {
                this.linked = false;
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
