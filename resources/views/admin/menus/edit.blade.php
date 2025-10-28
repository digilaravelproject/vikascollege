@extends('layouts.admin.app')
@section('title', 'Edit Menu')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Edit Menu</h1>
            <a href="{{ route('admin.menus.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg shadow">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-lg bg-red-100 text-red-700 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.menus.form', ['menu' => $menu])
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
                    <i class="bi bi-pencil-square"></i> Update Menu
                </button>
            </div>
        </form>
    </div>
@endsection