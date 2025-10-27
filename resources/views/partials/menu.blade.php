<ul class="navbar-nav me-auto mb-2 mb-lg-0">
    @foreach($menus as $menu)
        @if($menu->children->count())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{ $menu->id }}" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $menu->title }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{ $menu->id }}">
                    @foreach($menu->children as $child)
                        <li><a class="dropdown-item" href="{{ $child->url }}">{{ $child->title }}</a></li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" href="{{ $menu->url }}">{{ $menu->title }}</a>
            </li>
        @endif
    @endforeach
</ul>


{{-- @include('partials.menu') --}}