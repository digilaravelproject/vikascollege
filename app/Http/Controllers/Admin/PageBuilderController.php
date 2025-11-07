<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Exception;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the pages.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(): ViewView|RedirectResponse
    {
        $this->authorize('view pages');
        try {
            $pages = Page::latest()->get();
            return view('admin.pagebuilder.index', compact('pages'));
        } catch (Exception $e) {
            Log::error('PageBuilder Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load pages.');
        }
    }

    /**
     * Show the form for creating a new page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(): ViewView|RedirectResponse
    {
        $this->authorize('create pages');
        try {
            return view('admin.pagebuilder.create');
        } catch (Exception $e) {
            Log::error('PageBuilder Create View Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to open create form.');
        }
    }

    /**
     * Store a newly created page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create pages');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

            if ($request->hasFile('image')) {
                // Re-using the private helper for simplicity and cleaner store logic
                $validated['image'] = $this->storeRegularFile($request->file('image'), 'uploads/pages');
            }

            Page::create($validated);
            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page created successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create page.');
        }
    }

    /**
     * Show the form for editing the specified page.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Page $page): ViewView|RedirectResponse
    {
        $this->authorize('edit pages');
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
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->authorize('edit pages');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $this->deleteOldFile($page->image);
                // Re-using the private helper for file storage
                $validated['image'] = $this->storeRegularFile($request->file('image'), 'uploads/pages');
            }

            $page->update($validated);
            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page updated successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update page.');
        }
    }

    /**
     * Remove the specified page from storage.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete pages');
        try {
            // Delete associated image before deleting the page record
            $this->deleteOldFile($page->image);
            $page->delete();
            return back()->with('success', 'Page deleted successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete page.');
        }
    }


    /**
     * ADDED: Toggle the status of the specified page.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Page $page): RedirectResponse
    {
        $this->authorize('manage menus');

        try {
            // Step 1: Toggle the page status (true to false, false to true)
            $page->update(['status' => !$page->status]);

            // Step 2: Check if the page has a related menu
            if ($page->menu) {
                // If the page has a related menu, also toggle the status of the menu
                $page->menu->update(['status' => !$page->menu->status]);
            }

            // Step 3: Determine success message based on the page status
            $message = $page->status ? 'Page enabled successfully!' : 'Page disabled successfully!';

            // If the menu was also updated, add a note about that
            if ($page->menu && $page->menu->status === false) {
                $message .= ' Menu item also disabled.';
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            // Log any errors that occur during the status toggle
            Log::error('PageBuilder Toggle Status Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update page status.');
        }
    }

    public function toggleStatus_old(Page $page): RedirectResponse
    {
        try {
            // Page ki current status ko ulta kar dein (true to false, false to true)
            $page->update(['status' => !$page->status]);

            // Status ke hisaab se message set karein
            $message = $page->status ? 'Page enabled successfully!' : 'Page disabled successfully!';

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('PageBuilder Toggle Status Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update page status.');
        }
    }

    /**
     * Show the page builder interface for the specified page.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function builder(Page $page): ViewView|RedirectResponse
    {
        $this->authorize('edit pages');

        try {
            return view('admin.pagebuilder.builder', compact('page'));
        } catch (Exception $e) {
            Log::error('PageBuilder Builder Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load page builder.');
        }
    }

    /**
     * Save the page builder content (JSON) to the specified page.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveBuilder(Request $request, Page $page): JsonResponse // Note: Only returns JsonResponse now
    {
        $this->authorize('edit pages');

        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        try {
            $page->update(['content' => $validated['content']]);

            // ✅ THE FIX: Return a JSON object on success
            return response()->json([
                'success' => true,
                'message' => 'Page saved successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('PageBuilder Save Error: ' . $e->getMessage());

            // This part was already correct!
            return response()->json([
                'success' => false,
                'message' => 'Failed to save content.'
            ], 500); // Also good to add a 500 status on server error
        }
    }

    /**
     * Handle AJAX media upload for the page builder.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\JsonResponse
     */
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
            $customName = $validated['custom_name'] ?? null;
            // 2. Determine Subfolder (Unchanged)
            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' && $customName => 'uploads', // if custom_name present
                $mime === 'application/pdf' => 'uploads/pdfs',           // otherwise default
                default => null,
            };

            if (!$subFolder) {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
            }

            // ------------------ FILENAME LOGIC FIX START (Raw Custom Name Priority) ------------------
            $ext = $file->getClientOriginalExtension();
            $rawPathAndName = trim($validated['custom_name'] ?? '');
            $finalName = '';

            // CRITICAL FIX: Custom name ko pura filename manenge, lekin extension add karenge.
            if ($rawPathAndName) {
                // Filename part nikalna
                $filenamePart = pathinfo($rawPathAndName, PATHINFO_FILENAME);

                // Filename ke path component ko directory adjustment ke liye nikalna
                $customPath = trim(pathinfo($rawPathAndName, PATHINFO_DIRNAME), './');

                // Filename ko ussi roop mein rakhenge, lekin extension confirm karenge
                $finalName = $filenamePart . '.' . $ext;

                // IMPORTANT: Agar custom path diya gaya hai, to use $subFolder mein merge karna
                if ($customPath && $customPath !== '/') {
                    $subFolder = trim($subFolder . '/' . $customPath, '/');
                }
            } else {
                // Agar custom_name nahi diya, to original name use karo
                $finalName = $file->getClientOriginalName();
            }
            // ------------------ FILENAME LOGIC FIX END ------------------

            $basePath = $validated['base_path'] ?? 'wp-content';
            $directory = "{$basePath}/{$subFolder}";

            // 4. Store File
            if ($basePath === 'wp-content') {
                $targetPath = public_path($directory);

                if (!is_dir($targetPath)) {
                    if (!mkdir($targetPath, 0775, true)) {
                        throw new Exception("Failed to create directory: {$targetPath}");
                    }
                }

                $file->move($targetPath, $finalName);
                $url = asset("{$directory}/{$finalName}");
                $finalDirectory = $directory;
            } else {
                // Laravel Storage method (storage/app/public)
                $path = $file->storeAs($subFolder, $finalName, 'public');
                $url = Storage::url($path);
                $finalDirectory = "storage/{$subFolder}";
            }

            // 5. Return Success Response
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => "{$finalDirectory}/{$finalName}",
                'filename' => $finalName,
            ]);
        } catch (Exception $e) {
            Log::error('Upload Media Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }


    /**
     * Stores a regular file (e.g., image upload from CRUD forms) using Laravel Storage.
     * Replaces the redundant handleFileUpload logic in store/update.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string|null The stored file path relative to the 'public' disk.
     */
    private function storeRegularFile($file, string $path): ?string
    {
        try {
            if ($file) {
                // Generates a unique filename and stores it.
                return $file->store($path, 'public');
            }
            return null;
        } catch (Exception $e) {
            Log::error('Store Regular File Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Deletes an old file from the storage/app/public disk.
     *
     * @param string|null $filePath
     * @return void
     */
    private function deleteOldFile(?string $filePath): void
    {
        try {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        } catch (Exception $e) {
            Log::warning('Delete Old File Warning: ' . $e->getMessage());
            // Warning instead of error, as deletion failure shouldn't stop CRUD operations
        }
    }
}
