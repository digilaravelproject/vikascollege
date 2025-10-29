@props(['menu' => null, 'menus' => [], 'routes' => []])

<div class="p-6 space-y-5 bg-white shadow rounded-2xl"
     x-data="{
        open: false,
        selectedRoute: '{{ old('url', $menu->url ?? '') }}',
        paramValue: ''
     }"
     x-effect="
        if (selectedRoute.includes('{') && paramValue) {
            selectedRoute = selectedRoute.replace(/\{.*?\}\??/, paramValue);
        }
     ">

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        <!-- Title -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Menu Title <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title"
                   value="{{ old('title', $menu->title ?? '') }}"
                   required
                   class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        <!-- Route / URL Hybrid -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Slug or Custom URL</label>
            <div class="space-y-2">

                <!-- Dropdown -->
                <div class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm
                                   bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <span x-text="selectedRoute ? selectedRoute : '— Select from App Routes —'"></span>
                        <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
                             :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Options -->
                    <ul x-show="open" @click.away="open = false" x-transition
                        class="absolute z-10 w-full mt-1 overflow-y-auto text-sm bg-white border border-gray-200 rounded-lg shadow max-h-60">
                        <li @click="selectedRoute = ''; open = false"
                            class="px-3 py-2 text-gray-500 transition-colors cursor-pointer hover:bg-gray-100">
                            — None —
                        </li>

                        @foreach($routes as $route)
                            <li @click="
                                    selectedRoute = '{{ $route['uri'] }}';
                                    paramValue = '';
                                    open = false;
                                "
                                class="px-3 py-2 transition-colors cursor-pointer hover:bg-blue-50">
                                <span class="font-medium text-gray-700">{{ $route['name'] ?? 'unnamed' }}</span>
                                <span class="ml-1 text-xs text-gray-500">/{{ $route['uri'] }}</span>

                                @if(!empty($route['parameters']))
                                    <span class="block mt-1 text-xs text-gray-400">
                                        Params: {{ implode(', ', $route['parameters']) }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Bound Input -->
                <input type="text" name="url" x-model="selectedRoute" placeholder="/about-us or route URI"
                       class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />

                <!-- Parameter Input -->
                <template x-if="selectedRoute.includes('{')">
                    <div class="mt-2">
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Route Parameter Value
                        </label>
                        <input type="text" x-model="paramValue" placeholder="e.g. my-trust-slug"
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </template>

            </div>
        </div>

        <!-- Parent Menu (Recursive) -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Parent Menu</label>
            <select name="parent_id"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-gray-50 hover:bg-gray-100 cursor-pointer
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">None</option>
                @foreach($menus as $m)
                    @include('admin.menus.partials.parent-options', [
                        'menuItem' => $m,
                        'level' => 0,
                        'selected' => old('parent_id', $menu->parent_id ?? '')
                    ])
                @endforeach
            </select>
        </div>

        <!-- Order -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Display Order</label>
            <input type="number" name="order" value="{{ old('order', $menu->order ?? 0) }}"
                   class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        <!-- Status -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       {{ old('status', $menu->status ?? true) ? 'checked' : '' }}>
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
