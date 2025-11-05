<section class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">{{ $title }}</h2>

    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No updates found.</p>
    @else
        <div class="space-y-4">
            @foreach ($items as $notification)
                <div
                    class="flex items-center gap-4 p-4 transition-all bg-gray-50 border border-gray-200 rounded-lg hover:shadow-sm">
                    <span class="text-2xl">{{ $notification->icon }}</span>
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800">{{ $notification->title }}</p>
                        <span class="text-xs text-gray-500">{{ $notification->display_date->format('M d, Y') }}</span>
                    </div>
                    @if ($notification->href)
                        <a href="{{ $notification->href }}"
                            class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700"
                            target="_blank" rel="noopener noreferrer">
                            {{ $notification->button_name ?: 'View' }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
