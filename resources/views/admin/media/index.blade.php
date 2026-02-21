@extends('layouts.admin.app')

@section('title', 'Media Management')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- 0. STORAGE STATISTICS CARD --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Occupied Storage</h3>
                    <p class="text-xl font-bold text-gray-900">{{ $storageStats['used_readable'] }}</p>
                    <p class="text-xs text-blue-600 font-medium">{{ $storageStats['percent'] }}% of media</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Available Storage</h3>
                    <p class="text-xl font-bold text-gray-900">{{ $storageStats['free_readable'] }}</p>
                    <p class="text-xs text-gray-400">Total: {{ $storageStats['total_readable'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-medium text-gray-700">Server Capacity</h3>
                    <span class="text-xs font-bold text-gray-900">{{ $storageStats['overall_percent'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $storageStats['overall_percent'] }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-2 italic">*Based on server disk space</p>
            </div>
        </div>

        {{-- 1. MODERN UPLOAD HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
                <div class="flex-1 w-full lg:w-auto">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Media Library</h1>

                    {{-- SEARCH BAR --}}
                    <div class="mt-4 relative max-w-md">
                        <form action="{{ route('admin.media.index') }}" method="GET" class="relative group">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search files..."
                                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all group-hover:border-gray-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            @if($search)
                                <a href="{{ route('admin.media.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <form id="uploadForm" action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data"
                    class="w-full lg:w-auto flex flex-col sm:flex-row items-end gap-3">
                    @csrf

                    <div class="flex flex-col gap-1 w-full sm:w-auto">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">File</label>
                        <label for="media_file"
                            class="flex items-center justify-center px-4 py-2 text-gray-700 rounded-xl border border-gray-300 border-dashed cursor-pointer hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-200 min-h-[42px]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span class="text-xs font-medium truncate max-w-[100px]" id="file-label">Choose</span>
                            <input type="file" name="media_file" id="media_file" required class="hidden"
                                onchange="document.getElementById('file-label').innerText = this.files[0].name">
                        </label>
                    </div>

                    {{-- <div class="flex flex-col gap-1 w-full sm:w-40">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Custom Name</label>
                        <input type="text" name="custom_name" placeholder="Optional"
                            class="w-full px-3 py-2 text-xs border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div> --}}
{{--
                    <div class="flex flex-col gap-1 w-full sm:w-32">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Destination</label>
                        <select name="destination_disk"
                            class="w-full px-3 py-2 text-xs border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white">
                            <option value="storage" selected>Storage</option>
                            <option value="wp-content">WP-Content</option>
                        </select>
                    </div> --}}

                    <button type="submit" id="uploadBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 font-bold text-xs uppercase whitespace-nowrap h-[42px] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span>Upload</span>
                    </button>
                </form>
            </div>

            {{-- PROGRESS BAR SECTION (Initially Hidden) --}}
            <div id="progress-container" class="mt-4 hidden animate-fade-in-down">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-blue-700" id="progress-status-text">Uploading...</span>
                    <span class="text-sm font-medium text-blue-700" id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-150 ease-out" style="width: 0%"
                        id="progress-bar"></div>
                </div>
            </div>
        </div>

        {{-- 2. ALERTS (Blade fallback, JS uses SweetAlert) --}}
        @if (session('success'))
            <div
                class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 animate-fade-in-down">
                <svg class="h-5 w-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3 animate-fade-in-down">
                <svg class="h-5 w-5 flex-shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 3. MEDIA GRID --}}
        {{-- Responsive Grid: 2 cols on mobile, 3 on tablet, 4 on laptop, 5 on desktop --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">

            @forelse ($mediaItems as $item)
                <div
                    class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden h-full">

                    {{-- Preview Area (Fixed Aspect Ratio) --}}
                    <div class="relative aspect-square bg-gray-50 border-b border-gray-100 overflow-hidden">

                        {{-- Disk Badge --}}
                        <div class="absolute top-2 left-2 z-10 hidden">
                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-white rounded-md bg-opacity-90 backdrop-blur-sm shadow-sm {{ $item['disk'] === 'storage' ? 'bg-blue-500' : 'bg-emerald-500' }}">
                                {{ $item['disk'] === 'storage' ? 'Local' : 'WP' }}
                            </span>
                        </div>

                        @if (in_array($item['type'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                            {{--
                            PERFORMANCE FIX:
                            1. loading="lazy": Delays loading off-screen images.
                            2. decoding="async": Prevents blocking the main thread while decoding.
                            3. object-cover: Ensures image fills the square without stretching.
                            --}}
                            <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                        @elseif (in_array($item['type'], ['mp4', 'mov', 'webm', 'ogg']))
                            {{-- PERFORMANCE FIX: preload="metadata" only loads info, not the whole video --}}
                            <video class="w-full h-full object-cover bg-black" controls preload="metadata">
                                <source src="{{ $item['url'] }}" type="video/{{ $item['type'] }}">
                            </video>

                        @else
                            {{-- File Fallback --}}
                            <div
                                class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 group-hover:bg-gray-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold uppercase text-gray-500">{{ $item['type'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Details Footer --}}
                    <div class="p-3 flex flex-col justify-between flex-grow">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800 truncate leading-tight"
                                title="{{ $item['name'] }}">
                                {{ $item['name'] }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                <span>{{ $item['size'] }}</span>
                                <span class="text-gray-300">&bull;</span>
                                <span>{{ \Carbon\Carbon::createFromTimestamp($item['timestamp'])->diffForHumans(null, true) }}</span>
                            </div>
                        </div>

                        {{-- Action Toolbar --}}
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">

                        <a href="{{ $item['url'] }}" download="{{ $item['name'] }}" title="Download"
                                class="flex items-center justify-center p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors border border-gray-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>

                            {{-- Copy Button --}}
                            <button type="button" onclick="copyToClipboard('{{ $item['url'] }}', this)"
                                class="flex items-center gap-1.5 text-xs font-medium text-gray-600 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 px-2.5 py-1.5 rounded-md transition-all w-full justify-center mr-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="btn-text">Copy</span>
                            </button>

                            {{-- Delete Button --}}
                            <form action="{{ route('admin.media.destroy') }}" method="POST"
                                onsubmit="return confirm('Delete this file permanently?');">
                                @csrf
                                <input type="hidden" name="file_path" value="{{ $item['path'] }}">
                                <input type="hidden" name="disk" value="{{ $item['disk'] }}">
                                <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                                    title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div
                        class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="bg-gray-50 p-4 rounded-full mb-3">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No media files found</h3>
                        <p class="text-sm text-gray-500 mt-1">Upload your first file using the form above.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- REQUIRED SCRIPTS: SweetAlert2 & Axios --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Script for Copy Functionality & AJAX Upload --}}
    <script>
        // Copy to Clipboard Logic
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.querySelector('.btn-text').innerText;
                const originalHtml = btn.innerHTML;

                // Show Feedback
                btn.classList.remove('bg-gray-50', 'text-gray-600', 'hover:text-blue-600', 'hover:bg-blue-50');
                btn.classList.add('bg-green-100', 'text-green-700');
                btn.querySelector('.btn-text').innerText = 'Copied!';

                // Revert after 2 seconds
                setTimeout(() => {
                    btn.classList.remove('bg-green-100', 'text-green-700');
                    btn.classList.add('bg-gray-50', 'text-gray-600', 'hover:text-blue-600', 'hover:bg-blue-50');
                    btn.innerHTML = originalHtml;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                // Fallback SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to copy URL',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }

        // AJAX UPLOAD WITH PROGRESS BAR
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressStatusText = document.getElementById('progress-status-text');
            const uploadBtn = document.getElementById('uploadBtn');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const fileInput = document.getElementById('media_file');

                    if (!fileInput.files.length) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No File Selected',
                            text: 'Please select a file to upload.',
                        });
                        return;
                    }

                    // Reset UI
                    progressContainer.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressText.innerText = '0%';
                    progressStatusText.innerText = 'Uploading...';
                    uploadBtn.disabled = true;
                    uploadBtn.innerText = 'Uploading...';

                    // Axios Configuration
                    const config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-Requested-With': 'XMLHttpRequest', // Tells Laravel it's AJAX
                            'Accept': 'application/json' // Tells Laravel to return JSON errors
                        },
                        onUploadProgress: function(progressEvent) {
                            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent
                                .total);

                            // Visual update
                            progressBar.style.width = percentCompleted + '%';
                            progressText.innerText = percentCompleted + '%';

                            // If upload hits 100%, show "Processing"
                            // This helps user understand it's the server working, not network lagging
                            if(percentCompleted === 100) {
                                progressStatusText.innerText = 'Finalizing & Saving...';
                                progressBar.classList.add('animate-pulse'); // Adds a pulse effect
                            }
                        }
                    };

                    // Send Request
                    axios.post(form.action, formData, config)
                        .then(function(response) {
                            // SUCCESS
                            progressBar.style.width = '100%';
                            progressText.innerText = '100%';
                            progressBar.classList.remove('animate-pulse');

                            Swal.fire({
                                icon: 'success',
                                title: 'Uploaded!',
                                text: response.data.message || 'File uploaded successfully.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload(); // Reload to show new file
                            });
                        })
                        .catch(function(error) {
                            // ERROR HANDLING
                            uploadBtn.disabled = false;
                            uploadBtn.innerText = 'Upload';
                            progressContainer.classList.add('hidden');
                            progressBar.classList.remove('animate-pulse');

                            let errorMessage = 'Something went wrong.';

                            if (error.response) {
                                // Server responded with a status code outside of 2xx
                                if (error.response.status === 422) {
                                    // Validation Error
                                    const errors = error.response.data.errors;
                                    const firstError = Object.values(errors)[0][0];
                                    errorMessage = firstError;
                                } else {
                                    errorMessage = error.response.data.message || 'Server Error';
                                }
                            } else if (error.request) {
                                errorMessage = 'No response from server. Check your internet.';
                            } else {
                                errorMessage = error.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: errorMessage,
                            });
                        });
                });
            }
        });
    </script>

    {{-- Custom Animation Style --}}
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -10px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out;
        }
    </style>
@endsection
