<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Exception;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the pages.
     */
     public function index(Request $request): ViewView|JsonResponse|RedirectResponse
{
    $this->authorize('view pages');
    try {
        $query = Page::query()->latest();

        // Search Logic (Title aur Slug dono par)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Pagination (10 pages per page)
        $pages = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.pagebuilder.partials._table_rows', compact('pages'))->render(),
                'pagination' => (string) $pages->links()
            ]);
        }

        return view('admin.pagebuilder.index', compact('pages'));
    } catch (Exception $e) {
        Log::error('PageBuilder Index Error: ' . $e->getMessage());
        return back()->with('error', 'Failed to load pages.');
    }
}
    public function index_old(): ViewView|RedirectResponse
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
     */
    public function store(Request $request): RedirectResponse
    {
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

            // Warm up cache for the new page
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
        $this->authorize('edit pages');
        try {
            return view('admin.pagebuilder.edit', compact('page'));
        } catch (Exception $e) {
            Log::error('PageBuilder Edit Error: ' . $e->getMessage());

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

            // Clear outdated caches and warm up new ones
            $this->clearAllCaches($page);
            Artisan::call('cache:warm-pages');

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
            // Clear cache before deletion
            $this->clearAllCaches($page);

            $this->deleteOldFile($page->image);
            $page->delete();

            // Rebuild cache to reflect deletion
            Artisan::call('cache:warm-pages');

            return back()->with('success', 'Page deleted successfully!');
        } catch (Exception $e) {
            Log::error('PageBuilder Delete Error: ' . $e->getMessage());

            return back()->with('error', 'Failed to delete page.');
        }
    }

    /**
     * Toggle the status of the specified page.
     */
    public function toggleStatus(Page $page): RedirectResponse
    {
        $this->authorize('manage menus');

        try {
            // Toggle page status
            $page->update(['status' => ! $page->status]);

            // Sync status with related menu item if exists
            if ($page->menu) {
                $page->menu->update(['status' => $page->status]);
            }

            // Refresh caches
            $this->clearAllCaches($page);
            Artisan::call('cache:warm-pages');

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

    /**
     * Show the page builder interface for the specified page.
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
     */
    public function saveBuilder(Request $request, Page $page): JsonResponse
    {
        try {
            $this->authorize('edit pages');

            // Capture raw input for debugging
            Log::debug('PageBuilder Save Request Received', [
                'page_id' => $page->id,
                'content_length' => strlen($request->input('content', '')),
                'content_type' => $request->header('Content-Type')
            ]);

            // Explicit validation so we can catch and log errors
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'content' => 'required|json',
            ]);

            if ($validator->fails()) {
                Log::warning('PageBuilder Validation Failed', [
                    'errors' => $validator->errors()->toArray(),
                    'page_id' => $page->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $page->update(['content' => $request->input('content')]);

            // Clear cache for the current page and its menu hierarchy
            $this->clearAllCaches($page);
            
            // Log success
            Log::info('PageBuilder Save Successful', ['page_id' => $page->id, 'slug' => $page->slug]);

            return response()->json([
                'success' => true,
                'message' => 'Page saved successfully! Cache cleared.',
            ]);

        } catch (\Throwable $e) {
            Log::error('PageBuilder Save Fatal Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000),
                'page_id' => $page->id ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Media upload via AJAX for the page builder.
     */
    public function uploadMedia(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,webm,mov,pdf|max:81920',
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

            if (! $subFolder) {
                return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
            }

            $ext = $file->getClientOriginalExtension();
            $rawPathAndName = trim($validated['custom_name'] ?? '');
            $finalName = '';

            if ($rawPathAndName) {
                $filenamePart = pathinfo($rawPathAndName, PATHINFO_FILENAME);
                $customPath = trim(pathinfo($rawPathAndName, PATHINFO_DIRNAME), './');

                // Sanitize filename: allow alphanumeric, underscores, dashes, dots, and spaces.
                $cleanName = preg_replace('/[^A-Za-z0-9_\-\. ]/', '', $filenamePart);
                $finalName = $cleanName . '.' . $ext;

                if ($customPath && $customPath !== '/') {
                    $subFolder = trim($subFolder . '/' . $customPath, '/');
                }
            } else {
                $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // Sanitize filename
                $cleanOriginal = preg_replace('/[^A-Za-z0-9_\-\. ]/', '', $original);
                $finalName = $cleanOriginal . '.' . $ext;
            }

            $basePath = $validated['base_path'] ?? 'wp-content';

            if ($basePath === 'wp-content') {
                $directory = "vikas/wp-content/{$subFolder}";
                $targetPath = public_path($directory);
                if (! is_dir($targetPath)) {
                    if (! mkdir($targetPath, 0775, true)) {
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

    /**
     * Store a file in the public disk.
     */
    private function storeRegularFile($file, string $path): ?string
    {
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

    /**
     * Delete a file from the public disk if it exists.
     */
    private function deleteOldFile(?string $filePath): void
    {
        try {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        } catch (Exception $e) {
            Log::warning('Delete Old File Warning: ' . $e->getMessage());
        }
    }

    /**
     * Clears all relevant caches for a page and its related menu hierarchies.
     */
    private function clearAllCaches(Page $page): void
    {
        try {
            // Clear specific page view cache
            Cache::forget('page:view:' . $page->slug);
            Log::info('Cache cleared for dynamic page view: ' . $page->slug);

            // If the page is linked to a menu, clear menu-related caches
            // We use 'load' to ensure we have the menu relationship
            $page->load('menu'); 
            
            if ($page->menu) {
                $menu = $page->menu;

                // Clear top parent cache for this specific menu item
                Cache::forget('menu:top_parent:' . $menu->id);

                // Find top parent to clear sidebar cache safely
                $current = $menu;
                $safetyLimit = 0;
                
                // Traverse up to find the root menu item
                while ($current && $current->parent_id && $safetyLimit < 10) {
                    $parent = $current->parent; // This will trigger a load if not already loaded
                    if (!$parent) {
                        Log::warning("Broken menu hierarchy detected for menu ID: " . $current->id);
                        break;
                    }
                    $current = $parent;
                    $safetyLimit++;
                    
                    // Clear intermediate caches if they exist
                    Cache::forget('menu:top_parent:' . $current->id);
                }

                if ($current) {
                    Cache::forget('menu:sidebar:' . $current->id);
                    Log::info('Menu cache cleared for root: ' . $current->id);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to clear caches for page ID ' . $page->id . ': ' . $e->getMessage());
        }
    }
}
