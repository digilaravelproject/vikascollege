<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;

class GalleryImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all categories for the filter tabs
        $categories = GalleryCategory::orderBy('name')->get(['id', 'name']);

        // Fetch images with category relationship
        $images = GalleryImage::with('category')
            ->latest()
            ->paginate(24);

        return view('admin.gallery.images.index', compact('images', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = GalleryCategory::orderBy('name')->pluck('name', 'id');
        return view('admin.gallery.images.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:gallery_categories,id',
            'image' => 'required|image|max:8192',
            'title' => 'nullable|string|max:255',
        ]);

        $validated['image'] = $request->file('image')->store('uploads/gallery', 'public');

        GalleryImage::create($validated);

        return redirect()
            ->route('admin.gallery-images.index')
            ->with('success', 'Image added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryImage $galleryImage)
    {
        $categories = GalleryCategory::orderBy('name')->pluck('name', 'id');
        return view('admin.gallery.images.edit', [
            'image' => $galleryImage,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GalleryImage $galleryImage)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:gallery_categories,id',
            'image' => 'nullable|image|max:8192',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/gallery', 'public');
        }

        $galleryImage->update($validated);

        return redirect()
            ->route('admin.gallery-images.index')
            ->with('success', 'Image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryImage $galleryImage)
    {
        $galleryImage->delete();

        return back()->with('success', 'Image deleted successfully.');
    }
}
