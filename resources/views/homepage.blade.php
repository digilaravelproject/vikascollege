@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div id="homepage-wrapper">
        @php
            // Cache decoded blocks to avoid JSON parsing on every request
            $hpBlocks = \Illuminate\Support\Facades\Cache::remember('homepage_layout_blocks', 3600, function() {
                $hp = setting('homepage_layout');
                if ($hp) {
                    $parsed = json_decode($hp, true);
                    return $parsed['blocks'] ?? [];
                }
                return [];
            });
        @endphp

        @include('partials.hero-banner')

        @if (!empty($hpBlocks))
            @foreach ($hpBlocks as $block)
                {{-- Passing $loop for potential nth-child styling optimizations in sub-components --}}
                <x-home-page-block :block="$block" :loop="$loop" />
            @endforeach
        @endif
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Safe AOS init checks for global AOS existence
            if (window.AOS) {
                AOS.init({
                    once: true
                });
            }
        });
    </script>
@endpush
