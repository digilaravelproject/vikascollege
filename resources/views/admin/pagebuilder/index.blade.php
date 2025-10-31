@extends('layouts.admin.app')

{{-- ADDED: Section for the title --}}
@section('title', 'Page Management')

@section('content')
    {{-- MODIFIED: Main container with consistent spacing and styling --}}
    <div class="space-y-6">

        {{-- MODIFIED: Responsive header with improved typography --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">All Pages</h1>
            <a href="{{ route('admin.pagebuilder.create') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="bi bi-plus-circle me-2"></i>
                Create New Page
            </a>
        </div>

        {{-- MODIFIED: Wrapped table in a clean, shadowed card --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    {{-- MODIFIED: Professional admin table header --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase lg:table-cell">
                                #</th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Title</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase lg:table-cell">
                                Slug</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:table-cell">
                                Status</th>
                            <th scope="col"
                                class="hidden px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:table-cell">
                                Updated</th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pages as $page)
                            <tr class="transition hover:bg-gray-50">
                                {{-- ... (baki ke 'td' jaise #, Title, Slug, Status, Updated) ... --}}

                                <td class="hidden px-6 py-4 text-gray-500 whitespace-nowrap lg:table-cell">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $page->title }}</div>
                                    <div class="font-mono text-xs text-gray-500 lg:hidden">
                                        /{{ $page->slug }}
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 whitespace-nowrap lg:table-cell">
                                    <div class="font-mono text-xs text-gray-600">/{{ $page->slug }}</div>
                                </td>
                                <td class="hidden px-6 py-4 whitespace-nowrap sm:table-cell">
                                    @if ($page->status)
                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Disabled
                                        </span>
                                    @endif
                                </td>
                                <td class="hidden px-6 py-4 text-gray-500 whitespace-nowrap sm:table-cell">
                                    {{ $page->updated_at->diffForHumans() }}
                                </td>


                                {{-- +++ MODIFIED: Action Buttons Section +++ --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap items-center justify-center gap-2">

                                        {{-- +++ MODIFIED: Toggle Status Form +++ --}}
                                        <form action="{{ route('admin.pagebuilder.toggleStatus', $page) }}" method="POST"
                                              class="inline toggle-status-form"
                                              data-message="{{ $page->status ? 'Disable' : 'Enable' }} this page?">
                                            @csrf
                                            @if ($page->status)
                                                {{-- Show Disable Button --}}
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                                                    {{-- REMOVED: onclick --}}
                                                    <i class="bi bi-eye-slash me-1"></i> Disable
                                                </button>
                                            @else
                                                {{-- Show Enable Button --}}
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs bg-green-100 hover:bg-green-200 text-green-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2">
                                                    {{-- REMOVED: onclick --}}
                                                    <i class="bi bi-eye me-1"></i> Enable
                                                </button>
                                            @endif
                                        </form>

                                        <a href="{{ route('admin.pagebuilder.builder', $page) }}"
                                            class="px-3 py-1.5 text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-md font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-grid-1x2 me-1"></i> Builder
                                        </a>

                                        <a href="{{ route('admin.pagebuilder.edit', $page) }}"
                                            class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>

                                        {{-- +++ MODIFIED: Delete Form +++ --}}
                                        <form action="{{ route('admin.pagebuilder.delete', $page) }}" method="POST"
                                            class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                {{-- REMOVED: onclick --}}
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- ... (empty state) ... --}}
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <i class="text-5xl text-gray-300 bi bi-file-earmark-plus"></i>
                                    <p class="mt-3 text-lg font-medium">No pages found</p>
                                    <p class="text-sm">Get started by creating a new page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- +++ ADDED: SweetAlert2 script (Complete) +++ --}}

    {{-- 1. SweetAlert Library (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. Logic jo session check karega aur Toast dikhayega --}}
    <script>
        // Check for success message (Yeh reload ke baad aata hai)
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Check for error message (Yeh reload ke baad aata hai)
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif


        // --- YEH NAYA ADD KIYA GAYA HAI ---

        // 3. Logic jo 'Enable/Disable' form submit hone se pehle puchega
        document.querySelectorAll('.toggle-status-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Form ko submit hone se roko

                // Form se custom message nikalo (jo humne data-message me set kiya tha)
                const message = this.getAttribute('data-message');

                Swal.fire({
                    title: 'Are you sure?',
                    text: message, // Yahan custom message dikhao
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, do it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Agar user "Yes" bole, toh form submit karo
                    }
                });
            });
        });

        // 4. Logic jo 'Delete' form submit hone se pehle puchega
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Form ko submit hone se roko

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Delete ke liye red button
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Agar user "Yes" bole, toh form submit karo
                    }
                });
            });
        });

    </script>

@endsection
