<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page)
    {
        // 'page:view:SLUG' waala cache clear karein
        Cache::forget('page:view:' . $page->slug);
    }

    public function deleted(Page $page)
    {
        Cache::forget('page:view:' . $page->slug);
    }
}
