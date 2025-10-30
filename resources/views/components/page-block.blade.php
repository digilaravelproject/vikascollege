@props(['block'])

@php
    $type = $block['type'] ?? '';
@endphp

@switch($type)
    {{-- ==================== SECTION (Expandable) ==================== --}}
    @case('section')
        <section
            x-data="{ open: {{ $block['expanded'] ?? false ? 'true' : 'false' }} }"
            class="p-4 mb-6 border border-gray-200 shadow-sm rounded-2xl bg-gray-50"
        >
            {{-- Header --}}
            <div
                @click="open = !open"
                class="flex items-center justify-between cursor-pointer select-none"
            >
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $block['title'] ?? 'Untitled Section' }}
                </h2>

                {{-- Toggle Icons --}}
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 5v14m7-7H5" />
                </svg>

                <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Content --}}
            <div x-show="open" x-collapse class="mt-4 space-y-6">
                @if (!empty($block['blocks']) && is_array($block['blocks']))
                    @foreach ($block['blocks'] as $sub)
                        <x-page-block :block="$sub" />
                    @endforeach
                @else
                    <p class="italic text-gray-400">No content in this section.</p>
                @endif
            </div>
        </section>
    @break

    {{-- ==================== HEADING ==================== --}}
    @case('heading')
        <div
            style="
                font-size: {{ $block['fontSize'] ?? 24 }}px;
                color: {{ $block['color'] ?? '#000' }};
                text-align: {{ $block['textAlign'] ?? 'left' }};
                font-weight: {{ ($block['bold'] ?? false) ? 'bold' : 'normal' }};
                font-style: {{ ($block['italic'] ?? false) ? 'italic' : 'normal' }};
                text-decoration: {{ ($block['underline'] ?? false) ? 'underline' : 'none' }};
                line-height: {{ $block['lineHeight'] ?? 1.5 }};
                margin-bottom: 1rem;
            "
        >
            {!! $block['content'] ?? '' !!}
        </div>
    @break

    {{-- ==================== TEXT ==================== --}}
    @case('text')
        <div
            class="prose prose-gray max-w-none"
            style="
                font-size: {{ $block['fontSize'] ?? 16 }}px;
                color: {{ $block['color'] ?? '#000' }};
                text-align: {{ $block['textAlign'] ?? 'left' }};
                line-height: {{ $block['lineHeight'] ?? 1.6 }};
                margin-bottom: 1rem;
            "
        >
            {!! $block['content'] ?? '' !!}
        </div>
    @break

    {{-- ==================== IMAGE ==================== --}}
    @case('image')
        <div class="my-6 text-center">
            <img
                src="{{ $block['src'] ?? '' }}"
                alt="{{ $block['alt'] ?? 'Image' }}"
                style="max-width: 100%; height: auto;"
                class="object-contain mx-auto rounded-lg shadow-md"
                loading="lazy"
            />
            @if (!empty($block['caption']))
                <p class="mt-2 text-sm text-gray-500">{{ $block['caption'] }}</p>
            @endif
        </div>
    @break

    {{-- ==================== VIDEO ==================== --}}
    @case('video')
        <div class="my-6 text-center">
            <video
                src="{{ $block['src'] ?? '' }}"
                controls
                class="max-w-full mx-auto rounded-lg shadow-md"
                style="height: {{ $block['height'] ?? '315px' }};"
            ></video>
        </div>
    @break

    {{-- ==================== PDF ==================== --}}
    @case('pdf')
        <div class="my-6">
            <iframe
                src="{{ $block['src'] ?? '' }}"
                class="w-full h-[700px] border rounded-lg shadow-inner"
                frameborder="0"
                loading="lazy"
            ></iframe>
        </div>
    @break

    {{-- ==================== DEFAULT (Unknown Type) ==================== --}}
    @default
        <div class="p-4 text-sm text-gray-500 bg-gray-100 rounded-lg">
            Unknown block type: <strong>{{ $type }}</strong>
        </div>
@endswitch
