@php
    use App\Models\Menu;

    // --- 1. HELPER FUNCTION FOR TITLE FORMATTING (Added this here) ---
    if (!function_exists('getMenuLabel')) {
        function getMenuLabel($title)
        {
            // Configuration: Is list me jo word jaise likhoge, waisa hi dikhega.
            $exactCasingList = [
                'IQAC',
                'RTI',
                'NAAC',
                'NCC',
                'NSS',
                'IQAC Home',
                'AQAR',
                'IIQA',
                'SSR',
                'ISO Certification',
                'DVV Clarifications',
                'e-Governance',
                'PhD'
            ];

            foreach ($exactCasingList as $exact) {
                if (strcasecmp($title, $exact) === 0) {
                    return $exact; // Exact matching format return karega
                }
            }

            // Default behavior: Capitalize (e.g. "student life" -> "Student Life")
            return ucwords(strtolower($title));
        }
    }

    // --- 2. Fetch & Filter Menus ---
    $rawMenus = Menu::where('status', 1)
        ->whereNull('parent_id')
        ->with('childrenRecursive')
        ->orderBy('order')
        ->get();

    $menus = $rawMenus->filter(function ($value, $key) {
        return !str_starts_with((string) $value->order, '100');
    });

    // --- 3. Fetch Settings ---
    $backgroundAudio = setting('background_audio');
    $topBannerImage = setting('top_banner_image');

    // Fetch Social Media Links
    $email = setting('email');
    $phone = setting('phone');
    $facebook = setting('facebook_url');
    $twitter = setting('twitter_url');
    $instagram = setting('instagram_url');
    $youtube = setting('youtube_url');
    $linkedin = setting('linkedin_url');

    // --- 4. Flatten Menus for Real-Time Search (Desktop Search Bar) ---
    $searchableMenus = collect();
    $flattenMenus = function ($items) use (&$flattenMenus, &$searchableMenus) {
        foreach ($items as $item) {
            $searchableMenus->push([
                'title' => getMenuLabel($item->title), // UPDATED: Using helper function
                'link' => $item->link ?? '#'
            ]);
            if ($item->children->count()) {
                $flattenMenus($item->children);
            }
        }
    };
    $flattenMenus($menus);

    // --- 5. Mobile Recursive Function ---
    if (!function_exists('renderMobileRecursive')) {
        function renderMobileRecursive($items, $level = 0)
        {
            if ($items->isEmpty())
                return '';
            $borderClass = $level >= 0 ? 'border-l-2 border-[#013954]/20 ml-4' : '';
            $bgClass = $level % 2 == 0 ? 'bg-[#013954]/5' : 'bg-white';
            $html = '<div class="flex flex-col space-y-0.5 ' . $borderClass . ' ' . $bgClass . '">';
            foreach ($items as $item) {
                $hasChildren = $item->children->count() > 0;

                // UPDATED: Using helper function for Title
                $title = getMenuLabel($item->title);

                $link = $item->link ?? '#';
                $html .= '<div x-data="{ open: false }" class="w-full">';
                $html .= '<div class="flex items-center justify-between w-full pr-4">';

                // NOTE: Removed 'capitalize' class from <a> tag below because getMenuLabel handles casing now
                $html .= '<a href="' . $link . '" class="flex-1 py-2.5 pl-3 text-sm font-medium text-[#013954] hover:text-[#013954] hover:bg-[#013954]/10 rounded-l-md transition">' . $title . '</a>';

                if ($hasChildren) {
                    $html .= '<button @click="open = !open" class="p-2.5 text-[#013954] hover:bg-[#013954]/10 rounded-r-md transition">';
                    $html .= '<svg :class="open ? \'rotate-180\' : \'\'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>';
                    $html .= '</button>';
                }
                $html .= '</div>';
                if ($hasChildren) {
                    $html .= '<div x-show="open" x-cloak x-collapse class="pb-2">';
                    $html .= renderMobileRecursive($item->children, $level + 1);
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
            return $html;
        }
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    header {
        font-family: 'Roboto', sans-serif !important;
    }

    @keyframes music-bar {

        0%,
        100% {
            height: 20%;
        }

        50% {
            height: 100%;
        }
    }

    [x-cloak] {
        display: none !important;
    }

    /* Scrollbar for Search Results */
    .thin-scrollbar::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .thin-scrollbar::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 4px;
    }

    .thin-scrollbar::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 10px;
    }

    .thin-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #9ca3af;
    }
</style>

{{-- ANNOUNCEMENT & MUSIC BAR --}}
<section class="hidden md:flex w-full text-[#ffffff] overflow-hidden flex flex-wrap items-center border-[#D6DBE2]"
    style="background:linear-gradient(90deg, rgba(1, 39, 112, 0.1) 0%, #013954 62.5%);">

    {{-- Desktop Music Player --}}
    <div class="relative z-20 flex items-center h-full px-3 py-1" x-data="{
            playing: false,
            audio: null,
            url: '{{ asset('storage/' . $backgroundAudio) }}',
            toggleMusic() {
                if (!this.audio) {
                    this.audio = new Audio(this.url);
                    this.audio.addEventListener('ended', () => this.playing = false);
                }
                if (this.playing) {
                    this.audio.pause();
                    this.playing = false;
                } else {
                    this.audio.play();
                    this.playing = true;
                }
            }
         }">
        <button @click="toggleMusic()"
            class="flex items-center gap-2 px-2 py-1 rounded-full bg-white/20 hover:bg-white/30 transition-all duration-300 text-[#013954] focus:outline-none group">
            <svg x-show="!playing" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
            <svg x-show="playing" x-cloak class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
            </svg>
            <span class="text-xs font-bold tracking-wider uppercase"
                x-text="playing ? 'College Song Pause' : 'College Song Play'"></span>
            <div x-show="playing" x-cloak class="flex gap-[2px] h-3 items-end ml-1">
                <span class="w-[2px] bg-[#013954] animate-[music-bar_0.5s_ease-in-out_infinite]"></span>
                <span class="w-[2px] bg-[#013954] animate-[music-bar_0.7s_ease-in-out_infinite]"></span>
                <span class="w-[2px] bg-[#013954] animate-[music-bar_0.4s_ease-in-out_infinite]"></span>
            </div>
        </button>
    </div>

    <div
        class="flex items-center justify-center px-2 py-2 lg:px-5 sm:py-3 text-xs sm:text-sm md:text-base font-semibold tracking-wide text-[#013954] uppercase">
        📢 Announcement</div>

    <div class="relative flex-1 py-2 overflow-hidden text-xs sm:text-sm md:text-[15px] font-medium tracking-wide">
        <div class="marquee">
            <div class="track">
                @php
                    $notifService = app(\App\Services\NotificationService::class);

                    // This is the update:
                    // We directly call the new function to get ONLY the top-featured notifications.
                    $marqueeNotifications = $notifService->getMarqueeNotifications();

                    // The old .filter() block is no longer needed.
                @endphp

                @if (count($marqueeNotifications))
                    @foreach ($marqueeNotifications as $n)
                        @php
                            $icon = $n->icon ?: '🔔';
                            $title = $n->title;
                            $href = $n->href;
                            $btn = $n->button_name ?: 'Click Here';
                        @endphp
                        <span>{{ $icon }} {{ $title }} — @if ($href)<a href="{{ $href }}"
                        class="marquee-link">{{ $btn }}</a>@endif</span>
                    @endforeach

                    @foreach ($marqueeNotifications as $n)
                        @php
                            $icon = $n->icon ?: '🔔';
                            $title = $n->title;
                            $href = $n->href;
                            $btn = $n->button_name ?: 'Click Here';
                        @endphp
                        <span>{{ $icon }} {{ $title }} — @if ($href)<a href="{{ $href }}"
                        class="marquee-link">{{ $btn }}</a>@endif</span>
                    @endforeach
                @else
                    {{-- This is your original fallback content --}}
                    <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                    <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                    <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                    <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>

                    <span>🎓 Admissions Open 2025–26 — <a href="#" class="marquee-link">Apply Now</a></span>
                    <span>🏆 Merit List Declared — <a href="#" class="marquee-link">View Results</a></span>
                    <span>🎭 Annual Cultural Fest Coming Soon — <a href="#" class="marquee-link">Know More</a></span>
                    <span>📚 Exam Timetable Released — <a href="#" class="marquee-link">Check Schedule</a></span>
                @endif
            </div>
        </div>
    </div>
</section>

{{--
HEADER WRAPPER
Contains: Logo + Search (Desktop) + Hamburger (Mobile)
--}}
<div x-data="{ mobileMenuOpen: false }" class="relative z-[65] font-inter bg-white border-b border-gray-100">

    <header class="w-full">
        {{--
        STRICT ALIGNMENT CONTAINER:
        Reduced padding: lg:py-1 (was lg:py-3) to decrease height
        --}}
        <div
            class="w-full max-w-[1380px] px-2 lg:px-4 mx-auto flex items-center justify-between py-2 lg:py-1 overflow-hidden">

            {{-- LOGO SECTION --}}
            <div class="flex-shrink-0">
                <a href="{{ url('/') }}">
                    @if ($topBannerImage)
                        {{-- Added loading="lazy" and decoding="async" here --}}
                        <img loading="lazy" decoding="async" src="{{ asset('storage/' . $topBannerImage) }}"
                            alt="College Banner" class="object-contain object-left w-auto h-24 sm:h-28 md:h-28 lg:h-32">
                    @else
                        {{-- Fallback --}}
                        <div class="h-24 flex items-center text-[#013954] font-bold text-xl">
                            {{ config('app.name') }}
                        </div>
                    @endif
                </a>
            </div>

            {{-- RIGHT SIDE CONTENT --}}
            <div class="flex items-center gap-4">

                {{-- DESKTOP: SEARCH + SOCIAL ICONS STACKED --}}
                <div class="flex-col items-end justify-center hidden gap-2 lg:flex">

                    {{-- Search Bar --}}
                    <div class="relative" x-data="{
                            query: '',
                            menus: {{ $searchableMenus->toJson() }},
                            results: [],
                            search() {
                                if (this.query.length < 2) {
                                    this.results = [];
                                    return;
                                }
                                const q = this.query.toLowerCase();
                                this.results = this.menus.filter(m => m.title.toLowerCase().includes(q));
                            }
                        }" @click.away="results = []">

                        <div class="relative group">
                            {{-- Increased width to w-80 and added border-[#013954] --}}
                            <input type="text" x-model="query" @input="search()" placeholder="Search Menu..."
                                class="w-80 pl-10 pr-4 py-2 bg-gray-50 border border-[#013954] rounded-full text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#013954]/50 focus:border-[#013954] transition-all shadow-sm">

                            {{-- Search Icon --}}
                            <svg class="w-4 h-4 absolute left-3.5 top-1/2 transform -translate-y-1/2 text-[#013954]"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        {{-- Search Results Dropdown --}}
                        <div x-show="results.length > 0 && query.length >= 2" x-cloak
                            class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden z-[1001] max-h-80 overflow-y-auto thin-scrollbar">
                            <ul>
                                <template x-for="result in results">
                                    <li>
                                        <a :href="result.link"
                                            class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-[#013954]/5 hover:text-[#013954] border-b border-gray-50 last:border-0 transition-colors">
                                            <div class="font-medium capitalize" x-text="result.title"></div>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        {{-- No Results --}}
                        <div x-show="query.length >= 2 && results.length === 0" x-cloak
                            class="absolute right-0 p-4 mt-2 text-sm text-center text-gray-500 bg-white border border-gray-100 rounded-lg shadow-lg top-full w-80 z-35">
                            No menu items found.
                        </div>
                    </div>

                    {{-- Social Media Icons (Below Search Bar) --}}
                    {{-- Social Media Icons (Below Search Bar) --}}
                    @if ($facebook || $twitter || $instagram || $youtube || $linkedin)
                        <div class="flex justify-center w-full gap-2">
                            @if ($facebook)
                                <a href="{{ $facebook }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full transition border border-[#1877F2] bg-[#1877F2] text-white hover:bg-white hover:text-[#1877F2]"
                                    aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0 0 22 12" />
                                    </svg>
                                </a>
                            @endif
                            @if ($twitter)
                                <a href="{{ $twitter }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full transition border border-[#1DA1F2] bg-[#1DA1F2] text-white hover:bg-white hover:text-[#1DA1F2]"
                                    aria-label="Twitter">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M22.46 6c-.77.35-1.6.58-2.46.69a4.25 4.25 0 0 0 1.86-2.35 8.52 8.52 0 0 1-2.7 1.03 4.24 4.24 0 0 0-7.22 3.87A12.04 12.04 0 0 1 3.1 4.9a4.22 4.22 0 0 0 1.31 5.66 4.2 4.2 0 0 1-1.92-.53v.05a4.24 4.24 0 0 0 3.4 4.16 4.25 4.25 0 0 1-1.91.07 4.24 4.24 0 0 0 3.95 2.93A8.5 8.5 0 0 1 2 19.54a12.02 12.02 0 0 0 6.51 1.91c7.82 0 12.1-6.48 12.1-12.1l-.01-.55A8.64 8.64 0 0 0 22.46 6z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($instagram)
                                <a href="{{ $instagram }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full transition border border-[#E1306C] bg-[#E1306C] text-white hover:bg-white hover:text-[#E1306C]"
                                    aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.4.4.6.2 1 .4 1.4.8.4.4.7.8.8 1.4.2.5.3 1.2.4 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.4-.2.6-.4 1-.8 1.4-.4.4-.8.7-1.4.8-.5.2-1.2.3-2.4.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.4-.4-.6-.2-1-.4-1.4-.8-.4-.4-.7-.8-.8-1.4-.2-.5-.3-1.2-.4-2.4C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.4.2-.6.4-1 .8-1.4.4-.4.8-.7 1.4-.8.5-.2 1.2-.3 2.4-.4C8.4 2.2 8.8 2.2 12 2.2m0 1.8c-3.1 0-3.5 0-4.7.1-1 .1-1.5.2-1.9.3-.5.2-.8.3-1.1.6-.3.3-.5.6-.6 1.1-.1.4-.3.9-.3 1.9-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1 .2 1.5.3 1.9.2.5.3.8.6 1.1.3.3.6.5 1.1.6.4.1.9.3 1.9.3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1 0 1.5-.2 1.9-.3.5-.2.8-.3 1.1-.6.3-.3.5-.6.6-1.1.1-.4.3-.9.3-1.9.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c0-1-.2-1.5-.3-1.9-.2-.5-.3-.8-.6-1.1-.3-.3-.6-.5-1.1-.6-.4-.1-.9-.3-1.9-.3-1.2-.1-1.6-.1-4.7-.1m0 2.3a5.7 5.7 0 1 1 0 11.4 5.7 5.7 0 0 1 0-11.4m0 1.8a3.9 3.9 0 1 0 0 7.8 3.9 3.9 0 0 0 0-7.8M17.6 6a1.3 1.3 0 1 0 0 2.6 1.3 1.3 0 0 0 0-2.6z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($linkedin)
                                <a href="{{ $linkedin }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full transition border border-[#0077B5] bg-[#0077B5] text-white hover:bg-white hover:text-[#0077B5]"
                                    aria-label="LinkedIn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M6.94 6.5a2.19 2.19 0 1 1-4.38 0 2.19 2.19 0 0 1 4.38 0M2.88 8.82h3.82v12.3H2.88zM9.08 8.82h3.66v1.68h.05c.51-.96 1.76-1.98 3.62-1.98 3.88 0 4.6 2.55 4.6 5.86v6.74h-3.82v-5.98c0-1.43-.03-3.28-2-3.28-2 0-2.3 1.56-2.3 3.17v6.09H9.08z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($youtube)
                                <a href="{{ $youtube }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full transition border border-[#FF0000] bg-[#FF0000] text-white hover:bg-white hover:text-[#FF0000]"
                                    aria-label="YouTube">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 0 0-2.1 2.1C0 8.1 0 12 0 12s0 3.9.6 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.5 15.5v-7l6 3.5-6 3.5z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                    {{-- VISITORS COUNTER (Desktop) --}}
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[11px] font-bold text-[#013954] uppercase tracking-wide">
                            Total Visitors:
                        </span>
                        <a href="#" target="_blank">
                            <img src="https://hitwebcounter.com/counter/counter.php?page=21461883&style=0030&nbdigits=5&type=page&initCount=9999"
                                loading="lazy" title="Free Tools" Alt="Free Tools" border="0" />
                        </a>
                    </div>
                </div>

                {{-- MOBILE HAMBURGER BUTTON --}}
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 text-[#013954] transition rounded-lg hover:bg-[#013954]/10 focus:outline-none focus:ring-2 focus:ring-[#013954]">
                        {{-- Hamburger Icon --}}
                        <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        {{-- Close Icon --}}
                        <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-8 h-8"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </header>

    {{-- MOBILE MENU DRAWER (Slide down) --}}
    <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="overflow-y-auto max-h-[85vh] bg-white shadow-xl lg:hidden absolute top-full left-0 w-full border-t-2 border-[#013954] z-[999]">

        {{-- Mobile Music Player --}}
        <div class="w-full bg-[#013954]/5 border-b border-[#013954]/10 py-3 px-4 flex items-center justify-between"
            x-data="{
                playing: false,
                audio: null,
                url: '{{ asset('storage/' . $backgroundAudio) }}',
                toggleMusic() {
                    if (!this.audio) {
                        this.audio = new Audio(this.url);
                        this.audio.addEventListener('ended', () => this.playing = false);
                    }
                    if (this.playing) {
                        this.audio.pause();
                        this.playing = false;
                    } else {
                        this.audio.play();
                        this.playing = true;
                    }
                }
             }">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-[#013954] uppercase tracking-wide">College Song</span>
                <div x-show="playing" x-cloak class="flex gap-[2px] h-3 items-end">
                    <span class="w-[2px] bg-[#013954] animate-[music-bar_0.5s_ease-in-out_infinite]"></span>
                    <span class="w-[2px] bg-[#013954] animate-[music-bar_0.7s_ease-in-out_infinite]"></span>
                    <span class="w-[2px] bg-[#013954] animate-[music-bar_0.4s_ease-in-out_infinite]"></span>
                </div>
            </div>
            <button @click="toggleMusic()"
                class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-[#013954]/20 text-[#013954] shadow-sm active:scale-95 transition-all">
                <span class="text-xs font-bold" x-text="playing ? 'Pause' : 'Play'"></span>
            </button>
        </div>

        {{-- Mobile Menu Items --}}
        <ul class="flex flex-col py-2 pb-6">
            @foreach ($menus as $menu)
                <li x-data="{ openSub: false }" class="border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center justify-between w-full pr-4 bg-white">
                        <a href="{{ $menu->link }}"
                            class="flex-1 px-4 py-3 text-base font-bold text-[#013954] uppercase hover:bg-[#013954]/5 transition">
                            {{ getMenuLabel($menu->title) }}
                        </a>
                        @if ($menu->children->count())
                            <button @click="openSub = !openSub"
                                class="p-3 text-[#013954] hover:bg-[#013954]/10 rounded-full transition">
                                <svg :class="openSub ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-300"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                    @if ($menu->children->count())
                        <div x-show="openSub" x-cloak x-collapse class="bg-[#013954]/5 border-t border-gray-100 pb-2">
                            {!! renderMobileRecursive($menu->children) !!}
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
        {{-- VISITORS COUNTER (Mobile Menu Footer) --}}
        <div class="flex items-center justify-center w-full gap-3 py-4 border-t border-gray-100 bg-gray-50">
            <span class="text-xs font-bold text-[#013954] uppercase tracking-wide">
                Total Visitors:
            </span>
            <a href="#" target="_blank">
                <img src="https://hitwebcounter.com/counter/counter.php?page=21461883&style=0030&nbdigits=5&type=page&initCount=9999"
                    loading="lazy" title="Free Tools" Alt="Free Tools" border="0" />
            </a>
        </div>
    </div>
</div>

<style>
    .announcement-label {
        clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
        font-family: "Poppins", "Open Sans", sans-serif;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        margin-right: -3px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    /* Marquee wrapper */
    .marquee {
        overflow: hidden;
        white-space: nowrap;
        width: 100%;
    }

    /* Marquee track */
    .track {
        display: inline-flex;
        gap: 3rem;
        animation: marquee 60s linear infinite;
        will-change: transform;
    }

    .track span {
        display: inline-block;
        white-space: nowrap;
    }

    /* Links inside marquee */
    .marquee-link {
        /* color: #1E90FF; */
        color: #0000EE;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s, text-decoration 0.3s;
    }

    .marquee-link:hover {
        color: #0A1F44;
        text-decoration: underline;
    }

    /* Animation */
    @keyframes marquee {
        0% {
            transform: translate3d(0, 0, 0);
        }

        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }

    /* 📱 Responsive Scaling */
    @media (max-width: 1024px) {
        .announcement-label {
            clip-path: polygon(0 0, 92% 0, 100% 50%, 92% 100%, 0 100%);
        }
    }

    @media (max-width: 768px) {
        .announcement-label {
            clip-path: polygon(0 0, 94% 0, 100% 50%, 94% 100%, 0 100%);
            font-size: 13px;
            /* padding: 6px 16px; */
        }

        .track {
            gap: 2rem;
            animation-duration: 30s;
        }
    }

    @media (max-width: 480px) {
        .announcement-label {
            clip-path: polygon(0 0, 96% 0, 100% 50%, 96% 100%, 0 100%);
            font-size: 12px;
            /* padding: 5px 14px; */
        }

        .track {
            gap: 1.5rem;
            animation-duration: 35s;
        }
    }
</style>