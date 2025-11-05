@extends('layouts.admin.app')
@section('title', 'Why Choose Us')

@section('content')
    <div class="p-4 sm:p-6 space-y-6"
        x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', formToSubmit: null }">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Why Choose Us</h1>
            <a href="{{ route('admin.why-choose-us.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i> Add Item
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show"
                class="flex items-center justify-between p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50"
                x-transition>
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill text-lg me-3"></i>
                    <div><span class="font-medium">Success:</span> {{ session('success') }}</div>
                </div>
                <button @click="show = false" class="ml-3 text-green-700/70 hover:text-green-900">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        {{-- Table Container --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">All Items</h2>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Icon / Image</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Title</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Order</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($items as $it)
                            <tr class="transition hover:bg-gray-50">
                                {{-- Icon/Image --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($it->icon_or_image)
                                        <img src="{{ asset('storage/' . $it->icon_or_image) }}"
                                            class="object-cover w-12 h-12 rounded-lg border border-gray-200 shadow-sm" alt="Icon">
                                    @else
                                        <div
                                            class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-lg text-gray-400">
                                            <i class="bi bi-image text-lg"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- Title --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $it->title }}</div>
                                    @if($it->description)
                                        <div class="text-xs text-gray-500 line-clamp-1 max-w-[300px]">
                                            {{ Str::limit($it->description, 80) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Sort Order --}}
                                <td class="px-6 py-3 text-center text-gray-600 whitespace-nowrap">
                                    {{ $it->sort_order }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.why-choose-us.edit', $it) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>

                                        <form action="{{ route('admin.why-choose-us.destroy', $it) }}" method="POST"
                                            id="delete-form-{{ $it->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" @click.prevent="
                                                            confirmTitle = 'Delete Item';
                                                            confirmMessage = 'Are you sure you want to delete this item?';
                                                            formToSubmit = document.getElementById('delete-form-{{ $it->id }}');
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
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i class="bi bi-info-circle text-4xl text-gray-300"></i>
                                    <p class="mt-2 text-lg font-medium">No items found</p>
                                    <p class="text-sm text-gray-500">Start by adding a new item.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($items->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-gray-900/50" x-transition>
            <div @click="showConfirmModal = false" class="absolute inset-0"></div>
            <div class="relative z-50 w-full max-w-md p-6 bg-white rounded-2xl shadow-xl" x-transition.scale>
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0">
                        <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold text-gray-900" x-text="confirmTitle"></h3>
                        <p class="mt-2 text-sm text-gray-600" x-text="confirmMessage"></p>
                    </div>
                </div>

                <div class="mt-6 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button @click="formToSubmit.submit(); showConfirmModal = false"
                        class="inline-flex justify-center w-full px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Confirm Delete
                    </button>
                    <button @click="showConfirmModal = false; formToSubmit = null"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-semibold text-gray-900 bg-white rounded-lg shadow-sm ring-1 ring-gray-300 hover:bg-gray-100 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
