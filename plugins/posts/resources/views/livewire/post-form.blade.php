<div class="flex flex-col h-full bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] transition-colors duration-200 antialiased font-sans relative overflow-hidden">
    {{-- Context Bar --}}
    <div class="flex items-center gap-3 px-6 py-4 md:px-10 border-b border-gray-200 dark:border-[#272B30] bg-white/50 dark:bg-[#0B0B0B]/50 shrink-0">
        <a class="h-9 w-9 flex items-center justify-center rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition-all shrink-0"
            href="{{ route('admin.posts.index') }}" wire:navigate>
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <h1 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                {{ $postId ? 'Edit Post' : 'Add New Post' }}
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

    <div class="flex-1 flex overflow-hidden">
        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">
            <div class="max-w-4xl mx-auto space-y-10">
                {{-- Language tabs --}}
                @if(count($availableLocales) > 1)
                    @php
                        $localeLabels = ['id' => 'Bahasa Indonesia', 'en' => 'English', 'ja' => '日本語', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español', 'zh' => '中文'];
                        $defaultLocale = \Plugins\Posts\Models\Post::defaultLocale();
                    @endphp
                    <div class="flex items-center gap-1 border-b border-gray-200 dark:border-[#272B30] -mb-px">
                        @foreach($availableLocales as $loc)
                            @php
                                $active = $loc === $editingLocale;
                                $hasContent = $loc === $defaultLocale
                                    ? true
                                    : !empty(($localizedSnapshots[$loc]['title'] ?? '') . ($localizedSnapshots[$loc]['slug'] ?? ''));
                            @endphp
                            <button type="button" wire:click="switchLocale('{{ $loc }}')"
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
                                Editing translation — leave blank to use default values.
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Title -->
                <div class="space-y-4">
                    <input wire:model.live="title"
                        class="w-full bg-transparent border-none text-4xl md:text-5xl font-extrabold text-[#111827] dark:text-[#FCFCFC] placeholder-gray-400 dark:placeholder-[#272B30] focus:ring-0 focus:outline-none shadow-none focus:shadow-none px-0 @error('title') text-red-500 placeholder-red-300 @enderror"
                        placeholder="Enter Post Title..." type="text" />
                    
                    @error('title')
                        <p class="text-sm text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                    
                    @if($slug)
                    <div class="flex items-center gap-2 text-xs font-bold text-[#6F767E] uppercase tracking-wider pl-1">
                        <span>PERMALINK:</span>
                        <span class="text-[#6F767E] lowercase font-normal">{{ url('/') }}/</span>
                        <div x-data="{ editing: false }" class="relative flex items-center gap-2">
                            <span x-show="!editing" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#272B30]">{{ $slug }}</span>
                            <input x-show="editing" wire:model.blur="slug" @blur="editing = false" @keydown.enter="editing = false" type="text" class="bg-[#1A1A1A] px-2 py-0.5 rounded text-[#FCFCFC] lowercase font-normal border border-[#2563EB] focus:outline-none w-auto min-w-[100px]" x-cloak>
                            <button @click="editing = !editing; $nextTick(() => $el.previousElementSibling.focus())" class="text-[#6F767E] hover:text-[#FCFCFC] transition-colors">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Content Editor (Simplified to Textarea for now) -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Content</h3>
                    </div>
                    <div wire:ignore x-data="tiptapEditor('content')" 
                         @tiptap-undo.window="undo()" 
                         @tiptap-redo.window="redo()"
                         id="post-content-editor" class="h-[600px] min-h-[500px] rounded-3xl border border-gray-200 dark:border-[#272B30]/30 bg-white dark:bg-[#1A1A1A] flex flex-col overflow-hidden shadow-sm">

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
                                <button type="button" @click="toggleHeading(1)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 1 }) }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 1">
                                    <span class="material-symbols-outlined text-[20px]">format_h1</span>
                                </button>
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
                                <button type="button" @click="openButtonCreator()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Insert Button">
                                    <span class="material-symbols-outlined text-[20px]">smart_button</span>
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

                        <!-- Button Creator Modal -->
                        <div x-show="showButtonCreator" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak>
                            <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-6 w-full max-w-md space-y-4 shadow-xl" @click.away="showButtonCreator = false">
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
                                    <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                                        <span class="material-symbols-outlined">smart_button</span>
                                        <span>Create Link Button</span>
                                    </h3>
                                    <button type="button" @click="showButtonCreator = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <!-- Button Text -->
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-[#6F767E]">Button Text</label>
                                        <input type="text" x-model="buttonText" class="w-full h-10 px-3 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" placeholder="Download PDF / Visit Link">
                                    </div>
                                    
                                    <!-- Link Type Selection -->
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-[#6F767E]">Link Type</label>
                                        <select x-model="buttonLinkType" class="w-full h-10 px-2 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                            <option value="url">External / Custom URL</option>
                                            <option value="media">Uploaded File (Media Library)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- URL Input (for Custom URL) -->
                                    <div x-show="buttonLinkType === 'url'" class="space-y-1">
                                        <label class="text-xs font-semibold text-[#6F767E]">Target URL</label>
                                        <input type="text" x-model="buttonUrl" class="w-full h-10 px-3 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" placeholder="https://example.com">
                                    </div>
                                    
                                    <!-- Media File Selection -->
                                    <div x-show="buttonLinkType === 'media'" class="space-y-2">
                                        <label class="text-xs font-semibold text-[#6F767E]">Select Downloadable File</label>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="buttonUrl" readonly class="flex-1 h-10 px-3 rounded-lg bg-gray-100 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#6F767E] focus:outline-none" placeholder="No file selected">
                                            <button type="button" @click="openButtonMediaPicker()" class="px-4 h-10 rounded-lg bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-600 transition-colors">Select File</button>
                                        </div>
                                    </div>

                                    <!-- Button Style -->
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-[#6F767E]">Button Style</label>
                                        <select x-model="buttonStyle" class="w-full h-10 px-2 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                            <option value="btn-primary">Primary (Blue Fill)</option>
                                            <option value="btn-secondary">Secondary (Gray Fill)</option>
                                            <option value="btn-success">Success (Green Fill)</option>
                                            <option value="btn-danger">Danger (Red Fill)</option>
                                            <option value="btn-outline">Outline (Border Only)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Download Attribute Switch -->
                                    <div class="flex items-center justify-between pt-2">
                                        <span class="text-xs font-semibold text-[#6F767E]">Force file download (download attribute)</span>
                                        <button type="button" 
                                            @click="buttonDownload = !buttonDownload"
                                            class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="buttonDownload ? 'bg-[#2563EB]' : 'bg-gray-200 dark:bg-[#272B30]'">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
                                                :class="buttonDownload ? 'translate-x-5' : 'translate-x-0'">
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                                    <button type="button" @click="showButtonCreator = false" class="px-4 py-2 text-xs font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">Cancel</button>
                                    <button type="button" @click="insertButton()" class="px-5 py-2 text-xs font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-lg transition-colors">Insert</button>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Excerpt -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-[#6F767E] uppercase tracking-widest">Excerpt</h3>
                            </div>
                            <textarea wire:model="excerpt" rows="3" 
                                class="w-full rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] p-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent resize-none"
                                placeholder="Write a short excerpt..."></textarea>
                        </div>

                        <!-- SEO Settings (Centralized Component) -->
                        <livewire:admin.seo.seo-meta-box
                            seoable-type="Plugins\Posts\Models\Post"
                            :seoable-id="$postId"
                            :locale="$editingLocale"
                            :key="'post-seo-meta-box-'.($postId ?? 'new')"
                        />
                    </div>
                </div>
            </div>
    
            <!-- Sidebar -->
            <aside class="w-[360px] bg-[#F4F5F6] dark:bg-[#0B0B0B] border-l border-gray-200 dark:border-[#272B30] overflow-y-auto no-scrollbar hidden lg:block shrink-0">
                <div class="p-6 space-y-6">
                    <!-- Actions Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] p-5 border border-gray-200 dark:border-[#272B30] shadow-sm dark:shadow-none">
                        <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">rocket_launch</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Actions</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button 
                                type="button"
                                wire:click="save('published')" 
                                wire:loading.attr="disabled"
                                class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="save('published')" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">save</span>
                                    <span>{{ $status === 'published' ? 'Update' : 'Publish' }}</span>
                                </span>
                                <span wire:loading wire:target="save('published')">Saving...</span>
                            </button>

                            <button 
                                type="button"
                                wire:click="save('draft')" 
                                wire:loading.attr="disabled"
                                class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-white bg-gray-100 dark:bg-[#272B30]/60 hover:bg-gray-200 dark:hover:bg-[#272B30] transition-all flex items-center justify-center gap-2"
                            >
                                <span wire:loading.remove wire:target="save('draft')">Save Draft</span>
                                <span wire:loading wire:target="save('draft')">Saving Draft...</span>
                            </button>

                            @if($slug)
                                <a href="{{ route('posts.show', $slug) }}" target="_blank"
                                    class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-[#111827] dark:text-white bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] hover:border-[#6F767E] transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg">open_in_new</span>
                                    Preview
                                </a>
                            @endif

                            @if($postId)
                                <button 
                                    type="button"
                                    wire:click="delete"
                                    wire:confirm="Are you sure you want to move this post to trash?"
                                    class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                    Move to Trash
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Word Document Import Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                        <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">description</span>
                            <span class="text-xs font-bold uppercase tracking-widest">DOCX Source</span>
                        </div>
                        
                        <div class="space-y-3">
                            <p class="text-xs text-[#6F767E]">Import post title, structure, format, and embedded images directly from a Word document (.docx).</p>
                            
                            <div class="relative flex items-center justify-center border-2 border-dashed border-gray-200 dark:border-[#272B30] hover:border-[#2563EB] transition-colors rounded-xl p-4 cursor-pointer">
                                <input type="file" wire:model="docxFile" accept=".docx" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                <div class="text-center space-y-1">
                                    <span class="material-symbols-outlined text-[#6F767E] text-2xl">cloud_upload</span>
                                    <span class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Upload .docx file</span>
                                    <span class="block text-[10px] text-[#6F767E]">Max 10MB</span>
                                </div>
                            </div>
                            
                            <div wire:loading wire:target="docxFile" class="text-xs text-[#2563EB] font-semibold animate-pulse flex items-center gap-2 mt-2">
                                <span class="inline-block animate-spin w-3 h-3 border-2 border-[#2563EB] border-t-transparent rounded-full"></span>
                                <span>Parsing Word document...</span>
                            </div>

                            @error('docxFile')
                                <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Publishing Info Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none" x-data="{ editingStatus: false, editingVisibility: false }">
                        <div class="flex items-center gap-2 mb-6 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">tune</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Publishing Info</span>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Featured Toggle -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-[#6F767E]">Featured Post</span>
                                <button type="button" 
                                    wire:click="$toggle('is_featured')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                    :class="{ 'bg-[#2563EB]': @js($is_featured), 'bg-gray-200 dark:bg-[#272B30]': !@js($is_featured) }"
                                    role="switch" 
                                    aria-checked="false">
                                    <span aria-hidden="true" 
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="{ 'translate-x-5': @js($is_featured), 'translate-x-0': !@js($is_featured) }">
                                    </span>
                                </button>
                            </div>

                            <!-- Status -->
                            <div class="group">
                            <div class="flex items-center justify-between" x-show="!editingStatus">
                                <span class="text-sm text-[#6F767E]">Status:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ ucfirst($status) }}</span>
                                    <button @click="editingStatus = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingStatus" class="bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <select wire:model="status" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <div class="flex justify-end">
                                    <button @click="editingStatus = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>

                        <!-- Visibility -->
                        <div class="group">
                            <div class="flex items-center justify-between" x-show="!editingVisibility">
                                <span class="text-sm text-[#6F767E]">Visibility:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ ucfirst($visibility) }}</span>
                                    <button @click="editingVisibility = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingVisibility" class="bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2" x-cloak>
                                <select wire:model.live="visibility" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="password">Password Protected</option>
                                </select>
                                
                                <div x-show="$wire.visibility === 'password'" x-transition>
                                    <input type="password" wire:model="password" placeholder="Enter password" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                </div>

                                <div class="flex justify-end">
                                    <button @click="editingVisibility = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>

                        <!-- Author -->
                        <div class="group" x-data="{ editingAuthor: false }">
                            <div class="flex items-center justify-between" x-show="!editingAuthor">
                                <span class="text-sm text-[#6F767E]">Author:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $authors->find($author_id)->name ?? 'Unknown' }}</span>
                                    <button @click="editingAuthor = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingAuthor" class="bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-2 relative z-10 shadow-lg animate-in fade-in duration-200" x-cloak @click.away="editingAuthor = false">
                                <div class="flex items-center justify-between gap-2 border-b border-gray-100 dark:border-[#272B30] pb-1.5 mb-1.5">
                                    <span class="text-[10px] font-bold text-[#6F767E] uppercase">Select Author</span>
                                    <button type="button" @click="$wire.set('addingAuthor', !$wire.get('addingAuthor'))" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Add New</button>
                                </div>

                                <div x-show="$wire.addingAuthor" class="flex gap-2 mb-2" x-transition>
                                    <input type="text" wire:model="newAuthorName" 
                                        @keydown.enter.prevent="$wire.addAuthor(newAuthorName)"
                                        class="flex-1 h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" 
                                        placeholder="Author name">
                                    <button type="button" @click="$wire.addAuthor(newAuthorName)" class="px-2 h-8 rounded-md bg-[#2563EB] text-white text-xs font-bold">Add</button>
                                </div>

                                <select wire:model="author_id" class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                    @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                <div class="flex justify-end">
                                    <button @click="editingAuthor = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Date -->
                        <div class="group" x-data="{ editingPublish: false }">
                            <div class="flex items-center justify-between" x-show="!editingPublish">
                                <span class="text-sm text-[#6F767E]">Publish:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">
                                        {{ $published_at ? \Carbon\Carbon::parse($published_at)->format('M d, Y H:i') : 'Immediately' }}
                                    </span>
                                    <button @click="editingPublish = true" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Edit</button>
                                </div>
                            </div>
                            <div x-show="editingPublish" class="bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-3 rounded-lg space-y-3 mt-2" x-cloak>
                                <div class="flex items-center gap-2">
                                    <input type="radio" id="publish_immediately" name="publish_type" 
                                        @click="$wire.set('published_at', null)" 
                                        :checked="{{ $published_at ? 'false' : 'true' }}"
                                        class="text-[#2563EB] focus:ring-[#2563EB] bg-white dark:bg-[#0B0B0B] border-gray-300 dark:border-[#272B30]">
                                    <label for="publish_immediately" class="text-xs font-medium text-[#111827] dark:text-[#FCFCFC]">Immediately</label>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" id="publish_schedule" name="publish_type" 
                                            :checked="{{ $published_at ? 'true' : 'false' }}"
                                            class="text-[#2563EB] focus:ring-[#2563EB] bg-white dark:bg-[#0B0B0B] border-gray-300 dark:border-[#272B30]">
                                        <label for="publish_schedule" class="text-xs font-medium text-[#111827] dark:text-[#FCFCFC]">Schedule</label>
                                    </div>
                                    <input wire:model="published_at" type="datetime-local" 
                                        class="w-full h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                                </div>
                                <div class="flex justify-end">
                                    <button @click="editingPublish = false" class="text-xs text-[#2563EB] font-bold hover:underline">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-[#272B30] flex items-center justify-end text-end">
                        <button wire:click="delete" wire:confirm="Are you sure you want to move this post to trash?" class="text-xs font-bold text-[#FF6A55] hover:text-[#ff4f38] transition-colors">
                            Move to Trash
                        </button>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-6 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">image</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Featured Image</span>
                    </div>
                    
                    <livewire:admin.media-picker 
                        field="featured_image" 
                        :value="$featured_image"
                        label="Select Featured Image"
                    />
                </div>

                <!-- Organization Card -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-6 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">folder_open</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Organization</span>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Categories -->
                        <div class="space-y-2" x-data="{ addingCategory: false, newCategoryName: '' }">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Categories</label>
                                <button @click="addingCategory = !addingCategory" class="text-[10px] font-bold text-[#2563EB] hover:underline uppercase">Add New</button>
                            </div>
                            
                            <div x-show="addingCategory" class="flex gap-2 mb-2" x-cloak>
                                <input x-model="newCategoryName" 
                                    @keydown.enter.prevent="$wire.addCategory(newCategoryName); newCategoryName = ''; addingCategory = false"
                                    type="text" 
                                    class="flex-1 h-8 rounded-md bg-white dark:bg-[#0B0B0B] border-gray-200 dark:border-[#272B30] text-xs font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" 
                                    placeholder="Category name">
                                <button @click="$wire.addCategory(newCategoryName); newCategoryName = ''; addingCategory = false" class="px-2 h-8 rounded-md bg-[#2563EB] text-white text-xs font-bold">Add</button>
                            </div>

                            <div class="max-h-40 overflow-y-auto space-y-1 p-2 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-transparent focus-within:border-[#2563EB] transition-colors">
                                @foreach($categories as $category)
                                <label class="flex items-center gap-2 cursor-pointer group py-1">
                                    <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}" class="w-4 h-4 rounded border-gray-300 text-[#2563EB] focus:ring-[#2563EB] bg-white dark:bg-[#1A1A1A] dark:border-[#272B30]">
                                    <span class="text-sm text-[#111827] dark:text-[#FCFCFC] group-hover:text-[#2563EB] transition-colors">{{ $category->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="space-y-2" x-data="{ newTag: '' }">
                            <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Tags</label>
                            <div class="relative">
                                <input x-model="newTag" 
                                    @keydown.enter.prevent="$wire.addTag(newTag); newTag = ''"
                                    type="text" 
                                    class="w-full h-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] pl-4 pr-10"
                                    placeholder="Add tags...">
                                <button @click="$wire.addTag(newTag); newTag = ''" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-[#6F767E] hover:text-[#2563EB] transition-colors">
                                    <span class="material-symbols-outlined text-xl">add</span>
                                </button>
                            </div>
                            <!-- Visual Chips for Tags -->
                            @if($tags)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach(array_filter(array_map('trim', explode(',', $tags))) as $tag)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 dark:bg-[#272B30] border border-gray-200 dark:border-[#33383f]">
                                    <span class="text-[10px] font-bold text-[#111827] dark:text-[#FCFCFC] uppercase">{{ $tag }}</span>
                                    <button wire:click="removeTag('{{ $tag }}')" class="text-[#6F767E] hover:text-[#FF6A55] transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                    </button>
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    {{-- TipTap Media Picker Modal --}}
    <livewire:admin.tiptap-media-picker />
</div>

