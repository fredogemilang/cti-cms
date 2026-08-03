<div>
    {{-- Step 1: Input URL --}}
    @if($step === 1)
    <div class="space-y-6">
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-2xl bg-[#2563EB]/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#2563EB] text-2xl">cloud_download</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Import from WordPress</h2>
                    <p class="text-sm text-[#6F767E]">Enter your WordPress site URL to import all posts</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#6F767E] mb-2">WordPress Site URL</label>
                    <div class="flex gap-3">
                        <input
                            wire:model="wpUrl"
                            type="url"
                            placeholder="https://yoursite.com or https://yoursite.com/wp-json/wp/v2/posts"
                            class="flex-1 h-12 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] px-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-[#6F767E]"
                        />
                        <button
                            wire:click="fetchPostsInfo"
                            wire:loading.attr="disabled"
                            class="h-12 px-6 rounded-xl bg-[#2563EB] text-white font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="fetchPostsInfo" class="material-symbols-outlined text-xl">search</span>
                            <svg wire:loading wire:target="fetchPostsInfo" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="fetchPostsInfo">Check Posts</span>
                            <span wire:loading wire:target="fetchPostsInfo">Checking...</span>
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

                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-blue-500 mt-0.5">info</span>
                        <div class="text-sm text-blue-600 dark:text-blue-400">
                            <p class="font-medium mb-1">How it works:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-500">
                                <li>Enter your WordPress site URL</li>
                                <li>We'll automatically fetch and import ALL posts</li>
                                <li>Images will be downloaded to your Media Library</li>
                                <li>Original publication dates are preserved for SEO</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Post Content Image Cleaner & Featured Image Extractor --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-2xl">auto_fix_high</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Content Image Cleaner & Featured Image Extractor</h2>
                    <p class="text-sm text-[#6F767E]">Fix articles missing featured images and clean up duplicate top images from post bodies</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Scanner 1 --}}
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-7 w-7 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 font-bold text-xs">1</div>
                            <h3 class="font-bold text-[#111827] dark:text-[#FCFCFC] text-sm">Scanner 1: Extract Featured Image</h3>
                        </div>
                        <p class="text-xs text-[#6F767E] leading-relaxed">
                            Scan posts without a featured image, extract the first image found at top of content, and set it as Featured Image.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button
                            wire:click="runScanner1"
                            wire:loading.attr="disabled"
                            class="w-full h-11 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-sm transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="runScanner1" class="material-symbols-outlined text-lg">search_check</span>
                            <svg wire:loading wire:target="runScanner1" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="runScanner1">Run Scanner 1</span>
                            <span wire:loading wire:target="runScanner1">Scanning & Extracting...</span>
                        </button>

                        @if($scanner1Results)
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-700 dark:text-emerald-400 space-y-1">
                            <p class="font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check_circle</span> Scanner 1 Finished!</p>
                            <p>• Scanned {{ $scanner1Results['scanned'] }} post(s) without featured image</p>
                            <p>• Successfully set {{ $scanner1Results['updated'] }} post(s) featured image</p>
                            <p>• Skipped {{ $scanner1Results['skipped'] }} post(s) (no image in body)</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Scanner 2 --}}
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] flex flex-col justify-between space-y-4 {{ !$scanner1Completed ? 'opacity-60' : '' }}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="h-7 w-7 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-500 font-bold text-xs">2</div>
                                <h3 class="font-bold text-[#111827] dark:text-[#FCFCFC] text-sm">Scanner 2: Remove Top Body Image</h3>
                            </div>
                            @if(!$scanner1Completed)
                            <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold border border-amber-500/20">Locked (Requires Scanner 1)</span>
                            @else
                            <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold border border-emerald-500/20">Unlocked & Ready</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#6F767E] leading-relaxed">
                            Scan posts and remove the duplicate top image from the article body content so it doesn't appear twice under the Featured Image.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button
                            wire:click="runScanner2"
                            wire:loading.attr="disabled"
                            @if(!$scanner1Completed) disabled @endif
                            class="w-full h-11 rounded-xl bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 dark:disabled:bg-gray-800 disabled:cursor-not-allowed text-white font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove wire:target="runScanner2" class="material-symbols-outlined text-lg">content_cut</span>
                            <svg wire:loading wire:target="runScanner2" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="runScanner2">Run Scanner 2</span>
                            <span wire:loading wire:target="runScanner2">Removing Top Images...</span>
                        </button>

                        @if($scanner2Results)
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-700 dark:text-emerald-400 space-y-1">
                            <p class="font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-base">check_circle</span> Scanner 2 Finished!</p>
                            <p>• Scanned {{ $scanner2Results['scanned'] }} post(s) with featured image</p>
                            <p>• Cleaned {{ $scanner2Results['cleaned'] }} post(s) top body image</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 2: Configure & Import --}}
    @if($step === 2)
    <div class="space-y-6">
        {{-- Summary Card --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-[#83BF6E]/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#83BF6E] text-2xl">check_circle</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">WordPress Site Found</h2>
                        <p class="text-sm text-[#6F767E]">{{ $totalPosts }} posts ready to import ({{ $totalPages }} pages)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] text-sm font-bold">
                        {{ $totalPosts }} Posts
                    </span>
                </div>
            </div>

            {{-- Preview Posts --}}
            @if(count($previewPosts) > 0)
            <div class="border-t border-gray-100 dark:border-[#272B30] pt-4">
                <h4 class="text-sm font-bold text-[#6F767E] mb-3">Preview (first 5 posts):</h4>
                <div class="space-y-2">
                    @foreach($previewPosts as $post)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B]">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] truncate">{{ $post['title'] }}</p>
                            <p class="text-xs text-[#6F767E]">{{ $post['slug'] }}</p>
                        </div>
                        <span class="text-xs text-[#6F767E] ml-4">{{ \Carbon\Carbon::parse($post['date'])->format('M d, Y') }}</span>
                    </div>
                    @endforeach
                    @if($totalPosts > 5)
                    <p class="text-xs text-[#6F767E] text-center py-2">... and {{ $totalPosts - 5 }} more posts</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Import Options --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6">
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-4">Import Options</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                @foreach(['title' => 'Title', 'slug' => 'Slug', 'content' => 'Content', 'excerpt' => 'Excerpt', 'published_at' => 'Original Date (SEO)', 'categories' => 'Categories', 'tags' => 'Tags'] as $field => $label)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="fieldMappings.{{ $field }}" class="custom-checkbox" />
                    <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            
            {{-- Image Options --}}
            <div class="border-t border-gray-100 dark:border-[#272B30] pt-4">
                <h4 class="text-sm font-bold text-[#6F767E] mb-3">Image Options</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#6F767E] mb-2">Featured Image Mode</label>
                        <select wire:model.live="fieldMappings.featured_image" class="w-full h-10 rounded-lg border-none bg-gray-50 dark:bg-[#0B0B0B] px-3 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB]">
                            <option value="download">Download to Media Library</option>
                            <option value="url">Keep as External URL</option>
                            <option value="skip">Skip featured images</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer h-10">
                            <input type="checkbox" wire:model.live="fieldMappings.content_images" class="custom-checkbox" />
                            <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Download content images to Media Library</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-3 border-t border-dashed border-gray-200 dark:border-[#272B30]">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="fieldMappings.auto_extract_featured" class="custom-checkbox" />
                        <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Auto-extract Featured Image from top content image if missing (Priority: EN WP ➔ ID WP ➔ EN Content ➔ ID Content)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="fieldMappings.auto_clean_top_image" class="custom-checkbox" />
                        <span class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">Auto-remove duplicate top image from article body content</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Discovered Taxonomies & Mapping Card --}}
        @if(count($discoveredTaxonomies) > 0)
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Taxonomy Detector & Mapping</h3>
                    <p class="text-xs text-[#6F767E]">Map WordPress taxonomies (categories, tags, custom taxonomies) to CMS Categories or Tags</p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-[#8B5CF6] text-xs font-bold">
                    {{ count($discoveredTaxonomies) }} Taxonomies Detected
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @foreach($discoveredTaxonomies as $slug => $tax)
                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] flex flex-col gap-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-500 text-lg">schema</span>
                            <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $tax['name'] }}</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-200 dark:bg-[#272B30] text-[#6F767E] uppercase tracking-wider">{{ $slug }}</span>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <label class="text-xs text-[#6F767E] shrink-0 font-medium">Map to CMS:</label>
                        <select wire:model.live="taxonomyMappings.{{ $slug }}" class="flex-1 h-9 rounded-xl border-none bg-white dark:bg-[#1A1A1A] px-3 text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#8B5CF6]">
                            <option value="category">Category (Import as New Category if missing)</option>
                            <option value="tag">Tag (Import as New Tag if missing)</option>
                            <option value="skip">-- Skip Taxonomy --</option>
                        </select>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <p class="text-xs text-[#6F767E] mt-2">Downloaded images will be saved to the Media Library.</p>
        
        {{-- Action Buttons --}}
        <div class="flex items-center justify-between gap-4">
            <button
                wire:click="resetMigration"
                class="h-12 px-6 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-bold text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Back
            </button>
            <div class="flex items-center gap-3">
                <button
                    wire:click="importPosts(6)"
                    wire:loading.attr="disabled"
                    class="h-12 px-6 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-[#8B5CF6] border border-purple-200 dark:border-purple-800 font-bold text-sm hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-all flex items-center gap-2"
                >
                    <span wire:loading.remove wire:target="importPosts(6)" class="material-symbols-outlined text-xl">science</span>
                    <svg wire:loading wire:target="importPosts(6)" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Test Run (Import 6 Posts)
                </button>
                <button
                    wire:click="importAllPosts"
                    wire:loading.attr="disabled"
                    class="h-12 px-8 rounded-xl bg-[#2563EB] text-white font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2"
                >
                    <span wire:loading.remove wire:target="importAllPosts" class="material-symbols-outlined text-xl">cloud_download</span>
                    <svg wire:loading wire:target="importAllPosts" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Import All {{ $totalPosts }} Posts
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 3: Import Progress & Results --}}
    @if($step === 3)
    <div class="space-y-6" @if($isBatchImporting && !$importFinished) wire:poll.100ms="processNextBatch" @endif>
        @if($isBatchImporting && !$importFinished)
        {{-- Live Progress Card --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex flex-col items-center text-center max-w-xl mx-auto">
                <div class="h-16 w-16 rounded-full bg-blue-500/10 flex items-center justify-center mb-4">
                    <svg class="animate-spin h-8 w-8 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">Importing Posts in Batches...</h2>
                <p class="text-sm text-[#6F767E] mb-6">{{ $currentBatchStatus ?: "Processing batch {$currentBatchIndex} of {$totalBatchCount}..." }}</p>

                {{-- Progress Bar --}}
                <div class="w-full bg-gray-100 dark:bg-[#272B30] h-4 rounded-full overflow-hidden mb-3 relative">
                    <div class="bg-[#2563EB] h-full transition-all duration-300 rounded-full" style="width: {{ $importProgress }}%"></div>
                </div>

                <div class="flex justify-between w-full text-xs font-bold text-[#6F767E] mb-6">
                    <span>Batch {{ $currentBatchIndex }} / {{ $totalBatchCount }} ({{ $batchSize }} items/batch)</span>
                    <span class="text-[#2563EB]">{{ $importProgress }}% Completed</span>
                </div>

                {{-- Live Counter Grid --}}
                <div class="grid grid-cols-3 gap-4 w-full">
                    <div class="p-4 rounded-2xl bg-[#83BF6E]/10 border border-[#83BF6E]/20 text-center">
                        <p class="text-2xl font-bold text-[#83BF6E]">{{ $importResults['success'] }}</p>
                        <p class="text-xs font-medium text-[#6F767E]">Imported</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center">
                        <p class="text-2xl font-bold text-amber-500">{{ $importResults['skipped'] }}</p>
                        <p class="text-xs font-medium text-[#6F767E]">Skipped</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-center">
                        <p class="text-2xl font-bold text-red-500">{{ $importResults['failed'] }}</p>
                        <p class="text-xs font-medium text-[#6F767E]">Failed</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Final Results Card --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] p-8">
            <div class="flex flex-col items-center text-center">
                @if($importResults['failed'] === 0)
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
                <p class="text-[#6F767E] mb-8">All {{ $totalBatchCount }} batches (12 articles/batch) have been processed successfully.</p>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4 w-full max-w-md mb-8">
                    <div class="p-4 rounded-2xl bg-[#83BF6E]/10 border border-[#83BF6E]/20">
                        <p class="text-3xl font-bold text-[#83BF6E]">{{ $importResults['success'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Imported</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20">
                        <p class="text-3xl font-bold text-amber-500">{{ $importResults['skipped'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Skipped</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20">
                        <p class="text-3xl font-bold text-red-500">{{ $importResults['failed'] }}</p>
                        <p class="text-sm font-medium text-[#6F767E]">Failed</p>
                    </div>
                </div>

                {{-- Skipped Posts List --}}
                @if(!empty($importResults['skipped_posts']))
                <div class="w-full max-w-lg text-left mb-6">
                    <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">skip_next</span>
                        Skipped Posts ({{ count($importResults['skipped_posts']) }}):
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
                    <a
                        href="{{ route('admin.posts.index') }}"
                        wire:navigate
                        class="h-12 px-6 rounded-xl bg-[#2563EB] text-white font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2"
                    >
                        <span class="material-symbols-outlined text-xl">article</span>
                        View All Posts
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Loading Overlay (for import) --}}
    @if($isLoading && $step === 2)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-8 shadow-2xl text-center max-w-sm w-full mx-4">
            <div class="mb-6">
                <svg class="animate-spin h-12 w-12 text-[#2563EB] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-2">Importing Posts...</h3>
            <p class="text-sm text-[#6F767E] mb-2">Processing page {{ $currentPageImporting }} of {{ $totalPages }}</p>
            <p class="text-xs text-[#6F767E] mb-4">Please wait while we download and import your posts.</p>
            <div class="w-full bg-gray-100 dark:bg-[#272B30] rounded-full h-2.5">
                <div class="bg-[#2563EB] h-2.5 rounded-full transition-all duration-300" style="width: {{ $importProgress }}%"></div>
            </div>
            <p class="text-sm font-bold text-[#2563EB] mt-2">{{ $importProgress }}%</p>
        </div>
    </div>
    @endif
</div>
