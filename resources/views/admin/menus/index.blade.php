@extends('layouts.admin.app')

@section('content')
    <h1>Menus</h1>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">Add Menu</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Parent</th>
                <th>Order</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($menus as $menu)
                <tr>
                    <td>{{ $menu->id }}</td>
                    <td>{{ $menu->title }}</td>
                    <td>{{ optional($menu->parent)->title ?? '-' }}</td>
                    <td>{{ $menu->order }}</td>
                    <td>{{ $menu->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection