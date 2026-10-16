@php
    $currentLocale = app()->getLocale();
    $siteLogo = setting('site_logo') ? resolve_block_asset(setting('site_logo')) : theme_asset('images/kouventa-logo.png');
    $siteName = setting('site_name', 'Kouventa');
    $instagramUrl = setting('social_instagram', 'https://instagram.com/kouventa.ai');
    $linkedinUrl = setting('social_linkedin', 'https://linkedin.com/company/kouventa');
@endphp

<!-- Mobile Bottom Navigation Bar & Bottom Sheets (CDT Architecture) -->
<div id="mobile-bottom-nav" style="position: fixed; bottom: 0; left: 0; right: 0; pointer-events: none; z-index: 9999;">
    <div style="width: 100%; height: 76px; position: relative; pointer-events: auto; transform: translateY(0px);">

        <!-- Dark Glass Navy Dock Bar -->
        <div style="position: absolute; inset: 0; background: rgba(7,28,51,0.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-top: 1px solid rgba(255,255,255,0.12); padding: 8px 12px; box-shadow: 0 -4px 24px rgba(0,0,0,0.4);">
            <div style="display: flex; align-items: center; justify-content: space-around; height: 100%; position: relative;">
                
                <!-- 1. Product -->
                <button type="button" @click="activeSheet = activeSheet === 'products' ? null : 'products'"
                        class="kv-dock-btn"
                        :class="activeSheet === 'products' ? 'is-active' : ''">
                    <div class="kv-dock-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                            <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                        </svg>
                    </div>
                    <span>{{ t('kouventa.Product', 'Product') }}</span>
                </button>

                <!-- 2. Solutions -->
                <button type="button" @click="activeSheet = activeSheet === 'solutions' ? null : 'solutions'"
                        class="kv-dock-btn"
                        :class="activeSheet === 'solutions' ? 'is-active' : ''">
                    <div class="kv-dock-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2 2 7l10 5 10-5-10-5Z"></path>
                            <path d="m2 17 10 5 10-5"></path>
                            <path d="m2 12 10 5 10-5"></path>
                        </svg>
                    </div>
                    <span>{{ t('kouventa.Solution', 'Solution') }}</span>
                </button>

                <!-- 3. Floating Center Action (Book Demo with Telephone Icon) -->
                <a href="{{ localized_url('/book-demo') }}"
                   style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 54px; height: 54px; margin-top: -24px; background: #2BD46A; color: #06251A; border-radius: 16px; box-shadow: 0 8px 20px rgba(43,212,106,0.45); border: 2px solid #071C33; text-decoration: none;"
                   title="{{ t('kouventa.Book a demo', 'Book a demo') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </a>

                <!-- 4. Pricing -->
                <button type="button" @click="activeSheet = activeSheet === 'pricing' ? null : 'pricing'"
                        class="kv-dock-btn"
                        :class="activeSheet === 'pricing' ? 'is-active' : ''">
                    <div class="kv-dock-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                            <line x1="2" x2="22" y1="10" y2="10"></line>
                        </svg>
                    </div>
                    <span>{{ t('kouventa.Pricing', 'Pricing') }}</span>
                </button>

                <!-- 5. More -->
                <button type="button" @click="activeSheet = activeSheet === 'more' ? null : 'more'"
                        class="kv-dock-btn"
                        :class="activeSheet === 'more' ? 'is-active' : ''">
                    <div class="kv-dock-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <span>{{ t('kouventa.More', 'More') }}</span>
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Mobile Slide-Up Menu Overlay -->
<div x-show="activeSheet !== null" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="kv-sheet-overlay"
     @click="activeSheet = null">
</div>

<!-- ================= BOTTOM SHEET 1: PRODUCTS ================= -->
<div x-show="activeSheet === 'products'" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="kv-bottom-sheet">
    
    <div>
        <div style="width: 44px; height: 5px; background: rgba(255,255,255,0.25); border-radius: 999px; margin: 0 auto 16px;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #FFFFFF;">{{ t('kouventa.Product Offerings', 'Product Offerings') }}</span>
            <button type="button" @click="activeSheet = null"
                    aria-label="Close"
                    class="kv-sheet-close-btn"
                    style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; padding: 0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Product AI -->
            <a href="{{ localized_url('/product/ai') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div class="kv-sheet-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 3.3 2 6.1 5 7.4V20a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2.6c3-1.3 5-4.1 5-7.4a8 8 0 0 0-8-8z"></path><path d="M9 22h6"></path></svg>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 700; color: #FFFFFF; font-size: 15px;">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
                        <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; background: rgba(43,212,106,0.18); color: #2BD46A; padding: 2px 8px; border-radius: 999px;">AI Platform</span>
                    </div>
                    <p style="margin: 4px 0 0; font-size: 12px; color: #B9C6D3; line-height: 1.4;">{{ t('kouventa.Knowledge & automation agents', 'Knowledge & automation agents for enterprise teams.') }}</p>
                </div>
                <span style="color: #2BD46A; font-size: 16px; font-weight: 700;">→</span>
            </a>

            <!-- Product CE -->
            <a href="{{ localized_url('/product/customer-engagement') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div class="kv-sheet-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 700; color: #FFFFFF; font-size: 15px;">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
                        <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; background: rgba(43,212,106,0.18); color: #2BD46A; padding: 2px 8px; border-radius: 999px;">WhatsApp API</span>
                    </div>
                    <p style="margin: 4px 0 0; font-size: 12px; color: #B9C6D3; line-height: 1.4;">{{ t('kouventa.WhatsApp & omnichannel', 'WhatsApp & omnichannel chatbots with human handoff.') }}</p>
                </div>
                <span style="color: #2BD46A; font-size: 16px; font-weight: 700;">→</span>
            </a>
        </div>
    </div>
    <div class="h-20 w-full"></div>
</div>

<!-- ================= BOTTOM SHEET 2: SOLUTIONS ================= -->
<div x-show="activeSheet === 'solutions'" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="kv-bottom-sheet">
    
    <div>
        <div style="width: 44px; height: 5px; background: rgba(255,255,255,0.25); border-radius: 999px; margin: 0 auto 16px;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #FFFFFF;">{{ t('kouventa.Enterprise Solutions', 'Enterprise Solutions') }}</span>
            <button type="button" @click="activeSheet = null"
                    aria-label="Close"
                    class="kv-sheet-close-btn"
                    style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; padding: 0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Section 1: AI Solutions -->
        <div style="margin-bottom: 16px;">
            <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #2BD46A; margin-bottom: 8px;">
                {{ t('kouventa.Artificial Intelligence Solutions', 'Artificial Intelligence Solutions') }}
            </div>
            <div class="kv-sheet-grid">
                <a href="{{ localized_url('/solutions/ai?dept=0') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Customer Service', 'Customer Service') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=1') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Recruitment', 'Recruitment') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=2') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=3') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Business Operation', 'Business Operation') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=4') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Finance', 'Finance') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=5') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Legal & Compliance', 'Legal & Compliance') }}
                </a>
            </div>
        </div>

        <!-- Section 2: Customer Engagement Solutions -->
        <div>
            <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #2BD46A; margin-bottom: 8px;">
                {{ t('kouventa.Customer Engagement Solutions', 'Customer Engagement Solutions') }}
            </div>
            <div class="kv-sheet-grid">
                <a href="{{ localized_url('/solutions/customer-engagement?case=0') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}
                </a>
                <a href="{{ localized_url('/solutions/customer-engagement?case=1') }}" @click="activeSheet = null" class="kv-sheet-sub-item">
                    {{ t('kouventa.Customer Service', 'Customer Service') }}
                </a>
                <a href="{{ localized_url('/solutions/customer-engagement?case=2') }}" @click="activeSheet = null" class="kv-sheet-sub-item" style="grid-column: span 2;">
                    {{ t('kouventa.Operation', 'Operation') }}
                </a>
            </div>
        </div>
    </div>
    <div class="h-20 w-full"></div>
</div>

<!-- ================= BOTTOM SHEET 3: PRICING ================= -->
<div x-show="activeSheet === 'pricing'" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="kv-bottom-sheet">
    
    <div>
        <div style="width: 44px; height: 5px; background: rgba(255,255,255,0.25); border-radius: 999px; margin: 0 auto 16px;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #FFFFFF;">{{ t('kouventa.Pricing & Plans', 'Pricing & Plans') }}</span>
            <button type="button" @click="activeSheet = null"
                    aria-label="Close"
                    class="kv-sheet-close-btn"
                    style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; padding: 0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ localized_url('/pricing?tab=ai') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #FFFFFF; font-size: 15px;">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</div>
                    <div style="font-size: 12px; color: #B9C6D3; margin-top: 2px;">Starter · Grow · Scale</div>
                </div>
                <span style="color: #2BD46A; font-weight: 700;">→</span>
            </a>

            <a href="{{ localized_url('/pricing?tab=ce') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #FFFFFF; font-size: 15px;">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</div>
                    <div style="font-size: 12px; color: #B9C6D3; margin-top: 2px;">Starter · Professional · Enterprise</div>
                </div>
                <span style="color: #2BD46A; font-weight: 700;">→</span>
            </a>

            <a href="{{ localized_url('/pricing#calculator') }}" @click="activeSheet = null" class="kv-sheet-card" style="background: rgba(43,212,106,0.12); border-color: rgba(43,212,106,0.3);">
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    <span style="width: 32px; height: 32px; border-radius: 8px; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">Rp</span>
                    <div>
                        <div style="font-weight: 700; color: #2BD46A; font-size: 14px;">{{ t('kouventa.WhatsApp Cost Calculator', 'WhatsApp Cost Calculator') }}</div>
                        <div style="font-size: 11px; color: #B9C6D3;">Zero markup on Meta conversation fees</div>
                    </div>
                </div>
                <span style="color: #2BD46A; font-weight: 700;">→</span>
            </a>
        </div>
    </div>
    <div class="h-20 w-full"></div>
</div>

<!-- ================= BOTTOM SHEET 4: MORE / OTHERS ================= -->
<div x-show="activeSheet === 'more'" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="kv-bottom-sheet">
    
    <div>
        <div style="width: 44px; height: 5px; background: rgba(255,255,255,0.25); border-radius: 999px; margin: 0 auto 16px;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #FFFFFF;">{{ t('kouventa.More Navigation', 'More Navigation') }}</span>
            <button type="button" @click="activeSheet = null"
                    aria-label="Close"
                    class="kv-sheet-close-btn"
                    style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; padding: 0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- About Us -->
            <a href="{{ localized_url('/about') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div class="kv-sheet-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #FFFFFF; font-size: 14px;">{{ t('kouventa.About us', 'About us') }}</div>
                    <div style="font-size: 11px; color: #B9C6D3;">Mission, leadership, and milestones</div>
                </div>
                <span style="color: #B9C6D3;">→</span>
            </a>

            <!-- Insight / Blog -->
            <a href="{{ localized_url('/insight') }}" @click="activeSheet = null" class="kv-sheet-card">
                <div class="kv-sheet-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path></svg>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #FFFFFF; font-size: 14px;">{{ t('kouventa.Insight', 'Insight (Blog)') }}</div>
                    <div style="font-size: 11px; color: #B9C6D3;">Articles, case studies & updates</div>
                </div>
                <span style="color: #B9C6D3;">→</span>
            </a>

            <!-- Book a Demo -->
            <a href="{{ localized_url('/book-demo') }}" @click="activeSheet = null" class="kv-sheet-card" style="background: rgba(43,212,106,0.14); border-color: rgba(43,212,106,0.3);">
                <div class="kv-sheet-card-icon" style="background: #2BD46A; color: #06251A;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #2BD46A; font-size: 14px;">{{ t('kouventa.Book a demo', 'Book a demo') }}</div>
                    <div style="font-size: 11px; color: #B9C6D3;">Get in touch for enterprise consultation</div>
                </div>
                <span style="color: #2BD46A; font-weight: 700;">→</span>
            </a>

            <!-- Social Links -->
            <div class="kv-sheet-socials">
                <span style="font-size: 12px; color: #B9C6D3;">Follow us</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="kv-social-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                        Instagram
                    </a>
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="kv-social-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="h-20 w-full"></div>
</div>
