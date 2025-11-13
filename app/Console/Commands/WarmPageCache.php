<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page; // Page model ko import karein
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // ⭐️ HTTP client import karein

class WarmPageCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warm-pages'; // Yeh command ka naam hai

    /**
     * The console command description.
     */
    protected $description = 'Warms the cache for all active pages by visiting them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to warm page cache...');

        // 1. Sirf active pages jinka slug hai, unhe hi fetch karein
        $pages = Page::where('status', true)->whereNotNull('slug')->get();

        if ($pages->isEmpty()) {
            $this->warn('No active pages with slugs found. Nothing to warm.');
            return 0;
        }

        $this->info("Found " . $pages->count() . " pages to warm.");

        $successCount = 0;
        $failCount = 0;

        foreach ($pages as $page) {
            try {
                // 'route()' helper use karein. Yeh 'frontend.page.show' route aapke web.php mein hona chahiye
                $url = route('frontend.page.show', ['slug' => $page->slug]);

                // Hum page ko "visit" karne ke liye HTTP client ka use karenge
                $response = Http::get($url); // ⭐️ Make sure APP_URL .env mein sahi set hai

                if ($response->successful()) {
                    $this->info("SUCCESS: Warmed cache for: " . $url);
                    $successCount++;
                } else {
                    $this->error("FAILED (HTTP Status: {$response->status()}): " . $url);
                    Log::error("Cache Warm Failed for {$page->slug}", ['status' => $response->status()]);
                    $failCount++;
                }
            } catch (\Exception $e) {
                // Yeh error tab aa sakta hai agar route define nahi hai ya APP_URL galat hai
                $this->error("EXCEPTION: Failed to warm cache for slug: {$page->slug}.");
                Log::error("Cache Warm Exception for {$page->slug}", ['error' => $e->getMessage()]);
                $failCount++;
            }
        }

        $this->info('-----------------------------');
        $this->info('Page cache warming complete!');
        $this->info("Successful: $successCount | Failed: $failCount");

        return 0;
    }
}
