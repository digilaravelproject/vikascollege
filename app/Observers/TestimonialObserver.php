<?php

namespace App\Observers;

use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class TestimonialObserver
{
    public function saved(Testimonial $model)
    {
        Cache::flush();
    }
    public function deleted(Testimonial $model)
    {
        Cache::flush();
    }
}
