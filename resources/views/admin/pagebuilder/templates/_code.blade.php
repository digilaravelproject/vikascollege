<template id="_code">
    <div>
        <label class="text-sm font-medium text-gray-600">Code</label>
        <textarea x-model.debounce.400ms="block.content" @input="pushHistory"
            class="w-full p-2 font-mono border rounded" rows="6" placeholder="<script>..."></textarea>
    </div>
</template>
