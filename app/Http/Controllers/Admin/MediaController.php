<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Dono locations se saari media files dikhaye.
     */
    public function index()
    {
        try {
            $mediaItems = collect();
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */

            // 1. 'storage/app/public' se files fetch karna
            $storageFiles = Storage::disk('public')->allFiles();

            foreach ($storageFiles as $filePath) {

                // 'public' disk ke root folders (e.g., 'uploads') ko hi include karein
                if (Str::startsWith($filePath, 'uploads/')) {
                    $mediaItems->push([
                        'disk' => 'storage',
                        'path' => $filePath,
                        'name' => basename($filePath),
                        'url' => Storage::disk('public')->url($filePath),
                        'type' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
                        'size' => $this->formatBytes(Storage::disk('public')->size($filePath)),
                        'timestamp' => Storage::disk('public')->lastModified($filePath), // <-- ADDED
                    ]);
                }
            }

            // 2. 'public/wp-content' se files fetch karna
            $publicWpPath = public_path('wp-content');
            if (File::exists($publicWpPath)) {
                $publicFiles = File::allFiles($publicWpPath);

                foreach ($publicFiles as $file) {
                    $relativePath = str_replace('\\', '/', $file->getRelativePathname());
                    $filePath = 'wp-content/' . $relativePath;

                    $mediaItems->push([
                        'disk' => 'public_wp',
                        'path' => $filePath,
                        'name' => $file->getFilename(),
                        'url' => asset($filePath),
                        'type' => strtolower($file->getExtension()),
                        'size' => $this->formatBytes($file->getSize()),
                        'timestamp' => $file->getMTime(), // <-- ADDED
                    ]);
                }
            }

            // Nayi files ko upar dikhane ke liye (timestamp se sort karein)
            $mediaItems = $mediaItems->sortByDesc('timestamp')->values(); // <-- UPDATED SORTING

            return view('admin.media.index', compact('mediaItems'));
        } catch (Exception $e) {
            Log::error('Media Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load media files.');
        }
    }

    /**
     * Nayi file upload karega (Custom name aur destination logic ke saath)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'media_file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,mp4,mov|max:20480', // 20MB Max
            'custom_name' => 'nullable|string|max:255',
            'destination_disk' => 'required|string|in:storage,wp-content',
        ]);

        try {
            $file = $request->file('media_file');
            $disk = $validated['destination_disk'];
            $extension = $file->getClientOriginalExtension();

            // Final filename determine karein
            if ($request->input('custom_name')) {
                // User ne custom name diya hai. Use slug karke extension add karein.
                $nameWithoutExtension = pathinfo($request->input('custom_name'), PATHINFO_FILENAME);
                $finalName = Str::slug($nameWithoutExtension) . '.' . $extension;
            } else {
                // Koi custom name nahi, original use karein
                $finalName = $file->getClientOriginalName();
            }

            // File type ke hisab se subfolder determine karein
            $mime = $file->getMimeType();
            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' => 'uploads/pdfs',
                default => 'uploads/others',
            };

            // Destination ke hisab se save karein
            if ($disk === 'storage') {
                // 'storage/app/public' mein save karein
                $directory = $subFolder; // Path relative to storage/app/public
                $file->storeAs($directory, $finalName, 'public');
                $message = 'File uploaded successfully to storage!';
            } else {
                // 'public/wp-content' mein save karein
                $directory = "wp-content/{$subFolder}";
                $targetPath = public_path($directory);

                if (!File::isDirectory($targetPath)) {
                    File::makeDirectory($targetPath, 0775, true);
                }

                $file->move($targetPath, $finalName);
                $message = 'File uploaded successfully to wp-content!';
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('Media Store Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload file. ' . $e->getMessage());
        }
    }

    /**
     * File ko delete karega... (Yeh function same rahega)
     */
    public function destroy(Request $request)
    {
        // ... (Aapka pehle wala destroy logic bilkul sahi hai, koi change nahi) ...
        $validated = $request->validate([
            'file_path' => 'required|string',
            'disk' => 'required|string|in:storage,public_wp',
        ]);

        try {
            $path = $validated['file_path'];
            $disk = $validated['disk'];

            if ($disk === 'storage') {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    return back()->with('success', 'File deleted from storage.');
                }
            } elseif ($disk === 'public_wp') {
                $publicFilePath = public_path($path);
                if (File::exists($publicFilePath)) {
                    File::delete($publicFilePath);
                    return back()->with('success', 'File deleted from public/wp-content.');
                }
            }

            return back()->with('error', 'File not found or already deleted.');
        } catch (Exception $e) {
            Log::error('Media Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete file.');
        }
    }

    /**
     * Helper function to format file size (Yeh function same rahega)
     */
    private function formatBytes($bytes, $precision = 2)
    {
        // ... (Aapka pehle wala formatBytes logic bilkul sahi hai, koi change nahi) ...
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
