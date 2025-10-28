@php
    use App\Models\Menu;
    $menus = Menu::where('status', 1)
        ->whereNull('parent_id')
        ->with('childrenRecursive')
        ->orderBy('order')
        ->get();
@endphp

<nav x-data="{ open: false }" class="shadow-md sticky top-0 z-50 bg-[#013954] uppercase font-sans">
    <div class="container px-4 mx-auto">
        <div class="flex items-center justify-between py-3">
            <!-- Desktop Menu -->
            <div class="justify-center flex-1 hidden lg:flex">
                <ul class="flex items-center space-x-6">
                    @foreach($menus as $menu)
                        <li x-data="{ openSub: false, activeLeftIndex: 0 }" class="relative group">
                            @if($menu->children->count())
                             <a href="{{ $menu->url }}"
   @mouseenter="openSub = true"
   @mouseleave="openSub = false"
   class="relative flex items-center gap-1 text-xs font-medium text-gray-100 uppercase transition duration-200
          hover:text-[#35abe7]
          after:content-[''] after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:bg-[#35abe7]
          after:transition-all after:duration-300
          {{ Request::is(trim(parse_url($menu->url, PHP_URL_PATH), '/')) ? 'after:w-full text-[#35abe7]' : 'after:w-0 hover:after:w-full' }}">
    {{ $menu->title }}
    <svg class="w-4 h-4 text-gray-100 transition group-hover:text-gray-300" fill="none"
        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M6 9l6 6 6-6"></path>
    </svg>
</a>


                                <!-- Submenu / Mega Menu -->

                                @if($menu->children->count() >= 6)
                                <div x-show="openSub" @mouseenter="openSub = true" @mouseleave="openSub = false" x-transition class="fixed left-0 z-40 w-full text-gray-800 shadow-lg rounded-b-xl">
                                        <!-- ADVANCED MEGA MENU (from first version) -->
                                     <div class="mx-auto overflow-hidden text-gray-800 bg-white rounded-b-lg shadow-lg max-w-7xl">


                                            <div class="flex">
                                                <!-- LEFT COLUMN -->
                                                <div class="w-1/3 border-r border-gray-100">
                                                    <ul class="divide-y divide-gray-100">
                                                        @foreach($menu->children as $index => $leftItem)
                                                            <li>
                                                                <button
                                                                    @mouseenter="activeLeftIndex = {{ $index }}"
                                                                    @click="activeLeftIndex = {{ $index }}"
                                                                    :class="activeLeftIndex === {{ $index }} ? 'bg-gray-50 text-[#013954]' : 'text-gray-700'"
                                                                    class="flex items-center justify-between w-full px-6 py-4 text-sm font-semibold text-left uppercase transition hover:bg-gray-50">
                                                                    <span>{{ $leftItem->title }}</span>
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                                                         viewBox="0 0 24 24">
                                                                        <path d="M6 9l6 6 6-6"></path>
                                                                    </svg>
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                <!-- RIGHT COLUMN -->
                                                <div class="w-2/3 p-6">
                                                    @foreach($menu->children as $index => $leftItem)
                                                        <div x-show="activeLeftIndex === {{ $index }}" x-cloak x-transition class="space-y-4">
                                                            @php $subs = $leftItem->children ?? collect(); $count = $subs->count(); @endphp

                                                            @if($count == 0)
                                                                <div class="text-sm text-gray-600">No items available.</div>
                                                            @elseif($count <= 6)
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    @foreach($subs as $sub)
                                                                        <a href="{{ $sub->url }}"
                                                                           class="block uppercase text-sm text-gray-700 hover:text-[#013954] transition">
                                                                            {{ $sub->title }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="grid grid-cols-3 gap-6">
                                                                    @foreach($subs as $sub)
                                                                        <div>
                                                                            <a href="{{ $sub->url }}" class="block text-sm font-semibold text-[#013954] hover:text-blue-700">
                                                                                {{ $sub->title }}
                                                                            </a>
                                                                            @if($sub->children && $sub->children->count())
                                                                                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                                                                    @foreach($sub->children as $subsub)
                                                                                        <li><a href="{{ $subsub->url }}" class="block uppercase hover:text-[#35abe7]">{{ $subsub->title }}</a></li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                    <div x-show="openSub" @mouseenter="openSub = true" @mouseleave="openSub = false" x-transition class="absolute left-0 mt-2 text-gray-800 bg-white shadow-lg top-full rounded-b-xl">

                                        <!-- REGULAR DROPDOWN -->
                                        <ul class="flex flex-col divide-y divide-gray-100 py-2 min-w-[240px]">
                                            @foreach($menu->children as $child)
                                                <li class="relative group">
                                                    <a href="{{ $child->url }}"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition">
                                                        {{ $child->title }}
                                                    </a>
                                                    @if($child->children->count())
                                                        <ul class="absolute left-full top-0 hidden group-hover:block bg-white border border-gray-100 rounded shadow-lg min-w-[200px]">
                                                            @foreach($child->children as $subchild)
                                                                <li>
                                                                    <a href="{{ $subchild->url }}"
                                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition">
                                                                        {{ $subchild->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @else
                            <a href="{{ $menu->url }}"
                                class="relative text-xs font-medium text-gray-100 transition
                                    hover:text-[#35abe7]
                                    after:content-[''] after:absolute after:left-0 after:-bottom-0.5 after:h-[2px]
                                    after:bg-[#35abe7] after:transition-all after:duration-300
                                    {{ Request::is(trim(parse_url($menu->url, PHP_URL_PATH), '/')) ? 'after:w-full text-[#35abe7]' : 'after:w-0 hover:after:w-full' }}">
                                {{ $menu->title }}
                            </a>

                            @endif
                        </li>
                @if (!$loop->last)
                        <span class="mx-2 text-gray-400">|</span>
                    @endif
                    @endforeach
                </ul>
            </div>

            <!-- Mobile Toggle -->
            <div class="flex justify-center flex-1 lg:hidden">
                <button @click="open = !open"
                    class="p-2 text-gray-100 transition rounded-md hover:text-gray-400 focus:outline-none">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown -->
        <div x-show="open" x-transition class="overflow-hidden text-gray-800 bg-white shadow-md lg:hidden">
            <ul class="flex flex-col py-2 space-y-1">
                @foreach($menus as $menu)
                    <li x-data="{ openSub: false }" class="border-b border-gray-100">
                        <button @click="openSub = !openSub"
                            class="flex justify-between w-full px-4 py-2 text-sm font-semibold text-gray-800 uppercase hover:bg-gray-50">
                            {{ $menu->title }}
                            @if($menu->children->count())
                                <svg :class="openSub ? 'rotate-180 text-blue-600' : ''"
                                    class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            @endif
                        </button>

                        @if($menu->children->count())
                            <div x-show="openSub" x-transition class="bg-gray-50">
                                @foreach($menu->children as $child)
                                    <a href="{{ $child->url }}"
                                        class="block px-8 py-2 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition">
                                        {{ $child->title }}
                                    </a>
                                    @if($child->children->count())
                                        @foreach($child->children as $subchild)
                                            <a href="{{ $subchild->url }}" class="block px-12 py-2 text-xs text-gray-600 hover:bg-blue-50">
                                                — {{ $subchild->title }}
                                            </a>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    nav { font-family: 'Roboto', sans-serif !important; }
    .animate-fadeIn { animation: fadeIn 0.25s ease-out; }
    @keyframes fadeIn { from {opacity: 0; transform: translateY(4px);} to {opacity: 1; transform: translateY(0);} }
</style>
