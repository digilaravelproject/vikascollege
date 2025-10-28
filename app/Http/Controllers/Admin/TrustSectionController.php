<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrustSection;
use App\Models\TrustSectionImage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class TrustSectionController extends Controller
{
    /**
     * Display a listing of all trust sections.
     *
     * @return View
     */
    public function index(): View
    {
        $sections = TrustSection::orderBy('id')->get();
        return view('admin.trust.index', compact('sections'));
    }

    /**
     * Show the form for creating a new trust section.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.trust.create');
    }

    /**
     * Store a newly created trust section in the database.
     *
     * @param Request $request  The incoming HTTP request containing form input and uploaded files.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trust_sections,slug',
            'content' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $trustSection = TrustSection::create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
                'content' => $validated['content'] ?? null,
            ]);

            // Handle PDF upload
            if ($request->hasFile('pdf')) {
                $pdfPath = $request->file('pdf')->store('trust/pdfs', 'public');
                $trustSection->update(['pdf_path' => $pdfPath]);
            }

            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('trust/images', 'public');
                    TrustSectionImage::create([
                        'trust_section_id' => $trustSection->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()
                ->route('admin.trust.index')
                ->with('success', 'Section created successfully.');
        } catch (Throwable $e) {
            Log::error('Error creating Trust Section: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'An unexpected error occurred while creating the section.');
        }
    }

    /**
     * Show the form for editing an existing trust section.
     *
     * @param TrustSection $trustSection  The trust section instance to edit.
     * @return View
     */
    public function edit(TrustSection $trustSection): View
    {
        return view('admin.trust.edit', compact('trustSection'));
    }

    /**
     * Update an existing trust section in storage.
     *
     * @param Request $request       The incoming HTTP request containing updated input and uploaded files.
     * @param TrustSection $trustSection  The trust section instance being updated.
     * @return RedirectResponse
     */
    public function update(Request $request, TrustSection $trustSection): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trust_sections,slug,' . $trustSection->id,
            'content' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $trustSection->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
                'content' => $validated['content'] ?? null,
            ]);

            // Handle PDF replacement
            if ($request->hasFile('pdf')) {
                if ($trustSection->pdf_path && Storage::disk('public')->exists($trustSection->pdf_path)) {
                    Storage::disk('public')->delete($trustSection->pdf_path);
                }
                $pdfPath = $request->file('pdf')->store('trust/pdfs', 'public');
                $trustSection->update(['pdf_path' => $pdfPath]);
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('trust/images', 'public');
                    TrustSectionImage::create([
                        'trust_section_id' => $trustSection->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()
                ->route('admin.trust.index')
                ->with('success', 'Section updated successfully.');
        } catch (Throwable $e) {
            Log::error('Error updating Trust Section: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'An unexpected error occurred while updating the section.');
        }
    }

    /**
     * Delete a specific image associated with a trust section.
     *
     * @param TrustSectionImage $image  The image instance to delete.
     * @return RedirectResponse
     */
    public function destroyImage(TrustSectionImage $image): RedirectResponse
    {
        try {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Error deleting Trust Section image: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete image.');
        }
    }

    /**
     * Remove the attached PDF file from a trust section.
     *
     * @param TrustSection $trustSection  The trust section instance whose PDF will be removed.
     * @return RedirectResponse
     */
    public function removePdf(TrustSection $trustSection): RedirectResponse
    {
        try {
            if ($trustSection->pdf_path && Storage::disk('public')->exists($trustSection->pdf_path)) {
                Storage::disk('public')->delete($trustSection->pdf_path);
                $trustSection->update(['pdf_path' => null]);
            }

            return back()->with('success', 'PDF removed successfully.');
        } catch (Throwable $e) {
            Log::error('Error removing Trust Section PDF: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to remove PDF.');
        }
    }
}
