@extends('layouts.app')

@section('page-title', 'Roles')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Roles</h5>
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo rol
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-roles" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuarios</th>
                        <th>Permisos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $rol)
                        <tr>
                            <td>{{ $rol->name }}</td>
                            <td>{{ $rol->users_count }}</td>
                            <td>{{ $rol->permissions_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este rol?');">
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
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-roles'));
    </script>
@endpush
