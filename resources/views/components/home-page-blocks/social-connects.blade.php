{{-- === Main Title Section === --}}
<div class="text-center" data-aos="fade-up">
    <h2 class="mb-4 text-4xl font-medium text-gray-900" style="font-family: 'Times New Roman', serif;">
        Follow Us
    </h2>
    <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 m-auto"></div>
</div>
{{-- === End Main Title Section === --}}

{{-- === Social Media Section Start === --}}
@php
    $facebook = setting('facebook_url');
    $twitter = setting('twitter_url');
    $instagram = setting('instagram_url');
    $youtube = setting('youtube_url');
    $linkedin = setting('linkedin_url');
@endphp

@if ($facebook || $twitter || $instagram || $youtube || $linkedin)
    <div class="flex flex-wrap justify-center gap-6 mt-8 mb-10" data-aos="fade-up" data-aos-delay="100">

        {{-- Facebook --}}
        {{-- Logic: Default Blue Background & White Text -> Hover Light Blue Background & Blue Text --}}
        @if ($facebook)
            <a href="{{ $facebook }}" target="_blank" rel="noopener"
                class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-300 bg-blue-600 rounded-full shadow-sm group hover:bg-blue-50 hover:text-blue-600"
                aria-label="Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0 0 22 12" />
                </svg>
            </a>
        @endif

        {{-- Instagram --}}
        {{-- Logic: Default Gradient & White Text -> Hover Light Pink Background & Pink Text --}}
        {{-- Note: Gradient ko hover pe hatane ke liye hum 'hover:from-pink-50' etc use kar rahe hain taaki gradient
        overwrite ho jaye --}}
        @if ($instagram)
            <a href="{{ $instagram }}" target="_blank" rel="noopener"
                class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-300 rounded-full shadow-sm group bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 hover:from-pink-50 hover:via-pink-50 hover:to-pink-50 hover:bg-pink-50 hover:text-pink-600"
                aria-label="Instagram">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.4.4.6.2 1 .4 1.4.8.4.4.7.8.8 1.4.2.5.3 1.2.4 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.4-.2.6-.4 1-.8 1.4-.4.4-.8.7-1.4.8-.5.2-1.2.3-2.4.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.4-.4-.6-.2-1-.4-1.4-.8-.4-.4-.7-.8-.8-1.4-.2-.5-.3-1.2-.4-2.4C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.4.2-.6.4-1 .8-1.4.4-.4.8-.7 1.4-.8.5-.2 1.2-.3 2.4-.4C8.4 2.2 8.8 2.2 12 2.2m0 1.8c-3.1 0-3.5 0-4.7.1-1 .1-1.5.2-1.9.3-.5.2-.8.3-1.1.6-.3.3-.5.6-.6 1.1-.1.4-.3.9-.3 1.9-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1 .2 1.5.3 1.9.2.5.3.8.6 1.1.3.3.6.5 1.1.6.4.1.9.3 1.9.3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1 0 1.5-.2 1.9-.3.5-.2.8-.3 1.1-.6.3-.3.5-.6.6-1.1.1-.4.3-.9.3-1.9.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c0-1-.2-1.5-.3-1.9-.2-.5-.3-.8-.6-1.1-.3-.3-.6-.5-1.1-.6-.4-.1-.9-.3-1.9-.3-1.2-.1-1.6-.1-4.7-.1m0 2.3a5.7 5.7 0 1 1 0 11.4 5.7 5.7 0 0 1 0-11.4m0 1.8a3.9 3.9 0 1 0 0 7.8 3.9 3.9 0 0 0 0-7.8M17.6 6a1.3 1.3 0 1 0 0 2.6 1.3 1.3 0 0 0 0-2.6z" />
                </svg>
            </a>
        @endif

        {{-- Twitter / X --}}
        {{-- Logic: Default Sky Blue & White Text -> Hover Light Sky & Sky Text --}}
        @if ($twitter)
            <a href="{{ $twitter }}" target="_blank" rel="noopener"
                class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-300 rounded-full shadow-sm group bg-sky-500 hover:bg-sky-50 hover:text-sky-500"
                aria-label="Twitter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M22.46 6c-.77.35-1.6.58-2.46.69a4.25 4.25 0 0 0 1.86-2.35 8.52 8.52 0 0 1-2.7 1.03 4.24 4.24 0 0 0-7.22 3.87A12.04 12.04 0 0 1 3.1 4.9a4.22 4.22 0 0 0 1.31 5.66 4.2 4.2 0 0 1-1.92-.53v.05a4.24 4.24 0 0 0 3.4 4.16 4.25 4.25 0 0 1-1.91.07 4.24 4.24 0 0 0 3.95 2.93A8.5 8.5 0 0 1 2 19.54a12.02 12.02 0 0 0 6.51 1.91c7.82 0 12.1-6.48 12.1-12.1l-.01-.55A8.64 8.64 0 0 0 22.46 6z" />
                </svg>
            </a>
        @endif

        {{-- YouTube --}}
        {{-- Logic: Default Red Background & White Text -> Hover Light Red & Red Text --}}
        @if ($youtube)
            <a href="{{ $youtube }}" target="_blank" rel="noopener"
                class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-300 bg-red-600 rounded-full shadow-sm group hover:bg-red-50 hover:text-red-600"
                aria-label="YouTube">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 0 0-2.1 2.1C0 8.1 0 12 0 12s0 3.9.6 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.5 15.5v-7l6 3.5-6 3.5z" />
                </svg>
            </a>
        @endif

        {{-- LinkedIn --}}
        {{-- Logic: Default Dark Blue & White Text -> Hover Light Blue & Dark Blue Text --}}
        @if ($linkedin)
            <a href="{{ $linkedin }}" target="_blank" rel="noopener"
                class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-300 bg-blue-700 rounded-full shadow-sm group hover:bg-blue-50 hover:text-blue-700"
                aria-label="LinkedIn">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M6.94 6.5a2.19 2.19 0 1 1-4.38 0 2.19 2.19 0 0 1 4.38 0M2.88 8.82h3.82v12.3H2.88zM9.08 8.82h3.66v1.68h.05c.51-.96 1.76-1.98 3.62-1.98 3.88 0 4.6 2.55 4.6 5.86v6.74h-3.82v-5.98c0-1.43-.03-3.28-2-3.28-2 0-2.3 1.56-2.3 3.17v6.09H9.08z" />
                </svg>
            </a>
        @endif

    </div>
@endif
{{-- === Social Media Section End === --}}