@extends('kouventa::layouts.app')

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

/* ============ CE PRODUCTS SECTION & TABS ============ */
.kv-ce-products-section {
    padding: 48px 80px 104px;
    display: flex;
    flex-direction: column;
    gap: 32px;
}
.kv-products-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
}
.kv-products-heading {
    margin: 0;
    font-size: 44px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: -0.02em;
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
    flex: 1 0 auto;
    min-height: 72px;
    padding: 12px 18px;
    border-radius: 16px;
    border: 1px solid;
    display: flex;
    align-items: center;
    gap: 14px;
    text-align: left;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}
.kv-product-tab:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(7, 28, 51, 0.08);
}
.kv-product-tab-icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.25s ease;
}
.kv-product-tab:hover .kv-product-tab-icon {
    transform: scale(1.06);
}
.kv-product-tab-text {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.kv-product-tab-name {
    font-size: 16px;
    font-weight: 800;
}
.kv-product-tab-role {
    font-size: 13px;
    line-height: 1.4;
}
.kv-product-tab-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: background 0.25s ease;
}
.kv-product-panel {
    background: #FFFFFF;
    border: 1px solid #E3E8E6;
    border-radius: 24px;
    padding: 40px;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 460px;
    gap: 40px;
    align-items: center;
}
.kv-product-info {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.kv-product-mockup-container {
    height: 420px;
    border-radius: 18px;
    background: #F4F6F5;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ============================================================
   RESPONSIVE MEDIA QUERIES FOR CE PRODUCTS SECTION
   ============================================================ */

/* Tablet & Smaller Screens (<= 1024px) */
@media (max-width: 1024px) {
    .kv-ce-products-section {
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
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
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
}

/* Mobile Screens (<= 640px) */
@media (max-width: 640px) {
    .kv-ce-products-section {
        padding: 32px 20px 60px !important;
        gap: 20px !important;
        scroll-margin-top: 80px !important;
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

    .kv-unlinked-pill {
        white-space: normal !important;
        font-size: 13.5px !important;
        line-height: 1.4 !important;
    }

    .kv-products-layout {
        gap: 16px !important;
    }

    .kv-product-tablist {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
    }

    .kv-product-tab {
        min-height: 70px !important;
        padding: 10px 10px !important;
        border-radius: 14px !important;
        gap: 10px !important;
        align-items: flex-start !important;
    }

    .kv-product-tab-icon {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        flex-shrink: 0 !important;
    }

    .kv-product-tab-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .kv-product-tab-name {
        font-size: 14px !important;
        line-height: 1.25 !important;
    }

    .kv-product-tab-role {
        font-size: 11px !important;
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
        font-size: 11px !important;
    }

    .kv-product-info > span:nth-child(2) {
        font-size: 24px !important;
        line-height: 1.2 !important;
    }

    .kv-product-info > span:nth-child(3) {
        font-size: 14.5px !important;
        line-height: 1.5 !important;
    }

    .kv-product-mockup-container {
        min-height: 400px !important;
        height: auto !important;
        padding: 20px 10px !important;
        border-radius: 16px !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .kv-product-mockup-container [style*="display: none"] {
        display: none !important;
    }

    /* Mockups scaling - fills the box with minimal wasted margin (matching Auth) */
    .kv-mockup-auth {
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 !important;
    }

    .kv-mockup-broadcast {
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 !important;
        gap: 16px !important;
    }

    .kv-mockup-broadcast > div:first-child {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .kv-mockup-broadcast > div:first-child > div:first-child {
        width: 135px !important;
        flex-shrink: 0 !important;
    }

    .kv-mockup-broadcast > div:first-child > div:nth-child(2) {
        flex-grow: 1 !important;
        min-width: 20px !important;
        margin: 0 6px !important;
    }

    .kv-mockup-broadcast > div:first-child > div:last-child {
        width: 130px !important;
        flex-shrink: 0 !important;
    }

    .kv-mockup-broadcast > div:last-child {
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .kv-mockup-chatbot {
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 !important;
    }

    .kv-mockup-chatbot .kv-chat-bubble-in[style*="max-width: 90%"] {
        max-width: 96% !important;
    }

    .kv-mockup-chatbot .kv-chat-bubble-in[style*="max-width: 82%"] {
        max-width: 88% !important;
    }

    /* Integration - Vertical Mobile Pipeline */
    .kv-mockup-integration {
        width: 100% !important;
        max-width: 280px !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 auto !important;
    }

    .kv-mockup-integration:not([style*="display: none"]) {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
    }

    .kv-mockup-integration .kv-integration-source {
        width: 100% !important;
        max-width: 270px !important;
        padding: 10px 14px !important;
        border-radius: 12px !important;
        box-sizing: border-box !important;
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
    }

    .kv-mockup-integration .kv-integration-source-header {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
        text-align: left !important;
        min-width: 0 !important;
    }

    .kv-mockup-integration .kv-integration-source-header span:first-child {
        font-size: 11.5px !important;
        font-weight: 700 !important;
    }

    .kv-mockup-integration .kv-integration-source-header span:last-child {
        font-size: 10px !important;
        color: #4A5866 !important;
    }

    .kv-mockup-integration .kv-integration-source-badge {
        display: flex !important;
        flex-direction: column !important;
        gap: 3px !important;
        align-items: flex-end !important;
        flex-shrink: 0 !important;
    }

    .kv-mockup-integration .kv-integration-source-badge span:first-child {
        font-size: 10px !important;
        font-weight: 700 !important;
        padding: 4px 7px !important;
        border-radius: 6px !important;
    }

    .kv-mockup-integration .kv-integration-source-badge span:last-child {
        font-size: 9px !important;
        font-family: 'JetBrains Mono', monospace !important;
    }

    .kv-mockup-integration .kv-integration-connector {
        width: 2px !important;
        height: 18px !important;
        flex-grow: 0 !important;
        background: #DCE3E0 !important;
        position: relative !important;
        margin: 0 auto !important;
    }

    .kv-mockup-integration .kv-integration-connector > span {
        position: absolute !important;
        left: -3px !important;
        top: 0 !important;
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        background: #13803F !important;
        --d: 18px !important;
        animation: kvTravelY 1.6s linear infinite !important;
    }

    .kv-mockup-integration .kv-connector-2 > span {
        animation-delay: 0.8s !important;
    }

    .kv-mockup-integration .kv-integration-node {
        width: 44px !important;
        height: 44px !important;
        border-radius: 13px !important;
        flex-shrink: 0 !important;
        margin: 0 auto !important;
    }

    .kv-mockup-integration .kv-integration-node img {
        width: 22px !important;
        height: 22px !important;
    }

    .kv-mockup-integration .kv-integration-target {
        width: 100% !important;
        max-width: 270px !important;
        box-sizing: border-box !important;
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 7px !important;
        font-size: 11.5px !important;
        line-height: 1.4 !important;
    }

    .kv-mockup-integration .kv-integration-target > div {
        padding: 8px 12px !important;
        font-size: 11.5px !important;
        border-radius: 4px 12px 12px 12px !important;
        box-sizing: border-box !important;
    }

    .kv-mockup-integration .kv-integration-target > span {
        font-size: 9.5px !important;
        color: #4A5866 !important;
        text-align: left !important;
    }

    .kv-mockup-flows {
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 !important;
    }

    /* Omni - Vertical Mobile Pipeline */
    .kv-mockup-omni {
        width: 100% !important;
        max-width: 280px !important;
        box-sizing: border-box !important;
        transform: none !important;
        margin: 0 auto !important;
    }

    .kv-mockup-omni:not([style*="display: none"]) {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
    }

    .kv-mockup-omni .kv-omni-channels {
        width: 100% !important;
        max-width: 280px !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5px !important;
        box-sizing: border-box !important;
    }

    .kv-mockup-omni .kv-omni-ch-item {
        padding: 5px 7px !important;
        gap: 5px !important;
        border-radius: 8px !important;
        font-size: 10.5px !important;
        box-sizing: border-box !important;
        align-items: center !important;
        min-width: 0 !important;
    }

    .kv-mockup-omni .kv-omni-ch-item img {
        width: 15px !important;
        height: 15px !important;
        flex-shrink: 0 !important;
    }

    .kv-mockup-omni .kv-omni-ch-item span span:first-child {
        font-size: 10.5px !important;
        font-weight: 700 !important;
    }

    .kv-mockup-omni .kv-omni-ch-item span span:last-child {
        font-size: 8.5px !important;
        color: #4A5866 !important;
    }

    .kv-mockup-omni .kv-omni-connectors {
        display: none !important;
    }

    .kv-mockup-omni .kv-omni-v-connector {
        display: block !important;
        width: 2px !important;
        height: 16px !important;
        background: #DCE3E0 !important;
        position: relative !important;
        margin: 3px auto !important;
    }

    .kv-mockup-omni .kv-omni-v-connector span {
        position: absolute !important;
        left: -3px !important;
        top: 0 !important;
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        background: #13803F !important;
        --d: 16px !important;
        animation: kvTravelY 1.5s linear infinite !important;
    }

    .kv-mockup-omni .kv-omni-inbox {
        width: 100% !important;
        max-width: 280px !important;
        box-sizing: border-box !important;
        padding: 10px 10px !important;
        gap: 6px !important;
        border-radius: 14px !important;
    }

    .kv-mockup-omni .kv-omni-inbox > span:first-child {
        font-size: 11.5px !important;
        font-weight: 700 !important;
    }

    .kv-mockup-omni .kv-omni-inbox > div[style*="border-radius: 8px"] {
        padding: 5px 8px !important;
        font-size: 10.5px !important;
        gap: 6px !important;
    }

    .kv-mockup-omni .kv-omni-inbox img {
        width: 15px !important;
        height: 15px !important;
    }

    .kv-mockup-omni .kv-omni-inbox span[style*="font-size: 9.5px"] {
        font-size: 9px !important;
        padding: 1px 4px !important;
    }
}

/* Extra Small Screens (<= 380px) */
@media (max-width: 380px) {
    .kv-products-heading {
        font-size: 25px !important;
    }

    .kv-product-tab {
        padding: 8px 8px !important;
        gap: 8px !important;
    }

    .kv-product-tab-name {
        font-size: 12.5px !important;
    }

    .kv-product-tab-role {
        font-size: 10px !important;
    }

    .kv-mockup-broadcast {
        transform: scale(0.96) !important;
        transform-origin: center center !important;
    }

    .kv-mockup-chatbot {
        transform: scale(0.98) !important;
        transform-origin: center center !important;
    }

    .kv-mockup-integration {
        transform: none !important;
        max-width: 270px !important;
    }

    .kv-mockup-flows {
        transform: scale(0.98) !important;
        transform-origin: center center !important;
    }

    .kv-mockup-omni {
        transform: none !important;
        max-width: 270px !important;
    }
}

/* Smallest Screens (<= 350px) */
@media (max-width: 350px) {
    .kv-mockup-integration,
    .kv-mockup-integration .kv-integration-source,
    .kv-mockup-integration .kv-integration-target {
        max-width: 250px !important;
    }

    .kv-mockup-omni,
    .kv-mockup-omni .kv-omni-channels,
    .kv-mockup-omni .kv-omni-inbox {
        max-width: 250px !important;
    }
}

/* Smallest Screens (<= 360px) */
@media (max-width: 360px) {
    .kv-product-tablist {
        grid-template-columns: 1fr !important;
        gap: 8px !important;
    }
}

/* Flows Mockup Styles */
.kv-flow-type-active {
    background: #FFFFFF !important;
    color: #0E1A24 !important;
    box-shadow: 0 1px 3px rgba(7,28,51,0.12) !important;
}
.kv-flow-type-inactive {
    background: transparent !important;
    color: #4A5866 !important;
    box-shadow: none !important;
}

.kv-flow-check-active {
    background: #25D366 !important;
    border: 1.5px solid #25D366 !important;
}
.kv-flow-check-idle {
    background: #FFFFFF !important;
    border: 1.5px solid #D5DDD9 !important;
}

.kv-flow-btn-press {
    transform: scale(0.96) !important;
    background: #1EBE5D !important;
}
.kv-flow-btn-loading {
    background: #1EBE5D !important;
}

@keyframes kvPopIn {
    0% { opacity: 0; transform: scale(0.7); }
    70% { transform: scale(1.08); }
    100% { opacity: 1; transform: scale(1); }
}

@keyframes kvBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.kv-spin { animation: kvSpin 0.9s linear infinite; }
.kv-pop-in { animation: kvPopIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) both; }
.kv-chat-bubble-in { animation: kvBubbleIn 0.32s cubic-bezier(0.16, 1, 0.3, 1) both; }
@keyframes kvBubbleIn {
    0% { opacity: 0; transform: translateY(10px) scale(0.97); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

.kv-ce-control-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 20px;
}
.kv-control-card {
    background: #FFFFFF;
    border: 1px solid #E3E8E6;
    border-radius: 20px;
    padding: 28px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.kv-control-card:hover {
    border-color: #13803F;
    box-shadow: 0 12px 30px rgba(7,28,51,0.06);
    transform: translateY(-2px);
}
@media (max-width: 1024px) {
    .kv-ce-control-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }
}
@media (max-width: 640px) {
    .kv-ce-control-grid {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
    }
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
    margin-top: 104px;
}
.kv-why-heading {
    margin: 0;
    font-size: 44px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: -0.02em;
    position: relative;
    color: #FFFFFF;
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
    padding: 32px 28px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-height: 270px;
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
    transform: translateY(-8px);
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
    box-shadow: 0 10px 30px rgba(43, 212, 106, 0.3);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.kv-why-card:hover .kv-why-icon {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 14px 34px rgba(43, 212, 106, 0.5);
}
.kv-why-icon svg {
    transition: transform 0.3s ease;
}
.kv-why-card:hover .kv-why-icon svg {
    transform: scale(1.06);
}
.kv-why-title {
    font-size: 20px;
    line-height: 1.25;
    font-weight: 800;
    color: #FFFFFF;
    padding-top: 6px;
    margin: 0;
}
.kv-why-desc {
    font-size: 15px;
    line-height: 1.6;
    color: #94A3B8;
    margin: 0;
}
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
        min-height: auto !important;
    }
}
@media (max-width: 640px) {
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
        padding: 20px !important;
    }
}

/* ============================================================
   RESPONSIVE MEDIA QUERIES FOR HERO & JOURNEY SECTIONS (MOBILE)
   ============================================================ */
@media (max-width: 768px) {
    /* Hero section dashboard */
    .kv-ce-hero-dashboard {
        width: 600px !important;
        max-width: none !important;
        flex-shrink: 0 !important;
        transform: scale(0.55) !important;
        transform-origin: top center !important;
        margin: 0 auto -250px !important;
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
        padding: 18px 6px 16px !important;
        border-radius: 18px !important;
    }
    .kv-journey-track-container {
        height: 104px !important;
    }
    .kv-journey-track-bg,
    .kv-journey-track-fill {
        top: 34px !important;
        height: 3px !important;
    }
    .kv-journey-pointer {
        display: none !important;
    }

    /* Journey Tab Buttons (4 stages) */
    .kv-journey-tab-btn {
        top: 14px !important;
        width: 72px !important;
        gap: 3px !important;
        padding: 0 !important;
    }
    .kv-journey-tab-btn > span:first-child {
        width: 40px !important;
        height: 40px !important;
    }
    .kv-journey-tab-btn > span:first-child svg {
        width: 17px !important;
        height: 17px !important;
    }
    .kv-journey-tab-btn > span:nth-child(2) {
        font-size: 9.5px !important;
    }
    .kv-journey-tab-btn > span:last-child {
        font-size: 11.5px !important;
        line-height: 1.15 !important;
        word-break: break-word !important;
    }

    /* Journey Tabpanel Card */
    .kv-journey-tabpanel {
        padding: 24px 16px !important;
        border-radius: 18px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 20px !important;
    }
    .kv-journey-tabpanel span[x-text*="headline"] {
        font-size: 24px !important;
        line-height: 1.2 !important;
    }
    .kv-journey-tabpanel span[x-text*="sub"] {
        font-size: 14px !important;
        line-height: 1.55 !important;
    }
    .kv-journey-tabpanel button {
        width: 100% !important;
        justify-content: center !important;
        font-size: 14px !important;
        padding: 12px 18px !important;
        min-height: 46px !important;
    }
}

@media (max-width: 380px) {
    .kv-ce-hero-dashboard {
        transform: scale(0.48) !important;
        margin: 0 auto -290px !important;
    }
    .kv-journey-tab-btn {
        width: 66px !important;
    }
    .kv-journey-tab-btn > span:first-child {
        width: 36px !important;
        height: 36px !important;
    }
    .kv-journey-tab-btn > span:last-child {
        font-size: 10.5px !important;
    }
}
</style>
@endpush

@php
    $isIdLocale = app()->getLocale() === 'id';
    $heroTitle = $page?->titleBlock('hero_title', [
        'prefix' => $isIdLocale ? 'Cara lebih cerdas untuk' : 'The smarter way to',
        'main' => $isIdLocale ? 'berinteraksi dengan pelanggan.' : 'engage customers.'
    ]);
    $heroSubtitle = $page?->block('hero_subtitle', $isIdLocale
        ? 'Jangkau lebih banyak pelanggan, balas lebih cepat, dan buat percakapan lancar di WhatsApp dan channel lainnya.'
        : 'Reach more customers, respond faster, and keep conversations seamless on WhatsApp and beyond.');
    $heroCta = $page?->buttonBlock('hero_cta', ['text' => $isIdLocale ? 'Jadwalkan demo' : 'Book a demo', 'url' => '/book-demo']);
    $channelsTitle = $page?->titleBlock('channels_title', ['prefix' => $isIdLocale ? 'Setiap pesan yang terlewat adalah' : 'Every missed message is a', 'main' => $isIdLocale ? 'peluang yang hilang.' : 'missed sale.']);
    if ($isIdLocale) {
        if (($channelsTitle['main'] ?? '') === 'penjualan yang hilang.') {
            $channelsTitle['main'] = 'peluang yang hilang.';
        }
        if (($channelsTitle['prefix'] ?? '') === 'Tiap pesan yang terlewat adalah') {
            $channelsTitle['prefix'] = 'Setiap pesan yang terlewat adalah';
        }
    }
    $featuresTitle = $page?->titleBlock('features_title', ['prefix' => $isIdLocale ? 'Satu platform.' : 'One platform.', 'main' => $isIdLocale ? 'Tanpa pesan yang terlewat.' : 'Zero missed messages.']);
    $bottomCtaTitle = $page?->block('bottom_cta_title', $isIdLocale ? 'Mulai terhubung dengan pelanggan lewat WhatsApp.' : 'Start engaging customers on WhatsApp.');
    if ($isIdLocale && $bottomCtaTitle === 'Mulai berinteraksi dengan pelanggan di WhatsApp.') {
        $bottomCtaTitle = 'Mulai terhubung dengan pelanggan lewat WhatsApp.';
    }
@endphp

@section('content')
<div class="kv-canvas-wrap" x-data="{
    stage: 0,
    product: 'broadcast',
    linked: true,
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
            sub: '{{ t('kouventa.Guide new customers through registration, verification and data submission, all in chat.', 'Guide new customers through registration, verification and data submission, all in chat.') }}',
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
            desc: '{{ t('kouventa.Static and AI chatbots answer fast, and hand over to a human when needed.', 'Static and AI chatbots answer fast, and hand over to a human when needed.') }}',
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
            desc: '{{ t('kouventa.Trigger WhatsApp messages from your business systems: reminders, orders, deliveries, appointments.', 'Trigger WhatsApp messages from your business systems: reminders, orders, deliveries, appointments.') }}',
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
        this.$nextTick(() => {
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
    },
    unlink() {
        this.linked = false;
    }
}">
<!-- ============ NAV + HERO ============ -->
<section class="kv-hero-container" style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<nav aria-label="Products" style="height: 56px; padding: 0 80px; display: flex; align-items: center; gap: 32px; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 4">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #93A4B6">{{ t('kouventa.Products', 'Products') }}</span>
<a href="{{ localized_url('/product/ai') }}" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 600; color: #93A4B6; border-bottom: 2px solid transparent">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</a>
<a href="{{ localized_url('/product/customer-engagement') }}" aria-current="page" style="height: 56px; box-sizing: border-box; display: flex; align-items: center; padding: 0 4px; font-size: 15px; font-weight: 700; color: #FFFFFF; border-bottom: 2px solid #2BD46A">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</a>
</nav>

<div class="kv-hero-section" style="height: 704px; box-sizing: border-box; padding: 0 80px; display: grid; grid-template-columns: minmax(0, 1fr) 600px; gap: 56px; align-items: center; position: relative">
<div class="kv-hero-content" style="display: flex; flex-direction: column; gap: 26px">
<span style="align-self: flex-start; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 8px 14px; border-radius: 999px; background: rgba(43,212,106,0.12); color: #2BD46A"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>{{ t('kouventa.Official Meta Business Solution Provider', 'Official Meta Business Solution Provider') }}</span>
<h1 style="margin: 0; font-size: 72px; line-height: 1.02; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'The smarter way to' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'engage customers.' }}</span></h1>
<p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 620px">{!! nl2br(e($heroSubtitle)) !!}</p>
<div style="display: flex; gap: 12px; padding-top: 4px">
<a href="{{ localized_url($heroCta['url'] ?? '/book-demo') }}" style="background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ $heroCta['text'] ?? 'Book a demo' }}</a>
</div>
</div>

<!-- hero dashboard: Omnichannel Unified Inbox -->
<div class="kv-ce-hero-dashboard-wrap" style="width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center">
<div role="img" class="kv-ce-hero-dashboard" aria-label="Animated illustration: the Kouventa dashboard shows conversations from WhatsApp, Instagram, Facebook and Live Chat in one inbox. One customer starts on Instagram DM, is answered by an agent, then followed up by a WhatsApp broadcast and answered by Kouventa AI." data-kv-fit="600" style="width: 600px; justify-self: end; background: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 40px 80px rgba(0,0,0,0.4); color: #0E1A24; display: flex; flex-direction: column; animation: kvFloat 6s ease-in-out infinite">
<div style="height: 44px; flex-shrink: 0; display: flex; align-items: center; gap: 10px; padding: 0 16px; border-bottom: 1px solid #E3E8E6">
<svg width="17.5" height="22" viewBox="0 0 96 121" role="img" aria-label="Kouventa" style="display: block; flex-shrink: 0"><rect x="0" y="0" width="40" height="121" fill="#12BB3A"></rect><path d="M40 0H96L40 60.5L96 121H40Z" fill="#82D48B"></path></svg>
<span style="font-size: 13px; font-weight: 800">Kouventa</span>
<span style="font-size: 12px; color: #4A5866">Interactions › Chat</span>
<span style="margin-left: auto; display: flex; align-items: center; gap: 6px">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 14px; height: 14px; object-fit: contain; display: block; flex-shrink: 0">
<img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 14px; height: 14px; object-fit: contain; display: block; flex-shrink: 0">
<img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 14px; height: 14px; object-fit: contain; display: block; flex-shrink: 0">
<img src="{{ theme_asset('images/icon-livechat.png') }}" alt="Live Chat" style="width: 14px; height: 14px; object-fit: contain; display: block; flex-shrink: 0">
</span>
</div>
<div style="display: flex; gap: 18px; padding: 0 16px; font-size: 12px; border-bottom: 1px solid #E3E8E6; flex-shrink: 0">
<span style="padding: 10px 2px; color: #13803F; font-weight: 700; border-bottom: 2px solid #13803F">{{ $isIdLocale ? 'Semua channel' : t('kouventa.All channels', 'All channels') }}</span>
<span style="padding: 10px 2px; color: #4A5866; border-bottom: 2px solid transparent">WhatsApp</span>
<span style="padding: 10px 2px; color: #4A5866; border-bottom: 2px solid transparent">Instagram</span>
<span style="padding: 10px 2px; color: #4A5866; border-bottom: 2px solid transparent">Facebook</span>
<span style="padding: 10px 2px; color: #4A5866; border-bottom: 2px solid transparent">Live Chat</span>
</div>
<div style="display: grid; grid-template-columns: 208px minmax(0, 1fr); grid-template-rows: 492px">
<div style="border-right: 1px solid #E3E8E6; padding: 8px 6px; display: flex; flex-direction: column; gap: 2px; overflow: hidden">
<div style="display: flex; gap: 8px; padding: 9px 10px; border-radius: 10px; background: #EEF6F1">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #DDF3E4; color: #0B5E2C; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">RS</span>
<span style="display: flex; flex-direction: column; gap: 2px; min-width: 0; flex-grow: 1">
<span style="font-size: 12px; font-weight: 700; color: #0E1A24">Rina S.</span>
<span style="font-size: 11px; color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Ukuran M masih ada?' : t('kouventa.Is size M still available?', 'Is size M still available?') }}</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866; white-space: nowrap">
<img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
Instagram · WhatsApp
</span>
</span>
</div>
<div style="display: flex; gap: 8px; padding: 9px 10px; border-radius: 10px">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #E3ECF7; color: #1E4E8C; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">BU</span>
<span style="display: flex; flex-direction: column; gap: 2px; min-width: 0; flex-grow: 1">
<span style="font-size: 12px; font-weight: 700; color: #0E1A24">Budi</span>
<span style="font-size: 11px; color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Pesanan #2219 saya di mana?' : t('kouventa.Where is my order #2219?', 'Where is my order #2219?') }}</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
WhatsApp
</span>
</span>
</div>
<div style="display: flex; gap: 8px; padding: 9px 10px; border-radius: 10px">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F6E3EC; color: #8C1E4E; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">SA</span>
<span style="display: flex; flex-direction: column; gap: 2px; min-width: 0; flex-grow: 1">
<span style="font-size: 12px; font-weight: 700; color: #0E1A24">Sari</span>
<span style="font-size: 11px; color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Ongkir ke Bandung gratis?' : t('kouventa.Is delivery free to Bandung?', 'Is delivery free to Bandung?') }}</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-facebook.png') }}" alt="Facebook" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
Facebook
</span>
</span>
</div>
<div style="display: flex; gap: 8px; padding: 9px 10px; border-radius: 10px">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #F2EBDC; color: #7A5A12; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">DI</span>
<span style="display: flex; flex-direction: column; gap: 2px; min-width: 0; flex-grow: 1">
<span style="font-size: 12px; font-weight: 700; color: #0E1A24">Dimas</span>
<span style="font-size: 11px; color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Halo, saya butuh bantuan untuk akun saya' : t('kouventa.Hi, I need help with my account', 'Hi, I need help with my account') }}</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-livechat.png') }}" alt="Live Chat" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
Live Chat
</span>
</span>
</div>
<div style="display: flex; gap: 8px; padding: 9px 10px; border-radius: 10px">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #E6E9F7; color: #3B3F8C; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">MA</span>
<span style="display: flex; flex-direction: column; gap: 2px; min-width: 0; flex-grow: 1">
<span style="font-size: 12px; font-weight: 700; color: #0E1A24">Maya</span>
<span style="font-size: 11px; color: #4A5866; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">{{ $isIdLocale ? 'Terima kasih, sudah diterima!' : t('kouventa.Thanks, received!', 'Thanks, received!') }}</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
WhatsApp
</span>
</span>
</div>
</div>

<div style="display: flex; flex-direction: column; min-width: 0; min-height: 0; background: #F7F9F8">
<div style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #FFFFFF; border-bottom: 1px solid #E3E8E6">
<span style="width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #DDF3E4; color: #0B5E2C; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center">RS</span>
<span style="display: flex; flex-direction: column; gap: 2px; flex-grow: 1; min-width: 0">
<span style="font-size: 12.5px; font-weight: 700">Rina S.</span>
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
{{ $isIdLocale ? 'Satu profil, dua channel' : t('kouventa.One profile, two channels', 'One profile, two channels') }}
</span>
</span>
<span style="font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 7px; background: #F2C230; color: #3A2A00">{{ $isIdLocale ? 'Ringkas' : t('kouventa.Summarize', 'Summarize') }}</span>
</div>

<div style="flex-grow: 1; min-height: 0; overflow: hidden; padding: 10px 14px; display: flex; flex-direction: column; gap: 7px">
<div style="align-self: flex-start; max-width: 78%; display: flex; flex-direction: column; gap: 3px; animation: kvM1 16s ease infinite both">
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
{{ $isIdLocale ? 'DM Instagram · 10:02' : 'Instagram DM · 10:02' }}
</span>
<span style="background: #FFFFFF; border: 1px solid #E3E8E6; padding: 7px 10px; border-radius: 4px 12px 12px; font-size: 12px; line-height: 1.45; color: #0E1A24">
{{ $isIdLocale ? 'Halo! Weekend Sale masih berlaku?' : t('kouventa.Hi! Is the Weekend Sale still on?', 'Hi! Is the Weekend Sale still on?') }}
</span>
</div>

<div style="align-self: flex-end; max-width: 78%; display: flex; flex-direction: column; gap: 3px; animation: kvM2 16s ease infinite both">
<span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-instagram.png') }}" alt="Instagram" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
{{ $isIdLocale ? 'Agen · DM Instagram · 10:04' : 'Agent · Instagram DM · 10:04' }}
</span>
<span style="background: #DDF3E4; padding: 7px 10px; border-radius: 12px 4px 12px 12px; font-size: 12px; line-height: 1.45; color: #0E1A24">
{{ $isIdLocale ? 'Masih, sampai Minggu! Mau akses awal lewat WhatsApp?' : t('kouventa.Yes, until Sunday! Want early access on WhatsApp?', 'Yes, until Sunday! Want early access on WhatsApp?') }}
</span>
</div>

<div style="display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 700; color: #4A5866; animation: kvM3 16s ease infinite both">
<span style="flex-grow: 1; height: 1px; background: #E3E8E6"></span>
{{ $isIdLocale ? 'Esok hari · campaign WhatsApp' : t('kouventa.Next day · WhatsApp campaign', 'Next day · WhatsApp campaign') }}
<span style="flex-grow: 1; height: 1px; background: #E3E8E6"></span>
</div>

<div style="align-self: flex-end; width: 62%; display: flex; flex-direction: column; gap: 3px; animation: kvM3 16s ease infinite both">
<span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
{{ $isIdLocale ? 'Broadcast campaign · WhatsApp · 09:00' : 'Campaign broadcast · WhatsApp · 09:00' }}
</span>
<span style="background: #DDF3E4; border-radius: 12px 4px 12px 12px; overflow: hidden; font-size: 12px; line-height: 1.45; color: #0E1A24">
<span style="display: block; background: #071C33; color: #2BD46A; font-weight: 800; font-size: 12px; text-align: center; padding: 7px">WEEKEND SALE</span>
<span style="display: block; padding: 7px 10px">{{ $isIdLocale ? 'Halo Rina, akses awal Anda sudah dibuka: diskon 30% sampai Minggu.' : t('kouventa.Hi Rina, your early access is open: 30% off until Sunday.', 'Hi Rina, your early access is open: 30% off until Sunday.') }}</span>
</span>
</div>

<div style="align-self: flex-start; max-width: 78%; display: flex; flex-direction: column; gap: 3px; animation: kvM4 16s ease infinite both">
<span style="display: flex; align-items: center; gap: 4px; font-size: 10px; color: #4A5866">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 11px; height: 11px; object-fit: contain; display: block; flex-shrink: 0">
WhatsApp · 09:12
</span>
<span style="background: #FFFFFF; border: 1px solid #E3E8E6; padding: 7px 10px; border-radius: 4px 12px 12px; font-size: 12px; line-height: 1.45; color: #0E1A24">
{{ $isIdLocale ? 'Ukuran M masih ada?' : t('kouventa.Is size M still available?', 'Is size M still available?') }}
</span>
</div>

<div style="align-self: flex-end; max-width: 78%; display: flex; flex-direction: column; gap: 3px; animation: kvM5 16s ease infinite both">
<span style="align-self: flex-end; display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #13803F">
<svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 6.5l1.7 4.8 4.8 1.7-4.8 1.7L10 19.5l-1.7-4.8L3.5 13l4.8-1.7Z"></path><path d="M18 3.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8Z"></path></svg>
Kouventa AI · WhatsApp · 09:12
</span>
<span style="background: #DDF3E4; padding: 7px 10px; border-radius: 12px 4px 12px 12px; font-size: 12px; line-height: 1.45; color: #0E1A24">
{{ $isIdLocale ? 'Ukuran M masih tersedia. Sudah saya simpankan untuk Anda.' : t("kouventa.Yes, size M is in stock. I've reserved it for you.", "Yes, size M is in stock. I've reserved it for you.") }}
</span>
</div>
</div>

<div style="flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 8px 14px 12px">
<span style="flex-grow: 1; height: 32px; border-radius: 8px; background: #FFFFFF; border: 1px solid #E3E8E6; display: flex; align-items: center; gap: 6px; padding: 0 10px; font-size: 11.5px; color: #8696A0">
<img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="WhatsApp" style="width: 12px; height: 12px; object-fit: contain; display: block; flex-shrink: 0">
{{ $isIdLocale ? 'Balas di WhatsApp…' : t('kouventa.Reply on WhatsApp…', 'Reply on WhatsApp…') }}
</span>
<span style="height: 32px; padding: 0 14px; border-radius: 8px; background: #13803F; color: #FFFFFF; font-size: 11.5px; font-weight: 700; display: flex; align-items: center">{{ $isIdLocale ? 'Kirim' : t('kouventa.Send', 'Send') }}</span>
</div>
</div>
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
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; transform-origin: 50% 50%; animation: kvClockTick 3.2s ease-in-out infinite"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2M9 2h6"></path></svg></span>
<span style="display: flex; gap: 4px"><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .2s infinite"></span><span style="width: 8px; height: 8px; border-radius: 50%; background: #E79F8E; animation: kvBlink 1s ease .4s infinite"></span></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Customers wait too long for a reply', 'Customers wait too long for a reply') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.', 'Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 0.8s both">
<div aria-hidden="true" style="height: 56px; display: flex; align-items: center; gap: 12px">
<span style="width: 52px; height: 52px; border-radius: 14px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; animation: kvBlockedPulse 3.2s ease-in-out infinite"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m4.9 4.9 14.2 14.2"></path></svg></span>
<span style="display: flex; gap: 6px"><span style="width: 16px; height: 6px; border-radius: 3px; background: #F0C4BA; transform-origin: left; animation: kvSignalDash 1.8s ease-in-out infinite"></span><span style="width: 24px; height: 6px; border-radius: 3px; background: #E79F8E; transform-origin: left; animation: kvSignalDash 1.8s ease-in-out .3s infinite"></span><span style="width: 12px; height: 6px; border-radius: 3px; background: #F0C4BA; transform-origin: left; animation: kvSignalDash 1.8s ease-in-out .6s infinite"></span></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Campaigns don\'t reach the right people', 'Campaigns don\'t reach the right people') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.Promotions sent manually reach too few customers, and there\'s no clear view of who received, read, or acted on them.', 'Promotions sent manually reach too few customers, and there\'s no clear view of who received, read, or acted on them.') }}</p>
</article>
<article style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 16px; animation: kvIn .6s ease 1.4s both">
<div aria-hidden="true" style="height: 56px; position: relative; width: 140px">
<span style="position: absolute; left: 0; top: 12px; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; animation: kvFloatDrift1 2.8s ease-in-out infinite alternate"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span>
<span style="position: absolute; left: 44px; top: 0; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; animation: kvFloatDrift2 3.2s ease-in-out .4s infinite alternate"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></span>
<span style="position: absolute; left: 88px; top: 16px; width: 36px; height: 36px; border-radius: 10px; background: #FBE6E1; color: #B4412A; display: flex; align-items: center; justify-content: center; animation: kvFloatDrift3 2.5s ease-in-out .8s infinite alternate"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></span>
</div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">{{ t('kouventa.Conversations are scattered across channels', 'Conversations are scattered across channels') }}</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5866">{{ t('kouventa.WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.', 'WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.') }}</p>
</article>
</div>
</section>

<!-- ============ JOURNEY ============ -->
<section id="journey" class="kv-journey-section" style="padding: 104px 80px 0; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 14px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Never miss a', 'Never miss a') }} <span style="color: #13803F">{{ t('kouventa.message.', 'message.') }}</span></h2>
<p style="margin: 0; font-size: 18px; line-height: 1.5; color: #4A5866">{{ t('kouventa.From first hello to after-sales. Pick a stage to see which products help.', 'From first hello to after-sales. Pick a stage to see which products help.') }}</p>
</div>
<div role="tablist" class="kv-journey-tablist-wrap" aria-label="Customer journey stage" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 28px 0 26px">
<div class="kv-journey-track-container" style="position: relative; height: 176px">
<span aria-hidden="true" class="kv-journey-track-bg" style="position: absolute; top: 76px; left: 12.5%; width: 75%; height: 4px; border-radius: 4px; background: repeating-linear-gradient(90deg, #C9D2CE 0 10px, transparent 10px 18px)"></span>
<span aria-hidden="true" class="kv-ce-track-fill kv-journey-track-fill" style="position: absolute; top: 76px; left: 12.5%; height: 4px; border-radius: 4px; background: #13803F; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1)" :style="{ width: (stage * 25) + '%' }"></span>
<span aria-hidden="true" class="kv-ce-customer-pointer kv-journey-pointer" style="position: absolute; top: 0; transform: translateX(-50%); transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; z-index: 10" :style="{ left: (12.5 + stage * 25) + '%' }">
<span style="animation: kvFloat 2.4s ease-in-out infinite; display: flex; flex-direction: column; align-items: center">
<span style="display: flex; align-items: center; gap: 8px; padding: 5px 14px 5px 5px; border-radius: 999px; background: #071C33; color: #FFFFFF; font-size: 13px; font-weight: 700; white-space: nowrap; box-shadow: 0 6px 16px rgba(7, 28, 51, 0.18)"><span style="width: 26px; height: 26px; border-radius: 50%; background: #2BD46A; color: #071C33; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg></span>{{ t('kouventa.Your customer', 'Your customer') }}</span>
<span style="width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 7px solid #071C33"></span>
</span>
</span>

<!-- Stage 0: Prospecting -->
<button type="button" role="tab" class="kv-journey-tab-btn" :aria-selected="stage === 0" @click="pickStage(0)" style="position: absolute; top: 50px; left: 12.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
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
<button type="button" role="tab" class="kv-journey-tab-btn" :aria-selected="stage === 1" @click="pickStage(1)" style="position: absolute; top: 50px; left: 37.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
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
<button type="button" role="tab" class="kv-journey-tab-btn" :aria-selected="stage === 2" @click="pickStage(2)" style="position: absolute; top: 50px; left: 62.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
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
<button type="button" role="tab" class="kv-journey-tab-btn" :aria-selected="stage === 3" @click="pickStage(3)" style="position: absolute; top: 50px; left: 87.5%; transform: translateX(-50%); width: 220px; padding: 0; border: 0; background: transparent; color: #0E1A24; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; cursor: pointer">
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

<div role="tabpanel" class="kv-journey-tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px 44px; display: flex; justify-content: space-between; align-items: center; gap: 48px">
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
<section id="products" class="kv-ce-products-section" style="padding: 48px 80px 104px; display: flex; flex-direction: column; gap: 32px">
<div class="kv-products-header" style="display: flex; flex-direction: column; align-items: flex-start; gap: 16px">
<h2 class="kv-products-heading" style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">@if(!empty($featuresTitle['prefix'])){{ $featuresTitle['prefix'] }} <span style="color: #13803F">{{ $featuresTitle['main'] }}</span>@else{{ $featuresTitle['main'] ?? $featuresTitle }}@endif</h2>
<span class="kv-linked-pill" x-show="linked" x-cloak style="min-height: 48px; box-sizing: border-box; align-items: center; gap: 10px; padding: 6px 6px 6px 16px; border-radius: 999px; background: #FFFFFF; border: 1px solid #E3E8E6; font-size: 15px; color: #4A5866; white-space: nowrap">
<span class="kv-pill-dot" style="width: 10px; height: 10px; border-radius: 50%; background: #13803F; flex-shrink: 0; display: inline-block"></span>
<span>{{ t('kouventa.Highlighted: how each product helps in', 'Highlighted: how each product helps in') }} <strong style="color: #0E1A24" x-text="stages[stage].name">{{ t('kouventa.Prospecting', 'Prospecting') }}</strong></span>
<button type="button" @click="unlink()" class="kv-btn-show-all" style="margin-left: 6px; white-space: nowrap; flex-shrink: 0; min-height: 36px; padding: 6px 14px; border-radius: 999px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer">{{ t('kouventa.Show all products', 'Show all products') }}<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></button>
</span>
<span class="kv-unlinked-pill" x-show="!linked" style="min-height: 48px; align-items: center; font-size: 15px; color: #4A5866">
<a href="#journey" @click.prevent="document.getElementById('journey')?.scrollIntoView({ behavior: 'smooth' })" class="kv-unlinked-link" style="display: inline-flex; align-items: center; gap: 8px; color: #4A5866; text-decoration: none; cursor: pointer">
<span>{{ t('kouventa.Pick a journey stage above to see which products help', 'Pick a journey stage above to see which products help') }}</span>
<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</a>
</span>
</div>

<div class="kv-products-layout" style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; align-items: stretch">
<div class="kv-product-tablist" role="tablist" aria-label="Products" aria-orientation="vertical" style="display: flex; flex-direction: column; gap: 10px">

<!-- Auth Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'auth'" @click="product = 'auth'"
    :style="{
        background: product === 'auth' ? '#071C33' : '#FFFFFF',
        color: product === 'auth' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'auth' ? '#071C33' : (isToolInStage('auth') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('auth') || product === 'auth') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'auth' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'auth' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.Authentication', 'Authentication') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('auth')" x-text="getToolRole('auth')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('auth')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Secure OTP verification', 'Secure OTP verification') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('auth') ? (product === 'auth' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Broadcast Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'broadcast'" @click="product = 'broadcast'"
    :style="{
        background: product === 'broadcast' ? '#071C33' : '#FFFFFF',
        color: product === 'broadcast' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'broadcast' ? '#071C33' : (isToolInStage('broadcast') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('broadcast') || product === 'broadcast') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'broadcast' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'broadcast' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.Broadcasting', 'Broadcasting') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('broadcast')" x-text="getToolRole('broadcast')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('broadcast')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Campaigns at scale', 'Campaigns at scale') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('broadcast') ? (product === 'broadcast' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Chatbot Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'chatbot'" @click="product = 'chatbot'"
    :style="{
        background: product === 'chatbot' ? '#071C33' : '#FFFFFF',
        color: product === 'chatbot' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'chatbot' ? '#071C33' : (isToolInStage('chatbot') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('chatbot') || product === 'chatbot') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'chatbot' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'chatbot' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="3"></rect><path d="M12 8V5M9 13h.01M15 13h.01M10 17h4"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.Chatbot', 'Chatbot') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('chatbot')" x-text="getToolRole('chatbot')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('chatbot')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.AI & predefined chatbots', 'AI & predefined chatbots') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('chatbot') ? (product === 'chatbot' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Integration Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'integration'" @click="product = 'integration'"
    :style="{
        background: product === 'integration' ? '#071C33' : '#FFFFFF',
        color: product === 'integration' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'integration' ? '#071C33' : (isToolInStage('integration') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('integration') || product === 'integration') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'integration' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'integration' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10H7z"></path><path d="M3 9h4M3 15h4M17 9h4M17 15h4M9 3v4M15 3v4M9 17v4M15 17v4"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.Cross Platform Integration', 'Cross Platform Integration') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('integration')" x-text="getToolRole('integration')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('integration')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Automated system updates', 'Automated system updates') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('integration') ? (product === 'integration' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Flows Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'flows'" @click="product = 'flows'"
    :style="{
        background: product === 'flows' ? '#071C33' : '#FFFFFF',
        color: product === 'flows' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'flows' ? '#071C33' : (isToolInStage('flows') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('flows') || product === 'flows') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'flows' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'flows' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M9 8h6M9 12h6M9 16h3"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.WhatsApp Flows', 'WhatsApp Flows') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('flows')" x-text="getToolRole('flows')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('flows')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.Forms inside the chat', 'Forms inside the chat') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('flows') ? (product === 'flows' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

<!-- Omni Product Tab -->
<button type="button" role="tab" class="kv-product-tab" :aria-selected="product === 'omni'" @click="product = 'omni'"
    :style="{
        background: product === 'omni' ? '#071C33' : '#FFFFFF',
        color: product === 'omni' ? '#FFFFFF' : '#0E1A24',
        borderColor: product === 'omni' ? '#071C33' : (isToolInStage('omni') ? '#13803F' : '#E3E8E6'),
        opacity: (!linked || isToolInStage('omni') || product === 'omni') ? 1 : 0.6
    }"
    style="flex: 1 0 auto; min-height: 72px; padding: 12px 18px; border-radius: 16px; border: 1px solid; display: flex; align-items: center; gap: 14px; text-align: left; cursor: pointer; transition: all 0.2s">
<span class="kv-product-tab-icon" :style="{
    background: product === 'omni' ? 'rgba(43,212,106,0.18)' : '#EAF7EF',
    color: product === 'omni' ? '#2BD46A' : '#13803F'
}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1Z"></path></svg>
</span>
<span class="kv-product-tab-text" style="flex-grow: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0">
<span class="kv-product-tab-name" style="font-size: 16px; font-weight: 800">{{ t('kouventa.Omnichannel', 'Omnichannel') }}</span>
<span class="kv-product-tab-role" x-show="isToolInStage('omni')" x-text="getToolRole('omni')" style="font-size: 13px; line-height: 1.4; font-weight: 500; opacity: 0.85"></span>
<span class="kv-product-tab-role" x-show="!isToolInStage('omni')" style="font-size: 13px; line-height: 1.4; opacity: 0.75">{{ t('kouventa.One inbox for all channels', 'One inbox for all channels') }}</span>
</span>
<span class="kv-product-tab-dot" :style="{ background: isToolInStage('omni') ? (product === 'omni' ? '#2BD46A' : '#13803F') : 'transparent' }" style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0"></span>
</button>

</div>

<!-- Product Detail Panel (Right Side) -->
<div class="kv-product-panel" role="tabpanel" style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1fr) 460px; gap: 40px; align-items: center">
<div class="kv-product-info" style="display: flex; flex-direction: column; gap: 18px">
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
<div class="kv-product-mockup-container" role="img" :aria-label="products[product].alt" style="height: 420px; border-radius: 18px; background: #F4F6F5; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center">

<!-- Auth Illustration -->
<div class="kv-flex-col kv-mockup-auth" x-show="product === 'auth'" style="width: 300px; gap: 14px">
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
<div class="kv-flex-col kv-mockup-broadcast" x-show="product === 'broadcast'" x-data="kvBroadcastCounter()" style="width: 400px; gap: 22px">
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
<div style="display: grid; grid-template-columns: 70px minmax(0, 1fr) 36px; gap: 8px 10px; align-items: center; font-size: 12px; color: #4A5866">
<span>{{ t('kouventa.Delivered', 'Delivered') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px; overflow: hidden"><div :style="{ width: p1 + '%' }" style="width: 0%; height: 8px; border-radius: 999px; background: #13803F"></div></div><span style="text-align: right; font-weight: 700; color: #0E1A24; font-variant-numeric: tabular-nums" x-text="p1 + '%'">0%</span>
<span>{{ t('kouventa.Read', 'Read') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px; overflow: hidden"><div :style="{ width: p2 + '%' }" style="width: 0%; height: 8px; border-radius: 999px; background: #2E9E5B"></div></div><span style="text-align: right; font-weight: 700; color: #0E1A24; font-variant-numeric: tabular-nums" x-text="p2 + '%'">0%</span>
<span>{{ t('kouventa.Clicked', 'Clicked') }}</span><div style="height: 8px; background: #EEF2F0; border-radius: 999px; overflow: hidden"><div :style="{ width: p3 + '%' }" style="width: 0%; height: 8px; border-radius: 999px; background: #071C33"></div></div><span style="text-align: right; font-weight: 700; color: #0E1A24; font-variant-numeric: tabular-nums" x-text="p3 + '%'">0%</span>
</div>
</div>
</div>

<!-- Chatbot Illustration -->
<div class="kv-flex-col kv-mockup-chatbot" x-show="product === 'chatbot'" x-data="kvChatbotDemo()" style="width: 100%; max-width: 380px; gap: 7px; font-size: 12px; line-height: 1.4; transition: opacity 0.35s ease" :style="{ opacity: isResetting ? 0 : 1 }">

    <!-- Bubble 1: Automation Flow -->
    <div x-show="step >= 1" class="kv-chat-bubble-in" style="align-self: flex-start; max-width: 90%; background: #FFFFFF; padding: 9px 13px; border-radius: 13px; box-shadow: 0 4px 18px rgba(7,28,51,0.04)">
        <div style="font-size: 10px; font-weight: 800; color: #4A5866; letter-spacing: 0.04em; margin-bottom: 3px">CHAT AUTOMATION FLOW</div>
        <div style="color: #0E1A24; font-weight: 500; font-size: 12.5px; line-height: 1.35">{{ $isIdLocale ? 'Halo! Ada yang bisa kami bantu hari ini?' : t('kouventa.Hi! How can we help you today?', 'Hi! How can we help you today?') }}</div>
        <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-top: 6px">
            <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #13803F; color: #FFFFFF; border: 1px solid #13803F">{{ $isIdLocale ? 'Lacak pesanan' : t('kouventa.Track order', 'Track order') }}</span>
            <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #FFFFFF; color: #13803F; border: 1px solid #13803F">Promo</span>
            <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #FFFFFF; color: #13803F; border: 1px solid #13803F">{{ $isIdLocale ? 'Jam buka toko' : t('kouventa.Store hours', 'Store hours') }}</span>
        </div>
    </div>

    <!-- Bubble 2: User Inquiry -->
    <div x-show="step >= 2" class="kv-chat-bubble-in" style="align-self: flex-end; max-width: 82%; background: #071C33; color: #FFFFFF; padding: 8px 13px; border-radius: 13px 4px 13px 13px; font-size: 12px; line-height: 1.4; box-shadow: 0 4px 14px rgba(7,28,51,0.08)">
        {{ $isIdLocale ? 'Status paket saya terkirim, tapi saya belum menerimanya.' : t('kouventa.My package says delivered, but I don\'t have it.', 'My package says delivered, but I don\'t have it.') }}
    </div>

    <!-- Bubble 3: AI Bot Handover -->
    <div x-show="step >= 3" class="kv-chat-bubble-in" style="align-self: flex-start; max-width: 90%; background: #FFFFFF; padding: 9px 13px; border-radius: 4px 13px 13px 13px; box-shadow: 0 4px 18px rgba(7,28,51,0.04)">
        <div style="font-size: 10px; font-weight: 800; color: #13803F; letter-spacing: 0.04em; margin-bottom: 3px">{{ $isIdLocale ? 'BOT AI · MENGAMBIL ALIH' : t('kouventa.AI BOT · TOOK OVER', 'AI BOT · TOOK OVER') }}</div>
        <div style="color: #0E1A24; font-size: 12px; line-height: 1.4">{{ $isIdLocale ? 'Saya sudah cek pesanan Anda: paket dititipkan di resepsionis pukul 14.10. Mau saya hubungkan dengan tim kami?' : t('kouventa.I checked your order: it was left at the front desk at 14:10. Want me to connect you to our team?', 'I checked your order: it was left at the front desk at 14:10. Want me to connect you to our team?') }}</div>
    </div>

    <!-- Bubble 4: User Response -->
    <div x-show="step >= 4" class="kv-chat-bubble-in" style="align-self: flex-end; background: #071C33; color: #FFFFFF; padding: 7px 13px; border-radius: 13px 4px 13px 13px; font-size: 12px; line-height: 1.4; box-shadow: 0 4px 14px rgba(7,28,51,0.08)">
        {{ $isIdLocale ? 'Boleh, tolong ya.' : t('kouventa.Yes, please.', 'Yes, please.') }}
    </div>

    <!-- Bubble 5: System Handover Badge -->
    <div x-show="step >= 5" class="kv-chat-bubble-in" style="align-self: center; background: #E1F4E7; color: #0B5E2C; padding: 5px 14px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 0.01em">
        {{ $isIdLocale ? 'Dialihkan ke agent · Tiket dibuat' : t('kouventa.Transferred to agent · Ticket created', 'Transferred to agent · Ticket created') }}
    </div>

    <!-- Bubble 6: Support Agent -->
    <div x-show="step >= 6" class="kv-chat-bubble-in" style="align-self: flex-start; max-width: 90%; background: #FFFFFF; padding: 9px 13px; border-radius: 4px 13px 13px 13px; box-shadow: 0 4px 18px rgba(7,28,51,0.04)">
        <div style="font-size: 10px; font-weight: 800; color: #071C33; letter-spacing: 0.04em; margin-bottom: 3px">DEWI · {{ $isIdLocale ? 'SUPPORT AGENT' : t('kouventa.SUPPORT AGENT', 'SUPPORT AGENT') }}</div>
        <div style="color: #0E1A24; font-size: 12px; line-height: 1.4">{{ $isIdLocale ? 'Halo, saya bisa lihat seluruh chat-nya. Saya bantu urus dengan kurirnya ya.' : t('kouventa.Hi, I can see the full chat. Let me sort this out with the courier.', 'Hi, I can see the full chat. Let me sort this out with the courier.') }}</div>
    </div>
</div>

<!-- Integration Illustration -->
<div class="kv-flex kv-items-center kv-mockup-integration" x-show="product === 'integration'" style="width: 420px">
<div class="kv-integration-source" style="width: 118px; flex-shrink: 0; background: #FFFFFF; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
<div class="kv-integration-source-header" style="display: flex; flex-direction: column; gap: 2px;">
<span style="font-size: 12px; font-weight: 700">{{ t('kouventa.Your system', 'Your system') }}</span>
<span style="font-size: 11px; color: #4A5866">ERP · CRM · POS</span>
</div>
<div class="kv-integration-source-badge" style="display: flex; flex-direction: column; gap: 4px;">
<span style="font-size: 11px; font-weight: 700; padding: 6px 8px; border-radius: 8px; background: #DDF3E4; color: #0B5E2C; animation: kvLoop 6s ease 0.2s infinite both">{{ t('kouventa.Order shipped', 'Order shipped') }}</span>
<span style="display: flex; align-items: center; gap: 5px; font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 700; color: #4A5866"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>{{ t('kouventa.via Webhook', 'via Webhook') }}</span>
</div>
</div>
<div class="kv-integration-connector kv-connector-1" style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear infinite"></span></div>
<div class="kv-integration-node" style="width: 56px; height: 56px; flex-shrink: 0; border-radius: 16px; background: #071C33; display: flex; align-items: center; justify-content: center; animation: kvPulse 2.4s ease infinite">
<img src="{{ resolve_block_asset(setting('site_favicon', 'media/kouventa-favicon-1792137759-KNT6XRpc.webp')) }}" alt="Kouventa" style="width: 28px; height: 28px; object-fit: contain; display: block">
</div>
<div class="kv-integration-connector kv-connector-2" style="flex-grow: 1; height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -4px; left: 0; width: 10px; height: 10px; border-radius: 50%; background: #13803F; --d: 34px; animation: kvTravel 1.6s linear 0.8s infinite"></span></div>
<div class="kv-integration-target" style="width: 160px; flex-shrink: 0; display: flex; flex-direction: column; gap: 8px; font-size: 12px; line-height: 1.45">
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 1.4s infinite both">{{ t('kouventa.Hi Rina, your order is on the way.', 'Hi Rina, your order is on the way.') }} <span style="color: #13803F; font-weight: 700">{{ t('kouventa.Track order', 'Track order') }}</span></div>
<div style="background: #FFFFFF; border-radius: 4px 12px 12px 12px; padding: 10px 12px; box-shadow: 0 10px 30px rgba(7,28,51,0.08); animation: kvLoop 6s ease 2.4s infinite both">{{ t('kouventa.Reminder: your appointment is tomorrow, 10:00.', 'Reminder: your appointment is tomorrow, 10:00.') }}</div>
<span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4A5866">template: order_update</span>
</div>
</div>

<!-- Flows Illustration -->
<div class="kv-flex-col kv-mockup-flows" x-show="product === 'flows'" x-data="kvFlowsDemo()" style="width: 330px; background: #FFFFFF; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(7,28,51,0.1); display: flex; flex-direction: column">
    <!-- Header -->
    <div style="padding: 10px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #E9EDEF">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#54656F" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
        <span style="display: flex; flex-direction: column; flex-grow: 1">
            <span style="font-size: 14px; font-weight: 800; color: #0E1A24">{{ t('kouventa.Open an account', app()->getLocale() === 'id' ? 'Buka rekening' : 'Open an account') }}</span>
            <span style="font-size: 11px; color: #667781">Your Brand · WhatsApp Flows</span>
        </span>
        <span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Step 1 of 2', app()->getLocale() === 'id' ? 'Langkah 1 dari 2' : 'Step 1 of 2') }}</span>
    </div>

    <!-- Progress bar -->
    <div style="height: 3px; background: #E9EDEF">
        <div style="height: 3px; width: 50%; background: #25D366"></div>
    </div>

    <!-- Body -->
    <div style="padding: 12px 16px 14px; display: flex; flex-direction: column; gap: 9px">
        <!-- 1. Full name -->
        <div style="display: flex; flex-direction: column; gap: 3px">
            <span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Full name', app()->getLocale() === 'id' ? 'Nama lengkap' : 'Full name') }}</span>
            <div style="height: 32px; box-sizing: border-box; border-radius: 8px; border: 1px solid #D5DDD9; padding: 0 10px; display: flex; align-items: center; gap: 2px; font-size: 13px; color: #0E1A24; overflow: hidden">
                <span x-text="name" style="white-space: nowrap"></span>
                <span x-show="isTypingName" style="display: inline-block; width: 1.5px; height: 14px; background: #13803F; animation: kvBlink 0.6s infinite"></span>
            </div>
        </div>

        <!-- 2. Phone number (static from start) -->
        <div style="display: flex; flex-direction: column; gap: 3px">
            <span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Phone number', app()->getLocale() === 'id' ? 'Nomor HP' : 'Phone number') }}</span>
            <div style="height: 32px; box-sizing: border-box; border-radius: 8px; border: 1px solid #D5DDD9; padding: 0 10px; display: flex; align-items: center; gap: 8px; font-size: 13px; color: #0E1A24; background: #F4F6F5">
                <span style="flex-grow: 1">+62 812-3456-7890</span>
                <span style="font-size: 10px; font-weight: 700; color: #0B5E2C; background: #DDF3E4; padding: 2px 6px; border-radius: 999px">{{ t('kouventa.From WhatsApp', app()->getLocale() === 'id' ? 'Dari WhatsApp' : 'From WhatsApp') }}</span>
            </div>
        </div>

        <!-- 3. Email -->
        <div style="display: flex; flex-direction: column; gap: 3px">
            <span style="font-size: 11px; font-weight: 700; color: #4A5866">Email</span>
            <div style="height: 32px; box-sizing: border-box; border-radius: 8px; border: 1px solid #D5DDD9; padding: 0 10px; display: flex; align-items: center; gap: 2px; font-size: 13px; color: #0E1A24; overflow: hidden">
                <span x-text="email" style="white-space: nowrap"></span>
                <span x-show="isTypingEmail" style="display: inline-block; width: 1.5px; height: 14px; background: #13803F; animation: kvBlink 0.6s infinite"></span>
            </div>
        </div>

        <!-- 4. City & 5. Account type -->
        <div style="display: flex; gap: 10px">
            <!-- City -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 3px">
                <span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.City', app()->getLocale() === 'id' ? 'Kota' : 'City') }}</span>
                <div style="height: 32px; box-sizing: border-box; border-radius: 8px; border: 1px solid #D5DDD9; padding: 0 10px; display: flex; align-items: center; gap: 8px; font-size: 13px; color: #0E1A24; justify-content: space-between">
                    <span>Jakarta</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4A5866" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                </div>
            </div>

            <!-- Account type -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 3px">
                <span style="font-size: 11px; font-weight: 700; color: #4A5866">{{ t('kouventa.Account type', app()->getLocale() === 'id' ? 'Jenis akun' : 'Account type') }}</span>
                <div style="height: 32px; box-sizing: border-box; display: flex; padding: 2px; border-radius: 8px; background: #EEF1F0; font-size: 12px; font-weight: 700; gap: 2px">
                    <span :class="accountType === 'personal' ? 'kv-flow-type-active' : 'kv-flow-type-inactive'" style="flex: 1 1 0%; min-width: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; transition: all 0.25s ease">{{ t('kouventa.Personal', app()->getLocale() === 'id' ? 'Pribadi' : 'Personal') }}</span>
                    <span :class="accountType === 'business' ? 'kv-flow-type-active' : 'kv-flow-type-inactive'" style="flex: 1 1 0%; min-width: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; transition: all 0.25s ease">{{ t('kouventa.Business', app()->getLocale() === 'id' ? 'Bisnis' : 'Business') }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Agreement checkbox -->
        <div style="display: flex; align-items: center; gap: 8px; font-size: 11.5px; color: #4A5866">
            <span :class="agreed ? 'kv-flow-check-active' : 'kv-flow-check-idle'" style="width: 16px; height: 16px; flex-shrink: 0; border-radius: 4px; display: flex; align-items: center; justify-content: center; box-sizing: border-box; transition: all 0.25s ease">
                <svg x-show="agreed" x-transition.scale.origin.center class="kv-pop-in" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
            </span>
            <span>{{ t('kouventa.I agree to the terms & privacy policy', app()->getLocale() === 'id' ? 'Saya setuju dengan syarat & kebijakan privasi' : 'I agree to the terms & privacy policy') }}</span>
        </div>

        <!-- 7. Submit button -->
        <div :class="{ 'kv-flow-btn-press': btnPressed, 'kv-flow-btn-loading': isProcessing }" style="height: 36px; border-radius: 999px; background: #25D366; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; position: relative; overflow: hidden; transition: all 0.2s ease">
            <span x-show="!isProcessing" x-transition.opacity>{{ t('kouventa.Continue', app()->getLocale() === 'id' ? 'Lanjut' : 'Continue') }}</span>
            <span x-show="isProcessing" x-transition.opacity style="display: flex; align-items: center; gap: 6px">
                <svg class="kv-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
                <span>{{ t('kouventa.Processing...', app()->getLocale() === 'id' ? 'Memproses...' : 'Processing...') }}</span>
            </span>
        </div>

        <!-- 8. Data received notification -->
        <div style="min-height: 22px; display: flex; align-items: center; justify-content: center">
            <div x-show="dataReceived" x-transition.opacity.duration.300ms class="kv-pop-in" style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700; color: #0B5E2C">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                <span>{{ t('kouventa.Data received, without leaving the chat', app()->getLocale() === 'id' ? 'Data diterima, tanpa keluar dari chat' : 'Data received, without leaving the chat') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Omni Illustration -->
<div class="kv-flex kv-items-center kv-mockup-omni" x-show="product === 'omni'" style="width: 420px; gap: 0">
<div class="kv-omni-channels" style="width: 118px; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px">
<span class="kv-omni-ch-item" style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-whatsapp.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>WhatsApp</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">Chat</span></span></span>
<span class="kv-omni-ch-item" style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-1.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Instagram</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.DM & comments', 'DM & comments') }}</span></span></span>
<span class="kv-omni-ch-item" style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-2.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Facebook</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.DM & comments', 'DM & comments') }}</span></span></span>
<span class="kv-omni-ch-item" style="font-size: 12px; font-weight: 700; padding: 7px 8px; border-radius: 10px; background: #FFFFFF; box-shadow: 0 6px 16px rgba(7,28,51,0.06); display: flex; align-items: center; gap: 6px"><img src="{{ theme_asset('images/icon-channel-3.png') }}" alt="" style="width: 20px; height: 20px; flex-shrink: 0; object-fit: contain"><span style="display: flex; flex-direction: column; gap: 1px; min-width: 0"><span>Live Chat</span><span style="font-size: 10px; font-weight: 600; color: #4A5866; white-space: nowrap">{{ t('kouventa.Website chat', 'Website chat') }}</span></span></span>
</div>
<div class="kv-omni-connectors" style="width: 50px; flex-shrink: 0; height: 214px; display: flex; flex-direction: column; justify-content: space-around">
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.45s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 0.9s infinite"></span></div>
<div style="height: 2px; background: #DCE3E0; position: relative"><span style="position: absolute; top: -3px; left: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 42px; animation: kvTravel 1.8s linear 1.35s infinite"></span></div>
</div>
<!-- Vertical connector for mobile -->
<div class="kv-omni-v-connector" style="display: none;">
<div style="width: 2px; height: 18px; background: #DCE3E0; position: relative; margin: 0 auto;"><span style="position: absolute; left: -3px; top: 0; width: 8px; height: 8px; border-radius: 50%; background: #13803F; --d: 18px; animation: kvTravelY 1.5s linear infinite;"></span></div>
</div>
<div class="kv-omni-inbox" style="flex-grow: 1; background: #FFFFFF; border-radius: 16px; padding: 14px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 10px 30px rgba(7,28,51,0.08)">
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
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">
    {{ $isIdLocale ? 'Semua percakapan, tetap dalam kendali Anda.' : t('kouventa.Stay in control of every conversation.', 'Stay in control of every conversation.') }}
</h2>
<div class="kv-ce-control-grid">

    <!-- Card 1: Mode supervisor -->
    <article class="kv-control-card">
        <div style="color: #13803F; display: flex; align-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0E1A24; line-height: 1.3">{{ $isIdLocale ? 'Mode supervisor' : t('kouventa.Supervisor mode', 'Supervisor mode') }}</h3>
        <p style="margin: 0; font-size: 14px; line-height: 1.55; color: #4A5866">{{ $isIdLocale ? 'Lead melihat chat timnya. Agent hanya melihat chat miliknya.' : t('kouventa.Leads see their team\'s chats. Agents see only their own.', 'Leads see their team\'s chats. Agents see only their own.') }}</p>
    </article>

    <!-- Card 2: Multi-akun -->
    <article class="kv-control-card">
        <div style="color: #13803F; display: flex; align-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1.5"></rect><rect width="7" height="7" x="14" y="3" rx="1.5"></rect><rect width="7" height="7" x="14" y="14" rx="1.5"></rect><rect width="7" height="7" x="3" y="14" rx="1.5"></rect></svg>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0E1A24; line-height: 1.3">{{ $isIdLocale ? 'Multi-akun' : t('kouventa.Multi-account', 'Multi-account') }}</h3>
        <p style="margin: 0; font-size: 14px; line-height: 1.55; color: #4A5866">{{ $isIdLocale ? 'Semua akun dan nomor bisnis, dalam satu platform.' : t('kouventa.All business accounts and numbers, one platform.', 'All business accounts and numbers, one platform.') }}</p>
    </article>

    <!-- Card 3: Akses berbasis peran -->
    <article class="kv-control-card">
        <div style="color: #13803F; display: flex; align-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0E1A24; line-height: 1.3">{{ $isIdLocale ? 'Akses berbasis peran' : t('kouventa.Role-based access', 'Role-based access') }}</h3>
        <p style="margin: 0; font-size: 14px; line-height: 1.55; color: #4A5866">{{ $isIdLocale ? 'Atur siapa yang bisa memakai modul, fitur, dan akun tertentu.' : t('kouventa.Decide who can use which modules, features and accounts.', 'Decide who can use which modules, features and accounts.') }}</p>
    </article>

    <!-- Card 4: Riwayat percakapan -->
    <article class="kv-control-card">
        <div style="color: #13803F; display: flex; align-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M12 7v5l4 2"></path></svg>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0E1A24; line-height: 1.3">{{ $isIdLocale ? 'Riwayat percakapan' : t('kouventa.Conversation history', 'Conversation history') }}</h3>
        <p style="margin: 0; font-size: 14px; line-height: 1.55; color: #4A5866">{{ $isIdLocale ? 'Semua chat tersimpan untuk visibilitas dan follow-up.' : t('kouventa.Every chat stored for visibility and follow-up.', 'Every chat stored for visibility and follow-up.') }}</p>
    </article>

    <!-- Card 5: Ticketing -->
    <article class="kv-control-card">
        <div style="color: #13803F; display: flex; align-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0E1A24; line-height: 1.3">{{ $isIdLocale ? 'Ticketing' : t('kouventa.Ticketing', 'Ticketing') }}</h3>
        <p style="margin: 0; font-size: 14px; line-height: 1.55; color: #4A5866">{{ $isIdLocale ? 'Chat dengan agent? Tiket otomatis dibuat.' : t('kouventa.Chat with an agent? A ticket is created automatically.', 'Chat with an agent? A ticket is created automatically.') }}</p>
    </article>

</div>
</section>

<!-- ============ WHY ============ -->
<section class="kv-why-section">
<div aria-hidden="true" style="position: absolute; left: -200px; bottom: -320px; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07); pointer-events: none"></div>
<div aria-hidden="true" style="position: absolute; right: -160px; top: -260px; width: 560px; height: 560px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07); pointer-events: none"></div>
<div aria-hidden="true" style="position: absolute; right: -240px; top: -340px; width: 800px; height: 800px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.04); pointer-events: none"></div>

<h2 class="kv-why-heading">
    {{ $isIdLocale ? 'Kenapa Kouventa untuk customer' : 'Why Kouventa for customer' }} <span style="color: #2BD46A">engagement</span>
</h2>

<div class="kv-why-grid">
    <!-- Card 1: Official Meta Business Solution Provider -->
    <div class="kv-why-card">
        <span class="kv-why-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="6"></circle>
                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
            </svg>
        </span>
        <h3 class="kv-why-title">Official Meta Business Solution Provider</h3>
        <p class="kv-why-desc">{{ $isIdLocale ? 'Akses resmi ke WhatsApp Business Platform.' : t('kouventa.Trusted access to the WhatsApp Business Platform.', 'Trusted access to the WhatsApp Business Platform.') }}</p>
    </div>

    <!-- Card 2: Built around your business -->
    <div class="kv-why-card">
        <span class="kv-why-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="21" x2="14" y1="4" y2="4"></line>
                <line x1="10" x2="3" y1="4" y2="4"></line>
                <line x1="21" x2="12" y1="12" y2="12"></line>
                <line x1="8" x2="3" y1="12" y2="12"></line>
                <line x1="21" x2="16" y1="20" y2="20"></line>
                <line x1="12" x2="3" y1="20" y2="20"></line>
                <line x1="14" x2="14" y1="2" y2="6"></line>
                <line x1="8" x2="8" y1="10" y2="14"></line>
                <line x1="16" x2="16" y1="18" y2="22"></line>
            </svg>
        </span>
        <h3 class="kv-why-title">{{ $isIdLocale ? 'Dibangun sesuai bisnis Anda' : t('kouventa.Built around your business', 'Built around your business') }}</h3>
        <p class="kv-why-desc">{{ $isIdLocale ? 'Disesuaikan dengan industri, alur, dan sistem Anda.' : t('kouventa.Customized to your industry, flows and systems.', 'Customized to your industry, flows and systems.') }}</p>
    </div>

    <!-- Card 3: Secure & trusted -->
    <div class="kv-why-card">
        <span class="kv-why-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="m9 12 2 2 4-4"></path>
            </svg>
        </span>
        <h3 class="kv-why-title">{{ $isIdLocale ? 'Aman & terpercaya' : t('kouventa.Secure & trusted', 'Secure & trusted') }}</h3>
        <p class="kv-why-desc">{{ $isIdLocale ? 'Keamanan kelas enterprise untuk data bisnis dan pelanggan.' : t('kouventa.Enterprise-grade security for business and customer data.', 'Enterprise-grade security for business and customer data.') }}</p>
    </div>

    <!-- Card 4: Post-paid messaging -->
    <div class="kv-why-card">
        <span class="kv-why-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                <line x1="2" x2="22" y1="10" y2="10"></line>
                <line x1="6" x2="10" y1="15" y2="15"></line>
            </svg>
        </span>
        <h3 class="kv-why-title">{{ $isIdLocale ? 'Pesan pascabayar' : t('kouventa.Post-paid messaging', 'Post-paid messaging') }}</h3>
        <p class="kv-why-desc">{{ $isIdLocale ? 'Kirim dulu. Bayar belakangan, sesuai pemakaian.' : t('kouventa.Send first. Pay later, for what you actually use.', 'Send first. Pay later, for what you actually use.') }}</p>
    </div>
</div>
</section>

<!-- ============ CTA ============ -->
<section style="padding: 104px 80px">
<div class="kv-panel" style="background: #2BD46A; border-radius: 28px; padding: 72px 64px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 32px; color: #06251A">
<h2 style="margin: 0; font-size: 44px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">{{ $bottomCtaTitle }}</h2>
<div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap">
<a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-navy" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; white-space: nowrap; text-decoration: none">{{ t('kouventa.Book a demo', 'Book a demo') }}</a>
<a href="{{ localized_url('/pricing?tab=ce') }}" class="kv-btn-action kv-btn-outline" style="box-sizing: border-box; min-width: 180px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: #06251A; font-size: 17px; font-weight: 700; padding: 0 28px; border-radius: 12px; border: 2px solid #06251A; white-space: nowrap; text-decoration: none">{{ t('kouventa.See pricing', 'See pricing') }}</a>
</div>
</div>
</section>

</div>
@endsection

@push('scripts')
<script>
window.kvBroadcastCounter = function() {
    return {
        p1: 0,
        p2: 0,
        p3: 0,
        init() {
            const ease = t => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2);
            let startTime = null;
            const tick = (now) => {
                if (!startTime) startTime = now;
                const elapsed = (now - startTime) % 6500;

                // Bar 1 (Terkirim, max 92%)
                if (elapsed < 400) {
                    this.p1 = 0;
                } else if (elapsed < 2400) {
                    this.p1 = Math.round(92 * ease((elapsed - 400) / 2000));
                } else if (elapsed < 5800) {
                    this.p1 = 92;
                } else {
                    this.p1 = Math.round(92 * (1 - (elapsed - 5800) / 700));
                }

                // Bar 2 (Dibaca, max 70%)
                if (elapsed < 700) {
                    this.p2 = 0;
                } else if (elapsed < 2700) {
                    this.p2 = Math.round(70 * ease((elapsed - 700) / 2000));
                } else if (elapsed < 5800) {
                    this.p2 = 70;
                } else {
                    this.p2 = Math.round(70 * (1 - (elapsed - 5800) / 700));
                }

                // Bar 3 (Diklik, max 38%)
                if (elapsed < 1000) {
                    this.p3 = 0;
                } else if (elapsed < 3000) {
                    this.p3 = Math.round(38 * ease((elapsed - 1000) / 2000));
                } else if (elapsed < 5800) {
                    this.p3 = 38;
                } else {
                    this.p3 = Math.round(38 * (1 - (elapsed - 5800) / 700));
                }

                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        }
    };
};

window.kvFlowsDemo = function() {
    return {
        name: '',
        isTypingName: false,
        email: '',
        isTypingEmail: false,
        accountType: 'business',
        agreed: false,
        btnPressed: false,
        isProcessing: false,
        dataReceived: false,

        init() {
            this.startLoop();
        },

        async startLoop() {
            const fullName = 'Rina Saraswati';
            const fullEmail = 'rina.saraswati@mail.com';
            const sleep = ms => new Promise(r => setTimeout(r, ms));

            while (true) {
                // Reset state
                this.name = '';
                this.isTypingName = false;
                this.email = '';
                this.isTypingEmail = false;
                this.accountType = 'business';
                this.agreed = false;
                this.btnPressed = false;
                this.isProcessing = false;
                this.dataReceived = false;

                await sleep(500);

                // 1. Type name
                this.isTypingName = true;
                for (let i = 1; i <= fullName.length; i++) {
                    this.name = fullName.slice(0, i);
                    await sleep(55);
                }
                this.isTypingName = false;
                await sleep(350);

                // 2. Type email
                this.isTypingEmail = true;
                for (let i = 1; i <= fullEmail.length; i++) {
                    this.email = fullEmail.slice(0, i);
                    await sleep(40);
                }
                this.isTypingEmail = false;
                await sleep(450);

                // 3. Switch account type from business to personal
                this.accountType = 'personal';
                await sleep(600);

                // 4. Check agreement
                this.agreed = true;
                await sleep(600);

                // 5. Click Lanjut
                this.btnPressed = true;
                await sleep(150);
                this.btnPressed = false;
                this.isProcessing = true;
                await sleep(1200);

                // 6. Data received
                this.isProcessing = false;
                this.dataReceived = true;

                // 7. Pause to view completed state
                await sleep(2500);
            }
        }
    };
};

window.kvChatbotDemo = function() {
    return {
        step: 0,
        isResetting: false,
        init() {
            this.startLoop();
        },
        async startLoop() {
            const sleep = ms => new Promise(r => setTimeout(r, ms));
            while (true) {
                // Reset full
                this.isResetting = true;
                this.step = 0;
                await sleep(350);
                this.isResetting = false;
                await sleep(250);

                // Step 1: Automation Flow greeting + quick replies
                this.step = 1;
                await sleep(1400);

                // Step 2: User inquiry
                this.step = 2;
                await sleep(1400);

                // Step 3: AI Bot takeover
                this.step = 3;
                await sleep(1600);

                // Step 4: User confirmation
                this.step = 4;
                await sleep(1100);

                // Step 5: System ticket handover badge
                this.step = 5;
                await sleep(1100);

                // Step 6: Human support agent responds
                this.step = 6;
                // Hold reading view for 4 seconds after chat terakhir muncul
                await sleep(4000);
            }
        }
    };
};

document.addEventListener('alpine:init', () => {
    Alpine.data('kvBroadcastCounter', window.kvBroadcastCounter);
    Alpine.data('kvFlowsDemo', window.kvFlowsDemo);
    Alpine.data('kvChatbotDemo', window.kvChatbotDemo);
});
</script>
@endpush
