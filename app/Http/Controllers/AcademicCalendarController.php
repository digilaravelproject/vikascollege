<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AcademicCalendarController extends Controller
{
    use AuthorizesRequests;


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check

            $items = AcademicCalendar::latest('event_datetime')->paginate(15);
            return view('admin.academic_calendar.index', compact('items'));
        } catch (\Exception $e) {
            Log::error("Error fetching academic calendar items: " . $e->getMessage());
            return back()->with('error', 'Failed to load academic calendar items.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check
            return view('admin.academic_calendar.create');
        } catch (\Exception $e) {
            Log::error("Error opening create academic calendar form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:academic_calendars,slug',
                'event_datetime' => 'required|date',
                'image' => 'nullable|image|max:4096',
                'description' => 'nullable|string',
                'link_href' => 'nullable|url|max:255',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('uploads/academic', 'public');
            }
            $validated['status'] = (bool)($validated['status'] ?? true);

            AcademicCalendar::create($validated);

            return redirect()->route('admin.academic-calendar.index')->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating academic calendar item: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create academic calendar item.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicCalendar $academicCalendar)
    {
        return redirect()->route('admin.academic-calendar.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicCalendar $academicCalendar)
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check
            return view('admin.academic_calendar.edit', ['item' => $academicCalendar]);
        } catch (\Exception $e) {
            Log::error("Error opening edit form for academic calendar item: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicCalendar $academicCalendar)
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:academic_calendars,slug,' . $academicCalendar->id,
                'event_datetime' => 'required|date',
                'image' => 'nullable|image|max:4096',
                'description' => 'nullable|string',
                'link_href' => 'nullable|url|max:255',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('uploads/academic', 'public');
            }

            $validated['status'] = (bool)($validated['status'] ?? $academicCalendar->status);

            $academicCalendar->update($validated);

            return redirect()->route('admin.academic-calendar.index')->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating academic calendar item: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update academic calendar item.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicCalendar $academicCalendar)
    {
        try {
            $this->authorize('Manage academic calendar'); // Permission check
            $academicCalendar->delete();

            return back()->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting academic calendar item: " . $e->getMessage());
            return back()->with('error', 'Failed to delete academic calendar item.');
        }
    }
}
