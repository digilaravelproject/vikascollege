@extends('layouts.admin.app')
@section('content')
    <div class="max-w-3xl p-6 mx-auto bg-white shadow rounded-2xl">
        <h1 class="mb-5 text-xl font-semibold text-gray-800">Create New Page</h1>

        <form action="{{ route('admin.pagebuilder.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-1 font-medium text-gray-700">Page Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Slug (auto if empty)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Content (for builder JSON or HTML)</label>
                <textarea name="content" rows="6"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Feature Image</label>
                <input type="file" name="image"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Attach PDF</label>
                <input type="file" name="pdf" accept="application/pdf"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    Save Page
                </button>
                <a href="{{ route('admin.pagebuilder.index') }}" class="ml-3 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection