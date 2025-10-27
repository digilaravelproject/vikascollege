@extends('layouts.admin.app')

@section('content')
    <h1>{{ isset($menu) ? 'Edit' : 'Create' }} Menu</h1>

    <form action="{{ isset($menu) ? route('admin.menus.update', $menu->id) : route('admin.menus.store') }}" method="POST">
        @csrf
        @if(isset($menu))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" value="{{ $menu->title ?? old('title') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>URL</label>
            <input type="text" name="url" value="{{ $menu->url ?? old('url') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Parent Menu</label>
            <select name="parent_id" class="form-control">
                <option value="">None</option>
                @foreach($menus as $m)
                    <option value="{{ $m->id }}" {{ (isset($menu) && $menu->parent_id == $m->id) ? 'selected' : '' }}>
                        {{ $m->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Order</label>
            <input type="number" name="order" value="{{ $menu->order ?? 0 }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" {{ (isset($menu) && $menu->status) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($menu) && !$menu->status) ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
@endsection