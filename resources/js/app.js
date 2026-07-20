import './bootstrap';

import { Editor, Node } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import OfficePaste from '@intevation/tiptap-extension-office-paste';

const CustomButton = Node.create({
    name: 'customButton',
    group: 'inline',
    inline: true,
    selectable: true,
    draggable: true,
    atom: true,

    addAttributes() {
        return {
            href: { default: '#' },
            text: { default: 'Button' },
            style: { default: 'btn-primary' },
            download: { default: false },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'a[data-type="custom-button"]',
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        const classes = ['btn', HTMLAttributes.style || 'btn-primary'];
        const attrs = {
            'data-type': 'custom-button',
            href: HTMLAttributes.href || '#',
            class: classes.join(' '),
        };

        if (HTMLAttributes.download) {
            attrs.download = '';
        }

        return ['a', attrs, HTMLAttributes.text || 'Button'];
    },
});

// Store editor instances outside Alpine's reactive scope
window._tiptapEditors = window._tiptapEditors || {};
import Sortable from 'sortablejs';
window.Sortable = Sortable;

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', (modelName = 'content') => {
        // Use closure to keep editor reference non-reactive
        let editorInstance = null;
        let editorId = null;
        
        return {
            // Button Creator State
            showButtonCreator: false,
            buttonText: '',
            buttonUrl: '',
            buttonStyle: 'btn-primary',
            buttonDownload: false,
            buttonLinkType: 'url',
            mediaPickerPurpose: 'image',

            // Return editor via function to bypass Alpine proxy
            getEditor() {
                return editorInstance;
            },
            
            init() {
                editorId = this.$el.id || 'tiptap-' + Date.now();
                
                // Check if already initialized
                if (window._tiptapEditors[editorId]) {
                    editorInstance = window._tiptapEditors[editorId];
                    return;
                }

                // Get initial content from Livewire
                const initialContent = this.$wire.get(modelName) || '';

                editorInstance = new Editor({
                    element: this.$refs.editor,
                    extensions: [
                        StarterKit.configure({
                            heading: { levels: [1, 2, 3] },
                            link: {
                                openOnClick: false,
                                HTMLAttributes: { class: 'text-blue-500 hover:underline' },
                            },
                        }),
                        Image.configure({
                            HTMLAttributes: { class: 'rounded-lg max-w-full h-auto' },
                        }),
                        CustomButton,
                        Placeholder.configure({
                            placeholder: 'Start writing your story...',
                            emptyEditorClass: 'is-editor-empty',
                        }),
                        TextAlign.configure({
                            types: ['heading', 'paragraph'],
                        }),
                        OfficePaste,
                    ],
                    editorProps: {
                        attributes: {
                            class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none min-h-[500px] p-6',
                        },
                        clipboardTextParser: (text, context) => {
                            // Check if text contains HTML tags
                            const hasHtmlTags = /<[a-z][\s\S]*>/i.test(text);
                            
                            if (hasHtmlTags) {
                                // Parse HTML string into a temporary DOM element
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(text, 'text/html');
                                
                                // Return the parsed HTML body content
                                // TipTap will convert this to proper nodes
                                return doc.body;
                            }
                            
                            // Return null to use default text parsing
                            return null;
                        },
                    },
                    content: initialContent,
                    onBlur: () => {
                        // Sync to Livewire on blur
                        this.$wire.set(modelName, editorInstance.getHTML());
                    },
                });

                window._tiptapEditors[editorId] = editorInstance;

                // Sync from Livewire model changes (e.g. Docx upload)
                this.$watch('$wire.' + modelName, (value) => {
                    if (editorInstance && value !== editorInstance.getHTML()) {
                        editorInstance.commands.setContent(value || '', false);
                    }
                });
                
                // Listen for media picker selection
                Livewire.on('tiptap-media-selected', (data) => {
                    // Only process if this is the active editor
                    if (editorInstance && window.activeTiptapEditorId === editorId) {
                        if (this.mediaPickerPurpose === 'button') {
                            this.buttonUrl = data.url;
                            if (! this.buttonText) {
                                const parts = data.url.split('/');
                                const filename = parts[parts.length - 1];
                                this.buttonText = decodeURIComponent(filename);
                            }
                        } else {
                            editorInstance.chain().focus().setImage({ 
                                src: data.url, 
                                alt: data.alt || '' 
                            }).run();
                        }
                    }
                });
            },

            // Toolbar commands - access editor via closure, not Alpine
            toggleBold() {
                if (editorInstance) editorInstance.chain().focus().toggleBold().run();
            },
            toggleItalic() {
                if (editorInstance) editorInstance.chain().focus().toggleItalic().run();
            },
            toggleStrike() {
                if (editorInstance) editorInstance.chain().focus().toggleStrike().run();
            },
            toggleHeading(level) {
                if (editorInstance) editorInstance.chain().focus().toggleHeading({ level }).run();
            },
            toggleBulletList() {
                if (editorInstance) editorInstance.chain().focus().toggleBulletList().run();
            },
            toggleOrderedList() {
                if (editorInstance) editorInstance.chain().focus().toggleOrderedList().run();
            },
            toggleBlockquote() {
                if (editorInstance) editorInstance.chain().focus().toggleBlockquote().run();
            },
            toggleCodeBlock() {
                if (editorInstance) editorInstance.chain().focus().toggleCodeBlock().run();
            },
            setHorizontalRule() {
                if (editorInstance) editorInstance.chain().focus().setHorizontalRule().run();
            },
            setLink() {
                const previousUrl = editorInstance.getAttributes('link').href;
                const url = window.prompt('URL', previousUrl);
                
                // cancelled
                if (url === null) {
                    return;
                }
                
                // empty
                if (url === '') {
                    editorInstance.chain().focus().extendMarkRange('link').unsetLink().run();
                    return;
                }
                
                // update
                editorInstance.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
            },
            unsetLink() {
                if (editorInstance) editorInstance.chain().focus().unsetLink().run();
            },
            addImage() {
                // Fallback to URL prompt if Media Picker is not available
                const url = window.prompt('Image URL');
                if (url) {
                    editorInstance.chain().focus().setImage({ src: url }).run();
                }
            },
            openMediaPicker() {
                this.mediaPickerPurpose = 'image';
                window.activeTiptapEditorId = editorId;
                Livewire.dispatch('openTiptapMediaPicker');
            },
            openButtonCreator() {
                this.buttonText = '';
                this.buttonUrl = '';
                this.buttonStyle = 'btn-primary';
                this.buttonDownload = false;
                this.buttonLinkType = 'url';
                this.showButtonCreator = true;
            },
            openButtonMediaPicker() {
                this.mediaPickerPurpose = 'button';
                window.activeTiptapEditorId = editorId;
                Livewire.dispatch('openTiptapMediaPicker');
            },
            insertButton() {
                if (! this.buttonUrl) {
                    alert('Please enter a target URL or select a file.');
                    return;
                }
                const text = this.buttonText || 'Button';
                editorInstance.chain().focus().insertContent({
                    type: 'customButton',
                    attrs: {
                        href: this.buttonUrl,
                        text: text,
                        style: this.buttonStyle,
                        download: this.buttonDownload,
                    }
                }).run();
                this.showButtonCreator = false;
            },
            // Insert image from Media Library (called via Livewire event)
            insertMediaImage(url, alt) {
                if (editorInstance && url) {
                    editorInstance.chain().focus().setImage({ src: url, alt: alt || '' }).run();
                }
            },
            setTextAlign(align) {
                if (editorInstance) editorInstance.chain().focus().setTextAlign(align).run();
            },
            undo() {
                if (editorInstance) editorInstance.chain().focus().undo().run();
            },
            redo() {
                if (editorInstance) editorInstance.chain().focus().redo().run();
            },
            clearFormatting() {
                if (editorInstance) editorInstance.chain().focus().unsetAllMarks().clearNodes().run();
            },
            isActive(name, attrs = {}) {
                return editorInstance ? editorInstance.isActive(name, attrs) : false;
            },

            syncContent() {
                if (editorInstance) {
                    this.$wire.set(modelName, editorInstance.getHTML());
                }
            },

            destroy() {
                if (editorInstance) {
                    editorInstance.destroy();
                    delete window._tiptapEditors[editorId];
                    editorInstance = null;
                }
            },
        };
    });
});

