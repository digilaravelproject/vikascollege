<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    /**
     * Show a page based on the given slug.
     */
    public function show($slug)
    {
        try {
            // 1️⃣ Fetch active page by slug (status must be active)
            $activeSection = Page::where('slug', $slug)
                ->where('status', true) // Ensure the page is active
                ->firstOrFail();

            // 2️⃣ Find related menu (if any)
            $activeMenu = Menu::whereHas('page', function ($q) use ($activeSection) {
                $q->where('id', $activeSection->id);
            })->first();

            if (!$activeMenu) {
                $activeMenu = Menu::where('url', $slug)->where('status', true)->first(); // Ensure the menu is active
            }

            // 3️⃣ Prepare sidebar menus
            $menus = collect();
            $topParent = null;

            if ($activeMenu) {
                $topParent = $this->getTopParent($activeMenu);
                $menus = Menu::with(['childrenRecursive.page'])
                    ->where('id', $topParent->id)
                    ->get();
            }

            // 4️⃣ Decode JSON content safely
            $blocks = [];
            if (!empty($activeSection->content)) {
                $decoded = json_decode($activeSection->content, true);
                $blocks = $decoded['blocks'] ?? $decoded ?? [];
            }

            // 5️⃣ Render view
            return view('frontend.pages.show', compact('activeSection', 'menus', 'activeMenu', 'topParent', 'blocks'));
        } catch (ModelNotFoundException $e) {
            // If page or menu not found, log and show 404 error
            Log::error("Page or Menu not found for slug: {$slug}", ['exception' => $e->getMessage()]);
            abort(404, 'Page not found.');
        } catch (\Throwable $e) {
            // Log any other errors with more context
            Log::error("Error in PageController@show for slug: {$slug}", ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(500, 'Something went wrong. Please try again later.');
        }
    }


    /**
     * Get the top-most parent menu.
     */
    private function getTopParent(Menu $menu)
    {
        try {
            if ($menu->parent_id) {
                return $this->getTopParent(Menu::find($menu->parent_id));
            }
            return $menu;
        } catch (\Throwable $e) {
            Log::error("Error retrieving top parent for menu ID: {$menu->id}", ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $menu; // Return the current menu if something goes wrong
        }
    }
}
