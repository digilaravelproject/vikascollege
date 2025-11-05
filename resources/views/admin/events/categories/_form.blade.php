@props(['category' => null])

<div class="space-y-6" x-data="categoryFormData({
        name: '{{ old('name', $category?->name ?? '') }}',
        slug: '{{ old('slug', $category?->slug ?? '') }}'
     })" x-init="init()">

    {{-- Main Card --}}
    <div class="p-6 bg-white shadow-lg rounded-2xl">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- Name --}}
            <div class="md:col-span-2">
                <label for="name" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" x-model="name" placeholder="Enter category name" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            {{-- Slug --}}
            <div class="md:col-span-2">
                <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Slug (auto-generated)
                </label>
                <input type="text" id="slug" name="slug" x-model="slug" placeholder="category-name" class="w-full px-3 py-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1.5 text-xs text-gray-500">Auto-fills from name. You can edit manually.</p>
            </div>

        </div>
    </div>

    {{-- SEO Section --}}
    <div class="p-6 bg-white shadow-lg rounded-2xl">
        <h3 class="text-lg font-semibold text-gray-800">SEO Meta</h3>
        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">

            {{-- Meta Title --}}
            <div>
                <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Meta Title
                </label>
                <input type="text" id="meta_title" name="meta_title"
                    value="{{ old('meta_title', $category?->meta_title) }}" placeholder="Enter meta title" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Meta Description --}}
            <div>
                <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Meta Description
                </label>
                <input type="text" id="meta_description" name="meta_description"
                    value="{{ old('meta_description', $category?->meta_description) }}"
                    placeholder="Enter short meta description" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

        </div>
    </div>
</div>

{{-- Alpine Logic --}}
<script>
    function categoryFormData(initialData) {
        return {
            name: initialData.name || '',
            slug: initialData.slug || '',
            userEditedSlug: false,

            init() {
                this.$watch('name', (val) => {
                    if (!this.userEditedSlug) {
                        this.slug = this.slugify(val);
                    }
                });
                this.$watch('slug', (val) => {
                    if (val !== this.slugify(this.name)) {
                        this.userEditedSlug = true;
                    }
                });
            },

            slugify(text) {
                if (!text) return '';
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            },
        };
    }
</script>
