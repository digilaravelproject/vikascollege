<div x-data="eventTabs({{ $eventCategories->toJson() }}, {{ $items->toJson() }})" x-init="init()"
    class="w-full">

    {{-- Section Header (No Change) --}}
    <div class="mb-8 text-center sm:mb-10" data-aos="fade-up" data-aos-duration="800">
        <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-gray-800 sm:text-5xl sm:mb-4">
            {{ $title }}
        </h2>
        <div class="w-24 h-1.5 bg-[#013954] rounded-full my-4 m-auto"></div>
        @if ($description)
            <p class="max-w-3xl px-4 mx-auto text-base font-light leading-relaxed text-gray-600 sm:text-lg">
                {{ $description }}
            </p>
        @endif
    </div>

    {{-- Tabs & Action Link Container (No Change) --}}
    <div class="relative flex flex-col items-center justify-center mb-5 md:flex-row sm:mb-6" data-aos="fade-up"
        data-aos-delay="200">
        <div
            class="flex justify-start w-full gap-3 px-2 pb-2 overflow-x-auto flex-nowrap md:flex-wrap md:overflow-visible md:justify-center md:w-auto md:px-0 md:pb-0 hide-scrollbar">
            <template x-for="(category, index) in eventCategories" :key="category.id">
                <button @click="setActiveCategory(category.id)" :data-aos="'fade-up'" :data-aos-delay="index * 100"
                    class="px-1 py-2 sm:px-2 sm:py-2.5 rounded-full text-xs sm:text-base uppercase font-medium transition-all duration-300 ease-in-out border-2 whitespace-nowrap flex-shrink-0"
                    :class="{
                            'border-[#013954] text-[#013954] bg-[#013954]/5 font-bold shadow-sm': activeCategoryId == category.id,
                            'border-transparent text-gray-500 hover:text-[#013954] hover:bg-gray-50': activeCategoryId != category.id
                        }">
                    <span x-text="category.name"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Empty State (No Change) --}}
    <template x-if="filteredEventsForCategory.length === 0">
        <div class="py-10 text-center" data-aos="fade-up">
            <p class="text-lg text-gray-500">No events found for this category.</p>
        </div>
    </template>

    {{-- NEW: Wrapper for Slider and Controls --}}
    <div class="flex items-center justify-center">

        {{-- Slider Control: PREV (Hidden on small screens) --}}
        <div x-show="maxPages > 1" class="hidden pr-4 lg:block">
            <button @click="prevPage()" :disabled="currentPage === 1"
                class="p-3 text-white transition-colors duration-300 rounded-full bg-[#013954] disabled:bg-gray-400 hover:bg-blue-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        {{-- Events Grid (Core Content) --}}
        <div class="w-full">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-10" data-aos="fade-up" data-aos-delay="100">
                <template x-for="(item, index) in paginatedEvents" :key="item.id">
                    <div class="flex flex-col overflow-hidden transition-all duration-300 bg-white border border-gray-200 rounded-none shadow-lg hover:shadow-2xl hover:-translate-y-1 group"
                        data-aos="zoom-in" :data-aos-delay="index * 100" data-aos-duration="700">
                        {{-- Event Image or Fallback --}}
                        <a :href="item.link || '#'" class="relative block overflow-hidden aspect-square">
                            <template x-if="item.image_url">
                                <img :src="item.image_url" :alt="item.title"
                                    class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy">
                            </template>
                            <template x-if="!item.image_url">
                                <div class="flex items-center justify-center w-full h-full p-6 text-center text-white bg-gradient-to-br from-[#013954] to-[#012740] transition-transform duration-700 group-hover:scale-105">
                                    <h4 class="text-xl font-bold leading-tight uppercase" x-text="item.title"></h4>
                                </div>
                            </template>
                            <div class="absolute inset-0 transition-colors duration-300 bg-black/0 group-hover:bg-black/10">
                            </div>
                        </a>
                        {{-- Event Details --}}
                        <div class="relative flex flex-col flex-1 p-5">
                            <p class="text-sm font-bold text-[#013954] mb-2 uppercase tracking-wide"
                                x-text="item.formatted_date"></p>
                            <h3 class="mb-2 text-xl font-bold leading-tight text-gray-900">
                                <a :href="item.link || '#'" class="hover:text-[#013954] transition-colors"
                                    x-text="item.title"></a>
                            </h3>
                            <div class="flex items-center pt-3 mt-auto text-sm text-gray-600 border-t border-gray-100">
                                <svg class="w-4 h-4 mr-2 text-[#013954]" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                    </path>
                                </svg>
                                <span x-text="item.location || 'Venue Details'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Slider Control: NEXT (Hidden on small screens) --}}
        <div x-show="maxPages > 1" class="hidden pl-4 lg:block">
            <button @click="nextPage()" :disabled="currentPage === maxPages"
                class="p-3 text-white transition-colors duration-300 rounded-full bg-[#013954] disabled:bg-gray-400 hover:bg-blue-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

    </div>
    {{-- NEW: Mobile Controls (Show on small screens, below grid) --}}
    <div x-show="maxPages > 1" class="flex justify-center mt-6 space-x-4 lg:hidden" data-aos="fade-up" data-aos-delay="200">
        <button @click="prevPage()" :disabled="currentPage === 1"
            class="p-2 text-white transition-colors duration-300 rounded-full bg-[#013954] disabled:bg-gray-400 hover:bg-blue-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button @click="nextPage()" :disabled="currentPage === maxPages"
            class="p-2 text-white transition-colors duration-300 rounded-full bg-[#013954] disabled:bg-gray-400 hover:bg-blue-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800, easing: 'ease-in-out' });

    function eventTabs(eventCategories, allEvents) {
        return {
            eventCategories: eventCategories || [],
            allEvents: allEvents || [],
            activeCategoryId: null,
            // SLIDER PROPERTIES
            itemsPerPage: 3, // Display 3 items per page/slide
            currentPage: 1,  // Start on the first page
            // END SLIDER PROPERTIES

            init() {
                if (this.eventCategories.length > 0) {
                    this.activeCategoryId = this.eventCategories[0].id;
                }
            },

            // Function to handle category click and reset page (Updated)
            setActiveCategory(categoryId) {
                this.activeCategoryId = categoryId;
                this.currentPage = 1; // Reset to the first page when changing categories
            },

            // Filters events for the currently active category (No Change)
            get filteredEventsForCategory() {
                if (!this.activeCategoryId) return [];
                return this.allEvents
                    .filter(e => e.category_id == this.activeCategoryId);
            },

            // Calculates the total number of pages (No Change)
            get maxPages() {
                return Math.ceil(this.filteredEventsForCategory.length / this.itemsPerPage);
            },

            // Returns the events for the current page (No Change)
            get paginatedEvents() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredEventsForCategory
                    .slice(start, end);
            },

            // Moves to the next page (No Change)
            nextPage() {
                if (this.currentPage < this.maxPages) {
                    this.currentPage++;
                }
            },

            // Moves to the previous page (No Change)
            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            }
        }
    }
</script>
