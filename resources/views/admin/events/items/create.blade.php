@extends('layouts.admin.app')
@section('title', 'Add New Event')

@section('content')
    <div class="p-4 sm:p-6 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Add New Event</h1>
            <a href="{{ route('admin.event-items.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to List
            </a>
        </div>

        {{-- Validation Errors (Copied from your reference) --}}
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

        <form action="{{ route('admin.event-items.store') }}" method="POST" enctype="multipart/form-data" id="event-form">
            @csrf

            {{-- Include the new shared form --}}
            {{-- Note: Controller must pass $categories --}}
            @include('admin.events.items._form', ['item' => null, 'categories' => $categories])

            {{-- Form Actions (Copied from your reference) --}}
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.event-items.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-save me-2"></i>
                    Save Event
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    {{-- Quill.js styles (from your reference) --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    {{-- Quill.js script (from your reference) --}}
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

            // Sync Quill editor content to the hidden input
            var form = document.getElementById('event-form');
            var hiddenInput = document.getElementById('full_content_input');

            form.addEventListener('submit', function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        });
    </script>
@endpush
