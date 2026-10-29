{{-- Schema Form Fields for Product: AI --}}

{{-- 1. HERO SECTION --}}
@if($activeTab === 'hero')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">1. Hero & Navigation</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit main title, subtitle, CTA buttons, and subnav links.</p>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary Button (Green)</span>
                    <input type="text" placeholder="Button Text" wire:model.defer="sections.hero.primary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    <input type="text" placeholder="Button URL (e.g. /book-demo)" wire:model.defer="sections.hero.primary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
                </div>

                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Button (Outline)</span>
                    <input type="text" placeholder="Button Text" wire:model.defer="sections.hero.secondary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    <input type="text" placeholder="Target Anchor (e.g. #ai-products)" wire:model.defer="sections.hero.secondary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
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
                <p class="text-xs text-[#6F767E] mt-0.5">Edit heading and the 3 pain points addressed by Kouventa AI.</p>
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
                        <textarea rows="2" wire:model.defer="sections.challenges.cards.{{ $idx }}.desc" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 3. ENTERPRISE AI JOURNEY --}}
@if($activeTab === 'journey')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">3. Enterprise AI Journey (3 Stages)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit heading, subtitle, and the 3 stages: Answer, Analyze, and Execute.</p>
            </div>
            <button type="button" wire:click="resetSection('journey')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="space-y-4">
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
                <textarea rows="2" wire:model.defer="sections.journey.subtitle" class="w-full p-3 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            @foreach($sections['journey']['stages'] ?? [] as $sIdx => $stage)
                <div class="p-5 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-[#2563EB] text-xs font-bold flex items-center justify-center">{{ $stage['number'] ?? '0' . ($sIdx + 1) }}</span>
                        <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Stage {{ $stage['number'] ?? '0' . ($sIdx + 1) }}: {{ $stage['name'] ?? '' }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Stage Tab Name</label>
                            <input type="text" wire:model.defer="sections.journey.stages.{{ $sIdx }}.name" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Stage CTA Button Text</label>
                            <input type="text" wire:model.defer="sections.journey.stages.{{ $sIdx }}.cta.text" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Headline</label>
                        <input type="text" wire:model.defer="sections.journey.stages.{{ $sIdx }}.headline" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Description</label>
                        <textarea rows="2" wire:model.defer="sections.journey.stages.{{ $sIdx }}.sub" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 4. OUR PRODUCTS --}}
@if($activeTab === 'products')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">4. Our Products (4 Flagships)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Customize AI Company Wikipedia, AI Task Force, Chat Playground, and TaskO.</p>
            </div>
            <button type="button" wire:click="resetSection('products')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title</label>
            <input type="text" wire:model.defer="sections.products.title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        @php
            $prodKeys = ['wiki' => '1. AI Company Wikipedia', 'taskforce' => '2. AI Task Force', 'playground' => '3. Chat Playground', 'workflow' => '4. TaskO'];
        @endphp

        <div class="space-y-6 pt-2">
            @foreach($prodKeys as $pk => $prodLabel)
                <div class="p-5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] space-y-4 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-2">
                        <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $prodLabel }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Product Tab Name</label>
                            <input type="text" wire:model.defer="sections.products.items.{{ $pk }}.name" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Role / Sub-tag</label>
                            <input type="text" wire:model.defer="sections.products.items.{{ $pk }}.tag" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#6F767E] mb-1">Headline / Description Paragraph</label>
                        <textarea rows="3" wire:model.defer="sections.products.items.{{ $pk }}.headline" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-transparent text-xs font-medium"></textarea>
                    </div>

                    {{-- Features List --}}
                    <div class="space-y-2 pt-2">
                        <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Key Feature Bullets</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($sections['products']['items'][$pk]['features'] ?? [] as $fIdx => $feat)
                                <div class="p-3 rounded-lg border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-2">
                                    <input type="text" placeholder="Feature Title" wire:model.defer="sections.products.items.{{ $pk }}.features.{{ $fIdx }}.title" class="w-full h-8 px-2.5 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold">
                                    <textarea rows="2" placeholder="Feature Description" wire:model.defer="sections.products.items.{{ $pk }}.features.{{ $fIdx }}.desc" class="w-full p-2 rounded border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-[11px] font-medium"></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- 5. PLATFORM CONTROL & GOVERNANCE --}}
@if($activeTab === 'governance')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">5. Platform Control & Governance (6 Cards)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit title and the 6 security and governance cards.</p>
            </div>
            <button type="button" wire:click="resetSection('governance')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Section Title</label>
                <input type="text" wire:model.defer="sections.governance.title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @foreach($sections['governance']['cards'] ?? [] as $gIdx => $gCard)
                    <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-2">
                        <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Card {{ $gIdx + 1 }}</span>
                        <input type="text" placeholder="Card Title" wire:model.defer="sections.governance.cards.{{ $gIdx }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold">
                        <textarea rows="2" placeholder="Description" wire:model.defer="sections.governance.cards.{{ $gIdx }}.desc" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- 6. WHY KOUVENTA --}}
@if($activeTab === 'why')
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">6. Why Kouventa (4 Cards)</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit heading and the 4 reasons to choose Kouventa AI.</p>
            </div>
            <button type="button" wire:click="resetSection('why')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Title Prefix</label>
                <input type="text" wire:model.defer="sections.why.title.prefix" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Title Highlight (Green)</label>
                <input type="text" wire:model.defer="sections.why.title.main" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="space-y-4 pt-2">
            @foreach($sections['why']['cards'] ?? [] as $wIdx => $wCard)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-2">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Card {{ $wIdx + 1 }}</span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <input type="text" placeholder="Title" wire:model.defer="sections.why.cards.{{ $wIdx }}.title" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-bold">
                        <input type="text" placeholder="Badge (e.g. Solves: ...)" wire:model.defer="sections.why.cards.{{ $wIdx }}.badge" class="w-full h-9 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    </div>
                    <textarea rows="2" placeholder="Description" wire:model.defer="sections.why.cards.{{ $wIdx }}.desc" class="w-full p-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium"></textarea>
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
                <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">7. Bottom CTA Banner</h2>
                <p class="text-xs text-[#6F767E] mt-0.5">Edit closing call to action title and buttons.</p>
            </div>
            <button type="button" wire:click="resetSection('cta')" wire:confirm="Reset this section to original defaults?" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Reset Section
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">CTA Title</label>
                <input type="text" wire:model.defer="sections.cta.title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Primary Button (Dark Navy)</span>
                    <input type="text" placeholder="Button Text" wire:model.defer="sections.cta.primary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    <input type="text" placeholder="Button URL (e.g. /book-demo)" wire:model.defer="sections.cta.primary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
                </div>

                <div class="p-4 rounded-xl border border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 space-y-3">
                    <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Secondary Button (Outline)</span>
                    <input type="text" placeholder="Button Text" wire:model.defer="sections.cta.secondary_cta.text" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium">
                    <input type="text" placeholder="Button URL / Anchor (e.g. /pricing?tab=ai)" wire:model.defer="sections.cta.secondary_cta.url" class="w-full h-10 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-xs font-medium font-mono">
                </div>
            </div>
        </div>
    </div>
@endif
