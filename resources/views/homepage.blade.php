@extends('layouts.app')

@section('content')
    @php
        $hp = setting('homepage_layout');
        $hpBlocks = [];
        if ($hp) {
            $parsed = json_decode($hp, true);
            $hpBlocks = $parsed['blocks'] ?? [];
        }
        $notificationsJson = setting('homepage_notifications');
        $notifications = $notificationsJson ? json_decode($notificationsJson, true) : [];
    @endphp

    @if (!empty($notifications))
    <section class="py-2 bg-yellow-50 border-b border-yellow-200">
        <div class="container px-4 mx-auto">
            <div class="relative h-8 overflow-hidden" x-data="{ i: 0 }" x-init="let el=$refs.list; const items=el.children; setInterval(()=>{ i=(i+1)%items.length; el.style.transform=`translateY(-${i*2.25}rem)`; el.style.transition='transform 500ms'; }, 2500)">
                <ul class="absolute w-full space-y-2" x-ref="list" style="will-change: transform;">
                    @foreach ($notifications as $n)
                        @php $t = $n['title'] ?? ''; $href = $n['href'] ?? ''; $isNew = !empty($n['isNew']); @endphp
                        <li class="flex items-center justify-between h-9">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-yellow-700">🔔</span>
                                @if ($href)
                                    <a href="{{ $href }}" class="font-medium text-yellow-900 hover:underline">{{ $t }}</a>
                                @else
                                    <span class="font-medium text-yellow-900">{{ $t }}</span>
                                @endif
                                @if ($isNew)
                                    <span class="px-2 py-0.5 text-xs font-semibold text-white bg-red-600 rounded-full">NEW</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    @endif

    {{-- Slider (from Website Settings) --}}
    @include('partials.hero-banner')

    @if (!empty($hpBlocks))
        @foreach ($hpBlocks as $block)
            @php $type = $block['type'] ?? ''; @endphp
            @if ($type === 'intro')
                @php
                    $layout = $block['layout'] ?? 'left';
                    $img = $block['image'] ?? '';
                    $heading = $block['heading'] ?? '';
                    $text = $block['text'] ?? '';
                    $btnText = $block['buttonText'] ?? '';
                    $btnHref = $block['buttonHref'] ?? '';
                @endphp
                <section class="py-10">
                    <div class="container grid items-center grid-cols-1 gap-8 px-4 mx-auto md:grid-cols-2">
                        @if ($layout === 'right' || $layout === 'top')
                            <div class="{{ $layout === 'top' ? 'order-1 md:col-span-2' : 'order-2' }}">
                                @if ($img)
                                    <img src="{{ $img }}" class="w-full rounded-xl shadow-md" alt="">
                                @endif
                            </div>
                        @endif

                        <div class="order-1">
                            @if ($heading)
                                <h2 class="text-2xl font-bold text-gray-900 md:text-3xl">{{ $heading }}</h2>
                            @endif
                            @if ($text)
                                <p class="mt-3 text-gray-600">{{ $text }}</p>
                            @endif
                            @if ($btnText && $btnHref)
                                <a href="{{ $btnHref }}" class="inline-block px-5 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700">{{ $btnText }}</a>
                            @endif
                        </div>

                        @if ($layout === 'left')
                            <div class="order-2">
                                @if ($img)
                                    <img src="{{ $img }}" class="w-full rounded-xl shadow-md" alt="">
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
            @elseif ($type === 'sectionLinks')
                @php
                    $title = $block['title'] ?? '';
                    $columns = max(2, min(4, (int)($block['columns'] ?? 3)));
                    $items = $block['items'] ?? [];
                    $gridClass = match($columns) { 2 => 'sm:grid-cols-2', 4 => 'sm:grid-cols-2 lg:grid-cols-4', default => 'sm:grid-cols-2 lg:grid-cols-3' };
                @endphp
                <section class="py-8">
                    <div class="container px-4 mx-auto">
                        @if ($title)
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">{{ $title }}</h3>
                        @endif
                        <div class="grid grid-cols-1 gap-4 {{ $gridClass }}">
                            @foreach ($items as $it)
                                @php $t = $it['title'] ?? ''; $href = $it['href'] ?? '#'; $isNew = !empty($it['isNew']); @endphp
                                <a href="{{ $href }}" class="flex items-center justify-between p-4 transition bg-white border rounded-lg shadow-sm hover:shadow">
                                    <span class="text-sm font-medium text-gray-800">{{ $t }}</span>
                                    <span class="flex items-center gap-2">
                                        @if ($isNew)
                                            <span class="px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">NEW</span>
                                        @endif
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif ($type === 'divider')
                <hr class="my-8 border-gray-200" />
            @endif
        @endforeach
    @else
        {{-- Fallback --}}
        <div class="container">
            <h2 class="mb-6 text-2xl font-bold text-center">Welcome to {{ setting('college_name') }}</h2>
            <p class="text-center text-gray-600">Explore our academic programs, admissions, and campus life.</p>
        </div>
    @endif
@endsection