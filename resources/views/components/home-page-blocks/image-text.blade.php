@php
    $layout = $block['layout'] ?? 'image_left';
    $image = $block['image'] ?? '';
    $content = $block['content'] ?? '';
@endphp

<div class="grid items-center gap-8 md:gap-12 md:grid-cols-2">
    {{-- Image --}}
    <div class="h-96 overflow-hidden rounded-lg shadow-lg
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
    {{-- Prose class typography ke liye best hai --}}
    <div
        class="prose prose-lg max-w-none text-gray-700 prose-headings:font-extrabold prose-headings:text-gray-900 prose-a:text-blue-600 hover:prose-a:underline">
        {!! $content !!}
    </div>
</div>
