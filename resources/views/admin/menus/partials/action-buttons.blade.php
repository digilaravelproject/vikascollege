<div class="flex items-center justify-center gap-2">
    <a href="{{ route('admin.menus.edit', $item->id) }}"
        class="px-3 py-1.5 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500 focus-visible:ring-offset-2">
        <i class="bi bi-pencil-square"></i> Edit
    </a>
    <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="inline">
        @csrf @method('DELETE')
        <button type="submit" @click.prevent="
                confirmModalTitle = 'Delete Menu Item';
                confirmModalMessage = 'Are you sure you want to delete this item? This action cannot be undone.';
                formToSubmit = $el.closest('form');
                showConfirmModal = true;
            "
            class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
</div>
