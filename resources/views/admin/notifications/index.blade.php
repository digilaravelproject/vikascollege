@extends('layouts.admin.app')

@section('content')
    <div class="container px-4 mx-auto">
        <div class="flex flex-col items-start justify-between py-4 sm:flex-row sm:items-center">
            <h1 class="text-xl font-semibold text-gray-800">Notifications</h1>
            <a href="{{ route('admin.notifications.create') }}"
                class="w-full px-4 py-2 mt-2 text-sm font-medium text-center text-white bg-blue-600 rounded-md sm:w-auto sm:mt-0 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Add Notification
            </a>
        </div>

        {{-- This will show the session success message as a SweetAlert toast --}}
        @if (session('success'))
            <div x-data x-init="
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        })
                    "></div>
        @endif

        {{--
        Mobile Card View
        - Shown on small screens (hidden on md and up)
        --}}
        <div class="space-y-4 md:hidden">
            @forelse ($notifications as $n)
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm" x-data="{
                                status: {{ $n->status ? 'true' : 'false' }},
                                featured: {{ $n->featured ? 'true' : 'false' }},
                                featureOnTop: {{ $n->feature_on_top ? 'true' : 'false' }}
                            }">

                    {{-- Icon & Title --}}
                    <div class="flex items-center space-x-3">
                        <span class="text-xl">{{ $n->icon }}</span>
                        <h3 class="font-semibold text-gray-900">{{ $n->title }}</h3>
                    </div>

                    {{-- Details --}}
                    <div class="mt-3 space-y-2 text-sm text-gray-600">
                        <div>
                            <strong>Button:</strong> {{ $n->button_name ?: 'Click Here' }}
                        </div>
                        <div>
                            <strong>Date:</strong> {{ $n->display_date ? $n->display_date->format('Y-m-d') : 'N/A' }}
                        </div>
                        @if ($n->href)
                            <div>
                                <strong>Link:</strong>
                                <a href="{{ $n->href }}" class="text-blue-600 hover:underline" target="_blank"
                                    rel="noopener">{{ $n->href }}</a>
                            </div>
                        @endif
                    </div>

                    {{-- Toggles --}}
                    <div class="pt-3 mt-3 space-y-2 border-t border-gray-200">
                        {{-- Status Toggle --}}
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <button
                                @click="status = !status; handleToggle('{{ route('admin.notifications.toggle-status', $n) }}', 'Status')"
                                type="button"
                                class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="status ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="status">
                                <span
                                    class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                    :class="{ 'translate-x-6': status, 'translate-x-1': !status }"></span>
                            </button>
                        </div>
                        {{-- Featured Toggle --}}
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Featured</label>
                            <button
                                @click="featured = !featured; handleToggle('{{ route('admin.notifications.toggle-featured', $n) }}', 'Featured')"
                                type="button"
                                class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="featured ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="featured">
                                <span
                                    class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                    :class="{ 'translate-x-6': featured, 'translate-x-1': !featured }"></span>
                            </button>
                        </div>
                        {{-- Feature on Top Toggle --}}
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Feature on Top</label>
                            <button
                                @click="featureOnTop = !featureOnTop; handleToggle('{{ route('admin.notifications.toggle-feature-on-top', $n) }}', 'Feature on Top')"
                                type="button"
                                class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="featureOnTop ? 'bg-blue-600' : 'bg-gray-200'" role="switch"
                                :aria-checked="featureOnTop">
                                <span
                                    class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                    :class="{ 'translate-x-6': featureOnTop, 'translate-x-1': !featureOnTop }"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center pt-3 mt-3 space-x-2 border-t border-gray-200">
                        <a href="{{ route('admin.notifications.edit', $n) }}"
                            class="flex-1 px-3 py-2 text-xs font-medium text-center text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Edit</a>
                        <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" class="flex-1"
                            @submit.prevent="confirmDelete($event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-3 py-2 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 bg-white border border-gray-200 rounded-lg">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        {{--
        Desktop Table View
        - Hidden on small screens (visible on md and up)
        --}}
        <div class="hidden overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm md:block">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-4 py-3 font-medium text-gray-600">Icon</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Title</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Link</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Button</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Date</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Featured</th>
                        <th class="px-4 py-3 font-medium text-gray-600">On Top</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($notifications as $n)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-lg">{{ $n->icon }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $n->title }}</td>
                            <td class="px-4 py-3">
                                @if ($n->href)
                                    <a href="{{ $n->href }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">
                                        {{ Str::limit($n->href, 30) }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $n->button_name ?: 'Click Here' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $n->display_date?->format('Y-m-d') }}</td>

                            {{-- Status Toggle --}}
                            <td class="px-4 py-3" x-data="{ enabled: {{ $n->status ? 'true' : 'false' }} }"
                                x-init="$watch('enabled', value => handleToggle('{{ route('admin.notifications.toggle-status', $n) }}', 'Status'))">
                                <button @click="enabled = !enabled" type="button"
                                    class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                                    <span
                                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                                </button>
                            </td>

                            {{-- Featured Toggle --}}
                            <td class="px-4 py-3" x-data="{ enabled: {{ $n->featured ? 'true' : 'false' }} }"
                                x-init="$watch('enabled', value => handleToggle('{{ route('admin.notifications.toggle-featured', $n) }}', 'Featured'))">
                                <button @click="enabled = !enabled" type="button"
                                    class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                                    <span
                                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                                </button>
                            </td>

                            {{-- Feature on Top Toggle --}}
                            <td class="px-4 py-3" x-data="{ enabled: {{ $n->feature_on_top ? 'true' : 'false' }} }"
                                x-init="$watch('enabled', value => handleToggle('{{ route('admin.notifications.toggle-feature-on-top', $n) }}', 'Feature on Top'))">
                                <button @click="enabled = !enabled" type="button"
                                    class="relative inline-flex items-center h-6 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                                    <span
                                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.notifications.edit', $n) }}"
                                    class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Edit</a>
                                <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" class="inline"
                                    @submit.prevent="confirmDelete($event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No notifications yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>

    <script>
        // This function handles the "Are you sure?" confirmation for deleting
        function confirmDelete(event) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    event.target.submit();
                }
            });
        }

        // This function handles all toggle AJAX requests
        async function handleToggle(url, fieldName) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                // Show success toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `${fieldName} updated!`,
                    showConfirmButton: false,
                    timer: 2000
                });

                // The 'data' object (from your controller) can be used
                // if you need to do more, e.g., update text
                console.log(data);

            } catch (error) {
                console.error('There was a problem with the fetch operation:', error);
                // Show error toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: `Failed to update ${fieldName}`,
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
    </script>
@endsection
