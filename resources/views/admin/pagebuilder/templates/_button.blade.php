<template id="_button">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-gray-600">Button Text</label>
            <input type="text" x-model.debounce.400ms="block.text" @input="pushHistory"
                class="w-full p-2 border rounded" placeholder="Click Here">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Button Link (URL)</label>
            <input type="text" x-model.debounce.400ms="block.href" @input="pushHistory"
                class="w-full p-2 border rounded" placeholder="https://...">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Alignment</label>
            <select x-model.debounce.400ms="block.align" @change="pushHistory"
                class="w-full p-2 border rounded bg-white">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Target</label>
            <select x-model.debounce.400ms="block.target" @change="pushHistory"
                class="w-full p-2 border rounded bg-white">
                <option value="_self">Same Tab (_self)</option>
                <option value="_blank">New Tab (_blank)</option>
            </select>
        </div>
    </div>
</template>
