<?php

namespace App\View\Components;

use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\EventItem;
use App\Models\GalleryImage;
use App\Models\Notification;
use App\Models\Testimonial;
use App\Models\WhyChooseUs;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class HomePageBlock extends Component
{
    public array $block;
    public string $type;
    public $items; // This will hold dynamic data
    public $title;
    public $description;

    /**
     * Create a new component instance.
     *
     * @param array $block
     */
    public function __construct(array $block)
    {
        $this->block = $block;
        $this->type = $block['type'] ?? 'unknown';
        $this->items = collect(); // Default to empty collection

        // Get title/description, handling different key names
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

    // --- Private data loading methods ---

    private function loadLatestUpdates()
    {
        $this->items = (new NotificationService())->getRestNotifications();
    }

    private function loadAnnouncements()
    {
        $count = $this->block['display_count'] ?? 5;
        $type = $this->block['content_type'] ?? 'student';
        $this->items = Announcement::where('status', 1)
            ->where('type', $type)
            ->latest()
            ->take($count)
            ->get();
    }

    private function loadEvents()
    {
        // Get 3 upcoming events
        $upcoming = EventItem::with('category')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();

        // If no upcoming, get 3 recent past events
        if ($upcoming->isEmpty()) {
            $this->items = EventItem::with('category')
                ->where('event_date', '<', now())
                ->orderBy('event_date', 'desc')
                ->take(3)
                ->get();
        } else {
            $this->items = $upcoming;
        }
    }

    private function loadAcademicCalendar()
    {
        $count = $this->block['item_count'] ?? 7;
        // Get upcoming active calendar items
        $this->items = AcademicCalendar::where('status', 1)
            ->where('event_datetime', '>=', now()->startOfDay())
            ->orderBy('event_datetime', 'asc')
            ->take($count)
            ->get();
    }

    private function loadGallery()
    {
        // Get 8 most recent images from any category
        $this->items = GalleryImage::with('category')
            ->latest()
            ->take(8)
            ->get();
    }

    private function loadTestimonials()
    {
        // Get all active testimonials
        $this->items = Testimonial::where('status', 1)
            ->latest()
            ->get();
    }

    private function loadWhyChooseUs()
    {
        // Get all items, ordered by sort_order
        $this->items = WhyChooseUs::orderBy('sort_order')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // The view file will handle the switching
        return view('components.home-page-block');
    }
}
