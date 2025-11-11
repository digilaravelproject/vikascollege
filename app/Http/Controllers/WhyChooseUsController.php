<?php

namespace App\Http\Controllers;

use App\Models\WhyChooseUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhyChooseUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = WhyChooseUs::orderBy('sort_order')->paginate(20);
        return view('admin.why_choose_us.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.why_choose_us.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'icon_or_image' => 'nullable|image|max:4096',
                'sort_order' => 'nullable|integer',
            ]);

            // Limit description to 250 characters
            $validated['description'] = substr($validated['description'], 0, 250);

            // Handle file upload
            if ($request->hasFile('icon_or_image')) {
                $validated['icon_or_image'] = $request->file('icon_or_image')->store('uploads/why', 'public');
            }

            $validated['sort_order'] = $validated['sort_order'] ?? 0;

            WhyChooseUs::create($validated);

            return redirect()
                ->route('admin.why-choose-us.index')
                ->with('success', 'Item created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong while creating the item. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WhyChooseUs $whyChooseUs)
    {
        return redirect()->route('admin.why-choose-us.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhyChooseUs $why_choose_u)
    {
        return view('admin.why_choose_us.edit', ['item' => $why_choose_u]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $whyChooseUs = WhyChooseUs::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'icon_or_image' => 'nullable|image|max:4096',
                'sort_order' => 'nullable|integer',
            ]);

            // Limit description to 250 characters
            $validated['description'] = substr($validated['description'], 0, 250);

            // Handle file upload
            if ($request->hasFile('icon_or_image')) {
                // Delete old file if it exists
                if ($whyChooseUs->icon_or_image && Storage::disk('public')->exists($whyChooseUs->icon_or_image)) {
                    Storage::disk('public')->delete($whyChooseUs->icon_or_image);
                }

                // Upload new file
                $validated['icon_or_image'] = $request->file('icon_or_image')->store('uploads/why', 'public');
            } else {
                // Keep existing file
                $validated['icon_or_image'] = $whyChooseUs->icon_or_image;
            }

            // Preserve existing sort order if not provided
            $validated['sort_order'] = $validated['sort_order'] ?? $whyChooseUs->sort_order;

            $whyChooseUs->update($validated);

            return redirect()
                ->route('admin.why-choose-us.index')
                ->with('success', 'Item updated successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->with('error', 'The record you are trying to update was not found.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'An unexpected error occurred while updating. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhyChooseUs $whyChooseUs)
    {
        try {
            // Delete associated image if exists
            if ($whyChooseUs->icon_or_image && Storage::disk('public')->exists($whyChooseUs->icon_or_image)) {
                Storage::disk('public')->delete($whyChooseUs->icon_or_image);
            }

            $whyChooseUs->delete();

            return redirect()
                ->back()
                ->with('success', 'Item deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->with('error', 'The item you are trying to delete was not found.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong while deleting the item. Please try again later.');
        }
    }
}
