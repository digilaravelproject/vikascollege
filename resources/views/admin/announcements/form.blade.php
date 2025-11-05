@props(['announcement' => null])

{{-- Alpine.js data scope for the status toggle --}}
<div class="p-6 space-y-6 bg-white shadow-lg rounded-2xl"
    x-data="{ status: @json(old('status', $announcement?->status ?? true)) ? true : false }">

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- Title --}}
        <div class="md:col-span-2">
            <label for="title" class="block mb-1.5 text-sm font-medium text-gray-700">Title <span
                    class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $announcement?->title) }}"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required>
        </div>

        {{-- Type --}}
        <div>
            <label for="type" class="block mb-1.5 text-sm font-medium text-gray-700">Type <span
                    class="text-red-500">*</span></label>
            @php
                $selectedType = old('type', $announcement?->type);
            @endphp
            <select id="type" name="type"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="student" {{ $selectedType === 'student' ? 'selected' : '' }}>Student Corner</option>
                <option value="faculty" {{ $selectedType === 'faculty' ? 'selected' : '' }}>Faculty Corner</option>
            </select>
        </div>

        {{-- Status Toggle --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                {{-- Copied styling from your menu form reference --}}
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border border-gray-300 after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600
                    transition-colors duration-300 ease-in-out">
                </div>
                {{-- Dynamic text label --}}
                <span class="ml-3 text-sm font-medium text-gray-700" x-text="status ? 'Published' : 'Draft'"></span>
            </label>
            <p class="mt-1.5 text-xs text-gray-500">Controls the visibility of this item on the site.</p>
        </div>

        {{-- Content --}}
        <div class="md:col-span-2">
            <label for="content" class="block mb-1.5 text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" name="content" rows="6"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('content', $announcement?->content) }}</textarea>
        </div>

        {{-- Meta Title --}}
        <div>
            <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title"
                value="{{ old('meta_title', $announcement?->meta_title) }}"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Meta Description --}}
        <div>
            <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">Meta
                Description</label>
            <input type="text" id="meta_description" name="meta_description"
                value="{{ old('meta_description', $announcement?->meta_description) }}"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
</div>
