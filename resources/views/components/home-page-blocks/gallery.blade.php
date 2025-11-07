{{-- Updated typography --}}
<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">{{ $title }}</h2>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No gallery images found.</p>
@else
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        @foreach ($items as $image)
            <a href="{{ asset('storage/' . $image->image) }}" class="block overflow-hidden rounded-lg shadow-md group"
                data-fancybox="gallery" data-caption="{{ $image->title ?? '' }}">
                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title ?? 'Gallery Image' }}"
                    class="object-cover w-full h-40 transition-transform duration-300 group-hover:scale-110">
            </a>
        @endforeach
    </div>
    <div class="mt-10 text-center">
        <a href="#" class="font-medium text-blue-600 hover:underline">View Full Gallery &rarr;</a>
    </div>
    {{-- Isko (data-fancybox) chalane ke liye aapko Fancybox JS/CSS add karna hoga --}}
@endif
