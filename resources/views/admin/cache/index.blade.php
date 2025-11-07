@extends('layouts.admin.app')

@section('title', 'Cache Management')

@section('content')
    <div class="p-4 sm:p-6 space-y-6" x-data="cachePage()">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Cache Management</h1>
        </div>

        {{-- Success/Error Messages (from session redirect) --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="flex items-center justify-between p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50"
                role="alert">
                <div class="flex items-center">
                    <i class="text-lg bi bi-check-circle-fill me-3"></i>
                    <div>
                        <span class="font-medium">Success!</span> {{ session('success') }}
                    </div>
                </div>
                <button @click="show = false" class="ml-3 -mr-1 text-green-700/70 hover:text-green-900">
                    <span class="sr-only">Close</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="flex items-center justify-between p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50"
                role="alert">
                <div class="flex items-center">
                    <i class="text-lg bi bi-exclamation-triangle-fill me-3"></i>
                    <div>
                        <span class="font-medium">Error!</span> {{ session('error') }}
                    </div>
                </div>
                <button @click="show = false" class="ml-3 -mr-1 text-red-700/70 hover:text-red-900">
                    <span class="sr-only">Close</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        {{-- Cache Actions Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Card 1: Clear All Caches --}}
            {{-- FIXED: 'classread-only' typo corrected to 'class' --}}
            <div class="p-6 bg-white rounded-2xl shadow-xl border border-gray-100 flex flex-col items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-red-100 rounded-full">
                    <i class="bi bi-trash text-2xl text-red-600"></i>
                </div>
                <div class="flex-grow"> {{-- Added flex-grow to ensure text block takes available space --}}
                    <h2 class="text-lg font-semibold text-gray-900">Clear Application Cache</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{-- UPDATED: Text changed to English --}}
                        If content changes (e.g., pages, settings) aren't appearing on your site, use this button. This runs
                        the `optimize:clear` command.
                    </p>
                </div>
                <button type="button" @click.prevent="runCacheAction(
                            '{{ route('admin.cache.clear-all') }}',
                            'Clear All Caches?',
                            'Your site may be temporarily slow. This action will clear the config, route, and view caches.'
                        )"
                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm
                               hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mt-auto">
                    <i class="bi bi-trash me-2"></i>
                    Clear All Caches
                </button>
            </div>

            {{-- Card 2: Re-Optimize --}}
            <div class="p-6 bg-white rounded-2xl shadow-xl border border-gray-100 flex flex-col items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-green-100 rounded-full">
                    <i class="bi bi-rocket-takeoff text-2xl text-green-600"></i>
                </div>
                <div class="flex-grow"> {{-- Added flex-grow to ensure text block takes available space --}}
                    <h2 class="text-lg font-semibold text-gray-900">Re-Optimize for Speed</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{-- UPDATED: Text changed to English --}}
                        After clearing the cache, use this button to speed up the site again. This runs `optimize` and
                        `view:cache`.
                    </p>
                </div>
                <button type="button" @click.prevent="runCacheAction(
                            '{{ route('admin.cache.re-optimize') }}',
                            'Re-Optimize Application?',
                            'This will optimize the application for production mode.'
                        )"
                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm
                               hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 mt-auto">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Re-Optimize Application
                </button>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function cachePage() {
            return {
                runCacheAction(url, title, confirmText) {
                    Swal.fire({
                        title: title,
                        text: confirmText,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6', // Blue
                        cancelButtonColor: '#d33',     // Red
                        confirmButtonText: 'Yes, do it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 1. Show processing modal
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we run the command.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // 2. Run fetch request
                            fetch(url, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                                // Check if response is ok, then parse as JSON
                                .then(response => {
                                    if (!response.ok) {
                                        // Throw an error to be caught by .catch()
                                        throw new Error('Network response was not ok: ' + response.statusText);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        // 3. Show success
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success'
                                        }).then(() => {
                                            // Reload page to show session message (optional)
                                            window.location.reload();
                                        });
                                    } else {
                                        // 3. Show handled error (e.g., from controller)
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message || 'An unknown error occurred.',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => {
                                    // 3. Show network/fetch error
                                    console.error('Fetch Error:', error); // Log error to console
                                    Swal.fire({
                                        title: 'Request Failed!',
                                        text: 'Could not connect to the server or an error occurred. Please check logs or try again.',
                                        icon: 'error'
                                    });
                                });
                        }
                    })
                }
            }
        }
    </script>
@endpush
