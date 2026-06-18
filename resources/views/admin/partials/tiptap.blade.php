{{-- Tiptap Editor Styling --}}
<style>
    .tiptap-content .ProseMirror {
        outline: none;
        flex: 1;
        min-height: 100%;
        padding: 1rem;
    }
    /* Placeholder extension style */
    .tiptap-content .ProseMirror p.is-editor-empty:first-child::before {
        color: #94a3b8;
        content: attr(data-placeholder);
        float: left;
        height: 0;
        pointer-events: none;
    }
    .dark .tiptap-content .ProseMirror p.is-editor-empty:first-child::before {
        color: #475569;
    }
    /* Simple styles for table selection and cell resizing */
    .tiptap-content table {
        border-collapse: collapse;
        margin: 0;
        overflow: hidden;
        table-layout: fixed;
        width: 100%;
    }
    .tiptap-content table td,
    .tiptap-content table th {
        border: 1px solid #cbd5e1;
        box-sizing: border-box;
        min-width: 1em;
        padding: 6px 8px;
        position: relative;
        vertical-align: top;
    }
    .dark .tiptap-content table td,
    .dark .tiptap-content table th {
        border-color: #334155;
    }
    .tiptap-content table th {
        background-color: #f1f5f9;
        font-weight: bold;
        text-align: left;
    }
    .dark .tiptap-content table th {
        background-color: #1e293b;
    }
    .tiptap-content table .selectedCell::after {
        background: rgba(99, 102, 241, 0.08);
        content: "";
        left: 0; right: 0; top: 0; bottom: 0;
        pointer-events: none;
        position: absolute;
        z-index: 2;
    }
    /* Active button highlight in toolbar */
    .tiptap-toolbar button.active {
        background-color: #e0e7ff !important;
        color: #4f46e5 !important;
        border-color: #c7d2fe !important;
    }
    .dark .tiptap-toolbar button.active {
        background-color: #312e81 !important;
        color: #e0e7ff !important;
        border-color: #3730a3 !important;
    }

    /* Standalone CTA Button style in Editor */
    .ProseMirror a.cta-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff !important;
        text-decoration: none !important;
        border-radius: 9999px;
        box-shadow: 0 4px 12px 0 rgba(99, 102, 241, 0.3);
        transition: all 0.2s ease-in-out;
        border: none;
        cursor: pointer;
        margin: 0.5rem 0;
        letter-spacing: 0.025em;
    }
    .ProseMirror a.cta-button:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px 0 rgba(99, 102, 241, 0.4);
    }
    .dark .ProseMirror a.cta-button {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        box-shadow: 0 4px 12px 0 rgba(99, 102, 241, 0.2);
    }
    .dark .ProseMirror a.cta-button:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        box-shadow: 0 6px 16px 0 rgba(99, 102, 241, 0.3);
    }
</style>

{{-- Load Tiptap from Bundled Assets --}}
<script>
    // Global registry of active Tiptap instances
    window.tiptapInstances = window.tiptapInstances || {};

    // Definition of initEditor helper
    window.initEditor = function(selector, height = 500) {
        const textarea = document.querySelector(selector);
        if (!textarea) return;

        // Prevent double initialization
        const id = textarea.id || selector.replace('#', '');
        if (window.tiptapInstances[id]) {
            return;
        }

        if (!window.Tiptap) {
            console.error("Tiptap bundle not found! Make sure resources/js/app.js is compiled properly.");
            return;
        }

        const { Editor, Node, mergeAttributes, StarterKit, Underline, Image, Link, Table, TableRow, TableCell, TableHeader, Placeholder, TextAlign, Highlight, Color, TextStyle, BulletList } = window.Tiptap;

        // Custom Font Size extension by extending TextStyle
        const FontSize = TextStyle.extend({
            addAttributes() {
                return {
                    ...this.parent?.(),
                    fontSize: {
                        default: null,
                        parseHTML: element => element.style.fontSize,
                        renderHTML: attributes => {
                            if (!attributes.fontSize) {
                                return {};
                            }
                            return {
                                style: `font-size: ${attributes.fontSize}`,
                            };
                        },
                    },
                };
            },
            addCommands() {
                return {
                    ...this.parent?.(),
                    setFontSize: fontSize => ({ chain }) => {
                        return chain()
                            .setMark('textStyle', { fontSize })
                            .run();
                    },
                    unsetFontSize: () => ({ chain }) => {
                        return chain()
                            .setMark('textStyle', { fontSize: null })
                            .run();
                    },
                };
            },
        });

        // Custom CTA Button Node
        const CtaButton = Node.create({
            name: 'ctaButton',
            group: 'inline',
            inline: true,
            content: 'text*',
            atom: false,
            addAttributes() {
                return {
                    href: {
                        default: '#',
                        parseHTML: element => element.getAttribute('href'),
                        renderHTML: attributes => ({ href: attributes.href })
                    },
                    target: {
                        default: '_blank',
                        parseHTML: element => element.getAttribute('target') || '_blank',
                        renderHTML: attributes => ({ target: attributes.target })
                    }
                };
            },
            parseHTML() {
                return [{
                    tag: 'a.cta-button',
                }];
            },
            renderHTML({ HTMLAttributes }) {
                return ['a', mergeAttributes(HTMLAttributes, {
                    class: 'cta-button'
                }), 0];
            }
        });

        // Custom BulletList Node to support bullet classes (disc, circle, square, none)
        const CustomBulletList = BulletList.extend({
            addAttributes() {
                return {
                    class: {
                        default: 'list-disc',
                        parseHTML: element => element.getAttribute('class') || 'list-disc',
                        renderHTML: attributes => {
                            return { class: attributes.class };
                        }
                    }
                };
            }
        });

        // Custom Div Node to allow Slider, CTA and split layout blocks
        const DivNode = Node.create({
            name: 'div',
            group: 'block',
            content: 'block*',
            defining: true,
            addAttributes() {
                return {
                    class: { default: null },
                    style: { default: null }
                };
            },
            parseHTML() {
                return [{ tag: 'div' }];
            },
            renderHTML({ HTMLAttributes }) {
                return ['div', mergeAttributes(HTMLAttributes), 0];
            }
        });

        // Hide original textarea
        textarea.style.display = 'none';

        // Create Container
        const container = document.createElement('div');
        container.id = `tiptap-container-${id}`;
        container.className = 'tiptap-editor border border-gray-300 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm bg-white dark:bg-slate-900 transition-all flex flex-col mt-2';
        if (height) {
            container.style.height = `${height}px`;
        }
        
        // Build Toolbar and Editor structure
        container.innerHTML = `
            <div class="tiptap-toolbar flex flex-wrap gap-1 items-center p-2 bg-gray-50 dark:bg-slate-950 border-b border-gray-200 dark:border-slate-800 select-none">
                <!-- Undo/Redo -->
                <button type="button" data-cmd="undo" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Undo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                </button>
                <button type="button" data-cmd="redo" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Redo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                </button>
                
                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Headings -->
                <button type="button" data-cmd="paragraph" class="px-2 py-1 text-xs font-semibold rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Paragraph">P</button>
                <button type="button" data-cmd="h1" class="px-2 py-1 text-xs font-bold rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Heading 1">H1</button>
                <button type="button" data-cmd="h2" class="px-2 py-1 text-xs font-bold rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Heading 2">H2</button>
                <button type="button" data-cmd="h3" class="px-2 py-1 text-xs font-bold rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Heading 3">H3</button>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Formatting -->
                <button type="button" data-cmd="bold" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                </button>
                <button type="button" data-cmd="italic" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Italic">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m-4 0h4m-8 16h4"/></svg>
                </button>
                <button type="button" data-cmd="underline" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 3v7a6 6 0 0012 0V3M4 21h16"/></svg>
                </button>
                <button type="button" data-cmd="strike" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Strikethrough">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-7-6a4 4 0 018 0v1H7V6zm0 11v-1h10v1a4 4 0 01-8 0z"/></svg>
                </button>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Font Size Dropdown -->
                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="fontsize-menu-${id}" class="px-2 py-1 border border-gray-200 dark:border-slate-800 rounded bg-white dark:bg-slate-900 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-300 transition-colors flex items-center gap-1.5 text-xs font-semibold cursor-pointer" title="Font Size">
                        <span id="fontsize-preview-${id}">16px</span>
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="fontsize-menu-${id}" class="absolute left-0 mt-1 w-32 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 py-1 hidden max-h-56 overflow-y-auto">
                        <button type="button" data-fontsize="12px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-[10px] text-gray-400 w-4">12</span> 12px (Small)
                        </button>
                        <button type="button" data-fontsize="14px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-xs text-gray-400 w-4">14</span> 14px (Normal)
                        </button>
                        <button type="button" data-fontsize="16px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-sm text-gray-400 w-4">16</span> 16px (Medium)
                        </button>
                        <button type="button" data-fontsize="18px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-base text-gray-400 w-4">18</span> 18px (Large)
                        </button>
                        <button type="button" data-fontsize="20px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-lg text-gray-400 w-4">20</span> 20px (XL)
                        </button>
                        <button type="button" data-fontsize="24px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-xl text-gray-400 w-4">24</span> 24px (2XL)
                        </button>
                        <button type="button" data-fontsize="30px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-2xl text-gray-400 w-4">30</span> 30px (3XL)
                        </button>
                        <button type="button" data-fontsize="36px" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-3xl text-gray-400 w-4">36</span> 36px (4XL)
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" data-fontsize-clear class="w-full text-left px-3 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center gap-2">
                            <span class="w-4">✕</span> Clear Size
                        </button>
                    </div>
                </div>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Color & Highlight Dropdowns -->
                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="color-menu-${id}" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors flex items-center gap-0.5" title="Text Color">
                        <span class="w-4 h-4 rounded-full border border-gray-300 dark:border-slate-700 bg-black flex items-center justify-center text-[10px] text-white font-bold" id="color-preview-${id}">A</span>
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="color-menu-${id}" class="absolute left-0 mt-1 w-48 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 p-2 hidden">
                        <div class="grid grid-cols-5 gap-1 mb-2">
                            <button type="button" data-color="#000000" class="w-6 h-6 rounded border border-gray-250 bg-black" title="Black"></button>
                            <button type="button" data-color="#4b5563" class="w-6 h-6 rounded border border-gray-250 bg-gray-600" title="Gray"></button>
                            <button type="button" data-color="#ef4444" class="w-6 h-6 rounded border border-gray-250 bg-red-500" title="Red"></button>
                            <button type="button" data-color="#f97316" class="w-6 h-6 rounded border border-gray-250 bg-orange-500" title="Orange"></button>
                            <button type="button" data-color="#eab308" class="w-6 h-6 rounded border border-gray-250 bg-yellow-500" title="Yellow"></button>
                            <button type="button" data-color="#22c55e" class="w-6 h-6 rounded border border-gray-250 bg-green-500" title="Green"></button>
                            <button type="button" data-color="#06b6d4" class="w-6 h-6 rounded border border-gray-250 bg-cyan-500" title="Cyan"></button>
                            <button type="button" data-color="#3b82f6" class="w-6 h-6 rounded border border-gray-250 bg-blue-500" title="Blue"></button>
                            <button type="button" data-color="#6366f1" class="w-6 h-6 rounded border border-gray-250 bg-indigo-500" title="Indigo"></button>
                            <button type="button" data-color="#a855f7" class="w-6 h-6 rounded border border-gray-250 bg-purple-500" title="Purple"></button>
                        </div>
                        <div class="flex items-center gap-2 pt-1.5 border-t border-gray-100 dark:border-slate-700">
                            <span class="text-[10px] text-gray-500 dark:text-slate-400">Custom:</span>
                            <input type="color" id="custom-color-input-${id}" class="w-6 h-6 p-0 border-0 bg-transparent cursor-pointer rounded" />
                            <button type="button" data-color-clear class="text-[10px] text-red-500 hover:underline ml-auto">Clear</button>
                        </div>
                    </div>
                </div>

                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="highlight-menu-${id}" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors flex items-center gap-0.5" title="Highlight Text">
                        <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 24 24">
                            <path d="M15.24 3.585a.75.75 0 011.06 0l4.115 4.115a.75.75 0 010 1.06l-11.23 11.23-4.115-4.115 11.23-11.23zm-.53 4.175l-9.11 9.11 2.525 2.525 9.11-9.11-2.525-2.525zM3.44 19.345a.75.75 0 00-.94.94c.32.96 1.135 1.775 2.095 2.095a.75.75 0 00.94-.94l-2.095-2.095z" />
                        </svg>
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="highlight-menu-${id}" class="absolute left-0 mt-1 w-48 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 p-2 hidden">
                        <div class="grid grid-cols-5 gap-1 mb-2">
                            <button type="button" data-highlight="#fef08a" class="w-6 h-6 rounded border border-gray-250 bg-yellow-200" title="Yellow"></button>
                            <button type="button" data-highlight="#bbf7d0" class="w-6 h-6 rounded border border-gray-250 bg-green-200" title="Green"></button>
                            <button type="button" data-highlight="#bfdbfe" class="w-6 h-6 rounded border border-gray-250 bg-blue-200" title="Blue"></button>
                            <button type="button" data-highlight="#fbcfe8" class="w-6 h-6 rounded border border-gray-250 bg-pink-200" title="Pink"></button>
                            <button type="button" data-highlight="#e9d5ff" class="w-6 h-6 rounded border border-gray-250 bg-purple-200" title="Purple"></button>
                            <button type="button" data-highlight="#ffedd5" class="w-6 h-6 rounded border border-gray-250 bg-orange-100" title="Orange"></button>
                            <button type="button" data-highlight="#ccfbf1" class="w-6 h-6 rounded border border-gray-250 bg-teal-100" title="Teal"></button>
                            <button type="button" data-highlight="#fee2e2" class="w-6 h-6 rounded border border-gray-250 bg-red-100" title="Red"></button>
                            <button type="button" data-highlight="#e0e7ff" class="w-6 h-6 rounded border border-gray-250 bg-indigo-100" title="Indigo"></button>
                        </div>
                        <div class="flex items-center gap-2 pt-1.5 border-t border-gray-100 dark:border-slate-700">
                            <span class="text-[10px] text-gray-500 dark:text-slate-400">Custom:</span>
                            <input type="color" id="custom-highlight-input-${id}" class="w-6 h-6 p-0 border-0 bg-transparent cursor-pointer rounded" />
                            <button type="button" data-highlight-clear class="text-[10px] text-red-500 hover:underline ml-auto">Clear</button>
                        </div>
                    </div>
                </div>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Lists & Blockquote -->
                <div class="relative dropdown-container flex items-center border border-gray-200 dark:border-slate-800 rounded bg-white dark:bg-slate-900">
                    <button type="button" data-cmd="bulletList" class="p-1.5 rounded-l text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors border-r border-gray-200 dark:border-slate-800" title="Bullet List">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M9 6H9.01M9 12H9.01M9 18H9.01"/></svg>
                    </button>
                    <button type="button" data-dropdown-toggle="bullet-style-menu-${id}" class="p-1 px-1.5 rounded-r text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors flex items-center justify-center" title="Choose Bullet Style">
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="bullet-style-menu-${id}" class="absolute left-0 mt-8 w-44 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 py-1 hidden" style="top: 100%;">
                        <button type="button" data-bullet-style="list-disc" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gray-900 dark:bg-slate-100 inline-block"></span> 🔵 Filled Circle (Disc)
                        </button>
                        <button type="button" data-bullet-style="list-circle" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full border border-gray-950 dark:border-slate-100 inline-block text-center flex items-center justify-center"></span> ⚪ Hollow Circle (Circle)
                        </button>
                        <button type="button" data-bullet-style="list-square" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="w-2 h-2 bg-gray-900 dark:bg-slate-100 inline-block"></span> ⬛ Square (Square)
                        </button>
                        <button type="button" data-bullet-style="list-none" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span class="text-gray-400 font-mono text-[9px] w-2 flex items-center justify-center">∅</span> 🚫 None (Plain List)
                        </button>
                    </div>
                </div>

                <button type="button" data-cmd="orderedList" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Ordered List">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/></svg>
                </button>
                <button type="button" data-cmd="blockquote" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Blockquote">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </button>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Alignment -->
                <button type="button" data-cmd="alignLeft" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Align Left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h12M4 18h16"/></svg>
                </button>
                <button type="button" data-cmd="alignCenter" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Align Center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M4 18h16"/></svg>
                </button>
                <button type="button" data-cmd="alignRight" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Align Right">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M12 12h8M4 18h16"/></svg>
                </button>
                <button type="button" data-cmd="alignJustify" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Align Justify">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Link & Image Dropdown -->
                <button type="button" data-cmd="link" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Insert Link">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </button>

                <!-- Select Image Dropdown -->
                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="image-menu-${id}" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors flex items-center gap-0.5" title="Insert Image">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="image-menu-${id}" class="absolute left-0 mt-1 w-44 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 py-1 hidden">
                        <button type="button" data-tiptap-action="upload-image" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span>📤</span> Upload Local File
                        </button>
                        <button type="button" data-cmd="insertSlider" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2 border-t border-gray-100 dark:border-slate-700">
                            <span>🎠</span> Image Slider
                        </button>
                        <button type="button" data-tiptap-action="remove-image" class="w-full text-left px-3 py-1.5 text-xs text-red-650 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center gap-2 border-t border-gray-100 dark:border-slate-700">
                            <span>🗑️</span> Remove Selected Image
                        </button>
                    </div>
                </div>

                <!-- Table Dropdown -->
                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="table-menu-${id}" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors flex items-center gap-0.5" title="Table Controls">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="table-menu-${id}" class="absolute left-0 mt-1 w-44 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 py-1 hidden">
                        <button type="button" data-cmd="insertTable" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span>➕</span> Insert Table (3x3)
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" data-cmd="addColumnBefore" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650">Add Column Before</button>
                        <button type="button" data-cmd="addColumnAfter" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650">Add Column After</button>
                        <button type="button" data-cmd="deleteColumn" class="w-full text-left px-3 py-1.5 text-xs text-red-650 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20">Delete Column</button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" data-cmd="addRowBefore" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650">Add Row Before</button>
                        <button type="button" data-cmd="addRowAfter" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650">Add Row After</button>
                        <button type="button" data-cmd="deleteRow" class="w-full text-left px-3 py-1.5 text-xs text-red-650 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20">Delete Row</button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" data-cmd="deleteTable" class="w-full text-left px-3 py-1.5 text-xs text-red-650 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center gap-2">
                            <span>🗑️</span> Delete Table
                        </button>
                    </div>
                </div>
                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Insert CTA Button -->
                <button type="button" data-cmd="insertCTA" class="px-2 py-1 border border-emerald-200 dark:border-emerald-900 rounded bg-emerald-50/50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 transition-colors flex items-center gap-1 text-xs font-semibold cursor-pointer" title="Insert Call-To-Action (CTA) Box">
                    <span>📢 CTA</span>
                </button>
                <button type="button" data-cmd="insertCTAButton" class="px-2 py-1 border border-indigo-200 dark:border-indigo-900 rounded bg-indigo-50/50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 transition-colors flex items-center gap-1 text-xs font-semibold cursor-pointer" title="Insert standalone CTA Button">
                    <span>🔘 CTA Button</span>
                </button>

                <!-- Layout Blocks -->
                <div class="relative dropdown-container">
                    <button type="button" data-dropdown-toggle="blocks-menu-${id}" class="px-2 py-1 border border-indigo-200 dark:border-indigo-900 rounded bg-indigo-50/50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 transition-colors flex items-center gap-0.5 text-xs font-semibold cursor-pointer" title="Custom Layout Blocks">
                        <span>🧩 Blocks</span>
                        <svg class="w-2.5 h-2.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="blocks-menu-${id}" class="absolute left-0 mt-1 w-48 rounded-md shadow-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 z-50 py-1 hidden">
                        <button type="button" data-cmd="insertSlider" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span>🖼️</span> Insert Slider
                        </button>
                        <button type="button" data-cmd="insertImageLeft" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span>⬅️</span> Image Left Block
                        </button>
                        <button type="button" data-cmd="insertImageRight" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2">
                            <span>➡️</span> Image Right Block
                        </button>
                        <button type="button" data-cmd="insertCTA" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2 border-t border-gray-100 dark:border-slate-700">
                            <span>📢</span> Insert Post CTA
                        </button>
                        <button type="button" data-cmd="insertCTAButton" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-650 flex items-center gap-2 border-t border-gray-100 dark:border-slate-700">
                            <span>🔘</span> Insert CTA Button
                        </button>
                    </div>
                </div>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1"></span>

                <!-- Clear formatting -->
                <button type="button" data-cmd="clear" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="Clear Formatting">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </button>

                <span class="w-px h-5 bg-gray-200 dark:bg-slate-800 mx-1 ml-auto"></span>

                <!-- Code View -->
                <button type="button" data-cmd="codeView" class="p-1.5 rounded text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-slate-100 transition-colors" title="HTML Source Code View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </button>
            </div>
            <div class="tiptap-content-wrapper flex-1 flex flex-col overflow-y-auto relative">
                <div class="tiptap-content prose dark:prose-invert max-w-none flex-1 flex flex-col bg-white dark:bg-slate-900 text-gray-850 dark:text-slate-200"></div>
                <textarea class="tiptap-code-view hidden w-full h-full min-h-[300px] border-0 p-4 outline-none resize-none font-mono text-sm bg-slate-900 text-slate-100 focus:ring-0 dark:bg-slate-950 dark:text-slate-200" style="flex: 1; min-height: 100%; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;"></textarea>
            </div>
            <input type="file" class="tiptap-file-upload hidden" accept="image/*">
        `;

        // Insert container right after textarea
        textarea.parentNode.insertBefore(container, textarea.nextSibling);

        const editorElement = container.querySelector('.tiptap-content');
        const fileInput = container.querySelector('.tiptap-file-upload');
        const codeTextArea = container.querySelector('.tiptap-code-view');
        let codeViewActive = false;

        // Create the Tiptap Editor instance
        let editor;
        try {
            editor = new Editor({
                element: editorElement,
                extensions: [
                    StarterKit.configure({
                        bulletList: false, // Disable to use custom extended bulletList
                        link: {
                            openOnClick: false,
                            HTMLAttributes: {
                                class: 'text-indigo-600 dark:text-indigo-400 hover:underline',
                                target: '_blank',
                                rel: 'noopener noreferrer'
                            }
                        },
                        underline: {}
                    }),
                    Image.configure({
                        HTMLAttributes: {
                            class: 'rounded-lg max-w-full my-4 mx-auto block shadow-sm border border-gray-200 dark:border-slate-800'
                        }
                    }),
                    Table.configure({
                        resizable: true,
                        HTMLAttributes: {
                            class: 'border-collapse border border-gray-300 dark:border-slate-800 my-4 w-full'
                        }
                    }),
                    TableRow,
                    TableCell.configure({
                        HTMLAttributes: {
                            class: 'border border-gray-300 dark:border-slate-800 px-4 py-2 text-sm'
                        }
                    }),
                    TableHeader.configure({
                        HTMLAttributes: {
                            class: 'border border-gray-300 dark:border-slate-800 px-4 py-2 text-sm bg-gray-50 dark:bg-slate-800 font-bold'
                        }
                    }),
                    Placeholder.configure({
                        placeholder: textarea.placeholder || 'Start typing post body content here...'
                    }),
                    TextAlign.configure({
                        types: ['heading', 'paragraph'],
                    }),
                    Highlight.configure({
                        multicolor: true
                    }),
                    FontSize,
                    Color,
                    CustomBulletList,
                    DivNode,
                    CtaButton
                ],
                content: textarea.value || '',
                onUpdate({ editor }) {
                    if (codeViewActive) return; // If code view is active, it handles updates directly
                    
                    const html = editor.getHTML();
                    textarea.value = html === '<p></p>' ? '' : html;
                    
                    // Trigger events so any listener (e.g. dynamic state) registers the change
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));

                    // Keep CTA section visible if post-cta exists in content
                    if (window.checkCtaPlaceholder) {
                        window.checkCtaPlaceholder(html);
                    }
                },
                onSelectionUpdate({ editor }) {
                    updateToolbarStates(editor, container);
                },
                onFocus() {
                    container.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500');
                },
                onBlur() {
                    container.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500');
                },
                editorProps: {
                    handleDrop(view, event, slice, moved) {
                        if (!moved && event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
                            const files = Array.from(event.dataTransfer.files);
                            const images = files.filter(file => file.type.startsWith('image/'));
                            if (images.length > 0) {
                                event.preventDefault();
                                const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY });
                                const pos = coordinates ? coordinates.pos : view.state.selection.from;
                                images.forEach(file => {
                                    window.registerAndLogImageFile(file);
                                    uploadImageAndInsert(view, file, pos);
                                });
                                return true;
                            }
                        }
                        return false;
                    },
                    handlePaste(view, event) {
                        if (event.clipboardData && event.clipboardData.files && event.clipboardData.files.length) {
                            const files = Array.from(event.clipboardData.files);
                            const images = files.filter(file => file.type.startsWith('image/'));
                            if (images.length > 0) {
                                event.preventDefault();
                                images.forEach(file => {
                                    window.registerAndLogImageFile(file);
                                    uploadImageAndInsert(view, file, view.state.selection.from);
                                });
                                return true;
                            }
                        }
                        return false;
                    }
                }
            });
        } catch (e) {
            console.error("Failed to initialize Tiptap Editor:", e);
            const errDiv = document.createElement('div');
            errDiv.className = 'bg-red-50 dark:bg-red-950/20 border-b border-red-200 dark:border-red-900 text-red-800 dark:text-red-300 p-4 text-xs font-mono';
            errDiv.innerHTML = `<strong>Error initializing editor:</strong> ${e.message}<br><small>${e.stack}</small>`;
            container.insertBefore(errDiv, container.firstChild);
            return;
        }

        // Store instance in the global registry
        window.tiptapInstances[id] = editor;

        // Create or find image delete overlay button
        const wrapper = container.querySelector('.tiptap-content-wrapper');
        let deleteBtn = container.querySelector('.tiptap-image-delete-btn');
        if (!deleteBtn) {
            deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'tiptap-image-delete-btn absolute bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-lg px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1.5 transition-all z-40 hidden duration-200 ease-out active:scale-95';
            deleteBtn.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Remove Image
            `;
            wrapper.appendChild(deleteBtn);
        }

        let activeImage = null;

        function positionDeleteButton(img) {
            if (!img || !deleteBtn) return;
            const imgRect = img.getBoundingClientRect();
            const wrapperRect = wrapper.getBoundingClientRect();
            
            const top = imgRect.top - wrapperRect.top + wrapper.scrollTop + 8;
            const left = imgRect.left - wrapperRect.left + wrapper.scrollLeft + imgRect.width - deleteBtn.offsetWidth - 8;
            
            deleteBtn.style.top = `${top}px`;
            deleteBtn.style.left = `${left}px`;
            deleteBtn.classList.remove('hidden');
        }

        function hideDeleteButton() {
            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
            }
            activeImage = null;
        }

        function uploadImageAndInsert(view, file, pos) {
            const formData = new FormData();
            formData.append('file', file);

            const notification = document.createElement('div');
            notification.className = 'fixed bottom-5 right-5 bg-indigo-650 text-white px-4 py-2 rounded-lg shadow-lg text-xs z-[9999] flex items-center gap-2 animate-bounce';
            notification.innerHTML = '<span>⏳</span> Uploading image...';
            document.body.appendChild(notification);

            fetch('{{ route('admin.media.json-upload', [], false) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => {
                notification.remove();
                if (!res.ok) throw new Error('Network error during upload');
                return res.json();
            })
            .then(data => {
                if (data && data.location) {
                    const node = view.state.schema.nodes.image.create({ src: data.location, alt: file.name });
                    const transaction = view.state.tr.insert(pos, node);
                    view.dispatch(transaction);
                } else {
                    alert('Invalid response from server.');
                }
            })
            .catch(err => {
                notification.remove();
                alert('Upload failed: ' + err.message);
            });
        }

        deleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (activeImage && editor) {
                const pos = editor.view.posAtDOM(activeImage, 0);
                if (pos !== undefined) {
                    editor.chain().focus().deleteRange({ from: pos, to: pos + 1 }).run();
                }
                hideDeleteButton();
            }
        });

        wrapper.addEventListener('scroll', () => {
            if (activeImage) {
                positionDeleteButton(activeImage);
            }
        });

        editor.on('blur', () => {
            setTimeout(() => {
                if (document.activeElement !== deleteBtn && !deleteBtn.contains(document.activeElement)) {
                    hideDeleteButton();
                }
            }, 150);
        });

        editor.on('update', () => {
            hideDeleteButton();
        });

        // Auto-focus editor when clicking anywhere on the content area
        editorElement.addEventListener('click', (e) => {
            if (codeViewActive) return;
            const img = e.target.closest('img');
            if (img && editorElement.contains(img)) {
                activeImage = img;
                setTimeout(() => {
                    positionDeleteButton(img);
                }, 10);
            } else {
                if (!e.target.closest('.tiptap-image-delete-btn')) {
                    hideDeleteButton();
                }
            }
            if (e.target === editorElement || e.target.classList.contains('ProseMirror')) {
                editor.commands.focus();
            }
        });

        // Dropdown toggle logic
        const dropdownToggles = container.querySelectorAll('[data-dropdown-toggle]');
        const dropdownMenus = container.querySelectorAll('[id^="image-menu-"], [id^="table-menu-"], [id^="blocks-menu-"], [id^="color-menu-"], [id^="highlight-menu-"], [id^="bullet-style-menu-"], [id^="fontsize-menu-"]');

        function closeAllDropdowns() {
            dropdownMenus.forEach(menu => menu.classList.add('hidden'));
        }

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                if (codeViewActive) return;
                const targetId = toggle.getAttribute('data-dropdown-toggle');
                const targetMenu = container.querySelector('[id="' + targetId + '"]');
                if (targetMenu) {
                    const isHidden = targetMenu.classList.contains('hidden');
                    closeAllDropdowns();
                    if (isHidden) {
                        targetMenu.classList.remove('hidden');
                    }
                }
            });
        });

        // Close dropdowns on document click
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                closeAllDropdowns();
            } else {
                if (!e.target.closest('.dropdown-container')) {
                    closeAllDropdowns();
                }
            }
        });

        // Code View Textarea updates sync
        codeTextArea.addEventListener('input', () => {
            const html = codeTextArea.value;
            textarea.value = html === '<p></p>' ? '' : html;
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            
            if (window.checkCtaPlaceholder) {
                window.checkCtaPlaceholder(html);
            }
        });

        // Code View Toggle Logic
        const codeViewButton = container.querySelector('button[data-cmd="codeView"]');
        const buttonsToDisable = container.querySelectorAll('.tiptap-toolbar button:not([data-cmd="codeView"])');
        const dropdownsToDisable = container.querySelectorAll('.tiptap-toolbar .dropdown-container');

        function toggleCodeView() {
            codeViewActive = !codeViewActive;
            if (codeViewActive) {
                // Populate and show textarea
                codeTextArea.value = editor.getHTML();
                editorElement.classList.add('hidden');
                codeTextArea.classList.remove('hidden');
                codeViewButton.classList.add('active');

                // Disable and fade other toolbar buttons
                buttonsToDisable.forEach(btn => btn.classList.add('opacity-40', 'pointer-events-none'));
                dropdownsToDisable.forEach(d => d.classList.add('opacity-40', 'pointer-events-none'));
                
                closeAllDropdowns();
                codeTextArea.focus();
            } else {
                // Load updated content back to ProseMirror
                const html = codeTextArea.value;
                editor.commands.setContent(html);
                
                codeTextArea.classList.add('hidden');
                editorElement.classList.remove('hidden');
                codeViewButton.classList.remove('active');

                // Re-enable toolbar buttons
                buttonsToDisable.forEach(btn => btn.classList.remove('opacity-40', 'pointer-events-none'));
                dropdownsToDisable.forEach(d => d.classList.remove('opacity-40', 'pointer-events-none'));

                // Final sync
                textarea.value = html === '<p></p>' ? '' : html;
                textarea.dispatchEvent(new Event('change', { bubbles: true }));
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                
                editor.commands.focus();
            }
        }

        // Toolbar Event Listeners
        const toolbar = container.querySelector('.tiptap-toolbar');
        toolbar.querySelectorAll('button[data-cmd]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const cmd = button.getAttribute('data-cmd');
                
                if (cmd === 'codeView') {
                    toggleCodeView();
                    return;
                }

                if (codeViewActive) return;

                closeAllDropdowns();
                
                if (cmd === 'undo') editor.chain().focus().undo().run();
                else if (cmd === 'redo') editor.chain().focus().redo().run();
                else if (cmd === 'paragraph') editor.chain().focus().setParagraph().run();
                else if (cmd === 'h1') editor.chain().focus().toggleHeading({ level: 1 }).run();
                else if (cmd === 'h2') editor.chain().focus().toggleHeading({ level: 2 }).run();
                else if (cmd === 'h3') editor.chain().focus().toggleHeading({ level: 3 }).run();
                else if (cmd === 'bold') editor.chain().focus().toggleBold().run();
                else if (cmd === 'italic') editor.chain().focus().toggleItalic().run();
                else if (cmd === 'underline') editor.chain().focus().toggleUnderline().run();
                else if (cmd === 'strike') editor.chain().focus().toggleStrike().run();
                else if (cmd === 'bulletList') editor.chain().focus().toggleBulletList().run();
                else if (cmd === 'orderedList') editor.chain().focus().toggleOrderedList().run();
                else if (cmd === 'blockquote') editor.chain().focus().toggleBlockquote().run();
                else if (cmd === 'alignLeft') editor.chain().focus().setTextAlign('left').run();
                else if (cmd === 'alignCenter') editor.chain().focus().setTextAlign('center').run();
                else if (cmd === 'alignRight') editor.chain().focus().setTextAlign('right').run();
                else if (cmd === 'alignJustify') editor.chain().focus().setTextAlign('justify').run();
                else if (cmd === 'clear') editor.chain().focus().clearNodes().unsetAllMarks().run();
                else if (cmd === 'insertTable') editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
                else if (cmd === 'addColumnBefore') editor.chain().focus().addColumnBefore().run();
                else if (cmd === 'addColumnAfter') editor.chain().focus().addColumnAfter().run();
                else if (cmd === 'deleteColumn') editor.chain().focus().deleteColumn().run();
                else if (cmd === 'addRowBefore') editor.chain().focus().addRowBefore().run();
                else if (cmd === 'addRowAfter') editor.chain().focus().addRowAfter().run();
                else if (cmd === 'deleteRow') editor.chain().focus().deleteRow().run();
                else if (cmd === 'deleteTable') editor.chain().focus().deleteTable().run();
                else if (cmd === 'link') {
                    const previousUrl = editor.getAttributes('link').href;
                    const url = window.prompt('Enter Link URL:', previousUrl || 'https://');
                    if (url === '') {
                        editor.chain().focus().extendMarkRange('link').unsetLink().run();
                    } else if (url) {
                        editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
                    }
                }
                else if (cmd === 'insertSlider') {
                    if (window.openMediaPicker) {
                        const urls = [];
                        const modal = document.querySelector('[x-data="mediaPicker()"]');
                        
                        const getNextImage = () => {
                            let selectedThisTurn = false;
                            let observer = null;
                            
                            if (modal) {
                                observer = new MutationObserver(() => {
                                    if (modal.style.display === 'none') {
                                        observer.disconnect();
                                        // Wait a moment for Alpine.js/DOM to settle
                                        setTimeout(() => {
                                            if (!selectedThisTurn) {
                                                insertCollectedSlider();
                                            }
                                        }, 100);
                                    }
                                });
                                observer.observe(modal, { attributes: true, attributeFilter: ['style'] });
                            }
                            
                            window.openMediaPicker(function(url, meta) {
                                selectedThisTurn = true;
                                if (observer) observer.disconnect();
                                
                                if (window.registerAndLogImageDetails) {
                                    window.registerAndLogImageDetails(meta.alt, meta.mime_type, meta.file_size);
                                }
                                urls.push({ url: url, alt: meta.alt || '' });
                                
                                setTimeout(() => {
                                    if (confirm('Image added! Would you like to select another image to this slider?')) {
                                        getNextImage();
                                    } else {
                                        insertCollectedSlider();
                                    }
                                }, 250);
                            });
                        };
                        
                        const insertCollectedSlider = () => {
                            if (urls.length === 0) return;
                            let imagesHtml = '';
                            urls.forEach(item => {
                                imagesHtml += '  <p><img src="' + item.url + '" style="width:150px; height:100px; object-fit:cover; border-radius:4px;" alt="' + item.alt + '" /></p>';
                            });
                            editor.chain().focus().insertContent(
                                '<div class="post-slider bg-gray-50 dark:bg-slate-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 flex gap-4 overflow-x-auto min-h-[120px] items-center justify-start">' +
                                imagesHtml +
                                '</div><p></p>'
                            ).run();
                        };
                        
                        getNextImage();
                    } else {
                        const url = window.prompt('Enter Image URL to start slider:');
                        if (url) {
                            editor.chain().focus().insertContent(
                                '<div class="post-slider bg-gray-50 dark:bg-slate-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 flex gap-4 overflow-x-auto min-h-[120px] items-center justify-start">' +
                                '  <p><img src="' + url + '" style="width:150px; height:100px; object-fit:cover; border-radius:4px;" /></p>' +
                                '</div><p></p>'
                            ).run();
                        }
                    }
                }
                else if (cmd === 'insertImageLeft') {
                    editor.chain().focus().insertContent(
                        '<div class="image-split">' +
                        '  <p><img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800" alt="Coding" /></p>' +
                        '  <div>' +
                        '    <h3>Topic Title</h3>' +
                        '    <p>Replace this text with your actual content. The image and text will display side-by-side on desktop and stack vertically on mobile screens.</p>' +
                        '  </div>' +
                        '</div><p></p>'
                    ).run();
                }
                else if (cmd === 'insertImageRight') {
                    editor.chain().focus().insertContent(
                        '<div class="image-split image-right">' +
                        '  <p><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800" alt="Data" /></p>' +
                        '  <div>' +
                        '    <h3>Topic Title</h3>' +
                        '    <p>Replace this text with your actual content. The image will appear on the right side of the text on desktop screens.</p>' +
                        '  </div>' +
                        '</div><p></p>'
                    ).run();
                }
                else if (cmd === 'insertCTA') {
                    editor.chain().focus().insertContent(
                        '<div class="post-cta bg-indigo-50/50 dark:bg-slate-900/30 p-6 rounded-2xl border border-indigo-200 dark:border-slate-800 text-center my-8">' +
                        '  <p class="text-sm font-semibold text-indigo-650 dark:text-indigo-400">Post-Specific CTA Placeholder</p>' +
                        '  <p class="text-xs text-gray-500">Your dynamic Call-To-Action and links directory will render here on the frontend.</p>' +
                        '</div><p></p>'
                    ).run();
                }
                else if (cmd === 'insertCTAButton') {
                    const text = window.prompt('Enter Button Text:', 'Click Here');
                    if (!text) return;
                    const url = window.prompt('Enter Button URL:', 'https://');
                    if (!url) return;
                    editor.chain().focus().insertContent(`<a class="cta-button" href="${url}" target="_blank" rel="noopener noreferrer">${text}</a> `).run();
                }

                updateToolbarStates(editor, container);
            });
        });

        // Bullet list style menu action handlers
        const bulletStyleMenu = container.querySelector(`#bullet-style-menu-${id}`);
        if (bulletStyleMenu) {
            bulletStyleMenu.querySelectorAll('[data-bullet-style]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    const styleClass = btn.getAttribute('data-bullet-style');
                    
                    // Activate bullet list node first if it's not active
                    if (!editor.isActive('bulletList')) {
                        editor.chain().focus().toggleBulletList().run();
                    }
                    
                    // Set class attribute to the active list node
                    editor.chain().focus().updateAttributes('bulletList', { class: styleClass }).run();
                    closeAllDropdowns();
                    updateToolbarStates(editor, container);
                });
            });
        }

        // Color Picker actions
        const colorMenu = container.querySelector(`#color-menu-${id}`);
        if (colorMenu) {
            colorMenu.querySelectorAll('[data-color]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    const color = btn.getAttribute('data-color');
                    editor.chain().focus().setColor(color).run();
                    closeAllDropdowns();
                });
            });
            const customColorInput = colorMenu.querySelector(`#custom-color-input-${id}`);
            if (customColorInput) {
                customColorInput.addEventListener('input', (e) => {
                    if (codeViewActive) return;
                    editor.chain().focus().setColor(e.target.value).run();
                });
                customColorInput.addEventListener('change', () => {
                    closeAllDropdowns();
                });
            }
            const colorClear = colorMenu.querySelector('[data-color-clear]');
            if (colorClear) {
                colorClear.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    editor.chain().focus().unsetColor().run();
                    closeAllDropdowns();
                });
            }
        }

        // Font Size dropdown action handlers
        const fontSizeMenu = container.querySelector(`#fontsize-menu-${id}`);
        if (fontSizeMenu) {
            fontSizeMenu.querySelectorAll('[data-fontsize]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    const size = btn.getAttribute('data-fontsize');
                    editor.chain().focus().setFontSize(size).run();
                    closeAllDropdowns();
                    updateToolbarStates(editor, container);
                });
            });
            const fontSizeClear = fontSizeMenu.querySelector('[data-fontsize-clear]');
            if (fontSizeClear) {
                fontSizeClear.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    editor.chain().focus().unsetFontSize().run();
                    closeAllDropdowns();
                    updateToolbarStates(editor, container);
                });
            }
        }

        // Highlight Picker actions
        const highlightMenu = container.querySelector(`#highlight-menu-${id}`);
        if (highlightMenu) {
            highlightMenu.querySelectorAll('[data-highlight]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    const highlightColor = btn.getAttribute('data-highlight');
                    editor.chain().focus().setHighlight({ color: highlightColor }).run();
                    closeAllDropdowns();
                });
            });
            const customHighlightInput = highlightMenu.querySelector(`#custom-highlight-input-${id}`);
            if (customHighlightInput) {
                customHighlightInput.addEventListener('input', (e) => {
                    if (codeViewActive) return;
                    editor.chain().focus().setHighlight({ color: e.target.value }).run();
                });
                customHighlightInput.addEventListener('change', () => {
                    closeAllDropdowns();
                });
            }
            const highlightClear = highlightMenu.querySelector('[data-highlight-clear]');
            if (highlightClear) {
                highlightClear.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (codeViewActive) return;
                    editor.chain().focus().unsetHighlight().run();
                    closeAllDropdowns();
                });
            }
        }

        // Image Selection Action Handlers
        const galleryBtn = container.querySelector('[data-tiptap-action="gallery-image"]');
        const uploadBtn = container.querySelector('[data-tiptap-action="upload-image"]');

        if (galleryBtn) {
            galleryBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (codeViewActive) return;
                closeAllDropdowns();
                if (window.openMediaPicker) {
                    window.openMediaPicker(function(url, meta) {
                        if (window.registerAndLogImageDetails) {
                            window.registerAndLogImageDetails(meta.alt, meta.mime_type, meta.file_size);
                        }
                        editor.chain().focus().setImage({ src: url, alt: meta.alt || '' }).run();
                    });
                } else {
                    alert('Media library is loading, please try again.');
                }
            });
        }

        if (uploadBtn) {
            uploadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (codeViewActive) return;
                closeAllDropdowns();
                fileInput.click();
            });
        }

        const removeImageBtn = container.querySelector('[data-tiptap-action="remove-image"]');
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (codeViewActive) return;
                closeAllDropdowns();
                
                if (activeImage && editor) {
                    const pos = editor.view.posAtDOM(activeImage, 0);
                    if (pos !== undefined) {
                        editor.chain().focus().deleteRange({ from: pos, to: pos + 1 }).run();
                    }
                    hideDeleteButton();
                } else if (editor && editor.state.selection.node && editor.state.selection.node.type.name === 'image') {
                    editor.chain().focus().deleteSelection().run();
                } else {
                    alert('Please select or click an image inside the editor to remove it.');
                }
            });
        }

        // Asynchronous image uploader
        fileInput.addEventListener('change', (e) => {
            if (codeViewActive) return;
            const file = e.target.files[0];
            if (!file) return;

            if (window.registerAndLogImageFile) {
                window.registerAndLogImageFile(file);
            }

            const formData = new FormData();
            formData.append('file', file);

            // Temporarily insert a uploading placeholder or show a prompt
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-5 right-5 bg-indigo-650 text-white px-4 py-2 rounded-lg shadow-lg text-xs z-[9999] flex items-center gap-2 animate-bounce';
            notification.innerHTML = '<span>⏳</span> Uploading image...';
            document.body.appendChild(notification);

            fetch('{{ route('admin.media.json-upload', [], false) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => {
                notification.remove();
                if (!res.ok) throw new Error('Network error during upload');
                return res.json();
            })
            .then(data => {
                if (data && data.location) {
                    editor.chain().focus().setImage({ src: data.location, alt: file.name }).run();
                } else {
                    alert('Invalid response from server.');
                }
            })
            .catch(err => {
                notification.remove();
                alert('Upload failed: ' + err.message);
            });

            // Reset file input
            fileInput.value = '';
        });

        // Run initial check for CTA visibility
        if (window.checkCtaPlaceholder) {
            window.checkCtaPlaceholder(textarea.value);
        }
    };

    // Helper to toggle active state of toolbar buttons based on selection
    function updateToolbarStates(editor, container) {
        if (!editor) return;

        container.querySelectorAll('button[data-cmd]').forEach(button => {
            const cmd = button.getAttribute('data-cmd');
            if (cmd === 'codeView') return; // code view active state managed manually
            
            if (cmd === 'bold') {
                button.classList.toggle('active', editor.isActive('bold'));
            } else if (cmd === 'italic') {
                button.classList.toggle('active', editor.isActive('italic'));
            } else if (cmd === 'underline') {
                button.classList.toggle('active', editor.isActive('underline'));
            } else if (cmd === 'strike') {
                button.classList.toggle('active', editor.isActive('strike'));
            } else if (cmd === 'bulletList') {
                button.classList.toggle('active', editor.isActive('bulletList'));
            } else if (cmd === 'orderedList') {
                button.classList.toggle('active', editor.isActive('orderedList'));
            } else if (cmd === 'blockquote') {
                button.classList.toggle('active', editor.isActive('blockquote'));
            } else if (cmd === 'paragraph') {
                button.classList.toggle('active', editor.isActive('paragraph'));
            } else if (cmd === 'h1') {
                button.classList.toggle('active', editor.isActive('heading', { level: 1 }));
            } else if (cmd === 'h2') {
                button.classList.toggle('active', editor.isActive('heading', { level: 2 }));
            } else if (cmd === 'h3') {
                button.classList.toggle('active', editor.isActive('heading', { level: 3 }));
            } else if (cmd === 'alignLeft') {
                button.classList.toggle('active', editor.isActive({ textAlign: 'left' }));
            } else if (cmd === 'alignCenter') {
                button.classList.toggle('active', editor.isActive({ textAlign: 'center' }));
            } else if (cmd === 'alignRight') {
                button.classList.toggle('active', editor.isActive({ textAlign: 'right' }));
            } else if (cmd === 'alignJustify') {
                button.classList.toggle('active', editor.isActive({ textAlign: 'justify' }));
            }
        });

        // Update Text Color indicator preview
        const id = container.id.replace('tiptap-container-', '');

        // Update Font Size indicator preview
        const fontSizePreview = container.querySelector(`#fontsize-preview-${id}`);
        if (fontSizePreview) {
            const currentFontSize = editor.getAttributes('textStyle').fontSize || '16px';
            fontSizePreview.innerText = currentFontSize;
        }

        const colorPreview = container.querySelector(`#color-preview-${id}`);
        if (colorPreview) {
            const currentColor = editor.getAttributes('textStyle').color || '#000000';
            colorPreview.style.backgroundColor = currentColor;
            if (currentColor === 'transparent' || currentColor === 'rgba(0,0,0,0)' || currentColor === '') {
                colorPreview.style.color = '#000000';
            } else {
                // simple brightness check to determine black/white text contrast
                const hex = currentColor.replace('#', '');
                if (hex.length === 6) {
                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);
                    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                    colorPreview.style.color = brightness > 125 ? '#000000' : '#ffffff';
                }
            }
        }
    }

    // Toggle CTA placeholder container form fields dynamically
    window.checkCtaPlaceholder = function(content) {
        const section = document.getElementById('custom-cta-section');
        if (section) {
            if (content && content.indexOf('post-cta') !== -1) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        }
    };

    // 2. Global simulated TinyMCE API adapter
    window.tinymce = {
        get: function(id) {
            const cleanId = id.startsWith('#') ? id.substring(1) : id;
            const editor = window.tiptapInstances[cleanId];
            if (!editor) return null;

            const container = document.getElementById(`tiptap-container-${cleanId}`);

            return {
                setContent: function(content) {
                    editor.commands.setContent(content);
                    const textarea = document.getElementById(cleanId);
                    if (textarea) textarea.value = content;
                    
                    const codeTextArea = container ? container.querySelector('.tiptap-code-view') : null;
                    if (codeTextArea) codeTextArea.value = content;
                    
                    if (window.checkCtaPlaceholder) {
                        window.checkCtaPlaceholder(content);
                    }
                },
                getContent: function() {
                    const codeTextArea = container ? container.querySelector('.tiptap-code-view') : null;
                    const isCodeView = codeTextArea && !codeTextArea.classList.contains('hidden');
                    if (isCodeView) {
                        return codeTextArea.value;
                    }
                    return editor.getHTML();
                },
                remove: function() {
                    editor.destroy();
                    delete window.tiptapInstances[cleanId];
                    if (container) container.remove();
                    const textarea = document.getElementById(cleanId);
                    if (textarea) textarea.style.display = 'block';
                }
            };
        },
        triggerSave: function() {
            for (const id in window.tiptapInstances) {
                const editor = window.tiptapInstances[id];
                const textarea = document.getElementById(id);
                const container = document.getElementById(`tiptap-container-${id}`);
                if (textarea && editor) {
                    const codeTextArea = container ? container.querySelector('.tiptap-code-view') : null;
                    const isCodeView = codeTextArea && !codeTextArea.classList.contains('hidden');
                    if (isCodeView) {
                        textarea.value = codeTextArea.value;
                    } else {
                        textarea.value = editor.getHTML();
                    }
                }
            }
        },
        init: function() {
            // No-op - we handle initialization through window.initEditor
        }
    };
</script>
