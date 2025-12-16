<div>
    {{-- Desktop Floating Buttons --}}
    <div class="flex flex-col fixed right-0 top-1/2 -translate-y-1/2 z-[75] space-y-3">

        {{-- Notice Board Button --}}
        <button type="button" id="open-notice-modal"
            class="animate-pulse bg-white text-blue-800 font-semibold rounded-l-xl shadow-xl py-3 px-2 transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-2xl group relative w-[36px] 
            border-t-[3px] border-l-[3px] border-gray-500">
            
  <span class="absolute -top-1 -left-1 flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
    </span>

            <span class="block text-[14px] font-bold tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Notice Board
            </span>
        </button>

        {{-- Apply Now Button --}}
        <button @click="openApply()" type="button"
            class="hidden lg:flex bg-white text-blue-800 font-semibold rounded-l-xl shadow-lg py-3 px-2 transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-xl group w-[36px]">
            <span class="block text-[14px] font-bold tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Apply Now
            </span>
        </button>

        {{-- Enquire Now Button --}}
        <button @click="openEnquire()" type="button"
            class="hidden lg:flex bg-white text-blue-800 font-semibold rounded-l-xl shadow-lg py-3 px-2 transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-xl group w-[36px]">
            <span class="block text-[14px] font-bold tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Enquire Now
            </span>
        </button>
    </div>

    {{-- Mobile Bottom Sticky Buttons --}}
    <div class="lg:hidden fixed bottom-0 w-full z-[55] flex shadow-2xl">
        <button @click="openApply()" type="button"
            class="w-1/2 bg-[#013954] text-white py-3 font-semibold hover:bg-blue-800 transition">
            Apply Now
        </button>
        <button @click="openEnquire()" type="button"
            class="w-1/2 bg-white text-blue-600 py-3 font-semibold hover:bg-gray-100 transition">
            Enquire Now
        </button>
    </div>
</div>
