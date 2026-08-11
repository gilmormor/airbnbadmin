@php $tieneHijos = $menu->hijos->isNotEmpty(); @endphp
<tr class="fila-menu" data-menu-id="{{ $menu->id }}" data-parent-id="{{ $padreId ?? '' }}" data-nivel="{{ $nivel }}" style="{{ $nivel > 0 ? 'display:none' : '' }}">
    <td style="padding-left: {{ $nivel * 24 }}px">
        @if ($tieneHijos)
            <a href="javascript:void(0)" class="toggle-nodo text-dark text-decoration-none me-1" data-menu-id="{{ $menu->id }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="d-inline-block me-1" style="width: 1rem"></span>
        @endif
        @if ($menu->icono)
            <i class="{{ $menu->icono }} me-1"></i>
        @endif
        {{ $menu->nombre }}
    </td>
    @foreach ($rolesSeleccionados as $rol)
        <td class="text-center">
            <input
                type="checkbox"
                class="form-check-input toggle-menu-rol"
                data-menu-id="{{ $menu->id }}"
                data-rol-id="{{ $rol->id }}"
                @checked($menu->roles->contains('id', $rol->id))
            >
        </td>
    @endforeach
</tr>
@foreach ($menu->hijos as $hijo)
    @include('admin.menu-rol._nodo', ['menu' => $hijo, 'rolesSeleccionados' => $rolesSeleccionados, 'nivel' => $nivel + 1, 'padreId' => $menu->id])
@endforeach
