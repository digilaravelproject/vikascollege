<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Exception;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageBuilderController_old extends Controller
{
    public function index(): ViewView|RedirectResponse
    {
        try {
            $pages = Page::latest()->get();
            return view('admin.pagebuilder.index', compact('pages'));
        } catch (Exception $e) {
            Log::error('PageBuilder Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load pages.');
        }
    }

    public function create(): ViewView|RedirectResponse
    {
        try {
            return view('admin.pagebuilder.create');
        } catch (Exception $e) {
            Log::error('PageBuilder Create View Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to open create form.');
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
            $validated['image'] = $this->handleFileUpload($request, 'image', 'uploads/pages');
            Page::create($validated);
            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page created successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create page.');
        }
    }

    public function edit(Page $page): ViewView|RedirectResponse
    {
        try {
            return view('admin.pagebuilder.edit', compact('page'));
        } catch (Exception $e) {
            Log::error("PageBuilder Edit Error: " . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }
    /**
     * Update a specific page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $this->deleteOldFile($page->image);
                $validated['image'] = $this->handleFileUpload($request, 'image', 'uploads/pages');
            }

            $page->update($validated);
            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page updated successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update page.');
        }
    }

    public function destroy(Page $page): RedirectResponse
    {
        try {
            $page->delete();
            return back()->with('success', 'Page deleted successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete page.');
        }
    }

    public function builder(Page $page): ViewView|RedirectResponse
    {
        try {
            return view('admin.pagebuilder.builder', compact('page'));
        } catch (Exception $e) {
            Log::error('PageBuilder Builder Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load page builder.');
        }
    }

    public function saveBuilder(Request $request, Page $page): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        try {
            $page->update(['content' => $validated['content']]);
            return back()->with('success', 'Page saved successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Save Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save content.']);
        }
    }

    /** ✅ Media upload via AJAX */
    public function uploadMedia(Request $request, Page $page): JsonResponse
    {
        // 1. Validation (Same as before)
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,pdf|max:51200',
            'base_path' => 'nullable|string|in:storage,wp-content',
            'custom_name' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();

            // 2. Decide subfolder based on file type (Same as before)
            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' => 'uploads/pdfs',
                default => null,
            };

            if (!$subFolder) {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
            }

            // 3. Prepare Paths
            $basePath = $validated['base_path'] ?? 'wp-content';

            // FILENAME LOGIC FIX START
            $ext = $file->getClientOriginalExtension();
            $rawPathAndName = trim($validated['custom_name'] ?? '');

            if ($rawPathAndName) {
                // If custom_name contains path (e.g., 2024/08/file.pdf)
                $pathInfo = pathinfo($rawPathAndName);
                $customSubPath = trim($pathInfo['dirname'], '.');
                $filenameWithoutExt = $pathInfo['filename'];

                // Use Str::slug only on the filename part (without extension or path)
                $sluggedFilename = Str::slug($filenameWithoutExt);
                $finalName = $sluggedFilename . '.' . $ext;

                // Adjust subFolder/directory if custom path is given
                if ($customSubPath && $customSubPath !== '/') {
                    // Combine original subFolder with the custom path (e.g., uploads/pdfs/2024/08)
                    $subFolder = trim($subFolder . '/' . $customSubPath, '/');
                }
            } else {
                // Use original file name if no custom name is provided
                $finalName = $file->getClientOriginalName();
            }
            // FILENAME LOGIC FIX END

            $directory = "{$basePath}/{$subFolder}";

            // 4. Move file according to base path (Fixed logic inside try block)
            if ($basePath === 'wp-content') {
                $targetPath = public_path($directory);

                if (!is_dir($targetPath)) {
                    // Use try/catch for mkdir as it's prone to permission errors
                    try {
                        mkdir($targetPath, 0775, true);
                    } catch (\Throwable $th) {
                        Log::error('Upload Media Error: Failed to create directory: ' . $targetPath . ' - ' . $th->getMessage());
                        return response()->json(['success' => false, 'message' => 'Upload failed (directory creation error).'], 500);
                    }
                }

                // Use try/catch for file move
                try {
                    $file->move($targetPath, $finalName);
                    $url = asset("{$directory}/{$finalName}");
                } catch (\Throwable $th) {
                    Log::error('Upload Media Error: Failed to move file: ' . $th->getMessage());
                    return response()->json(['success' => false, 'message' => 'Upload failed (file move error).'], 500);
                }
            } else {
                // Laravel Storage method (storage)
                try {
                    $path = $file->storeAs($subFolder, $finalName, 'public');
                    $url = Storage::url($path);
                    $directory = 'storage'; // For correct path reporting in JSON
                } catch (\Throwable $th) {
                    Log::error('Upload Media Error: Storage storeAs failed: ' . $th->getMessage());
                    return response()->json(['success' => false, 'message' => 'Upload failed (storage error).'], 500);
                }
            }

            // 5. Return Success Response
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => "{$directory}/{$finalName}",
                'filename' => $finalName,
            ]);
        } catch (Exception $e) {
            Log::error('Upload Media Error (General): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }
    public function uploadMedia_old_2(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,pdf|max:51200',
            'base_path' => 'nullable|string|in:storage,wp-content',
            'custom_name' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();

            // ✅ Decide subfolder based on file type
            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' => 'uploads/pdfs',
                default => null,
            };

            if (!$subFolder) {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
            }

            // ✅ Use base path or default
            $basePath = $validated['base_path'] ?? 'wp-content';
            $directory = "{$basePath}/{$subFolder}";

            // ✅ Use manual name if given
            $ext = $file->getClientOriginalExtension();
            $rawName = trim($validated['custom_name'] ?? '');
            $finalName = $rawName
                ? Str::slug(pathinfo($rawName, PATHINFO_FILENAME)) . '.' . $ext
                : $file->getClientOriginalName();

            // ✅ Move file according to base path
            if ($basePath === 'wp-content') {
                $targetPath = public_path($directory);
                if (!is_dir($targetPath))
                    mkdir($targetPath, 0775, true);
                $file->move($targetPath, $finalName);
                $url = asset("{$directory}/{$finalName}");
            } else {
                $path = $file->storeAs($subFolder, $finalName, 'public');
                $url = Storage::url($path);
            }

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => "{$directory}/{$finalName}",
                'filename' => $finalName,
            ]);
        } catch (Exception $e) {
            Log::error('Upload Media Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }
    public function uploadMedia_old(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,pdf|max:51200',
        ]);

        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();

            if (str_starts_with($mime, 'image/'))
                $dir = 'uploads/pages';
            elseif (str_starts_with($mime, 'video/'))
                $dir = 'uploads/videos';
            elseif ($mime === 'application/pdf')
                $dir = 'uploads/pdfs';
            else
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);

            $path = $file->store($dir, 'public');
            $url = Storage::url($path);

            return response()->json(['success' => true, 'url' => $url, 'path' => $path]);
        } catch (Exception $e) {
            Log::error('Upload Media Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }

    /** ✅ Optional: delete old upload (AJAX) */
    public function deleteUploadedMedia(Request $request): JsonResponse
    {
        $request->validate(['path' => 'required|string']);
        try {
            if (Storage::disk('public')->exists($request->path)) {
                Storage::disk('public')->delete($request->path);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        } catch (Exception $e) {
            Log::error('Delete Uploaded Media Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    private function handleFileUpload(Request $request, string $field, string $path): ?string
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($path, 'public');
        }
        return null;
    }

    private function deleteOldFile(?string $filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
