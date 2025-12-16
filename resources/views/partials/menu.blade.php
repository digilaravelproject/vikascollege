@php
    use App\Models\Menu;

    // Fetch & Filter Menus
    $rawMenus = Menu::where('status', 1)
                    ->whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->orderBy('order')
                    ->get();

    $menus = $rawMenus->filter(function($value, $key) {
        return !str_starts_with((string) $value->order, '100');
    });

    // --- HELPER FUNCTION FOR TITLE FORMATTING ---
    if (!function_exists('getMenuLabel')) {
        function getMenuLabel($title) {
            // CONFIGURATION: Is list me jo word jaise likhoge, waisa hi dikhega.
            // Example: Agar DB me 'iqac' hai aur yaha 'IQAC' likha hai, to result 'IQAC' hoga.
            $exactCasingList = [
                'IQAC', 
                'RTI', 
                'NAAC', 
                'NCC', 
                'NSS', 
                'IQAC Home',
                'AQAR',
                'IIQA',
                'SSR',
                'ISO Certification',
                'DVV Clarifications',
                'e-Governance', 
                'PhD'
            ];

            foreach ($exactCasingList as $exact) {
                if (strcasecmp($title, $exact) === 0) {
                    return $exact; // List wala exact format return karega
                }
            }
            
            // Default behavior: Capitalize (e.g. "student life" -> "Student Life")
            return ucwords(strtolower($title));
        }
    }

    // Helper Function for Standard Dropdowns
    if (!function_exists('renderDesktopRecursive')) {
        function renderDesktopRecursive($items, $level = 0) {
            if ($items->isEmpty()) return '';
            $paddingLeft = 14 + ($level * 12);
            $wrapperClass = ($level === 0) ? 'bg-gray-50 py-1 rounded-sm w-full' : 'mt-0.5 w-full';
            $html = '<ul class="space-y-0.5 ' . $wrapperClass . '">';
            foreach ($items as $item) {
                $hasChildren = $item->children->count() > 0;
                
                // Use the new formatting helper
                $title = getMenuLabel($item->title);

                $html .= '<li class="w-full">';
                // NOTE: Removed 'capitalize' class here
                $html .= '<a href="' . $item->link . '" class="block text-xs font-normal text-gray-600 hover:text-[#013954] hover:underline transition duration-150 break-words whitespace-normal" style="padding-left: ' . $paddingLeft . 'px;">' . ($level > 0 ? '↳ ' : '— ') . $title . '</a>';
                if ($hasChildren) { $html .= renderDesktopRecursive($item->children, $level + 1); }
                $html .= '</li>';
            }
            $html .= '</ul>';
            return $html;
        }
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    nav { font-family: 'Roboto', sans-serif !important; }
    .animate-fadeInUp { animation: fadeInUp 0.3s ease-out forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    [x-cloak] { display: none !important; }
    .mega-menu-tab-link { padding: 10px 16px; font-size: 0.9rem; font-weight: 500; }
    .thin-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .thin-scrollbar::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 4px; }
    .thin-scrollbar::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 10px; }
    .thin-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #9ca3af; }
</style>

<nav x-data="{ open: false }"
     class="shadow-md hidden lg:flex sticky top-0 z-50 bg-[#013954] font-roboto h-[40px] relative border-b border-[#013954]">

    {{-- Responsive Container: Adapts to screen width to prevent cutting --}}
    <div class="w-full max-w-[1340px] px-2 lg:px-4 mx-auto h-full">
        <div class="flex items-center justify-center h-full">

            <div class="flex items-center justify-center w-full h-full">
                <ul class="flex items-center justify-center w-full h-full whitespace-nowrap lg:gap-x-0 xl:gap-x-1">

                    @foreach ($menus as $menu)
                        @php
                            $hasChildren = $menu->children->count() > 0;
                            $isMegaMenu = ($menu->children->count() >= 6) ||
                                          ($menu->children->count() == 1 && $menu->children->first() && $menu->children->first()->children->count() >= 6);
                            $isStandardDropdown = $hasChildren && !$isMegaMenu;

                            $liClass = 'group flex items-center h-full';
                            if ($isStandardDropdown) {
                                $liClass .= ' relative';
                            } else {
                                $liClass .= ' static';
                            }
                        @endphp

                        <li x-data="{ openSub: false, activeTabIndex: 0 }" class="{{ $liClass }}">

                            {{--
                                Responsive Padding & Font Size:
                                lg:text-[13px] lg:px-2 -> fits 14" screens
                                xl:text-sm xl:px-3 -> fits 16"+ screens
                            --}}
                            <a href="{{ $menu->link }}"
                               @if ($hasChildren) @mouseenter="openSub = true" @mouseleave="openSub = false" @endif
                               class="relative h-full flex items-center gap-1 font-medium text-white transition duration-200
                                      lg:px-2 xl:px-3 lg:text-[13px] xl:text-sm
                                      hover:text-gray-200
                                      after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-[3px] after:bg-white
                                      after:transition-all after:duration-300
                                      {{ Request::is(trim(parse_url($menu->url, PHP_URL_PATH), '/')) ? 'after:w-full text-white font-bold' : 'after:w-0 hover:after:w-full' }}">

                                {{-- LEVEL 1: Use helper function --}}
                                {{ getMenuLabel($menu->title) }}

                            </a>

                            {{-- Responsive Separator --}}
                            @if (!$loop->last)
                                <div class="w-[2px] h-3.5 bg-white/20 rounded-sm lg:mx-0.5 xl:mx-1"></div>
                            @endif

                            {{-- MEGA MENU STRUCTURE --}}
                            @if ($isMegaMenu)
                                <div x-show="openSub" x-cloak
                                     @mouseenter="openSub = true"
                                     @mouseleave="openSub = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute left-0 z-50 w-full text-gray-800 top-full">

                                    <div class="mx-auto overflow-hidden bg-white shadow-xl max-w-[1250px] border-b border-gray-200 rounded-b-lg border-t-2 border-[#013954]">
                                        <div class="flex">

                                            {{-- Level 2 (Tabs) --}}
                                            <div class="w-1/4 bg-gray-50 border-r border-gray-200 py-2 max-h-[30rem] overflow-y-auto thin-scrollbar">
                                                <ul class="space-y-0.5">
                                                    @foreach ($menu->children as $index => $tabItem)
                                                        <li>
                                                            <a href="{{ $tabItem->link }}"
                                                               @mouseenter="activeTabIndex = {{ $index }}"
                                                               :class="activeTabIndex === {{ $index }} ? 'bg-white text-[#013954] font-semibold shadow-sm border-l-4 border-l-[#013954]' : 'text-gray-700 hover:bg-gray-100 border-l-4 border-l-transparent'"
                                                               class="block transition-all duration-200 cursor-pointer mega-menu-tab-link"> {{-- Removed capitalize --}}
                                                                {{ getMenuLabel($tabItem->title) }}
                                                                <span x-show="activeTabIndex === {{ $index }}" class="float-right text-[#013954]">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            {{-- Level 3 (Grid Content) --}}
                                            <div class="w-3/4 p-6 bg-white max-h-[30rem] overflow-y-auto thin-scrollbar relative">
                                                @foreach ($menu->children as $index => $tabItem)
                                                    <div x-show="activeTabIndex === {{ $index }}" x-cloak class="space-y-4 animate-fadeInUp">
                                                        <div class="grid grid-cols-3 gap-x-8 gap-y-6 min-w-0">

                                                            @foreach ($tabItem->children as $sub)
                                                                @if($sub->children->count() > 0)
                                                                    <div class="flex flex-col w-full">
                                                                        <a href="{{ $sub->link }}" class="block mb-2 text-sm font-bold text-gray-800 border-b-2 border-gray-100 pb-1 hover:text-[#013954] hover:border-blue-100 transition-colors break-words whitespace-normal"> {{-- Removed capitalize --}}
                                                                            {{ getMenuLabel($sub->title) }}
                                                                        </a>
                                                                        <div class="max-h-[20rem] w-full overflow-y-auto overflow-x-hidden thin-scrollbar pr-2">
                                                                            {!! renderDesktopRecursive($sub->children) !!}
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="py-1">
                                                                        <a href="{{ $sub->link }}" class="block text-sm font-normal text-gray-700 hover:text-[#013954] hover:translate-x-1 transition-transform duration-200 break-words whitespace-normal"> {{-- Removed capitalize --}}
                                                                            {{ getMenuLabel($sub->title) }}
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            {{-- STANDARD DROPDOWN --}}
                            @elseif ($isStandardDropdown)
                                <div x-show="openSub" x-cloak
                                     @mouseenter="openSub = true"
                                     @mouseleave="openSub = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute left-0 top-full mt-0 text-gray-800 bg-white border-t-2 border-[#013954] shadow-xl z-50 rounded-b-lg">

                                    <div class="p-2 min-w-[220px]">
                                        <ul class="flex flex-col py-1">
                                            @foreach ($menu->children as $child)
                                                <li x-data="{ openChild: false }" @mouseenter="openChild = true" @mouseleave="openChild = false" class="relative">
                                                    <a href="{{ $child->link }}" class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#013954] transition duration-150 rounded-md"> {{-- Removed capitalize --}}
                                                        <span>{{ getMenuLabel($child->title) }}</span>
                                                        @if ($child->children->count())
                                                            <svg class="w-3 h-3 text-gray-400 group-hover:text-[#013954]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                                                        @endif
                                                    </a>

                                                    @if ($child->children->count())
                                                        <ul x-show="openChild" x-cloak
                                                            x-transition:enter="transition ease-out duration-200"
                                                            x-transition:enter-start="opacity-0 -translate-x-2"
                                                            x-transition:enter-end="opacity-100 translate-x-0"
                                                            class="absolute left-full top-0 ml-1 bg-white border border-gray-200 rounded-lg shadow-md min-w-[200px] z-50 p-1">
                                                            @foreach ($child->children as $subchild)
                                                                <li>
                                                                    <a href="{{ $subchild->link }}" class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#013954] transition duration-150 rounded-md"> {{-- Removed capitalize --}}
                                                                        {{ getMenuLabel($subchild->title) }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</nav>