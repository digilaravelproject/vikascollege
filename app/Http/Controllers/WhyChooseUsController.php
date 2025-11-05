<?php

namespace App\Http\Controllers;

use App\Models\WhyChooseUs;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_or_image' => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);
        if ($request->hasFile('icon_or_image')) {
            $validated['icon_or_image'] = $request->file('icon_or_image')->store('uploads/why', 'public');
        }
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        WhyChooseUs::create($validated);
        return redirect()->route('admin.why-choose-us.index')->with('success', 'Item created');
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
    public function edit(WhyChooseUs $whyChooseUs)
    {
        return view('admin.why_choose_us.edit', ['item' => $whyChooseUs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhyChooseUs $whyChooseUs)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_or_image' => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);
        if ($request->hasFile('icon_or_image')) {
            $validated['icon_or_image'] = $request->file('icon_or_image')->store('uploads/why', 'public');
        }
        $validated['sort_order'] = $validated['sort_order'] ?? $whyChooseUs->sort_order;
        $whyChooseUs->update($validated);
        return redirect()->route('admin.why-choose-us.index')->with('success', 'Item updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhyChooseUs $whyChooseUs)
    {
        $whyChooseUs->delete();
        return back()->with('success', 'Item deleted');
    }
}
