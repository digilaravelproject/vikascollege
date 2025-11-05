<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = AcademicCalendar::latest('event_datetime')->paginate(15);
        return view('admin.academic_calendar.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic_calendar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return redirect()->route('admin.academic-calendar.index')->with('success', 'Item created');
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
        return view('admin.academic_calendar.edit', ['item' => $academicCalendar]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicCalendar $academicCalendar)
    {
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
        return redirect()->route('admin.academic-calendar.index')->with('success', 'Item updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicCalendar $academicCalendar)
    {
        $academicCalendar->delete();
        return back()->with('success', 'Item deleted');
    }
}
