<section class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">{{ $title }}</h2>
    @if ($items->isEmpty())
        <p class="text-center text-gray-500">No calendar items found.</p>
    @else
        <div class="flow-root">
            <ul class="-mb-8">
                @foreach ($items as $item)
                    <li>
                        <div class="relative pb-8">
                            @if (!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span
                                        class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full ring-8 ring-white">
                                        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-500">
                                        {{ $item->event_datetime->format('M d, Y') }}
                                    </p>
                                    <p class="font-medium text-gray-800">{{ $item->title }}</p>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
