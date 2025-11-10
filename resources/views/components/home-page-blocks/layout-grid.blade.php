@php
    $columns = $block['columns'] ?? [];
@endphp

<section class="w-full py-16 md:py-24 {{ $loop && $loop->even ? 'bg-gray-50' : 'bg-white' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-12 gap-6">
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
