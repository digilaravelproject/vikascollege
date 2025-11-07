@extends('layouts.admin.app')

@section('title', 'Site Management')

@push('styles')
    {{-- Toggle switch ke liye CSS --}}
    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #4ade80;
            /* green-400 */
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #4ade80;
            /* green-400 */
        }
    </style>
@endpush

@section('content')
    <div class="p-4 sm:p-6 space-y-6" x-data="siteManagement(
                                    '{{ $app_env }}',
                                    {{ $app_debug ? 'true' : 'false' }},
                                    {{ $is_maintenance ? 'true' : 'false' }}
                                )">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Site Management & Tools</h1>
        </div>

        {{-- Success/Error Messages (AJAX ke liye, dynamic) --}}
        <div x-show="message.text" :class="{
                                    'text-green-700 border-green-200 bg-green-50': message.type === 'success',
                                    'text-red-700 border-red-200 bg-red-50': message.type === 'error'
                                }" class="flex items-center justify-between p-4 text-sm border rounded-lg" role="alert"
            x-transition>
            <div class="flex items-center">
                <i class="text-lg bi me-3"
                    :class="{ 'bi-check-circle-fill': message.type === 'success', 'bi-exclamation-triangle-fill': message.type === 'error' }"></i>
                <div>
                    <span class="font-medium" x-text="message.type === 'success' ? 'Success!' : 'Error!'"></span>
                    <span x-text="message.text"></span>
                </div>
            </div>
            <button @click="message.text = ''" class="ml-3 -mr-1"
                :class="{ 'text-green-700/70 hover:text-green-900': message.type === 'success', 'text-red-700/70 hover:text-red-900': message.type === 'error' }">
                <span class="sr-only">Close</span>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- 1. Site Info (Sirf dikhane ke liye) --}}
        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Site Information</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 truncate">PHP Version</h3>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $php_version }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 truncate">Laravel Version</h3>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $laravel_version }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 truncate">App Environment</h3>
                <p class="mt-1 text-2xl font-semibold" :class="appEnv === 'production' ? 'text-red-600' : 'text-green-600'"
                    x-text="appEnv">
                </p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 truncate">Debug Mode</h3>
                <p class="mt-1 text-2xl font-semibold" :class="appDebug ? 'text-red-600' : 'text-green-600'"
                    x-text="appDebug ? 'ON' : 'OFF'">
                </p>
            </div>
        </div>

        {{-- 2. Developer Actions --}}
        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Developer Tools</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Card 1: Maintenance Mode --}}
            <div class="p-5 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-yellow-100 rounded-full">
                        <i class="bi bi-cone-striped text-xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Maintenance Mode</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600 flex-grow">
                    Site ko offline (maintenance) ya live karein. Offline hone par users ko maintenance page dikhega.
                </p>
                <div class="relative inline-block w-full mt-4 text-left" x-data="{ toggling: false }">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm font-medium text-gray-700"
                            x-text="isMaintenance ? 'Site is OFFLINE' : 'Site is LIVE'"></span>
                        <div
                            class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="toggle" id="maintenanceToggle"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                :class="{ 'opacity-50 cursor-not-allowed': toggling }" :disabled="toggling"
                                :checked="isMaintenance" @click="toggleMaintenance()" />
                            <label for="maintenanceToggle"
                                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Card 2: Application Environment --}}
            <div class="p-5 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-100 rounded-full">
                        <i class="bi bi-gear-wide-connected text-xl text-blue-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Application Mode</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600 flex-grow">
                    Site ka environment change karein. 'Production' mode security aur speed ke liye hai.
                </p>
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="setEnv('local')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="appEnv === 'local' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        Local
                    </button>
                    <button type="button" @click="setEnv('production')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="appEnv === 'production' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        Production
                    </button>
                </div>
            </div>

            {{-- Card 3: Debug Mode --}}
            <div class="p-5 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-purple-100 rounded-full">
                        <i class="bi bi-bug text-xl text-purple-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Debug Mode</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600 flex-grow">
                    Errors dikhane ke liye debug mode 'ON' karein.
                    <strong class="text-red-600">Production par hamesha 'OFF' rakhein.</strong>
                </p>
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="setDebug(true)"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="appDebug ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        ON
                    </button>
                    <button type="button" @click="setDebug(false)"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="!appDebug ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        OFF
                    </button>
                </div>
            </div>

        </div>

        {{-- 3. Cache Actions --}}
        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Cache Tools</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Card 4: Clear All Caches --}}
            <div class="p-5 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-red-100 rounded-full">
                        <i class="bi bi-trash text-xl text-red-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Clear Application Cache</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600 flex-grow">
                    Agar content changes (pages, settings) nahi dikh rahe hain. Yeh `optimize:clear` chalata hai.
                </p>
                <button type="button" @click.prevent="runCacheAction(
                                                        '{{ route('admin.site.cache.clear-all') }}',
                                                        'Clear All Caches?',
                                                        'Site thodi slow ho sakti hai. Yeh config, route, aur view cache clear kar dega.'
                                                    )"
                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm
                                                        hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mt-4">
                    <i class="bi bi-trash me-2"></i>
                    Clear All Caches
                </button>
            </div>

            {{-- Card 5: Re-Optimize --}}
            <div class="p-5 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-green-100 rounded-full">
                        <i class="bi bi-rocket-takeoff text-xl text-green-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Re-Optimize for Speed</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600 flex-grow">
                    Cache clear karne ke baad site ko speed-up karein. Yeh `optimize` aur `view:cache` chalata hai.
                </p>
                <button type="button" @click.prevent="runCacheAction('{{ route('admin.site.cache.re-optimize') }}',
                                                        'Re-Optimize Application?',
                                                        'Yeh application ko production mode ke liye optimize kar dega.'
                                                    )"
                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm
                                                        hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 mt-4">
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
        function siteManagement(initialEnv, initialDebug, initialMaintenance) {
            return {
                appEnv: initialEnv,
                appDebug: initialDebug,
                isMaintenance: initialMaintenance,
                message: { text: '', type: 'success' }, // For AJAX messages
                toggling: false, // Maintenance toggle state

                // -- 1. Toggle Maintenance Mode --
                toggleMaintenance() {
                    if (this.toggling) return; // Ek saath do click na ho

                    const newState = !this.isMaintenance;
                    const action = newState ? 'down' : 'up';
                    const title = newState ? 'Go Offline?' : 'Go Live?';
                    const text = newState ? 'Site maintenance mode mein chali jayegi.' : 'Site wapas live ho jayegi.';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, do it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.toggling = true;
                            this.runAction(
                                '{{ route('admin.site.toggle-maintenance') }}',
                                { action: action },
                                'Processing...',
                                (data) => {
                                    // Success
                                    this.isMaintenance = newState;
                                    this.showMessage(data.message, 'success');
                                    this.toggling = false;
                                },
                                (errorMsg) => {
                                    // Error
                                    this.showMessage(errorMsg, 'error');
                                    this.toggling = false;
                                }
                            );
                        }
                    });
                },

                // -- 2. Set Environment --
                setEnv(mode) {
                    if (mode === this.appEnv) return; // Agar same hai toh kuch na karein

                    Swal.fire({
                        title: `Set Mode to '${mode}'?`,
                        text: "Yeh .env file ko update karega aur page reload hoga. Kya aap sure hain?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.runAction(
                                '{{ route('admin.site.set-env') }}',
                                { mode: mode },
                                'Setting Environment...',
                                (data) => {
                                    this.showMessage(data.message, 'success');
                                    Swal.fire('Success!', data.message, 'success').then(() => window.location.reload());
                                },
                                (errorMsg) => {
                                    this.showMessage(errorMsg, 'error');
                                }
                            );
                        }
                    });
                },

                // -- 3. Set Debug Mode --
                setDebug(mode) {
                    if (mode === this.appDebug) return; // Agar same hai toh kuch na karein

                    const modeText = mode ? 'ON' : 'OFF';
                    Swal.fire({
                        title: `Turn Debug Mode '${modeText}'?`,
                        text: "Yeh .env file ko update karega aur page reload hoga. Kya aap sure hain?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.runAction(
                                '{{ route('admin.site.toggle-debug') }}',
                                { debug: mode ? 'true' : 'false' },
                                'Setting Debug Mode...',
                                (data) => {
                                    this.showMessage(data.message, 'success');
                                    Swal.fire('Success!', data.message, 'success').then(() => window.location.reload());
                                },
                                (errorMsg) => {
                                    this.showMessage(errorMsg, 'error');
                                }
                            );
                        }
                    });
                },

                // -- 4. Original Cache Action (Clear/Optimize) --
                runCacheAction(url, title, confirmText) {
                    Swal.fire({
                        title: title,
                        text: confirmText,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6', // Blue
                        cancelButtonColor: '#d33', // Red
                        confirmButtonText: 'Yes, do it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Empty body (null) pass karein
                            this.runAction(
                                url,
                                null, // No body
                                'Processing...',
                                (data) => {
                                    // Success
                                    this.showMessage(data.message, 'success');
                                    Swal.fire('Success!', data.message, 'success');
                                },
                                (errorMsg) => {
                                    // Error
                                    this.showMessage(errorMsg, 'error');
                                }
                            );
                        }
                    });
                },

                // -- Universal Helper: Show AJAX Message --
                showMessage(text, type = 'success') {
                    this.message.text = text;
                    this.message.type = type;
                    // 5 second ke baad hide karein
                    setTimeout(() => {
                        this.message.text = ''
                    }, 5000);
                },

                // -- Universal Helper: Run AJAX Action (Ab POST use karega) --
                runAction(url, body, loadingText, successCallback, errorCallback) {
                    // 1. Show processing modal
                    Swal.fire({
                        title: loadingText,
                        text: 'Please wait...',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // 2. Run fetch request
                    fetch(url, {
                        method: 'POST', // Hamesha POST use karein state change ke liye
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: body ? JSON.stringify(body) : null // Body ko JSON stringify karein
                    })
                        .then(response => {
                            if (!response.ok) {
                                // Server error (e.g., 500)
                                return response.json().then(errData => {
                                    throw new Error(errData.message || 'Server error: ' + response.statusText);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.close(); // Loading modal band karein
                            if (data.status === 'success') {
                                successCallback(data); // Success function call karein
                            } else {
                                // Handled error (e.g., validation)
                                throw new Error(data.message || 'An unknown error occurred.');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            Swal.fire({
                                title: 'Request Failed!',
                                text: error.message || 'Could not connect to the server.',
                                icon: 'error'
                            });
                            errorCallback(error.message); // Error function call karein
                        });
                }
            }
        }
    </script>
@endpush
