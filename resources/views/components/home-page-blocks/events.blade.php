<section class="p-6 bg-white rounded-lg shadow-md">
    <div class="text-center">
        <h2 class="mb-2 text-2xl font-bold text-gray-800">{{ $title }}</h2>
        @if ($description)
            <p class="mb-6 text-gray-600">{{ $description }}</p>
        @endif
    </div>

    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No events found.</p>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($items as $item)
                <div class="overflow-hidden bg-gray-50 border rounded-lg shadow-sm">
                    <a href="#">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/400x250' }}"
                            alt="{{ $item->title }}"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105">
                    </a>
                    <div class="p-4">
                        <span
                            class="text-xs font-semibold text-blue-600 uppercase">{{ $item->category->name ?? 'Event' }}</span>
                        <h3 classs="mt-1 font-bold text-gray-800">
                            <a href="#" class="hover:underline">{{ $item->title }}</a>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $item->event_date->format('D, M d, Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="#" class="text-sm font-medium text-blue-600 hover:underline">View All Events &rarr;</a>
        </div>
    @endif
</section>
