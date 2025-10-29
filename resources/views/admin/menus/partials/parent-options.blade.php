@props(['menuItem', 'level' => 0, 'selected' => null])

<option value="{{ $menuItem->slug }}" {{ $selected == $menuItem->id ? 'selected' : '' }}>
    {{ str_repeat('--', $level) }} {{ $menuItem->title }}
</option>

@if($menuItem->childrenRecursive)
    @foreach($menuItem->childrenRecursive as $child)
        @include('admin.menus.partials.parent-options', [
            'menuItem' => $child,
            'level' => $level + 1,
            'selected' => $selected
        ])
    @endforeach
@endif
