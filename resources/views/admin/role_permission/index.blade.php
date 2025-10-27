@extends('layouts.admin.app')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('content')

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Add Role & Permission --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.roles-permissions.create-role') }}" method="POST" class="d-flex">
                    @csrf
                    <input type="text" name="name" class="form-control me-2" placeholder="New Role" required>
                    <button class="btn btn-sm btn-primary">Add Role</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.roles-permissions.create-permission') }}" method="POST" class="d-flex">
                    @csrf
                    <input type="text" name="name" class="form-control me-2" placeholder="New Permission" required>
                    <button class="btn btn-sm btn-success">Add Permission</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Roles & Permissions Table --}}
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.roles-permissions.assign') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Permissions \ Roles</th>
                            @foreach($roles as $role)
                                <th>{{ ucfirst($role->name) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td class="text-start">{{ ucfirst($permission->name) }}</td>
                                @foreach($roles as $role)
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="permissions[{{ $role->id }}][{{ $permission->id }}]"
                                            class="form-check-input"
                                            {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        </form>
    </div>
</div>

@endsection
