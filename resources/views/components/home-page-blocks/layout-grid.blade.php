@php
    $columns = $block['columns'] ?? [];
    $title = $block['title'] ?? '';
@endphp

<section class="w-full py-8 md:py-12 {{ $loop && $loop->even ? 'bg-gray-50' : 'bg-white' }}">
    <div class="max-w-[90rem] mx-auto px-0 sm:px-2 lg:px-4">
        @if (!empty($title))
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl mb-2 text-center">
                {{ $title }}
            </h2>
            <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 m-auto"></div>
        @endif
       <div class="grid grid-cols-12 gap-y-6 md:gap-6">
            @foreach ($columns as $col)
                @php
                    $span = $col['span'] ?? 12; // Default 12
                    $childBlocks = $col['blocks'] ?? [];
                @endphp

                {{-- Tailwind grid system ke hisab se column span --}}
                <div class="col-span-12 lg:col-span-{{ $span }}">
                    <div class="space-y-6"> {{-- Nested blocks ke beech space --}}
                        @foreach ($childBlocks as $childBlock)
                            {{-- Yahaan $loop pass nahi kar rahe hain --}}
                            <x-home-page-block :block="$childBlock" />
                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
