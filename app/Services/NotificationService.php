<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Return a random default icon when none selected.
     * (Bug fix: Use double quotes "" for unicode emojis)
     */
    public function getDefaultIcon(): string
    {
        $icons = ["\u{1F393}", "\u{1F3C6}", "\u{1F3AD}", "\u{1F4DA}", "\u{1F514}", "\u{1F4C5}"];
        return $icons[array_rand($icons)];
    }

    /**
     * NEW: Gets ONLY the "Top" notifications for the marquee.
     * (status = true AND feature_on_top = true)
     */
    public function getMarqueeNotifications(): Collection
    {
        return Notification::query()
            ->where('status', true)
            ->where('feature_on_top', true)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * NEW: Gets ONLY the "Rest" active notifications.
     * (status = true AND (feature_on_top = false OR null))
     */
    public function getRestNotifications(): Collection
    {
        return Notification::query()
            ->where('status', true)
            ->where('featured', true)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * OPTIONAL: (Aapka original function)
     * Gets ALL active notifications, with "Top" items first.
     * Hum ise DRY rakhenge aur upar wale functions ko call karenge.
     */
    public function getAllActiveSorted(): Collection
    {
        $top = $this->getMarqueeNotifications();
        $rest = $this->getRestNotifications();

        // Collection return karega, array ke liye ->all() istemaal karein
        return $top->concat($rest);
    }
}
