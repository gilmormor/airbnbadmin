@extends('layouts.app')

@section('page-title', 'Propiedades en Beds24')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <p class="text-body-secondary small mb-0">
                Copia el <strong>Property ID</strong> (y el Room ID si la propiedad tiene varias unidades) a cada
                Departamento en <a href="{{ route('departamentos.index') }}">Departamentos</a> para que la sincronización
                automática asigne correctamente cada reserva.
            </p>
        </div>
    </div>

    @if ($error)
        <div class="alert alert-danger">
            No se pudo consultar la API de Beds24: {{ $error }}
            <div class="small mt-2">Verifica que <code>BEDS24_REFRESH_TOKEN</code> esté configurado correctamente en el archivo <code>.env</code>.</div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Propiedades</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Property ID</th>
                            <th>Nombre</th>
                            <th>Room ID</th>
                            <th>Nombre de unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($propiedades as $propiedad)
                            @php $rooms = $propiedad['roomTypes'] ?? $propiedad['rooms'] ?? []; @endphp
                            @if (count($rooms) > 0)
                                @foreach ($rooms as $room)
                                    <tr>
                                        <td>{{ $propiedad['id'] ?? $propiedad['propId'] ?? '—' }}</td>
                                        <td>{{ $propiedad['name'] ?? '—' }}</td>
                                        <td>{{ $room['id'] ?? $room['roomId'] ?? '—' }}</td>
                                        <td>{{ $room['name'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $propiedad['id'] ?? $propiedad['propId'] ?? '—' }}</td>
                                    <td>{{ $propiedad['name'] ?? '—' }}</td>
                                    <td>—</td>
                                    <td>—</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary">Sin propiedades devueltas por la API.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <details class="mb-4">
            <summary class="text-body-secondary small">Ver respuesta completa de la API (por si los nombres de campo no coinciden arriba)</summary>
            <pre class="small bg-body-tertiary p-3 mt-2" style="max-height: 400px; overflow: auto;">{{ json_encode($propiedades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </details>
    @endif
@endsection
