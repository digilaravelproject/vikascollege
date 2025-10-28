@extends('layouts.app')

@section('content')
    {{-- Hero Banner --}}
    @include('partials.hero-banner')
    <div class="container">
        <h2 class="mb-6 text-2xl font-bold text-center">Welcome to {{ setting('college_name') }}</h2>
        <p class="text-center text-gray-600">
            Explore our academic programs, admissions, and campus life.
        </p>
    </div>
@endsection