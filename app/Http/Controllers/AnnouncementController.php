<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view announcements');
            $announcements = Announcement::latest()->paginate(15);
            return view('admin.announcements.index', compact('announcements'));
        } catch (\Exception $e) {
            Log::error("Error fetching announcements: " . $e->getMessage());
            return back()->with('error', 'Failed to load announcements.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('create announcements');
            return view('admin.announcements.create');
        } catch (\Exception $e) {
            Log::error("Error opening create announcement form: " . $e->getMessage());
            return back()->with('error', 'Failed to open create announcement form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create announcements');

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:student,faculty',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            // Set the status to true if not provided
            $validated['status'] = (bool)($validated['status'] ?? true);

            Announcement::create($validated);
            return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully');
        } catch (\Exception $e) {
            Log::error("Error creating announcement: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create announcement.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        // Redirecting to index as the show method is not required
        return redirect()->route('admin.announcements.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        try {
            $this->authorize('edit announcements');
            return view('admin.announcements.edit', compact('announcement'));
        } catch (\Exception $e) {
            Log::error("Error opening edit announcement form: " . $e->getMessage());
            return back()->with('error', 'Failed to open edit announcement form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        try {
            $this->authorize('edit announcements');

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:student,faculty',
                'status' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            $validated['status'] = (bool)($validated['status'] ?? $announcement->status);
            $announcement->update($validated);

            return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully');
        } catch (\Exception $e) {
            Log::error("Error updating announcement: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update announcement.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        try {
            $this->authorize('delete announcements');
            $announcement->delete();
            return back()->with('success', 'Announcement deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error deleting announcement: " . $e->getMessage());
            return back()->with('error', 'Failed to delete announcement.');
        }
    }

    /**
     * Publish the specified resource.
     */
    public function publish(Announcement $announcement)
    {
        try {
            $this->authorize('publish announcements');

            $announcement->update(['status' => true]);
            return back()->with('success', 'Announcement published successfully');
        } catch (\Exception $e) {
            Log::error("Error publishing announcement: " . $e->getMessage());
            return back()->with('error', 'Failed to publish announcement.');
        }
    }
}
