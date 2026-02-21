@props([
    'menu' => null,
    'menus' => [],
    'routes' => [],
    'pages' => [],
])

{{-- PHP Logic for default toggle state --}}
@php
    $defaultCreatePage = $menu?->page ? true : (!$menu ? true : false);
    // Check if we are in edit mode to prevent auto-overwriting order on initial load
    $isEditMode = $menu ? true : false;
@endphp

<div class="p-6 space-y-6 bg-white shadow-lg rounded-2xl" x-data="menuForm()">

    {{-- === VALIDATION ERRORS === --}}
    @if ($errors->any())
        <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
            <svg class="flex-shrink-0 inline w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Danger</span>
            <div>
                <span class="font-medium">Please fix the following errors:</span>
                <ul class="mt-1.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- === FORM FIELDS === --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- 1. Page Selector --}}
        <div class="relative md:col-span-2">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Select Existing Page</label>
            <div class="relative">
                <button type="button"
                    @click="openPage = !openPage; if(openPage) $nextTick(() => $refs.pageSearch.focus())"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="selectedPageData ? selectedPageData.title : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openPage ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openPage" @click.away="openPage=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">
                    <input type="text" x-model="searchPage" x-ref="pageSearch" placeholder="Search page..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <ul class="divide-y divide-gray-100">
                        <li @click="clearPage()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —</li>
                        <template x-for="page in filteredPages()" :key="page.id">
                            <li @click="selectPage(page)"
                                class="flex flex-col px-4 py-2 text-sm cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-800" x-text="page.title"></span>
                                <span class="text-xs text-gray-500" x-text="'/' + page.slug"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <input type="hidden" name="page_id" x-model="selectedPage">
            </div>
        </div>

        {{-- 2. Menu Title --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Menu Title <span
                    class="text-red-500">*</span></label>
            <input type="text" name="title" x-model="title" @input="generateSlug()" required
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        {{-- 3. Route / URL / Slug --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Slug or Custom URL</label>
            <div class="relative space-y-2">
                <button type="button"
                    @click="openRoute = !openRoute; if(openRoute) $nextTick(() => $refs.routeSearch.focus())"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="selectedRoute ? selectedRoute : '— Select from App Routes —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openRoute ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openRoute" @click.away="openRoute=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">
                    <input type="text" x-model="searchRoute" x-ref="routeSearch" placeholder="Search route..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <ul class="divide-y divide-gray-100">
                        <li @click="clearRoute()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —</li>
                        <template x-for="route in filteredRoutes()" :key="route.uri">
                            <li @click="selectRoute(route)"
                                class="flex flex-col px-4 py-2 text-sm cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-800" x-text="route.name ?? 'unnamed'"></span>
                                <span class="text-xs text-gray-500" x-text="'/' + route.uri"></span>
                                <template x-if="route.parameters && route.parameters.length">
                                    <span class="text-xs text-gray-400"
                                        x-text="'Params: ' + route.parameters.join(', ')"></span>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>

                <input type="text" name="url" x-model="selectedRoute" @input="slugManuallyEdited = true"
                    placeholder="/about-us or route URI"
                    class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
        </div>

        {{-- 4. Parent Menu --}}
        <div class="relative">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Parent Menu</label>
            <div class="relative">
                <button type="button"
                    @click="openParent = !openParent; if(openParent) $nextTick(() => $refs.parentSearch.focus())"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="parentId ? parentName() : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openParent ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openParent" @click.away="openParent=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">

                    <input type="text" x-model="searchParent" x-ref="parentSearch" placeholder="Search parent..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                    <ul class="divide-y divide-gray-100">
                        <li @click="clearParent()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —
                        </li>

                        <template x-for="menuItem in filteredParents()" :key="menuItem.id">
                            <li @click="selectParent(menuItem)"
                                class="py-2 text-sm text-gray-800 cursor-pointer hover:bg-blue-50 truncate pr-4"
                                :style="'padding-left: ' + (menuItem.depth * 20 + 16) + 'px'">
                                <span x-show="menuItem.depth > 0" class="text-gray-400 mr-1">↳</span>
                                <span x-text="menuItem.title"></span>
                            </li>
                        </template>

                        <li x-show="filteredParents().length === 0"
                            class="px-4 py-3 text-sm text-gray-400 text-center">
                            No menus found
                        </li>
                    </ul>
                </div>

                <input type="hidden" name="parent_id" x-model="parentId" />
            </div>
        </div>

        {{-- 5. Display Order --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Display Order</label>
            <input type="number" name="order" x-model="order"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        {{-- 6. Status --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border border-gray-300 after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600
                    transition-colors duration-300 ease-in-out">
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700" x-text="status ? 'Active' : 'Inactive'"></span>
            </label>
            <p class="mt-1.5 text-xs text-gray-500">Controls the visibility of this item on the site.</p>
        </div>

        {{-- 7. Auto-create Page --}}
      <div x-data="{ createPage: false }"> <label class="block mb-1.5 text-sm font-medium text-gray-700">Auto-create Page</label>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="create_page" value="1" class="sr-only peer" x-model="createPage">
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out"></div>
        <span class="ml-3 text-sm font-medium text-gray-700" x-text="createPage ? 'Enabled' : 'Disabled'"></span>
    </label>

            <p class="mt-1.5 text-xs text-gray-500">If active, a page will be created if the URL does not exist.</p>
        </div>

    </div>
</div>

<script>
    function menuForm() {
        const pages = @json($pages);
        const menus = @json($menus);
        const routes = @json($routes);

        const flattenMenus = (items, depth = 0, parentId = null) => {
            let flattened = [];
            items.forEach(item => {
                let flatItem = {
                    ...item,
                    depth: depth,
                    parent_id_ref: parentId
                };
                flattened.push(flatItem);

                if (item.children_recursive && item.children_recursive.length > 0) {
                    flattened = flattened.concat(flattenMenus(item.children_recursive, depth + 1, item.id));
                }
            });
            return flattened;
        };

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
            selectedPage: @json(old('page_id', isset($menu->page) ? $menu->page->id : '')),
            selectedPageData: @json($menu->page ?? null),
            order: @json(old('order', $menu->order ?? 0)),
            status: @json(old('status', $menu->status ?? true)) ? true : false,
            createPage: @json(old('create_page', $defaultCreatePage)) ? true : false,
            slugManuallyEdited: @json($menu ? true : false),
            isEditMode: @json($isEditMode), // To check if we should auto-calc on load

            pages,
            routes,
            parents: flattenMenus(menus),

            init() {
                // Only calculate default order if creating new item
                if (!this.isEditMode) {
                    this.calculateNextOrder();
                }
            },

            parentName() {
                try {
                    const p = this.parents.find(p => p.id == this.parentId);
                    return p ? p.title : '';
                } catch (error) {
                    return '';
                }
            },

            // === NEW: Function to calculate Next Order ===
            calculateNextOrder() {
                // 1. Normalize parentId (treat '' and null as same)
                const pId = this.parentId || null;

                // 2. Filter only siblings (items sharing the same parent)
                const siblings = this.parents.filter(p => p.parent_id_ref == pId);

                // 3. If no siblings, start with 0
                if (siblings.length === 0) {
                    this.order = 0;
                    return;
                }

                // 4. Find the maximum order among siblings
                const maxOrder = siblings.reduce((max, item) => {
                    // Ensure numeric comparison
                    const itemOrder = parseInt(item.order) || 0;
                    return itemOrder > max ? itemOrder : max;
                }, -1);

                // 5. Set next available order
                this.order = maxOrder + 1;
            },

            selectParent(menuItem) {
                this.parentId = menuItem.id;
                this.openParent = false;
                this.searchParent = '';

                // Auto-calculate next order when parent changes
                this.calculateNextOrder();
            },

            clearParent() {
                this.parentId = '';
                this.openParent = false;

                // Auto-calculate next order for root
                this.calculateNextOrder();
            },

            selectPage(page) {
                this.selectedPage = page.id;
                this.selectedPageData = page;
                this.title = page.title;
                this.selectedRoute = '/' + page.slug;
                this.slugManuallyEdited = true;
                this.openPage = false;
            },

            clearPage() {
                this.selectedPage = '';
                this.selectedPageData = null;
                this.slugManuallyEdited = false;
                this.openPage = false;
            },

            selectRoute(route) {
                this.selectedRoute = '/' + route.uri;
                this.slugManuallyEdited = true;
                this.openRoute = false;
            },

            clearRoute() {
                this.selectedRoute = '';
                this.slugManuallyEdited = false;
                this.openRoute = false;
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

                const lowerSearch = this.searchParent.toLowerCase();
                const parentsMap = new Map(this.parents.map(p => [p.id, p]));
                const matchedIds = new Set();

                this.parents.forEach(p => {
                    if (p.title.toLowerCase().includes(lowerSearch)) {
                        matchedIds.add(p.id);
                    }
                });

                return this.parents.filter(item => {
                    if (matchedIds.has(item.id)) return true;

                    let currentParentId = item.parent_id_ref;
                    while (currentParentId) {
                        if (matchedIds.has(currentParentId)) return true;
                        const parentObj = parentsMap.get(currentParentId);
                        currentParentId = parentObj ? parentObj.parent_id_ref : null;
                    }
                    return false;
                });
            },

            slugify(text) {
                if (!text) return '';
                return text.toString().toLowerCase().trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-');
            },

            generateSlug() {
                if (!this.slugManuallyEdited) {
                    let slug = this.slugify(this.title);
                    this.selectedRoute = slug.length > 0 ? '/' + slug : '';
                }
            }
        }
    }
</script>
