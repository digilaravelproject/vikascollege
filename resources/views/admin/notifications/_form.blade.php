{{--
This partial needs two variables:
$notification - The notification model (or null for create)
$icons - The array of preset icons
--}}
<div class="space-y-6">

    {{-- Section 1: Content --}}
    <fieldset>
        <legend class="text-base font-semibold text-gray-900">Content</legend>
        <div class="mt-4 space-y-4">

            {{-- Icon Picker (Unchanged from before) --}}
            <div x-data="{ icon: '{{ old('icon', optional($notification)->icon) }}' }">
                <label class="block text-sm font-medium text-gray-700">Icon</label>
                <div class="flex flex-wrap gap-2 p-2 mt-1 border border-gray-200 rounded-md">
                    @foreach ($icons as $ic)
                        <label class="px-3 py-1 transition-all border rounded-md cursor-pointer" x-bind:class="icon === '{{ $ic }}' ?
                                    'border-blue-500 bg-blue-50 ring-1 ring-blue-500' :
                                    'border-gray-300 bg-white hover:bg-gray-50'">
                            <input type="radio" name="icon" value="{{ $ic }}" x-model="icon" class="hidden">
                            <span>{{ $ic }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="text" name="icon" x-model="icon" placeholder="Or paste custom emoji/icon"
                    class="block w-full p-2 mt-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">
                    @if (request()->routeIs('admin.notifications.create'))
                        If empty, a random icon will be used.
                    @endif
                </p>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title"
                    class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('title', optional($notification)->title) }}" required>
            </div>
        </div>
    </fieldset>

    {{-- Section 2: Action Button --}}
    <fieldset>
        <legend class="text-base font-semibold text-gray-900">Action</legend>
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
            <div>
                <label for="href" class="block text-sm font-medium text-gray-700">Link (href)</label>
                <input type="text" name="href" id="href"
                    class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('href', optional($notification)->href) }}" placeholder="https://...">
            </div>

            <div>
                <label for="button_name" class="block text-sm font-medium text-gray-700">Button Name</label>
                <input type="text" name="button_name" id="button_name"
                    class="block w-full p-2 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('button_name', optional($notification)->button_name) }}" placeholder="Click Here">
            </div>
        </div>
    </fieldset>

    {{-- Section 3: Settings (NOW WITH TOGGLES) --}}
    <fieldset>
        <legend class="text-base font-semibold text-gray-900">Settings</legend>
        <div class="grid grid-cols-1 gap-y-4 gap-x-4 mt-4 sm:grid-cols-3">

            {{-- Toggle for Status --}}
            <div
                x-data="{ enabled: {{ old('status', optional($notification)->status ?? 1) == 1 ? 'true' : 'false' }} }">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <input type="hidden" name="status" :value="enabled ? '1' : '0'">
                <button @click="enabled = !enabled" type="button"
                    class="relative inline-flex items-center h-6 mt-1 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                    <span
                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                </button>
                <span x-text="enabled ? 'Active' : 'Inactive'" class="ml-2 text-sm text-gray-600"></span>
            </div>

            {{-- Toggle for Featured --}}
            <div
                x-data="{ enabled: {{ old('featured', optional($notification)->featured ?? 0) == 1 ? 'true' : 'false' }} }">
                <label class="block text-sm font-medium text-gray-700">Featured</label>
                <input type="hidden" name="featured" :value="enabled ? '1' : '0'">
                <button @click="enabled = !enabled" type="button"
                    class="relative inline-flex items-center h-6 mt-1 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                    <span
                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                </button>
                <span x-text="enabled ? 'Yes' : 'No'" class="ml-2 text-sm text-gray-600"></span>
            </div>

            {{-- Toggle for Feature on Top --}}
            <div
                x-data="{ enabled: {{ old('feature_on_top', optional($notification)->feature_on_top ?? 0) == 1 ? 'true' : 'false' }} }">
                <label class="block text-sm font-medium text-gray-700">Feature on Top</label>
                <input type="hidden" name="feature_on_top" :value="enabled ? '1' : '0'">
                <button @click="enabled = !enabled" type="button"
                    class="relative inline-flex items-center h-6 mt-1 transition-colors duration-200 ease-in-out rounded-full w-11 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="enabled ? 'bg-blue-600' : 'bg-gray-200'" role="switch" :aria-checked="enabled">
                    <span
                        class="inline-block w-4 h-4 transition duration-200 ease-in-out transform bg-white rounded-full"
                        :class="{ 'translate-x-6': enabled, 'translate-x-1': !enabled }"></span>
                </button>
                <span x-text="enabled ? 'On' : 'Off'" class="ml-2 text-sm text-gray-600"></span>
            </div>

        </div>
    </fieldset>

    {{-- Section 4: Date (NOW WITH FLATPICKR) --}}
    <div>
        <label for="display_date" class="block text-sm font-medium text-gray-700">Date</label>
        <div class="relative mt-1" x-data x-init="flatpickr($refs.datepicker, { dateFormat: 'Y-m-d' })">

            {{-- This is the icon --}}
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>

            {{-- This is the input that Flatpickr will target --}}
            <input x-ref="datepicker" type="text" name="display_date" id="display_date"
                class="block w-full p-2 pl-10 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                value="{{ old('display_date', $notification ? optional($notification->display_date)->format('Y-m-d') : '') }}"
                placeholder="Select a date">
        </div>
    </div>
</div>
