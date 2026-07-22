{{-- Modern SEO & GEO Metabox UI --}}
<div class="seo-metabox bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-200 dark:border-[#272B30] overflow-hidden" wire:submit.prevent="save">

    {{-- Card Header --}}
    <div class="border-b border-gray-200 dark:border-[#272B30] bg-[#F9FAFB] dark:bg-[#121212]/60">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">SEO & GEO Settings</h3>
            </div>
        </div>

        {{-- Horizontal Tab Navigation --}}
        <div class="flex items-center gap-1 px-6 -mb-px overflow-x-auto no-scrollbar" role="tablist">
            <button type="button" wire:click="$set('activeTab', 'seo')" role="tab"
                    class="flex items-center gap-2 px-4 py-3 text-xs font-bold transition border-b-2
                           {{ $activeTab === 'seo' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-[#6F767E] dark:text-[#9A9A9A] border-transparent hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                SEO
            </button>

            <button type="button" wire:click="$set('activeTab', 'social')" role="tab"
                    class="flex items-center gap-2 px-4 py-3 text-xs font-bold transition border-b-2
                           {{ $activeTab === 'social' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-[#6F767E] dark:text-[#9A9A9A] border-transparent hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Social
            </button>

            @if($this->canAccessAdvanced())
                <button type="button" wire:click="$set('activeTab', 'schema')" role="tab"
                        class="flex items-center gap-2 px-4 py-3 text-xs font-bold transition border-b-2
                               {{ $activeTab === 'schema' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-[#6F767E] dark:text-[#9A9A9A] border-transparent hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    Schema
                </button>
            @endif

            <button type="button" wire:click="$set('activeTab', 'geo')" role="tab"
                    class="flex items-center gap-2 px-4 py-3 text-xs font-bold transition border-b-2
                           {{ $activeTab === 'geo' ? 'text-purple-600 dark:text-purple-400 border-purple-600 dark:border-purple-400' : 'text-[#6F767E] dark:text-[#9A9A9A] border-transparent hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/></svg>
                GEO / AI
                <span class="inline-flex items-center rounded-md bg-purple-100 dark:bg-purple-900/50 px-1.5 py-0.5 text-[9px] font-extrabold text-purple-700 dark:text-purple-300 leading-none">NEW</span>
            </button>
        </div>
    </div>

    {{-- Tab Panels --}}
    <div class="p-6">

        {{-- ═══════════════ SEO TAB ═══════════════ --}}
        <div x-data x-show="$wire.activeTab === 'seo'" x-cloak class="space-y-6">

            {{-- SERP Preview (Google Snippet Card) --}}
            <div class="space-y-2">
                <span class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider">Search Engine Preview</span>
                <div class="rounded-2xl border border-gray-200 dark:border-[#272B30] p-5 bg-[#F9FAFB] dark:bg-[#0B0B0B] space-y-1.5">
                    <div class="text-[18px] leading-snug text-[#1a0dab] dark:text-[#8ab4f8] font-medium truncate">
                        {{ $title ?: 'Page Title Here' }}
                    </div>
                    <div class="text-[13px] text-[#006621] dark:text-[#81c995] truncate font-mono">
                        {{ $canonical_url ?: url('/') }}
                    </div>
                    <div class="text-[13px] text-[#4d5156] dark:text-[#bdc1c6] line-clamp-2 leading-relaxed">
                        {{ $description ?: 'Add a meta description to control how this page appears in search results…' }}
                    </div>
                </div>
            </div>

            {{-- Focus Keyphrase --}}
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Focus Keyphrase</label>
                <div class="relative">
                    <input type="text" wire:model="focus_keyword"
                           class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4 pr-10 placeholder:text-gray-400 dark:placeholder:text-gray-600 transition-all"
                           placeholder="e.g. generative engine optimization">
                    @if($focus_keyword)
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                    @endif
                </div>
            </div>

            {{-- SEO Title --}}
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">SEO Title</label>
                <input type="text" wire:model.live.debounce.300ms="title" maxlength="70"
                       class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4 placeholder:text-gray-400 dark:placeholder:text-gray-600 transition-all"
                       placeholder="Auto-generated from page title">
                {{-- Modern progress bar --}}
                <div class="space-y-1 pt-1">
                    <div class="h-1.5 rounded-full bg-gray-200 dark:bg-[#272B30] overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300
                                    {{ $this->titleLength === 0 ? 'w-0' : '' }}
                                    {{ $this->titleLength > 0 && $this->titleLength <= 60 ? 'bg-emerald-500' : '' }}
                                    {{ $this->titleLength > 60 ? 'bg-rose-500' : '' }}"
                             style="width: {{ min($this->titleLength / 70 * 100, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="{{ $this->titleLength > 60 ? 'text-rose-500 font-bold' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $this->titleLength > 60 ? 'Too long for Google display' : 'Optimal length' }}
                        </span>
                        <span class="font-mono text-gray-400 dark:text-gray-500">{{ $this->titleLength }}/60</span>
                    </div>
                </div>
            </div>

            {{-- Meta Description --}}
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Meta Description</label>
                <textarea wire:model.live.debounce.300ms="description" maxlength="160" rows="3"
                          class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 placeholder:text-gray-400 dark:placeholder:text-gray-600 resize-y transition-all"
                          placeholder="Auto-generated from excerpt or content if left empty"></textarea>
                {{-- Modern progress bar --}}
                <div class="space-y-1">
                    <div class="h-1.5 rounded-full bg-gray-200 dark:bg-[#272B30] overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300
                                    {{ $this->descriptionLength === 0 ? 'w-0' : '' }}
                                    {{ $this->descriptionLength > 0 && $this->descriptionLength <= 120 ? 'bg-amber-400' : '' }}
                                    {{ $this->descriptionLength > 120 && $this->descriptionLength <= 160 ? 'bg-emerald-500' : '' }}
                                    {{ $this->descriptionLength > 160 ? 'bg-rose-500' : '' }}"
                             style="width: {{ min($this->descriptionLength / 160 * 100, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="{{ $this->descriptionLength > 160 ? 'text-rose-500 font-bold' : ($this->descriptionLength < 120 && $this->descriptionLength > 0 ? 'text-amber-500 font-medium' : 'text-gray-400 dark:text-gray-500') }}">
                            {{ $this->descriptionLength > 160 ? 'Too long' : ($this->descriptionLength < 120 && $this->descriptionLength > 0 ? 'A bit short' : 'Optimal length') }}
                        </span>
                        <span class="font-mono text-gray-400 dark:text-gray-500">{{ $this->descriptionLength }}/160</span>
                    </div>
                </div>
            </div>

            {{-- Advanced SEO Collapsible --}}
            <details class="group pt-2">
                <summary class="cursor-pointer flex items-center gap-2 text-xs font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    Advanced Crawling & Indexing
                </summary>
                <div class="mt-4 space-y-4 pl-6 border-l-2 border-gray-200 dark:border-[#272B30]">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Canonical URL</label>
                        <input type="url" wire:model="canonical_url" placeholder="Leave blank to use current permalink"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Robots Meta Directives</label>
                        <select wire:model="robots" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            <option value="index,follow">index, follow (Default)</option>
                            <option value="noindex,follow">noindex, follow</option>
                            <option value="index,nofollow">index, nofollow</option>
                            <option value="noindex,nofollow">noindex, nofollow</option>
                        </select>
                    </div>
                </div>
            </details>
        </div>

        {{-- ═══════════════ SOCIAL TAB ═══════════════ --}}
        <div x-data x-show="$wire.activeTab === 'social'" x-cloak class="space-y-6">

            {{-- Facebook / Open Graph --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <h4 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">Facebook / Open Graph</h4>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">OG Title</label>
                        <input type="text" wire:model="og_title"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4"
                               placeholder="Defaults to SEO Title">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">OG Description</label>
                        <textarea wire:model="og_description" rows="2"
                                  class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 resize-y"
                                  placeholder="Defaults to Meta Description"></textarea>
                    </div>

                    {{-- OG Image --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider">OG Image</label>
                            @if($ogImageUrl)
                                <button type="button" wire:click="removeOgImage" class="text-[11px] font-bold text-rose-500 hover:text-rose-600 transition-colors">Remove</button>
                            @endif
                        </div>
                        @if($ogImageUrl)
                            <div class="relative aspect-video w-full rounded-xl overflow-hidden border border-gray-200 dark:border-[#272B30] group">
                                <img src="{{ $ogImageUrl }}" alt="OG Image" class="w-full h-full object-cover">
                                <button type="button" wire:click="openOgImagePicker"
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-bold text-sm gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    Change Image
                                </button>
                            </div>
                        @else
                            <button type="button" wire:click="openOgImagePicker"
                                class="w-full h-28 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-2 border-dashed border-gray-300 dark:border-[#272B30] flex flex-col items-center justify-center gap-1.5 hover:bg-gray-100 dark:hover:bg-[#1A1A1A] hover:border-blue-400 dark:hover:border-blue-600 transition-all cursor-pointer">
                                <svg class="w-6 h-6 text-gray-300 dark:text-[#3a3a3a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                                </svg>
                                <span class="text-[11px] font-bold text-[#6F767E]">Select OG Image</span>
                            </button>
                        @endif
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Leave empty to use featured image. Recommended: 1200×630px</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-[#272B30]">

            {{-- X (Twitter) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-800 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <h4 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-wider">X (Twitter) Card</h4>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Card Type</label>
                    <select wire:model="twitter_card"
                            class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                        <option value="summary">Summary</option>
                        <option value="summary_large_image">Summary with Large Image</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ═══════════════ SCHEMA TAB ═══════════════ --}}
        <div x-data x-show="$wire.activeTab === 'schema'" x-cloak class="space-y-6">
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">Structured Data Type</label>
                <select wire:model="schema_type"
                        class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                    <option value="">🔄 Auto-detect</option>
                    <option value="Article">📄 Article</option>
                    <option value="BlogPosting">📝 Blog Posting</option>
                    <option value="NewsArticle">📰 News Article</option>
                    <option value="WebPage">🌐 Web Page</option>
                    <option value="Event">📅 Event</option>
                    <option value="Organization">🏢 Organization</option>
                    <option value="FAQPage">❓ FAQ Page</option>
                </select>
            </div>

            <div class="rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-blue-900 dark:text-blue-200 leading-relaxed">
                        JSON-LD structured data is automatically compiled based on content and author profiles. Global Organization schema with E-E-A-T trust signals is configured in <strong>Settings → SEO</strong>.
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══════════════ GEO / AI TAB ═══════════════ --}}
        <div x-data x-show="$wire.activeTab === 'geo'" x-cloak class="space-y-6">

            {{-- AI Summary --}}
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">AI Summary & Citation Abstract</label>
                <textarea wire:model="ai_summary" rows="3" maxlength="500"
                          class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-purple-500 p-4 placeholder:text-gray-400 dark:placeholder:text-gray-600 resize-y transition-all"
                          placeholder="A concise, quotable summary of this content for AI search engines (Perplexity, ChatGPT Search, Google AI Overviews)…"></textarea>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                    Injected as <code class="bg-[#F4F5F6] dark:bg-[#272B30] px-1.5 py-0.5 rounded text-purple-600 dark:text-purple-300 font-mono text-[10px]">abstract</code> in schema. Write a clear, factual 2-3 sentence summary that AI search engines can easily cite.
                </p>
            </div>

            {{-- Cornerstone Content Toggle --}}
            <div class="rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_cornerstone"
                           class="mt-1 rounded-md border-gray-300 dark:border-gray-700 text-amber-600 shadow-sm focus:ring-amber-500">
                    <div>
                        <span class="text-sm font-bold text-amber-950 dark:text-amber-200 block">Mark as Cornerstone Content</span>
                        <p class="text-xs text-amber-900/80 dark:text-amber-300/80 mt-0.5 leading-relaxed">
                            Cornerstone content represents your standard-setting core guides. They are prioritized for LLM indexing, internal link audits, and freshness checks.
                        </p>
                    </div>
                </label>
            </div>

            {{-- GEO Features Feature Card --}}
            <div class="rounded-2xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/50 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/></svg>
                    <h4 class="text-xs font-bold text-purple-950 dark:text-purple-200 uppercase tracking-wider">Active GEO Engine Optimizations</h4>
                </div>
                <ul class="text-xs text-purple-900/90 dark:text-purple-200/90 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></span>
                        <code class="bg-purple-100 dark:bg-purple-900/60 px-1.5 py-0.5 rounded font-mono text-[10px]">speakable</code> schema for AI voice assistant quoting
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></span>
                        <code class="bg-purple-100 dark:bg-purple-900/60 px-1.5 py-0.5 rounded font-mono text-[10px]">abstract</code> structured summary for LLM context retrieval
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></span>
                        Author E-E-A-T trust signals enriched with credentials & social profiles
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></span>
                        Auto-generated <code class="bg-purple-100 dark:bg-purple-900/60 px-1.5 py-0.5 rounded font-mono text-[10px]">/llms.txt</code> for LLM crawler discovery
                    </li>
                </ul>
            </div>
        </div>

    </div>

    {{-- Media Picker Modal --}}
    @if($showMediaPicker)
        <livewire:admin.media-picker field="seo_og_image" :show-modal="true" :key="'seo-og-media-picker'" />
    @endif
</div>
