@extends('layouts.app')
@section('title', $activeSection->title)
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css"
        integrity="sha512-UmV2ARg2MsY8TysMjhJvXSQHYgiYSVPS5ULXZCsTP3RgiMmBJhf8qP93vEyJgYuGt3u9V6wem73b11/Y8GVcOg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('content')
    <section class="container px-4 py-10 mx-auto">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">

            {{-- Sidebar --}}
            <aside class="space-y-2 md:sticky md:top-24 h-fit">
                <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b">The Trust</h2>
                @foreach($sections as $section)
                        <a href="{{ route('trust.index', $section->slug) }}" class="block px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $activeSection->id == $section->id
                    ? 'bg-[#013954] text-white shadow-md'
                    : 'bg-gray-100 text-gray-800 hover:bg-blue-50 hover:text-[#013954]' }}">
                            {{ strtoupper($section->title) }}
                        </a>
                @endforeach
            </aside>

            {{-- Main Content --}}
            <main class="p-6 space-y-6 bg-white shadow-md rounded-2xl md:col-span-3">
                {{-- <h1 class="pb-3 text-2xl font-semibold text-gray-800 border-b">{{ $activeSection->title }}</h1> --}}

                {{-- Display Content Exactly --}}
                @if($activeSection->content)
                    <div id="quill-content" class="max-w-full">
                        {!! $activeSection->content !!}
                    </div>
                @endif

                {{-- Image Gallery --}}
                @if($activeSection->images->count())
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 md:grid-cols-3">
                        @foreach($activeSection->images as $img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $activeSection->title }}"
                                class="object-cover w-full h-64 rounded-lg shadow">
                        @endforeach
                    </div>
                @endif

                {{-- PDF Download --}}
                @if($activeSection->pdf_path)
                    <div class="mt-0">
                        <iframe src="{{ asset('storage/' . $activeSection->pdf_path) }}"
                            class="w-full h-[600px] border rounded-lg shadow-inner"></iframe>
                    </div>
                @endif
            </main>
        </div>
    </section>


    <style>
        #quill-content p {
            /* margin-bottom: 1rem; */
        }

        #quill-content h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        #quill-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        #quill-content h3 {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        #quill-content ul,
        #quill-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        #quill-content li {
            /* margin-bottom: 0.5rem; */
        }

        #quill-content strong {
            font-weight: 700;
        }

        #quill-content em {
            font-style: italic;
        }

        #quill-content a {
            color: #3b82f6;
            /* Tailwind blue-500 */
            text-decoration: underline;
        }

        #quill-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
            border-radius: 0.5rem;
        }

        #quill-content .ql-align-center {
            text-align: center;
        }

        #quill-content .ql-align-right {
            text-align: right;
        }

        #quill-content .ql-align-justify {
            text-align: justify;
        }

        /* Ordered lists */
        #quill-content [data-list="ordered"],
        #quill-content .ql-list-ordered {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Unordered lists */
        #quill-content [data-list="bullet"],
        #quill-content .ql-list-bullet {
            list-style-type: disc;
            margin-left: 1.5rem;
            /* margin-bottom: 1rem; */
        }

        /* Nested list indents */
        #quill-content .ql-indent-1 {
            margin-left: 2em;
        }

        #quill-content .ql-indent-2 {
            margin-left: 4em;
        }

        #quill-content .ql-indent-3 {
            margin-left: 6em;
        }


        #quill-content ul,
        #quill-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>

@endsection