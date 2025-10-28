@extends('layouts.admin.app')
@section('title', 'Create Trust Section')

@section('content')
    <div class="max-w-5xl p-6 mx-auto bg-white shadow rounded-2xl">
        <h1 class="mb-5 text-xl font-semibold text-gray-800">Add New Section</h1>

        <form id="trustForm" action="{{ route('admin.trust.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            {{-- Slug --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required readonly>
            </div>

            {{-- Content --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Content (optional)</label>
                <div id="quillEditor" class="border border-gray-300 rounded-lg p-2.5" style="height:300px;"></div>
                <textarea name="content" id="editor" hidden>{{ old('content') }}</textarea>
            </div>

            {{-- PDF --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">PDF File (optional)</label>
                <input type="file" name="pdf" accept="application/pdf" class="block w-full text-sm text-gray-700">
            </div>

            {{-- Images --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Images (optional, multiple)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-700">
            </div>

            <button type="submit"
                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Create Section
            </button>
        </form>
    </div>

    @push('scripts')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const quill = new Quill('#quillEditor', {
                    theme: 'snow',
                    placeholder: 'Enter content here...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'image', 'code-block'],
                        ]
                    }
                });

                // Load old content
                const existingContent = @json(old('content', ''));
                quill.root.innerHTML = existingContent || '<p><br></p>';

                // Sync Quill with textarea on submit
                document.getElementById('trustForm').addEventListener('submit', function () {
                    const html = quill.root.innerHTML;
                    document.getElementById('editor').value = (html === '<p><br></p>') ? '' : html;
                });

                // Auto-generate slug
                const title = document.getElementById('title');
                const slug = document.getElementById('slug');
                title.addEventListener('input', function () {
                    slug.value = this.value.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim()
                        .replace(/\s+/g, '-');
                });
            });
        </script>
    @endpush
@endsection