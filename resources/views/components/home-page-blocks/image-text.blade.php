@php
    $layout = $block['layout'] ?? 'image_left';
    $image = $block['image'] ?? '';
    $content = $block['content'] ?? '';
@endphp

<section class="p-6 bg-white rounded-lg shadow-md">
    <div class="grid items-center gap-6 md:gap-10 md:grid-cols-2">
        {{-- Image --}}
        <div class="h-80 overflow-hidden rounded-lg
            {{ $layout === 'content_left' ? 'md:order-last' : '' }}">
            @if ($image)
                <img src="{{ $image }}" alt="Section Image"
                    class="object-cover w-full h-full transition-transform duration-300 hover:scale-105">
            @else
                <div class="flex items-center justify-center w-full h-full bg-gray-200">
                    <span class="text-gray-500">Image</span>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="prose max-w-none text-gray-600">
            {!! $content !!}
        </div>
    </div>
</section>
