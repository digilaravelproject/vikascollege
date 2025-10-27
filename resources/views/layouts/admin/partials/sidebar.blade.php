<div class="bg-dark text-white border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 fw-bold fs-4 border-bottom">College Admin</div>
    <div class="list-group list-group-flush my-3" style="height: 45.97em;overflow-y: auto;">
        <a href="{{ route('admin.dashboard') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="#studentsSubmenu" data-bs-toggle="collapse"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people-fill me-2"></i> Students</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse" id="studentsSubmenu">
            <a href="#" class="list-group-item list-group-item-action ps-5">All Students</a>
            <a href="#" class="list-group-item list-group-item-action ps-5">Add Student</a>
        </div>

        <a href="#facultySubmenu" data-bs-toggle="collapse"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><i class="bi bi-person-badge-fill me-2"></i> Faculty</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse" id="facultySubmenu">
            <a href="#" class="list-group-item list-group-item-action ps-5">All Faculty</a>
            <a href="#" class="list-group-item list-group-item-action ps-5">Add Faculty</a>
        </div>

        <a href="#coursesSubmenu" data-bs-toggle="collapse"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book-fill me-2"></i> Courses</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse" id="coursesSubmenu">
            <a href="#" class="list-group-item list-group-item-action ps-5">All Courses</a>
            <a href="#" class="list-group-item list-group-item-action ps-5">Add Course</a>
        </div>
        <a href="#menuSubmenu" data-bs-toggle="collapse"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book-fill me-2"></i> Menus</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse" id="menuSubmenu">
            <a href="{{ route('admin.menus.index') }}" class="list-group-item list-group-item-action ps-5">All Menus</a>
            <a href="{{ route('admin.menus.create') }}" class="list-group-item list-group-item-action ps-5">Add Menu</a>
        </div>
        <a href="{{ route('admin.roles-permissions.index') }}" class="list-group-item list-group-item-action">

            <i class="bi bi-person-rolodex"></i> All Roles
        </a>
        <a href="#" class="list-group-item list-group-item-action">
            {{-- <a href="{{ route('settings') }}" class="list-group-item list-group-item-action"> --}}
                <i class="bi bi-gear-fill me-2"></i> Settings
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action border-0 text-start bg-dark text-white">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
    </div>
</div>