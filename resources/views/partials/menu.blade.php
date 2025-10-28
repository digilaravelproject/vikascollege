@php
    $menus = \App\Models\Menu::where('status', 1)
        ->whereNull('parent_id')
        ->with('children')
        ->orderBy('order')
        ->get();
@endphp

<nav x-data="{ open: false }" class=" shadow-md sticky top-0 z-50 bg-[#013954] uppercase font-sans">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-3">

            <!-- Left spacer -->
            {{-- <div class="flex-1"></div> --}}

            <!-- Center Menu (Desktop + Mobile unified) -->
            <div class="flex-1 flex justify-center">
                <ul class="hidden lg:flex items-center space-x-6 ">
                    @foreach($menus as $menu)
                        @if($menu->children->count())
                            <li class="relative group">
                                <button
                                    class="flex items-center gap-1 uppercase text-gray-100 font-medium hover:text-gray-600 transition duration-200">
                                    {{ $menu->title }}
                                    <svg class="w-4 h-4 text-gray-100 group-hover:text-gray-600 transition" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </button>

                                <div
                                    class="absolute left-0 top-full hidden group-hover:block bg-gray-100 text-gray-800 rounded-md shadow-lg min-w-[180px] animate-fadeIn transition-all duration-200">
                                    @foreach($menu->children as $child)
                                        <a href="{{ $child->url }}"
                                            class="no-underline outline-none focus:outline-none active:outline-none focus:ring-0 active:ring-0 block px-4 py-2 text-sm rounded-md shadow-lg hover:bg-gray-400 transition">
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @else
                            <li>
                                <a href="{{ $menu->url }}"
                                    class="no-underline outline-none focus:outline-none active:outline-none focus:ring-0 active:ring-0 text-gray-100 font-medium hover:text-gray-600 transition duration-200">
                                    {{ $menu->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Mobile toggle -->
            <div class="flex-1 flex justify-end lg:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none transition">
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

        <!-- Mobile dropdown menu -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-3"
            class="lg:hidden bg-white shadow-md rounded-lg overflow-hidden">
            <ul class="flex flex-col py-2 space-y-1">
                @foreach($menus as $menu)
                    @if($menu->children->count())
                        <li x-data="{ openSub: false }" class="border-b border-gray-100">
                            <button @click="openSub = !openSub"
                                class="flex justify-between w-full px-4 py-2 text-gray-800 font-medium hover:bg-gray-50 transition">
                                {{ $menu->title }}
                                <svg :class="openSub ? 'rotate-180 text-blue-600' : ''"
                                    class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>
                            <div x-show="openSub" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1" class="bg-blue-600 text-white">
                                @foreach($menu->children as $child)
                                    <a href="{{ $child->url }}"
                                        class="no-underline outline-none focus:outline-none active:outline-none focus:ring-0 active:ring-0 block px-6 py-2 text-sm hover:bg-blue-700 transition">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    @else
                        <li>
                            <a href="{{ $menu->url }}"
                                class="no-underline outline-none focus:outline-none active:outline-none focus:ring-0 active:ring-0 block px-4 py-2 text-gray-800 font-medium hover:bg-gray-50 transition">
                                {{ $menu->title }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</nav>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    /* Navigation styling */
    nav {
        /* background: #013954;
        text-transform: uppercase;
        font-family: 'Poppins', sans-serif; */
        font-family: 'Roboto', sans-serif !important;
    }

    a {
        -webkit-tap-highlight-color: transparent;
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

    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }
</style>


{{-- <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    @foreach($menus as $menu)
    @if($menu->children->count())
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $menu->id }}" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            {{ $menu->title }}
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{ $menu->id }}">
            @foreach($menu->children as $child)
            <li><a class="dropdown-item" href="{{ $child->url }}">{{ $child->title }}</a></li>
            @endforeach
        </ul>
    </li>
    @else
    <li class="nav-item">
        <a class="nav-link" href="{{ $menu->url }}">{{ $menu->title }}</a>
    </li>
    @endif
    @endforeach
</ul> --}}


{{-- @include('partials.menu') --}}