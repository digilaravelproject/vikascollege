@php
    $topBannerImage = setting('top_banner_image');
@endphp

@if($topBannerImage)
    <div class="w-full bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto flex flex-col sm:flex-row items-center justify-center sm:justify-between px-4 py-2">

            {{-- Left: Banner Image --}}
            <div class="flex-shrink-0 transform transition-transform duration-300 ease-in-out text-center sm:text-left">
                <img src="{{ asset('storage/' . $topBannerImage) }}" alt="Top Banner"
                    class="h-16 sm:h-16 md:h-20 lg:h-24 object-contain object-center sm:object-left mx-auto sm:mx-0">
            </div>

            {{-- Right: Menu Section (hidden on mobile) --}}
            <div class="hidden lg:flex items-center space-x-4 text-gray-700 font-medium text-sm sm:text-base">
                <a href="#" class="hover:text-blue-600 transition">Home</a>
                <a href="#" class="hover:text-blue-600 transition">About</a>
                <a href="#" class="hover:text-blue-600 transition">Services</a>
                <a href="#" class="hover:text-blue-600 transition">Contact</a>
            </div>
        </div>
    </div>
@endif
