<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-12 tracking-tight" data-aos="fade-up">
    {{ $title }}
</h2>

@if ($items->isEmpty())
    <p class="text-center text-gray-500" data-aos="fade-up" data-aos-delay="100">
        No announcements found.
    </p>
@else
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-sm border" data-aos="fade-up" data-aos-delay="100">
        <ul class="space-y-4 divide-y divide-gray-100">
            @foreach ($items as $item)
                <li class="pt-4 first:pt-0">
                    <a href="#" class="font-semibold text-lg text-gray-800 hover:underline hover:text-blue-600">
                        {{ $item->title }}
                    </a>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm text-gray-500">{{ $item->created_at->format('M d, Y') }}</span>
                        @if ($item->is_new) {{-- Example condition --}}
                            <span class="px-2 py-0.5 text-xs font-medium text-white bg-red-500 rounded-full">NEW</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-8 text-center">
            <a href="#" class="font-medium text-blue-600 hover:underline">View All Announcements &rarr;</a>
        </div>
    </div>
@endif
