@extends('layouts.admin.app')

@section('content')
    <div class="p-6 bg-white shadow rounded-2xl">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-semibold text-gray-800">All Pages</h1>
            <a href="{{ route('admin.pagebuilder.create') }}"
                class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                + Create New Page
            </a>
        </div>

        @if(session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
        @endif

        <table class="min-w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left border-b">#</th>
                    <th class="px-3 py-2 text-left border-b">Title</th>
                    <th class="px-3 py-2 text-left border-b">Slug</th>
                    <th class="px-3 py-2 text-left border-b">Updated</th>
                    <th class="px-3 py-2 text-right border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border-b">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 border-b">{{ $page->title }}</td>
                        <td class="px-3 py-2 text-gray-600 border-b">/{{ $page->slug }}</td>
                        <td class="px-3 py-2 text-gray-500 border-b">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-3 py-2 space-x-2 text-right border-b">
                            <!-- Edit button -->
                            <a href="{{ route('admin.pagebuilder.edit', $page) }}"
                                class="text-blue-600 hover:underline">Edit</a>

                            {{-- <!-- Page Builder button -->
                            <a href="{{ route('admin.pagebuilder.builder', $page) }}"
                                class="font-semibold text-indigo-600 hover:underline">
                                🧱 Builder
                            </a> --}}

                            <!-- Delete button -->
                            <form action="{{ route('admin.pagebuilder.delete', $page) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline"
                                    onclick="return confirm('Delete this page?')">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">No pages created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection