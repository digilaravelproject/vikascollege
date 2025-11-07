<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SiteManagementController extends Controller
{
    /**
     * Site Management dashboard dikhayein.
     */
    public function index()
    {
        // View ko zaroori data pass karein
        $data = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'is_maintenance' => app()->isDownForMaintenance(),
        ];
        return view('admin.cache.index', $data);
    }

    /**
     * Sabhi application caches ko clear karein.
     */
    public function clearAllCache()
    {
        try {
            // 'optimize:clear' command chalayein
            Artisan::call('optimize:clear');
            Log::info('Admin user cleared all application caches.');

            // Success JSON response bhein
            return response()->json([
                'status' => 'success',
                'message' => 'Application cache has been cleared successfully!'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache. Please check logs.'
            ], 500);
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
            // Aapki request: permission cache reset
            Artisan::call('permission:cache-reset');

            Log::info('Admin user re-optimized the application with permission reset.');

            // Success JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Application has been re-optimized successfully!'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to optimize app: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to optimize application. Please check logs.'
            ], 500);
        }
    }

    /**
     * Maintenance mode ko toggle (on/off) karein.
     */
    public function toggleMaintenance(Request $request)
    {
        try {
            // Request se 'action' lein (up ya down)
            $action = $request->input('action', 'down');

            if ($action === 'down') {
                Artisan::call('down');
                $message = 'Site is now in maintenance mode.';
            } else {
                Artisan::call('up');
                $message = 'Site is now live.';
            }

            $this->runOptimizeClear(); // Har action ke baad cache clear karein
            Log::info("Admin user set maintenance mode to: $action");

            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Throwable $e) {
            Log::error('Failed to toggle maintenance mode: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to change maintenance mode. Please check logs.'
            ], 500);
        }
    }

    /**
     * APP_ENV ko .env file mein set karein (local/production).
     */
    public function setAppEnv(Request $request)
    {
        try {
            $mode = $request->input('mode', 'local'); // 'local' ya 'production'

            if (!in_array($mode, ['local', 'production'])) {
                return response()->json(['status' => 'error', 'message' => 'Invalid mode specified.'], 400);
            }

            $this->setEnvVariable('APP_ENV', $mode);
            $this->runOptimizeClear(); // Har action ke baad cache clear karein

            return response()->json([
                'status' => 'success',
                'message' => "App environment set to '{$mode}'. Page will reload."
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to set APP_ENV: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update .env file. Check file permissions.'
            ], 500);
        }
    }

    /**
     * APP_DEBUG ko .env file mein set karein (true/false).
     */
    public function toggleDebug(Request $request)
    {
        try {
            $debug = $request->input('debug', 'false'); // 'true' ya 'false'

            $this->setEnvVariable('APP_DEBUG', $debug);
            $this->runOptimizeClear(); // Har action ke baad cache clear karein

            return response()->json([
                'status' => 'success',
                'message' => "App debug mode set to '{$debug}'. Page will reload."
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to set APP_DEBUG: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update .env file. Check file permissions.'
            ], 500);
        }
    }

    // --- Private Helper Functions ---

    /**
     * .env file mein variable set karne ke liye helper function.
     */
    private function setEnvVariable(string $key, string $value)
    {
        $path = app()->environmentFilePath();

        // Value ko format karein (spaces ho toh quotes lagayein)
        $escapedValue = $value;
        if (str_contains($value, ' ') || str_contains($value, '#')) {
            $escapedValue = '"' . $value . '"';
        }

        // boolean ko string 'true'/'false' mein badlein
        if (is_bool($value)) {
            $escapedValue = $value ? 'true' : 'false';
        }

        $content = file_get_contents($path);
        $keyExists = (strpos($content, $key . '=') !== false);

        if ($keyExists) {
            // Key hai, replace karein
            $content = preg_replace(
                "/^{$key}=.*/m", // /m = multiline mode
                "{$key}={$escapedValue}",
                $content
            );
        } else {
            // Key nahi hai, end mein add karein
            $content .= "\n{$key}={$escapedValue}\n";
        }

        file_put_contents($path, $content);
    }

    /**
     * Har action ke baad 'optimize:clear' chalaane ke liye helper.
     */
    private function runOptimizeClear()
    {
        try {
            Artisan::call('optimize:clear');
            Log::info('Post-action optimize:clear executed successfully.');
        } catch (\Throwable $e) {
            Log::error('Post-action optimize:clear failed: ' . $e->getMessage());
        }
    }
}
