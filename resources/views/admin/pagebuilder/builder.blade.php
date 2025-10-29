@extends('layouts.admin.app')

@section('content')
    <div x-data="pageBuilder()" class="min-h-screen p-6 bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Page Builder — {{ $page->title }}</h1>

            <button @click="savePage" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                💾 Save Page
            </button>
        </div>

        <!-- Toolbox -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-3 p-4 bg-white rounded-lg shadow">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">Blocks</h2>
                <template x-for="block in availableBlocks" :key="block.type">
                    <div draggable="true" @dragstart="dragBlock($event, block)"
                        class="p-2 mb-2 transition border border-gray-200 rounded cursor-grab hover:bg-blue-50">
                        <span x-text="block.label"></span>
                    </div>
                </template>
            </div>

            <!-- Canvas -->
            <div class="col-span-9 bg-white p-6 rounded-lg shadow min-h-[70vh]" @dragover.prevent @drop="dropBlock">


                <template x-if="blocks.length === 0">
                    <p class="mt-20 text-center text-gray-400">Drag elements here to start building your page ✨</p>
                </template>

                <template x-for="(block, index) in blocks" :key="index">
                    <div class="relative p-4 mb-4 border rounded-lg bg-gray-50 group">
                        <!-- Delete -->
                        <button @click="removeBlock(index)"
                            class="absolute text-xs text-red-600 transition opacity-0 top-2 right-2 group-hover:opacity-100">
                            ✖
                        </button>

                        <template x-if="block.type === 'heading'">
                            <input type="text" x-model="block.content" placeholder="Enter heading"
                                class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0" />
                        </template>

                        <template x-if="block.type === 'text'">
                            <textarea x-model="block.content" placeholder="Enter paragraph text"
                                class="w-full text-gray-700 bg-transparent border-none focus:ring-0"></textarea>
                        </template>

                        <template x-if="block.type === 'image'">
                            <div>
                                <input type="text" x-model="block.src" placeholder="Image URL"
                                    class="w-full p-2 mb-2 text-sm border rounded" />
                                <template x-if="block.src">
                                    <img :src="block.src" class="object-cover rounded-lg shadow-md max-h-60" />
                                </template>
                            </div>
                        </template>

                        <template x-if="block.type === 'video'">
                            <div>
                                <input type="text" x-model="block.src" placeholder="YouTube Embed URL"
                                    class="w-full p-2 mb-2 text-sm border rounded" />
                                <iframe x-if="block.src" :src="block.src" class="w-full rounded-lg h-60"></iframe>
                            </div>
                        </template>

                        <template x-if="block.type === 'pdf'">
                            <div>
                                <input type="text" x-model="block.src" placeholder="PDF file URL"
                                    class="w-full p-2 mb-2 text-sm border rounded" />
                                <iframe x-if="block.src" :src="block.src" class="w-full border rounded h-72"></iframe>
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
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function pageBuilder() {
            return {
                availableBlocks: [
                    { type: 'heading', label: '🧱 Heading', content: '' },
                    { type: 'text', label: '📝 Text', content: '' },
                    { type: 'image', label: '🖼️ Image', src: '' },
                    { type: 'video', label: '🎥 Video', src: '' },
                    { type: 'pdf', label: '📄 PDF', src: '' },
                ],
                blocks: @json(json_decode($page->content ?? '[]')),
                dragged: null,

                // Start dragging
                dragBlock(event, block) {
                    this.dragged = JSON.parse(JSON.stringify(block));
                    event.dataTransfer.effectAllowed = 'move';
                },

                // Drop block
                dropBlock(event) {
                    event.preventDefault();
                    if (this.dragged) {
                        this.blocks.push(this.dragged);
                        this.dragged = null;
                    }
                },

                removeBlock(i) { this.blocks.splice(i, 1); },

                savePage() {
                    const json = JSON.stringify(this.blocks);
                    document.getElementById('pageContent').value = json;
                    document.getElementById('saveForm').submit();
                }
            }
        }
    </script>

@endsection