@php
    $topBannerImage = setting('top_banner_image');
@endphp

@if($topBannerImage)
    <div class="w-full bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto flex items-center justify-between px-4 py-2">

            {{-- Left: Banner Image --}}
            <div class="flex-shrink-0">
                <img src="{{ asset('storage/' . $topBannerImage) }}" alt="Top Banner"
                    class="h-12 sm:h-16 md:h-20 lg:h-24 object-contain object-left transition-all duration-300 ease-in-out">
            </div>

            {{-- Right: Menu Section --}}
            <div class="flex items-center space-x-4 text-gray-700 font-medium text-sm sm:text-base">
                <a href="#" class="hover:text-blue-600 transition">Home</a>
                <a href="#" class="hover:text-blue-600 transition">About</a>
                <a href="#" class="hover:text-blue-600 transition">Services</a>
                <a href="#" class="hover:text-blue-600 transition">Contact</a>
            </div>
        </div>
    </div>
@endif
