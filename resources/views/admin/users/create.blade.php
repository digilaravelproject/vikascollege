@extends('layouts.admin.app')
@section('title', 'Add New User')

@section('content')
    <div class="max-w-2xl p-6 mx-auto bg-white border border-gray-200 shadow-lg rounded-2xl">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Add New User</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="mt-6 space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 mt-1 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 mt-1 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 mt-1 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-3 py-2 mt-1 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Roles --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Assign Roles</label>
                <p class="text-xs text-gray-500">Select roles to assign. Leave blank for no roles.</p>
                <div class="mt-2 space-y-2">
                    @forelse($roles as $role)
                        <div class="flex items-center">
                            <input id="role-{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->name }}"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="role-{{ $role->id }}" class="ml-2 text-sm text-gray-800">{{ $role->name }}</label>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No roles found. Create roles first.</p>
                    @endforelse
                </div>
                @error('roles') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Submit Button --}}
            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Create User
                </button>
            </div>
        </form>
    </div>
@endsection
