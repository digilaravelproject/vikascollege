<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $service)
    {
    }

    public function index()
    {
        $notifications = Notification::query()->orderByDesc('created_at')->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $icons = ['🎓','🏆','🎭','📚','🔔','📅'];
        return view('admin.notifications.create', compact('icons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => ['nullable','string','max:16'],
            'title' => ['required','string','max:255'],
            'href' => ['nullable','string','max:1024'],
            'button_name' => ['nullable','string','max:64'],
            'status' => ['nullable','boolean'],
            'featured' => ['nullable','boolean'],
            'feature_on_top' => ['nullable','boolean'],
            'display_date' => ['nullable','date'],
        ]);

        if (empty($data['icon'])) {
            $data['icon'] = $this->service->getDefaultIcon();
        }
        $data['button_name'] = $data['button_name'] ?: 'Click Here';
        $data['status'] = (bool)($data['status'] ?? true);
        $data['featured'] = (bool)($data['featured'] ?? false);
        $data['feature_on_top'] = (bool)($data['feature_on_top'] ?? false);

        Notification::create($data);
        return redirect()->route('admin.notifications.index')->with('success', 'Notification created');
    }

    public function edit(Notification $notification)
    {
        $icons = ['🎓','🏆','🎭','📚','🔔','📅'];
        return view('admin.notifications.edit', compact('notification','icons'));
    }

    public function update(Request $request, Notification $notification)
    {
        $data = $request->validate([
            'icon' => ['nullable','string','max:16'],
            'title' => ['required','string','max:255'],
            'href' => ['nullable','string','max:1024'],
            'button_name' => ['nullable','string','max:64'],
            'status' => ['nullable','boolean'],
            'featured' => ['nullable','boolean'],
            'feature_on_top' => ['nullable','boolean'],
            'display_date' => ['nullable','date'],
        ]);

        if (empty($data['icon'])) {
            $data['icon'] = $notification->icon ?: $this->service->getDefaultIcon();
        }
        $data['button_name'] = $data['button_name'] ?: 'Click Here';
        $data['status'] = (bool)($data['status'] ?? false);
        $data['featured'] = (bool)($data['featured'] ?? false);
        $data['feature_on_top'] = (bool)($data['feature_on_top'] ?? false);

        $notification->update($data);
        return redirect()->route('admin.notifications.index')->with('success', 'Notification updated');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted');
    }

    public function toggleStatus(Notification $notification)
    {
        $notification->status = !$notification->status;
        $notification->save();
        return response()->json(['ok' => true, 'status' => $notification->status]);
    }

    public function toggleFeatured(Notification $notification)
    {
        $notification->featured = !$notification->featured;
        $notification->save();
        return response()->json(['ok' => true, 'featured' => $notification->featured]);
    }

    public function toggleFeatureOnTop(Notification $notification)
    {
        $notification->feature_on_top = !$notification->feature_on_top;
        $notification->save();
        return response()->json(['ok' => true, 'feature_on_top' => $notification->feature_on_top]);
    }
}


