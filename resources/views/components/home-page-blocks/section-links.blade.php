@php
    $title = $block['title'] ?? 'Useful Links';
    $columns = $block['columns'] ?? 3;
    $items = $block['items'] ?? [];

    $colClass = match ((int) $columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
    };
@endphp

<section class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">{{ $title }}</h2>
    @if (!empty($items))
        <div class="grid {{ $colClass }} gap-4">
            @foreach ($items as $item)
                <a href="{{ $item['href'] ?? '#' }}"
                    class="relative block p-4 font-medium text-gray-700 transition-all bg-gray-50 border rounded-lg hover:bg-blue-50 hover:shadow-sm hover:text-blue-600">
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
</section>
