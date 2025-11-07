@php
    $title = $block['title'] ?? 'Useful Links';
    $columns = $block['columns'] ?? 3;
    $items = $block['items'] ?? [];

    $colClass = match ((int) $columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };
@endphp

{{-- Updated typography --}}
<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">{{ $title }}</h2>

@if (!empty($items))
    <div class="grid {{ $colClass }} gap-5">
        @foreach ($items as $item)
            <a href="{{ $item['href'] ?? '#' }}"
                class="relative block p-5 font-medium text-gray-800 transition-all duration-300 transform bg-white border rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 hover:text-blue-600">
                {{ $item['title'] ?? 'Link Item' }}
                @if ($item['isNew'] ?? false)
                    <span
                        class="absolute top-0 right-0 px-2 py-0.5 -mt-2 -mr-2 text-xs font-bold text-white bg-red-500 rounded-full">NEW</span>
                @endif
            </a>
        @endforeach
    </div>
@else
    <p class="text-center text-gray-500">No links have been added to this section yet.</p>
@endif
