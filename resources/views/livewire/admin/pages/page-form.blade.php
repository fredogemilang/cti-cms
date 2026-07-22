<div class="flex flex-col h-full" wire:poll.30s="autosave">
    {{-- Context Bar --}}
    <div class="flex items-center gap-3 px-6 py-4 md:px-10 border-b border-gray-200 dark:border-[#272B30] bg-white/50 dark:bg-[#0B0B0B]/50">
        <a class="h-9 w-9 flex items-center justify-center rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition-all shrink-0"
            href="{{ route('admin.pages.index') }}">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div class="flex items-center gap-3 min-w-0">
            <h1 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                {{ $isEdit ? 'Edit Page' : 'Add New Page' }}
            </h1>
            <div class="flex items-center gap-2 text-xs text-[#6F767E] shrink-0">
                @if($isSystemPage)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[10px]">shield</span>
                        System
                    </span>
                @endif
                @if($status === 'published')
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Published
                    </span>
                @elseif($status === 'draft')
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                        Draft
                    </span>
                @elseif($status === 'scheduled')
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Scheduled
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                        {{ ucfirst($status) }}
                    </span>
                @endif
                @if($lastSavedAt)
                    <span class="hidden sm:inline text-[11px] text-[#6F767E]">Saved {{ $lastSavedAt }}</span>
                @elseif($hasUnsavedChanges)
                    <span class="hidden sm:inline text-[11px] text-amber-500 font-medium">Unsaved changes</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- Left Panel: Editor --}}
        <div class="flex-1 overflow-y-auto p-10 no-scrollbar">
            <div class="max-w-4xl mx-auto space-y-10">
                {{-- Language tabs (only when more than one locale is configured) --}}
                @if(count($availableLocales) > 1)
                    @php
                        $localeLabels = ['id' => 'Bahasa Indonesia', 'en' => 'English', 'ja' => '日本語', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español', 'zh' => '中文'];
                        $defaultLocale = \App\Models\Page::defaultLocale();
                    @endphp
                    <div class="flex items-center gap-1 border-b border-gray-200 dark:border-[#272B30] -mb-px">
                        @foreach($availableLocales as $loc)
                            @php
                                $active = $loc === $editingLocale;
                                $hasContent = $loc === $defaultLocale
                                    ? true
                                    : !empty(($localizedSnapshots[$loc]['title'] ?? '') . ($localizedSnapshots[$loc]['slug'] ?? ''));
                            @endphp
                            <button
                                type="button"
                                wire:click="switchLocale('{{ $loc }}')"
                                @class([
                                    'flex items-center gap-2 px-4 py-2.5 text-sm font-bold transition border-b-2 -mb-px',
                                    'text-[#2563EB] border-[#2563EB]' => $active,
                                    'text-[#6F767E] border-transparent hover:text-[#111827] dark:hover:text-[#FCFCFC]' => !$active,
                                ])
                            >
                                <span class="material-symbols-outlined text-[16px]">{{ $loc === $defaultLocale ? 'star' : 'translate' }}</span>
                                {{ $localeLabels[$loc] ?? strtoupper($loc) }}
                                @if($hasContent)
                                    <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-[#2563EB]' : 'bg-emerald-500' }}"></span>
                                @endif
                            </button>
                        @endforeach
                        @if($editingLocale !== $defaultLocale)
                            <span class="ml-auto text-[11px] text-[#6F767E] py-2.5">
                                Editing translation for <strong>{{ $localeLabels[$editingLocale] ?? strtoupper($editingLocale) }}</strong> — leave blank to inherit from default.
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Title & Slug --}}
                <div class="space-y-4">
                    <input wire:model.live.debounce.500ms="title"
                        class="w-full bg-transparent border-none text-5xl font-extrabold text-[#111827] dark:text-[#FCFCFC] placeholder-gray-400 dark:placeholder-[#272B30] focus:ring-0 px-0"
                        placeholder="Enter Page Title..." type="text" />
                    @error('title')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    @if($slug)
                    <div class="flex items-center gap-2 text-xs font-bold text-[#6F767E] uppercase tracking-wider pl-1">
                        <span>PERMALINK:</span>
                        <span class="text-[#6F767E] lowercase font-normal">{{ url('/') }}/</span>
                        @if($isSystemPage)
                            <span class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#272B30] flex items-center gap-1">
                                {{ $slug }}
                                <span class="material-symbols-outlined text-[12px] text-amber-500" title="System page slug is locked">lock</span>
                            </span>
                        @else
                            <div x-data="{ editing: false }" class="relative flex items-center gap-2">
                                <span x-show="!editing" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#272B30]">{{ $slug }}</span>
                                <input x-show="editing" wire:model.blur="slug" @blur="editing = false" @keydown.enter="editing = false" type="text" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#2563EB] focus:outline-none w-auto min-w-[100px]" x-cloak>
                                <button @click="editing = !editing; $nextTick(() => $el.previousElementSibling.focus())" class="text-[#6F767E] hover:text-[#FCFCFC] transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    @endif
                    @error('slug')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content Builder --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Content Builder</h3>
                        <div class="text-xs text-[#6F767E]">{{ count($blocks) }} blocks</div>
                    </div>

                    <div class="builder-dropzone min-h-[400px] rounded-3xl p-8 flex flex-col gap-6 border border-gray-200 dark:border-[#272B30]/30"
                        style="background-image: radial-gradient(#E5E7EB 1px, transparent 1px); background-size: 24px 24px;"
                        x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                        :style="darkMode ? 'background-image: radial-gradient(#272B30 1px, transparent 1px)' : ''">

                        @forelse($blocks as $index => $block)
                            @include('livewire.admin.pages.blocks._block-wrapper', ['index' => $index, 'block' => $block])
                        @empty
                            <div class="text-center py-12 text-[#6F767E]">
                                <span class="material-symbols-outlined text-5xl mb-4 block opacity-30">widgets</span>
                                <p class="font-medium">No blocks yet</p>
                                <p class="text-sm">Click "Add Block" to start building your page</p>
                            </div>
                        @endforelse

                        @php $isDefaultLocaleEditing = $editingLocale === \App\Models\Page::defaultLocale(); @endphp

                        {{-- Add Block Button (only on default locale to keep structure consistent) --}}
                        @if($isDefaultLocaleEditing)
                            <button wire:click="openBlockSelector"
                                class="w-full h-16 rounded-2xl border-2 border-dashed border-gray-300 dark:border-[#272B30] hover:border-primary/50 text-[#6F767E] hover:text-primary transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">add_circle</span>
                                <span class="font-bold">Add Block</span>
                            </button>
                        @else
                            <div class="w-full p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 flex items-start gap-3">
                                <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">info</span>
                                <div class="flex-1 text-sm">
                                    <p class="font-bold text-amber-900 dark:text-amber-300">Translating mode</p>
                                    <p class="text-amber-800 dark:text-amber-400/80 text-xs mt-0.5">
                                        Block structure (add / remove / reorder) is managed from the default locale only. Switch to the default tab to change the layout.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @error('blocks')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SEO Settings (Centralized Component) --}}
                <livewire:admin.seo.seo-meta-box
                    seoable-type="App\Models\Page"
                    :seoable-id="$pageId"
                    :locale="$editingLocale"
                    :key="'seo-page-' . ($pageId ?? 'new')"
                />
            </div>
        </div>

        {{-- Right Panel: Settings --}}
        <aside class="w-[320px] bg-[#F4F5F6] dark:bg-[#0B0B0B] border-l border-gray-200 dark:border-[#272B30] overflow-y-auto no-scrollbar hidden lg:block">
            <div class="p-6 space-y-6">
                {{-- Action Buttons --}}
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">rocket_launch</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Actions</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button wire:click="publish" wire:loading.attr="disabled"
                            class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading.remove wire:target="publish">
                                <span class="material-symbols-outlined text-lg">publish</span>
                                Publish
                            </span>
                            <span wire:loading wire:target="publish">Publishing...</span>
                        </button>
                        <button wire:click="saveAsDraft" wire:loading.attr="disabled"
                            class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white bg-gray-50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#272B30] border border-gray-200 dark:border-[#272B30] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveAsDraft">
                                <span class="material-symbols-outlined text-lg">save</span>
                                Save Draft
                            </span>
                            <span wire:loading wire:target="saveAsDraft">Saving...</span>
                        </button>
                        @if($isEdit)
                        <a href="{{ url($slug) }}" target="_blank"
                            class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                            Preview
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Page Settings Card --}}
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none" x-data="{ editingStatus: false, editingTemplate: false, editingParent: false }">
                    <div class="flex items-center gap-2 mb-6 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">tune</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Page Settings</span>
                    </div>

                    <div class="space-y-4">
                        {{-- Status --}}
                        <div class="group">
                            <div class="flex items-center justify-between" x-show="!editingStatus">
                                <span class="text-sm text-[#6F767E]">Status:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ ucfirst($status) }}</span>
                                    <button @click="editingStatus = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingStatus" class="bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <select wire:model="status" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="private">Private</option>
                                </select>
                                <div class="flex justify-end">
                                    <button @click="editingStatus = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>

                        {{-- Publish Date --}}
                        @if($status === 'scheduled' || $status === 'published')
                        <div class="group" x-data="{ editingPublish: false }">
                            <div class="flex items-center justify-between" x-show="!editingPublish">
                                <span class="text-sm text-[#6F767E]">Publish:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">
                                        {{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('M d, Y H:i') : 'Immediately' }}
                                    </span>
                                    <button @click="editingPublish = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingPublish" class="bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <input wire:model="publishedAt" type="datetime-local"
                                    class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                <div class="flex justify-end">
                                    <button @click="editingPublish = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Template --}}
                        <div class="group">
                            <div class="flex items-center justify-between" x-show="!editingTemplate">
                                <span class="text-sm text-[#6F767E]">Template:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $templates[$template] ?? ucfirst($template) }}</span>
                                    @if($isSystemPage)
                                        <span class="material-symbols-outlined text-[12px] text-amber-500" title="System page template is locked">lock</span>
                                    @else
                                        <button @click="editingTemplate = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                    @endif
                                </div>
                            </div>
                            @if(!$isSystemPage)
                            <div x-show="editingTemplate" class="bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <select wire:model="template" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    @foreach($templates as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="flex justify-end">
                                    <button @click="editingTemplate = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Parent Page --}}
                        <div class="group">
                            <div class="flex items-center justify-between" x-show="!editingParent">
                                <span class="text-sm text-[#6F767E]">Parent:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $parentId ? ($parentPages->find($parentId)->title ?? 'Unknown') : 'Top Level' }}</span>
                                    <button @click="editingParent = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingParent" class="bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <select wire:model="parentId" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    <option value="">None (Top Level)</option>
                                    @foreach($parentPages as $parentPage)
                                        <option value="{{ $parentPage->id }}">{{ $parentPage->title }}</option>
                                    @endforeach
                                </select>
                                <div class="flex justify-end">
                                    <button @click="editingParent = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Order --}}
                        <div class="group" x-data="{ editingOrder: false }">
                            <div class="flex items-center justify-between" x-show="!editingOrder">
                                <span class="text-sm text-[#6F767E]">Menu Order:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $menuOrder ?? 0 }}</span>
                                    <button @click="editingOrder = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingOrder" class="bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <input wire:model="menuOrder" type="number" min="0"
                                    class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                <div class="flex justify-end">
                                    <button @click="editingOrder = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete action --}}
                    @if($isEdit && !$isSystemPage)
                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-[#272B30] flex items-center justify-end">
                        <button wire:click="delete" wire:confirm="Are you sure you want to delete this page?" class="text-xs font-bold text-[#FF6A55] hover:text-[#ff4f38] transition-colors">
                            Move to Trash
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Featured Image Card --}}
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">image</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Featured Image</span>
                        </div>
                        @if($featuredImage)
                            <button wire:click="clearFeaturedImage" class="text-xs font-bold text-[#FF6A55] hover:text-[#ff4f38] transition-colors">Clear</button>
                        @endif
                    </div>

                    @if($featuredImage)
                        <div class="relative aspect-video w-full rounded-xl overflow-hidden border border-gray-200 dark:border-[#272B30]">
                            <img src="{{ asset('storage/' . $featuredImage) }}" alt="Featured" class="w-full h-full object-cover" />
                            <button wire:click="openMediaPicker('featured_image')"
                                class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center text-white font-bold text-sm">
                                <span class="material-symbols-outlined mr-1">swap_horiz</span>
                                Change Image
                            </button>
                        </div>
                    @else
                        <div wire:click="openMediaPicker('featured_image')"
                            class="aspect-video w-full rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border-2 border-dashed border-gray-200 dark:border-[#272B30] flex flex-col items-center justify-center gap-2 hover:border-[#2563EB] hover:bg-blue-50/50 dark:hover:bg-[#1A1A1A] transition-all cursor-pointer group">
                            <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-[#272B30] group-hover:text-[#2563EB] transition-colors">add_photo_alternate</span>
                            <span class="text-[10px] font-bold text-[#6F767E] uppercase group-hover:text-[#2563EB] transition-colors">Select Featured Image</span>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    {{-- Block Selector Modal --}}
    @if($showBlockSelector)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm"
        x-data x-on:keydown.escape.window="$wire.closeBlockSelector()">
        <div class="w-full max-w-[640px] bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-[32px] shadow-2xl flex flex-col max-h-[90vh]"
            x-on:click.outside="$wire.closeBlockSelector()">
            <div class="flex items-center justify-between p-8 border-b border-gray-100 dark:border-[#272B30]">
                <div>
                    <h3 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Add Block</h3>
                    <p class="text-sm text-[#6F767E]">Select a field type to add to your content</p>
                </div>
                <button wire:click="closeBlockSelector"
                    class="h-10 w-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-8 no-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($blockTypes as $type => $config)
                        <button wire:click="addBlock('{{ $type }}')"
                            class="group flex flex-col items-center gap-3 p-4 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] hover:bg-gray-50 dark:hover:bg-[#272B30] hover:border-primary transition-all">
                            <div class="h-12 w-12 rounded-xl {{ $colorClasses[$config['color']] ?? 'bg-gray-500/10 text-gray-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-2xl">{{ $config['icon'] }}</span>
                            </div>
                            <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $config['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="p-8 border-t border-gray-100 dark:border-[#272B30] flex justify-end">
                <button wire:click="closeBlockSelector"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-[#111827] dark:text-[#FCFCFC] bg-gray-100 dark:bg-[#272B30] hover:brightness-95 transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Media Picker Modal --}}
    @if($showMediaPicker)
        <livewire:admin.media-picker :field="$mediaPickerField" :show-modal="true" :key="'page-media-picker-'.$mediaPickerField" />
    @endif
</div>
