<div>
    <form wire:submit="save" class="space-y-6">

        {{-- Posts Permalink --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#2563EB] text-xl">rss_feed</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Posts</h2>
                    <p class="text-xs text-[#6F767E]">Configure the base URL slug for your blog posts.</p>
                </div>
            </div>

            <div class="space-y-5">
                {{-- Post Base --}}
                <div>
                    <label class="block text-sm font-semibold text-[#111827] dark:text-[#FCFCFC] mb-2">Post Archive Slug</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-[#6F767E] whitespace-nowrap">{{ $siteUrl }}/</span>
                        <input type="text" wire:model.live.debounce.300ms="postBase"
                               class="flex-1 px-3 py-2 bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] rounded-xl text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all font-mono"
                               placeholder="blog">
                    </div>
                    @error('postBase') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2 px-3 py-2 bg-gray-50 dark:bg-[#111315] rounded-lg border border-dashed border-gray-200 dark:border-[#272B30]">
                        <p class="text-xs text-[#6F767E]">
                            <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Preview:</span>
                            <span class="font-mono text-[#2563EB] dark:text-blue-400">{{ $siteUrl }}/{{ $postBase ?: 'blog' }}/my-awesome-post</span>
                        </p>
                    </div>
                </div>

                {{-- Category Base --}}
                <div>
                    <label class="block text-sm font-semibold text-[#111827] dark:text-[#FCFCFC] mb-2">Category Base</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-[#6F767E] whitespace-nowrap font-mono">{{ $siteUrl }}/{{ $postBase ?: 'blog' }}/</span>
                        <input type="text" wire:model.live.debounce.300ms="categoryBase"
                               class="flex-1 px-3 py-2 bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] rounded-xl text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all font-mono"
                               placeholder="category">
                        <span class="text-sm text-[#6F767E] font-mono">/sample-category</span>
                    </div>
                    @error('categoryBase') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2 px-3 py-2 bg-gray-50 dark:bg-[#111315] rounded-lg border border-dashed border-gray-200 dark:border-[#272B30]">
                        <p class="text-xs text-[#6F767E]">
                            <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Preview:</span>
                            <span class="font-mono text-[#2563EB] dark:text-blue-400">{{ $siteUrl }}/{{ $postBase ?: 'blog' }}/{{ $categoryBase ?: 'category' }}/cloud-computing</span>
                        </p>
                    </div>
                </div>

                {{-- Tag Base --}}
                <div>
                    <label class="block text-sm font-semibold text-[#111827] dark:text-[#FCFCFC] mb-2">Tag Base</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-[#6F767E] whitespace-nowrap font-mono">{{ $siteUrl }}/{{ $postBase ?: 'blog' }}/</span>
                        <input type="text" wire:model.live.debounce.300ms="tagBase"
                               class="flex-1 px-3 py-2 bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] rounded-xl text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all font-mono"
                               placeholder="tag">
                        <span class="text-sm text-[#6F767E] font-mono">/sample-tag</span>
                    </div>
                    @error('tagBase') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2 px-3 py-2 bg-gray-50 dark:bg-[#111315] rounded-lg border border-dashed border-gray-200 dark:border-[#272B30]">
                        <p class="text-xs text-[#6F767E]">
                            <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Preview:</span>
                            <span class="font-mono text-[#2563EB] dark:text-blue-400">{{ $siteUrl }}/{{ $postBase ?: 'blog' }}/{{ $tagBase ?: 'tag' }}/agentic-ai</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pages Info --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">description</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Pages</h2>
                    <p class="text-xs text-[#6F767E]">Pages always use a flat URL structure.</p>
                </div>
            </div>
            <div class="px-3 py-2 bg-gray-50 dark:bg-[#111315] rounded-lg border border-dashed border-gray-200 dark:border-[#272B30]">
                <p class="text-xs text-[#6F767E]">
                    <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Structure:</span>
                    <span class="font-mono text-emerald-600 dark:text-emerald-400">{{ $siteUrl }}/{page-slug}</span>
                </p>
            </div>
            <p class="text-xs text-[#6F767E] mt-2">Pages use their slug directly as the URL path. To change a page's URL, edit the slug in the page editor.</p>
        </div>

        {{-- Custom Post Types --}}
        @if(count($cpts) > 0)
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-xl">folder_open</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Custom Post Types</h2>
                    <p class="text-xs text-[#6F767E]">Configure the base URL slug for each custom post type.</p>
                </div>
            </div>

            <div class="space-y-5">
                @foreach($cpts as $cpt)
                <div>
                    <label class="block text-sm font-semibold text-[#111827] dark:text-[#FCFCFC] mb-2">
                        {{ $cpt->name }}
                        <span class="text-xs font-normal text-[#6F767E]">(originally: {{ $cpt->slug }})</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-[#6F767E] whitespace-nowrap">{{ $siteUrl }}/</span>
                        <input type="text" wire:model.live.debounce.300ms="cptBases.{{ $cpt->slug }}"
                               class="flex-1 px-3 py-2 bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] rounded-xl text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all font-mono"
                               placeholder="{{ $cpt->slug }}">
                        <span class="text-sm text-[#6F767E] font-mono">/entry-slug</span>
                    </div>
                    @error('cptBases.' . $cpt->slug) <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2 px-3 py-2 bg-gray-50 dark:bg-[#111315] rounded-lg border border-dashed border-gray-200 dark:border-[#272B30]">
                        <p class="text-xs text-[#6F767E]">
                            <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Preview:</span>
                            <span class="font-mono text-amber-600 dark:text-amber-400">{{ $siteUrl }}/{{ $cptBases[$cpt->slug] ?? $cpt->slug }}/sample-entry</span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick Reference --}}
        <div class="rounded-3xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-[#1A1A1A] dark:to-[#1A1A1A] border border-blue-100 dark:border-[#272B30] p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-[#2563EB] text-xl">info</span>
                <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Quick Reference</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="text-xs text-[#6F767E] space-y-1">
                    <p><span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Posts:</span> <span class="font-mono">/{{ $postBase ?: 'blog' }}/{slug}</span></p>
                    <p><span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Categories:</span> <span class="font-mono">/{{ $postBase ?: 'blog' }}/{{ $categoryBase ?: 'category' }}/{slug}</span></p>
                    <p><span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Tags:</span> <span class="font-mono">/{{ $postBase ?: 'blog' }}/{{ $tagBase ?: 'tag' }}/{slug}</span></p>
                </div>
                <div class="text-xs text-[#6F767E] space-y-1">
                    <p><span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">Pages:</span> <span class="font-mono">/{slug}</span></p>
                    @foreach($cpts as $cpt)
                    <p><span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">{{ $cpt->name }}:</span> <span class="font-mono">/{{ $cptBases[$cpt->slug] ?? $cpt->slug }}/{slug}</span></p>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex items-center justify-end sticky bottom-0 bg-gradient-to-t from-[#F4F5F6] dark:from-[#0B0B0B] via-[#F4F5F6]/95 dark:via-[#0B0B0B]/95 to-transparent py-4 -mx-4 px-4 z-10">
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#2563EB] text-white text-sm font-bold hover:bg-blue-700 transition-colors disabled:opacity-50"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                Save Permalink Settings
            </button>
        </div>
    </form>
</div>
