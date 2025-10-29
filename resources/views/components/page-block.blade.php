@props(['block'])

@switch($block['type'] ?? '')
    @case('heading')
        <div style="
            font-size: {{ $block['fontSize'] ?? 24 }}px;
            color: {{ $block['color'] ?? '#000' }};
            text-align: {{ $block['textAlign'] ?? 'left' }};
            font-weight: {{ ($block['bold'] ?? false) ? 'bold' : 'normal' }};
            font-style: {{ ($block['italic'] ?? false) ? 'italic' : 'normal' }};
            text-decoration: {{ ($block['underline'] ?? false) ? 'underline' : 'none' }};
            line-height: {{ $block['lineHeight'] ?? 1.5 }};
            margin-bottom: 1rem;
        ">
            {!! $block['content'] ?? '' !!}
        </div>
    @break

    @case('text')
        <div style="
            font-size: {{ $block['fontSize'] ?? 16 }}px;
            color: {{ $block['color'] ?? '#000' }};
            text-align: {{ $block['textAlign'] ?? 'left' }};
            font-weight: {{ ($block['bold'] ?? false) ? 'bold' : 'normal' }};
            font-style: {{ ($block['italic'] ?? false) ? 'italic' : 'normal' }};
            text-decoration: {{ ($block['underline'] ?? false) ? 'underline' : 'none' }};
            line-height: {{ $block['lineHeight'] ?? 1.5 }};
            margin-bottom: 1rem;
        ">
            {!! $block['content'] ?? '' !!}
        </div>
    @break

    @case('image')
        <img src="{{ $block['src'] ?? '' }}"
             alt="{{ $block['alt'] ?? 'Image' }}"
             style="width: {{ $block['width'] ?? 'auto' }}; height: {{ $block['height'] ?? 'auto' }};"
             class="object-contain mb-6 rounded-lg shadow" loading="lazy" />
    @break

    @case('video')
        <div class="my-4">
            <video src="{{ $block['src'] ?? '' }}"
                   style="width: {{ $block['width'] ?? '100%' }}; height: {{ $block['height'] ?? '315px' }};"
                   class="rounded-lg shadow" controls></video>
        </div>
    @break

    @case('pdf')
        {{-- <iframe src="{{ $block['src'] ?? '' }}"
                style="width: {{ $block['width'] ?? '100%' }}; height: {{ $block['height'] ?? '600px' }};"
                class="mb-6 border rounded-lg"></iframe> --}}
<iframe src="{{ $block['src'] ?? '' }}" class="w-full h-[700px] border rounded-lg shadow-inner" frameborder="0"
    loading="lazy"></iframe>
                 {{-- <x-pdf-viewer :src="{{ $block['src'] ?? '' }}" /> --}}
    @break
@endswitch
