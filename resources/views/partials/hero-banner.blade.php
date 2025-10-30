<section class="w-full bg-[#F1F4F9] text-[#2E2E2E] overflow-hidden flex flex-wrap items-center border-[#D6DBE2]">
    <!-- Label -->
    <div
        class="flex items-center justify-center px-2 py-2 lg:px-5 sm:py-3 text-xs sm:text-sm md:text-base font-semibold tracking-wide text-white uppercase bg-[#0A1F44] announcement-label">
        📢 Announcement
    </div>

    <!-- Marquee -->
    <div class="relative flex-1 py-2 overflow-hidden text-xs sm:text-sm md:text-[15px] font-medium tracking-wide">
        <div class="marquee">
            <div class="track">
                <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>

                <!-- Duplicate for smooth infinite scroll -->
                <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>
            </div>
        </div>
    </div>
</section>



<!-- 🔹 Dynamic Banner Section (Image/Video Slider) -->
@php
    $banners = \App\Models\Setting::where('key', 'like', 'banner_media_%')->get();
@endphp

@if ($banners->count())
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <section class="relative w-full overflow-hidden banner-image">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach ($banners as $banner)
                    @php $data = json_decode($banner->value, true); @endphp
                    <div class="relative swiper-slide">
                        @if ($data['type'] === 'image')
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

                            @if (setting('banner_heading'))
                                <h1
                                    class="text-2xl font-extrabold leading-tight tracking-wide sm:text-3xl md:text-4xl lg:text-5xl drop-shadow-lg">
                                    {{ setting('banner_heading') }}
                                </h1>
                            @endif

                            @if (setting('banner_subheading'))
                                <p class="max-w-2xl mt-4 text-base text-gray-200 sm:text-lg md:text-xl">
                                    {{ setting('banner_subheading') }}
                                </p>
                            @endif

                            @if (setting('banner_button_text') && setting('banner_button_link'))
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
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            effect: "fade",
            fadeEffect: {
                crossFade: true
            },
        });
    </script>
@endif

<style>
    .banner-image {
        font-family: 'Montserrat', sans-serif;
    }

    .announcement-label {
        clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
        font-family: "Poppins", "Open Sans", sans-serif;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        margin-right: -3px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    /* Marquee wrapper */
    .marquee {
        overflow: hidden;
        white-space: nowrap;
        width: 100%;
    }

    /* Marquee track */
    .track {
        display: inline-flex;
        gap: 3rem;
        animation: marquee 25s linear infinite;
        will-change: transform;
    }

    .track span {
        display: inline-block;
        white-space: nowrap;
    }

    /* Links inside marquee */
    .marquee-link {
        color: #1E90FF;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s, text-decoration 0.3s;
    }

    .marquee-link:hover {
        color: #0A1F44;
        text-decoration: underline;
    }

    /* Animation */
    @keyframes marquee {
        0% {
            transform: translate3d(0, 0, 0);
        }

        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }

    /* 📱 Responsive Scaling */
    @media (max-width: 1024px) {
        .announcement-label {
            clip-path: polygon(0 0, 92% 0, 100% 50%, 92% 100%, 0 100%);
        }
    }

    @media (max-width: 768px) {
        .announcement-label {
            clip-path: polygon(0 0, 94% 0, 100% 50%, 94% 100%, 0 100%);
            font-size: 13px;
            /* padding: 6px 16px; */
        }

        .track {
            gap: 2rem;
            animation-duration: 30s;
        }
    }

    @media (max-width: 480px) {
        .announcement-label {
            clip-path: polygon(0 0, 96% 0, 100% 50%, 96% 100%, 0 100%);
            font-size: 12px;
            /* padding: 5px 14px; */
        }

        .track {
            gap: 1.5rem;
            animation-duration: 35s;
        }
    }
</style>