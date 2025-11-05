@extends('layouts.admin.app')

@section('content')
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Events</h1>
			<a href="{{ route('admin.event-items.create') }}" class="px-3 py-2 text-white bg-blue-600 rounded">Add Event</a>
		</div>

		@if(session('success'))
			<div class="p-2 mb-3 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
		@endif

		<div class="overflow-x-auto bg-white rounded shadow">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-100">
					<tr>
						<th class="px-3 py-2 text-left">Title</th>
						<th class="px-3 py-2">Category</th>
						<th class="px-3 py-2">Date</th>
						<th class="px-3 py-2">Venue</th>
						<th class="px-3 py-2"></th>
					</tr>
				</thead>
				<tbody>
					@forelse($items as $e)
						<tr class="border-t">
							<td class="px-3 py-2">{{ $e->title }}</td>
							<td class="px-3 py-2 text-center">{{ optional($e->category)->name }}</td>
							<td class="px-3 py-2 text-center">{{ $e->event_date?->format('Y-m-d H:i') }}</td>
							<td class="px-3 py-2 text-center">{{ $e->venue }}</td>
							<td class="px-3 py-2 text-right space-x-2">
								<a href="{{ route('admin.event-items.edit', $e) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
								<form action="{{ route('admin.event-items.destroy', $e) }}" method="POST" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" onclick="return confirm('Delete this item?')" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr>
							<td class="px-3 py-6 text-center text-gray-500" colspan="5">No events</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="mt-3">{{ $items->links() }}</div>
	</div>
@endsection
