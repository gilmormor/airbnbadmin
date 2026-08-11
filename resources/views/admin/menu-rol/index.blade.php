@extends('layouts.app')

@section('page-title', 'Menú - Rol')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Asignar menús por rol</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('menu-rol.index') }}">
                <label class="form-label">Seleccione uno o más roles:</label>
                <div class="d-flex gap-2 mb-2">
                    <button type="button" id="seleccionar-todos-roles" class="btn btn-sm btn-outline-secondary">Seleccionar todo</button>
                    <button type="button" id="borrar-todos-roles" class="btn btn-sm btn-outline-secondary">Borrar todo</button>
                </div>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach ($todosLosRoles as $rol)
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $rol->id }}"
                                id="rol-{{ $rol->id }}"
                                class="form-check-input casilla-rol"
                                @checked($rolesSeleccionados->contains('id', $rol->id))
                            >
                            <label class="form-check-label" for="rol-{{ $rol->id }}">{{ $rol->name }}</label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-search"></i> Consultar
                </button>
            </form>
        </div>
    </div>

    @if ($rolesSeleccionados->isNotEmpty())
        <div class="alert alert-dark d-flex align-items-center gap-2 flex-wrap">
            <i class="bi bi-lock"></i> <strong>Asignando acceso a:</strong>
            @foreach ($rolesSeleccionados as $rol)
                <span class="badge text-bg-info">{{ $rol->name }}</span>
            @endforeach
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Estructura del menú</h5>
                <div class="d-flex gap-2">
                    <button type="button" id="expandir-todo" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrows-expand"></i> Expandir todo
                    </button>
                    <button type="button" id="colapsar-todo" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrows-collapse"></i> Colapsar todo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle" id="tabla-menu-rol">
                        <thead>
                            <tr>
                                <th>Menú</th>
                                @foreach ($rolesSeleccionados as $rol)
                                    <th class="text-center">{{ $rol->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($arbol as $menu)
                                @include('admin.menu-rol._nodo', ['menu' => $menu, 'rolesSeleccionados' => $rolesSeleccionados, 'nivel' => 0])
                            @empty
                                <tr>
                                    <td colspan="{{ $rolesSeleccionados->count() + 1 }}" class="text-center text-body-secondary">
                                        No hay ítems de menú creados todavía. Ve a <a href="{{ route('menus.create') }}">Menú</a> para crear el primero.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('seleccionar-todos-roles')?.addEventListener('click', () => {
                document.querySelectorAll('.casilla-rol').forEach((c) => (c.checked = true));
            });
            document.getElementById('borrar-todos-roles')?.addEventListener('click', () => {
                document.querySelectorAll('.casilla-rol').forEach((c) => (c.checked = false));
            });

            function colapsarDescendientes(menuId) {
                document.querySelectorAll(`tr.fila-menu[data-parent-id="${menuId}"]`).forEach((fila) => {
                    fila.style.display = 'none';
                    const enlace = fila.querySelector('.toggle-nodo i');
                    if (enlace) {
                        enlace.classList.remove('bi-chevron-down');
                        enlace.classList.add('bi-chevron-right');
                    }
                    colapsarDescendientes(fila.dataset.menuId);
                });
            }

            document.querySelectorAll('.toggle-nodo').forEach((enlace) => {
                enlace.addEventListener('click', function () {
                    const menuId = this.dataset.menuId;
                    const icono = this.querySelector('i');
                    const expandido = icono.classList.contains('bi-chevron-down');

                    document.querySelectorAll(`tr.fila-menu[data-parent-id="${menuId}"]`).forEach((fila) => {
                        fila.style.display = expandido ? 'none' : '';
                    });

                    icono.classList.toggle('bi-chevron-right', expandido);
                    icono.classList.toggle('bi-chevron-down', !expandido);

                    if (expandido) {
                        colapsarDescendientes(menuId);
                    }
                });
            });

            document.getElementById('expandir-todo')?.addEventListener('click', () => {
                document.querySelectorAll('tr.fila-menu').forEach((fila) => (fila.style.display = ''));
                document.querySelectorAll('.toggle-nodo i').forEach((icono) => {
                    icono.classList.remove('bi-chevron-right');
                    icono.classList.add('bi-chevron-down');
                });
            });

            document.getElementById('colapsar-todo')?.addEventListener('click', () => {
                document.querySelectorAll('tr.fila-menu').forEach((fila) => {
                    if (fila.dataset.nivel !== '0') {
                        fila.style.display = 'none';
                    }
                });
                document.querySelectorAll('.toggle-nodo i').forEach((icono) => {
                    icono.classList.remove('bi-chevron-down');
                    icono.classList.add('bi-chevron-right');
                });
            });

            const token = document.querySelector('meta[name="csrf-token"]').content;
            document.querySelectorAll('.toggle-menu-rol').forEach((checkbox) => {
                checkbox.addEventListener('change', async function () {
                    this.disabled = true;
                    try {
                        const response = await fetch('{{ route('menu-rol.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                menu_id: this.dataset.menuId,
                                rol_id: this.dataset.rolId,
                                checked: this.checked,
                            }),
                        });
                        if (!response.ok) {
                            throw new Error('No se pudo guardar');
                        }
                    } catch (e) {
                        this.checked = !this.checked;
                        alert('No se pudo guardar el cambio. Intenta de nuevo.');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush
