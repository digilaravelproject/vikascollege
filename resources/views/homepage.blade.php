@extends('layouts.app')
@section('title', 'Homepage')

@section('content')
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

    <div class="bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-12">

            @if (!empty($hpBlocks))
                @foreach ($hpBlocks as $block)
                    <x-home-page-block :block="$block" />
                @endforeach
            @endif

        </div>
    </div>
@endsection
