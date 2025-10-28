@extends('layouts.admin.app')
@section('title', 'Edit Trust Section')

@section('content')
    <div class="max-w-5xl p-6 mx-auto bg-white shadow rounded-2xl">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-semibold text-gray-800">Edit Section: {{ $trustSection->title }}</h1>
            <a href="{{ route('admin.trust.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow hover:bg-gray-200">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        @if(session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
        @endif

        <form id="trustForm" action="{{ route('admin.trust.update', $trustSection->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-5">
                <label class="block mb-1 text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $trustSection->title) }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            {{-- Slug --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $trustSection->slug) }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required readonly>
            </div>

            {{-- Content --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Content (optional)</label>
                <div id="quillEditor" class="border border-gray-300 rounded-lg p-2.5" style="height:300px;"></div>
                <textarea name="content" id="editor" hidden>{{ old('content', $trustSection->content) }}</textarea>
            </div>

            {{-- PDF --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">PDF File (optional)</label>
                @if($trustSection->pdf_path)
                    <div class="flex justify-between p-3 mb-2 border rounded-lg bg-gray-50">
                        <a href="{{ asset('storage/' . $trustSection->pdf_path) }}" target="_blank"
                            class="text-sm font-medium text-blue-600 hover:underline">📄 View Current PDF</a>
                        <form action="{{ route('admin.trust.pdf.remove', $trustSection->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                        </form>
                    </div>
                @endif
                <input type="file" name="pdf" accept="application/pdf" class="block w-full text-sm text-gray-700">
            </div>

            {{-- Images --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Images (optional, multiple)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-700">
            </div>

            {{-- Existing Images --}}
            @if($trustSection->images->count())
                <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($trustSection->images as $img)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                class="object-cover w-full h-40 rounded-lg shadow-md">
                            <form action="{{ route('admin.trust.image.destroy', $img->id) }}" method="POST"
                                class="absolute hidden top-2 right-2 group-hover:block">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-2 py-1 text-xs text-white bg-red-600 rounded-full hover:bg-red-700">✕</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <button type="submit"
                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Save Changes
            </button>
        </form>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css"
            integrity="sha512-UmV2ARg2MsY8TysMjhJvXSQHYgiYSVPS5ULXZCsTP3RgiMmBJhf8qP93vEyJgYuGt3u9V6wem73b11/Y8GVcOg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"
            integrity="sha512-1nmY9t9/Iq3JU1fGf0OpNCn6uXMmwC1XYX9a6547vnfcjCY1KvU9TE5e8jHQvXBoEH7hcKLIbbOjneZ8HCeNLA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const quill = new Quill('#quillEditor', {
                    theme: 'snow',
                    placeholder: 'Enter content here...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image', 'code-block'],
                        ]
                    }
                });

                // Load existing content exactly
                const existingContent = {!! json_encode(old('content', $trustSection->content)) !!};
                quill.root.innerHTML = existingContent || '<p><br></p>';

                // Save Quill content on form submit
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