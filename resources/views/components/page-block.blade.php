@props(['block'])

@php
    $type = $block['type'] ?? '';
    $content = $block['content'] ?? '';

    // Common style attributes for Quill/Text blocks (From your original code)
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
    {{-- ==================== SECTION (Original) ==================== --}}
    @case('section')
        <section x-data="{ open: {{ $block['expanded'] ?? false ? 'true' : 'false' }} }" class="p-4 mb-6 border border-gray-200 shadow-sm rounded-2xl bg-gray-50">
            <div @click="open = !open" class="flex items-center justify-between cursor-pointer select-none">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $block['title'] ?? 'Untitled Section' }}
                </h2>

                {{-- Toggle Icons (Original) --}}
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

    {{-- ==================== HEADING, TEXT (Original) ==================== --}}
    @case('heading')
    @case('text')
        <div class="mb-4 prose-sm prose prose-gray max-w-none" style="{{ $style }}">
            {!! $content !!}
        </div>
    @break

    {{-- ==================== IMAGE (Original) ==================== --}}
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

    {{-- ==================== VIDEO (Original) ==================== --}}
    @case('video')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <video src="{{ $block['src'] }}" controls class="max-w-full mx-auto rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"></video>
            </div>
        @endif
    @break

    {{-- ==================== PDF (Original & Working) ==================== --}}
    @case('pdf_old')
    @if (!empty($block['src']))

        {{-- 1. PDF ko preload karein (yeh ab bhi zaroori hai) --}}
        <link rel="preload" href="{{ $block['src'] }}" as="fetch" crossorigin="anonymous">

        {{-- 2. SECURITY (Aadha-Adhura): Print ko CSS se block karein --}}
        <style>
            @media print {
                .pdf-iframe-wrapper {
                    display: none !important;
                }
            }
        </style>

        <div class="my-6 pdf-iframe-wrapper">
            {{--
                3. Naya Viewer: Simple IFRAME
                Humne URL mein #toolbar=0 add kiya hai taaki download/print buttons
                jo default dikhte hain, woh chhup jaayein.
            --}}
            <iframe
                src="{{ $block['src'] }}#toolbar=0"
                width="100%"
                height="700"
                class="border rounded-lg shadow-inner"

                {{-- 4. SPEED: Native Lazy Loading --}}
                loading="lazy"

                {{-- 5. SECURITY (Aadha-Adhura): Right-click block karein --}}
                oncontextmenu="return false;"
            >
                PDF load nahi ho paayi.
            </iframe>
        </div>

        {{-- 6. SECURITY: Ctrl+P aur Ctrl+S ab bhi block karein --}}
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
   @case('pdf')
    @if (!empty($block['src']))

        {{-- ⭐️ NYA FIX: Hum loop ki jagah ek unique ID yahaan generate karenge --}}
        @php $uniqueId = 'pdf_viewer_' . uniqid(); @endphp

        {{-- 1. SPEED: PDF ko background mein preload karein --}}
        <link rel="preload" href="{{ $block['src'] }}" as="fetch" crossorigin="anonymous">

        {{-- 2. SECURITY: Print ko CSS se block karein --}}
        <style>
            @media print {
                .pdf-viewer-wrapper-{{ $uniqueId }} {
                    display: none !important;
                }
            }
        </style>

        {{-- Humne poore block ko ek wrapper div mein daal diya hai --}}
        <div class="my-6 pdf-viewer-wrapper-{{ $uniqueId }}">

            {{-- ⭐️ FIX: Yahaan $loop->index ki jagah $uniqueId ka istemal karein --}}
            <div id="{{ $uniqueId }}_container" class="w-full max-h-[700px] border rounded-lg shadow-inner overflow-auto relative" data-pdf-url="{{ $block['src'] }}">

                {{-- Loading Skeleton --}}
                {{-- ⭐️ FIX: Yahaan bhi $uniqueId ka istemal karein --}}
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

        {{-- PDF.js library ko 'defer' ke saath load karein --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer></script>

        {{-- 3. LAZY LOADING + SECURITY SCRIPT --}}
        <script>
            // ⭐️ SECURITY: Ctrl+P (Print) aur Ctrl+S (Save) ko block karein
            if (!window.pdfJsGlobalKeysAdded) {
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey && e.key === 'p') || (e.ctrlKey && e.key === 's')) {
                        e.preventDefault();
                    }
                });
                window.pdfJsGlobalKeysAdded = true;
            }

            document.addEventListener('DOMContentLoaded', () => {
                {{-- ⭐️ FIX: Javascript mein bhi $uniqueId ka istemal karein --}}
                const viewer = document.getElementById('{{ $uniqueId }}_container');
                const loadingMessage = document.getElementById('{{ $uniqueId }}_loading');

                // Zaroori check: Agar element hai tabhi aage badhein
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

            // (Poora loadPdf function waisa hi rahega jaisa pehle tha)
            function loadPdf(url, viewer, loadingMessage) {
                const devicePixelRatio = window.devicePixelRatio || 1;
                const baseScale = 1.2;
                let pdfDoc = null;
                const renderedPages = new Set();

                // ⭐️ SECURITY: Right-click aur Text Selection block karein
                viewer.style.userSelect = "none";
                viewer.addEventListener('contextmenu', e => e.preventDefault());

                // Configure PDF.js worker
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                // Load PDF file
                pdfjsLib.getDocument(url).promise.then(pdf => {
                    pdfDoc = pdf;
                    const initialPages = Math.min(3, pdfDoc.numPages);
                    for (let i = 1; i <= initialPages; i++) renderPage(i);
                    if (loadingMessage) loadingMessage.remove();
                    if (pdfDoc.numPages > initialPages) renderVisiblePages();
                }).catch(err => {
                    console.error('PDF load error:', err);
                    if (loadingMessage) {
                        loadingMessage.innerText = "Error: Unable to load PDF.";
                    } else {
                        viewer.innerText = "Error: Unable to load PDF.";
                    }
                });

                // (Baaki ka rendering code waisa hi hai)
                function renderPage(pageNumber) {
                    if (renderedPages.has(pageNumber)) return;
                    pdfDoc.getPage(pageNumber).then(page => {
                        const viewport = page.getViewport({ scale: baseScale * devicePixelRatio });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        const pageContainer = document.createElement('div');
                        pageContainer.className = 'pdf-page-container';
                        pageContainer.style.width = '100%';
                        pageContainer.style.marginBottom = "1rem";
                        pageContainer.style.height = `${viewport.height / devicePixelRatio}px`;
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        canvas.style.width = "100%";
                        canvas.style.height = "auto";
                        canvas.style.display = "block";
                        pageContainer.appendChild(canvas);
                        viewer.appendChild(pageContainer);
                        const renderTask = page.render({ canvasContext: context, viewport: viewport });
                        renderTask.promise.then(() => {
                            renderedPages.add(pageNumber);
                        });
                    });
                }
                function renderVisiblePages() {
                    if (!pdfDoc) return;
                    const viewerTop = viewer.scrollTop;
                    const viewerBottom = viewerTop + viewer.clientHeight;
                    const pageHeightEstimate = viewer.scrollHeight / pdfDoc.numPages;
                    for (let i = 1; i <= pdfDoc.numPages; i++) {
                        const pageTop = (i - 1) * pageHeightEstimate;
                        const pageBottom = pageTop + pageHeightEstimate;
                        if ((pageBottom >= viewerTop - 300) && (pageTop <= viewerBottom + 300)) {
                            renderPage(i);
                        }
                    }
                }
                function cleanupOffscreenPages() {
                    const pages = viewer.querySelectorAll('.pdf-page-container');
                    const viewerTop = viewer.scrollTop;
                    const viewerBottom = viewerTop + viewer.clientHeight;
                    pages.forEach((page, index) => {
                        const pageTop = page.offsetTop;
                        const pageBottom = pageTop + page.offsetHeight;
                        if (pageBottom < viewerTop - 1000 || pageTop > viewerBottom + 1000) {
                            page.remove();
                            renderedPages.delete(index + 1);
                        }
                    });
                }
                let scrollTimeout;
                viewer.addEventListener('scroll', () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        renderVisiblePages();
                        cleanupOffscreenPages();
                    }, 150);
                });
                window.addEventListener('resize', renderVisiblePages);
            }
        </script>
    @endif
@break

    {{-- ==================== NEW: EMBED (YouTube, Vimeo, etc.) ==================== --}}
    @case('embed')
        @php
            $embedUrl = null;
            $src = $block['src'] ?? '';
            if (
                preg_match(
                    '/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/',
                    $src,
                    $matches,
                )
            ) {
                $embedUrl = 'https://www.youtube.com/embed/' . $matches[2] . '?rel=0';
            }
        @endphp
        @if ($embedUrl)
            <div class="my-6 aspect-w-16 aspect-h-9">
                <iframe src="{{ $embedUrl }}" class="w-full h-[500px] rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy"></iframe>
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
            $buttonAlignClass = match ($buttonAlignment) {
                'center' => 'text-center',
                'right' => 'text-right',
                default => 'text-left',
            };
        @endphp
        <div class="my-6 {{ $buttonAlignClass }}">
            <a href="{{ $buttonHref }}" target="{{ $buttonTarget }}"
                rel="{{ $buttonTarget === '_blank' ? 'noopener noreferrer' : '' }}"
                class="inline-block px-5 py-2 font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
