@extends('layouts.admin.app')

@section('title', 'Enquiry Leads')

@section('content')
    <div class="space-y-6 p-6"> {{-- Use space-y-6 for consistent vertical rhythm --}}

        {{-- Page Header and Actions (Matching H1 style) --}}
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">📬 Enquiry Leads</h1>

            <div class="flex gap-3">
                {{-- Reset Button Style --}}
                <a href="{{ route('admin.leads.enquiries') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 transition">
                    Reset Filters
                </a>

                {{-- Export Button Style (Matching the blue button from reference) --}}
                <a href="#"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition">
                    Export CSV
                </a>
            </div>
        </div>



        {{-- Filters (Wrapped in a card, consistent input/calendar style) --}}
        <div class="p-4 bg-white border border-gray-200 shadow-md rounded-xl">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-7">

                {{-- Input Styling Definition: Base classes for consistency --}}
                @php
                    $inputClasses = 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm';
                @endphp

                {{-- Search Input --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone"
                    class="{{ $inputClasses }} md:col-span-2">

                {{-- Date Input (From) with UX enhancement --}}
                <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date From"
                    name="from" value="{{ request('from') }}" class="{{ $inputClasses }}">

                {{-- Date Input (To) with UX enhancement --}}
                <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date To"
                    name="to" value="{{ request('to') }}" class="{{ $inputClasses }}">

                {{-- Apply Filters Button (Matching blue button style) --}}
                <button
                    class="w-full py-2 text-sm font-medium text-white transition bg-blue-600 rounded-lg md:col-span-2 hover:bg-blue-700 shadow-sm">
                    Apply Filters
                </button>
            </form>
        </div>


        {{-- Table Container (Matching the elegant shadow/border from reference) --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50"> {{-- Light gray header background --}}
                        <tr>
                            {{-- Adjusted padding and font to match reference table headers --}}
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Name</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Email</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Mobile</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Message</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                                Created</th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
                                Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($enquiries as $e)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $e->first_name }} {{ $e->last_name }}</td>
                                <td class="px-6 py-4 text-blue-600 whitespace-nowrap">{{ $e->email }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $e->mobile_prefix }} {{ $e->mobile_no }}</td>
                                {{-- Increased font size and padding to match table body style --}}
                                <td class="px-6 py-4 text-gray-700 max-w-xs overflow-hidden">
                                    {{ Str::limit($e->message, 50) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $e->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    {{-- Role badge style from reference, adjusted colors for consent --}}
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full
                                             {{ $e->authorised_contact ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $e->authorised_contact ? 'Authorised' : 'No Consent' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Empty state text adjusted for better look --}}
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <i class="text-5xl text-gray-300 bi bi-inbox"></i>
                                    <p class="mt-3 text-lg font-medium">No enquiry leads found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Pagination --}}
        <div class="mt-6">
            {{ $enquiries->links() }}
        </div>
    </div>
@endsection
