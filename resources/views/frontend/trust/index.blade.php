@extends('layouts.app')

{{-- Safety Check: Agar title nahi hai to default show kare --}}
@section('title', $activeSection ? $activeSection->title : 'Trust Section')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css"
        integrity="sha512-UmV2ARg2MsY8TysMjhJvXSQHYgiYSVPS5ULXZCsTP3RgiMmBJhf8qP93vEyJgYuGt3u9V6wem73b11/Y8GVcOg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <section class="container px-4 py-10 mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">

            {{-- Sidebar --}}
            <aside class="space-y-2 md:sticky md:top-24 h-fit">
                <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b">The Trust</h2>
                
                @if(isset($sections) && count($sections) > 0)
                    @foreach($sections as $section)
                        <a href="{{ route('trust.index', $section->slug) }}" 
                           class="block px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                           {{ (isset($activeSection) && $activeSection->id == $section->id)
                                ? 'bg-[#013954] text-white shadow-md'
                                : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]' }}">
                            {{ strtoupper($section->title) }}
                        </a>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">No sections found.</p>
                @endif
            </aside>

            {{-- Main Content --}}
            <main class="p-6 space-y-6 bg-white shadow-md rounded-2xl md:col-span-3">
                
                @if(isset($activeSection) && $activeSection)
                    
                    {{-- Display Content --}}
                    @if($activeSection->content)
                        <div id="quill-content" class="max-w-full">
                            {!! $activeSection->content !!}
                        </div>
                    @endif

                    {{-- Image Gallery --}}
                    @if($activeSection->images && $activeSection->images->count())
                        <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 md:grid-cols-3">
                            @foreach($activeSection->images as $img)
                                <img src="{{ asset('storage/' . $img->image_path) }}" 
                                     alt="{{ $activeSection->title }}"
                                     class="object-cover w-full h-64 rounded-lg shadow">
                            @endforeach
                        </div>
                    @endif

                    {{-- PDF Viewer (Replaced iframe with PDF.js) --}}
                    @if($activeSection->pdf_path)
                        @php
                            $uniqueId = 'trust_pdf_' . uniqid();
                            // .htaccess bypass ke liye ?view=raw add kiya hai
                            $src = asset('storage/' . $activeSection->pdf_path) . '?view=raw';
                        @endphp

                        <div class="mt-6 pdf-viewer-wrapper-{{ $uniqueId }} w-full relative">
                            
                            {{-- Viewer Container --}}
                            <div id="{{ $uniqueId }}_container"
                                class="w-full h-[600px] border rounded-lg shadow-inner overflow-auto relative bg-gray-50"
                                data-pdf-url="{{ $src }}">

                                {{-- Loading Spinner --}}
                                <div id="{{ $uniqueId }}_loading"
                                    class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-gray-50 rounded-lg">
                                    <div class="animate-pulse space-y-4 w-64">
                                        <div class="h-4 bg-gray-300 rounded w-3/4 mx-auto"></div>
                                        <div class="h-4 bg-gray-300 rounded w-1/2 mx-auto"></div>
                                    </div>
                                    <p class="mt-4 text-sm flex items-center justify-center gap-2 text-gray-500">
                                        Loading Document...
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- PDF Scripts --}}
                        <script>
                            if (!window.pdfjsLib) {
                                const script = document.createElement('script');
                                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                                document.head.appendChild(script);
                            }
                        </script>

                        <script>
                            (function() {
                                const containerId = "{{ $uniqueId }}_container";
                                const loadingId = "{{ $uniqueId }}_loading";

                                const initViewer = () => {
                                    const viewer = document.getElementById(containerId);
                                    const loadingMessage = document.getElementById(loadingId);
                                    if (!viewer) return;

                                    const url = viewer.dataset.pdfUrl;

                                    const observer = new IntersectionObserver((entries) => {
                                        entries.forEach(entry => {
                                            if (entry.isIntersecting) {
                                                loadPdfScoped(url, viewer, loadingMessage);
                                                observer.unobserve(viewer);
                                            }
                                        });
                                    }, { rootMargin: '100px' });
                                    observer.observe(viewer);
                                };

                                async function loadPdfScoped(url, viewer, loadingMessage) {
                                    if (typeof pdfjsLib === 'undefined') {
                                        setTimeout(() => loadPdfScoped(url, viewer, loadingMessage), 100);
                                        return;
                                    }

                                    const devicePixelRatio = window.devicePixelRatio || 1;
                                    const baseScale = window.innerWidth < 768 ? 0.8 : 1.3;
                                    let pdfDoc = null;
                                    const renderedPages = new Set();

                                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                                    try {
                                        pdfDoc = await pdfjsLib.getDocument(url).promise;
                                        const initialPages = Math.min(3, pdfDoc.numPages);
                                        for (let i = 1; i <= initialPages; i++) {
                                            await renderPage(i);
                                        }

                                        if (loadingMessage) loadingMessage.remove();

                                        if (pdfDoc.numPages > initialPages) {
                                            let scrollTimeout;
                                            viewer.addEventListener('scroll', () => {
                                                clearTimeout(scrollTimeout);
                                                scrollTimeout = setTimeout(() => renderVisiblePages(), 150);
                                            });
                                        }
                                    } catch (error) {
                                        console.error('PDF Error:', error);
                                        if(loadingMessage) loadingMessage.innerHTML = '<span class="text-red-500">Failed to load PDF</span>';
                                    }

                                    async function renderPage(pageNumber) {
                                        if (renderedPages.has(pageNumber)) return;
                                        renderedPages.add(pageNumber);

                                        const page = await pdfDoc.getPage(pageNumber);
                                        const viewport = page.getViewport({ scale: baseScale * devicePixelRatio });
                                        
                                        const wrap = document.createElement('div');
                                        wrap.style.marginBottom = "1rem";
                                        wrap.className = "relative bg-white shadow-sm mx-auto";
                                        wrap.style.maxWidth = "100%";

                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');
                                        canvas.width = viewport.width;
                                        canvas.height = viewport.height;
                                        canvas.style.width = "100%";
                                        canvas.style.height = "auto";

                                        wrap.appendChild(canvas);
                                        viewer.appendChild(wrap);

                                        await page.render({ canvasContext: ctx, viewport }).promise;
                                    }

                                    function renderVisiblePages() {
                                        const scrollTop = viewer.scrollTop;
                                        const scrollBottom = scrollTop + viewer.clientHeight;
                                        const firstCanvas = viewer.querySelector('canvas');
                                        const estHeight = firstCanvas ? firstCanvas.clientHeight : 800;

                                        for (let i = 1; i <= pdfDoc.numPages; i++) {
                                            const top = (i - 1) * estHeight;
                                            if (top < scrollBottom + 500 && top > scrollTop - 500) {
                                                renderPage(i);
                                            }
                                        }
                                    }
                                }

                                document.addEventListener('DOMContentLoaded', initViewer);
                            })();
                        </script>
                    @endif

                @else
                    {{-- Agar Active Section Found Nahi hua --}}
                    <div class="text-center py-10">
                        <h2 class="text-xl font-bold text-gray-700">Section Not Found</h2>
                        <p class="text-gray-500">Please select a section from the sidebar.</p>
                    </div>
                @endif
            </main>
        </div>
    </section>

    {{-- Styles --}}
    <style>
        #quill-content p { /* margin-bottom: 1rem; */ }
        #quill-content h1 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
        #quill-content h2 { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem; }
        #quill-content h3 { font-size: 1.25rem; font-weight: 500; margin-bottom: 0.5rem; }
        #quill-content ul, #quill-content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
        #quill-content strong { font-weight: 700; }
        #quill-content em { font-style: italic; }
        #quill-content a { color: #3b82f6; text-decoration: underline; }
        #quill-content img { max-width: 100%; height: auto; margin: 1rem 0; border-radius: 0.5rem; }
        #quill-content .ql-align-center { text-align: center; }
        #quill-content .ql-align-right { text-align: right; }
        #quill-content .ql-align-justify { text-align: justify; }
        #quill-content [data-list="ordered"], #quill-content .ql-list-ordered { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
        #quill-content [data-list="bullet"], #quill-content .ql-list-bullet { list-style-type: disc; margin-left: 1.5rem; }
        #quill-content .ql-indent-1 { margin-left: 2em; }
        #quill-content .ql-indent-2 { margin-left: 4em; }
        #quill-content .ql-indent-3 { margin-left: 6em; }
    </style>
@endsection