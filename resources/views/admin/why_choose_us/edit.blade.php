@extends('layouts.admin.app')

@section('content')
	<div class="max-w-2xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Edit Why Choose Us Item</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.why-choose-us.update', $item) }}" method="POST" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			<div class="grid grid-cols-1 gap-4">
				<div>
					<label class="block mb-1 text-sm font-medium">Title</label>
					<input type="text" name="title" value="{{ old('title', $item->title) }}" class="w-full p-2 border rounded">
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Description</label>
					<textarea name="description" rows="5" class="w-full p-2 border rounded">{{ old('description', $item->description) }}</textarea>
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Icon/Image</label>
					<input type="file" name="icon_or_image" accept="image/*" class="w-full p-2 border rounded">
					@if($item->icon_or_image)
						<p class="mt-1 text-sm text-gray-600">Current: {{ $item->icon_or_image }}</p>
					@endif
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Sort Order</label>
					<input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" class="w-full p-2 border rounded">
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.why-choose-us.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Update</button>
			</div>
		</form>
	</div>
@endsection
