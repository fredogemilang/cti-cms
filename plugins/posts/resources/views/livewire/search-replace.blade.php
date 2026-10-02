<div x-data="{
    init() {
        window.addEventListener('start-batch-loop', () => {
            setTimeout(() => {
                $wire.processBatch();
            }, 100);
        });
        window.addEventListener('continue-batch-loop', () => {
            setTimeout(() => {
                $wire.processBatch();
            }, 150);
        });
    }
}" class="space-y-6">

    {{-- Header Banner Card --}}
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-[#2563EB]/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#2563EB] text-2xl">find_replace</span>
                </div>
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-[#111827] dark:text-[#FCFCFC]">Search & Replace</h1>
                    <p class="text-sm text-[#6F767E] mt-0.5">Find and replace text, keywords, or URLs across all blog posts and translations.</p>
                </div>
            </div>

            @if($isPreviewing || $isProcessing || $isCompleted)
            <button
                wire:click="resetForm"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 dark:hover:bg-[#333] transition-all self-start sm:self-auto"
            >
                <span class="material-symbols-outlined text-sm">refresh</span>
                New Search
            </button>
            @endif
        </div>
    </div>

    {{-- Error Banner --}}
    @if($errorMessage)
    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 animate-fade-in">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $errorMessage }}</p>
        </div>
    </div>
    @endif

    {{-- Configuration Form Card --}}
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 lg:p-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Search String --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-[#6F767E] mb-2">
                    Search String / Keyword <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                    <input
                        wire:model="searchString"
                        type="text"
                        placeholder="e.g. old-domain.com or brand name"
                        :disabled="$wire.isProcessing"
                        class="w-full h-12 pl-10 pr-4 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-gray-400 disabled:opacity-50"
                    />
                </div>
            </div>

            {{-- Replace String --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-[#6F767E] mb-2">
                    Replace With
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg">swap_horiz</span>
                    <input
                        wire:model="replaceString"
                        type="text"
                        placeholder="e.g. new-domain.com (leave blank to delete)"
                        :disabled="$wire.isProcessing"
                        class="w-full h-12 pl-10 pr-4 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-gray-400 disabled:opacity-50"
                    />
                </div>
            </div>
        </div>

        {{-- Target Fields Selection --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-[#6F767E] mb-3">
                Target Fields to Replace
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#151515] transition-all cursor-pointer">
                    <input type="checkbox" wire:model="targetFields" value="content" :disabled="$wire.isProcessing" class="w-4 h-4 rounded text-[#2563EB] focus:ring-[#2563EB] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1A1A1A]">
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Content Body</span>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#151515] transition-all cursor-pointer">
                    <input type="checkbox" wire:model="targetFields" value="title" :disabled="$wire.isProcessing" class="w-4 h-4 rounded text-[#2563EB] focus:ring-[#2563EB] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1A1A1A]">
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Post Title</span>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#151515] transition-all cursor-pointer">
                    <input type="checkbox" wire:model="targetFields" value="excerpt" :disabled="$wire.isProcessing" class="w-4 h-4 rounded text-[#2563EB] focus:ring-[#2563EB] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1A1A1A]">
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Excerpt</span>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#151515] transition-all cursor-pointer">
                    <input type="checkbox" wire:model="targetFields" value="meta" :disabled="$wire.isProcessing" class="w-4 h-4 rounded text-[#2563EB] focus:ring-[#2563EB] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1A1A1A]">
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Meta / Custom</span>
                </label>
            </div>
        </div>

        {{-- Filters & Options --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-gray-100 dark:border-[#272B30]">
            {{-- Language Scope --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-[#6F767E] mb-1.5">Language Scope</label>
                <select
                    wire:model="selectedLocale"
                    :disabled="$wire.isProcessing"
                    class="w-full h-10 px-3 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB]"
                >
                    <option value="all">All Languages (EN & Translations)</option>
                    <option value="en">Default (English)</option>
                    @foreach($availableLocales as $loc)
                        @if($loc !== 'en')
                            <option value="{{ $loc }}">{{ strtoupper($loc) }} Translation</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Post Status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-[#6F767E] mb-1.5">Post Status</label>
                <select
                    wire:model="statusFilter"
                    :disabled="$wire.isProcessing"
                    class="w-full h-10 px-3 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB]"
                >
                    <option value="all">All Statuses</option>
                    <option value="published">Published Only</option>
                    <option value="draft">Drafts Only</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>

            {{-- Category Filter --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-[#6F767E] mb-1.5">Category</label>
                <select
                    wire:model="categoryFilter"
                    :disabled="$wire.isProcessing"
                    class="w-full h-10 px-3 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB]"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Case Sensitivity --}}
            <div class="flex flex-col justify-end">
                <label class="flex items-center gap-2.5 h-10 px-3 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] cursor-pointer">
                    <input type="checkbox" wire:model="caseSensitive" :disabled="$wire.isProcessing" class="w-4 h-4 rounded text-[#2563EB] focus:ring-[#2563EB] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1A1A1A]">
                    <span class="text-xs font-medium text-[#111827] dark:text-[#FCFCFC]">Case Sensitive</span>
                </label>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100 dark:border-[#272B30]">
            {{-- Dry Run Preview Button --}}
            <button
                wire:click="scanPreview"
                wire:loading.attr="disabled"
                :disabled="$wire.isProcessing"
                type="button"
                class="h-11 px-6 rounded-xl bg-gray-900 hover:bg-black dark:bg-[#272B30] dark:hover:bg-[#333] text-white font-bold text-sm transition-all shadow-sm flex items-center gap-2 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="scanPreview" class="material-symbols-outlined text-lg">preview</span>
                <svg wire:loading wire:target="scanPreview" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Preview Matches (Dry Run)</span>
            </button>

            {{-- Replace Button --}}
            <button
                wire:click="startReplace"
                wire:loading.attr="disabled"
                :disabled="$wire.isProcessing"
                type="button"
                class="h-11 px-6 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-sm transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="startReplace" class="material-symbols-outlined text-lg">bolt</span>
                <svg wire:loading wire:target="startReplace" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Execute Replace</span>
            </button>

            @if($isProcessing)
            <button
                wire:click="cancelProcess"
                type="button"
                class="h-11 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-all flex items-center gap-1.5"
            >
                <span class="material-symbols-outlined text-base">stop</span>
                Cancel
            </button>
            @endif
        </div>
    </div>

    {{-- Live Progress Bar Section --}}
    @if($isProcessing || $isCompleted)
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 lg:p-8 space-y-6 animate-fade-in">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                @if($isProcessing)
                    <div class="h-9 w-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                @else
                    <div class="h-9 w-9 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-500 text-xl">check_circle</span>
                    </div>
                @endif
                <div>
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">
                        {{ $isCompleted ? 'Search & Replace Completed' : 'Replacing in Progress...' }}
                    </h3>
                    <p class="text-xs text-[#6F767E]">{{ $statusMessage }}</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-2xl font-black {{ $isCompleted ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#2563EB]' }}">
                    {{ $progressPercent }}%
                </span>
            </div>
        </div>

        {{-- Progress Bar Bar --}}
        <div class="w-full bg-gray-100 dark:bg-[#0B0B0B] rounded-full h-4 overflow-hidden p-0.5 ring-1 ring-gray-200 dark:ring-[#272B30]">
            <div
                class="h-full rounded-full transition-all duration-300 ease-out {{ $isCompleted ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : 'bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-400 animate-pulse' }}"
                style="width: {{ $progressPercent }}%"
            ></div>
        </div>

        {{-- Realtime Stats Counter --}}
        <div class="grid grid-cols-3 gap-4 pt-2">
            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]">
                <p class="text-xs font-semibold text-gray-500 dark:text-[#6F767E]">Posts Processed</p>
                <p class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mt-1">{{ $processedCount }} / {{ $totalPostsCount }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]">
                <p class="text-xs font-semibold text-gray-500 dark:text-[#6F767E]">Posts Modified</p>
                <p class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mt-1 text-emerald-600 dark:text-emerald-400">{{ $updatedPostsCount }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]">
                <p class="text-xs font-semibold text-gray-500 dark:text-[#6F767E]">Occurrences Replaced</p>
                <p class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mt-1 text-[#2563EB]">{{ $replacedOccurrencesCount }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Dry Run Preview Matches Card --}}
    @if($isPreviewing && !$isProcessing && !$isCompleted)
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 lg:p-8 space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Dry Run Preview</h3>
                <p class="text-xs text-[#6F767E] mt-0.5">{{ $statusMessage }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    {{ $totalMatchedPosts }} Posts Matched
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    {{ $totalMatchedOccurrences }} Occurrences
                </span>
            </div>
        </div>

        @if(!empty($previewResults))
        <div class="space-y-4">
            @foreach($previewResults as $item)
            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-200 dark:bg-[#272B30] text-gray-700 dark:text-gray-300">#{{ $item['id'] }}</span>
                        <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $item['title'] }}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full">
                            {{ $item['match_count'] }} matches
                        </span>
                        <a href="{{ route('admin.posts.edit', $item['id']) }}" target="_blank" class="text-xs text-[#2563EB] hover:underline flex items-center gap-1 font-medium">
                            Edit Post <span class="material-symbols-outlined text-xs">open_in_new</span>
                        </a>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    @foreach($item['fields'] as $f)
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300">
                            {{ $f }}
                        </span>
                    @endforeach
                </div>

                @if(!empty($item['snippets']))
                <div class="p-3 rounded-xl bg-white dark:bg-[#151515] border border-gray-200/60 dark:border-[#272B30] text-xs text-gray-600 dark:text-gray-300 space-y-1.5">
                    @foreach($item['snippets'] as $snippet)
                        <p class="leading-relaxed">{!! $snippet !!}</p>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- Execution Logs Card --}}
    @if(!empty($executionLogs))
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 lg:p-8 space-y-4 animate-fade-in">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Execution Log (Updated Posts)</h3>
            <span class="text-xs text-[#6F767E]">Showing recent {{ count($executionLogs) }} modified posts</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#272B30] text-[#6F767E]">
                        <th class="py-2.5 font-bold">Time</th>
                        <th class="py-2.5 font-bold">Post ID</th>
                        <th class="py-2.5 font-bold">Title</th>
                        <th class="py-2.5 font-bold">Fields Affected</th>
                        <th class="py-2.5 font-bold">Replacements</th>
                        <th class="py-2.5 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30] text-gray-700 dark:text-gray-300">
                    @foreach($executionLogs as $log)
                    <tr>
                        <td class="py-2.5 font-mono text-gray-400">{{ $log['time'] }}</td>
                        <td class="py-2.5 font-mono">#{{ $log['id'] }}</td>
                        <td class="py-2.5 font-semibold text-[#111827] dark:text-[#FCFCFC] max-w-xs truncate">{{ $log['title'] }}</td>
                        <td class="py-2.5">
                            <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 font-medium">
                                {{ implode(', ', $log['fields']) }}
                            </span>
                        </td>
                        <td class="py-2.5 font-bold text-emerald-600 dark:text-emerald-400">{{ $log['count'] }} replaced</td>
                        <td class="py-2.5 text-right">
                            <a href="{{ route('admin.posts.edit', $log['id']) }}" target="_blank" class="text-[#2563EB] hover:underline font-semibold">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
