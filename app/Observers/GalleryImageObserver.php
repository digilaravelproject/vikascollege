<?php

namespace App\Observers;

use App\Models\GalleryImage;
use Illuminate\Support\Facades\Cache;

class GalleryImageObserver
{
    public function saved(GalleryImage $model)
    {
        Cache::flush();
    }
    public function deleted(GalleryImage $model)
    {
        Cache::flush();
    }
}
