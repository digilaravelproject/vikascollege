@extends('layouts.admin.app')

@section('content')
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Announcements</h1>
			<a href="{{ route('admin.announcements.create') }}" class="px-3 py-2 text-white bg-blue-600 rounded">Add Announcement</a>
		</div>

		@if(session('success'))
			<div class="p-2 mb-3 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
		@endif

		<div class="overflow-x-auto bg-white rounded shadow">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-100">
					<tr>
						<th class="px-3 py-2 text-left">Title</th>
						<th class="px-3 py-2">Type</th>
						<th class="px-3 py-2">Status</th>
						<th class="px-3 py-2">Updated</th>
						<th class="px-3 py-2"></th>
					</tr>
				</thead>
				<tbody>
					@forelse($announcements as $a)
						<tr class="border-t">
							<td class="px-3 py-2">{{ $a->title }}</td>
							<td class="px-3 py-2 text-center">{{ ucfirst($a->type) }}</td>
							<td class="px-3 py-2 text-center">
								<span class="px-2 py-1 text-xs rounded {{ $a->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $a->status ? 'Published' : 'Draft' }}</span>
							</td>
							<td class="px-3 py-2 text-center">{{ $a->updated_at->format('Y-m-d H:i') }}</td>
							<td class="px-3 py-2 text-right space-x-2">
								<a href="{{ route('admin.announcements.edit', $a) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
								<form action="{{ route('admin.announcements.destroy', $a) }}" method="POST" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Delete this item?')" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr>
							<td class="px-3 py-6 text-center text-gray-500" colspan="5">No announcements</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="mt-3">{{ $announcements->links() }}</div>
	</div>
@endsection
