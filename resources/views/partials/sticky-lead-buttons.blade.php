<div>
    {{-- Desktop Floating Buttons (Vertical Placement) --}}
    <div class="d-flex flex-col fixed right-0 top-1/2 -translate-y-1/2 z-[55] space-y-3">
        {{--
        <button type="button" id="open-notice-modal" class="animate-pulse fixed z-40 right-0 top-1/2 -translate-y-1/2
               bg-white text-blue-800 font-semibold
               p-4 rounded-l-xl shadow-lg   py-3 px-3
               transition-all duration-300 ease-in-out
                hover:bg-blue-50 hover:shadow-xl group ">
            <span class="block text-[13px] font-medium tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Notice Board
            </span>
            <span
                class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full animate-pulse shadow-md ring-2 ring-white">
            </span>
        </button> --}}

        <button type="button" id="open-notice-modal" class="animate-pulse bg-white text-blue-800 font-semibold rounded-l-xl shadow-lg py-3 px-3
                   transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-xl group">
            <span class="block text-[13px] font-medium tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                <span class="block text-[13px] font-medium tracking-wide"
                    style="writing-mode: vertical-rl; transform: rotate(180deg);">
                    Notice Board
                </span>
            </span>
        </button>
        <button @click="openApply" class="hidden lg:flex bg-white text-blue-800 font-semibold rounded-l-xl shadow-lg py-3 px-3
                   transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-xl group">
            <span class="block text-[13px] font-medium tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Apply Now
            </span>
        </button>

        <button @click="openEnquire" class="hidden lg:flex bg-white text-blue-800 font-semibold rounded-l-xl shadow-lg py-3 px-3
                   transition-all duration-300 ease-in-out hover:bg-blue-50 hover:shadow-xl group">
            <span class="block text-[13px] font-medium tracking-wide"
                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                Enquire Now
            </span>
        </button>
    </div>

    {{-- Mobile Bottom Buttons (High z-index to be clickable) --}}
    <div class="lg:hidden fixed bottom-0 w-full z-[55] flex shadow-2xl">
        <button @click="openApply"
            class="w-1/2 bg-[#013954] text-white py-3 font-semibold hover:bg-blue-700 transition">
            Apply Now
        </button>
        <button @click="openEnquire"
            class="w-1/2 bg-white text-blue-600 py-3 font-semibold hover:bg-gray-100 transition">
            Enquire Now
        </button>
    </div>
</div>
