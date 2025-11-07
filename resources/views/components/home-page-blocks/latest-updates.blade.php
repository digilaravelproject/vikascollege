@php
    // Animation ki speed ko items ke count ke hisab se set karenge
    $itemCount = $items->count();
    // Har item ke liye 4 seconds. Kam time = faster scroll.
    $animationDuration = $itemCount * 4;
@endphp

{{-- 1. CSS FOR SCROLLING --}}
@pushOnce('styles')
<style>
    @keyframes scroll-up {
        0% {
            transform: translateY(0);
        }

        100% {
            /* -50% isliye kyonki humne list ko duplicate kiya hai */
            transform: translateY(-50%);
        }
    }

    .ticker-content {
        animation: scroll-up linear infinite;
        animation-duration:
            {{ $animationDuration }}
            s;
    }

    .ticker-wrapper:hover .ticker-content {
        /* Hover karne par animation pause ho jayega */
        animation-play-state: paused;
    }
</style>
@endpushOnce

{{-- 2. HEADING --}}
<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">{{ $title }}</h2>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No updates found.</p>
@else
    {{-- 3. THE SCROLLING CONTAINER --}}
    <div class="max-w-3xl mx-auto">
        {{--
        Yeh wrapper hai jo scroll ko "kaat" dega (overflow: hidden)
        - max-h-[450px] se iski height limit kar di hai.
        - 'mask-image' se top aur bottom me fade effect diya hai (professional touch)
        --}}
        <div class="ticker-wrapper"
            style="max-height: 450px; overflow: hidden; -webkit-mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent); mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent);">

            {{-- Yeh content hai jo animate hoga --}}
            <div class="ticker-content">

                {{-- LIST 1 (Original) --}}
                @foreach ($items as $notification)
                    {{-- Yeh 'item' file ko call kar raha hai --}}
                    @include('components.home-page-blocks.partials.latest-update-item', ['notification' => $notification])
                @endforeach

                {{-- LIST 2 (Duplicate for Seamless Loop) --}}
                @foreach ($items as $notification)
                    @include('components.home-page-blocks.partials.latest-update-item', ['notification' => $notification])
                @endforeach

            </div>
        </div>
    </div>
@endif
