@php
    // Mobile menu ke liye $menus query aur $topBannerImage, dono yahan define kiye gaye hain
    use App\Models\Menu;
    // Data fetching logic ko original rakha gaya hai jaisa ki aapne kaha.
    $menus = Menu::where('status', 1)->whereNull('parent_id')->with('childrenRecursive')->orderBy('order')->get();
    $topBannerImage = setting('top_banner_image');
@endphp

{{-- Root div mobile menu state (open/close) ko control karega --}}
<div x-data="{ open: false }" class="relative z-40 font-inter">

    {{-- HEADER/TOP BAR (Fixed height aur professional alignment) --}}

    <header class="w-full bg-white shadow-md border-b border-gray-200">
        {{-- Container padding ko responsive banaya gaya hai (sm:px-6 lg:px-8) --}}
        <div class="container mx-auto flex items-center justify-between px-6 sm:px-8 lg:px-20 py-2">
            @if ($topBannerImage)
                {{-- Left: Banner/Logo Image (Asset path use kiya gaya hai) --}}
                <div class="flex-shrink-0 transform transition-transform duration-300 ease-in-out text-center sm:text-left">
                    {{-- Image height ko mobile ke liye chhota kiya gaya hai (h-12) --}}
                    <img src="{{ asset('storage/' . $topBannerImage) }}" alt="Top Banner"
                        class="h-12 sm:h-16 md:h-20 lg:h-24 object-contain object-center sm:object-left mx-auto sm:mx-0">
                </div>
            @endif
            {{-- Center: Desktop Menu (Hidden on mobile) --}}
            {{-- Font styling ko nav tag se hata diya gaya hai taaki 'a' tags use handle kar sakein --}}
            <nav class="hidden lg:flex items-center h-full space-x-0">
                {{-- NOTE: Links ko new styling di gayi hai: text-black, text-xs, font-medium, uppercase --}}
                @foreach ($menus as $menu)
                    @php
                        $order = (string) $menu->order;

                        // Show only items starting with "100"
                        if (!str_starts_with($order, '100')) {
                            continue;
                        }
                    @endphp

                    <a href="{{ $menu->link }}"
                        class="h-full flex items-center px-2 border-b-2 border-transparent text-black font-medium text-xs uppercase hover:border-red-600 hover:text-red-600 transition duration-200">
                        {{ $menu->title }}</a>
                @endforeach

            </nav>

            {{-- Right: MOBILE MENU BUTTON (Vertically centered) --}}
            <div class="flex items-center lg:hidden">
                <button @click="open = !open"
                    class="p-2 text-indigo-700 transition rounded-lg hover:bg-gray-100 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                    {{-- Hamburger Icon (Closed state) --}}
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    {{-- Close Icon (Open state) --}}
                    <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>



        {{-- MOBILE MENU DROPDOWN (Sleek, Multi-level Design with smooth transitions) --}}
        {{-- Is part ko change nahi kiya gaya hai, jaisa aapne kaha --}}
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4"
            class="overflow-hidden bg-white shadow-xl lg:hidden absolute top-full left-0 w-full rounded-b-lg border-t-2 border-red-600">
            <ul class="flex flex-col py-2">
                @foreach ($menus as $menu)
                    {{-- Level 1 Item: Bold, Primary Color, Clear Button/Link --}}
                    <li x-data="{ openSub: false }" class="border-b border-gray-100 last:border-b-0">
                        @if ($menu->children->count())
                            {{-- Level 1 Toggle Button --}}
                            <button @click="openSub = !openSub"
                                class="flex justify-between items-center w-full px-4 py-3 text-base font-bold text-indigo-700 uppercase hover:bg-red-50 hover:text-red-600 transition">
                                {{ $menu->title }}
                                <svg :class="openSub ? 'rotate-180 text-red-600' : 'text-indigo-500'"
                                    class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>
                        @else
                            {{-- Level 1 Link --}}
                            <a href="{{ $menu->link }}"
                                class="block w-full px-4 py-3 text-base font-bold text-indigo-700 uppercase hover:bg-red-50 hover:text-red-600 transition">
                                {{ $menu->title }}
                            </a>
                        @endif

                        {{-- Sub-Menu (Level 2/3/4) --}}
                        @if ($menu->children->count())
                            <div x-show="openSub" x-cloak x-transition class="bg-gray-50 border-l-4 border-red-600">
                                {{-- Left
                                Border for Visual Hierarchy --}}
                                @foreach ($menu->children as $child)
                                    {{-- Level 2 Item: Indented, Medium Text --}}
                                    <div x-data="{ openChild: false }" class="pl-4">
                                        <div class="flex items-center justify-between border-b border-gray-100 last:border-b-0">
                                            <a href="{{ $child->link }}"
                                                class="block flex-1 px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-600 transition capitalize truncate">
                                                {{ $child->title }}
                                            </a>
                                            @if ($child->children->count())
                                                <button @click="openChild = !openChild"
                                                    class="p-2 mr-2 text-gray-500 hover:text-red-600 transition">
                                                    <svg :class="openChild ? 'rotate-180 text-red-600' : ''"
                                                        class="w-4 h-4 transition-transform duration-300" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M6 9l6 6 6-6"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Sub-Sub-Menu (Level 3 + Level 4) --}}
                                        @if ($child->children->count())
                                            <div x-show="openChild" x-cloak x-transition class="bg-white border-l-2 border-red-300">
                                                {{--
                                                Deeper Left Border --}}
                                                @foreach ($child->children as $subchild)
                                                    {{-- Level 3 Link/Group Header: Slightly smaller, different color --}}
                                                    <a href="{{ $subchild->link ?? '#' }}"
                                                        class="block pl-4 pr-4 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-gray-100 hover:text-red-600 transition capitalize">
                                                        {{ $subchild->title }}
                                                    </a>

                                                    @if ($subchild->children->count())
                                                        {{-- Level 4 Nested List: Smallest text, indented further --}}
                                                        <ul
                                                            class="bg-gray-50 py-1 pl-8 text-xs space-y-0.5 border-t border-dashed border-gray-200">
                                                            @foreach ($subchild->children as $subsubchild)
                                                                <li>
                                                                    <a href="{{ $subsubchild->link }}"
                                                                        class="hover:text-red-600 block text-gray-600 transition p-0.5">
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
</header>
