<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification; // <-- 1. IMPORT NOTIFICATION MODEL
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageSetupController extends Controller
{
    /**
     * Show the homepage setup page.
     * We load the layout and all active/featured notifications.
     */
    public function index()
    {
        $layout = Setting::get('homepage_layout') ?: '{"blocks":[]}';

        // 2. FETCH NOTIFICATIONS FOR THE BUILDER
        $notifications = Notification::where('status', 1)
            ->where('featured', 1)
            ->where('feature_on_top', 0)
            ->orderByDesc('display_date')
            ->get();

        // 3. GET ICONS (same as your NotificationController)
        $icons = ['🎓', '🏆', '🎭', '📚', '🔔', '📅'];

        // 4. PASS EVERYTHING TO THE VIEW
        return view('admin.homepage.setup', compact('layout', 'notifications', 'icons'));
    }

    /**
     * Save the homepage layout.
     */
    public function save(Request $request)
    {
        // This controller's save function remains unchanged
        // as the notifications are not saved in the layout JSON.
        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        try {
            Setting::set('homepage_layout', $validated['content']);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Homepage layout save failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Save failed'], 500);
        }
    }
}
