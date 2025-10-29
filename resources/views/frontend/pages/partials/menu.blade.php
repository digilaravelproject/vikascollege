@props(['menus', 'activeSection'])

<ul class="space-y-1">
    @foreach($menus as $menu)
        @php
            $isActive = ($activeSection->id ?? 0) === ($menu->page->id ?? 0);
            $url = $menu->page ? route('page.view', $menu->page->slug) : '#';
        @endphp

        <li>
            <a href="{{ $url }}"
                class="block px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                      {{ $isActive ? 'bg-[#013954] text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]' }}">
                {{ strtoupper($menu->title) }}
            </a>

            @if($menu->childrenRecursive->count())
                <x-menu :menus="$menu->childrenRecursive" :activeSection="$activeSection" />
            @endif
        </li>
    @endforeach
</ul>
