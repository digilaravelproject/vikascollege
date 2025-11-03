<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageSetupController extends Controller
{
    public function index()
    {
        $layout = Setting::get('homepage_layout') ?: '{"blocks":[]}';
        $notifications = (Setting::get('homepage_notifications')) ?: '[]';
        return view('admin.homepage.setup', compact('layout', 'notifications'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|json',
            'notifications' => 'nullable|json',
        ]);

        try {
            Setting::set('homepage_layout', $validated['content']);
            if (isset($validated['notifications'])) {
                Setting::set('homepage_notifications', $validated['notifications']);
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Homepage layout save failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Save failed'], 500);
        }
    }
}
