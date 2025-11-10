<?php

namespace App\View\Components;

use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\EventCategory;
use App\Models\EventItem;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\Notification;
use App\Models\Testimonial;
use App\Models\WhyChooseUs;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache; // 1. Cache facade import karein

class HomePageBlock extends Component
{
    public array $block;
    public string $type;
    public $items; // This will hold dynamic data
    public $title;
    public $description;
    public $loop;
    public $eventCategories;

    /**
     * Create a new component instance.
     */
    public function __construct(array $block, $loop = null)
    {
        $this->block = $block;
        $this->type = $block['type'] ?? 'unknown';
        $this->items = collect(); // Default to empty collection
        $this->eventCategories = collect();
        $this->loop = $loop;

        // Get title/description
        $this->title = $block['section_title'] ?? $block['title'] ?? '';
        $this->description = $block['section_description'] ?? '';

        // Load data based on block type
        match ($this->type) {
            'latestUpdates' => $this->loadLatestUpdates(),
            'announcements' => $this->loadAnnouncements(),
            'events' => $this->loadEvents(),
            'academic_calendar' => $this->loadAcademicCalendar(),
            'gallery' => $this->loadGallery(),
            'testimonials' => $this->loadTestimonials(),
            'why_choose_us' => $this->loadWhyChooseUs(),
            default => null,
        };
    }

    // --- Private data loading methods (Ab Cache ho gaye) ---

    private function loadLatestUpdates()
    {
        // YEH PEHLE SE HI CACHED HAI (NotificationService se)
        $this->items = (new NotificationService())->getRestNotifications();
    }

    private function loadAnnouncements()
    {
        $count = $this->block['display_count'] ?? 5;
        $type = $this->block['content_type'] ?? 'student';
        $cacheKey = "announcements:type:{$type}:count:{$count}";

        $this->items = Cache::remember($cacheKey, 3600, function () use ($count, $type) {
            return Announcement::where('status', 1)
                ->where('type', $type)
                ->latest()
                ->take($count)
                ->get();
        });
    }
    private function loadEvents()
    {
        // 1. Fetch *all* categories (even if no events)
        $this->eventCategories = Cache::remember('event_categories_all', 3600, function () {
            return EventCategory::orderBy('id', 'asc')->get(['id', 'name']);
        });

        // 2. Fetch upcoming and recent events
        $this->items = Cache::remember('all_events_for_homepage', 3600, function () {
            $upcoming = EventItem::with('category')
                ->where('event_date', '>=', now())
                ->orderBy('event_date', 'asc')
                ->get();

            $recent = EventItem::with('category')
                ->where('event_date', '<', now())
                ->orderBy('event_date', 'desc')
                ->take(10)
                ->get();

            return $upcoming->merge($recent);
        });

        // 3. Format data for Alpine.js
        $this->items = $this->items->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'formatted_date' => $item->event_date ? $item->event_date->format('l, jS F Y') : '',
                'location' => $item->venue,
                'category_id' => $item->category->id ?? null,
                'image_url' => $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/400x250',
            ];
        });
    }

    private function loadEvents_old()
    {
        $cacheKey = "events:homepage_block";

        $this->items = Cache::remember($cacheKey, 3600, function () {
            $upcoming = EventItem::with('category')
                ->where('event_date', '>=', now())
                ->orderBy('event_date', 'asc')
                ->take(10)
                ->get();

            if ($upcoming->isEmpty()) {
                return EventItem::with('category')
                    ->where('event_date', '<', now())
                    ->orderBy('event_date', 'desc')
                    ->take(10)
                    ->get();
            }
            return $upcoming;
        });
        dd('Events', $this->items);
    }
    private function loadAcademicCalendar()
    {
        $count = $this->block['item_count'] ?? 7;
        $cacheKey = "academic_calendar:homepage:count:{$count}";

        $this->items = Cache::remember($cacheKey, 3600, function () use ($count) {

            // 1. Pehle UPCOMING events dhoondein
            $upcoming = AcademicCalendar::where('status', 1)
                ->where('event_datetime', '>=', now()->startOfDay())
                ->orderBy('event_datetime', 'asc') // Aane waale pehle
                ->take($count)
                ->get();

            // 2. Agar UPCOMING event nahi milte hain (khaali hai)
            if ($upcoming->isEmpty()) {
                // Toh RECENT PAST events dhoondein
                return AcademicCalendar::where('status', 1)
                    ->where('event_datetime', '<', now()->startOfDay())
                    ->orderBy('event_datetime', 'desc') // Sabse naye (past) waale pehle
                    ->take($count)
                    ->get();
            }

            // 3. Agar upcoming events mile hain, toh unhe return karein
            return $upcoming;
        });
    }
    private function loadAcademicCalendar_old()
    {
        $count = $this->block['item_count'] ?? 7;
        $cacheKey = "academic_calendar:homepage:count:{$count}";

        $this->items = Cache::remember($cacheKey, 3600, function () use ($count) {
            return AcademicCalendar::where('status', 1)
                ->where('event_datetime', '>=', now()->startOfDay())
                ->orderBy('event_datetime', 'asc')
                ->take($count)
                ->get();
        });
    }
    private function loadGallery()
    {
        // Cache key badal diya hai
        $cacheKey = "gallery:homepage:categories:with_images";

        // $this->items me ab Images nahi, Categories aayengi
        $this->items = Cache::remember($cacheKey, 3600, function () {
            // Yahan hum GalleryImage nahi, GalleryCategory fetch kar rahe hain
            // 'galleryImages' aapke relation ka naam hona chahiye (GalleryCategory model me)
            return GalleryCategory::with(['images' => function ($query) {
                // Har category ki sirf 8 images load karein (limit)
                $query->latest()->take(10);
            }])
                ->get();
        });
    }
    private function loadGallery_old()
    {
        $cacheKey = "gallery:homepage:count:8";

        $this->items = Cache::remember($cacheKey, 3600, function () {
            return GalleryImage::with('category')
                ->latest()
                ->take(8)
                ->get();
        });
    }

    private function loadTestimonials()
    {
        $cacheKey = "testimonials:active";

        $this->items = Cache::remember($cacheKey, 3600, function () {
            return Testimonial::where('status', 1)
                ->latest()
                ->get();
        });
    }

    private function loadWhyChooseUs()
    {
        $cacheKey = "why_choose_us:sorted";

        $this->items = Cache::remember($cacheKey, 3600, function () {
            return WhyChooseUs::orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.home-page-block');
    }
}
