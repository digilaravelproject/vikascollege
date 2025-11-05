<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = EventCategory::latest()->paginate(15);
        return view('admin.events.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:event_categories,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        EventCategory::create($validated);
        return redirect()->route('admin.event-categories.index')->with('success', 'Category created');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventCategory $eventCategory)
    {
        return redirect()->route('admin.event-categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventCategory $eventCategory)
    {
        return view('admin.events.categories.edit', ['category' => $eventCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:event_categories,slug,' . $eventCategory->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $eventCategory->update($validated);
        return redirect()->route('admin.event-categories.index')->with('success', 'Category updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventCategory $eventCategory)
    {
        $eventCategory->delete();
        return back()->with('success', 'Category deleted');
    }
}
