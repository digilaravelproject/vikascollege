<section class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">{{ $title }}</h2>
    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No testimonials found.</p>
    @else
        {{-- Yeh ek basic grid hai. Aap yahan 'Swiper.js' jaisa slider use kar sakte hain --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($items as $item)
                <div class="flex flex-col items-center p-6 text-center bg-gray-50 rounded-lg border">
                    <img class="object-cover w-20 h-20 mb-4 rounded-full shadow-lg"
                        src="{{ $item->student_image ? asset('storage/' . $item->student_image) : 'https://via.placeholder.com/100' }}"
                        alt="{{ $item->student_name }}">
                    <p class="mb-3 italic text-gray-600">"{{ $item->testimonial_text }}"</p>
                    <h4 class="font-bold text-gray-800">{{ $item->student_name }}</h4>
                </div>
            @endforeach
        </div>
    @endif
</section>
