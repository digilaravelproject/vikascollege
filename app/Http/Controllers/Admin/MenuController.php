<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
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
            $menus = Menu::with('parent')
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
            $validated['status'] = $request->has('status') ? 1 : 0;
            Menu::create($validated);

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
            $menus = Menu::with('childrenRecursive')
                ->whereNull('parent_id')
                ->where('id', '!=', $menu->id)
                ->orderBy('order')
                ->get();

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
            $validated['status'] = $request->has('status') ? 1 : 0;
            $menu->update($validated);

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
                    'parameters' => $route->parameterNames(), // <-- Add this
                ];
            })
            ->filter(function ($r) {
                // Sirf named routes rakho (parameters allowed)
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
