{{-- =========================================================================
     SOLUTION CE SCHEMA TEMPLATE
     Tabs:
     - hero: Hero Section
     - role_sales: Sales & Marketing
     - role_cs: Customer Service
     - role_ops: Operations
     - cta: Bottom CTA
     ========================================================================= --}}

{{-- ==================== TAB: HERO ==================== --}}
@if($activeTab === 'hero')
<div class="space-y-6">
    <div class="border-b border-gray-100 dark:border-[#272B30] pb-4">
        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Hero Section</h2>
        <p class="text-xs text-[#6F767E] mt-0.5">Edit hero title, subtitle, CTA buttons, and top sub-navigation.</p>
    </div>

    {{-- Subnav --}}
    <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Sub-Navigation Bar</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Category Label</label>
                <input type="text" wire:model.defer="sections.hero.subnav.label" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">AI Tab Label</label>
                <input type="text" wire:model.defer="sections.hero.subnav.ai_label" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">CE Tab Label</label>
                <input type="text" wire:model.defer="sections.hero.subnav.ce_label" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
        </div>
    </div>

    {{-- Titles --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Title (Prefix - White)</label>
            <input type="text" wire:model.defer="sections.hero.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Title (Main - Green)</label>
            <input type="text" wire:model.defer="sections.hero.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Subtitle</label>
        <textarea wire:model.defer="sections.hero.subtitle" rows="3" class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium"></textarea>
    </div>

    {{-- CTAs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary CTA Button</h3>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.hero.primary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button URL</label>
                <input type="text" wire:model.defer="sections.hero.primary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Action Button</h3>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.hero.secondary_btn.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <p class="text-[11px] text-[#6F767E]">Triggers smooth scroll directly to role tabs.</p>
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: SALES & MARKETING ==================== --}}
@if($activeTab === 'role_sales')
<div class="space-y-6">
    <div class="border-b border-gray-100 dark:border-[#272B30] pb-4">
        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Sales & Marketing Solutions</h2>
        <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Sales & Marketing.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Tab Label</label>
            <input type="text" wire:model.defer="sections.roles.sales_marketing.name" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Badge Code</label>
            <input type="text" wire:model.defer="sections.roles.sales_marketing.badge" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
    </div>

    {{-- Challenges (4 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges (4 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @for($i = 0; $i < 4; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                    <textarea wire:model.defer="sections.roles.sales_marketing.challenges.{{ $i }}.text" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endfor
        </div>
    </div>

    {{-- Solutions (5 Items) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">How Kouventa Helps (5 Solutions)</h3>
        <div class="space-y-3">
            @foreach($sections['roles']['sales_marketing']['solutions'] ?? [] as $sIdx => $sol)
                <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
                    <span class="text-[11px] font-bold text-green-600 uppercase">Product Solution #{{ $sIdx + 1 }}</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Name</label>
                            <input type="text" wire:model.defer="sections.roles.sales_marketing.solutions.{{ $sIdx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Description</label>
                            <textarea wire:model.defer="sections.roles.sales_marketing.solutions.{{ $sIdx }}.desc" rows="2" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Business Impact (4 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact (4 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @for($i = 0; $i < 4; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                    <input type="text" wire:model.defer="sections.roles.sales_marketing.impacts.{{ $i }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                </div>
            @endfor
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: CUSTOMER SERVICE ==================== --}}
@if($activeTab === 'role_cs')
<div class="space-y-6">
    <div class="border-b border-gray-100 dark:border-[#272B30] pb-4">
        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Customer Service Solutions</h2>
        <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Customer Service.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Tab Label</label>
            <input type="text" wire:model.defer="sections.roles.customer_service.name" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Badge Code</label>
            <input type="text" wire:model.defer="sections.roles.customer_service.badge" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
    </div>

    {{-- Challenges (3 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges (3 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @for($i = 0; $i < 3; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                    <textarea wire:model.defer="sections.roles.customer_service.challenges.{{ $i }}.text" rows="4" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endfor
        </div>
    </div>

    {{-- Solutions (3 Items) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">How Kouventa Helps (3 Solutions)</h3>
        <div class="space-y-3">
            @foreach($sections['roles']['customer_service']['solutions'] ?? [] as $sIdx => $sol)
                <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
                    <span class="text-[11px] font-bold text-green-600 uppercase">Product Solution #{{ $sIdx + 1 }}</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Name</label>
                            <input type="text" wire:model.defer="sections.roles.customer_service.solutions.{{ $sIdx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Description</label>
                            <textarea wire:model.defer="sections.roles.customer_service.solutions.{{ $sIdx }}.desc" rows="2" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Business Impact (3 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact (3 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @for($i = 0; $i < 3; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                    <input type="text" wire:model.defer="sections.roles.customer_service.impacts.{{ $i }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold">
                    <textarea wire:model.defer="sections.roles.customer_service.impacts.{{ $i }}.desc" rows="3" class="w-full p-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endfor
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: OPERATIONS ==================== --}}
@if($activeTab === 'role_ops')
<div class="space-y-6">
    <div class="border-b border-gray-100 dark:border-[#272B30] pb-4">
        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Operations Solutions</h2>
        <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Operations.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Tab Label</label>
            <input type="text" wire:model.defer="sections.roles.operations.name" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Badge Code</label>
            <input type="text" wire:model.defer="sections.roles.operations.badge" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
    </div>

    {{-- Challenges (2 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges (2 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @for($i = 0; $i < 2; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                    <textarea wire:model.defer="sections.roles.operations.challenges.{{ $i }}.text" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endfor
        </div>
    </div>

    {{-- Solutions (1 Item) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">How Kouventa Helps (Solution)</h3>
        <div class="space-y-3">
            @foreach($sections['roles']['operations']['solutions'] ?? [] as $sIdx => $sol)
                <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
                    <span class="text-[11px] font-bold text-green-600 uppercase">Product Solution #{{ $sIdx + 1 }}</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Name</label>
                            <input type="text" wire:model.defer="sections.roles.operations.solutions.{{ $sIdx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Solution Description</label>
                            <textarea wire:model.defer="sections.roles.operations.solutions.{{ $sIdx }}.desc" rows="2" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Business Impact (2 Cards) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact (2 Cards)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @for($i = 0; $i < 2; $i++)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                    <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                    <input type="text" wire:model.defer="sections.roles.operations.impacts.{{ $i }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold">
                    <textarea wire:model.defer="sections.roles.operations.impacts.{{ $i }}.desc" rows="2" class="w-full p-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endfor
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: CTA ==================== --}}
@if($activeTab === 'cta')
<div class="space-y-6">
    <div class="border-b border-gray-100 dark:border-[#272B30] pb-4">
        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Bottom Closing CTA</h2>
        <p class="text-xs text-[#6F767E] mt-0.5">Edit closing banner heading, copy, and call-to-action buttons.</p>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Title</label>
        <input type="text" wire:model.defer="sections.cta.title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Subtitle</label>
        <textarea wire:model.defer="sections.cta.subtitle" rows="3" class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary Action (Left Button)</h3>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.cta.primary_btn.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button URL</label>
                <input type="text" wire:model.defer="sections.cta.primary_btn.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Action (Right Button)</h3>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.cta.secondary_btn.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Button URL</label>
                <input type="text" wire:model.defer="sections.cta.secondary_btn.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
            </div>
        </div>
    </div>
</div>
@endif
