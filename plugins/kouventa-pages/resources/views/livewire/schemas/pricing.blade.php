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
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-[#6F767E]">Feature Bullets</label>
                <button type="button" wire:click="addRepeaterItem('tiers_ai.{{ $tKey }}.bullets', '')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Bullet
                </button>
            </div>
            <div class="space-y-2">
                @foreach($sections['tiers_ai'][$tKey]['bullets'] ?? [] as $b => $bullet)
                <div class="flex items-center gap-1.5" wire:key="tier-ai-bullet-{{ $tKey }}-{{ $b }}">
                    <span class="text-xs font-mono text-[#6F767E] w-5 text-right flex-shrink-0">{{ $b + 1 }}.</span>
                    <input type="text" wire:model.defer="sections.tiers_ai.{{ $tKey }}.bullets.{{ $b }}" class="flex-grow h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                    <div class="flex items-center gap-0.5 flex-shrink-0">
                        @if($b > 0)
                        <button type="button" wire:click="moveRepeaterItem('tiers_ai.{{ $tKey }}.bullets', {{ $b }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_upward</span>
                        </button>
                        @endif
                        @if($b < count($sections['tiers_ai'][$tKey]['bullets'] ?? []) - 1)
                        <button type="button" wire:click="moveRepeaterItem('tiers_ai.{{ $tKey }}.bullets', {{ $b }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_downward</span>
                        </button>
                        @endif
                        <button type="button" wire:click="removeRepeaterItem('tiers_ai.{{ $tKey }}.bullets', {{ $b }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                            <span class="material-symbols-outlined text-sm leading-none">delete</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" wire:click="addRepeaterItem('tiers_ai.{{ $tKey }}.bullets', '')" class="mt-2 w-full py-1.5 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">add</span>
                Add Bullet
            </button>
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
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-[#6F767E]">Feature Bullets</label>
                <button type="button" wire:click="addRepeaterItem('tiers_ce.{{ $tKey }}.bullets', '')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Bullet
                </button>
            </div>
            <div class="space-y-2">
                @foreach($sections['tiers_ce'][$tKey]['bullets'] ?? [] as $b => $bullet)
                <div class="flex items-center gap-1.5" wire:key="tier-ce-bullet-{{ $tKey }}-{{ $b }}">
                    <span class="text-xs font-mono text-[#6F767E] w-5 text-right flex-shrink-0">{{ $b + 1 }}.</span>
                    <input type="text" wire:model.defer="sections.tiers_ce.{{ $tKey }}.bullets.{{ $b }}" class="flex-grow h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                    <div class="flex items-center gap-0.5 flex-shrink-0">
                        @if($b > 0)
                        <button type="button" wire:click="moveRepeaterItem('tiers_ce.{{ $tKey }}.bullets', {{ $b }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_upward</span>
                        </button>
                        @endif
                        @if($b < count($sections['tiers_ce'][$tKey]['bullets'] ?? []) - 1)
                        <button type="button" wire:click="moveRepeaterItem('tiers_ce.{{ $tKey }}.bullets', {{ $b }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_downward</span>
                        </button>
                        @endif
                        <button type="button" wire:click="removeRepeaterItem('tiers_ce.{{ $tKey }}.bullets', {{ $b }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                            <span class="material-symbols-outlined text-sm leading-none">delete</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" wire:click="addRepeaterItem('tiers_ce.{{ $tKey }}.bullets', '')" class="mt-2 w-full py-1.5 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">add</span>
                Add Bullet
            </button>
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

{{-- ==================== TAB: COMPARE AI FEATURES ==================== --}}
@if($activeTab === 'compare_ai')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Artificial Intelligence Feature Comparison Matrix</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Manage categories, features, and plan inclusions across Starter, Grow, and Scale tiers.</p>
        </div>
        <button type="button" wire:click="resetSection('compare_ai')" wire:confirm="Reset AI feature comparison table to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div class="space-y-4">
        @foreach(($sections['compare_ai'] ?? []) as $cIdx => $cat)
        <div x-data="{ open: true }" class="bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-200 dark:border-[#272B30] overflow-hidden">
            <!-- Category Header -->
            <div class="p-3.5 bg-gray-100/70 dark:bg-[#1A1D1F] flex items-center justify-between gap-3 border-b border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center gap-2 flex-1">
                    <button type="button" @click="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined text-base transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <span class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-[#272B30] text-[10px] font-mono font-bold text-gray-700 dark:text-gray-300">#{{ $cIdx + 1 }}</span>
                    <input type="text" wire:model.defer="sections.compare_ai.{{ $cIdx }}.category" placeholder="Category Name (e.g. Generative AI)" class="flex-1 max-w-sm h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-xs font-bold">
                    <span class="text-[11px] text-[#6F767E] font-medium">({{ count($cat['rows'] ?? []) }} features)</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" wire:click="moveRepeaterItem('compare_ai', {{ $cIdx }}, 'up')" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->first) disabled @endif title="Move Category Up">
                        <span class="material-symbols-outlined text-sm">arrow_upward</span>
                    </button>
                    <button type="button" wire:click="moveRepeaterItem('compare_ai', {{ $cIdx }}, 'down')" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->last) disabled @endif title="Move Category Down">
                        <span class="material-symbols-outlined text-sm">arrow_downward</span>
                    </button>
                    <button type="button" wire:click="removeRepeaterItem('compare_ai', {{ $cIdx }})" wire:confirm="Delete this entire category and all its features?" class="p-1 rounded text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30" title="Delete Category">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>

            <!-- Category Rows -->
            <div x-show="open" class="p-4 space-y-3">
                <div class="hidden sm:grid sm:grid-cols-12 gap-2 text-[11px] font-bold text-[#6F767E] px-2 pb-1 border-b border-gray-200 dark:border-[#272B30]">
                    <div class="sm:col-span-5">Feature Name</div>
                    <div class="sm:col-span-2">Starter</div>
                    <div class="sm:col-span-2">Grow</div>
                    <div class="sm:col-span-2">Scale</div>
                    <div class="sm:col-span-1 text-right">Action</div>
                </div>

                @foreach(($cat['rows'] ?? []) as $rIdx => $row)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-2.5 rounded-lg bg-white dark:bg-[#1A1D1F] border border-gray-100 dark:border-[#272B30] items-center">
                    <div class="sm:col-span-5">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Feature Name</label>
                        <input type="text" wire:model.defer="sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.feature" placeholder="Feature title" class="w-full h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs font-semibold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Starter</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col1" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col1', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col1', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col1', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Grow</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col2" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col2', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col2', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col2', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Scale</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col3" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col3', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col3', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ai.{{ $cIdx }}.rows.{{ $rIdx }}.col3', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-1 flex items-center justify-end gap-1">
                        <button type="button" wire:click="moveRepeaterItem('compare_ai.{{ $cIdx }}.rows', {{ $rIdx }}, 'up')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->first) disabled @endif title="Move Row Up">
                            <span class="material-symbols-outlined text-xs">arrow_upward</span>
                        </button>
                        <button type="button" wire:click="moveRepeaterItem('compare_ai.{{ $cIdx }}.rows', {{ $rIdx }}, 'down')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->last) disabled @endif title="Move Row Down">
                            <span class="material-symbols-outlined text-xs">arrow_downward</span>
                        </button>
                        <button type="button" wire:click="removeRepeaterItem('compare_ai.{{ $cIdx }}.rows', {{ $rIdx }})" class="p-1 text-red-400 hover:text-red-600" title="Delete Row">
                            <span class="material-symbols-outlined text-xs">close</span>
                        </button>
                    </div>
                </div>
                @endforeach

                <button type="button" wire:click="addRepeaterItem('compare_ai.{{ $cIdx }}.rows', {{ var_export(['feature' => '', 'col1' => '—', 'col2' => '✓', 'col3' => '✓'], true) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Add Feature Row to {{ $cat['category'] ?? 'Category' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <button type="button" wire:click="addRepeaterItem('compare_ai', {{ var_export(['category' => 'New Category', 'rows' => []], true) }})" class="w-full py-3 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#33383F] text-sm font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-base">add_circle</span>
        Add New Comparison Category (AI)
    </button>
</div>
@endif

{{-- ==================== TAB: COMPARE CE FEATURES ==================== --}}
@if($activeTab === 'compare_ce')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Customer Engagement Feature Comparison Matrix</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Manage categories, features, and plan inclusions across Starter, Professional, and Enterprise tiers.</p>
        </div>
        <button type="button" wire:click="resetSection('compare_ce')" wire:confirm="Reset CE feature comparison table to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div class="space-y-4">
        @foreach(($sections['compare_ce'] ?? []) as $cIdx => $cat)
        <div x-data="{ open: true }" class="bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-200 dark:border-[#272B30] overflow-hidden">
            <!-- Category Header -->
            <div class="p-3.5 bg-gray-100/70 dark:bg-[#1A1D1F] flex items-center justify-between gap-3 border-b border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center gap-2 flex-1">
                    <button type="button" @click="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined text-base transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    <span class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-[#272B30] text-[10px] font-mono font-bold text-gray-700 dark:text-gray-300">#{{ $cIdx + 1 }}</span>
                    <input type="text" wire:model.defer="sections.compare_ce.{{ $cIdx }}.category" placeholder="Category Name (e.g. Channels)" class="flex-1 max-w-sm h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-xs font-bold">
                    <span class="text-[11px] text-[#6F767E] font-medium">({{ count($cat['rows'] ?? []) }} features)</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" wire:click="moveRepeaterItem('compare_ce', {{ $cIdx }}, 'up')" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->first) disabled @endif title="Move Category Up">
                        <span class="material-symbols-outlined text-sm">arrow_upward</span>
                    </button>
                    <button type="button" wire:click="moveRepeaterItem('compare_ce', {{ $cIdx }}, 'down')" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->last) disabled @endif title="Move Category Down">
                        <span class="material-symbols-outlined text-sm">arrow_downward</span>
                    </button>
                    <button type="button" wire:click="removeRepeaterItem('compare_ce', {{ $cIdx }})" wire:confirm="Delete this entire category and all its features?" class="p-1 rounded text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30" title="Delete Category">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>

            <!-- Category Rows -->
            <div x-show="open" class="p-4 space-y-3">
                <div class="hidden sm:grid sm:grid-cols-12 gap-2 text-[11px] font-bold text-[#6F767E] px-2 pb-1 border-b border-gray-200 dark:border-[#272B30]">
                    <div class="sm:col-span-5">Feature Name</div>
                    <div class="sm:col-span-2">Starter</div>
                    <div class="sm:col-span-2">Professional</div>
                    <div class="sm:col-span-2">Enterprise</div>
                    <div class="sm:col-span-1 text-right">Action</div>
                </div>

                @foreach(($cat['rows'] ?? []) as $rIdx => $row)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-2.5 rounded-lg bg-white dark:bg-[#1A1D1F] border border-gray-100 dark:border-[#272B30] items-center">
                    <div class="sm:col-span-5">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Feature Name</label>
                        <input type="text" wire:model.defer="sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.feature" placeholder="Feature title" class="w-full h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs font-semibold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Starter</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col1" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col1', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col1', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col1', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Professional</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col2" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col2', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col2', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col2', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="sm:hidden block text-[10px] font-bold text-[#6F767E] mb-0.5">Enterprise</label>
                        <div class="space-y-1">
                            <input type="text" wire:model.defer="sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col3" placeholder="✓ / — / text" class="w-full h-8 px-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#111315] text-xs">
                            <div class="flex gap-1">
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col3', '✓')" class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 rounded hover:bg-green-100">✓</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col3', '—')" class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded hover:bg-gray-200">—</button>
                                <button type="button" @click="$wire.set('sections.compare_ce.{{ $cIdx }}.rows.{{ $rIdx }}.col3', 'Add-on')" class="px-1 py-0.5 text-[9px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 rounded hover:bg-gray-200">+Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-1 flex items-center justify-end gap-1">
                        <button type="button" wire:click="moveRepeaterItem('compare_ce.{{ $cIdx }}.rows', {{ $rIdx }}, 'up')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->first) disabled @endif title="Move Row Up">
                            <span class="material-symbols-outlined text-xs">arrow_upward</span>
                        </button>
                        <button type="button" wire:click="moveRepeaterItem('compare_ce.{{ $cIdx }}.rows', {{ $rIdx }}, 'down')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30" @if($loop->last) disabled @endif title="Move Row Down">
                            <span class="material-symbols-outlined text-xs">arrow_downward</span>
                        </button>
                        <button type="button" wire:click="removeRepeaterItem('compare_ce.{{ $cIdx }}.rows', {{ $rIdx }})" class="p-1 text-red-400 hover:text-red-600" title="Delete Row">
                            <span class="material-symbols-outlined text-xs">close</span>
                        </button>
                    </div>
                </div>
                @endforeach

                <button type="button" wire:click="addRepeaterItem('compare_ce.{{ $cIdx }}.rows', {{ var_export(['feature' => '', 'col1' => '—', 'col2' => '✓', 'col3' => '✓'], true) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Add Feature Row to {{ $cat['category'] ?? 'Category' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <button type="button" wire:click="addRepeaterItem('compare_ce', {{ var_export(['category' => 'New Category', 'rows' => []], true) }})" class="w-full py-3 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#33383F] text-sm font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-base">add_circle</span>
        Add New Comparison Category (CE)
    </button>
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
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Customer Engagement Add-ons</h3>
            <button type="button" wire:click="addRepeaterItem('addons.items_ce', {{ json_encode(['title' => '', 'desc' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add CE Add-on
            </button>
        </div>
        <div class="space-y-2">
            @foreach($sections['addons']['items_ce'] ?? [] as $i => $item)
            <div class="p-3 bg-gray-50 dark:bg-[#202428] rounded-lg border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="ce-addon-{{ $i }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-bold text-[#6F767E]">#{{ $i + 1 }}</span>
                    <div class="flex items-center gap-1">
                        @if($i > 0)
                        <button type="button" wire:click="moveRepeaterItem('addons.items_ce', {{ $i }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_upward</span>
                        </button>
                        @endif
                        @if($i < count($sections['addons']['items_ce'] ?? []) - 1)
                        <button type="button" wire:click="moveRepeaterItem('addons.items_ce', {{ $i }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_downward</span>
                        </button>
                        @endif
                        <button type="button" wire:click="removeRepeaterItem('addons.items_ce', {{ $i }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                            <span class="material-symbols-outlined text-sm leading-none">delete</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-0.5">Title</label>
                        <input type="text" wire:model.defer="sections.addons.items_ce.{{ $i }}.title" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] text-[#6F767E] mb-0.5">Description</label>
                        <input type="text" wire:model.defer="sections.addons.items_ce.{{ $i }}.desc" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('addons.items_ce', {{ json_encode(['title' => '', 'desc' => '']) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span>
            Add CE Add-on
        </button>
    </div>

    {{-- AI Addons --}}
    <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-[#272B30]">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">AI Platform Add-ons</h3>
            <button type="button" wire:click="addRepeaterItem('addons.items_ai', {{ json_encode(['title' => '', 'desc' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add AI Add-on
            </button>
        </div>
        <div class="space-y-2">
            @foreach($sections['addons']['items_ai'] ?? [] as $i => $item)
            <div class="p-3 bg-gray-50 dark:bg-[#202428] rounded-lg border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="ai-addon-{{ $i }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-bold text-[#6F767E]">#{{ $i + 1 }}</span>
                    <div class="flex items-center gap-1">
                        @if($i > 0)
                        <button type="button" wire:click="moveRepeaterItem('addons.items_ai', {{ $i }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_upward</span>
                        </button>
                        @endif
                        @if($i < count($sections['addons']['items_ai'] ?? []) - 1)
                        <button type="button" wire:click="moveRepeaterItem('addons.items_ai', {{ $i }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_downward</span>
                        </button>
                        @endif
                        <button type="button" wire:click="removeRepeaterItem('addons.items_ai', {{ $i }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                            <span class="material-symbols-outlined text-sm leading-none">delete</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[11px] text-[#6F767E] mb-0.5">Title</label>
                        <input type="text" wire:model.defer="sections.addons.items_ai.{{ $i }}.title" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] text-[#6F767E] mb-0.5">Description</label>
                        <input type="text" wire:model.defer="sections.addons.items_ai.{{ $i }}.desc" class="w-full h-8 px-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('addons.items_ai', {{ json_encode(['title' => '', 'desc' => '']) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span>
            Add AI Add-on
        </button>
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
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-[#6F767E]">Use Case Items</label>
                    <button type="button" wire:click="addRepeaterItem('templates.items.{{ $i }}.items', '')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                        <span class="material-symbols-outlined text-xs">add_circle</span>
                        Add Item
                    </button>
                </div>
                <div class="space-y-1.5">
                    @foreach($tp['items'] ?? [] as $itIdx => $item)
                    <div class="flex items-center gap-1.5" wire:key="tmpl-item-{{ $i }}-{{ $itIdx }}">
                        <span class="text-xs font-mono text-[#6F767E] w-4 text-center">•</span>
                        <input type="text" wire:model.defer="sections.templates.items.{{ $i }}.items.{{ $itIdx }}" class="flex-grow h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1D1F] text-xs">
                        <div class="flex items-center gap-0.5 flex-shrink-0">
                            @if($itIdx > 0)
                            <button type="button" wire:click="moveRepeaterItem('templates.items.{{ $i }}.items', {{ $itIdx }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($itIdx < count($tp['items'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('templates.items.{{ $i }}.items', {{ $itIdx }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('templates.items.{{ $i }}.items', {{ $itIdx }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addRepeaterItem('templates.items.{{ $i }}.items', '')" class="mt-2 w-full py-1 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-xs">add</span>
                    Add Use Case Item
                </button>
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

