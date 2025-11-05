@extends('layouts.admin.app')
@section('title', 'Edit Announcement')

@section('content')
    <div class="p-4 sm:p-6 space-y-6">

        {{-- ENHANCED: Page Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Edit Announcement</h1>
            <a href="{{ route('admin.announcements.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to List
            </a>
        </div>

        {{-- ENHANCED: Validation Errors (copied from your menu reference) --}}
        @if ($errors->any())
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
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

        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- This includes the new shared form component, passing the $announcement --}}
            @include('admin.announcements.form', ['announcement' => $announcement])

            {{-- ENHANCED: Form Actions --}}
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.announcements.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-pencil-square me-2"></i>
                    Update Announcement
                </button>
            </div>
        </form>
    </div>
@endsection
@push('styles')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@endpush
