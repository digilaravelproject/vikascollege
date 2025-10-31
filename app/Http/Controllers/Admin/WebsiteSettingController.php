<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Import DB facade
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\Video\ResizeFilter;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        $data = [
            'college_name' => Setting::get('college_name'),
            'banner_heading' => Setting::get('banner_heading'),
            'banner_subheading' => Setting::get('banner_subheading'),
            'banner_button_text' => Setting::get('banner_button_text'),
            'banner_button_link' => Setting::get('banner_button_link'),
            'college_logo' => Setting::get('college_logo'),
            'favicon' => Setting::get('favicon'),
            'banner_media' => $this->getBannerMedia(),
        ];

        return view('admin.settings.website', compact('data'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:255',
            'banner_heading' => 'nullable|string|max:255',
            'banner_subheading' => 'nullable|string|max:255',
            'banner_button_text' => 'nullable|string|max:100',
            'banner_button_link' => 'nullable|url',
            'college_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:1024',
            'banner_media' => 'nullable|array', // Validate as array
            'banner_media.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:51200', // 50MB
        ]);

        DB::beginTransaction();
        try {
            // Save general settings
            foreach ($validated as $key => $value) {
                if (!in_array($key, ['college_logo', 'favicon', 'banner_media'])) {
                    Setting::set($key, $value);
                }
            }

            // Upload logo
            if ($request->hasFile('college_logo')) {
                // Delete old logo if it exists
                if ($oldLogo = Setting::get('college_logo')) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('college_logo')->store('logos', 'public');
                Setting::set('college_logo', $path);
            }

            // Upload favicon
            if ($request->hasFile('favicon')) {
                // Delete old favicon if it exists
                if ($oldFavicon = Setting::get('favicon')) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                $path = $request->file('favicon')->store('favicons', 'public');
                Setting::set('favicon', $path);
            }

            // Upload banner media
            if ($request->hasFile('banner_media')) {
                $this->handleBannerMedia($request->file('banner_media'));
            }

            DB::commit();
            return back()->with('success', 'Website settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update settings: " . $e->getMessage());
            return back()->with('error', 'Failed to update settings. Please check logs. ' . $e->getMessage());
        }
    }

    /**
     * Handle multiple banner files (images/videos)
     * This will DELETE all old media first.
     */
    private function handleBannerMedia(array $files)
    {
        // 1. Delete all old banner media
        $oldMedia = Setting::where('key', 'like', 'banner_media_%')->get();
        foreach ($oldMedia as $item) {
            try {
                $media = json_decode($item->value, true);
                if (isset($media['path'])) {
                    Storage::disk('public')->delete($media['path']);
                }
                $item->delete();
            } catch (\Exception $e) {
                Log::error("Failed to delete old banner media: " . $item->key . " - " . $e->getMessage());
            }
        }

        // 2. Upload new media
        foreach ($files as $index => $file) {
            try {
                $mime = $file->getMimeType();
                $key = "banner_media_{$index}";

                if (str_starts_with($mime, 'image/')) {
                    $this->compressImage($file, $key);
                } elseif (str_starts_with($mime, 'video/')) {
                    $this->compressVideo($file, $key);
                }
            } catch (\Exception $e) {
                Log::error("Banner media #{$index} failed to process: " . $e->getMessage());
            }
        }
    }

    /**
     * Compress and save image
     */
    private function compressImage($file, $key)
    {
        $path = 'banners/' . uniqid('img_') . '.webp';

        // Use Storage::disk('public') to get the full path for optimization
        $fullPath = Storage::disk('public')->path($path);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory(dirname($path));

        // Save original as WebP
        file_put_contents($fullPath, file_get_contents($file->getRealPath()));

        // Optimize
        try {
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($fullPath);
        } catch (\Exception $e) {
            Log::warning("Could not optimize image {$path}. Using unoptimized version. Error: " . $e->getMessage());
        }

        Setting::set($key, json_encode([
            'type' => 'image',
            'path' => $path, // Save the relative public path
        ]));

        Log::info("Image banner saved: {$path}");
    }

    /**
     * Compress and save video
     */
    private function compressVideo($file, $key)
    {
        // Store temp file in private 'storage/app/temp'
        $tempPath = $file->store('temp');
        $fullTempPath = Storage::path($tempPath);

        $filename = 'video_' . uniqid() . '.mp4';
        // Final relative path in 'storage/app/public/banners'
        $finalRelativePath = 'banners/' . $filename;
        // Final absolute path for FFMpeg to write to
        $fullCompressedPath = Storage::disk('public')->path($finalRelativePath);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory(dirname($finalRelativePath));

        try {
            // Detect OS for FFMpeg binaries (Update these paths if necessary)
            $ffmpegPath = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? 'C:\\ffmpeg\\bin\\ffmpeg.exe'
                : '/usr/bin/ffmpeg'; // Common Linux path

            $ffprobePath = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? 'C:\\ffmpeg\\bin\\ffprobe.exe'
                : '/usr/bin/ffprobe'; // Common Linux path

            // Check if binaries exist
            if (!file_exists($ffmpegPath) || !file_exists($ffprobePath)) {
                throw new \Exception("FFMpeg binaries not found at: $ffmpegPath, $ffprobePath");
            }

            // Create FFMpeg instance
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => $ffprobePath,
                'timeout'          => 3600,
                'ffmpeg.threads'   => 4,
            ]);

            /** @var \FFMpeg\Media\Video $video */
            $video = $ffmpeg->open($fullTempPath);

            // Resize to 720p (compress resolution)
            $video->filters()->resize(
                new Dimension(1280, 720),
                ResizeFilter::RESIZEMODE_FIT,
                true
            );

            $format = new X264('aac', 'libx264');
            $format->setKiloBitrate(1500);

            // Save compressed video to public disk
            $video->save($format, $fullCompressedPath);

            // Store final setting (public relative path)
            Setting::set($key, json_encode([
                'type' => 'video',
                'path' => $finalRelativePath, // Save 'banners/video_xxx.mp4'
                'original_name' => $file->getClientOriginalName(),
            ]));

            Log::info("✅ Video compressed and saved: {$finalRelativePath}");
        } catch (\Exception $e) {
            Log::error("❌ FFMpeg compression failed: " . $e->getMessage());
            // Re-throw exception to be caught by handleBannerMedia
            throw $e;
        } finally {
            // ALWAYS delete temporary file
            if (Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }
        }
    }

    /**
     * Get all banner media from database
     */
    private function getBannerMedia()
    {
        // MODIFIED: Return both key and value
        $media = [];
        // This query is more efficient
        $settings = Setting::where('key', 'like', 'banner_media_%')->orderBy('key')->get();

        foreach ($settings as $item) {
            $media[] = (object)[
                'key' => $item->key,
                'value' => $item->value
            ];
        }
        return $media;
    }

    /**
     * ADDED: Delete a specific banner media item
     */
    public function deleteBannerMedia(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|starts_with:banner_media_'
        ]);

        DB::beginTransaction();
        try {
            $key = $validated['key'];
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $media = json_decode($setting->value, true);

                // Delete file from public storage
                if (isset($media['path'])) {
                    Storage::disk('public')->delete($media['path']);
                }

                // Delete setting from database
                $setting->delete();

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Media deleted successfully.']);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Media not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete media {$request->key}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error, could not delete media.'], 500);
        }
    }
}
