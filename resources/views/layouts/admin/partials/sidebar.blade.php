<aside
    class="fixed inset-y-0 left-0 z-20 transition-transform duration-300 transform bg-white border-r border-gray-200 shadow-xl w-72 lg:static lg:inset-auto lg:translate-x-0 lg:shadow-none"
    :class="{'-translate-x-72': !sidebarOpen}">

    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('storage/' . setting('college_logo')) }}" alt="logo" class="object-contain w-10 h-10">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">{{ setting('college_name') }}</h1>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
        </a>
        <button class="p-2 rounded-md lg:hidden hover:bg-gray-100" @click="sidebarOpen = false">
            <i class="text-gray-600 fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                   {{ request()->routeIs('admin.dashboard')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="w-5 fas fa-fw fa-tachometer-alt"></i>
            Dashboard
        </a>

        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Content
        </h3>
        <div class="space-y-1">
            <a href="{{ route('admin.pagebuilder.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('admin.pagebuilder*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 fas fa-fw fa-file-alt"></i>
                Pages
            </a>
            <a href="{{ route('admin.trust.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('admin.trust*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 fas fa-fw fa-landmark"></i>
                The Trust
            </a>
            <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('admin.menus*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 fas fa-fw fa-bars"></i>
                Menus
            </a>
        </div>

        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Administration
        </h3>
        <div class="space-y-1">
            <a href="{{ route('admin.roles-permissions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('admin.roles-permissions*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 fas fa-fw fa-user-shield"></i>
                Roles & Permissions
            </a>
            <a href="{{ route('admin.website-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('admin.website-settings*')  /* Assuming this is the route */
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 fas fa-fw fa-cog"></i>
                Site Settings
            </a>
        </div>
    </nav>

    <div class="absolute bottom-0 w-full p-4 bg-white border-t border-gray-200">
        <div class="flex items-center gap-3">
            @if (auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" class="object-cover w-8 h-8 rounded-full">
            @else
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-white bg-indigo-600 rounded-full">
                    {{ auth()->user()->initials }}
                </div>
            @endif
            <div>
                <div class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</div>
                <a href="{{ route('logout') }}"
                    class="text-xs font-medium text-red-600 transition-colors duration-150 hover:text-red-800"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sign out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</aside>