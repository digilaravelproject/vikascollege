<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'banner_media.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:51200', // 50MB
        ]);

        // Save general settings
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['college_logo', 'favicon', 'banner_media'])) {
                Setting::set($key, $value);
            }
        }

        // Upload logo
        if ($request->hasFile('college_logo')) {
            $path = $request->file('college_logo')->store('logos', 'public');
            Setting::set('college_logo', $path);
        }

        // Upload favicon
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicons', 'public');
            Setting::set('favicon', $path);
        }

        // Upload banner media
        if ($request->hasFile('banner_media')) {
            $this->handleBannerMedia($request->file('banner_media'));
        }

        return back()->with('success', 'Website settings updated successfully!');
    }

    /**
     * Handle multiple banner files (images/videos)
     */
    private function handleBannerMedia(array $files)
    {
        foreach ($files as $index => $file) {
            try {
                $mime = $file->getMimeType();

                if (str_starts_with($mime, 'image/')) {
                    $this->compressImage($file, $index);
                } elseif (str_starts_with($mime, 'video/')) {
                    $this->compressVideo($file, $index);
                }
            } catch (\Exception $e) {
                Log::error("Banner media #{$index} failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Compress and save image
     */
    private function compressImage($file, $index)
    {
        $path = 'banners/img_' . uniqid() . '.webp';
        $fullPath = Storage::path('public/' . $path);

        // Save original as WebP
        file_put_contents($fullPath, file_get_contents($file->getRealPath()));

        // Optimize
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($fullPath);

        Setting::set("banner_media_{$index}", json_encode([
            'type' => 'image',
            'path' => $path,
        ]));

        Log::info("Image banner saved: {$path}");
    }

    /**
     * Compress and save video
     */
    private function compressVideo($file, $index)
    {
        // Generate unique filename
        $filename = 'video_' . uniqid() . '.mp4';

        // Save uploaded file temporarily in storage/app/public/temp
        $tempPath = $file->storeAs('public/temp', uniqid() . '.' . $file->getClientOriginalExtension());
        $fullTempPath = Storage::path($tempPath);

        // Final compressed path: storage/app/public/banners/video_xxx.mp4
        $compressedPath = 'public/banners/' . $filename;
        $fullCompressedPath = Storage::path($compressedPath);

        // Detect OS for FFMpeg binaries
        $ffmpegPath = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? 'C:\\ffmpeg\\bin\\ffmpeg.exe'
            : '/usr/bin/ffmpeg';

        $ffprobePath = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? 'C:\\ffmpeg\\bin\\ffprobe.exe'
            : '/usr/bin/ffprobe';

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

        // Use H.264 codec (MP4 format) and reduce bitrate
        $format = new X264('aac', 'libx264');
        $format->setKiloBitrate(1500); // target bitrate ~1.5Mbps (compressed)

        // Save compressed video
        $video->save($format, $fullCompressedPath);

        // Delete temporary file
        Storage::delete($tempPath);

        // Store final setting (public path)
        Setting::set("banner_media_{$index}", json_encode([
            'type' => 'video',
            'path' => str_replace('public/', '', $compressedPath), // remove 'public/' for URL
            'original_name' => $file->getClientOriginalName(),
            'compressed_name' => $filename,
        ]));

        Log::info("✅ Video compressed and saved: {$compressedPath}");
    }


    /**
     * Get all banner media from database
     */
    private function getBannerMedia()
    {
        $media = [];
        for ($i = 0; $i < 10; $i++) { // Adjust max media count if needed
            $item = Setting::get("banner_media_{$i}");
            if ($item) {
                $media[] = (object)['value' => $item];
            }
        }
        return $media;
    }
}
