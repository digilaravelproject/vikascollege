<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageSetupController extends Controller
{
    /**
     * Show the homepage setup page.
     */
    public function index()
    {
        $layout = Setting::get('homepage_layout') ?: '{"blocks":[]}';
        $notifications = (new NotificationService())->getRestNotifications();
        $icons = ['🎓', '🏆', '🎭', '📚', '🔔', '📅'];

        return view('admin.homepage.setup', compact('layout', 'notifications', 'icons'));
    }

    /**
     * Save the homepage layout.
     */
    public function save(Request $request)
    {

        $validated = $request->validate([
            'blocks' => 'required|array', // 'blocks' ek array hona chahiye
        ]);

        try {
            $jsonContentToSave = json_encode(['blocks' => $validated['blocks']]);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to encode JSON: ' . json_last_error_msg());
            }


            Setting::set('homepage_layout', $jsonContentToSave);

            // Success response dein
            return response()->json([
                'success' => true,
                'message' => 'Homepage layout saved successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Homepage layout save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
