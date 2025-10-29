@extends('layouts.admin.app')

@section('content')
    <div class="max-w-3xl p-6 mx-auto bg-white shadow rounded-2xl">
        <h1 class="mb-5 text-xl font-semibold text-gray-800">Edit Page</h1>

        @if(session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.pagebuilder.update', $page) }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf

            <div>
                <label class="block mb-1 font-medium text-gray-700">Page Title *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Content</label>
                <textarea name="content" rows="6"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">{{ old('content', $page->content) }}</textarea>
            </div>

            @if($page->image)
                <div class="mt-3">
                    <p class="mb-1 text-sm text-gray-600">Current Image:</p>
                    <img src="{{ asset('storage/' . $page->image) }}" alt="" class="w-40 border rounded-lg">
                </div>
            @endif

            <div>
                <label class="block mb-1 font-medium text-gray-700">Replace Image</label>
                <input type="file" name="image"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            @if($page->pdf)
                <div class="mt-3">
                    <p class="mb-1 text-sm text-gray-600">Current PDF:</p>
                    <a href="{{ asset('storage/' . $page->pdf) }}" target="_blank" class="text-blue-600 hover:underline">View
                        PDF</a>
                </div>
            @endif

            <div>
                <label class="block mb-1 font-medium text-gray-700">Replace PDF</label>
                <input type="file" name="pdf" accept="application/pdf"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    Update Page
                </button>
                <a href="{{ route('admin.pagebuilder.index') }}" class="ml-3 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection