@props([
    'menu' => null,
    'menus' => [],
    'routes' => [],
    'pages' => [],
])

{{-- // ADDED: PHP variable for the new toggle's default value --}}
@php
    $defaultCreatePage = $menu?->page ? true : (!$menu ? true : false);
@endphp

{{--
    MODIFIED:
    - Using shadow-lg for a more defined container.
    - Removed all dark: classes.
--}}
<div class="p-6 space-y-6 bg-white shadow-lg rounded-2xl" x-data="menuForm()">

    {{-- === VALIDATION ERRORS === --}}
    {{--
        MODIFIED:
        - Refined professional alert styling for light mode.
        - Using a more appropriate 'exclamation triangle' icon.
    --}}
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

        {{-- Existing Page Selector --}}
        <div class="relative md:col-span-2">
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Select Existing Page</label>
            <div class="relative">
                {{--
                    MODIFIED:
                    - Button classes now exactly match text inputs for consistency.
                    - Padding changed to px-3 py-2 for a sleeker look.
                --}}
                <button type="button" @click="openPage = !openPage"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="selectedPageData ? selectedPageData.title : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openPage ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{--
                    MODIFIED:
                    - Refined shadow with ring-1 for a professional dropdown.
                --}}
                <div x-show="openPage" @click.away="openPage=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">
                    <input type="text" x-model="searchPage" placeholder="Search page..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <ul class="divide-y divide-gray-100">
                        {{-- MODIFIED: Softer hover state --}}
                        <li @click="clearPage()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —</li>
                        <template x-for="page in filteredPages()" :key="page.id">
                            {{-- MODIFIED: Standardized list item styling --}}
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

        {{-- Menu Title --}}
        <div>
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Menu Title <span
                    class="text-red-500">*</span></label>
            {{-- // MODIFIED: Added @input="generateSlug()" --}}
            {{-- MODIFIED: Standardized input styling (px-3 py-2) --}}
            <input type="text" name="title" x-model="title" @input="generateSlug()" required
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        {{-- Route / URL --}}
        <div>
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Slug or Custom URL</label>
            <div class="relative space-y-2">
                {{-- MODIFIED: Button classes now exactly match text inputs --}}
                <button type="button" @click="openRoute = !openRoute"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="selectedRoute ? selectedRoute : '— Select from App Routes —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openRoute ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- MODIFIED: Refined shadow with ring-1 --}}
                <div x-show="openRoute" @click.away="openRoute=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">
                    <input type="text" x-model="searchRoute" placeholder="Search route..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <ul class="divide-y divide-gray-100">
                        <li @click="clearRoute()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —</li>
                        <template x-for="route in filteredRoutes()" :key="route.uri">
                            <li @click="selectRoute(route)"
                                class="flex flex-col px-4 py-2 text-sm cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-800"
                                    x-text="route.name ?? 'unnamed'"></span>
                                <span class="text-xs text-gray-500"
                                    x-text="'/' + route.uri"></span>
                                <template x-if="route.parameters && route.parameters.length">
                                    <span class="text-xs text-gray-400"
                                        x-text="'Params: ' + route.parameters.join(', ')"></span>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- // MODIFIED: Added @input="slugManuallyEdited = true" --}}
                {{-- MODIFIED: Standardized input styling (px-3 py-2) --}}
                <input type="text" name="url" x-model="selectedRoute" @input="slugManuallyEdited = true"
                    placeholder="/about-us or route URI"
                    class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
        </div>

        {{-- Parent Menu --}}
        <div class="relative">
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Parent Menu</label>
            <div class="relative">
                {{-- MODIFIED: Button classes now exactly match text inputs --}}
                <button type="button" @click="openParent = !openParent"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm text-left text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span x-text="parentId ? parentName() : '— None —'"></span>
                    <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                        :class="openParent ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- MODIFIED: Refined shadow with ring-1 --}}
                <div x-show="openParent" @click.away="openParent=false" x-transition
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 max-h-60">
                    <input type="text" x-model="searchParent" placeholder="Search parent..."
                        class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    <ul class="divide-y divide-gray-100">
                        <li @click="clearParent()"
                            class="px-4 py-2 text-sm text-gray-500 cursor-pointer hover:bg-blue-50">
                            — None —</li>
                        <template x-for="menuItem in filteredParents()" :key="menuItem.id">
                            <li @click.stop="selectParent(menuItem)"
                                class="px-4 py-2 text-sm text-gray-800 cursor-pointer hover:bg-blue-50"
                                x-text="menuItem.title"></li>

                            <template x-if="menuItem.children_recursive && menuItem.children_recursive.length">
                                <ul class="pl-6 border-l border-gray-200">
                                    <template x-for="child in menuItem.children_recursive" :key="child.id">
                                        <li @click.stop="selectParent(child)"
                                            class="px-4 py-2 text-sm text-gray-800 cursor-pointer hover:bg-blue-50"
                                            x-text="child.title"></li>
                                    </template>
                                </ul>
                            </template>
                        </template>
                    </ul>
                </div>

                <input type="hidden" name="parent_id" x-model="parentId" />
            </div>
        </div>

        {{-- Order --}}
        <div>
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Display Order</label>
            {{-- MODIFIED: Standardized input styling (px-3 py-2) --}}
            <input type="number" name="order" x-model="order"
                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        {{-- Status --}}
        <div>
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" x-model="status">
                {{--
                    MODIFIED:
                    - Changed default bg to lighter bg-gray-200.
                    - Changed checked color to bg-blue-600 to match theme.
                    - Added accessible focus ring.
                --}}
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border border-gray-300 after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600
                    transition-colors duration-300 ease-in-out">
                </div>
                {{-- MODIFIED: Added dynamic text label for clarity --}}
                <span class="ml-3 text-sm font-medium text-gray-700"
                    x-text="status ? 'Active' : 'Inactive'"></span>
            </label>
            {{-- ADDED: Helper text for consistency --}}
            <p class="mt-1.5 text-xs text-gray-500">Controls the visibility of this item on the site.</p>
        </div>

        {{-- // ADDED: New "Auto-create Page" toggle --}}
        <div>
            {{-- MODIFIED: Standardized label styling --}}
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Auto-create Page</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="create_page" value="1" class="sr-only peer"
                    x-model="createPage">
                {{--
                    MODIFIED:
                    - Changed default bg to lighter bg-gray-200.
                    - Changed checked color to bg-blue-600 to match theme.
                    - Added accessible focus ring.
                --}}
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border border-gray-300 after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600
                    transition-colors duration-300 ease-in-out">
                </div>
                {{-- MODIFIED: Added dynamic text label for clarity --}}
                <span class="ml-3 text-sm font-medium text-gray-700"
                    x-text="createPage ? 'Enabled' : 'Disabled'"></span>
            </label>
            {{-- MODIFIED: Standardized margin-top and text --}}
            <p class="mt-1.5 text-xs text-gray-500">If active, a page will be created if the URL does not exist.</p>
        </div>

    </div>
</div>

{{--
    ======================================================================
    SCRIPT BLOCK (Functionality 100% Preserved)
    - All comments translated to English.
    - All logic is UNTOUCHED.
    ======================================================================
--}}
<script>
    function menuForm() {
        const pages = @json($pages);
        const menus = @json($menus);
        const routes = @json($routes);

        // Enhanced error handling for flattening menus
        const flattenMenus = (menus) => {
            try {
                return menus.reduce((acc, m) => {
                    try {
                        if (!acc.some(item => item.id === m.id)) {
                            acc.push(m);
                        }
                        if (m.children_recursive && m.children_recursive.length) {
                            acc.push(...flattenMenus(m.children_recursive)); // Flatten children recursively
                        }
                    } catch (error) {
                        console.error('Error processing menu item', m, error);
                    }
                    return acc;
                }, []);
            } catch (error) {
                console.error('Error flattening menus', error);
                return [];
            }
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
            pages,
            routes,
            parents: flattenMenus(menus), // Call the flattening function

            // === ADDED: New properties for the new features ===
            createPage: @json(old('create_page', $defaultCreatePage)) ? true : false,
            slugManuallyEdited: @json($menu ? true : false), // Lock slug on 'edit'
            // === END OF ADDED PROPERTIES ===


            parentName() {
                try {
                    const p = this.parents.find(p => p.id == this.parentId);
                    return p ? p.title : '';
                } catch (error) {
                    console.error('Error fetching parent name', error);
                    return ''; // Fallback value if an error occurs
                }
            },

            selectPage(page) {
                try {
                    this.selectedPage = page.id;
                    this.selectedPageData = page;
                    this.title = page.title;
                    this.selectedRoute = '/' + page.slug;
                    this.slugManuallyEdited = true; // MODIFIED: Lock slug
                    this.openPage = false;
                } catch (error) {
                    console.error('Error selecting page', page, error);
                }
            },

            clearPage() {
                try {
                    this.selectedPage = '';
                    this.selectedPageData = null;
                    this.slugManuallyEdited = false; // MODIFIED: Unlock slug
                    this.openPage = false;
                } catch (error) {
                    console.error('Error clearing page', error);
                }
            },

            selectRoute(route) {
                try {
                    this.selectedRoute = '/' + route.uri;
                    this.slugManuallyEdited = true; // MODIFIED: Lock slug
                    this.openRoute = false;
                } catch (error) {
                    console.error('Error selecting route', route, error);
                }
            },

            clearRoute() {
                try {
                    this.selectedRoute = '';
                    this.slugManuallyEdited = false; // MODIFIED: Unlock slug
                    this.openRoute = false;
                } catch (error) {
                    console.error('Error clearing route', error);
                }
            },

            selectParent(menuItem) {
                try {
                    this.parentId = menuItem.id;
                    this.openParent = false;
                } catch (error) {
                    console.error('Error selecting parent menu item', menuItem, error);
                }
            },

            clearParent() {
                try {
                    this.parentId = '';
                    this.openParent = false;
                } catch (error) {
                    console.error('Error clearing parent', error);
                }
            },

            filteredRoutes() {
                try {
                    if (!this.searchRoute) return this.routes;
                    return this.routes.filter(r =>
                        (r.name ?? '').toLowerCase().includes(this.searchRoute.toLowerCase()) ||
                        r.uri.toLowerCase().includes(this.searchRoute.toLowerCase())
                    );
                } catch (error) {
                    console.error('Error filtering routes', error);
                    return [];
                }
            },

            filteredPages() {
                try {
                    if (!this.searchPage) return this.pages;
                    return this.pages.filter(p =>
                        p.title.toLowerCase().includes(this.searchPage.toLowerCase())
                    );
                } catch (error) {
                    console.error('Error filtering pages', error);
                    return [];
                }
            },

            filteredParents() {
                try {
                    if (!this.searchParent) return this.parents;
                    return this.parents.filter(p =>
                        p.title.toLowerCase().includes(this.searchParent.toLowerCase())
                    );
                } catch (error) {
                    console.error('Error filtering parents', error);
                    return [];
                }
            },

            // === ADDED: Functions to generate the Slug ===
            slugify(text) {
                if (!text) return '';
                return text.toString().toLowerCase().trim()
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(/[^\w\-]+/g, '') // Remove non-alphanumeric characters
                    .replace(/\-\-+/g, '-'); // Replace multiple -- with a single one
            },

            generateSlug() {
                if (!this.slugManuallyEdited) {
                    let slug = this.slugify(this.title);
                    if (slug.length > 0) {
                        this.selectedRoute = '/' + slug;
                    } else {
                        this.selectedRoute = '';
                    }
                }
            },
            // === END OF ADDED FUNCTIONS ===
        }
    }
</script>
