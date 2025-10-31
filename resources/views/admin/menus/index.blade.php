@extends('layouts.admin.app')

@section('title', 'Menu Management')

@section('content')
    <div class="space-y-6">

        {{-- MODIFIED: Increased heading size for better hierarchy --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Menu Management</h1>
            <a href="{{ route('admin.menus.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Menu
            </a>
        </div>

        {{-- MODIFIED: Enhanced alert styling with icons --}}
        @if (session('success'))
            <div class="flex p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="bi bi-check-circle-fill me-3"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                <div>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- MODIFIED: Stronger shadow and border for a cleaner card --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Website Menus</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    {{-- MODIFIED: Professional admin header style --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Title</th>
                            {{-- ADDED: New URL / Slug column, responsive --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:table-cell">
                                URL / Slug</th>
                            {{-- MODIFIED: Hidden on small/medium screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase lg:table-cell">
                                Parent</th>
                            {{-- MODIFIED: Hidden on small/medium screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase lg:table-cell">
                                Order</th>
                            {{-- MODIFIED: Hidden on smallest screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase sm:table-cell">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($menus->where('parent_id', null)->sortBy('order') as $menu)
                    <tbody x-data="{ open: true }" class="group">

                        {{-- MODIFIED: Cleaner hover state --}}
                        <tr class="transition cursor-pointer bg-gray-50 hover:bg-gray-100" @click="open = !open">
                            <td class="flex items-center px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                <i :class="open ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"
                                    class="text-gray-600 transition-transform duration-200 me-2"></i>
                                <i class="text-blue-600 bi bi-folder-fill me-2"></i>
                                {{ $menu->title }}
                            </td>
                            {{-- ADDED: URL data, responsive --}}
                            <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                {{ $menu->url }}
                            </td>
                            {{-- MODIFIED: Responsive --}}
                            <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">—</td>
                            {{-- MODIFIED: Responsive --}}
                            <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                {{ $menu->order }}</td>
                            {{-- MODIFIED: Responsive, themed toggle to blue, added focus rings --}}
                            <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-data="{ checked: {{ $menu->status ? 'true' : 'false' }} }" x-model="checked"
                                        @change="
                                                    fetch('{{ route('admin.menus.toggle-status', $menu->id) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        },
                                                        body: JSON.stringify({ status: checked })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success){
                                                            $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                        } else {
                                                            $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                        }
                                                    })
                                                "
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                after:bg-white after:border-gray-300 after:border after:rounded-full
                                                after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                    </div>
                                </label>
                            </td>
                            {{-- MODIFIED: Refined action buttons --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                        class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this menu?')"
                                            class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @foreach ($menus->where('parent_id', $menu->id)->sortBy('order') as $child)
                            <tr x-show="open" x-transition class="transition hover:bg-gray-50">
                                <td class="flex items-center px-10 py-4 text-gray-700 whitespace-nowrap">
                                    <i class="text-gray-400 bi bi-arrow-return-right me-2"></i>
                                    {{ $child->title }}
                                </td>
                                {{-- ADDED: URL data, responsive --}}
                                <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                    {{ $child->url }}
                                </td>
                                {{-- MODIFIED: Responsive --}}
                                <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                    {{ $menu->title }}</td>
                                {{-- MODIFIED: Responsive --}}
                                <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                    {{ $child->order }}</td>
                                {{-- MODIFIED: Responsive, themed toggle to blue --}}
                                <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $child->status ? 'true' : 'false' }} }" x-model="checked"
                                            @change="
                                                        fetch('{{ route('admin.menus.toggle-status', $child->id) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            },
                                                            body: JSON.stringify({ status: checked })
                                                        })
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            if(data.success){
                                                                $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                            } else {
                                                                $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                            }
                                                        })
                                                    "
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                        </div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.menus.edit', $child->id) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this submenu?')"
                                                class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($menus->where('parent_id', $child->id)->sortBy('order') as $subchild)
                                <tr x-show="open" x-transition class="transition hover:bg-gray-50">
                                    <td class="flex items-center py-4 italic text-gray-600 px-14 whitespace-nowrap">
                                        <i class="text-gray-400 bi bi-dash-lg me-2"></i>
                                        {{ $subchild->title }}
                                    </td>
                                    {{-- ADDED: URL data, responsive --}}
                                    <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                        {{ $subchild->url }}
                                    </td>
                                    {{-- MODIFIED: Responsive --}}
                                    <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                        {{ $child->title }}</td>
                                    {{-- MODIFIED: Responsive --}}
                                    <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                        {{ $subchild->order }}</td>
                                    {{-- MODIFIED: Responsive, themed toggle to blue --}}
                                    <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-data="{ checked: {{ $subchild->status ? 'true' : 'false' }} }" x-model="checked"
                                                @change="
                                                            fetch('{{ route('admin.menus.toggle-status', $subchild->id) }}', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                },
                                                                body: JSON.stringify({ status: checked })
                                                            })
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                if(data.success){
                                                                    $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                                } else {
                                                                    $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                                }
                                                            })
                                                        "
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                        peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                                        after:h-5 after:w-5 after:transition-all
                                                        peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                            </div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.menus.edit', $subchild->id) }}"
                                                class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.menus.destroy', $subchild->id) }}"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Delete this sub-submenu?')"
                                                    class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                @empty
                    {{-- ADDED: Empty state row --}}
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="text-4xl text-gray-300 bi bi-list-ul"></i>
                            <p class="mt-2 text-lg font-medium">No menus found</p>
                            <p class="text-sm">Get started by creating a new menu.</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODIFIED: Now supports 'success' (blue) and 'error' (red) types --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
            @notify.window="
                message = $event.detail.message;
                type = $event.detail.type || 'success';
                show = true;
                setTimeout(() => show = false, 3000);
            "
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed z-50 flex items-center justify-between px-4 py-3 text-white rounded-lg shadow-lg bottom-5 right-5"
            :class="type === 'success' ? 'bg-blue-600' : 'bg-red-600'">

            <i :class="type === 'success' ? 'bi bi-check-circle' : 'bi bi-exclamation-triangle'"
                class="text-lg me-2"></i>
            <span x-text="message" class="font-medium"></span>

            <button @click="show = false" class="ml-3 -mr-1 transition text-white/70 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

    </div>
@endsection
