<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\GalleryCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryImageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view gallery images'); // Add permission check
            // Fetch all categories for the filter tabs
            $categories = GalleryCategory::orderBy('name')->get(['id', 'name']);

            // Fetch images with category relationship
            $images = GalleryImage::with('category')
                ->latest()
                ->paginate(24);

            return view('admin.gallery.images.index', compact('images', 'categories'));
        } catch (\Exception $e) {
            Log::error("Error fetching gallery images: " . $e->getMessage());
            return back()->with('error', 'Failed to load gallery images.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('upload gallery images'); // Add permission check
            $categories = GalleryCategory::orderBy('name')->pluck('name', 'id');
            return view('admin.gallery.images.create', compact('categories'));
        } catch (\Exception $e) {
            Log::error("Error opening create gallery image form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create gallery image form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('upload gallery images'); // Add permission check

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
        } catch (\Exception $e) {
            Log::error("Error creating gallery image: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to upload image.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryImage $galleryImage)
    {
        try {
            $this->authorize('edit gallery images'); // Add permission check
            $categories = GalleryCategory::orderBy('name')->pluck('name', 'id');
            return view('admin.gallery.images.edit', [
                'image' => $galleryImage,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error("Error opening edit gallery image form: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit image form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GalleryImage $galleryImage)
    {
        try {
            $this->authorize('edit gallery images'); // Add permission check

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
        } catch (\Exception $e) {
            Log::error("Error updating gallery image: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update image.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryImage $galleryImage)
    {
        try {
            $this->authorize('delete gallery images'); // Add permission check
            $galleryImage->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting gallery image: " . $e->getMessage());
            return back()->with('error', 'Failed to delete image.');
        }
    }
}
