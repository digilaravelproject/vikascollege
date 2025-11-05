<div class="page-block-wrapper">
    {{--
        Yeh file ek router ki tarah kaam karti hai.
        $block variable (static data) aur $items, $title, $description variables (dynamic data)
        component class (PageBlock.php) se automatically pass ho rahe hain.
    --}}
    @switch($type)
        @case('intro')
            @include('components.home-page-blocks.intro', ['block' => $block])
            @break

        @case('sectionLinks')
            @include('components.home-page-blocks.section-links', ['block' => $block])
            @break

        @case('latestUpdates')
            @include('components.home-page-blocks.latest-updates', ['items' => $items, 'title' => $title])
            @break

        @case('announcements')
            @include('components.home-page-blocks.announcements', ['items' => $items, 'title' => $title])
            @break

        @case('events')
            @include('components.home-page-blocks.events', [
                'items' => $items,
                'title' => $title,
                'description' => $description,
            ])
            @break

        @case('academic_calendar')
            @include('components.home-page-blocks.academic-calendar', ['items' => $items, 'title' => $title])
            @break

        @case('image_text')
            @include('components.home-page-blocks.image-text', ['block' => $block])
            @break

        @case('gallery')
            @include('components.home-page-blocks.gallery', ['items' => $items, 'title' => $title])
            @break

        @case('testimonials')
            @include('components.home-page-blocks.testimonials', ['items' => $items, 'title' => $title])
            @break

        @case('why_choose_us')
            @include('components.home-page-blocks.why-choose-us', [
                'items' => $items,
                'title' => $title,
                'description' => $description,
            ])
            @break

        @case('divider')
            @include('components.home-page-blocks.divider')
            @break

        @default
            {{-- Aap yahan ek default view ya error dikha sakte hain --}}
            @endswitch
</div>
