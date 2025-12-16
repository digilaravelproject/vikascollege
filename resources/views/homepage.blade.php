@extends('layouts.app')
@section('title', 'Home')
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        /* Google Font (Inter) import karein */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        /* Is ID ko use karke, font sirf isi page ke content par apply hoga */
        #homepage-wrapper {
            font-family: 'Inter', sans-serif;
        }
    </style>
@endpush
@section('content')
    <div id="homepage-wrapper">
        @php
            // Load homepage layout from settings
            $hp = setting('homepage_layout');
            $hpBlocks = [];
            if ($hp) {
                $parsed = json_decode($hp, true);
                $hpBlocks = $parsed['blocks'] ?? [];
            }
        @endphp

        @include('partials.hero-banner')
        @if (!empty($hpBlocks))
            @foreach ($hpBlocks as $block)
                {{-- Sabse important change: Hum yahan $loop pass kar rahe hain --}}
                <x-home-page-block :block="$block" :loop="$loop" />
            @endforeach
        @endif
    </div>
@endsection
@push('script')
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true // Animation sirf ek baar chalega
        });
    </script>

@endpush
