<div class="text-center">
    {{-- Updated typography --}}
    <h2 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">{{ $title }}</h2>
    @if ($description)
        <p class="mb-10 text-lg text-gray-600 max-w-2xl mx-auto">{{ $description }}</p>
    @endif
</div>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No events found.</p>
@else
    {{-- CARD GRID se LIST VIEW me change kar diya --}}
    <div class="max-w-4xl mx-auto space-y-8">
        @foreach ($items as $item)
            <div
                class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center bg-white p-4 rounded-lg shadow-sm border transition-shadow hover:shadow-md">
                {{-- Image --}}
                <div class="md:col-span-4 lg:col-span-3">
                    <a href="#">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/400x250' }}"
                            alt="{{ $item->title }}" class="object-cover w-full h-48 md:h-full rounded-md shadow-md">
                    </a>
                </div>
                {{-- Content --}}
                <div class="md:col-span-8 lg:col-span-9">
                    <span class="text-sm font-semibold text-blue-600 uppercase">{{ $item->category->name ?? 'Event' }}</span>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">
                        <a href="#" class="hover:underline">{{ $item->title }}</a>
                    </h3>
                    <p class="mt-2 text-md font-medium text-gray-700">{{ $item->event_date->format('D, M d, Y') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $item->location ?? 'Venue Details' }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <a href="#" class="font-medium text-blue-600 hover:underline">View All Events &rarr;</a>
    </div>
@endif
