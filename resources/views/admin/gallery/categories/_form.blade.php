@props(['category' => null])

<div class="space-y-6" x-data="categoryForm({
        name: '{{ old('name', $category?->name ?? '') }}',
        slug: '{{ old('slug', $category?->slug ?? '') }}',
        meta_title: '{{ old('meta_title', $category?->meta_title ?? '') }}',
        meta_description: '{{ old('meta_description', $category?->meta_description ?? '') }}'
     })" x-init="init()">

    {{-- Main Card --}}
    <div class="p-6 bg-white rounded-2xl shadow-lg space-y-6">

        {{-- Name --}}
        <div>
            <label for="name" class="block mb-1.5 text-sm font-medium text-gray-700">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name" x-model="name" placeholder="Enter category name" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        {{-- Slug --}}
        <div>
            <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">
                Slug
            </label>
            <input type="text" id="slug" name="slug" x-model="slug" placeholder="Auto-generated if left blank" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500">Slug will auto-generate from name.</p>
        </div>

        {{-- Meta Title --}}
        <div>
            <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">
                Meta Title
            </label>
            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $category?->meta_title) }}"
                placeholder="Enter SEO title" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Meta Description --}}
        <div>
            <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">
                Meta Description
            </label>
            <input type="text" id="meta_description" name="meta_description"
                value="{{ old('meta_description', $category?->meta_description) }}" placeholder="Enter SEO description"
                class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

    </div>
</div>

{{-- Alpine.js Logic --}}
<script>
    function categoryForm(initialData) {
        return {
            name: initialData.name || '',
            slug: initialData.slug || '',
            userEditedSlug: false,
            init() {
                this.$watch('name', (value) => {
                    if (!this.userEditedSlug) {
                        this.slug = this.slugify(value);
                    }
                });
                this.$watch('slug', (value) => {
                    if (value !== this.slugify(this.name)) {
                        this.userEditedSlug = true;
                    }
                });
            },
            slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }
        };
    }
</script>
