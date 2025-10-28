@extends('layouts.admin.app')

@section('title', 'Menu Management')

@section('content')
    <div x-data="{ openMenu: false }" class="space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Menu Management</h1>
            <a href="{{ route('admin.menus.create') }}"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                <i class="bi bi-plus-circle me-1"></i> Add Menu
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menu Table -->
        <div class="overflow-hidden bg-white border border-gray-100 shadow rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-700">Website Menus</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead class="text-gray-600 bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-medium text-left">Title</th>
                            <th class="px-6 py-3 font-medium text-left">Parent</th>
                            <th class="px-6 py-3 font-medium text-center">Order</th>
                            <th class="px-6 py-3 font-medium text-center">Status</th>
                            <th class="px-6 py-3 font-medium text-center">Actions</th>
                        </tr>
                    </thead>
              <tbody class="text-gray-700 divide-y divide-gray-100">
    @foreach($menus->where('parent_id', null)->sortBy('order') as $menu)
        <!-- Parent Menu -->
        <tr class="transition bg-gray-50 hover:bg-gray-100">
            <td class="px-6 py-3 font-semibold text-gray-900">
                <i class="text-blue-600 bi bi-folder-fill me-1"></i>
                {{ $menu->title }}
            </td>
            <td class="px-6 py-3">—</td>
            <td class="px-6 py-3 text-center">{{ $menu->order }}</td>
            <td class="px-6 py-3 text-center">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                        x-data="{ checked: {{ $menu->status ? 'true' : 'false' }} }"
                        x-model="checked"
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
            <td class="flex justify-center gap-2 px-6 py-3 text-center">
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

        <!-- Child Menus -->
        @foreach($menu->children->sortBy('order') as $child)
            <tr class="transition hover:bg-gray-50">
                <td class="px-10 py-3 text-gray-700">
                    <i class="text-gray-500 bi bi-arrow-return-right me-1"></i>
                    {{ $child->title }}
                </td>
                <td class="px-6 py-3">{{ $menu->title }}</td>
                <td class="px-6 py-3 text-center">{{ $child->order }}</td>
                <td class="px-6 py-3 text-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                            x-data="{ checked: {{ $child->status ? 'true' : 'false' }} }"
                            x-model="checked"
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
                <td class="flex justify-center gap-2 px-6 py-3 text-center">
                    <a href="{{ route('admin.menus.edit', $child->id) }}"
                        class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg font-medium transition">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this submenu?')"
                            class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Sub-submenus -->
            @foreach($child->children->sortBy('order') as $subchild)
                <tr class="transition hover:bg-gray-50">
                    <td class="py-3 italic text-gray-600 px-14">
                        <i class="text-gray-400 bi bi-dash-lg me-1"></i>
                        {{ $subchild->title }}
                    </td>
                    <td class="px-6 py-3">{{ $child->title }}</td>
                    <td class="px-6 py-3 text-center">{{ $subchild->order }}</td>
                    <td class="px-6 py-3 text-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                x-data="{ checked: {{ $subchild->status ? 'true' : 'false' }} }"
                                x-model="checked"
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
                    <td class="flex justify-center gap-2 px-6 py-3 text-center">
                        <a href="{{ route('admin.menus.edit', $subchild->id) }}"
                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg font-medium transition">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('admin.menus.destroy', $subchild->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this sub-submenu?')"
                                class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
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
            class="fixed px-4 py-2 text-white bg-green-600 rounded-lg shadow-lg bottom-5 right-5">
            <span x-text="message"></span>
        </div>

    </div>
@endsection
