<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UniversalPdfController extends Controller
{
    public function handle(Request $request, $any)
    {
        try {
            // -------------------------------
            // 1. SECURITY: Prevent Path Traversal
            // -------------------------------
            $safePath = str_replace(['../', './', '..\\', '.\\'], '', $any);

            // -------------------------------
            // 2. Build Absolute Path
            // -------------------------------
            $filePath = public_path($safePath);

            // -------------------------------
            // 3. File Exist Check
            // -------------------------------
            if (!file_exists($filePath)) {
                Log::warning("PDF not found: {$filePath}");

                return response()->view('errors.custom', [
                    'message' => 'Requested file was not found on the server.'
                ], HttpResponse::HTTP_NOT_FOUND);
            }

            // -------------------------------
            // 4. Only PDF Allowed (MIME check)
            // -------------------------------
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (!in_array($extension, ['pdf'])) {
                Log::error("Blocked non-PDF file access attempt: {$filePath}");

                return response()->view('errors.custom', [
                    'message' => 'Invalid file format. Only PDFs are allowed.'
                ], HttpResponse::HTTP_FORBIDDEN);
            }

            // -------------------------------
            // 5. Direct Stream Mode (Iframe)
            // -------------------------------
            if ($request->boolean('view_embedded')) {
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
                ]);
            }

            // -------------------------------
            // 6. Normal Request — show wrapper
            // -------------------------------
            return view('partials.pdf-wrapper', [
                'pdfUrl' => url()->current() . '?view_embedded=true'
            ]);
        } 
        
        catch (\Throwable $e) {
            // Log detailed exception for developers
            Log::error("PDF Load Error: " . $e->getMessage(), [
                'file' => $any,
                'trace' => $e->getTraceAsString()
            ]);

            // User-friendly message
            return response()->view('errors.custom', [
                'message' => 'Something went wrong while loading the PDF.'
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
