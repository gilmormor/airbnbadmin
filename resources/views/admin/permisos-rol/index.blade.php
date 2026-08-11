@extends('layouts.app')

@section('page-title', 'Permiso - Rol')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Asignar permisos por rol</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('permiso-rol.index') }}">
                <label class="form-label">Seleccione uno o más roles:</label>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach ($todosLosRoles as $rol)
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $rol->id }}"
                                id="rol-{{ $rol->id }}"
                                class="form-check-input"
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
            <i class="bi bi-shield-lock"></i> <strong>Asignando permisos a:</strong>
            @foreach ($rolesSeleccionados as $rol)
                <span class="badge text-bg-info">{{ $rol->name }}</span>
            @endforeach
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Permisos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle" id="tabla-permiso-rol">
                        <thead>
                            <tr>
                                <th>Permiso</th>
                                @foreach ($rolesSeleccionados as $rol)
                                    <th class="text-center">{{ $rol->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permisos as $permiso)
                                <tr>
                                    <td>{{ $permiso->name }}</td>
                                    @foreach ($rolesSeleccionados as $rol)
                                        <td class="text-center">
                                            <input
                                                type="checkbox"
                                                class="form-check-input toggle-permiso-rol"
                                                data-permiso-id="{{ $permiso->id }}"
                                                data-rol-id="{{ $rol->id }}"
                                                @checked($permiso->roles->contains('id', $rol->id))
                                            >
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $rolesSeleccionados->count() + 1 }}" class="text-center text-body-secondary">
                                        No hay permisos creados todavía. Ve a <a href="{{ route('permisos.create') }}">Permisos</a> para crear el primero.
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
            const token = document.querySelector('meta[name="csrf-token"]').content;

            document.querySelectorAll('.toggle-permiso-rol').forEach((checkbox) => {
                checkbox.addEventListener('change', async function () {
                    this.disabled = true;
                    try {
                        const response = await fetch('{{ route('permiso-rol.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                permiso_id: this.dataset.permisoId,
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
