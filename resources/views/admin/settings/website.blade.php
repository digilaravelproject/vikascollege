@extends('layouts.admin.app')
@section('title', 'Website Settings')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Website Settings</h1>
        </div>

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
            - Added @submit="showSavingAlert"
            - This will call the Alpine function when the form is submitted.
        --}}
        <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6" x-data="settingsForm()" @submit="showSavingAlert">
            @csrf

            {{-- CARD 1: GENERAL SETTINGS --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">General Settings</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="college_name" class="block mb-1.5 text-sm font-medium text-gray-700">College Name <span class="text-red-500">*</span></label>
                            <input type="text" id="college_name" name="college_name"
                                value="{{ old('college_name', $data['college_name']) }}"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="banner_heading" class="block mb-1.5 text-sm font-medium text-gray-700">Banner Heading</label>
                            <input type="text" id="banner_heading" name="banner_heading"
                                value="{{ old('banner_heading', $data['banner_heading']) }}"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="banner_subheading" class="block mb-1.5 text-sm font-medium text-gray-700">Banner Subheading</label>
                            <input type="text" id="banner_subheading" name="banner_subheading"
                                value="{{ old('banner_subheading', $data['banner_subheading']) }}"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="banner_button_text" class="block mb-1.5 text-sm font-medium text-gray-700">Button Text</label>
                            <input type="text" id="banner_button_text" name="banner_button_text"
                                value="{{ old('banner_button_text', $data['banner_button_text']) }}"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="banner_button_link" class="block mb-1.5 text-sm font-medium text-gray-700">Button Link</label>
                            <input type="url" id="banner_button_link" name="banner_button_link"
                                value="{{ old('banner_button_link', $data['banner_button_link']) }}"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: BRANDING --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Branding</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="college_logo" class="block mb-1.5 text-sm font-medium text-gray-700">College Logo</label>
                            <input type="file" id="college_logo" name="college_logo" accept="image/*"
                                class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                            <p class="mt-1.5 text-xs text-gray-500">Recommended: SVG, PNG, or JPG (max 2MB)</p>
                            @if ($data['college_logo'])
                                <img src="{{ asset('storage/' . $data['college_logo']) }}"
                                    class="object-contain w-auto h-24 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                            @endif
                        </div>
                        <div>
                            <label for="favicon" class="block mb-1.5 text-sm font-medium text-gray-700">Favicon</label>
                            <input type="file" id="favicon" name="favicon" accept="image/png, image/x-icon"
                                class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-100 file:border-0 file:px-3 file:py-2.5 file:me-3 file:text-gray-700 file:font-medium">
                            <p class="mt-1.5 text-xs text-gray-500">Recommended: 32x32 PNG or ICO</p>
                            @if ($data['favicon'])
                                <img src="{{ asset('storage/' . $data['favicon']) }}"
                                    class="object-contain w-16 h-16 p-2 mt-3 bg-gray-100 border border-gray-200 rounded-lg">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2.5: CONTACT INFORMATION --}}
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
                            <label for="map_embed_url" class="block mb-1.5 text-sm font-medium text-gray-700">Google Maps Embed URL</label>
                            <input type="url" id="map_embed_url" name="map_embed_url" placeholder="https://www.google.com/maps/embed?pb=..." value="{{ old('map_embed_url', $data['map_embed_url']) }}" class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1.5 text-xs text-gray-500">Paste the full iframe src URL from Google Maps Embed.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2.6: SOCIAL LINKS --}}
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

            {{-- CARD 2.7: FOOTER CONTENT --}}
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

            {{-- CARD 3: BANNER MEDIA --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Homepage Banner Media</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="banner_media" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Upload New Media
                        </label>
                        <input type="file" id="banner_media" name="banner_media[]" accept="image/*,video/*" multiple
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
                                        <button type="button"
                                            @click.prevent="deleteMedia('{{ $mediaKey }}')"
                                            class="absolute z-10 flex items-center justify-center w-6 h-6 text-white transition-colors bg-red-600 rounded-full top-2 right-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                            <i class="bi bi-x-lg" style="font-size: 0.8rem; line-height: 1;"></i>
                                        </button>

                                        @if ($media['type'] === 'image')
                                            <img src="{{ asset('storage/' . $media['path']) }}"
                                                class="object-cover w-full h-32 border border-gray-200 rounded-lg shadow-sm">
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

            {{-- FORM SUBMIT FOOTER --}}
            <div class="flex justify-end pt-2">
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
                // ... (Your existing deleteMedia function) ...
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

                // === ADDED: Function to show saving alert ===
                showSavingAlert() {
                    Swal.fire({
                        title: 'Saving Settings...',
                        text: 'Please wait while files are processed.',
                        icon: 'info',
                        allowOutsideClick: false, // Don't allow closing
                        showConfirmButton: false, // Hide the "OK" button
                        didOpen: () => {
                            Swal.showLoading(); // Show the loading spinner
                        }
                    });

                    // The form will submit naturally after this function runs.
                    // When the page reloads, the alert will automatically disappear.
                }
            }));
        });
    </script>
@endpush
