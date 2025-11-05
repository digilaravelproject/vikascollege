@extends('layouts.admin.app')

@section('content')
	<div class="max-w-2xl p-4 mx-auto">
		<h1 class="mb-4 text-xl font-semibold">Add Testimonial</h1>
		@if ($errors->any())
			<div class="p-2 mb-3 text-red-800 bg-red-100 rounded">
				<ul class="ml-5 list-disc">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="grid grid-cols-1 gap-4">
				<div>
					<label class="block mb-1 text-sm font-medium">Student Name</label>
					<input type="text" name="student_name" value="{{ old('student_name') }}" class="w-full p-2 border rounded">
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Student Image</label>
					<input type="file" name="student_image" accept="image/*" class="w-full p-2 border rounded">
				</div>
				<div>
					<label class="block mb-1 text-sm font-medium">Testimonial</label>
					<textarea name="testimonial_text" rows="5" class="w-full p-2 border rounded">{{ old('testimonial_text') }}</textarea>
				</div>
				<label class="inline-flex items-center gap-2">
					<input type="checkbox" name="status" value="1" class="rounded">
					<span>Approved</span>
				</label>
			</div>
			<div class="flex justify-end gap-2 mt-4">
				<a href="{{ route('admin.testimonials.index') }}" class="px-3 py-2 bg-gray-200 rounded">Cancel</a>
				<button class="px-3 py-2 text-white bg-blue-600 rounded">Save</button>
			</div>
		</form>
	</div>
@endsection
