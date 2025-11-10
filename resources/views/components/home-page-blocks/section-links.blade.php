@php
    // Data Class se nahi, seedha $block array se aa raha hai
    $links = $block['links'] ?? [];
    $colClass = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'; // Default 3 columns
@endphp

<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight" data-aos="fade-up">
    {{ $title }} {{-- $title Class se aa raha hai --}}
</h2>

@if (!empty($links))
    <div class="grid {{ $colClass }} gap-5" data-aos="fade-up" data-aos-delay="100">
        @foreach ($links as $link)
            <a href="{{ $link['url'] ?? '#' }}"
                class="relative block p-5 font-medium text-gray-800 transition-all duration-300 transform bg-white border rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 hover:text-blue-600">

                {{ $link['text'] ?? 'Link Item' }}

                {{-- @if ($link['isNew'] ?? false) --}} {{-- Agar 'isNew' ka feature add karein toh --}}
                {{-- <span
                    class="absolute top-0 right-0 px-2 py-0.5 -mt-2 -mr-2 text-xs font-bold text-white bg-red-500 rounded-full">NEW</span>
                --}}
                {{-- @endif --}}
            </a>
        @endforeach
    </div>
@else
    <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="100">
        No links have been added to this section yet.
    </p>
@endif
