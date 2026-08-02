<div class="flex flex-col h-full overflow-hidden">
    {{-- Context Bar --}}
    <div class="flex items-center gap-3 px-6 py-4 md:px-10 border-b border-gray-200 dark:border-[#272B30] bg-white/50 dark:bg-[#0B0B0B]/50">
        <a class="h-9 w-9 flex items-center justify-center rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition-all shrink-0"
            href="{{ route('admin.cpt.entries.index', $postType->slug) }}">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div class="flex items-center gap-3 min-w-0">
            <h1 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                {{ $isEdit ? 'Edit' : 'Add New' }} {{ $postType->singular_label }}
            </h1>
            <div class="flex items-center gap-2 text-xs text-[#6F767E] shrink-0">
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
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Center Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">
            <div class="max-w-4xl mx-auto space-y-10">
                {{-- Language tabs (only when more than one locale is configured) --}}
                @if(count($availableLocales) > 1)
                    @php
                        $localeLabels = ['id' => 'Bahasa Indonesia', 'en' => 'English', 'ja' => '日本語', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español', 'zh' => '中文'];
                        $defaultLocale = \App\Models\CptEntry::defaultLocale();
                    @endphp
                    <div class="flex items-center gap-1 border-b border-gray-200 dark:border-[#272B30] -mb-px">
                        @foreach($availableLocales as $loc)
                            @php
                                $active = $loc === $editingLocale;
                                $hasContent = $loc === $defaultLocale
                                    ? true
                                    : !empty(($localizedSnapshots[$loc]['title'] ?? '') . ($localizedSnapshots[$loc]['content'] ?? ''));
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
                                Editing translation for <strong>{{ $localeLabels[$editingLocale] ?? strtoupper($editingLocale) }}</strong> — title, content, excerpt, and meta fields are translated per locale.
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Global Validation Errors Alert --}}
                @if ($errors->any())
                    <div class="rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 p-5 shadow-sm">
                        <div class="flex items-center gap-2 text-red-800 dark:text-red-300 font-bold mb-2 text-sm">
                            <span class="material-symbols-outlined text-lg">error</span>
                            <span>Validation errors occurred:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Title & Permalink -->
                <div class="space-y-4">
                    <input wire:model.blur="title"
                        class="w-full bg-transparent border-none text-4xl md:text-5xl font-extrabold text-[#111827] dark:text-[#FCFCFC] placeholder-gray-400 dark:placeholder-[#272B30] focus:ring-0 focus:outline-none shadow-none focus:shadow-none px-0 @error('title') text-red-500 placeholder-red-300 @enderror"
                        placeholder="Enter Title..." type="text" />
                    
                    @error('title')
                        <p class="text-sm text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                    
                    @if($slug)
                    <div class="flex items-center gap-2 text-xs font-bold text-[#6F767E] uppercase tracking-wider pl-1">
                        <span>PERMALINK:</span>
                        <span class="text-[#6F767E] lowercase font-normal">{{ $permalinkPrefix }}</span>
                        <div x-data="{ editing: false }" class="relative flex items-center gap-2">
                            <span x-show="!editing" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#272B30]">{{ $slug }}</span>
                            <input x-show="editing" wire:model.blur="slug" @blur="editing = false" @keydown.enter="editing = false" type="text" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#2563EB] focus:outline-none w-auto min-w-[100px]" x-cloak>
                            <button @click="editing = !editing; $nextTick(() => $el.previousElementSibling.focus())" class="text-[#6F767E] hover:text-[#FCFCFC] transition-colors">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                            </button>
                            <button type="button" wire:click="generateSlug" class="text-[#6F767E] hover:text-[#FCFCFC] transition-colors" title="Regenerate slug">
                                <span class="material-symbols-outlined text-[14px]">refresh</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Content Editor -->
                @if(in_array('editor', $postType->supports ?? []))
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Content</h3>
                    </div>
                    <div wire:ignore x-data="tiptapEditor('content')" 
                         @tiptap-undo.window="undo()" 
                         @tiptap-redo.window="redo()"
                         id="cpt-content-editor" class="h-[600px] min-h-[500px] rounded-3xl border border-gray-200 dark:border-[#272B30]/30 bg-white dark:bg-[#1A1A1A] flex flex-col overflow-hidden shadow-sm">

                        <!-- Toolbar -->
                        <div class="flex items-center gap-1 p-2 border-b border-gray-200 dark:border-[#272B30] overflow-x-auto flex-wrap shrink-0 bg-white dark:bg-[#1A1A1A] rounded-t-3xl">
                            <!-- Text Formatting -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="toggleBold()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('bold') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Bold">
                                    <span class="material-symbols-outlined text-[20px]">format_bold</span>
                                </button>
                                <button type="button" @click="toggleItalic()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('italic') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Italic">
                                    <span class="material-symbols-outlined text-[20px]">format_italic</span>
                                </button>
                                <button type="button" @click="toggleStrike()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('strike') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Strike">
                                    <span class="material-symbols-outlined text-[20px]">strikethrough_s</span>
                                </button>
                                <button type="button" @click="toggleCodeBlock()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('codeBlock') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Code Block">
                                    <span class="material-symbols-outlined text-[20px]">code</span>
                                </button>
                            </div>
                            
                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Headings -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="toggleHeading(2)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 2 }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 2">
                                    <span class="material-symbols-outlined text-[20px]">format_h2</span>
                                </button>
                                <button type="button" @click="toggleHeading(3)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 3 }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 3">
                                    <span class="material-symbols-outlined text-[20px]">format_h3</span>
                                </button>
                            </div>

                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Alignment -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="setTextAlign('left')" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive({ textAlign: 'left' }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Align Left">
                                    <span class="material-symbols-outlined text-[20px]">format_align_left</span>
                                </button>
                                <button type="button" @click="setTextAlign('center')" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive({ textAlign: 'center' }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Align Center">
                                    <span class="material-symbols-outlined text-[20px]">format_align_center</span>
                                </button>
                                <button type="button" @click="setTextAlign('right')" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive({ textAlign: 'right' }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Align Right">
                                    <span class="material-symbols-outlined text-[20px]">format_align_right</span>
                                </button>
                                <button type="button" @click="setTextAlign('justify')" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive({ textAlign: 'justify' }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Justify">
                                    <span class="material-symbols-outlined text-[20px]">format_align_justify</span>
                                </button>
                            </div>

                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Lists & Indent -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="toggleBulletList()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('bulletList') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Bullet List">
                                    <span class="material-symbols-outlined text-[20px]">format_list_bulleted</span>
                                </button>
                                <button type="button" @click="toggleOrderedList()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('orderedList') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Ordered List">
                                    <span class="material-symbols-outlined text-[20px]">format_list_numbered</span>
                                </button>
                            </div>

                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Insert -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="setLink()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('link') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Link">
                                    <span class="material-symbols-outlined text-[20px]">link</span>
                                </button>
                                <button type="button" @click="openMediaPicker()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Image from Media Library">
                                    <span class="material-symbols-outlined text-[20px]">image</span>
                                </button>
                                <button type="button" @click="toggleBlockquote()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('blockquote') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Blockquote">
                                    <span class="material-symbols-outlined text-[20px]">format_quote</span>
                                </button>
                                <button type="button" @click="setHorizontalRule()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Horizontal Rule">
                                    <span class="material-symbols-outlined text-[20px]">horizontal_rule</span>
                                </button>
                            </div>

                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Clear & History -->
                            <div class="flex items-center gap-0.5">
                                <button type="button" @click="clearFormatting()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Clear Formatting">
                                    <span class="material-symbols-outlined text-[20px]">format_clear</span>
                                </button>
                                <button type="button" @click="undo()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Undo">
                                    <span class="material-symbols-outlined text-[20px]">undo</span>
                                </button>
                                <button type="button" @click="redo()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Redo">
                                    <span class="material-symbols-outlined text-[20px]">redo</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Editor Area -->
                        <div x-ref="editor" class="flex-1 overflow-y-auto cursor-text relative"></div>
                    </div>
                </div>
                @endif

                <!-- Excerpt -->
                @if(in_array('excerpt', $postType->supports ?? []))
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Excerpt</h3>
                    </div>
                    <textarea wire:model="excerpt" rows="3" 
                        class="w-full rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] p-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent resize-none"
                        placeholder="Write a short description..."></textarea>
                </div>
                @endif

                <!-- Meta Boxes (Normal & Advanced) -->
                <!-- Meta Boxes (Normal & Advanced) -->
                <div class="space-y-6">
                    @php
                        $normalBoxes = collect($metaBoxes)->filter(fn($box) => $box['context'] === 'normal' && isset($groupedFields[$box['id']]));
                        $advancedBoxes = collect($metaBoxes)->filter(fn($box) => $box['context'] === 'advanced' && isset($groupedFields[$box['id']]));
                    @endphp

                    {{-- Normal Context - Tabbed Layout --}}
                    @if($normalBoxes->isNotEmpty())
                        <div x-data="{ activeTab: @entangle('activeTab').live }" class="bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-200 dark:border-[#272B30] overflow-hidden">
                            {{-- Tabs Header --}}
                            <div class="flex overflow-x-auto no-scrollbar border-b border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B]/20">
                                @foreach($normalBoxes as $box)
                                    <button 
                                        type="button"
                                        @click="activeTab = '{{ $box['id'] }}'"
                                        :class="activeTab === '{{ $box['id'] }}' ? 'text-blue-600 border-b-2 border-blue-600 bg-white dark:bg-[#1A1A1A]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white border-b-2 border-transparent'"
                                        class="px-6 py-4 text-sm font-bold uppercase tracking-widest whitespace-nowrap transition-all"
                                    >
                                        {{ $box['title'] }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Tabs Content --}}
                            <div>
                                @foreach($normalBoxes as $box)
                                    <div x-show="activeTab === '{{ $box['id'] }}'" style="display: none;" class="p-6 space-y-4">
                                        {{-- Add x-cloak handling via style or class if needed, but style display:none works with x-show for initial load if js not ready --}}
                                        @foreach($groupedFields[$box['id']] as $field)
                                            @include('livewire.admin.cpt.entries.partials.field-render', ['field' => $field])
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Advanced Context - Stacked Layout --}}
                    @foreach($advancedBoxes as $box)
                        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-200 dark:border-[#272B30] overflow-hidden">
                            <div class="border-b border-gray-200 dark:border-[#272B30] px-6 py-4 bg-gray-50/50 dark:bg-[#0B0B0B]/20">
                                <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">{{ $box['title'] }}</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                @foreach($groupedFields[$box['id']] as $field)
                                    @include('livewire.admin.cpt.entries.partials.field-render', ['field' => $field])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(isset($groupedFields['default']))
                    <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-200 dark:border-[#272B30] overflow-hidden">
                        <div class="border-b border-gray-200 dark:border-[#272B30] px-6 py-4 bg-gray-50/50 dark:bg-[#0B0B0B]/20">
                            <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Custom Fields</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($groupedFields['default'] as $field)
                                @include('livewire.admin.cpt.entries.partials.field-render', ['field' => $field])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($postType->publicly_queryable)
                {{-- SEO Settings (Centralized Component) --}}
                <livewire:admin.seo.seo-meta-box
                    seoable-type="App\Models\CptEntry"
                    :seoable-id="$entryId"
                    :locale="$editingLocale"
                    :key="'seo-entry-' . ($entryId ?? 'new')"
                />
                @endif
            </div>
        </div>

        <!-- Right Sidebar -->
        <aside class="w-[320px] bg-[#F4F5F6] dark:bg-[#0B0B0B] border-l border-gray-200 dark:border-[#272B30] overflow-y-auto no-scrollbar hidden lg:block">
            <div class="p-6 space-y-6">
                {{-- Action Buttons --}}
                <!-- Unified Publishing & Actions Card (Optimized for Narrow Sidebar) -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none space-y-5">
                    
                    <!-- Title / Header (Aligned with Featured Image card) -->
                    <div class="flex items-center gap-2 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">rocket_launch</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Publishing & Actions</span>
                    </div>

                    <!-- Main Primary Submit / Action Button (Top Highlight) -->
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <span wire:loading.remove class="flex items-center gap-2">
                            @if($status === 'scheduled')
                                <span class="material-symbols-outlined text-lg">event</span>
                                <span>Schedule Entry</span>
                            @elseif($status === 'published')
                                <span class="material-symbols-outlined text-lg">published_with_changes</span>
                                <span>Update Entry</span>
                            @elseif($status === 'archived')
                                <span class="material-symbols-outlined text-lg">archive</span>
                                <span>Archive Entry</span>
                            @else
                                <span class="material-symbols-outlined text-lg">publish</span>
                                <span>Publish Entry</span>
                            @endif
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg animate-spin">sync</span>
                            <span>Saving...</span>
                        </span>
                    </button>

                    <!-- Secondary Actions Row (Save Draft & Preview) -->
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button" 
                            wire:click="saveAsDraft" 
                            wire:loading.attr="disabled"
                            class="w-full py-2 px-3 rounded-xl text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white bg-gray-50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#272B30] border border-gray-200 dark:border-[#272B30] transition-all flex items-center justify-center gap-1.5 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveAsDraft" class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">save</span>
                                <span>Save Draft</span>
                            </span>
                            <span wire:loading wire:target="saveAsDraft" class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base animate-spin">sync</span>
                                <span>Saving...</span>
                            </span>
                        </button>

                        @if($isEdit && $previewUrl)
                        <a 
                            href="{{ $previewUrl }}" 
                            target="_blank"
                            class="w-full py-2 px-3 rounded-xl text-xs font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white bg-gray-50 dark:bg-[#0B0B0B] hover:bg-gray-100 dark:hover:bg-[#272B30] border border-gray-200 dark:border-[#272B30] transition-all flex items-center justify-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-base">visibility</span>
                            <span>Preview</span>
                        </a>
                        @else
                        <div class="w-full py-2 px-3 rounded-xl text-xs font-semibold text-gray-400 bg-gray-50/50 dark:bg-[#0B0B0B]/50 border border-dashed border-gray-200 dark:border-[#272B30] flex items-center justify-center gap-1.5 opacity-50 cursor-not-allowed">
                            <span class="material-symbols-outlined text-base">visibility_off</span>
                            <span>Preview</span>
                        </div>
                        @endif
                    </div>

                    <!-- Publishing Controls Box (Status Dropdown & Schedule Date Picker) -->
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5 space-y-4">
                        
                        <!-- Status Selector Field -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400">key</span>
                                <span>Status</span>
                            </label>
                            <select 
                                wire:model.live="status" 
                                class="w-full h-9 px-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <!-- Schedule / Publish Date & Time (Formatted Text + Expandable Picker) -->
                        <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-white/5" x-data="{ editingDate: false }">
                            <!-- Formatted Text View (No input box when inactive) -->
                            <div class="flex items-center justify-between text-xs py-1" x-show="!editingDate && status !== 'scheduled'">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base text-gray-400">calendar_month</span>
                                    <span class="text-gray-600 dark:text-gray-400">Publish:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('M d, Y @ H:i') : 'Immediately' }}
                                    </span>
                                </div>
                                <button 
                                    type="button" 
                                    @click="editingDate = true" 
                                    class="text-[11px] font-bold text-[#2563EB] hover:underline uppercase flex items-center gap-0.5 transition-colors"
                                >
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    <span>Edit</span>
                                </button>
                            </div>

                            <!-- Expandable Datetime Picker Box -->
                            <div x-show="editingDate || status === 'scheduled'" class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] space-y-3" x-cloak x-transition>
                                <div class="flex items-center justify-between text-xs">
                                    <label class="font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-blue-500">schedule</span>
                                        <span>Select Date & Time</span>
                                    </label>
                                    @if($publishedAt)
                                    <button 
                                        type="button" 
                                        wire:click="$set('publishedAt', null)" 
                                        class="text-[11px] font-semibold text-gray-400 hover:text-red-500 transition-colors"
                                    >
                                        Immediately
                                    </button>
                                    @endif
                                </div>

                                <input 
                                    type="datetime-local" 
                                    wire:model.live="publishedAt"
                                    class="w-full h-10 px-3 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all shadow-sm"
                                >

                                <div class="flex items-center justify-between pt-1 border-t border-gray-200/50 dark:border-white/5">
                                    <p class="text-[11px] text-gray-400 leading-tight">
                                        @if($status === 'scheduled')
                                            📅 Will publish automatically at this time.
                                        @elseif($publishedAt)
                                            Published on {{ \Carbon\Carbon::parse($publishedAt)->format('M d, Y @ H:i') }}
                                        @else
                                            Publishes immediately upon save.
                                        @endif
                                    </p>
                                    @if($status !== 'scheduled')
                                    <button 
                                        type="button" 
                                        @click="editingDate = false" 
                                        class="px-3 py-1 bg-[#2563EB] hover:bg-blue-600 text-white rounded-lg text-xs font-bold transition-colors shrink-0"
                                    >
                                        Done
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Actions (Move to Trash) -->
                    @if($isEdit)
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs">
                        <button 
                            type="button" 
                            wire:click="moveToTrash" 
                            wire:confirm="Are you sure you want to move this entry to trash?"
                            class="font-bold text-red-600 hover:text-red-700 dark:text-red-400 hover:underline transition-colors flex items-center gap-1"
                        >
                            <span class="material-symbols-outlined text-base">delete</span>
                            <span>Move to Trash</span>
                        </button>
                    </div>
                    @endif

                </div>

                <!-- Featured Image Card -->
                @if(in_array('thumbnail', $postType->supports ?? []))
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-6 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">image</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Featured Image</span>
                    </div>
                    
                    <livewire:admin.media-picker 
                        field="featured_image" 
                        :value="$featuredImage"
                        label="Select Featured Image"
                    />
                </div>
                @endif

                <!-- Taxonomies -->
                @foreach($taxonomies as $taxonomy)
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">folder_open</span>
                        <span class="text-xs font-bold uppercase tracking-widest">{{ $taxonomy->plural_label }}</span>
                    </div>
                    
                    <div class="max-h-40 overflow-y-auto space-y-1 p-2 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-transparent focus-within:border-[#2563EB] transition-colors">
                        @if(isset($taxonomyTerms[$taxonomy->id]) && $taxonomyTerms[$taxonomy->id]->count() > 0)
                            @foreach($taxonomyTerms[$taxonomy->id] as $term)
                                <label class="flex items-center gap-2 cursor-pointer group py-1" style="margin-left: {{ ($term->depth ?? 0) * 1.25 }}rem">
                                    <input 
                                        type="checkbox"
                                        wire:click="toggleTerm({{ $taxonomy->id }}, {{ $term->id }})"
                                        @checked(in_array($term->id, $selectedTerms[$taxonomy->id] ?? []))
                                        class="w-4 h-4 rounded border-gray-300 text-[#2563EB] focus:ring-[#2563EB] bg-white dark:bg-[#1A1A1A] dark:border-[#272B30]"
                                    >
                                    <span class="text-sm text-[#111827] dark:text-[#FCFCFC] group-hover:text-[#2563EB] transition-colors">{{ $term->name }}</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-xs text-[#6F767E] p-2">No {{ strtolower($taxonomy->plural_label) }} found.</p>
                        @endif
                    </div>
                    
                    <!-- Quick Add Term -->
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5" x-data="{ show: false }">
                        <button type="button" @click="show = !show" class="text-xs font-semibold text-[#2563EB] hover:text-blue-600 flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-sm" x-show="!show">add</span>
                            <span class="material-symbols-outlined text-sm" x-show="show">remove</span>
                            <span x-text="show ? 'Cancel' : 'Add New {{ $taxonomy->singular_label }}'"></span>
                        </button>

                        <div x-show="show" class="mt-2" x-transition>
                            <input 
                                type="text" 
                                wire:model="newTermInput.{{ $taxonomy->id }}" 
                                wire:keydown.enter.prevent="createTerm({{ $taxonomy->id }})"
                                placeholder="Term Name"
                                class="w-full mb-2 px-3 py-2 bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] rounded-lg text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]"
                            >
                            <button 
                                type="button" 
                                wire:click="createTerm({{ $taxonomy->id }})"
                                class="w-full px-3 py-2 bg-[#2563EB] text-white text-xs font-bold rounded-lg hover:bg-blue-600 transition-colors"
                                wire:loading.attr="disabled"
                            >
                                Add New Term
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Parent (for hierarchical) -->
                @if($postType->is_hierarchical && $possibleParents->count() > 0)
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">account_tree</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Parent</span>
                    </div>
                    <select 
                        wire:model="parentId"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] rounded-xl text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all text-sm"
                    >
                        <option value="">(No Parent)</option>
                        @foreach($possibleParents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Order (for hierarchical) -->
                @if($postType->is_hierarchical)
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">sort</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Order</span>
                    </div>
                    <input 
                        type="number"
                        wire:model="menuOrder"
                        min="0"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] rounded-xl text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all text-sm"
                    >
                    <p class="mt-2 text-xs text-[#6F767E]">Lower number = higher priority</p>
                </div>
                @endif

                <!-- Meta Boxes (Side) -->
                @foreach($metaBoxes as $box)
                    @if($box['context'] === 'side' && isset($groupedFields[$box['id']]))
                        <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                            <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                                <span class="material-symbols-outlined text-lg">extension</span>
                                <span class="text-xs font-bold uppercase tracking-widest">{{ $box['title'] }}</span>
                            </div>
                            <div class="space-y-4">
                                @foreach($groupedFields[$box['id']] as $field)
                                    @include('livewire.admin.cpt.entries.partials.field-render', ['field' => $field])
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Revisions History Card (Bottom Sidebar) -->
                @if($isEdit && !empty($revisions) && $revisions->count() > 0)
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none" x-data="{ open: true }">
                    <div class="flex items-center justify-between text-[#6F767E] cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-blue-500">history</span>
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-white">Revisions ({{ $revisions->count() }})</span>
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform" :class="{ 'rotate-180': !open }">expand_more</span>
                    </div>

                    <div x-show="open" class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5 space-y-2 max-h-60 overflow-y-auto pr-1" x-cloak>
                        @foreach($revisions as $rev)
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]">
                            <div class="space-y-0.5">
                                <p class="font-bold text-gray-900 dark:text-white leading-tight">{{ $rev->user?->name ?? 'System User' }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $rev->created_at->diffForHumans() }}</p>
                            </div>
                            <button type="button" 
                                wire:click="restoreRevision({{ $rev->id }})" 
                                wire:confirm="Are you sure you want to restore this revision? Current unsaved changes will be overwritten."
                                class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg font-bold text-[11px] transition-colors"
                                title="Restore to this version">
                                Restore
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </aside>
    </div>
    
    {{-- TipTap Media Picker Modal --}}
    @if(in_array('thumbnail', $postType->supports ?? []))
    <livewire:admin.tiptap-media-picker />
    @endif

    {{-- Relationship Picker Modal --}}
    @if($showRelationshipModal)
        @php
            $activeField = $postType->metaFields->where('id', $activeRelationshipFieldId)->first();
            $candidates = $targetEntriesByField[$activeRelationshipFieldId] ?? collect();
            if(!empty($relationshipSearch)) {
                $candidates = $candidates->filter(fn($item) => 
                    str_contains(strtolower($item->title), strtolower($relationshipSearch)) || 
                    str_contains(strtolower($item->slug), strtolower($relationshipSearch))
                );
            }
            $cardinality = $activeField->options['cardinality'] ?? 'many_to_many';
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-[#272B30]">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Select {{ $activeField->label ?? 'Related Entries' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $cardinality === 'one_to_many' ? 'Select 1 entry (Single)' : 'Select multiple entries (Multi-Check)' }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeRelationshipModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Search Box --}}
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="relationshipSearch" 
                        placeholder="Search entries by title or slug..." 
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                {{-- Candidates List --}}
                <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                    @forelse($candidates as $cand)
                        @php
                            $isSelected = in_array($cand->id, $tempSelectedRelationshipIds, true);
                        @endphp
                        <div 
                            wire:click="toggleRelationshipTempSelection({{ $cand->id }})"
                            class="flex items-center justify-between p-3 rounded-2xl border transition-all cursor-pointer {{ $isSelected ? 'bg-blue-50/50 dark:bg-blue-900/20 border-blue-500' : 'bg-gray-50/50 dark:bg-[#0B0B0B]/50 border-gray-200 dark:border-[#272B30] hover:border-gray-300 dark:hover:border-gray-700' }}"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="{{ $cardinality === 'one_to_many' ? 'radio' : 'checkbox' }}" 
                                    @checked($isSelected)
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ $cand->title }}</h4>
                                    <p class="text-[10px] text-gray-400">/{{ $cand->slug }}</p>
                                </div>
                            </div>
                            @if($isSelected)
                                <span class="material-symbols-outlined text-blue-500 text-sm">check_circle</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-gray-400 italic">No entries found matching search query.</div>
                    @endforelse
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#272B30]">
                    <button type="button" wire:click="closeRelationshipModal" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-white">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirmRelationshipSelection" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-500/20">
                        Confirm Selection ({{ count($tempSelectedRelationshipIds) }})
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
