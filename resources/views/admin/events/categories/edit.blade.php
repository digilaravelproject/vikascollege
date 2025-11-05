@extends('layouts.admin.app')

@section('content')
	<div class="max-w-2xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Edit Event Category</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.event-categories.update', $category) }}" method="POST">
			@csrf
			@method('PUT')
			<div class="grid grid-cols-1 gap-4">
				<div>
					<label class="block mb-1 text-sm font-medium">Name</label>
					<input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full p-2 border rounded">
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Slug</label>
					<input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full p-2 border rounded">
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Title</label>
						<input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Description</label>
						<input type="text" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}" class="w-full p-2 border rounded">
					</div>
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.event-categories.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Update</button>
			</div>
		</form>
	</div>
@endsection
