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

    // -------------------
    // Helper: Convert image to WebP
    // -------------------
    private function convertToWebp($file, $path)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $imagePath = $file->getRealPath();

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($imagePath);
                break;
            case 'png':
                $image = imagecreatefrompng($imagePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = imagecreatefromgif($imagePath);
                break;
            default:
                return null;
        }

        $webpName = uniqid() . '.webp';
        $fullPath = storage_path("app/public/$path/" . $webpName);

        imagewebp($image, $fullPath, 80); // 80% quality
        imagedestroy($image);

        return "$path/$webpName";
    }

    // -------------------
    // Index
    // -------------------
    public function index()
    {
        try {
            $this->authorize('view gallery images');
            $categories = GalleryCategory::orderBy('name')->get(['id', 'name']);

            $images = GalleryImage::with('category')
                ->latest()
                ->paginate(24);

            return view('admin.gallery.images.index', compact('images', 'categories'));
        } catch (\Exception $e) {
            Log::error("Error fetching gallery images: " . $e->getMessage());
            return back()->with('error', 'Failed to load gallery images.');
        }
    }

    // -------------------
    // Create
    // -------------------
    public function create()
    {
        try {
            $this->authorize('upload gallery images');
            $categories = GalleryCategory::orderBy('name')->pluck('name', 'id');
            return view('admin.gallery.images.create', compact('categories'));
        } catch (\Exception $e) {
            Log::error("Error opening create gallery image form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create gallery image form.');
        }
    }

    // -------------------
    // Store
    // -------------------
    public function store(Request $request)
    {
        try {
            $this->authorize('upload gallery images');

            $validated = $request->validate([
                'category_id' => 'required|exists:gallery_categories,id',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif,svg,bmp,tiff|max:15360', // 15MB
                'title' => 'nullable|string|max:255',
            ]);

            // Convert to WebP
            $webpPath = $this->convertToWebp($request->file('image'), 'uploads/gallery');
            if (!$webpPath) {
                return back()->with('error', 'Unsupported image format.');
            }
            $validated['image'] = $webpPath;

            GalleryImage::create($validated);

            return redirect()
                ->route('admin.gallery-images.index')
                ->with('success', 'Image added successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating gallery image: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to upload image.');
        }
    }

    // -------------------
    // Edit
    // -------------------
    public function edit(GalleryImage $galleryImage)
    {
        try {
            $this->authorize('edit gallery images');
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

    // -------------------
    // Update
    // -------------------
    public function update(Request $request, GalleryImage $galleryImage)
    {
        try {
            $this->authorize('edit gallery images');

            $validated = $request->validate([
                'category_id' => 'required|exists:gallery_categories,id',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg,bmp,tiff|max:15360', // 15MB
                'title' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('image')) {
                // Delete old image
                if ($galleryImage->image && file_exists(storage_path('app/public/' . $galleryImage->image))) {
                    unlink(storage_path('app/public/' . $galleryImage->image));
                }

                // Convert new image to WebP
                $webpPath = $this->convertToWebp($request->file('image'), 'uploads/gallery');
                if (!$webpPath) {
                    return back()->with('error', 'Unsupported image format.');
                }

                $validated['image'] = $webpPath;
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

    // -------------------
    // Destroy
    // -------------------
    public function destroy(GalleryImage $galleryImage)
    {
        try {
            $this->authorize('delete gallery images');

            // Delete image file
            if ($galleryImage->image && file_exists(storage_path('app/public/' . $galleryImage->image))) {
                unlink(storage_path('app/public/' . $galleryImage->image));
            }

            $galleryImage->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting gallery image: " . $e->getMessage());
            return back()->with('error', 'Failed to delete image.');
        }
    }
}
