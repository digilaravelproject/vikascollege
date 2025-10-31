@php
    use App\Models\Menu;
    $menus = Menu::where('status', 1)->whereNull('parent_id')->with('childrenRecursive')->orderBy('order')->get();
@endphp

<nav x-data="{ open: false }" class="shadow-md sticky top-0 z-50 bg-[#013954] uppercase font-sans">
    <div class="container px-4 mx-auto">
        <div class="flex items-center justify-between py-3">
            <div class="justify-center flex-1 hidden lg:flex">
                <ul class="flex items-center space-x-6">
                    @foreach ($menus as $menu)
                        <li x-data="{ openSub: false, activeLeftIndex: 0 }" class="relative group">
                            @if ($menu->children->count())
                                <a href="{{ $menu->link }}" @mouseenter="openSub = true" @mouseleave="openSub = false"
                                    class="relative flex items-center gap-1 text-xs font-medium text-gray-100 uppercase transition duration-200
                                            hover:text-[#35abe7]
                                            after:content-[''] after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:bg-[#35abe7]
                                            after:transition-all after:duration-300
                                            {{-- YEH LINE NAHI BADALNI HAI --}}
                                            {{ Request::is(trim(parse_url($menu->url, PHP_URL_PATH), '/')) ? 'after:w-full text-[#35abe7]' : 'after:w-0 hover:after:w-full' }}">
                                    {{ $menu->title }}
                                    <svg class="w-4 h-4 text-gray-100 transition group-hover:text-gray-300" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </a>


                                @if ($menu->children->count() >= 6)
                                    <div x-show="openSub" @mouseenter="openSub = true" @mouseleave="openSub = false" x-transition
                                        class="fixed left-0 z-40 w-full text-gray-800 rounded-b-xl">
                                        <div
                                            class="mx-auto overflow-hidden text-gray-800 bg-white rounded-b-lg shadow-lg max-w-7xl">


                                            <div class="flex">
                                                <div class="w-1/3 border-r border-gray-100">
                                                    <ul class="divide-y divide-gray-100">
                                                        @foreach ($menu->children as $index => $leftItem)
                                                            <li>
                                                                <button @mouseenter="activeLeftIndex = {{ $index }}"
                                                                    @click="activeLeftIndex = {{ $index }}" :class="activeLeftIndex === {{ $index }} ?
                                                                                        'bg-gray-50 text-[#013954]' : 'text-gray-700'"
                                                                    class="flex items-center justify-between w-full px-6 py-4 text-sm font-semibold text-left uppercase transition hover:bg-gray-50">
                                                                    <span>{{ $leftItem->title }}</span>
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                                        stroke-width="2" viewBox="0 0 24 24">
                                                                        <path d="M6 9l6 6 6-6"></path>
                                                                    </svg>
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                <div class="w-2/3 p-6">
                                                    @foreach ($menu->children as $index => $leftItem)
                                                        <div x-show="activeLeftIndex === {{ $index }}" x-cloak x-transition
                                                            class="space-y-4">
                                                            @php
                                                                $subs = $leftItem->children ?? collect();
                                                                $count = $subs->count();
                                                            @endphp

                                                            @if ($count == 0)
                                                                <div class="text-sm text-gray-600">No items available.
                                                                </div>
                                                            @elseif($count <= 6)
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    @foreach ($subs as $sub)
                                                                        <a href="{{ $sub->link }}"
                                                                            class="block uppercase text-sm text-gray-700 hover:text-[#013954] transition">
                                                                            {{ $sub->title }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="grid grid-cols-3 gap-6">
                                                                    @foreach ($subs as $sub)
                                                                        <div>
                                                                            <a href="{{ $sub->link }}"
                                                                                class="block text-sm font-semibold text-[#013954] hover:text-blue-700">
                                                                                {{ $sub->title }}
                                                                            </a>
                                                                            @if ($sub->children && $sub->children->count())
                                                                                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                                                                    @foreach ($sub->children as $subsub)
                                                                                        <li><a href="{{ $subsub->link }}"
                                                                                                class="block uppercase hover:text-[#35abe7]">{{ $subsub->title }}</a>
                                                                                        </li>
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
                                    </div>
                                @else
                                    <div x-show="openSub" @mouseenter="openSub = true" @mouseleave="openSub = false" x-transition
                                        class="absolute left-0 mt-1 text-gray-800 bg-white border border-gray-100 rounded-lg shadow-lg top-full">

                                        <ul class="flex flex-col py-1 min-w-[220px]">
                                            @foreach ($menu->children as $child)
                                                <li x-data="{ openChild: false }" @mouseenter="openChild = true"
                                                    @mouseleave="openChild = false" class="relative">

                                                    <a href="{{ $child->link }}"
                                                        class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition duration-150">
                                                        <span>{{ $child->title }}</span>

                                                        @if ($child->children->count())
                                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" fill="none"
                                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        @endif
                                                    </a>

                                                    @if ($child->children->count())
                                                        <ul x-show="openChild" x-cloak x-transition.opacity.duration.150
                                                            class="absolute left-full top-0 mt-0 bg-gray-50 border border-gray-200 rounded-md shadow-md min-w-[200px] z-50"
                                                            style="display: none;">
                                                            @foreach ($child->children as $subchild)
                                                                <li>
                                                                    <a href="{{ $subchild->link }}"
                                                                        class="block px-5 py-1.5 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition duration-150">
                                                                        <span class="inline-block pl-1 border-l-2 border-[#35abe7]">
                                                                            {{ $subchild->title }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @else
                                <a href="{{ $menu->link }}"
                                    class="relative text-xs font-medium text-gray-100 transition
                                                    hover:text-[#35abe7]
                                                    after:content-[''] after:absolute after:left-0 after:-bottom-0.5 after:h-[2px]
                                                    after:bg-[#35abe7] after:transition-all after:duration-300
                                                    {{-- YEH LINE NAHI BADALNI HAI --}}
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

        <div x-show="open" x-transition class="overflow-hidden text-gray-800 bg-white shadow-md lg:hidden">
            <ul class="flex flex-col py-2 space-y-1">
                @foreach ($menus as $menu)
                    <li x-data="{ openSub: false }" class="border-b border-gray-100">
                        {{--
                        Mobile menu mein, humein button ko alag aur link ko alag handle karna chahiye.
                        Lekin aapke current setup mein, button hi link hai.
                        Hum yahan check karenge: agar child nahi hain, toh ise link banayenge, varna button.
                        --}}

                        @if ($menu->children->count())
                            {{-- Agar children hain, toh yeh ek dropdown button hai --}}
                            <button @click="openSub = !openSub"
                                class="flex justify-between w-full px-4 py-2 text-sm font-semibold text-gray-800 uppercase hover:bg-gray-50">
                                {{ $menu->title }}
                                <svg :class="openSub ? 'rotate-180 text-blue-600' : ''"
                                    class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>
                        @else
                            {{-- Agar children nahi hain, toh yeh ek seedha link hai --}}
                            <a href="{{ $menu->link }}"
                                class="flex justify-between w-full px-4 py-2 text-sm font-semibold text-gray-800 uppercase hover:bg-gray-50">
                                {{ $menu->title }}
                            </a>
                        @endif


                        @if ($menu->children->count())
                            <div x-show="openSub" x-transition class="bg-gray-50">
                                @foreach ($menu->children as $child)
                                    <a href="{{ $child->link }}"
                                        class="block px-8 py-2 text-sm text-gray-700 hover:bg-[#013954] hover:text-white transition">
                                        {{ $child->title }}
                                    </a>
                                    @if ($child->children->count())
                                        @foreach ($child->children as $subchild)
                                            <a href="{{ $subchild->link }}" class="block px-12 py-2 text-xs text-gray-600 hover:bg-blue-50">
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
    nav {
        font-family: 'Roboto', sans-serif !important;
    }

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
</style>