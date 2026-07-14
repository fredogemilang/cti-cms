{{-- WYSIWYG Block Entry (Quill Editor) --}}
<div class="space-y-2">
    <label class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider">Rich Content</label>
    <div wire:ignore class="wysiwyg-editor-wrapper">
        <div
            x-data="{
                content: @entangle('blocks.' . $index . '.value'),
                editor: null,
                init() {
                    this.editor = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: '{{ $block['options']['placeholder'] ?? 'Write your content here...' }}',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                [{ 'align': [] }],
                                ['blockquote', 'code-block'],
                                ['link', 'image'],
                                ['clean']
                            ]
                        }
                    });

                    // Set initial content
                    if (this.content) {
                        this.editor.root.innerHTML = this.content;
                    }

                    // Update Livewire on change
                    this.editor.on('text-change', () => {
                        this.content = this.editor.root.innerHTML;
                    });

                    // Watch for external changes
                    this.$watch('content', (value) => {
                        if (value !== this.editor.root.innerHTML) {
                            this.editor.root.innerHTML = value || '';
                        }
                    });
                }
            }"
            class="quill-wrapper rounded-xl overflow-hidden border border-gray-200 dark:border-[#272B30]"
        >
            <div x-ref="editor"></div>
        </div>
    </div>
</div>

@pushOnce('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* === Quill Editor — Light Mode (default) === */
    .quill-wrapper .ql-toolbar {
        border: none !important;
        border-bottom: 1px solid #E5E7EB !important;
        background: #F9FAFB !important;
        padding: 8px !important;
    }
    .quill-wrapper .ql-container {
        border: none !important;
        background: #FFFFFF !important;
        font-size: 14px;
    }
    .quill-wrapper .ql-editor {
        min-height: 200px;
        color: #111827 !important;
        padding: 16px !important;
        line-height: 1.6;
    }
    .quill-wrapper .ql-editor.ql-blank::before {
        color: #9CA3AF !important;
        font-style: normal !important;
    }
    .quill-wrapper .ql-editor:focus {
        outline: none;
    }
    /* Toolbar icons — light */
    .quill-wrapper .ql-toolbar .ql-stroke {
        stroke: #6B7280 !important;
    }
    .quill-wrapper .ql-toolbar .ql-fill {
        fill: #6B7280 !important;
    }
    .quill-wrapper .ql-toolbar .ql-picker {
        color: #6B7280 !important;
    }
    .quill-wrapper .ql-toolbar .ql-picker-label {
        color: #6B7280 !important;
    }
    .quill-wrapper .ql-toolbar button:hover .ql-stroke,
    .quill-wrapper .ql-toolbar .ql-picker-label:hover .ql-stroke {
        stroke: #111827 !important;
    }
    .quill-wrapper .ql-toolbar button:hover .ql-fill,
    .quill-wrapper .ql-toolbar .ql-picker-label:hover .ql-fill {
        fill: #111827 !important;
    }
    .quill-wrapper .ql-toolbar button.ql-active .ql-stroke {
        stroke: #2563EB !important;
    }
    .quill-wrapper .ql-toolbar button.ql-active .ql-fill {
        fill: #2563EB !important;
    }
    /* Dropdown menus — light */
    .quill-wrapper .ql-picker-options {
        background: #FFFFFF !important;
        border-color: #E5E7EB !important;
    }
    .quill-wrapper .ql-picker-item {
        color: #6B7280 !important;
    }
    .quill-wrapper .ql-picker-item:hover {
        color: #111827 !important;
    }
    /* Snow theme tooltip — light */
    .ql-snow .ql-tooltip {
        background: #FFFFFF !important;
        border-color: #E5E7EB !important;
        color: #111827 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .ql-snow .ql-tooltip input[type=text] {
        background: #F9FAFB !important;
        border-color: #E5E7EB !important;
        color: #111827 !important;
    }
    .ql-snow .ql-tooltip a.ql-action,
    .ql-snow .ql-tooltip a.ql-remove {
        color: #2563EB !important;
    }

    /* === Quill Editor — Dark Mode === */
    .dark .quill-wrapper .ql-toolbar {
        border-bottom-color: #272B30 !important;
        background: #1A1A1A !important;
    }
    .dark .quill-wrapper .ql-container {
        background: #0B0B0B !important;
    }
    .dark .quill-wrapper .ql-editor {
        color: #FCFCFC !important;
    }
    .dark .quill-wrapper .ql-editor.ql-blank::before {
        color: #6F767E !important;
    }
    .dark .quill-wrapper .ql-toolbar .ql-stroke {
        stroke: #9CA3AF !important;
    }
    .dark .quill-wrapper .ql-toolbar .ql-fill {
        fill: #9CA3AF !important;
    }
    .dark .quill-wrapper .ql-toolbar .ql-picker,
    .dark .quill-wrapper .ql-toolbar .ql-picker-label {
        color: #9CA3AF !important;
    }
    .dark .quill-wrapper .ql-toolbar button:hover .ql-stroke,
    .dark .quill-wrapper .ql-toolbar .ql-picker-label:hover .ql-stroke {
        stroke: #FCFCFC !important;
    }
    .dark .quill-wrapper .ql-toolbar button:hover .ql-fill,
    .dark .quill-wrapper .ql-toolbar .ql-picker-label:hover .ql-fill {
        fill: #FCFCFC !important;
    }
    .dark .quill-wrapper .ql-toolbar button.ql-active .ql-stroke {
        stroke: #3B82F6 !important;
    }
    .dark .quill-wrapper .ql-toolbar button.ql-active .ql-fill {
        fill: #3B82F6 !important;
    }
    .dark .quill-wrapper .ql-picker-options {
        background: #1A1A1A !important;
        border-color: #272B30 !important;
    }
    .dark .quill-wrapper .ql-picker-item {
        color: #9CA3AF !important;
    }
    .dark .quill-wrapper .ql-picker-item:hover {
        color: #FCFCFC !important;
    }
    .dark .ql-snow .ql-tooltip {
        background: #1A1A1A !important;
        border-color: #272B30 !important;
        color: #FCFCFC !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
    }
    .dark .ql-snow .ql-tooltip input[type=text] {
        background: #0B0B0B !important;
        border-color: #272B30 !important;
        color: #FCFCFC !important;
    }

    /* === Editor content styling (shared) === */
    .quill-wrapper .ql-editor h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5em;
    }
    .quill-wrapper .ql-editor h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5em;
    }
    .quill-wrapper .ql-editor h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5em;
    }
    .quill-wrapper .ql-editor blockquote {
        border-left: 4px solid #3B82F6;
        padding-left: 1rem;
        margin: 1em 0;
        color: #9CA3AF;
    }
    .quill-wrapper .ql-editor pre.ql-syntax {
        background: #111827 !important;
        color: #E5E7EB !important;
        border-radius: 8px;
        padding: 1rem;
        overflow-x: auto;
    }
    .quill-wrapper .ql-editor a {
        color: #3B82F6;
    }
    .quill-wrapper .ql-editor ul,
    .quill-wrapper .ql-editor ol {
        padding-left: 1.5em;
    }
</style>
@endPushOnce

@pushOnce('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@endPushOnce
