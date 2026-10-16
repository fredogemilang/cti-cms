@php
    $currentLocale = app()->getLocale();
    $siteLogo = setting('site_logo') ? resolve_block_asset(setting('site_logo')) : theme_asset('images/kouventa-logo.png');
    $siteName = setting('site_name', 'Kouventa');
    $instagramUrl = setting('social_instagram', 'https://instagram.com/kouventa.ai');
    $linkedinUrl = setting('social_linkedin', 'https://linkedin.com/company/kouventa');
@endphp

<!-- Mobile Bottom Navigation Bar & Bottom Sheets (CDT Architecture) -->
<div id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 left-0 right-0 pointer-events-none" style="z-index: 9999;">
    <div class="w-full h-[76px] relative transition-transform duration-300 ease-out pointer-events-auto"
         :style="showMenu ? 'transform: translateY(0px);' : 'transform: translateY(100%);'">

        <!-- Dark Glass Navy Dock Bar -->
        <div class="absolute inset-0 bg-[#071C33]/95 backdrop-blur-lg border-t border-white/10 px-2 py-2 shadow-[0_-4px_24px_rgba(0,0,0,0.4)]">
            <div class="flex items-center justify-around h-full relative">
                
                <!-- 1. Product -->
                <button type="button" @click="activeSheet = activeSheet === 'products' ? null : 'products'"
                        class="flex flex-col items-center justify-center w-16 gap-1 transition-colors"
                        :class="activeSheet === 'products' ? 'text-[#2BD46A]' : 'text-[#B9C6D3]'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                         :class="activeSheet === 'products' ? 'bg-[#2BD46A]/15 text-[#2BD46A] shadow-[0_2px_8px_rgba(43,212,106,0.3)]' : 'bg-white/5 text-[#B9C6D3]'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                            <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight uppercase">{{ t('kouventa.Product', 'Product') }}</span>
                </button>

                <!-- 2. Solutions -->
                <button type="button" @click="activeSheet = activeSheet === 'solutions' ? null : 'solutions'"
                        class="flex flex-col items-center justify-center w-16 gap-1 transition-colors"
                        :class="activeSheet === 'solutions' ? 'text-[#2BD46A]' : 'text-[#B9C6D3]'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                         :class="activeSheet === 'solutions' ? 'bg-[#2BD46A]/15 text-[#2BD46A] shadow-[0_2px_8px_rgba(43,212,106,0.3)]' : 'bg-white/5 text-[#B9C6D3]'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2 2 7l10 5 10-5-10-5Z"></path>
                            <path d="m2 17 10 5 10-5"></path>
                            <path d="m2 12 10 5 10-5"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight uppercase">{{ t('kouventa.Solution', 'Solution') }}</span>
                </button>

                <!-- 3. Floating Center Action (Book Demo) -->
                <a href="{{ localized_url('/book-demo') }}"
                   class="flex flex-col items-center justify-center w-14 h-14 -mt-6 bg-[#2BD46A] text-[#06251A] rounded-2xl shadow-[0_8px_20px_rgba(43,212,106,0.45)] border-2 border-[#071C33] active:scale-95 transition-transform"
                   title="{{ t('kouventa.Book a demo', 'Book a demo') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </a>

                <!-- 4. Pricing -->
                <button type="button" @click="activeSheet = activeSheet === 'pricing' ? null : 'pricing'"
                        class="flex flex-col items-center justify-center w-16 gap-1 transition-colors"
                        :class="activeSheet === 'pricing' ? 'text-[#2BD46A]' : 'text-[#B9C6D3]'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                         :class="activeSheet === 'pricing' ? 'bg-[#2BD46A]/15 text-[#2BD46A] shadow-[0_2px_8px_rgba(43,212,106,0.3)]' : 'bg-white/5 text-[#B9C6D3]'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                            <line x1="2" x2="22" y1="10" y2="10"></line>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight uppercase">{{ t('kouventa.Pricing', 'Pricing') }}</span>
                </button>

                <!-- 5. More -->
                <button type="button" @click="activeSheet = activeSheet === 'more' ? null : 'more'"
                        class="flex flex-col items-center justify-center w-16 gap-1 transition-colors"
                        :class="activeSheet === 'more' ? 'text-[#2BD46A]' : 'text-[#B9C6D3]'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                         :class="activeSheet === 'more' ? 'bg-[#2BD46A]/15 text-[#2BD46A] shadow-[0_2px_8px_rgba(43,212,106,0.3)]' : 'bg-white/5 text-[#B9C6D3]'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight uppercase">{{ t('kouventa.More', 'More') }}</span>
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
     class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm"
     @click="activeSheet = null"
     style="z-index: 10001;">
</div>

<!-- ================= BOTTOM SHEET 1: PRODUCTS ================= -->
<div x-show="activeSheet === 'products'" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#071C33] text-white rounded-t-[28px] pt-3 pb-8 px-6 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between border-t border-white/10"
     style="z-index: 10002; max-height: 85vh;">
    
    <div>
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-[#B9C6D3] uppercase tracking-wider">{{ t('kouventa.Product Offerings', 'Product Offerings') }}</span>
            <button @click="activeSheet = null" class="p-2 text-white/70 hover:text-white bg-white/10 rounded-full transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-3">
            <!-- Product AI -->
            <a href="{{ localized_url('/product/ai') }}" @click="activeSheet = null" class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white no-underline block">
                <div class="w-11 h-11 rounded-xl bg-[#2BD46A]/20 text-[#2BD46A] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 3.3 2 6.1 5 7.4V20a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2.6c3-1.3 5-4.1 5-7.4a8 8 0 0 0-8-8z"></path><path d="M9 22h6"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-white text-base">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-[#2BD46A]/20 text-[#2BD46A] px-2 py-0.5 rounded-full">AI Platform</span>
                    </div>
                    <p class="text-xs text-[#B9C6D3] mt-1">{{ t('kouventa.Knowledge & automation agents', 'Knowledge & automation agents for enterprise teams.') }}</p>
                </div>
                <span class="text-[#2BD46A] text-lg font-bold self-center">→</span>
            </a>

            <!-- Product CE -->
            <a href="{{ localized_url('/product/customer-engagement') }}" @click="activeSheet = null" class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white no-underline block">
                <div class="w-11 h-11 rounded-xl bg-[#2BD46A]/20 text-[#2BD46A] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-white text-base">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-[#2BD46A]/20 text-[#2BD46A] px-2 py-0.5 rounded-full">WhatsApp API</span>
                    </div>
                    <p class="text-xs text-[#B9C6D3] mt-1">{{ t('kouventa.WhatsApp & omnichannel', 'WhatsApp & omnichannel chatbots with human handoff.') }}</p>
                </div>
                <span class="text-[#2BD46A] text-lg font-bold self-center">→</span>
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
     class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#071C33] text-white rounded-t-[28px] pt-3 pb-8 px-6 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between border-t border-white/10"
     style="z-index: 10002; max-height: 85vh;">
    
    <div>
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[#B9C6D3] uppercase tracking-wider">{{ t('kouventa.Enterprise Solutions', 'Enterprise Solutions') }}</span>
            <button @click="activeSheet = null" class="p-2 text-white/70 hover:text-white bg-white/10 rounded-full transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Section 1: AI Solutions -->
        <div class="mb-4">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-[#2BD46A] mb-2 px-1">
                {{ t('kouventa.Artificial Intelligence Solutions', 'Artificial Intelligence Solutions') }}
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="{{ localized_url('/solutions/ai?dept=0') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Customer Service', 'Customer Service') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=1') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Recruitment', 'Recruitment') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=2') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=3') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Business Operation', 'Business Operation') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=4') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Finance', 'Finance') }}
                </a>
                <a href="{{ localized_url('/solutions/ai?dept=5') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Legal & Compliance', 'Legal & Compliance') }}
                </a>
            </div>
        </div>

        <!-- Section 2: Customer Engagement Solutions -->
        <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-[#2BD46A] mb-2 px-1">
                {{ t('kouventa.Customer Engagement Solutions', 'Customer Engagement Solutions') }}
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="{{ localized_url('/solutions/customer-engagement?case=0') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Sales & Marketing', 'Sales & Marketing') }}
                </a>
                <a href="{{ localized_url('/solutions/customer-engagement?case=1') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block">
                    {{ t('kouventa.Customer Service', 'Customer Service') }}
                </a>
                <a href="{{ localized_url('/solutions/customer-engagement?case=2') }}" @click="activeSheet = null" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white no-underline font-semibold block col-span-2">
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
     class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#071C33] text-white rounded-t-[28px] pt-3 pb-8 px-6 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between border-t border-white/10"
     style="z-index: 10002; max-height: 85vh;">
    
    <div>
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-[#B9C6D3] uppercase tracking-wider">{{ t('kouventa.Pricing & Plans', 'Pricing & Plans') }}</span>
            <button @click="activeSheet = null" class="p-2 text-white/70 hover:text-white bg-white/10 rounded-full transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-3">
            <a href="{{ localized_url('/pricing?tab=ai') }}" @click="activeSheet = null" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white no-underline">
                <div>
                    <div class="font-bold text-white text-base">{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}</div>
                    <div class="text-xs text-[#B9C6D3] mt-0.5">Starter · Grow · Scale</div>
                </div>
                <span class="text-[#2BD46A] font-bold">→</span>
            </a>

            <a href="{{ localized_url('/pricing?tab=ce') }}" @click="activeSheet = null" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white no-underline">
                <div>
                    <div class="font-bold text-white text-base">{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}</div>
                    <div class="text-xs text-[#B9C6D3] mt-0.5">Starter · Professional · Enterprise</div>
                </div>
                <span class="text-[#2BD46A] font-bold">→</span>
            </a>

            <a href="{{ localized_url('/pricing#calculator') }}" @click="activeSheet = null" class="flex items-center justify-between p-4 rounded-2xl bg-[#2BD46A]/10 border border-[#2BD46A]/30 text-white no-underline">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-[#2BD46A] text-[#06251A] flex items-center justify-center font-bold text-sm">Rp</span>
                    <div>
                        <div class="font-bold text-[#2BD46A] text-sm">{{ t('kouventa.WhatsApp Cost Calculator', 'WhatsApp Cost Calculator') }}</div>
                        <div class="text-[11px] text-[#B9C6D3]">Zero markup on Meta conversation fees</div>
                    </div>
                </div>
                <span class="text-[#2BD46A] font-bold">→</span>
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
     class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#071C33] text-white rounded-t-[28px] pt-3 pb-8 px-6 shadow-2xl overflow-y-auto overscroll-y-contain flex flex-col justify-between border-t border-white/10"
     style="z-index: 10002; max-height: 85vh;">
    
    <div>
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-[#B9C6D3] uppercase tracking-wider">{{ t('kouventa.More Navigation', 'More Navigation') }}</span>
            <button @click="activeSheet = null" class="p-2 text-white/70 hover:text-white bg-white/10 rounded-full transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-2.5">
            <!-- About Us -->
            <a href="{{ localized_url('/about') }}" @click="activeSheet = null" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white no-underline">
                <div class="w-9 h-9 rounded-xl bg-[#2BD46A]/20 text-[#2BD46A] flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-white text-sm">{{ t('kouventa.About us', 'About us') }}</div>
                    <div class="text-[11px] text-[#B9C6D3]">Mission, leadership, and milestones</div>
                </div>
                <span class="text-[#B9C6D3]">→</span>
            </a>

            <!-- Insight / Blog -->
            <a href="{{ localized_url('/insight') }}" @click="activeSheet = null" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white no-underline">
                <div class="w-9 h-9 rounded-xl bg-[#2BD46A]/20 text-[#2BD46A] flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-white text-sm">{{ t('kouventa.Insight', 'Insight (Blog)') }}</div>
                    <div class="text-[11px] text-[#B9C6D3]">Articles, case studies & updates</div>
                </div>
                <span class="text-[#B9C6D3]">→</span>
            </a>

            <!-- Book a Demo -->
            <a href="{{ localized_url('/book-demo') }}" @click="activeSheet = null" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-[#2BD46A]/15 hover:bg-[#2BD46A]/25 border border-[#2BD46A]/30 transition-all text-white no-underline">
                <div class="w-9 h-9 rounded-xl bg-[#2BD46A] text-[#06251A] flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-[#2BD46A] text-sm">{{ t('kouventa.Book a demo', 'Book a demo') }}</div>
                    <div class="text-[11px] text-[#B9C6D3]">Get in touch for enterprise consultation</div>
                </div>
                <span class="text-[#2BD46A] font-bold">→</span>
            </a>

            <!-- Social Links -->
            <div class="pt-2 flex items-center justify-between border-t border-white/10 text-xs text-[#B9C6D3]">
                <span>Follow us</span>
                <div class="flex items-center gap-2">
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg bg-white/10 text-white font-semibold text-xs no-underline flex items-center gap-1.5">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                        Instagram
                    </a>
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg bg-white/10 text-white font-semibold text-xs no-underline flex items-center gap-1.5">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="h-20 w-full"></div>
</div>
