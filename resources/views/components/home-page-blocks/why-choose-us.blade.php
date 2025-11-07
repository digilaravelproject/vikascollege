<div class="text-center">
    {{-- Updated typography --}}
    <h2 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">{{ $title }}</h2>
    @if ($description)
        <p class="mb-10 text-lg text-gray-600 max-w-2xl mx-auto">{{ $description }}</p>
    @endif
</div>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No items found.</p>
@else
    {{-- Card UI (border, shadow) hata kar clean flat icon grid banaya --}}
    <div class="grid grid-cols-1 gap-x-8 gap-y-12 md:grid-cols-3">
        @foreach ($items as $item)
            <div class="p-4 text-center">
                @if ($item->icon_or_image)
                    <img class="object-contain w-20 h-20 mx-auto mb-5" src="{{ asset('storage/' . $item->icon_or_image) }}"
                        alt="{{ $item->title }}">
                @endif
                <h3 class="mb-2 text-lg font-bold text-gray-900">{{ $item->title }}</h3>
                <p class="text-base text-gray-600">{{ $item->description }}</p>
            </div>
        @endforeach
    </div>
@endif
