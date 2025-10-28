<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrustSection;
use App\Models\TrustSectionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrustSectionController extends Controller
{
    public function index()
    {
        $sections = TrustSection::orderBy('id')->get();
        return view('admin.trust.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.trust.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trust_sections,slug',
            'content' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $trustSection = TrustSection::create([
                'title' => $request->title,
                'slug' => Str::slug($request->slug),
                'content' => $request->content,
            ]);

            // Handle PDF
            if ($request->hasFile('pdf')) {
                $trustSection->update([
                    'pdf_path' => $request->file('pdf')->store('trust/pdfs', 'public')
                ]);
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('trust/images', 'public');
                    TrustSectionImage::create([
                        'trust_section_id' => $trustSection->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()->route('admin.trust.index')->with('success', 'Section created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(TrustSection $trustSection)
    {
        return view('admin.trust.edit', compact('trustSection'));
    }
    public function update(Request $request, TrustSection $trustSection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trust_sections,slug,' . $trustSection->id,
            'content' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            // Clean BOM/Invisible chars from content
            $content = $request->input('content');

            $trustSection->update([
                'title' => $request->title,
                'slug' => Str::slug($request->slug),
                'content' => $content,
            ]);

            // Handle PDF
            if ($request->hasFile('pdf')) {
                if ($trustSection->pdf_path) {
                    Storage::disk('public')->delete($trustSection->pdf_path);
                }
                $trustSection->update([
                    'pdf_path' => $request->file('pdf')->store('trust/pdfs', 'public')
                ]);
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('trust/images', 'public');
                    TrustSectionImage::create([
                        'trust_section_id' => $trustSection->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()->route('admin.trust.index')->with('success', 'Section updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function update_old(Request $request, TrustSection $trustSection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trust_sections,slug,' . $trustSection->id,
            'content' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $trustSection->update([
                'title' => $request->title,
                'slug' => Str::slug($request->slug),
                'content' => $request->content,
            ]);

            // Handle PDF
            if ($request->hasFile('pdf')) {
                if ($trustSection->pdf_path) {
                    Storage::disk('public')->delete($trustSection->pdf_path);
                }
                $trustSection->update([
                    'pdf_path' => $request->file('pdf')->store('trust/pdfs', 'public')
                ]);
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('trust/images', 'public');
                    TrustSectionImage::create([
                        'trust_section_id' => $trustSection->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()->route('admin.trust.index')->with('success', 'Section updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroyImage(TrustSectionImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }

    public function removePdf(TrustSection $trustSection)
    {
        if ($trustSection->pdf_path) {
            Storage::disk('public')->delete($trustSection->pdf_path);
            $trustSection->update(['pdf_path' => null]);
        }
        return back()->with('success', 'PDF removed successfully!');
    }
}
