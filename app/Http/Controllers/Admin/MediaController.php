<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediaController extends Controller
{
    /**
     * Fetch media from storage/app/public/uploads and public/vikas/wp-content
     */
    public function index()
    {
        try {
            $mediaItems = collect();

            // -----------------------------
            // 1. Files from storage/app/public
            // -----------------------------
            $storageFiles = Storage::disk('public')->allFiles();

            foreach ($storageFiles as $filePath) {
                if (Str::startsWith($filePath, 'uploads/')) {

                    $mediaItems->push([
                        'disk'      => 'storage',
                        'path'      => $filePath,
                        'name'      => basename($filePath),
                        'url'       => Storage::disk('public')->url($filePath),
                        'type'      => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
                        'size'      => $this->formatBytes(Storage::disk('public')->size($filePath)),
                        'timestamp' => Storage::disk('public')->lastModified($filePath),
                    ]);
                }
            }
            // -----------------------------
            // OLD FILES: public/wp-content
            // -----------------------------
            $oldWpPath = public_path('wp-content');

            if (File::exists($oldWpPath)) {

                $oldFiles = File::allFiles($oldWpPath);

                foreach ($oldFiles as $file) {

                    $relativePath = str_replace('\\', '/', $file->getRelativePathname());
                    $filePath = 'wp-content/' . $relativePath;

                    $mediaItems->push([
                        'disk'      => 'public_wp',
                        'path'      => $filePath,
                        'name'      => $file->getFilename(),
                        'url'       => asset($filePath),
                        'type'      => strtolower($file->getExtension()),
                        'size'      => $this->formatBytes($file->getSize()),
                        'timestamp' => $file->getMTime(),
                    ]);
                }
            }
            // -----------------------------
            // 2. Files from public/vikas/wp-content
            // -----------------------------
            $wpPath = public_path('vikas/wp-content');

            if (File::exists($wpPath)) {

                $publicFiles = File::allFiles($wpPath);

                foreach ($publicFiles as $file) {

                    $relativePath = str_replace('\\', '/', $file->getRelativePathname());

                    // IMPORTANT: update filePath
                    $filePath = 'vikas/wp-content/' . $relativePath;

                    $mediaItems->push([
                        'disk'      => 'public_wp',
                        'path'      => $filePath,
                        'name'      => $file->getFilename(),
                        'url'       => asset($filePath),
                        'type'      => strtolower($file->getExtension()),
                        'size'      => $this->formatBytes($file->getSize()),
                        'timestamp' => $file->getMTime(),
                    ]);
                }
            }

            // Sort newest first
            $mediaItems = $mediaItems->sortByDesc('timestamp')->values();

            return view('admin.media.index', compact('mediaItems'));
        } catch (Exception $e) {
            Log::error('Media Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load media files.');
        }
    }

    /**
     * Upload media to correct location with custom paths and clean naming
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'media_file'        => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,mp4,mov|max:20480',
            'custom_name'       => 'nullable|string|max:255',
            'destination_disk'  => 'required|string|in:storage,wp-content',
        ]);

        try {

            $file       = $request->file('media_file');
            $disk       = $validated['destination_disk'];
            $extension  = $file->getClientOriginalExtension();
            $customPath = $validated['custom_name'];

            // Determine base folder by MIME type
            $mime = $file->getMimeType();

            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' && !empty($customPath) => 'uploads',
                $mime === 'application/pdf'       => 'uploads/pdfs',
                default                           => 'uploads/others',
            };

            // ----------------------------
            // Build final directory & filename
            // ----------------------------
            $finalDirectory = $subFolder;
            $finalFilename = '';

            if ($customPath) {

                $normalized = Str::replace('\\', '/', $customPath);
                $parts = explode('/', $normalized);

                $filenameOnly = array_pop($parts);
                $customSubDir = implode('/', $parts);

                if ($customSubDir) {
                    $finalDirectory = $subFolder . '/' . $customSubDir;
                }

                $cleanName = preg_replace('/[^A-Za-z0-9_\- ]/', '', pathinfo($filenameOnly, PATHINFO_FILENAME));
                $finalFilename = $cleanName . '.' . $extension;
            } else {

                $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanOriginal = preg_replace('/[^A-Za-z0-9_\- ]/', '', $original);
                $finalFilename = $cleanOriginal . '.' . $extension;
            }


            // ----------------------------
            // Save to storage
            // ----------------------------
            if ($disk === 'storage') {

                $file->storeAs($finalDirectory, $finalFilename, 'public');

                return back()->with('success', 'File uploaded successfully to storage!');
            }


            // ----------------------------
            // Save to public/vikas/wp-content
            // ----------------------------
            if ($disk === 'wp-content') {

                $directory = public_path("vikas/wp-content/{$finalDirectory}");

                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0775, true);
                }

                $file->move($directory, $finalFilename);

                return back()->with('success', 'File uploaded successfully to vikas/wp-content!');
            }

            // Fallback
            return back()->with('error', 'Invalid destination selected.');
        } catch (Exception $e) {
            Log::error('Media Store Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Delete media from required location
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'file_path' => 'required|string',
            'disk'      => 'required|string|in:storage,public_wp',
        ]);

        try {

            $path = $validated['file_path'];
            $disk = $validated['disk'];

            // Delete from storage
            if ($disk === 'storage') {

                if (Storage::disk('public')->exists($path)) {

                    Storage::disk('public')->delete($path);

                    return back()->with('success', 'File deleted from storage.');
                }

                return back()->with('error', 'File not found in storage.');
            }

            // Delete from public/vikas/wp-content
            if ($disk === 'public_wp') {

                $filePath = public_path($path);

                if (File::exists($filePath)) {

                    File::delete($filePath);

                    return back()->with('success', 'File deleted from vikas/wp-content.');
                }

                return back()->with('error', 'File not found in vikas/wp-content.');
            }

            return back()->with('error', 'Invalid disk selected.');
        } catch (Exception $e) {
            Log::error('Media Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete file.');
        }
    }

    /**
     * Format bytes to human friendly values
     */
    private function formatBytes($bytes, $precision = 2)
    {
        try {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);

            $bytes /= (1 << (10 * $pow));

            return round($bytes, $precision) . ' ' . $units[$pow];
        } catch (Exception $e) {
            Log::error('Format Bytes Error: ' . $e->getMessage());
            return "0 B";
        }
    }
}
