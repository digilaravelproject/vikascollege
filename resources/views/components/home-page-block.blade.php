@php
    // $loop variable ko PageBlock.php class se pass karna hoga
    // (Aapko PageBlock.php me public $loop; add karna pad sakta hai)
    $loop = $loop ?? null;

    // Default view path
    $includePath = null;

    // Baki sab blocks ke liye unka view path set karein
    $includePath = match ($type) {
        'intro' => 'components.home-page-blocks.intro',
        'sectionLinks' => 'components.home-page-blocks.section-links',
        // 'latestUpdates' => 'components.home-page-blocks.latest-updates',
        'announcements' => 'components.home-page-blocks.announcements',
        'events' => 'components.home-page-blocks.events',
        'academic_calendar' => 'components.home-page-blocks.academic-calendar',
        // 'image_text' => 'components.home-page-blocks.image-text', // Yeh 'intro' jaisa lag raha hai, iska naam 'image_text' hai
        'gallery' => 'components.home-page-blocks.gallery',
        'testimonials' => 'components.home-page-blocks.testimonials',
        'why_choose_us' => 'components.home-page-blocks.why-choose-us',
        'divider' => 'components.home-page-blocks.divider',
        'layout_grid' => 'components.home-page-blocks.layout-grid',
        default => null
    };

@endphp

@if ($includePath)
    @if ($type === 'divider')
        {{-- Divider ko wrapper/padding nahi chahiye --}}
        @include($includePath)

    @elseif ($type === 'layout_grid')
        {{-- Layout Grid ka wrapper alag hai (yeh recursive hai) --}}
        @include($includePath, [
            'block' => $block,
            'loop' => $loop // $loop ko nested blocks me pass karein
        ])
    @else
                        <section class="w-full py-4 md:py-8 {{ $loop && $loop->even ? 'bg-gray-50' : 'bg-white' }}">
             {{-- Content ko max-width container me rakhenge --}}
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                      @include($includePath, [
                        'block' => $block, // Pura block pass karein
                        'items' => $items, // DB se laaya hua data
                        'title' => $title, // DB se laaya hua title
                        'description' => $description, // DB se laaya hua description
                    ])

                     </div>
                     </section>
        @endif
@endif
