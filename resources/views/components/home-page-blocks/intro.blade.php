@php
    $layout = $block['layout'] ?? 'left'; // 'left', 'right', 'top'
    $image = $block['image'] ?? '';
    $heading = $block['heading'] ?? '';
    $text = $block['text'] ?? '';
    $btnText = $block['buttonText'] ?? 'Read More';
    $btnHref = $block['buttonHref'] ?? '#';

    $shortText = Str::limit(strip_tags($text), 700);
@endphp

{{--
MAIN GRID:
- Responsive (grid-cols-1 md:grid-cols-2)
- Vertically centered (items-center)
--}}
<div class="grid gap-10 md:gap-16 items-center
        {{ $layout === 'top' ? 'grid-cols-1' : 'md:grid-cols-2' }}">

    {{-- 1. IMAGE BLOCK --}}
    {{--
    Image wrapper. Isme 'overflow-hidden' zaroori hai parallax ke liye.
    AOS animation (load per) bhi hai.
    --}}
    <div class="overflow-hidden rounded-xl shadow-xl
            {{ $layout === 'right' ? 'md:order-last' : '' }}"
        data-aos="{{ $layout === 'right' ? 'fade-left' : ($layout === 'left' ? 'fade-right' : 'fade-up') }}"
        data-aos-duration="700">

        @if ($image)
            {{--
            YAHAN HAI NAYA CHANGE:
            'data-parallax-image' attribute add kiya hai.
            Yeh JS ko batayega ki is image ko animate karna hai.
            --}}
            <img src="{{ $image }}" alt="{{ $heading }}"
                class="object-cover w-full h-full min-h-[450px] transition-transform duration-500 hover:scale-105"
        data-parallax-image> {{-- <-- SCROLL EFFECT KE LIYE YEH ADD KIYA --}} @else <div
                        class="flex items-center justify-center w-full min-h-[450px] h-full bg-gray-200 rounded-xl">
                        <span class="text-gray-500">Image Placeholder</span>
                </div>
            @endif
</div>

{{-- 2. CONTENT BLOCK --}}
{{-- Text block pehle jaisa hi hai, AOS animation ke saath --}}
<div class="py-4 {{ $layout === 'top' ? 'text-center' : 'text-left' }}"
    data-aos="{{ $layout === 'right' ? 'fade-right' : ($layout === 'left' ? 'fade-left' : 'fade-up') }}"
    data-aos-duration="700" data-aos-delay="200">

    {{-- Heading --}}
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
        {{ $heading }}
    </h2>

    {{-- Decorative line --}}
    <div class="w-24 h-1.5 bg-blue-600 rounded-full my-6 {{ $layout === 'top' ? 'mx-auto' : '' }}"></div>

    {{-- Text Content --}}
    <div class="text-lg text-gray-600 leading-relaxed space-y-3 text-justify">
        {!! $shortText !!}
    </div>

    {{-- Button --}}
    @if ($btnText && $btnHref)
        <a href="{{ $btnHref }}" class="inline-block px-7 py-3 mt-8 text-base font-semibold text-white transition-all duration-300 bg-blue-600 rounded-lg shadow-lg
                                              hover:bg-blue-700 hover:-translate-y-1 hover:shadow-xl
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $btnText }}
        </a>
    @endif
</div>
</div>

{{--
YAHAN HAI SCROLL ANIMATION KA JAVASCRIPT
Yeh JS code 'data-parallax-image' waali sabhi images ko dhoondhega
aur unpar scroll effect apply karega.
--}}
@pushOnce('scripts')
<script>
    document.addEventListener('scroll', function () {
        // Performance ke liye function ko throttle kar rahe hain
        throttle(applyParallax, 16)(); // 16ms ~ 60fps
    });

    function applyParallax() {
        const images = document.querySelectorAll('[data-parallax-image]');
        const triggerOffset = window.innerHeight * 0.2; // 20% screen pe aane pe trigger

        images.forEach(image => {
            const wrapper = image.closest('div'); // Image ka parent container
            if (!wrapper) return;

            const rect = wrapper.getBoundingClientRect();
            const elTop = rect.top;
            const elHeight = rect.height;

            // Check karo ki element screen par hai
            if (elTop < (window.innerHeight - triggerOffset) && (elTop + elHeight) > triggerOffset) {
                // Element kitna screen par hai, uske hisab se value calculate karo
                // (0 jab element bottom me ho, 1 jab top me ho)
                let scrollPercent = (window.innerHeight - elTop) / (window.innerHeight + elHeight);
                // Value ko -0.5 se 0.5 ke beech rakho
                let centeredPercent = scrollPercent - 0.5;

                // 'intensity' set karo (image kitna move karegi)
                let intensity = 30; // 30px upar ya neeche
                let moveY = centeredPercent * intensity * -1; // -1 se direction reverse hoga

                // CSS transform apply karo
                image.style.transform = `translateY(${moveY.toFixed(2)}px) scale(1.05)`;
                // Scale 1.05 rakha hai taaki move karte time edges na dikhein
            }
        });
    }

    // Throttling function (performance ke liye)
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

    // Pehli baar load pe bhi chala do
    document.addEventListener('DOMContentLoaded', applyParallax);
</script>
@endpushOnce
