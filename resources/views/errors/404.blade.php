@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
    <div class="w-full min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 md:px-10">
        
        <div class="max-w-md w-full text-center space-y-6">
            {{-- Error Illustration / Icon --}}
            <div class="relative">
                <h1 class="text-9xl font-extrabold text-[#013954] opacity-20">404</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#013954]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Error Message --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-gray-800">Page Not Found</h2>
                <p class="text-gray-500">
                    Sorry, the page you are looking for doesn't exist or has been moved.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#013954] text-white rounded-lg hover:bg-[#022a3d] transition-colors shadow-md font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection