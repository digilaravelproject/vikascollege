<?php

namespace App\Providers;

// Base Models
use App\Models\Page;
use App\Models\Menu;
use App\Models\Notification;

// HomePageBlock Models (Yeh sabhi add karein)
use App\Models\Announcement;
use App\Models\EventItem;
use App\Models\AcademicCalendar;
use App\Models\GalleryImage;
use App\Models\Testimonial;
use App\Models\WhyChooseUs;

// Base Observers
use App\Observers\PageObserver;
use App\Observers\MenuObserver;
use App\Observers\NotificationObserver;

// HomePageBlock Observers (Yeh sabhi add karein)
use App\Observers\AnnouncementObserver;
use App\Observers\EventItemObserver;
use App\Observers\AcademicCalendarObserver;
use App\Observers\GalleryImageObserver;
use App\Observers\TestimonialObserver;
use App\Observers\WhyChooseUsObserver;

use Illuminate\Support\ServiceProvider; // Aapka base class

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Aapke puraane observers
        Page::observe(PageObserver::class);
        Menu::observe(MenuObserver::class);
        Notification::observe(NotificationObserver::class);

        // Homepage waale 6 naye observers
        Announcement::observe(AnnouncementObserver::class);
        EventItem::observe(EventItemObserver::class);
        AcademicCalendar::observe(AcademicCalendarObserver::class);
        GalleryImage::observe(GalleryImageObserver::class);
        Testimonial::observe(TestimonialObserver::class);
        WhyChooseUs::observe(WhyChooseUsObserver::class);
    }
}
