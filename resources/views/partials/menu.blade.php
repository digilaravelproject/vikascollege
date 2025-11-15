@php
    use App\Models\Menu;
    $menus = Menu::where('status', 1)->whereNull('parent_id')->with('childrenRecursive')->orderBy('order')->get();
@endphp
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    /* 🎨 Styling for a cleaner look */
    nav {
        font-family: 'Roboto', sans-serif !important;
    }

    /* Standard Dropdown slide-down effect (for clean open/close) */
    .animate-slideDown {
        animation: slideDown 0.2s ease-out;
        transform-origin: top;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: scaleY(0.95) translateY(-5px);
        }
        to {
            opacity: 1;
            transform: scaleY(1) translateY(0);
        }
    }

    /* General Fade-in for Mega Menu content */
    .animate-fadeIn {
        animation: fadeIn 0.25s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    [x-cloak] {
        display: none !important;
    }

    /* Mega Menu Specific Styling - Adjusted to match reference image */
    .mega-menu-tab-link {
        /* Styling for the Level 2 tabs on the left, matching Somaiya's look */
        padding: 10px 16px;
        font-size: 0.9rem;
        /* text-sm */
        font-weight: 500;
        /* font-medium */
    }
</style>

<nav x-data="{ open: false, isScrolled: false }" @scroll.window="isScrolled = (window.scrollY >= 120)" class="shadow-md hidden lg:flex sticky top-0 z-50 bg-white font-roboto">
    <div class="container px-4 mx-auto">
        <div class="flex items-center justify-between py-4">
            {{-- DESKTOP NAVIGATION --}}
            <div class="justify-center flex-1 hidden lg:flex">
                <ul class="flex items-center space-x-4 whitespace-nowrap">
                    @foreach ($menus as $menu)
                        @php
                            // Convert to string
                            $order = (string) $menu->order;

                            // Skip items starting with "00"
                            if (str_starts_with($order, '100')) {
                                continue;
                            }
                        @endphp

                        {{--  logic check: is this a mega menu? --}}
                        @php
                            $hasChildren = $menu->children->count() > 0;
                            $isMegaMenu = ($menu->children->count() >= 6) ||
                                          ($menu->children->count() == 1 && $menu->children->first() && $menu->children->first()->children->count() >= 6);
                            $isStandardDropdown = $hasChildren && !$isMegaMenu;

                            // 🐞 BUG FIX: 'relative' class sirf standard dropdown par lagegi.
                            // Mega menu (fixed position) aur normal links ko 'relative' ki zaroorat nahi hai.
                            $liClass = 'group';
                            if ($isStandardDropdown) {
                                $liClass .= ' relative'; // Standard dropdowns NEED relative
                            }
                        @endphp

                        {{-- Class 'relative' ab dynamic hai --}}
                        <li x-data="{ openSub: false, activeTabIndex: 0 }" class="{{ $liClass }}">

                            {{-- Main Link (Level 1) --}}
                            <a href="{{ $menu->link }}"
                                {{-- Only add hover listeners if there are children --}}
                                @if ($hasChildren)
                                    @mouseenter="openSub = true"
                                    @mouseleave="openSub = false"
                                @endif
                                class="relative flex items-center gap-1 text-sm font-medium text-gray-700 uppercase transition duration-200
                                            hover:text-red-600
                                            after:content-[''] after:absolute after:left-0 after:-bottom-1.5 after:h-[2px] after:bg-red-600
                                            after:transition-all after:duration-300
                                            {{-- Active state check --}}
                                            {{ Request::is(trim(parse_url($menu->url, PHP_URL_PATH), '/')) ? 'after:w-full text-red-600' : 'after:w-0 hover:after:w-full' }}">

                                {{ $menu->title }}

                                {{-- Show arrow only if there are children --}}
                                @if ($hasChildren)
                                    <svg class="w-4 h-4 text-gray-500 transition" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                @endif
                            </a>

                            {{-- 🛑 MEGA MENU (fixed position) --}}
                            @if ($isMegaMenu)
                                {{-- Iska parent <li> 'relative' nahi hai, isliye ab jump nahi karega --}}
                                <div x-show="openSub" x-cloak @mouseenter="openSub = true" @mouseleave="openSub = false"
                                    x-transition.duration.200
                                    class="fixed left-0 z-40 w-full text-gray-800"
                                    :class="isScrolled ? 'top-[60px]' : 'top-[180px]'">
                                    <div class="mx-auto overflow-hidden bg-white shadow-xl max-w-7xl border-b border-gray-200">
                                        <div class="flex">

                                            {{-- 1. Left Tabs Area (Level 2: The selector list) --}}
                                            <div
                                                class="w-1/4 bg-gray-100 border-r border-gray-200 py-2 max-h-[70vh] overflow-y-auto">
                                                <ul class="space-y-0">
                                                    @foreach ($menu->children as $index => $tabItem)
                                                        <li>
                                                            <a href="{{ $tabItem->link }}"
                                                                @click.prevent="activeTabIndex = {{ $index }}"
                                                                @mouseenter="activeTabIndex = {{ $index }}"
                                                                :class="activeTabIndex === {{ $index }} ?
                                                                        'bg-white text-red-600 font-medium' :
                                                                        'text-gray-700 hover:bg-gray-200'"
                                                                class="block mega-menu-tab-link transition duration-150 capitalize">
                                                                {{ $tabItem->title }}

                                                                {{-- Right Arrow for selected tab --}}
                                                                <span x-show="activeTabIndex === {{ $index }}" class="float-right text-red-600">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                        stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path d="M9 5l7 7-7 7"></path>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            {{-- 2. Right Content Area (Level 3/4 Grouping) --}}
                                            <div class="w-3/4 p-6 bg-white max-h-[70vh] overflow-y-auto">
                                                @foreach ($menu->children as $index => $tabItem)
                                                    <div x-show="activeTabIndex === {{ $index }}" x-cloak
                                                        x-transition.opacity.duration.300 class="space-y-4 animate-fadeIn">

                                                        {{-- Grid Layout for Content Links (Level 3) --}}
                                                        <div class="grid grid-cols-3 gap-x-6 gap-y-2">
                                                            @foreach ($tabItem->children as $sub)

                                                                {{-- If L3 has L4 children, show L3 as bold header --}}
                                                                @if($sub->children->count() > 0)
                                                                    <div class="py-1">
                                                                        <a href="{{ $sub->link }}" class="block text-sm font-bold text-gray-800 hover:text-red-600">
                                                                            {{ $sub->title }}
                                                                        </a>
                                                                        <ul class="mt-1 space-y-1">
                                                                            @foreach($sub->children as $subsub)
                                                                                <li>
                                                                                    <a href="{{ $subsub->link }}" class="block text-sm font-normal text-gray-600 hover:text-red-600 hover:underline">
                                                                                        {{ $subsub->title }}
                                                                                    </a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @else
                                                                {{-- If L3 has NO children, show L3 as simple link --}}
                                                                    <a href="{{ $sub->link }}"
                                                                        class="block text-sm font-normal text-gray-700 hover:text-red-600 hover:underline transition duration-150">
                                                                        {{ $sub->title }}
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- 🛑 STANDARD DROPDOWN (absolute position) --}}
                            @elseif ($isStandardDropdown)
                                {{-- Iska parent <li> 'relative' hai, isliye yeh sahi se position hoga --}}
                                <div x-show="openSub"
                                    x-cloak @mouseenter="openSub = true" @mouseleave="openSub = false"
                                    x-transition:enter="animate-slideDown" x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    class="absolute left-0 mt-1 text-gray-800 bg-white border border-gray-100 rounded-lg shadow-xl top-full origin-top">

                                    <div class="p-2 min-w-[220px]">
                                        <ul class="flex flex-col py-1">
                                            @foreach ($menu->children as $child)
                                                <li x-data="{ openChild: false }" @mouseenter="openChild = true"
                                                    @mouseleave="openChild = false" class="relative">

                                                    <a href="{{ $child->link }}"
                                                        class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-red-600 hover:text-white transition duration-150 capitalize rounded-md">
                                                        <span>{{ $child->title }}</span>

                                                        @if ($child->children->count())
                                                            <svg class="w-3 h-3 text-gray-400 group-hover:text-white"
                                                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        @endif
                                                    </a>

                                                    {{-- Level 3 Dropdown --}}
                                                    @if ($child->children->count())
                                                        <ul x-show="openChild" x-cloak x-transition.opacity.duration.150
                                                            class="absolute left-full top-0 mt-0 bg-white border border-gray-200 rounded-lg shadow-md min-w-[200px] z-50 p-1">
                                                            @foreach ($child->children as $subchild)
                                                                <li>
                                                                    <a href="{{ $subchild->link }}"
                                                                        class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-red-600 hover:text-white transition duration-150 capitalize rounded-md">
                                                                        <span class="inline-block pl-2 border-l-2 border-red-300">
                                                                            {{ $subchild->title }}
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif {{-- End of MegaMenu / Standard Dropdown logic --}}

                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- MOBILE MENU BUTTON (Theme-matched) --}}
            <div class="flex justify-center flex-1 lg:hidden">
                <button @click="open = !open"
                    class="p-2 text-gray-700 transition rounded-md hover:text-red-600 focus:outline-none">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- MOBILE MENU DROPDOWN (Theme-matched) --}}
        <div x-show="open" x-cloak x-transition class="overflow-hidden text-gray-800 bg-white shadow-md lg:hidden">
            <ul class="flex flex-col py-2 space-y-1">
                @foreach ($menus as $menu)
                    <li x-data="{ openSub: false }" class="border-b border-gray-100">
                        {{-- Main Menu Item (Level 1) --}}
                        @if ($menu->children->count())
                            <button @click="openSub = !openSub"
                                class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold text-gray-800 uppercase hover:bg-gray-100">
                                {{ $menu->title }}
                                <svg :class="openSub ? 'rotate-180 text-red-600' : ''"
                                    class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>
                        @else
                            <a href="{{ $menu->link }}"
                                class="block w-full px-4 py-3 text-sm font-semibold text-gray-800 uppercase hover:bg-gray-100">
                                {{ $menu->title }}
                            </a>
                        @endif

                        {{-- Sub-Menu (Level 2, 3, 4) --}}
                        @if ($menu->children->count())
                            <div x-show="openSub" x-cloak x-transition class="bg-gray-50 border-l-4 border-gray-200">
                                @foreach ($menu->children as $child)
                                    <div x-data="{ openChild: false }">
                                        {{-- Level 2 Link/Toggle --}}
                                        <div class="flex items-center justify-between border-b border-gray-100 last:border-b-0">
                                            <a href="{{ $child->link }}"
                                                class="block flex-1 px-8 py-2 text-sm text-gray-700 hover:text-red-600 transition capitalize">
                                                {{ $child->title }}
                                            </a>
                                            @if ($child->children->count())
                                                <button @click="openChild = !openChild" class="p-2 mr-2">
                                                    <svg :class="openChild ? 'rotate-180 text-red-600' : 'text-gray-500'"
                                                        class="w-4 h-4 transition-transform" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M6 9l6 6 6-6"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Sub-Sub-Menu (Level 3 + Level 4) --}}
                                        @if ($child->children->count())
                                            <div x-show="openChild" x-cloak x-transition class="bg-white border-l-4 border-red-600">
                                                @foreach ($child->children as $subchild)
                                                    {{-- Level 3 Link/Group Header --}}
                                                    <a href="{{ $subchild->link ?? '#' }}"
                                                        class="block px-12 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 hover:text-red-600 capitalize">
                                                        {{ $subchild->title }}
                                                    </a>

                                                    @if ($subchild->children->count())
                                                        {{-- Level 4 Nested List (Indentation) --}}
                                                        <ul class="bg-gray-50 py-1 pl-14 text-xs space-y-0.5">
                                                            @foreach ($subchild->children as $subsubchild)
                                                                <li>
                                                                    <a href="{{ $subsubchild->link }}" class="hover:text-red-600 block">
                                                                        — {{ $subsubchild->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
