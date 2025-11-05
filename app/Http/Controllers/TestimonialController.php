<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonial::latest()->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.testimonials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_image' => 'nullable|image|max:4096',
            'testimonial_text' => 'required|string|max:1000',
            'status' => 'nullable|boolean',
        ]);
        if ($request->hasFile('student_image')) {
            $validated['student_image'] = $request->file('student_image')->store('uploads/testimonials', 'public');
        }
        $validated['status'] = (bool)($validated['status'] ?? false);
        Testimonial::create($validated);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', ['testimonial' => $testimonial]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_image' => 'nullable|image|max:4096',
            'testimonial_text' => 'required|string|max:1000',
            'status' => 'nullable|boolean',
        ]);
        if ($request->hasFile('student_image')) {
            $validated['student_image'] = $request->file('student_image')->store('uploads/testimonials', 'public');
        }
        $validated['status'] = (bool)($validated['status'] ?? $testimonial->status);
        $testimonial->update($validated);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted');
    }
}
