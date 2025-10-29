<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pagebuilder.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pagebuilder.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:4096',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/pages', 'public');
        }

        // Handle pdf upload
        if ($request->hasFile('pdf')) {
            $validated['pdf'] = $request->file('pdf')->store('uploads/pdfs', 'public');
        }

        Page::create($validated);

        return redirect()->route('admin.pagebuilder.index')->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        return view('admin.pagebuilder.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/pages', 'public');
        }

        if ($request->hasFile('pdf')) {
            $validated['pdf'] = $request->file('pdf')->store('uploads/pdfs', 'public');
        }

        $page->update($validated);

        return redirect()->route('admin.pagebuilder.index')->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'Page deleted successfully!');
    }
    public function builder(Page $page)
    {
        // Builder editor page
        return view('admin.pagebuilder.builder', compact('page'));
    }

    public function saveBuilder(Request $request, Page $page)
    {
        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        $page->update([
            'content' => $validated['content'],
        ]);

        return response()->json(['success' => true]);
    }
}
