<?php

namespace App\Observers;

use App\Models\EventItem;
use Illuminate\Support\Facades\Cache;

class EventItemObserver
{
    public function saved(EventItem $model)
    {
        Cache::flush();
    }
    public function deleted(EventItem $model)
    {
        Cache::flush();
    }
}
