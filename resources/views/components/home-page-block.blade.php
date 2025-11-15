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
<style>
    @import url('https://fonts.googleapis.com/css2?family=Marcellus&display=swap');

    :root {
        --header-font: "Marcellus", serif !important;
    }

    /* GLOBAL HEADING RENDER OPTIMIZATION */
    h1,
    h2,
    h3,
    .header-title {
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
        font-weight: 400 !important;
        /* Marcellus default elegant weight */
        color: #1a1a1a !important;
        /* classy dark color */
        letter-spacing: 0.3px !important;
        /* subtle premium spacing */
    }

    /* ================================
   H1 (Main Big Title)
================================ */
    h1,
    .header-title.h1 {
        font-family: var(--header-font) !important;
        font-size: clamp(34px, 6vw, 42px) !important;
        line-height: 52px !important;
        font-weight: 400 !important;
        text-align: center !important;
        margin-bottom: 20px !important;
    }

    /* ================================
   H2 (Section Heading)
================================ */
    h2,
    .header-title.h2 {
        font-family: var(--header-font) !important;
        font-size: clamp(28px, 5vw, 34px) !important;
        line-height: 44px !important;
        font-weight: 400 !important;
        text-align: center !important;
        margin-bottom: 18px !important;
    }

    /* ================================
   H3 (Sub-heading)
================================ */
    h3,
    .header-title.h3 {
        font-family: var(--header-font) !important;
        font-size: clamp(22px, 4vw, 28px) !important;
        line-height: 36px !important;
        font-weight: 400 !important;
        text-align: center !important;
        margin-bottom: 14px !important;
    }

    /* ================================
   DEFAULT .header-title (H2-style)
================================ */
    .header-title {
        font-family: var(--header-font) !important;
        font-size: clamp(28px, 5vw, 34px) !important;
        line-height: 44px !important;
        font-weight: 400 !important;
        text-align: center !important;
        margin-bottom: 18px !important;
    }
</style>


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
            <section class="w-full py-2 md:py-6 bg-white">
                      {{-- <section class="w-full py-2 md:py-6 {{ $loop && $loop->even ? 'bg-gray-50' : 'bg-white' }}"> --}}
                     {{-- Content ko max-width container me rakhenge --}}
                       <div class="max-w-[90rem] mx-auto px-1 sm:px-2 lg:px-4">
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
