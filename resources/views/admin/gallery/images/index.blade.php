@extends('layouts.admin.app')

@section('content')
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Gallery Images</h1>
			<a href="{{ route('admin.gallery-images.create') }}" class="px-3 py-2 text-white bg-blue-600 rounded">Add Image</a>
		</div>
		@if(session('success'))
			<div class="p-2 mb-3 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
		@endif
		<div class="grid grid-cols-2 gap-3 md:grid-cols-4">
			@forelse($images as $img)
				<div class="p-2 bg-white border rounded shadow-sm">
					<div class="text-xs text-gray-500">{{ optional($img->category)->name }}</div>
					@if($img->image)
						<img src="{{ asset('storage/'.$img->image) }}" class="object-cover w-full h-40 rounded" alt="{{ $img->title }}">
					@endif
					<div class="flex items-center justify-between mt-2">
						<div class="text-sm">{{ $img->title }}</div>
						<div class="space-x-2">
							<a href="{{ route('admin.gallery-images.edit', $img) }}" class="px-2 py-1 text-xs text-white bg-yellow-600 rounded">Edit</a>
							<form action="{{ route('admin.gallery-images.destroy', $img) }}" method="POST" class="inline">
								@csrf
								@method('DELETE')
								<button type="submit" onclick="return confirm('Delete this image?')" class="px-2 py-1 text-xs text-white bg-red-600 rounded">Delete</button>
							</form>
						</div>
					</div>
				</div>
			@empty
				<div class="col-span-2 p-6 text-center text-gray-500 bg-white rounded">No images</div>
			@endforelse
		</div>
		<div class="mt-3">{{ $images->links() }}</div>
	</div>
@endsection
