<template id="_embed">
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-600">Embed URL (YouTube, etc.)</label>
        <input type="text" x-model.debounce.400ms="block.src" @input="pushHistory" class="w-full p-2 border rounded"
            placeholder="https://www.youtube.com/watch?v=...">
    </div>
</template>
