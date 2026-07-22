<div class="space-y-8">
    {{-- Page Header --}}
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">SEO Overview & Health</h1>
        <p class="text-sm font-normal text-[#6F767E] dark:text-[#9A9FA5] mt-1">Monitor search engine indexation, site health, and quick actions</p>
    </div>

    {{-- Top Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white dark:bg-[#1A1A1A] p-6 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">travel_explore</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">Configured Meta</p>
                <h3 class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC] mt-0.5">{{ $configuredSeoCount }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A1A1A] p-6 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">description</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">Pages</p>
                <h3 class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC] mt-0.5">{{ $totalPages }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A1A1A] p-6 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">newspaper</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">Posts</p>
                <h3 class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC] mt-0.5">{{ $totalPosts }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A1A1A] p-6 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">layers</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">CPT Entries</p>
                <h3 class="text-2xl font-extrabold text-[#111827] dark:text-[#FCFCFC] mt-0.5">{{ $totalCptEntries }}</h3>
            </div>
        </div>
    </div>

    {{-- System Status & Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Health Status List --}}
        <div class="lg:col-span-2 bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                <div>
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Search Engine Readiness Checklist</h3>
                    <p class="text-xs text-[#6F767E] mt-0.5">Key site-wide indicators for search indexation</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                    Active
                </span>
            </div>

            <div class="space-y-4">
                {{-- Status Item 1 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                    <div class="p-2.5 rounded-xl shrink-0 {{ $allowIndexing ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400' }}">
                        <span class="material-symbols-outlined text-xl">{{ $allowIndexing ? 'check_circle' : 'block' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Search Engine Indexing</h4>
                            <span class="text-xs font-bold {{ $allowIndexing ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                {{ $allowIndexing ? 'Enabled (index, follow)' : 'Blocked (noindex)' }}
                            </span>
                        </div>
                        <p class="text-xs text-[#6F767E] mt-1">
                            {{ $allowIndexing ? 'Search engine crawlers are allowed to discover and index your public content.' : 'Indexing is currently disabled in site settings.' }}
                        </p>
                    </div>
                </div>

                {{-- Status Item 2 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                    <div class="p-2.5 rounded-xl shrink-0 {{ $sitemapEnabled ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-amber-100 text-amber-600' }}">
                        <span class="material-symbols-outlined text-xl">map</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">XML Sitemap</h4>
                            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                View /sitemap.xml <span class="material-symbols-outlined text-xs">open_in_new</span>
                            </a>
                        </div>
                        <p class="text-xs text-[#6F767E] mt-1">
                            Dynamic XML sitemap automatically lists all published pages, posts, and CPT entries.
                        </p>
                    </div>
                </div>

                {{-- Status Item 3 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                    <div class="p-2.5 rounded-xl shrink-0 {{ $llmsEnabled ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400' : 'bg-gray-200 text-gray-500' }}">
                        <span class="material-symbols-outlined text-xl">smart_toy</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">GEO / LLMS.txt Protocol</h4>
                            <a href="{{ url('/llms.txt') }}" target="_blank" class="text-xs font-bold text-purple-600 hover:underline inline-flex items-center gap-1">
                                View /llms.txt <span class="material-symbols-outlined text-xs">open_in_new</span>
                            </a>
                        </div>
                        <p class="text-xs text-[#6F767E] mt-1">
                            AI Search Engine Optimization file served to Perplexity, ChatGPT Search, and Claude crawlers.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Card --}}
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-5 shadow-sm">
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Quick Actions</h3>

            <div class="flex flex-col gap-3">
                <a wire:navigate href="{{ route('admin.seo.settings') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-blue-50/80 dark:hover:bg-blue-900/20 transition-all group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">tune</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC] block">General Settings</span>
                            <span class="text-[11px] text-[#6F767E] block">Title pattern, meta & schema</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all text-xl">chevron_right</span>
                </a>

                <a wire:navigate href="{{ route('admin.seo.redirects') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-emerald-50/80 dark:hover:bg-emerald-900/20 transition-all group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">sync_alt</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC] block">URL Redirects (301)</span>
                            <span class="text-[11px] text-[#6F767E] block">Manage 301/302 & regex rules</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all text-xl">chevron_right</span>
                </a>

                <a wire:navigate href="{{ route('admin.seo.settings', ['tab' => 'llms-txt']) }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-purple-50/80 dark:hover:bg-purple-900/20 transition-all group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">auto_awesome</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC] block">GEO & AI Optimization</span>
                            <span class="text-[11px] text-[#6F767E] block">llms.txt & E-E-A-T signals</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-all text-xl">chevron_right</span>
                </a>

                <a wire:navigate href="{{ route('admin.seo.bulk-editor') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-amber-50/80 dark:hover:bg-amber-900/20 transition-all group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">edit_note</span>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC] block">Bulk Meta Editor</span>
                            <span class="text-[11px] text-[#6F767E] block">Batch edit Titles & Descriptions</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400 group-hover:text-amber-600 group-hover:translate-x-1 transition-all text-xl">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
</div>
