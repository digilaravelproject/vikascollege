<section class="p-6 bg-white rounded-lg shadow-md">
    <div class="text-center">
        <h2 class="mb-2 text-2xl font-bold text-gray-800">{{ $title }}</h2>
        @if ($description)
            <p class="mb-6 text-gray-600">{{ $description }}</p>
        @endif
    </div>
    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No items found.</p>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($items as $item)
                <div class="p-6 text-center bg-gray-50 rounded-lg border">
                    @if ($item->icon_or_image)
                        <img class="object-contain w-16 h-16 mx-auto mb-4" src="{{ asset('storage/' . $item->icon_or_image) }}"
                            alt="{{ $item->title }}">
                    @endif
                    <h3 class="mb-2 text-lg font-bold text-gray-800">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $item->description }}</p>
                </div>
            @endforeach
        </div>
    @endif
</section>
