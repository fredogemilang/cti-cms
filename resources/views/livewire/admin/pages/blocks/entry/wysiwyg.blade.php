{{-- WYSIWYG Block Entry (TipTap Editor) --}}
<div class="space-y-2">
    <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Rich Content</label>
    
    <div wire:ignore x-data="tiptapEditor('blocks.{{ $index }}.value')" 
         @tiptap-undo.window="undo()" 
         @tiptap-redo.window="redo()"
         class="relative">
        <div id="block-editor-{{ $index }}" class="h-[400px] min-h-[300px] rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] flex flex-col overflow-hidden shadow-sm">
            <!-- Toolbar -->
            <div class="flex items-center gap-1 p-2 border-b border-gray-200 dark:border-[#272B30] overflow-x-auto flex-wrap shrink-0 bg-white dark:bg-[#1A1A1A] rounded-t-xl">
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
                    <button type="button" @click="toggleStrike()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('strike') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Strikethrough">
                        <span class="material-symbols-outlined text-[20px]">strikethrough_s</span>
                    </button>
                    <button type="button" @click="toggleCode()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('code') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Inline Code">
                        <span class="material-symbols-outlined text-[20px]">code</span>
                    </button>
                </div>

                <div class="h-4 w-px bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                <!-- Headings -->
                <div class="flex items-center gap-0.5">
                    <button type="button" @click="toggleHeading(2)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 2 }) }" class="px-2 py-1 text-xs font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 2">
                        H2
                    </button>
                    <button type="button" @click="toggleHeading(3)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 3 }) }" class="px-2 py-1 text-xs font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 3">
                        H3
                    </button>
                    <button type="button" @click="toggleHeading(4)" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('heading', { level: 4 }) }" class="px-2 py-1 text-xs font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Heading 4">
                        H4
                    </button>
                </div>

                <div class="h-4 w-px bg-gray-200 dark:bg-[#272B30] mx-1"></div>

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
                </div>

                <div class="h-4 w-px bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                <!-- Lists -->
                <div class="flex items-center gap-0.5">
                    <button type="button" @click="toggleBulletList()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('bulletList') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Bullet List">
                        <span class="material-symbols-outlined text-[20px]">format_list_bulleted</span>
                    </button>
                    <button type="button" @click="toggleOrderedList()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('orderedList') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Numbered List">
                        <span class="material-symbols-outlined text-[20px]">format_list_numbered</span>
                    </button>
                </div>

                <div class="h-4 w-px bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                <!-- Inserts & Links -->
                <div class="flex items-center gap-0.5">
                    <button type="button" @click="openLinkModal()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('link') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Insert / Edit Link">
                        <span class="material-symbols-outlined text-[20px]">link</span>
                    </button>
                    <button type="button" @click="openMediaPicker()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Insert Image from Media Library">
                        <span class="material-symbols-outlined text-[20px]">image</span>
                    </button>
                    <button type="button" @click="openButtonCreator()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Create Link Button">
                        <span class="material-symbols-outlined text-[20px]">smart_button</span>
                    </button>
                    <button type="button" @click="toggleBlockquote()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('blockquote') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Blockquote">
                        <span class="material-symbols-outlined text-[20px]">format_quote</span>
                    </button>
                    <button type="button" @click="toggleCodeBlock()" :class="{ 'bg-gray-100 dark:bg-[#272B30] text-[#2563EB]': isActive('codeBlock') }" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Code Block">
                        <span class="material-symbols-outlined text-[20px]">developer_mode</span>
                    </button>
                    <button type="button" @click="setHorizontalRule()" class="p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#6F767E] transition-colors" title="Horizontal Rule">
                        <span class="material-symbols-outlined text-[20px]">horizontal_rule</span>
                    </button>
                </div>

                <div class="h-4 w-px bg-gray-200 dark:bg-[#272B30] mx-1"></div>

                <!-- History -->
                <div class="flex items-center gap-0.5">
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
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-[#6F767E]">Button Text</label>
                        <input type="text" x-model="buttonText" class="w-full h-10 px-3 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" placeholder="Download PDF / Visit Link">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-[#6F767E]">Link Type</label>
                        <select x-model="buttonLinkType" class="w-full h-10 px-2 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]">
                            <option value="url">External / Custom URL</option>
                            <option value="media">Uploaded File (Media Library)</option>
                        </select>
                    </div>
                    <div x-show="buttonLinkType === 'url'" class="space-y-1">
                        <label class="text-xs font-semibold text-[#6F767E]">Target URL</label>
                        <input type="text" x-model="buttonUrl" class="w-full h-10 px-3 rounded-lg bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB]" placeholder="https://example.com">
                    </div>
                    <div x-show="buttonLinkType === 'media'" class="space-y-2">
                        <label class="text-xs font-semibold text-[#6F767E]">Select Downloadable File</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="buttonUrl" readonly class="flex-1 h-10 px-3 rounded-lg bg-gray-100 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm text-[#6F767E] focus:outline-none" placeholder="No file selected">
                            <button type="button" @click="openButtonMediaPicker()" class="px-4 h-10 rounded-lg bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-600 transition-colors">Select File</button>
                        </div>
                    </div>
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
</div>
