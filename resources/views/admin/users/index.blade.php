@extends('layouts.admin.app')
@section('title', 'Manage Users')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <a href="{{ route('admin.users.create') }}"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700">
                <i class="bi bi-plus-lg me-1"></i> Add New User
            </a>
        </div>

        {{-- Session Alerts --}}
        @if (session('success'))
            <div class="flex p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="bi bi-check-circle-fill me-3"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Name</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Email</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Roles</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-right text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    @forelse($user->getRoleNames() as $role)
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">{{ $role }}</span>
                                    @empty
                                        <span class="text-xs text-gray-400">No Role</span>
                                    @endforelse
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-blue-600 hover:text-blue-800">Edit</a>
                                    {{-- Add delete button here if you want --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">
                                    <i class="text-5xl text-gray-300 bi bi-people"></i>
                                    <p class="mt-3 text-lg font-medium">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
