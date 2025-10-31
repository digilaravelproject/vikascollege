@props(['label', 'name', 'xModel' => null])

<div>
    <label class="block mb-1 text-sm font-medium text-gray-700">{{ $label }}</label>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="{{ $name }}" value="1" class="sr-only peer" x-model="{{ $xModel }}">
        <div
            class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-600 transition-all relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:h-5 after:w-5 after:rounded-full after:transition-all peer-checked:after:translate-x-5">
        </div>
    </label>
</div>