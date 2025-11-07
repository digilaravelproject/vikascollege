{{--
Yahi hai naya COMPACT design.
- Padding py-3 px-3 (kam kar di)
- Gap gap-3 (kam kar diya)
- Shadow hata kar border-b use kiya hai
--}}
<a href="{{ $notification->href ?: '#' }}" target="_blank" rel="noopener noreferrer"
    class="flex items-center gap-3 py-3 px-3 border-b border-gray-100 group transition-all duration-300 hover:bg-gray-50 rounded-lg">

    {{-- Icon: w-10 h-10 (chhota kar diya) --}}
    <div
        class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
        {{-- Icon Size: text-xl (chhota kar diya) --}}
        <span class="text-xl">{{ $notification->icon }}</span>
    </div>

    {{-- Title and Date --}}
    <div class="flex-grow min-w-0"> {{-- min-w-0 zaroori hai text ko wrap karne ke liye --}}
        {{-- Font Size: text-base (normal) kiya, text-lg hataya --}}
        <p class="font-medium text-gray-800 group-hover:text-blue-700 transition-colors duration-300 truncate">
            {{ $notification->title }}
        </p>
        <span class="text-sm text-gray-500">{{ $notification->display_date->format('M d, Y') }}</span>
    </div>

    {{-- Arrow Animation on Hover --}}
    <div
        class="flex-shrink-0 text-blue-600 opacity-0 group-hover:opacity-100 transform -translate-x-3 group-hover:translate-x-0 transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </div>
</a>
