<template id="_media-sub">
    <div class="text-center">
        <template x-if="sub.src">
            <template x-if="sub.type === 'image'">
                <img :src="sub.src" class="max-w-full mx-auto rounded-lg shadow-md" />
            </template>
            <template x-if="sub.type === 'video'">
                <video :src="sub.src" controls class="max-w-full mx-auto rounded-lg shadow-md"></video>
            </template>
            <template x-if="sub.type === 'pdf'">
                <iframe :src="sub.src" class="w-full h-[400px] rounded-lg shadow-md"></iframe>
            </template>
            <div class="flex justify-center gap-2 mt-2">
                {{-- Note: This is an exception, removeMediaFromSub needs parent scope access --}}
                <button @click="$parent.confirmRemoveSub($parent.block, sIndex)"
                    class="px-2 py-1 text-sm bg-red-100 rounded">Remove</button>
            </div>
        </template>
        <template x-if="!sub.src">
            <label class="block mt-2 cursor-pointer">
                <input type="file"
                    :accept="sub.type === 'image' ? 'image/*' : (sub.type === 'video' ? 'video/*' : 'application/pdf')"
                    @change="$parent.handleFileUpload($event, sub.id, sub.type, $parent.block)" class="hidden" />
                <div class="p-4 border border-gray-300 border-dashed rounded-lg hover:bg-blue-50">
                    <p class="text-sm text-gray-500"
                        x-text="sub.type === 'image' ? '📁 Click to upload image' : (sub.type === 'video' ? '🎬 Click to upload video' : '📄 Click to upload PDF')">
                    </p>
                </div>
            </label>
        </template>
    </div>
</template>
