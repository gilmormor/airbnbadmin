@extends('layouts.app')

@section('page-title', 'Importar reservas')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Importar reservas desde CSV</h5>
        </div>
        <div class="card-body">
            @include('partials.form-errors')

            <p class="text-body-secondary small">
                Sube el archivo exportado desde Airbnb, Booking.com o VRBO. El sistema intentará
                relacionar cada fila con un departamento a partir del nombre del anuncio; si no lo
                encuentra, se usará el departamento por defecto que selecciones abajo.
            </p>

            <form method="POST" action="{{ route('importaciones.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Plataforma</label>
                    <select name="plataforma_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach ($plataformas as $plataforma)
                            <option value="{{ $plataforma->id }}">{{ $plataforma->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento por defecto (opcional)</label>
                    <select name="departamento_id" class="form-select">
                        <option value="">— Ninguno —</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">{{ $departamento->edificio->nombre }} - {{ $departamento->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Archivo CSV</label>
                    <input type="file" name="archivo" accept=".csv,.txt" class="form-control" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Historial de importaciones</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Origen</th>
                        <th>Usuario</th>
                        <th class="text-end">Filas</th>
                        <th class="text-end">Creadas</th>
                        <th class="text-end">Actualizadas</th>
                        <th class="text-end">Errores</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($importaciones as $importacion)
                        <tr>
                            <td>{{ $importacion->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $importacion->tipo }}</td>
                            <td>{{ $importacion->origen }}</td>
                            <td>{{ $importacion->usuario?->name ?? '—' }}</td>
                            <td class="text-end">{{ $importacion->total_filas }}</td>
                            <td class="text-end">{{ $importacion->total_creadas }}</td>
                            <td class="text-end">{{ $importacion->total_actualizadas }}</td>
                            <td class="text-end">
                                @if ($importacion->total_error > 0)
                                    <span class="badge text-bg-danger" data-bs-toggle="tooltip" title="{{ collect($importacion->detalle_errores)->pluck('error')->implode(' | ') }}">
                                        {{ $importacion->total_error }}
                                    </span>
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-body-secondary">Sin importaciones registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
