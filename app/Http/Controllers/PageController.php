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
            // 1. Fetch the page
            $activeSection = Page::where('slug', $slug)->firstOrFail();

            // 2. Find menu linked to this page
            $activeMenu = Menu::whereHas('page', function ($q) use ($activeSection) {
                $q->where('id', $activeSection->id);
            })->first();

            // 3. If no menu found, try direct URL match
            if (!$activeMenu) {
                $activeMenu = Menu::where('url', $slug)->first();
            }

            $menus = collect();

            if ($activeMenu) {
                $topParent = $this->getTopParent($activeMenu);

                // Load top parent with all children + their pages recursively
                $menus = Menu::with(['childrenRecursive.page'])
                    ->where('id', $topParent->id)
                    ->get();
            } else {
                $topParent = null;
            }

            return view('frontend.pages.show', compact('activeSection', 'menus', 'activeMenu', 'topParent'));
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
