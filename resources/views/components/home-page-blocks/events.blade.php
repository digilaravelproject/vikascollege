{{-- Alpine.js component initialization --}}
<div x-data="eventTabs({{ $eventCategories->toJson() }}, {{ $items->toJson() }})" x-init="init()" {{-- Container width
    and padding --}} class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">

    {{-- 1. Title and Description (Exact Match) --}}
    <div class="text-center" data-aos="fade-up" data-aos-duration="800">
        {{-- Title: Bold Serif (or equivalent system-ui), Dark Gray, Tracking adjusted --}}
        <h2 class="text-4xl sm:text-5xl font-serif font-bold text-gray-800 tracking-tight mb-6">
            {{ $title }}
        </h2>
        @if ($description)
            {{-- Description: Light Gray, Normal Line Height, Correct Max Width --}}
            <p class="mb-10 text-lg text-gray-700 max-w-4xl mx-auto font-light leading-relaxed" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="800">
                {{ $description }}
            </p>
        @endif
    </div>


    {{-- 2. Dynamic Tabs & "More Items" link (Exact Match: Spacing, Divider, Underline) --}}
    <div class="flex flex-col sm:flex-row justify-center items-center mb-10 border-b border-gray-300 relative"
        data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">

        {{-- Tabs Container: The border-b is on the parent div to act as the main horizontal divider --}}
        <div class="flex flex-wrap justify-center space-x-6 sm:space-x-8 -mb-px">
            <template x-for="(category, index) in eventCategories" :key="category.id">
                {{-- Tabs: Space-x-6/8 provides the gap, divide-x is not needed here as per image design --}}
                <button @click="activeCategoryId = category.id" :data-aos="'fade-up'" :data-aos-delay="index * 100"
                    data-aos-duration="600" :class="{
                       'text-black font-semibold border-b-2 border-black': activeCategoryId == category.id,
                       'text-gray-600 font-medium hover:text-gray-900': activeCategoryId != category.id
                    }" {{-- Active tab's bottom border overlaps the main parent border (using -mb-0.5 for a clean look)
                    --}} class="text-lg py-3 px-1 transition-all duration-300 ease-in-out whitespace-nowrap -mb-0.5">
                    <span x-text="category.name"></span>
                </button>
            </template>
        </div>

        {{-- More Items Link (Positioned absolutely on desktop for exact right alignment) --}}
        <div class="sm:absolute right-0 top-0 mt-3 sm:mt-0" data-aos="fade-left" data-aos-delay="300">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-700 text-base sm:text-lg whitespace-nowrap">
                More Items &rarr;
            </a>
        </div>
    </div>



    {{-- 3. Empty State --}}
    <template x-if="filteredEvents.length === 0">
        <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="400">
            No events found for this category.
        </p>
    </template>

    {{-- 4. Card Grid (Exact Match: Square Image, subtle shadow, typography) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10" data-aos="fade-up" data-aos-delay="100">
        <template x-for="(item, index) in filteredEvents" :key="item.id">
            {{-- Card: Subtle Shadow, Thin Border, Sharp Corners --}}
            <div class="flex flex-col bg-white rounded-none shadow-lg border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                data-aos="zoom-in" :data-aos-delay="index * 100" data-aos-duration="700">

                {{-- Image: 1:1 Aspect Ratio (Square) --}}
                <a href="#" class="block aspect-square">
                    <img :src="item.image_url" :alt="item.title" class="object-cover w-full h-full" loading="lazy">
                </a>

                {{-- Content: Padding and Typography --}}
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                        <a href="#" class="hover:text-blue-600 transition-colors" x-text="item.title"></a>
                    </h3>
                    <p class="text-sm text-gray-600 font-normal mb-0" x-text="item.formatted_date"></p>
                    <p class="text-sm text-gray-600 font-normal mt-0"
                        x-text="'Venue - ' + (item.location || 'Venue Details')"></p>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- AOS Initialization (Must be included for the animations to work) --}}
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-in-out',
    });
</script>

<script>
    function eventTabs(eventCategories, allEvents) {
        return {
            eventCategories: eventCategories || [],
            allEvents: allEvents || [],
            activeCategoryId: null,

            init() {
                if (this.eventCategories.length > 0) {
                    this.activeCategoryId = this.eventCategories[0].id;
                }
            },

            get filteredEvents() {
                if (!this.activeCategoryId) return [];
                const filtered = this.allEvents.filter(
                    e => e.category_id == this.activeCategoryId
                );
                return filtered.slice(0, 3); // show first 3
            }
        }
    }
</script>
