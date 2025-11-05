@props(['item' => null])

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div x-data="itemFormData({
        initialPreview: '{{ $item?->icon_or_image ? asset('storage/' . $item->icon_or_image) : '' }}'
    })" class="p-6 md:p-8 bg-white rounded-2xl shadow-xl border border-gray-100">

    {{-- Responsive Grid Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-[minmax(120px,220px)_1fr] gap-6 md:gap-8 items-start md:items-center">

        {{-- LEFT: Image Upload --}}
        <div class="flex flex-col justify-start md:justify-center">
            <label class="block mb-3 text-sm font-semibold text-gray-800">
                Icon / Image
            </label>

            {{-- Upload Zone --}}
            <div class="group relative flex items-center justify-center
                w-full max-w-[220px] md:max-w-[240px] lg:max-w-[260px]
                aspect-square rounded-xl transition-all duration-300
                border-2 border-dashed border-gray-300 bg-gray-50
                hover:border-blue-500 hover:bg-gray-100">

                {{-- Empty State --}}
                <template x-if="!imagePreview">
                    <div class="flex flex-col items-center justify-center text-center p-4 cursor-pointer"
                        @click="$refs.imageInput.click()">
                        <i class="bi bi-image text-4xl text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        <p class="mt-2 text-xs font-medium text-gray-600 group-hover:text-blue-700">
                            Upload image or icon
                        </p>
                        <p class="mt-1 text-[11px] text-gray-400">1:1 ratio recommended</p>
                    </div>
                </template>

                {{-- Preview State --}}
                <template x-if="imagePreview">
                    <div class="relative w-full h-full">
                        <img :src="imagePreview"
                            class="absolute inset-0 w-full h-full object-cover rounded-xl shadow-sm border border-gray-200"
                            alt="Image Preview">

                        {{-- Hover Overlay with Actions --}}
                        <div class="absolute inset-0 w-full h-full rounded-xl bg-black/60
                            flex items-center justify-center gap-2 opacity-0
                            group-hover:opacity-100 transition-opacity duration-300">

                            {{-- Change --}}
                            <button type="button" @click="$refs.imageInput.click()"
                                class="flex items-center gap-1.5 px-3 py-1 bg-white/90 text-gray-900 rounded-lg shadow hover:bg-white transition-all text-xs font-medium">
                                <i class="bi bi-arrow-repeat"></i> Change
                            </button>

                            {{-- Remove --}}
                            <button type="button" @click="clearImage()"
                                class="flex items-center gap-1.5 px-3 py-1 bg-red-600/90 text-white rounded-lg shadow hover:bg-red-700 transition-all text-xs font-medium">
                                <i class="bi bi-trash-fill"></i> Remove
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Hidden File Input --}}
                <input type="file" name="icon_or_image" id="icon_or_image" accept="image/*" x-ref="imageInput"
                    @change="previewImage($event)" class="hidden" />
            </div>
        </div>

        {{-- RIGHT: Form Fields --}}
        <div class="space-y-6">
            {{-- Title --}}
            <div>
                <label for="title" class="block mb-2 text-sm font-semibold text-gray-800">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $item?->title) }}"
                    placeholder="Enter item title" class="w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    required>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block mb-2 text-sm font-semibold text-gray-800">
                    Description
                </label>
                <textarea id="description" name="description" rows="6" placeholder="Enter item description..."
                    class="w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">{{ old('description', $item?->description) }}</textarea>
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block mb-2 text-sm font-semibold text-gray-800">
                    Sort Order
                </label>
                <input type="number" id="sort_order" name="sort_order"
                    value="{{ old('sort_order', $item?->sort_order ?? 0) }}" placeholder="Enter display order" class="w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>
        </div>

    </div>
</div>

{{-- Alpine.js Logic --}}
<script>
    function itemFormData(config) {
        return {
            imagePreview: config.initialPreview || '',

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => this.imagePreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            clearImage() {
                this.imagePreview = '';
                this.$refs.imageInput.value = null;
            }
        }
    }
</script>
