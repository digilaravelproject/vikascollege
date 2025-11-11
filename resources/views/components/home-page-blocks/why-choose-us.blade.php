{{--
File: resources/views/components/home-page/your-component-name.blade.php
(Complete Replacement)
--}}
@props(['title', 'description', 'items'])

{{-- === Main Title Section === --}}
<div class="text-center" data-aos="fade-up">

    {{--
    PERFECT TITLE:
    - Font: Serif (image jaisa)
    - Size: text-4xl (kam bulky)
    - Weight: font-medium
    --}}
    <h2 class="text-4xl font-medium text-gray-900 mb-4" style="font-family: 'Times New Roman', serif;">
        {{ $title }}
    </h2>

    {{-- PERFECT RED LINE: Patli aur choti --}}
    <div class="flex justify-center mb-6">
        <div class="w-20 h-0.5 bg-red-600"></div>
    </div>

    @if ($description)
        <p class="mb-4 text-lg text-gray-600 max-w-full md:max-w-5xl mx-auto text-center px-4 md:px-0">
            {{ $description }}
        </p>

    @endif

</div>
{{-- === End Main Title Section === --}}


@if ($items->isEmpty())
    <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="100">
        No items found.
    </p>
@else
    {{-- PERFECT GRID: Gap adjust kiya finishing ke liye --}}
    <div class="grid grid-cols-1 gap-8 md:grid-cols-3" data-aos="fade-up" data-aos-delay="100">

        @foreach ($items as $item)
            {{--
            PERFECT CARD (FINISHING):
            - Koi border nahi.
            - Halka, professional shadow (shadow-lg)
            - Padding (p-6) kam ki taaki bulky na lage.
            - border-gray-200 ka ek halka border finishing touch ke liye.
            --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg">

                @if ($item->icon_or_image)
                    <img class="object-contain w-full h-44 mx-auto mb-6" src="{{ asset('storage/' . $item->icon_or_image) }}"
                alt="{{ $item->title }}" loading="lazy"> @endif <h3
                    class="mb-2 text-lg font-semibold text-gray-900 text-center">
                    {{ $item->title }}
                </h3>

                {{-- PERFECT TEXT: Size (text-base) adjust kiya --}}
                <p class="text-base text-gray-600 text-center">
                    {{ $item->description }}
                </p>
            </div>
        @endforeach
    </div>
@endif
