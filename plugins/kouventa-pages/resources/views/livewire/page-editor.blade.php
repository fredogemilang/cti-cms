<div class="space-y-6" wire:poll.30s="autosave">

    {{-- Top Action Bar --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-[#1A1A1A] p-4 sm:p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kouventa-pages.index') }}" class="h-10 w-10 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $title }}</h1>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-gray-100 dark:bg-[#272B30] text-[#6F767E]">/{{ $slug }}</span>
                </div>
                <div class="text-xs text-[#6F767E] mt-0.5 flex items-center gap-2">
                    <span>Editing for: <strong class="text-[#111827] dark:text-[#FCFCFC] uppercase">{{ $editingLocale }}</strong></span>
                    <span>•</span>
                    <span class="text-emerald-600 dark:text-emerald-400">Autosave active (30s)</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Language Switcher --}}
            <div class="inline-flex items-center bg-gray-100 dark:bg-[#0B0B0B] p-1 rounded-xl ring-1 ring-gray-200 dark:ring-[#272B30]">
                <button type="button" wire:click="setLocale('en')" class="h-9 px-3.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $editingLocale === 'en' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                    <span>🇺🇸</span>
                    <span>English</span>
                </button>
                <button type="button" wire:click="setLocale('id')" class="h-9 px-3.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $editingLocale === 'id' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                    <span>🇮🇩</span>
                    <span>Indonesia</span>
                </button>
            </div>

            @if($editingLocale === 'id')
                <button type="button" wire:click="copyFromEn" class="h-10 px-3.5 rounded-xl border border-dashed border-gray-300 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-1.5 text-xs font-bold transition-all" title="Copy default content from English">
                    <span class="material-symbols-outlined text-sm">content_copy</span>
                    <span>Copy from EN</span>
                </button>
            @endif

            <a href="{{ $page->getUrl($editingLocale) }}?preview=1" target="_blank" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-1.5 text-xs font-bold transition-all">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span>Preview Draft</span>
            </a>

            <button type="button" wire:click="save" class="h-10 px-5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-sm">save</span>
                <span wire:loading wire:target="save" class="animate-spin h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    {{-- Main Editor Body (Tabs + Content) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left: Section Tabs Navigation --}}
        <div class="lg:col-span-3 bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] p-3 shadow-sm sticky top-6 space-y-1">
            <div class="px-3 py-2 text-xs font-bold text-[#6F767E] uppercase tracking-wider">
                Page Sections
            </div>
            @foreach($tabs as $tab)
                <button type="button"
                    wire:click="setTab('{{ $tab['id'] }}')"
                    class="w-full px-3.5 py-3 rounded-xl text-left text-xs font-bold flex items-center gap-2.5 transition-all {{ $activeTab === $tab['id'] ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30]/30' }}">
                    <span class="material-symbols-outlined text-base {{ $activeTab === $tab['id'] ? 'text-[#2563EB]' : 'text-[#6F767E]' }}">{{ $tab['icon'] }}</span>
                    <span class="truncate">{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Right: Active Section Form --}}
        <div class="lg:col-span-9 bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] p-6 shadow-sm space-y-6">

            @if($activeTab === 'seo')
                {{-- SEO & Settings Tab --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Page Settings & SEO</h2>
                            <p class="text-xs text-[#6F767E] mt-0.5">Control publication state, URL slug, and complete search engine optimization tags.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Page Title</label>
                            <input type="text" wire:model.defer="title" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-transparent text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Publication Status</label>
                            <select wire:model.defer="status" class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="published">Published (Live)</option>
                                <option value="draft">Draft (Admin Only)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Core SEO Meta Box --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-[#272B30]">
                        <livewire:admin.seo.seo-meta-box
                            :seoable-type="\Plugins\KouventaPages\Models\KouventaPage::class"
                            :seoable-id="$page->id"
                            :locale="$editingLocale"
                            wire:key="'seo-box-'.$page->id.'-'.$editingLocale"
                        />
                    </div>

                    {{-- Revision History --}}
                    <div class="pt-6 border-t border-gray-100 dark:border-[#272B30] space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Revision History (Undo / Restore)</h3>
                            <span class="text-xs text-[#6F767E]">Up to 20 snapshots</span>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-[#272B30] border border-gray-100 dark:border-[#272B30] rounded-xl overflow-hidden">
                            @forelse($revisions as $rev)
                                <div class="p-3.5 flex items-center justify-between text-xs hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors">
                                    <div>
                                        <div class="font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $rev['summary'] }}</div>
                                        <div class="text-[#6F767E] mt-0.5">{{ $rev['created_at'] }} by {{ $rev['user'] }}</div>
                                    </div>
                                    <button type="button"
                                        wire:click="restoreRevision({{ $rev['id'] }})"
                                        wire:confirm="Restore this snapshot? Any unsaved edits will be replaced."
                                        class="h-8 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] font-bold text-xs transition-colors">
                                        Restore
                                    </button>
                                </div>
                            @empty
                                <div class="p-6 text-center text-xs text-[#6F767E]">
                                    No previous revisions recorded yet. Revisions are created automatically upon save.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                {{-- Content Section Schemas --}}
                @if($page->template === 'product-ai' || $page->slug === 'product/ai')
                    @include('kouventa-pages::livewire.schemas.product-ai')
                @endif
            @endif

        </div>

    </div>

</div>

