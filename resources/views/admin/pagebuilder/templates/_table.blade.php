<template id="_table">
    <div class="space-y-3">

        {{-- Table Renderer --}}
        <div class="overflow-x-auto border border-gray-300 rounded shadow-inner">
            <table class="min-w-full divide-y divide-gray-300">
                <template x-if="block.data.length > 0">
                    <thead class="bg-gray-200 border-b border-gray-300">
                        <tr class="text-xs text-gray-700 font-semibold uppercase leading-normal">
                            <template x-for="(col, colIndex) in block.data[0]" :key="colIndex">
                                <th class="py-2 px-1 border-r border-gray-300 last:border-r-0">
                                    <input type="text" x-model.debounce.400ms="block.data[0][colIndex]"
                                        @input="pushHistory"
                                        class="w-full text-center bg-transparent border border-gray-400 rounded focus:ring-1 focus:ring-blue-500 p-0.5 text-xs font-semibold"
                                        placeholder="Header" />
                                </th>
                            </template>
                        </tr>
                    </thead>
                </template>
                <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                    <template x-for="(row, rowIndex) in block.data" :key="rowIndex">
                        <template x-if="rowIndex > 0">
                            <tr class="hover:bg-gray-50">
                                <template x-for="(cell, colIndex) in row" :key="colIndex">
                                    <td class="py-1 px-1 border-r border-gray-200 last:border-r-0 align-top">
                                        <textarea x-model.debounce.400ms="block.data[rowIndex][colIndex]"
                                            @input="pushHistory" rows="1"
                                            class="w-full p-1 text-xs border border-gray-200 rounded focus:border-blue-400 resize-none min-h-6"></textarea>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Table Controls --}}
        <div class="flex flex-wrap items-center gap-2 justify-end">
            <button @click.prevent="addRow()"
                class="flex items-center space-x-1 px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"><span>+</span><span>Row</span></button>
            <button @click.prevent="removeRow()" :disabled="block.data.length <= 1"
                class="flex items-center space-x-1 px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"><span>-</span><span>Row</span></button>
            <span class="text-gray-300">|</span>
            <button @click.prevent="addCol()"
                class="flex items-center space-x-1 px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"><span>+</span><span>Col</span></button>
            <button @click.prevent="removeCol()" :disabled="block.data[0].length <= 1"
                class="flex items-center space-x-1 px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"><span>-</span><span>Col</span></button>
        </div>
    </div>
</template>
