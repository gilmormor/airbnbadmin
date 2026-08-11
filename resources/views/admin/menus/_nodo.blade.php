<li class="menu-nodo list-group-item" data-id="{{ $item->id }}">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="drag-handle text-body-secondary" style="cursor: grab;">
                <i class="bi bi-grip-vertical"></i>
            </span>
            @if ($item->icono)
                <i class="{{ $item->icono }}"></i>
            @endif
            <span>{{ $item->nombre }}</span>
            @if ($item->ruta)
                <code class="small text-body-secondary">{{ $item->ruta }}</code>
            @endif
        </div>
        <div>
            <a href="{{ route('menus.edit', $item) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('menus.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este ítem de menú?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    <ul class="menu-tree list-unstyled ps-4 mt-2 mb-0" data-menu-id="{{ $item->id }}">
        @foreach ($item->hijosArbol as $hijo)
            @include('admin.menus._nodo', ['item' => $hijo])
        @endforeach
    </ul>
</li>
