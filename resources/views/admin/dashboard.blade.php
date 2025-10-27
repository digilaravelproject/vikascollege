@extends('layouts.admin.app')

@section('title', 'Admin Dashboard')

@section('page_title', 'Admin Dashboard')

@section('content')
    <div class="row g-3">
        <!-- Total Students Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-primary rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase"><i class="bi bi-people-fill me-2"></i> Total Students</h6>
                        <h3 class="card-text fw-bold">1,234</h3>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Total Faculty Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-success rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase"><i class="bi bi-person-badge-fill me-2"></i> Total Faculty
                        </h6>
                        <h3 class="card-text fw-bold">56</h3>
                    </div>
                    <i class="bi bi-person-badge-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Total Courses Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-warning rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase"><i class="bi bi-book-fill me-2"></i> Total Courses</h6>
                        <h3 class="card-text fw-bold">32</h3>
                    </div>
                    <i class="bi bi-book-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments Table -->
    <div class="card mt-4 shadow-sm rounded-3 border-0">
        <div class="card-header bg-white fw-bold">
            Recent Enrollments
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Physics</td>
                            <td>2025-10-27</td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Math</td>
                            <td>2025-10-26</td>
                        </tr>
                        <tr>
                            <td>Robert Brown</td>
                            <td>Chemistry</td>
                            <td>2025-10-25</td>
                        </tr>
                        <tr>
                            <td>Mary Johnson</td>
                            <td>Biology</td>
                            <td>2025-10-24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection