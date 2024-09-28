@php
    $menus = App\Models\Menu::orderBy('order', 'ASC')->get();
@endphp

<div id="menu">
    <div class="trigger-menu">
        <button type="button" class="btn btn-dark d-flex align-items-center gap-1" id="trigger-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
            </svg>
            <span>Menu</span>
        </button>
    </div>

    <div class="list-menu">
        <div class="list-group border-0">
            <a href="{{ route('dasbor') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('dasbor*') ? 'active' : '' }}">
                Dasbor
            </a>

            @foreach ($menus as $menu)
                @if (is_null($menu->parent_id) &&
                        auth()->user()->hasAnyRole(explode('|', $menu->role)))
                    <div class="fw-bold my-3">{{ $menu->name }}</div>

                    @if ($menu->children->isNotEmpty())
                        @foreach ($menu->children as $child)
                            @if (auth()->user()->hasAnyRole(explode('|', $child->role)))
                                <a href="{{ route($child->route) }}"
                                    class="list-group-item list-group-item-action border-0 {{ request()->is($child->route . '*') ? 'active' : '' }}">
                                    {{ $child->name }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>
