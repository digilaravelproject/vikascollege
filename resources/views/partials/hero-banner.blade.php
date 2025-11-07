<section class="w-full text-[#ffffff] overflow-hidden flex flex-wrap items-center border-[#D6DBE2]"
    style="background:linear-gradient(90deg, rgba(1, 39, 112, 0.1) 0%, #013954 62.5%);">

    <div
        class="flex items-center justify-center px-2 py-2 lg:px-5 sm:py-3 text-xs sm:text-sm md:text-base font-semibold tracking-wide text-white uppercase">
        📢 Announcement</div>

    {{-- <div
        class="flex items-center justify-center px-2 py-2 lg:px-5 sm:py-3 text-xs sm:text-sm md:text-base font-semibold tracking-wide text-white uppercase bg-[#0A1F44] announcement-label">
        📢 Announcement
    </div> --}}

    <div class="relative flex-1 py-2 overflow-hidden text-xs sm:text-sm md:text-[15px] font-medium tracking-wide">
        <div class="marquee">
            <div class="track">
                @php
                    $notifService = app(\App\Services\NotificationService::class);

                    // This is the update:
                    // We directly call the new function to get ONLY the top-featured notifications.
                    $marqueeNotifications = $notifService->getMarqueeNotifications();

                    // The old .filter() block is no longer needed.
                @endphp

                @if (count($marqueeNotifications))
                    @foreach ($marqueeNotifications as $n)
                        @php
                            $icon = $n->icon ?: '🔔';
                            $title = $n->title;
                            $href = $n->href;
                            $btn = $n->button_name ?: 'Click Here';
                        @endphp
                        <span>{{ $icon }} {{ $title }} — @if ($href)<a href="{{ $href }}"
                        class="marquee-link">{{ $btn }}</a>@endif</span>
                    @endforeach

                    @foreach ($marqueeNotifications as $n)
                        @php
                            $icon = $n->icon ?: '🔔';
                            $title = $n->title;
                            $href = $n->href;
                            $btn = $n->button_name ?: 'Click Here';
                        @endphp
                        <span>{{ $icon }} {{ $title }} — @if ($href)<a href="{{ $href }}"
                        class="marquee-link">{{ $btn }}</a>@endif</span>
                    @endforeach
                @else
                    {{-- This is your original fallback content --}}
                    <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                    <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                    <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                    <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>

                    <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                    <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                    <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                    <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 2. DYNAMIC BANNER SECTION (Slider) --}}
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
                            <video
                                class="w-full h-[380px] sm:h-[450px] lg:h-[520px] xl:h-[600px] object-cover object-center your-slider-video"
                                autoplay muted loop playsinline preload="metadata" ">
                                                                        <source src=" {{ asset('storage/' . $data['path']) }}"
                                type="video/mp4" />
                            Aapka browser video tag support nahi karta.
                            </video>
                        @endif

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

{{-- 3. NOTICE BOARD BUTTON (Aligned to the Right, Slightly Above Center) --}}
<button type="button" id="open-notice-modal"
        class="animate-pulse fixed z-40 right-0 top-1/2 -translate-y-1/2
               bg-white text-blue-800 font-semibold
               p-4 rounded-l-xl shadow-lg   py-3 px-3
               transition-all duration-300 ease-in-out
                hover:bg-blue-50 hover:shadow-xl group ">
    <span class="block text-[13px] font-medium tracking-wide" style="writing-mode: vertical-rl; transform: rotate(180deg);">
        Notice Board
    </span>
    <span
    class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full animate-pulse shadow-md ring-2 ring-white">
</span>
</button>



{{-- 4. NOTICE BOARD MODAL --}}
<div id="notice-modal"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 bg-black/50 transition-all duration-300 ease-out hidden opacity-0">

    <div id="notice-modal-content"
        class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col transform transition-all duration-300 ease-out scale-90 opacity-0 overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b bg-gradient-to-r from-blue-600 to-blue-800 text-white">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                📋 <span>Notice Board</span>
            </h3>
            <button type="button" id="close-notice-modal"
                class="text-white/80 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-3 overflow-y-auto text-[14px] scrollbar-thin scrollbar-thumb-blue-300 scrollbar-track-transparent">
            @if (count($marqueeNotifications))
                @foreach ($marqueeNotifications as $n)
                    @php
                        $icon = $n->icon ?: '🔔';
                        $title = $n->title;
                        $href = $n->href;
                        $btn = $n->button_name ?: 'View';
                    @endphp

                    <a href="{{ $href ?? '#' }}" target="_blank"
                        class="group flex items-center gap-3 py-3 px-4 border border-gray-200 rounded-xl transition-all duration-200 bg-white/60 hover:bg-blue-50 hover:scale-[1.01] shadow-sm">
                        <span class="text-lg w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-700 rounded-md">
                            {{ $icon }}
                        </span>
                        <div class="flex flex-col flex-grow min-w-0">
                            <p class="font-medium text-gray-800 truncate">{{ $title }}</p>
                        </div>
                        @if ($href)
                            <span
                                class="text-blue-600 text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $btn }} →
                            </span>
                        @endif
                    </a>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                    <span class="text-4xl mb-2">📭</span>
                    <p>No current announcements.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 5. CLEAN ANIMATION STYLE --}}
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
    /* 🔴 Pulse Indicator + Subtle Shake Animation */
    #open-notice-modal .animate-pulse {
        animation: pulse-shake 2s infinite;
    }

    @keyframes pulse-shake {
        0%, 100% {
            transform: translate(0, 0) scale(1);
            opacity: 1;
        }
        25% {
            transform: translate(1px, -1px) scale(1.2);
            opacity: 0.9;
        }
        50% {
            transform: translate(-1px, 1px) scale(1);
            opacity: 1;
        }
        75% {
            transform: translate(0px, -1px) scale(1.2);
            opacity: 0.9;
        }
    }

    /* Modal Animation */
    #notice-modal-content {
        transition: all 0.3s ease;
    }

    #notice-modal:not(.hidden) {
        animation: fadeIn 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            backdrop-filter: blur(0px);
        }
        to {
            opacity: 1;
            backdrop-filter: blur(3px);
        }
    }

    /* Scrollbar Styling */
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    .scrollbar-thumb-blue-300::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-thumb-blue-300::-webkit-scrollbar-thumb {
        background-color: #93c5fd;
        border-radius: 10px;
    }
</style>

{{-- 6. SCRIPT (Removed Hover-to-Open Logic, Click Only) --}}
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('open-notice-modal');
            const closeBtn = document.getElementById('close-notice-modal');
            const modal = document.getElementById('notice-modal');
            const modalContent = document.getElementById('notice-modal-content');

            const openModal = () => {
                modal.classList.remove('hidden', 'opacity-0');
                document.body.style.overflow = 'hidden';
                requestAnimationFrame(() => {
                    modalContent.classList.remove('scale-90', 'opacity-0');
                });
            };

            const closeModal = () => {
                modalContent.classList.add('scale-90', 'opacity-0');
                modal.classList.add('opacity-0');
                document.body.style.overflow = 'auto';
                setTimeout(() => modal.classList.add('hidden'), 250);
            };

            // Only Click Open Now
            openBtn.addEventListener('click', openModal);

            // Close Events
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => e.target === modal && closeModal());
            document.addEventListener('keydown', (e) => e.key === 'Escape' && !modal.classList.contains('hidden') && closeModal());
        });
    })();
</script>
