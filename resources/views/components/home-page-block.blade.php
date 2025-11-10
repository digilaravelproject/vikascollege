@php
    // $loop variable ko PageBlock.php class se pass karna hoga
    // (Aapko PageBlock.php me public $loop; add karna pad sakta hai)
    $loop = $loop ?? null;

    // Default view path
    $includePath = null;

    // Divider ko alag se handle karein, usme padding/wrapper nahi chahiye
    if ($type === 'divider') {
        $includePath = 'components.home-page-blocks.divider';
    } else {
        // Baki sab blocks ke liye unka view path set karein
        $includePath = match ($type) {
            'intro' => 'components.home-page-blocks.intro',
            'sectionLinks' => 'components.home-page-blocks.section-links',
            // 'latestUpdates' => 'components.home-page-blocks.latest-updates',
            'announcements' => 'components.home-page-blocks.announcements',
            'events' => 'components.home-page-blocks.events',
            'academic_calendar' => 'components.home-page-blocks.academic-calendar',
            'image_text' => 'components.home-page-blocks.image-text', // Yeh 'intro' jaisa lag raha hai, iska naam 'image_text' hai
            'gallery' => 'components.home-page-blocks.gallery',
            'testimonials' => 'components.home-page-blocks.testimonials',
            'why_choose_us' => 'components.home-page-blocks.why-choose-us',
            default => null
        };
    }
@endphp

@if ($type === 'divider')
    {{-- Divider me koi wrapper nahi ayega --}}
    @include($includePath)

@elseif ($includePath)
    {{--
    YAHAN HAI ASLI MAGIC:
    Har block ko hum ek full-width section me wrap kar rahe hain.
    $loop->even se background color alternate hoga (white, gray-50, white, gray-50...)
    --}}
    <section class="w-full py-12 md:py-20 {{ $loop && $loop->even ? 'bg-gray-50' : 'bg-white' }}">
        {{-- Content ko max-width container me rakhenge --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @include($includePath, [
                'block' => $block ?? null,
                'items' => $items ?? null,
                'title' => $title ?? null,
                'description' => $description ?? null,
                'loop' => $loop // Modal ID ke liye $loop pass kar rahe hain
            ])

                    </div>
                </section>
@endif
