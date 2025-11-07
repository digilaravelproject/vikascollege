<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- ✅ Favicon --}}
    <link rel="icon" href="{{ asset('storage/' . setting('favicon')) }}" type="image/image">

    @php
        // Database se settings fetch karna
        $seoTitle = setting('meta_title');
        $seoDescription = setting('meta_description');
        $seoImage = setting('meta_image');

        // Agar meta_title set nahi hai, toh default college_name use karna
        // setting('college_name', config('app.name')) ensures a fallback
        $pageTitle = $seoTitle ?: setting('college_name', config('app.name'));
    @endphp

    <title>@yield('title', $pageTitle)</title>
    <meta name="description" content="@yield('description', $seoDescription)">
    <meta name="author" content="{{ setting('college_name') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $pageTitle)">
    <meta property="og:description" content="@yield('description', $seoDescription)">
    @if ($seoImage)
        <meta property="og:image" content="{{ asset('storage/' . $seoImage) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', $pageTitle)">
    <meta name="twitter:description" content="@yield('description', $seoDescription)">
    @if ($seoImage)
        <meta name="twitter:image" content="{{ asset('storage/' . $seoImage) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ Styles pushed from child views (like scroll animation CSS) --}}
    @stack('styles')
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    {{-- ✅ Top Banner --}}
    @include('partials.top-banner')

    {{-- ✅ Menu --}}
    @include('partials.menu')

    {{-- ✅ Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ✅ Footer --}}
    @include('partials.footer')

    {{-- ✅ Alpine.js (only one version, latest stable) --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Prevent dragging of media */
        img,
        video,
        embed,
        iframe {
            -webkit-user-drag: none;
            user-drag: none;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide content on print */
        @media print {
            body {
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>

    <script>
        (function () {
            // --- 1. Disable Ctrl+S / Ctrl+P shortcuts ---
            document.addEventListener('keydown', function (event) {
                if (event.ctrlKey || event.metaKey) {
                    if (event.keyCode === 83 || event.keyCode === 80) {
                        event.preventDefault();
                    }
                }
            });

            // --- 2. Disable right-click ---
            document.addEventListener('contextmenu', function (event) {
                event.preventDefault();
            });

            // --- 3. Disable video download button ---
            document.addEventListener('DOMContentLoaded', function () {
                const videos = document.querySelectorAll('video');
                videos.forEach(function (video) {
                    video.setAttribute('controlslist', 'nodownload');
                });

                // --- 4. Lockdown PDFs ---
                const pdfElements = document.querySelectorAll('embed[src$=".pdf"], iframe[src$=".pdf"]');
                pdfElements.forEach(function (el) {
                    // Hide toolbar (for Chrome/Edge)
                    if (!el.src.includes('#')) {
                        try {
                            el.src = el.src + '#toolbar=0&navpanes=0';
                        } catch (e) { }
                    }

                    // Sandbox for iframes
                    if (el.tagName.toLowerCase() === 'iframe') {
                        el.setAttribute('sandbox', 'allow-scripts allow-same-origin');
                    }
                });
            });
        })();
    </script>

</body>

</html>
