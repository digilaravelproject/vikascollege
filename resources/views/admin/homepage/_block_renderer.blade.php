<div class="block-container min-h-[50px] space-y-4" :data-sortable-container="`{{ $parentPath }}`">

    {{-- Loop through blocks (Level 1) --}}
    <template x-for="(block, blockIndex) in {{ $blocks }}" :key="block.id">
        <div class="relative p-4 transition border rounded-lg bg-gray-50 hover:shadow-md group" :data-id="block.id">

            {{-- 🧰 Block Controls (Level 1) --}}
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-700 cursor-grab"
                        x-text="availableBlocks.find(b => b.type === block.type)?.label || block.type"></span>
                </div>

                <div class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                    {{-- Yeh buttons ab 'setup.blade.php' mein define kiye gaye functions ko call karenge --}}
                    <button @click="moveBlockUp('{{ $parentPath }}', blockIndex)" :disabled="blockIndex === 0"
                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↑</button>

                    <button @click="moveBlockDown('{{ $parentPath }}', blockIndex)"
                        :disabled="blockIndex === {{ $blocks }}.length - 1"
                        class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↓</button>

                    <button @click="duplicateBlock('{{ $parentPath }}', blockIndex)"
                        class="px-2 py-1 text-sm bg-white border rounded">⧉</button>

                    <button @click="confirmRemove('{{ $parentPath }}', blockIndex)"
                        class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                </div>
            </div>

            <hr class="mb-4">

            {{-- =================================== --}}
            {{-- 🧩 BLOCK-SPECIFIC SETTINGS (Level 1) --}}
            {{-- =================================== --}}

            {{-- ⭐️ ENHANCED: 'intro' block --}}
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
                        <div>
                            <label class="text-sm font-medium text-gray-600">Heading</label>
                            <input type="text" x-model="block.heading" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded" placeholder="Block Heading">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Content</label>
                            <textarea x-model="block.text" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded">
                            </textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Button Text</label>
                            <input type="text" x-model="block.buttonText" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded" placeholder="Learn More">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Button Link</label>
                            <input type="text" x-model="block.buttonLink" @input="pushHistoryDebounced"
                                class="w-full p-2 border rounded" placeholder="/about-us">
                        </div>
                    </div>
                </div>
            </template>

            {{-- Gallery Block Settings --}}
            <template x-if="block.type === 'gallery'">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </template>
            {{-- ⭐️ ENHANCED: 'sectionLinks' block --}}
            <template x-if="block.type === 'sectionLinks'">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</label>
                        <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded" placeholder="Section Title">
                    </div>

                    {{-- NEW: Repeater for links --}}
                    <div class="p-3 space-y-3 border rounded bg-gray-100/50">
                        <label class="text-sm font-medium text-gray-600">Links</label>
                        <template x-for="(link, linkIndex) in block.links" :key="linkIndex">
                            <div class="grid grid-cols-1 gap-2 p-2 border rounded bg-white sm:grid-cols-2 sm:gap-4">
                                <input type="text" x-model="link.text" @input="pushHistoryDebounced"
                                    class="w-full p-2 text-sm border rounded" placeholder="Link Text">
                                <div class="flex gap-2">
                                    <input type="text" x-model="link.url" @input="pushHistoryDebounced"
                                        class="w-full p-2 text-sm border rounded" placeholder="Link URL (e.g., /page)">
                                    <button @click="block.links.splice(linkIndex, 1); pushHistory();"
                                        class="px-2 text-red-500 border rounded bg-white hover:bg-red-50">✖</button>
                                </div>
                            </div>
                        </template>
                        <button @click="block.links.push({ text: 'New Link', url: '#' }); pushHistory();"
                            class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                            + Add Link
                        </button>
                    </div>
                </div>
            </template>

            {{-- 'latestUpdates' block --}}
            {{-- <template x-if="block.type === 'latestUpdates'">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</label>
                        <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded" placeholder="Latest Updates">
                    </div>
                </div>
            </template> --}}

            {{-- Events Block Settings --}}
            <template x-if="block.type === 'events'">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Description</label>
                        <textarea x-model="block.section_description" @input="pushHistoryDebounced" rows="3"
                            class="w-full p-2 border rounded"></textarea>
                    </div>
                </div>
            </template>
            {{-- Academic Calendar Block Settings --}}
            <template x-if="block.type === 'academic_calendar'">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Item Count</label>
                        <input type="number" min="1" x-model.number="block.item_count" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </template>
            {{-- Testimonials Block Settings --}}
            <template x-if="block.type === 'testimonials'">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </template>
            {{-- Why Choose Us Block Settings --}}
            <template x-if="block.type === 'why_choose_us'">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Description</label>
                        <textarea x-model="block.section_description" @input="pushHistoryDebounced" rows="3"
                            class="w-full p-2 border rounded"></textarea>
                    </div>
                </div>
            </template>

            {{-- 'divider' block --}}
            <template x-if="block.type === 'divider'">
                <hr class="my-4 border-gray-300 border-dashed">
            </template>
            <template x-if="block.type === 'announcements'">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Section Title</label>
                        <input type="text" x-model="block.section_title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Content Type</label>
                        <select x-model="block.content_type" @change="pushHistoryDebounced"
                            class="w-full p-2 bg-white border rounded">
                            <option value="student">Student Corner</option>
                            <option value="faculty">Faculty Corner</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Display Count</label>
                        <input type="number" min="1" x-model.number="block.display_count" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </template>

            {{-- =================================== --}}
            {{-- ⭐️ 'layout_grid' BLOCK (Level 1) ⭐️ --}}
            {{-- =================================== --}}
            <template x-if="block.type === 'layout_grid'">
                <div class="space-y-4">
                    {{-- 1️⃣ Grid Layout Selector --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</label>
                        <input type="text" x-model="block.title" @input="pushHistoryDebounced"
                            class="w-full p-2 border rounded" placeholder="Layout Title">
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
                            <option value="3-3-3-3">4 Columns (25% / 25% / 25% / 25%)</option>
                        </select>
                    </div>

                    {{-- 2️⃣ Recursive Column Rendering (Level 2) --}}
                    <div class="grid grid-cols-12 gap-4 pt-2">
                        <template x-for="(col, colIndex) in block.columns" :key="colIndex">
                            <div :class="`col-span-12 lg:col-span-${col.span}`">
                                <div class="p-4 border border-dashed border-blue-400 rounded-lg bg-blue-50/50">
                                    <span class="block mb-2 text-xs font-medium text-blue-700"
                                        x-text="`Column ${colIndex + 1} (${col.span}/12)`"></span>

                                    {{-- =================================================================== --}}
                                    {{-- ✅ LEVEL 3: RECURSIVE DROP AREA (Column Content) --}}
                                    {{-- =================================================================== --}}


                                    <div class="block-container min-h-[50px] space-y-4"
                                        :data-sortable-container="`{{ $parentPath }}[${blockIndex}].columns[${colIndex}].blocks`">

                                        {{-- Loop through blocks (Level 3) --}}
                                        <template x-for="(childBlock, childIndex) in col.blocks" :key="childBlock.id">
                                            <div class="relative p-4 transition border rounded-lg bg-gray-50 hover:shadow-md group"
                                                :data-id="childBlock.id">

                                                {{-- 🧰 Block Controls (Level 3) --}}
                                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-gray-700 cursor-grab"
                                                            x-text="availableBlocks.find(b => b.type === childBlock.type)?.label || childBlock.type"></span>
                                                    </div>

                                                    <div
                                                        class="flex flex-wrap items-center gap-2 max-sm:w-full max-sm:justify-end">
                                                        <button
                                                            @click="moveBlockUp(`{{ $parentPath }}[${blockIndex}].columns[${colIndex}].blocks`, childIndex)"
                                                            :disabled="childIndex === 0"
                                                            class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↑</button>

                                                        <button
                                                            @click="moveBlockDown(`{{ $parentPath }}[${blockIndex}].columns[${colIndex}].blocks`, childIndex)"
                                                            :disabled="childIndex === col.blocks.length - 1"
                                                            class="px-2 py-1 text-sm bg-white border rounded disabled:opacity-50">↓</button>

                                                        <button
                                                            @click="duplicateBlock(`{{ $parentPath }}[${blockIndex}].columns[${colIndex}].blocks`, childIndex)"
                                                            class="px-2 py-1 text-sm bg-white border rounded">⧉</button>

                                                        <button
                                                            @click="confirmRemove(`{{ $parentPath }}[${blockIndex}].columns[${colIndex}].blocks`, childIndex)"
                                                            class="px-2 py-1 text-sm text-red-600 bg-white border rounded">✖</button>
                                                    </div>
                                                </div>

                                                <hr class="mb-4">

                                                {{-- =================================== --}}
                                                {{-- 🧩 BLOCK-SPECIFIC SETTINGS (Level 3) --}}
                                                {{-- =================================== --}}

                                                {{-- ⭐️ ENHANCED: 'intro' block --}}

                                                <template x-if="childBlock.type === 'intro'">
                                                    <div class="space-y-4">
                                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                            <div>
                                                                <label
                                                                    class="text-sm font-medium text-gray-600">Layout</label>
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
                                                                <input type="text" x-model="childBlock.image"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="https://...">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="text-sm font-medium text-gray-600">Heading</label>
                                                                <input type="text" x-model="childBlock.heading"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="Block Heading">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="text-sm font-medium text-gray-600">Content</label>
                                                                <textarea x-model="childBlock.content"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded">
                                                                    </textarea>
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-600">Button
                                                                    Text</label>
                                                                <input type="text" x-model="childBlock.buttonText"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="Learn More">
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-600">Button
                                                                    Link</label>
                                                                <input type="text" x-model="childBlock.buttonLink"
                                                                    @input="pushHistoryDebounced"
                                                                    class="w-full p-2 border rounded"
                                                                    placeholder="/about-us">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: 'Gallery Block Settings' block --}}
                                                <template x-if="childBlock.type === 'gallery'">
                                                    <div class="grid grid-cols-1 gap-3">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: 'Testimonials Block Settings' --}}
                                                <template x-if="childBlock.type === 'testimonials'">
                                                    <div class="grid grid-cols-1 gap-3">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: '' announcment block --}}
                                                <template x-if="childBlock.type === 'announcements'">
                                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Content
                                                                Type</label>
                                                            <select x-model="childBlock.content_type"
                                                                @change="pushHistoryDebounced"
                                                                class="w-full p-2 bg-white border rounded">
                                                                <option value="student">Student Corner</option>
                                                                <option value="faculty">Faculty Corner</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Display
                                                                Count</label>
                                                            <input type="number" min="1"
                                                                x-model.number="childBlock.display_count"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: Academic Calendar Block Settings --}}
                                                <template x-if="childBlock.type === 'academic_calendar'">
                                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                        <div class="sm:col-span-2">
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Item
                                                                Count</label>
                                                            <input type="number" min="1"
                                                                x-model.number="childBlock.item_count"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: 'Events' block --}}

                                                <template x-if="childBlock.type === 'events'">
                                                    <div class="grid grid-cols-1 gap-3">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Description</label>
                                                            <textarea x-model="childBlock.section_description"
                                                                @input="pushHistoryDebounced" rows="3"
                                                                class="w-full p-2 border rounded"></textarea>
                                                        </div>
                                                    </div>
                                                </template>
                                                {{-- ⭐️ ENHANCED: 'sectionLinks' block --}}
                                                <template x-if="childBlock.type === 'sectionLinks'">
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label
                                                                class="text-sm font-medium text-gray-600">Title</label>
                                                            <input type="text" x-model="childBlock.title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded"
                                                                placeholder="Section Title">
                                                        </div>
                                                        {{-- NEW: Repeater for links (Level 3) --}}
                                                        <div class="p-3 space-y-3 border rounded bg-gray-100/50">
                                                            <label
                                                                class="text-sm font-medium text-gray-600">Links</label>
                                                            <template x-for="(link, linkIndex) in childBlock.links"
                                                                :key="linkIndex">
                                                                <div
                                                                    class="grid grid-cols-1 gap-2 p-2 border rounded bg-white sm:grid-cols-2 sm:gap-4">
                                                                    <input type="text" x-model="link.text"
                                                                        @input="pushHistoryDebounced"
                                                                        class="w-full p-2 text-sm border rounded"
                                                                        placeholder="Link Text">
                                                                    <div class="flex gap-2">
                                                                        <input type="text" x-model="link.url"
                                                                            @input="pushHistoryDebounced"
                                                                            class="w-full p-2 text-sm border rounded"
                                                                            placeholder="Link URL (e.g., /page)">
                                                                        <button
                                                                            @click="childBlock.links.splice(linkIndex, 1); pushHistory();"
                                                                            class="px-2 text-red-500 border rounded bg-white hover:bg-red-50">✖</button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <button
                                                                @click="childBlock.links.push({ text: 'New Link', url: '#' }); pushHistory();"
                                                                class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                                                + Add Link
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>


                                                {{-- Why Choose Us Block Settings --}}
                                                <template x-if="childBlock.type === 'why_choose_us'">
                                                    <div class="grid grid-cols-1 gap-3">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Title</label>
                                                            <input type="text" x-model="childBlock.section_title"
                                                                @input="pushHistoryDebounced"
                                                                class="w-full p-2 border rounded">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Section
                                                                Description</label>
                                                            <textarea x-model="childBlock.section_description"
                                                                @input="pushHistoryDebounced" rows="3"
                                                                class="w-full p-2 border rounded"></textarea>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- 'divider' block --}}
                                                <template x-if="childBlock.type === 'divider'">
                                                    <hr class="my-4 border-gray-300 border-dashed">
                                                </template>

                                            </div>
                                        </template>
                                    </div>
                                    {{-- ============================================= --}}
                                    {{-- ✅ END OF LEVEL 3 RECURSIVE DROP AREA --}}
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
