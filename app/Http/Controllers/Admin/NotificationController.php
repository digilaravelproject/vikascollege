<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    public function index()
    {
        $notifications = Notification::query()->orderByDesc('created_at')->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Return a JSON list of active, featured notifications
     * for the homepage builder.
     */
    public function listActiveFeatured()
    {
        $notifications = Notification::where('status', 1)
            ->where('featured', 1)
            ->where('feature_on_top', 0)
            ->orderByDesc('display_date')
            ->get();

        return response()->json($notifications);
    }

    public function create()
    {
        $icons = ['🎓', '🏆', '🎭', '📚', '🔔', '📅'];
        return view('admin.notifications.create', compact('icons'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'icon' => ['nullable', 'string', 'max:16'],
                'title' => ['required', 'string', 'max:255'],
                'href' => ['nullable', 'string', 'max:1024'],
                'button_name' => ['nullable', 'string', 'max:64'],
                'status' => ['nullable', 'boolean'],
                'featured' => ['nullable', 'boolean'],
                'feature_on_top' => ['nullable', 'boolean'],
                'display_date' => ['nullable', 'date'],
            ]);

            if (empty($data['icon'])) {
                $data['icon'] = $this->service->getDefaultIcon();
            }
            $data['button_name'] = $data['button_name'] ?: 'Click Here';

            // Corrected logic: Use the value from the request if it exists, otherwise default.
            $data['status'] = $request->has('status') ? (bool)$data['status'] : true;
            $data['featured'] = $request->has('featured') ? (bool)$data['featured'] : false;
            $data['feature_on_top'] = $request->has('feature_on_top') ? (bool)$data['feature_on_top'] : false;

            $notification = Notification::create($data);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'notification' => $notification]);
            }

            return redirect()->route('admin.notifications.index')->with('success', 'Notification created');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
    }

    public function edit(Notification $notification)
    {
        $icons = ['🎓', '🏆', '🎭', '📚', '🔔', '📅'];
        return view('admin.notifications.edit', compact('notification', 'icons'));
    }

    public function update(Request $request, Notification $notification)
    {
        try {
            $data = $request->validate([
                'icon' => ['nullable', 'string', 'max:16'],
                'title' => ['required', 'string', 'max:255'],
                'href' => ['nullable', 'string', 'max:1024'],
                'button_name' => ['nullable', 'string', 'max:64'],
                'status' => ['nullable', 'boolean'],
                'featured' => ['nullable', 'boolean'],
                'feature_on_top' => ['nullable', 'boolean'],
                'display_date' => ['nullable', 'date'],
            ]);

            if (empty($data['icon'])) {
                $data['icon'] = $notification->icon ?: $this->service->getDefaultIcon();
            }
            $data['button_name'] = $data['button_name'] ?: 'Click Here';

            // Corrected logic: Use the value from the request if it exists, otherwise default to false.
            $data['status'] = $request->has('status') ? (bool)$data['status'] : false;
            $data['featured'] = $request->has('featured') ? (bool)$data['featured'] : false;
            $data['feature_on_top'] = $request->has('feature_on_top') ? (bool)$data['feature_on_top'] : false;

            $notification->update($data);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'notification' => $notification]);
            }

            return redirect()->route('admin.notifications.index')->with('success', 'Notification updated');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted');
    }

    // === ❇️ START OF FIX ❇️ ===
    // These methods now toggle the value and return a JSON response.

    public function toggleStatus(Notification $notification)
    {
        $notification->status = !$notification->status;
        $notification->save();

        return response()->json(['success' => true, 'new_status' => $notification->status]);
    }

    public function toggleFeatured(Notification $notification)
    {
        $notification->featured = !$notification->featured;
        $notification->save();

        return response()->json(['success' => true, 'new_status' => $notification->featured]);
    }

    public function toggleFeatureOnTop(Notification $notification)
    {
        $notification->feature_on_top = !$notification->feature_on_top;
        $notification->save();

        return response()->json(['success' => true, 'new_status' => $notification->feature_on_top]);
    }
    // === ❇️ END OF FIX ❇️ ===
}
