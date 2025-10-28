<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - {{ setting('college_name') }}</title>
    <link rel="icon" href="{{ asset('storage/' . setting('favicon')) }}" type="image/x-icon">

    {{-- Tailwind CDN for rapid prototyping. Replace with Vite-built assets in production. --}}
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

    {{-- Alpine.js for small interactions --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Optional: Heroicons (SVG) CDN --}}
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free/css/all.min.css">

    <style>
        /* tiny tweaks to make cards look crisp */
        .card-shadow {
            box-shadow: 0 6px 18px rgba(8, 15, 52, 0.08);
        }

        .glass {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(6px);
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 text-gray-800" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.admin.partials.sidebar')

        <!-- Content area -->
        <div class="flex-1 flex flex-col overflow-hidden lg:pl-0">

            <!-- Topbar -->
            @include('layouts.admin.partials.topbar')

            <!-- Main -->
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <footer class="border-t bg-white p-4 text-sm text-gray-500">
                <div class="max-w-7xl mx-auto text-center">© {{ date('Y') }} {{ config('app.name') }}. All rights
                    reserved.</div>
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>

</html>