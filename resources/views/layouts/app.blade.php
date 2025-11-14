<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- SEO & Styles (keep these as they are) --}}
    {{-- ... (meta tags, title, description, favicon) ... --}}
    <link rel="icon" href="{{ asset('storage/' . setting('favicon')) }}" type="image/image">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    @php
        $seoTitle = setting('meta_title');
        $seoDescription = setting('meta_description');
        $seoImage = setting('meta_image');
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

    @stack('styles')
</head>

<body class="flex flex-col min-h-screen bg-gray-50 font-sans antialiased">

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

    {{-- ✅ Alpine.js CDN --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- 🌟 FIX: ONE Alpine root for all lead components (Sticky Buttons & Modals) --}}
    <div x-data="leadForms()" x-init="init()">
        @include('partials.sticky-lead-buttons')
        @include('partials.lead-modals')
    </div>

    <style>
        /* Your existing styles (Media Drag/Print/etc.) */
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

        @media print {
            body {
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>

    <script>
        // Your existing lockdown scripts (Ctrl+S, Right-click, etc.)
        (function () {
            document.addEventListener('keydown', function (event) {
                if (event.ctrlKey || event.metaKey) {
                    if (event.keyCode === 83 || event.keyCode === 80) {
                        event.preventDefault();
                    }
                }
            });

            document.addEventListener('contextmenu', function (event) {
                event.preventDefault();
            });

            document.addEventListener('DOMContentLoaded', function () {
                const videos = document.querySelectorAll('video');
                videos.forEach(function (video) {
                    video.setAttribute('controlslist', 'nodownload');
                });

                const pdfElements = document.querySelectorAll('embed[src$=".pdf"], iframe[src$=".pdf"]');
                pdfElements.forEach(function (el) {
                    if (!el.src.includes('#')) {
                        try {
                            el.src = el.src + '#toolbar=0&navpanes=0';
                        } catch (e) { }
                    }

                    if (el.tagName.toLowerCase() === 'iframe') {
                        el.setAttribute('sandbox', 'allow-scripts allow-same-origin');
                    }
                });
            });
        })();

        // 🌟 FIX: leadForms() function definition (Placed here ONCE for Alpine)
        function leadForms() {
            return {
                applyOpen: false,
                enquireOpen: false,
                otpSent: false,
                otpSending: false,
                otpVerified: false,
                otpFor: null,
                enteredOtp: '',
                sending: false,
                ad: { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', discipline: '', level: '', programme: '', authorised_contact: false },
                en: { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', level: '', discipline: '', programme: '', message: '', authorised_contact: false },

                init() { },
                openApply() { this.applyOpen = true; this.otpSent = false; this.otpVerified = false; this.enteredOtp = ''; this.otpFor = null; },
                closeApply() { this.applyOpen = false; },
                openEnquire() { this.enquireOpen = true; this.otpSent = false; this.otpVerified = false; this.enteredOtp = ''; this.otpFor = null; },
                closeEnquire() { this.enquireOpen = false; },

                async sendOtp(type) {
                    this.otpSending = true;
                    const email = (type === 'admission') ? this.ad.email : this.en.email;
                    try {
                        const res = await fetch('{{ route("send.otp") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ email, type })
                        });
                        const json = await res.json();
                        if (json.ok) {
                            this.otpSent = true;
                            this.otpFor = type;
                            alert('OTP sent to ' + email);
                        } else {
                            alert(json.message || 'Failed to send OTP');
                        }
                    } catch (e) {
                        alert('Error sending OTP');
                    } finally { this.otpSending = false; }
                },

                async verifyOtp(type) {
                    try {
                        const email = (type === 'admission') ? this.ad.email : this.en.email;
                        const res = await fetch('{{ route("verify.otp") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ email, otp: this.enteredOtp, type: type })
                        });
                        const json = await res.json();
                        if (json.ok) {
                            this.otpVerified = true;
                            alert('Email verified');
                        } else {
                            alert(json.message || 'Invalid OTP');
                        }
                    } catch (e) {
                        alert('Error verifying OTP');
                    }
                },

                async submitAdmission() {
                    if (!this.otpVerified) { alert('Please verify email first'); return; }
                    this.sending = true;
                    try {
                        const res = await fetch('{{ route("submit.admission") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(this.ad)
                        });
                        const json = await res.json();
                        if (json.ok) {
                            alert('Application submitted');
                            this.closeApply();
                            this.ad = { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', discipline: '', level: '', programme: '', authorised_contact: false };
                        } else {
                            alert(json.message || 'Submission failed');
                        }
                    } catch (e) {
                        alert('Error submitting');
                    } finally { this.sending = false; }
                },

                async submitEnquiry() {
                    if (!this.otpVerified) { alert('Please verify email first'); return; }
                    this.sending = true;
                    try {
                        const res = await fetch('{{ route("submit.enquiry") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(this.en)
                        });
                        const json = await res.json();
                        if (json.ok) {
                            alert('Enquiry submitted');
                            this.closeEnquire();
                            this.en = { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', level: '', discipline: '', programme: '', message: '', authorised_contact: false };
                        } else {
                            alert(json.message || 'Submission failed');
                        }
                    } catch (e) {
                        alert('Error submitting');
                    } finally { this.sending = false; }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true
        });
    </script>
</body>

</html>
