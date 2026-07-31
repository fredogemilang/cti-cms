<div class="space-y-6">
    <!-- Flash Message -->
    @if (session()->has('message'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-2xl text-sm flex items-center justify-between shadow-sm">
        <span class="font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-emerald-600 dark:text-emerald-400">check_circle</span>
            {{ session('message') }}
        </span>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-200">&times;</button>
    </div>
    @endif

    <!-- Language Progress Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($availableLocales as $loc)
        @php
            $locStat = $stats[$loc] ?? ['total' => 0, 'translated' => 0, 'percentage' => 100];
            $isTarget = ($loc === $targetLocale);
        @endphp
        <div class="bg-white dark:bg-[#1A1A1A] p-5 rounded-2xl border {{ $isTarget ? 'border-[#2563EB] ring-2 ring-[#2563EB]/20' : 'border-gray-200 dark:border-[#272B30]' }} shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-[#111827] dark:text-[#FCFCFC] uppercase text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg text-[#2563EB]">language</span>
                    {{ strtoupper($loc) }}
                </span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $locStat['percentage'] === 100 ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' }}">
                    {{ $locStat['percentage'] }}% Translated
                </span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-[#272B30] rounded-full h-2 mb-2 overflow-hidden">
                <div class="bg-[#2563EB] h-2 rounded-full transition-all duration-500" style="width: {{ $locStat['percentage'] }}%"></div>
            </div>
            <div class="text-xs text-[#6F767E] flex justify-between font-medium">
                <span>{{ $locStat['translated'] }} of {{ $locStat['total'] }} keys</span>
                @if(!$isTarget && $loc !== 'en')
                <button wire:click="$set('targetLocale', '{{ $loc }}')" class="text-[#2563EB] font-bold hover:underline cursor-pointer">Select Target</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Status Filters & Action Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Filter Pills -->
        <div class="inline-flex flex-wrap items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30] gap-1">
            <button
                wire:click="$set('statusFilter', 'all')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'all' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                All Keys
            </button>
            <button
                wire:click="$set('statusFilter', 'missing')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'missing' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <span class="material-symbols-outlined text-base text-amber-500">warning</span>
                Missing
            </button>
            <button
                wire:click="$set('statusFilter', 'completed')"
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'completed' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <span class="material-symbols-outlined text-base text-emerald-500">check_circle</span>
                Completed
            </button>
        </div>

        <!-- Action Button -->
        <div class="flex items-center gap-3">
            <x-admin.ui.button
                wire:click="scanStrings"
                wire:loading.attr="disabled"
                class="h-10 !px-4 !rounded-xl text-sm font-semibold flex items-center gap-2"
            >
                <span wire:loading.remove wire:target="scanStrings" class="material-symbols-outlined text-lg">sync</span>
                <span wire:loading wire:target="scanStrings" class="material-symbols-outlined text-lg animate-spin">refresh</span>
                <span>Scan Website Strings</span>
            </x-admin.ui.button>
        </div>
    </div>

    <!-- Search & Dropdown Filters Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search box -->
            <div class="relative group w-full md:w-[320px]">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors z-10">search</span>
                <x-admin.ui.input
                    name="search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="!pl-12 !py-2.5 !rounded-xl !h-12 text-sm !w-full"
                    placeholder="Search key, default, or translation..." 
                />
            </div>

            <!-- Group Filter Dropdown -->
            <select wire:model.live="selectedGroup" class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer">
                <option value="all">All Category Groups</option>
                @foreach($groups as $g)
                <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                @endforeach
            </select>

            <!-- Source Type Filter Dropdown -->
            <select wire:model.live="selectedSourceType" class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer">
                <option value="all">All Source Types</option>
                <option value="core">Core System</option>
                <option value="theme">Themes</option>
                <option value="plugin">Plugins</option>
            </select>

            @if($search || $selectedGroup !== 'all' || $selectedSourceType !== 'all')
                <x-admin.ui.button
                    wire:click="$set('search', ''); $set('selectedGroup', 'all'); $set('selectedSourceType', 'all')"
                    variant="secondary"
                    class="h-12 !px-4 !rounded-xl text-sm font-semibold flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-lg">close</span>
                    Clear Filters
                </x-admin.ui.button>
            @endif
        </div>
    </div>

    <!-- Data Table (Matching pages-table Component) -->
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="px-6">Canonical Key & Group</x-admin.ui.table-header>
            <x-admin.ui.table-header>Default Value (EN Master)</x-admin.ui.table-header>
            <x-admin.ui.table-header>Translation ({{ strtoupper($targetLocale) }})</x-admin.ui.table-header>
            <x-admin.ui.table-header>Source Location</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-6 w-24">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($translationKeys as $tk)
            @php
                $hasTrans = !empty($editingTranslations[$tk->id] ?? null);
            @endphp
            <x-admin.ui.table-row wire:key="tk-{{ $tk->id }}">
                <x-admin.ui.table-cell class="px-6">
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-[#272B30] text-[#6F767E] uppercase tracking-wider mb-1">
                            {{ $tk->group }}
                        </span>
                        <div class="font-bold text-[#111827] dark:text-[#FCFCFC] font-mono text-xs">
                            {{ $tk->key }}
                        </div>
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] max-w-xs block truncate">
                        {{ $tk->default_value ?: $tk->key }}
                    </span>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <div class="flex items-center gap-2 max-w-sm">
                        <x-admin.ui.input
                            name="trans_{{ $tk->id }}"
                            type="text"
                            wire:model="editingTranslations.{{ $tk->id }}"
                            class="!h-10 text-sm !rounded-xl {{ $hasTrans ? '' : '!border-amber-400 !bg-amber-50/30 dark:!bg-amber-950/20' }}"
                            placeholder="Enter {{ strtoupper($targetLocale) }} translation..."
                        />
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    @if($tk->sources->count() > 0)
                        @php $firstSource = $tk->sources->first(); @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono bg-gray-100 dark:bg-[#272B30] text-[#6F767E] max-w-[200px] truncate">
                            @if($firstSource->source_type === 'theme')
                                <span class="material-symbols-outlined text-xs text-purple-500">palette</span>
                            @elseif($firstSource->source_type === 'plugin')
                                <span class="material-symbols-outlined text-xs text-blue-500">extension</span>
                            @else
                                <span class="material-symbols-outlined text-xs text-gray-500">settings</span>
                            @endif
                            {{ $firstSource->source_file }}
                        </span>
                    @else
                        <span class="text-xs text-[#6F767E]">Manual / Core</span>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    <x-admin.ui.button
                        wire:click="saveTranslation({{ $tk->id }}, editingTranslations[{{ $tk->id }}])"
                        class="!h-9 !px-3.5 !rounded-xl text-xs font-bold"
                    >
                        Save
                    </x-admin.ui.button>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell colspan="5" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">translate</span>
                        <p class="text-[#6F767E] font-medium text-sm">No translation keys found matching current filter.</p>
                        <p class="text-xs text-[#6F767E]">Try clicking "Scan Website Strings" to discover frasa from views.</p>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @endforelse
    </x-admin.ui.table>

    @if($translationKeys->hasPages())
    <div class="pt-4">
        {{ $translationKeys->links() }}
    </div>
    @endif
</div>
