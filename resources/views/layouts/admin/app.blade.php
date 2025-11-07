<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- ✅ Admin Panel Title --}}
    <title>@yield('title', 'Admin Panel') - {{ setting('college_name', config('app.name')) }}</title>

    {{-- ✅ Favicon --}}
    <link rel="icon" href="{{ asset('storage/' . setting('favicon')) }}" type="image/x-icon">

    {{-- ✅ IMPORTANT: Tell Google to NOT index this admin panel --}}
    <meta name="robots" content="noindex, nofollow">

    {{-- ✅ Alpine.js --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- ✅ Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- ✅ Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f172a', // dark navy
                        accent: '#2563eb',  // blue
                    }
                }
            }
        }
    </script>

    {{-- ✅ Optional: Font Awesome (icons) --}}
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free/css/all.min.css">

    <style>
        /* --- Basic UI Enhancements --- */
        [x-cloak] {
            display: none !important;
        }

        .card-shadow {
            box-shadow: 0 6px 18px rgba(8, 15, 52, 0.08);
        }

        .glass {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(6px);
        }
    </style>

    {{-- ✅ Custom styles from child views --}}
    @stack('styles')
</head>

<body class="min-h-screen text-gray-800 bg-gray-50" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        {{-- ✅ Sidebar --}}
        @include('layouts.admin.partials.sidebar')

        {{-- ✅ Main Area --}}
        <div class="flex flex-col flex-1 overflow-hidden">

            {{-- ✅ Topbar --}}
            @include('layouts.admin.partials.topbar')

            {{-- ✅ Main Content --}}
            <main class="flex-1 p-6 overflow-auto">
                @if (session('permission_error'))
                    <div class="flex p-3 text-xs text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{-- Aap ise alag se style kar sakte hain --}}
                        <strong>Access Denied:</strong> {{ session('permission_error') }}
                    </div>
                @endif
                <div class="mx-auto max-w-7xl">
                    @can('view admin dashboard')
                        @yield('content')
                    @else
                        <div class="p-6 text-center text-red-600 bg-red-50 rounded-lg">
                            <i class="bi bi-lock-fill text-xl"></i>
                            <p class="mt-2 text-sm font-semibold">Access Denied — You don't have permission to view this
                                page.</p>
                        </div>
                    @endcan
                </div>
            </main>

            {{-- ✅ Footer --}}
            <footer class="p-4 text-sm text-gray-500 bg-white border-t">
                <div class="mx-auto text-center max-w-7xl">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </footer>

        </div>
    </div>

    {{-- ✅ Extra scripts (SweetAlert, etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    {{-- ✅ Scripts pushed from child views --}}
    @stack('scripts')

</body>

</html>
