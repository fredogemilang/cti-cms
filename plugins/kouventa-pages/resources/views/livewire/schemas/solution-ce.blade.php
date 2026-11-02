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
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Hero Section</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Edit hero title, subtitle, CTA buttons, and top sub-navigation.</p>
        </div>
        <button type="button" wire:click="resetSection('hero')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
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
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Sales & Marketing Solutions</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Sales & Marketing.</p>
        </div>
        <button type="button" wire:click="resetSection('roles.sales_marketing')" wire:confirm="Reset Sales & Marketing to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
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

    {{-- Challenges --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges</h3>
            <button type="button" wire:click="addRepeaterItem('roles.sales_marketing.challenges')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Challenge
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($sections['roles']['sales_marketing']['challenges'] ?? [] as $i => $ch)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-sm-ch-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.sales_marketing.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['sales_marketing']['challenges'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.sales_marketing.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.sales_marketing.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <textarea wire:model.defer="sections.roles.sales_marketing.challenges.{{ $i }}.text" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.sales_marketing.challenges')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Challenge Card
        </button>
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

    {{-- Business Impact --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact</h3>
            <button type="button" wire:click="addRepeaterItem('roles.sales_marketing.impacts')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Impact
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($sections['roles']['sales_marketing']['impacts'] ?? [] as $i => $imp)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-sm-imp-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.sales_marketing.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['sales_marketing']['impacts'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.sales_marketing.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.sales_marketing.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <input type="text" wire:model.defer="sections.roles.sales_marketing.impacts.{{ $i }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.sales_marketing.impacts')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Impact Card
        </button>
    </div>
</div>
@endif

{{-- ==================== TAB: CUSTOMER SERVICE ==================== --}}
@if($activeTab === 'role_cs')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Customer Service Solutions</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Customer Service.</p>
        </div>
        <button type="button" wire:click="resetSection('roles.customer_service')" wire:confirm="Reset Customer Service to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
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

    {{-- Challenges --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges</h3>
            <button type="button" wire:click="addRepeaterItem('roles.customer_service.challenges')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Challenge
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($sections['roles']['customer_service']['challenges'] ?? [] as $i => $ch)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-cs-ch-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.customer_service.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['customer_service']['challenges'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.customer_service.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.customer_service.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <textarea wire:model.defer="sections.roles.customer_service.challenges.{{ $i }}.text" rows="4" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.customer_service.challenges')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Challenge Card
        </button>
    </div>

    {{-- Solutions (4 Items) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">How Kouventa Helps (4 Solutions)</h3>
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

    {{-- Business Impact --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact</h3>
            <button type="button" wire:click="addRepeaterItem('roles.customer_service.impacts')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Impact
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($sections['roles']['customer_service']['impacts'] ?? [] as $i => $imp)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-cs-imp-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.customer_service.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['customer_service']['impacts'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.customer_service.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.customer_service.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <input type="text" wire:model.defer="sections.roles.customer_service.impacts.{{ $i }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold" placeholder="Impact Title">
                    <textarea wire:model.defer="sections.roles.customer_service.impacts.{{ $i }}.desc" rows="3" class="w-full p-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium" placeholder="Impact Description"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.customer_service.impacts')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Impact Card
        </button>
    </div>
</div>
@endif

{{-- ==================== TAB: OPERATIONS ==================== --}}
@if($activeTab === 'role_ops')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Operations Solutions</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, CE solutions, and business impact for Operations.</p>
        </div>
        <button type="button" wire:click="resetSection('roles.operations')" wire:confirm="Reset Operations to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
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

    {{-- Challenges --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges</h3>
            <button type="button" wire:click="addRepeaterItem('roles.operations.challenges')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Challenge
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($sections['roles']['operations']['challenges'] ?? [] as $i => $ch)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-ops-ch-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.operations.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['operations']['challenges'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.operations.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.operations.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <textarea wire:model.defer="sections.roles.operations.challenges.{{ $i }}.text" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.operations.challenges')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Challenge Card
        </button>
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

    {{-- Business Impact --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Business Impact</h3>
            <button type="button" wire:click="addRepeaterItem('roles.operations.impacts')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Impact
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($sections['roles']['operations']['impacts'] ?? [] as $i => $imp)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="role-ops-imp-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('roles.operations.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['roles']['operations']['impacts'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('roles.operations.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('roles.operations.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <input type="text" wire:model.defer="sections.roles.operations.impacts.{{ $i }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold" placeholder="Impact Title">
                    <textarea wire:model.defer="sections.roles.operations.impacts.{{ $i }}.desc" rows="2" class="w-full p-2 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium" placeholder="Impact Description"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('roles.operations.impacts')" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Impact Card
        </button>
    </div>
</div>
@endif

{{-- ==================== TAB: CTA ==================== --}}
@if($activeTab === 'cta')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Bottom Closing CTA</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Edit closing banner heading, copy, and call-to-action buttons.</p>
        </div>
        <button type="button" wire:click="resetSection('cta')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
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

