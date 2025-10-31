@props([
    'label',
    'placeholder' => '— Select —',
    'items' => [],
    'itemLabel' => 'title',
    'itemSubtext' => null,
    'hierarchical' => false,
    'xModel' => null,
])

<div class="relative">
    <label class="block mb-1 text-sm font-medium text-gray-700">{{ $label }}</label>

    <div @click.stop="toggleDropdown('{{ $label }}')"
        class="w-full flex justify-between items-center border border-gray-300 rounded-lg p-2.5 text-sm bg-gray-50 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer">
        <span x-text="{{ $xModel }} ? '{{ $placeholder }}' : '{{ $placeholder }}'"></span>
        <svg class="w-4 h-4 ml-2 text-gray-500 transition-transform duration-200 transform"
            :class="openDropdown === '{{ $label }}' ? 'rotate-180' : ''"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <div x-show="openDropdown === '{{ $label }}'" x-transition
        class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow max-h-60">
        <input type="text" placeholder="Search..." x-model.debounce.300ms="search"
            class="w-full px-3 py-2 text-sm border-b border-gray-200 focus:outline-none" />

        <ul class="divide-y divide-gray-100">
            <li @click="$dispatch('cleared')" class="px-3 py-2 text-gray-500 cursor-pointer hover:bg-gray-100">
                — None —
            </li>

            <template x-for="item in {{ $hierarchical ? 'filteredParents()' : ($itemSubtext ? 'filteredRoutes()' : 'filteredPages()') }}" :key="item.id || item.uri">
                <li @click="$dispatch('selected', item)"
                    class="px-3 py-2 cursor-pointer hover:bg-blue-50">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700" x-text="item.{{ $itemLabel }}"></span>
                        @if($itemSubtext)
                            <span class="text-xs text-gray-500" x-text="'/' + item.{{ $itemSubtext }}"></span>
                        @endif
                    </div>
                </li>
            </template>
        </ul>
    </div>
</div>
