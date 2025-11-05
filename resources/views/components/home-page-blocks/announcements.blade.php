<section class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">{{ $title }}</h2>
    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No announcements found.</p>
    @else
        <ul class="space-y-3 list-disc list-inside">
            @foreach ($items as $item)
                <li class="text-gray-700">
                    <a href="#" class="font-medium hover:underline hover:text-blue-600">
                        {{ $item->title }}
                    </a>
                    <span class="text-sm text-gray-500"> - {{ $item->created_at->format('M d') }}</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-6 text-center">
            <a href="#" class="text-sm font-medium text-blue-600 hover:underline">View All Announcements &rarr;</a>
        </div>
    @endif
</section>
