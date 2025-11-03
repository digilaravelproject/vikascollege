<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Return a random default icon when none selected.
     */
    public function getDefaultIcon(): string
    {
        $icons = ['\u{1F393}', '\u{1F3C6}', '\u{1F3AD}', '\u{1F4DA}', '\u{1F514}', '\u{1F4C5}'];
        return $icons[array_rand($icons)];
    }

    /**
     * Prepare marquee items ordered with feature_on_top first and latest first.
     */
    public function getActiveForMarquee(): array
    {
        $top = Notification::query()
            ->where('status', true)
            ->where('feature_on_top', true)
            ->orderByDesc('created_at')
            ->get();

        $rest = Notification::query()
            ->where('status', true)
            ->where(function ($q) {
                $q->where('feature_on_top', false)->orWhereNull('feature_on_top');
            })
            ->orderByDesc('created_at')
            ->get();

        return $top->concat($rest)->all();
    }
}


