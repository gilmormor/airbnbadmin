@php
    $tieneHijos = $item->hijosArbol->isNotEmpty();
    $ramaActiva = $tieneHijos && $item->contieneRutaActiva();
@endphp
<li class="nav-item {{ $ramaActiva ? 'menu-open' : '' }}">
    @if ($tieneHijos)
        <a href="#" class="nav-link" aria-expanded="{{ $ramaActiva ? 'true' : 'false' }}">
            @if ($item->icono)
                <i class="nav-icon {{ $item->icono }}"></i>
            @endif
            <p>
                {{ $item->nombre }}
                <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @foreach ($item->hijosArbol as $hijo)
                @include('partials.menu-item', ['item' => $hijo])
            @endforeach
        </ul>
    @else
        <a href="{{ $item->ruta && \Illuminate\Support\Facades\Route::has($item->ruta) ? route($item->ruta) : '#' }}"
           class="nav-link {{ $item->ruta && request()->routeIs($item->ruta.'*') ? 'active' : '' }}">
            @if ($item->icono)
                <i class="nav-icon {{ $item->icono }}"></i>
            @endif
            <p>{{ $item->nombre }}</p>
        </a>
    @endif
</li>
