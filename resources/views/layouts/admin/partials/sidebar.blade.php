<aside
    class="fixed inset-y-0 left-0 z-20 transition duration-300 transform bg-white border-r w-72 lg:translate-x-0 lg:static lg:inset-0"
    :class="{'-translate-x-72': !sidebarOpen}">
    <div class="flex items-center justify-between px-6 py-4 border-b">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('storage/' . setting('college_logo')) }}" alt="logo" class="object-contain w-10 h-10">

            <div>
                {{-- <h1 class="text-lg font-semibold">{{ config('app.name', 'College') }}</h1> --}}
                <h1 class="text-lg font-semibold">{{ setting('college_name') }}</h1>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
        </a>
        <button class="p-2 rounded-md lg:hidden hover:bg-gray-100" @click="sidebarOpen = false">
            <i class="text-gray-600 fas fa-times"></i>
        </button>
    </div>

    <nav class="px-4 py-6 space-y-1">
        {{-- Example menu items. Replace with dynamic $menus if needed. --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : '' }}">
            <i class="w-4 fas fa-tachometer-alt"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.trust.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 {{ request()->routeIs('admin.trust*') ? 'bg-gray-100' : '' }}">
            <i class="w-4 fas fa-handshake"></i>
            The Trust
        </a>

        <a href="{{ route('admin.roles-permissions.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 {{ request()->routeIs('admin.roles-permissions*') ? 'bg-gray-100' : '' }}">
            <i class="w-4 fas fa-user-shield"></i>
            Roles & Permissions
        </a>

        <a href="{{ route('admin.menus.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 {{ request()->routeIs('admin.menus*') ? 'bg-gray-100' : '' }}">
            <i class="w-4 fas fa-bars"></i>
            Menus
        </a>

        {{-- Divider --}}
        <div class="my-3 border-t"></div>

        <a href="{{ route('admin.website-settings.index') }}"
            class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg hover:bg-gray-100">
            <i class="w-4 fas fa-cog"></i>
            Site Settings
        </a>

    </nav>

    <div class="absolute bottom-0 w-full p-4 border-t">
        <div class="flex items-center gap-3">
            <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
                class="object-cover w-10 h-10 rounded-full">
            <div>
                <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                <a href="{{ route('logout') }}" class="text-xs text-red-500">Sign out</a>
            </div>
        </div>
    </div>
</aside>