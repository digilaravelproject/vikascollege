@props(['item' => null, 'categories' => []])

{{-- Flatpickr Styles --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

{{-- Alpine component --}}
<div class="space-y-6" x-data="eventFormData({
        title: '{{ old('title', $item?->title ?? '') }}',
        slug: '{{ old('slug', $item?->slug ?? '') }}',
        status: {{ old('status', $item?->status ?? true) ? 'true' : 'false' }},
        imagePreview: '{{ $item?->image ? Storage::url($item->image) : '' }}'
    })" x-init="init()">

    {{-- Main Content Card --}}
    <div class="p-6 bg-white shadow-lg rounded-2xl">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

            {{-- Main Content (2/3 width) --}}
            <div class="space-y-6 md:col-span-2">

                {{-- Title --}}
                <div>
                    <label for="title" class="block mb-1.5 text-sm font-medium text-gray-700">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" x-model="title" placeholder="Enter event title"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                {{-- Short Description --}}
                <div>
                    <label for="short_description" class="block mb-1.5 text-sm font-medium text-gray-700">Short
                        Description</label>
                    <textarea id="short_description" name="short_description" rows="3"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('short_description', $item?->short_description) }}</textarea>
                </div>

                {{-- Full Content --}}
                <div>
                    <label for="full_content" class="block mb-1.5 text-sm font-medium text-gray-700">Full
                        Content</label>
                    <input type="hidden" name="full_content" id="full_content_input"
                        value="{{ old('full_content', $item?->full_content) }}">
                    <div id="quill-editor" style="min-height: 250px;"
                        class="bg-white rounded-lg border border-gray-300">
                        {!! old('full_content', $item?->full_content) !!}
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/3 width) --}}
            <div class="space-y-6 md:col-span-1">

                {{-- Status Toggle --}}
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700"
                            x-text="status ? 'Published' : 'Draft'"></span>
                    </label>
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block mb-1.5 text-sm font-medium text-gray-700">Category <span
                            class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ old('category_id', $item?->category_id) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Event Date (UPDATED) --}}
                <div>
                    <label for="event_date" class="block mb-1.5 text-sm font-medium text-gray-700">Event Date &
                        Time</label>
                    {{-- Changed type="datetime-local" to "text" and added x-init to call Flatpickr --}}
                    <input type="text" id="event_date" name="event_date" {{-- Format changed from 'Y-m-d\TH:i'
                        to 'Y-m-d H:i' to work with Flatpickr --}}
                        value="{{ old('event_date', optional($item?->event_date)->format('Y-m-d H:i')) }}"
                        x-init="initFlatpickr($el)"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select date and time">
                </div>

                {{-- Venue --}}
                <div>
                    <label for="venue" class="block mb-1.5 text-sm font-medium text-gray-700">Venue</label>
                    <input type="text" id="venue" name="venue" value="{{ old('venue', $item?->venue) }}"
                        placeholder="Enter venue"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Image Upload & Preview --}}
                <div>
                    <label for="image" class="block mb-1.5 text-sm font-medium text-gray-700">Featured Image</label>
                    <input type="file" id="image" name="image" accept="image/*" @change="previewImage($event)"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors cursor-pointer">
                    <template x-if="imagePreview">
                        <img :src="imagePreview"
                            class="object-cover w-full h-32 mt-3 rounded-lg shadow-sm border border-gray-200">
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- SEO Fields --}}
    <div class="p-6 bg-white shadow-lg rounded-2xl">
        <h3 class="text-lg font-semibold text-gray-800">SEO Meta</h3>
        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">

            {{-- Slug --}}
            <div>
                <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">Slug (auto-generated)</label>
                <input type="text" id="slug" name="slug" x-model="slug"
                    class="w-full px-3 py-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1.5 text-xs text-gray-500">Auto-fills from title. You can edit manually.</p>
            </div>

            {{-- Meta Title --}}
            <div>
                <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $item?->meta_title) }}"
                    class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Meta Description --}}
            <div class="md:col-span-2">
                <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">Meta
                    Description</label>
                <input type="text" id="meta_description" name="meta_description"
                    value="{{ old('meta_description', $item?->meta_description) }}"
                    class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>
</div>

{{-- Flatpickr JS --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Alpine.js logic --}}
<script>
    function eventFormData(initialData) {
        return {
            title: initialData.title || '',
            slug: initialData.slug || '',
            status: initialData.status ?? true,
            imagePreview: initialData.imagePreview || '',
            userManuallyEditedSlug: false,

            init() {
                // Watch title changes
                this.$watch('title', (value) => {
                    if (!this.userManuallyEditedSlug) {
                        this.slug = this.slugify(value);
                    }
                });

                // Watch slug changes to detect manual edits
                this.$watch('slug', (value) => {
                    if (value !== this.slugify(this.title)) {
                        this.userManuallyEditedSlug = true;
                    }
                });
            },

            slugify(text) {
                if (!text) return '';
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                    .replace(/\-\-+/g, '-') // Replace multiple - with single -
                    .replace(/^-+/, '') // Trim - from start
                    .replace(/-+$/, ''); // Trim - from end
            },

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => this.imagePreview = e.target.result;
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
        }
    }
</script>
