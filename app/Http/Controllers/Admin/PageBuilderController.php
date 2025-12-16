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
        $this->authorize('edit pages');

        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        try {
            $page->update(['content' => $validated['content']]);

            // Clear cache and queue a warm-up
            $this->clearAllCaches($page);
            Artisan::queue('cache:warm-pages');

            return response()->json([
                'success' => true,
                'message' => 'Page saved! Cache is rebuilding in background.',
            ]);
        } catch (Exception $e) {
            Log::error('PageBuilder Save Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save content.',
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
            Log::info('Cache cleared for page: ' . $page->slug);

            // If the page is linked to a menu, clear menu-related caches
            if ($page->menu) {
                $menu = $page->menu;

                // Clear top parent cache
                Cache::forget('menu:top_parent:' . $menu->id);

                // Find top parent to clear sidebar cache
                $current = $menu;
                while ($current->parent_id && $current->parent) {
                    $current = $current->parent;
                }

                Cache::forget('menu:sidebar:' . $current->id);
                Log::info('Cache cleared for sidebar menu: ' . $current->id);
            }
        } catch (Exception $e) {
            Log::error('Failed to clear menu cache for page ID ' . $page->id . ': ' . $e->getMessage());
        }
    }
}
