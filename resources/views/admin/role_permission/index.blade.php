@extends('layouts.admin.app')

@section('title', 'Roles & Permissions')

@section('content')
    {{-- MODIFIED: Standardized spacing --}}
    <div x-data="{ openRole: false, openPermission: false }" class="space-y-6">

        {{-- MODIFIED: Responsive header and standardized styling --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Roles & Permissions</h1>
            <div class="flex items-center gap-3">
                <button @click="openRole = true"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-person-plus me-2"></i> Add Role
                </button>
                <button @click="openPermission = true"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="bi bi-key me-2"></i> Add Permission
                </button>
            </div>
        </div>

        {{-- MODIFIED: Enhanced alert styling --}}
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

        {{-- MODIFIED: Enhanced card styling --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Access Control Matrix</h2>
                <p class="mt-1 text-sm text-gray-500">Changes are saved automatically when you toggle a permission.</p>
                {{-- REMOVED: Redundant "Save Changes" button, as toggles auto-save --}}
            </div>

            <div class="overflow-x-auto">
                {{-- REMOVED: Redundant <form> wrapper --}}
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    {{-- MODIFIED: Professional admin header style --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="sticky left-0 z-10 px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Permission</th>
                            @foreach ($roles as $role)
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                    {{ ucfirst($role->name) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($permissions as $permission)
                            <tr class="transition hover:bg-gray-50">
                                {{-- MODIFIED: Sticky first column for better horizontal scrolling --}}
                                <td class="sticky left-0 z-10 px-6 py-4 font-medium text-gray-900 bg-white whitespace-nowrap group-hover:bg-gray-50">
                                    {{ ucfirst($permission->name) }}
                                </td>
                                @foreach ($roles as $role)
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                x-data="{ checked: {{ $role->hasPermissionTo($permission) ? 'true' : 'false' }} }" x-model="checked"
                                                {{-- MODIFIED: Enhanced fetch with error handling and better toasts --}}
                                                @change="
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
                                                    })
                                                    .then(res => {
                                                        if (!res.ok) throw new Error('Network response was not ok');
                                                        return res.json();
                                                    })
                                                    .then(data => {
                                                        if(data.success){
                                                            $dispatch('notify', { message: 'Permission updated!', type: 'success' })
                                                        } else {
                                                            throw new Error(data.message || 'Server returned an error');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Fetch error:', error);
                                                        $dispatch('notify', { message: 'Failed to update: ' + error.message, type: 'error' });
                                                        checked = !checked; // Revert checkbox on failure
                                                    })
                                                "
                                                class="sr-only peer">
                                            {{-- MODIFIED: Themed toggle with focus rings --}}
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
                                @endforeach
                            </tr>
                        @empty
                            {{-- ADDED: Empty state --}}
                            <tr>
                                <td colspan="{{ $roles->count() + 1 }}" class="py-12 text-center text-gray-500">
                                    <i class="text-5xl text-gray-300 bi bi-key"></i>
                                    <p class="mt-3 text-lg font-medium">No permissions found</p>
                                    <p class="text-sm">Start by adding a new permission.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODIFIED: Added transitions and professional modal styling --}}
        <div x-show="openRole" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class_="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openRole = false"></div>

            <div class="relative w-full max-w-md bg-white shadow-lg rounded-xl"
                @click.away="openRole = false"
                x-show="openRole"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Role</h3>
                    <button @click="openRole = false" class="text-gray-400 hover:text-gray-600">
                        <i class="text-xl bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.roles-permissions.create-role') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <label for="role_name" class="block mb-1.5 text-sm font-medium text-gray-700">Role Name</label>
                        <input type="text" id="role_name" name="name" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Editor" required>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button type="button" @click="openRole = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700">Save Role</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODIFIED: Added transitions and professional modal styling --}}
        <div x-show="openPermission" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openPermission = false"></div>

            <div class="relative w-full max-w-md bg-white shadow-lg rounded-xl"
                @click.away="openPermission = false"
                x-show="openPermission"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Permission</h3>
                    <button @click="openPermission = false" class="text-gray-400 hover:text-gray-600">
                        <i class="text-xl bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.roles-permissions.create-permission') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <label for="permission_name" class="block mb-1.5 text-sm font-medium text-gray-700">Permission Name</label>
                        <input type="text" id="permission_name" name="name" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., edit posts" required>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button type="button" @click="openPermission = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700">Save Permission</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODIFIED: Upgraded to success/error toast --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
            @notify.window="
                message = $event.detail.message;
                type = $event.detail.type || 'success';
                show = true;
                setTimeout(() => show = false, 3000);
            "
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed z-50 flex items-center justify-between px-4 py-3 text-white rounded-lg shadow-lg bottom-5 right-5"
            :class="type === 'success' ? 'bg-blue-600' : 'bg-red-600'">

            <i :class="type === 'success' ? 'bi bi-check-circle' : 'bi bi-exclamation-triangle'" class="text-lg me-2"></i>
            <span x-text="message" class="font-medium"></span>

            <button @click="show = false" class="ml-3 -mr-1 transition text-white/70 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

    </div>
@endsection
