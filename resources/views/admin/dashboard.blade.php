@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-sm text-gray-500">Welcome back, Admin 👋</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between border border-gray-100 hover:shadow-lg transition">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Pending Testimonials</p>
                    <h2 class="text-3xl font-bold text-blue-600 mt-1">{{ $pendingTestimonials ?? 0 }}</h2>
                </div>
                <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                    <i class="fas fa-comment-dots text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between border border-gray-100 hover:shadow-lg transition">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Upcoming Events</p>
                    <h2 class="text-3xl font-bold text-green-600 mt-1">{{ $upcomingEvents ?? 0 }}</h2>
                </div>
                <div class="p-3 bg-green-100 text-green-600 rounded-full">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between border border-gray-100 hover:shadow-lg transition">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Announcements</p>
                    <h2 class="text-3xl font-bold text-purple-600 mt-1">{{ $activeAnnouncements ?? 0 }}</h2>
                </div>
                <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between border border-gray-100 hover:shadow-lg transition">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Gallery Images</p>
                    <h2 class="text-3xl font-bold text-red-600 mt-1">{{ \App\Models\GalleryImage::count() }}</h2>
                </div>
                <div class="p-3 bg-red-100 text-red-600 rounded-full">
                    <i class="fas fa-images text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Enrollment Trends -->
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Enrollment Trends</h2>
                    <span class="text-xs text-gray-400">Last 6 Months</span>
                </div>
                <div class="h-60 flex items-center justify-center text-gray-400">
                    <span class="text-sm">[ Chart Placeholder ]</span>
                </div>
            </div>

            <!-- Department Distribution -->
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Department Distribution</h2>
                    <span class="text-xs text-gray-400">Current Semester</span>
                </div>
                <div class="h-60 flex items-center justify-center text-gray-400">
                    <span class="text-sm">[ Chart Placeholder ]</span>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments Table -->
        <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
            <div class="flex justify-between items-center border-b p-4">
                <h2 class="text-lg font-semibold text-gray-700">Recent Enrollments</h2>
                <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-gray-600 text-sm">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium">Student</th>
                            <th class="px-6 py-3 text-left font-medium">Course</th>
                            <th class="px-6 py-3 text-left font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">John Doe</td>
                            <td class="px-6 py-3">Physics</td>
                            <td class="px-6 py-3">2025-10-27</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">Jane Smith</td>
                            <td class="px-6 py-3">Mathematics</td>
                            <td class="px-6 py-3">2025-10-26</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">Robert Brown</td>
                            <td class="px-6 py-3">Chemistry</td>
                            <td class="px-6 py-3">2025-10-25</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">Mary Johnson</td>
                            <td class="px-6 py-3">Biology</td>
                            <td class="px-6 py-3">2025-10-24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection