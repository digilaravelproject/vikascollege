{{-- =================================================================== --}}
{{-- ✅ LEVEL 1: Sortable.js Drop Area --}}
{{-- =================================================================== --}}
<div class="block-container min-h-[50px] space-y-4" :data-sortable-container="`{{ $parentPath }}`"
    @drop.prevent.stop="dropBlock($event, `{{ $parentPath }}`)">

    {{-- Loop through blocks (Level 1) --}}
    {{-- ❗️ FIX: Renamed '(block, index)' to '(block, blockIndex)' --}}
    <template x-for="(block, blockIndex) in {{ $blocks }}" :key="block.id">
        <div class="relative p-4 transition border rounded-lg bg-gray-50 hover:shadow-md group" :data-id="block.id">

            {{-- 🧰 Block Controls (Level 1) --}}
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-700 cursor-grab"
                        x-text="availableBlocks.find(b => b.type === block.type)?.label || block.type"></span>
                </div>

                <div class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                    {{-- ❗️ FIX: Use 'blockIndex' --}}
                    <button @click="moveBlockUp({{ $parentPath }}, blockIndex)" :disabled="blockIndex === 0"
                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↑</button>

                    {{-- ❗️ FIX: Use 'blockIndex' and '{{ $blocks }}' --}}
                    <button @click="moveBlockDown({{ $parentPath }}, blockIndex)"
                        :disabled="blockIndex === {{ $blocks }}.length - 1"
                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↓</button>

                    {{-- ❗️ FIX: Use 'blockIndex' --}}
                    <button @click="duplicateBlock({{ $parentPath }}, blockIndex)"
                        class="px-2 py-1 text-sm bg-white border rounded">⧉</button>

                    {{-- ❗️ FIX: Use 'blockIndex' --}}
                    <button @click="confirmRemove({{ $parentPath }}, blockIndex)"
                        class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                </div>
            </div>

            <hr class="mb-4">

            {{-- =================================== --}}
            {{-- 🧩 BLOCK-SPECIFIC SETTINGS (Level 1) --}}
            {{-- =================================== --}}

            {{-- 'intro' block --}}
            <template x-if="block.type === 'intro'">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Layout</label>
                            {{-- ❗️ FIX: Use 'block.layout' (this is correct for Level 1) --}}
                            <select x-model="block.layout" @change="pushHistoryDebounced"
                                class="w-full p-2 border rounded">
                                <option value="left">Image Left</option>
                                <option value="right">Image Right</option>
                                <option value="top">Image Top</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Image URL</label>
                            {{-- ❗️ FIX: Use 'block.image' --}}
                            <input type="text" x-model="block.image" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded" placeholder="https://...">
                        </div>
                    </div>
                    {{-- ... more intro inputs (heading, buttonText, etc.) --}}
                </div>
            </template>

            {{-- 'sectionLinks' block --}}
            <template x-if="block.type === 'sectionLinks'">
                <div class="space-y-4">
                    {{-- ... your sectionLinks fields (title, links, etc.) --}}
                    {{-- Example: --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</label>
                        <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded" placeholder="Section Title">
                    </div>
                </div>
            </template>

            {{-- 'latestUpdates' block --}}
            <template x-if="block.type === 'latestUpdates'">
                <div class="space-y-3">
                    {{-- ... your latestUpdates inputs --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</a bel>
                            <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded" placeholder="Latest Updates">
                    </div>
                </div>
            </template>

            {{-- 'divider' block --}}
            <template x-if="block.type === 'divider'">
                <hr class="my-4 border-gray-300 border-dashed">
            </template>

            {{-- ... Other blocks (announcements, events, etc.) ... --}}


            {{-- =================================== --}}
            {{-- ⭐️ 'layout_grid' BLOCK (Level 1) ⭐️ --}}
            {{-- =================================== --}}
            <template x-if="block.type === 'layout_grid'">
                <div class="space-y-4">
                    {{-- 1️⃣ Grid Layout Selector --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Grid Layout</label>
                        {{-- ❗️ FIX: Use 'block.layout' (correct for Level 1) --}}
                        <select x-model="block.layout" @change="changeGridLayout(block)"
                            class="w-full p-2 bg-white border rounded">
                            <option value="12">1 Column (100%)</option>
                            <option value="6-6">2 Columns (50% / 50%)</option>
                            <option value="4-4-4">3 Columns (33% / 33% / 33%)</option>
                            <option value="8-4">2 Columns (66% / 33%)</option>
                            <option value="4-8">2 Columns (33% / 66%)</option>
                            <option value="3-3-3-3">4 Columns (25% / 25% / 25% / 25%)</option>
                        </select>
                    </div>

                    {{-- 2️⃣ Recursive Column Rendering --}}
                    <div class="grid grid-cols-12 gap-4 pt-2">
                        {{-- Level 2 loop --}}
                        <template x-for="(col, colIndex) in block.columns" :key="colIndex">
                            <div :class="`col-span-12 lg:col-span-${col.span}`">
                                <div class="p-4 border border-dashed border-blue-400 rounded-lg bg-blue-50/50">
                                    <span class="block mb-2 text-xs font-medium text-blue-700"
                                        x-text="`Column ${colIndex + 1} (${col.span}/12)`"></span>

                                    {{-- =================================================================== --}}
                                    {{-- ✅ LEVEL 3: RECURSIVE PASTE --}}
                                    {{-- Yahaan $parentPath, blockIndex, colIndex, childBlock, childIndex ka --}}
                                    {{-- istemaal HOGA --}}
                                    {{-- =================================================================== --}}
                                    <div class="block-container min-h-[50px] space-y-4" {{-- ❗️ FIX: Path
                                        uses 'blockIndex' from Level 1 --}}
                                        :data-sortable-container="`{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`"
                                        @drop.prevent.stop="dropBlock($event, `{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`)">

                                        {{-- Loop through blocks (Level 3) --}}
                                        {{-- ❗️ FIX: Renamed to '(childBlock, childIndex)' --}}
                                        <template x-for="(childBlock, childIndex) in col.blocks" :key="childBlock.id">
                                            <div class="relative p-4 transition border rounded-lg bg-gray-50 hover:shadow-md group"
                                                :data-id="childBlock.id">

                                                {{-- 🧰 Block Controls (Level 3) --}}
                                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                                    <div class="flex items-center gap-2">
                                                        {{-- ❗️ FIX: Use 'childBlock' --}}
                                                        <span class="font-semibold text-gray-700 cursor-grab"
                                                            x-text="availableBlocks.find(b => b.type === childBlock.type)?.label || childBlock.type"></span>
                                                    </div>

                                                    <div
                                                        class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                                                        {{-- ❗️ FIX: Path uses 'blockIndex' & 'colIndex', function uses
                                                        'childIndex' --}}
                                                        <button
                                                            @click="moveBlockUp(`{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`, childIndex)"
                                                            :disabled="childIndex === 0"
                                                            class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↑</button>

                                                        {{-- ❗️ FIX: Path uses 'blockIndex' & 'colIndex', function uses
                                                        'childIndex' --}}
                                                        <button
                                                            @click="moveBlockDown(`{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`, childIndex)"
                                                            :disabled="childIndex === col.blocks.length - 1"
                                                            class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↓</button>

                                                        {{-- ❗️ FIX: Path uses 'blockIndex' & 'colIndex', function uses
                                                        'childIndex' --}}
                                                        <button
                                                            @click="duplicateBlock(`{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`, childIndex)"
                                                            class="px-2 py-1 text-sm bg-white border rounded">⧉</button>

                                                        {{-- ❗️ FIX: Path uses 'blockIndex' & 'colIndex', function uses
                                                        'childIndex' --}}
                                                        <button
                                                            @click="confirmRemove(`{{ $parentPath }}[\${blockIndex}].columns[\${colIndex}].blocks`, childIndex)"
                                                            class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                                                    </div>
                                                </div>

                                                <hr class="mb-4">

                                                {{-- =================================== --}}
                                                {{-- 🧩 BLOCK-SPECIFIC SETTINGS (Level 3) --}}
                                                {{-- =================================== --}}

                                                {{-- 'intro' block --}}
                                                {{-- ❗️ FIX: Use 'childBlock' --}}
                                                <template x-if="childBlock.type === 'intro'">
                                                    <div class="space-y-4">
                                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                            <div>
                                                                <label
                                                                    class="text-sm font-medium text-gray-600">Layout</label>
                                                                {{-- ❗️ FIX: Use 'childBlock.layout' --}}
                                                                <select x-model="childBlock.layout"
                                                                    @change="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded">
                                                                    <option value="left">Image Left</option>
                                                                    <option value="right">Image Right</option>
                                                                    <option value="top">Image Top</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-600">Image
                                                                    URL</label>
                                                                {{-- ❗️ FIX: Use 'childBlock.image' --}}
                                                                <input type="text" x-model="childBlock.image"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="https://...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- 'sectionLinks' block --}}
                                                {{-- ❗️ FIX: Use 'childBlock' --}}
                                                <template x-if="childBlock.type === 'sectionLinks'">
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label
                                                                class="text-sm font-medium text-gray-600">Title</label>
                                                            {{-- ❗️ FIX: Use 'childBlock.title' --}}
                                                            <input type="text" x-model="childBlock.title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded"
                                                                placeholder="Section Title">
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- 'latestUpdates' block --}}
                                                {{-- ❗️ FIX: Use 'childBlock' --}}
                                                <template x-if="childBlock.type === 'latestUpdates'">
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label
                                                                class="text-sm font-medium text-gray-600">Title</label>
                                                            {{-- ❗️ FIX: Use 'childBlock.title' --}}
                                                            <input type="text" x-model="childBlock.title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded"
                                                                placeholder="Latest Updates">
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- 'divider' block --}}
                                                <template x-if="childBlock.type === 'divider'">
                                                    <hr class="my-4 border-gray-300 border-dashed">
                                                </template>

                                                {{-- ... Other blocks (announcements, events, etc.) ... --}}

                                                {{-- Yahaan par nested 'layout_grid' add na karein jab tak ki --}}
                                                {{-- aap fully recursive Blade partials (@include) use na kar rahe hon
                                                --}}

                                            </div>
                                        </template>
                                    </div>
                                    {{-- ============================================= --}}
                                    {{-- ✅ END OF LEVEL 3 RECURSIVE PASTE --}}
                                    {{-- ============================================= --}}

                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>
    </template>
</div>
