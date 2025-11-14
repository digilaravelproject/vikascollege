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
     * Normalize link value
     */
    private function normalizeLink(?string $link): ?string
    {
        if (!$link) {
            return null;
        }

        // Trim spaces
        $link = trim($link);

        // Fix internal spaces → %20
        $link = str_replace(' ', '%20', $link);

        // If not full URL or /storage, convert to storage path
        if (!Str::startsWith($link, ['http://', 'https://', '/storage'])) {
            $link = '/storage/' . ltrim($link, '/');
        }

        return $link;
    }

    public function index()
    {
        $this->authorize('view announcements');

        $announcements = Announcement::latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->authorize('create announcements');
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create announcements');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:student,faculty',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'link' => 'nullable|string|max:500',
        ]);

        try {
            // Normalize link
            $validated['link'] = $this->normalizeLink($validated['link'] ?? null);

            $validated['status'] = (bool)($validated['status'] ?? true);

            Announcement::create($validated);

            return redirect()
                ->route('admin.announcements.index')
                ->with('success', 'Announcement created successfully');
        } catch (\Exception $e) {
            Log::error("Announcement Store Error: " . $e->getMessage(), ['data' => $validated]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating announcement.');
        }
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('edit announcements');
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('edit announcements');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:student,faculty',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'link' => 'nullable|string|max:500',
        ]);

        try {
            // Normalize link
            $validated['link'] = $this->normalizeLink($validated['link'] ?? null);

            $validated['status'] = (bool)($validated['status'] ?? $announcement->status);
            log::info('', ['data' => $validated]);
            $announcement->update($validated);

            return redirect()
                ->route('admin.announcements.index')
                ->with('success', 'Announcement updated successfully');
        } catch (\Exception $e) {
            Log::error("Announcement Update Error: " . $e->getMessage(), ['data' => $validated]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating announcement.');
        }
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete announcements');

        try {
            $announcement->delete();

            return back()->with('success', 'Announcement deleted successfully');
        } catch (\Exception $e) {
            Log::error("Announcement Delete Error: " . $e->getMessage());

            return back()->with('error', 'Failed to delete announcement.');
        }
    }

    public function publish(Announcement $announcement)
    {
        $this->authorize('publish announcements');

        try {
            $announcement->update(['status' => true]);

            return back()->with('success', 'Announcement published successfully');
        } catch (\Exception $e) {
            Log::error("Announcement Publish Error: " . $e->getMessage());

            return back()->with('error', 'Failed to publish announcement.');
        }
    }
}
