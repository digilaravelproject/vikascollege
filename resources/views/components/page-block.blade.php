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
    @case('pdf')
        @if (!empty($block['src']))
            <div class="my-6">
                <div id="pdf-viewer" class="w-full max-h-[700px] border rounded-lg shadow-inner overflow-auto relative"></div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
            <script>
                const url = '{{ $block['src'] }}';
                const viewer = document.getElementById('pdf-viewer');
                const devicePixelRatio = window.devicePixelRatio || 1; // Retina / high-DPI scaling
                const baseScale = 1.5;
                let pdfDoc = null;
                const renderedPages = new Set();

                // Disable text selection & right-click
                viewer.style.userSelect = "none";
                viewer.addEventListener('contextmenu', e => e.preventDefault());

                // PDF.js worker
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                // Load PDF
                pdfjsLib.getDocument(url).promise.then(pdf => {
                    pdfDoc = pdf;
                    // Initially render visible pages
                    renderVisiblePages();
                }).catch(err => {
                    console.error('PDF load error:', err);
                    viewer.innerText = "Unable to load PDF.";
                });

                // Render a single page on canvas
                function renderPage(pageNumber) {
                    if (renderedPages.has(pageNumber)) return; // Already rendered
                    pdfDoc.getPage(pageNumber).then(page => {
                        const viewport = page.getViewport({
                            scale: baseScale * devicePixelRatio
                        });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        canvas.style.width = "100%";
                        canvas.style.height = "auto";
                        canvas.style.display = "block";
                        canvas.style.marginBottom = "1rem";

                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });
                        viewer.appendChild(canvas);
                        renderedPages.add(pageNumber);
                    });
                }

                // Lazy load pages near viewport
                function renderVisiblePages() {
                    const viewerTop = viewer.scrollTop;
                    const viewerBottom = viewerTop + viewer.clientHeight;
                    const pageHeightEstimate = viewer.scrollHeight / pdfDoc.numPages;

                    for (let i = 1; i <= pdfDoc.numPages; i++) {
                        const pageTop = (i - 1) * pageHeightEstimate;
                        const pageBottom = pageTop + pageHeightEstimate;
                        // Render if page is in viewport (+ buffer 300px)
                        if ((pageBottom >= viewerTop - 300) && (pageTop <= viewerBottom + 300)) {
                            renderPage(i);
                        }
                    }
                }

                // Scroll listener for lazy load
                viewer.addEventListener('scroll', renderVisiblePages);
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
