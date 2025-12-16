<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class NotificationObserver
{
    /**
     * Handle the Notification "saved" (created or updated) event.
     */
    public function saved(Notification $notification): void
    {
        $this->clearCaches();
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        $this->clearCaches();
    }

    /**
     * Dono notification caches ko clear karein.
     */
    private function clearCaches(): void
    {
        Cache::forget('notifications:marquee');
        Cache::forget('notifications:rest');
    }
}
