<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EventCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view event categories');
            $categories = EventCategory::latest()->paginate(15);
            return view('admin.events.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error("Error fetching event categories: " . $e->getMessage());
            return back()->with('error', 'Failed to load event categories.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('create event categories');
            return view('admin.events.categories.create');
        } catch (\Exception $e) {
            Log::error("Error opening create category form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create category form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create event categories');

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:event_categories,slug',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
            EventCategory::create($validated);

            return redirect()->route('admin.event-categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating event category: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create category.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventCategory $eventCategory)
    {
        try {
            $this->authorize('edit event categories');
            return view('admin.events.categories.edit', ['category' => $eventCategory]);
        } catch (\Exception $e) {
            Log::error("Error opening edit category form: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit category form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        try {
            $this->authorize('edit event categories');

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:event_categories,slug,' . $eventCategory->id,
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $eventCategory->update($validated);

            return redirect()->route('admin.event-categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating event category: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventCategory $eventCategory)
    {
        try {
            $this->authorize('delete event categories');
            $eventCategory->delete();
            return back()->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting event category: " . $e->getMessage());
            return back()->with('error', 'Failed to delete category.');
        }
    }
}
