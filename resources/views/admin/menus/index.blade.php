@extends('layouts.app')

@section('page-title', 'Menú')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Ítems de menú</h5>
            <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo ítem
            </a>
        </div>
        <div class="card-body">
            <p class="text-body-secondary small">
                Arrastra un ítem (desde <i class="bi bi-grip-vertical"></i>) para reordenarlo o soltarlo dentro de
                otro y convertirlo en submenú. Los cambios se guardan automáticamente.
            </p>

            <ul id="arbol-menus" class="menu-tree list-group list-unstyled">
                @forelse ($arbol as $item)
                    @include('admin.menus._nodo', ['item' => $item])
                @empty
                    <li class="list-group-item text-body-secondary text-center">
                        No hay ítems de menú creados todavía.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Sortable) {
                return;
            }

            document.querySelectorAll('.menu-tree').forEach((lista) => {
                new Sortable(lista, {
                    group: 'menus',
                    handle: '.drag-handle',
                    animation: 150,
                    forceFallback: true,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: guardarEstructura,
                });
            });

            function serializarLista(ul) {
                return Array.from(ul.children)
                    .filter((li) => li.classList.contains('menu-nodo'))
                    .map((li) => {
                        const hijosUl = li.querySelector(':scope > ul.menu-tree');
                        return {
                            id: li.dataset.id,
                            hijos: hijosUl ? serializarLista(hijosUl) : [],
                        };
                    });
            }

            async function guardarEstructura() {
                const raiz = document.getElementById('arbol-menus');
                const estructura = serializarLista(raiz);
                const token = document.querySelector('meta[name="csrf-token"]').content;

                try {
                    const response = await fetch('{{ route('menus.guardar-orden') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ estructura }),
                    });
                    if (!response.ok) {
                        throw new Error('No se pudo guardar');
                    }
                } catch (e) {
                    alert('No se pudo guardar el nuevo orden. Recarga la página e intenta de nuevo.');
                }
            }
        });
    </script>
@endpush
