@extends('layouts.admin.app')

@section('title', 'Menu Management')

@section('content')
    <div x-data="{ openMenu: false }" class="space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Menu Management</h1>
            <a href="{{ route('admin.menus.create') }}"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">
                <i class="bi bi-plus-circle me-1"></i> Add Menu
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-lg bg-green-100 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-lg bg-red-100 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menu Table -->
        <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
            <div class="border-b px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-700">Website Menus</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium">Title</th>
                            <th class="px-6 py-3 text-left font-medium">Parent</th>
                            <th class="px-6 py-3 text-center font-medium">Order</th>
                            <th class="px-6 py-3 text-center font-medium">Status</th>
                            <th class="px-6 py-3 text-center font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($menus as $menu)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium">{{ $menu->title }}</td>
                                <td class="px-6 py-3">{{ $menu->parent->title ?? '-' }}</td>
                                <td class="px-6 py-3 text-center">{{ $menu->order }}</td>
                                <td class="px-6 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $menu->status ? 'true' : 'false' }} }"
                                            x-model="checked" @change="
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
                                                                    $dispatch('notify', { message: 'Status updated!' })
                                                                } else {
                                                                    $dispatch('notify', { message: 'Failed to update status' })
                                                                }
                                                            })
                                                           " class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer
                                                               peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                               after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                               after:bg-white after:border-gray-300 after:border after:rounded-full
                                                               after:h-5 after:w-5 after:transition-all
                                                               peer-checked:bg-green-600 transition-colors duration-300 ease-in-out">
                                        </div>
                                    </label>
                                </td>
                                <td class="px-6 py-3 text-center flex justify-center gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                        class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg font-medium transition">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this menu?')"
                                            class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No menus found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Notification Toast -->
        <div x-data="{ show: false, message: '' }" @notify.window="
                            message = $event.detail.message;
                            show = true;
                            setTimeout(() => show = false, 2000);
                         " x-show="show" x-transition
            class="fixed bottom-5 right-5 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
            <span x-text="message"></span>
        </div>

    </div>
@endsection