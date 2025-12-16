<?php

namespace App\Observers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    // Menu create, update, ya delete hone par...
    public function saved(Menu $menu)
    {
        $this->clearAllCaches();
    }

    public function deleted(Menu $menu)
    {
        $this->clearAllCaches();
    }

    // Sabhi relevant caches ko clear karein
    private function clearAllCaches()
    {
        // Sabse aasaan tareeka hai poora cache clear kar dena
        // Yeh 'page:view:*' aur 'menu:top_parent:*' dono ko clear kar dega
        Cache::flush();

        // Note: Agar aap Redis use kar rahe hain, toh 'tags' use karna behtar hai,
        // lekin file-based cache ke liye flush() sabse reliable hai.
    }
}
