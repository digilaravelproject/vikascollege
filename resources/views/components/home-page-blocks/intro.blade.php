@php
    $layout = $block['layout'] ?? 'left'; // 'left', 'right', 'top'
    $image = $block['image'] ?? '';
    $heading = $block['heading'] ?? '';
    $text = $block['text'] ?? '';
    $btnText = $block['buttonText'] ?? 'Read More';
    $btnHref = $block['buttonLink'] ?? '/#';

    $shortText = Str::limit(strip_tags($text), 700);
@endphp

{{-- 
    MAIN GRID:
    - Reduced Gap: gap-6 md:gap-10 (Pehle gap-8/16 tha)
    - Compact Layout
--}}
<div class="grid gap-6 md:gap-10 items-center 
     {{ $layout === 'top' ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2' }}">

    {{-- 
        1. IMAGE BLOCK 
        - Mobile: Order 2 (Baad me dikhega)
        - Height: Optimized for mobile landscape
    --}}
    <div class="overflow-hidden rounded-none md:rounded-xl shadow-none md:shadow-xl order-2
         {{ $layout === 'right' ? 'md:order-last' : 'md:order-first' }}
         {{ $layout === 'top' ? 'md:order-2' : '' }}"
         data-aos="{{ $layout === 'right' ? 'fade-left' : ($layout === 'left' ? 'fade-right' : 'fade-up') }}"
         data-aos-duration="700">

        @if ($image)
            <img src="{{ $image }}" alt="{{ $heading }}"
                 class="object-cover w-full h-56 sm:h-64 md:h-full md:min-h-[400px] transition-transform duration-500 hover:scale-105"
                 data-parallax-image> 
        @else 
            <div class="flex items-center justify-center w-full h-56 sm:h-64 md:h-full md:min-h-[400px] bg-gray-200 rounded-xl">
                <span class="text-gray-500">Image Placeholder</span>
            </div>
        @endif
    </div>

    {{-- 
        2. CONTENT BLOCK 
        - Mobile: Order 1 (Pehle dikhega)
        - Padding: py-0 px-2 (0 0.5rem) as requested
    --}}
    <div class="py-0 px-2 text-left order-1 
         {{ $layout === 'right' ? 'md:order-first' : 'md:order-last' }}
         {{ $layout === 'top' ? 'md:order-1' : '' }}"
         data-aos="{{ $layout === 'right' ? 'fade-right' : ($layout === 'left' ? 'fade-left' : 'fade-up') }}"
         data-aos-duration="700" data-aos-delay="200">

        {{-- Heading with !important Left Align --}}
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
            style="text-align: left !important;">
            {{ $heading }}
        </h2>

        {{-- Decorative line (Margin reduced: my-4) --}}
        <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 mr-auto"></div>

        {{-- Text Content --}}
        <div class="text-lg text-gray-600 leading-relaxed space-y-2 text-justify">
            {!! $shortText !!}
        </div>

        {{-- Button (Margin reduced: mt-6) --}}
        @if ($btnText && $btnHref)
            <a href="{{ $btnHref }}" 
               class="inline-block px-7 py-3 mt-6 text-base font-semibold text-white transition-all duration-300 bg-[#013954] rounded-lg shadow-lg
                      hover:bg-[#013954]/90 hover:-translate-y-1 hover:shadow-xl
                      focus:outline-none focus:ring-2 focus:ring-[#013954] focus:ring-offset-2">
                {{ $btnText }}
            </a>
        @endif
    </div>
</div>

{{-- PARALLAX SCRIPT --}}
@pushOnce('scripts')
<script>
    document.addEventListener('scroll', function () {
        throttle(applyParallax, 16)(); 
    });

    function applyParallax() {
        const images = document.querySelectorAll('[data-parallax-image]');
        const isMobile = window.innerWidth < 768;
        const triggerOffset = window.innerHeight * 0.2; 

        images.forEach(image => {
            const wrapper = image.closest('div'); 
            if (!wrapper) return;

            const rect = wrapper.getBoundingClientRect();
            const elTop = rect.top;
            const elHeight = rect.height;

            if (elTop < (window.innerHeight - triggerOffset) && (elTop + elHeight) > triggerOffset) {
                let scrollPercent = (window.innerHeight - elTop) / (window.innerHeight + elHeight);
                let centeredPercent = scrollPercent - 0.5;
                let intensity = isMobile ? 15 : 30; 
                let moveY = centeredPercent * intensity * -1; 

                image.style.transform = `translateY(${moveY.toFixed(2)}px) scale(1.05)`;
            }
        });
    }

    let throttleTimer = false;
    function throttle(callback, time) {
        return function () {
            if (throttleTimer) return;
            throttleTimer = true;
            setTimeout(() => {
                callback.apply(this, arguments);
                throttleTimer = false;
            }, time);
        }
    }
    document.addEventListener('DOMContentLoaded', applyParallax);
</script>
@endpushOnce