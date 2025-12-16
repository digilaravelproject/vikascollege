<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{
    /**
     * Cache management page dikhayein.
     */
    public function index()
    {
        // Bas view return karein jahaan buttons honge
        return view('admin.cache.index');
    }

    /**
     * Sabhi application caches ko clear karein.
     */
    public function clearAllCache()
    {
        try {
            // Yeh data, routes, config, aur views sab clear kar dega
            Artisan::call('optimize:clear');

            Log::info('Admin user cleared all application caches.');

            // --- CHANGED ---
            // Redirect ke bajaaye JSON response return karein
            return response()->json([
                'status' => 'success',
                'message' => 'Application cache has been cleared successfully!'
            ]);
            // --- END CHANGE ---

        } catch (\Throwable $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());

            // --- CHANGED ---
            // Error JSON response return karein
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache. Please check logs.'
            ], 500); // 500 HTTP status code bhejें
            // --- END CHANGE ---
        }
    }

    /**
     * Application ko speed ke liye optimize karein.
     */
    public function reOptimizeApp()
    {
        try {
            // Config aur Routes ko cache karein
            Artisan::call('optimize');

            // Views ko cache karein
            Artisan::call('view:cache');

            Log::info('Admin user re-optimized the application.');

            // --- CHANGED ---
            // Success JSON response return karein
            return response()->json([
                'status' => 'success',
                'message' => 'Application has been re-optimized successfully!'
            ]);
            // --- END CHANGE ---

        } catch (\Throwable $e) {
            Log::error('Failed to optimize app: ' . $e->getMessage());

            // --- CHANGED ---
            // Error JSON response return karein
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to optimize application. Please check logs.'
            ], 500);
            // --- END CHANGE ---
        }
    }
}
