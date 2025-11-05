@props(['item' => null])

{{-- Flatpickr Styles --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="space-y-6" x-data="calendarFormData({
        title: '{{ old('title', $item?->title ?? '') }}',
        slug: '{{ old('slug', $item?->slug ?? '') }}',
        status: {{ old('status', $item?->status ?? true) ? 'true' : 'false' }},
        imagePreview: '{{ $item?->image ? Storage::url($item->image) : '' }}'
    })" x-init="init()">

    {{-- Main Info --}}
    <div class="p-6 bg-white rounded-2xl shadow-lg">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- Title --}}
            <div class="md:col-span-2">
                <label for="title" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" x-model="title" placeholder="Enter event title" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Slug (auto-generated)
                </label>
                <input type="text" id="slug" name="slug" x-model="slug" placeholder="auto-generated-slug" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">You can edit this manually if needed.</p>
            </div>

            {{-- Date & Time (UPDATED) --}}
            <div>
                <label for="event_datetime" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Date & Time
                </label>
                {{-- Changed type to "text", added x-init and placeholder --}}
                <input type="text" id="event_datetime" name="event_datetime" {{-- Format changed from 'Y-m-d\TH:i'
                    to 'Y-m-d H:i' --}}
                    value="{{ old('event_datetime', optional($item?->event_datetime)->format('Y-m-d H:i')) }}"
                    x-init="initFlatpickr($el)" placeholder="Select date and time" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- External Link --}}
            <div>
                <label for="link_href" class="block mb-1.5 text-sm font-medium text-gray-700">
                    External Link (optional)
                </label>
                <input type="url" id="link_href" name="link_href" value="{{ old('link_href', $item?->link_href) }}"
                    placeholder="https://example.com" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Image --}}
            <div>
                <label for="image" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Image
                </label>
                <input type="file" id="image" name="image" accept="image/*" @change="previewImage($event)" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3
                            file:rounded-lg file:border-0 file:font-medium file:bg-blue-50
                            file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <template x-if="imagePreview">
                    <img :src="imagePreview"
                        class="object-cover w-full h-32 mt-3 rounded-lg border border-gray-200 shadow-sm">
                </template>
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label for="description" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea id="description" name="description" rows="6" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter detailed description...">{{ old('description', $item?->description) }}</textarea>
            </div>
        </div>
    </div>

    {{-- SEO + Status --}}
    <div class="p-6 bg-white rounded-2xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Meta & Status</h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Meta Title
                </label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $item?->meta_title) }}"
                    placeholder="Enter SEO meta title" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">
                    Meta Description
                </label>
                <input type="text" id="meta_description" name="meta_description"
                    value="{{ old('meta_description', $item?->meta_description) }}"
                    placeholder="Enter short SEO description" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="mt-6">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</LAbel>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-2 peer-focus:ring-blue-500
                            peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px]
                            after:left-[2px] after:bg-white after:border after:rounded-full after:h-5
                            after:w-5 after:transition-all peer-checked:after:translate-x-5">
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700" x-text="status ? 'Active' : 'Inactive'"></span>
            </label>
        </div>
    </div>
</div>

{{-- Flatpickr JS --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Alpine Logic --}}
<script>
    function calendarFormData(initial) {
        return {
            title: initial.title || '',
            slug: initial.slug || '',
            status: initial.status ?? true,
            imagePreview: initial.imagePreview || '',
            manualSlug: false,

            init() {
                this.$watch('title', val => {
                    if (!this.manualSlug) this.slug = this.slugify(val);
                });
                this.$watch('slug', val => {
                    if (val !== this.slugify(this.title)) this.manualSlug = true;
                });
            },
            slugify(text) {
                if (!text) return '';
                return text.toLowerCase().replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-')
                    .replace(/^-+/, '').replace(/-+$/, '');
            },
            previewImage(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = ev => this.imagePreview = ev.target.result;
                    reader.readAsDataURL(file);
                }
            },

            // --- NEW FUNCTION TO INITIALIZE FLATPICKR ---
            initFlatpickr(element) {
                flatpickr(element, {
                    enableTime: true,        // Enable time picker
                    dateFormat: "Y-m-d H:i", // Format sent to the server (matches input value)
                    altInput: true,          // Show a user-friendly format
                    altFormat: "F j, Y at h:i K", // e.g., "June 10, 2025 at 02:00 PM"
                    defaultDate: element.value  // Set the initial date from the input's value
                });
            }
        };
    }
</script>
