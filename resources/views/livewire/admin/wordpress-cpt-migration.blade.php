<div>
    {{-- Step 1: Input URL --}}
    @if($step === 1)
    <div class="space-y-6">
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-2xl bg-[#8B5CF6]/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#8B5CF6] text-2xl">widgets</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Import WordPress CPT</h2>
                    <p class="text-sm text-[#6F767E]">Import Custom Post Types from WordPress</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#6F767E] mb-2">WordPress Site URL</label>
                    <div class="flex gap-3">
                        <input
                            wire:model="wpUrl"
                            type="url"
                            placeholder="https://yoursite.com"
                            class="flex-1 h-12 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] px-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6] transition-all placeholder:text-[#6F767E]"
                        />
                        <button
                            wire:click="fetchCptTypes"
                            wire:loading.attr="disabled"
                            class="h-12 px-6 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 disabled:opacity-50 flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="fetchCptTypes" class="material-symbols-outlined text-xl">search</span>
                            <svg wire:loading wire:target="fetchCptTypes" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="fetchCptTypes">Discover CPTs</span>
                            <span wire:loading wire:target="fetchCptTypes">Loading...</span>
                        </button>
                    </div>
                </div>

                @if($errorMessage)
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-red-500">error</span>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                </div>
                @endif

                <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-purple-500 mt-0.5">info</span>
                        <div class="text-sm text-purple-600 dark:text-purple-400">
                            <p class="font-medium mb-1">Custom Post Type Migration:</p>
                            <ul class="list-disc list-inside space-y-1 text-purple-500">
                                <li>Discover available CPTs from WordPress</li>
                                <li>Map WordPress fields to CMS fields</li>
                                <li>Support for ACF and custom meta fields</li>
                                <li>Images downloaded to Media Library</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 2: Select WordPress CPT --}}
    @if($step === 2)
    <div class="space-y-6">
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-2xl bg-[#83BF6E]/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#83BF6E] text-2xl">check_circle</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Select Post Type</h2>
                    <p class="text-sm text-[#6F767E]">Choose a WordPress post type to import</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#6F767E] mb-2">WordPress Post Type</label>
                    <select
                        wire:model.live="selectedWpCpt"
                        class="w-full h-12 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] px-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6]"
                    >
                        <option value="">-- Select Post Type --</option>
                        @foreach($availableCpts as $cpt)
                        <option value="{{ $cpt['slug'] }}">{{ $cpt['name'] }} ({{ $cpt['slug'] }})</option>
                        @endforeach
                    </select>
                </div>

                @if($errorMessage)
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-red-500">error</span>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                </div>
                @endif

                {{-- Available CPTs List --}}
                <div class="border-t border-gray-100 dark:border-[#272B30] pt-4">
                    <h4 class="text-sm font-bold text-[#6F767E] mb-3">Available Post Types:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($availableCpts as $cpt)
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] {{ $selectedWpCpt === $cpt['slug'] ? 'ring-2 ring-[#8B5CF6]' : '' }}">
                            <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">{{ $cpt['name'] }}</p>
                            <p class="text-xs text-[#6F767E]">{{ $cpt['slug'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Taxonomy Import Options Card --}}
                <div class="border-t border-gray-100 dark:border-[#272B30] pt-4 space-y-3">
                    <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-500 text-lg">schema</span>
                        Taxonomy Import Options
                    </h4>
                    <p class="text-xs text-[#6F767E]">Choose which taxonomies should be auto-created and linked to your imported posts.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] cursor-pointer hover:bg-gray-100 dark:hover:bg-[#151515] transition-all">
                            <input type="checkbox" wire:model.live="importCategories" class="w-4 h-4 rounded text-[#8B5CF6] focus:ring-[#8B5CF6] border-gray-300">
                            <div>
                                <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-amber-500 text-base">folder</span>
                                    Import Categories
                                </p>
                                <p class="text-xs text-[#6F767E]">Fetch WP categories and link them to imported posts</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] cursor-pointer hover:bg-gray-100 dark:hover:bg-[#151515] transition-all">
                            <input type="checkbox" wire:model.live="importTags" class="w-4 h-4 rounded text-[#8B5CF6] focus:ring-[#8B5CF6] border-gray-300">
                            <div>
                                <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-blue-500 text-base">label</span>
                                    Import Tags
                                </p>
                                <p class="text-xs text-[#6F767E]">Fetch WP tags and associate them with imported posts</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between">
            <button
                wire:click="goBack"
                class="h-12 px-6 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-bold text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Back
            </button>
            <button
                wire:click="selectWpCpt"
                wire:loading.attr="disabled"
                class="h-12 px-8 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 disabled:opacity-50 flex items-center gap-2"
            >
                <span wire:loading.remove wire:target="selectWpCpt" class="material-symbols-outlined text-xl">arrow_forward</span>
                <svg wire:loading wire:target="selectWpCpt" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Continue
            </button>
        </div>
    </div>
    @endif

    {{-- Step 3: Preview & Select Posts --}}
    @if($step === 3)
    <div class="space-y-6">
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-500 text-2xl">preview</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Preview & Select Posts</h2>
                        <p class="text-sm text-[#6F767E]">{{ count($previewPosts) }} posts found · {{ $totalPosts }} total in WordPress</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 text-sm font-medium">
                        {{ count($selectedPostIds) }} selected
                    </span>
                    <button wire:click="continueToFieldMapping"
                        class="h-11 px-6 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 flex items-center gap-2">
                        Continue to Mapping
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Language Filter (Polylang only) --}}
            @if($isPolylang)
            <div class="mb-4 p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                <p class="text-sm font-bold text-blue-700 dark:text-blue-300 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">translate</span>
                    Filter by Language
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($polylangLanguages as $lang => $count)
                    <button wire:click="toggleLanguage('{{ $lang }}')"
                        class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                            {{ in_array($lang, $selectedLanguages) ? 'bg-blue-500 text-white shadow-md' : 'bg-white dark:bg-[#0B0B0B] text-[#6F767E] border border-gray-200 dark:border-[#272B30]' }}">
                        {{ strtoupper($lang) }}
                        <span class="text-xs opacity-70">({{ $count }})</span>
                    </button>
                    @endforeach
                </div>
                <p class="text-xs text-[#6F767E] mt-2">
                    Importing <strong>{{ strtoupper($defaultImportLocale) }}</strong> as primary, other languages as translations
                </p>
            </div>
            @endif

            {{-- Select All Toggle --}}
            <div class="mb-4 flex items-center gap-3">
                <button wire:click="toggleAllPosts"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all
                        {{ $selectAllPosts ? 'bg-[#8B5CF6] text-white' : 'bg-gray-100 dark:bg-[#0B0B0B] text-[#6F767E] border border-gray-200 dark:border-[#272B30]' }}">
                    {{ $selectAllPosts ? '✓ All Selected' : 'Select All' }}
                </button>
                <span class="text-xs text-[#6F767E]">Click individual posts to toggle</span>
            </div>

            {{-- Post List --}}
            <div class="max-h-[500px] overflow-y-auto rounded-2xl border border-gray-100 dark:border-[#272B30] divide-y divide-gray-100 dark:divide-[#272B30]">
                @if($fetchAllDone)
                    @foreach($previewPosts as $post)
                    <div wire:click="togglePost({{ $post['id'] }})"
                        class="flex items-center gap-4 px-5 py-3 cursor-pointer transition-all
                            {{ in_array($post['id'], $selectedPostIds) ? 'bg-purple-50/50 dark:bg-purple-900/10 hover:bg-purple-100 dark:hover:bg-purple-900/20' : 'hover:bg-gray-50 dark:hover:bg-[#0B0B0B] opacity-60' }}">
                        <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center shrink-0 transition-all
                            {{ in_array($post['id'], $selectedPostIds) ? 'bg-[#8B5CF6] border-[#8B5CF6]' : 'border-gray-300 dark:border-[#272B30]' }}">
                            @if(in_array($post['id'], $selectedPostIds))
                            <span class="material-symbols-outlined text-white text-sm">check</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $post['title'] }}</p>
                            <p class="text-xs text-[#6F767E]">{{ $post['slug'] }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($post['lang'])
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase
                                {{ $post['lang'] === 'en' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                {{ $post['lang'] }}
                            </span>
                            @endif
                            @if($post['has_image'])
                            <span class="material-symbols-outlined text-sm text-[#6F767E]" title="Has featured image">image</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="p-12 text-center">
                        <svg wire:loading class="animate-spin h-8 w-8 text-[#8B5CF6] mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm text-[#6F767E]">Fetching posts for preview...</p>
                    </div>
                @endif
            </div>

            {{-- Bottom nav --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-[#272B30] mt-4">
                <button wire:click="goBack" class="h-11 px-6 rounded-xl bg-gray-100 dark:bg-[#0B0B0B] text-sm font-bold text-[#6F767E] hover:bg-gray-200 dark:hover:bg-[#272B30] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Back
                </button>
                <button wire:click="continueToFieldMapping"
                    class="h-11 px-6 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 flex items-center gap-2">
                    Continue to Field Mapping
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 4: Field Mapping --}}
    @if($step === 4)
    <div class="space-y-6">
        {{-- Summary --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-[#8B5CF6]/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#8B5CF6] text-2xl">sync_alt</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Field Mapping</h2>
                        <p class="text-sm text-[#6F767E]">{{ $totalPosts }} items found in "{{ $selectedWpCpt }}"</p>
                        @if($isPolylang)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">translate</span>
                            Polylang detected —
                            @foreach($polylangLanguages as $lang => $count)
                                <strong>{{ strtoupper($lang) }}</strong> ({{ $count }})
                                @if(!$loop->last) + @endif
                            @endforeach
                            · importing <strong>{{ strtoupper($defaultImportLocale) }}</strong> as primary
                        </p>
                        @endif
                    </div>
                </div>
                <span class="px-3 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-[#8B5CF6] text-sm font-bold">
                    {{ $totalPosts }} Items
                </span>
            </div>

            {{-- Target CMS CPT Selection --}}
            <div class="border-t border-gray-100 dark:border-[#272B30] pt-4 mb-4">
                <label class="block text-sm font-medium text-[#6F767E] mb-2">Target CMS Post Type</label>
                <select
                    wire:model.live="selectedCmsCpt"
                    class="w-full h-12 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] px-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6]"
                >
                    <option value="">-- Select Target Post Type --</option>
                    @foreach($cmsCpts as $cpt)
                    <option value="{{ $cpt['id'] }}">{{ $cpt['name'] }} ({{ $cpt['slug'] }})</option>
                    @endforeach
                </select>
            </div>

            {{-- CLI Command Box --}}
            <div class="mt-4 p-4 rounded-2xl bg-gray-900 text-gray-100 dark:bg-[#0B0B0B] dark:border dark:border-[#272B30]">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-400 text-lg">terminal</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Run via CLI (Unlimited Execution Time)</span>
                    </div>
                    <span class="text-[11px] text-gray-400">Bypass 120s timeout</span>
                </div>
                <div class="flex items-center gap-2 bg-black/50 p-3 rounded-xl font-mono text-xs text-green-400 overflow-x-auto select-all">
                    <code>php artisan cms:import-wp --url={{ $wpUrl ?: 'https://www.centraldatatech.com' }} --wp-cpt={{ $selectedWpCpt ?: 'post' }} --target={{ $selectedCmsCpt ?: 'plugin_post' }}</code>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">💡 Salin perintah di atas dan jalankan di Terminal CLI Laragon untuk mengimpor seluruh postingan & gambar secara cepat tanpa batas waktu.</p>
            </div>
        </div>

        {{-- Field Mapping Table --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-4">Map Fields</h3>
            
            <div class="space-y-3">
                @foreach($cmsCptFields as $cmsField)
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] space-y-3">
                    <div class="flex items-center gap-4">
                        <div class="w-1/3">
                            <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">{{ $cmsField['label'] }}</p>
                            <p class="text-xs text-[#6F767E]">{{ $cmsField['key'] }}</p>
                        </div>
                        <span class="material-symbols-outlined text-[#6F767E]">arrow_forward</span>
                        <div class="flex-1">
                            <select
                                wire:model.live="fieldMappings.{{ $cmsField['key'] }}"
                                class="w-full h-10 rounded-lg border-none bg-white dark:bg-[#1A1A1A] px-3 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6]"
                            >
                                <option value="">-- Don't import --</option>
                                @foreach($wpCptFields as $wpField)
                                <option value="{{ $wpField['path'] }}">{{ $wpField['label'] }} {{ $wpField['sample'] ? '(' . $wpField['sample'] . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @php
                        $selectedWpRepeaterPath = data_get($fieldMappings, $cmsField['key']);
                        $availableWpSubFields = $this->getWpRepeaterSubFields($selectedWpRepeaterPath);
                    @endphp

                    {{-- Repeater Sub-field Mapping UI --}}
                    @if(($cmsField['type'] ?? '') === 'repeater' && !empty($selectedWpRepeaterPath) && !empty($cmsField['sub_fields']))
                    <div class="ml-4 md:ml-6 p-4 rounded-2xl bg-purple-50/60 dark:bg-purple-900/15 border border-purple-200/80 dark:border-purple-800/40 space-y-3">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-500 text-sm">account_tree</span>
                                <p class="text-xs font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider">Sub-field Pairing for "{{ $cmsField['label'] }}"</p>
                            </div>
                            <span class="text-[11px] text-[#6F767E]">Pair inner sub-fields from WordPress</span>
                        </div>

                        @foreach($cmsField['sub_fields'] as $subField)
                        <div class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-[#111111] border border-purple-100 dark:border-purple-900/30">
                            <div class="w-1/3 pl-2">
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $subField['label'] }}</span>
                                <span class="text-[10px] text-[#6F767E] block font-mono">key: {{ $subField['name'] }} ({{ $subField['type'] }})</span>
                            </div>
                            <span class="material-symbols-outlined text-xs text-purple-400">subdirectory_arrow_right</span>
                            <div class="flex-1">
                                <select
                                    wire:model.live="repeaterSubMappings.{{ $cmsField['key'] }}.{{ $subField['name'] }}"
                                    class="w-full h-9 rounded-lg border-none bg-gray-50 dark:bg-[#1A1A1A] px-3 text-xs font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6]"
                                >
                                    <option value="">-- Don't import sub-field --</option>
                                    @if(!empty($availableWpSubFields))
                                        @foreach($availableWpSubFields as $wpSubName)
                                        <option value="{{ $wpSubName }}">WP Sub-field: {{ $wpSubName }}</option>
                                        @endforeach
                                    @else
                                        @foreach($wpCptFields as $wpField)
                                        <option value="{{ $wpField['path'] }}">{{ $wpField['label'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Import Options --}}
            <div class="border-t border-gray-100 dark:border-[#272B30] pt-4 mt-4">
                <h4 class="text-sm font-bold text-[#6F767E] mb-3">Image Options</h4>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="downloadFeaturedImage" class="custom-checkbox" />
                        <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Download Featured Images</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="downloadContentImages" class="custom-checkbox" />
                        <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Download Content Images</span>
                    </label>
                </div>
            </div>
        </div>

        @if($errorMessage)
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-red-500">error</span>
                <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between">
            <button
                wire:click="goBack"
                class="h-12 px-6 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-bold text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Back
            </button>
            <button
                wire:click="importAllPosts"
                wire:loading.attr="disabled"
                class="h-12 px-8 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 disabled:opacity-50 flex items-center gap-2"
            >
                <span wire:loading.remove wire:target="importAllPosts" class="material-symbols-outlined text-xl">cloud_download</span>
                <svg wire:loading wire:target="importAllPosts" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Import {{ count($selectedPostIds) }} Selected Items
            </button>
        </div>
    </div>
    @endif

    {{-- Step 5: Import Results & Batch Progress --}}
    @if($step === 5)
    <div class="space-y-6" @if($isBatchImporting) wire:poll.200ms="processNextBatch" @endif>
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex flex-col items-center text-center">
                @if($isBatchImporting)
                <div class="h-16 w-16 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center mb-6">
                    <svg class="animate-spin h-8 w-8 text-[#8B5CF6]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">Importing Batch {{ $currentBatchIndex }} of {{ $totalBatchCount }}...</h2>
                <p class="text-[#6F767E] mb-6">Processing 5 items per batch to avoid HTTP timeout. Progress: {{ $importProgress }}%</p>

                {{-- Progress Bar --}}
                <div class="w-full max-w-lg bg-gray-200 dark:bg-[#272B30] h-4 rounded-full overflow-hidden mb-8">
                    <div class="bg-[#8B5CF6] h-full transition-all duration-300 rounded-full" style="width: {{ $importProgress }}%"></div>
                </div>
                @else
                @if(($importResults['failed'] ?? 0) === 0)
                <div class="h-16 w-16 rounded-full bg-[#83BF6E]/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-[#83BF6E] text-3xl">check_circle</span>
                </div>
                <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">Import Completed!</h2>
                @else
                <div class="h-16 w-16 rounded-full bg-amber-500/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-amber-500 text-3xl">warning</span>
                </div>
                <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">Import Completed with Issues</h2>
                @endif
                <p class="text-[#6F767E] mb-8">Your WordPress CPT entries have been imported safely.</p>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-4 gap-4 w-full max-w-lg mb-4">
                    <div class="p-4 rounded-2xl bg-[#83BF6E]/10 border border-[#83BF6E]/20">
                        <p class="text-3xl font-bold text-[#83BF6E]">{{ $importResults['success'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Imported</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20">
                        <p class="text-3xl font-bold text-amber-500">{{ $importResults['skipped'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Skipped</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20">
                        <p class="text-3xl font-bold text-blue-500">{{ $importResults['translated'] ?? 0 }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Translated</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20">
                        <p class="text-3xl font-bold text-red-500">{{ $importResults['failed'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Failed</p>
                    </div>
                </div>

                {{-- Taxonomy & Hierarchy Stats --}}
                @if(!empty($importResults['categories']) || !empty($importResults['tags']) || !empty($importResults['hierarchical_parents']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full max-w-lg mb-8">
                    @if(!empty($importResults['categories']))
                    <div class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-left flex items-center gap-3">
                        <span class="material-symbols-outlined text-purple-500 text-2xl">folder</span>
                        <div>
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $importResults['categories'] ?? 0 }}</p>
                            <p class="text-xs font-medium text-[#6F767E]">Categories Linked</p>
                        </div>
                    </div>
                    @endif
                    @if(!empty($importResults['tags']))
                    <div class="p-3.5 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-left flex items-center gap-3">
                        <span class="material-symbols-outlined text-indigo-500 text-2xl">label</span>
                        <div>
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $importResults['tags'] ?? 0 }}</p>
                            <p class="text-xs font-medium text-[#6F767E]">Tags Linked</p>
                        </div>
                    </div>
                    @endif
                    @if(!empty($importResults['hierarchical_parents']))
                    <div class="p-3.5 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-left flex items-center gap-3">
                        <span class="material-symbols-outlined text-teal-500 text-2xl">account_tree</span>
                        <div>
                            <p class="text-xl font-bold text-teal-600 dark:text-teal-400">{{ $importResults['hierarchical_parents'] ?? 0 }}</p>
                            <p class="text-xs font-medium text-[#6F767E]">Hierarchical Parents Linked</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Skipped Posts List --}}
                @if(!empty($importResults['skipped_posts']))
                <div class="w-full max-w-lg text-left mb-6">
                    <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">skip_next</span>
                        Skipped Items ({{ count($importResults['skipped_posts']) }}):
                    </h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach(array_slice($importResults['skipped_posts'], 0, 10) as $skipped)
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                            <p class="text-sm font-medium text-amber-700 dark:text-amber-300">{{ Str::limit($skipped['title'], 50) }}</p>
                            <p class="text-xs text-amber-500">{{ $skipped['reason'] }} — <code class="bg-amber-100 dark:bg-amber-800/30 px-1 rounded">{{ $skipped['slug'] }}</code></p>
                        </div>
                        @endforeach
                        @if(count($importResults['skipped_posts']) > 10)
                        <p class="text-xs text-[#6F767E] text-center py-2">... and {{ count($importResults['skipped_posts']) - 10 }} more skipped</p>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Errors List --}}
                @if(!empty($importResults['errors']))
                <div class="w-full max-w-lg text-left mb-8">
                    <h4 class="text-sm font-bold text-red-600 dark:text-red-400 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">error</span>
                        Failed Imports ({{ count($importResults['errors']) }}):
                    </h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach(array_slice($importResults['errors'], 0, 10) as $error)
                        <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">{!! Str::limit(strip_tags($error['title']), 50) !!}</p>
                            <p class="text-xs text-red-500">{{ $error['error'] }}</p>
                        </div>
                        @endforeach
                        @if(count($importResults['errors']) > 10)
                        <p class="text-xs text-[#6F767E] text-center">... and {{ count($importResults['errors']) - 10 }} more errors</p>
                        @endif
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-4">
                    <button
                        wire:click="resetMigration"
                        class="h-12 px-6 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-bold text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all"
                    >
                        Import More
                    </button>
                    @if($selectedCmsCpt)
                    @php
                        $targetCpt = collect($cmsCpts)->firstWhere('id', $selectedCmsCpt);
                    @endphp
                    <a
                        href="{{ route('admin.cpt.entries.index', $targetCpt['slug'] ?? '') }}"
                        wire:navigate
                        class="h-12 px-6 rounded-xl bg-[#8B5CF6] text-white font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20 flex items-center gap-2"
                    >
                        <span class="material-symbols-outlined text-xl">list</span>
                        View Entries
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Loading Overlay (for import) --}}
    @if($isLoading && $step === 3)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-8 shadow-2xl text-center max-w-sm w-full mx-4">
            <div class="mb-6">
                <svg class="animate-spin h-12 w-12 text-[#8B5CF6] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">Importing CPT Entries...</h3>
            <p class="text-sm text-[#6F767E] mb-2">Processing page {{ $currentPageImporting }} of {{ $totalPages }}</p>
            <p class="text-xs text-[#6F767E] mb-4">Please wait while we import your entries.</p>
            <div class="w-full bg-gray-100 dark:bg-[#272B30] rounded-full h-2.5">
                <div class="bg-[#8B5CF6] h-2.5 rounded-full transition-all duration-300" style="width: {{ $importProgress }}%"></div>
            </div>
            <p class="text-sm font-bold text-[#8B5CF6] mt-2">{{ $importProgress }}%</p>
        </div>
    </div>
    @endif
</div>
