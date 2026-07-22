<div class="space-y-6">
    <form wire:submit.prevent="save">
        {{-- Sticky Top Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">SEO Settings</h1>
                <p class="text-sm font-normal text-[#6F767E] dark:text-[#9A9FA5] mt-1">Manage all search engine optimization, indexing, social tags, and AI search settings in one place.</p>
            </div>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2 shrink-0">
                <span class="material-symbols-outlined text-xl">save</span>
                <span>Save Changes</span>
            </button>
        </div>

        {{-- 2-Column Yoast-Style Layout --}}
        <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
            {{-- Left Vertical Navigation Sidebar --}}
            <aside class="w-full lg:w-64 shrink-0 bg-white dark:bg-[#1A1A1A] rounded-3xl p-4 shadow-sm space-y-4">
                {{-- Quick Filter Search --}}
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                    <input type="text" wire:model.live="searchQuery" placeholder="Filter settings..." class="w-full h-10 pl-10 pr-3 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Vertical Accordion Groups --}}
                <nav class="space-y-4 text-xs font-semibold" x-data="{ openGroups: { general: true, content: true, taxonomy: true, geo: true, advanced: true } }">
                    {{-- Group 1: General --}}
                    <div class="space-y-1">
                        <button type="button" @click="openGroups.general = !openGroups.general" class="w-full flex items-center justify-between py-1.5 px-2 text-[#6F767E] dark:text-[#9A9FA5] hover:text-[#111827] uppercase tracking-wider font-bold text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">tune</span>
                                <span>General</span>
                            </span>
                            <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !openGroups.general }">expand_more</span>
                        </button>
                        <div x-show="openGroups.general" class="pl-4 space-y-1">
                            <button type="button" wire:click="setSection('site-features')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'site-features' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>Site features</span>
                                @if($allowIndexing)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Features Active"></span>
                                @endif
                            </button>
                            <button type="button" wire:click="setSection('site-basics')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'site-basics' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>Site basics</span>
                                @if($siteName !== '')
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Configured"></span>
                                @endif
                            </button>
                            <button type="button" wire:click="setSection('site-representation')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'site-representation' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>Site representation</span>
                                @if($orgName !== '')
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Configured"></span>
                                @endif
                            </button>
                            <button type="button" wire:click="setSection('site-connections')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'site-connections' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>Site connections</span>
                                @if($googleVerification !== '' || $bingVerification !== '' || $gskGa4TagId !== '')
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Connected"></span>
                                @endif
                            </button>
                            <button type="button" wire:click="setSection('breadcrumbs')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'breadcrumbs' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>Breadcrumbs</span>
                                @if($breadcrumbsEnabled)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Active"></span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Group 2: Content Types (Auto-Generated Submenus for Pages, Posts, CPTs) --}}
                    <div class="space-y-1">
                        <button type="button" @click="openGroups.content = !openGroups.content" class="w-full flex items-center justify-between py-1.5 px-2 text-[#6F767E] dark:text-[#9A9FA5] hover:text-[#111827] uppercase tracking-wider font-bold text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">article</span>
                                <span>Content Types</span>
                            </span>
                            <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !openGroups.content }">expand_more</span>
                        </button>
                        <div x-show="openGroups.content" class="pl-4 space-y-1">
                            @foreach($contentTypes as $slug => $typeConfig)
                                @php
                                    $isTabActive = ($activeSection === $slug || $activeSection === "content-type-{$slug}");
                                @endphp
                                <button type="button" wire:click="setSection('{{ $slug }}')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $isTabActive ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">{{ $typeConfig['icon'] ?? 'article' }}</span>
                                        <span>{{ $typeConfig['label'] }}</span>
                                    </span>
                                    @if($contentTypeSettings[$slug]['index_enabled'] ?? true)
                                        <span class="w-2 h-2 rounded-full bg-emerald-500" title="Indexed"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-amber-500" title="NoIndex"></span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Group 3: Categories & tags (Taxonomies Auto-Generated Submenus) --}}
                    <div class="space-y-1">
                        <button type="button" @click="openGroups.taxonomy = !openGroups.taxonomy" class="w-full flex items-center justify-between py-1.5 px-2 text-[#6F767E] dark:text-[#9A9FA5] hover:text-[#111827] uppercase tracking-wider font-bold text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">label</span>
                                <span>Categories & tags</span>
                            </span>
                            <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !openGroups.taxonomy }">expand_more</span>
                        </button>
                        <div x-show="openGroups.taxonomy" class="pl-4 space-y-1">
                            @foreach($taxonomies as $slug => $taxConfig)
                                @php
                                    $isTaxActive = ($activeSection === "taxonomy-{$slug}" || $activeSection === "tax-{$slug}");
                                @endphp
                                <button type="button" wire:click="setSection('taxonomy-{{ $slug }}')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $isTaxActive ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">{{ $taxConfig['icon'] ?? 'folder' }}</span>
                                        <span>{{ $taxConfig['label'] }}</span>
                                    </span>
                                    @if($taxonomySettings[$slug]['index_enabled'] ?? true)
                                        <span class="w-2 h-2 rounded-full bg-emerald-500" title="Indexed"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-amber-500" title="NoIndex"></span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Group 4: GEO & AI Search --}}
                    <div class="space-y-1">
                        <button type="button" @click="openGroups.geo = !openGroups.geo" class="w-full flex items-center justify-between py-1.5 px-2 text-[#6F767E] dark:text-[#9A9FA5] hover:text-[#111827] uppercase tracking-wider font-bold text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base text-purple-600">auto_awesome</span>
                                <span>GEO & AI Search</span>
                            </span>
                            <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !openGroups.geo }">expand_more</span>
                        </button>
                        <div x-show="openGroups.geo" class="pl-4 space-y-1">
                            <button type="button" wire:click="setSection('llms-txt')" class="w-full text-left py-2 px-3 rounded-xl transition-colors {{ $activeSection === 'llms-txt' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                LLMS.txt & AI Summary
                            </button>
                            <button type="button" wire:click="setSection('site-policies')" class="w-full text-left py-2 px-3 rounded-xl transition-colors {{ $activeSection === 'site-policies' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                Site policies (E-E-A-T)
                            </button>
                        </div>
                    </div>

                    {{-- Group 5: Advanced --}}
                    <div class="space-y-1">
                        <button type="button" @click="openGroups.advanced = !openGroups.advanced" class="w-full flex items-center justify-between py-1.5 px-2 text-[#6F767E] dark:text-[#9A9FA5] hover:text-[#111827] uppercase tracking-wider font-bold text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">build</span>
                                <span>Advanced</span>
                            </span>
                            <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !openGroups.advanced }">expand_more</span>
                        </button>
                        <div x-show="openGroups.advanced" class="pl-4 space-y-1">
                            <button type="button" wire:click="setSection('robots-txt')" class="w-full text-left py-2 px-3 rounded-xl transition-colors {{ $activeSection === 'robots-txt' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                Robots.txt directives
                            </button>
                            <button type="button" wire:click="setSection('indexnow')" class="w-full text-left py-2 px-3 rounded-xl transition-colors flex items-center justify-between {{ $activeSection === 'indexnow' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30]/50' }}">
                                <span>IndexNow Protocol</span>
                                @if($indexNowEnabled)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Active"></span>
                                @endif
                            </button>
                        </div>
                    </div>
                </nav>
            </aside>

            {{-- Right Main Content Area --}}
            <main class="flex-1 w-full min-w-0 bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 lg:p-8 shadow-sm space-y-8">
                {{-- Section 1: Site Features --}}
                @if($activeSection === 'site-features')
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site features</h2>
                            <p class="text-xs text-[#6F767E] mt-1">Tell us which features you want to use</p>
                        </div>

                        <div class="space-y-4">
                            {{-- Feature Card 1: Indexing --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-xl">travel_explore</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Search Engine Indexing</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">Allows search engine crawlers (Google, Bing) to discover and index public site content.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="allowIndexing" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- Feature Card 2: XML Sitemaps --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-xl">map</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">XML Sitemaps</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">Enable XML sitemap output at <code>/sitemap.xml</code> for search engines.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="sitemapEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- Feature Card 3: GEO / LLMS.txt --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-xl">smart_toy</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">GEO / LLMS.txt Protocol</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">AI Search Engine Optimization file served to Perplexity, ChatGPT Search, and Claude crawlers.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="llmsEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- Feature Card 4: OpenGraph Data --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-xl">share</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Open Graph Data</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">Add meta data for Facebook, LinkedIn, and social media post previews.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="openGraphEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section 2: Site Basics (Yoast-Style) --}}
                @if($activeSection === 'site-basics')
                    <div class="space-y-10">
                        {{-- 1. Site Info --}}
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site info</h2>
                                <p class="text-xs text-[#6F767E] mt-1">Set the basic info for your website used when configuring title patterns and search appearance.</p>
                            </div>

                            <div class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Website name</label>
                                    <input type="text" wire:model="siteName" placeholder="CTI CMS" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Alternate website name</label>
                                    <input type="text" wire:model="siteAlternateName" placeholder="Use the alternate website name for acronyms or a shorter version of your site's name." class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Use the alternate website name for acronyms (e.g. CTI) or a shorter version of your website name.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Tagline</label>
                                    <input type="text" wire:model="siteTagline" placeholder="Enterprise Content Management System" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">This field updates the site tagline used in search snippets.</p>
                                </div>

                                {{-- Title Separator Picker --}}
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Title separator</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($separators as $sep)
                                            <button type="button" wire:click="selectSeparator('{{ $sep }}')" class="w-10 h-10 rounded-xl text-sm font-bold transition-all flex items-center justify-center border {{ $titleSeparator === $sep ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border-transparent hover:bg-gray-200 dark:hover:bg-[#272B30]' }}">
                                                {{ $sep }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <p class="text-[11px] text-[#6F767E] mt-1">Chosen separator character: <code class="font-bold text-blue-600">{{ $titleSeparator }}</code>. Used in <code>{page} {sep} {site}</code> patterns.</p>
                                </div>

                                {{-- Site Image / Default OG Image --}}
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Site image (Default Social / OG Image)</label>
                                    
                                    <div class="p-6 rounded-2xl border-2 border-dashed border-gray-200 dark:border-[#272B30] bg-[#F4F5F6] dark:bg-[#0B0B0B] text-center space-y-4">
                                        @if($defaultOgImage)
                                            <div class="relative max-w-sm mx-auto group">
                                                <img src="{{ url($defaultOgImage) }}" alt="Site Image Preview" class="w-full h-40 object-cover rounded-xl shadow-sm">
                                                <button type="button" wire:click="removeOgImage" class="absolute top-2 right-2 p-1.5 rounded-full bg-rose-600 text-white shadow-md hover:bg-rose-700 transition-colors">
                                                    <span class="material-symbols-outlined text-sm">close</span>
                                                </button>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center">
                                                <span class="material-symbols-outlined text-2xl">image</span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">Recommended size for site image is 1200x675px</p>
                                                <p class="text-[11px] text-[#6F767E] mt-0.5">This image is used as a fallback for posts & pages that don't have a featured image set.</p>
                                            </div>
                                        @endif

                                        <button type="button" wire:click="openMediaPicker" class="px-5 py-2.5 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                                            <span class="material-symbols-outlined text-base">photo_library</span>
                                            <span>{{ $defaultOgImage ? 'Change Image' : 'Select Image' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 dark:border-[#272B30]">

                        {{-- 2. Site Preferences --}}
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site preferences</h2>
                                <p class="text-xs text-[#6F767E] mt-1">Configure author permissions and telemetry preferences.</p>
                            </div>

                            <div class="space-y-4">
                                <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Restrict advanced settings for authors</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">By default only editors and administrators can access the Advanced and Schema section of the SEO metabox.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" wire:model="restrictAdvancedSettings" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section 3: Site Representation (Yoast Layout with Social Profiles) --}}
                @if($activeSection === 'site-representation')
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site representation</h2>
                            <p class="text-xs text-[#6F767E] mt-1">This info is intended to appear in Google Knowledge Graph and AI search engines.</p>
                        </div>

                        {{-- 1. Entity Radio Switcher --}}
                        <div class="space-y-3">
                            <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization/person</label>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                                    <input type="radio" wire:model.live="orgType" value="Organization" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <span>Organization</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                                    <input type="radio" wire:model.live="orgType" value="Person" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <span>Person</span>
                                </label>
                            </div>
                        </div>

                        <hr class="border-gray-100 dark:border-[#272B30]">

                        {{-- 2. Organization Info --}}
                        <div class="space-y-5">
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Organization Info</h3>

                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization name</label>
                                    <input type="text" wire:model="orgName" placeholder="PT Computradetech Technology International" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Alternate organization name</label>
                                    <input type="text" wire:model="orgAlternateName" placeholder="CTI" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Use the alternate organization name for acronyms or a shorter version of your organization's name.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization logo</label>
                                    <input type="text" wire:model="orgLogo" placeholder="https://example.com/logo.png" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 dark:border-[#272B30]">

                        {{-- 3. Other Profiles (Social Profiles for Knowledge Graph) --}}
                        <div class="space-y-5">
                            <div>
                                <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Other profiles (Social Profiles)</h3>
                                <p class="text-xs text-[#6F767E] mt-0.5">Tell us if you have any other profiles on the web that belong to your organization for Google Knowledge Graph and AI search engines.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Facebook</label>
                                    <input type="url" wire:model="facebookUrl" placeholder="https://facebook.com/yourbrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">X (Twitter)</label>
                                    <input type="text" wire:model="twitterHandle" placeholder="https://x.com/yourbrand or @yourbrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">LinkedIn</label>
                                    <input type="url" wire:model="linkedinUrl" placeholder="https://linkedin.com/company/yourbrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Instagram</label>
                                    <input type="url" wire:model="instagramUrl" placeholder="https://instagram.com/yourbrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">YouTube</label>
                                    <input type="url" wire:model="youtubeUrl" placeholder="https://youtube.com/@yourbrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Wikipedia</label>
                                    <input type="url" wire:model="wikipediaUrl" placeholder="https://en.wikipedia.org/wiki/YourBrand" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 dark:border-[#272B30]">

                        {{-- 4. Additional Organization Info --}}
                        <div class="space-y-5">
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Additional organization info</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization description</label>
                                    <textarea wire:model="orgDescription" rows="3" placeholder="Brief summary of company products and services..." class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 resize-y"></textarea>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization email address</label>
                                    <input type="email" wire:model="orgEmail" placeholder="info@example.com" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization phone number</label>
                                    <input type="text" wire:model="orgPhone" placeholder="+62 21 555 1234" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Organization's legal name</label>
                                    <input type="text" wire:model="orgLegalName" placeholder="PT Computradetech Technology International" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section 4: Site Connections (Yoast Layout + Google Site Kit Integrations) --}}
                @if($activeSection === 'site-connections' || $activeSection === 'webmaster')
                    <div class="space-y-10">
                        {{-- Part A: Webmaster Verification Tags --}}
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site connections</h2>
                                <p class="text-xs text-[#6F767E] mt-1">Verify your site with different tools. This will add a verification meta tag to your homepage. You can find instructions on how to verify your site for each platform by following the link in the description.</p>
                            </div>

                            <div class="space-y-6">
                                {{-- 1. Ahrefs --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Ahrefs</label>
                                    <input type="text" wire:model="ahrefsVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Get your verification code in <a href="https://ahrefs.com/site-audit" target="_blank" class="text-blue-600 hover:underline font-semibold">Ahrefs</a>.</p>
                                </div>

                                {{-- 2. Baidu --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Baidu</label>
                                    <input type="text" wire:model="baiduVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Get your verification code in <a href="https://ziyuan.baidu.com" target="_blank" class="text-blue-600 hover:underline font-semibold">Baidu Webmaster tools</a>.</p>
                                </div>

                                {{-- 3. Bing --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Bing</label>
                                    <input type="text" wire:model="bingVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Get your verification code in <a href="https://www.bing.com/webmasters" target="_blank" class="text-blue-600 hover:underline font-semibold">Bing Webmaster tools</a>.</p>
                                </div>

                                {{-- 4. Google --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Google</label>
                                    <input type="text" wire:model="googleVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Get your verification code in <a href="https://search.google.com/search-console" target="_blank" class="text-blue-600 hover:underline font-semibold">Google Search console</a>.</p>
                                </div>

                                {{-- 5. Pinterest --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Pinterest</label>
                                    <input type="text" wire:model="pinterestVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Claim your site over at <a href="https://www.pinterest.com/settings/claim" target="_blank" class="text-blue-600 hover:underline font-semibold">Pinterest</a>.</p>
                                </div>

                                {{-- 6. Yandex --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] block">Yandex</label>
                                    <input type="text" wire:model="yandexVerification" placeholder="Add verification code" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <p class="text-[11px] text-[#6F767E]">Get your verification code in <a href="https://webmaster.yandex.com" target="_blank" class="text-blue-600 hover:underline font-semibold">Yandex Webmaster tools</a>.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 dark:border-[#272B30]">

                        {{-- Part B: Google Site Kit Tracking & Analytics Connections --}}
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Google Site Kit & Analytics Integrations</h3>
                                <p class="text-xs text-[#6F767E] mt-1">Configure Google Analytics 4, Tag Manager, Ads, and PageSpeed Insights API keys.</p>
                            </div>

                            <div class="space-y-5">
                                {{-- Code Injection Toggle --}}
                                <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable HTML Code Injection</h4>
                                        <p class="text-xs text-[#6F767E] mt-0.5">Automatically inject Google Analytics (gtag.js), Tag Manager (GTM), and Ads tracking scripts in website header.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" wire:model="gskEnabled" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                {{-- GA4 Tag ID --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Google Analytics 4 (GA4) Tag ID</label>
                                    <input type="text" wire:model="gskGa4TagId" placeholder="G-XXXXXXXXXX" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                {{-- GTM ID --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Google Tag Manager (GTM) ID</label>
                                    <input type="text" wire:model="gskGtmId" placeholder="GTM-XXXXXXX" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                {{-- Ads ID --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Google Ads Conversion ID</label>
                                    <input type="text" wire:model="gskAdsId" placeholder="AW-XXXXXXXXX" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                {{-- PageSpeed Insights API Key --}}
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">PageSpeed Insights API Key</label>
                                    <input type="text" wire:model="gskPagespeedApiKey" placeholder="Enter API Key for PageSpeed Insight metrics..." class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section: Breadcrumbs (Yoast 2026 Layout) --}}
                @if($activeSection === 'breadcrumbs')
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Breadcrumbs settings</h2>
                            <p class="text-xs text-[#6F767E] mt-1">Configure breadcrumb navigation appearance for theme views and automatic JSON-LD BreadcrumbList schema.</p>
                        </div>

                        {{-- 1. Enable Breadcrumbs Toggle --}}
                        <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable Breadcrumbs</h3>
                                <p class="text-xs text-[#6F767E] mt-0.5">Enable automatic BreadcrumbList JSON-LD schema in &lt;head&gt; and &lt;x-seo-breadcrumbs /&gt; component rendering in theme templates.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="breadcrumbsEnabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        {{-- 2. Breadcrumb Separator Picker --}}
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Breadcrumb Separator</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($breadcrumbSeparators as $sep)
                                    <button type="button" wire:click="selectBreadcrumbSeparator('{{ $sep }}')" class="w-10 h-10 rounded-xl text-sm font-bold transition-all flex items-center justify-center border {{ $breadcrumbSeparator === $sep ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border-transparent hover:bg-gray-200 dark:hover:bg-[#272B30]' }}">
                                        {{ $sep }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- 3. Anchor Text for Homepage --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Anchor text for the Homepage</label>
                            <input type="text" wire:model="breadcrumbHomeText" placeholder="Home" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            <p class="text-[11px] text-[#6F767E]">Text used for the first item linking back to homepage (e.g. Home, Beranda).</p>
                        </div>

                        {{-- 4. Prefix for Breadcrumb path --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Prefix for Breadcrumb path</label>
                            <input type="text" wire:model="breadcrumbPrefix" placeholder="You are here:" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            <p class="text-[11px] text-[#6F767E]">Optional prefix text displayed before the breadcrumbs path (leave blank for no prefix).</p>
                        </div>

                        {{-- 5. Primary Taxonomy for Posts --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Taxonomy to show in breadcrumbs for Posts</label>
                            <select wire:model="breadcrumbPostTaxonomy" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                <option value="categories">Categories (Default)</option>
                                <option value="tags">Tags</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        {{-- 6. Bold Last Page Toggle --}}
                        <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Bold the last page in breadcrumbs</h3>
                                <p class="text-xs text-[#6F767E] mt-0.5">Apply bold font weight to the current active page item in frontend breadcrumbs.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="breadcrumbBoldLast" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Section 5: Content Types (Yoast 2026 Layout per Content Type / CPT) --}}
                @foreach($contentTypes as $slug => $typeConfig)
                    @if($activeSection === $slug || $activeSection === "content-type-{$slug}")
                        <div class="space-y-8">
                            <div>
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $typeConfig['label'] }}</h2>
                                <p class="text-xs text-[#6F767E] mt-1">Configure default search appearance, indexing, and title templates for {{ strtolower($typeConfig['label']) }}.</p>
                            </div>

                            {{-- 1. Search Engine Indexing Toggle Card --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Show {{ strtolower($typeConfig['label']) }} in search results?</h3>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Allow search engines to discover and index {{ strtolower($typeConfig['label']) }} in Google and Bing search results.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="contentTypeSettings.{{ $slug }}.index_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- 2. SEO Title Pattern --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">SEO Title Pattern</label>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="contentTypeSettings.{{ $slug }}.title_pattern" placeholder="{title} {sep} {site}" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                {{-- Quick Insert Snippet Variables --}}
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[11px] font-semibold text-[#6F767E] mr-1">Insert Snippet Variable:</span>
                                    <button type="button" wire:click="insertSnippetVariable('{{ $slug }}', 'title_pattern', '{title}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Title
                                    </button>
                                    <button type="button" wire:click="insertSnippetVariable('{{ $slug }}', 'title_pattern', '{sep}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Separator
                                    </button>
                                    <button type="button" wire:click="insertSnippetVariable('{{ $slug }}', 'title_pattern', '{site}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Site Name
                                    </button>
                                    <button type="button" wire:click="insertSnippetVariable('{{ $slug }}', 'title_pattern', '{tagline}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Tagline
                                    </button>
                                    <button type="button" wire:click="insertSnippetVariable('{{ $slug }}', 'title_pattern', '{category}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Primary Category
                                    </button>
                                </div>
                            </div>

                            {{-- 3. Meta Description Pattern --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Meta Description Pattern</label>
                                <textarea wire:model="contentTypeSettings.{{ $slug }}.description_pattern" rows="3" placeholder="Enter default meta description snippet pattern..." class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 resize-y"></textarea>
                                <p class="text-[11px] text-[#6F767E]">Snippet pattern to use when a {{ strtolower($typeConfig['singular'] ?? 'item') }} does not have a custom meta description set.</p>
                            </div>

                            <hr class="border-gray-100 dark:border-[#272B30]">

                            {{-- 4. Default Schema.org Type --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Default Schema Type</label>
                                <select wire:model="contentTypeSettings.{{ $slug }}.schema_default" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    @foreach($schemaTypes as $schemaKey => $schemaLabel)
                                        <option value="{{ $schemaKey }}">{{ $schemaLabel }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Default structured data type describing {{ strtolower($typeConfig['label']) }} for search engine Knowledge Graph & Rich Snippets.</p>
                            </div>

                            <hr class="border-gray-100 dark:border-[#272B30]">

                            {{-- 5. Default Social / OpenGraph Image --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Default Social Image (Open Graph)</label>
                                
                                <div class="p-6 rounded-2xl border-2 border-dashed border-gray-200 dark:border-[#272B30] bg-[#F4F5F6] dark:bg-[#0B0B0B] text-center space-y-4">
                                    @if(!empty($contentTypeSettings[$slug]['social_image']))
                                        <div class="relative max-w-sm mx-auto group">
                                            <img src="{{ url($contentTypeSettings[$slug]['social_image']) }}" alt="{{ $typeConfig['label'] }} Preview Image" class="w-full h-40 object-cover rounded-xl shadow-sm">
                                            <button type="button" wire:click="removeContentTypeSocialImage('{{ $slug }}')" class="absolute top-2 right-2 p-1.5 rounded-full bg-rose-600 text-white shadow-md hover:bg-rose-700 transition-colors">
                                                <span class="material-symbols-outlined text-sm">close</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center">
                                            <span class="material-symbols-outlined text-2xl">image</span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">Recommended size for social image is 1200x675px</p>
                                            <p class="text-[11px] text-[#6F767E] mt-0.5">Used as a fallback when a {{ strtolower($typeConfig['singular'] ?? 'item') }} does not have a featured image set.</p>
                                        </div>
                                    @endif

                                    <button type="button" wire:click="openMediaPicker('content_type_{{ $slug }}')" class="px-5 py-2.5 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">photo_library</span>
                                        <span>{{ !empty($contentTypeSettings[$slug]['social_image']) ? 'Change Image' : 'Select Image' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Section 6: Categories & Tags (Taxonomies Yoast 2026 Layout per Taxonomy) --}}
                @foreach($taxonomies as $slug => $taxConfig)
                    @if($activeSection === "taxonomy-{$slug}" || $activeSection === "tax-{$slug}")
                        <div class="space-y-8">
                            <div>
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $taxConfig['label'] }}</h2>
                                <p class="text-xs text-[#6F767E] mt-1">Configure default search appearance, indexing, and title templates for {{ strtolower($taxConfig['label']) }} archive pages.</p>
                            </div>

                            {{-- 1. Search Engine Indexing Toggle Card --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Show {{ strtolower($taxConfig['label']) }} in search results?</h3>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Allow search engines to discover and index {{ strtolower($taxConfig['label']) }} archive pages in search results.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="taxonomySettings.{{ $slug }}.index_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- 2. SEO Title Pattern --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">SEO Title Pattern</label>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="taxonomySettings.{{ $slug }}.title_pattern" placeholder="{term} Archives {sep} {site}" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                </div>

                                {{-- Quick Insert Snippet Variables --}}
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[11px] font-semibold text-[#6F767E] mr-1">Insert Snippet Variable:</span>
                                    <button type="button" wire:click="insertTaxonomySnippetVariable('{{ $slug }}', 'title_pattern', '{term}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Term Name
                                    </button>
                                    <button type="button" wire:click="insertTaxonomySnippetVariable('{{ $slug }}', 'title_pattern', '{sep}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Separator
                                    </button>
                                    <button type="button" wire:click="insertTaxonomySnippetVariable('{{ $slug }}', 'title_pattern', '{site}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Site Name
                                    </button>
                                    <button type="button" wire:click="insertTaxonomySnippetVariable('{{ $slug }}', 'title_pattern', '{description}')" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold hover:bg-blue-100 transition-colors">
                                        + Term Description
                                    </button>
                                </div>
                            </div>

                            {{-- 3. Meta Description Pattern --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Meta Description Pattern</label>
                                <textarea wire:model="taxonomySettings.{{ $slug }}.description_pattern" rows="3" placeholder="Enter default meta description snippet pattern for {{ strtolower($taxConfig['label']) }}..." class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 resize-y"></textarea>
                                <p class="text-[11px] text-[#6F767E]">Snippet pattern to use when a term archive does not have a custom meta description set.</p>
                            </div>

                            <hr class="border-gray-100 dark:border-[#272B30]">

                            {{-- 4. Default Schema.org Type --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Default Schema Type</label>
                                <select wire:model="taxonomySettings.{{ $slug }}.schema_default" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    @foreach($schemaTypes as $schemaKey => $schemaLabel)
                                        <option value="{{ $schemaKey }}">{{ $schemaLabel }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Default structured data type describing {{ strtolower($taxConfig['label']) }} archive pages for search engine Knowledge Graph.</p>
                            </div>

                            <hr class="border-gray-100 dark:border-[#272B30]">

                            {{-- 5. Default Social / OpenGraph Image --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Default Social Image (Open Graph)</label>
                                
                                <div class="p-6 rounded-2xl border-2 border-dashed border-gray-200 dark:border-[#272B30] bg-[#F4F5F6] dark:bg-[#0B0B0B] text-center space-y-4">
                                    @if(!empty($taxonomySettings[$slug]['social_image']))
                                        <div class="relative max-w-sm mx-auto group">
                                            <img src="{{ url($taxonomySettings[$slug]['social_image']) }}" alt="{{ $taxConfig['label'] }} Preview Image" class="w-full h-40 object-cover rounded-xl shadow-sm">
                                            <button type="button" wire:click="removeTaxonomySocialImage('{{ $slug }}')" class="absolute top-2 right-2 p-1.5 rounded-full bg-rose-600 text-white shadow-md hover:bg-rose-700 transition-colors">
                                                <span class="material-symbols-outlined text-sm">close</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center">
                                            <span class="material-symbols-outlined text-2xl">image</span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">Recommended size for social image is 1200x675px</p>
                                            <p class="text-[11px] text-[#6F767E] mt-0.5">Used as a fallback for {{ strtolower($taxConfig['label']) }} archive pages when sharing on social media.</p>
                                        </div>
                                    @endif

                                    <button type="button" wire:click="openMediaPicker('taxonomy_{{ $slug }}')" class="px-5 py-2.5 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">photo_library</span>
                                        <span>{{ !empty($taxonomySettings[$slug]['social_image']) ? 'Change Image' : 'Select Image' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Section 7: GEO & AI --}}
                @if($activeSection === 'llms-txt')
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">LLMS.txt & AI Summary</h2>
                            <p class="text-xs text-[#6F767E] mt-1">Optimize how AI Search Engines (Perplexity, ChatGPT Search, Claude) index your brand.</p>
                        </div>

                        <div class="space-y-5">
                            {{-- Enable /llms.txt Endpoint Toggle Card --}}
                            <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-xl">smart_toy</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable /llms.txt Endpoint</h3>
                                        <p class="text-xs text-[#6F767E] mt-0.5">Serve structured plain-text file at <code>/llms.txt</code> for Perplexity, ChatGPT Search, and Claude AI crawlers.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model="llmsEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Site Summary for AI Crawlers</label>
                                <textarea wire:model="aiSummary" rows="4" class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-purple-500 p-4 resize-y" placeholder="Brief summary of company products, services, and core expertise for LLMs..."></textarea>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section: Site Policies / E-E-A-T Signals --}}
                @if($activeSection === 'site-policies')
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Site policies (E-E-A-T Signals)</h2>
                            <p class="text-xs text-[#6F767E] mt-1">Select pages on your website which contain information about your organizational and publishing policies for search engines and AI crawlers.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Policy 1 --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Publishing principles</label>
                                <select wire:model="publishingPrinciplesPageId" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <option value="0">None (Select a page)</option>
                                    @foreach($publishedPages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Select a page describing the editorial principles of your organization.</p>
                            </div>

                            {{-- Policy 2 --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Ownership / Funding info</label>
                                <select wire:model="ownershipFundingPageId" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <option value="0">None (Select a page)</option>
                                    @foreach($publishedPages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Select a page describing the ownership structure of your organization.</p>
                            </div>

                            {{-- Policy 3 --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Corrections policy</label>
                                <select wire:model="correctionsPolicyPageId" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <option value="0">None (Select a page)</option>
                                    @foreach($publishedPages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Select a page outlining your procedure for addressing errors & publishing corrections.</p>
                            </div>

                            {{-- Policy 4 --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Ethics policy</label>
                                <select wire:model="ethicsPolicyPageId" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <option value="0">None (Select a page)</option>
                                    @foreach($publishedPages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Select a page describing the personal and corporate standards of behavior.</p>
                            </div>

                            {{-- Policy 5 --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">Diversity policy</label>
                                <select wire:model="diversityPolicyPageId" class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                                    <option value="0">None (Select a page)</option>
                                    @foreach($publishedPages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-[#6F767E]">Select a page providing information about your diversity policies.</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section 8: Robots.txt --}}
                @if($activeSection === 'robots-txt')
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Robots.txt directives</h2>
                            <p class="text-xs text-[#6F767E] mt-1">Append custom directives verbatim to <code>/robots.txt</code></p>
                        </div>

                        <div class="space-y-1.5">
                            <textarea wire:model="robotsExtra" rows="10" placeholder="Disallow: /private/&#10;Crawl-delay: 10" class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-4 resize-y"></textarea>
                        </div>
                    </div>
                @endif

                {{-- Section 9: IndexNow Protocol --}}
                @if($activeSection === 'indexnow')
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">IndexNow Instant Indexing Protocol</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Bing / Yandex</span>
                            </div>
                            <p class="text-xs text-[#6F767E] mt-1">Automatically notify search engines the moment content is published, updated, or deleted.</p>
                        </div>

                        <div class="space-y-6">
                            {{-- Feature Toggles --}}
                            <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable IndexNow Protocol</h4>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Allows sending instant change notifications to IndexNow search engine partners.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="indexNowEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Automatic Submit on Publish & Update</h4>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Automatically trigger pings whenever Pages, Posts, or CPT entries are published or updated.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="indexNowAutoPing" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            {{-- API Key Management --}}
                            <div class="p-6 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] space-y-4">
                                <div>
                                    <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">IndexNow API Key & Verification Endpoint</h4>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Search engines verify domain ownership by fetching your key verification file.</p>
                                </div>

                                <div class="space-y-3">
                                    <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">API Key</label>
                                    <div class="flex items-center gap-3">
                                        <input type="text" value="{{ $indexNowKey }}" readonly class="flex-1 h-10 rounded-xl bg-white dark:bg-[#1A1A1A] border-none text-xs font-mono font-bold text-[#111827] dark:text-[#FCFCFC] px-4 ring-1 ring-gray-200 dark:ring-[#272B30]">
                                        <button type="button" wire:click="regenerateIndexNowKey" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-[#272B30] hover:bg-gray-300 dark:hover:bg-[#32383E] text-xs font-bold transition-colors">
                                            Regenerate Key
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-2 flex items-center justify-between text-xs">
                                    <span class="text-[#6F767E]">Verification File:</span>
                                    <a href="{{ url('/indexnow-' . $indexNowKey . '.txt') }}" target="_blank" class="text-blue-600 hover:underline font-bold inline-flex items-center gap-1">
                                        View Verification File (/indexnow-{{ substr($indexNowKey, 0, 8) }}....txt)
                                        <span class="material-symbols-outlined text-xs">open_in_new</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Manual URL Batch Submission --}}
                            <div class="p-6 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] space-y-4">
                                <div>
                                    <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Manual URL Submission</h4>
                                    <p class="text-xs text-[#6F767E] mt-0.5">Paste full URLs below (one per line) to immediately submit them to IndexNow API.</p>
                                </div>

                                <div class="space-y-3">
                                    <textarea wire:model="manualUrlsInput" rows="5" placeholder="https://example.com/page-1&#10;https://example.com/page-2" class="w-full rounded-2xl bg-white dark:bg-[#1A1A1A] border-none p-4 text-xs font-mono text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" wire:click="submitManualUrls" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">send</span>
                                        Submit URLs Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </form>

    {{-- Livewire Media Picker Component Modal Integration --}}
    @if(class_exists(\App\Livewire\Admin\Media\MediaPicker::class))
        <livewire:admin.media.media-picker />
    @endif
</div>
