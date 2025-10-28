@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-2xl font-bold text-center mb-6">Welcome to {{ setting('college_name') }}</h2>
        <p class="text-center text-gray-600">
            Explore our academic programs, admissions, and campus life.
        </p>
    </div>
@endsection