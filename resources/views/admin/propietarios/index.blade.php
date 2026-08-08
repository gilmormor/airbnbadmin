@extends('layouts.app')

@section('page-title', 'Propietarios')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Propietarios</h5>
            <a href="{{ route('propietarios.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo propietario
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-propietarios" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Departamentos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($propietarios as $propietario)
                        <tr>
                            <td>{{ $propietario->nombre }}</td>
                            <td>{{ $propietario->email }}</td>
                            <td>{{ $propietario->telefono }}</td>
                            <td>{{ $propietario->departamentos_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('propietarios.edit', $propietario) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('propietarios.destroy', $propietario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este propietario?');">
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
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-propietarios'));
    </script>
@endpush
