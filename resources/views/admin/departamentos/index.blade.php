@extends('layouts.app')

@section('page-title', 'Departamentos')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Departamentos</h5>
            <a href="{{ route('departamentos.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo departamento
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-departamentos" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Edificio</th>
                        <th>Propietario</th>
                        <th>% Comisión coanfitrión</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departamentos as $departamento)
                        <tr>
                            <td>{{ $departamento->nombre }}</td>
                            <td>{{ $departamento->edificio->nombre }}</td>
                            <td>{{ $departamento->propietario->nombre }}</td>
                            <td>{{ $departamento->comision_coanfitrion_pct !== null ? $departamento->comision_coanfitrion_pct.'%' : '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('departamentos.edit', $departamento) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('departamentos.destroy', $departamento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este departamento?');">
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
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-departamentos'));
    </script>
@endpush
