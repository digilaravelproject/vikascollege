@extends('layouts.admin.app')

@section('title', 'Edit Page')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Page</h1>
                {{-- MODIFIED: Subtitle for context --}}
                <p class="mt-1 text-sm text-gray-500">Make changes to your page: <strong
                        class="font-medium text-gray-700">{{ $page->title }}</strong></p>
            </div>
            <a href="{{ route('admin.pagebuilder.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to All Pages
            </a>
        </div>

        {{-- MODIFIED: Added professional alert/validation blocks --}}
        @if (session('success'))
            <div class="flex p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="bi bi-check-circle-fill me-3"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                <div>
                    <span class="font-medium">Please fix the following errors:</span>
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- MODIFIED: Added x-data for Alpine.js --}}
        <form action="{{ route('admin.pagebuilder.update', $page) }}" method="POST" enctype="multipart/form-data"
            class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl" x-data="pageForm()">
            @csrf
            @method('PUT') {{-- ADDED: Method for updates --}}

            {{-- Main form content area --}}
            <div class="p-6 space-y-6">
                {{-- MODIFIED: Added responsive 2-column grid --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="title" class="block mb-1.5 text-sm font-medium text-gray-700">Page Title <span
                                class="text-red-500">*</span></label>
                        {{-- MODIFIED: Added x-model and @input --}}
                        <input type="text" id="title" name="title" required
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            x-model="title" @input="generateSlug()">
                    </div>

                    <div>
                        <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">Slug</label>
                        {{-- MODIFIED: Added x-model and @input --}}
                        <input type="text" id="slug" name="slug"
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            x-model="slug" @input="slugManuallyEdited = true">
                        <p class="mt-1.5 text-xs text-gray-500">Caution: Changing this may break existing links.</p>
                    </div>
                </div>

                {{-- ADDED: 'hidden' attribute to this div to hide all fields inside --}}
                <div hidden>
                    <div>
                        <label for="content" class="block mb-1.5 text-sm font-medium text-gray-700">Content</label>
                        <textarea id="content" name="content" rows="6"
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('content', $page->content) }}</textarea>
                    </div>

                    @if($page->image)
                        <div class="mt-3">
                            <p class="mb-1 text-sm text-gray-600">Current Image:</p>
                            <img src="{{ asset('storage/' . $page->image) }}" alt="" class="w-40 border rounded-lg">
                        </div>
                    @endif
                    <div>
                        <label class="block mb-1.5 font-medium text-gray-700">Replace Image</label>
                        <input type="file" name="image"
                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2 file:me-3 file:text-gray-700 file:font-medium">
                    </div>

                    @if($page->pdf)
                        <div class="mt-3">
                            <p class="mb-1 text-sm text-gray-600">Current PDF:</p>
                            <a href="{{ asset('storage/' . $page->pdf) }}" target="_blank"
                                class="text-blue-600 hover:underline">View PDF</a>
                        </div>
                    @endif
                    <div>
                        <label class="block mb-1.5 font-medium text-gray-700">Replace PDF</label>
                        <input type="file" name="pdf" accept="application/pdf"
                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2 file:me-3 file:text-gray-700 file:font-medium">
                    </div>
                </div>
            </div>

            {{-- Form footer with buttons --}}
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 gap-x-3">
                <a href="{{ route('admin.pagebuilder.index') }}"
                    class="text-sm font-medium text-gray-600 transition hover:underline">Cancel</a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-check-circle me-2"></i>
                    Update Page
                </button>
            </div>
        </form>
    </div>

    {{-- ADDED: Alpine.js script for this form --}}
    <script>
        function pageForm() {
            return {
                title: @json(old('title', $page->title)),
                slug: @json(old('slug', $page->slug)),

                // On an edit page, we lock the slug by default
                // to prevent accidentally breaking links.
                slugManuallyEdited: true,

                // The function to generate a slug
                slugify(text) {
                    if (!text) return '';
                    return text.toString().toLowerCase().trim()
                        .replace(/\s+/g, '-')       // Replace spaces with -
                        .replace(/[^\w\-]+/g, '')   // Remove all non-word chars
                        .replace(/\-\-+/g, '-');    // Replace multiple - with single -
                },

                // The event handler for the title input
                generateSlug() {
                    // It will only run if the user *manually* unlocks it
                    // (e.g., by clearing the slug field, which would require more logic)
                    // For safety, this is disabled by default on edit.
                    if (!this.slugManuallyEdited) {
                        this.slug = this.slugify(this.title);
                    }
                }
            };
        }
    </script>
@endsection