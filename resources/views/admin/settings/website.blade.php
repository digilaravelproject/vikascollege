@extends('layouts.admin.app')
@section('title', 'Website Settings')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-800">Website Settings</h1>

        @if(session('success'))
            <div class="p-4 rounded-lg bg-green-100 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow p-6 space-y-8">
            @csrf

            {{-- GENERAL INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">College Name</label>
                    <input type="text" name="college_name" value="{{ old('college_name', $data['college_name']) }}"
                        class="w-full border border-gray-300  rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banner Heading</label>
                    <input type="text" name="banner_heading" value="{{ old('banner_heading', $data['banner_heading']) }}"
                        class="w-full border border-gray-300  rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banner Subheading</label>
                    <input type="text" name="banner_subheading"
                        value="{{ old('banner_subheading', $data['banner_subheading']) }}"
                        class="w-full border border-gray-300  rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                    <input type="text" name="banner_button_text"
                        value="{{ old('banner_button_text', $data['banner_button_text']) }}"
                        class="w-full border border-gray-300  rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                    <input type="url" name="banner_button_link"
                        value="{{ old('banner_button_link', $data['banner_button_link']) }}"
                        class="w-full border border-gray-300  rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- BRANDING SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">College Logo</label>
                    <input type="file" name="college_logo" accept="image/*"
                        class="block w-full text-sm text-gray-700 border border border-gray-300  rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['college_logo'])
                        <img src="{{ asset('storage/' . $data['college_logo']) }}"
                            class="mt-3 rounded-lg w-40 h-40 object-contain border shadow">
                    @endif
                </div>

                {{-- Favicon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                    <input type="file" name="favicon" accept="image/*"
                        class="block w-full text-sm text-gray-700 border border border-gray-300  rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['favicon'])
                        <img src="{{ asset('storage/' . $data['favicon']) }}"
                            class="mt-3 rounded-lg w-16 h-16 object-contain border shadow">
                    @endif
                </div>

                {{-- Banner --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Banner</label>
                    <input type="file" name="banner_image" accept="image/*"
                        class="block w-full text-sm text-gray-700 border border border-gray-300  rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['banner_image'])
                        <img src="{{ asset('storage/' . $data['banner_image']) }}"
                            class="mt-3 rounded-lg w-full max-h-48 object-cover border shadow">
                    @endif
                </div>
                {{-- Top Banner --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Top Banner</label>
                    <input type="file" name="top_banner_image" accept="image/*"
                        class="block w-full text-sm text-gray-700 border border border-gray-300  rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500">
                    @if ($data['top_banner_image'])
                        <img src="{{ asset('storage/' . $data['top_banner_image']) }}"
                            class="mt-3 rounded-lg w-full max-h-48 object-cover border shadow">
                    @endif
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
                    <i class="bi bi-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection