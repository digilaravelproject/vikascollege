<header class="w-full bg-white border-b">
    <div class="flex items-center justify-between gap-4 px-4 lg:px-8 py-3">
        <div class="flex items-center gap-3">
            <button class="p-2 rounded-md hover:bg-gray-100 lg:hidden" @click="sidebarOpen = true">
                <i class="fas fa-bars"></i>
            </button>

            <div class="text-sm text-gray-500">@yield('page_title', 'Dashboard')</div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Search --}}
            <div class="hidden md:block">
                <input type="search" placeholder="Search..."
                    class="w-64 rounded-full border px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-accent">
            </div>

            {{-- Notifications --}}
            <button class="p-2 rounded-md hover:bg-gray-100">
                <i class="fas fa-bell"></i>
            </button>

            {{-- User dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-md">
                    <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
                        class="w-8 h-8 rounded-full object-cover">
                    <span class="hidden md:inline text-sm">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-44 bg-white border rounded-md shadow-lg py-2">
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm text-red-500">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>