<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Route;

class MenuController extends Controller
{
    /**
     * Display all menus.
     */
    public function index()
    {
        try {
            $menus = Menu::with('parent')->orderBy('order')->get();
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
            $menus = Menu::whereNull('parent_id')->get();

            // Fetch all named routes
            $routes = collect(Route::getRoutes())
                ->map(function ($route) {
                    return [
                        'name' => $route->getName(),
                        'uri'  => $route->uri(),
                    ];
                })
                ->filter(function ($r) {
                    // Sirf wo routes jinke naam hain
                    if (!$r['name']) {
                        return false;
                    }

                    // Parameterized routes hatao (jinme {something} hai)
                    return !preg_match('/\{.*?\}/', $r['uri']);
                })
                ->values();


            return view('admin.menus.create', compact('menus', 'routes'));
        } catch (\Exception $e) {
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
            return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully!');
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
            $menus = Menu::whereNull('parent_id')
                ->where('id', '!=', $menu->id)
                ->get();
            // Fetch all named routes
            $routes = collect(Route::getRoutes())
                ->map(function ($route) {
                    return [
                        'name' => $route->getName(),
                        'uri'  => $route->uri(),
                    ];
                })
                ->filter(function ($r) {
                    // Sirf wo routes jinke naam hain
                    if (!$r['name']) {
                        return false;
                    }

                    // Parameterized routes hatao (jinme {something} hai)
                    return !preg_match('/\{.*?\}/', $r['uri']);
                })
                ->values();

            $routes_old = collect(Route::getRoutes())->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'uri' => $route->uri(),
                ];
            })->filter(fn($r) => $r['name']);

            return view('admin.menus.edit', compact('menu', 'menus', 'routes'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.menus.index')->with('error', 'Menu not found.');
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
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            $validated['status'] = $request->has('status') ? 1 : 0;
            $menu->update($validated);
            return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
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
            $menu->delete();
            return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
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
}
