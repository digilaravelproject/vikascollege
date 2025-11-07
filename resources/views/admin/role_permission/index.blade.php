@extends('layouts.admin.app')

@section('title', 'Roles & Permissions')

@section('content')
    {{-- MODIFIED: Added openCategory state for accordion --}}
    <div x-data="{ openRole: false, openPermission: false, openCategory: '{{ array_key_first($groupedPermissions ?? []) }}' }"
        class="space-y-4">

        {{-- Header (MODIFIED: Compact) --}}
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
            <div class="flex items-center gap-2">
                <button @click="openRole = true"
                    class="flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-person-plus me-1.5"></i> Add Role
                </button>
                <button @click="openPermission = true"
                    class="flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="bi bi-key me-1.5"></i> Add Permission
                </button>
            </div>
        </div>

        {{-- Session Alerts (MODIFIED: Compact) --}}
        @if (session('success'))
            <div class="flex p-3 text-xs text-green-700 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="flex p-3 text-xs text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Permission Matrix Card (MODIFIED: Compact) --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-800">Access Control Matrix</h2>
                <p class="mt-1 text-xs text-gray-500">Changes are saved automatically when you toggle a permission.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="sticky left-0 z-10 px-4 py-2 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Permission</th>
                            @foreach ($roles as $role)
                                <th scope="col"
                                    class="px-4 py-2 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                    {{ ucfirst($role->name) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($groupedPermissions as $categoryName => $permissions)
                            {{-- MODIFIED: Category Header Row (Clickable) --}}
                            <tr class="bg-gray-100 cursor-pointer hover:bg-gray-200"
                                @click="openCategory = (openCategory === '{{ $categoryName }}' ? null : '{{ $categoryName }}')">
                                <td colspan="{{ $roles->count() + 1 }}" class="px-4 py-2 text-sm font-semibold text-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span>{{ $categoryName }}</span>
                                        <i class="bi bi-chevron-down transition-transform duration-200"
                                            :class="{ 'rotate-180': openCategory === '{{ $categoryName }}' }"></i>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODIFIED: Collapsible Permission Rows --}}
                            @foreach($permissions as $permission)
                                <tr class="transition hover:bg-gray-50" x-show="openCategory === '{{ $categoryName }}'"
                                    x-transition>
                                    <td
                                        class="sticky left-0 z-10 px-4 py-2 text-xs font-normal text-gray-800 bg-white whitespace-nowrap group-hover:bg-gray-50">
                                        <span class="ml-4">{{ ucfirst($permission->name) }}</span>
                                    </td>

                                    @foreach ($roles as $role)
                                        <td class="px-4 py-2 text-center whitespace-nowrap">
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
                                                                        })
                                                                        .then(res => res.json())
                                                                        .then(data => {
                                                                            if(data.success){
                                                                                $dispatch('notify', { message: 'Permission updated!', type: 'success' })
                                                                            } else {
                                                                                throw new Error(data.message || 'Server error');
                                                                            }
                                                                        })
                                                                        .catch(error => {
                                                                            $dispatch('notify', { message: 'Update failed!', type: 'error' });
                                                                            checked = !checked; // Revert
                                                                        })
                                                                    " class="sr-only peer">
                                                {{-- MODIFIED: Smaller Toggle --}}
                                                <div
                                                    class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                                    peer peer-checked:after:translate-x-4 peer-checked:after:border-white
                                                                    after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                                                    after:h-4 after:w-4 after:transition-all
                                                                    peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                                </div>
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ $roles->count() + 1 }}" class="py-10 text-center text-gray-500">
                                    <i class="text-4xl text-gray-300 bi bi-key"></i>
                                    <p class="mt-2 text-base font-medium">No permissions found</p>
                                    <p class="text-xs">Start by adding a new permission.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Partials (Yeh files bhi update karni hongi) --}}
        @include('admin.role_permission.partials.add-role-modal')
        @include('admin.role_permission.partials.add-permission-modal')
        @include('admin.role_permission.partials.toast')

    </div>
@endsection
