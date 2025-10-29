@extends('layouts.admin.app')

@section('content')
    <div x-data="pageBuilder({{ $page->content ? json_encode($page->content) : 'null' }})" x-init="initAll()"
        class="relative min-h-screen p-4 bg-gray-50">

        <!-- Header -->
        <div class="flex flex-col mb-4 space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <h1 class="text-2xl font-bold text-gray-800">🧱 Page Builder — {{ $page->title }}</h1>
            <button @click="savePage"
                class="flex items-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                <span>💾</span><span>Save Page</span>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Toolbox -->
            <div class="p-4 bg-white rounded-lg shadow lg:col-span-3">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">Available Blocks</h2>
                <template x-for="tpl in availableBlocks" :key="tpl.type">
                    <div draggable="true" @dragstart="dragBlock($event, tpl)"
                        class="p-3 mb-2 text-gray-700 transition border border-gray-200 rounded cursor-grab hover:bg-blue-50">
                        <span x-text="tpl.label"></span>
                    </div>
                </template>
            </div>

            <!-- Canvas -->
            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building</p>
                </template>

                <template x-for="(block, index) in blocks" :key="block.id">
                    <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group">
                        <button @click="confirmRemove(block.id, index)"
                            class="absolute text-xs text-red-600 opacity-0 top-2 right-2 group-hover:opacity-100">✖</button>

                        <!-- Text / Heading block: Quill editor -->
                        <template x-if="block.type === 'text' || block.type === 'heading'">
                            <div class="space-y-2">
                                <!-- Toolbar specific to this block -->
                                <div :id="'toolbar-' + block.id"
                                    class="flex flex-wrap items-center gap-2 p-2 bg-white rounded shadow-sm">
                                    <!-- You can add more toolbar items; Quill toolbar below matches these selectors -->
                                    <select class="ql-size"></select>
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <select class="ql-color"></select>
                                    <select class="ql-align"></select>
                                    <button class="ql-clean"></button>
                                </div>

                                <!-- Editor container -->
                                <div :id="'editor-' + block.id" class="bg-white border rounded quill-editor"
                                    style="min-height:120px;">
                                    {{-- Quill will populate this --}}
                                </div>

                                <!-- Small helper controls for block-level width/height if you want -->
                            </div>
                        </template>

                        <!-- Image -->
                        <template x-if="block.type === 'image'">
                            <div class="text-center">
                                <template x-if="block.src">
                                    <img :src="block.src" :style="getMediaStyle(block)"
                                        class="mx-auto rounded-lg shadow-md" />
                                </template>
                                <template x-if="!block.src">
                                    <div class="mt-2">
                                        <input type="file" accept="image/*"
                                            @change="handleFileUpload($event, block.id, 'image')"
                                            class="w-full p-2 border rounded" />
                                        <p class="mt-1 text-sm text-gray-400">Upload an image</p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Video -->
                        <template x-if="block.type === 'video'">
                            <div class="text-center">
                                <template x-if="block.src">
                                    <video :src="block.src" controls :style="getMediaStyle(block)"
                                        class="mx-auto rounded-lg shadow-md"></video>
                                </template>
                                <template x-if="!block.src">
                                    <div class="mt-2">
                                        <input type="file" accept="video/*"
                                            @change="handleFileUpload($event, block.id, 'video')"
                                            class="w-full p-2 border rounded" />
                                        <p class="mt-1 text-sm text-gray-400">Upload a video file</p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- PDF -->
                        <template x-if="block.type === 'pdf'">
                            <div class="text-center">
                                <template x-if="block.src">
                                    <iframe :src="block.src" :style="getMediaStyle(block)"
                                        class="w-full rounded-lg shadow-md"></iframe>
                                </template>
                                <template x-if="!block.src">
                                    <div class="mt-2">
                                        <input type="file" accept="application/pdf"
                                            @change="handleFileUpload($event, block.id, 'pdf')"
                                            class="w-full p-2 border rounded" />
                                        <p class="mt-1 text-sm text-gray-400">Upload a PDF file</p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <form id="saveForm" method="POST" action="{{ route('admin.pagebuilder.builder.save', $page) }}">
            @csrf
            <input type="hidden" name="content" id="pageContent">
        </form>

    </div>

    <!-- CSS/JS CDN -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        function pageBuilder(savedContent = null) {
            return {
                availableBlocks: [
                    { type: 'heading', label: '🧱 Heading', defaultContent: '<p><strong>Heading</strong></p>' },
                    { type: 'text', label: '📝 Text', defaultContent: '<p>Type something...</p>' },
                    { type: 'image', label: '🖼️ Image', src: '', width: 400, height: 300 },
                    { type: 'video', label: '🎥 Video', src: '', width: 560, height: 315 },
                    { type: 'pdf', label: '📄 PDF', src: '', width: 600, height: 800 },
                ],

                // blocks array (each block must have unique id)
                blocks: [],
                quills: {}, // store quill instances keyed by block.id

                // initialize saved content or start with empty
                initAll() {
                    // restore saved content
                    if (savedContent) {
                        try {
                            const parsed = JSON.parse(savedContent);
                            // ensure each block has id (in case older data didn't)
                            this.blocks = parsed.map(b => ({ ...b, id: b.id || this._genId() }));
                        } catch (e) {
                            console.error('Saved content parse error', e);
                            this.blocks = [];
                        }
                    } else {
                        this.blocks = [];
                    }

                    // init Quill editors for text blocks after DOM ready
                    this.$nextTick(() => {
                        this.blocks.forEach((b, idx) => {
                            if (b.type === 'text' || b.type === 'heading') {
                                this.initQuill(b.id, b.content || b.defaultContent || '');
                            }
                        });
                    });
                },

                // helpers
                _genId() {
                    return 'b_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8);
                },

                dragBlock(e, tpl) {
                    e.dataTransfer.setData('blockTpl', JSON.stringify(tpl));
                },

                dropBlock(e) {
                    const data = e.dataTransfer.getData('blockTpl');
                    if (!data) return;
                    const tpl = JSON.parse(data);
                    const newBlock = JSON.parse(JSON.stringify(tpl));
                    newBlock.id = this._genId();
                    // if text/heading, initialize content property
                    if (newBlock.type === 'text' || newBlock.type === 'heading') {
                        newBlock.content = newBlock.defaultContent || '<p></p>';
                    }
                    this.blocks.push(newBlock);

                    // initialize Quill for new text blocks
                    this.$nextTick(() => {
                        const idx = this.blocks.findIndex(b => b.id === newBlock.id);
                        if (newBlock.type === 'text' || newBlock.type === 'heading') {
                            this.initQuill(newBlock.id, newBlock.content);
                        }
                    });
                },

                initQuill(blockId, initialHtml = '') {
                    // avoid re-init
                    if (this.quills[blockId]) return;

                    const toolbarSelector = '#toolbar-' + blockId;
                    const editorSelector = '#editor-' + blockId;

                    // If elements not yet in DOM wait a bit
                    const attemptInit = () => {
                        const tb = document.querySelector(toolbarSelector);
                        const ed = document.querySelector(editorSelector);
                        if (!ed) {
                            // try again shortly
                            setTimeout(attemptInit, 50);
                            return;
                        }

                        // create quill instance
                        const quill = new Quill(editorSelector, {
                            theme: 'snow',
                            modules: {
                                toolbar: tb ? tb : [
                                    [{ 'size': [] }],
                                    ['bold', 'italic', 'underline'],
                                    [{ 'color': [] }],
                                    [{ 'align': [] }],
                                    ['clean']
                                ]
                            },
                            placeholder: 'Type here...'
                        });

                        // set initial html (if any)
                        if (initialHtml) {
                            quill.root.innerHTML = initialHtml;
                        }

                        // store quill instance by id
                        this.quills[blockId] = quill;

                        // when content changes, update blocks array
                        quill.on('text-change', () => {
                            const idx = this.blocks.findIndex(b => b.id === blockId);
                            if (idx !== -1) {
                                // store HTML so formatting and selection-level styling persist
                                this.blocks[idx].content = quill.root.innerHTML;
                            }
                        });
                    };

                    attemptInit();
                },

                // when removing block, confirm and cleanup quill
                confirmRemove(blockId, index) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This block will be removed.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove it',
                    }).then((res) => {
                        if (res.isConfirmed) {
                            // destroy quill instance if exists
                            if (this.quills[blockId]) {
                                try {
                                    // Quill has no explicit destroy; remove listeners and DOM reference
                                    this.quills[blockId] = null;
                                    delete this.quills[blockId];
                                } catch (e) { /* ignore */ }
                            }
                            this.blocks.splice(index, 1);
                            Swal.fire('Removed', 'Block deleted.', 'success');
                        }
                    });
                },

                // File upload for media (image/video/pdf)
                handleFileUpload(e, blockId, type) {
                    const file = e.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = () => {
                        const idx = this.blocks.findIndex(b => b.id === blockId);
                        if (idx === -1) return;
                        this.blocks[idx].src = reader.result;
                        Swal.fire({
                            icon: 'success',
                            title: `${type.toUpperCase()} Uploaded`,
                            text: 'Upload successful!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    };
                    reader.readAsDataURL(file);
                },

                // Save page: before submit, ensure all quills are read into blocks
                savePage() {
                    // push quill content explicitly (in case some quill didn't fire)
                    Object.keys(this.quills).forEach(id => {
                        const q = this.quills[id];
                        const idx = this.blocks.findIndex(b => b.id === id);
                        if (q && idx !== -1) {
                            this.blocks[idx].content = q.root.innerHTML;
                        }
                    });

                    const payload = JSON.stringify(this.blocks);
                    document.getElementById('pageContent').value = payload;

                    Swal.fire({
                        title: 'Saving...',
                        text: 'Please wait',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    });

                    // Submit form
                    this.$nextTick(() => {
                        document.getElementById('saveForm').submit();
                    });
                },

                getMediaStyle(block) {
                    const w = block.width || 400;
                    const h = block.height || 300;
                    return `width:${w}px; height:${h}px; object-fit:contain;`;
                },

            };
        }
    </script>

    <style>
        /* light styling to make quill editors fit nicely */
        .ql-editor {
            min-height: 120px;
            outline: none;
        }

        .quill-editor {
            border: 1px solid #e5e7eb;
        }
    </style>
@endsection
