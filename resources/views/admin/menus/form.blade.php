@props([
    'menu' => null,
    'menus' => [],
    'routes' => [],
    'pages' => [],
])

<div class="p-6 space-y-6 bg-white shadow rounded-2xl" x-data="menuForm()">

    {{-- === VALIDATION ERRORS === --}}
    @if ($errors->any())
        <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <ul class="pl-4 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- === FORM FIELDS === --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- Existing Page Selector --}}
        <div class="relative md:col-span-2">
            <label class="block mb-1 text-sm font-medium text-gray-700">Select Existing Page</label>
            <div class="relative">
                <button type="button" @click="openPage = !openPage"
                    class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm
                        bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <span x-text="selectedPageData ? selectedPageData.title : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openPage ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openPage" @click.away="openPage=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow max-h-60 scrollbar-thin scrollbar-thumb-gray-300">
                    <input type="text" x-model="searchPage" placeholder="Search page..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none" />

                    <ul class="divide-y divide-gray-100">
                        <li @click="clearPage()" class="px-3 py-2 text-gray-500 cursor-pointer hover:bg-gray-100">
                            — None —
                        </li>
                        <template x-for="page in filteredPages()" :key="page.id">
                            <li @click="selectPage(page)"
                                class="flex flex-col px-3 py-2 cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-700" x-text="page.title"></span>
                                <span class="text-xs text-gray-500" x-text="'/' + page.slug"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <input type="hidden" name="page_id" x-model="selectedPage">
            </div>
        </div>

        {{-- Menu Title --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Menu Title <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" x-model="title" required
                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        {{-- Route / URL --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Slug or Custom URL</label>
            <div class="relative space-y-2">

                <button type="button" @click="openRoute = !openRoute"
                    class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm
                        bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <span x-text="selectedRoute ? selectedRoute : '— Select from App Routes —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openRoute ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openRoute" @click.away="openRoute=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow max-h-60 scrollbar-thin scrollbar-thumb-gray-300">

                    <input type="text" x-model="searchRoute" placeholder="Search route..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none" />

                    <ul class="divide-y divide-gray-100">
                        <li @click="clearRoute()" class="px-3 py-2 text-gray-500 cursor-pointer hover:bg-gray-100">
                            — None —
                        </li>

                        <template x-for="route in filteredRoutes()" :key="route.uri">
                            <li @click="selectRoute(route)"
                                class="flex flex-col px-3 py-2 cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-700" x-text="route.name ?? 'unnamed'"></span>
                                <span class="text-xs text-gray-500" x-text="'/' + route.uri"></span>
                                <template x-if="route.parameters && route.parameters.length">
                                    <span class="text-xs text-gray-400"
                                        x-text="'Params: ' + route.parameters.join(', ')"></span>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>

                <input type="text" name="url" x-model="selectedRoute" placeholder="/about-us or route URI"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
            </div>
        </div>

        {{-- Parent Menu --}}
        <div class="relative">
            <label class="block mb-1 text-sm font-medium text-gray-700">Parent Menu</label>
            <div class="relative">
                <button type="button" @click="openParent = !openParent"
                    class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm
                        bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <span x-text="parentId ? parentName() : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openParent ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openParent" @click.away="openParent=false" x-transition x-cloak
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow max-h-60 scrollbar-thin scrollbar-thumb-gray-300">

                    <input type="text" x-model="searchParent" placeholder="Search parent..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none" />

                    <ul class="divide-y divide-gray-100">
                        <li @click="clearParent()" class="px-3 py-2 text-gray-500 cursor-pointer hover:bg-gray-100">
                            — None —
                        </li>

                        <template x-for="menuItem in filteredParents()" :key="menuItem.id">
                            <li>
                                <div @click.stop="selectParent(menuItem)" class="px-3 py-2 hover:bg-blue-50"
                                    x-text="menuItem.title"></div>
                                <template x-if="menuItem.children && menuItem.children.length">
                                    <ul class="pl-4 border-l border-gray-200">
                                        <template x-for="child in menuItem.children" :key="child.id">
                                            <li>
                                                <div @click.stop="selectParent(child)"
                                                    class="px-3 py-2 hover:bg-blue-50" x-text="child.title"></div>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>

                <input type="hidden" name="parent_id" x-model="parentId">
            </div>
        </div>

        {{-- Order --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Display Order</label>
            <input type="number" name="order" x-model="order"
                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        {{-- Status --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full
                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border border-gray-300 after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600
                    transition-colors duration-300 ease-in-out"></div>
            </label>
        </div>
    </div>
</div>

<script>
function menuForm() {
    const pages = @json($pages);
    const menus = @json($menus);
    const routes = @json($routes);
    const flattenMenus = (menus) => menus.reduce((acc, m) => {
        acc.push(m);
        if (m.children) acc.push(...flattenMenus(m.children));
        return acc;
    }, []);

    return {
        openRoute: false,
        openPage: false,
        openParent: false,
        searchRoute: '',
        searchPage: '',
        searchParent: '',
        selectedRoute: @json(old('url', $menu->url ?? '')),
        title: @json(old('title', $menu->title ?? '')),
        parentId: @json(old('parent_id', $menu->parent_id ?? '')),
        selectedPage: @json(old('page_id', $menu->page->id ?? '')),
        selectedPageData: @json($menu->page ?? null),
        order: @json(old('order', $menu->order ?? 0)),
        status: @json(old('status', $menu->status ?? true)) ? true : false,
        pages,
        routes,
        parents: flattenMenus(menus),

        // Methods
        parentName() {
            const p = this.parents.find(p => p.id == this.parentId);
            return p ? p.title : '';
        },
        selectPage(page) {
            this.selectedPage = page.id;
            this.selectedPageData = page;
            this.title = page.title;
            this.selectedRoute = '/' + page.slug;
            this.openPage = false;
        },
        clearPage() {
            this.selectedPage = '';
            this.selectedPageData = null;
            this.openPage = false;
        },
        selectRoute(route) {
            this.selectedRoute = '/' + route.uri;
            this.openRoute = false;
        },
        clearRoute() {
            this.selectedRoute = '';
            this.openRoute = false;
        },
        selectParent(menuItem) {
            this.parentId = menuItem.id;
            this.openParent = false;
        },
        clearParent() {
            this.parentId = '';
            this.openParent = false;
        },
        filteredRoutes() {
            if (!this.searchRoute) return this.routes;
            return this.routes.filter(r =>
                (r.name ?? '').toLowerCase().includes(this.searchRoute.toLowerCase()) ||
                r.uri.toLowerCase().includes(this.searchRoute.toLowerCase())
            );
        },
        filteredPages() {
            if (!this.searchPage) return this.pages;
            return this.pages.filter(p =>
                p.title.toLowerCase().includes(this.searchPage.toLowerCase())
            );
        },
        filteredParents() {
            if (!this.searchParent) return this.parents;
            return this.parents.filter(p =>
                p.title.toLowerCase().includes(this.searchParent.toLowerCase())
            );
        },
    }
}
</script>
