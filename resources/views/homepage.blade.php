@extends('layouts.app')
@section('title', 'Homepage')

@section('content')
    @php
        use App\Models\Notification;

        // Load homepage layout from settings
        $hp = setting('homepage_layout');
        $hpBlocks = [];
        if ($hp) {
            $parsed = json_decode($hp, true);
            $hpBlocks = $parsed['blocks'] ?? [];
        }
    @endphp

    @include('partials.hero-banner')

    <div class="bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-12">

            @if (!empty($hpBlocks))
                @foreach ($hpBlocks as $block)
                    @php $type = $block['type'] ?? ''; @endphp

                    {{-- ========== INTRO BLOCK ========== --}}
                    @if ($type === 'intro')
                        @php
                            $layout = $block['layout'] ?? 'left';
                            $img = $block['image'] ?? '';
                            $heading = $block['heading'] ?? '';
                            $text = $block['text'] ?? '';
                            $btnText = $block['buttonText'] ?? '';
                            $btnHref = $block['buttonHref'] ?? '';
                            $hasImage = !empty($img);
                        @endphp

                        <section>
                            <div
                                class="container mx-auto grid grid-cols-1 {{ $hasImage && $layout !== 'top' ? 'lg:grid-cols-2' : '' }} items-center gap-8 md:gap-12">
                                @if ($hasImage && ($layout === 'right' || $layout === 'top'))
                                    <div class="{{ $layout === 'top' ? 'order-1' : 'order-1 lg:order-2' }}">
                                        <img src="{{ $img }}" class="w-full h-auto rounded-lg shadow-lg object-cover"
                                            alt="">
                                    </div>
                                @endif

                                <div class="order-2 {{ $hasImage && $layout === 'right' ? 'lg:order-1' : '' }}">
                                    @if ($heading)
                                        <h2 class="text-3xl font-bold text-gray-900 md:text-4xl">{{ $heading }}</h2>
                                    @endif
                                    @if ($text)
                                        <p class="mt-4 text-gray-600 text-base md:text-lg leading-relaxed">
                                            {!! nl2br(e($text)) !!}</p>
                                    @endif
                                    @if ($btnText && $btnHref)
                                        <a href="{{ $btnHref }}"
                                            class="inline-block px-6 py-3 mt-6 text-base font-medium text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                                            {{ $btnText }}
                                        </a>
                                    @endif
                                </div>

                                @if ($hasImage && $layout === 'left')
                                    <div class="order-1">
                                        <img src="{{ $img }}"
                                            class="w-full h-auto rounded-lg shadow-lg object-cover" alt="">
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- ========== SECTION LINKS BLOCK ========== --}}
                    @elseif ($type === 'sectionLinks')
                        @php
                            $title = $block['title'] ?? '';
                            $columns = max(1, min(4, (int) ($block['columns'] ?? 3)));
                            $items = $block['items'] ?? [];

                            $gridClass = match ($columns) {
                                1 => 'grid-cols-1',
                                2 => 'grid-cols-1 sm:grid-cols-2',
                                3 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
                                4 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4',
                                default => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
                            };
                        @endphp

                        <section>
                            <div class="container mx-auto">
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                                    @if ($title)
                                        <div class="p-5 md:p-6 border-b border-gray-200 bg-gray-50">
                                            <h3 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $title }}
                                            </h3>
                                        </div>
                                    @endif
                                    @if (!empty($items))
                                        <div class="p-5 md:p-6">
                                            <div class="grid {{ $gridClass }} gap-4 md:gap-5">
                                                @foreach ($items as $it)
                                                    @php
                                                        $t = $it['title'] ?? '';
                                                        $href = $it['href'] ?? '#';
                                                        $isNew = !empty($it['isNew']);
                                                    @endphp
                                                    <a href="{{ $href }}"
                                                        class="flex items-center justify-between p-4 transition duration-300 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-blue-500 hover:bg-blue-50 group">
                                                        <span
                                                            class="text-sm font-medium text-gray-800 group-hover:text-blue-700">{{ $t }}</span>
                                                        <span class="flex items-center flex-shrink-0 gap-2 pl-2">
                                                            @if ($isNew)
                                                                <span
                                                                    class="px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">NEW</span>
                                                            @endif
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-4 h-4 text-gray-400 group-hover:text-blue-600"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>

                        {{-- ========== DIVIDER BLOCK ========== --}}
                    @elseif ($type === 'divider')
                        <hr class="border-gray-200" />

                        {{-- ========== LATEST UPDATES BLOCK (Bottom-to-Top Infinite Scroll) ========== --}}
                    @elseif ($type === 'latestUpdates')
                        @php
                            $title = $block['title'] ?? 'Latest Updates';
                            $updates = Notification::where('status', 1)
                                ->where('feature_on_top', 1)
                                ->orderByDesc('display_date')
                                ->limit(20)
                                ->get();

                            $duration = max(20, $updates->count() * 3); // control scroll speed
                        @endphp

                        @push('styles')
                            <style>
                                @keyframes scroll-up {
                                    0% {
                                        transform: translateY(0);
                                    }

                                    100% {
                                        transform: translateY(-50%);
                                    }
                                }

                                .scroll-wrapper {
                                    position: absolute;
                                    width: 100%;
                                    top: 0;
                                    animation: scroll-up var(--scroll-duration, 20s) linear infinite;
                                    will-change: transform;
                                }

                                .scroller-container {
                                    position: relative;
                                    height: 24rem;
                                    /* h-96 */
                                    overflow: hidden;
                                }

                                .scroller-container:hover .scroll-wrapper {
                                    animation-play-state: paused;
                                }
                            </style>
                        @endpush

                        <section>
                            <div class="container mx-auto">
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                                    @if ($title)
                                        <div class="p-5 md:p-6 border-b border-gray-200 bg-gray-50">
                                            <h3 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $title }}
                                            </h3>
                                        </div>
                                    @endif

                                    {{-- Bottom-to-Top Infinite Scroll --}}
                                    <div class="scroller-container p-5 md:p-6 relative" x-data x-init="$nextTick(() => {
                                        let list1 = $refs.list1;
                                        let list2 = $refs.list2;
                                        if (list1.children.length > 0) {
                                            // Duplicate list for seamless looping
                                            list2.innerHTML = list1.innerHTML;
                                            $refs.wrapper.classList.add('scroll-wrapper');
                                            $refs.wrapper.style.setProperty('--scroll-duration', '{{ $duration }}s');
                                            // Start animation from bottom
                                            $refs.wrapper.style.transform = 'translateY(0)';
                                        }
                                    });">
                                        <div class="top-0 left-0 right-0" x-ref="wrapper">
                                            {{-- Original List --}}
                                            <ul class="space-y-3 px-1" x-ref="list1">
                                                @forelse ($updates as $update)
                                                    @php
                                                        $isNew =
                                                            $update->is_new ||
                                                            ($update->display_date &&
                                                                $update->display_date->gt(now()->subDays(7)));
                                                    @endphp
                                                    <li class="flex">
                                                        <a href="{{ $update->href ?? '#' }}"
                                                            {{ $update->href ? 'target="_blank" rel="noopener"' : '' }}
                                                            class="flex items-center gap-3 p-2 rounded-md w-full transition duration-300 hover:bg-gray-100 group">
                                                            <span
                                                                class="text-lg flex-shrink-0">{{ $update->icon ?? '✨' }}</span>
                                                            <div class="flex-grow min-w-0">
                                                                <span
                                                                    class="text-sm font-medium text-gray-800 group-hover:text-blue-700 block truncate">{{ $update->title }}</span>
                                                                @if ($update->display_date)
                                                                    <p class="text-xs text-gray-500">
                                                                        {{ $update->display_date->format('F j, Y') }}</p>
                                                                @endif
                                                            </div>
                                                            @if ($isNew)
                                                                <span
                                                                    class="flex-shrink-0 ml-auto px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">NEW</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @empty
                                                    <li class="h-24 flex items-center justify-center">
                                                        <p class="text-gray-500">No updates are available at this time.</p>
                                                    </li>
                                                @endforelse
                                            </ul>

                                            {{-- Duplicate list for seamless loop --}}
                                            <ul class="space-y-3 px-1" x-ref="list2" aria-hidden="true"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif
                @endforeach
            @else
                <div class="container mx-auto text-center">
                    <h2 class="mb-6 text-2xl font-bold">Welcome to {{ setting('college_name') }}</h2>
                    <p class="text-gray-600">Explore our academic programs, admissions, and campus life.</p>
                </div>
            @endif

        </div>
    </div>
@endsection
