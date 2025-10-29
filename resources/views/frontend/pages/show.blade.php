@extends('layouts.app')

@section('title', $page->title)

@section('content')
    <div class="max-w-5xl px-4 py-10 mx-auto">
        <h1 class="mb-6 text-3xl font-bold text-gray-800">{{ $page->title }}</h1>

        @if($page->image)
            <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}"
                class="object-cover w-full mb-6 rounded-lg shadow max-h-96">
        @endif

        <div class="prose prose-blue max-w-none">
            {!! $page->content !!}
        </div>
        {{-- <div class="prose prose-blue max-w-none">
            @php
            $blocks = json_decode($page->content, true);
            @endphp

            @if(is_array($blocks))
            @foreach($blocks as $block)
            @switch($block['type'])
            @case('heading')
            <h2 class="my-4 text-2xl font-bold">{{ $block['content'] ?? '' }}</h2>
            @break

            @case('text')
            <p class="mb-4 text-gray-700">{{ $block['content'] ?? '' }}</p>
            @break

            @case('image')
            <img src="{{ $block['src'] ?? '' }}" class="mb-6 rounded-lg shadow" />
            @break

            @case('video')
            <div class="my-4">
                <iframe src="{{ $block['src'] ?? '' }}" class="w-full h-64 rounded-lg"></iframe>
            </div>
            @break

            @case('pdf')
            <iframe src="{{ $block['src'] ?? '' }}" class="w-full mb-6 border rounded-lg h-96"></iframe>
            @break
            @endswitch
            @endforeach
            @endif
        </div> --}}

        @if($page->pdf)
            <div class="mt-8">
                <a href="{{ asset('storage/' . $page->pdf) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    📄 View Attached PDF
                </a>
            </div>
        @endif
    </div>
@endsection