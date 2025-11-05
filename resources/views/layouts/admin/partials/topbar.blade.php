<header class="w-full bg-white border-b border-gray-200">
    <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-8">

        <div class="flex items-center gap-3">
            <button
                class="p-2 rounded-md hover:bg-gray-100 lg:hidden focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @click="sidebarOpen = true">
                <span class="sr-only">Open sidebar</span>
                <i class="fas fa-bars"></i>
            </button>

            <div class="text-sm text-gray-500">
                @yield('page_title', 'Dashboard')
            </div>
        </div>

        <div class="flex items-center gap-3">

            <div class="hidden md:block">
                <input type="search" placeholder="Search..."
                    class="w-64 px-3 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button
                class="p-2 text-gray-500 rounded-md hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="sr-only">View notifications</span>
                <i class="fas fa-bell"></i>
            </button>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 p-1 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    @if (auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" class="object-cover w-8 h-8 rounded-full">
                    @else
                        <div
                            class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-white bg-indigo-600 rounded-full">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <span class="hidden text-sm text-gray-700 md:inline">{{ auth()->user()->name }}</span>
                    <i class="text-xs text-gray-500 fas fa-chevron-down"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 py-1 mt-2 bg-white border border-gray-200 rounded-md shadow-lg w-44"
                    style="display: none; z-index: 1000;">

                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Profile
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Settings
                    </a>

                    <div class="my-1 border-t border-gray-100"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
