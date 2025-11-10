<div x-data="{
        items: {{ $items->toJson() }},
        currentIndex: 0,
        perPage: 2,
        get transformValue() {
            return `translateX(-${this.currentIndex * (100 / this.perPage)}%)`;
        },
        next() {
            if (this.currentIndex < this.items.length - this.perPage) this.currentIndex++;
        },
        prev() {
            if (this.currentIndex > 0) this.currentIndex--;
        },
        init() {
            if (window.innerWidth < 1024) this.perPage = 1;
            window.addEventListener('resize', () => {
                this.perPage = window.innerWidth < 1024 ? 1 : 2;
            });
        }
    }" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-20 relative">

    <!-- Title and Description -->
    <div class="text-center mb-10" data-aos="fade-up">
        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-gray-900 mb-4">
            Testimonial
        </h2>
        <p class="mb-14 text-lg text-gray-600 max-w-4xl mx-auto leading-relaxed">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
            industry's standard dummy text ever since the 1500s.
        </p>
    </div>

    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No testimonials found.</p>
    @else
        <div class="relative">

            <!-- Slider Track -->
            <div class="overflow-hidden">
                {{-- <div x-ref="slider" :style="`transform: ${transformValue}; width: ${items.length * (100 / perPage)}%`"
                    --}} <div x-ref="slider" :style="`transform: ${transformValue}; width: 100%"
                    class="flex transition-transform duration-500 ease-in-out">
                    @foreach ($items as $item)
                        <div class="flex-shrink-0 px-2 w-full lg:w-1/2" data-aos="fade-up"
                            data-aos-delay="{{ $loop->index * 150 }}">
                            <div class="bg-white border border-gray-100 shadow-md overflow-hidden relative">
                                <!-- Red Accent Line -->
                                <div class="h-2 bg-red-600"></div>

                                <!-- Content -->
                                <div class="p-6 sm:p-8 flex items-start space-x-6">
                                    <!-- Image -->
                                    <div class="flex-shrink-0 w-24 h-24 sm:w-32 sm:h-32">
                                        <img class="object-cover w-full h-full rounded-none"
                                            src="{{ $item->student_image ? asset('storage/' . $item->student_image) : 'https://via.placeholder.com/150' }}"
                                            alt="{{ $item->student_name }}" loading="lazy">
                                    </div>

                                    <!-- Text -->
                                    <div class="flex-grow">
                                        <blockquote class="mb-4">
                                            <p class="text-base text-gray-700 leading-relaxed">
                                                {{ $item->testimonial_text ?? "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s." }}
                                            </p>
                                        </blockquote>
                                        <figcaption>
                                            <div class="font-bold text-lg sm:text-xl text-gray-900">
                                                {{ $item->student_name }}
                                            </div>
                                        </figcaption>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button @click="prev()" :disabled="currentIndex === 0"
                class="absolute left-0 top-1/2 transform -translate-y-1/2 -ml-8 z-10 p-3 bg-gray-100 rounded-full shadow-md disabled:opacity-30 disabled:cursor-not-allowed hidden lg:block">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button @click="next()" :disabled="currentIndex >= items.length - perPage"
                class="absolute right-0 top-1/2 transform -translate-y-1/2 -mr-8 z-10 p-3 bg-gray-100 rounded-full shadow-md disabled:opacity-30 disabled:cursor-not-allowed hidden lg:block">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Mobile indicator -->
            <div class="flex justify-center mt-6 lg:hidden">
                <p class="text-sm text-gray-500">
                    <span x-text="currentIndex + 1"></span> of <span x-text="items.length"></span>
                </p>
            </div>
        </div>
    @endif
</div>
