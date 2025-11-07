{{-- Updated typography --}}
<h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">{{ $title }}</h2>

@if ($items->isEmpty())
    <p class="text-center text-gray-500">No calendar items found.</p>
@else
    <div class="flow-root max-w-2xl mx-auto">
        <ul class="-mb-8">
            @foreach ($items as $item)
                <li>
                    <div class="relative pb-8">
                        @if (!$loop->last)
                            <span class="absolute top-4 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-4">
                            <div>
                                <span
                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-8 ring-white">
                                    <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0 pt-1.5">
                                <p class="text-sm text-gray-500">
                                    {{ $item->event_datetime->format('M d, Y') }}
                                </p>
                                <p class="font-semibold text-lg text-gray-800">{{ $item->title }}</p>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
