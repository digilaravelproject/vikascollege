@extends('layouts.admin.app')
@section('title', 'Page Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <h1 class="text-3xl font-bold text-gray-900">All Pages</h1>
        <div class="flex gap-4 w-full sm:w-auto">
            {{-- SEARCH BAR --}}
            <div class="relative flex-grow sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </span>
                <input type="text" id="pageSearch" placeholder="Search title or slug..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <a href="{{ route('admin.pagebuilder.create') }}" class="flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition whitespace-nowrap">
                <i class="bi bi-plus-circle me-2"></i> Create New Page
            </a>
        </div>
    </div>

    {{-- DRAGGABLE CONTAINER --}}
    <div id="dragContainer" class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl cursor-grab active:cursor-grabbing">
        <div class="overflow-x-auto no-scrollbar scroll-smooth" id="scrollContent">
            <table class="min-w-full text-sm divide-y divide-gray-200 select-none">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="hidden px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase lg:table-cell">#</th>
                        <th class="px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Title</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase lg:table-cell">Slug</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase sm:table-cell">Status</th>
                        <th class="hidden px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase sm:table-cell">Updated</th>
                        <th class="px-6 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                    @include('admin.pagebuilder.partials._table_rows')
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION CONTAINER --}}
    <div id="paginationLinks" class="mt-4">
        {{ $pages->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- 1. DRAG TO SCROLL (Photoshop Style) ---
    const slider = document.getElementById('scrollContent');
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.classList.add('active');
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });
    slider.addEventListener('mouseleave', () => isDown = false);
    slider.addEventListener('mouseup', () => isDown = false);
    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed
        slider.scrollLeft = scrollLeft - walk;
    });

    // --- 2. AJAX SEARCH & PAGINATION ---
    const searchInput = document.getElementById('pageSearch');
    const tableBody = document.getElementById('tableBody');
    const paginationLinks = document.getElementById('paginationLinks');

    const fetchPages = (page = 1, search = '') => {
        const url = `{{ route('admin.pagebuilder.index') }}?page=${page}&search=${search}`;
        
        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.html;
                paginationLinks.innerHTML = data.pagination;
                // Re-initialize SweetAlert for new buttons
                attachFormListeners();
            });
    };

    searchInput.addEventListener('input', (e) => {
        fetchPages(1, e.target.value);
    });

    // Pagination links click par AJAX call
    document.addEventListener('click', (e) => {
        if (e.target.closest('#paginationLinks a')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page');
            fetchPages(page, searchInput.value);
        }
    });

    // --- 3. SWEETALERT & STATUS TOGGLE (Logic shifted to function for AJAX compatibility) ---
    function attachFormListeners() {
        document.querySelectorAll('.toggle-status-form, .delete-form').forEach(form => {
            form.onsubmit = function(e) {
                e.preventDefault();
                const isDelete = this.classList.contains('delete-form');
                const message = this.getAttribute('data-message') || "You won't be able to revert this!";
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isDelete ? '#d33' : '#3085d6',
                    confirmButtonText: isDelete ? 'Yes, delete it!' : 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            };
        });
    }
    attachFormListeners();

    // Success/Error Toasts (existing logic)
    @if(session('success'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session('success') }}', showConfirmButton: false, timer: 3000 });
    @endif
</script>

<style>
    /* Table scrollbar hide karne ke liye lekin scroll working rahega */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .cursor-grab { cursor: grab; }
    .cursor-grabbing { cursor: grabbing; }
</style>
@endsection