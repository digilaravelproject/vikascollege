<?php

namespace App\Observers;

use App\Models\WhyChooseUs;
use Illuminate\Support\Facades\Cache;

class WhyChooseUsObserver
{
    public function saved(WhyChooseUs $model)
    {
        Cache::flush();
    }
    public function deleted(WhyChooseUs $model)
    {
        Cache::flush();
    }
}
