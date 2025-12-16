@php
    $id = uniqid('scroll_links_');
    $links = $block['links'] ?? [];
@endphp

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

    /* Smooth Auto Scroll */
    .auto-scroll {
        scroll-behavior: smooth;
        position: relative;
    }

    .title-auto {
        font-size: clamp(1rem, 2vw, 1.6rem) !important;
        line-height: 1.2 !important;
        color: white !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 90%;
        margin:auto !important;
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
        background: linear-gradient(to bottom, rgba(255,255,255,1), rgba(255,255,255,0));
    }
    .fade-bottom {
        bottom: 0;
        background: linear-gradient(to top, rgba(255,255,255,1), rgba(255,255,255,0));
    }

    /* Fade-in animation */
    .fade-item {
        opacity: 0;
        animation: fadeIn 1s ease forwards;
    }
    @keyframes fadeIn {
        to { opacity: 1; }
    }
</style>


<div class="scroll-container w-full shadow-xl overflow-hidden rounded-xl" 
     data-aos="fade-up" data-aos-delay="100">

    <!-- Header -->
<div class="scroll-header bg-[#0B2B3F] text-center font-bold uppercase px-5 shadow-md
            h-24 flex flex-col items-center justify-center">

    <h2 class="title-auto text-white font-sans text-center px-2">
        {{ $title }}
    </h2>

</div>



    <!-- Body (Responsive & Standard Height) -->
    <div id="{{ $id }}"
         class="relative p-6 h-80 md:h-96 overflow-y-auto auto-scroll custom-scrollbar">

        <!-- Fade overlays -->
        <div class="fade-top"></div>
        <div class="fade-bottom"></div>

        <div class="space-y-4 text-gray-700 font-sans text-sm">

            @forelse ($links as $link)
                <p class="fade-item">
                    <a href="{{ $link['url'] ?? '#' }}" 
                       target="_blank"
                       class="text-[#0B2B3F] hover:underline font-medium">
                        {{ $link['text'] ?? 'Link Item' }}
                    </a>
                </p>
            @empty
                <p class="text-gray-500">No links added.</p>
            @endforelse
        </div>

    </div>
</div>


<!-- Auto Scroll Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    function initAutoScroll(boxId, speed = 1.2) {
        const box = document.getElementById(boxId);
        if (!box) return;

        let direction = 1;
        let interval;

        function start() {
            interval = setInterval(() => {
                box.scrollTop += direction * speed;

                if (box.scrollTop + box.clientHeight >= box.scrollHeight - 1) {
                    direction = -1; // bottom → go up
                }
                if (box.scrollTop <= 0) {
                    direction = 1; // top → go down
                }

            }, 30);
        }

        function stop() {
            clearInterval(interval);
        }

        start();
        box.addEventListener("mouseenter", stop);
        box.addEventListener("mouseleave", start);
    }

    initAutoScroll("{{ $id }}", 1.2);
});
</script>
