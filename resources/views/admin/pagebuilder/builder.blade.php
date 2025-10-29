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
            <!-- Toolbox (sticky) -->
            <div class="sticky self-start p-4 bg-white rounded-lg shadow lg:col-span-3 h-fit top-4">
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

                        <!-- Section -->
                        <template x-if="block.type === 'section'">
                            <div class="overflow-hidden bg-white border rounded-lg shadow">
                                <button @click="block.expanded = !block.expanded"
                                    class="flex items-center justify-between w-full px-4 py-2 transition bg-blue-100 hover:bg-blue-200">
                                    <input type="text" x-model="block.title"
                                        class="flex-1 font-semibold text-gray-700 bg-transparent border-none outline-none" />
                                    <span x-text="block.expanded ? '▾' : '▸'"></span>
                                </button>

                                <div x-show="block.expanded" x-collapse class="p-4 bg-gray-50">
                                    <div class="border-2 border-dashed border-gray-300 rounded p-4 min-h-[100px]"
                                        @dragover.prevent @drop="dropBlockToSection($event, block)">
                                        <template x-if="!block.blocks || block.blocks.length === 0">
                                            <p class="text-sm text-center text-gray-400">Drag content blocks here...</p>
                                        </template>

                                        <template x-for="(sub, sIndex) in block.blocks" :key="sub.id">
                                            <div class="relative p-3 mb-3 bg-white border rounded shadow-sm group">
                                                <button @click="confirmRemoveSub(block, sIndex)"
                                                    class="absolute text-xs text-red-600 opacity-0 top-2 right-2 group-hover:opacity-100">✖</button>

                                                <!-- Text / Heading (nested) -->
                                                <template x-if="sub.type === 'text' || sub.type === 'heading'">
                                                    <div>
                                                        <div :id="'toolbar-' + sub.id"
                                                            class="flex flex-wrap gap-2 p-2 mb-2 bg-white rounded shadow-sm">
                                                            <select class="ql-size"></select>
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <select class="ql-color"></select>
                                                            <select class="ql-align"></select>
                                                            <button class="ql-clean"></button>
                                                        </div>
                                                        <div :id="'editor-' + sub.id"
                                                            class="bg-white border rounded quill-editor"
                                                            style="min-height:100px;"></div>
                                                    </div>
                                                </template>

                                                <!-- Image (nested) -->
                                                <template x-if="sub.type === 'image'">
                                                    <div class="text-center">
                                                        <template x-if="sub.src">
                                                            <img :src="sub.src"
                                                                class="max-w-full mx-auto rounded-lg shadow-md" />
                                                        </template>
                                                        <template x-if="!sub.src">
                                                            <label class="block mt-2 cursor-pointer">
                                                                <input type="file" accept="image/*"
                                                                    @change="handleFileUpload($event, sub.id, 'image', block)"
                                                                    class="hidden" />
                                                                <div
                                                                    class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                                                    <p class="text-sm text-gray-500">📁 Click to upload
                                                                        image</p>
                                                                </div>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Video (nested) -->
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
                                                                    <p class="text-sm text-gray-500">🎬 Click to upload
                                                                        video</p>
                                                                </div>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- PDF (nested) -->
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
                                                                    <p class="text-sm text-gray-500">📄 Click to upload PDF
                                                                    </p>
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
                        </template>

                        <!-- Text / Heading (root) -->
                        <template x-if="block.type === 'text' || block.type === 'heading'">
                            <div class="space-y-2">
                                <div :id="'toolbar-' + block.id"
                                    class="flex flex-wrap items-center gap-2 p-2 bg-white rounded shadow-sm">
                                    <select class="ql-size"></select>
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <select class="ql-color"></select>
                                    <select class="ql-align"></select>
                                    <button class="ql-clean"></button>
                                </div>

                                <div :id="'editor-' + block.id" class="bg-white border rounded quill-editor"
                                    style="min-height:100px;"></div>
                            </div>
                        </template>

                        <!-- Image (root) -->
                        <template x-if="block.type === 'image'">
                            <div class="text-center">
                                <template x-if="block.src">
                                    <img :src="block.src" :style="getMediaStyle(block)"
                                        class="mx-auto rounded-lg shadow-md" />
                                </template>
                                <template x-if="!block.src">
                                    <label class="block mt-2 cursor-pointer">
                                        <input type="file" accept="image/*"
                                            @change="handleFileUpload($event, block.id, 'image')" class="hidden" />
                                        <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                            <p class="text-sm text-gray-500">📁 Click to upload image</p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- Video (root) -->
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
                                        <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                                            <p class="text-sm text-gray-500">🎬 Click to upload video</p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- PDF (root) -->
                        <template x-if="block.type === 'pdf'">
                            <div class="text-center">
                                <template x-if="block.src">
                                    <iframe :src="block.src" class="w-full rounded-lg shadow-md h-[500px]"></iframe>
                                </template>
                                <template x-if="!block.src">
                                    <label class="block mt-2 cursor-pointer">
                                        <input type="file" accept="application/pdf"
                                            @change="handleFileUpload($event, block.id, 'pdf')" class="hidden" />
                                        <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
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

        <form id="saveForm" method="POST" action="{{ route('admin.pagebuilder.builder.save', $page) }}">
            @csrf
            <input type="hidden" name="content" id="pageContent">
        </form>

    </div>

    <!-- CSS/JS CDN -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        function pageBuilder(savedContent = null) {
            return {
                availableBlocks: [
                    { type: 'section', label: '📁 Section', title: 'New Section', blocks: [], expanded: true },
                    { type: 'heading', label: '🧱 Heading', defaultContent: '<p><strong>Heading</strong></p>' },
                    { type: 'text', label: '📝 Text', defaultContent: '<p>Type something...</p>' },
                    { type: 'image', label: '🖼️ Image', src: '' },
                    { type: 'video', label: '🎥 Video', src: '' },
                    { type: 'pdf', label: '📄 PDF', src: '' },
                ],
                blocks: [],
                quills: {},

                initAll() {
                    if (savedContent) {
                        try {
                            const parsed = JSON.parse(savedContent);
                            // ensure ids exist
                            this.blocks = parsed.map(b => ({ ...b, id: b.id || this._genId() }));
                        } catch (e) {
                            console.error('Saved content parse error', e);
                            this.blocks = [];
                        }
                    } else {
                        this.blocks = [];
                    }

                    // init quills
                    this.$nextTick(() => {
                        this.blocks.forEach(b => this.initBlockQuills(b));
                    });
                },

                initBlockQuills(block) {
                    if (!block) return;
                    if (block.type === 'text' || block.type === 'heading') {
                        this.initQuill(block.id, block.content || block.defaultContent || '');
                    }
                    if (block.type === 'section' && Array.isArray(block.blocks)) {
                        block.blocks.forEach(sub => {
                            if (sub.type === 'text' || sub.type === 'heading') {
                                this.initQuill(sub.id, sub.content || sub.defaultContent || '');
                            }
                        });
                    }
                },

                _genId() {
                    return 'b_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8);
                },

                dragBlock(e, tpl) {
                    e.dataTransfer.setData('blockTpl', JSON.stringify(tpl));
                },

                dropBlock(e) {
                    // drop on root canvas only
                    const data = e.dataTransfer.getData('blockTpl');
                    if (!data) return;
                    const tpl = JSON.parse(data);
                    const newBlock = JSON.parse(JSON.stringify(tpl));
                    newBlock.id = this._genId();
                    if (newBlock.type === 'text' || newBlock.type === 'heading') {
                        newBlock.content = newBlock.defaultContent || '<p></p>';
                    }
                    // if the tpl is a 'section' it may have blocks property; ensure it's new array
                    if (newBlock.type === 'section' && !Array.isArray(newBlock.blocks)) {
                        newBlock.blocks = [];
                        newBlock.expanded = true;
                        newBlock.title = newBlock.title || 'New Section';
                    }
                    this.blocks.push(newBlock);
                    this.$nextTick(() => this.initBlockQuills(newBlock));
                },

                dropBlockToSection(e, section) {
                    // stop propagation so the root drop doesn't also add the block
                    e.stopPropagation();
                    const data = e.dataTransfer.getData('blockTpl');
                    if (!data) return;
                    const tpl = JSON.parse(data);
                    const newBlock = JSON.parse(JSON.stringify(tpl));
                    newBlock.id = this._genId();
                    if (newBlock.type === 'text' || newBlock.type === 'heading') {
                        newBlock.content = newBlock.defaultContent || '<p></p>';
                    }
                    if (newBlock.type === 'section') {
                        // don't allow nested sections for now — convert to simple section or ignore
                        newBlock.blocks = newBlock.blocks || [];
                        newBlock.expanded = true;
                        newBlock.title = newBlock.title || 'New Section';
                    }
                    // ensure section.blocks exists
                    if (!Array.isArray(section.blocks)) section.blocks = [];
                    section.blocks.push(newBlock);
                    // force reactivity and init quill for nested text blocks
                    this.$nextTick(() => {
                        this.blocks = [...this.blocks];
                        this.initBlockQuills(newBlock);
                    });
                },

                initQuill(blockId, initialHtml = '') {
                    if (this.quills[blockId]) return;
                    const toolbarSelector = '#toolbar-' + blockId;
                    const editorSelector = '#editor-' + blockId;

                    const attemptInit = () => {
                        const ed = document.querySelector(editorSelector);
                        if (!ed) return setTimeout(attemptInit, 50);

                        const tbEl = document.querySelector(toolbarSelector);
                        const quill = new Quill(editorSelector, {
                            theme: 'snow',
                            modules: {
                                toolbar: tbEl ? tbEl : [
                                    [{ 'size': [] }],
                                    ['bold', 'italic', 'underline'],
                                    [{ 'color': [] }],
                                    [{ 'align': [] }],
                                    ['clean']
                                ]
                            },
                            placeholder: 'Type here...'
                        });

                        if (initialHtml) quill.root.innerHTML = initialHtml;
                        this.quills[blockId] = quill;
                        quill.on('text-change', () => {
                            this.updateQuillContent(blockId, quill.root.innerHTML);
                        });
                    };

                    attemptInit();
                },

                updateQuillContent(blockId, html) {
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
                    // force Alpine to re-render
                    this.blocks = [...this.blocks];
                },

                confirmRemove(blockId, index) {
                    Swal.fire({ title: 'Delete?', text: 'Remove this block?', icon: 'warning', showCancelButton: true })
                        .then(res => { if (res.isConfirmed) this.blocks.splice(index, 1); });
                },

                confirmRemoveSub(section, index) {
                    Swal.fire({ title: 'Delete?', text: 'Remove sub-block?', icon: 'warning', showCancelButton: true })
                        .then(res => { if (res.isConfirmed) section.blocks.splice(index, 1); });
                },
                async handleFileUpload(e, blockId, type, section = null) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // 🧾 Ask user for filename + base path
                    const { value: formValues } = await Swal.fire({
                        title: "Upload Options",
                        html: `
                <input id="custom_name" class="swal2-input" placeholder="File name (optional)" />
                <select id="base_path" class="swal2-select">
                    <option value="wp-content" selected>wp-content (recommended)</option>
                    <option value="storage">storage</option>
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

                    if (!formValues) return; // cancelled

                    const formData = new FormData();
                    formData.append("file", file);
                    formData.append("base_path", formValues.base_path || "wp-content");
                    formData.append("custom_name", formValues.custom_name || "");

                    try {
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
                                    if (b.id === blockId) {
                                        b.src = url;
                                        return true;
                                    }
                                    if (b.type === "section" && Array.isArray(b.blocks)) {
                                        if (update(b.blocks)) return true;
                                    }
                                }
                                return false;
                            };

                            update(this.blocks);
                            this.blocks = [...this.blocks];

                            Swal.fire({
                                icon: "success",
                                title: "✅ File Uploaded",
                                text: `${data.filename}`,
                                timer: 1400,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire("Error", data.message || "Upload failed.", "error");
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire("Error", "Upload failed.", "error");
                    }
                },

                async handleFileUpload_old(e, blockId, type, section = null) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        // use your actual named route for upload
                        const res = await fetch('{{ route('admin.pagebuilder.builder.upload', $page) }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });

                        const data = await res.json();

                        if (data.success) {
                            const url = data.url;

                            // recursive update (works for root and nested)
                            const update = (arr) => {
                                for (let b of arr) {
                                    if (b.id === blockId) {
                                        b.src = url;
                                        return true;
                                    }
                                    if (b.type === 'section' && Array.isArray(b.blocks)) {
                                        if (update(b.blocks)) return true;
                                    }
                                }
                                return false;
                            };

                            update(this.blocks);

                            // force Alpine reactivity refresh
                            this.blocks = [...this.blocks];

                            Swal.fire({
                                icon: 'success',
                                title: `${type.toUpperCase()} Uploaded`,
                                text: 'Upload successful!',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Upload failed.', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire('Error', 'Upload failed.', 'error');
                    }
                },

                getMediaStyle(block) {
                    const w = block.width || 400;
                    const h = block.height || 300;
                    return `width:${w}px; height:${h}px; object-fit:contain;`;
                },

                savePage() {
                    // push quill content explicitly
                    Object.keys(this.quills).forEach(id => {
                        const q = this.quills[id];
                        if (q) this.updateQuillContent(id, q.root.innerHTML);
                    });
                    document.getElementById('pageContent').value = JSON.stringify(this.blocks);
                    Swal.fire({ title: 'Saving...', text: 'Please wait', icon: 'info', showConfirmButton: false });
                    this.$nextTick(() => document.getElementById('saveForm').submit());
                },
            };
        }
    </script>

    <style>
        .ql-editor {
            min-height: 120px;
        }

        .quill-editor {
            border: 1px solid #e5e7eb;
        }
    </style>
@endsection
