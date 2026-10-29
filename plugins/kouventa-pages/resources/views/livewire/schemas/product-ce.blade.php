{{-- Schema Form Fields for Product: Customer Engagement --}}

{{-- 1. HERO SECTION --}}
@if($activeTab === 'hero')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">1. Hero & Navigation</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit main title, subtitle, Meta badge, CTA button, and interactive inbox mockup.</p>
            </div>
            <button type="button" wire:click="resetSection('hero')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="space-y-4">
            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Products Sub-navigation Labels</span>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Prefix Label</label>
                        <input type="text" wire:model.defer="sections.hero.subnav.label" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">AI Tab Label</label>
                        <input type="text" wire:model.defer="sections.hero.subnav.ai_label" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Customer Engagement Tab Label</label>
                        <input type="text" wire:model.defer="sections.hero.subnav.ce_label" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Eyebrow Badge (Meta BSP)</label>
                <input type="text" wire:model.defer="sections.hero.eyebrow" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Title Prefix (White Text)</label>
                    <input type="text" wire:model.defer="sections.hero.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Title Highlight (Green Text)</label>
                    <input type="text" wire:model.defer="sections.hero.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Subtitle</label>
                <textarea rows="3" wire:model.defer="sections.hero.subtitle" class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary CTA Button (Green)</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" placeholder="Button Text" wire:model.defer="sections.hero.primary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    <input type="text" placeholder="Button URL (e.g. /book-demo)" wire:model.defer="sections.hero.primary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
                </div>
            </div>

            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Hero Unified Inbox Simulation Texts</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Channel Tab Label</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.all_channels" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Customer Profile Subtitle</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.profile_label" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Bubble 1: Instagram Customer Inquiry</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_m1" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Bubble 2: Agent Response</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_m2" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Divider Timeline Label</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_divider" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Bubble 3: Broadcast Campaign Text</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_m3_text" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Bubble 4: WhatsApp Follow-up Question</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_m4" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Bubble 5: Kouventa AI Reply</label>
                        <input type="text" wire:model.defer="sections.hero.mockup.chat_m5" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- 2. CHALLENGES SECTION --}}
@if($activeTab === 'challenges')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">2. The Challenges (3 Cards)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit heading and the 3 pain points addressed by Kouventa Customer Engagement.</p>
            </div>
            <button type="button" wire:click="resetSection('challenges')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Prefix</label>
                <input type="text" wire:model.defer="sections.challenges.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Highlight (Green)</label>
                <input type="text" wire:model.defer="sections.challenges.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="space-y-4 pt-2">
            @foreach($sections['challenges']['cards'] ?? [] as $idx => $card)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Challenge Card {{ $idx + 1 }}</span>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Title</label>
                        <input type="text" wire:model.defer="sections.challenges.cards.{{ $idx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description</label>
                        <textarea rows="2" wire:model.defer="sections.challenges.cards.{{ $idx }}.desc" class="w-full p-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 3. CUSTOMER JOURNEY (4 STAGES) --}}
@if($activeTab === 'journey')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">3. Customer Journey (4 Stages)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Manage heading, customer pointer, and the 4 interactive stages (Prospecting, Onboarding, Upselling, Support).</p>
            </div>
            <button type="button" wire:click="resetSection('journey')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Prefix</label>
                <input type="text" wire:model.defer="sections.journey.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Highlight (Green)</label>
                <input type="text" wire:model.defer="sections.journey.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Subtitle</label>
            <textarea rows="2" wire:model.defer="sections.journey.subtitle" class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="space-y-4 pt-2">
            @foreach($sections['journey']['stages'] ?? [] as $sIdx => $stg)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Stage {{ $stg['number'] ?? ($sIdx + 1) }}: {{ $stg['name'] ?? '' }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Stage Tab Name</label>
                            <input type="text" wire:model.defer="sections.journey.stages.{{ $sIdx }}.name" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Headline</label>
                            <input type="text" wire:model.defer="sections.journey.stages.{{ $sIdx }}.headline" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description / Sub</label>
                        <textarea rows="2" wire:model.defer="sections.journey.stages.{{ $sIdx }}.sub" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 4. CE PRODUCTS (6 PRODUCTS) --}}
@if($activeTab === 'products')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">4. CE Products (6 Products)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit headings, roles, features bullets, and text for Authentication, Broadcasting, Chatbot, Integration, Flows, and Omnichannel.</p>
            </div>
            <button type="button" wire:click="resetSection('products')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Prefix</label>
                <input type="text" wire:model.defer="sections.products.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Highlight (Green)</label>
                <input type="text" wire:model.defer="sections.products.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="space-y-6 pt-2">
            @php
                $productList = [
                    'auth' => '1. Authentication',
                    'broadcast' => '2. Broadcasting',
                    'chatbot' => '3. Chatbot',
                    'integration' => '4. Cross Platform Integration',
                    'flows' => '5. WhatsApp Flows',
                    'omni' => '6. Omnichannel',
                ];
            @endphp

            @foreach($productList as $pKey => $pLabel)
                <div class="p-4 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ $pLabel }}
                    </span>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Product Name</label>
                            <input type="text" wire:model.defer="sections.products.items.{{ $pKey }}.name" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Tag / Role in Tab</label>
                            <input type="text" wire:model.defer="sections.products.items.{{ $pKey }}.tag" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Headline</label>
                        <input type="text" wire:model.defer="sections.products.items.{{ $pKey }}.headline" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description</label>
                        <textarea rows="2" wire:model.defer="sections.products.items.{{ $pKey }}.desc" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>

                    <div class="space-y-2 pt-1">
                        <label class="block text-[11px] font-bold text-[#6F767E]">Feature Bullets (3 items)</label>
                        @for($b = 0; $b < 3; $b++)
                            <input type="text" placeholder="Bullet {{ $b + 1 }}" wire:model.defer="sections.products.items.{{ $pKey }}.bullets.{{ $b }}" class="w-full h-8 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 5. PLATFORM CONTROL (5 CARDS) --}}
@if($activeTab === 'platform_control')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">5. Platform Control (5 Cards)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Supervisor mode, Multi-account, Role-based access, History, and Ticketing.</p>
            </div>
            <button type="button" wire:click="resetSection('platform_control')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title</label>
            <input type="text" wire:model.defer="sections.platform_control.title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="space-y-4 pt-2">
            @foreach($sections['platform_control']['cards'] ?? [] as $idx => $card)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Card {{ $idx + 1 }}</span>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Title</label>
                        <input type="text" wire:model.defer="sections.platform_control.cards.{{ $idx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description</label>
                        <textarea rows="2" wire:model.defer="sections.platform_control.cards.{{ $idx }}.desc" class="w-full p-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 6. WHY KOUVENTA (4 CARDS) --}}
@if($activeTab === 'why')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">6. Why Kouventa (4 Cards)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Meta BSP, Built around business, Secure & trusted, and Post-paid messaging.</p>
            </div>
            <button type="button" wire:click="resetSection('why')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Prefix</label>
                <input type="text" wire:model.defer="sections.why.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title Highlight (Green)</label>
                <input type="text" wire:model.defer="sections.why.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="space-y-4 pt-2">
            @foreach($sections['why']['cards'] ?? [] as $idx => $card)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Card {{ $idx + 1 }}</span>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Title</label>
                        <input type="text" wire:model.defer="sections.why.cards.{{ $idx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description</label>
                        <textarea rows="2" wire:model.defer="sections.why.cards.{{ $idx }}.desc" class="w-full p-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 7. BOTTOM CTA --}}
@if($activeTab === 'cta')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">7. Bottom CTA</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit title and buttons in the bottom green panel.</p>
            </div>
            <button type="button" wire:click="resetSection('cta')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Title</label>
            <textarea rows="2" wire:model.defer="sections.cta.title" class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary Button (Navy)</span>
                <input type="text" placeholder="Button Text" wire:model.defer="sections.cta.primary_btn.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                <input type="text" placeholder="URL (e.g. /book-demo)" wire:model.defer="sections.cta.primary_btn.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
            </div>

            <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Button (Outline)</span>
                <input type="text" placeholder="Button Text" wire:model.defer="sections.cta.secondary_btn.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                <input type="text" placeholder="URL (e.g. /pricing?tab=ce)" wire:model.defer="sections.cta.secondary_btn.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
            </div>
        </div>
    </div>
@endif
