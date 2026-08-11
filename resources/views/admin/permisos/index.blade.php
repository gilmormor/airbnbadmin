@extends('layouts.app')

@section('page-title', 'Permisos')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Permisos</h5>
            <a href="{{ route('permisos.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo permiso
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-permisos" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Roles que lo tienen</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permisos as $permiso)
                        <tr>
                            <td>{{ $permiso->name }}</td>
                            <td>{{ $permiso->roles_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('permisos.edit', $permiso) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('permisos.destroy', $permiso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este permiso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-permisos'));
    </script>
@endpush
