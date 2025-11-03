@extends('layouts.admin.app')

@section('content')
    <div class="container px-4 mx-auto max-w-3xl">
        <div class="flex items-center justify-between py-4">
            <h1 class="text-xl font-semibold">Edit Notification</h1>
            <a href="{{ route('admin.notifications.index') }}" class="text-sm text-blue-600 hover:underline">Back</a>
        </div>

        <div class="p-6 bg-white border rounded">
            <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block mb-1 text-sm font-medium">Icon</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach ($icons as $ic)
                            <label class="px-3 py-1 border rounded cursor-pointer">
                                <input type="radio" name="icon" value="{{ $ic }}" class="hidden"
                                    @checked($notification->icon === $ic)>
                                <span>{{ $ic }}</span>
                            </label>
                        @endforeach
                    </div>
                    <input type="text" name="icon" placeholder="Or paste custom emoji/icon"
                        class="w-full p-2 border rounded" value="{{ old('icon', $notification->icon) }}">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Title</label>
                    <input type="text" name="title" class="w-full p-2 border rounded" required
                        value="{{ old('title', $notification->title) }}">
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Link (href)</label>
                        <input type="text" name="href" class="w-full p-2 border rounded"
                            value="{{ old('href', $notification->href) }}">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Button Name</label>
                        <input type="text" name="button_name" class="w-full p-2 border rounded"
                            value="{{ old('button_name', $notification->button_name) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Status</label>
                        <select name="status" class="w-full p-2 border rounded">
                            <option value="1" @selected($notification->status)>Active</option>
                            <option value="0" @selected(!$notification->status)>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Featured</label>
                        <select name="featured" class="w-full p-2 border rounded">
                            <option value="0" @selected(!$notification->featured)>No</option>
                            <option value="1" @selected($notification->featured)>Yes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Feature on Top</label>
                        <select name="feature_on_top" class="w-full p-2 border rounded">
                            <option value="0" @selected(!$notification->feature_on_top)>Off</option>
                            <option value="1" @selected($notification->feature_on_top)>On</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Date</label>
                    <input type="date" name="display_date" class="w-full p-2 border rounded"
                        value="{{ old('display_date', optional($notification->display_date)->format('Y-m-d')) }}">
                </div>

                <div class="pt-2">
                    <button class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
