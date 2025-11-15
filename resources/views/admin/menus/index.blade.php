@extends('layouts.admin.app')

@section('title', 'Menu Management')

@section('content')
    {{-- ADDED: Alpine.js data scope for the new confirmation modal --}}
    <div class="space-y-6" x-data="{ showConfirmModal: false, confirmModalTitle: '', confirmModalMessage: '', formToSubmit: null }">

        {{-- MODIFIED: Increased heading size for better hierarchy --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Menu Management</h1>
            <a href="{{ route('admin.menus.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Menu
            </a>
        </div>

        {{-- ENHANCED: Added dismissible button with Alpine.js for "sweeter" alerts --}}
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

        {{-- MODIFIED: Stronger shadow and border for a cleaner card --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Website Menus</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    {{-- MODIFIED: Professional admin header style --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Title</th>
                            {{-- ADDED: New URL / Slug column, responsive --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:table-cell">
                                URL / Slug</th>
                            {{-- MODIFIED: Hidden on small/medium screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase lg:table-cell">
                                Parent</th>
                            {{-- MODIFIED: Hidden on small/medium screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase lg:table-cell">
                                Order</th>
                            {{-- MODIFIED: Hidden on smallest screens --}}
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase sm:table-cell">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Level 1: Main Menu (parent_id is null) --}}
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
                                <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                    {{ $menu->url }}
                                </td>
                                <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">—</td>
                                <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                    {{ $menu->order }}</td>
                                <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-data="{ checked: {{ $menu->status ? 'true' : 'false' }} }" x-model="checked"
                                            @change="
                                                fetch('{{ route('admin.menus.toggle-status', $menu->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    },
                                                    body: JSON.stringify({ status: checked })
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    if(data.success){
                                                        $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                    } else {
                                                        $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                    }
                                                })
                                            "
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                after:bg-white after:border-gray-300 after:border after:rounded-full
                                                after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                        </div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                @click.prevent="
                                                    confirmModalTitle = 'Delete Menu';
                                                    confirmModalMessage = 'Are you sure you want to delete this menu? This action cannot be undone.';
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

                            {{-- Level 2: Submenu --}}
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
                                        {{ $menu->title }}</td>
                                    <td class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                        {{ $child->order }}</td>
                                    <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-data="{ checked: {{ $child->status ? 'true' : 'false' }} }" x-model="checked"
                                                @change="
                                                    fetch('{{ route('admin.menus.toggle-status', $child->id) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        },
                                                        body: JSON.stringify({ status: checked })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success){
                                                            $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                        } else {
                                                            $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                        }
                                                    })
                                                "
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                    peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                            </div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.menus.edit', $child->id) }}"
                                                class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    @click.prevent="
                                                        confirmModalTitle = 'Delete Submenu';
                                                        confirmModalMessage = 'Are you sure you want to delete this submenu? This action cannot be undone.';
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

                                {{-- Level 3: Sub-Submenu --}}
                                @foreach ($menus->where('parent_id', $child->id)->sortBy('order') as $subchild)
                                    <tr x-show="open" x-transition class="transition bg-gray-50/50 hover:bg-gray-100">
                                        <td
                                            class="flex items-center py-4 italic text-gray-600 px-14 whitespace-nowrap">
                                            <i class="text-gray-400 bi bi-dash-lg me-2"></i>
                                            {{ $subchild->title }}
                                        </td>
                                        <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                            {{ $subchild->url }}
                                        </td>
                                        <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                            {{ $child->title }}</td>
                                        <td
                                            class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                            {{ $subchild->order }}</td>
                                        <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-data="{ checked: {{ $subchild->status ? 'true' : 'false' }} }"
                                                    x-model="checked"
                                                    @change="
                                                        fetch('{{ route('admin.menus.toggle-status', $subchild->id) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            },
                                                            body: JSON.stringify({ status: checked })
                                                        })
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            if(data.success){
                                                                $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                            } else {
                                                                $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                            }
                                                        })
                                                    "
                                                    class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                        peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                                        after:h-5 after:w-5 after:transition-all
                                                        peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.menus.edit', $subchild->id) }}"
                                                    class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.menus.destroy', $subchild->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        @click.prevent="
                                                            confirmModalTitle = 'Delete Sub-Submenu';
                                                            confirmModalMessage = 'Are you sure you want to delete this item? This action cannot be undone.';
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

                                    {{-- ADDED: Fourth level of nesting (Sub-Sub-Submenu) --}}
                                    @foreach ($menus->where('parent_id', $subchild->id)->sortBy('order') as $subsubchild)
                                        <tr x-show="open" x-transition class="transition bg-gray-100/50 hover:bg-gray-200">
                                            <td
                                                class="flex items-center py-4 italic text-gray-500 px-16 whitespace-nowrap">
                                                <i class="text-gray-300 bi bi-dot me-2 text-xl"></i>
                                                {{ $subsubchild->title }}
                                            </td>
                                            <td class="hidden px-6 py-4 font-mono text-xs text-gray-600 sm:table-cell">
                                                {{ $subsubchild->url }}
                                            </td>
                                            <td class="hidden px-6 py-4 text-gray-600 whitespace-nowrap lg:table-cell">
                                                {{ $subchild->title }}</td>
                                            <td
                                                class="hidden px-6 py-4 text-center text-gray-600 whitespace-nowrap lg:table-cell">
                                                {{ $subsubchild->order }}</td>
                                            <td class="hidden px-6 py-4 text-center whitespace-nowrap sm:table-cell">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" x-data="{ checked: {{ $subsubchild->status ? 'true' : 'false' }} }"
                                                        x-model="checked"
                                                        @change="
                                                            fetch('{{ route('admin.menus.toggle-status', $subsubchild->id) }}', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                },
                                                                body: JSON.stringify({ status: checked })
                                                            })
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                if(data.success){
                                                                    $dispatch('notify', { message: 'Status updated!', type: 'success' })
                                                                } else {
                                                                    $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                                                                }
                                                            })
                                                        "
                                                        class="sr-only peer">
                                                    <div
                                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
                                                            peer peer-checked:after:translate-x-5 peer-checked:after:border-white
                                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                                            after:h-5 after:w-5 after:transition-all
                                                            peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('admin.menus.edit', $subsubchild->id) }}"
                                                        class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.menus.destroy', $subsubchild->id) }}"
                                                        method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            @click.prevent="
                                                                confirmModalTitle = 'Delete Sub-Sub-Submenu';
                                                                confirmModalMessage = 'Are you sure you want to delete this item? This action cannot be undone.';
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
                                    @endforeach
                                    {{-- END ADDED: Fourth level of nesting --}}

                                @endforeach
                            @endforeach
                        </tbody>
                        @empty
                            {{-- ADDED: Empty state row --}}
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

        {{-- ENHANCED: "Sweet" notification toast with progress bar (Unchanged) --}}
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
                }, 50); // Update progress every 50ms for smooth animation
            " x-show="show" x-transition:enter="transition ease-out duration-300 transform"
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


        {{-- ADDED: Beautiful Confirmation Modal (Unchanged) --}}
        <div x-show="showConfirmModal" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 flex items-center justify-center p-4">
            <div @click="showConfirmModal = false; formToSubmit = null;" class="absolute inset-0 bg-gray-900/50"></div>

            <div x-show="showConfirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-50 w-full max-w-md p-6 overflow-hidden bg-white shadow-xl rounded-2xl">

                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
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
