@extends('layouts.admin.app')

@section('title', 'Testimonials')

@section('content')
    <div class="p-4 sm:p-6 space-y-6"
        x-data="{ showConfirmModal: false, confirmModalTitle: '', confirmModalMessage: '', formToSubmit: null }">

        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Testimonials</h1>
            <a href="{{ route('admin.testimonials.create') }}" class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm
                      hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i> Add Testimonial
            </a>
        </div>

        {{-- Success Message --}}
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

        {{-- Table Card --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">All Testimonials</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Image</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Student</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Status</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Updated</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($testimonials as $t)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($t->student_image)
                                        <img src="{{ asset('storage/' . $t->student_image) }}" alt="{{ $t->student_name }}"
                                            class="object-cover w-10 h-10 rounded-full">
                                    @else
                                        <div
                                            class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100 rounded-full">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $t->student_name }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                            {{ $t->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $t->status ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    {{ $t->updated_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.testimonials.edit', $t) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition
                                                      focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>

                                        <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                                            id="delete-form-{{ $t->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click.prevent="
                                                            confirmModalTitle = 'Delete Testimonial';
                                                            confirmModalMessage = 'Are you sure you want to delete this testimonial? This action cannot be undone.';
                                                            formToSubmit = document.getElementById('delete-form-{{ $t->id }}');
                                                            showConfirmModal = true;
                                                        "
                                                class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition
                                                               focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="text-4xl text-gray-300 bi bi-person-x"></i>
                                    <p class="mt-2 text-lg font-medium">No testimonials found</p>
                                    <p class="text-sm">Add your first testimonial to see it here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($testimonials->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $testimonials->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showConfirmModal" style="display: none;" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 flex items-center justify-center p-4" x-cloak>

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
                    <button @click="formToSubmit.submit(); showConfirmModal = false;" type="button" class="inline-flex justify-center w-full px-4 py-2 text-sm font-semibold text-white transition
                                   bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none
                                   focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Confirm Delete
                    </button>
                    <button @click="showConfirmModal = false; formToSubmit = null;" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-semibold text-gray-900 transition
                                   bg-white rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
