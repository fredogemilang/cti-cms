<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-zinc-200">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 flex items-center gap-2">
                <span>🌐</span> Centralized String Translation Registry
            </h1>
            <p class="text-sm text-zinc-500 mt-1">
                Manage UI string keys, buttons, and localized translations across Themes, Plugins, and Core.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="scanStrings" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-medium rounded-xl text-sm transition-all shadow-sm">
                <svg wire:loading.remove wire:target="scanStrings" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <svg wire:loading wire:target="scanStrings" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span>Scan Website Strings</span>
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
        <span>{{ session('message') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
    </div>
    @endif

    <!-- Language Progress Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($availableLocales as $loc)
        @php
            $locStat = $stats[$loc] ?? ['total' => 0, 'translated' => 0, 'percentage' => 100];
            $isTarget = ($loc === $targetLocale);
        @endphp
        <div class="bg-white p-5 rounded-2xl border {{ $isTarget ? 'border-primary ring-2 ring-primary/20' : 'border-zinc-200' }} shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-zinc-900 uppercase text-sm flex items-center gap-2">
                    @if($loc === 'en') 🇬🇧 @elseif($loc === 'id') 🇮🇩 @else 🌐 @endif
                    {{ strtoupper($loc) }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $locStat['percentage'] === 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $locStat['percentage'] }}% Translated
                </span>
            </div>
            <div class="w-full bg-zinc-100 rounded-full h-2 mb-2 overflow-hidden">
                <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: {{ $locStat['percentage'] }}%"></div>
            </div>
            <div class="text-xs text-zinc-500 flex justify-between">
                <span>{{ $locStat['translated'] }} of {{ $locStat['total'] }} keys</span>
                @if(!$isTarget && $loc !== 'en')
                <button wire:click="$set('targetLocale', '{{ $loc }}')" class="text-primary font-medium hover:underline">Select Target</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 uppercase mb-1.5">Search Key / String</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search key, default, translation..." class="w-full text-sm border border-zinc-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-primary focus:border-primary">
            </div>

            <!-- Group Filter -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 uppercase mb-1.5">Category Group</label>
                <select wire:model.live="selectedGroup" class="w-full text-sm border border-zinc-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-primary">
                    <option value="all">All Groups</option>
                    @foreach($groups as $g)
                    <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Source Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 uppercase mb-1.5">Source Type</label>
                <select wire:model.live="selectedSourceType" class="w-full text-sm border border-zinc-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-primary">
                    <option value="all">All Sources</option>
                    <option value="core">Core System</option>
                    <option value="theme">Themes</option>
                    <option value="plugin">Plugins</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 uppercase mb-1.5">Translation Status ({{ strtoupper($targetLocale) }})</label>
                <select wire:model.live="statusFilter" class="w-full text-sm border border-zinc-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-primary">
                    <option value="all">All Keys</option>
                    <option value="missing">⚠️ Missing Translation</option>
                    <option value="completed">✅ Completed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-xs uppercase font-semibold text-zinc-600">
                    <tr>
                        <th class="px-6 py-4">Canonical Key & Group</th>
                        <th class="px-6 py-4">Default Value (EN Master)</th>
                        <th class="px-6 py-4">Translation ({{ strtoupper($targetLocale) }})</th>
                        <th class="px-6 py-4">Source Location</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($translationKeys as $tk)
                    @php
                        $hasTrans = !empty($editingTranslations[$tk->id] ?? null);
                    @endphp
                    <tr class="hover:bg-zinc-50/80 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-zinc-800">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-600 font-semibold mb-1 text-[10px] uppercase">{{ $tk->group }}</span>
                            <div class="font-semibold text-zinc-900">{{ $tk->key }}</div>
                        </td>
                        <td class="px-6 py-4 text-zinc-700 max-w-xs truncate">
                            {{ $tk->default_value ?: $tk->key }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                    wire:model="editingTranslations.{{ $tk->id }}"
                                    placeholder="Enter {{ strtoupper($targetLocale) }} translation..." 
                                    class="w-full text-sm border {{ $hasTrans ? 'border-zinc-300' : 'border-amber-300 bg-amber-50/50' }} rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-primary focus:bg-white">
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-500 max-w-xs truncate">
                            @if($tk->sources->count() > 0)
                                @php $firstSource = $tk->sources->first(); @endphp
                                <span class="inline-flex items-center gap-1 font-mono text-[11px]">
                                    @if($firstSource->source_type === 'theme') 🎨 @elseif($firstSource->source_type === 'plugin') 🧩 @else ⚙️ @endif
                                    {{ $firstSource->source_file }}
                                </span>
                            @else
                                <span class="text-zinc-400">Manual / Core</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="saveTranslation({{ $tk->id }}, editingTranslations[{{ $tk->id }}])" class="px-3 py-1.5 bg-primary hover:bg-red-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm">
                                Save
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-zinc-500 text-sm">
                            No translation keys found matching current filter. Try clicking "Scan Website Strings".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($translationKeys->hasPages())
        <div class="p-4 border-t border-zinc-200 bg-zinc-50">
            {{ $translationKeys->links() }}
        </div>
        @endif
    </div>
</div>
