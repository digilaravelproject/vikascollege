@extends('layouts.app')

@section('title', $activeSection->title)

@section('content')
    <style>
        section>h2 {
            position: sticky;
            top: 0;
            background: #f9fafb;
            z-index: 10;
        }
    </style>
    <section class="container px-4 py-10 mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">

            {{-- Sidebar --}}
            @php
                function renderMenu($menus, $activeSection, $level = 1)
                {
                    $html = '<ul class="space-y-1">';

                    foreach ($menus as $menu) {
                        $hasChildren = $menu->childrenRecursive->count() > 0;
                        $isActive = ($activeSection->id ?? 0) === ($menu->page->id ?? 0);
                        $url = $menu->link;

                        // Track level (1 = main, 2 = child, 3+ = deeper)
                        $html .= '<li ' . ($level >= 2 && $hasChildren ? ' x-data="{ open: false }"' : '') . '>';

                        // Determine item rendering logic
                        if ($hasChildren && $level >= 2) {
                            // Only level 2+ menus are expandable
                            $html .=
                                '<div class="flex items-center justify-between w-full">
                                                                                    <a href="' .
                                e($url) .
                                '" class="flex-1 block px-4 py-2 text-sm font-medium transition-all duration-200 rounded-lg ' .
                                ($isActive
                                    ? 'bg-[#013954] text-white shadow-md'
                                    : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]') .
                                '">' .
                                e($menu->title) .
                                '
                                                                                    </a>
                                                                                    <button @click="open = !open"
                                                                                        class="px-2 text-gray-600 transition hover:text-[#013954] focus:outline-none"
                                                                                        title="Toggle submenu">
                                                                                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                                                                        </svg>
                                                                                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 15l-6-6-6 6" />
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>';
                        } else {
                            // Normal link for all other levels
                            $html .=
                                '<a href="' .
                                e($url) .
                                '" class="block px-4 py-2 text-sm font-medium transition-all duration-200 rounded-lg ' .
                                ($isActive
                                    ? 'bg-[#013954] text-white shadow-md'
                                    : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]') .
                                '">' .
                                e($menu->title) .
                                '</a>';
                        }

                        // Render children recursively
                        if ($hasChildren) {
                            if ($level >= 2) {
                                // Expandable for deeper levels
                                $html .= '<div x-show="open" x-collapse class="pl-4 mt-1">';
                                $html .= renderMenu($menu->childrenRecursive, $activeSection, $level + 1);
                                $html .= '</div>';
                            } else {
                                // Directly show child menus (level 1)
                                $html .= '<div class="pl-4 mt-1">';
                                $html .= renderMenu($menu->childrenRecursive, $activeSection, $level + 1);
                                $html .= '</div>';
                            }
                        }

                        $html .= '</li>';
                    }

                    $html .= '</ul>';
                    return $html;
                }
            @endphp


            {{-- Render the menu --}}
            <aside class="space-y-2 md:sticky md:top-24 h-fit">
                <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b">
                    {{ $topParent->title ?? 'Sections' }}
                </h2>

                {!! renderMenu($menus, $activeSection) !!}

            </aside>



            {{-- ==================== MAIN CONTENT ==================== --}}
            <main class="p-4 space-y-6 bg-white shadow-md rounded-2xl md:col-span-3">
                @php
                    $content = json_decode($activeSection->content, true);
                    $blocks = $content['blocks'] ?? $content ?? [];
                @endphp

                @if (!empty($blocks) && is_array($blocks))
                    @foreach ($blocks as $block)
                        <x-page-block :block="$block" />
                    @endforeach
                @else
                    <p class="italic text-gray-400">No content found for this page.</p>
                @endif

                @if (!empty($activeSection->pdf_path))
                    <div class="mt-8">
                        <x-pdf-viewer :src="asset('storage/' . $activeSection->pdf_path)" />
                    </div>
                @endif
            </main>

        </div>
    </section>
@endsection