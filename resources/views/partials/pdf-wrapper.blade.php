@extends('layouts.app') {{-- Tumhara Main Layout --}}

@section('title', 'Document Viewer')

@section('content')
    {{-- Main Wrapper --}}
    <div class="w-full min-h-screen bg-gray-100 flex flex-col pt-4 pb-10 px-4 md:px-10">
        
        {{-- Header Section (Optional Title) --}}
        <div class="max-w-5xl mx-auto w-full mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-[#013954] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Document Viewer
            </h1>
            {{-- Back Button (Optional) --}}
            <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-[#013954] font-medium underline">
                &larr; Go Back
            </a>
        </div>

        {{-- 
            🔥 YOUR CUSTOM PDF VIEWER LOGIC STARTS HERE 
            Hum wahi code use kar rahe hain jo tumne diya, bas $block['src'] ki jagah $pdfUrl variable use karenge.
        --}}
        
        @php 
            $uniqueId = 'pdf_viewer_' . uniqid(); 
            // Controller se $pdfUrl variable aayega
            $src = $pdfUrl; 
        @endphp

        <link rel="preload" href="{{ $src }}" as="fetch" crossorigin="anonymous">

        <style>
            @media print {
                .pdf-viewer-wrapper-{{ $uniqueId }} {
                    display: none !important;
                }
            }
            /* Custom Scrollbar for Viewer */
            #{{ $uniqueId }}_container::-webkit-scrollbar {
                width: 8px;
            }
            #{{ $uniqueId }}_container::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            #{{ $uniqueId }}_container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }
            #{{ $uniqueId }}_container::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
        </style>

        <div class="pdf-viewer-wrapper-{{ $uniqueId }} w-full max-w-5xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            
            {{-- Viewer Container --}}
            <div id="{{ $uniqueId }}_container"
                class="w-full h-[85vh] overflow-y-auto relative bg-gray-50/50"
                data-pdf-url="{{ $src }}">
                
                {{-- Loading State --}}
                <div id="{{ $uniqueId }}_loading"
                    class="flex flex-col items-center justify-center h-full w-full absolute top-0 left-0 z-10 bg-white">
                    <div class="w-64 space-y-4 p-4">
                        <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse mx-auto"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6 animate-pulse mx-auto"></div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-[#013954]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Loading Document...
                    </p>
                </div>

            </div>
        </div>

        {{-- PDF.js Library --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

        <script>
            // Disable Print Shortcuts
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

                    // Initialize PDF Loader
                    loadPdf(url, viewer, loadingMessage);
                }
            });

            function loadPdf(url, viewer, loadingMessage) {
                const devicePixelRatio = window.devicePixelRatio || 1;
                // Thoda scale badhaya hai for better clarity on desktop
                const baseScale = window.innerWidth < 768 ? 0.8 : 1.3; 
                let pdfDoc = null;
                const renderedPages = new Set();

                // Disable right click
                viewer.style.userSelect = "none";
                viewer.addEventListener('contextmenu', e => e.preventDefault());

                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                pdfjsLib.getDocument(url).promise.then(pdf => {
                    pdfDoc = pdf;
                    // Initial load: 3 pages
                    const initialPages = Math.min(3, pdfDoc.numPages);

                    for (let i = 1; i <= initialPages; i++) {
                        renderPage(i);
                    }

                    if (loadingMessage) loadingMessage.remove();

                    // Scroll event lagao baki pages ke liye
                    if (pdfDoc.numPages > initialPages) {
                        viewer.addEventListener('scroll', handleScroll);
                    }
                }).catch(error => {
                    console.error("Error loading PDF:", error);
                    if(loadingMessage) {
                        loadingMessage.innerHTML = `<p class="text-red-500">Error loading document.</p>`;
                    }
                });

                function renderPage(pageNumber) {
                    if (renderedPages.has(pageNumber)) return;

                    // Mark as rendering/rendered to prevent duplicate calls
                    renderedPages.add(pageNumber);

                    pdfDoc.getPage(pageNumber).then(page => {
                        const viewport = page.getViewport({
                            scale: baseScale * devicePixelRatio
                        });
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        const wrap = document.createElement('div');
                        wrap.className = 'pdf-page-container relative bg-white shadow-sm mx-auto mb-4';
                        // Max width restrict kiya hai taaki bhut bada na ho jaye
                        wrap.style.maxWidth = '100%'; 
                        
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        
                        // Responsive Styling
                        canvas.style.width = "100%";
                        canvas.style.height = "auto";
                        canvas.style.display = "block";

                        wrap.appendChild(canvas);
                        
                        // Append in order? Simple append works because we call in loop 1..N
                        // but for scroll loading, we might need checking order. 
                        // For this simple viewer, appending is usually fine.
                        
                        // Agar koi placeholder hai us number ka to replace kro, nahi to append
                        // (Simplified logic: just append)
                        viewer.appendChild(wrap);

                        const renderContext = {
                            canvasContext: ctx,
                            viewport: viewport
                        };
                        
                        page.render(renderContext);
                    });
                }

                let scrollTimeout;
                function handleScroll() {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        renderVisiblePages();
                    }, 150);
                }

                function renderVisiblePages() {
                    if (!pdfDoc) return;

                    const scrollTop = viewer.scrollTop;
                    const clientHeight = viewer.clientHeight;
                    const scrollBottom = scrollTop + clientHeight;
                    
                    // Estimate page height based on first child (if exists)
                    const firstPage = viewer.querySelector('canvas');
                    const estHeight = firstPage ? firstPage.clientHeight + 20 : 800; // +20 margin

                    for (let i = 1; i <= pdfDoc.numPages; i++) {
                        if (renderedPages.has(i)) continue;

                        const top = (i - 1) * estHeight;
                        const bottom = top + estHeight;

                        // Buffer of 500px
                        if (bottom >= scrollTop - 500 && top <= scrollBottom + 500) {
                            renderPage(i);
                        }
                    }
                }
            }
        </script>

    </div>
@endsection