@extends('layouts.admin.app')

@section('title', 'Notifications Management')

@section('content')
    <div class="space-y-6"
        x-data="{
            showConfirmModal: false,
            confirmModalTitle: '',
            confirmModalMessage: '',
            formToSubmit: null,
        }">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <a href="{{ route('admin.notifications.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i>
                Add Notification
            </a>
        </div>

        {{-- Success/Error Alerts --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="flex items-center justify-between p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50"
                role="alert">
                <div class="flex items-center">
                    <i class="text-lg bi bi-check-circle-fill me-3"></i>
                    <div>
                        <span class="font-medium">Success!</span> {{ session('success') }}
                    </div>
                </div>
                <button @click="show = false" class="ml-3 -mr-1 text-green-700/70 hover:text-green-900">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        {{-- Table Container --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">All Notifications</h2>
            </div>

            {{-- Responsive Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase">Icon</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase">Title</th>
                            <th class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase sm:table-cell">Link</th>
                            <th class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase sm:table-cell">Button</th>
                            <th class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase sm:table-cell">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-gray-500 uppercase">Featured</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-gray-500 uppercase">On Top</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($notifications as $n)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 text-lg text-gray-700">{{ $n->icon }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $n->title }}</td>
                                <td class="hidden px-6 py-4 text-blue-600 sm:table-cell">
                                    @if ($n->href)
                                        <a href="{{ $n->href }}" target="_blank" class="hover:underline">{{ Str::limit($n->href, 40) }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="hidden px-6 py-4 text-gray-600 sm:table-cell">{{ $n->button_name ?: 'Click Here' }}</td>
                                <td class="hidden px-6 py-4 text-gray-600 sm:table-cell">{{ $n->display_date?->format('Y-m-d') ?: 'N/A' }}</td>

                                {{-- Status Toggle --}}
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $n->status ? 'true' : 'false' }} }"
                                            x-model="checked"
                                            @change="
                                                fetch('{{ route('admin.notifications.toggle-status', $n) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ status: checked })
                                                })
                                                .then(res => res.json())
                                                .then(data => $dispatch('notify', { message: 'Status updated!', type: 'success' }))
                                                .catch(() => $dispatch('notify', { message: 'Failed to update status', type: 'error' }))
                                            "
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                </td>

                                {{-- Featured Toggle --}}
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $n->featured ? 'true' : 'false' }} }"
                                            x-model="checked"
                                            @change="
                                                fetch('{{ route('admin.notifications.toggle-featured', $n) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ featured: checked })
                                                })
                                                .then(res => res.json())
                                                .then(data => $dispatch('notify', { message: 'Featured updated!', type: 'success' }))
                                                .catch(() => $dispatch('notify', { message: 'Failed to update featured', type: 'error' }))
                                            "
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                </td>

                                {{-- On Top Toggle --}}
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $n->feature_on_top ? 'true' : 'false' }} }"
                                            x-model="checked"
                                            @change="
                                                fetch('{{ route('admin.notifications.toggle-feature-on-top', $n) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ feature_on_top: checked })
                                                })
                                                .then(res => res.json())
                                                .then(data => $dispatch('notify', { message: 'Feature On Top updated!', type: 'success' }))
                                                .catch(() => $dispatch('notify', { message: 'Failed to update', type: 'error' }))
                                            "
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.notifications.edit', $n) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                @click.prevent="
                                                    confirmModalTitle = 'Delete Notification';
                                                    confirmModalMessage = 'Are you sure you want to delete this notification? This action cannot be undone.';
                                                    formToSubmit = $el.closest('form');
                                                    showConfirmModal = true;
                                                "
                                                class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="text-4xl text-gray-300 bi bi-bell"></i>
                                    <p class="mt-2 text-lg font-medium">No notifications found</p>
                                    <p class="text-sm">Start by creating a new notification.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">{{ $notifications->links() }}</div>

        {{-- Toast Notifications --}}
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            timer: null,
            duration: 3000,
            progress: 100
        }"
            @notify.window="
                message = $event.detail.message;
                type = $event.detail.type;
                show = true;
                progress = 100;
                clearInterval(timer);
                const startTime = Date.now();
                timer = setInterval(() => {
                    const elapsed = Date.now() - startTime;
                    progress = 100 - (elapsed / duration) * 100;
                    if (elapsed >= duration) { show = false; clearInterval(timer); }
                }, 50);
            "
            x-show="show" x-transition
            class="fixed z-50 w-full max-w-sm overflow-hidden rounded-lg shadow-lg bottom-5 right-5"
            :class="{ 'bg-blue-600': type === 'success', 'bg-red-600': type === 'error' }">

            <div class="flex items-start p-4">
                <div class="flex-shrink-0">
                    <template x-if="type === 'success'">
                        <i class="text-2xl text-white bi bi-check-circle-fill"></i>
                    </template>
                    <template x-if="type === 'error'">
                        <i class="text-2xl text-white bi bi-exclamation-triangle-fill"></i>
                    </template>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="font-bold text-white" x-text="message"></p>
                    <p class="mt-1 text-sm text-white/80" x-text="type === 'success' ? 'Update successful!' : 'An error occurred.'"></p>
                </div>
                <button @click="show = false; clearInterval(timer);" class="ml-4 text-white/70 hover:text-white focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="h-1 bg-white/50" :style="`width: ${progress}%`"></div>
        </div>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" style="display: none;" x-transition
            class="fixed inset-0 z-40 flex items-center justify-center p-4">
            <div @click="showConfirmModal = false; formToSubmit = null;" class="absolute inset-0 bg-gray-900/50"></div>
            <div class="relative z-50 w-full max-w-md p-6 overflow-hidden bg-white shadow-xl rounded-2xl">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="text-xl text-red-600 bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900" x-text="confirmModalTitle"></h3>
                        <p class="mt-2 text-sm text-gray-600" x-text="confirmModalMessage"></p>
                    </div>
                </div>
                <div class="mt-6 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button @click="formToSubmit.submit(); showConfirmModal = false;"
                        class="inline-flex justify-center w-full px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500 sm:w-auto">
                        Confirm Delete
                    </button>
                    <button @click="showConfirmModal = false; formToSubmit = null;"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-semibold text-gray-900 bg-white rounded-lg shadow-sm ring-1 ring-gray-300 hover:bg-gray-100 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
