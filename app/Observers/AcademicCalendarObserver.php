<?php

namespace App\Observers;

use App\Models\AcademicCalendar;
use Illuminate\Support\Facades\Cache;

class AcademicCalendarObserver
{
    public function saved(AcademicCalendar $model)
    {
        Cache::flush();
    }
    public function deleted(AcademicCalendar $model)
    {
        Cache::flush();
    }
}
