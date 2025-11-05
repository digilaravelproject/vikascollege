<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = GalleryCategory::latest()->paginate(15);
        return view('admin.gallery.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gallery.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:gallery_categories,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        GalleryCategory::create($validated);
        return redirect()->route('admin.gallery-categories.index')->with('success', 'Category created');
    }

    /**
     * Display the specified resource.
     */
    public function show(GalleryCategory $galleryCategory)
    {
        return redirect()->route('admin.gallery-categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryCategory $galleryCategory)
    {
        return view('admin.gallery.categories.edit', ['category' => $galleryCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GalleryCategory $galleryCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:gallery_categories,slug,' . $galleryCategory->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $galleryCategory->update($validated);
        return redirect()->route('admin.gallery-categories.index')->with('success', 'Category updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryCategory $galleryCategory)
    {
        $galleryCategory->delete();
        return back()->with('success', 'Category deleted');
    }
}
