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
                        <th>Sucursal / edificio</th>
                        <th>Capacidad</th>
                        <th>Precio/noche</th>
                        <th>Fotos</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departamentos as $departamento)
                        <tr>
                            <td>
                                {{ $departamento->nombre }}
                                <div class="small text-secondary">{{ $departamento->propietario->nombre }}</div>
                            </td>
                            <td>
                                {{ $departamento->edificio->sucursal?->nombre }}
                                <div class="small text-secondary">
                                    {{ $departamento->edificio->nombre }}@if ($departamento->piso !== null) · piso {{ $departamento->piso }}@endif
                                </div>
                            </td>
                            <td>
                                {{ $departamento->capacidad_huespedes }}
                                <span class="text-secondary small">
                                    · {{ $departamento->dormitorios }} dorm
                                </span>
                            </td>
                            <td>
                                @if ($departamento->precio_base_noche > 0)
                                    {{ $departamento->moneda }} {{ number_format($departamento->precio_base_noche, 2) }}
                                @else
                                    <span class="badge text-bg-warning">Sin precio</span>
                                @endif
                            </td>
                            <td>
                                @if ($departamento->fotos_count > 0)
                                    {{ $departamento->fotos_count }}
                                @else
                                    <span class="badge text-bg-warning">Sin fotos</span>
                                @endif
                            </td>
                            <td>
                                @if ($departamento->publicado)
                                    <span class="badge text-bg-success">Publicado</span>
                                @else
                                    <span class="badge text-bg-secondary">Borrador</span>
                                @endif
                            </td>
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
