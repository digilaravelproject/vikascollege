@extends('layouts.admin.app')
@section('title', 'Edit Event')

@section('content')
    <div class="p-4 sm:p-6 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
            <a href="{{ route('admin.event-items.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to List
            </a>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="flex-shrink-0 inline w-5 h-5 mr-3 bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <span class="sr-only">Danger</span>
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

        <form action="{{ route('admin.event-items.update', $item) }}" method="POST" enctype="multipart/form-data"
            id="event-form">
            @csrf
            @method('PUT')

            {{-- Include the new shared form, passing the $item --}}
            {{-- Note: Controller must pass $categories --}}
            @include('admin.events.items._form', ['item' => $item, 'categories' => $categories])

            {{-- Form Actions --}}
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.event-items.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-pencil-square me-2"></i>
                    Update Event
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    {{-- Quill.js styles --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    {{-- Quill.js script --}}
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'image']
                    ]
                }
            });

            // Sync Quill editor content to the hidden input on form submit
            var form = document.getElementById('event-form');
            var hiddenInput = document.getElementById('full_content_input');

            form.addEventListener('submit', function (e) {
                // Get the HTML content from Quill
                var content = quill.root.innerHTML;

                // Set value, but check if editor is effectively empty
                if (content === '<p><br></p>') {
                    hiddenInput.value = '';
                } else {
                    hiddenInput.value = content;
                }
            });

            // Set initial content for edit page
            // (The hidden input already has the value, Quill picks it up from the div's content)
        });
    </script>
@endpush
