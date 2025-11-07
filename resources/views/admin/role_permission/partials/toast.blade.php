<div x-data="{ show: false, message: '', type: 'success' }" @notify.window="
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        show = true;
        setTimeout(() => show = false, 3000);
    " x-show="show" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed z-50 flex items-center justify-between px-3 py-2 text-white rounded-lg shadow-lg bottom-5 right-5"
    :class="type === 'success' ? 'bg-blue-600' : 'bg-red-600'">

    <i :class="type === 'success' ? 'bi bi-check-circle' : 'bi bi-exclamation-triangle'" class="text-base me-1.5"></i>
    <span x-text="message" class="text-sm font-normal"></span>

    <button @click="show = false" class="ml-2 -mr-1 transition text-white/70 hover:text-white">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
