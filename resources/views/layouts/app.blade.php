<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('college_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('storage/' . setting('favicon')) }}" type="image/x-icon">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    {{-- Top Banner --}}
    @include('partials.top-banner')

    {{-- Menu --}}
    @include('partials.menu')

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
        /* 1. Images/Videos ko drag-and-drop se rokna */
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

        /* 2. (ENHANCEMENT) Print karne par page ko blank kar dena */
        @media print {
            body {
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>

    <script>
        (function () {
            // --- 1. KEYBOARD SHORTCUTS BLOCKER (Ctrl+S, Ctrl+P) ---
            // Yeh DOMContentLoaded ke bahar hona zaroori hai taaki turant kaam kare
            document.addEventListener('keydown', function (event) {
                // 'metaKey' Mac par Command key ke liye hai
                if (event.ctrlKey || event.metaKey) {
                    // 's' (83) ya 'p' (80) ko check karo
                    if (event.keyCode === 83 || event.keyCode === 80) {
                        event.preventDefault(); // Default action (Save/Print) ko roko
                        // console.warn('Save/Print shortcut disabled.'); // Testing ke liye
                    }
                }
            });

            // --- Baaki script page load hone ke baad chalegi ---
            document.addEventListener('DOMContentLoaded', function () {

                // --- 2. PURI WEBSITE PAR RIGHT-CLICK DISABLE KAREIN ---
                document.addEventListener('contextmenu', function (event) {
                    event.preventDefault(); // Right-click menu ko roko
                });

                // --- 3. SABHI VIDEOS SE DOWNLOAD BUTTON HATAYEIN ---
                const videos = document.querySelectorAll('video');
                videos.forEach(function (video) {
                    video.setAttribute('controlslist', 'nodownload');
                });

                // --- 4. SABHI PDFs KO LOCKDOWN KAREIN (MERGED LOGIC) ---
                // Yeh <embed> aur <iframe> dono ko dhoondhega
                const pdfElements = document.querySelectorAll('embed[src$=".pdf"], iframe[src$=".pdf"]');

                pdfElements.forEach(function (el) {

                    // (A) Toolbar ko hide karne ki koshish (Chrome/Edge ke liye)
                    // Yeh <embed> aur <iframe> dono par kaam karega
                    if (!el.src.includes('#')) {
                        try {
                            // try...catch block mein daala taaki agar cross-origin error aaye toh script crash na ho
                            el.src = el.src + '#toolbar=0&navpanes=0';
                        } catch (e) {
                            // console.error('Could not modify PDF src:', e);
                        }
                    }

                    // (B) IFRAME-specific lockdown (Sandbox)
                    // Yeh check karega ki element iframe hai ya nahi
                    if (el.tagName.toLowerCase() === 'iframe') {
                        // Yeh Firefox/Safari ke liye 'download' rokega
                        el.setAttribute('sandbox', 'allow-scripts allow-same-origin');
                    }
                });

            });
        })(); // Script ko turant execute karne ke liye wrapper
    </script>
</body>

</html>