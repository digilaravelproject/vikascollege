@extends('layouts.admin.app')
@section('title', 'Create Notification')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <div class="container max-w-3xl px-4 mx-auto">
        <div class="flex items-center justify-between py-4">
            <h1 class="text-xl font-semibold text-gray-800">Create Notification</h1>
            <a href="{{ route('admin.notifications.index') }}"
                class="text-sm font-medium text-blue-600 hover:underline">Back to list</a>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                {{-- Include the reusable form partial --}}
                @include('admin.notifications._form', [
                    'notification' => null,
                    'icons' => $icons,
                ])          <div class="pt-4 mt-6 border-t border-gray-200">
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Notification
                </button>
            </div>
                </form>
        </div>
        </div>
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        @endpush
@endsection
