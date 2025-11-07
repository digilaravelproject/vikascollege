@extends('layouts.admin.app')

@section('title', 'Media Management')

@section('content')

    <div class="container mx-auto p-6">

        {{-- 1. HEADER & UPLOAD FORM --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4 border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-bold text-gray-900">Media Management</h1>

            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                @csrf

                <input type="file" name="media_file" required
                    class="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 block w-full text-sm text-gray-500 rounded-lg border border-gray-300 cursor-pointer focus:outline-none">

                <input type="text" name="custom_name" placeholder="Optional: Custom file name"
                    class="form-input rounded-lg border-gray-300 shadow-sm text-sm w-full md:w-auto focus:ring-blue-500 focus:border-blue-500">

                <select name="destination_disk"
                    class="form-select rounded-lg border-gray-300 shadow-sm text-sm w-full md:w-auto focus:ring-blue-500 focus:border-blue-500">
                    <option value="storage" selected>Save to: Storage</option>
                    <option value="wp-content">Save to: WP-Content</option>
                </select>

                {{-- Added icon and spacing to button --}}
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 whitespace-nowrap flex items-center justify-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Upload</span>
                </button>
            </form>
        </div>

        {{-- 2. ENHANCED ALERTS (with Icons) --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 flex items-center gap-3"
                role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 flex items-center gap-3"
                role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 3. MEDIA GRID --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">

            @forelse ($mediaItems as $item)
                {{-- Enhanced Card: Added hover effect and softer shadow/border --}}
                <div
                    class="bg-white shadow-md hover:shadow-lg transition-shadow duration-200 rounded-lg overflow-hidden border border-gray-200">

                    {{-- Enhanced Preview: Increased height and added better fallback icon --}}
                    <div class="relative h-36 w-full">

                        @if (in_array($item['type'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                            {{-- This is for IMAGES --}}
                            <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">

                        @elseif (in_array($item['type'], ['mp4', 'mov', 'webm', 'ogg']))
                            {{-- This is the NEW block for VIDEOS --}}
                            <video class="h-full w-full object-cover bg-gray-900" controls>
                                <source src="{{ $item['url'] }}" type="video/{{ $item['type'] }}">
                                Your browser does not support the video tag.
                            </video>

                        @else
                            {{-- This is the FALLBACK for other files (PDF, etc.) --}}
                            <div class="h-full w-full bg-gray-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="absolute bottom-4 text-gray-500 text-lg font-bold uppercase">{{ $item['type'] }}</span>
                            </div>
                        @endif

                        {{-- Badge: Moved slightly for better padding --}}
                        <span
                            class="absolute top-2 right-2 text-xs px-2 py-0.5 rounded-full {{ $item['disk'] === 'storage' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                            {{ $item['disk'] === 'storage' ? 'Storage' : 'WP-Content' }}
                        </span>
                    </div>

                    {{-- Card Info --}}
                    <div class="p-3">
                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $item['name'] }}">
                            {{ $item['name'] }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $item['size'] }} &bull;
                            {{ \Carbon\Carbon::createFromTimestamp($item['timestamp'])->diffForHumans() }}
                        </p>

                        {{-- Enhanced Actions: Better buttons and feedback --}}
                        <div class="flex justify-between items-center mt-3 text-xs">

                            {{-- Enhanced Copy Button: Now with icons and better feedback --}}
                            <button x-data="{ feedback: 'Copy' }" x-on:click="navigator.clipboard.writeText('{{ $item['url'] }}');
                                                        feedback = 'Copied!';
                                                        setTimeout(() => feedback = 'Copy', 1500)"
                                class="flex items-center gap-1 text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-200 transition-colors"
                                :class="{ 'bg-green-100 text-green-700': feedback === 'Copied!' }">

                                {{-- Clipboard Icon --}}
                                <svg x-show="feedback === 'Copy'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                {{-- Check Icon --}}
                                <svg x-show="feedback === 'Copied!'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                    style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="feedback"></span>
                            </button>

                            {{-- Enhanced Delete Button: Now a subtle icon button --}}
                            <form action="{{ route('admin.media.destroy') }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this file?');">
                                @csrf
                                <input type="hidden" name="file_path" value="{{ $item['path'] }}">
                                <input type="hidden" name="disk" value="{{ $item['disk'] }}">
                                <button type="submit"
                                    class="text-gray-400 hover:text-red-600 p-1 rounded-full transition-colors"
                                    title="Delete file">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                {{-- 4. ENHANCED EMPTY STATE --}}
                <div class="col-span-full bg-white rounded-lg shadow-sm border border-dashed border-gray-300 p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-800">No media files found</h3>
                    <p class="mt-1 text-sm text-gray-500">Upload your first file using the form above.</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection
