@forelse($pages as $page)
    <tr class="transition hover:bg-gray-50">
        {{-- Sahi Serial Number (Page change hone par bhi sahi rahega) --}}
        <td class="hidden px-6 py-4 text-gray-500 whitespace-nowrap lg:table-cell">
            {{ ($pages->currentPage() - 1) * $pages->perPage() + $loop->iteration }}
        </td>
        
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="font-medium text-gray-900">{{ $page->title }}</div>
            <div class="font-mono text-xs text-gray-500 lg:hidden">/{{ $page->slug }}</div>
        </td>

        <td class="hidden px-6 py-4 whitespace-nowrap lg:table-cell">
            <div class="font-mono text-xs text-gray-600">/{{ $page->slug }}</div>
        </td>

        <td class="hidden px-6 py-4 whitespace-nowrap sm:table-cell">
            <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full {{ $page->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $page->status ? 'Active' : 'Disabled' }}
            </span>
        </td>

        <td class="hidden px-6 py-4 text-gray-500 whitespace-nowrap sm:table-cell">
            {{ $page->updated_at->diffForHumans() }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center">
            <div class="flex flex-wrap items-center justify-center gap-2">
                
                {{-- Toggle Status --}}
                <form action="{{ route('admin.pagebuilder.toggleStatus', $page) }}" method="POST" class="inline toggle-status-form" data-message="{{ $page->status ? 'Disable' : 'Enable' }} this page?">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs {{ $page->status ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }} rounded-md font-medium transition focus:outline-none">
                        <i class="bi {{ $page->status ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i> {{ $page->status ? 'Disable' : 'Enable' }}
                    </button>
                </form>

                {{-- Builder --}}
                <a href="{{ route('admin.pagebuilder.builder', $page) }}" class="px-3 py-1.5 text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-md font-semibold transition">
                    <i class="bi bi-grid-1x2 me-1"></i> Builder
                </a>

                {{-- Edit --}}
                <a href="{{ route('admin.pagebuilder.edit', $page) }}" class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>

                {{-- Delete FIX: Yahan 'admin.pagebuilder.delete' use kiya hai --}}
                <form action="{{ route('admin.pagebuilder.delete', $page) }}" method="POST" class="inline delete-form">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>

            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="py-12 text-center text-gray-500">
            <i class="text-5xl text-gray-300 bi bi-file-earmark-plus"></i>
            <p class="mt-3 text-lg font-medium">No pages found</p>
        </td>
    </tr>
@endforelse