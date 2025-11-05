@extends('layouts.admin.app')

@section('content')
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Gallery Categories</h1>
			<a href="{{ route('admin.gallery-categories.create') }}" class="px-3 py-2 text-white bg-blue-600 rounded">Add Category</a>
		</div>
		@if(session('success'))
			<div class="p-2 mb-3 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
		@endif
		<div class="overflow-x-auto bg-white rounded shadow">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-100">
					<tr>
						<th class="px-3 py-2 text-left">Name</th>
						<th class="px-3 py-2">Slug</th>
						<th class="px-3 py-2"></th>
					</tr>
				</thead>
				<tbody>
					@forelse($categories as $c)
						<tr class="border-t">
							<td class="px-3 py-2">{{ $c->name }}</td>
							<td class="px-3 py-2 text-center">{{ $c->slug }}</td>
							<td class="px-3 py-2 text-right space-x-2">
								<a href="{{ route('admin.gallery-categories.edit', $c) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
								<form action="{{ route('admin.gallery-categories.destroy', $c) }}" method="POST" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Delete this item?')" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr><td class="px-3 py-6 text-center text-gray-500" colspan="3">No categories</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="mt-3">{{ $categories->links() }}</div>
	</div>
@endsection
