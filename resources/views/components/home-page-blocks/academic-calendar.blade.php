{{-- ========================================= --}}
{{-- Styles: Swiper CSS & Custom Overrides --}}
{{-- ========================================= --}}
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<style>
    /* ---------- ARROWS (fixed: only one icon visible) ---------- */
    .calendar-swiper .swiper-button-next,
    .calendar-swiper .swiper-button-prev {
        color: #DC2626;
        background: rgba(255, 255, 255, 0.85);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-size: 12px;
        background-repeat: no-repeat;
        background-position: center;
    }

    /* remove default arrows completely */
    .calendar-swiper .swiper-button-next:after,
    .calendar-swiper .swiper-button-prev:after {
        content: '' !important;
    }

    /* custom SVG icons */
    .calendar-swiper .swiper-button-next {
        background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' fill='red' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 3l5 5-5 5' stroke='%23DC2626' stroke-width='2' fill='none'/%3E%3C/svg%3E");
    }

    .calendar-swiper .swiper-button-prev {
        background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' fill='red' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 3L6 8l5 5' stroke='%23DC2626' stroke-width='2' fill='none'/%3E%3C/svg%3E");
    }

    /* ---------- Pagination active dot ---------- */
    .swiper-pagination-bullet-active {
        background: #DC2626 !important;
    }

    /* ---------- Equal Card Height Wrapper ---------- */
    .swiper-slide {
        display: flex;
        height: auto;
    }

    .calendar-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* ---------- Full description (no limit) ---------- */
    .calendar-desc {
        overflow: visible !important;
    }

    /* ---------- Title with custom size + !important ---------- */
    .calendar-title {
        font-size: 22px !important;
        line-height: 1.3 !important;
        margin-bottom: 1rem;
    }
</style>

<section class="bg-white relative">
    <div class="text-center mb-12" data-aos="fade-up">
        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-gray-900 tracking-tight mb-4">
            {{ $title }}
        </h2>
    </div>

    @if ($items->isEmpty())
        <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="100">
            No calendar items found.
        </p>
    @else
        <div class="mx-auto px-4 sm:px-6 relative" data-aos="fade-up" data-aos-delay="100">

            <div class="swiper calendar-swiper pb-12 px-2">
                <div class="swiper-wrapper">

                    @foreach ($items as $item)
                        <div class="swiper-slide">

                            <div class="calendar-card bg-gray-50 p-6 sm:p-8
                                                            rounded-lg shadow-sm hover:shadow-md
                                                            hover:-translate-y-1 transition-all duration-300
                                                            border border-transparent hover:border-gray-200">

                                <a href="{{ $item->link_href }}" class="flex flex-col h-full">

                                    <!-- Date -->
                                    <div class="pb-5 border-b border-gray-300 mb-5">
                                        <p class="text-5xl font-extrabold text-red-600 leading-none">
                                            {{ $item->event_datetime->format('d') }}
                                        </p>
                                        <p class="text-xl text-gray-900">
                                            {{ $item->event_datetime->format('F Y') }}
                                        </p>
                                    </div>

                                    <!-- Title (full + !important size) -->
                                    <h3 class="calendar-title font-bold text-gray-900">
                                        {{ $item->title }}
                                    </h3>

                                    <!-- Time -->
                                    <p class="text-lg font-extrabold text-red-600 mb-4">
                                        {{ $item->event_datetime->format('g:i A') }} –
                                        {{ $item->end_time ? $item->end_time->format('g:i A') : '10:00 AM' }}
                                    </p>

                                    <!-- Description (FULL, not cut) -->
                                    <p class="text-sm text-gray-600 calendar">
                                        {{ $item->description }}
                                    </p>

                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>

                @if($items->count() > 3)
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                @endif
            </div>

        </div>
    @endif
</section>



{{-- ========================================= --}}
{{-- Scripts: AOS & Swiper JS --}}
{{-- ========================================= --}}
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-in-out'
        });

        const swiper = new Swiper('.calendar-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            grabCursor: true,

            breakpoints: {
                480: { slidesPerView: 1.4, spaceBetween: 20 },
                640: { slidesPerView: 2, spaceBetween: 24 },
                1024: { slidesPerView: 3, spaceBetween: 30 },
            },

            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },

            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

    });
</script>
