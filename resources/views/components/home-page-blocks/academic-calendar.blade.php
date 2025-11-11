<section class="py-16 md:py-24 bg-white">

    {{-- Title and Description (Exact Match: Font-Serif, Spacing) --}}
    <div class="text-center mb-16" data-aos="fade-up">
        {{-- Title: Bold Serif (Playfair Display equivalent), Large size, Dark color --}}
        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-gray-900 tracking-tight mb-4">
            Academic Calendar
        </h2>

        {{-- Description: Light Gray, centered, correct line height --}}
        {{-- <p class="mb-10 text-lg text-gray-700 max-w-4xl mx-auto font-normal leading-normal">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
            industry's standard dummy text ever since the 1500s.
        </p> --}}
    </div>

    {{-- Conditional Rendering --}}
    @if ($items->isEmpty())
        {{-- Empty State (Simple text) --}}
        <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="100">
            No calendar items found.
        </p>
    @else
        {{-- Card Grid (Exact Match: Layout, Card Styling, Typography) --}}
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 px-4 sm:px-6"
            data-aos="fade-up" data-aos-delay="100">

            @foreach ($items as $item)
                {{-- Card Container --}}
                <div class="flex flex-col bg-gray-50 p-6 sm:p-8 rounded-none shadow-none transition-all duration-300 hover:shadow-md hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    <a href="{{ $item->link_href }}">
                        {{-- Date Section --}}
                        <div class="pb-6 border-b border-gray-300 mb-6">
                            {{-- Day --}}
                            <p class="text-5xl font-extrabold text-red-600 mb-1 leading-none">
                                {{ $item->event_datetime->format('d') }}
                            </p>
                            {{-- Month and Year --}}
                            <p class="text-xl font-normal text-gray-900">
                                {{ $item->event_datetime->format('F Y') }}
                            </p>
                        </div>

                        {{-- Event Title --}}
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 leading-snug">
                            {{ $item->title }}
                        </h3>

                        {{-- Time (Bold, Red Accent) --}}
                        <div class="mb-4">
                            <p class="text-lg font-extrabold text-red-600">
                                {{ $item->event_datetime->format('g:i A') }}
                                -
                                {{ $item->end_time ? $item->end_time->format('g:i A') : '10:00 AM' }}
                            </p>
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-600 flex-grow">
                            {{ $item->description ?? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.' }}
                        </p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ========================================= --}}
{{-- Scripts: AOS Animation --}}
{{-- ========================================= --}}
@push('script')
    {{-- AOS Library --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,            // Animation plays once
            duration: 800,         // Default duration
            easing: 'ease-in-out', // Smooth easing
        });
    </script>
@endpush
