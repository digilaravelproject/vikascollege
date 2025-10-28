@extends('layouts.admin.app')
@section('title', 'Trust Page Sections')

@section('content')
    <div class="p-6 bg-white shadow rounded-2xl">
        <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-gray-800">Trust Page Sections</h1>

            <a href="{{ route('admin.trust.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-blue-600 rounded-lg hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Create New Section
            </a>
        </div>

        @if(session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-left text-gray-700">#</th>
                        <th class="px-4 py-3 font-semibold text-left text-gray-700">Title</th>
                        <th class="px-4 py-3 font-semibold text-left text-gray-700">Slug</th>
                        <th class="px-4 py-3 font-semibold text-left text-gray-700">PDF</th>
                        <th class="px-4 py-3 font-semibold text-left text-gray-700">Images</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-right text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sections as $index => $section)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $section->title }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $section->slug }}</td>

                            <td class="px-4 py-3">
                                @if($section->pdf_path)
                                    <a href="{{ asset('storage/' . $section->pdf_path) }}" target="_blank"
                                        class="text-sm text-blue-600 hover:underline">View PDF</a>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if($section->images->count())
                                    <div class="flex -space-x-2">
                                        @foreach($section->images->take(3) as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                class="object-cover w-8 h-8 border rounded-full">
                                        @endforeach
                                        @if($section->images->count() > 3)
                                            <div
                                                class="flex items-center justify-center w-8 h-8 text-xs text-gray-700 bg-gray-200 rounded-full">
                                                +{{ $section->images->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if($section->status)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.trust.edit', $section->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No trust sections found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection