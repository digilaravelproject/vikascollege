{{-- Student Life Section --}}
@pushOnce('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<style>
    /* --- Base Layout --- */
    .student-life-section {
        margin: 0 auto;
        padding: 2rem 0.5rem;
        font-family: 'Georgia', 'Times New Roman', serif;
        color: #1f2937;
    }

    /* Desktop ke liye wapis normal spacing */
    @media (min-width: 768px) {
        .student-life-section {
            padding: 3rem 1.5rem;
        }
    }

    /* --- Heading --- */
    .student-life-heading {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .student-life-heading h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    /* --- TABS (Updated to Pill Shape) --- */
    .student-life-tabs {
        display: flex;
        flex-wrap: nowrap;
        /* Mobile: One line */
        overflow-x: auto;
        /* Mobile: Scrollable */
        justify-content: flex-start;
        /* Mobile: Left Align */
        gap: 0.75rem;
        /* Gap between pills */
        margin-bottom: 2.5rem;
        padding-bottom: 5px;
        /* Space for touch */

        /* Hide Scrollbar */
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .student-life-tabs::-webkit-scrollbar {
        display: none;
    }

    /* Desktop Layout for Tabs */
    @media (min-width: 768px) {
        .student-life-tabs {
            justify-content: center;
            flex-wrap: wrap;
            overflow: visible;
        }
    }

    .student-life-tabs button {
        background: transparent;
        border: 2px solid transparent;
        /* Invisible border default */
        border-radius: 9999px;
        /* Pill Shape */
        padding: 0.6rem 1.5rem;
        /* Padding inside pill */
        font-size: 1rem;
        font-weight: 500;
        color: #6b7280;
        /* Gray Text */
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* --- ACTIVE STATE (Primary Color Highlight) --- */
    .student-life-tabs button.active {
        border-color: #013954;
        /* Primary Border */
        color: #013954;
        /* Primary Text */
        background-color: rgba(1, 57, 84, 0.05);
        /* Light Tint BG */
        font-weight: 700;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .student-life-tabs button:hover:not(.active) {
        color: #013954;
        background-color: #f9fafb;
    }

    /* --- Masonry Layout --- */
    .masonry-grid {
        column-count: 1;
        column-gap: 1.25rem;
    }

    @media (min-width: 640px) {
        .masonry-grid {
            column-count: 2;
        }
    }

    @media (min-width: 1024px) {
        .masonry-grid {
            column-count: 3;
        }
    }

    .masonry-item {
        display: inline-block;
        width: 100%;
        break-inside: avoid;
        margin-bottom: 1.25rem;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        cursor: zoom-in;
        border-radius: 0;
        /* Sharp corners */
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .masonry-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .masonry-item img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* --- Caption (Overlay) --- */
    .image-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.95);
        color: #013954;
        /* Primary Color Text */
        font-size: 0.85rem;
        font-weight: 700;
        padding: 6px 14px;
        backdrop-filter: blur(2px);
    }

    /* --- View Link --- */
    .view-full {
        text-align: center;
        margin-top: 2.5rem;
    }

    .view-full a {
        color: #013954;
        font-weight: 600;
        text-decoration: none;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: color 0.2s;
    }

    .view-full a:hover {
        color: #025075;
    }
</style>
@endpushOnce

@if ($items->isEmpty())
    <p class="py-10 text-center text-gray-500">No gallery categories found.</p>
@else
    <div class="student-life-section" data-aos="fade-up">

        {{-- Heading --}}
        <div class="student-life-heading" data-aos="fade-up">
            <h2>{{ $title }}</h2>
            <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 m-auto"></div>
        </div>

        {{-- Tabs Wrapper --}}
        <div x-data="{ activeTab: '{{ $items->first()->slug ?? 'default' }}' }">

            {{-- Tabs (Pill Style) --}}
            <div class="student-life-tabs" data-aos="fade-up" data-aos-delay="100">
                @foreach ($items as $category)
                    <button @click="activeTab = '{{ $category->slug }}'"
                        :class="activeTab === '{{ $category->slug }}' ? 'active' : ''">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            {{-- Gallery Grid --}}
            <div class="gallery-wrapper" data-aos="fade-up" data-aos-delay="150">
                @foreach ($items as $category)
                    <div x-show="activeTab === '{{ $category->slug }}'" x-transition.opacity.duration.500ms
                        style="display: none;">

                        <div class="masonry-grid">
                            @forelse ($category->images as $image)
                                <div class="masonry-item" data-fancybox="gallery-{{ $category->slug }}"
                                    data-src="{{ asset('storage/' . $image->image) }}" data-caption="{{ $image->title ?? '' }}"
                                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">

                                    <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title ?? '' }}"
                                        loading="lazy">

                                    @if($image->title)
                                        <div class="image-caption">
                                            {{ $image->title }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="w-full col-span-3 py-4 text-center text-gray-500">No images found in this category.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- View Full Link --}}
        <!--<div class="view-full" data-aos="fade-up" data-aos-delay="200">-->
        <!--    <a href="#">-->
        <!--        View Full Gallery-->
        <!--        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"-->
        <!--            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
        <!--            <line x1="5" y1="12" x2="19" y2="12"></line>-->
        <!--            <polyline points="12 5 19 12 12 19"></polyline>-->
        <!--        </svg>-->
        <!--    </a>-->
        <!--</div>-->
    </div>
@endif

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        once: true,
        duration: 700,
        offset: 100
    });

    // Initialize Fancybox
    Fancybox.bind("[data-fancybox]", {
        showClass: "fancybox-zoomIn",
        hideClass: "fancybox-zoomOut",
        Toolbar: {
            display: {
                left: ["infobar"],
                middle: [],
                right: ["slideshow", "thumbs", "close"],
            },
        },
        caption: (fancybox, carousel, slide) => slide.$trigger?.dataset.caption || ''
    });
</script>
@endpushOnce