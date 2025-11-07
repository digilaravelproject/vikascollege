{{-- Updated typography --}}
<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">{{ $title }}</h2>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No testimonials found.</p>
@else
    {{-- 3-Column CARD-UI ko hata kar List/Stack me convert kiya --}}
    <div class="max-w-3xl mx-auto space-y-12">
        @foreach ($items as $item)
            <figure class="text-center">
                <img class="object-cover w-24 h-24 mx-auto mb-4 rounded-full shadow-lg"
                    src="{{ $item->student_image ? asset('storage/' . $item->student_image) : 'https://via.placeholder.com/100' }}"
                    alt="{{ $item->student_name }}">
                <blockquote class="mt-6">
                    <p class="text-xl font-medium text-gray-800 italic">
                        "{{ $item->testimonial_text }}"
                    </p>
                </blockquote>
                <figcaption class="mt-4">
                    <div class="font-bold text-gray-900">{{ $item->student_name }}</div>
                    <div class="text-sm text-gray-500">{{ $item->student_course ?? 'Alumni' }}</div>
                </figcaption>
            </figure>
        @endforeach
    </div>
@endif
