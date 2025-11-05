@extends('layouts.admin.app')

@section('content')
	<div class="max-w-3xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Add Gallery Image</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.gallery-images.store') }}" method="POST" enctype="multipart/form-data">
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
					<label class="block mb-1 text-sm font-medium">Image</label>
					<input type="file" name="image" accept="image/*" class="w-full p-2 border rounded">
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Title (optional)</label>
					<input type="text" name="title" value="{{ old('title') }}" class="w-full p-2 border rounded">
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.gallery-images.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Save</button>
			</div>
		</form>
	</div>
@endsection
