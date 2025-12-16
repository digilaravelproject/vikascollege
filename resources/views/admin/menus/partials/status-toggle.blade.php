<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox" x-data="{ checked: {{ $item->status ? 'true' : 'false' }} }" x-model="checked" @change="
            fetch('{{ route('admin.menus.toggle-status', $item->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ status: checked })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    $dispatch('notify', { message: 'Status updated!', type: 'success' })
                } else {
                    $dispatch('notify', { message: 'Failed to update status', type: 'error' })
                }
            })
        " class="sr-only peer">
    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 rounded-full
            peer peer-checked:after:translate-x-5 peer-checked:after:border-white
            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
            after:bg-white after:border-gray-300 after:border after:rounded-full
            after:h-5 after:w-5 after:transition-all
            peer-checked:bg-blue-600 transition-colors duration-300 ease-in-out">
    </div>
</label>
