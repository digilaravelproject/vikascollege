<?php

namespace App\Observers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Cache;

class AnnouncementObserver
{
    public function saved(Announcement $model)
    {
        Cache::flush();
    }
    public function deleted(Announcement $model)
    {
        Cache::flush();
    }
}
