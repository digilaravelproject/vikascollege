@php
    $layout = $block['layout'] ?? 'left';
    $image = $block['image'] ?? '';
    $heading = $block['heading'] ?? '';
    $text = $block['text'] ?? '';
    $btnText = $block['buttonText'] ?? '';
    $btnHref = $block['buttonHref'] ?? '';
    $shortText = Str::limit(strip_tags($text), 1500); // Sirf 1500 chars show karega
@endphp

<section class="p-6 bg-white rounded-lg shadow-md relative">
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

            <p class="text-gray-600 text-justify text-wrap" id="short-text-{{ $loop->index ?? 1 }}">
                {!! $shortText !!}
                @if (strlen(strip_tags($text)) > 1500)
                    <button onclick="openModal({{ $loop->index ?? 1 }})" class="text-blue-600 hover:text-blue-800">Read
                        more</button>
                @endif
            </p>

            @if ($btnText && $btnHref)
                <a href="{{ $btnHref }}"
                    class="inline-block px-6 py-2 text-white transition-colors bg-blue-600 rounded-md shadow hover:bg-blue-700">
                    {{ $btnText }}
                </a>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    <div id="modal-{{ $loop->index ?? 1 }}"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-2 rounded-lg shadow-lg max-w-lg w-full relative">
            <button onclick="closeModal({{ $loop->index ?? 1 }})"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>


            <h2 class="text-2xl font-bold px-4 mb-4">{{ $heading }}</h2>
            <div class="text-gray-700 px-4 max-h-[80vh] overflow-y-auto text-justify text-wrap">{!! $text !!}</div>

        </div>
    </div>
</section>

{{-- JS --}}
<script>
    function openModal(id) {
        document.getElementById(`modal-${id}`).classList.remove('hidden');
        document.getElementById(`modal-${id}`).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(`modal-${id}`).classList.add('hidden');
        document.getElementById(`modal-${id}`).classList.remove('flex');
    }
</script>
