@extends('layouts.admin.app')

@section('content')
    {{-- 1. Main Alpine Component and Initialization --}}
    <div x-data="homepageBuilder()" x-init="initAll()" class="relative min-h-screen p-2 bg-gray-50 sm:p-4">

        {{-- 1. HEADER --}}
        <div class="flex flex-col flex-wrap justify-between gap-3 mb-4 sm:flex-row sm:items-center">
            <h1 class="text-xl font-bold text-gray-800">🏠 Homepage Setup</h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button @click="exportJSON" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Export JSON</button>
                <button @click="importJSONPrompt" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Import JSON</button>
                <button @click="undo" :disabled="historyStack.length <= 1" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">Undo</button>
                <button @click="redo" :disabled="redoStack.length === 0" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">Redo</button>
                <button @click="savePage" class="flex items-center justify-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                    <span>💾</span><span>Save Homepage</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- 2. AVAILABLE BLOCKS (Static HTML container populated by JS) --}}
            <div class="self-start p-4 bg-white rounded-lg shadow lg:col-span-3 h-fit lg:sticky lg:top-4">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">Available Blocks</h2>
                <div id="available-blocks-list" class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-1">
                    {{-- Content will be inserted here dynamically on init --}}
                </div>
            </div>

            {{-- 3. BUILDER AREA --}}
            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]">

                <template x-if="blocks.length === 0">
                    <div class="flex items-center justify-center min-h-[40vh] p-10 border border-dashed rounded-lg"
                        :data-sortable-container="'blocks'"
                        @dragover.prevent>
                        <p class="text-center text-gray-400">🚀 Drag blocks here to start building the homepage</p>
                    </div>
                </template>

                {{-- Root Block Renderer (Level 1) --}}
                <div id="rootBlocks" class="space-y-4">
                    @include('admin.homepage._block_renderer', [
                        'blocks' => 'blocks',
                        'parentPath' => 'blocks' // 'blocks' string hai, bilkul sahi
                    ])
                </div>
            </div>
        </div>

        {{-- 4. NOTIFICATION CREATE MODAL --}}
        <div x-show="showNotificationModal" @keydown.escape.window="closeNotificationModal()" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            {{-- ... (Aapka modal content yahaan) ... --}}
            <div class="fixed inset-0 transition-opacity" @click="closeNotificationModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="relative w-full max-w-lg p-6 mx-auto my-8 bg-white rounded-lg shadow-xl">
                <h3 class="text-lg font-medium">New Notification</h3>
                <p>Modal content yahaan...</p>
                <button @click="closeNotificationModal()" class="mt-4 px-4 py-2 bg-gray-200 rounded">Close</button>
            </div>
        </div>

        {{-- 5. SCRIPT TAGS --}}
        <script type="application/json" id="hp-initial-content">{!! $layout !!}</script>
        <script type="application/json" id="hp-initial-notifications">{!! json_encode($notifications) !!}</script>
        <script type="application/json" id="hp-notification-icons">{!! json_encode($icons) !!}</script>

        {{-- 6. CDN scripts --}}
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


        {{-- =================================== --}}
        {{-- ✅ 7. ALPINE JS (FULL WORKING LOGIC) --}}
        {{-- =================================== --}}
        <script>
            function homepageBuilder() {
                return {
                    {{-- ⭐️ ENHANCED: availableBlocks array with new fields --}}
                    availableBlocks: [
                        {
                            type: 'intro',
                            label: '✨ Intro',
                            layout: 'left',
                            image: '',
                            heading: 'Block Heading',
                            content: 'Block content',
                            text: 'Block Text',
                            buttonText: 'Learn More',
                            buttonLink: '#'
                        },
                        {
                            type: 'sectionLinks',
                            label: '📚 Section Links',
                            title: 'Quick Links',
                            links: [
                                { text: 'Sample Link 1', url: '#' },
                                { text: 'Sample Link 2', url: '#' }
                            ]
                        },
                        // { type: 'latestUpdates', label: '📣 Latest Updates', title: 'Latest Updates', count: 5 },
                        { type: 'divider', label: '⎯⎯ Divider' },
                        { type: 'announcements', label: '📢 Announcements', items: [] },
                        { type: 'events', label: "🎫 What's Happening (Events)", items: [] },
                        { type: 'academic_calendar', label: '📅 Academic Calendar', items: [] },
                        // { type: 'image_text', label: '🖼️ Image + Text', image: '', text: '' },
                        { type: 'gallery', label: '🖼️ Gallery', images: [] },
                        { type: 'testimonials', label: '⭐ Testimonials', items: [] },
                        { type: 'why_choose_us', label: '🎯 Why Choose Us', items: [] },
                        {
                            type: 'layout_grid',
                            label: 'Layout (Grid)',
                            title: 'Layout',
                            layout: '6-6',
                            columns: [
                                { span: 6, blocks: [] },
                                { span: 6, blocks: [] }
                            ]
                        }
                    ],
                    blocks: [],

                    // Notifications/History
                    allNotifications: [],
                    notificationIcons: [],
                    showNotificationModal: false,
                    newNotification: null,
                    historyStack: [],
                    redoStack: [],
                    _historyTimer: null,
                    _debounceTimer: null,

                    initAll() {
                        const scriptEl = document.getElementById('hp-initial-content');
                        let initial = null;
                        if (scriptEl) {
                            try { initial = JSON.parse(scriptEl.textContent || 'null'); } catch(e){ initial = null; }
                        }
                        if (initial && initial.blocks && Array.isArray(initial.blocks)) {
                            this.blocks = this._processLoadedBlocks(initial.blocks);
                        } else {
                            this.blocks = [];
                        }

                        const notifScriptEl = document.getElementById('hp-initial-notifications');
                        if (notifScriptEl) {
                            try { this.allNotifications = JSON.parse(notifScriptEl.textContent || '[]'); } catch(e){ this.allNotifications = []; }
                        }
                        const iconScriptEl = document.getElementById('hp-notification-icons');
                        if (iconScriptEl) {
                            try { this.notificationIcons = JSON.parse(iconScriptEl.textContent || '[]'); } catch(e){ this.notificationIcons = []; }
                        }

                        this.newNotification = this._getDefaultNotification();
                        this.pushHistory(true); // Initial state

                        this.initAvailableBlocksList();
                        this.$nextTick(() => this.initSortables());
                    },

                    _processLoadedBlocks(blocks) {
                        if (!blocks || !Array.isArray(blocks)) return [];
                        return blocks.map(b => {
                            if (!b || !b.type) return null; // Invalid block
                            const defaults = this._getBlockDefaults(b.type);
                            if (!defaults) {
                                console.warn('Unknown block type found during load:', b.type);
                                return null; // Unknown block type
                            }

                            // Deep merge (Object.assign shallow merge karta hai)
                            const merged = JSON.parse(JSON.stringify(defaults));
                            Object.assign(merged, b); // Saved properties ko overwrite karein

                            merged.id = merged.id || this._genId();

                            if (merged.type === 'layout_grid' && Array.isArray(merged.columns)) {
                                merged.columns = merged.columns.map(col => {
                                    return {
                                        span: col.span || 12,
                                        blocks: this._processLoadedBlocks(col.blocks || [])
                                    };
                                });
                            }
                            // ❗️ FIX for sectionLinks: Ensure 'links' is always an array
                            if (merged.type === 'sectionLinks' && !Array.isArray(merged.links)) {
                                merged.links = defaults.links || [];
                            }
                            return merged;
                        }).filter(Boolean); // Filter out null/invalid blocks
                    },

                    initAvailableBlocksList() {
                        const container = document.getElementById('available-blocks-list');
                        if (!container) return;
                        container.innerHTML = '';
                        this.availableBlocks.forEach(tpl => {
                            const div = document.createElement('div');
                            div.className = "p-3 text-gray-700 transition border border-gray-200 rounded cursor-grab hover:bg-blue-50";
                            div.setAttribute('data-block-type', tpl.type);
                            div.setAttribute('draggable', 'true');

                            div.addEventListener('dragstart', (e) => {
                                try {
                                    e.dataTransfer.setData('block-type', tpl.type);
                                    e.dataTransfer.effectAllowed = 'copy';
                                } catch (err) { console.error('Error setting dataTransfer', err); }
                            });

                            const span = document.createElement('span');
                            span.textContent = tpl.label;
                            div.appendChild(span);
                            container.appendChild(div);
                        });

                        // Initialize Sortable on the sidebar (clone group)
                        const sidebar = document.getElementById('available-blocks-list');
                        if (sidebar && !sidebar._sortable) {
                            sidebar._sortable = Sortable.create(sidebar, {
                                group: { name: 'shared-blocks', pull: 'clone', put: false },
                                sort: false,
                                animation: 150,
                            });
                        }
                    },

                    // ----------------- SORTABLE LOGIC -----------------

                    initSortables() {
                        this.destroySortables(); // Pehle purane instances ko destroy karein

                        const containers = this.$el.querySelectorAll('[data-sortable-container]');

                        containers.forEach(container => {
                            if (container._sortable) return; // Already initialized

                            const sortableInstance = Sortable.create(container, {
                                group: { name: 'shared-blocks', pull: true, put: true },
                                handle: '.cursor-grab',
                                draggable: '[data-id]', // Sirf data-id waale items ko drag karein
                                animation: 150,

                                onStart(evt) {
                                    if (evt.item.dataset.id) {
                                        evt.item.classList.add('opacity-50', 'border-blue-500');
                                    }
                                },

                                onAdd: (evt) => {
                                    const blockType = evt.item.dataset.blockType;
                                    const newIndex = evt.newIndex;
                                    const currentPath = evt.to.dataset.sortableContainer;

                                    if (blockType) { // Case: Added from Sidebar (Clone)
                                        const tpl = this.availableBlocks.find(b => b.type === blockType);
                                        if (!tpl) { evt.item.remove(); return; }

                                        const newBlock = JSON.parse(JSON.stringify(tpl));
                                        newBlock.id = this._genId();

                                        if (newBlock.type === 'layout_grid' && Array.isArray(newBlock.columns)) {
                                            newBlock.columns = newBlock.columns.map(c => ({ span: c.span || 12, blocks: [] }));
                                        }

                                        const targetArr = this.getDeep(currentPath);
                                        if (Array.isArray(targetArr)) {
                                            targetArr.splice(newIndex, 0, newBlock);
                                            this.pushHistory();
                                        }

                                        evt.item.remove(); // DOM clone ko remove karein
                                    }
                                },

                                onEnd: (evt) => {
                                    evt.item.classList.remove('opacity-50', 'border-blue-500');

                                    const blockId = evt.item.dataset.id;
                                    if (!blockId) return; // Sidebar clone, onAdd ne handle kiya

                                    const fromPath = evt.from.dataset.sortableContainer;
                                    const toPath = evt.to.dataset.sortableContainer;
                                    const oldIndex = evt.oldIndex;
                                    const newIndex = evt.newIndex;

                                    if (fromPath === toPath && oldIndex === newIndex) return;

                                    const [foundBlock, oldParentArray] = this._findAndRemoveBlock(blockId, this.blocks);
                                    if (!foundBlock) {
                                        console.error('Moved block not found in data', blockId);
                                        return;
                                    }

                                    const newParentArray = this.getDeep(toPath);
                                    if (Array.isArray(newParentArray)) {
                                        newParentArray.splice(newIndex, 0, foundBlock);
                                        this.pushHistory(); // Triggers re-render and re-initSortables
                                    } else {
                                        console.error('Target parent array not found during onEnd', toPath);
                                        if (oldParentArray && Array.isArray(oldParentArray)) {
                                            oldParentArray.splice(oldIndex, 0, foundBlock);
                                        }
                                    }
                                }
                            });
                            container._sortable = sortableInstance;
                        });
                    },

                    destroySortables() {
                        const containers = this.$el.querySelectorAll('[data-sortable-container]');
                        containers.forEach(container => {
                            if (container._sortable && typeof container._sortable.destroy === 'function') {
                                container._sortable.destroy();
                                delete container._sortable;
                            }
                        });
                    },

                    // ----------------- ❗️ NEW/MISSING FUNCTIONS ADDED HERE -----------------

                    moveBlockUp(path, index) {
                        const [parentArray] = this.getDeepParent(path, index);
                        if (!parentArray || index <= 0) return;
                        // Simple array swap
                        [parentArray[index - 1], parentArray[index]] = [parentArray[index], parentArray[index - 1]];
                        this.pushHistory();
                    },

                    moveBlockDown(path, index) {
                        const [parentArray] = this.getDeepParent(path, index);
                        if (!parentArray || index >= parentArray.length - 1) return;
                        // Simple array swap
                        [parentArray[index + 1], parentArray[index]] = [parentArray[index], parentArray[index + 1]];
                        this.pushHistory();
                    },

                    duplicateBlock(path, index) {
                        const [parentArray] = this.getDeepParent(path, index);
                        if (!parentArray) return;
                        const original = parentArray[index];
                        const clone = JSON.parse(JSON.stringify(original));

                        // Sabhi nested IDs ko regenerate karein
                        const regenerateIds = (b) => {
                            b.id = this._genId();
                            if (b.type === 'layout_grid' && Array.isArray(b.columns)) {
                                b.columns.forEach(col => {
                                    col.blocks = (col.blocks || []).map(child => {
                                        regenerateIds(child);
                                        return child;
                                    });
                                });
                            }
                        };
                        regenerateIds(clone);

                        parentArray.splice(index + 1, 0, clone);
                        this.pushHistory();
                    },

                    confirmRemove(path, index) {
                        const that = this;
                        Swal.fire({
                            title: 'Delete Block?',
                            text: "This will remove the block and all nested content. Are you sure?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const [parentArray] = that.getDeepParent(path, index);
                                if (parentArray) {
                                    parentArray.splice(index, 1);
                                    that.pushHistory();
                                }
                            }
                        });
                    },


                    // ----------------- UTILITY & HISTORY -----------------

                    _getBlockDefaults(type) {
                        const tpl = this.availableBlocks.find(b => b.type === type);
                        return tpl ? JSON.parse(JSON.stringify(tpl)) : null; // Return null if unknown
                    },
                    _genId() {
                        return 'hp_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2,6);
                    },
                    getDeep(path) {
                        if (!path) return undefined;
                        // 'blocks[0].columns[1].blocks' ko 'blocks.0.columns.1.blocks' mein convert karein
                        const normalized = path.replace(/\[(\d+)\]/g, '.$1').replace(/^\.+/, '');
                        const parts = normalized.split('.');
                        let obj = this;
                        for (let p of parts) {
                            if (obj === null || typeof obj === 'undefined') return undefined;
                            if (p === '') continue;
                            if (/^\d+$/.test(p)) {
                                obj = obj[Number(p)];
                            } else {
                                obj = obj[p];
                            }
                        }
                        return obj;
                    },
                    getDeepParent(path, index) {
                        try {
                            const normalized = path.replace(/\[(\d+)\]/g, '.$1').replace(/^\.+/, '');
                            const parts = normalized.split('.');
                            let obj = this;
                            for (let i = 0; i < parts.length; i++) {
                                const key = parts[i];
                                if (key === '') continue;
                                if (/^\d+$/.test(key)) obj = obj[Number(key)];
                                else obj = obj[key];
                            }
                            if (Array.isArray(obj) && typeof obj[index] !== 'undefined') {
                                return [obj, index];
                            }
                        } catch (e) {
                            console.error('getDeepParent error', e);
                        }
                        return [null, -1];
                    },
                    _findAndRemoveBlock(id, blocksArray) {
                        for (let i = 0; i < blocksArray.length; i++) {
                            const b = blocksArray[i];
                            if (b.id === id) {
                                blocksArray.splice(i, 1);
                                return [b, blocksArray];
                            }
                            if (b.type === 'layout_grid' && Array.isArray(b.columns)) {
                                for (let col of b.columns) {
                                    if (!Array.isArray(col.blocks)) continue;
                                    const found = this._findAndRemoveBlock(id, col.blocks);
                                    if (found && found[0]) return found;
                                }
                            }
                        }
                        return [null, null];
                    },
                    changeGridLayout(block) {
                        if (!block || !block.layout) return;
                        const spans = ('' + block.layout).split('-').map(s => parseInt(s, 10) || 12);
                        const oldColumns = Array.isArray(block.columns) ? block.columns : [];
                        const newColumns = [];

                        for (let i = 0; i < spans.length; i++) {
                            if (oldColumns[i]) {
                                newColumns.push({ span: spans[i], blocks: oldColumns[i].blocks });
                            } else {
                                newColumns.push({ span: spans[i], blocks: [] });
                            }
                        }
                        if (oldColumns.length > newColumns.length) {
                            for (let i = newColumns.length; i < oldColumns.length; i++) {
                                if (Array.isArray(oldColumns[i].blocks) && oldColumns[i].blocks.length) {
                                    newColumns[newColumns.length - 1].blocks.push(...oldColumns[i].blocks);
                                }
                            }
                        }
                        block.columns = newColumns;
                        this.pushHistoryDebounced();
                    },

                    _snapshot() {
                        return JSON.parse(JSON.stringify({ blocks: this.blocks }));
                    },
                    pushHistory(isInitial = false) {
                        const snap = this._snapshot();
                        this.historyStack.push(snap);
                        if (this.historyStack.length > 100) this.historyStack.shift();
                        if (!isInitial) {
                            this.redoStack = [];
                        }
                        // Crucial: Re-init sortables after data change
                        this.$nextTick(() => this.initSortables());
                    },
                    pushHistoryDebounced() {
                        if (this._debounceTimer) clearTimeout(this._debounceTimer);
                        this._debounceTimer = setTimeout(() => {
                            this.pushHistory();
                        }, 350);
                    },
                    undo() {
                        if (this.historyStack.length <= 1) return;
                        const current = this.historyStack.pop();
                        this.redoStack.push(current);
                        const prev = this.historyStack[this.historyStack.length - 1];
                        if (prev) {
                            this.blocks = this._processLoadedBlocks(prev.blocks || []);
                            this.$nextTick(() => this.initSortables());
                        }
                    },
                    redo() {
                        if (this.redoStack.length === 0) return;
                        const snap = this.redoStack.pop();
                        if (snap) {
                            this.historyStack.push(snap);
                            this.blocks = this._processLoadedBlocks(snap.blocks || []);
                            this.$nextTick(() => this.initSortables());
                        }
                    },

                    // ❗️ NOTE: `dropBlock` function ko hata diya gaya hai.
                    // Aapka `initSortables` mein `onAdd` event ab 100% drops ko handle karta hai.

                    // --- Other Methods (Unchanged) ---
                    _getDefaultNotification() { return { id: null, title: '', message: '', icon: this.notificationIcons?.[0] || null, send_at: null }; },
                    openNewNotificationModal() { this.newNotification = this._getDefaultNotification(); this.showNotificationModal = true; },
                    closeNotificationModal() { this.showNotificationModal = false; },
                    refreshNotifications() { console.log('refreshNotifications: not implemented'); },
                    saveNewNotification() { const payload = Object.assign({}, this.newNotification); payload.id = payload.id || this._genId(); this.allNotifications.unshift(payload); this.closeNotificationModal(); },

                    savePage() {
                        // Agar aap route name istemaal nahi kar rahe hain, toh URL hardcode karein
                        const url = '/admin/homepage-setup/save';
                        // Agar route name use kar rahe hain:
                        // const url = '{{ route("admin.homepage.save") }}';

                        const payload = { blocks: this.blocks };

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify(payload)
                        }).then(r => {
                            if (r.ok) {
                                Swal.fire({ title: 'Saved!', icon: 'success', timer: 1200, showConfirmButton: false });
                                return r.json();
                            } else {
                                // Server se error message lene ki koshish karein
                                return r.json().then(err => {
                                    throw new Error(err.message || 'Server error. Status: ' + r.status);
                                }).catch(() => {
                                    // Agar JSON parse nahi hota hai
                                    throw new Error('Server error. Status: ' + r.status);
                                });
                            }
                        }).then(data => {
                            console.log(data.message); // "Layout saved successfully."
                        }).catch(err => {
                            console.error('Save error', err);
                            Swal.fire({ title: 'Save error', text: err.message || 'Check console for details', icon: 'error' });
                        });
                    },

                    exportJSON() {
                        const data = JSON.stringify({ blocks: this.blocks }, null, 2);
                        const blob = new Blob([data], { type: 'application/json' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'homepage-layout.json';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                    },

                    importJSONPrompt() {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'application/json';
                        input.onchange = (e) => {
                            const file = e.target.files[0];
                            if (!file) return;
                            const reader = new FileReader();
                            reader.onload = (ev) => {
                                try {
                                    const json = JSON.parse(ev.target.result);
                                    if (json && Array.isArray(json.blocks)) {
                                        this.blocks = this._processLoadedBlocks(json.blocks);
                                        this.pushHistory();
                                        Swal.fire({ title: 'Imported', icon: 'success', timer: 1000, showConfirmButton: false });
                                    } else {
                                        Swal.fire({ title: 'Invalid JSON', text: 'Missing blocks array', icon: 'error' });
                                    }
                                } catch (ex) {
                                    Swal.fire({ title: 'Invalid JSON', text: ex.message, icon: 'error' });
                                }
                            };
                            reader.readAsText(file);
                        };
                        input.click();
                    },

                } // return end
            } // function end
        </script>
    </div>
@endsection
