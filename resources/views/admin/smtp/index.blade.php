@extends('layouts.admin.app')
@section('title', 'SMTP Settings')

@section('content')
    <div class="space-y-6 p-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">⚙️ SMTP Settings</h1>
        </div>

        {{-- Session Alerts --}}
        @if (session('success'))
            <div class="flex p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50 shadow-md" role="alert">
                <i class="bi bi-check-circle-fill me-3"></i>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif

        {{-- SMTP Form Card --}}
        <div class="bg-white border border-gray-200 shadow-lg rounded-2xl p-6">
            <form action="{{ route('admin.smtp.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Input Styling Definition --}}
                @php
                    $inputClasses = 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm';
                    $labelClasses = 'block text-sm font-medium text-gray-700 mb-1';
                @endphp

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    {{-- Host --}}
                    <div>
                        <label for="host" class="{{ $labelClasses }}">Host</label>
                        <input type="text" id="host" name="host" value="{{ $setting->host ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="e.g., smtp.gmail.com">
                    </div>

                    {{-- Port (ID added for JavaScript targeting) --}}
                    <div>
                        <label for="port" class="{{ $labelClasses }}">Port</label>
                        <input type="number" id="port" name="port" value="{{ $setting->port ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="e.g., 587 or 465">
                    </div>

                    {{-- Username --}}
                    <div>
                        <label for="username" class="{{ $labelClasses }}">Username</label>
                        <input type="text" id="username" name="username" value="{{ $setting->username ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="Your SMTP username">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="{{ $labelClasses }}">Password</label>
                        <input type="password" id="password" name="password" value="{{ $setting->password ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="Your SMTP password">
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    {{-- Encryption (ID and onChange added) --}}
                    <div>
                        <label for="encryption" class="{{ $labelClasses }}">Encryption</label>
                        <select id="encryption" name="encryption" class="{{ $inputClasses }}" onchange="syncPort()">
                            @php $currentEncryption = strtolower($setting->encryption ?? 'tls'); @endphp
                            <option value="tls" @selected($currentEncryption == 'tls')>TLS (Recommended)</option>
                            <option value="ssl" @selected($currentEncryption == 'ssl')>SSL</option>
                            <option value="none" @selected($currentEncryption == 'none' || $currentEncryption == '')>None (Port
                                25)</option>
                        </select>
                    </div>

                    {{-- From Address --}}
                    <div>
                        <label for="from_address" class="{{ $labelClasses }}">From Address</label>
                        <input type="email" id="from_address" name="from_address" value="{{ $setting->from_address ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="e.g., no-reply@yoursite.com">
                    </div>

                    {{-- From Name --}}
                    <div>
                        <label for="from_name" class="{{ $labelClasses }}">From Name</label>
                        <input type="text" id="from_name" name="from_name" value="{{ $setting->from_name ?? '' }}"
                            class="{{ $inputClasses }}" placeholder="e.g., Your Company Name">
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Receiving Email --}}
                <div>
                    <label for="receiving_email" class="{{ $labelClasses }}">Receiving Email (where admin wants to receive
                        leads)</label>
                    <input type="email" id="receiving_email" name="receiving_email"
                        value="{{ $setting->receiving_email ?? '' }}" class="{{ $inputClasses }}"
                        placeholder="e.g., admin@yoursite.com">
                </div>

                {{-- Save Button --}}
                <div class="pt-2">
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 transition">
                        <i class="bi bi-save me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript to sync port and encryption --}}
    <script>
        function syncPort() {
            const encryptionSelect = document.getElementById('encryption');
            const portInput = document.getElementById('port');
            const encryption = encryptionSelect.value;
            const currentPort = portInput.value;

            // Define standard ports
            const ports = {
                'tls': '587',
                'ssl': '465',
                'none': '25'
            };

            // Only update the port if it's currently empty OR if it matches a standard port
            // This prevents overwriting a custom port the user may have manually entered.
            if (!currentPort || Object.values(ports).includes(currentPort)) {
                if (ports[encryption]) {
                    portInput.value = ports[encryption];
                } else {
                    portInput.value = ''; // Clear if encryption is unknown
                }
            }
        }

        // Run once on page load to ensure initial state is correct if data is loaded
        // and to ensure the port value is present.
        document.addEventListener('DOMContentLoaded', function () {
            // This is a minimal implementation. For reverse sync (port -> encryption),
            // you'd add logic here to listen to port changes, but auto-filling the port
            // based on encryption is the most common and user-friendly approach.

            // Check if port is empty and encryption is set, then fill it.
            const portInput = document.getElementById('port');
            if (!portInput.value) {
                syncPort();
            }
        });
    </script>
@endsection
