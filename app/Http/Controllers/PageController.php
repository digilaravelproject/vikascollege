<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Cache facade
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    /**
     * Show a page based on the given slug.
     */
    public function show($slug)
    {
        try {
            // 1. Poore page view ko 24 ghante ke liye cache karein
            $viewData = Cache::remember('page:view:' . $slug, 86400, function () use ($slug) {

                // 1️⃣ Fetch active page
                $activeSection = Page::where('slug', $slug)
                    ->where('status', true)
                    ->firstOrFail(); // Agar nahi mila toh ModelNotFoundException throw karega

                // 2️⃣ Find related menu (Eager load 'parent' for optimization)
                $activeMenu = Menu::with('parent') // ⭐️ N+1 problem se bachne ke liye
                    ->where('status', true)
                    ->whereHas('page', function ($q) use ($activeSection) {
                        $q->where('id', $activeSection->id);
                    })
                    ->first();

                if (!$activeMenu) {
                    $activeMenu = Menu::with('parent') // ⭐️ Yahaan bhi
                        ->where('url', $slug)
                        ->where('status', true)
                        ->first();
                }

                // 3️⃣ Prepare sidebar menus
                $menus = collect();
                $topParent = null;

                if ($activeMenu) {
                    // getTopParent() function ab internally cached hai
                    $topParent = $this->getTopParent($activeMenu);

                    // 🚀🚀 NAYA OPTIMIZATION: Nested Caching 🚀🚀
                    // Ab hum sirf menu tree ko alag se cache karenge.
                    $menus = Cache::remember('menu:sidebar:' . $topParent->id, 86400, function () use ($topParent) {
                        Log::info("Caching sidebar menu for: " . $topParent->id); // Log karein jab cache bane
                        return Menu::with(['childrenRecursive.page']) // Recursive load
                            ->where('id', $topParent->id)
                            ->where('status', true)
                            ->get();
                    });
                }

                // 4️⃣ Decode JSON content
                $blocks = [];
                if (!empty($activeSection->content)) {
                    $decoded = json_decode($activeSection->content, true);
                    $blocks = $decoded['blocks'] ?? $decoded ?? [];
                }

                // 5️⃣ Saara data ek array mein return karein taaki cache ho sake
                return compact('activeSection', 'menus', 'activeMenu', 'topParent', 'blocks');
            });

            // 6️⃣ Cached data se view render karein
            return view('frontend.pages.show', $viewData);
        } catch (ModelNotFoundException $e) {
            Log::error("Page or Menu not found for slug: {$slug}", ['exception' => $e->getMessage()]);
            abort(404, 'Page not found.');
        } catch (\Throwable $e) {
            Log::error("Error in PageController@show for slug: {$slug}", ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(500, 'Something went wrong. Please try again later.');
        }
    }


    /**
     * Get the top-most parent menu (Yeh pehle se hi cached hai, perfect).
     */
    private function getTopParent(Menu $menu)
    {
        // Har menu ke top parent ko 24 ghante ke liye cache karein
        return Cache::remember('menu:top_parent:' . $menu->id, 86400, function () use ($menu) {
            try {
                if ($menu->parent_id) {
                    // find() use karein taaki agar parent delete ho gaya ho toh error na aaye
                    $parent = Menu::find($menu->parent_id);
                    if ($parent) {
                        // Recursive call bhi ab cache use karegi
                        return $this->getTopParent($parent);
                    }
                }
                // Agar parent_id nahi hai, ya parent nahi mila, toh yehi top parent hai
                return $menu;
            } catch (\Throwable $e) {
                Log::error("Error retrieving top parent for menu ID: {$menu->id}", ['exception' => $e->getMessage()]);
                return $menu; // Error hone par current menu return karein
            }
        });
    }
}
