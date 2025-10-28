@php
    $topBannerImage = setting('top_banner_image');
@endphp

@if($topBannerImage)
    <div class="relative w-full bg-white overflow-hidden">
        <div class="w-full py-1">
            <img src="{{ asset('storage/' . $topBannerImage) }}" alt="Top Banner"
                class="w-full h-20 sm:h-28 md:h-32 lg:h-36 object-contain object-center transition-all duration-500 ease-in-out">

        </div>

        {{-- Optional overlay text (you can remove this if not needed) --}}
        {{-- @if(setting('top_banner_text'))
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
            <h2 class="text-white text-lg sm:text-2xl md:text-3xl font-semibold drop-shadow-md text-center">
                {{ setting('top_banner_text') }}
            </h2>
        </div>
        @endif --}}
    </div>
@endif