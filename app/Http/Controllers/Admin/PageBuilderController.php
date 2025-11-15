<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Menu; // ⭐️ CACHE: Menu model ko import karein
use Exception;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache; // ⭐️ CACHE: Cache import karein
use Illuminate\Support\Facades\Artisan; // ⭐️ CACHE: Artisan import karein
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    use AuthorizesRequests;

    // ... (index, create, store methods mein koi change nahi) ...

    /**
     * Display a listing of the pages.
     */
    public function index(): ViewView|RedirectResponse
    {
        // ... (No change) ...
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
     */
    public function create(): ViewView|RedirectResponse
    {
        // ... (No change) ...
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
     */
    public function store(Request $request): RedirectResponse
    {
        // ... (No change, lekin store ke baad bhi cache warm kar sakte hain) ...
        $this->authorize('create pages');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable',
            'image' => 'nullable|image|max:20480',
        ]);

        try {
            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

            if ($request->hasFile('image')) {
                $validated['image'] = $this->storeRegularFile($request->file('image'), 'uploads/pages');
            }

            Page::create($validated);

            // ⭐️ CACHE: Naya page bana hai, toh sabhi cache ko warm kar lein
            Artisan::call('cache:warm-pages');

            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page created successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create page.');
        }
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): ViewView|RedirectResponse
    {
        // ... (No change) ...
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
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->authorize('edit pages');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable',
            'image' => 'nullable|image|max:20480',
        ]);

        try {
            if ($request->hasFile('image')) {
                $this->deleteOldFile($page->image);
                $validated['image'] = $this->storeRegularFile($request->file('image'), 'uploads/pages');
            }

            $page->update($validated);

            // ⭐️ CACHE INTEGRATION START ⭐️
            // 1. Purana page aur menu cache clear karein
            $this->clearAllCaches($page);
            // 2. Naya cache banne ke liye command run karein
            Artisan::call('cache:warm-pages');
            // ⭐️ CACHE INTEGRATION END ⭐️

            return redirect()->route('admin.pagebuilder.index')->with('success', 'Page updated successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update page.');
        }
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete pages');
        try {
            // ⭐️ CACHE INTEGRATION START ⭐️
            // Delete karne se pehle page aur menu cache clear karein
            $this->clearAllCaches($page);

            // Ab file aur page delete karein
            $this->deleteOldFile($page->image);
            $page->delete();

            // Cache warm command chalayein (taaki naya state cache ho)
            Artisan::call('cache:warm-pages');
            // ⭐️ CACHE INTEGRATION END ⭐️

            return back()->with('success', 'Page deleted successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete page.');
        }
    }


    /**
     * ADDED: Toggle the status of the specified page.
     */
    public function toggleStatus(Page $page): RedirectResponse
    {
        $this->authorize('manage menus'); // (Aapne 'manage menus' use kiya tha, wahi rakha hai)

        try {
            // Step 1: Toggle the page status
            $page->update(['status' => !$page->status]);

            // Step 2: Check if the page has a related menu
            if ($page->menu) {
                // If the page has a related menu, also toggle the status of the menu
                $page->menu->update(['status' => $page->status]); // ⭐️ FIX: Menu status page status jaisa hona chahiye
            }

            // ⭐️ CACHE INTEGRATION START ⭐️
            // 1. Purana page aur menu cache clear karein
            $this->clearAllCaches($page);
            // 2. Naya cache banne ke liye command run karein
            Artisan::call('cache:warm-pages');
            // ⭐️ CACHE INTEGRATION END ⭐️

            // Step 3: Determine success message
            $message = $page->status ? 'Page enabled successfully!' : 'Page disabled successfully!';
            if ($page->menu) {
                $message .= ' Related menu item also updated.';
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('PageBuilder Toggle Status Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update page status.');
        }
    }

    // ... (toggleStatus_old method delete kar sakte hain) ...

    /**
     * Show the page builder interface for the specified page.
     */
    public function builder(Page $page): ViewView|RedirectResponse
    {
        // ... (No change) ...
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
     */
    public function saveBuilder(Request $request, Page $page): JsonResponse
    {
        $this->authorize('edit pages');

        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        try {
            $page->update(['content' => $validated['content']]);

            // ⭐️ CACHE INTEGRATION START ⭐️
            // 1. Purana page aur menu cache clear karein
            $this->clearAllCaches($page);

            // 2. Naya cache banne ke liye command run karein (QUEUE MEIN)
            // Yeh JSON response hai, isliye response fast hona chahiye.
            // `Artisan::queue` command ko background mein chala dega.
            // Iske liye aapka queue driver (e.g., database, redis) setup hona chahiye.
            // Agar queue setup nahi hai, toh `Artisan::call` use karein.
            Artisan::queue('cache:warm-pages');
            // ⭐️ CACHE INTEGRATION END ⭐️

            return response()->json([
                'success' => true,
                'message' => 'Page saved! Cache is rebuilding in background.' // Message update kiya
            ]);
        } catch (Exception $e) {
            Log::error('PageBuilder Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save content.'
            ], 500);
        }
    }

    // ... (uploadMedia method mein koi change nahi) ...

    /** ✅ Media upload via AJAX */
    public function uploadMedia(Request $request, Page $page): JsonResponse
    {
        // ... (No change) ...
        $validated = $request->validate([
            // 'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,pdf|max:81920',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,
            mp4,mov,avi,wmv,flv,mkv,webm,
            pdf|max:81920',

            'base_path' => 'nullable|string|in:storage,wp-content',
            'custom_name' => 'nullable|string|max:255',
        ]);
        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();
            $customName = $validated['custom_name'] ?? null;
            $subFolder = match (true) {
                str_starts_with($mime, 'image/') => 'uploads/images',
                str_starts_with($mime, 'video/') => 'uploads/videos',
                $mime === 'application/pdf' && $customName => 'uploads',
                $mime === 'application/pdf' => 'uploads/pdfs',
                default => null,
            };
            if (!$subFolder) {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
            }
            $ext = $file->getClientOriginalExtension();
            $rawPathAndName = trim($validated['custom_name'] ?? '');
            $finalName = '';
            if ($rawPathAndName) {
                $filenamePart = pathinfo($rawPathAndName, PATHINFO_FILENAME);
                $customPath = trim(pathinfo($rawPathAndName, PATHINFO_DIRNAME), './');
                $finalName = $filenamePart . '.' . $ext;
                if ($customPath && $customPath !== '/') {
                    $subFolder = trim($subFolder . '/' . $customPath, '/');
                }
            } else {
                $finalName = $file->getClientOriginalName();
            }
            $basePath = $validated['base_path'] ?? 'wp-content';
            // $directory = "{$basePath}/{$subFolder}";
            if ($basePath === 'wp-content') {
                $directory = "vikas/wp-content/{$subFolder}";
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
                $path = $file->storeAs($subFolder, $finalName, 'public');
                $url = Storage::url($path);
                $finalDirectory = "storage/{$subFolder}";
            }
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


    // ... (storeRegularFile aur deleteOldFile methods mein koi change nahi) ...

    private function storeRegularFile($file, string $path): ?string
    {
        // ... (No change) ...
        try {
            if ($file) {
                return $file->store($path, 'public');
            }
            return null;
        } catch (Exception $e) {
            Log::error('Store Regular File Error: ' . $e->getMessage());
            return null;
        }
    }

    private function deleteOldFile(?string $filePath): void
    {
        // ... (No change) ...
        try {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        } catch (Exception $e) {
            Log::warning('Delete Old File Warning: ' . $e->getMessage());
        }
    }

    // ⭐️⭐️⭐️ NYA HELPER FUNCTION ⭐️⭐️⭐️
    /**
     * Clears all relevant caches for a page and its related menu.
     *
     * @param \App\Models\Page $page
     * @return void
     */
    private function clearAllCaches(Page $page): void
    {
        try {
            // 1. Page cache ko clear karein
            Cache::forget('page:view:' . $page->slug);
            Log::info("Cache cleared for page: " . $page->slug);

            // 2. Agar page se juda menu hai, toh menu cache bhi clear karein
            // Yeh zaroori hai taaki menu mein 'active' state ya naya/purana link sahi ho
            if ($page->menu) {
                $menu = $page->menu; // Load the related menu

                // Is menu ka 'top_parent' cache clear karein
                Cache::forget('menu:top_parent:' . $menu->id);

                // Ab top parent find karein (non-cached way)
                // (Iske liye Menu model mein 'parent' relationship hona zaroori hai)
                $current = $menu;
                while ($current->parent_id && $current->parent) {
                    $current = $current->parent;
                }

                // Top parent mil gaya, ab sidebar cache clear karein
                Cache::forget('menu:sidebar:' . $current->id);
                Log::info("Cache cleared for sidebar menu: " . $current->id);
            }
        } catch (Exception $e) {
            // Agar menu ya parent relationship fail ho, toh log karein
            Log::error('Failed to clear menu cache for page ID ' . $page->id . ': ' . $e->getMessage());
        }
    }
}
