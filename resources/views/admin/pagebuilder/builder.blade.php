@extends('layouts.admin.app')

@section('content')
    <div x-data="pageBuilder()" x-init="initAll()" class="relative min-h-screen p-2 bg-gray-50 sm:p-4">

        {{-- 1. HEADER: Added flex-wrap and justify-between for better mobile layout --}}
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

        {{-- 2. MAIN GRID: Changed to grid-cols-1 (mobile-first) and lg:grid-cols-12 for desktop --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- 3. SIDEBAR: Removed sticky for mobile, added lg:sticky for desktop --}}
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

            {{-- 4. CONTENT AREA: Changed to lg:col-span-9 --}}
            <div class="lg:col-span-9 bg-white p-4 sm:p-6 rounded-lg shadow min-h-[60vh]" @dragover.prevent
                @drop="dropBlock($event)">
                <template x-if="blocks.length === 0">
                    <p class="mt-10 text-center text-gray-400">🚀 Drag blocks here to start building</p>
                </template>

                <div id="rootBlocks">
                    <template x-for="(block, index) in blocks" :key="block.id">
                        <div class="relative p-4 mb-4 transition border rounded-lg bg-gray-50 hover:shadow group"
                            :data-id="block.id">

                            {{-- 5. BLOCK HEADER: Added flex-wrap for mobile controls --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-700" x-text="block.type"></span>
                                </div>

                                {{-- 6. BLOCK CONTROLS: Added flex-wrap and mobile-specific justification --}}
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

                            <template x-if="block.type === 'section'">
                                <div class="overflow-hidden bg-white border rounded-lg shadow">
                                    <button @click="block.expanded = !block.expanded"
                                        class="flex items-center justify-between w-full px-4 py-2 transition bg-blue-100 hover:bg-blue-200">
                                        <input type="text" x-model="block.title" @input="pushHistory"
                                            class="flex-1 font-semibold text-gray-700 bg-transparent border-none outline-none" />
                                        <span x-text="block.expanded ? '▾' : '▸'"></span>
                                    </button>

                                    <div x-show="block.expanded" x-collapse class="p-2 bg-gray-50 sm:p-4">
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
                                                        {{-- 7. SUB-BLOCK HEADER: Added flex-wrap --}}
                                                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
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

                                                        {{-- Sub-block content render --}}
                                                        <template x-if="sub.type === 'text' || sub.type === 'heading'">
                                                            <div>
                                                                {{-- 8. QUILL TOOLBAR: Added flex-wrap --}}
                                                                <div :id="'toolbar-' + sub.id"
                                                                    class="flex flex-wrap gap-1 p-1 mb-2 bg-white rounded shadow-sm sm:gap-2 sm:p-2">
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

                                                        {{-- 9. SUB-BLOCK FORM: Changed to grid-cols-1 (mobile) and
                                                        sm:grid-cols-2 (desktop) --}}
                                                        <template x-if="sub.type === 'button'">
                                                            <div class="space-y-3">
                                                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                                                    <div>
                                                                        <label
                                                                            class="text-sm font-medium text-gray-600">Button
                                                                            Text</label>
                                                                        <input type="text" x-model="sub.text"
                                                                            @input="pushHistory"
                                                                            class="w-full p-2 border rounded"
                                                                            placeholder="Click Here">
                                                                    </div>

                                                                    <div>
                                                                        <label
                                                                            class="text-sm font-medium text-gray-600">Button
                                                                            Link (URL)</label>
                                                                        <input type="text" x-model="sub.href"
                                                                            @input="pushHistory"
                                                                            class="w-full p-2 border rounded"
                                                                            placeholder="https://...">
                                                                    </div>

                                                                    <div>
                                                                        <label
                                                                            class="text-sm font-medium text-gray-600">Alignment</label>
                                                                        <select x-model="sub.align" @change="pushHistory"
                                                                            class="w-full p-2 border rounded bg-white">
                                                                            <option value="left">Left</option>
                                                                            <option value="center">Center</option>
                                                                            <option value="right">Right</option>
                                                                        </select>
                                                                    </div>

                                                                    <div>
                                                                        <label
                                                                            class="text-sm font-medium text-gray-600">Target</label>
                                                                        <select x-model="sub.target" @change="pushHistory"
                                                                            class="w-full p-2 border rounded bg-white">
                                                                            <option value="_self">Same Tab (_self)</option>
                                                                            <option value="_blank">New Tab (_blank)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="text-sm font-medium text-gray-600">Display
                                                                            Mode</label>
                                                                        <select x-model="block.displayMode"
                                                                            @change="pushHistory"
                                                                            class="w-full p-2 border rounded bg-white">
                                                                            <option value="default">Default</option>
                                                                            <option value="inline">Inline</option>
                                                                        </select>
                                                                    </div>

                                                                </div>

                                                                <div
                                                                    class="p-3 border border-gray-200 rounded-lg bg-gray-100">
                                                                    <h4 class="mb-2 text-sm font-medium text-gray-700">
                                                                        Optional Image/Icon</h4>

                                                                    <div class="text-center">

                                                                        <template x-if="sub.src">
                                                                            <div>
                                                                                <img :src="sub.src"
                                                                                    class="max-w-[100px] max-h-[100px] mx-auto rounded" />
                                                                                <div class="flex justify-center gap-2 mt-2">
                                                                                    <button
                                                                                        @click="removeMediaFromSub(block, sub.id)"
                                                                                        class="px-2 py-1 text-xs bg-red-100 rounded">Remove
                                                                                        Image</button>
                                                                                </div>
                                                                            </div>
                                                                        </template>

                                                                        <template x-if="!sub.src">
                                                                            <label class="block cursor-pointer">
                                                                                <input type="file" accept="image/*"
                                                                                    @change="handleFileUpload($event, sub.id, 'image', block)"
                                                                                    class="hidden" />
                                                                                <div
                                                                                    class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-100">
                                                                                    <p class="text-sm text-gray-500">🖼️
                                                                                        Click to upload button image/icon
                                                                                    </p>
                                                                                </div>
                                                                            </label>
                                                                        </template>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>


                                                        <template x-if="sub.type === 'embed'">
                                                            <div class="space-y-2">
                                                                <label class="text-sm font-medium text-gray-600">Embed
                                                                    URL (YouTube, etc.)</label>
                                                                <input type="text" x-model="sub.src" @input="pushHistory"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="https://www.youtube.com/watch?v=...">
                                                            </div>
                                                        </template>

                                                        <template x-if="sub.type === 'divider'">
                                                            <hr class="my-4 border-gray-300 border-dashed">
                                                        </template>

                                                        <template x-if="sub.type === 'code'">
                                                            <div>
                                                                <label
                                                                    class="text-sm font-medium text-gray-600">Code</label>
                                                                <textarea x-model="sub.content" @input="pushHistory"
                                                                    class="w-full p-2 font-mono border rounded" rows="6"
                                                                    placeholder="<script>..."></textarea>
                                                            </div>
                                                        </template>

                                                        {{-- START: NEW TABLE SUB-BLOCK TEMPLATE --}}
<template x-if="sub.type === 'table'">
    <div class="space-y-4">
        {{-- Table Controls --}}
        <div class="flex flex-wrap gap-2">
            <button @click.prevent="addRow(sub)"
                class="px-3 py-1 text-sm bg-green-100 rounded hover:bg-green-200">+ Row</button>
            <button @click.prevent="removeRow(sub)" :disabled="sub.data.length <= 1"
                class="px-3 py-1 text-sm bg-red-100 rounded hover:bg-red-200 disabled:opacity-50">- Row</button>
            <button @click.prevent="addCol(sub)"
                class="px-3 py-1 text-sm bg-green-100 rounded hover:bg-green-200">+ Column</button>
            <button @click.prevent="removeCol(sub)" :disabled="sub.data[0].length <= 1"
                class="px-3 py-1 text-sm bg-red-100 rounded hover:bg-red-200 disabled:opacity-50">- Column</button>
        </div>

        {{-- Table Renderer --}}
        <div class="overflow-x-auto border border-gray-200 rounded">
            <table class="min-w-full divide-y divide-gray-200">
                <template x-if="sub.data.length > 0">
                    <thead class="bg-gray-100">
                        <tr class="text-xs text-gray-700 uppercase leading-normal">
                            <template x-for="(col, colIndex) in sub.data[0]" :key="colIndex">
                                <th class="py-2 px-3 border-r border-gray-200 min-w-[150px]">
                                    {{-- Header is also an object now --}}
                                    <input type="text"
                                        x-model.debounce.400ms="sub.data[0][colIndex].text"
                                        @input="pushHistory"
                                        class="w-full text-center bg-transparent border-none focus:ring-0 p-0 text-sm font-bold"
                                        placeholder="Header" />
                                </th>
                            </template>
                        </tr>
                    </thead>
                </template>
                <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                    <template x-for="(row, rowIndex) in sub.data" :key="rowIndex">
                        <template x-if="rowIndex > 0">
                            <tr class="hover:bg-gray-50">
                                <template x-for="(cell, colIndex) in row" :key="colIndex">
                                    <td class="py-2 px-2 border-r border-gray-200 align-top min-w-[150px]">

                                        {{-- 1. IMAGE DISPLAY --}}
                                        <template x-if="cell.img && cell.img !== ''">
                                            <div class="mb-2 relative group-img text-center">
                                                <img :src="cell.img" class="w-16 h-16 object-cover rounded mx-auto border shadow-sm" />
                                                <button @click="cell.img = ''; pushHistory();"
                                                    class="absolute -top-2 right-0 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-md"
                                                    title="Remove Image">
                                                    ✕
                                                </button>
                                            </div>
                                        </template>

                                        {{-- 2. TEXT INPUT --}}
                                        {{-- Note: Binding to cell.text now --}}
                                        <textarea
                                            x-model.debounce.400ms="cell.text"
                                            @input="pushHistory"
                                            rows="2"
                                            class="w-full p-2 text-sm border border-gray-200 rounded focus:border-blue-300 focus:ring-2 focus:ring-blue-100 outline-none resize-none transition"
                                            placeholder="Type text..."></textarea>

                                        {{-- 3. UPLOAD BUTTON --}}
                                        {{-- Only shows if no image exists --}}
                                        <template x-if="!cell.img">
                                            <div class="mt-2 text-center">
                                                <label class="cursor-pointer inline-flex items-center gap-1 text-xs font-medium text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded transition">
                                                    <span>📷 Add Photo</span>
                                                    {{-- Passing 'sub' as the block --}}
                                                    <input type="file" accept="image/*" class="hidden"
                                                        @change="handleTableUpload($event, sub, rowIndex, colIndex)">
                                                </label>
                                            </div>
                                        </template>

                                    </td>
                                </template>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>
{{-- END: NEW TABLE SUB-BLOCK TEMPLATE --}}


                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Root block render --}}
                            <template x-if="block.type === 'text' || block.type === 'heading'">
                                <div class="space-y-2">
                                    {{-- 10. ROOT QUILL TOOLBAR: Added flex-wrap --}}
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

                                    <div :id="'editor-' + block.id" class="bg-white border rounded quill-editor"
                                        style="min-height:100px;"></div>
                                </div>
                            </template>

                            <template x-if="block.type === 'image'">
                                <div class="text-center">
                                    <template x-if="block.src">
                                        <img :src="block.src" class="max-w-full mx-auto rounded-lg shadow-md" />
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
                                        <video :src="block.src" controls
                                            class="max-w-full mx-auto rounded-lg shadow-md"></video>
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

                            {{-- 11. ROOT FORM: Changed to grid-cols-1 (mobile) and sm:grid-cols-2 (desktop) --}}
                            <template x-if="block.type === 'button'">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Text</label>
                                            <input type="text" x-model="block.text" @input="pushHistory"
                                                class="w-full p-2 border rounded" placeholder="Click Here">
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Button Link (URL)</label>
                                            <input type="text" x-model="block.href" @input="pushHistory"
                                                class="w-full p-2 border rounded" placeholder="https://...">
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Alignment</label>
                                            <select x-model="block.align" @change="pushHistory"
                                                class="w-full p-2 border rounded bg-white">
                                                <option value="left">Left</option>
                                                <option value="center">Center</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Target</label>
                                            <select x-model="block.target" @change="pushHistory"
                                                class="w-full p-2 border rounded bg-white">
                                                <option value="_self">Same Tab (_self)</option>
                                                <option value="_blank">New Tab (_blank)</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Display Mode</label>
                                            <select x-model="block.displayMode" @change="pushHistory"
                                                class="w-full p-2 border rounded bg-white">
                                                <option value="default">Default</option>
                                                <option value="inline">Inline</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                        <h4 class="mb-2 text-sm font-medium text-gray-700">Optional Image/Icon</h4>
                                        <div class="text-center">

                                            <template x-if="block.src">
                                                <div>
                                                    <img :src="block.src"
                                                        class="max-w-[100px] max-h-[100px] mx-auto rounded" />
                                                    <div class="flex justify-center gap-2 mt-2">
                                                        <button @click="removeMedia(block.id)"
                                                            class="px-2 py-1 text-xs bg-red-100 rounded">Remove
                                                            Image</button>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="!block.src">
                                                <label class="block cursor-pointer">
                                                    <input type="file" accept="image/*"
                                                        @change="handleFileUpload($event, block.id, 'image')"
                                                        class="hidden" />
                                                    <div
                                                        class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-100">
                                                        <p class="text-sm text-gray-500">🖼️ Click to upload button
                                                            image/icon</p>
                                                    </div>
                                                </label>
                                            </template>

                                        </div>
                                    </div>
                                </div>
                            </template>


                            <template x-if="block.type === 'embed'">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-600">Embed URL (YouTube,
                                        etc.)</label>
                                    <input type="text" x-model="block.src" @input="pushHistory"
                                        class="w-full p-2 border rounded" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                            </template>

                            <template x-if="block.type === 'divider'">
                                <hr class="my-4 border-gray-300 border-dashed">
                            </template>

                            <template x-if="block.type === 'code'">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Code</label>
                                    <textarea x-model="block.content" @input="pushHistory"
                                        class="w-full p-2 font-mono border rounded" rows="6"
                                        placeholder="<script>..."></textarea>
                                </div>
                            </template>

                            {{-- START: NEW TABLE ROOT BLOCK TEMPLATE --}}
                            <template x-if="block.type === 'table'">
                                <div class="space-y-4">
                                    {{-- Table Controls --}}
                                    <div class="flex flex-wrap gap-2">
                                        <button @click.prevent="addRow(block)"
                                            class="px-3 py-1 text-sm bg-green-100 rounded hover:bg-green-200">+
                                            Row</button>
                                        <button @click.prevent="removeRow(block)" :disabled="block.data.length <= 1"
                                            class="px-3 py-1 text-sm bg-red-100 rounded hover:bg-red-200 disabled:opacity-50">-
                                            Row</button>
                                        <button @click.prevent="addCol(block)"
                                            class="px-3 py-1 text-sm bg-green-100 rounded hover:bg-green-200">+
                                            Column</button>
                                        <button @click.prevent="removeCol(block)" :disabled="block.data[0].length <= 1"
                                            class="px-3 py-1 text-sm bg-red-100 rounded hover:bg-red-200 disabled:opacity-50">-
                                            Column</button>
                                    </div>

                                    {{-- Table Renderer --}}
                                    <div class="overflow-x-auto border border-gray-200 rounded">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <template x-if="block.data.length > 0">
                                                <thead class="bg-gray-100">
                                                    <tr class="text-xs text-gray-700 uppercase leading-normal">
                                                        <template x-for="(col, colIndex) in block.data[0]" :key="colIndex">
                                                            <th class="py-2 px-3 border-r border-gray-200">
                                                                    <input type="text"
                                                                        x-model.debounce.400ms="block.data[0][colIndex].text"
                                                                        @input="pushHistory"
                                                                        class="w-full text-center bg-transparent border-none focus:ring-0 p-0 text-sm font-bold"
                                                                        placeholder="Header" />
                                                                    <input type="text"
                                                                        x-model.debounce.400ms="block.data[0][colIndex].href"
                                                                        @input="pushHistory"
                                                                        class="w-full mt-1 text-center bg-transparent border-none focus:ring-0 p-0 text-[10px] text-blue-500 placeholder-blue-300"
                                                                        placeholder="URL" title="Header Link" />
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
                    <td class="py-2 px-2 border-r border-gray-200 align-top">

                        <template x-if="cell.img && cell.img !== ''">
                            <div class="mb-2 relative group-img text-center">
                                <img :src="cell.img" class="w-16 h-16 object-cover rounded mx-auto border shadow-sm" />
                                <button @click="cell.img = ''; pushHistory();"
                                        class="absolute -top-2 right-0 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-md">
                                    ✕
                                </button>
                            </div>
                        </template>

                        <textarea
                            x-model.debounce.400ms="cell.text"
                            @input="pushHistory"
                            rows="2"
                            class="w-full p-2 text-sm border border-gray-200 rounded focus:border-blue-300 focus:ring-2 focus:ring-blue-100 outline-none resize-none transition"
                            placeholder="Type here..."></textarea>

                        <div class="mt-1 relative">
                            <input type="text"
                                x-model.debounce.400ms="cell.href"
                                @input="pushHistory"
                                class="w-full p-1 text-[11px] border border-gray-100 rounded focus:border-blue-200 outline-none transition bg-gray-50/50"
                                placeholder="🔗 Link URL" />
                        </div>

                        <template x-if="!cell.img">
                            <div class="mt-2 text-center">
                                <label class="cursor-pointer inline-flex items-center gap-1 text-xs font-medium text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded transition">
                                    <span>📷 Add Photo</span>
                                    <input type="file" accept="image/*" class="hidden"
                                           @change="handleTableUpload($event, block, rowIndex, colIndex)">
                                </label>
                            </div>
                        </template>

                    </td>
                </template>
            </tr>
        </template>
    </template>
</tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
{{-- ✅ NEW: 'layout_grid' BLOCK (Level 1) --}}
<template x-if="block.type === 'layout_grid'">
    <div class="space-y-4">
        {{-- 1️⃣ Grid Layout Selector --}}
        <div class="p-4 bg-gray-50 border border-gray-200 rounded">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Layout Title</label>
                    <input type="text" x-model="block.title" @input="pushHistory"
                        class="w-full p-2 border rounded bg-white" placeholder="Layout Title">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Grid Layout</label>
                    <select x-model="block.layout" @change="changeGridLayout(block)"
                        class="w-full p-2 bg-white border rounded">
                        <option value="12">1 Column (100%)</option>
                        <option value="6-6">2 Columns (50% / 50%)</option>
                        <option value="4-4-4">3 Columns (33% / 33% / 33%)</option>
                        <option value="8-4">2 Columns (66% / 33%)</option>
                        <option value="4-8">2 Columns (33% / 66%)</option>
                        <option value="3-3-3-3">4 Columns (25%)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 2️⃣ Column Rendering --}}
        <div class="grid grid-cols-12 gap-4 pt-2">
            <template x-for="(col, colIndex) in block.columns" :key="colIndex">
                <div :class="`col-span-12 lg:col-span-${col.span}`">

                    {{-- Drop Zone --}}
                    <div :id="`column-drop-${block.id}-${colIndex}`"
                         class="p-3 border-2 border-dashed border-gray-300 rounded bg-gray-50/50 min-h-[150px]"
                         @dragover.prevent @drop="dropBlockToSection($event, block.columns[colIndex])">

                        <div class="mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider"
                             x-text="`Column ${colIndex + 1}`"></div>

                        <template x-if="!col.blocks || col.blocks.length === 0">
                            <div class="h-20 flex items-center justify-center text-gray-400 text-sm italic border border-gray-200 border-dashed rounded bg-white">
                                Drop Button Here
                            </div>
                        </template>

                        {{-- Child Blocks Loop --}}
                        <div :id="`column-list-${block.id}-${colIndex}`" class="space-y-3">
                            <template x-for="(childBlock, childIndex) in col.blocks" :key="childBlock.id">
                                <div class="relative p-3 bg-white border rounded shadow-sm group" :data-id="childBlock.id">

                                    {{-- Child Header (Drag Handle & Controls) --}}
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3 pb-2 border-b border-gray-100">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="childBlock.type"></span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="px-1 text-base text-gray-400 cursor-grab active:cursor-grabbing handle">☰</span>
                                            <button @click="moveSubUp(block.columns[colIndex], childIndex)" class="p-1 hover:bg-gray-100 rounded text-gray-500">↑</button>
                                            <button @click="moveSubDown(block.columns[colIndex], childIndex)" class="p-1 hover:bg-gray-100 rounded text-gray-500">↓</button>
                                            <button @click="duplicateSub(block.columns[colIndex], childIndex)" class="p-1 hover:bg-gray-100 rounded text-gray-500">⧉</button>
                                            <button @click="confirmRemoveSub(block.columns[colIndex], childIndex)" class="p-1 hover:bg-red-50 text-red-500 rounded">✖</button>
                                        </div>
                                    </div>

                                    {{-- ✅ CHILD BLOCKS (Expanded Support: Text, Image, Table, Button) --}}

                                    {{-- 1. TEXT / HEADING --}}
                                    <template x-if="childBlock.type === 'text' || childBlock.type === 'heading'">
                                        <div class="space-y-2">
                                            <div :id="'toolbar-' + childBlock.id"
                                                class="flex flex-wrap items-center gap-1 p-1 mb-2 bg-white rounded shadow-sm">
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
                                                <button class="ql-list" value="ordered"></button>
                                                <button class="ql-list" value="bullet"></button>
                                                <button class="ql-align"></button>
                                                <button class="ql-link"></button>
                                                <button @click.prevent="openLinkDialog(childBlock.id)" class="ql-custom">🔗</button>
                                            </div>
                                            <div :id="'editor-' + childBlock.id" class="bg-white border rounded quill-editor"
                                                style="min-height:80px;"></div>
                                        </div>
                                    </template>

                                    {{-- 2. IMAGE --}}
                                    <template x-if="childBlock.type === 'image'">
                                        <div class="text-center">
                                            <template x-if="childBlock.src">
                                                <div class="relative">
                                                    <img :src="childBlock.src" class="max-w-full mx-auto rounded shadow-md" />
                                                    <div class="flex justify-center gap-2 mt-2">
                                                        <button @click="removeMediaFromSub(block.columns[colIndex], childBlock.id)"
                                                            class="px-2 py-1 text-xs bg-red-100 rounded text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!childBlock.src">
                                                <label class="block mt-1 cursor-pointer">
                                                    <input type="file" accept="image/*"
                                                        @change="handleFileUpload($event, childBlock.id, 'image', block.columns[colIndex])" class="hidden" />
                                                    <div class="p-3 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50 transition">
                                                        <p class="text-xs text-gray-400">📁 Upload Image</p>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- 3. BUTTON --}}
                                    <template x-if="childBlock.type === 'button'">
                                        <div class="space-y-3">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="text-[10px] font-medium text-gray-500 uppercase">Text</label>
                                                    <input type="text" x-model="childBlock.text" @input="pushHistory"
                                                        class="w-full p-1.5 text-sm border rounded" placeholder="Click Here">
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-medium text-gray-500 uppercase">Link</label>
                                                    <input type="text" x-model="childBlock.href" @input="pushHistory"
                                                        class="w-full p-1.5 text-sm border rounded" placeholder="https://...">
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between gap-2">
                                                <select x-model="childBlock.align" @change="pushHistory" class="text-xs p-1 border rounded bg-white">
                                                    <option value="left">Left</option>
                                                    <option value="center">Center</option>
                                                    <option value="right">Right</option>
                                                </select>
                                                <button @click="childBlock.expanded = !childBlock.expanded" class="text-[10px] text-blue-500 underline">Settings</button>
                                            </div>

                                            <div x-show="childBlock.expanded" class="p-2 bg-gray-50 border rounded space-y-2">
                                                <div>
                                                    <label class="text-[10px]">Target</label>
                                                    <select x-model="childBlock.target" @change="pushHistory" class="w-full text-xs p-1 border rounded">
                                                        <option value="_self">Same Tab</option>
                                                        <option value="_blank">New Tab</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[10px]">Image/Icon</label>
                                                    <template x-if="childBlock.src">
                                                        <div class="flex items-center gap-2">
                                                            <img :src="childBlock.src" class="w-8 h-8 rounded border" />
                                                            <button @click="removeMediaFromSub(block.columns[colIndex], childBlock.id)" class="text-[9px] text-red-500">Remove</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!childBlock.src">
                                                        <input type="file" accept="image/*" @change="handleFileUpload($event, childBlock.id, 'image', block.columns[colIndex])" class="text-[9px]" />
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- 4. TABLE --}}
                                    <template x-if="childBlock.type === 'table'">
                                        <div class="space-y-3">
                                            <div class="flex flex-wrap gap-1">
                                                <button @click.prevent="addRow(childBlock)" class="px-2 py-1 text-[10px] bg-green-50 text-green-700 border border-green-200 rounded">+ R</button>
                                                <button @click.prevent="removeRow(childBlock)" :disabled="childBlock.data.length <= 1" class="px-2 py-1 text-[10px] bg-red-50 text-red-700 border border-red-200 rounded disabled:opacity-50">- R</button>
                                                <button @click.prevent="addCol(childBlock)" class="px-2 py-1 text-[10px] bg-green-50 text-green-700 border border-green-200 rounded">+ C</button>
                                                <button @click.prevent="removeCol(childBlock)" :disabled="childBlock.data[0].length <= 1" class="px-2 py-1 text-[10px] bg-red-50 text-red-700 border border-red-200 rounded disabled:opacity-50">- C</button>
                                            </div>

                                            <div class="overflow-x-auto border border-gray-200 rounded max-h-[300px]">
                                                <table class="min-w-full divide-y divide-gray-200 text-[10px]">
                                                    <thead class="bg-gray-50 sticky top-0">
                                                        <tr>
                                                            <template x-for="(h, hi) in childBlock.data[0]" :key="hi">
                                                                <th class="p-1 border-r border-gray-200 text-center">
                                                                    <input type="text" x-model.debounce.400ms="childBlock.data[0][hi].text" @input="pushHistory"
                                                                        class="w-full text-center bg-transparent border-none p-0 font-bold focus:ring-0" />
                                                                    <input type="text" x-model.debounce.400ms="childBlock.data[0][hi].href" @input="pushHistory"
                                                                        class="w-full text-center bg-transparent border-none p-0 text-[8px] text-blue-500 placeholder-blue-300"
                                                                        placeholder="URL" />
                                                                </th>
                                                            </template>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100">
                                                        <template x-for="(row, ri) in childBlock.data" :key="ri">
                                                            <template x-if="ri > 0">
                                                                <tr class="hover:bg-gray-50">
                                                                    <template x-for="(cell, ci) in row" :key="ci">
                                                                        <td class="p-1 border-r border-gray-200 align-top">
                                                                            <template x-if="cell.img">
                                                                                <div class="relative group mb-1">
                                                                                    <img :src="cell.img" class="w-8 h-8 object-cover rounded mx-auto border" />
                                                                                    <button @click="cell.img = ''; pushHistory();" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-3 h-3 flex items-center justify-center text-[8px]">×</button>
                                                                                </div>
                                                                            </template>
                                                                            <textarea x-model.debounce.400ms="cell.text" @input="pushHistory" rows="2"
                                                                                class="w-full p-1 border-gray-100 rounded focus:border-blue-200 focus:ring-1 focus:ring-blue-100 resize-none leading-tight" placeholder="Text"></textarea>
                                                                            <input type="text" x-model.debounce.400ms="cell.href" @input="pushHistory"
                                                                                class="w-full mt-1 p-1 text-[9px] border border-gray-50 rounded focus:border-blue-100 outline-none bg-gray-50/50"
                                                                                placeholder="🔗 Link" />
                                                                            <template x-if="!cell.img">
                                                                                <label class="cursor-pointer text-[9px] text-blue-500 mt-1 block text-center hover:underline">
                                                                                    + Photo
                                                                                    <input type="file" accept="image/*" class="hidden" @change="handleTableUpload($event, childBlock, ri, ci)" />
                                                                                </label>
                                                                            </template>
                                                                        </td>
                                                                    </template>
                                                                </tr>
                                                            </template>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </template>
                                    {{-- ✅ END CHILD BLOCKS --}}
                                    {{-- END BUTTON --}}

                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
{{-- END: NEW LAYOUT_GRID BLOCK --}}
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

    <script type="application/json" id="pb-initial-content">{!! $page->content ?: '{"blocks":[]}' !!}</script>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    {{--
    <script src="https://cdn.tailwindcss.com"></script> --}} {{-- Tailwind is already loaded by admin.app --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- TABLE LINKS REMOVED --}}

    <script>
        // =================================================================
        //         TABLE MODULE REGISTRATION (REMOVED)
        // =================================================================

        // =================================================================
        //               MAIN ALPINE JS LOGIC
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
                // --- NEW BLOCKS ---
                {
                    type: 'embed',
                    label: '▶️ YouTube/Embed',
                    src: ''
                },
                {
                    type: 'button',
                    label: '🔘 Button',
                    text: 'Click Here',
                    href: '#',
                    align: 'left',
                    target: '_self',
                    displayMode: 'default',
                    src: ''
                },
                {
                    type: 'divider',
                    label: '⎯⎯ Divider'
                },
                {
                    type: 'code',
                    label: '💻 Code Block',
                    defaultContent: ''
                },
                // START: NEW TABLE BLOCK DEFINITION
               {
    type: 'table',
    label: '📊 Table',
    // Ab hum Text string ki jagah Object use karenge { text: '', img: '' }
    data: [
        [
            { text: 'Header 1', img: '', href: '' },
            { text: 'Header 2', img: '', href: '' },
            { text: 'Header 3', img: '', href: '' }
        ],
        [
            { text: 'Cell 1', img: '', href: '' },
            { text: 'Cell 2', img: '', href: '' },
            { text: 'Cell 3', img: '', href: '' }
        ],
    ],
},
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
                    // END: NEW Layout Grid BLOCK DEFINITION
                ],
                blocks: [],
                quills: {},
                historyStack: [],
                redoStack: [],

initAll() {
    try {
        // Robust initial content loading via script[type=application/json]
        const scriptEl = document.getElementById('pb-initial-content');
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
                ...b,
                id: b.id || this._genId()
            }));
        } else if (Array.isArray(initial)) {
            this.blocks = initial.map(b => ({
                ...b,
                id: b.id || this._genId()
            }));
        } else {
            this.blocks = [];
        }

        // ============================================================
        // 🔥 FIX FOR OLD DATA (Migration Script - Recursive)
        // ============================================================
        const migrateTableData = (block) => {
            // 1. Table Data Fix
            if (block.type === 'table' && Array.isArray(block.data)) {
                block.data = block.data.map(row => {
                    return row.map(cell => {
                        // Agar cell String hai (Purana Data), toh usse Object bana do
                        if (typeof cell !== 'object' || cell === null) {
                            return { text: cell, img: '', href: '' };
                        }
                        // Ensure href exists even if cell is object
                        if (typeof cell.href === 'undefined') {
                            cell.href = '';
                        }
                        return cell;
                    });
                });
            }

            // 2. Recursive Fix for Nested Blocks (Sections)
            if (block.type === 'section' && Array.isArray(block.blocks)) {
                block.blocks.forEach(sub => migrateTableData(sub));
            }

            // 3. Recursive Fix for Layout Grid Blocks
            if (block.type === 'layout_grid' && Array.isArray(block.columns)) {
                block.columns.forEach(col => {
                    if (Array.isArray(col.blocks)) {
                        col.blocks.forEach(child => migrateTableData(child));
                    }
                });
            }
        };

        this.blocks.forEach(block => migrateTableData(block));

        // ============================================================
        // END FIX
        // ============================================================

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
                initAll_old() {
                    try {
                        // Robust initial content loading via script[type=application/json]
                        const scriptEl = document.getElementById('pb-initial-content');
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
                                ...b,
                                id: b.id || this._genId()
                            }));
                        } else if (Array.isArray(initial)) {
                            this.blocks = initial.map(b => ({
                                ...b,
                                id: b.id || this._genId()
                            }));
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
                        const types = ['text', 'heading']; // Quill editors only for these

                        if (types.includes(block.type)) {
                            const initialHtml = (block.contentHtml || block.content || block
                                .defaultContent || '<p></p>');
                            const initialDelta = block.contentDelta || null;
                            this.initQuill(block.id, initialHtml, initialDelta);
                        }

                        if (block.type === 'section' && Array.isArray(block.blocks)) {
                            block.blocks.forEach(sub => {
                                if (types.includes(sub.type)) {
                                    const subHtml = (sub.contentHtml || sub.content || sub
                                        .defaultContent || '<p></p>');
                                    const subDelta = sub.contentDelta || null;
                                    this.initQuill(sub.id, subHtml, subDelta);
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

                        // Set default content for text-based blocks
                        if (['text', 'heading'].includes(newBlock.type)) {
                            newBlock.content = newBlock.defaultContent || '<p></p>';
                        }
                        if (newBlock.type === 'code') {
                            newBlock.content = newBlock.defaultContent || '';
                        }
                        // START: New logic for table initialization
                        if (newBlock.type === 'table' && !Array.isArray(newBlock.data)) {
                            newBlock.data = [
                               // Row 1 (Headers)
        [
            { text: 'Header 1', img: '', href: '' },
            { text: 'Header 2', img: '', href: '' },
            { text: 'Header 3', img: '', href: '' }
        ],
        // Row 2 (Data)
        [
            { text: 'Cell 1,1', img: '', href: '' },
            { text: 'Cell 1,2', img: '', href: '' },
            { text: 'Cell 1,3', img: '', href: '' }
        ],
        // Row 3 (Data)
        [
            { text: 'Cell 2,1', img: '', href: '' },
            { text: 'Cell 2,2', img: '', href: '' },
            { text: 'Cell 2,3', img: '', href: '' }
        ],
                            ];
                        }
                        // END: New logic for table initialization
                        // Start Layout Grid feature
                        if (newBlock.type === 'layout_grid' && Array.isArray(newBlock.columns)) {
                            newBlock.columns = newBlock.columns.map(c => ({ span: c.span || 12, blocks: [] }));
                        }
                        // End Layout Grid feature

                        if (newBlock.type === 'section' && !Array.isArray(newBlock.blocks)) {
                            newBlock.blocks = [];
                            newBlock.expanded = true;
                            newBlock.title = newBlock.title || 'New Section';
                        }
                        this.blocks.push(newBlock);
                        this.$nextTick(() => {
                            this.initBlockQuills(newBlock); // Only inits Quill if needed
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

                        // Set default content for text-based blocks
                        if (['text', 'heading'].includes(newBlock.type)) {
                            newBlock.content = newBlock.defaultContent || '<p></p>';
                        }
                        if (newBlock.type === 'code') {
                            newBlock.content = newBlock.defaultContent || '';
                        }
                        // START: New logic for table initialization
                        if (newBlock.type === 'table' && !Array.isArray(newBlock.data)) {
                            newBlock.data = [
                               // Row 1 (Headers)
        [
            { text: 'Header 1', img: '', href: '' },
            { text: 'Header 2', img: '', href: '' },
            { text: 'Header 3', img: '', href: '' }
        ],
        // Row 2 (Data)
        [
            { text: 'Cell 1,1', img: '', href: '' },
            { text: 'Cell 1,2', img: '', href: '' },
            { text: 'Cell 1,3', img: '', href: '' }
        ],
        // Row 3 (Data)
        [
            { text: 'Cell 2,1', img: '', href: '' },
            { text: 'Cell 2,2', img: '', href: '' },
            { text: 'Cell 2,3', img: '', href: '' }
        ],
                            ];
                        }
                        // END: New logic for table initialization

                        if (!Array.isArray(section.blocks)) section.blocks = [];
                        section.blocks.push(newBlock);
                        this.$nextTick(() => {
                            this.initBlockQuills(newBlock); // Only inits Quill if needed
                            this.initSortables();
                            this.pushHistory();
                        });
                    } catch (e) {
                        console.error('dropBlockToSection failed:', e);
                        Swal.fire('Error', 'Failed to drop block into section.', 'error');
                    }
                },
                // ... (rest of the existing functions: initSortables, reorderByIds, etc.)

                // START: NEW TABLE MANIPULATION FUNCTIONS
addRow(block) {
    try {
        const numCols = block.data[0] ? block.data[0].length : 3;
        // String ki jagah object push karein
        const newRow = Array(numCols).fill(null).map(() => ({ text: 'New Cell', img: '', href: '' }));
        block.data.push(newRow);
        this.pushHistory();
    } catch (e) { console.error('addRow failed:', e); }
},
                removeRow(block) {
                    try {
                        if (block.data.length > 1) {
                            block.data.pop();
                            this.pushHistory();
                        } else {
                            Swal.fire('Cannot remove last row', 'A table must have at least one row.', 'info');
                        }
                    } catch (e) { console.error('removeRow failed:', e); }
                },
addCol(block) {
    try {
        // String ki jagah object push karein
        block.data.forEach(row => row.push({ text: 'New Cell', img: '', href: '' }));
        this.pushHistory();
    } catch (e) { console.error('addCol failed:', e); }
},
                removeCol(block) {
                    try {
                        if (block.data[0].length > 1) {
                            block.data.forEach(row => row.pop());
                            this.pushHistory();
                        } else {
                            Swal.fire('Cannot remove last column', 'A table must have at least one column.', 'info');
                        }
                    } catch (e) { console.error('removeCol failed:', e); }
                },
                // END: NEW TABLE MANIPULATION FUNCTIONS

changeGridLayout(block) {
    if (!block || !block.layout) return;
    const spans = ('' + block.layout).split('-').map(s => parseInt(s, 10) || 12);
    const oldColumns = Array.isArray(block.columns) ? block.columns : [];
    const newColumns = [];

    for (let i = 0; i < spans.length; i++) {
        if (oldColumns[i]) {
            // Agar column already exist karta hai, to uske blocks ko rakhein
            newColumns.push({ span: spans[i], blocks: oldColumns[i].blocks });
        } else {
            // Naya column, empty blocks ke saath
            newColumns.push({ span: spans[i], blocks: [] });
        }
    }

    // Agar columns kam ho gaye hain, to extra blocks ko aakhri column mein move karein
    if (oldColumns.length > newColumns.length) {
        for (let i = newColumns.length; i < oldColumns.length; i++) {
            if (Array.isArray(oldColumns[i].blocks) && oldColumns[i].blocks.length) {
                // Aakhri naye column mein blocks ko append karein
                newColumns[newColumns.length - 1].blocks.push(...oldColumns[i].blocks);
            }
        }
    }
    block.columns = newColumns;
    this.pushHistory();
    this.$nextTick(() => this.initSortables());
},
// Layout Grid

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
                                    const ids = Array.from(rootEl.querySelectorAll(
                                        ':scope > div[data-id]')).map(el => el
                                            .getAttribute('data-id'));
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
                                            const ids = Array.from(secList
                                                .querySelectorAll(
                                                    ':scope > div[data-id]')).map(
                                                        el => el.getAttribute('data-id'));
                                            this.reorderSectionByIds(block, ids);
                                        }
                                    });
                                }
                            }
                            // ✅ NEW: LAYOUT_GRID COLUMN SORTABLES ADDITION
            if (block.type === 'layout_grid' && Array.isArray(block.columns)) {
                block.columns.forEach((col, colIndex) => {
                    // Unique ID: `column-list-BLOCK_ID-COL_INDEX`
                    const colList = document.getElementById(`column-list-${block.id}-${colIndex}`);

                    if (colList && !colList._sortable) {
                        colList._sortable = Sortable.create(colList, {
                            // Home page builder mein aapne 'shared-blocks' group use kiya tha.
                            // Page builder mein aap drag-and-drop ke liye section logic use karte hain.
                            // Yahan hum drag/drop ke liye "clone" functionality ko ignore kar rahe hain,
                            // sirf sorting ko enable kar rahe hain.
                            animation: 150,
                            draggable: '[data-id]',
                            dataIdAttr: 'data-id',

                            onEnd: (evt) => {
                                // 1. Updated order ke IDs ko fetch karein
                                const ids = Array.from(colList
                                    .querySelectorAll(':scope > div[data-id]'))
                                    .map(el => el.getAttribute('data-id'));

                                // 2. Data model mein blocks ko reorder karein
                                const map = {};
                                // Make sure 'col.blocks' array exists before mapping
                                (col.blocks || []).forEach(b => map[b.id] = b);
                                col.blocks = ids.map(id => map[id]).filter(Boolean);

                                // 3. History push karein aur Quills ko re-initialize karein
                                this.pushHistory();
                                this.$nextTick(() => this.initAllQuills());
                            },
                        });
                    }
                });
            }
                        });
                    } catch (e) {
                        console.error('initSortables failed:', e);
                    }
                },
                // ... (rest of the existing functions)
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
                // ... (moveBlockUp, moveBlockDown, duplicateBlock, confirmRemove, moveSubUp, moveSubDown, duplicateSub, confirmRemoveSub)
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
                            b.blocks = b.blocks.map(sb => ({
                                ...sb,
                                id: this._genId()
                            }));
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
                        [arr[sIndex + 1], arr[sIndex]] = [arr[sIndex], arr[sIndex + 1]];
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
                    } catch (e) {
                        console.error('confirmRemoveSub failed:', e);
                    }
                },
                // ... (rest of the existing Quill, File Upload, History functions)
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
                                modules: {
                                    toolbar: toolbarSelector
                                },
                                placeholder: 'Type here...'
                            });

                            if (initialDelta && typeof initialDelta === 'object') {
                                try {
                                    quill.setContents(initialDelta);
                                } catch (_) {
                                    quill.root.innerHTML = initialHtml || '<p></p>';
                                }
                            } else if (initialHtml) {
                                quill.root.innerHTML = initialHtml;
                            }
                            this.quills[blockId] = quill;

                            let debounceTimer;
                            quill.on('text-change', (delta, oldDelta, source) => {
                                if (source !== 'user') return;
                                clearTimeout(debounceTimer);
                                debounceTimer = setTimeout(() => {
                                    // This is the fix: only call pushHistory,
                                    // which will then call updateQuillContent for *all* editors.
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
                    try {
                        const findAndUpdate = (arr) => {
                            for (let b of arr) {
                                if (b.id === blockId) {
                                    b.content = html; // legacy field support
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
                        // 🚫 This is the root bug fix: DO NOT trigger reactivity here.
                        // this.blocks = [...this.blocks];
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
                            return Swal.fire('Select text', 'Please select the text you want to link.',
                                'info');
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
                                target: document.getElementById('swal-link-target').value ||
                                    '_blank'
                            }),
                            showCancelButton: true
                        }).then(res => {
                            if (res.isConfirmed && res.value.url) {
                                quill.format('link', res.value.url);
                                // Set target attribute after link is created
                                quill.formatText(range.index, range.length, 'link', {
                                    href: res.value.url,
                                    target: res.value.target
                                });

                                // Manually update the DOM as Quill doesn't natively support 'target'
                                setTimeout(() => {
                                    const anchors = quill.root.querySelectorAll('a[href="' + res.value.url + '"]');
                                    anchors.forEach(a => {
                                        if (!a.hasAttribute('target')) {
                                            a.setAttribute('target', res.value.target);
                                        }
                                    });
                                    this.updateQuillContent(blockId, quill.root.innerHTML, quill.getContents());
                                    this.pushHistory();
                                }, 100);
                            }
                        });
                    } catch (e) {
                        console.error('openLinkDialog failed:', e);
                    }
                },

                async handleFileUpload(e, blockId, type, section = null) {
                    try {
                        const file = e.target.files[0];
                        if (!file) return;

                        // const {
                        //     value: formValues
                        // } = await Swal.fire({
                        //     title: "Upload Options",
                        //     html: `
                        //                                                                         <input id="custom_name" class="swal2-input" placeholder="File name (optional)" style="width: 100%;" />
                        //                                                                         <select id="base_path" class="swal2-select" style="width: 100%; margin-top: 10px;">
                        //                                                                             <option value="storage" selected>storage</option>
                        //                                                                             <option value="wp-content">wp-content</option>
                        //                                                                         </select>
                        //                                                                     `,
                        //     focusConfirm: false,
                        //     preConfirm: () => ({
                        //         custom_name: document.getElementById("custom_name").value,
                        //         base_path: document.getElementById("base_path").value,
                        //     }),
                        //     confirmButtonText: "Upload",
                        //     showCancelButton: true,
                        // });

                        // if (!formValues) return;

                        // const formData = new FormData();
                        // formData.append("file", file);
                        // formData.append("base_path", formValues.base_path || "storage");
                        // formData.append("custom_name", formValues.custom_name || "");

                        const basePath = "storage";
        const customName = "";

        const formData = new FormData();
        formData.append("file", file);
        formData.append("base_path", basePath);
        formData.append("custom_name", customName);

                        Swal.fire({
                            title: 'Uploading...',
                            text: 'Please wait.',
                            didOpen: () => { Swal.showLoading() },
                            allowOutsideClick: false
                        });

                        const res = await fetch(
                            '{{ route('admin.pagebuilder.builder.upload', $page) }}', {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            },
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
                            this.blocks = [...this.blocks]; // This is OK here, only for media change

                            Swal.fire({
                                icon: "success",
                                title: "✅ File Uploaded",
                                text: `${data.filename}`,
                                timer: 1400,
                                showConfirmButton: false,
                            });

                            this.pushHistory();
                        } else {
                            Swal.fire("Error", data.message || "Upload failed.", "error");
                        }
                    } catch (err) {
                        console.error('handleFileUpload failed:', err);
                        Swal.fire("Error", "An error occurred during upload.", "error");
                    } finally {
                        // Clear the file input so the same file can be re-uploaded if needed
                        e.target.value = null;
                    }
                },
                // table handle
                async handleTableUpload(e, block, rowIndex, colIndex) {
    try {
        const file = e.target.files[0];
        if (!file) return;

        // 1. SweetAlert se naam aur path poochna (Same as existing logic)
        // const { value: formValues } = await Swal.fire({
        //     title: "Upload Photo",
        //     html: `
        //         <input id="custom_name_tbl" class="swal2-input" placeholder="File name (optional)" style="width: 100%;" />
        //         <select id="base_path_tbl" class="swal2-select" style="width: 100%; margin-top: 10px;">
        //             <option value="storage" selected>storage</option>
        //             <option value="wp-content">wp-content</option>
        //         </select>
        //     `,
        //     focusConfirm: false,
        //     preConfirm: () => ({
        //         custom_name: document.getElementById("custom_name_tbl").value,
        //         base_path: document.getElementById("base_path_tbl").value,
        //     }),
        //     confirmButtonText: "Upload",
        //     showCancelButton: true,
        // });

        // if (!formValues) {
        //     e.target.value = null; // Reset input
        //     return;
        // }
const basePath = "storage";
        const customName = "";
        // 2. Uploading Animation
        const formData = new FormData();
        formData.append("file", file);
        formData.append("base_path", basePath);
        formData.append("custom_name", customName);

        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait.',
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false
        });

        // 3. Server Request (AJAX)
        // Note: Yahan wahi route use karein jo handleFileUpload me hai
        const res = await fetch('{{ route('admin.pagebuilder.builder.upload', $page) }}', {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData,
        });

        const data = await res.json();

        // 4. Success Handling - Update Specific Cell
        if (data.success) {
            // Check karein agar cell abhi string hai ya object
            let currentCell = block.data[rowIndex][colIndex];

            // Agar purana data sirf text tha, usse object banao
            if (typeof currentCell !== 'object') {
                currentCell = { text: currentCell, img: '' };
            }

            // Image URL set karein
            currentCell.img = data.url;

            // Wapis data array me assign karein (Update)
            block.data[rowIndex][colIndex] = currentCell;

            Swal.fire({
                icon: "success",
                title: "✅ Photo Added",
                timer: 1000,
                showConfirmButton: false,
            });

            // UI Refresh Trigger
            this.blocks = [...this.blocks];
            this.pushHistory();

        } else {
            Swal.fire("Error", data.message || "Upload failed.", "error");
        }

    } catch (err) {
        console.error('handleTableUpload failed:', err);
        Swal.fire("Error", "An error occurred.", "error");
    } finally {
        e.target.value = null;
    }
},
                removeMedia(blockId) {
                    try {
                        const update = (arr) => {
                            for (let b of arr) {
                                if (b.id === blockId) {
                                    delete b.src;
                                    return true;
                                }
                                if (b.type === 'section' && Array.isArray(b.blocks)) {
                                    if (update(b.blocks)) return true;
                                }
                            }
                            return false;
                        };
                        update(this.blocks);
                        this.blocks = [...this.blocks]; // OK here
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
                            this.blocks = [...this.blocks]; // OK here
                            this.pushHistory();
                        }
                    } catch (e) {
                        console.error('removeMediaFromSub failed:', e);
                    }
                },
                getMediaStyle(block) {
                    // This function seems unused for root blocks, but left for completeness
                    try {
                        const w = block.width || 600;
                        const h = block.height || 300;
                        return `max-width: 100%; width:${w}px; height:${h}px; object-fit:contain;`;
                    } catch (e) {
                        console.error('getMediaStyle failed:', e);
                        return 'max-width: 100%;';
                    }
                },
                savePage() {
                    try {
                        // Sync all Quill content *before* saving
                        Object.keys(this.quills).forEach(id => {
                            const q = this.quills[id];
                            if (q) this.updateQuillContent(id, q.root.innerHTML, q
                                .getContents());
                        });

                        const payload = {
                            blocks: this.blocks
                        };
                        document.getElementById('pageContent').value = JSON.stringify(payload);

                        // Show saving indicator
                        Swal.fire({
                            title: '💾 Saving...',
                            text: 'Please wait.',
                            didOpen: () => { Swal.showLoading() },
                            allowOutsideClick: false
                        });

                        // Use fetch to submit the form for better UX
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
                                if (!res.ok) {
                                    // If server returns an error, show it
                                    return res.json().then(err => { throw new Error(err.message || 'Save failed') });
                                }
                                return res.json();
                            })
                            .then(data => {
                                // Check for a success flag from the controller
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "✅ Page Saved!",
                                        timer: 1500,
                                        showConfirmButton: false,
                                    });
                                    // Reset history stacks as the current state is now saved
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

                        // Original submit logic (commented out in favor of fetch)
                        // document.getElementById('saveForm').submit();

                    } catch (e) {
                        console.error('savePage failed:', e);
                        Swal.fire('Error', 'Failed to prepare for saving.', 'error');
                    }
                },
                exportJSON() {
                    try {
                        // Sync all Quill content *before* exporting
                        Object.keys(this.quills).forEach(id => {
                            const q = this.quills[id];
                            if (q) this.updateQuillContent(id, q.root.innerHTML, q
                                .getContents());
                        });

                        const payload = {
                            blocks: this.blocks
                        };
                        const blob = new Blob([JSON.stringify(payload, null, 2)], {
                            type: 'application/json'
                        });
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
                            html: `<input type="file" id="jsonFile" accept="application/json" class="swal2-file" style="width: 100%;">`,
                            showCancelButton: true,
                            preConfirm: () => {
                                return new Promise((resolve) => {
                                    const f = document.getElementById('jsonFile')
                                        .files[0];
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
                                        this.blocks = parsed.blocks.map(b => ({
                                            ...b,
                                            id: b.id || this._genId()
                                        }));
                                    } else {
                                        this.blocks = Array.isArray(parsed) ? parsed.map(b => ({
                                            ...b,
                                            id: b.id || this._genId()
                                        })) : [];
                                    }
                                    this.$nextTick(() => {
                                        this.quills = {}; // Clear old instances
                                        this.initAllQuills(); // Re-init all
                                        this.initSortables();
                                        this.pushHistory();
                                    });
                                    Swal.fire('Imported', 'JSON imported successfully',
                                        'success');
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
                        // Sync all Quill content *before* making snapshot
                        Object.keys(this.quills).forEach(id => {
                            const q = this.quills[id];
                            if (q) this.updateQuillContent(id, q.root.innerHTML, q
                                .getContents());
                        });

                        const snapshot = JSON.stringify(this.blocks);
                        if (this.historyStack.length > 0 && this.historyStack[this.historyStack.length -
                            1] === snapshot) return;

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
                            try {
                                this.blocks = JSON.parse(prev);
                            } catch (e) { }

                            this.quills = {}; // Clear old instances
                            this.$nextTick(() => {
                                this.initAllQuills(); // Re-init all
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
                        try {
                            this.blocks = JSON.parse(next);
                        } catch (e) { }
                        this.historyStack.push(next);

                        this.quills = {}; // Clear old instances
                        this.$nextTick(() => {
                            this.initAllQuills(); // Re-init all
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
            /* Tighter padding for mobile */
        }

        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 8px;
            /* Tighter margin */
        }

        .ql-snow .ql-picker-label {
            font-size: 12px;
            /* Smaller picker labels */
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
            /* Force full width */
            box-sizing: border-box;
            /* Ensure padding is included */
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .swal2-popup {
            width: 90vw !important;
            /* Use viewport width */
            max-width: 480px;
            /* Set a reasonable max-width */
        }
    </style>
@endsection
