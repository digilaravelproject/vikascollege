@extends('layouts.admin.app')

@section('title', 'Create New Page')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Page</h1>
                <p class="mt-1 text-sm text-gray-500">Fill in the details to create a new page.</p>
            </div>
            <a href="{{ route('admin.pagebuilder.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to All Pages
            </a>
        </div>

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

        {{-- MODIFIED: Added x-data="pageForm()" to initialize Alpine --}}
        <form action="{{ route('admin.pagebuilder.store') }}" method="POST" enctype="multipart/form-data"
            class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl" x-data="pageForm()">
            @csrf

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="title" class="block mb-1.5 text-sm font-medium text-gray-700">Page Title <span
                                class="text-red-500">*</span></label>
                        {{--
                        MODIFIED:
                        - Removed value=""
                        - Added x-model="title" to bind to Alpine
                        - Added @input="generateSlug()" to update slug on keypress
                        --}}
                        <input type="text" id="title" name="title" required
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            x-model="title" @input="generateSlug()">
                    </div>

                    <div>
                        <label for="slug" class="block mb-1.5 text-sm font-medium text-gray-700">Slug</label>
                        {{--
                        MODIFIED:
                        - Removed value=""
                        - Added x-model="slug" to bind to Alpine
                        - Added @input="slugManuallyEdited = true" to stop auto-generation
                        --}}
                        <input type="text" id="slug" name="slug"
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            x-model="slug" @input="slugManuallyEdited = true">
                        <p class="mt-1.5 text-xs text-gray-500">Will be auto-generated from the title if left empty.</p>
                    </div>
                </div>

                <div hidden>
                    <div>
                        <label for="content" class="block mb-1.5 text-sm font-medium text-gray-700">Content (for builder
                            JSON or HTML)</label>
                        <textarea id="content" name="content" rows="6"
                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('content') }}</textarea>
                    </div>

                    <div>
                        <label for="image" class="block mb-1.5 text-sm font-medium text-gray-700">Feature Image</label>
                        <input type="file" id="image" name="image"
                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2 file:me-3 file:text-gray-700 file:font-medium">
                    </div>

                    <div>
                        <label for="pdf" class="block mb-1.5 text-sm font-medium text-gray-700">Attach PDF</label>
                        <input type="file" id="pdf" name="pdf" accept="application/pdf"
                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2 file:me-3 file:text-gray-700 file:font-medium">
                    </div>
                </div>
            </div>

            {{-- Form footer with buttons --}}
            <div class="px-6 py-4 text-right border-t border-gray-200 bg-gray-50">
                <a href="{{ route('admin.pagebuilder.index') }}"
                    class="text-sm font-medium text-gray-600 transition hover:underline">Cancel</a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-check-circle me-2"></i>
                    Save Page
                </button>
            </div>
        </form>
    </div>

    {{-- ADDED: Alpine.js script for this form --}}
    <script>
        function pageForm() {
            return {
                title: @json(old('title', '')),
                slug: @json(old('slug', '')),
                // If there is an 'old' slug (from validation error), lock the slug
                slugManuallyEdited: @json(old('slug') ? true : false),

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
                    if (!this.slugManuallyEdited) {
                        this.slug = this.slugify(this.title);
                    }
                }
            };
        }
    </script>
@endsection