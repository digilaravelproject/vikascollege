@props(['title', 'items', 'id' => uniqid('scroll_')])

<style>
    /* === Custom Scrollbar === */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #1f497d;
    }

    /* Auto Scroll Smooth */
    .auto-scroll {
        scroll-behavior: smooth;
        position: relative;
    }

    /* Dark Mode */
    .dark .scroll-container {
        background: #0f172a;
        /* slate-900 */
        color: #e2e8f0;
        /* slate-200 */
    }

    .dark .scroll-header {
        background: #334155;
        /* slate-700 */
        color: white;
    }

    .dark .fade-top,
    .dark .fade-bottom {
        background: linear-gradient(to bottom, rgba(15, 23, 42, 1), rgba(15, 23, 42, 0));
    }

    /* Fade overlays */
    .fade-top,
    .fade-bottom {
        position: absolute;
        left: 0;
        right: 0;
        height: 25px;
        pointer-events: none;
        z-index: 10;
    }

    .fade-top {
        top: 0;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
    }

    .fade-bottom {
        bottom: 0;
        background: linear-gradient(to top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
    }

    /* fade-in for list items */
    .fade-item {
        opacity: 0;
        animation: fadeIn 1s ease forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }
</style>


<div class="scroll-container w-full bg-white  shadow-xl overflow-hidden rounded-xl" data-aos="fade-up"
    data-aos-delay="100">

    <!-- Header -->
    <div class="scroll-header bg-[#0B2B3F] text-white text-center font-bold uppercase py-3 px-5 shadow-md">
        <h3 class="text-xl tracking-wide font-sans" style="color:white !important;">
            {{ $title }}
        </h3>
    </div>

    <!-- Scrollable Body -->
    <div id="{{ $id }}" class="relative p-6 h-80 md:h-96 overflow-y-auto auto-scroll custom-scrollbar">

        <!-- Fade overlays -->
        <div class="fade-top"></div>
        <div class="fade-bottom"></div>

        <div class="space-y-4 text-gray-700 dark:text-slate-200 font-sans text-sm">
            @forelse ($items as $item)
                <p class="fade-item">
                    @if ($item->link)
                        <a href="{{ $item->link }}" target="_blank" class="text-[#0B2B3F]  hover:underline font-medium">
                            {{ $item->title }}
                        </a>
                    @else
                        {{ $item->title }}
                    @endif
                </p>
            @empty
                <p class="text-gray-500 dark:text-slate-400">No announcements found.</p>
            @endforelse
        </div>

        @if ($items->first()?->link)
            <a href="{{ $items->first()->link }}" target="_blank"
                class="inline-block mt-5 font-semibold text-[#0B2B3F] dark:text-red-400 hover:underline font-sans fade-item">
                Read More....
            </a>
        @endif

    </div>
</div>


<!-- === Auto Scroll Script | Supports MULTIPLE Boxes === -->
<script>
    document.addEventListener("DOMContentLoaded", function () {

        function initAutoScroll(boxId, speed = 1) {
            const box = document.getElementById(boxId);
            if (!box) return;

            let direction = 1;      // 1 = down, -1 = up
            let interval;

            function start() {
                interval = setInterval(() => {
                    box.scrollTop += direction * speed;

                    // At bottom → reverse direction
                    if (box.scrollTop + box.clientHeight >= box.scrollHeight - 1) {
                        direction = -1;
                    }

                    // At top → reverse again
                    if (box.scrollTop <= 0) {
                        direction = 1;
                    }

                }, 30);
            }

            function stop() {
                clearInterval(interval);
            }

            start();

            // Pause on hover
            box.addEventListener("mouseenter", stop);
            box.addEventListener("mouseleave", start);
        }

        // ===== Initialize this instance =====
        initAutoScroll("{{ $id }}", 1.2);   // 🔥 Change speed here (1 = slow, 3 = fast)

    });
</script>
