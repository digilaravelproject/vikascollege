@props(['block'])

@php
    $type = $block['type'] ?? '';
    $content = $block['content'] ?? '';

    // Common style attributes for Quill/Text blocks (From your original code)
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
    {{-- ==================== SECTION (Original) ==================== --}}
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

                {{-- Toggle Icons (Original) --}}
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

    {{-- ==================== HEADING, TEXT (Original) ==================== --}}
    @case('heading')
    @case('text')
        <div
            class="mb-4 prose-sm prose prose-gray max-w-none"
            style="{{ $style }}"
        >
            {!! $content !!}
        </div>
        @break

    {{-- ==================== IMAGE (Original) ==================== --}}
    @case('image')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <img
                    src="{{ $block['src'] }}"
                    alt="{{ $block['alt'] ?? 'Image' }}"
                    style="max-width: 100%; height: auto; {{ $style }}"
                    class="object-contain mx-auto rounded-lg shadow-md"
                    loading="lazy"
                />
                @if (!empty($block['caption']))
                    <p class="mt-2 text-sm text-gray-500">{{ $block['caption'] }}</p>
                @endif
            </div>
        @endif
        @break

    {{-- ==================== VIDEO (Original) ==================== --}}
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

    {{-- ==================== INTRO (Homepage) ==================== --}}
    @case('intro')
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
        @break

    {{-- ==================== LINKS GRID (Homepage) ==================== --}}
    @case('linkGrid')
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
        @break

    {{-- ==================== PDF (Original & Working) ==================== --}}
    @case('pdf')
        @if (!empty($block['src']))
            <div class="my-6">
                <iframe
                    src="{{ $block['src'] }}"
                    class="w-full h-[700px] border rounded-lg shadow-inner"
                    frameborder="0"
                    loading="lazy"
                    no-referrer="true" download="false"
                ></iframe>
            </div>
        @endif
        @break


    {{-- ==================== NEW: EMBED (YouTube, Vimeo, etc.) ==================== --}}
    @case('embed')
        @php
            $embedUrl = null;
            $src = $block['src'] ?? '';
            if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $src, $matches)) {
                $embedUrl = "https://www.youtube.com/embed/" . $matches[2] . '?rel=0';
            }
        @endphp
        @if ($embedUrl)
            <div class="my-6 aspect-w-16 aspect-h-9">
                <iframe
                    src="{{ $embedUrl }}"
                    class="w-full h-[500px] rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        @elseif (!empty($src))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                Unsupported embed URL: {{ $src }}
            </div>
        @endif
        @break

    {{-- ==================== NEW: DIVIDER ==================== --}}
    @case('divider')
        <hr class="my-8 border-gray-200" />
        @break

    {{-- ==================== NEW: BUTTON ==================== --}}
    @case('button')
        @php
            $buttonHref = $block['href'] ?? '#';
            $buttonTarget = ($block['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
            $buttonText = $block['text'] ?? ($block['content'] ?? 'Click Here');
            $buttonAlignment = $block['align'] ?? 'left';
            $buttonAlignClass = match($buttonAlignment) {
                'center' => 'text-center',
                'right' => 'text-right',
                default => 'text-left',
            };
        @endphp
        <div class="my-6 {{ $buttonAlignClass }}">
            <a
                href="{{ $buttonHref }}"
                target="{{ $buttonTarget }}"
                rel="{{ $buttonTarget === '_blank' ? 'noopener noreferrer' : '' }}"
                class="inline-block px-5 py-2 font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ $buttonText }}
            </a>
        </div>
        @break

    {{-- ==================== NEW: CODE BLOCK ==================== --}}
    @case('code')
        <div class="my-6">
            {{-- $content is already defined in the main @php block --}}
            <pre class="p-4 overflow-x-auto text-sm text-white bg-gray-800 rounded-lg shadow-md"><code>{{ $content }}</code></pre>
        </div>
        @break

    {{-- ==================== DEFAULT (Original) ==================== --}}
    @default
        <div class="p-4 text-sm text-gray-500 bg-gray-100 rounded-lg">
            Unknown block type: <strong>{{ $type }}</strong>
        </div>
@endswitch

