@extends('layouts.admin.app')

@section('title', 'Roles & Permissions')

@section('content')
    <div x-data="{ openRole: false, openPermission: false }" class="space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Roles & Permissions</h1>
            <div class="flex gap-3">
                <button @click="openRole = true"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">
                    <i class="bi bi-person-plus me-1"></i> Add Role
                </button>
                <button @click="openPermission = true"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow">
                    <i class="bi bi-key me-1"></i> Add Permission
                </button>
            </div>
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

        <!-- Roles & Permissions Table -->
        <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
            <div class="border-b px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-700">Access Control Matrix</h2>
                <button type="submit" form="rolePermissionForm"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
                    Save Changes
                </button>
            </div>

            <div class="overflow-x-auto">
                <form id="rolePermissionForm" action="{{ route('admin.roles-permissions.assign') }}" method="POST">
                    @csrf
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Permission</th>
                                @foreach($roles as $role)
                                    <th class="px-6 py-3 text-center font-medium">{{ ucfirst($role->name) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($permissions as $permission)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 font-medium">{{ ucfirst($permission->name) }}</td>
                                    @foreach($roles as $role)
                                        <td class="px-6 py-3 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                    x-data="{ checked: {{ $role->hasPermissionTo($permission) ? 'true' : 'false' }} }"
                                                    x-model="checked" @change="
                                                        fetch('{{ route('admin.roles-permissions.assign') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            },
                                                            body: JSON.stringify({
                                                                auto: true,
                                                                role_id: {{ $role->id }},
                                                                permission_id: {{ $permission->id }},
                                                                status: checked
                                                            })
                                                        }).then(res => res.json()).then(data => {
                                                            if(data.success){
                                                                $dispatch('notify', { message: 'Updated!' })
                                                            }
                                                        })
                                                    " class="sr-only peer">

                                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer
                                                           peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                           after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                           after:bg-white after:border-gray-300 after:border after:rounded-full
                                                           after:h-5 after:w-5 after:transition-all
                                                           peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                                </div>
                                            </label>

                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <!-- Add Role Modal -->
        <div x-show="openRole" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="openRole = false" class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-700">Add New Role</h3>
                <form action="{{ route('admin.roles-permissions.create-role') }}" method="POST">
                    @csrf
                    <input type="text" name="name" class="w-full border-gray-300 rounded-lg p-2.5 text-sm"
                        placeholder="Enter Role Name" required>
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="openRole = false"
                            class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Permission Modal -->
        <div x-show="openPermission" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="openPermission = false" class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-700">Add New Permission</h3>
                <form action="{{ route('admin.roles-permissions.create-permission') }}" method="POST">
                    @csrf
                    <input type="text" name="name" class="w-full border-gray-300 rounded-lg p-2.5 text-sm"
                        placeholder="Enter Permission Name" required>
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="openPermission = false"
                            class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium">Save</button>
                    </div>
                </form>
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