@extends('layouts.admin.app')
@section('title', 'Website Settings')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-800">Website Settings</h1>

        @if(session('success'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data"
            class="p-6 space-y-8 bg-white shadow rounded-2xl">
            @csrf

            {{-- GENERAL INFO --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">College Name</label>
                    <input type="text" name="college_name" value="{{ old('college_name', $data['college_name']) }}"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Banner Heading</label>
                    <input type="text" name="banner_heading" value="{{ old('banner_heading', $data['banner_heading']) }}"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Banner Subheading</label>
                    <input type="text" name="banner_subheading"
                        value="{{ old('banner_subheading', $data['banner_subheading']) }}"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Button Text</label>
                    <input type="text" name="banner_button_text"
                        value="{{ old('banner_button_text', $data['banner_button_text']) }}"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Button Link</label>
                    <input type="url" name="banner_button_link"
                        value="{{ old('banner_button_link', $data['banner_button_link']) }}"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- LOGO + FAVICON --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">College Logo</label>
                    <input type="file" name="college_logo" accept="image/*"
                        class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['college_logo'])
                        <img src="{{ asset('storage/' . $data['college_logo']) }}"
                            class="object-contain w-40 h-40 mt-3 border rounded-lg shadow">
                    @endif
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Favicon</label>
                    <input type="file" name="favicon" accept="image/*"
                        class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['favicon'])
                        <img src="{{ asset('storage/' . $data['favicon']) }}"
                            class="object-contain w-16 h-16 mt-3 border rounded-lg shadow">
                    @endif
                </div>

                {{-- Banner Media --}}
                <div class="col-span-3">
                    <label class="block mb-1 text-sm font-medium text-gray-700">
                        Banner Media (Upload Multiple Images or Videos)
                    </label>
                    <input type="file" name="banner_media[]" accept="image/*,video/*" multiple
                        class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">

                    <p class="mt-1 text-xs text-gray-500">
                        Upload multiple images/videos. They’ll be automatically optimized for speed.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-3">
                        @foreach($data['banner_media'] as $item)
                            @php $media = json_decode($item->value, true); @endphp
                            @if($media['type'] === 'image')
                                <img src="{{ asset('storage/' . $media['path']) }}"
                                    class="object-cover w-full h-40 border rounded-lg shadow">
                            @else
                                <video controls class="object-cover w-full h-40 border rounded-lg shadow">
                                    <source src="{{ asset('storage/' . $media['path']) }}" type="video/mp4">
                                </video>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700">
                    <i class="bi bi-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection