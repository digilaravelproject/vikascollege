<div x-data="{
    items: {{ $items->toJson() }},
    currentIndex: 0,
    perPage: 2,
    get transformValue() {
        return `translateX(-${(this.currentIndex * 100) / this.perPage}%)`;
    },
    next() {
        if (this.currentIndex < this.items.length - this.perPage) this.currentIndex++;
    },
    prev() {
        if (this.currentIndex > 0) this.currentIndex--;
    },
    init() {
        this.perPage = window.innerWidth < 1024 ? 1 : 2;
        window.addEventListener('resize', () => {
            this.perPage = window.innerWidth < 1024 ? 1 : 2;
            // ensure currentIndex does not exceed max after resize
            if(this.currentIndex > this.items.length - this.perPage) this.currentIndex = this.items.length - this.perPage;
        });
    }
}" x-init="init()">

    <!-- Title and Description -->
    <div class="mb-10 text-center" data-aos="fade-up">
        <h2 class="mb-4 font-serif text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
            {{ $title }}
        </h2>
        <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 m-auto"></div>
        <p class="max-w-4xl mx-auto text-lg leading-relaxed text-gray-600 mb-14">
            {{ $description }}
        </p>
    </div>

    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No testimonials found.</p>
    @else
        <div class="relative">
            <div class="overflow-hidden">   
                <!-- Slider Track -->
                <div x-ref="slider" :style="`transform: ${transformValue};`"
                     class="flex transition-transform duration-500 ease-in-out w-full">
                    @foreach ($items as $item)
                        <div class="flex-shrink-0 w-full px-2 lg:w-1/2" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                            <div class="relative overflow-hidden bg-white border border-gray-100 shadow-md">
    
                                <!-- Red Accent Line -->
                                <div class="h-2 bg-[#013954]"></div>
    
                                <!-- Content -->
                                <div class="flex items-start p-6 space-x-6 sm:p-8">
                                    <!-- Image -->
                                    <!--<div class="flex-shrink-0 w-24 h-24 sm:w-32 sm:h-32">-->
                                    <!--    <img class="object-cover w-full h-full rounded-none"-->
                                    <!--         src="{{ $item->student_image ? asset('storage/' . $item->student_image) : 'https://via.placeholder.com/150' }}"-->
                                    <!--         alt="{{ $item->student_name }}" loading="lazy">-->
                                    <!--</div>-->
    
                                    <!-- Text -->
                                    <div class="flex-grow">
                                        <blockquote class="mb-4">
                                            <p class="text-base leading-relaxed text-gray-700">
                                                {{ $item->testimonial_text ?? "Lorem Ipsum..." }}
                                            </p>
                                        </blockquote>
                                        <figcaption>
                                            <div class="text-lg font-bold text-gray-900 sm:text-xl">
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
            <!-- Left Arrow -->
            <button @click="prev()" :disabled="currentIndex === 0"
                    class="absolute left-0 z-10 p-3 -ml-8 transform -translate-y-1/2 bg-gray-100 rounded-full shadow-md top-1/2 disabled:opacity-30 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <!-- Right Arrow -->
            <button @click="next()" :disabled="currentIndex >= items.length - perPage"
                    class="absolute right-0 z-10 p-3 -mr-8 transform -translate-y-1/2 bg-gray-100 rounded-full shadow-md top-1/2 disabled:opacity-30 disabled:cursor-not-allowed">
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
