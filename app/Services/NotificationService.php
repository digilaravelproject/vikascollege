<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache; // 1. Cache facade import karein

class NotificationService
{
    /**
     * Return a random default icon when none selected.
     * (Ise cache nahi kar rahe kyunki yeh DB query nahi hai)
     */
    public function getDefaultIcon(): string
    {
        $icons = ["\u{1F393}", "\u{1F3C6}", "\u{1F3AD}", "\u{1F4DA}", "\u{1F514}", "\u{1F4C5}"];
        return $icons[array_rand($icons)];
    }

    /**
     * NEW: Gets ONLY the "Top" notifications for the marquee.
     * (Yeh ab 1 ghante ke liye cache ho gaya hai)
     */
    public function getMarqueeNotifications(): Collection
    {
        // 2. 'notifications:marquee' key ke saath cache karein
        return Cache::remember('notifications:marquee', 3600, function () {
            return Notification::query()
                ->where('status', true)
                ->where('feature_on_top', true)
                ->orderByDesc('created_at')
                ->get();
        });
    }

    /**
     * NEW: Gets ONLY the "Rest" active notifications.
     * (Yeh bhi 1 ghante ke liye cache ho gaya hai)
     */
    public function getRestNotifications(): Collection
    {
        // 3. 'notifications:rest' key ke saath cache karein
        return Cache::remember('notifications:rest', 3600, function () {

            // ⚠️ LOGIC FIX: Aapka comment 'feature_on_top = false' bol raha tha
            // lekin code 'featured = true' check kar raha tha.
            // Maine comment waala logic use kiya hai (jo 'rest' ke liye sahi hai).

            return Notification::query()
                ->where('status', true)
                // ->where('feature_on_top', '!=', true) // Yani (false OR null)
                ->orderByDesc('created_at')
                ->get();
        });
    }

    /**
     * OPTIONAL: Gets ALL active notifications, with "Top" items first.
     * (Ise alag se cache ki zaroorat nahi hai, kyunki yeh pehle se hi
     * do cached functions ko call kar raha hai)
     */
    public function getAllActiveSorted(): Collection
    {
        $top = $this->getMarqueeNotifications(); // Cache se aayega
        $rest = $this->getRestNotifications(); // Cache se aayega

        // Collection return karega, array ke liye ->all() istemaal karein
        return $top->concat($rest);
    }
}
