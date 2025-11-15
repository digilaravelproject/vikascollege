{{-- Student Life Section --}}
@pushOnce('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<style>
    /* --- Base Layout --- */
    .student-life-section {
        margin: 0 auto;
        padding: 3rem 1.5rem;
        font-family: 'Georgia', 'Times New Roman', serif;
        color: #1f2937;
    }

    /* --- Heading --- */
    .student-life-heading {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    /* --- Tabs --- */
    .student-life-tabs {
        display: flex;
        justify-content: center;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 2rem;
        gap: 2.5rem;
    }

    .student-life-tabs button {
        background: none;
        border: none;
        font-size: 1.05rem;
        font-weight: 600;
        color: #6b7280;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .student-life-tabs button.active {
        color: #1e40af;
        border-color: #1e40af;
    }

    .student-life-tabs button:hover {
        color: #1e3a8a;
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
        /* Caption ke liye zaroori */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;

        /* === AAPKI REQUEST: BORDER RADIUS NAHI === */
        border-radius: 0;
        overflow: hidden;
        /* Sharp corners ke liye yeh important hai */
    }

    .masonry-item img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* --- Caption (Overlay, Bottom-Left) --- */
    .image-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        background: rgb(255 255 255);
        color: #000000;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 4px 12px;
        opacity: 1;
    }

    /* --- View Link --- */
    .view-full {
        text-align: center;
        margin-top: 2.5rem;
    }

    .view-full a {
        color: #2563eb;
        font-weight: 500;
        text-decoration: none;
    }

    .view-full a:hover {
        text-decoration: underline;
    }
</style>
@endpushOnce

{{-- HTML (Overlay structure) --}}
@if ($items->isEmpty())
    <p class="text-center text-gray-500">No gallery categories found.</p>
@else
    <div class="student-life-section" data-aos="fade-up">

        {{-- Heading --}}
        <div class="student-life-heading" data-aos="fade-up">
            <h1>{{ $title }}</h1>
        </div>

        {{-- Tabs --}}
        <div x-data="{ activeTab: '{{ $items->first()->slug ?? 'default' }}' }">
            <div class="student-life-tabs" data-aos="fade-up" data-aos-delay="100">
                @foreach ($items as $category)
                    <button @click="activeTab = '{{ $category->slug }}'"
                        :class="activeTab === '{{ $category->slug }}' ? 'active' : ''">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            {{-- Gallery --}}
            <div class="gallery-wrapper" data-aos="fade-up" data-aos-delay="150">
                @foreach ($items as $category)
                    <div x-show="activeTab === '{{ $category->slug }}'" x-transition style="display: none;">
                        <div class="masonry-grid">
                            @forelse ($category->images as $image)
                                {{-- .masonry-item ab <a> tag hai --}}
                                    <div class="masonry-item" data-fancybox="gallery-{{ $category->slug }}"
                                        data-caption="{{ $image->title ?? '' }}" data-aos="fade-up"
                                        data-aos-delay="{{ $loop->index * 60 }}">

                                        <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title ?? '' }}">

                                        {{-- Caption <a> ke andar hai --}}
                                            <div class="image-caption">
                                                {{ $image->title ?? 'Image' }}
                                            </div>

                                    </div>
                            @empty
                                    <p class="text-gray-500 text-center">No images found in this category.</p>
                                @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- View Full Link --}}
        <div class="view-full" data-aos="fade-up" data-aos-delay="200">
            <a href="#">View Full Gallery &rarr;</a>
        </div>
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

    // Initialize Fancybox and show only caption
    Fancybox.bind("[data-fancybox]", {
        showClass: false,
        hideClass: false,
        caption: (fancybox, carousel, slide) => slide.$trigger?.dataset.caption || ''
    });
</script>
@endpushOnce
