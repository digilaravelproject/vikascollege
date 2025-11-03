@extends('layouts.admin.app')

@section('content')
    <div x-data="homepageBuilder()" x-init="initAll()" class="relative min-h-screen p-2 bg-gray-50 sm:p-4">

        <div class="flex flex-col flex-wrap justify-between gap-3 mb-4 sm:flex-row sm:items-center">
            <h1 class="text-xl font-bold text-gray-800">🏠 Homepage Setup</h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button @click="exportJSON" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Export
                    JSON</button>
                <button @click="importJSONPrompt" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Import
                    JSON</button>
                <button @click="undo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Undo</button>
                <button @click="redo" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Redo</button>
                <button @click="savePage"
                    class="flex items-center justify-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                    <span>💾</span><span>Save Homepage</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
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

            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building the homepage</p>
                </template>

                <div id="rootBlocks">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group"
                            :data-id="block.id">
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

                            <!-- Intro Block Editing -->
                            <template x-if="block.type === 'intro'">
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Heading</label>
                                        <input type="text" x-model="block.heading" @input="pushHistory"
                                            class="w-full p-2 border rounded" placeholder="Intro heading">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Text</label>
                                        <textarea x-model="block.text" @input="pushHistory"
                                            class="w-full p-2 border rounded" rows="3"
                                            placeholder="Intro description"></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-600">Image</label>
                                            <template x-if="block.image">
                                                <div class="space-y-2">
                                                    <img :src="block.image" class="object-cover w-full rounded-lg max-h-48">
                                                    <button @click="block.image=''; pushHistory()"
                                                        class="px-3 py-1 text-xs bg-red-100 rounded">Remove</button>
                                                </div>
                                            </template>
                                            <template x-if="!block.image">
                                                <label class="block mt-1 cursor-pointer">
                                                    <input type="file" accept="image/*"
                                                        @change="handleImageUpload($event, block)" class="hidden" />
                                                    <div
                                                        class="p-3 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50 text-sm text-gray-500">
                                                        📁 Upload image</div>
                                                </label>
                                            </template>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Layout</label>
                                            <select x-model="block.layout" @change="pushHistory"
                                                class="w-full p-2 border rounded bg-white">
                                                <option value="left">Image Left</option>
                                                <option value="right">Image Right</option>
                                                <option value="top">Image Top</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Text</label>
                                            <input type="text" x-model="block.buttonText" @input="pushHistory"
                                                class="w-full p-2 border rounded" placeholder="Optional">
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Link</label>
                                            <input type="url" x-model="block.buttonHref" @input="pushHistory"
                                                class="w-full p-2 border rounded" placeholder="https://...">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Section Links Editing -->
                            <template x-if="block.type === 'sectionLinks'">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-600">Section Name</label>
                                            <input type="text" x-model="block.title" @input="pushHistory"
                                                class="w-full p-2 border rounded"
                                                placeholder="e.g. Committees / Departments / Student Corner">
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Columns</label>
                                            <select x-model.number="block.columns" @change="pushHistory"
                                                class="w-full p-2 border rounded bg-white">
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div
                                        x-data="{ add(){ block.items = block.items||[]; block.items.push({title:'', href:'', isNew:false}); }, remove(i){ block.items.splice(i,1); pushHistory(); } }">
                                        <div class="flex items-center justify-between">
                                            <label class="text-sm font-medium text-gray-600">Items</label>
                                            <button type="button" @click="add()"
                                                class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Add
                                                Item</button>
                                        </div>
                                        <template x-if="!block.items || block.items.length===0">
                                            <div
                                                class="p-3 mt-2 text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded">
                                                No items yet</div>
                                        </template>
                                        <div class="mt-3 space-y-2">
                                            <template x-for="(it, i) in (block.items||[])" :key="i">
                                                <div
                                                    class="grid items-end grid-cols-1 gap-2 p-3 border border-gray-200 rounded-lg md:grid-cols-12 bg-white">
                                                    <div class="md:col-span-5">
                                                        <label class="block mb-1 text-xs font-medium text-gray-600">Item
                                                            Title</label>
                                                        <input type="text" x-model="it.title" @input="pushHistory"
                                                            class="w-full p-2 text-sm border rounded">
                                                    </div>
                                                    <div class="md:col-span-6">
                                                        <label class="block mb-1 text-xs font-medium text-gray-600">Item
                                                            Link</label>
                                                        <input type="url" x-model="it.href" @input="pushHistory"
                                                            class="w-full p-2 text-sm border rounded"
                                                            placeholder="https://...">
                                                    </div>
                                                    <div class="flex items-center gap-2 md:col-span-1">
                                                        <label class="text-xs text-gray-600"><input type="checkbox"
                                                                x-model="it.isNew" @change="pushHistory"
                                                                class="mr-1 align-middle"> New</label>
                                                        <button type="button" @click="remove(i)"
                                                            class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100">Remove</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Divider -->
                            <template x-if="block.type === 'divider'">
                                <hr class="my-4 border-gray-300 border-dashed">
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        <script type="application/json" id="hp-initial-content">{!! $layout !!}</script>
        <script type="application/json" id="hp-initial-notifications">{!! $notifications !!}</script>

        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        <script>
            function homepageBuilder() {
                return {
                    availableBlocks: [
                        { type: 'intro', label: '✨ Intro', heading: 'Welcome to our College', text: 'Write a short introduction about the institution, mission and highlights.', image: '', layout: 'left', buttonText: '', buttonHref: '' },
                        { type: 'sectionLinks', label: '📚 Section (links list)', title: 'Section Name', columns: 3, items: [] },
                        { type: 'divider', label: '⎯⎯ Divider' },
                    ],
                    blocks: [],
                    notifications: [],
                    historyStack: [],
                    redoStack: [],

                    initAll() {
                        const scriptEl = document.getElementById('hp-initial-content');
                        let initial = null;
                        if (scriptEl) {
                            try { initial = JSON.parse(scriptEl.textContent || ''); } catch (_) { initial = null; }
                        }
                        if (initial && initial.blocks && Array.isArray(initial.blocks)) {
                            this.blocks = initial.blocks.map(b => ({ ...b, id: this._genId() }));
                        } else { this.blocks = []; }
                        const nEl = document.getElementById('hp-initial-notifications');
                        if (nEl) { try { this.notifications = JSON.parse(nEl.textContent || '[]'); } catch (_) { this.notifications = []; } }
                        this.pushHistory();
                        this.$nextTick(() => this.initSortables());
                    },

                    _genId() { return 'hp_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 6); },

                    dragBlock(e, tpl) { e.dataTransfer.setData('blockTpl', JSON.stringify(tpl)); },
                    dropBlock(e) {
                        const data = e.dataTransfer.getData('blockTpl'); if (!data) return;
                        const tpl = JSON.parse(data); const b = JSON.parse(JSON.stringify(tpl)); b.id = this._genId();
                        if (b.type === 'sectionLinks' && !Array.isArray(b.items)) b.items = [];
                        this.blocks.push(b); this.pushHistory(); this.$nextTick(() => this.initSortables());
                    },
                    initSortables() {
                        const rootEl = document.getElementById('rootBlocks');
                        if (rootEl && !rootEl._sortable) {
                            rootEl._sortable = Sortable.create(rootEl, {
                                handle: '.group', animation: 150, draggable: '[data-id]', dataIdAttr: 'data-id',
                                onEnd: () => {
                                    const ids = Array.from(rootEl.querySelectorAll(':scope > div[data-id]')).map(el => el.getAttribute('data-id'));
                                    const map = {}; this.blocks.forEach(b => map[b.id] = b); this.blocks = ids.map(id => map[id]).filter(Boolean); this.pushHistory();
                                }
                            });
                        }
                    },
                    moveBlockUp(i) { if (i <= 0) return; const a = this.blocks;[a[i - 1], a[i]] = [a[i], a[i - 1]]; this.blocks = [...a]; this.pushHistory(); },
                    moveBlockDown(i) { if (i >= this.blocks.length - 1) return; const a = this.blocks;[a[i + 1], a[i]] = [a[i], a[i + 1]]; this.blocks = [...a]; this.pushHistory(); },
                    duplicateBlock(i) { const b = JSON.parse(JSON.stringify(this.blocks[i])); b.id = this._genId(); this.blocks.splice(i + 1, 0, b); this.pushHistory(); },
                    confirmRemove(_, i) { Swal.fire({ title: 'Delete?', icon: 'warning', showCancelButton: true }).then(r => { if (r.isConfirmed) { this.blocks.splice(i, 1); this.pushHistory(); } }); },

                    handleImageUpload(e, block) { const file = e.target.files[0]; if (!file) return; const reader = new FileReader(); reader.onload = () => { block.image = reader.result; this.pushHistory(); }; reader.readAsDataURL(file); e.target.value = null; },

                    pushHistory() { const snap = JSON.stringify(this.blocks); if (this.historyStack.at(-1) === snap) return; this.historyStack.push(snap); if (this.historyStack.length > 50) this.historyStack.shift(); this.redoStack = []; },
                    undo() { if (this.historyStack.length <= 1) return; const cur = this.historyStack.pop(); this.redoStack.push(cur); const prev = this.historyStack.at(-1); this.blocks = prev ? JSON.parse(prev) : []; },
                    redo() { if (!this.redoStack.length) return; const next = this.redoStack.pop(); this.historyStack.push(next); this.blocks = JSON.parse(next); },

                    savePage() {
                        const payload = { blocks: this.blocks };
                        const notif = this.notifications || [];
                        Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
                        fetch('{{ route('admin.homepage.save') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ content: JSON.stringify(payload), notifications: JSON.stringify(notif) }) })
                            .then(res => res.json()).then(data => { if (data.success) { Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }); } else { throw new Error(data.message || 'Save failed'); } })
                            .catch(e => { Swal.fire('Error', e.message || 'Save failed', 'error'); });
                    },

                    exportJSON() { const blob = new Blob([JSON.stringify({ blocks: this.blocks }, null, 2)], { type: 'application/json' }); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = 'homepage-layout.json'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url); },
                    importJSONPrompt() { Swal.fire({ title: 'Import JSON', html: `<input type="file" id="jsonFile" accept="application/json" class="swal2-file" style="width: 100%;">`, showCancelButton: true, preConfirm: () => new Promise((resolve) => { const f = document.getElementById('jsonFile').files[0]; if (!f) return resolve(null); const r = new FileReader(); r.onload = () => resolve(r.result); r.readAsText(f); }) }).then(res => { if (res.isConfirmed && res.value) { try { const parsed = JSON.parse(res.value); this.blocks = parsed.blocks ? parsed.blocks.map(b => ({ ...b, id: this._genId() })) : []; this.pushHistory(); Swal.fire('Imported', 'JSON imported successfully', 'success'); } catch (e) { Swal.fire('Error', 'Invalid JSON', 'error'); } } }); }
                }
            }
        </script>
        {{-- Notifications Manager --}}
        <div class="mt-6 overflow-hidden bg-white border border-gray-200 rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Notifications (Marquee)</h3>
                <p class="mt-1 text-sm text-gray-500">These appear above the slider on the homepage.</p>
            </div>
            <div class="p-6"
                x-data="{ n: notifications, add(){ n.push({title:'', href:'', isNew:true}); }, remove(i){ n.splice(i,1); } }">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">Notification Items</div>
                    <button type="button" @click="add()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Add
                        Notification</button>
                </div>
                <template x-if="n.length === 0">
                    <div class="p-3 mt-3 text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded">No
                        notifications yet</div>
                </template>
                <div class="mt-3 space-y-2">
                    <template x-for="(it, i) in n" :key="i">
                        <div
                            class="grid items-end grid-cols-1 gap-2 p-3 border border-gray-200 rounded-lg md:grid-cols-12 bg-white">
                            <div class="md:col-span-5">
                                <label class="block mb-1 text-xs font-medium text-gray-600">Title</label>
                                <input type="text" x-model="it.title" class="w-full p-2 text-sm border rounded">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block mb-1 text-xs font-medium text-gray-600">Link (optional)</label>
                                <input type="url" x-model="it.href" class="w-full p-2 text-sm border rounded"
                                    placeholder="https://...">
                            </div>
                            <div class="flex items-center gap-2 md:col-span-1">
                                <label class="text-xs text-gray-600"><input type="checkbox" x-model="it.isNew"
                                        class="mr-1 align-middle"> New</label>
                                <button type="button" @click="remove(i)"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100">Remove</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection
