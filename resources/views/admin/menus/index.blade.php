@extends('layouts.admin.app')

@section('title', 'Menu Management')

@section('content')
    {{-- Alpine.js data scope for the confirmation modal --}}
    <div class="space-y-6" x-data="{ showConfirmModal: false, confirmModalTitle: '', confirmModalMessage: '', formToSubmit: null }">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Menu Management</h1>
            <a href="{{ route('admin.menus.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Menu
            </a>
        </div>

        {{-- Notifications --}}
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
                    <span class="sr-only">Close</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="flex items-center justify-between p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50"
                role="alert">
                <div class="flex items-center">
                    <i class="text-lg bi bi-exclamation-triangle-fill me-3"></i>
                    <div>
                        <span class="font-medium">Error!</span> {{ session('error') }}
                    </div>
                </div>
                <button @click="show = false" class="ml-3 -mr-1 text-red-700/70 hover:text-red-900">
                    <span class="sr-only">Close</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        {{-- Menu Table Card --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Website Menus</h2>
                <span class="text-xs text-gray-400 hidden sm:inline-block"><i class="bi bi-arrows-move"></i> Click & Drag to
                    scroll</span>
            </div>

            {{--
            🚀 DRAG-TO-SCROLL IMPLEMENTATION
            Added cursor-grab styling and Alpine.js logic for mouse events.
            --}}
            <div class="overflow-x-auto cursor-grab active:cursor-grabbing" x-data="{
                isDown: false,
                startX: 0,
                scrollLeft: 0,
                mouseDown(e) {
                    this.isDown = true;
                    this.startX = e.pageX - $el.offsetLeft;
                    this.scrollLeft = $el.scrollLeft;
                },
                mouseLeave() {
                    this.isDown = false;
                },
                mouseUp() {
                    this.isDown = false;
                },
                mouseMove(e) {
                    if (!this.isDown) return;
                    e.preventDefault();
                    const x = e.pageX - $el.offsetLeft;
                    const walk = (x - this.startX) * 1.5; // Scroll-fast speed multiplier
                    $el.scrollLeft = this.scrollLeft - walk;
                }
            }" @mousedown="mouseDown"
                @mouseleave="mouseLeave" @mouseup="mouseUp" @mousemove="mouseMove">

                <table class="min-w-full text-sm divide-y divide-gray-200 select-none"> {{-- Added select-none to prevent
                    text highlighting while dragging --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Title</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:table-cell">
                                URL / Slug</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase lg:table-cell">
                                Parent</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase lg:table-cell">
                                Order</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase sm:table-cell">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Level 1 Loop --}}
                        @forelse($menus->where('parent_id', null)->sortBy('order') as $menu)
                    <tbody x-data="{ open: false }" class="group">
                        {{-- Level 1 Row --}}
                        <tr class="transition cursor-pointer bg-gray-50 hover:bg-gray-100" @click="open = !open">
                            <td class="flex items-center px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                <i :class="open ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"
                                    class="text-gray-600 transition-transform duration-200 me-2"></i>
                                <i class="text-blue-600 bi bi-folder-fill me-2"></i>
                                {{ $menu->title }}
                            </td>
                            <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">{{ $menu->url }}
                            </td>
                            <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">—</td>
                            <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                {{ $menu->order }}</td>
                            <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                @include('admin.menus.partials.status-toggle', ['item' => $menu])
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @include('admin.menus.partials.action-buttons', ['item' => $menu])
                            </td>
                        </tr>

                        {{-- Level 2 Loop --}}
                        @foreach ($menus->where('parent_id', $menu->id)->sortBy('order') as $child)
                            <tr x-show="open" x-transition class="transition bg-white hover:bg-gray-50">
                                <td class="flex items-center px-10 py-4 text-gray-700 whitespace-nowrap">
                                    <i class="text-gray-400 bi bi-arrow-return-right me-2"></i>
                                    {{ $child->title }}
                                </td>
                                <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                    {{ $child->url }}
                                </td>
                                <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                    {{ $menu->title }}
                                </td>
                                <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                    {{ $child->order }}</td>
                                <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                    @include('admin.menus.partials.status-toggle', ['item' => $child])
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @include('admin.menus.partials.action-buttons', ['item' => $child])
                                </td>
                            </tr>

                            {{-- Level 3 Loop --}}
                            @foreach ($menus->where('parent_id', $child->id)->sortBy('order') as $subchild)
                                <tr x-show="open" x-transition class="transition bg-gray-50/50 hover:bg-gray-100">
                                    <td class="flex items-center py-4 italic text-gray-600 px-14 whitespace-nowrap">
                                        <i class="text-gray-400 bi bi-dash-lg me-2"></i>
                                        {{ $subchild->title }}
                                    </td>
                                    <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                        {{ $subchild->url }}
                                    </td>
                                    <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                        {{ $child->title }}
                                    </td>
                                    <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                        {{ $subchild->order }}</td>
                                    <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                        @include('admin.menus.partials.status-toggle', [
                                            'item' => $subchild,
                                        ])
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @include('admin.menus.partials.action-buttons', [
                                            'item' => $subchild,
                                        ])
                                    </td>
                                </tr>

                                {{-- Level 4 Loop --}}
                                @foreach ($menus->where('parent_id', $subchild->id)->sortBy('order') as $l4)
                                    <tr x-show="open" x-transition class="transition bg-gray-100/50 hover:bg-gray-200">
                                        <td class="flex items-center py-4 italic text-gray-500 px-16 whitespace-nowrap">
                                            <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                            {{ $l4->title }}
                                        </td>
                                        <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                            {{ $l4->url }}</td>
                                        <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                            {{ $subchild->title }}</td>
                                        <td
                                            class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                            {{ $l4->order }}</td>
                                        <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                            @include('admin.menus.partials.status-toggle', ['item' => $l4])
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @include('admin.menus.partials.action-buttons', [
                                                'item' => $l4,
                                            ])
                                        </td>
                                    </tr>

                                    {{-- Level 5 Loop --}}
                                    @foreach ($menus->where('parent_id', $l4->id)->sortBy('order') as $l5)
                                        <tr x-show="open" x-transition
                                            class="transition bg-gray-100/70 hover:bg-gray-200/70">
                                            <td
                                                class="flex items-center py-4 italic text-gray-500 px-20 whitespace-nowrap">
                                                <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                                {{ $l5->title }}
                                            </td>
                                            <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                                {{ $l5->url }}</td>
                                            <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                                {{ $l4->title }}</td>
                                            <td
                                                class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                                {{ $l5->order }}</td>
                                            <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                                @include('admin.menus.partials.status-toggle', [
                                                    'item' => $l5,
                                                ])
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @include('admin.menus.partials.action-buttons', [
                                                    'item' => $l5,
                                                ])
                                            </td>
                                        </tr>

                                        {{-- Level 6 Loop --}}
                                        @foreach ($menus->where('parent_id', $l5->id)->sortBy('order') as $l6)
                                            <tr x-show="open" x-transition
                                                class="transition bg-gray-100/80 hover:bg-gray-200/80">
                                                <td
                                                    class="flex items-center py-4 italic text-gray-500 px-24 whitespace-nowrap">
                                                    <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                                    {{ $l6->title }}
                                                </td>
                                                <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                                    {{ $l6->url }}</td>
                                                <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                                    {{ $l5->title }}</td>
                                                <td
                                                    class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                                    {{ $l6->order }}</td>
                                                <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                                    @include('admin.menus.partials.status-toggle', [
                                                        'item' => $l6,
                                                    ])
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    @include('admin.menus.partials.action-buttons', [
                                                        'item' => $l6,
                                                    ])
                                                </td>
                                            </tr>

                                            {{-- Level 7 Loop --}}
                                            @foreach ($menus->where('parent_id', $l6->id)->sortBy('order') as $l7)
                                                <tr x-show="open" x-transition
                                                    class="transition bg-gray-100/90 hover:bg-gray-200/90">
                                                    <td
                                                        class="flex items-center py-4 italic text-gray-500 px-28 whitespace-nowrap">
                                                        <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                                        {{ $l7->title }}
                                                    </td>
                                                    <td
                                                        class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                                        {{ $l7->url }}</td>
                                                    <td
                                                        class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                                        {{ $l6->title }}</td>
                                                    <td
                                                        class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                                        {{ $l7->order }}</td>
                                                    <td
                                                        class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                                        @include('admin.menus.partials.status-toggle', [
                                                            'item' => $l7,
                                                        ])
                                                    </td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        @include('admin.menus.partials.action-buttons', [
                                                            'item' => $l7,
                                                        ])
                                                    </td>
                                                </tr>

                                                {{-- Level 8 Loop --}}
                                                @foreach ($menus->where('parent_id', $l7->id)->sortBy('order') as $l8)
                                                    <tr x-show="open" x-transition
                                                        class="transition bg-gray-100 hover:bg-gray-200">
                                                        <td
                                                            class="flex items-center py-4 italic text-gray-500 px-32 whitespace-nowrap">
                                                            <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                                            {{ $l8->title }}
                                                        </td>
                                                        <td
                                                            class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                                            {{ $l8->url }}</td>
                                                        <td
                                                            class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                                            {{ $l7->title }}</td>
                                                        <td
                                                            class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                                            {{ $l8->order }}</td>
                                                        <td
                                                            class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                                            @include(
                                                                'admin.menus.partials.status-toggle',
                                                                ['item' => $l8]
                                                            )
                                                        </td>
                                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                                            @include(
                                                                'admin.menus.partials.action-buttons',
                                                                ['item' => $l8]
                                                            )
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="text-4xl text-gray-300 bi bi-list-ul"></i>
                            <p class="mt-2 text-lg font-medium">No menus found</p>
                            <p class="text-sm">Get started by creating a new menu.</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Toast Notification (Bottom Right) --}}
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            timer: null,
            duration: 4000,
            progress: 100
        }"
            @notify.window="
                    message = $event.detail.message;
                    type = $event.detail.type || 'success';
                    show = true;
                    progress = 100;

                    clearInterval(timer);

                    const startTime = Date.now();
                    timer = setInterval(() => {
                        const elapsedTime = Date.now() - startTime;
                        progress = 100 - (elapsedTime / duration) * 100;
                        if (elapsedTime >= duration) {
                            show = false;
                            clearInterval(timer);
                        }
                    }, 50);
                "
            x-show="show" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed z-50 w-full max-w-sm overflow-hidden rounded-lg shadow-lg bottom-5 right-5"
            :class="{ 'bg-blue-600': type === 'success', 'bg-red-600': type === 'error' }" role="alert">

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
                    <p class="mt-1 text-sm text-white/80"
                        x-text="type === 'success' ? 'Update successful!' : 'An error occurred.'"></p>
                </div>

                <div class="flex flex-shrink-0 ml-4">
                    <button @click="show = false; clearInterval(timer);"
                        class="inline-flex transition rounded-md text-white/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
                        :class="{ 'hover:bg-blue-700': type === 'success', 'hover:bg-red-700': type === 'error' }">
                        <span class="sr-only">Close</span>
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="h-1" :class="{ 'bg-blue-800/50': type === 'success', 'bg-red-800/50': type === 'error' }">
                <div class="h-1 bg-white/50" :style="`width: ${progress}%`"></div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showConfirmModal" style="display: none;" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 flex items-center justify-center p-4">
            <div @click="showConfirmModal = false; formToSubmit = null;" class="absolute inset-0 bg-gray-900/50"></div>

            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-50 w-full max-w-md p-6 overflow-hidden bg-white shadow-xl rounded-2xl">

                <div class="sm:flex sm:items-start">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="text-xl text-red-600 bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900" x-text="confirmModalTitle"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600" x-text="confirmModalMessage"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button @click="formToSubmit.submit(); showConfirmModal = false;" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 text-sm font-semibold text-white transition bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Confirm Delete
                    </button>
                    <button @click="showConfirmModal = false; formToSubmit = null;" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-semibold text-gray-900 transition bg-white rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection
