{{-- =========================================================================
     Pricing Schema View: AI & CE Tiers, Calculator, Add-ons & CTAs
     Tabs:
     - hero: Hero Section & Toggle
     - tiers_ai: AI Tiers (Starter, Grow, Scale)
     - tiers_ce: CE Tiers (Starter, Professional, Enterprise)
     - addons: Add-ons & Extensions
     - templates: WhatsApp Templates
     - calculator: WhatsApp Calculator Rates
     - cta: Bottom CTA
     ========================================================================= --}}

{{-- ==================== TAB: HERO ==================== --}}
@if($activeTab === 'hero')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Hero Section & Platform Toggle</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Edit main pricing headline, subtitle, and platform toggle buttons.</p>
        </div>
        <button type="button" wire:click="resetSection('hero')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Hero Badge (Optional)</label>
        <input type="text" wire:model.defer="sections.hero.badge" class="w-full max-w-sm h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
    </div>

    {{-- Title Structure --}}
    <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Title Structure</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Prefix</label>
                <input type="text" wire:model.defer="sections.hero.title.prefix" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Highlight (Green text)</label>
                <input type="text" wire:model.defer="sections.hero.title.highlight" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm text-[#2BD46A] font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Suffix</label>
                <input type="text" wire:model.defer="sections.hero.title.suffix" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Subtitle</label>
        <textarea rows="3" wire:model.defer="sections.hero.subtitle" class="w-full p-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium"></textarea>
    </div>

    {{-- Toggle Buttons --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Toggle Label (AI)</label>
            <input type="text" wire:model.defer="sections.hero.toggle_ai" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Toggle Label (Customer Engagement)</label>
            <input type="text" wire:model.defer="sections.hero.toggle_ce" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: TIERS AI ==================== --}}
@if($activeTab === 'tiers_ai')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Artificial Intelligence Pricing Plans</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure Starter, Grow, and Scale packages for AI platform.</p>
        </div>
        <button type="button" wire:click="resetSection('tiers_ai')" wire:confirm="Reset AI pricing tiers to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    @foreach(['starter' => 'Starter Plan', 'grow' => 'Grow Plan (Featured)', 'scale' => 'Scale Plan'] as $tKey => $tLabel)
    <div class="p-5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $tLabel }}</h3>
            @if($tKey === 'grow')
            <div class="flex items-center gap-2">
                <label class="text-xs text-[#6F767E]">Badge:</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.badge" class="h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#2BD46A]">
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Plan Display Name</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.name" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Subtitle / Short Pitch</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.desc" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Price (e.g. IDR 5.999.000 or Custom pricing)</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.price" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Billing Period / Subtext</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.period" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Highlight Callout (Green pill)</label>
            <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.highlight" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-medium">
        </div>

        {{-- Feature Bullets --}}
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1.5">Feature Bullets (6 items)</label>
            <div class="space-y-2">
                @for($b = 0; $b < 6; $b++)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-[#6F767E] w-4">{{ $b + 1 }}.</span>
                    <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.bullets.{{ $b }}" class="flex-grow h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                </div>
                @endfor
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">CTA Button Text</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.cta_text" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-semibold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">CTA Button URL</label>
                <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.cta_url" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-mono">
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ==================== TAB: TIERS CE ==================== --}}
@if($activeTab === 'tiers_ce')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Customer Engagement Pricing Plans</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure Starter, Professional, and Enterprise packages for CE platform.</p>
        </div>
        <button type="button" wire:click="resetSection('tiers_ce')" wire:confirm="Reset CE pricing tiers to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    @foreach(['starter' => 'Starter Plan', 'pro' => 'Professional Plan (Featured)', 'enterprise' => 'Enterprise Plan'] as $tKey => $tLabel)
    <div class="p-5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $tLabel }}</h3>
            @if($tKey === 'pro')
            <div class="flex items-center gap-2">
                <label class="text-xs text-[#6F767E]">Badge:</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.badge" class="h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#2BD46A]">
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Plan Display Name</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.name" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Subtitle / Short Pitch</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.desc" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Price (e.g. IDR 1.499.000 or Custom pricing)</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.price" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Billing Period / Subtext</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.period" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Highlight Callout (Green pill)</label>
            <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.highlight" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-medium">
        </div>

        {{-- Feature Bullets --}}
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1.5">Feature Bullets (6 items)</label>
            <div class="space-y-2">
                @for($b = 0; $b < 6; $b++)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-[#6F767E] w-4">{{ $b + 1 }}.</span>
                    <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.bullets.{{ $b }}" class="flex-grow h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                </div>
                @endfor
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">CTA Button Text</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.cta_text" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-semibold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">CTA Button URL</label>
                <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.cta_url" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-mono">
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ==================== TAB: ADD-ONS ==================== --}}
@if($activeTab === 'addons')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Extend Your Plan (Add-ons)</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Edit add-on titles, descriptions, and callout text.</p>
        </div>
        <button type="button" wire:click="resetSection('addons')" wire:confirm="Reset Add-ons to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Section Title</label>
            <input type="text" wire:model.defer="sections.addons.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-bold">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Subtitle (AI tab)</label>
            <input type="text" wire:model.defer="sections.addons.subtitle_ai" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Subtitle (CE tab)</label>
            <input type="text" wire:model.defer="sections.addons.subtitle_ce" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm">
        </div>
    </div>

    {{-- CE Addons --}}
    <div class="space-y-3 pt-2">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Customer Engagement Add-ons (10 items)</h3>
        <div class="space-y-2">
            @foreach($sections['addons']['items_ce'] ?? [] as $i => $item)
            <div class="p-3 bg-gray-50 dark:bg-[#202428] rounded-lg border border-gray-100 dark:border-[#272B30] grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <label class="block text-[11px] text-[#6F767E] mb-0.5">Title</label>
                    <input type="text" wire:model.defer="sections.addons.items_ce.{{ $i }}.title" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] text-[#6F767E] mb-0.5">Description</label>
                    <input type="text" wire:model.defer="sections.addons.items_ce.{{ $i }}.desc" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- AI Addons --}}
    <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-[#272B30]">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">AI Platform Add-ons (10 items)</h3>
        <div class="space-y-2">
            @foreach($sections['addons']['items_ai'] ?? [] as $i => $item)
            <div class="p-3 bg-gray-50 dark:bg-[#202428] rounded-lg border border-gray-100 dark:border-[#272B30] grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <label class="block text-[11px] text-[#6F767E] mb-0.5">Title</label>
                    <input type="text" wire:model.defer="sections.addons.items_ai.{{ $i }}.title" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] text-[#6F767E] mb-0.5">Description</label>
                    <input type="text" wire:model.defer="sections.addons.items_ai.{{ $i }}.desc" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: TEMPLATES ==================== --}}
@if($activeTab === 'templates')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">WhatsApp Message Templates</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure 4 template categories (Marketing, Utility, Authentication, Service).</p>
        </div>
        <button type="button" wire:click="resetSection('templates')" wire:confirm="Reset WhatsApp Templates to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Section Title</label>
            <input type="text" wire:model.defer="sections.templates.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-bold">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Section Subtitle</label>
            <input type="text" wire:model.defer="sections.templates.subtitle" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm">
        </div>
    </div>

    <div class="space-y-4 pt-2">
        @foreach($sections['templates']['items'] ?? [] as $i => $tp)
        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">Template Name (Category {{ $tp['key'] ?? '' }})</label>
                <input type="text" wire:model.defer="sections.templates.items.{{ $i }}.name" class="w-full max-w-sm h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1.5">Use Case Items</label>
                <div class="space-y-1.5">
                    @foreach($tp['items'] ?? [] as $itIdx => $item)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono text-[#6F767E] w-4">•</span>
                        <input type="text" wire:model.defer="sections.templates.items.{{ $i }}.items.{{ $itIdx }}" class="flex-grow h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ==================== TAB: CALCULATOR ==================== --}}
@if($activeTab === 'calculator')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">WhatsApp Cost Calculator Rates</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Set conversation fees and tier platform thresholds used in the interactive cost estimator.</p>
        </div>
        <button type="button" wire:click="resetSection('calculator')" wire:confirm="Reset Calculator rates to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Calculator Title</label>
            <input type="text" wire:model.defer="sections.calculator.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-bold">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Calculator Subtitle</label>
            <textarea rows="2" wire:model.defer="sections.calculator.subtitle" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs"></textarea>
        </div>
    </div>

    {{-- Meta Conversation Rates --}}
    <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Per-Conversation Rates (IDR)</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Marketing (IDR)</label>
                <input type="number" wire:model.defer="sections.calculator.rates.mk" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Utility (IDR)</label>
                <input type="number" wire:model.defer="sections.calculator.rates.ut" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Authentication (IDR)</label>
                <input type="number" wire:model.defer="sections.calculator.rates.au" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Service (IDR)</label>
                <input type="number" wire:model.defer="sections.calculator.rates.sv" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-sm font-bold">
            </div>
        </div>
    </div>

    {{-- Platform Fees & Thresholds --}}
    <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Plan Thresholds & Base Platform Fees</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <span class="text-xs font-bold text-[#13803F]">Starter Tier</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-1">Max MAU Threshold</label>
                        <input type="number" wire:model.defer="sections.calculator.fees.starter_mau" class="w-full h-9 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-1">Monthly Fee (IDR)</label>
                        <input type="number" wire:model.defer="sections.calculator.fees.starter_fee" class="w-full h-9 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-bold text-[#2BD46A]">Professional Tier</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-1">Max MAU Threshold</label>
                        <input type="number" wire:model.defer="sections.calculator.fees.pro_mau" class="w-full h-9 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-1">Monthly Fee (IDR)</label>
                        <input type="number" wire:model.defer="sections.calculator.fees.pro_fee" class="w-full h-9 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: CTA ==================== --}}
@if($activeTab === 'cta')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Bottom Call to Action</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Edit closing banner headline, subtitle, and action buttons.</p>
        </div>
        <button type="button" wire:click="resetSection('cta')" wire:confirm="Reset CTA section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Title</label>
        <input type="text" wire:model.defer="sections.cta.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-bold">
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Subtitle</label>
        <textarea rows="3" wire:model.defer="sections.cta.subtitle" class="w-full p-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
            <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary Button</span>
            <div>
                <label class="block text-[11px] text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.cta.primary_btn.text" class="w-full h-8 px-2.5 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-semibold">
            </div>
            <div>
                <label class="block text-[11px] text-[#6F767E] mb-1">Button URL</label>
                <input type="text" wire:model.defer="sections.cta.primary_btn.url" class="w-full h-8 px-2.5 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-mono">
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
            <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Button</span>
            <div>
                <label class="block text-[11px] text-[#6F767E] mb-1">Button Text</label>
                <input type="text" wire:model.defer="sections.cta.secondary_btn.text" class="w-full h-8 px-2.5 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-semibold">
            </div>
            <div>
                <label class="block text-[11px] text-[#6F767E] mb-1">Button URL</label>
                <input type="text" wire:model.defer="sections.cta.secondary_btn.url" class="w-full h-8 px-2.5 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-mono">
            </div>
        </div>
    </div>
</div>
@endif

