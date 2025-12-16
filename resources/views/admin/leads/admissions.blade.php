@extends('layouts.admin.app')

@section('title', 'Admission Leads')

@section('content')
    <div class="space-y-6 p-6">

        {{-- Page Header and Actions --}}
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">🎓 Admission Leads</h1>

            <div class="flex gap-3">
                <a href="{{ route('admin.leads.admissions') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 transition">
                    Reset Filters
                </a>
                <a href="#"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition">
                    Export CSV
                </a>
            </div>
        </div>


        {{-- Filters (Enhanced Input Styling) --}}
        <div class="p-4 bg-white border border-gray-200 shadow-md rounded-xl">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-9">

                {{-- Input Styling Definition: Base classes for consistency --}}
                @php
                    $inputClasses = 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm';
                @endphp

                {{-- Search Input --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone"
                    class="{{ $inputClasses }} md:col-span-2">

                {{-- Discipline Select (Dropdown) --}}
                {{-- <select name="discipline" class="{{ $inputClasses }}">
                    <option value="">-- Select Discipline --</option>
                    @foreach($disciplines as $d)
                        <option value="{{ $d }}" @selected(request('discipline') == $d)>{{ $d }}</option>
                    @endforeach
                </select> --}}

                {{-- Date Input (From) --}}
                <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date From"
                    name="from" value="{{ request('from') }}" class="{{ $inputClasses }}">

                {{-- Date Input (To) --}}
                <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date To"
                    name="to" value="{{ request('to') }}" class="{{ $inputClasses }}">

                {{-- Apply Filters Button --}}
                <button
                    class="w-full py-2 text-sm font-medium text-white transition bg-blue-600 rounded-lg md:col-span-2 hover:bg-blue-700 shadow-sm">
                    Apply Filters
                </button>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Name</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Email</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Mobile</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Programme</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Created</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leads as $lead)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $lead->first_name }} {{ $lead->last_name }}</td>
                                <td class="px-6 py-4 text-blue-600 whitespace-nowrap">{{ $lead->email }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $lead->mobile_prefix }} {{ $lead->mobile_no }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $lead->programme }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $lead->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full
                                             {{ $lead->authorised_contact ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $lead->authorised_contact ? 'Verified' : 'Unverified' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <i class="text-5xl text-gray-300 bi bi-people"></i>
                                    <p class="mt-3 text-lg font-medium">No admission leads found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Pagination --}}
        <div class="mt-6">
            {{ $leads->links() }}
        </div>
    </div>
@endsection
