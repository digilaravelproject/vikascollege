@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
    <div class="w-full min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 md:px-10">
        
        <div class="max-w-md w-full text-center space-y-6">
            <div class="relative">
                <h1 class="text-9xl font-extrabold text-gray-200">403</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-gray-800">Access Denied</h2>
                <p class="text-gray-500">
                    You do not have permission to access this page.
                </p>
            </div>

            <div class="pt-4">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#013954] text-white rounded-lg hover:bg-[#022a3d] transition-colors shadow-md font-medium">
                    &larr; Go Back
                </a>
            </div>
        </div>
    </div>
@endsection