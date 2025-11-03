@extends('layouts.admin.app')

@section('content')
    {{-- Use x-data and x-init on the main container --}}
    <div x-data="homepageBuilder()" x-init="initAll()" class="relative min-h-screen p-2 bg-gray-50 sm:p-4">

        {{-- 1. HEADER (Unchanged) --}}
        <div class="flex flex-col flex-wrap justify-between gap-3 mb-4 sm:flex-row sm:items-center">
            <h1 class="text-xl font-bold text-gray-800">🏠 Homepage Setup</h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                {{-- ... (header buttons unchanged) ... --}}
                <button @click="exportJSON" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Export
                    JSON</button>
                <button @click="importJSONPrompt" class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Import
                    JSON</button>
                <button @click="undo" :disabled="historyStack.length <= 1"
                    class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">Undo</button>
                <button @click="redo" :disabled="redoStack.length === 0"
                    class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">Redo</button>
                <button @click="savePage"
                    class="flex items-center justify-center px-4 py-2 space-x-2 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                    <span>💾</span><span>Save Homepage</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- 2. AVAILABLE BLOCKS (Unchanged) --}}
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

            {{-- 3. BUILDER AREA (Updated) --}}
            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building the homepage</p>
                </template>

                <div id="rootBlocks">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group"
                            :data-id="block.id">
                            {{-- Block Controls (Unchanged) --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-700"
                                        x-text="availableBlocks.find(b => b.type === block.type)?.label || block.type"></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                                    <button @click="moveBlockUp(index)" :disabled="index === 0"
                                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↑</button>
                                    <button @click="moveBlockDown(index)" :disabled="index === blocks.length - 1"
                                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↓</button>
                                    <button @click="duplicateBlock(index)"
                                        class="px-2 py-1 text-sm bg-white border rounded">⧉</button>
                                    <button @click="confirmRemove(block.id, index)"
                                        class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                                </div>
                            </div>

                            {{-- 'intro' block (Editable UI) --}}
                            <template x-if="block.type === 'intro'">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Layout</label>
                                            <select x-model="block.layout" @change="pushHistoryDebounced"
                                                class="w-full p-2 border rounded">
                                                <option value="left">Image Left</option>
                                                <option value="right">Image Right</option>
                                                <option value="top">Image Top</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Image URL</label>
                                            <input type="text" x-model="block.image" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded" placeholder="https://...">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Heading</label>
                                            <input type="text" x-model="block.heading" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded" placeholder="Section heading">
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Text</label>
                                            <input type="text" x-model="block.buttonText" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded" placeholder="Learn more">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Link</label>
                                            <input type="text" x-model="block.buttonHref" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded" placeholder="https://...">
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Text</label>
                                            <textarea x-model="block.text" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded h-24" placeholder="Description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- 'sectionLinks' block (Editable UI) --}}
                            <template x-if="block.type === 'sectionLinks'">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Title</label>
                                            <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                                                class="w-full p-2 border rounded" placeholder="Useful Links">
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Columns</label>
                                            <select x-model.number="block.columns" @change="pushHistoryDebounced"
                                                class="w-full p-2 border rounded">
                                                <option :value="1">1</option>
                                                <option :value="2">2</option>
                                                <option :value="3">3</option>
                                                <option :value="4">4</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        <div class="flex items-center justify-between">
                                            <label class="text-sm font-medium text-gray-600">Links</label>
                                            <button type="button"
                                                @click="(block.items = block.items || []).push({ title: '', href: '#', isNew: false }); pushHistoryDebounced();"
                                                class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">+
                                                Add Link</button>
                                        </div>

                                        <template x-if="!block.items || block.items.length === 0">
                                            <div
                                                class="p-3 mt-2 text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded">
                                                No links added yet.</div>
                                        </template>

                                        <div class="mt-3 space-y-2">
                                            <template x-for="(it, iidx) in (block.items || [])" :key="iidx">
                                                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                                                        <div class="md:col-span-4">
                                                            <label class="text-xs font-medium text-gray-600">Title</label>
                                                            <input type="text" x-model="it.title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded" placeholder="Link title">
                                                        </div>
                                                        <div class="md:col-span-6">
                                                            <label class="text-xs font-medium text-gray-600">Href</label>
                                                            <input type="text" x-model="it.href"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded" placeholder="https://...">
                                                        </div>
                                                        <div class="flex items-center gap-3 md:col-span-2">
                                                            <label class="text-xs font-medium text-gray-600">NEW</label>
                                                            <input type="checkbox" x-model="it.isNew"
                                                                @change="pushHistoryDebounced" class="w-4 h-4">
                                                            <button type="button"
                                                                @click="block.items.splice(iidx,1); pushHistoryDebounced();"
                                                                class="px-2 py-1 text-xs text-red-600 bg-white border rounded">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- =================================== --}}
                            {{-- 4. LATEST UPDATES BLOCK (REBUILT) --}}
                            {{-- =================================== --}}
                            <template x-if="block.type === 'latestUpdates'">
                                <div class="space-y-3">
                                    {{-- 1. The "Section Name" input --}}
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Section Name</label>
                                        <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                                            class="w-full p-2 border rounded"
                                            placeholder="e.g. Latest Updates / Notifications">
                                    </div>

                                    {{-- 2. The "Add Update" button --}}
                                    <div class="flex items-center justify-between pt-2">
                                        <label class="text-sm font-medium text-gray-600">Displayed Updates</label>
                                        <button type="button" @click="openNewNotificationModal()"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                            + Add New Update
                                        </button>
                                    </div>

                                    {{-- 3. The list of notifications --}}
                                    <template x-if="allNotifications.length === 0">
                                        <div
                                            class="p-3 mt-2 text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded">
                                            No active, featured notifications found. Add one!
                                        </div>
                                    </template>
                                    <div class="mt-3 space-y-2 max-h-96 overflow-y-auto p-1">
                                        <template x-for="notif in allNotifications" :key="notif.id">
                                            <div
                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-lg" x-text="notif.icon"></span>
                                                    <div>
                                                        <p class="font-medium text-gray-800" x-text="notif.title"></p>
                                                        <p class="text-xs text-gray-500"
                                                            x-text="notif.display_date ? new Date(notif.display_date).toLocaleDateString('en-CA') : ''">
                                                        </p>
                                                    </div>
                                                </div>
                                                {{-- Link to the standard edit page --}}
                                                <a :href="`{{ route('admin.notifications.index') }}/${notif.id}/edit`"
                                                    target="_blank"
                                                    class="px-3 py-1 text-xs font-medium bg-gray-100 rounded hover:bg-gray-200">
                                                    Edit
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        This list shows active, featured notifications.
                                        <a href="{{ route('admin.notifications.index') }}" target="_blank"
                                            class="text-blue-600 underline">
                                            Manage all notifications here.
                                        </a>
                                    </p>
                                </div>
                            </template>

                            {{-- 'divider' block (Unchanged) --}}
                            <template x-if="block.type === 'divider'">
                                <hr class="my-4 border-gray-300 border-dashed">
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- =================================== --}}
        {{-- 5. NOTIFICATION CREATE MODAL (NEW) --}}
        {{-- =================================== --}}
        <div x-show="showNotificationModal" @keydown.escape.window="closeNotificationModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" style="display: none;"
            x-trap.inert="showNotificationModal">
            <div @click.away="closeNotificationModal()"
                class="w-full max-w-3xl p-6 bg-white rounded-lg shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b">
                    <h2 class="text-lg font-bold text-gray-900">Add New Update</h2>
                    <button @click="closeNotificationModal()" class="text-gray-500 hover:text-gray-700">✖</button>
                </div>
                <div class="mt-6">
                    {{-- This is your form partial, adapted for Alpine x-model --}}
                    <div class="space-y-6">

                        {{-- Section 1: Content --}}
                        <fieldset>
                            <legend class="text-base font-semibold text-gray-900">Content</legend>
                            <div class="mt-4 space-y-4">
                                {{-- Icon Picker --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Icon</label>
                                    <div class="flex flex-wrap gap-2 p-2 mt-1 border border-gray-200 rounded-md">
                                        <template x-for="ic in notificationIcons" :key="ic">
                                            <label class="px-3 py-1 transition-all border rounded-md cursor-pointer" :class="newNotification.icon === ic ?
                                                                    'border-blue-500 bg-blue-50 ring-1 ring-blue-500' :
                                                                    'border-gray-300 bg-white hover:bg-gray-50'">
                                                <input type="radio" name="modal_icon" :value="ic"
                                                    x-model="newNotification.icon" class="hidden">
                                                <span x-text="ic"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <input type="text" x-model="newNotification.icon"
                                        placeholder="Or paste custom emoji/icon"
                                        class="block w-full p-2 mt-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="modal_title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" id="modal_title" x-model="newNotification.title"
                                        class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section 2: Action Button --}}
                        <fieldset>
                            <legend class="text-base font-semibold text-gray-900">Action</legend>
                            <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
                                <div>
                                    <label for="modal_href" class="block text-sm font-medium text-gray-700">Link
                                        (href)</label>
                                    <input type="text" id="modal_href" x-model="newNotification.href"
                                        class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="https://...">
                                </div>
                                <div>
                                    <label for="modal_button_name" class="block text-sm font-medium text-gray-700">Button
                                        Name</label>
                                    <input type="text" id="modal_button_name" x-model="newNotification.button_name"
                                        class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Click Here">
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section 3: Settings --}}
                        <fieldset>
                            <legend class="text-base font-semibold text-gray-900">Settings</legend>
                            <p class="text-xs text-gray-500">For the homepage, 'Featured' is enabled and 'Feature on Top'
                                is disabled.</p>
                            <div class="grid grid-cols-1 gap-y-4 gap-x-4 mt-4 sm:grid-cols-3">
                                {{-- Toggle for Status --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <button @click="newNotification.status = !newNotification.status" type="button"
                                        class="relative inline-flex items-center h-6 mt-1 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        :class="newNotification.status ? 'bg-blue-600' : 'bg-gray-200'" role="switch"
                                        :aria-checked="newNotification.status">
                                        <span
                                            class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                            :class="{ 'translate-x-6': newNotification.status, 'translate-x-1': !newNotification.status }"></span>
                                    </button>
                                    <span x-text="newNotification.status ? 'Active' : 'Inactive'"
                                        class="ml-2 text-sm text-gray-600"></span>
                                </div>
                                {{-- Toggle for Featured (Disabled ON) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Featured</label>
                                    <button type="button"
                                        class="relative inline-flex items-center h-6 mt-1 rounded-full w-11 bg-blue-600 opacity-50 cursor-not-allowed"
                                        role="switch" aria-checked="true" disabled>
                                        <span
                                            class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full translate-x-6"></span>
                                    </button>
                                    <span x-text="newNotification.featured ? 'Yes' : 'No'"
                                        class="ml-2 text-sm text-gray-600"></span>
                                </div>
                                {{-- Toggle for Feature on Top (Disabled OFF) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Feature on Top</label>
                                    <button type="button"
                                        class="relative inline-flex items-center h-6 mt-1 rounded-full w-11 bg-gray-200 opacity-50 cursor-not-allowed"
                                        role="switch" aria-checked="false" disabled>
                                        <span
                                            class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full translate-x-1"></span>
                                    </button>
                                    <span x-text="newNotification.feature_on_top ? 'On' : 'Off'"
                                        class="ml-2 text-sm text-gray-600"></span>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section 4: Date --}}
                        <div>
                            <label for="modal_display_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                {{-- We use x-ref to re-init flatpickr when modal opens --}}
                                <input x-ref="modalDatepicker" type="text" id="modal_display_date"
                                    x-model="newNotification.display_date"
                                    class="block w-full p-2 pl-10 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Select a date">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal Footer --}}
                <div class="flex justify-end gap-3 pt-4 mt-6 border-t">
                    <button @click="closeNotificationModal()" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="saveNewNotification()" type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                        Create Notification
                    </button>
                </div>
            </div>
        </div>

        {{-- 6. SCRIPT TAGS (Updated) --}}
        <script type="application/json" id="hp-initial-content">{!! $layout !!}</script>
        {{-- NEW SCRIPT TAGS to pass data from controller --}}
        <script type="application/json" id="hp-initial-notifications">{!! json_encode($notifications) !!}</script>
        <script type="application/json" id="hp-notification-icons">{!! json_encode($icons) !!}</script>

        {{-- CDN scripts (Unchanged) --}}
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        {{-- We need flatpickr for the modal date picker --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


        {{-- =================================== --}}
        {{-- 7. ALPINE JS (HEAVILY UPDATED) --}}
        {{-- =================================== --}}
        <script>
            function homepageBuilder() {
                return {
                    availableBlocks: [{
                        type: 'intro',
                        label: '✨ Intro',
                        layout: 'left',
                        image: '',
                        heading: '',
                        text: '',
                        buttonText: '',
                        buttonHref: ''
                    }, {
                        type: 'sectionLinks',
                        label: '📚 Section (links list)',
                        title: 'Useful Links',
                        columns: 3,
                        items: []
                    }, {
                        type: 'latestUpdates',
                        label: '📣 Latest Updates',
                        title: 'Latest Updates',
                        // items array is no longer needed here
                    }, {
                        type: 'divider',
                        label: '⎯⎯ Divider'
                    },],
                    blocks: [],

                    // === NEW STATE FOR NOTIFICATIONS ===
                    allNotifications: [],
                    notificationIcons: [],
                    showNotificationModal: false,
                    newNotification: null, // Will be set to default object
                    // ===================================

                    historyStack: [],
                    redoStack: [],
                    _historyTimer: null,

                    initAll() {
                        // Load layout
                        const scriptEl = document.getElementById('hp-initial-content');
                        let initial = null;
                        if (scriptEl) {
                            try {
                                initial = JSON.parse(scriptEl.textContent || '');
                            } catch (_) {
                                initial = null;
                            }
                        }
                        if (initial && initial.blocks && Array.isArray(initial.blocks)) {
                            this.blocks = initial.blocks.map(b => ({
                                ...this._getBlockDefaults(b.type),
                                ...b,
                                id: this._genId()
                            }));
                        } else {
                            this.blocks = [];
                        }

                        // === NEW: Load notifications and icons ===
                        const notifScriptEl = document.getElementById('hp-initial-notifications');
                        if (notifScriptEl) {
                            try {
                                this.allNotifications = JSON.parse(notifScriptEl.textContent || '[]');
                            } catch (_) { }
                        }
                        const iconScriptEl = document.getElementById('hp-notification-icons');
                        if (iconScriptEl) {
                            try {
                                this.notificationIcons = JSON.parse(iconScriptEl.textContent || '[]');
                            } catch (_) { }
                        }
                        this.newNotification = this._getDefaultNotification();
                        // =======================================

                        this.pushHistory(); // Push initial state
                        this.$nextTick(() => this.initSortables());
                    },

                    // === NEW HELPER FUNCTIONS ===
                    _getDefaultNotification() {
                        return {
                            icon: this.notificationIcons.length > 0 ? this.notificationIcons[0] : '🔔',
                            title: '',
                            href: '',
                            button_name: 'Click Here',
                            status: true,
                            featured: true, // Default to true for this section
                            feature_on_top: false, // Default to false
                            display_date: new Date().toISOString().split('T')[0] // Today's date YYYY-MM-DD
                        };
                    },

                    openNewNotificationModal() {
                        this.newNotification = this._getDefaultNotification();
                        this.showNotificationModal = true;
                        // We need to init flatpickr on the modal *after* it's shown
                        this.$nextTick(() => {
                            if (this.$refs.modalDatepicker && !this.$refs.modalDatepicker._flatpickr) {
                                flatpickr(this.$refs.modalDatepicker, {
                                    dateFormat: 'Y-m-d',
                                    defaultDate: this.newNotification.display_date
                                });
                            }
                        });
                    },

                    closeNotificationModal() {
                        this.showNotificationModal = false;
                    },

                    refreshNotifications() {
                        // Fetch the updated list from a new route we will create
                        fetch('{{ route('admin.notifications.list-active-featured') }}')
                            .then(res => res.json())
                            .then(data => {
                                this.allNotifications = data;
                            })
                            .catch(e => console.error('Failed to refresh notifications', e));
                    },

                    saveNewNotification() {
                        // Prepare payload, convert booleans to 1/0
                        const payload = {
                            ...this.newNotification,
                            status: this.newNotification.status ? 1 : 0,
                            featured: this.newNotification.featured ? 1 : 0,
                            feature_on_top: this.newNotification.feature_on_top ? 1 : 0,
                        };

                        Swal.fire({
                            title: 'Creating...',
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false
                        });

                        fetch('{{ route('admin.notifications.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                            .then(res => {
                                if (!res.ok) {
                                    // Handle non-200 responses
                                    return res.json().then(err => Promise.reject(err));
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (data.success) { // Your controller returns {success: true, ...}
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Created!',
                                        timer: 1200,
                                        showConfirmButton: false
                                    });
                                    this.closeNotificationModal();
                                    this.refreshNotifications(); // Re-fetch the list
                                } else {
                                    // This else might not be needed if you always throw on error
                                    throw new Error(data.message || 'Save failed');
                                }
                            })
                            .catch(err => {
                                // Handle validation errors or network errors
                                let errorMsg = err.message || 'Failed to create notification.';
                                if (err.errors) {
                                    errorMsg = Object.values(err.errors).join('<br>');
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            });
                    },
                    // ============================

                    _getBlockDefaults(type) {
                        const tpl = this.availableBlocks.find(b => b.type === type);
                        // Deep copy the template
                        return tpl ? JSON.parse(JSON.stringify(tpl)) : {};
                    },

                    _genId() {
                        return 'hp_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 6);
                    },

                    dragBlock(e, tpl) {
                        e.dataTransfer.setData('blockTpl', JSON.stringify(tpl));
                    },
                    dropBlock(e) {
                        const data = e.dataTransfer.getData('blockTpl');
                        if (!data) return;
                        const tpl = JSON.parse(data);
                        const b = this._getBlockDefaults(tpl.type);
                        b.id = this._genId();

                        if (b.hasOwnProperty('items') && !Array.isArray(b.items)) {
                            b.items = [];
                        }

                        this.blocks.push(b);
                        this.pushHistory();
                        this.$nextTick(() => this.initSortables());
                    },

                    initSortables() {
                        const rootEl = document.getElementById('rootBlocks');
                        if (rootEl && !rootEl._sortable) {
                            rootEl._sortable = Sortable.create(rootEl, {
                                handle: '.group',
                                animation: 150,
                                draggable: '[data-id]',
                                dataIdAttr: 'data-id',
                                onEnd: (evt) => {
                                    const ids = Array.from(rootEl.querySelectorAll(':scope > div[data-id]'))
                                        .map(el => el.getAttribute('data-id'));
                                    const map = {};
                                    this.blocks.forEach(b => map[b.id] = b);
                                    this.blocks = ids.map(id => map[id]).filter(Boolean);
                                    this.pushHistory();
                                }
                            });
                        }
                    },
                    moveBlockUp(i) {
                        if (i <= 0) return;
                        const a = this.blocks;
                        [a[i - 1], a[i]] = [a[i], a[i - 1]];
                        this.blocks = [...a];
                        this.pushHistory();
                    },
                    moveBlockDown(i) {
                        if (i >= this.blocks.length - 1) return;
                        const a = this.blocks;
                        [a[i + 1], a[i]] = [a[i], a[i + 1]];
                        this.blocks = [...a];
                        this.pushHistory();
                    },
                    duplicateBlock(i) {
                        const b = JSON.parse(JSON.stringify(this.blocks[i]));
                        b.id = this._genId();
                        this.blocks.splice(i + 1, 0, b);
                        this.pushHistory();
                    },
                    confirmRemove(_, i) {
                        Swal.fire({
                            title: 'Delete Block?',
                            text: 'Are you sure you want to delete this block?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it'
                        }).then(r => {
                            if (r.isConfirmed) {
                                this.blocks.splice(i, 1);
                                this.pushHistory();
                            }
                        });
                    },

                    handleImageUpload(e, block) {
                        /* ... (unchanged) ... */
                    },

                    // History functions (unchanged)
                    pushHistoryDebounced() {
                        if (this._historyTimer) clearTimeout(this._historyTimer);
                        this._historyTimer = setTimeout(() => {
                            this.pushHistory();
                        }, 400);
                    },
                    pushHistory() {
                        if (this._historyTimer) clearTimeout(this._historyTimer);
                        const snap = JSON.stringify(this.blocks);
                        if (this.historyStack.at(-1) === snap) return;
                        this.historyStack.push(snap);
                        if (this.historyStack.length > 50) this.historyStack.shift();
                        this.redoStack = [];
                    },
                    undo() {
                        if (this.historyStack.length <= 1) return;
                        const cur = this.historyStack.pop();
                        this.redoStack.push(cur);
                        const prev = this.historyStack.at(-1);
                        this.blocks = prev ? JSON.parse(prev) : [];
                    },
                    redo() {
                        if (!this.redoStack.length) return;
                        const next = this.redoStack.pop();
                        this.historyStack.push(next);
                        this.blocks = JSON.parse(next);
                    },

                    // Save Page (Unchanged)
                    savePage() {
                        const payload = {
                            blocks: this.blocks
                        };
                        Swal.fire({
                            title: 'Saving...',
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false
                        });

                        fetch('{{ route('admin.homepage.save') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                content: JSON.stringify(payload)
                            })
                        })
                            .then(res => res.json()).then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Saved!',
                                        timer: 1200,
                                        showConfirmButton: false
                                    });
                                    this.historyStack = [JSON.stringify(this.blocks)];
                                    this.redoStack = [];
                                } else {
                                    throw new Error(data.message || 'Save failed');
                                }
                            })
                            .catch(e => {
                                Swal.fire('Error', e.message || 'Save failed', 'error');
                            });
                    },

                    // Import/Export (Unchanged)
                    exportJSON() { /* ... */ },
                    importJSONPrompt() { /* ... */ }
                }
            }
        </script>

    </div>
@endsection
