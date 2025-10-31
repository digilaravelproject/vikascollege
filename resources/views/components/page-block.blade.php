@props(['block'])

@php
    $type = $block['type'] ?? '';
    $content = $block['content'] ?? '';

    // Common style attributes for Quill/Text blocks
    $style = collect([
        'font-size'   => ($block['fontSize'] ?? null) ? $block['fontSize'] . 'px' : null,
        'color'       => $block['color'] ?? null,
        'text-align'  => $block['textAlign'] ?? null,
        'line-height' => $block['lineHeight'] ?? null,
        'font-weight' => ($block['bold'] ?? false) ? 'bold' : null,
        'font-style'  => ($block['italic'] ?? false) ? 'italic' : null,
        'text-decoration' => ($block['underline'] ?? false) ? 'underline' : null,
        'margin-bottom' => '1rem',
    ])->filter()->map(fn($v, $k) => "{$k}: {$v}")->implode('; ');
@endphp

@switch($type)
    {{-- ==================== SECTION (Collapsible UI Maintained) ==================== --}}
    @case('section')
        <section
            x-data="{ open: {{ $block['expanded'] ?? false ? 'true' : 'false' }} }"
            class="p-4 mb-6 border border-gray-200 shadow-sm rounded-2xl bg-gray-50"
        >
            <div
                @click="open = !open"
                class="flex items-center justify-between cursor-pointer select-none"
            >
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $block['title'] ?? 'Untitled Section' }}
                </h2>

                {{-- Toggle Icons (Maintained) --}}
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

    {{-- ==================== HEADING, TEXT, and TABLE (Quill Content Renderer) ==================== --}}
    @case('heading')
    @case('text')
    @case('table')
        {{--
            ENHANCEMENT: Combine similar text/table blocks and use 'prose'
            to ensure Quill's formatting (bold, lists, tables) is rendered correctly
            and responsively, matching the editor's visual settings.
        --}}
        <div
            class="mb-4 prose-sm prose prose-gray max-w-none"
            style="{{ $style }}"
        >
            {{-- Render Quill's raw HTML securely --}}
            {!! $content !!}
        </div>
        @break

    {{-- ==================== IMAGE ==================== --}}
    @case('image')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <img
                    src="{{ $block['src'] }}"
                    alt="{{ $block['alt'] ?? 'Image' }}"
                    style="max-width: 100%; height: auto;"
                    class="object-contain mx-auto rounded-lg shadow-md"
                    loading="lazy"
                />
                @if (!empty($block['caption']))
                    <p class="mt-2 text-sm text-gray-500">{{ $block['caption'] }}</p>
                @endif
            </div>
        @endif
        @break

    {{-- ==================== VIDEO ==================== --}}
    @case('video')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <video
                    src="{{ $block['src'] }}"
                    controls
                    class="max-w-full mx-auto rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"
                ></video>
            </div>
        @endif
        @break

    {{-- ==================== PDF ==================== --}}
    @case('pdf')
        @if (!empty($block['src']))
            <div class="my-6">
                <iframe
                    src="{{ $block['src'] }}"
                    class="w-full h-[700px] border rounded-lg shadow-inner"
                    frameborder="0"
                    loading="lazy"
                ></iframe>
            </div>
        @endif
        @break

    {{-- ==================== DEFAULT (Unknown Type) ==================== --}}
    @default
        <div class="p-4 text-sm text-gray-500 bg-gray-100 rounded-lg">
            Unknown block type: <strong>{{ $type }}</strong>
        </div>
@endswitch
