{{-- =========================================================================
     SOLUTION AI SCHEMA TEMPLATE
     Tabs:
     - hero: Hero Section
     - dept_cs: Customer Service
     - dept_recruitment: Recruitment
     - dept_sales: Sales & Marketing
     - dept_ops: Business Operations
     - dept_finance: Finance (Payment Prioritization & Bank Reconciliation)
     - dept_legal: Legal & Compliance
     - journey: Implementation Journey (4 Steps)
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
            <p class="text-[11px] text-[#6F767E]">Triggers smooth scroll directly to department use case tabs.</p>
        </div>
    </div>
</div>
@endif

{{-- ==================== HELPER MACRO FOR DEPARTMENTS ==================== --}}
@php
    $deptKeys = [
        'dept_cs' => 'cs',
        'dept_recruitment' => 'recruitment',
        'dept_sales' => 'sales_marketing',
        'dept_ops' => 'operations',
        'dept_legal' => 'legal_compliance',
    ];
@endphp

@if(isset($deptKeys[$activeTab]))
@php $dKey = $deptKeys[$activeTab]; @endphp
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $sections['departments'][$dKey]['name'] ?? 'Department' }} Use Case</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure challenges, AI agent solutions, and business impact for this team.</p>
        </div>
        <button type="button" wire:click="resetSection('departments.{{ $dKey }}')" wire:confirm="Reset this department to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">restart_alt</span>
            Reset Section
        </button>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Department Tab Label</label>
        <input type="text" wire:model.defer="sections.departments.{{ $dKey }}.name" class="w-full max-w-sm h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
    </div>

    {{-- Challenges --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Challenges</h3>
            <button type="button" wire:click="addRepeaterItem('departments.{{ $dKey }}.challenges', {{ json_encode(['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Challenge
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($sections['departments'][$dKey]['challenges'] ?? [] as $i => $ch)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="dept-{{ $dKey }}-ch-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-red-500 uppercase">Challenge {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('departments.{{ $dKey }}.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['departments'][$dKey]['challenges'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('departments.{{ $dKey }}.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('departments.{{ $dKey }}.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <textarea wire:model.defer="sections.departments.{{ $dKey }}.challenges.{{ $i }}.text" rows="4" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('departments.{{ $dKey }}.challenges', {{ json_encode(['text' => '']) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Challenge Card
        </button>
    </div>

    {{-- Solution Agents --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">How Kouventa AI Helps (Agent Buttons)</h3>
        <div class="space-y-3">
            @foreach($sections['departments'][$dKey]['solutions'] ?? [] as $sIdx => $sol)
                <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3">
                    <span class="text-[11px] font-bold text-green-600 uppercase">AI Agent #{{ $sIdx + 1 }}</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Agent Name</label>
                            <input type="text" wire:model.defer="sections.departments.{{ $dKey }}.solutions.{{ $sIdx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-[#6F767E] mb-1">Agent Description</label>
                            <textarea wire:model.defer="sections.departments.{{ $dKey }}.solutions.{{ $sIdx }}.desc" rows="2" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
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
            <button type="button" wire:click="addRepeaterItem('departments.{{ $dKey }}.impacts', {{ json_encode($dKey === 'sales_marketing' ? ['text' => '', 'desc' => ''] : ['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-xs">add_circle</span>
                Add Impact
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($sections['departments'][$dKey]['impacts'] ?? [] as $i => $imp)
                <div class="p-3.5 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2" wire:key="dept-{{ $dKey }}-imp-{{ $i }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-500 uppercase">Impact {{ $i + 1 }}</span>
                        <div class="flex items-center gap-1">
                            @if($i > 0)
                            <button type="button" wire:click="moveRepeaterItem('departments.{{ $dKey }}.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                            </button>
                            @endif
                            @if($i < count($sections['departments'][$dKey]['impacts'] ?? []) - 1)
                            <button type="button" wire:click="moveRepeaterItem('departments.{{ $dKey }}.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                                <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                            </button>
                            @endif
                            <button type="button" wire:click="removeRepeaterItem('departments.{{ $dKey }}.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                            </button>
                        </div>
                    </div>
                    <textarea wire:model.defer="sections.departments.{{ $dKey }}.impacts.{{ $i }}.text" rows="{{ $dKey === 'sales_marketing' ? 2 : 3 }}" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium" placeholder="Impact Headline"></textarea>
                    @if($dKey === 'sales_marketing')
                        <textarea wire:model.defer="sections.departments.sales_marketing.impacts.{{ $i }}.desc" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium" placeholder="Impact Subtitle / Description"></textarea>
                    @endif
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addRepeaterItem('departments.{{ $dKey }}.impacts', {{ json_encode($dKey === 'sales_marketing' ? ['text' => '', 'desc' => ''] : ['text' => '']) }})" class="w-full py-2 rounded-lg border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-xs">add</span>
            Add Impact Card
        </button>
    </div>
</div>
@endif

{{-- ==================== TAB: FINANCE (2 SUB-CASES) ==================== --}}
@if($activeTab === 'dept_finance')
<div class="space-y-8">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Finance Solutions (2 Use Cases)</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure Payment Prioritization and Bank Reconciliation use cases.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="resetSection('departments.payment_prioritization')" wire:confirm="Reset Payment Prioritization to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Payment
            </button>
            <button type="button" wire:click="resetSection('departments.bank_reconciliation')" wire:confirm="Reset Bank Reconciliation to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Reconciliation
            </button>
        </div>
    </div>

    {{-- Case 1: Payment Prioritization --}}
    <div class="p-5 bg-white dark:bg-[#202428] rounded-2xl border border-gray-200 dark:border-[#272B30] space-y-5">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
            <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">1. Payment Prioritization</h3>
            <input type="text" wire:model.defer="sections.departments.payment_prioritization.name" class="w-64 h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs font-medium" placeholder="Sub-tab Label">
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6F767E] uppercase">Challenges</span>
                <button type="button" wire:click="addRepeaterItem('departments.payment_prioritization.challenges', {{ json_encode(['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Challenge
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($sections['departments']['payment_prioritization']['challenges'] ?? [] as $i => $ch)
                    <div class="p-2.5 rounded-lg border border-gray-100 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] space-y-1.5" wire:key="pp-ch-{{ $i }}">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-red-500 uppercase">#{{ $i + 1 }}</span>
                            <div class="flex items-center gap-0.5">
                                @if($i > 0)
                                <button type="button" wire:click="moveRepeaterItem('departments.payment_prioritization.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Up">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                                </button>
                                @endif
                                @if($i < count($sections['departments']['payment_prioritization']['challenges'] ?? []) - 1)
                                <button type="button" wire:click="moveRepeaterItem('departments.payment_prioritization.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Down">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                                </button>
                                @endif
                                <button type="button" wire:click="removeRepeaterItem('departments.payment_prioritization.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600" title="Remove">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        </div>
                        <textarea wire:model.defer="sections.departments.payment_prioritization.challenges.{{ $i }}.text" rows="3" class="w-full p-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-3">
            <span class="text-xs font-bold text-[#6F767E] uppercase">AI Solution Agent & Feature Points</span>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-[#6F767E] mb-1">Agent Title</label>
                    <input type="text" wire:model.defer="sections.departments.payment_prioritization.solutions.0.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] text-xs font-medium">
                </div>
                <div class="md:col-span-3 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-[#6F767E]">Agent Feature Bullets</label>
                        <button type="button" wire:click="addRepeaterItem('departments.payment_prioritization.solutions.0.bullets', '')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                            <span class="material-symbols-outlined text-xs">add_circle</span>
                            Add Bullet
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($sections['departments']['payment_prioritization']['solutions'][0]['bullets'] ?? [] as $b => $bText)
                            <div class="flex items-center gap-1.5" wire:key="pp-bullet-{{ $b }}">
                                <input type="text" wire:model.defer="sections.departments.payment_prioritization.solutions.0.bullets.{{ $b }}" class="flex-grow h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] text-xs font-medium" placeholder="Bullet {{ $b + 1 }}">
                                <button type="button" wire:click="removeRepeaterItem('departments.payment_prioritization.solutions.0.bullets', {{ $b }})" class="p-1 rounded text-red-500 hover:text-red-600" title="Remove">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6F767E] uppercase">Business Impact</span>
                <button type="button" wire:click="addRepeaterItem('departments.payment_prioritization.impacts', {{ json_encode(['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Impact
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($sections['departments']['payment_prioritization']['impacts'] ?? [] as $i => $imp)
                    <div class="p-2.5 rounded-lg border border-gray-100 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] space-y-1.5" wire:key="pp-imp-{{ $i }}">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-blue-500 uppercase">#{{ $i + 1 }}</span>
                            <div class="flex items-center gap-0.5">
                                @if($i > 0)
                                <button type="button" wire:click="moveRepeaterItem('departments.payment_prioritization.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Up">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                                </button>
                                @endif
                                @if($i < count($sections['departments']['payment_prioritization']['impacts'] ?? []) - 1)
                                <button type="button" wire:click="moveRepeaterItem('departments.payment_prioritization.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Down">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                                </button>
                                @endif
                                <button type="button" wire:click="removeRepeaterItem('departments.payment_prioritization.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600" title="Remove">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        </div>
                        <textarea wire:model.defer="sections.departments.payment_prioritization.impacts.{{ $i }}.text" rows="3" class="w-full p-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Case 2: Bank Reconciliation --}}
    <div class="p-5 bg-white dark:bg-[#202428] rounded-2xl border border-gray-200 dark:border-[#272B30] space-y-5">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
            <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">2. Bank Reconciliation</h3>
            <input type="text" wire:model.defer="sections.departments.bank_reconciliation.name" class="w-64 h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs font-medium" placeholder="Sub-tab Label">
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6F767E] uppercase">Challenges</span>
                <button type="button" wire:click="addRepeaterItem('departments.bank_reconciliation.challenges', {{ json_encode(['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Challenge
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($sections['departments']['bank_reconciliation']['challenges'] ?? [] as $i => $ch)
                    <div class="p-2.5 rounded-lg border border-gray-100 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] space-y-1.5" wire:key="br-ch-{{ $i }}">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-red-500 uppercase">#{{ $i + 1 }}</span>
                            <div class="flex items-center gap-0.5">
                                @if($i > 0)
                                <button type="button" wire:click="moveRepeaterItem('departments.bank_reconciliation.challenges', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Up">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                                </button>
                                @endif
                                @if($i < count($sections['departments']['bank_reconciliation']['challenges'] ?? []) - 1)
                                <button type="button" wire:click="moveRepeaterItem('departments.bank_reconciliation.challenges', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Down">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                                </button>
                                @endif
                                <button type="button" wire:click="removeRepeaterItem('departments.bank_reconciliation.challenges', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600" title="Remove">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        </div>
                        <textarea wire:model.defer="sections.departments.bank_reconciliation.challenges.{{ $i }}.text" rows="3" class="w-full p-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-3">
            <span class="text-xs font-bold text-[#6F767E] uppercase">AI Solution Agents</span>
            <div class="space-y-3">
                @foreach($sections['departments']['bank_reconciliation']['solutions'] ?? [] as $sIdx => $sol)
                    <div class="p-4 bg-gray-50 dark:bg-[#1A1A1A] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-2">
                        <span class="text-[11px] font-bold text-green-600 uppercase">AI Agent #{{ $sIdx + 1 }}</span>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Agent Title</label>
                                <input type="text" wire:model.defer="sections.departments.bank_reconciliation.solutions.{{ $sIdx }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-[#6F767E] mb-1">Agent Description</label>
                                <textarea wire:model.defer="sections.departments.bank_reconciliation.solutions.{{ $sIdx }}.desc" rows="2" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium"></textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6F767E] uppercase">Business Impact</span>
                <button type="button" wire:click="addRepeaterItem('departments.bank_reconciliation.impacts', {{ json_encode(['text' => '']) }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2BD46A] hover:underline">
                    <span class="material-symbols-outlined text-xs">add_circle</span>
                    Add Impact
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($sections['departments']['bank_reconciliation']['impacts'] ?? [] as $i => $imp)
                    <div class="p-2.5 rounded-lg border border-gray-100 dark:border-[#272B30] bg-gray-50 dark:bg-[#1A1A1A] space-y-1.5" wire:key="br-imp-{{ $i }}">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-blue-500 uppercase">#{{ $i + 1 }}</span>
                            <div class="flex items-center gap-0.5">
                                @if($i > 0)
                                <button type="button" wire:click="moveRepeaterItem('departments.bank_reconciliation.impacts', {{ $i }}, 'up')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Up">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_upward</span>
                                </button>
                                @endif
                                @if($i < count($sections['departments']['bank_reconciliation']['impacts'] ?? []) - 1)
                                <button type="button" wire:click="moveRepeaterItem('departments.bank_reconciliation.impacts', {{ $i }}, 'down')" class="p-0.5 rounded text-[#6F767E] hover:text-white" title="Move Down">
                                    <span class="material-symbols-outlined text-xs leading-none">arrow_downward</span>
                                </button>
                                @endif
                                <button type="button" wire:click="removeRepeaterItem('departments.bank_reconciliation.impacts', {{ $i }})" class="p-0.5 rounded text-red-500 hover:text-red-600" title="Remove">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        </div>
                        <textarea wire:model.defer="sections.departments.bank_reconciliation.impacts.{{ $i }}.text" rows="3" class="w-full p-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#202428] text-xs font-medium"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- ==================== TAB: JOURNEY ==================== --}}
@if($activeTab === 'journey')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
        <div>
            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Implementation Journey Steps</h2>
            <p class="text-xs text-[#6F767E] mt-0.5">Configure implementation roadmap title and step details.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="addRepeaterItem('journey.steps', {{ json_encode(['title' => '', 'desc' => '']) }})" class="inline-flex items-center gap-1 text-xs font-bold text-[#2BD46A] hover:underline">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Add Step
            </button>
            <button type="button" wire:click="resetSection('journey')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title</label>
        <input type="text" wire:model.defer="sections.journey.title" class="w-full max-w-md h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($sections['journey']['steps'] ?? [] as $i => $step)
            <div class="p-4 bg-gray-50 dark:bg-[#202428] rounded-xl border border-gray-100 dark:border-[#272B30] space-y-3" wire:key="ai-journey-step-{{ $i }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Step #{{ $i + 1 }}</span>
                    <div class="flex items-center gap-1">
                        @if($i > 0)
                        <button type="button" wire:click="moveRepeaterItem('journey.steps', {{ $i }}, 'up')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Up">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_upward</span>
                        </button>
                        @endif
                        @if($i < count($sections['journey']['steps'] ?? []) - 1)
                        <button type="button" wire:click="moveRepeaterItem('journey.steps', {{ $i }}, 'down')" class="p-1 rounded text-[#6F767E] hover:text-[#111827] dark:hover:text-white" title="Move Down">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_downward</span>
                        </button>
                        @endif
                        <button type="button" wire:click="removeRepeaterItem('journey.steps', {{ $i }})" class="p-1 rounded text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Remove">
                            <span class="material-symbols-outlined text-sm leading-none">delete</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#6F767E] mb-1">Step Name</label>
                    <input type="text" wire:model.defer="sections.journey.steps.{{ $i }}.title" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#6F767E] mb-1">Step Description</label>
                    <textarea wire:model.defer="sections.journey.steps.{{ $i }}.desc" rows="3" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" wire:click="addRepeaterItem('journey.steps', {{ json_encode(['title' => '', 'desc' => '']) }})" class="w-full py-2.5 rounded-xl border border-dashed border-gray-300 dark:border-[#33383F] text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:border-gray-400 flex items-center justify-center gap-1.5">
        <span class="material-symbols-outlined text-sm">add</span>
        Add Journey Step
    </button>
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
