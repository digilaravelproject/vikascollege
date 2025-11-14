@props(['block'])

@php
    $type = $block['type'] ?? '';
    $content = $block['content'] ?? '';

    $style = collect([
        'font-size' => $block['fontSize'] ?? null ? $block['fontSize'] . 'px' : null,
        'color' => $block['color'] ?? null,
        'text-align' => $block['textAlign'] ?? null,
        'line-height' => $block['lineHeight'] ?? null,
        'font-weight' => $block['bold'] ?? false ? 'bold' : null,
        'font-style' => $block['italic'] ?? false ? 'italic' : null,
        'text-decoration' => $block['underline'] ?? false ? 'underline' : null,
        'margin-bottom' => '1rem',
    ])
        ->filter()
        ->map(fn($v, $k) => "{$k}: {$v}")
        ->implode('; ');
@endphp

<style>
    .prose .ql-align-center,
    .prose [class~="ql-align-center"] {
        text-align: center;
    }

    .prose .ql-align-right,
    .prose [class~="ql-align-right"] {
        text-align: right;
    }

    .prose .ql-align-justify,
    .prose [class~="ql-align-justify"] {
        text-align: justify;
    }
</style>
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>

@switch($type)

    {{-- ======================================================================
    SECTION
====================================================================== --}}
    @case('section')
        <section x-data="{ open: {{ $block['expanded'] ?? false ? 'true' : 'false' }} }" class="p-4 mb-6 border border-gray-200 shadow-sm rounded-2xl bg-gray-50">
            <div @click="open = !open" class="flex items-center justify-between cursor-pointer select-none">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $block['title'] ?? 'Untitled Section' }}
                </h2>

                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                </svg>

                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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

    {{-- ======================================================================
    HEADING & TEXT
====================================================================== --}}
    @case('heading')
    @case('text')
        <div class="mb-4 prose-sm prose prose-gray max-w-none" style="{{ $style }}">
            {!! $content !!}
        </div>
    @break

    {{-- ======================================================================
    IMAGE
====================================================================== --}}
    @case('image')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <img src="{{ $block['src'] }}" alt="{{ $block['alt'] ?? 'Image' }}"
                    style="max-width: 100%; height: auto; {{ $style }}"
                    class="object-contain mx-auto rounded-lg shadow-md" loading="lazy" />

                @if (!empty($block['caption']))
                    <p class="mt-2 text-sm text-gray-500">{{ $block['caption'] }}</p>
                @endif
            </div>
        @endif
    @break

    {{-- ======================================================================
    VIDEO
====================================================================== --}}
    @case('video')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <video src="{{ $block['src'] }}" controls class="max-w-full mx-auto rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"></video>
            </div>
        @endif
    @break

    {{-- ======================================================================
    PDF OLD
====================================================================== --}}
    @case('pdf_old')
        @if (!empty($block['src']))
            <link rel="preload" href="{{ $block['src'] }}" as="fetch" crossorigin="anonymous">

            <style>
                @media print {
                    .pdf-iframe-wrapper {
                        display: none !important;
                    }
                }
            </style>

            <div class="my-6 pdf-iframe-wrapper">
                <iframe src="{{ $block['src'] }}#toolbar=0" width="100%" height="700"
                    class="border rounded-lg shadow-inner" loading="lazy" oncontextmenu="return false;">
                    PDF load nahi ho paayi.
                </iframe>
            </div>

            <script>
                if (!window.pdfJsGlobalKeysAdded) {
                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey && e.key === 'p') || (e.ctrlKey && e.key === 's')) {
                            e.preventDefault();
                        }
                    });
                    window.pdfJsGlobalKeysAdded = true;
                }
            </script>
        @endif
    @break

    {{-- ======================================================================
    PDF NEW
====================================================================== --}}
    @case('pdf')
        @if (!empty($block['src']))
            @php $uniqueId = 'pdf_viewer_' . uniqid(); @endphp

            <link rel="preload" href="{{ $block['src'] }}" as="fetch" crossorigin="anonymous">

            <style>
                @media print {
                    .pdf-viewer-wrapper-{{ $uniqueId }} {
                        display: none !important;
                    }
                }
            </style>

            <div class="my-6 pdf-viewer-wrapper-{{ $uniqueId }}">
                <div id="{{ $uniqueId }}_container"
                    class="w-full max-h-[700px] border rounded-lg shadow-inner overflow-auto relative"
                    data-pdf-url="{{ $block['src'] }}">
                    <div id="{{ $uniqueId }}_loading"
                        class="p-8 text-center text-gray-500 font-semibold bg-gray-50 dark:bg-gray-800 rounded-lg h-full">
                        <div class="animate-pulse space-y-4">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mx-auto"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mx-auto"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-5/6 mx-auto"></div>
                        </div>
                        <p class="mt-4 text-sm flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Loading PDF...
                        </p>
                    </div>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer></script>

            <script>
                if (!window.pdfJsGlobalKeysAdded) {
                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey && e.key === 'p') || (e.ctrlKey && e.key === 's')) {
                            e.preventDefault();
                        }
                    });
                    window.pdfJsGlobalKeysAdded = true;
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const viewer = document.getElementById('{{ $uniqueId }}_container');
                    const loadingMessage = document.getElementById('{{ $uniqueId }}_loading');

                    if (viewer) {
                        const url = viewer.dataset.pdfUrl;

                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    loadPdf(url, viewer, loadingMessage);
                                    observer.unobserve(viewer);
                                }
                            });
                        }, {
                            rootMargin: '100px'
                        });

                        observer.observe(viewer);
                    }
                });

                function loadPdf(url, viewer, loadingMessage) {
                    const devicePixelRatio = window.devicePixelRatio || 1;
                    const baseScale = 1.2;
                    let pdfDoc = null;
                    const renderedPages = new Set();

                    viewer.style.userSelect = "none";
                    viewer.addEventListener('contextmenu', e => e.preventDefault());

                    pdfjsLib.GlobalWorkerOptions.workerSrc =
                        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                    pdfjsLib.getDocument(url).promise.then(pdf => {
                        pdfDoc = pdf;
                        const initialPages = Math.min(3, pdfDoc.numPages);

                        for (let i = 1; i <= initialPages; i++) {
                            renderPage(i);
                        }

                        if (loadingMessage) loadingMessage.remove();

                        if (pdfDoc.numPages > initialPages) renderVisiblePages();
                    });

                    function renderPage(pageNumber) {
                        if (renderedPages.has(pageNumber)) return;

                        pdfDoc.getPage(pageNumber).then(page => {
                            const viewport = page.getViewport({
                                scale: baseScale * devicePixelRatio
                            });
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');

                            const wrap = document.createElement('div');
                            wrap.className = 'pdf-page-container';
                            wrap.style.marginBottom = "1rem";
                            wrap.style.width = '100%';

                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            canvas.style.width = "100%";
                            canvas.style.height = "auto";

                            wrap.appendChild(canvas);
                            viewer.appendChild(wrap);

                            page.render({
                                canvasContext: ctx,
                                viewport
                            }).promise.then(() => {
                                renderedPages.add(pageNumber);
                            });
                        });
                    }

                    function renderVisiblePages() {
                        if (!pdfDoc) return;

                        const scrollTop = viewer.scrollTop;
                        const scrollBottom = scrollTop + viewer.clientHeight;
                        const estHeight = viewer.scrollHeight / pdfDoc.numPages;

                        for (let i = 1; i <= pdfDoc.numPages; i++) {
                            const top = (i - 1) * estHeight;
                            const bottom = top + estHeight;

                            if (bottom >= scrollTop - 300 && top <= scrollBottom + 300) {
                                renderPage(i);
                            }
                        }
                    }

                    let scrollTimeout;

                    viewer.addEventListener('scroll', () => {
                        clearTimeout(scrollTimeout);
                        scrollTimeout = setTimeout(() => {
                            renderVisiblePages();
                        }, 150);
                    });
                }
            </script>
        @endif
    @break

    {{-- ======================================================================
    EMBED
====================================================================== --}}
    @case('embed')
        @php
            $embedUrl = null;
            $src = $block['src'] ?? '';

            if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $src, $m)) {
                $embedUrl = 'https://www.youtube.com/embed/' . $m[2] . '?rel=0';
            }
        @endphp

        @if ($embedUrl)
            <div class="my-6 aspect-w-16 aspect-h-9">
                <iframe src="{{ $embedUrl }}" class="w-full h-[500px] rounded-lg shadow-md" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy"></iframe>
            </div>
        @elseif (!empty($src))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                Unsupported embed URL: {{ $src }}
            </div>
        @endif
    @break

    {{-- ======================================================================
    DIVIDER
====================================================================== --}}
    @case('divider')
        <hr class="my-8 border-gray-200" />
    @break

    {{-- ======================================================================
    BUTTON
====================================================================== --}}
    @case('button')
        @php
            $buttonHref = $block['href'] ?? '#';
            $buttonTarget = ($block['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
            $buttonText = $block['text'] ?? ($block['content'] ?? 'Click Here');

            $align = match ($block['align'] ?? 'left') {
                'center' => 'text-center',
                'right' => 'text-right',
                default => 'text-left',
            };
        @endphp

        <div class="my-6 {{ $align }}">
            <a href="{{ $buttonHref }}" target="{{ $buttonTarget }}"
                rel="{{ $buttonTarget === '_blank' ? 'noopener noreferrer' : '' }}"
                class="inline-block px-5 py-2 font-medium text-white transition-colors duration-200 bg-blue-600
               rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ $buttonText }}
            </a>
        </div>
    @break

    {{-- ======================================================================
    CODE BLOCK
====================================================================== --}}
    @case('code')
        <div class="my-6">
            <pre class="p-4 overflow-x-auto text-sm text-white bg-gray-800 rounded-lg shadow-md">
        <code>{{ $content }}</code>
    </pre>
        </div>
    @break

    {{-- ======================================================================
    TABLE (FIXED)
====================================================================== --}}
    @case('table')
        @php
            $tableData = $block['data'] ?? [];
        @endphp

        @if (!empty($tableData) && is_array($tableData))
            @php
                $headers = $tableData[0] ?? [];
                $rows = array_slice($tableData, 1);
            @endphp

            <div class="my-8 overflow-x-auto rounded-xl shadow-md border border-gray-300 bg-white">
                <table class="min-w-full text-sm text-gray-800 border border-gray-300">

                    {{-- Header --}}
                    <thead class="bg-[#013954] text-white">
                        <tr>
                            @foreach ($headers as $header)
                                <th class="px-6 py-3 font-semibold uppercase tracking-wide text-left border border-[#013954]">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition">
                                @foreach ($row as $cell)
                                    <td class="px-6 py-4 align-top leading-relaxed border border-gray-300">
                                        <div class="prose prose-sm max-w-none">
                                            {!! nl2br(e($cell)) !!}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    @break

    {{-- ======================================================================
    DEFAULT
====================================================================== --}}

    @default
        <div class="p-4 text-sm text-gray-500 bg-gray-100 rounded-lg">
            Unknown block type: <strong>{{ $type }}</strong>
        </div>

@endswitch
