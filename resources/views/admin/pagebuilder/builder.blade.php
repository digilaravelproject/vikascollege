@extends('layouts.admin.app')

@section('content')
    <div x-data="pageBuilder({{ $page->content ? json_encode($page->content) : 'null' }})" x-init="initAll()"
        class="relative min-h-screen p-4 bg-gray-50">

        <div class="flex flex-col mb-4 space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <h1 class="text-xl font-bold text-gray-800">🧱 Page Builder — {{ $page->title }}</h1>

            <div class="flex items-center gap-3">
                <button @click="exportJSON" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Export
                    JSON</button>
                <button @click="importJSONPrompt" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Import
                    JSON</button>

                <button @click="undo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Undo</button>
                <button @click="redo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Redo</button>

                <button @click="savePage"
                    class="flex items-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                    <span>💾</span><span>Save Page</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="sticky self-start p-4 bg-white rounded-lg shadow lg:col-span-3 h-fit top-4">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">Available Blocks</h2>
                <template x-for="tpl in availableBlocks" :key="tpl.type">
                    <div draggable="true" @dragstart="dragBlock($event, tpl)"
                        class="p-3 mb-2 text-gray-700 transition border border-gray-200 rounded cursor-grab hover:bg-blue-50">
                        <span x-text="tpl.label"></span>
                    </div>
                </template>
            </div>

            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building</p>
                </template>

                <div id="rootBlocks">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group"
                            :data-id="block.id">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-700" x-text="block.type"></span>
                                    {{-- <span class="text-xs text-gray-400" x-text="'id: ' + block.id"></span> --}}
                                </div>

                                <div class="flex items-center gap-2">
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

                            <template x-if="block.type === 'section'">
                                <div class="overflow-hidden bg-white border rounded-lg shadow">
                                    <button @click="block.expanded = !block.expanded"
                                        class="flex items-center justify-between w-full px-4 py-2 transition bg-blue-100 hover:bg-blue-200">
                                        <input type="text" x-model="block.title"
                                            class="flex-1 font-semibold text-gray-700 bg-transparent border-none outline-none" />
                                        <span x-text="block.expanded ? '▾' : '▸'"></span>
                                    </button>

                                    <div x-show="block.expanded" x-collapse class="p-4 bg-gray-50">
                                        <div :id="'section-drop-' + block.id"
                                            class="border-2 border-dashed border-gray-300 rounded p-4 min-h-[100px]"
                                            @dragover.prevent @drop="dropBlockToSection($event, block)">
                                            <template x-if="!block.blocks || block.blocks.length === 0">
                                                <p class="text-sm text-center text-gray-400">Drag content blocks here...</p>
                                            </template>

                                            <div :id="'section-list-' + block.id">
                                                <template x-for="(sub, sIndex) in block.blocks" :key="sub.id">
                                                    <div class="relative p-3 mb-3 bg-white border rounded shadow-sm group"
                                                        :data-id="sub.id">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="text-sm text-gray-700">
                                                                <span x-text="sub.type"></span>
                                                                <span class="text-xs text-gray-400"
                                                                    x-text="' — ' + sub.id"></span>
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

                                                        <template
                                                            x-if="sub.type === 'text' || sub.type === 'heading' || sub.type === 'table'">
                                                            <div>
                                                                <div :id="'toolbar-' + sub.id"
                                                                    class="flex flex-wrap gap-2 p-2 mb-2 bg-white rounded shadow-sm">
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
                                                                    <button @click.prevent="openLinkDialog(sub.id)"
                                                                        class="ql-custom">🔗</button>
                                                                    <button class="ql-table">📊</button>
                                                                </div>
                                                                <div :id="'editor-' + sub.id"
                                                                    class="bg-white border rounded quill-editor"
                                                                    style="min-height:100px;"></div>
                                                            </div>
                                                        </template>

                                                        <template x-if="sub.type === 'image'">
                                                            <div class="text-center">
                                                                <template x-if="sub.src">
                                                                    <img :src="sub.src"
                                                                        class="max-w-full mx-auto rounded-lg shadow-md" />
                                                                    <div class="flex justify-center gap-2 mt-2">
                                                                        <button @click="removeMediaFromSub(block, sub.id)"
                                                                            class="px-2 py-1 text-sm bg-red-100 rounded">Remove</button>
                                                                    </div>
                                                                </template>
                                                                <template x-if="!sub.src">
                                                                    <label class="block mt-2 cursor-pointer">
                                                                        <input type="file" accept="image/*"
                                                                            @change="handleFileUpload($event, sub.id, 'image', block)"
                                                                            class="hidden" />
                                                                        <div
                                                                            class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                                            <p class="text-sm text-gray-500">📁 Click to
                                                                                upload image</p>
                                                                        </div>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </template>

                                                        <template x-if="sub.type === 'video'">
                                                            <div class="text-center">
                                                                <template x-if="sub.src">
                                                                    <video :src="sub.src" controls
                                                                        class="max-w-full mx-auto rounded-lg shadow-md"></video>
                                                                </template>
                                                                <template x-if="!sub.src">
                                                                    <label class="block mt-2 cursor-pointer">
                                                                        <input type="file" accept="video/*"
                                                                            @change="handleFileUpload($event, sub.id, 'video', block)"
                                                                            class="hidden" />
                                                                        <div
                                                                            class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                                            <p class="text-sm text-gray-500">🎬 Click to
                                                                                upload video</p>
                                                                        </div>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </template>

                                                        <template x-if="sub.type === 'pdf'">
                                                            <div class="text-center">
                                                                <template x-if="sub.src">
                                                                    <iframe :src="sub.src"
                                                                        class="w-full h-[400px] rounded-lg shadow-md"></iframe>
                                                                </template>
                                                                <template x-if="!sub.src">
                                                                    <label class="block mt-2 cursor-pointer">
                                                                        <input type="file" accept="application/pdf"
                                                                            @change="handleFileUpload($event, sub.id, 'pdf', block)"
                                                                            class="hidden" />
                                                                        <div
                                                                            class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                                            <p class="text-sm text-gray-500">📄 Click to
                                                                                upload PDF</p>
                                                                        </div>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="block.type === 'text' || block.type === 'heading' || block.type === 'table'">
                                <div class="space-y-2">
                                    <div :id="'toolbar-' + block.id"
                                        class="flex flex-wrap items-center gap-2 p-2 bg-white rounded shadow-sm">
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
                                        <button class="ql-table">📊</button>
                                    </div>

                                    <div :id="'editor-' + block.id" class="bg-white border rounded quill-editor"
                                        style="min-height:100px;"></div>
                                </div>
                            </template>

                            <template x-if="block.type === 'image'">
                                <div class="text-center">
                                    <template x-if="block.src">
                                        <img :src="block.src" :style="getMediaStyle(block)"
                                            class="mx-auto rounded-lg shadow-md" />
                                        <div class="flex justify-center gap-2 mt-2">
                                            <button @click="removeMedia(block.id)"
                                                class="px-3 py-1 text-sm bg-red-100 rounded">Remove</button>
                                        </div>
                                    </template>
                                    <template x-if="!block.src">
                                        <label class="block mt-2 cursor-pointer">
                                            <input type="file" accept="image/*"
                                                @change="handleFileUpload($event, block.id, 'image')" class="hidden" />
                                            <div
                                                class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                <p class="text-sm text-gray-500">📁 Click to upload image</p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <template x-if="block.type === 'video'">
                                <div class="text-center">
                                    <template x-if="block.src">
                                        <video :src="block.src" controls :style="getMediaStyle(block)"
                                            class="mx-auto rounded-lg shadow-md"></video>
                                    </template>
                                    <template x-if="!block.src">
                                        <label class="block mt-2 cursor-pointer">
                                            <input type="file" accept="video/*"
                                                @change="handleFileUpload($event, block.id, 'video')" class="hidden" />
                                            <div
                                                class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                <p class="text-sm text-gray-500">🎬 Click to upload video</p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <template x-if="block.type === 'pdf'">
                                <div class="text-center">
                                    <template x-if="block.src">
                                        <iframe :src="block.src" class="w-full rounded-lg shadow-md h-[500px]"></iframe>
                                    </template>
                                    <template x-if="!block.src">
                                        <label class="block mt-2 cursor-pointer">
                                            <input type="file" accept="application/pdf"
                                                @change="handleFileUpload($event, block.id, 'pdf')" class="hidden" />
                                            <div
                                                class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                <p class="text-sm text-gray-500">📄 Click to upload PDF</p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        <form id="saveForm" method="POST" action="{{ route('admin.pagebuilder.builder.save', $page) }}">
            @csrf
            <input type="hidden" name="content" id="pageContent">
        </form>

    </div>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.min.css">
    <script src="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.min.js"></script>

    <script>
        // =================================================================
        //                 TABLE MODULE REGISTRATION (CRITICAL FIX)
        // =================================================================
        const QuillBetterTable = window.QuillBetterTable;
        try {
            // CRITICAL: Register 'modules/table' key to override native Quill table functionality
            Quill.register({ 'modules/table': QuillBetterTable.default }, true);
        } catch (e) {
            console.error('Quill Table Module Registration Failed:', e);
            // Handle registration failure gracefully if needed
        }

        // =================================================================
        //                 MAIN ALPINE JS LOGIC
        // =================================================================

        function pageBuilder(savedContent = null) {
            return {
                availableBlocks: [{
                    type: 'section',
                    label: '📁 Section',
                    title: 'New Section',
                    blocks: [],
                    expanded: true
                },
                {
                    type: 'heading',
                    label: '🧱 Heading',
                    defaultContent: '<h2>Heading</h2>'
                },
                {
                    type: 'text',
                    label: '📝 Text',
                    defaultContent: '<p>Type something...</p>'
                },
                // {
                //     type: 'table',
                //     label: '📊 Table',
                //     defaultContent: '<p>Click table icon to insert table</p>'
                // },
                {
                    type: 'image',
                    label: '🖼️ Image',
                    src: ''
                },
                {
                    type: 'video',
                    label: '🎥 Video',
                    src: ''
                },
                {
                    type: 'pdf',
                    label: '📄 PDF',
                    src: ''
                },
                ],
                blocks: [],
                quills: {},
                historyStack: [],
                redoStack: [],

                initAll() {
                    try {
                        if (savedContent) {
                            const parsed = JSON.parse(savedContent);
                            if (Array.isArray(parsed)) {
                                this.blocks = parsed.map(b => ({ ...b, id: b.id || this._genId() }));
                            } else if (parsed.blocks) {
                                this.blocks = parsed.blocks.map(b => ({ ...b, id: b.id || this._genId() }));
                            } else if (parsed.json) {
                                this.blocks = parsed.json || [];
                            } else {
                                this.blocks = parsed || [];
                            }
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
                    } catch (e) {
                        console.error('initAllQuills failed:', e);
                    }
                },

                initBlockQuills(block) {
                    try {
                        if (!block) return;
                        const types = ['text', 'heading', 'table'];

                        if (types.includes(block.type)) {
                            this.initQuill(block.id, block.content || block.defaultContent || '<p></p>');
                        }

                        if (block.type === 'section' && Array.isArray(block.blocks)) {
                            block.blocks.forEach(sub => {
                                if (types.includes(sub.type)) {
                                    this.initQuill(sub.id, sub.content || sub.defaultContent || '<p></p>');
                                }
                            });
                        }
                    } catch (e) {
                        console.error('initBlockQuills failed:', e);
                    }
                },

                _genId() {
                    return 'b_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8);
                },

                // --- Drag & Drop/Reorder/Delete logic ---

                dragBlock(e, tpl) {
                    try {
                        e.dataTransfer.setData('blockTpl', JSON.stringify(tpl));
                    } catch (e) {
                        console.error('dragBlock failed:', e);
                    }
                },

                dropBlock(e) {
                    try {
                        const data = e.dataTransfer.getData('blockTpl');
                        if (!data) return;
                        const tpl = JSON.parse(data);
                        const newBlock = JSON.parse(JSON.stringify(tpl));
                        newBlock.id = this._genId();
                        if (['text', 'heading', 'table'].includes(newBlock.type)) {
                            newBlock.content = newBlock.defaultContent || '<p></p>';
                        }
                        if (newBlock.type === 'section' && !Array.isArray(newBlock.blocks)) {
                            newBlock.blocks = [];
                            newBlock.expanded = true;
                            newBlock.title = newBlock.title || 'New Section';
                        }
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
                        const newBlock = JSON.parse(JSON.stringify(tpl));
                        newBlock.id = this._genId();
                        if (['text', 'heading', 'table'].includes(newBlock.type)) {
                            newBlock.content = newBlock.defaultContent || '<p></p>';
                        }
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
                    } catch (e) {
                        console.error('initSortables failed:', e);
                    }
                },

                reorderByIds(ids, scope = 'root') {
                    try {
                        if (!ids || !ids.length) return;
                        const map = {};
                        this.blocks.forEach(b => map[b.id] = b);
                        this.blocks = ids.map(id => map[id]).filter(Boolean);
                        this.pushHistory();
                        this.$nextTick(() => this.initAllQuills());
                    } catch (e) {
                        console.error('reorderByIds failed:', e);
                    }
                },

                reorderSectionByIds(section, ids) {
                    try {
                        if (!ids || !ids.length) return;
                        const map = {};
                        (section.blocks || []).forEach(b => map[b.id] = b);
                        section.blocks = ids.map(id => map[id]).filter(Boolean);
                        this.pushHistory();
                        this.$nextTick(() => this.initAllQuills());
                    } catch (e) {
                        console.error('reorderSectionByIds failed:', e);
                    }
                },

                moveBlockUp(index) {
                    try {
                        if (index <= 0) return;
                        const arr = this.blocks;
                        [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
                        this.blocks = [...arr];
                        this.pushHistory();
                    } catch (e) {
                        console.error('moveBlockUp failed:', e);
                    }
                },
                moveBlockDown(index) {
                    try {
                        if (index >= this.blocks.length - 1) return;
                        const arr = this.blocks;
                        [arr[index + 1], arr[index]] = [arr[index], arr[index + 1]];
                        this.blocks = [...arr];
                        this.pushHistory();
                    } catch (e) {
                        console.error('moveBlockDown failed:', e);
                    }
                },
                duplicateBlock(index) {
                    try {
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
                    } catch (e) {
                        console.error('duplicateBlock failed:', e);
                    }
                },
                confirmRemove(blockId, index) {
                    try {
                        Swal.fire({ title: 'Delete?', text: 'Remove this block?', icon: 'warning', showCancelButton: true })
                            .then(res => {
                                if (res.isConfirmed) {
                                    this.blocks.splice(index, 1);
                                    this.pushHistory();
                                }
                            });
                    } catch (e) {
                        console.error('confirmRemove failed:', e);
                    }
                },
                moveSubUp(section, sIndex) {
                    try {
                        if (sIndex <= 0) return;
                        const arr = section.blocks;
                        [arr[sIndex - 1], arr[sIndex]] = [arr[sIndex], arr[sIndex - 1]];
                        section.blocks = [...arr];
                        this.pushHistory();
                    } catch (e) {
                        console.error('moveSubUp failed:', e);
                    }
                },
                moveSubDown(section, sIndex) {
                    try {
                        if (sIndex >= section.blocks.length - 1) return;
                        const arr = section.blocks;
                        [arr[sIndex + 1], arr[sIndex]] = [arr[index], arr[sIndex + 1]];
                        section.blocks = [...arr];
                        this.pushHistory();
                    } catch (e) {
                        console.error('moveSubDown failed:', e);
                    }
                },
                duplicateSub(section, sIndex) {
                    try {
                        const sb = JSON.parse(JSON.stringify(section.blocks[sIndex]));
                        sb.id = this._genId();
                        section.blocks.splice(sIndex + 1, 0, sb);
                        this.$nextTick(() => {
                            this.initBlockQuills(sb);
                            this.initSortables();
                            this.pushHistory();
                        });
                    } catch (e) {
                        console.error('duplicateSub failed:', e);
                    }
                },
                confirmRemoveSub(section, index) {
                    try {
                        Swal.fire({ title: 'Delete?', text: 'Remove sub-block?', icon: 'warning', showCancelButton: true })
                            .then(res => {
                                if (res.isConfirmed) {
                                    section.blocks.splice(index, 1);
                                    this.pushHistory();
                                }
                            });
                    } catch (e) {
                        console.error('confirmRemoveSub failed:', e);
                    }
                },

                // =================================================================
                //                 QUILL INTEGRATION WITH TABLE FIX
                // =================================================================

                initQuill(blockId, initialHtml = '') {
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
                                modules: {
                                    toolbar: toolbarSelector,
                                    table: {
                                        operationMenu: {
                                            items: {
                                                unmergeCells: { text: 'Unmerge' }
                                            }
                                        },
                                    },
                                },
                                placeholder: 'Type here...'
                            });

                            if (initialHtml) quill.root.innerHTML = initialHtml;
                            this.quills[blockId] = quill;

                            // ✅ CRITICAL FIX: Custom handler for the table button to show SweetAlert prompt
                            const tableButton = tbEl.querySelector('.ql-table');
                            if (tableButton) {
                                const tableModule = quill.getModule('table');

                                // Remove native handler if it exists
                                if (tableModule && tableModule.clickHandler) {
                                    tableButton.removeEventListener('click', tableModule.clickHandler);
                                }

                                // Unique input IDs for the SweetAlert modal
                                const rowId = `tbl-rows-${blockId}`;
                                const colId = `tbl-cols-${blockId}`;

                                tableButton.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    e.stopPropagation();

                                    Swal.fire({
                                        title: 'Insert Table',
                                        html: `<input id="${rowId}" class="swal2-input" placeholder="Rows (e.g. 3)" value="3" type="number" min="1">
                                                                               <input id="${colId}" class="swal2-input" placeholder="Cols (e.g. 3)" value="3" type="number" min="1">`,
                                        preConfirm: () => ({
                                            rows: parseInt(document.getElementById(rowId).value || 0),
                                            cols: parseInt(document.getElementById(colId).value || 0)
                                        }),
                                        showCancelButton: true
                                    }).then(res => {
                                        if (res.isConfirmed && res.value.rows > 0 && res.value.cols > 0) {
                                            try {
                                                tableModule.insertTable(res.value.rows, res.value.cols);
                                                this.updateQuillContent(blockId, quill.root.innerHTML);
                                                this.pushHistory();
                                            } catch (e) {
                                                console.error('Table Insertion failed:', e);
                                                Swal.fire('Error', 'Table insertion failed.', 'error');
                                            }
                                        }
                                    });
                                });
                            }

                            quill.on('text-change', () => {
                                this.updateQuillContent(blockId, quill.root.innerHTML);
                            });
                        } catch (e) {
                            console.error(`Quill initialization for ${blockId} failed:`, e);
                        }
                    };

                    attemptInit();
                },

                updateQuillContent(blockId, html) {
                    try {
                        const findAndUpdate = (arr) => {
                            for (let b of arr) {
                                if (b.id === blockId) {
                                    b.content = html;
                                    return true;
                                }
                                if (b.type === 'section' && Array.isArray(b.blocks)) {
                                    if (findAndUpdate(b.blocks)) return true;
                                }
                            }
                            return false;
                        };
                        findAndUpdate(this.blocks);
                    } catch (e) {
                        console.error('updateQuillContent failed:', e);
                    }
                },

                openLinkDialog(blockId) {
                    try {
                        const quill = this.quills[blockId];
                        if (!quill) return Swal.fire('Error', 'Editor not ready', 'error');
                        const range = quill.getSelection();
                        if (!range || range.length === 0) {
                            return Swal.fire('Select text', 'Please select the text you want to link.', 'info');
                        }

                        Swal.fire({
                            title: 'Insert Link',
                            html: `<input id="swal-link-url" class="swal2-input" placeholder="https://example.com">
                                                                             <input id="swal-link-target" class="swal2-input" placeholder="Target (_blank or _self)" value="_blank">`,
                            preConfirm: () => ({
                                url: document.getElementById('swal-link-url').value,
                                target: document.getElementById('swal-link-target').value || '_blank'
                            }),
                            showCancelButton: true
                        }).then(res => {
                            if (res.isConfirmed && res.value.url) {
                                quill.format('link', res.value.url);
                                setTimeout(() => {
                                    const anchors = quill.root.querySelectorAll('a[href="' + res.value.url +
                                        '"]');
                                    if (anchors.length) anchors[anchors.length - 1].setAttribute('target', res
                                        .value.target);
                                    this.updateQuillContent(blockId, quill.root.innerHTML);
                                    this.pushHistory();
                                }, 50);
                            }
                        });
                    } catch (e) {
                        console.error('openLinkDialog failed:', e);
                    }
                },
                // --- Other Utility and History functions (with Try/Catch) ---
                makeButtonFromSelection(blockId) {
                    try {
                        const quill = this.quills[blockId];
                        if (!quill) return Swal.fire('Error', 'Editor not ready', 'error');
                        const range = quill.getSelection();
                        if (!range || range.length === 0) return Swal.fire('Select text', 'Select text to convert into button.',
                            'info');

                        Swal.fire({
                            title: 'Button Options',
                            html: `<input id="btn-url" class="swal2-input" placeholder="https://example.com">
                                                                             <input id="btn-class" class="swal2-input" placeholder="Additional classes (optional)" value="bg-blue-600 text-white px-4 py-2 rounded">`,
                            preConfirm: () => ({
                                url: document.getElementById('btn-url').value,
                                cls: document.getElementById('btn-class').value
                            }),
                            showCancelButton: true
                        }).then(res => {
                            if (res.isConfirmed) {
                                const { url, cls } = res.value;
                                const selectedHtml = quill.getText(range.index, range.length);
                                const html = `<a href="${url || '#'}" class="${cls || ''}" target="_blank">${quill.getText(range.index, range.length)}</a>`;
                                quill.deleteText(range.index, range.length);
                                quill.clipboard.dangerouslyPasteHTML(range.index, html);
                                this.updateQuillContent(blockId, quill.root.innerHTML);
                                this.pushHistory();
                            }
                        });
                    } catch (e) {
                        console.error('makeButtonFromSelection failed:', e);
                    }
                },
                async handleFileUpload(e, blockId, type, section = null) {
                    // This function already has robust catch blocks in your original code
                    // Reusing the original robust logic here for consistency
                    try {
                        const file = e.target.files[0];
                        if (!file) return;

                        const { value: formValues } = await Swal.fire({
                            title: "Upload Options",
                            html: `
                                                                <input id="custom_name" class="swal2-input" placeholder="File name (optional)" />
                                                                <select id="base_path" class="swal2-select">
                                                                    <option value="storage" selected>storage</option>
                                                                    <option value="wp-content">wp-content</option>
                                                                </select>
                                                            `,
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

                        const res = await fetch('{{ route('admin.pagebuilder.builder.upload', $page) }}', {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
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

                            Swal.fire({
                                icon: "success", title: "✅ File Uploaded", text: `${data.filename}`,
                                timer: 1400, showConfirmButton: false,
                            });

                            this.pushHistory();
                        } else {
                            Swal.fire("Error", data.message || "Upload failed.", "error");
                        }
                    } catch (err) {
                        console.error('handleFileUpload failed:', err);
                        Swal.fire("Error", "Upload failed.", "error");
                    }
                },
                removeMedia(blockId) {
                    try {
                        const update = (arr) => {
                            for (let b of arr) {
                                if (b.id === blockId) { delete b.src; return true; }
                                if (b.type === 'section' && Array.isArray(b.blocks)) {
                                    if (update(b.blocks)) return true;
                                }
                            }
                            return false;
                        };
                        update(this.blocks);
                        this.blocks = [...this.blocks];
                        this.pushHistory();
                    } catch (e) {
                        console.error('removeMedia failed:', e);
                    }
                },
                removeMediaFromSub(section, subId) {
                    try {
                        const idx = (section.blocks || []).findIndex(s => s.id === subId);
                        if (idx !== -1) {
                            delete section.blocks[idx].src;
                            this.blocks = [...this.blocks];
                            this.pushHistory();
                        }
                    } catch (e) {
                        console.error('removeMediaFromSub failed:', e);
                    }
                },
                getMediaStyle(block) {
                    try {
                        const w = block.width || 600;
                        const h = block.height || 300;
                        return `width:${w}px; height:${h}px; object-fit:contain;`;
                    } catch (e) {
                        console.error('getMediaStyle failed:', e);
                        return '';
                    }
                },
                savePage() {
                    try {
                        Object.keys(this.quills).forEach(id => {
                            const q = this.quills[id];
                            if (q && q.root.innerHTML) this.updateQuillContent(id, q.root.innerHTML);
                        });

                        const payload = { blocks: this.blocks };
                        document.getElementById('pageContent').value = JSON.stringify(payload);
                        Swal.fire({ title: 'Saving...', text: 'Please wait', icon: 'info', showConfirmButton: false });
                        this.$nextTick(() => document.getElementById('saveForm').submit());
                    } catch (e) {
                        console.error('savePage failed:', e);
                        Swal.fire('Error', 'Failed to prepare for saving.', 'error');
                    }
                },
                exportJSON() {
                    try {
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
                    } catch (e) {
                        console.error('exportJSON failed:', e);
                        Swal.fire('Error', 'Failed to export JSON.', 'error');
                    }
                },
                importJSONPrompt() {
                    try {
                        Swal.fire({
                            title: 'Import JSON',
                            html: `<input type="file" id="jsonFile" accept="application/json" class="swal2-file">`,
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
                                    if (parsed.blocks) {
                                        this.blocks = parsed.blocks.map(b => ({ ...b, id: b.id || this._genId() }));
                                    } else {
                                        this.blocks = Array.isArray(parsed) ? parsed.map(b => ({ ...b, id: b.id || this._genId() })) : [];
                                    }
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
                    } catch (e) {
                        console.error('importJSONPrompt failed:', e);
                    }
                },
                pushHistory() {
                    try {
                        Object.keys(this.quills).forEach(id => {
                            const q = this.quills[id];
                            if (q && q.root.innerHTML) this.updateQuillContent(id, q.root.innerHTML);
                        });

                        const snapshot = JSON.stringify(this.blocks);
                        if (this.historyStack.length > 0 && this.historyStack[this.historyStack.length - 1] === snapshot) return;

                        this.historyStack.push(snapshot);
                        if (this.historyStack.length > 50) this.historyStack.shift();
                        this.redoStack = [];
                    } catch (e) {
                        console.error('pushHistory failed:', e);
                    }
                },
                undo() {
                    try {
                        if (this.historyStack.length <= 1) return Swal.fire('Nothing to undo', '', 'info');
                        const cur = this.historyStack.pop();
                        this.redoStack.push(cur);

                        const prev = this.historyStack[this.historyStack.length - 1];
                        if (prev) {
                            try { this.blocks = JSON.parse(prev); } catch (e) { }

                            this.quills = {};
                            this.$nextTick(() => {
                                this.initAllQuills();
                                this.initSortables();
                            });
                        }
                    } catch (e) {
                        console.error('undo failed:', e);
                    }
                },
                redo() {
                    try {
                        if (!this.redoStack.length) return Swal.fire('Nothing to redo', '', 'info');
                        const next = this.redoStack.pop();
                        try { this.blocks = JSON.parse(next); } catch (e) { }
                        this.historyStack.push(next);

                        this.quills = {};
                        this.$nextTick(() => {
                            this.initAllQuills();
                            this.initSortables();
                        });
                    } catch (e) {
                        console.error('redo failed:', e);
                    }
                },
            };
        }
    </script>


    <style>
        /* 1. Base Quill Editor Styles */
        .ql-editor {
            min-height: 120px;
        }

        .quill-editor {
            border: 1px solid #e5e7eb;
        }

        /* ------------------------------------------------------------- */
        /* 2. Quill Better Table Styles (Table formatting aur visibility) */
        /* ------------------------------------------------------------- */

        .ql-editor table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .ql-editor th,
        .ql-editor td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        .ql-editor th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        /* Quill Better Table ke operation menu ko front mein rakhne ke liye z-index */
        .ql-better-table-operation-menu {
            z-index: 50;
        }
    </style>
@endsection
