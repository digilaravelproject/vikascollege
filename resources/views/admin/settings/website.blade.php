@extends('layouts.admin.app')
@section('title', 'Website Settings')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Website Settings</h1>
        </div>

        {{-- Session Messages --}}
        @if (session('success'))
            <div class="flex p-4 text-sm text-green-700 border border-green-200 rounded-lg bg-green-50" role="alert">
                <i class="bi bi-check-circle-fill me-3"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="flex p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                <div>
                    <span class="font-medium">Please fix the following errors:</span>
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{--
         MODIFIED:
         - Added x-data="{ activeTab: 'general' }" to manage tabs.
         - Added Tab Navigation.
         - Wrapped each card in a div with x-show="activeTab === '...'"
        --}}
        <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6" x-data="settingsForm()" @submit="showSavingAlert">
            @csrf

            <div x-data="{ activeTab: 'general' }">
                {{-- TAB NAVIGATION --}}
                <div class="mb-4 border-b border-gray-200">
                    <nav class="flex flex-wrap -mb-px space-x-4" aria-label="Tabs">
                        @php
                            $tabs = [
                                'general' => ['name' => 'General', 'icon' => 'bi-gear'],
                                'branding' => ['name' => 'Branding', 'icon' => 'bi-stars'],
                                'contact' => ['name' => 'Contact', 'icon' => 'bi-telephone'],
                                'social' => ['name' => 'Social Links', 'icon' => 'bi-share'],
                                'footer' => ['name' => 'Footer', 'icon' => 'bi-window-dock'],
                                'seo' => ['name' => 'SEO', 'icon' => 'bi-google'],
                                'media' => ['name' => 'Banner Media', 'icon' => 'bi-images'],
                            ];
                        @endphp

                        @foreach($tabs as $key => $tab)
                        <button type="button"
                            @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center px-1 py-3 text-sm font-medium border-b-2 group whitespace-nowrap focus:outline-none">
                            <i class="{{ $tab['icon'] }} me-2" :class="activeTab === '{{ $key }}' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'"></i>
                            {{ $tab['name'] }}
                        </button>
                        @endforeach
                    </nav>
                </div>

                {{-- TAB CONTENT PANELS --}}
                <div>
                    {{-- TAB 1: GENERAL SETTINGS --}}
                    <div x-show="activeTab === 'general'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">General Settings</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label for="college_name" class="block mb-1.5 text-sm font-medium text-gray-700">College Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="college_name" name="college_name" value="{{ old('college_name', $data['college_name']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="banner_heading" class="block mb-1.5 text-sm font-medium text-gray-700">Banner Heading</label>
                                        <input type="text" id="banner_heading" name="banner_heading" value="{{ old('banner_heading', $data['banner_heading']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="banner_subheading" class="block mb-1.5 text-sm font-medium text-gray-700">Banner Subheading</label>
                                        <input type="text" id="banner_subheading" name="banner_subheading" value="{{ old('banner_subheading', $data['banner_subheading']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="banner_button_text" class="block mb-1.5 text-sm font-medium text-gray-700">Button Text</label>
                                        <input type="text" id="banner_button_text" name="banner_button_text" value="{{ old('banner_button_text', $data['banner_button_text']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="banner_button_link" class="block mb-1.5 text-sm font-medium text-gray-700">Button Link</label>
                                        <input type="url" id="banner_button_link" name="banner_button_link" value="{{ old('banner_button_link', $data['banner_button_link']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://example.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: BRANDING --}}
                    <div x-show="activeTab === 'branding'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Branding</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label for="college_logo" class="block mb-1.5 text-sm font-medium text-gray-700">College Logo</label>
                                        {{-- FIX: Updated accept attribute --}}
                                        <input type="file" id="college_logo" name="college_logo" accept="image/png, image/jpeg, image/webp, image/svg+xml"
                                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended: SVG, PNG, or JPG (max 2MB)</p>
                                        @if ($data['college_logo'])
                                            <img src="{{ asset('storage/' . $data['college_logo']) }}" class="object-contain w-auto h-24 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                                        @endif
                                    </div>
                                    <div>
                                        <label for="favicon" class="block mb-1.5 text-sm font-medium text-gray-700">Favicon</label>
                                        {{-- FIX: Updated accept attribute --}}
                                        <input type="file" id="favicon" name="favicon" accept="image/png, image/x-icon, image/svg+xml"
                                        class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended: 32x32 PNG, ICO, or SVG</p>
                                        @if ($data['favicon'])
                                        <img src="{{ asset('storage/' . $data['favicon']) }}" class="object-contain w-16 h-16 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                                        @endif
                                    </div>
                                    <div>
                                        <label for="top_banner_image" class="block mb-1.5 text-sm font-medium text-gray-700">Top Banner Image</label>
                                        {{-- FIX: Updated accept attribute --}}
                                        <input type="file" id="top_banner_image" name="top_banner_image" accept="image/png, image/jpeg, image/webp, image/svg+xml"
                                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended: SVG, PNG, or JPG (max 2MB)</p>
                                        @if ($data['top_banner_image'])
                                            <img src="{{ asset('storage/' . $data['top_banner_image']) }}" class="object-contain w-auto h-24 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: CONTACT INFORMATION --}}
                    <div x-show="activeTab === 'contact'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Contact Information</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <div class="md:col-span-3">
                                        <label for="address" class="block mb-1.5 text-sm font-medium text-gray-700">Address</label>
                                        <textarea id="address" name="address" rows="2" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $data['address']) }}</textarea>
                                    </div>
                                    <div>
                                        <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $data['email']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="phone" class="block mb-1.5 text-sm font-medium text-gray-700">Phone</label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone', $data['phone']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="phone_alternate" class="block mb-1.5 text-sm font-medium text-gray-700">Alternate Phone</label>
                                        <input type="text" id="phone_alternate" name="phone_alternate" value="{{ old('phone_alternate', $data['phone_alternate']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label for="map_embed_url" class="block mb-1.5 text-sm font-medium text-gray-700">Google Maps Embed URL</label>
                                        <input type="url" id="map_embed_url" name="map_embed_url" placeholder="https://www.google.com/maps/embed?..." value="{{ old('map_embed_url', $data['map_embed_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <p class="mt-1.5 text-xs text-gray-500">Paste the full `src` URL from Google Maps Embed iframe.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: SOCIAL LINKS --}}
                    <div x-show="activeTab === 'social'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Social Links</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <div>
                                        <label for="facebook_url" class="block mb-1.5 text-sm font-medium text-gray-700">Facebook URL</label>
                                        <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $data['facebook_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="twitter_url" class="block mb-1.5 text-sm font-medium text-gray-700">Twitter/X URL</label>
                                        <input type="url" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $data['twitter_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="instagram_url" class="block mb-1.5 text-sm font-medium text-gray-700">Instagram URL</label>
                                        <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $data['instagram_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="youtube_url" class="block mb-1.5 text-sm font-medium text-gray-700">YouTube URL</label>
                                        <input type="url" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $data['youtube_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="linkedin_url" class="block mb-1.5 text-sm font-medium text-gray-700">LinkedIn URL</label>
                                        <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $data['linkedin_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 5: FOOTER CONTENT --}}
                    <div x-show="activeTab === 'footer'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Footer Content</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label for="footer_about" class="block mb-1.5 text-sm font-medium text-gray-700">About Text</label>
                                        <textarea id="footer_about" name="footer_about" rows="3" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Short description shown in the footer">{{ old('footer_about', $data['footer_about']) }}</textarea>
                                    </div>
                                    <div x-data="{ links: {{ Js::from($data['footer_links'] ?? []) }}, add(){ this.links.push({title:'', url:''}) }, remove(i){ this.links.splice(i,1) } }">
                                        <div class="flex items-center justify-between">
                                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Useful Links</label>
                                            <button type="button" @click="add()" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Add Link</button>
                                        </div>
                                        <template x-if="links.length === 0">
                                            <div class="p-3 mt-2 text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded">No links added yet.</div>
                                        </template>
                                        <div class="mt-3 space-y-3">
                                            <template x-for="(link, index) in links" :key="index">
                                                <div class="grid items-end grid-cols-1 gap-3 p-3 border border-gray-200 rounded-lg md:grid-cols-12 bg-white">
                                                    <div class="md:col-span-5">
                                                        <label :for="'footer_links_'+index+'_title'" class="block mb-1 text-xs font-medium text-gray-600">Title</label>
                                                        <input type="text" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" :id="'footer_links_'+index+'_title'" :name="`footer_links[${index}][title]`" x-model="link.title">
                                                    </div>
                                                    <div class="md:col-span-6">
                                                        <label :for="'footer_links_'+index+'_url'" class="block mb-1 text-xs font-medium text-gray-600">URL</label>
                                                        <input type="url" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" :id="'footer_links_'+index+'_url'" :name="`footer_links[${index}][url]`" x-model="link.url" placeholder="https://...">
                                                    </div>
                                                    <div class="flex md:col-span-1 md:justify-end">
                                                        <button type="button" @click="remove(index)" class="px-3 py-2 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100">Remove</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 6: SEO SETTINGS --}}
                    <div x-show="activeTab === 'seo'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">SEO Settings</h3>
                                <p class="mt-1 text-sm text-gray-600">Settings for search engine optimization (Google, Bing, etc.)</p>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <div class="md:col-span-3">
                                        <label for="meta_title" class="block mb-1.5 text-sm font-medium text-gray-700">Meta Title</label>
                                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $data['meta_title']) }}"
                                            class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Your College Name - Best Courses">
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended 50-60 characters.</p>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label for="meta_description" class="block mb-1.5 text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea id="meta_description" name="meta_description" rows="3" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Short description of the website for search results">{{ old('meta_description', $data['meta_description']) }}</textarea>
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended 150-160 characters.</p>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label for="meta_image" class="block mb-1.5 text-sm font-medium text-gray-700">Meta Image (Social Share)</label>
                                        {{-- FIX: Updated accept attribute --}}
                                        <input type="file" id="meta_image" name="meta_image" accept="image/png, image/jpeg, image/webp, image/svg+xml"
                                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                                        <p class="mt-1.5 text-xs text-gray-500">Recommended: 1200x630px (JPG, PNG, WEBP, SVG)</p>
                                        @if ($data['meta_image'])
                                            <img src="{{ asset('storage/' . $data['meta_image']) }}" class="object-contain w-auto h-32 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 7: BANNER MEDIA --}}
                    <div x-show="activeTab === 'media'" x-cloak>
                        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Homepage Banner Media</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="banner_media" class="block mb-1.5 text-sm font-medium text-gray-700">Upload New Media</label>
                                    {{-- FIX: Made accept more explicit --}}
                                    <input type="file" id="banner_media" name="banner_media[]" accept="image/png, image/jpeg, image/webp, image/svg+xml, video/mp4, video/mov, video/avi" multiple
                                        class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                                    <p class="mt-1.5 text-xs text-gray-500">
                                        <strong class="text-red-600">Warning:</strong> Uploading new media will <strong class="underline">delete and replace</strong> all existing ones.
                                    </p>
                                </div>

                                @if (!empty($data['banner_media']))
                                    <div>
                                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Current Media</label>
                                        <div class="grid grid-cols-2 gap-4 mt-2 sm:grid-cols-4 lg:grid-cols-5">
                                            @foreach ($data['banner_media'] as $item)
                                                @php
                                                    $media = json_decode($item->value, true);
                                                    $mediaKey = $item->key;
                                                @endphp
                                                <div class="relative" x-ref="{{ $mediaKey }}">
                                                    <button type="button" @click.prevent="deleteMedia('{{ $mediaKey }}')"
                                                        class="absolute z-10 flex items-center justify-center w-6 h-6 text-white transition-colors bg-red-600 rounded-full top-2 right-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                        <i class="bi bi-x-lg" style="font-size: 0.8rem; line-height: 1;"></i>
                                                    </button>

                                                    @if ($media['type'] === 'image')
                                                        <img src="{{ asset('storage/' . $media['path']) }}" class="object-cover w-full h-32 border border-gray-200 rounded-lg shadow-sm">
                                                        <span class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-black/50 text-white">
                                                            <i class="bi bi-image me-1"></i> Image
                                                        </span>
                                                    @else
                                                        <video controls class="object-cover w-full h-32 border border-gray-200 rounded-lg shadow-sm">
                                                            <source src="{{ asset('storage/' . $media['path']) }}" type="video/mp4">
                                                        </video>
                                                        <span class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-black/50 text-white">
                                                            <i class="bi bi-camera-video me-1"></i> Video
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- End x-data for tabs --}}

            {{-- FORM SUBMIT FOOTER --}}
            <div class="flex justify-end pt-2 mt-6 border-t border-gray-200">
                <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-save me-2"></i> Save All Settings
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('settingsForm', () => ({
                // This function is for deleting media
                deleteMedia(mediaKey) {
                    const mediaItem = this.$refs[mediaKey];
                    if (!mediaItem) {
                        console.error('Could not find media item: ', mediaKey);
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to delete this media item? This cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            mediaItem.style.opacity = '0.5';
                            fetch('{{ route('admin.website-settings.delete-media') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ key: mediaKey })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    mediaItem.remove();
                                    Swal.fire(
                                        'Deleted!',
                                        data.message || 'Media has been deleted.',
                                        'success'
                                    );
                                } else {
                                    throw new Error(data.message || 'Failed to delete media.');
                                }
                            })
                            .catch(error => {
                                mediaItem.style.opacity = '1';
                                Swal.fire(
                                    'Error!',
                                    error.message || 'Something went wrong.',
                                    'error'
                                );
                            });
                        }
                    });
                },

                // This function shows the 'Saving...' alert on submit
                showSavingAlert() {
                    Swal.fire({
                        title: 'Saving Settings...',
                        text: 'Please wait while files are processed.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            }));
        });
    </script>
@endpush
