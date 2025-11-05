@extends('layouts.admin.app')

@section('content')
	<div class="max-w-3xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Edit Academic Calendar Item</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.academic-calendar.update', $item) }}" method="POST" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			<div class="grid grid-cols-1 gap-4">
				<div>
					<label class="block mb-1 text-sm font-medium">Title</label>
					<input type="text" name="title" value="{{ old('title', $item->title) }}" class="w-full p-2 border rounded">
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Slug</label>
						<input type="text" name="slug" value="{{ old('slug', $item->slug) }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Date & Time</label>
						<input type="datetime-local" name="event_datetime" value="{{ old('event_datetime', optional($item->event_datetime)->format('Y-m-d\TH:i')) }}" class="w-full p-2 border rounded">
					</div>
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Image</label>
						<input type="file" name="image" accept="image/*" class="w-full p-2 border rounded">
						@if($item->image)
							<p class="mt-1 text-sm text-gray-600">Current: {{ $item->image }}</p>
						@endif
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">External Link (optional)</label>
						<input type="url" name="link_href" value="{{ old('link_href', $item->link_href) }}" class="w-full p-2 border rounded">
					</div>
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Description</label>
					<textarea name="description" rows="6" class="w-full p-2 border rounded">{{ old('description', $item->description) }}</textarea>
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Title</label>
						<input type="text" name="meta_title" value="{{ old('meta_title', $item->meta_title) }}" class="w-full p-2 border rounded">
					</div>
					<div>
						<label class="block mb-1 text-sm font-medium">Meta Description</label>
						<input type="text" name="meta_description" value="{{ old('meta_description', $item->meta_description) }}" class="w-full p-2 border rounded">
					</div>
				</div>
				<label class="inline-flex items-center gap-2">
					<input type="checkbox" name="status" value="1" class="rounded" {{ old('status', $item->status) ? 'checked' : '' }}>
					<span>Active</span>
				</label>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.academic-calendar.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Update</button>
			</div>
		</form>
	</div>
@endsection
