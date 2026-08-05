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
                        <span class="text-[#6F767E] lowercase font-normal">{{ url('/') }}/{{ !empty($archiveSlug) ? trim($archiveSlug, '/') . '/' : '' }}</span>
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
                         class="relative">
                        <div id="post-content-editor" class="h-[600px] min-h-[500px] rounded-3xl border border-gray-200 dark:border-[#272B30]/30 bg-white dark:bg-[#1A1A1A] flex flex-col overflow-hidden shadow-sm">

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
                                <button type="button" @click="toggleUnderline()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('underline') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Underline">
                                    <span class="material-symbols-outlined text-[20px]">format_underlined</span>
                                </button>
                                <button type="button" @click="toggleStrike()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('strike') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Strike">
                                    <span class="material-symbols-outlined text-[20px]">strikethrough_s</span>
                                </button>
                                <button type="button" @click="toggleCodeBlock()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('codeBlock') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Code Block">
                                    <span class="material-symbols-outlined text-[20px]">code</span>
                                </button>
                            </div>
                            
                            <div class="w-px h-5 bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                            <!-- Format Dropdown -->
                            <div class="flex items-center">
                                <select @change="setFormat($event.target.value)" 
                                        :value="isActive('heading', { level: 2 }) ? 'h2' : (isActive('heading', { level: 3 }) ? 'h3' : (isActive('heading', { level: 4 }) ? 'h4' : 'p'))" 
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-200 focus:outline-none focus:border-blue-500 cursor-pointer">
                                    <option value="p">Paragraph</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                    <option value="h4">Heading 4</option>
                                </select>
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
                    </div>

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

                        <!-- Custom Link Modal -->
                        <div x-show="showLinkModal" 
                             class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
                             x-cloak 
                             style="display: none;"
                             @keydown.escape.window="showLinkModal = false">
                            <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-6 w-full max-w-md space-y-5 shadow-2xl" @click.outside="showLinkModal = false">
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
                                    <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[20px] text-[#2563EB]">link</span>
                                        <span>Insert / Edit Link</span>
                                    </h3>
                                    <button type="button" @click="showLinkModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <!-- Display Text Input -->
                                    <div class="space-y-1.5">
                                        <label class="text-xs font-semibold text-[#6F767E] dark:text-gray-300">Display Text</label>
                                        <input x-model="linkSelectedText" 
                                               type="text" 
                                               class="w-full h-10 px-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB] focus:outline-none" 
                                               placeholder="Text to display (e.g. Privacy Policy)">
                                    </div>

                                    <!-- URL Input -->
                                    <div class="space-y-1.5">
                                        <label class="text-xs font-semibold text-[#6F767E] dark:text-gray-300">Target URL</label>
                                        <input x-ref="linkUrlInput" 
                                               x-model="linkUrl" 
                                               @keydown.enter.prevent="saveLink()"
                                               type="text" 
                                               class="w-full h-10 px-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB] focus:outline-none" 
                                               placeholder="https://example.com, /privacy-policy, mailto:info@example.com">
                                    </div>
                                    
                                    <!-- Open in New Tab Checkbox -->
                                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                        <input type="checkbox" x-model="linkTargetBlank" class="w-4 h-4 rounded border-gray-300 text-[#2563EB] focus:ring-[#2563EB] dark:border-gray-700 dark:bg-[#0B0B0B]">
                                        <span class="text-xs font-medium text-[#111827] dark:text-[#FCFCFC]">Open link in a new tab</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-[#272B30]">
                                    <div>
                                        <button x-show="isActive('link')" type="button" @click="removeLink()" class="px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                            Remove Link
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="showLinkModal = false" class="px-4 py-2 text-xs font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">Cancel</button>
                                        <button type="button" @click="saveLink()" class="px-5 py-2 text-xs font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-lg transition-colors shadow-sm">Save Link</button>
                                    </div>
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
                    <!-- Unified Publishing & Actions Card (Optimized for Narrow Sidebar) -->
                    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none space-y-5">
                        
                        <!-- Title / Header (Aligned with Featured Image card) -->
                        <div class="flex items-center gap-2 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">rocket_launch</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Publishing & Actions</span>
                        </div>

                        <!-- Main Primary Submit / Action Button (Top Highlight) -->
                        <button 
                            type="button"
                            wire:click="save" 
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                                @if($status === 'scheduled')
                                    <span class="material-symbols-outlined text-lg">event</span>
                                    <span>Schedule Post</span>
                                @elseif($status === 'published')
                                    <span class="material-symbols-outlined text-lg">published_with_changes</span>
                                    <span>Update Post</span>
                                @elseif($status === 'archived')
                                    <span class="material-symbols-outlined text-lg">archive</span>
                                    <span>Archive Post</span>
                                @else
                                    <span class="material-symbols-outlined text-lg">publish</span>
                                    <span>Publish Post</span>
                                @endif
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
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

                            @if($slug)
                            <a 
                                href="{{ route('posts.show', $slug) . ($editingLocale && $editingLocale !== Plugins\Posts\Models\Post::defaultLocale() ? '?lang='.$editingLocale : '') }}" 
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

                        <!-- Publishing Controls Box (Status Dropdown, Schedule Date Picker, Author, Featured Toggle) -->
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

                            <!-- Schedule / Publish Date & Time (2-Line Layout Matching Status) -->
                            <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-white/5" x-data="{ editingDate: false }">
                                <!-- Line 1: Header / Label + Edit Action -->
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-base text-gray-400">calendar_month</span>
                                        <span>Publish Date & Time</span>
                                    </label>
                                    <button 
                                        type="button" 
                                        @click="editingDate = !editingDate" 
                                        class="text-[11px] font-bold text-[#2563EB] hover:underline uppercase flex items-center gap-0.5 transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-xs" x-text="editingDate ? 'close' : 'edit'">edit</span>
                                        <span x-text="editingDate ? 'Cancel' : 'Edit'">Edit</span>
                                    </button>
                                </div>

                                <!-- Line 2 View Mode: Full-width Formatted Value Box -->
                                <div x-show="!editingDate && status !== 'scheduled'" class="w-full min-h-[36px] px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] flex items-center justify-between">
                                    <span>{{ $published_at ? \Carbon\Carbon::parse($published_at)->format('M d, Y @ H:i') : 'Immediately' }}</span>
                                    @if($published_at)
                                    <span class="text-[10px] text-gray-400 font-normal">Custom</span>
                                    @else
                                    <span class="text-[10px] text-emerald-500 font-bold">Instant</span>
                                    @endif
                                </div>

                                <!-- Line 2 Edit Mode: Expandable Datetime Picker Box -->
                                <div x-show="editingDate || status === 'scheduled'" class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] space-y-3 mt-1" x-cloak x-transition>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-sm text-blue-500">schedule</span>
                                            <span>Set Date & Time</span>
                                        </span>
                                        @if($published_at)
                                        <button 
                                            type="button" 
                                            wire:click="$set('published_at', null)" 
                                            class="text-[11px] font-semibold text-gray-400 hover:text-red-500 transition-colors"
                                        >
                                            Immediately
                                        </button>
                                        @endif
                                    </div>

                                    <input 
                                        type="datetime-local" 
                                        wire:model.live="published_at"
                                        class="w-full h-10 px-3 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all shadow-sm"
                                    >

                                    <div class="flex items-center justify-between pt-1 border-t border-gray-200/50 dark:border-white/5">
                                        <p class="text-[11px] text-gray-400 leading-tight">
                                            @if($status === 'scheduled')
                                                📅 Will publish automatically at this time.
                                            @elseif($published_at)
                                                Published on {{ \Carbon\Carbon::parse($published_at)->format('M d, Y @ H:i') }}
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

                            <!-- Author Selector Field -->
                            <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-white/5">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-base text-gray-400">person</span>
                                        <span>Author</span>
                                    </label>
                                    <button type="button" wire:click="$toggle('addingAuthor')" class="text-[11px] font-bold text-[#2563EB] hover:underline uppercase">
                                        + New
                                    </button>
                                </div>
                                @if($addingAuthor)
                                <div class="flex gap-2 my-1" x-transition>
                                    <input type="text" wire:model="newAuthorName" 
                                        @keydown.enter.prevent="$wire.addAuthor(newAuthorName)"
                                        class="flex-1 h-8 px-3 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB]" 
                                        placeholder="Author name">
                                    <button type="button" wire:click="addAuthor(newAuthorName)" class="px-3 h-8 rounded-xl bg-[#2563EB] text-white text-xs font-bold shrink-0">Add</button>
                                </div>
                                @endif
                                <select wire:model="author_id" class="w-full h-9 px-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] transition-all">
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Featured Post Toggle -->
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-white/5">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base text-amber-500">star</span>
                                    <span>Featured Post</span>
                                </label>
                                <button type="button" 
                                    wire:click="$toggle('is_featured')"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                    :class="{ 'bg-[#2563EB]': @js($is_featured), 'bg-gray-200 dark:bg-[#272B30]': !@js($is_featured) }"
                                    role="switch">
                                    <span aria-hidden="true" 
                                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="{ 'translate-x-4': @js($is_featured), 'translate-x-0': !@js($is_featured) }">
                                    </span>
                                </button>
                            </div>

                        </div>

                        <!-- Bottom Actions (Move to Trash) -->
                        @if($postId)
                        <div class="pt-3 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs">
                            <button 
                                type="button" 
                                wire:click="delete" 
                                wire:confirm="Are you sure you want to move this post to trash?"
                                class="font-bold text-red-600 hover:text-red-700 dark:text-red-400 hover:underline transition-colors flex items-center gap-1"
                            >
                                <span class="material-symbols-outlined text-base">delete</span>
                                <span>Move to Trash</span>
                            </button>
                        </div>
                        @endif

                    </div>

                    <!-- Word Document Import Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none space-y-3">
                        <div class="flex items-center gap-2 text-[#6F767E]">
                            <span class="material-symbols-outlined text-lg">description</span>
                            <span class="text-xs font-bold uppercase tracking-widest">DOCX Source</span>
                        </div>
                        
                        <p class="text-xs text-[#6F767E]">Import post title, content structure, format, and embedded images directly from a Word document (.docx).</p>
                        
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

                <!-- Related To Card (CPT Associations) -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm dark:shadow-none space-y-4">
                    <div class="flex items-center justify-between text-[#6F767E]">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">link</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Related To</span>
                        </div>
                        <button type="button" wire:click="openCptModal" class="text-xs font-bold text-[#2563EB] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">add</span>
                            <span>Add / Edit</span>
                        </button>
                    </div>

                    @if($attachedCptEntries->isNotEmpty())
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($attachedCptEntries as $attached)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-[#2563EB] dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/50">
                            <span>{{ $attached->title }}</span>
                            <span class="text-[10px] opacity-75 font-normal">({{ $attached->postType->plural_label ?? 'CPT' }})</span>
                            <button type="button" wire:click="removeCptEntry({{ $attached->id }})" class="hover:text-red-500 transition-colors">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-[#6F767E] italic">No CPT entries associated yet. Click "Add / Edit" to pair this post with Technology Alliances or other CPTs.</p>
                    @endif
                </div>

            </div>
        </aside>
    </div>

    <!-- CPT Search & Selection Modal -->
    @if($showCptModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 max-w-xl w-full shadow-2xl border border-gray-200 dark:border-[#272B30] space-y-4 max-h-[85vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
                <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2563EB]">link</span>
                    <span>Select Related CPT Entries</span>
                </h3>
                <button type="button" wire:click="$set('showCptModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <!-- Search & Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <!-- Filter by CPT Type -->
                @if($pairedCptTypes->count() > 1)
                <select wire:model.live="cptFilterSlug" class="h-10 px-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">
                    <option value="all">All Paired CPTs</option>
                    @foreach($pairedCptTypes as $pt)
                    <option value="{{ $pt->slug }}">{{ $pt->plural_label ?: $pt->name }}</option>
                    @endforeach
                </select>
                @endif

                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-lg">search</span>
                    <input type="text" wire:model.live.debounce.300ms="cptSearch" placeholder="Search entries by title..." class="w-full h-10 pl-9 pr-3 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-xs text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB]">
                </div>
            </div>

            <!-- Scrollable CPT List -->
            <div class="flex-1 overflow-y-auto space-y-2 pr-1 min-h-[240px]">
                @forelse($modalCptEntries as $entry)
                @php $isTempChecked = in_array((int)$entry->id, array_map('intval', $tempSelectedCptEntries), true); @endphp
                <div wire:click="toggleTempCptEntry({{ $entry->id }})" class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all {{ $isTempChecked ? 'bg-blue-50/50 dark:bg-blue-900/20 border-[#2563EB]/50' : 'bg-gray-50/50 dark:bg-[#0B0B0B]/50 border-gray-100 dark:border-[#272B30] hover:bg-gray-100 dark:hover:bg-[#272B30]' }}">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" {{ $isTempChecked ? 'checked' : '' }} class="rounded border-gray-300 text-[#2563EB] focus:ring-[#2563EB]">
                        <div>
                            <p class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $entry->title }}</p>
                            <p class="text-[10px] text-[#6F767E]">
                                Type: <span class="font-semibold">{{ $entry->postType->plural_label ?? 'CPT' }}</span> | Slug: {{ $entry->slug }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-xs text-[#6F767E]">
                    No CPT entries found matching your query.
                </div>
                @endforelse
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-[#272B30]">
                <span class="text-xs text-[#6F767E] font-medium">Selected: {{ count($tempSelectedCptEntries) }} entry(ies)</span>
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('showCptModal', false)" class="px-4 py-2 text-xs font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white rounded-lg border border-gray-200 dark:border-[#272B30]">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveCptSelections" class="px-5 py-2 text-xs font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-lg shadow-md">
                        Apply Selections
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TipTap Media Picker Modal --}}
    <livewire:admin.tiptap-media-picker />
</div>

