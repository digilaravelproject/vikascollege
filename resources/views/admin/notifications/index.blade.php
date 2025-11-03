@extends('layouts.admin.app')

@section('content')
    <div class="container px-4 mx-auto">
        <div class="flex items-center justify-between py-4">
            <h1 class="text-xl font-semibold">Notifications</h1>
            <a href="{{ route('admin.notifications.create') }}"
                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Add Notification</a>
        </div>

        @if (session('success'))
            <div class="p-3 mb-4 text-green-800 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto bg-white border rounded">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-3">Icon</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Link</th>
                        <th class="px-4 py-3">Button</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Featured</th>
                        <th class="px-4 py-3">Feature on Top</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notifications as $n)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $n->icon }}</td>
                            <td class="px-4 py-3 font-medium">{{ $n->title }}</td>
                            <td class="px-4 py-3">@if($n->href)<a href="{{ $n->href }}" class="text-blue-600 hover:underline"
                            target="_blank" rel="noopener">{{ $n->href }}</a>@endif</td>
                            <td class="px-4 py-3">{{ $n->button_name ?: 'Click Here' }}</td>
                            <td class="px-4 py-3">{{ $n->display_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <button data-url="{{ route('admin.notifications.toggle-status', $n) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium rounded toggle-ajax {{ $n->status ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $n->status ? 'Active' : 'Inactive' }}</button>
                            </td>
                            <td class="px-4 py-3">
                                <button data-url="{{ route('admin.notifications.toggle-featured', $n) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium rounded toggle-ajax {{ $n->featured ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $n->featured ? 'Yes' : 'No' }}</button>
                            </td>
                            <td class="px-4 py-3">
                                <button data-url="{{ route('admin.notifications.toggle-feature-on-top', $n) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium rounded toggle-ajax {{ $n->feature_on_top ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $n->feature_on_top ? 'On' : 'Off' }}</button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.notifications.edit', $n) }}"
                                    class="px-3 py-1 text-xs text-white bg-indigo-600 rounded hover:bg-indigo-700">Edit</a>
                                <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete this notification?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">No notifications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>

    <script>
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.toggle-ajax');
            if (!btn) return;
            e.preventDefault();
            const url = btn.getAttribute('data-url');
            const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            if (res.ok) location.reload();
        });
    </script>
@endsection
