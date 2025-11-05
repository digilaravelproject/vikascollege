@extends('layouts.admin.app')
@section('title', 'Add Academic Calendar Item')

@section('content')
    <div class="p-4 sm:p-6 space-y-6">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Add Academic Calendar Item</h1>
            <a href="{{ route('admin.academic-calendar.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg shadow-sm
                               hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <i class="bi bi-arrow-left me-2"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                <i class="bi bi-exclamation-triangle-fill w-5 h-5 mr-3"></i>
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

        <form action="{{ route('admin.academic-calendar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('admin.academic_calendar._form', ['item' => null])

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.academic-calendar.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm
                                   hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-save me-2"></i> Save Item
                </button>
            </div>
        </form>
    </div>
@endsection
