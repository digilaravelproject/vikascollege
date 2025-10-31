@props(['label', 'name', 'type' => 'text', 'xModel' => null, 'required' => false])

<div>
    <label class="block mb-1 text-sm font-medium text-gray-700">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <input type="{{ $type }}" name="{{ $name }}" {{ $required ? 'required' : '' }}
        x-model="{{ $xModel }}"
        {{ $attributes->merge(['class' => 'w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all']) }}>
</div>
