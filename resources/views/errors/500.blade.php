@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
    <div class="w-full min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 md:px-10">
        
        <div class="max-w-md w-full text-center space-y-6">
            {{-- Error Icon --}}
            <div class="relative">
                <h1 class="text-9xl font-extrabold text-red-100">500</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            {{-- Message --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-gray-800">Internal Server Error</h2>
                <p class="text-gray-500">
                    Oops! Something went wrong on our end. We are working to fix it.
                </p>
            </div>

            {{-- Action --}}
            <div class="pt-4 flex justify-center gap-4">
                <button onclick="window.location.reload()" class="px-6 py-3 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Try Again
                </button>
                <a href="{{ url('/') }}" class="px-6 py-3 bg-[#013954] text-white rounded-lg hover:bg-[#022a3d] transition-colors font-medium">
                    Go Home
                </a>
            </div>
        </div>
    </div>
@endsection