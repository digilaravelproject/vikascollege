@extends('layouts.admin.app')

@section('content')
    {{-- Main Alpine Data Scope for Page Builder Management --}}
    <div x-data="pageBuilder()" x-init="initAll()" class="relative min-h-screen p-2 bg-gray-50 sm:p-4">

        {{-- 1. HEADER --}}
        <div class="flex flex-col flex-wrap justify-between gap-3 mb-4 sm:flex-row sm:items-center">
            <h1 class="text-xl font-bold text-gray-800">🧱 Page Builder — {{ $page->title }}</h1>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button @click="exportJSON" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Export
                    JSON</button>
                <button @click="importJSONPrompt" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Import
                    JSON</button>

                <button @click="undo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Undo</button>
                <button @click="redo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Redo</button>

                <button @click="savePage"
                    class="flex items-center justify-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                    <span>💾</span><span>Save Page</span>
                </button>
            </div>
        </div>

        {{-- 2. MAIN GRID --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- 3. SIDEBAR (Available Blocks) --}}
            <div class="self-start p-4 bg-white rounded-lg shadow lg:col-span-3 h-fit lg:sticky lg:top-4">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">Available Blocks</h2>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-1">
                    <template x-for="tpl in availableBlocks" :key="tpl.type">
                        <div draggable="true" @dragstart="dragBlock($event, tpl)"
                            class="p-3 text-gray-700 transition border border-gray-200 rounded cursor-grab hover:bg-blue-50">
                            <span x-text="tpl.label"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 4. CONTENT AREA --}}
            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building</p>
                </template>

                <div id="rootBlocks">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group"
                            :data-id="block.id">

                            {{-- Block Header and Controls (Reusable across all blocks) --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-700" x-text="block.type"></span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                                    <button @click="moveBlockUp(index)"
                                        class="px-2 py-1 text-sm bg-white border rounded">↑</button>
                                    <button @click="moveBlockDown(index)"
                                        class="px-2 py-1 text-sm bg-white border rounded">↓</button>
                                    <button @click="duplicateBlock(index)"
                                        class="px-2 py-1 text-sm bg-white border rounded">⧉</button>
                                    <button @click="confirmRemove(block.id, index)"
                                        class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                                </div>
                            </div>

                            {{-- Dynamic Block Component Rendering --}}
                            <div
                                :x-data="getComponentData(block, index, blocks, quills, pushHistory, _genId, initBlockQuills, openLinkDialog, confirmRemoveSub, addRow, removeRow, addCol, removeCol, dropBlockToSection, initSortables, moveSubUp, moveSubDown, duplicateSub)">

                                {{-- SECTION BLOCK COMPONENT --}}
                                <template x-if="block.type === 'section'">
                                    <div class="overflow-hidden bg-white border rounded-lg shadow">
                                        <button @click="block.expanded = !block.expanded"
                                            class="flex items-center justify-between w-full px-4 py-2 transition bg-blue-100 hover:bg-blue-200">
                                            <input type="text" x-model.debounce.400ms="block.title" @input="pushHistory"
                                                class="flex-1 font-semibold text-gray-700 bg-transparent border-none outline-none" />
                                            <span x-text="block.expanded ? '▾' : '▸'"></span>
                                        </button>

                                        <div x-show="block.expanded" x-collapse class="p-2 bg-gray-50 sm:p-4">
                                            <div :id="'section-drop-' + block.id"
                                                class="border-2 border-dashed border-gray-300 rounded p-4 min-h-[100px]"
                                                @dragover.prevent @drop="dropBlockToSection($event, block)">
                                                <template x-if="!block.blocks || block.blocks.length === 0">
                                                    <p class="text-sm text-center text-gray-400">Drag content blocks here...
                                                    </p>
                                                </template>

                                                <div :id="'section-list-' + block.id">
                                                    <template x-for="(sub, sIndex) in block.blocks" :key="sub.id">
                                                        <div class="relative p-3 mb-3 bg-white border rounded shadow-sm group"
                                                            :data-id="sub.id">

                                                            {{-- SUB-BLOCK CONTROLS (Always visible for sub-blocks) --}}
                                                            <div
                                                                class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                                                <div class="text-sm text-gray-700">
                                                                    <span x-text="sub.type"></span>
                                                                    <span class="text-xs text-gray-400"
                                                                        x-text="' — ' + sub.id.slice(0, 8)"></span>
                                                                </div>
                                                                <div class="flex items-center gap-2">
                                                                    <span
                                                                        class="px-1 text-base text-gray-400 cursor-grab">☰</span>
                                                                    <button @click="moveSubUp(block, sIndex)"
                                                                        class="px-2 py-1 text-xs bg-white border rounded">↑</button>
                                                                    <button @click="moveSubDown(block, sIndex)"
                                                                        class="px-2 py-1 text-xs bg-white border rounded">↓</button>
                                                                    <button @click="duplicateSub(block, sIndex)"
                                                                        class="px-2 py-1 text-xs bg-white border rounded">⧉</button>
                                                                    <button @click="confirmRemoveSub(block, sIndex)"
                                                                        class="px-2 py-1 text-xs text-red-600 bg-white border rounded">✖</button>
                                                                </div>
                                                            </div>

                                                            {{-- Dynamic Sub-Block Component Rendering (Calls inner
                                                            component logic) --}}
                                                            <div
                                                                :x-data="getComponentData(sub, sIndex, block.blocks, quills, pushHistory, _genId, initBlockQuills, openLinkDialog, confirmRemoveSub, addRow, removeRow, addCol, removeCol)">
                                                                <template x-if="['text', 'heading'].includes(sub.type)">
                                                                    @include('admin.pagebuilder.templates._text-editor')
                                                                </template>
                                                                <template
                                                                    x-if="sub.type === 'image' || sub.type === 'video' || sub.type === 'pdf'">
                                                                    @include('admin.pagebuilder.templates._media-sub')
                                                                </template>
                                                                <template x-if="sub.type === 'button'">
                                                                    @include('admin.pagebuilder.templates._button')
                                                                </template>
                                                                <template x-if="sub.type === 'embed'">
                                                                    @include('admin.pagebuilder.templates._embed')
                                                                </template>
                                                                <template x-if="sub.type === 'divider'">
                                                                    @include('admin.pagebuilder.templates._divider')
                                                                </template>
                                                                <template x-if="sub.type === 'code'">
                                                                    @include('admin.pagebuilder.templates._code')
                                                                </template>
                                                                {{-- NEW TABLE SUB-BLOCK COMPONENT --}}
                                                                <template x-if="sub.type === 'table'">
                                                                    @include('admin.pagebuilder.templates._table')
                                                                </template>
                                                            </div>

                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- TEXT/HEADING BLOCK COMPONENT --}}
                                <template x-if="['text', 'heading'].includes(block.type)">
                                    @include('admin.pagebuilder.templates._text-editor')
                                </template>

                                {{-- MEDIA (Image/Video/PDF) BLOCK COMPONENT --}}
                                <template x-if="block.type === 'image' || block.type === 'video' || block.type === 'pdf'">
                                    @include('admin.pagebuilder.templates._media-root')
                                </template>

                                {{-- BUTTON BLOCK COMPONENT --}}
                                <template x-if="block.type === 'button'">
                                    @include('admin.pagebuilder.templates._button')
                                </template>

                                {{-- EMBED BLOCK COMPONENT --}}
                                <template x-if="block.type === 'embed'">
                                    @include('admin.pagebuilder.templates._embed')
                                </template>

                                {{-- DIVIDER BLOCK COMPONENT --}}
                                <template x-if="block.type === 'divider'">
                                    @include('admin.pagebuilder.templates._divider')
                                </template>

                                {{-- CODE BLOCK COMPONENT --}}
                                <template x-if="block.type === 'code'">
                                    @include('admin.pagebuilder.templates._code')
                                </template>

                                {{-- NEW TABLE ROOT BLOCK COMPONENT --}}
                                <template x-if="block.type === 'table'">
                                    @include('admin.pagebuilder.templates._table')
                                </template>
                            </div>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Save Form --}}
        <form id="saveForm" method="POST" action="{{ route('admin.pagebuilder.builder.save', $page) }}">
            @csrf
            <input type="hidden" name="content" id="pageContent">
        </form>

    </div>

    {{-- Initial Data and Libraries --}}
    <script type="application/json" id="pb-initial-content">{!! $page->content ?: '{"blocks":[]}' !!}</script>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        // =================================================================
        // 🚀 COMPONENT HELPER AND DEFINITIONS
        // =================================================================

        // Helper function to dynamically pass parent properties to component scope
        function getComponentData(block, index, blocks, quills, pushHistory, _genId, initBlockQuills, openLinkDialog, confirmRemoveSub, addRow, removeRow, addCol, removeCol, dropBlockToSection, initSortables, moveSubUp, moveSubDown, duplicateSub) {

            // Base context for all components
            const baseContext = {
                block,
                index,
                pushHistory,
            };

            // Enhanced context for utility-heavy blocks
            const extendedContext = {
                ...baseContext,
                quills,
                _genId,
                initBlockQuills,
                openLinkDialog,
                confirmRemoveSub,
                addRow,
                removeRow,
                addCol,
                removeCol,
                dropBlockToSection,
                initSortables,
                moveSubUp,
                moveSubDown,
                duplicateSub,
            };

            switch (block.type) {
                case 'table':
                    // Table uses block data and table manipulation methods
                    return `tableBlock(${JSON.stringify(block)}, ${index}, pushHistory, addRow, removeRow, addCol, removeCol)`;

                case 'section':
                    // Section needs access to almost all utilities for sub-blocks and drag/drop
                    return `sectionBlock(${JSON.stringify(block)}, ${index}, ${JSON.stringify(Object.keys(extendedContext))}, ${Object.values(extendedContext)})`;

                case 'text':
                case 'heading':
                    // Text/Heading needs Quill initialization/instance and link dialog
                    return `textEditorBlock(${JSON.stringify(block)}, ${index}, quills, pushHistory, initQuill, openLinkDialog)`;

                case 'image':
                case 'video':
                case 'pdf':
                    // Media needs upload/removal methods
                    return `mediaBlock(${JSON.stringify(block)}, ${index}, pushHistory, handleFileUpload, removeMedia)`;

                case 'button':
                case 'embed':
                case 'code':
                case 'divider':
                    // Simple forms or display only blocks
                    return `simpleBlock(${JSON.stringify(block)}, ${index}, pushHistory)`;

                default:
                    return '() => ({})';
            }
        }

        // --- 1. SIMPLE BLOCK Component (Default for most non-editor blocks) ---
        window.simpleBlock = (initialBlock, index, pushHistory) => {
            return {
                block: initialBlock,
                index: index,
                pushHistory,
                init() {
                    // Reactive properties are handled by pushHistory on @input, no extra init needed
                },
            }
        }

        // --- 2. TABLE BLOCK Component ---
        window.tableBlock = (initialBlock, index, pushHistory, addRow, removeRow, addCol, removeCol) => {
            return {
                block: initialBlock,
                index: index,
                pushHistory,
                // Map parent methods to component scope
                addRow: () => addRow(initialBlock),
                removeRow: () => removeRow(initialBlock),
                addCol: () => addCol(initialBlock),
                removeCol: () => removeCol(initialBlock),

                init() {
                    // Table data changes are automatically detected by pushHistory on input debounce
                },
            }
        }

        // --- 3. TEXT EDITOR BLOCK Component (For Text/Heading) ---
        window.textEditorBlock = (initialBlock, index, quills, pushHistory, initQuill, openLinkDialog) => {
            return {
                block: initialBlock,
                index: index,
                quillInstance: null,
                pushHistory,
                openLinkDialog: (id) => openLinkDialog(id),

                init() {
                    // Initialize Quill after DOM is ready
                    this.$nextTick(() => {
                        initQuill(this.block.id, this.block.contentHtml || this.block.content || this.block.defaultContent);
                        this.quillInstance = quills[this.block.id];
                    });
                }
            }
        }

        // --- 4. MEDIA BLOCK Component (For Image/Video/PDF) ---
        window.mediaBlock = (initialBlock, index, pushHistory, handleFileUpload, removeMedia) => {
            return {
                block: initialBlock,
                index: index,
                pushHistory,
                handleFileUpload: (e) => handleFileUpload(e, initialBlock.id, initialBlock.type),
                removeMedia: () => removeMedia(initialBlock.id),
            }
        }

        // --- 5. SECTION BLOCK Component (Handles nested blocks) ---
        window.sectionBlock = (initialBlock, index, extendedKeys, extendedValues) => {
            // Reconstruct the extended context from passed arguments
            const parentContext = extendedKeys.reduce((acc, key, i) => {
                acc[key] = extendedValues[i];
                return acc;
            }, {});

            return {
                block: initialBlock,
                index: index,
                ...parentContext, // Import all parent utilities

                init() {
                    // Ensure nested quills and sortables are correctly initialized/re-initialized
                    this.$watch('block.expanded', () => {
                        this.$nextTick(() => {
                            this.initBlockQuills(this.block);
                            this.initSortables();
                        });
                    });
                    this.$nextTick(() => {
                        this.initBlockQuills(this.block);
                        this.initSortables();
                    });
                },
            }
        }

        // =================================================================
        //               MAIN ALPINE JS LOGIC (pageBuilder)
        // =================================================================

        function pageBuilder() {
            return {
                availableBlocks: [
                    { type: 'section', label: '📁 Section', title: 'New Section', blocks: [], expanded: true },
                    { type: 'heading', label: '🧱 Heading', defaultContent: '<h2>Heading</h2>' },
                    { type: 'text', label: '📝 Text', defaultContent: '<p>Type something...</p>' },
                    { type: 'image', label: '🖼️ Image', src: '' },
                    { type: 'video', label: '🎥 Video', src: '' },
                    { type: 'pdf', label: '📄 PDF', src: '' },
                    { type: 'embed', label: '▶️ YouTube/Embed', src: '' },
                    { type: 'button', label: '🔘 Button', text: 'Click Here', href: '#', align: 'left', target: '_self' },
                    { type: 'divider', label: '⎯⎯ Divider' },
                    { type: 'code', label: '💻 Code Block', defaultContent: '' },
                    { type: 'table', label: '📊 Table', data: [['H1', 'H2', 'H3'], ['C1', 'C2', 'C3']] },
                ],
                blocks: [],
                quills: {},
                historyStack: [],
                redoStack: [],

                initAll() {
                    try {
                        const scriptEl = document.getElementById('pb-initial-content');
                        let initial = null;
                        if (scriptEl) {
                            try {
                                initial = JSON.parse(scriptEl.textContent || '');
                            } catch (_) { initial = null; }
                        }
                        if (initial && initial.blocks && Array.isArray(initial.blocks)) {
                            this.blocks = initial.blocks.map(b => ({ ...b, id: b.id || this._genId() }));
                        } else if (Array.isArray(initial)) {
                            this.blocks = initial.map(b => ({ ...b, id: b.id || this._genId() }));
                        } else {
                            this.blocks = [];
                        }

                        this.$nextTick(() => {
                            this.initAllQuills();
                            this.initSortables();
                            this.pushHistory();
                        });
                    } catch (e) {
                        console.error('initAll failed:', e);
                        Swal.fire('Error', 'Failed to initialize page builder.', 'error');
                    }
                },

                initAllQuills() {
                    try {
                        Object.keys(this.quills).forEach(id => {
                            const editorEl = document.getElementById('editor-' + id);
                            if (editorEl) editorEl.innerHTML = '';
                        });
                        this.quills = {};
                        this.blocks.forEach(b => this.initBlockQuills(b));
                    } catch (e) { console.error('initAllQuills failed:', e); }
                },

                initBlockQuills(block) {
                    try {
                        if (!block) return;
                        const types = ['text', 'heading'];

                        if (types.includes(block.type)) {
                            const initialHtml = (block.contentHtml || block.content || block.defaultContent || '<p></p>');
                            const initialDelta = block.contentDelta || null;
                            this.initQuill(block.id, initialHtml, initialDelta);
                        }

                        if (block.type === 'section' && Array.isArray(block.blocks)) {
                            block.blocks.forEach(sub => {
                                if (types.includes(sub.type)) {
                                    const subHtml = (sub.contentHtml || sub.content || sub.defaultContent || '<p></p>');
                                    const subDelta = sub.contentDelta || null;
                                    this.initQuill(sub.id, subHtml, subDelta);
                                }
                            });
                        }
                    } catch (e) { console.error('initBlockQuills failed:', e); }
                },

                // --- UTILITY METHODS ---

                _genId() {
                    return 'b_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8);
                },

                // --- Drag & Drop/Reorder/Delete logic ---

                dragBlock(e, tpl) {
                    try {
                        e.dataTransfer.setData('blockTpl', JSON.stringify(tpl));
                    } catch (e) { console.error('dragBlock failed:', e); }
                },

                _initNewBlock(tpl) {
                    const newBlock = JSON.parse(JSON.stringify(tpl));
                    newBlock.id = this._genId();

                    if (['text', 'heading'].includes(newBlock.type)) {
                        newBlock.content = newBlock.defaultContent || '<p></p>';
                    }
                    if (newBlock.type === 'code') {
                        newBlock.content = newBlock.defaultContent || '';
                    }
                    if (newBlock.type === 'table' && !Array.isArray(newBlock.data)) {
                        newBlock.data = [['H1', 'H2', 'H3'], ['C1', 'C2', 'C3']];
                    }
                    if (newBlock.type === 'section' && !Array.isArray(newBlock.blocks)) {
                        newBlock.blocks = [];
                        newBlock.expanded = true;
                        newBlock.title = newBlock.title || 'New Section';
                    }
                    return newBlock;
                },

                dropBlock(e) {
                    try {
                        const data = e.dataTransfer.getData('blockTpl');
                        if (!data) return;
                        const tpl = JSON.parse(data);
                        const newBlock = this._initNewBlock(tpl);

                        this.blocks.push(newBlock);
                        this.$nextTick(() => {
                            this.initBlockQuills(newBlock);
                            this.initSortables();
                            this.pushHistory();
                        });
                    } catch (e) {
                        console.error('dropBlock failed:', e);
                        Swal.fire('Error', 'Failed to drop block.', 'error');
                    }
                },

                dropBlockToSection(e, section) {
                    try {
                        e.stopPropagation();
                        const data = e.dataTransfer.getData('blockTpl');
                        if (!data) return;
                        const tpl = JSON.parse(data);
                        if (tpl.type === 'section') return;
                        const newBlock = this._initNewBlock(tpl);

                        if (!Array.isArray(section.blocks)) section.blocks = [];
                        section.blocks.push(newBlock);
                        this.$nextTick(() => {
                            this.initBlockQuills(newBlock);
                            this.initSortables();
                            this.pushHistory();
                        });
                    } catch (e) {
                        console.error('dropBlockToSection failed:', e);
                        Swal.fire('Error', 'Failed to drop block into section.', 'error');
                    }
                },

                initSortables() {
                    try {
                        const rootEl = document.getElementById('rootBlocks');
                        if (rootEl && !rootEl._sortable) {
                            rootEl._sortable = Sortable.create(rootEl, {
                                handle: '.group',
                                animation: 150,
                                draggable: '[data-id]',
                                dataIdAttr: 'data-id',
                                onEnd: (evt) => {
                                    const ids = Array.from(rootEl.querySelectorAll(':scope > div[data-id]')).map(el => el.getAttribute('data-id'));
                                    this.reorderByIds(ids, 'root');
                                }
                            });
                        }
                        this.blocks.forEach(block => {
                            if (block.type === 'section') {
                                const secList = document.getElementById('section-list-' + block.id);
                                if (secList && !secList._sortable) {
                                    secList._sortable = Sortable.create(secList, {
                                        animation: 150,
                                        draggable: '[data-id]',
                                        onEnd: (evt) => {
                                            const ids = Array.from(secList.querySelectorAll(':scope > div[data-id]')).map(el => el.getAttribute('data-id'));
                                            this.reorderSectionByIds(block, ids);
                                        }
                                    });
                                }
                            }
                        });
                    } catch (e) { console.error('initSortables failed:', e); }
                },

                reorderByIds(ids, scope = 'root') {
                    try {
                        if (!ids || !ids.length) return;
                        const map = {};
                        this.blocks.forEach(b => map[b.id] = b);
                        this.blocks = ids.map(id => map[id]).filter(Boolean);
                        this.pushHistory();
                        this.$nextTick(() => this.initAllQuills());
                    } catch (e) { console.error('reorderByIds failed:', e); }
                },

                reorderSectionByIds(section, ids) {
                    try {
                        if (!ids || !ids.length) return;
                        const map = {};
                        (section.blocks || []).forEach(b => map[b.id] = b);
                        section.blocks = ids.map(id => map[id]).filter(Boolean);
                        this.pushHistory();
                        this.$nextTick(() => this.initAllQuills());
                    } catch (e) { console.error('reorderSectionByIds failed:', e); }
                },

                // --- Block Control Methods ---
                moveBlockUp(index) {
                    if (index <= 0) return;
                    const arr = this.blocks;
                    [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
                    this.blocks = [...arr];
                    this.pushHistory();
                },
                moveBlockDown(index) {
                    if (index >= this.blocks.length - 1) return;
                    const arr = this.blocks;
                    [arr[index + 1], arr[index]] = [arr[index], arr[index + 1]];
                    this.blocks = [...arr];
                    this.pushHistory();
                },
                duplicateBlock(index) {
                    const b = JSON.parse(JSON.stringify(this.blocks[index]));
                    b.id = this._genId();
                    if (b.type === 'section' && Array.isArray(b.blocks)) {
                        b.blocks = b.blocks.map(sb => ({ ...sb, id: this._genId() }));
                    }
                    this.blocks.splice(index + 1, 0, b);
                    this.$nextTick(() => {
                        this.initBlockQuills(b);
                        this.initSortables();
                        this.pushHistory();
                    });
                },
                confirmRemove(blockId, index) {
                    Swal.fire({
                        title: 'Delete?',
                        text: 'Remove this block?',
                        icon: 'warning',
                        showCancelButton: true
                    }).then(res => {
                        if (res.isConfirmed) {
                            this.blocks.splice(index, 1);
                            this.pushHistory();
                        }
                    });
                },
                moveSubUp(section, sIndex) {
                    if (sIndex <= 0) return;
                    const arr = section.blocks;
                    [arr[sIndex - 1], arr[sIndex]] = [arr[sIndex], arr[sIndex - 1]];
                    section.blocks = [...arr];
                    this.pushHistory();
                },
                moveSubDown(section, sIndex) {
                    if (sIndex >= section.blocks.length - 1) return;
                    const arr = section.blocks;
                    [arr[sIndex + 1], arr[sIndex]] = [arr[sIndex], arr[sIndex + 1]];
                    section.blocks = [...arr];
                    this.pushHistory();
                },
                duplicateSub(section, sIndex) {
                    const sb = JSON.parse(JSON.stringify(section.blocks[sIndex]));
                    sb.id = this._genId();
                    section.blocks.splice(sIndex + 1, 0, sb);
                    this.$nextTick(() => {
                        this.initBlockQuills(sb);
                        this.initSortables();
                        this.pushHistory();
                    });
                },
                confirmRemoveSub(section, index) {
                    Swal.fire({
                        title: 'Delete?',
                        text: 'Remove sub-block?',
                        icon: 'warning',
                        showCancelButton: true
                    }).then(res => {
                        if (res.isConfirmed) {
                            section.blocks.splice(index, 1);
                            this.pushHistory();
                        }
                    });
                },

                // --- TABLE MANIPULATION FUNCTIONS ---
                addRow(block) {
                    const numCols = block.data[0] ? block.data[0].length : 3;
                    const newRow = Array(numCols).fill('New Cell');
                    block.data.push(newRow);
                    this.pushHistory();
                },
                removeRow(block) {
                    if (block.data.length > 1) {
                        block.data.pop();
                        this.pushHistory();
                    } else {
                        Swal.fire('Cannot remove last row', 'A table must have at least one row.', 'info');
                    }
                },
                addCol(block) {
                    block.data.forEach(row => row.push('New Cell'));
                    this.pushHistory();
                },
                removeCol(block) {
                    if (block.data[0].length > 1) {
                        block.data.forEach(row => row.pop());
                        this.pushHistory();
                    } else {
                        Swal.fire('Cannot remove last column', 'A table must have at least one column.', 'info');
                    }
                },

                // --- QUILL/MEDIA UTILITIES (Required by components) ---

                initQuill(blockId, initialHtml = '', initialDelta = null) {
                    if (this.quills[blockId]) return;
                    const toolbarSelector = '#toolbar-' + blockId;
                    const editorSelector = '#editor-' + blockId;

                    const attemptInit = () => {
                        const ed = document.querySelector(editorSelector);
                        const tbEl = document.querySelector(toolbarSelector);
                        if (!ed || !tbEl) return setTimeout(attemptInit, 50);

                        try {
                            if (this.quills[blockId]) delete this.quills[blockId];

                            const quill = new Quill(editorSelector, {
                                theme: 'snow',
                                modules: { toolbar: toolbarSelector },
                                placeholder: 'Type here...'
                            });

                            if (initialDelta && typeof initialDelta === 'object') {
                                try { quill.setContents(initialDelta); }
                                catch (_) { quill.root.innerHTML = initialHtml || '<p></p>'; }
                            } else if (initialHtml) {
                                quill.root.innerHTML = initialHtml;
                            }
                            this.quills[blockId] = quill;

                            let debounceTimer;
                            quill.on('text-change', (delta, oldDelta, source) => {
                                if (source !== 'user') return;
                                clearTimeout(debounceTimer);
                                debounceTimer = setTimeout(() => {
                                    this.pushHistory();
                                }, 400);
                            });
                        } catch (e) {
                            console.error(`Quill initialization for ${blockId} failed:`, e);
                        }
                    };
                    attemptInit();
                },

                updateQuillContent(blockId, html, delta = null) {
                    const findAndUpdate = (arr) => {
                        for (let b of arr) {
                            if (b.id === blockId) {
                                b.content = html;
                                b.contentHtml = html;
                                if (delta) b.contentDelta = delta;
                                return true;
                            }
                            if (b.type === 'section' && Array.isArray(b.blocks)) {
                                if (findAndUpdate(b.blocks)) return true;
                            }
                        }
                        return false;
                    };
                    findAndUpdate(this.blocks);
                },

                openLinkDialog(blockId) {
                    const quill = this.quills[blockId];
                    if (!quill) return Swal.fire('Error', 'Editor not ready', 'error');
                    const range = quill.getSelection();
                    if (!range || range.length === 0) {
                        return Swal.fire('Select text', 'Please select the text you want to link.', 'info');
                    }

                    Swal.fire({
                        title: 'Insert Link',
                        html: `<input id="swal-link-url" class="swal2-input" placeholder="https://example.com" style="width: 100%;">
                                   <select id="swal-link-target" class="swal2-select" style="width: 100%; margin-top: 10px;">
                                       <option value="_blank" selected>New Tab (_blank)</option>
                                       <option value="_self">Same Tab (_self)</option>
                                   </select>`,
                        preConfirm: () => ({
                            url: document.getElementById('swal-link-url').value,
                            target: document.getElementById('swal-link-target').value || '_blank'
                        }),
                        showCancelButton: true
                    }).then(res => {
                        if (res.isConfirmed && res.value.url) {
                            quill.format('link', res.value.url);
                            quill.formatText(range.index, range.length, 'link', {
                                href: res.value.url,
                                target: res.value.target
                            });

                            setTimeout(() => {
                                const anchors = quill.root.querySelectorAll('a[href="' + res.value.url + '"]');
                                anchors.forEach(a => {
                                    if (!a.hasAttribute('target')) { a.setAttribute('target', res.value.target); }
                                });
                                this.updateQuillContent(blockId, quill.root.innerHTML, quill.getContents());
                                this.pushHistory();
                            }, 100);
                        }
                    });
                },

                async handleFileUpload(e, blockId, type, section = null) {
                    // ... (keep the existing handleFileUpload logic, adapted to use blockId for update)
                    try {
                        const file = e.target.files[0];
                        if (!file) return;

                        const { value: formValues } = await Swal.fire({
                            title: "Upload Options",
                            html: `<input id="custom_name" class="swal2-input" placeholder="File name (optional)" style="width: 100%;" />
                                       <select id="base_path" class="swal2-select" style="width: 100%; margin-top: 10px;">
                                           <option value="storage" selected>storage</option>
                                           <option value="wp-content">wp-content</option>
                                       </select>`,
                            focusConfirm: false,
                            preConfirm: () => ({
                                custom_name: document.getElementById("custom_name").value,
                                base_path: document.getElementById("base_path").value,
                            }),
                            confirmButtonText: "Upload",
                            showCancelButton: true,
                        });

                        if (!formValues) return;

                        const formData = new FormData();
                        formData.append("file", file);
                        formData.append("base_path", formValues.base_path || "storage");
                        formData.append("custom_name", formValues.custom_name || "");

                        Swal.fire({
                            title: 'Uploading...',
                            text: 'Please wait.',
                            didOpen: () => { Swal.showLoading() },
                            allowOutsideClick: false
                        });

                        const res = await fetch(
                            '{{ route('admin.pagebuilder.builder.upload', $page) }}', {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                            body: formData,
                        });

                        const data = await res.json();

                        if (data.success) {
                            const url = data.url;
                            const update = (arr) => {
                                for (let b of arr) {
                                    if (b.id === blockId) { b.src = url; return true; }
                                    if (b.type === "section" && Array.isArray(b.blocks)) {
                                        if (update(b.blocks)) return true;
                                    }
                                }
                                return false;
                            };

                            update(this.blocks);
                            this.blocks = [...this.blocks];

                            Swal.fire({ icon: "success", title: "✅ File Uploaded", text: `${data.filename}`, timer: 1400, showConfirmButton: false });
                            this.pushHistory();
                        } else {
                            Swal.fire("Error", data.message || "Upload failed.", "error");
                        }
                    } catch (err) {
                        console.error('handleFileUpload failed:', err);
                        Swal.fire("Error", "An error occurred during upload.", "error");
                    } finally {
                        e.target.value = null;
                    }
                },
                removeMedia(blockId) {
                    const update = (arr) => {
                        for (let b of arr) {
                            if (b.id === blockId) { delete b.src; return true; }
                            if (b.type === 'section' && Array.isArray(b.blocks)) { if (update(b.blocks)) return true; }
                        }
                        return false;
                    };
                    update(this.blocks);
                    this.blocks = [...this.blocks];
                    this.pushHistory();
                },

                // --- History Methods ---

                pushHistory() {
                    Object.keys(this.quills).forEach(id => {
                        const q = this.quills[id];
                        if (q) this.updateQuillContent(id, q.root.innerHTML, q.getContents());
                    });

                    const snapshot = JSON.stringify(this.blocks);
                    if (this.historyStack.length > 0 && this.historyStack[this.historyStack.length - 1] === snapshot) return;

                    this.historyStack.push(snapshot);
                    if (this.historyStack.length > 50) this.historyStack.shift();
                    this.redoStack = [];
                },
                undo() {
                    if (this.historyStack.length <= 1) return Swal.fire('Nothing to undo', '', 'info');
                    const cur = this.historyStack.pop();
                    this.redoStack.push(cur);

                    const prev = this.historyStack[this.historyStack.length - 1];
                    if (prev) {
                        try { this.blocks = JSON.parse(prev); } catch (e) { }

                        this.quills = {};
                        this.$nextTick(() => { this.initAllQuills(); this.initSortables(); });
                    }
                },
                redo() {
                    if (!this.redoStack.length) return Swal.fire('Nothing to redo', '', 'info');
                    const next = this.redoStack.pop();
                    try { this.blocks = JSON.parse(next); } catch (e) { }
                    this.historyStack.push(next);

                    this.quills = {};
                    this.$nextTick(() => { this.initAllQuills(); this.initSortables(); });
                },

                // --- Save/Export ---
                savePage() {
                    Object.keys(this.quills).forEach(id => {
                        const q = this.quills[id];
                        if (q) this.updateQuillContent(id, q.root.innerHTML, q.getContents());
                    });

                    const payload = { blocks: this.blocks };
                    document.getElementById('pageContent').value = JSON.stringify(payload);

                    Swal.fire({ title: '💾 Saving...', text: 'Please wait.', didOpen: () => { Swal.showLoading() }, allowOutsideClick: false });

                    const form = document.getElementById('saveForm');
                    fetch(form.action, {
                        method: form.method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(new FormData(form))
                    })
                        .then(res => {
                            if (!res.ok) { return res.json().then(err => { throw new Error(err.message || 'Save failed') }); }
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({ icon: "success", title: "✅ Page Saved!", timer: 1500, showConfirmButton: false });
                                this.historyStack = [JSON.stringify(this.blocks)];
                                this.redoStack = [];
                            } else {
                                throw new Error(data.message || 'Save operation failed.');
                            }
                        })
                        .catch(e => {
                            console.error('savePage failed:', e);
                            Swal.fire('Error', e.message || 'Failed to save the page.', 'error');
                        });
                },
                exportJSON() {
                    Object.keys(this.quills).forEach(id => {
                        const q = this.quills[id];
                        if (q) this.updateQuillContent(id, q.root.innerHTML, q.getContents());
                    });

                    const payload = { blocks: this.blocks };
                    const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '{{ \Illuminate\Support\Str::slug($page->title ?: 'page') }}-layout.json';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                },
                importJSONPrompt() {
                    Swal.fire({
                        title: 'Import JSON',
                        html: `<input type="file" id="jsonFile" accept="application/json" class="swal2-file" style="width: 100%;">`,
                        showCancelButton: true,
                        preConfirm: () => {
                            return new Promise((resolve) => {
                                const f = document.getElementById('jsonFile').files[0];
                                if (!f) return resolve(null);
                                const reader = new FileReader();
                                reader.onload = () => resolve(reader.result);
                                reader.readAsText(f);
                            });
                        }
                    }).then(res => {
                        if (res.isConfirmed && res.value) {
                            try {
                                const parsed = JSON.parse(res.value);
                                const newBlocks = (parsed.blocks || parsed || []).map(b => ({ ...b, id: b.id || this._genId() }));

                                this.blocks = newBlocks;
                                this.$nextTick(() => {
                                    this.quills = {};
                                    this.initAllQuills();
                                    this.initSortables();
                                    this.pushHistory();
                                });
                                Swal.fire('Imported', 'JSON imported successfully', 'success');
                            } catch (e) {
                                Swal.fire('Error', 'Invalid JSON', 'error');
                            }
                        }
                    });
                },
            };
        }
    </script>

    {{-- 5. BLOCK TEMPLATES (These are included inline for simplicity) --}}

    {{-- Reusable Templates Directory: admin/pagebuilder/templates/ --}}

    {{-- _text-editor.blade.php (Text/Heading Component UI) --}}
    <template id="_text-editor">
        <div class="space-y-2">
            <div :id="'toolbar-' + block.id"
                class="flex flex-wrap items-center gap-1 p-1 mb-2 bg-white rounded shadow-sm sm:gap-2 sm:p-2">
                <select class="ql-header">
                    <option value="1"></option>
                    <option value="2"></option>
                    <option value="3"></option>
                    <option selected></option>
                </select>
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
                <button class="ql-code"></button>
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-blockquote"></button>
                <select class="ql-color"></select>
                <select class="ql-align"></select>
                <button class="ql-link"></button>
                <button @click.prevent="openLinkDialog(block.id)" class="ql-custom">🔗</button>
            </div>
            <div :id="'editor-' + block.id" class="bg-white border rounded quill-editor" style="min-height:100px;"></div>
        </div>
    </template>

    {{-- _media-root.blade.php (Image/Video/PDF Root Component UI) --}}
    <template id="_media-root">
        <div class="text-center">
            <template x-if="block.src">
                <template x-if="block.type === 'image'">
                    <img :src="block.src" class="max-w-full mx-auto rounded-lg shadow-md" />
                </template>
                <template x-if="block.type === 'video'">
                    <video :src="block.src" controls class="max-w-full mx-auto rounded-lg shadow-md"></video>
                </template>
                <template x-if="block.type === 'pdf'">
                    <iframe :src="block.src" class="w-full rounded-lg shadow-md h-[500px]"></iframe>
                </template>
                <div class="flex justify-center gap-2 mt-2">
                    <button @click="removeMedia()" class="px-3 py-1 text-sm bg-red-100 rounded">Remove</button>
                </div>
            </template>
            <template x-if="!block.src">
                <label class="block mt-2 cursor-pointer">
                    <input type="file"
                        :accept="block.type === 'image' ? 'image/*' : (block.type === 'video' ? 'video/*' : 'application/pdf')"
                        @change="handleFileUpload($event)" class="hidden" />
                    <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                        <p class="text-sm text-gray-500"
                            x-text="block.type === 'image' ? '📁 Click to upload image' : (block.type === 'video' ? '🎬 Click to upload video' : '📄 Click to upload PDF')">
                        </p>
                    </div>
                </label>
            </template>
        </div>
    </template>

    {{-- _media-sub.blade.php (Image/Video/PDF Sub-Block Component UI) --}}
    <template id="_media-sub">
        <div class="text-center">
            <template x-if="sub.src">
                <template x-if="sub.type === 'image'">
                    <img :src="sub.src" class="max-w-full mx-auto rounded-lg shadow-md" />
                </template>
                <template x-if="sub.type === 'video'">
                    <video :src="sub.src" controls class="max-w-full mx-auto rounded-lg shadow-md"></video>
                </template>
                <template x-if="sub.type === 'pdf'">
                    <iframe :src="sub.src" class="w-full h-[400px] rounded-lg shadow-md"></iframe>
                </template>
                <div class="flex justify-center gap-2 mt-2">
                    {{-- Note: This is an exception, removeMediaFromSub needs parent scope access --}}
                    <button @click="$parent.confirmRemoveSub($parent.block, sIndex)"
                        class="px-2 py-1 text-sm bg-red-100 rounded">Remove</button>
                </div>
            </template>
            <template x-if="!sub.src">
                <label class="block mt-2 cursor-pointer">
                    <input type="file"
                        :accept="sub.type === 'image' ? 'image/*' : (sub.type === 'video' ? 'video/*' : 'application/pdf')"
                        @change="$parent.handleFileUpload($event, sub.id, sub.type, $parent.block)" class="hidden" />
                    <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                        <p class="text-sm text-gray-500"
                            x-text="sub.type === 'image' ? '📁 Click to upload image' : (sub.type === 'video' ? '🎬 Click to upload video' : '📄 Click to upload PDF')">
                        </p>
                    </div>
                </label>
            </template>
        </div>
    </template>

    {{-- _button.blade.php (Button Component UI) --}}
    <template id="_button">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-gray-600">Button Text</label>
                <input type="text" x-model.debounce.400ms="block.text" @input="pushHistory"
                    class="w-full p-2 border rounded" placeholder="Click Here">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Button Link (URL)</label>
                <input type="text" x-model.debounce.400ms="block.href" @input="pushHistory"
                    class="w-full p-2 border rounded" placeholder="https://...">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Alignment</label>
                <select x-model.debounce.400ms="block.align" @change="pushHistory"
                    class="w-full p-2 border rounded bg-white">
                    <option value="left">Left</option>
                    <option value="center">Center</option>
                    <option value="right">Right</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Target</label>
                <select x-model.debounce.400ms="block.target" @change="pushHistory"
                    class="w-full p-2 border rounded bg-white">
                    <option value="_self">Same Tab (_self)</option>
                    <option value="_blank">New Tab (_blank)</option>
                </select>
            </div>
        </div>
    </template>

    {{-- _embed.blade.php (Embed Component UI) --}}
    <template id="_embed">
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-600">Embed URL (YouTube, etc.)</label>
            <input type="text" x-model.debounce.400ms="block.src" @input="pushHistory" class="w-full p-2 border rounded"
                placeholder="https://www.youtube.com/watch?v=...">
        </div>
    </template>

    {{-- _divider.blade.php (Divider Component UI) --}}
    <template id="_divider">
        <hr class="my-4 border-gray-300 border-dashed">
    </template>

    {{-- _code.blade.php (Code Component UI) --}}
    <template id="_code">
        <div>
            <label class="text-sm font-medium text-gray-600">Code</label>
            <textarea x-model.debounce.400ms="block.content" @input="pushHistory"
                class="w-full p-2 font-mono border rounded" rows="6" placeholder="<script>..."></textarea>
        </div>
    </template>

    {{-- _table.blade.php (Table Component UI - IMPROVED) --}}
    <template id="_table">
        <div class="space-y-3">

            {{-- Table Renderer --}}
            <div class="overflow-x-auto border border-gray-300 rounded shadow-inner">
                <table class="min-w-full divide-y divide-gray-300">
                    <template x-if="block.data.length > 0">
                        <thead class="bg-gray-200 border-b border-gray-300">
                            <tr class="text-xs text-gray-700 font-semibold uppercase leading-normal">
                                <template x-for="(col, colIndex) in block.data[0]" :key="colIndex">
                                    <th class="py-2 px-1 border-r border-gray-300 last:border-r-0">
                                        <input type="text" x-model.debounce.400ms="block.data[0][colIndex]"
                                            @input="pushHistory"
                                            class="w-full text-center bg-transparent border border-gray-400 rounded focus:ring-1 focus:ring-blue-500 p-0.5 text-xs font-semibold"
                                            placeholder="Header" />
                                    </th>
                                </template>
                            </tr>
                        </thead>
                    </template>
                    <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                        <template x-for="(row, rowIndex) in block.data" :key="rowIndex">
                            <template x-if="rowIndex > 0">
                                <tr class="hover:bg-gray-50">
                                    <template x-for="(cell, colIndex) in row" :key="colIndex">
                                        <td class="py-1 px-1 border-r border-gray-200 last:border-r-0 align-top">
                                            <textarea x-model.debounce.400ms="block.data[rowIndex][colIndex]"
                                                @input="pushHistory" rows="1"
                                                class="w-full p-1 text-xs border border-gray-200 rounded focus:border-blue-400 resize-none min-h-6"></textarea>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Table Controls --}}
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <button @click.prevent="addRow()"
                    class="flex items-center space-x-1 px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"><span>+</span><span>Row</span></button>
                <button @click.prevent="removeRow()" :disabled="block.data.length <= 1"
                    class="flex items-center space-x-1 px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"><span>-</span><span>Row</span></button>
                <span class="text-gray-300">|</span>
                <button @click.prevent="addCol()"
                    class="flex items-center space-x-1 px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"><span>+</span><span>Col</span></button>
                <button @click.prevent="removeCol()" :disabled="block.data[0].length <= 1"
                    class="flex items-center space-x-1 px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"><span>-</span><span>Col</span></button>
            </div>
        </div>
    </template>

    {{-- End of Block Templates --}}

    <style>
        /* 1. Base Quill Editor Styles */
        .ql-editor {
            min-height: 120px;
            font-size: 1rem;
        }

        .quill-editor {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
        }

        /* 2. Responsive Quill Toolbar */
        .ql-toolbar.ql-snow {
            border-radius: 0.375rem 0.375rem 0 0;
            border-bottom: 0;
            padding: 4px;
        }

        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 8px;
        }

        .ql-snow .ql-picker-label {
            font-size: 12px;
        }

        .ql-snow .ql-picker {
            margin-top: 2px;
        }

        .ql-snow .ql-stroke {
            stroke-width: 1.5px;
        }

        /* 3. SweetAlert Mobile Fixes */
        .swal2-input,
        .swal2-select,
        .swal2-file {
            width: 100% !important;
            box-sizing: border-box;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .swal2-popup {
            width: 90vw !important;
            max-width: 480px;
        }

        /* 4. Table UI Specific Styles */
        .last\:border-r-0:last-child {
            border-right-width: 0 !important;
        }

        /* This is important for making the textarea adjust better inside the tiny table cells */
        .min-h-6 {
            min-height: 1.5rem;
            /* ~ 24px */
            line-height: 1.25;
            padding: 0.25rem;
        }
    </style>
@endsection
