<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GalleryCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view gallery categories'); // Add permission check
            $categories = GalleryCategory::latest()->paginate(15);
            return view('admin.gallery.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error("Error fetching gallery categories: " . $e->getMessage());
            return back()->with('error', 'Failed to load gallery categories.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('create gallery categories'); // Add permission check
            return view('admin.gallery.categories.create');
        } catch (\Exception $e) {
            Log::error("Error opening create gallery category form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create category form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create gallery categories'); // Add permission check

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:gallery_categories,slug',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
            GalleryCategory::create($validated);

            return redirect()->route('admin.gallery-categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating gallery category: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create gallery category.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryCategory $galleryCategory)
    {
        try {
            $this->authorize('edit gallery categories'); // Add permission check
            return view('admin.gallery.categories.edit', ['category' => $galleryCategory]);
        } catch (\Exception $e) {
            Log::error("Error opening edit gallery category form: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit category form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GalleryCategory $galleryCategory)
    {
        try {
            $this->authorize('edit gallery categories'); // Add permission check

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:gallery_categories,slug,' . $galleryCategory->id,
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $galleryCategory->update($validated);

            return redirect()->route('admin.gallery-categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating gallery category: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update gallery category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryCategory $galleryCategory)
    {
        try {
            $this->authorize('delete gallery categories'); // Add permission check
            $galleryCategory->delete();

            return back()->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting gallery category: " . $e->getMessage());
            return back()->with('error', 'Failed to delete gallery category.');
        }
    }
}
