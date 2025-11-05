@props(['testimonial' => null])

{{-- Bootstrap Icons CDN (for uploader icons) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="space-y-6" x-data="testimonialFormData({
    initialPreview: '{{ $testimonial?->student_image ? asset('storage/' . $testimonial->student_image) : '' }}',
    initialStatus: {{ old('status', $testimonial?->status ?? false) ? 'true' : 'false' }}
})">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Content (Left Column) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-lg space-y-6">

                {{-- Student Name --}}
                <div>
                    <label for="student_name" class="block mb-1.5 text-sm font-medium text-gray-700">
                        Student Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="student_name" name="student_name"
                        value="{{ old('student_name', $testimonial?->student_name) }}"
                        placeholder="Enter student's full name" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                {{-- Testimonial Text --}}
                <div>
                    <label for="testimonial_text" class="block mb-1.5 text-sm font-medium text-gray-700">
                        Testimonial <span class="text-red-500">*</span>
                    </label>
                    <textarea id="testimonial_text" name="testimonial_text" rows="8"
                        placeholder="Enter student's testimonial..."
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg
                                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">{{ old('testimonial_text', $testimonial?->testimonial_text) }}</textarea>
                </div>

            </div>
        </div>

        {{-- Sidebar (Right Column) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Student Image Upload Card --}}
            <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-lg space-y-4">
                <label class="block text-sm font-medium text-gray-700">
                    Student Image
                </label>

                <div class="flex items-center gap-4">
                    {{-- Uploader/Preview Container (Size Reduced) --}}
                    <div class="relative group w-24 h-24 flex-shrink-0">

                        {{-- Empty State (No Image) --}}
                        <template x-if="!imagePreview">
                            <div @click="$refs.imageInput.click()"
                                class="w-full h-full rounded-full border-2 border-dashed border-gray-300 bg-gray-50
                                        flex items-center justify-center cursor-pointer hover:bg-gray-100 hover:border-blue-400 transition-all">
                                <i class="bi bi-person-bounding-box text-3xl text-gray-400 transition-colors"></i>
                            </div>
                        </template>

                        {{-- Preview State (With Image) --}}
                        <template x-if="imagePreview">
                            <div class="relative w-full h-full">
                                {{-- Image Preview (Size Reduced) --}}
                                <img :src="imagePreview"
                                    class="w-24 h-24 object-cover rounded-full shadow-md border-2 border-white ring-1 ring-gray-200"
                                    alt="Student Preview">
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 rounded-full bg-black/60 flex items-center justify-center gap-3
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    {{-- Change Button --}}
                                    <button type="button" @click="$refs.imageInput.click()"
                                        class="w-8 h-8 flex items-center justify-center bg-white/90 text-gray-900 rounded-full shadow-md hover:bg-white transition-all">
                                        <i class="bi bi-arrow-repeat text-sm"></i>
                                    </button>
                                    {{-- Remove Button --}}
                                    <button type="button" @click="clearImage()"
                                        class="w-8 h-8 flex items-center justify-center bg-red-600/90 text-white rounded-full shadow-md hover:bg-red-700 transition-all">
                                        <i class="bi bi-trash-fill text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Helper Text --}}
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">
                            Recommended: 1:1 ratio (square).
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            PNG, JPG, WEBP. Max 2MB.
                        </p>
                    </div>
                </div>
                {{-- Hidden File Input --}}
                <input type="file" name="student_image" id="student_image" accept="image/*" x-ref="imageInput"
                    @change="previewImage($event)" class="hidden">
            </div>

            {{-- Status Toggle Card --}}
            <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-lg">
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
                {{-- Hidden input to send the 1 or 0 value --}}
                <input type="hidden" name="status" :value="approved ? 1 : 0">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" x-model="approved">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-2 peer-focus:ring-blue-500
                                peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:border after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-5">
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700"
                        x-text="approved ? 'Approved' : 'Pending'"></span>
                </label>
            </div>

        </div>
    </div>
</div>

{{-- Alpine.js Logic --}}
<script>
    function testimonialFormData(config) {
        return {
            imagePreview: config.initialPreview || '',
            approved: config.initialStatus || false,

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
                this.$refs.imageInput.value = null; // Use null to clear
            }
        }
    }
</script>
