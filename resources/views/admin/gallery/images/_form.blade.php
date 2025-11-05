@props(['image' => null, 'categories' => []])

{{-- CDNs --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<div class="p-6 md:p-8 bg-white rounded-2xl shadow-xl border border-gray-100" x-data="imageUploaderData({
        initialPreview: '{{ $image?->image ? asset('storage/' . $image->image) : '' }}'
     })" x-init="init()">

    {{-- Responsive Grid: Left = Image, Right = Form --}}
    <div class="grid grid-cols-1 md:grid-cols-[minmax(160px,280px)_1fr] gap-6 md:gap-8 items-start md:items-center">

        {{-- LEFT: Image Upload --}}
        <div class="flex flex-col justify-start md:justify-center">
            <label class="block mb-3 text-sm font-semibold text-gray-800">
                Featured Image <span class="text-red-500">*</span>
            </label>

            <div class="group relative flex items-center justify-center
                        w-full max-w-[280px] aspect-video rounded-xl transition-all duration-300
                        border-2 border-dashed border-gray-300 bg-gray-50
                        hover:border-blue-500 hover:bg-gray-100">

                {{-- Empty State --}}
                <template x-if="!imagePreview">
                    <div class="flex flex-col items-center justify-center text-center p-4 cursor-pointer"
                        @click="$refs.imageInput.click()">
                        <i
                            class="bi bi-cloud-arrow-up text-4xl text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        <p class="mt-2 text-sm font-medium text-gray-600 group-hover:text-blue-700">Click to upload
                            image</p>
                        <p class="mt-1 text-xs text-gray-400">PNG, JPG, or WEBP (16:9 recommended)</p>
                    </div>
                </template>

                {{-- Preview State --}}
                <template x-if="imagePreview">
                    <div class="relative w-full h-full">
                        <img :src="imagePreview"
                            class="absolute inset-0 w-full h-full object-cover rounded-xl shadow-md border border-gray-200"
                            alt="Image Preview">

                        {{-- Hover Overlay Actions --}}
                        <div class="absolute inset-0 w-full h-full rounded-xl bg-black/60
                                    flex items-center justify-center gap-3 opacity-0
                                    group-hover:opacity-100 transition-opacity duration-300">

                            <button type="button" @click="$refs.imageInput.click()"
                                class="flex items-center gap-1.5 px-3 py-1 bg-white/90 text-gray-900 rounded-lg shadow hover:bg-white transition-all text-xs font-medium">
                                <i class="bi bi-arrow-repeat"></i> Change
                            </button>

                            <button type="button" @click="clearImage()"
                                class="flex items-center gap-1.5 px-3 py-1 bg-red-600/90 text-white rounded-lg shadow hover:bg-red-700 transition-all text-xs font-medium">
                                <i class="bi bi-trash-fill"></i> Remove
                            </button>
                        </div>
                    </div>
                </template>

                <input type="file" name="image" id="image" accept="image/*" x-ref="imageInput"
                    @change="previewImage($event)" class="hidden" />
            </div>
        </div>

        {{-- RIGHT: Form Fields --}}
        <div class="space-y-6">
            {{-- Category --}}
            <div>
                <label for="category_id" class="block mb-2 text-sm font-semibold text-gray-800">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category_id" id="category_id" x-ref="categorySelect" class="w-full" required>
                    <option value="">Select a category</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" {{ old('category_id', $image?->category_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block mb-2 text-sm font-semibold text-gray-800">
                    Title<span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $image?->title) }}"
                    placeholder="Enter image title or caption" class="w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    function imageUploaderData(config) {
        return {
            imagePreview: config.initialPreview || '',
            choicesInstance: null,

            init() {
                this.choicesInstance = new Choices(this.$refs.categorySelect, {
                    searchEnabled: true,
                    shouldSort: false,
                    itemSelectText: 'Press to select'
                });
            },

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

{{-- Styles to match Tailwind --}}
@push('styles')
    <style>
        .choices__inner {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            min-height: auto;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }

        .is-focused .choices__inner {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .choices__list--dropdown {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            margin-top: 4px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .choices__item--choice {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .choices__item--choice.is-highlighted {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .choices__placeholder {
            color: #6b7280;
            opacity: 1;
        }
    </style>
@endpush
