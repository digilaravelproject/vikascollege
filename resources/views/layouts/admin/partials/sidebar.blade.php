<aside
    class="fixed inset-y-0 left-0 z-20 flex flex-col transition-transform duration-300 transform bg-white border-r border-gray-200 shadow-xl w-72 lg:static lg:inset-auto lg:translate-x-0 lg:shadow-none"
    :class="{'-translate-x-72': !sidebarOpen}">

    {{-- Logo/Header --}}
    <div class="flex items-center justify-between flex-shrink-0 px-6 py-4 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('storage/' . setting('college_logo')) }}" alt="logo" class="object-contain w-10 h-10">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">{{ setting('college_name') }}</h1>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
        </a>
        <button class="p-2 rounded-md lg:hidden hover:bg-gray-100" @click="sidebarOpen = false">
            <i class="text-gray-600 bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Navigation (Scrollable Area) --}}
    <nav class="flex-1 px-4 py-6 overflow-y-auto">

        {{-- Dashboard Link --}}
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
            {{ request()->routeIs('admin.dashboard')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="w-5 bi bi-grid-1x2-fill"></i>
            Dashboard
        </a>

        {{-- Section: Website Content --}}
        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Website Content
        </h3>
        <div class="space-y-1">

            {{-- Dropdown: Homepage --}}
            @php
                $isHomeActive = request()->routeIs('admin.homepage*') ||
                    request()->routeIs('admin.notifications*') ||
                    request()->routeIs('admin.announcements*') ||
                    request()->routeIs('admin.event-items*') ||
                    request()->routeIs('admin.academic-calendar*') ||
                    request()->routeIs('admin.gallery-images*') ||
                    request()->routeIs('admin.testimonials*') ||
                    request()->routeIs('admin.why-choose-us*');
            @endphp
            <div x-data="{ homeMenuOpen: {{ $isHomeActive ? 'true' : 'false' }} }">
                {{-- Dropdown Toggle Button --}}
                <button @click="homeMenuOpen = !homeMenuOpen" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ $isHomeActive
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <i class="w-5 bi bi-house-door-fill"></i>
                        Homepage
                    </span>
                    <i class="bi bi-chevron-down transition-transform duration-200"
                        :class="{'rotate-180': homeMenuOpen}"></i>
                </button>

                {{-- Dropdown Content --}}
                <div x-show="homeMenuOpen" x-transition class="pl-5 space-y-1 mt-1 border-l border-gray-200 ml-2.5">
                    <a href="{{ route('admin.homepage.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.homepage*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-layout-wtf"></i>
                        Homepage Layout
                    </a>
                    <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.notifications*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-bell-fill"></i>
                        Notifications
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.announcements*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-megaphone-fill"></i>
                        Announcements
                    </a>
                    <a href="{{ route('admin.event-items.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.event-items*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-calendar-check-fill"></i>
                        Events
                    </a>
                    <a href="{{ route('admin.academic-calendar.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.academic-calendar*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-calendar-week-fill"></i>
                        Academic Calendar
                    </a>
                    <a href="{{ route('admin.gallery-images.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.gallery-images*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-images"></i>
                        Gallery Images
                    </a>
                    <a href="{{ route('admin.testimonials.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.testimonials*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-chat-quote-fill"></i>
                        Testimonials
                    </a>
                    <a href="{{ route('admin.why-choose-us.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.why-choose-us*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-patch-check-fill"></i>
                        Why Choose Us
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.pagebuilder.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.pagebuilder*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-layout-text-window"></i>
                Page Builder
            </a>
            <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.menus*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-list-task"></i>
                Menu Builder
            </a>
            <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.media*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-folder2-open"></i>
                Media Management
            </a>
            <a href="{{ route('admin.trust.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.trust*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-bank"></i>
                The Trust
            </a>
        </div>
        {{-- Section: Leads Management (YAHAN ADD KAREIN) --}}
        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Leads Management
        </h3>
        <div class="space-y-1">

            {{-- Dropdown: Leads --}}
            @php
                $isLeadsActive = request()->routeIs('admin.leads*');
            @endphp
            <div x-data="{ leadsMenuOpen: {{ $isLeadsActive ? 'true' : 'false' }} }">
                {{-- Dropdown Toggle Button --}}
                <button @click="leadsMenuOpen = !leadsMenuOpen" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ $isLeadsActive
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <i class="w-5 bi bi-person-lines-fill"></i>
                        Leads
                    </span>
                    <i class="bi bi-chevron-down transition-transform duration-200"
                        :class="{ 'rotate-180': leadsMenuOpen }"></i>
                </button>

                {{-- Dropdown Content --}}
                <div x-show="leadsMenuOpen" x-transition class="pl-5 space-y-1 mt-1 border-l border-gray-200 ml-2.5">
                    <a href="{{ route('admin.leads.admissions') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                    {{ request()->routeIs('admin.leads.admissions')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-person-check-fill"></i>
                        Admissions
                    </a>
                    <a href="{{ route('admin.leads.enquiries') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                    {{ request()->routeIs('admin.leads.enquiries')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="w-5 bi bi-question-circle-fill"></i>
                        Enquiries
                    </a>
                </div>
            </div>
        </div>
        {{-- Section: Access Control --}}
        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Access Control
        </h3>
        <div class="space-y-1">
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.users*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-people-fill"></i>
                Users Management
            </a>
            <a href="{{ route('admin.roles-permissions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.roles-permissions*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-shield-lock-fill"></i>
                Roles & Permissions
            </a>
        </div>

        {{-- Section: Configuration --}}
        <h3 class="px-3 pt-6 pb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
            Configuration
        </h3>
        <div class="space-y-1">
            <a href="{{ route('admin.website-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.website-settings*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-gear-wide-connected"></i>
                Site Settings
            </a>
            <a href="{{ route('admin.site.index') }}" classs="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                {{ request()->routeIs('admin.site*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-code-slash"></i>
                Developer Settings
            </a>
            <a href="{{ route('admin.smtp.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
            {{ request()->routeIs('admin.smtp*')
    ? 'bg-indigo-50 text-indigo-600 font-semibold'
    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="w-5 bi bi-envelope-fill"></i>
                SMTP Settings
            </a>
        </div>
    </nav>

    {{-- User Footer (Non-Absolute) --}}
    <div class="flex-shrink-0 p-4 bg-white border-t border-gray-200">
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
