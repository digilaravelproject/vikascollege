@php
    $layout = $block['layout'] ?? 'left';
    $image = $block['image'] ?? '';
    $heading = $block['heading'] ?? '';
    $text = $block['text'] ?? '';
    $btnText = $block['buttonText'] ?? '';
    $btnHref = $block['buttonHref'] ?? '';
@endphp

<section class="p-6 bg-white rounded-lg shadow-md">
    <div class="grid items-center gap-6 md:gap-10
           {{ $layout === 'top' ? 'grid-cols-1' : 'md:grid-cols-2' }}
           {{ $layout === 'right' ? 'md:grid-cols-2' : '' }}">

        {{-- Image --}}
        <div class="
            {{ $layout === 'right' ? 'md:order-last' : '' }}
            {{ $layout === 'top' ? 'w-full h-64' : 'h-80' }}
            overflow-hidden rounded-lg">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $heading }}"
                    class="object-cover w-full h-full transition-transform duration-300 hover:scale-105">
            @else
                <div class="flex items-center justify-center w-full h-full bg-gray-200">
                    <span class="text-gray-500">Image</span>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="space-y-4 {{ $layout === 'top' ? 'text-center' : '' }}">
            <h2 class="text-3xl font-bold text-gray-800">{{ $heading }}</h2>
            <p class="text-gray-600">{{ $text }}</p>
            @if ($btnText && $btnHref)
                <a href="{{ $btnHref }}"
                    class="inline-block px-6 py-2 text-white transition-colors bg-blue-600 rounded-md shadow hover:bg-blue-700">
                    {{ $btnText }}
                </a>
            @endif
        </div>

    </div>
</section>
