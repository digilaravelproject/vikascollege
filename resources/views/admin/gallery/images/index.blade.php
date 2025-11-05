@extends('layouts.admin.app')
@section('title', 'Gallery Images')

@section('content')
    <div class="p-4 sm:p-6 space-y-6" x-data="{
            activeCategory: 'all',
            showConfirmModal: false,
            confirmModalTitle: '',
            confirmModalMessage: '',
            formToSubmit: null
         }">

        {{-- 1. Page Header --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Gallery Images</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.gallery-categories.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                    <i class="bi bi-tags me-2"></i> Manage Categories
                </a>
                <a href="{{ route('admin.gallery-images.create') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-plus-circle me-2"></i> Add Image
                </a>
            </div>
        </div>

        {{-- 2. Flash Messages --}}
        @if(session('success'))
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

        {{-- 3. Category Filter Tabs --}}
        <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-2">
            <button @click="activeCategory = 'all'" :class="activeCategory === 'all'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-4 py-1.5 text-sm font-medium rounded-lg transition">
                All
            </button>

            @foreach($categories as $cat)
                <button @click="activeCategory = '{{ $cat->id }}'" :class="activeCategory === '{{ $cat->id }}'
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-1.5 text-sm font-medium rounded-lg transition">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        {{-- 4. Images Grid --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
            @forelse($images as $img)
                <div x-show="activeCategory === 'all' || activeCategory === '{{ $img->category_id }}'"
                    class="p-3 bg-white border border-gray-200 rounded-xl shadow-sm transition hover:shadow-md">

                    {{-- Category Name --}}
                    <div class="text-xs font-medium text-gray-500 mb-1">
                        {{ optional($img->category)->name ?? 'Uncategorized' }}
                    </div>

                    {{-- Image --}}
                    @if($img->image)
                        <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $img->title }}"
                            class="object-cover w-full h-36 rounded-lg">
                    @else
                        <div class="flex items-center justify-center w-full h-36 text-gray-400 bg-gray-50 rounded-lg">
                            <i class="text-3xl bi bi-image"></i>
                        </div>
                    @endif

                    {{-- Title + Actions --}}
                    <div class="flex items-center justify-between mt-2">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $img->title }}</div>
                        <div class="flex items-center gap-1">
                            {{-- Edit --}}
                            <a href="{{ route('admin.gallery-images.edit', $img) }}"
                                class="p-1.5 text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200 transition" title="Edit">
                                <i class="bi bi-pencil-square text-sm"></i>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.gallery-images.destroy', $img) }}" method="POST"
                                id="delete-form-{{ $img->id }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click.prevent="
                                            confirmModalTitle = 'Delete Image';
                                            confirmModalMessage = 'Are you sure you want to delete this image?';
                                            formToSubmit = document.getElementById('delete-form-{{ $img->id }}');
                                            showConfirmModal = true;
                                        " class="p-1.5 text-red-700 bg-red-100 rounded hover:bg-red-200 transition"
                                    title="Delete">
                                    <i class="bi bi-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-10 text-center text-gray-500 bg-white rounded-xl shadow-sm">
                    <i class="text-4xl text-gray-300 bi bi-images"></i>
                    <p class="mt-2 text-lg font-medium">No images found</p>
                    <p class="text-sm">Get started by uploading new gallery images.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($images->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $images->links() }}
            </div>
        @endif

        {{-- 5. Delete Confirmation Modal --}}
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
