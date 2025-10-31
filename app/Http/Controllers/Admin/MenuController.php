<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Exception;

class MenuController extends Controller
{
    /**
     * Display all menus in hierarchical order (cached).
     */
    public function index()
    {
        try {
            $menus = Cache::remember('menu_tree', 3600, function () {
                return Menu::with(['parent', 'childrenRecursive', 'page'])
                    ->orderBy('parent_id')
                    ->orderBy('order')
                    ->get();
            });

            return view('admin.menus.index', compact('menus'));
        } catch (Exception $e) {
            Log::error('Error loading menus in index', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Unable to load menus. Please try again later.');
        }
    }

    /**
     * Show form for creating a new menu.
     */
    public function create()
    {
        try {
            $menus = Menu::with('childrenRecursive')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();

            $routes = $this->getValidRoutes();
            $pages = Page::where('status', true)->orderBy('title')->get();

            return view('admin.menus.create', compact('menus', 'routes', 'pages'));
        } catch (Exception $e) {
            Log::error('Error loading create menu view', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load create menu form.');
        }
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateMenu($request);

            $menu = Menu::create($validated);

            if ($validated['create_page'] ?? false) {
                $this->ensurePageOrRouteExists($menu);
            }

            Cache::forget('menu_tree');

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu created successfully!');
        } catch (ValidationException $e) {
            Log::error('Validation error in store menu', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Error storing new menu', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Failed to create menu.');
        }
    }

    /**
     * Show form for editing a specific menu.
     */
    public function edit(Menu $menu)
    {
        try {
            $menus = Menu::with('childrenRecursive')
                ->whereNull('parent_id')
                ->where('id', '!=', $menu->id)
                ->orderBy('order')
                ->get();

            $routes = $this->getValidRoutes();
            $pages = Page::where('status', true)->orderBy('title')->get();

            return view('admin.menus.edit', compact('menu', 'menus', 'routes', 'pages'));
        } catch (Exception $e) {
            Log::error('Error loading edit menu view', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Unable to load edit form.');
        }
    }

    /**
     * Update a specific menu.
     */
    public function update(Request $request, Menu $menu)
    {
        try {
            $validated = $this->validateMenu($request, $menu->id);

            $menu->update($validated);

            if ($validated['create_page'] ?? false) {
                $this->ensurePageOrRouteExists($menu);
            }

            Cache::forget('menu_tree');

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu updated successfully!');
        } catch (ValidationException $e) {
            Log::error('Validation error in update menu', ['menu_id' => $menu->id, 'errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Error updating menu', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Failed to update menu.');
        }
    }

    /**
     * Delete a menu and its children (recursively).
     */
    public function destroy(Menu $menu)
    {
        try {
            $this->recursiveDelete($menu);
            Cache::forget('menu_tree');

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu deleted successfully!');
        } catch (Exception $e) {
            Log::error('Error deleting menu', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete menu.');
        }
    }

    /**
     * Toggle menu active status (AJAX).
     */
    /**
     * Toggle menu active status (AJAX).
     */
    public function toggleStatus(Request $request, Menu $menu)
    {
        try {
            $menu->update(['status' => $request->boolean('status')]);

            Cache::forget('menu_tree'); // <-- YEH LINE ADD KAREIN

            return response()->json(['success' => true, 'status' => $menu->status]);
        } catch (Exception $e) {
            Log::error('Error toggling menu status', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    /**
     * ======================================
     * HELPER METHODS
     * ======================================
     */

    /**
     * Validation rules for create/update.
     */
    private function validateMenu(Request $request, $menuId = null): array
    {
        try {
            $request->merge([
                'status' => $request->boolean('status'),
                'create_page' => $request->boolean('create_page'),
            ]);

            return $request->validate([
                'title'     => 'required|string|max:255',
                'url'       => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:menus,id|not_in:' . $menuId,
                'order'     => 'nullable|integer|min:0',
                'status'    => 'nullable|boolean',
                'create_page' => 'nullable|boolean',
            ]);
        } catch (Exception $e) {
            Log::error('Validation error in validateMenu', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e; // Re-throw exception for the caller to handle
        }
    }

    /**
     * Get all valid named routes (excluding those with parameters).
     */
    private function getValidRoutes()
    {
        try {
            return collect(Route::getRoutes())
                ->map(fn($route) => [
                    'name'       => $route->getName(),
                    'uri'        => $route->uri(),
                    'parameters' => $route->parameterNames(),
                ])
                ->filter(fn($r) => !empty($r['name']) && empty($r['parameters']))
                ->values();
        } catch (Exception $e) {
            Log::error('Error retrieving valid routes', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return []; // Return empty array if routes cannot be fetched
        }
    }

    /**
     * Ensure that a menu's URL corresponds to a route or page.
     * If no route exists, a page is auto-created or updated and linked.
     */
    private function ensurePageOrRouteExists(Menu $menu): void
    {
        try {
            if (empty($menu->url)) {
                return;
            }

            $slug = trim($menu->url, '/');
            $routeExists = collect(Route::getRoutes())
                ->contains(fn($route) => $route->uri() === $slug);

            $page = Page::withTrashed()->where('slug', $slug)->first();

            if (!$routeExists) {
                if ($page) {
                    $page->update(['title' => $menu->title]);

                    if ($page->trashed()) {
                        $page->restore();
                    }

                    if (!$page->menu_id) {
                        $page->update(['menu_id' => $menu->id]);
                    }
                } else {
                    Page::create([
                        'slug'    => $slug,
                        'title'   => $menu->title,
                        'content' => '',
                        'menu_id' => $menu->id,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Error ensuring page or route exists', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    /**
     * Recursively delete a menu and its children,
     * unlinking their pages safely.
     */
    private function recursiveDelete(Menu $menu): void
    {
        try {
            foreach ($menu->children as $child) {
                $this->recursiveDelete($child);
            }

            if ($menu->page) {
                $menu->page->update(['menu_id' => null]);
                $menu->page->delete(); // soft delete
            }

            $menu->delete();
        } catch (Exception $e) {
            Log::error('Error during recursive delete', ['menu_id' => $menu->id, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }
}
