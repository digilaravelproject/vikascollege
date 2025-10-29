<section class="w-full bg-[#013954] text-white overflow-hidden flex items-center">
    <!-- Label -->
    <div
        class="flex items-center px-6 py-3 text-sm font-bold tracking-wide text-black uppercase bg-yellow-400 announcement-label sm:text-base">
        Announcement
    </div>

    <!-- Marquee -->
    <div class="relative flex-1 py-2 overflow-hidden text-sm font-medium tracking-wide sm:text-base">
        <div class="marquee">
            <div class="track">
                <span>🚀 Welcome to our website! Enjoy amazing deals and latest updates every week!</span>
                <span>🎉 Don’t miss our upcoming offers — stay tuned!</span>
                <span>🌟 Quality products, trusted service, and unbeatable prices!</span>

                <!-- Duplicate for seamless loop -->
                <span>🚀 Welcome to our website! Enjoy amazing deals and latest updates every week!</span>
                <span>🎉 Don’t miss our upcoming offers — stay tuned!</span>
                <span>🌟 Quality products, trusted service, and unbeatable prices!</span>
            </div>
        </div>
    </div>
</section>



<!-- 🔹 Dynamic Banner Section (Image/Video Slider) -->
@php
    $banners = \App\Models\Setting::where('key', 'like', 'banner_media_%')->get();
@endphp

@if($banners->count())
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <section class="relative w-full overflow-hidden banner-image">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                    @php $data = json_decode($banner->value, true); @endphp
                    <div class="relative swiper-slide">
                        @if($data['type'] === 'image')
                            <img src="{{ asset('storage/' . $data['path']) }}"
                                class="w-full h-[380px] sm:h-[450px] lg:h-[520px] xl:h-[600px] object-cover object-center transition-transform duration-700 hover:scale-105" />
                        @else
                            <video autoplay muted loop playsinline
                                class="w-full h-[380px] sm:h-[450px] lg:h-[520px] xl:h-[600px] object-cover object-center">
                                <source src="{{ asset('storage/' . $data['path']) }}" type="video/mp4" />
                            </video>
                        @endif

                        <!-- 🔸 Overlay -->
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-center px-5 text-center text-white bg-gradient-to-b from-black/50 via-black/40 to-black/30 sm:px-16">

                            @if(setting('banner_heading'))
                                <h1
                                    class="text-2xl font-extrabold leading-tight tracking-wide sm:text-3xl md:text-4xl lg:text-5xl drop-shadow-lg">
                                    {{ setting('banner_heading') }}
                                </h1>
                            @endif

                            @if(setting('banner_subheading'))
                                <p class="max-w-2xl mt-4 text-base text-gray-200 sm:text-lg md:text-xl">
                                    {{ setting('banner_subheading') }}
                                </p>
                            @endif

                            @if(setting('banner_button_text') && setting('banner_button_link'))
                                <a href="{{ setting('banner_button_link') }}"
                                    class="inline-block px-6 py-3 mt-6 text-sm font-semibold text-white transition duration-300 transform bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 sm:text-base sm:px-8 hover:scale-105">
                                    {{ setting('banner_button_text') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Swiper Controls -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <script>
        new Swiper(".mySwiper", {
            loop: true,
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            autoplay: { delay: 5000, disableOnInteraction: false },
            effect: "fade",
            fadeEffect: { crossFade: true },
        });
    </script>
@endif

<style>
    .banner-image {
        font-family: 'Montserrat', sans-serif;
    }

    .announcement-label {
        position: relative;
        clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
        z-index: 10;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
    }

    /* Marquee Container */
    .marquee {
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        width: 100%;
    }

    /* Moving Track */
    .track {
        display: inline-flex;
        gap: 4rem;
        animation: marquee 20s linear infinite;
        will-change: transform;
        backface-visibility: hidden;
        transform: translate3d(0, 0, 0);
    }

    .track {
        animation-fill-mode: forwards;
        transform-origin: center;
    }

    /* Text */
    .track span {
        display: inline-block;
        white-space: nowrap;
    }

    /* Smooth Infinite Scroll Animation */
    @keyframes marquee {
        0% {
            transform: translate3d(0, 0, 0);
        }

        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }
</style>
