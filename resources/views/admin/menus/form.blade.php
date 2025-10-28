@props(['menu' => null, 'menus' => [], 'routes' => []])

<div class="bg-white rounded-2xl shadow p-6 space-y-5" x-data="{
        selectedRoute: '{{ old('url', $menu->url ?? '') }}',
        openRoutes: false
     }">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- Title -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Menu Title <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title', $menu->title ?? '') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        <!-- Route / URL Hybrid -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Slug or Custom URL
            </label>

            <div class="space-y-2">

                <!-- Custom Styled Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm
                               bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               transition-all duration-150 ease-in-out">
                        <span x-text="selectedRoute ? selectedRoute : '— Select from App Routes —'"
                            :class="selectedRoute ? 'text-gray-800' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-500 ml-2 transform transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Options -->
                    <ul x-show="open" @click.away="open = false" x-transition
                        class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow max-h-60 overflow-y-auto text-sm">
                        <li @click="selectedRoute = ''; open = false"
                            class="px-3 py-2 text-gray-500 hover:bg-gray-100 cursor-pointer transition-colors">
                            — None —
                        </li>
                        @foreach($routes as $route)
                            <li @click="selectedRoute = '{{ $route['uri'] }}'; open = false"
                                class="px-3 py-2 hover:bg-blue-50 cursor-pointer transition-colors">
                                <span class="font-medium text-gray-700">{{ $route['name'] ?? 'unnamed' }}</span>
                                <span class="text-gray-500 text-xs ml-1">/{{ $route['uri'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Bound Input -->
                <input type="text" name="url" x-model="selectedRoute" placeholder="/about-us or route URI" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
            </div>
        </div>

        <!-- Parent Menu -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Menu</label>
            <select name="parent_id" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                       bg-gray-50 hover:bg-gray-100 cursor-pointer
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition-all duration-150 ease-in-out">
                <option value="">None</option>
                @foreach($menus as $m)
                    <option value="{{ $m->id }}" {{ old('parent_id', $menu->parent_id ?? '') == $m->id ? 'selected' : '' }}>
                        {{ $m->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Order -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
            <input type="number" name="order" value="{{ old('order', $menu->order ?? 0) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" {{ old('status', $menu->status ?? true) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full
                           peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                           after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                           after:bg-white after:border border-gray-300 after:rounded-full
                           after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600
                           transition-colors duration-300 ease-in-out">
                </div>
            </label>
        </div>

    </div>
</div>