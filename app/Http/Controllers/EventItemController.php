<?php

namespace App\Http\Controllers;

use App\Models\EventItem;
use App\Models\EventCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EventItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view events');
            $items = EventItem::with('category')->latest()->paginate(15);
            return view('admin.events.items.index', compact('items'));
        } catch (\Exception $e) {
            Log::error("Error fetching event items: " . $e->getMessage());
            return back()->with('error', 'Failed to load event items.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('create events');
            $categories = EventCategory::orderBy('name')->pluck('name', 'id');
            return view('admin.events.items.create', compact('categories'));
        } catch (\Exception $e) {
            Log::error("Error opening create event form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create event form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create events');

            $validated = $request->validate([
                'category_id' => 'required|exists:event_categories,id',
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:event_items,slug',
                'image' => 'nullable|image|max:4096',
                'event_date' => 'required|date',
                'venue' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'full_content' => 'nullable|string',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
            $validated['status'] = $request->has('status'); // checkbox toggle

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('uploads/events', 'public');
            }

            EventItem::create($validated);

            return redirect()->route('admin.event-items.index')->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating event item: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create event.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventItem $eventItem)
    {
        try {
            $this->authorize('edit events');
            $categories = EventCategory::orderBy('name')->pluck('name', 'id');
            return view('admin.events.items.edit', ['item' => $eventItem, 'categories' => $categories]);
        } catch (\Exception $e) {
            Log::error("Error opening edit event form: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit event form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventItem $eventItem)
    {
        try {
            $this->authorize('edit events');

            $validated = $request->validate([
                'category_id' => 'required|exists:event_categories,id',
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:event_items,slug,' . $eventItem->id,
                'image' => 'nullable|image|max:4096',
                'event_date' => 'required|date',
                'venue' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'full_content' => 'nullable|string',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['status'] = $request->has('status');

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('uploads/events', 'public');
            }

            $eventItem->update($validated);

            return redirect()->route('admin.event-items.index')->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating event item: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update event.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventItem $eventItem)
    {
        try {
            $this->authorize('delete events');
            $eventItem->delete();
            return back()->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting event item: " . $e->getMessage());
            return back()->with('error', 'Failed to delete event.');
        }
    }
}
