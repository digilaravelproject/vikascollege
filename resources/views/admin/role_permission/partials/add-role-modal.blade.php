<div x-show="openRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openRole = false"></div>

    <div class="relative w-full max-w-md bg-white shadow-lg rounded-xl" @click.away="openRole = false" x-show="openRole"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">

        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Add New Role</h3>
            <button @click="openRole = false" class="text-gray-400 hover:text-gray-600">
                <i class="text-lg bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.roles-permissions.create-role') }}" method="POST">
            @csrf
            <div class="p-4 space-y-3">
                <label for="role_name" class="block mb-1 text-xs font-medium text-gray-700">Role Name</label>
                <input type="text" id="role_name" name="name"
                    class="w-full px-3 py-1.5 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Editor" required>
            </div>
            <div class="flex justify-end gap-2 px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button type="button" @click="openRole = false"
                    class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">Cancel</button>
                <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">Save
                    Role</button>
            </div>
        </form>
    </div>
</div>
