<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function show($slug)
    {
        try {
            // 1️⃣ Fetch page by slug
            $activeSection = Page::where('slug', $slug)->firstOrFail();

            // 2️⃣ Find related menu (if any)
            $activeMenu = Menu::whereHas('page', function ($q) use ($activeSection) {
                $q->where('id', $activeSection->id);
            })->first();

            if (!$activeMenu) {
                $activeMenu = Menu::where('url', $slug)->first();
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
        } catch (\Throwable $e) {
            Log::error("PageController@show: " . $e->getMessage());
            abort(404, 'Page not found.');
        }
    }

    private function getTopParent(Menu $menu)
    {
        if ($menu->parent_id) {
            return $this->getTopParent(Menu::find($menu->parent_id));
        }
        return $menu;
    }
}
