<?php

namespace App\Http\Controllers;

use App\Models\EventItem;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = EventItem::with('category')->latest()->paginate(15);
        return view('admin.events.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = EventCategory::orderBy('name')->pluck('name', 'id');
        return view('admin.events.items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:event_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:event_items,slug',
            'image' => 'nullable|image|max:4096',
            'event_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'full_content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/events', 'public');
        }
        EventItem::create($validated);
        return redirect()->route('admin.event-items.index')->with('success', 'Event created');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventItem $eventItem)
    {
        return redirect()->route('admin.event-items.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventItem $eventItem)
    {
        $categories = EventCategory::orderBy('name')->pluck('name', 'id');
        return view('admin.events.items.edit', ['item' => $eventItem, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventItem $eventItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:event_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:event_items,slug,' . $eventItem->id,
            'image' => 'nullable|image|max:4096',
            'event_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'full_content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/events', 'public');
        }
        $eventItem->update($validated);
        return redirect()->route('admin.event-items.index')->with('success', 'Event updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventItem $eventItem)
    {
        $eventItem->delete();
        return back()->with('success', 'Event deleted');
    }
}
