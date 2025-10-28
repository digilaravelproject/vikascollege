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

<body class="bg-gray-50">

    {{-- Top Banner --}}
    @include('partials.top-banner')

    {{-- Menu --}}
    @include('partials.menu')



    {{-- Main Content --}}
    <main class="">
        @yield('content')
    </main>

    <footer class="bg-[#013954] text-white text-center py-6 mt-10">
        <p class="mb-0 text-sm">&copy; {{ date('Y') }} {{ setting('college_name') }}. All rights reserved.</p>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>

</body>

</html>