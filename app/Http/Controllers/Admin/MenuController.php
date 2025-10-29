<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Page;  // Add this for Page model
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;
use Exception;

class MenuController extends Controller
{
    /**
     * Display all menus in hierarchical order.
     */
    public function index()
    {
        try {
            // Load all menus with parent-child relationship (recursive)
            $menus = Menu::with('parent', 'page') // Eager load page relation
                ->orderBy('parent_id')
                ->orderBy('order')
                ->get();

            return view('admin.menus.index', compact('menus'));
        } catch (Exception $e) {
            Log::error('Menu Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load menus. Please try again later.');
        }
    }

    /**
     * Show form for creating a new menu.
     */
    public function create()
    {
        try {
            // Fetch all menus for parent selection (nested)
            $menus = Menu::with('childrenRecursive')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();

            // Fetch all named routes for linking
            $routes = $this->getValidRoutes();

            return view('admin.menus.create', compact('menus', 'routes'));
        } catch (Exception $e) {
            Log::error('Menu Create View Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading the create form.');
        }
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            // Handle the status checkbox
            $validated['status'] = $request->has('status') ? 1 : 0;

            // Create the menu
            $menu = Menu::create($validated);

            // Check if this menu needs a page created
            if ($validated['url']) {
                // Create the page with the same URL/slug
                Page::create([
                    'slug' => $validated['url'],
                    'title' => $validated['title'],  // You can set other fields here as needed
                    'content' => '', // Empty content to start with
                ]);
            }

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu created successfully!');
        } catch (Exception $e) {
            Log::error('Menu Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create menu. Please try again.');
        }
    }

    /**
     * Show form for editing a specific menu.
     */
    public function edit(Menu $menu)
    {
        try {
            // Fetch all menus for parent selection (nested)
            $menus = Menu::with('childrenRecursive')
                ->whereNull('parent_id')
                ->where('id', '!=', $menu->id)
                ->orderBy('order')
                ->get();

            // Fetch all named routes for linking
            $routes = $this->getValidRoutes();

            return view('admin.menus.edit', compact('menu', 'menus', 'routes'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.menus.index')
                ->with('error', 'Menu not found.');
        } catch (Exception $e) {
            Log::error('Menu Edit Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load the edit form.');
        }
    }

    /**
     * Update a specific menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id|not_in:' . $menu->id,
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            // Handle the status checkbox
            $validated['status'] = $request->has('status') ? 1 : 0;

            // Update the menu
            $menu->update($validated);

            // Check if the page linked to the menu needs updating
            if ($menu->page) {
                $menu->page->update([
                    'slug' => $validated['url'],
                    'title' => $validated['title'], // Update the page title
                ]);
            }

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu updated successfully!');
        } catch (Exception $e) {
            Log::error('Menu Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update menu. Please try again.');
        }
    }

    /**
     * Delete a specific menu.
     */
    public function destroy(Menu $menu)
    {
        try {
            // Also delete child menus if they exist
            $menu->children()->delete();

            // Delete the associated page if it exists
            if ($menu->page) {
                $menu->page->delete();
            }

            // Delete the menu itself
            $menu->delete();

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu deleted successfully!');
        } catch (Exception $e) {
            Log::error('Menu Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete menu. Please try again.');
        }
    }

    /**
     * Toggle menu active status (AJAX).
     */
    public function toggleStatus(Request $request, Menu $menu)
    {
        try {
            $menu->update(['status' => $request->boolean('status')]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Menu Toggle Status Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    /**
     * Helper to fetch all valid named routes (no parameters).
     */
    private function getValidRoutes()
    {
        return collect(Route::getRoutes())
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'uri'  => $route->uri(),
                    'parameters' => $route->parameterNames(),
                ];
            })
            ->filter(function ($r) {
                return !empty($r['name']);
            })
            ->values();
    }

    private function getValidRoutes_old()
    {
        return collect(Route::getRoutes())
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'uri'  => $route->uri(),
                ];
            })
            ->filter(function ($r) {
                if (!$r['name']) return false;
                return !preg_match('/\{.*?\}/', $r['uri']);
            })
            ->values();
    }
}
