@extends('layouts.app')

@section('page-title', 'Edificios')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Edificios</h5>
            <a href="{{ route('edificios.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo edificio
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-edificios" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Departamentos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($edificios as $edificio)
                        <tr>
                            <td>{{ $edificio->nombre }}</td>
                            <td>{{ $edificio->direccion }}</td>
                            <td>{{ $edificio->departamentos_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('edificios.edit', $edificio) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('edificios.destroy', $edificio) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este edificio?');">
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
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-edificios'));
    </script>
@endpush
