@extends('layouts.admin.app')

@section('content')
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Testimonials</h1>
			<a href="{{ route('admin.testimonials.create') }}" class="px-3 py-2 text-white bg-blue-600 rounded">Add Testimonial</a>
		</div>
		@if(session('success'))
			<div class="p-2 mb-3 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
		@endif
		<div class="overflow-x-auto bg-white rounded shadow">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-100">
					<tr>
						<th class="px-3 py-2 text-left">Student</th>
						<th class="px-3 py-2">Status</th>
						<th class="px-3 py-2">Updated</th>
						<th class="px-3 py-2"></th>
					</tr>
				</thead>
				<tbody>
					@forelse($testimonials as $t)
						<tr class="border-t">
							<td class="px-3 py-2">{{ $t->student_name }}</td>
							<td class="px-3 py-2 text-center"><span class="px-2 py-1 text-xs rounded {{ $t->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $t->status ? 'Approved' : 'Pending' }}</span></td>
							<td class="px-3 py-2 text-center">{{ $t->updated_at->format('Y-m-d H:i') }}</td>
							<td class="px-3 py-2 text-right space-x-2">
								<a href="{{ route('admin.testimonials.edit', $t) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
								<form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Delete this item?')" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr><td class="px-3 py-6 text-center text-gray-500" colspan="4">No testimonials</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="mt-3">{{ $testimonials->links() }}</div>
	</div>
@endsection
