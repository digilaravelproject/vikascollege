@extends('layouts.admin.app')

@section('content')
	<div class="max-w-3xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Add Event</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.event-items.store') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="grid grid-cols-1 gap-4">
				<div>
					<label class="block mb-1 text-sm font-medium">Category</label>
					<select name="category_id" class="w-full p-2 bg-white border rounded">
						@foreach($categories as $id => $name)
							<option value="{{ $id }}" {{ old('category_id')==$id?'selected':'' }}>{{ $name }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Title</label>
					<input type="text" name="title" value="{{ old('title') }}" class="w-full p-2 border rounded">
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Slug (optional)</label>
						<input type="text" name="slug" value="{{ old('slug') }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Event Date & Time</label>
						<input type="datetime-local" name="event_date" value="{{ old('event_date') }}" class="w-full p-2 border rounded">
					</div>
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Venue</label>
						<input type="text" name="venue" value="{{ old('venue') }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Image</label>
						<input type="file" name="image" accept="image/*" class="w-full p-2 border rounded">
					</div>
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Short Description</label>
					<textarea name="short_description" rows="3" class="w-full p-2 border rounded">{{ old('short_description') }}</textarea>
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Full Content</label>
					<textarea name="full_content" rows="6" class="w-full p-2 border rounded">{{ old('full_content') }}</textarea>
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Title</label>
						<input type="text" name="meta_title" value="{{ old('meta_title') }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Description</label>
						<input type="text" name="meta_description" value="{{ old('meta_description') }}" class="w-full p-2 border rounded">
					</div>
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.event-items.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Save</button>
			</div>
		</form>
	</div>
@endsection
