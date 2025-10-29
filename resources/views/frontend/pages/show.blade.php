@extends('layouts.app')

@section('title', $activeSection->title)

@section('content')
    <section class="container px-4 py-10 mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">

            {{-- Sidebar --}}
            @php
                function renderMenu($menus, $activeSection)
                {
                    $html = '<ul class="space-y-1">';

                    foreach ($menus as $menu) {
                        $isActive = ($activeSection->id ?? 0) === ($menu->page->id ?? 0);
                        $url = $menu->link;

                        $html .= '<li>';
                        $html .= '<a href="' . $url . '" class="block px-4 py-2 text-sm font-medium transition-all duration-200 rounded-lg ' .
                            ($isActive ? 'bg-[#013954] text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]') . '">' .
                            $menu->title . '</a>';

                        if ($menu->childrenRecursive->count()) {
                            $html .= renderMenu($menu->childrenRecursive, $activeSection); // recursion
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



            {{-- Main Content --}}
            <main class="p-6 space-y-6 bg-white shadow-md rounded-2xl md:col-span-3">
                @php
                    $blocks = json_decode($activeSection->content, true);
                @endphp

                @if(is_array($blocks))
                    @foreach($blocks as $block)
                        <x-page-block :block="$block" />
                    @endforeach
                @endif

                @if($activeSection->pdf_path)
                    <div class="mt-8">
                        <x-pdf-viewer :src="asset('storage/' . $activeSection->pdf_path)" />
                    </div>
                @endif
            </main>
        </div>
    </section>
@endsection
